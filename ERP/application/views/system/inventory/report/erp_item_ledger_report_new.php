<!---- =============================================
-- File Name : erp_item_ledger_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 17 - July 2019
-- Description : This file contains Item ledger optimized query template view.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_itemLedger', 'Item Ledger');
        } ?>
    </div>
</div>
<div id="tbl_itemLedger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_ledger'); ?></div>
            <!--Item Ledger-->
            <div class="text-center reportHeaderColor"> <?php
                $datefrom = $this->lang->line('transaction_date_from');
                $dateto = $this->lang->line('transaction_date_to');

                echo "<strong><!--Date From-->$datefrom: </strong>" . $from . " - <strong><!--Date To--> $dateto: </strong>" . $to ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Warehouse:</i></strong> <?php echo join(",", $warehouse) ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <table class="borderSpace report-table-condensed" id="tbl_report">
                <?php
                if (!empty($output)) {
                    $qtyKey = array_search('transactionQTY', array_column($fieldNameDetails, 'fieldName'));
                    $locKey = array_search('companyLocalWacAmount', array_column($fieldNameDetails, 'fieldName'));
                    $rptKey = array_search('companyReportingWacAmount', array_column($fieldNameDetails, 'fieldName'));
                    $isLocalCost = false;
                    $isRptCost = false;
                    $grandTotalLocal = 0;
                    $grandTotalRpt = 0;
                    $count = count($caption);
                    if (in_array("companyLocalWacAmount", $fieldName)) {
                        $isLocalCost = true;
                    }
                    if (in_array("companyReportingWacAmount", $fieldName)) {
                        $isRptCost = true;
                    }
                    $category = array();
                    foreach ($output as $val) {
                        $SubCatkey = array_search($val['subcategoryID'], array_column($subSubCategories, 'itemCategoryID'));
                        $val['subCategory'] = $subSubCategories[$SubCatkey]['description'];
                        if($val['subSubCategoryID']){
                            $key = array_search($val['subSubCategoryID'], array_column($subSubCategories, 'itemCategoryID'));
                            $val["subsubCategory"] = $subSubCategories[$key]['description'];
                        }else{
                            $val["subsubCategory"] = 'Uncategorized';
                        }

                        $secondaryUOM = getPolicyValues('SUOM', 'All');
                        if($secondaryUOM==1) {
                            $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][$val["itemSystemCode"]]['d'] = array("Item Code" => $val["itemSystemCode"], "Item Description" => $val["itemDescription"], "UOM" => $val["transactionUOM"], "SUOM" => $val["secondaryUOMID"], "Sales Price" => format_number($val["salesPrice"], $this->common_data['company_data']['company_default_decimal']));
                        }else{
                            $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][$val["itemSystemCode"]]['d'] = array("Item Code" => $val["itemSystemCode"], "Item Description" => $val["itemDescription"], "UOM" => $val["transactionUOM"], "Sales Price" => format_number($val["salesPrice"], $this->common_data['company_data']['company_default_decimal']));
                        }
                        $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][$val["itemSystemCode"]]['t'][] = $val;
                    }
                    if (!empty($category)) {
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>Main Category :- " . $key . "</div></td></tr>";
                            foreach ($mainCategory as $key2 => $subCategory) {
                                echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>Sub Category :- " . $key2 . "</div></td></tr>";
                                foreach ($subCategory as $key4 => $subsubCategory) {
                                    echo "<tr><td colspan='" . $count . "'><div class='subCategoryHead'>Sub Sub Category :- " . $key4 . "</div></td></tr>";
                                    foreach ($subsubCategory as $key3 => $item) {
                                        //echo "<tr><td colspan='" . $count . "'><div class='subCategoryHead'>" . $key3 . "</div></td></tr>";
                                        if (!empty($item)) {
                                            if (!empty($item['d'])) {
                                                echo '<tr><td colspan="' . $count . '"><div><table><tr>';
                                                foreach ($item['d'] as $key5 => $val5) {
                                                    echo "<td width='50px'><strong>" . $key5 . ":</strong></td>";
                                                    echo "<td width='100px'>" . $val5 . "</td>";
                                                }
                                                echo "</tr></td></table></div></tr>";
                                            }
                                            if (!empty($item['t'])) {
                                                echo "<tr class='reportTableHeader'>";
                                                foreach ($caption as $capval) {
                                                    if ($capval == "Local Currency") {
                                                        echo "<td colspan='2' style='text-align: center'><strong>" . $capval . "(" . $this->common_data['company_data']['company_default_currency'] . ")</strong></td>";
                                                    } else if ($capval == "Rpt Currency") {
                                                        echo "<td colspan='2' style='text-align: center'><strong>" . $capval . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")</strong></td>";
                                                    }else if ($capval == "Secondary QTY") {
                                                        if($secondaryUOM==1) {
                                                            echo "<td rowspan='2'><strong>" . $capval . "</strong></td>";
                                                        }else{

                                                        }
                                                    } else {
                                                        echo "<td rowspan='2'><strong>" . $capval . "</strong></td>";
                                                    }
                                                }
                                                echo "</tr>";
                                                echo "<tr class='reportTableHeader'>";
                                                foreach ($caption as $capval) {
                                                    if ($capval == "Local Currency") {
                                                        echo "<td><strong>Wac</strong></td>";
                                                        echo "<td><strong>Asset Value</strong></td>";
                                                    }
                                                    if ($capval == "Rpt Currency") {
                                                        echo "<td><strong>Wac</strong></td>";
                                                        echo "<td><strong>Asset Value</strong></td>";
                                                    }
                                                }
                                                echo "</tr>";
                                            }
                                            if (!empty($item['t'])) {
                                                $total = 0;
                                                $totalQty = 0;
                                                $SumtotalQty = 0;
                                                $localAssetvalue = 0;
                                                $rptAssetvalue = 0;
                                                foreach ($item['t'] as $key5 => $val5) {
                                                    echo "<tr class='hoverTr'>";
                                                    foreach ($fieldNameDetails as $key => $value) {
                                                        if ($value["fieldName"] == "transactionQTY") {
                                                            $totalQty += $val5[$value["fieldName"]];
                                                        }

                                                        if ($value["fieldName"] == "SUOMQty") {
                                                            if($secondaryUOM==1) {
                                                                $SumtotalQty += $val5[$value["fieldName"]];
                                                            }else{

                                                            }
                                                        }

                                                        if ($value["isDecimalPlaceAllowed"]) {
                                                            if ($value["fieldName"] == "companyLocalWacAmount") {
                                                                echo "<td class='" . $value["textAlign"] . "'>" . format_number($val5["avgCompanyLocalAmount"], $val5[$value["fieldName"] . 'DecimalPlaces']) . "</td>";
                                                                $localAssetvalue += $val5["localCostAsset"];
                                                                $grandTotalLocal += $val5["localCostAsset"];
                                                            } else if ($value["fieldName"] == "companyReportingWacAmount") {
                                                                echo "<td class='" . $value["textAlign"] . "'>" . format_number($val5["avgCompanyReportingAmount"], $val5[$value["fieldName"] . 'DecimalPlaces']) . "</td>";
                                                                $rptAssetvalue += $val5["rptCostAsset"];
                                                                $grandTotalRpt += $val5["rptCostAsset"];
                                                            } else {
                                                                echo "<td class='" . $value["textAlign"] . "'>" . format_number($val5[$value["fieldName"]], 2) . "</td>";
                                                            }
                                                        } else {
                                                            if ($value["fieldName"] == "documentSystemCode") {
                                                                if ($type == 'html') {
                                                                    echo '<td class="' . $value["textAlign"] . '"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val5["documentID"] . '\',' . $val5["documentAutoID"] . ')">' . $val5[$value["fieldName"]] . '</a></td>';
                                                                } else {
                                                                    echo '<td class="' . $value["textAlign"] . '">' . $val5[$value["fieldName"]] . '</td>';
                                                                }
                                                            } else {
                                                                if ($value["fieldName"] == "documentDate" && $val5[$value["fieldName"]] == '1970-01-01') {
                                                                    echo "<td class='" . $value["textAlign"] . "'>-</td>";
                                                                } else if ($value["fieldName"] == "documentDate") {
                                                                    echo "<td class='" . $value["textAlign"] . "'>" . convert_date_format($val5[$value["fieldName"]]) . "</td>";
                                                                } else {
                                                                    echo "<td class='" . $value["textAlign"] . "'>" . $val5[$value["fieldName"]] . "</td>";
                                                                }
                                                            }
                                                        }

                                                        if ($value["fieldName"] == "companyLocalWacAmount") {
                                                            echo "<td class='text-right'>" . format_number($val5["localCostAsset"], $val5[$value["fieldName"] . 'DecimalPlaces']) . "</td>";
                                                        }
                                                        if ($value["fieldName"] == "companyReportingWacAmount") {
                                                            echo "<td class='text-right'>" . format_number($val5["rptCostAsset"], $val5[$value["fieldName"] . 'DecimalPlaces']) . "</td>";
                                                        }
                                                    }
                                                    echo "</tr>";
                                                }
                                            }
                                            if($secondaryUOM==1) {
                                                echo "<tr><td  colspan='" . ($qtyKey-1) . "'></td><td class='reporttotal text-right'>" . format_number($SumtotalQty, 2) . "</td><td class='reporttotal text-right'>" . format_number($totalQty, 2) . "</td>";
                                            }else{
                                                echo "<tr><td  colspan='" . ($qtyKey) . "'></td><td class='reporttotal text-right'>" . format_number($totalQty, 2) . "</td>";
                                            }

                                            if ($isLocalCost) {
                                                echo "<td></td><td class='reporttotal text-right'>" . format_number($localAssetvalue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                            if ($isRptCost) {
                                                echo "<td></td><td class='reporttotal text-right'>" . format_number($rptAssetvalue, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            echo "</tr>";
                                            echo "<tr><td  colspan='" . $count . "'>&nbsp;</td></tr>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    <tr>
                        <?php

                        if ($isLocalCost) {

                            echo "<td class='text-right' colspan='" . ($locKey + 1) . "'><strong>Grand Total</strong></td>";
                        } else if ($isRptCost) {
                            echo "<td class='text-right' colspan='" . ($rptKey + 1) . "'><strong>Grand Total</strong></td>";
                        }
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
                    $norecfound = $this->lang->line('common_no_records_found');
                    echo warning_message($norecfound);/*No Records Found!*/
                }
                ?>
            </table>
        </div>
    </div>
</div>
