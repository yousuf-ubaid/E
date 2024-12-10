<!---- =============================================
-- File Name : erp_item_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item counting.

-- REVISION HISTORY
-- Modified By : 15-05-2017 sub item configuration in the reports | to incorporate sub item master only in item counting report
-- Modified By : 11-02-2020 Server Side excel generation
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$showSubItem = false;
$isRptCost = false;
$isLocCost = false;
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
$extraColumn = 0;
$asof= $this->lang->line('transaction_as_of');
if (isset($fieldName)) {
    if (in_array("AssetValueRpt", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("AssetValueLocal", $fieldName)) {
        $isLocCost = true;
    }
    if (in_array("barcode", $fieldName)) {
        $barcode = true;
        $extraColumn++;
    }
    if (in_array("seconeryItemCode", $fieldName)) {
        $seconeryItemCode = true;
        $extraColumn++;
    }
    if (in_array("partNo", $fieldName)) {
        $partNo = true;
        $extraColumn++;
    }
}

if (isset($isSubItemExist) && $isSubItemExist == 1) {
    $showSubItem = true;
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
            // echo export_buttons('tbl_item_counting', 'Item Counting', false, true);
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
                <table class="borderSpace report-table-condensed" id="demo">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
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
                        <th><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                        <th><?php echo $this->lang->line('common_Location');?></th><!--Location-->
                        <th><?php echo $this->lang->line('transaction_qty_in_hand');?></th><!--Qty in Hand-->
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
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $this->lang->line('transaction_main_category') . ":- " . $key . "</div></td></tr>";
                            foreach ($mainCategory as $key2 => $subCategory) {
                                echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $this->lang->line('transaction_sub_category') . ":- " . $key2 . "</div></td></tr>";
                                foreach ($subCategory as $key3 => $subsubCategory) {
                                    echo "<tr><td colspan='" . $count . "'><div class='subCategoryHead'>" . $this->lang->line('erp_item_master_sub_sub_category') . ":- " . $key3 . "</div></td></tr>";
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
                                    if($itemSecondaryCodePolicy){
                                        $itemCode=$item["seconeryItemCode"];
                                    }else{
                                        $itemCode=$item["itemSystemCode"];
                                    }
                                    echo "<tr class='hoverTr'>";
                                    echo "<td>" . $itemCode . "</td>";
                                    echo "<td>" . $item["itemDescription"] . "</td>";
                                    if ($barcode) {
                                        echo "<td>" . $item["barcode"] . "</td>";
                                    }
                                    if ($seconeryItemCode) {
                                        echo "<td>" . $item["seconeryItemCode"] . "</td>";
                                    }
                                    if ($partNo) {
                                        echo "<td>" . $item["partNo"] . "</td>";
                                    }
                                    echo "<td>" . $item["defaultUnitOfMeasure"] . "</td>";
                                    echo "<td>" . $item["wareHouseLocation"] . "</td>";
                                    echo "<td class='text-right'>" . ($item["transactionQTY"]) . "</td>";
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
                                $colspan=5+$extraColumn;
                                echo "<td colspan='$colspan'></td>";
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
                        $colspan=5+$extraColumn;
                        $grandtot=$this->lang->line('common_grand_total');
                        echo "<td colspan='$colspan'><strong>$grandtot<!--Grant Total--></strong></td>";
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
<script>
    /* $(document).ready(function() {
     $('#demo').dragtable();
     });*/
</script>