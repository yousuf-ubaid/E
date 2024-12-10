<!---- =============================================
-- File Name : erp_item_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item counting.

-- REVISION HISTORY
-- Modified By : 15-05-2017 sub item configuration in the reports | to incorporate sub item master only in item counting report
-- Modified By : 20-12-2019 sub item configuration in the reports | to add secondary UOM and Secondary Qty in report view
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$showSubItem = false;
$isRptCost = false;
$isLocCost = false;

$asof= $this->lang->line('transaction_as_of');
if (isset($fieldName)) {
    if (in_array("AssetValueRpt", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("AssetValueLocal", $fieldName)) {
        $isLocCost = true;
    }
}

if (isset($isSubItemExist) && $isSubItemExist == 1) {
    $showSubItem = true;
}
?>

<div class="row">
    <div class="col-md-12">
        <?php
        if ($type == 'html') {
            echo export_buttons('tbl_item_counting', 'Item Counting', true, false);
        } ?>
    </div>
</div>
<div id="tbl_item_counting">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_counting');?></div><!--Item Counting-->
            <div
                class="text-center reportHeaderColor"> <?php echo "<strong><!--As of-->$asof: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Warehouse:</i></strong> <?php echo join(" | ",$warehouse) ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="demo">
                    <thead class="report-header">
                    <tr>
                        <th>Item Code</th>
                        <th><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                        <th><?php echo $this->lang->line('common_Location');?></th><!--Location-->
                        <th><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                        <th><?php echo $this->lang->line('transaction_qty_in_hand');?></th><!--Qty in Hand-->
                        <th>Secondary UOM</th><!--Secondary UOM-->
                        <th>Secondary Qty</th><!--Secondary Qty-->
                        <?php if ($isLocCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost_in_local');?></th><!--Avg Cost Local-->
                            <th><?php echo $this->lang->line('transaction_asset_value_local');?></th><!--Asset Value Local-->
                        <?php } ?>
                        <?php if ($isRptCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost_rpt');?></th><!--Avg Cost Rpt-->
                            <th><?php echo $this->lang->line('transaction_asset_value_rpt');?></th><!--Asset Value Rpt-->
                        <?php } ?>
                        <th><?php echo $this->lang->line('transaction_physical_qty');?></th><!--Physical Qty-->
                    </tr>
                    </thead>
                    <?php
                    $count = 9;
                    $category = array();
                    foreach ($output as $val) {
                        $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][] = $val;
                    }
                    $totalcurrentQty = 0;
                    $totallocalAssetvalue = 0;
                    $totalRptAssetvalue = 0;
                    if (!empty($category)) {
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>Main Category:- " . $key . "</div></td></tr>";
                            foreach ($mainCategory as $key2 => $subCategory) {
                                echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>Sub Category:- " . $key2 . "</div></td></tr>";
                                foreach ($subCategory as $key3 => $subsubCategory) {
                                    echo "<tr><td colspan='" . $count . "'><div class='subCategoryHead'>Sub Sub Category:- " . $key3 . "</div></td></tr>";
                                    $currentQty = 0;
                                    $localAssetvalue = 0;
                                    $rptAssetvalue = 0;
                                    $rptAssetvalue = 0;
                                    foreach ($subsubCategory as $item) {
                                        $totalcurrentQty += $item["transactionQTY"];
                                        $currentQty += $item["transactionQTY"];
                                        if ($isLocCost) {
                                            $localAssetvalue += $item["AssetValueLocal"];
                                            $totallocalAssetvalue += $item["AssetValueLocal"];
                                        }
                                        if ($isRptCost) {
                                            $rptAssetvalue += $item["AssetValueRpt"];
                                            $totalRptAssetvalue += $item["AssetValueRpt"];
                                        }
                                        if(empty($item['SUOMCode'])) {
                                            $item['SUOMCode'] = '&nbsp;&nbsp;&nbsp; - ';
                                        }
                                        echo "<tr class='hoverTr'>";
                                        echo "<td>" . $item["itemSystemCode"] . "</td>";
                                        echo "<td>" . $item["itemDescription"] . "</td>";
                                        echo "<td>" . $item["wareHouseLocation"] . "</td>";
                                        echo "<td>" . $item["transactionUOM"] . "</td>";
                                        echo "<td class='text-right'>" . format_number($item["transactionQTY"], 2) . "</td>";
                                        echo "<td>" . $item["SUOMCode"] . "</td>";
                                        echo "<td class='text-right'>" . format_number($item["SUOMQty"], 2) . "</td>";
                                        if ($isLocCost) {
                                            echo "<td class='text-right'>" . format_number($item["companyLocalWacAmount"], 4) . "</td>";
                                            echo "<td class='text-right'>" . format_number($item["AssetValueLocal"], $item["companyLocalCurrencyDecimalPlaces"]) . "</td>";
                                        }
                                        if ($isRptCost) {
                                            echo "<td class='text-right'>" . format_number($item["companyReportingWacAmount"], 4) . "</td>";
                                            echo "<td class='text-right'>" . format_number($item["AssetValueRpt"], $item["companyReportingCurrencyDecimalPlaces"]) . "</td>";
                                        }
                                        echo "<td style='vertical-align: bottom'><hr style='margin-top: 0px; margin-bottom: 0px;border-top: 1px solid #000000;'></td>";
                                        echo "</tr>";

                                        /** Added on 15-05-2017 to incorporate sub item master only in item counting report */
                                        if ($item["isSubitemExist"] == 1 && $showSubItem) {
                                            /** get sub item list from helper */
                                            $itemAutoID = $item['itemAutoID'];
                                            $warehouseID = $item['wareHouseAutoID'];
                                            $subItemList = load_subItem_notSold_report($itemAutoID, $warehouseID);


                                            if (!empty($subItemList)) {
                                                $i = 1;
                                                foreach ($subItemList as $subItem) {
                                                    ?>
                                                    <tr>
                                                        <td style="padding-left: 15px !important; color:#3173ac;">
                                                            <?php //echo $subItem['itemAutoID'] ?>
                                                            <?php echo $i . '.';
                                                            $i++; ?>
                                                            <?php echo $subItem['subItemCode'] ?> -
                                                            <?php echo $subItem['description'] ?>
                                                            - <?php echo $subItem['productReferenceNo'] ?>

                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }

                                        }
                                        /**end of Sub item config */

                                    }
                                    echo "<tr>";
                                    echo "<td colspan='7'></td>";
                                    //echo "<td class='reporttotal text-right'>" . format_number($currentQty, 2) . "</td>";
                                    if ($isLocCost) {
                                        echo "<td></td>";
                                        echo "<td class='reporttotal text-right'>" . format_number($localAssetvalue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    if ($isRptCost) {
                                        echo "<td></td>";
                                        echo "<td class='reporttotal text-right'>" . format_number($rptAssetvalue, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
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
                    <tr>
                        <?php
                        $grandtot=$this->lang->line('common_grand_total');
                        echo "<td colspan='7'><strong>$grandtot<!--Grant Total--></strong></td>";
                        //echo "<td class='reporttotal text-right'>" . format_number($totalcurrentQty, 2) . "</td>";
                        if ($isLocCost) {
                            echo "<td></td>";
                            echo "<td class='reporttotal text-right'>" . format_number($totallocalAssetvalue, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
                        if ($isRptCost) {
                            echo "<td></td>";
                            echo "<td class='reporttotal text-right'>" . format_number($totalRptAssetvalue, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        ?>
                    </tr>
                </table>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>