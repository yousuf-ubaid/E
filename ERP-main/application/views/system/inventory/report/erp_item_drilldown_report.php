<!---- =============================================
-- File Name : erp_item_drilldown_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 27 - November 2016
-- Description : This file contains Item ledger.

-- REVISION HISTORY
-- =============================================-->
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_itemLedger', 'Item Ledger',$excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = 'generateDrilldownReportPdf()');
        } ?>
    </div>
</div>
<div id="tbl_itemLedger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Item Ledger</div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php
            if (!empty($output)) {
            ?>
            <table class="borderSpace report-table-condensed" id="tbl_report">
                <?php
                $isLocalCost = false;
                $isRptCost = false;
                $grandTotalLocal = 0;
                $grandTotalRpt = 0;
                if ($fieldName == "companyLocalWacAmount") {
                    $isLocalCost = true;
                }
                if ($fieldName == "companyReportingWacAmount") {
                    $isRptCost = true;
                }
                $category = array();
                foreach ($output as $val) {
                    $category[$val["mainCategory"]][$val["subCategory"]][$val["itemSystemCode"]]['d'] = array("Item Code" => $val["itemSystemCode"], "Item Description" => $val["itemDescription"], "UOM" => $val["transactionUOM"], "Sales Price" => format_number($val["salesPrice"], $this->common_data['company_data']['company_default_decimal']));
                    $category[$val["mainCategory"]][$val["subCategory"]][$val["itemSystemCode"]]['t'][] = $val;
                }
                /* echo"<pre>";
                 print_r($category);
             echo"</pre>";*/
                if (!empty($category)) {
                    foreach ($category as $key => $mainCategory) {
                        echo "<tr><td colspan='8'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                        foreach ($mainCategory as $key2 => $subCategory) {
                            echo "<tr><td colspan='8'><div class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                            foreach ($subCategory as $key3 => $item) {
                                if (!empty($item)) {
                                    if (!empty($item['d'])) {
                                        echo '<tr><td colspan="8"><div><table><tr>';
                                        foreach ($item['d'] as $key5 => $val5) {
                                            echo "<td width='50px'><strong>" . $key5 . ":</strong></td>";
                                            echo "<td width='100px'>" . $val5 . "</td>";
                                        }
                                        echo "</tr></td></table></div></tr>";
                                    }
                                    if (!empty($item['t'])) {
                                        echo "<tr class='reportTableHeader'>";
                                        echo "<td><strong>Doc Type</strong></td>";
                                        echo "<td><strong>Doc Number</strong></td>";
                                        echo "<td><strong>Doc Date</strong></td>";
                                        echo "<td><strong>Segment</strong></td>";
                                        echo "<td><strong>Ref No</strong></td>";
                                        echo "<td><strong>Transaction QTY</strong></td>";
                                        if ($isLocalCost) {
                                            echo "<td><strong>Local Currency</strong></td>";
                                            echo "<td><strong>Asset Value</strong></td>";
                                        }
                                        if ($isRptCost) {
                                            echo "<td><strong>Reporting Currency</strong></td>";
                                            echo "<td><strong>Asset Value</strong></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    if (!empty($item['t'])) {
                                        $total = 0;
                                        $totalQty = 0;
                                        $localAssetvalue = 0;
                                        $rptAssetvalue = 0;
                                        foreach ($item['t'] as $key5 => $val5) {
                                            $totalQty += $val5["transactionQTY"];
                                            echo "<tr class='hoverTr'>";
                                            echo "<td>" . $val5["documentID"] . "</td>";
                                            echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val5["documentID"] . '\',' . $val5["documentAutoID"] . ')">' . $val5["documentSystemCode"] . '</a></td>';
                                            echo "<td>" . $val5["documentDate"] . "</td>";
                                            echo "<td>" . $val5["segmentCode"] . "</td>";
                                            echo "<td>" . $val5["referenceNumber"] . "</td>";
                                            echo "<td class='text-right'>" . $val5["transactionQTY"] . "</td>";
                                            if ($fieldName == "companyLocalWacAmount") {
                                                echo "<td class='text-right'>" . format_number($val5["avgCompanyLocalAmount"], $val5[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $localAssetvalue += $val5["localCostAsset"];
                                                $grandTotalLocal += $val5["localCostAsset"];
                                            } else if ($fieldName == "companyReportingWacAmount") {
                                                echo "<td class='text-right'>" . format_number($val5["avgCompanyReportingAmount"], $val5[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $rptAssetvalue += $val5["rptCostAsset"];
                                                $grandTotalRpt += $val5["rptCostAsset"];
                                            } else {
                                                echo "<td class='text-right'>" . format_number($val5[$fieldName], 2) . "</td>";
                                            }
                                            if ($fieldName == "companyLocalWacAmount") {
                                                echo "<td class='text-right'>" . format_number($val5["localCostAsset"], $val5[$fieldName . "DecimalPlaces"]) . "</td>";
                                            }
                                            if ($fieldName == "companyReportingWacAmount") {
                                                echo "<td class='text-right'>" . format_number($val5["rptCostAsset"], $val5[$fieldName . "DecimalPlaces"]) . "</td>";
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                    echo "<tr><td  colspan='5'></td><td class='reporttotal text-right'>" . $totalQty . "</td>";
                                    if ($isLocalCost) {
                                        echo "<td></td><td class='reporttotal text-right'>" . format_number($localAssetvalue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    if ($isRptCost) {
                                        echo "<td></td><td class='reporttotal text-right'>" . format_number($rptAssetvalue, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                    echo "<tr><td  colspan=''>&nbsp;</td></tr>";
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <?php
                    echo "<td class='text-right' colspan='7'><strong>Grand Total</strong></td>";
                    if ($isLocalCost) {
                        echo "<td class='reporttotal text-right'>" . format_number($grandTotalLocal, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                    }
                    if ($isRptCost) {
                        if ($isLocalCost && $isRptCost) {
                            echo "<td></td><td class='reporttotal text-right'>" . format_number($grandTotalRpt, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        } else {
                            echo "<td class='reporttotal text-right'>" . format_number($grandTotalRpt, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                    }
                    ?>
                </tr>
                <?php
                } else {
                    echo warning_message("No Records Found!");
                }
                ?>
            </table>
        </div>
    </div>
</div>
<script>
    $('.filterDate').datepicker({
        format: 'yyyy-mm-dd'
    });
</script>