<!---- =============================================
-- File Name : erp_item_valuation_summary_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item valuation summary.

-- REVISION HISTORY
-- Modified : 11-02-2020 Server Side excel generation
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$currency = "";
$isRptCost = false;
$isLocCost = false;
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
$asof=$this->lang->line('transaction_as_of');

if (isset($fieldName)) {
    if (in_array("companyReportingWacAmount", $fieldName)) {
        $currency = "companyReportingWacAmount";
        $isRptCost = true;
    }
    if (in_array("companyLocalWacAmount", $fieldName)) {
        $currency = "companyLocalWacAmount";
        $isLocCost = true;
    }
    if (in_array("barcode", $fieldName)) {
        $barcode = true;
    }
    if (in_array("seconeryItemCode", $fieldName)) {
        $seconeryItemCode = true;
    }
    if (in_array("partNo", $fieldName)) {
        $partNo = true;
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <?php
        if ($type == 'html') { ?>
            <a href="#" type="button" class="btn btn-excel btn-xs pull-right" style="margin-left: 2px" onclick="excel_export()">
                <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
            </a>
            <?php
            echo export_buttons('tbl_item_valuation_summary', 'Item Valuation Summary', false, true);
        } ?>
    </div>
</div>

<div id="tbl_item_valuation_summary">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>

            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_valuation_summary');?></div><!--Item Valuation Summary-->

            <div class="text-center reportHeaderColor"> <?php echo "<strong><!--As Of-->$asof: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong><?php echo $this->lang->line('common_filter');?> <i class="fa fa-filter"></i></strong><br>
            <strong><i><?php echo $this->lang->line('common_warehouse');?>:</i></strong> <?php echo join(" | ",$warehouse) ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) {
                $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                if($itemSecondaryCodePolicy){
                    $itemCodeLabel="Secondary Code";
                }else{
                    $itemCodeLabel=$this->lang->line('transaction_common_item_code');
                }

                ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('transaction_common_item_code');  ?></th>
                        <th><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                        <?php if ($barcode) { ?>
                            <th><?php echo $this->lang->line('transaction_barcode');?></th><!--transaction_barcode-->
                        <?php } ?>
                        <?php if ($seconeryItemCode) { ?>
                            <th><?php echo $this->lang->line('erp_item_master_secondary_code');?></th><!--Secondary Code-->
                        <?php } ?>
                        <?php if ($partNo) { ?>
                            <th><?php echo $this->lang->line('transaction_part_no');?></th><!--Part No-->
                        <?php } ?>
                        <th><?php echo $this->lang->line('transaction_on_hand');?> </th><!--On Hand-->
                        <?php if ($isLocCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost');?> (<?php echo $this->common_data['company_data']['company_default_currency'] ?><!--Avg Cost-->
                                )
                            </th>
                            <th><?php echo $this->lang->line('common_total_value');?>
                                (<?php echo $this->common_data['company_data']['company_default_currency'] ?>)<!--Total Value-->
                            </th>
                            <th>% <?php echo $this->lang->line('transaction_of_total');?></th><!--of Total-->
                        <?php } ?>
                        <?php if ($isRptCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost');?>(<?php echo $this->common_data['company_data']['company_reporting_currency'] ?><!--Avg Cost-->
                                )
                            </th>
                            <th><?php echo $this->lang->line('common_total_value');?>
                                (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)<!--Total Value-->
                            </th>
                            <th>% <?php echo $this->lang->line('transaction_of_total');?></th><!--of Total-->
                        <?php } ?>

                        <th><?php echo $this->lang->line('transaction_sales_price');?></th><!--Sales Price-->
                        <th><?php echo $this->lang->line('transaction_retail_value');?></th><!--Retail Value-->
                        <th>% <?php echo $this->lang->line('transaction_of_total_retail');?> </th><!--of Total Retail-->
                        <th>% <?php echo $this->lang->line('transaction_margin');?></th><!--Margin-->
                    </tr>
                    </thead>
                    <?php
                    $count = 12;
                    $category = array();
                    //echo '<pre>';print_r($output); echo '</pre>'; die();
                    foreach ($output as $val) {
                        $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][] = $val;
                    }
                    $totalcurrentQty = 0;
                    $totallocalAssetvaluePer = 0;
                    $totalRptAssetvaluePer = 0;
                    $totalretailValuePer = 0;
                    if (!empty($category)) {
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $this->lang->line('transaction_main_category') . ":- " . $key . "</div></td></tr>";
                            foreach ($mainCategory as $key2 => $subCategory) {
                                echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead'>" . $this->lang->line('transaction_sub_category') . ":- " . $key2 . "</div></td></tr>";
                                foreach ($subCategory as $key3 => $subsubCategory) {
                                    echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='subCategoryHead'>" . $this->lang->line('erp_item_master_sub_sub_category') . ":- " . $key3 . "</div></td></tr>";
                                $currentQty = 0;
                                $localAssetvalue = 0;
                                $localAssetvaluePer = 0;
                                $rptAssetvalue = 0;
                                $rptAssetvaluePer = 0;
                                $retailValue = 0;
                                $retailValuePer = 0;
                                $rptAssetvalue = 0;
                                foreach ($subsubCategory as $item) {
                                    $totalcurrentQty += (int)$item["transactionQTY"];
                                    $currentQty += (int)$item["transactionQTY"];
                                    if ($isLocCost) {
                                        $localAssetvalue += $item["AssetValueLocal"];
                                        $localAssetvaluePer += ($TotalAssetValue["TotalAssetValueLocal"] > 0 || $TotalAssetValue["TotalAssetValueLocal"] < 0 ? (($item["AssetValueLocal"] / $TotalAssetValue["TotalAssetValueLocal"]) * 100) : 0);
                                        $totallocalAssetvaluePer += ($TotalAssetValue["TotalAssetValueLocal"] > 0 || $TotalAssetValue["TotalAssetValueLocal"] < 0 ? (($item["AssetValueLocal"] / $TotalAssetValue["TotalAssetValueLocal"]) * 100) : 0);
                                    }
                                    if ($isRptCost) {
                                        $rptAssetvalue += $item["AssetValueRpt"];
                                        $rptAssetvaluePer += ($TotalAssetValue["TotalAssetValueRpt"] > 0 || $TotalAssetValue["TotalAssetValueRpt"] < 0 ? (($item["AssetValueRpt"] / $TotalAssetValue["TotalAssetValueRpt"]) * 100) : 0);
                                        $totalRptAssetvaluePer += ($TotalAssetValue["TotalAssetValueRpt"] > 0 || $TotalAssetValue["TotalAssetValueRpt"] < 0 ? (($item["AssetValueRpt"] / $TotalAssetValue["TotalAssetValueRpt"]) * 100) : 0);
                                    }
                                    $retailValuePer += ($TotalAssetValue["TotalRetailValue"] > 0 ? (($item["RetailValue"] / $TotalAssetValue["TotalRetailValue"]) * 100) : 0);
                                    $totalretailValuePer += ($TotalAssetValue["TotalRetailValue"] > 0 ? (($item["RetailValue"] / $TotalAssetValue["TotalRetailValue"]) * 100) : 0);
                                    $retailValue += $item["RetailValue"];
                                    $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                                    if($itemSecondaryCodePolicy){
                                        $itemCode=$item["seconeryItemCode"];
                                    }else{
                                        $itemCode=$item["itemSystemCode"];
                                    }
                                    echo "<tr class='hoverTr'>";
                                    echo "<td><div style='margin-left: 30px'>" . $itemCode . "</div></td>";
                                    echo "<td><div style='margin-left: 30px'>" . $item["itemDescription"] . "</div></td>";
                                    if ($barcode) {
                                        echo "<td>" . $item["barcode"] . "</td>";
                                    }
                                    if ($seconeryItemCode) {
                                        echo "<td>" . $item["seconeryItemCode"] . "</td>";
                                    }
                                    if ($partNo) {
                                        echo "<td>" . $item["partNo"] . "</td>";
                                    }
                                    echo "<td class='text-right'>" . ($item["transactionQTY"]) . "</td>";
                                    if ($isLocCost) {
                                        echo "<td class='text-right'>" . format_number($item["companyLocalWacAmount"], $item["companyLocalWacAmountDecimalPlaces"]) . "</td>";
                                        if ($type == 'html') {
                                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["itemAutoID"] . '\',\'' . $item["itemSystemCode"] . '\',\'' . $currency . '\')">' . format_number($item["AssetValueLocal"], $item["companyLocalWacAmountDecimalPlaces"]) . '</a></td>';
                                        } else {
                                            echo '<td class="text-right">' . format_number($item["AssetValueLocal"], $item["companyLocalWacAmountDecimalPlaces"]) . '</td>';
                                        }
                                        echo "<td class='text-right'>" . ($TotalAssetValue["TotalAssetValueLocal"] > 0 || $TotalAssetValue["TotalAssetValueLocal"] < 0 ? format_number((($item["AssetValueLocal"] / $TotalAssetValue["TotalAssetValueLocal"]) * 100), $item["companyLocalWacAmountDecimalPlaces"]) : 0) . "</td>";
                                    }
                                    if ($isRptCost) {
                                        echo "<td class='text-right'>" . format_number($item["companyReportingWacAmount"], $item["companyReportingWacAmountDecimalPlaces"]) . "</td>";
                                        if ($type == 'html') {
                                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["itemAutoID"] . '\',\'' . $item["itemSystemCode"] . '\',\'' . $currency . '\')">' . format_number($item["AssetValueRpt"], $item["companyReportingWacAmountDecimalPlaces"]) . '</a></td>';
                                        } else {
                                            echo '<td class="text-right">' . format_number($item["AssetValueRpt"], $item["companyReportingWacAmountDecimalPlaces"]) . '</td>';
                                        }
                                        echo "<td class='text-right'>" . ($TotalAssetValue["TotalAssetValueRpt"] > 0 || $TotalAssetValue["TotalAssetValueRpt"] < 0 ? format_number((($item["AssetValueRpt"] / $TotalAssetValue["TotalAssetValueRpt"]) * 100), $item["companyReportingWacAmountDecimalPlaces"]) : 0) . "</td>";
                                    }

                                    echo "<td class='text-right'>" . format_number($item["salesPrice"], $item["companyLocalWacAmountDecimalPlaces"]) . "</td>";
                                    echo "<td class='text-right'>" . format_number($item["RetailValue"], $item["companyLocalWacAmountDecimalPlaces"]) . "</td>";
                                    echo "<td class='text-right'>" . ($TotalAssetValue["TotalRetailValue"] > 0 ? format_number((($item["RetailValue"] / $TotalAssetValue["TotalRetailValue"]) * 100), $item["companyLocalWacAmountDecimalPlaces"]) : 0) . "</td>";
                                    if ($isLocCost) {
                                        echo "<td class='text-right'>" . ($item["companyLocalWacAmount"] > 0 ? format_number((($item["salesPrice"] - $item["companyLocalWacAmount"]) / $item["companyLocalWacAmount"] * 100), $item["companyLocalWacAmountDecimalPlaces"]) : 0) . "</td>";
                                    }
                                    if ($isRptCost) {
                                        echo "<td class='text-right'>" . ($item["companyReportingWacAmount"] > 0 ? format_number((($item["salesPrice"] - $item["companyReportingWacAmount"]) / $item["companyReportingWacAmount"] * 100), $item["companyLocalWacAmountDecimalPlaces"]) : 0) . "</td>";
                                    }

                                    echo "</tr>";
                                }
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td></td>";
                                if ($barcode) {
                                    echo "<td></td>";
                                }
                                if ($seconeryItemCode) {
                                    echo "<td></td>";
                                }
                                if ($partNo) {
                                    echo "<td></td>";
                                }
                                echo "<td></td>";
                                //echo "<td class='reporttotal text-right'>" . format_number($currentQty, 2) . "</td>";
                                if ($isLocCost) {
                                    echo "<td></td>";
                                    echo "<td class='reporttotal text-right'>" . format_number($localAssetvalue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . format_number($localAssetvaluePer, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                }
                                if ($isRptCost) {
                                    echo "<td></td>";
                                    echo "<td class='reporttotal text-right'>" . format_number($rptAssetvalue, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . format_number($rptAssetvaluePer, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                }
                                echo "<td></td>";
                                echo "<td class='reporttotal text-right'>" . format_number($retailValue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                echo "<td class='reporttotal text-right'>" . format_number($retailValuePer, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                echo "</tr>";
                            }
                        }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                    </tr>
                    <tr>
                        <?php
                        $grandtot= $this->lang->line('common_grand_total');
                        echo "<td><strong><!--Grand Total-->$grandtot </strong></td>";
                        //echo "<td class='reporttotal text-right'>" . format_number($totalcurrentQty, 2) . "</td>";
                        echo "<td class='text-right'></td>";
                        if ($barcode) {
                            echo "<td></td>";
                        }
                        if ($seconeryItemCode) {
                            echo "<td></td>";
                        }
                        if ($partNo) {
                            echo "<td></td>";
                        }
                        echo "<td class='text-right'></td>";
                        if ($isLocCost) {
                            echo "<td></td>";
                            echo "<td class='reporttotal text-right'>" . format_number($TotalAssetValue["TotalAssetValueLocal"], $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            echo "<td class='reporttotal text-right'>" . format_number($totallocalAssetvaluePer, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
                        if ($isRptCost) {
                            echo "<td></td>";
                            echo "<td class='reporttotal text-right'>" . format_number($TotalAssetValue["TotalAssetValueRpt"], $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            echo "<td class='reporttotal text-right'>" . format_number($totalRptAssetvaluePer, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
                        echo "<td></td>";
                        echo "<td class='reporttotal text-right'>" . format_number($TotalAssetValue["TotalRetailValue"], $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        echo "<td class='reporttotal text-right'>" . format_number($totalretailValuePer, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        ?>
                    </tr>
                </table>
                <?php
            } else {
                echo warning_message("No Records Found!");
            }
            ?>
        </div>
    </div>
</div>