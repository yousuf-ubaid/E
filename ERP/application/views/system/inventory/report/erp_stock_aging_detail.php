<!---- =============================================
-- File Name : erp_item_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item counting.

-- REVISION HISTORY
-- Modified By : 15-05-2017 sub item configuration in the reports | to incorporate sub item master only in item counting report
-- Modified By : 12-02-2020 Server Side excel generation
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$isRptCost = false;
$islocal = false;
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
$extraColumn=0;
$asof = $this->lang->line('transaction_as_of');

if (isset($isSubItemExist) && $isSubItemExist == 1) {
    $showSubItem = true;
}
if (isset($fieldName)) {

    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;

    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $islocal = true;
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
?>


<div class="row">
    <div class="col-md-12">
        <?php ?>
        <a href="#" type="button" class="btn btn-excel btn-xs pull-right" style="margin-left: 2px" onclick="excel_export_stock_aging()">
            <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
        </a>
        <?php if ($type == 'html') {
            echo export_buttons('tbl_item_counting', 'Item Counting', false, false);
        } ?>
    </div>
</div>
<div id="tbl_item_counting">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor">Stock Aging Report</div><!--Item Counting-->
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>As of : </strong>" . $asofdateconverted ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Warehouse:</i></strong> <?php echo join(" | ", $warehouse) ?>
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
                <table class="borderSpace report-table-condensed" id="stock_aging_detail">
                    <thead class="report-header">
                    <tr>
                        <th rowspan="3"><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                        <th rowspan="3">Item Description</th>
                        <?php if ($barcode) { ?>
                            <th rowspan="3"><?php echo $this->lang->line('transaction_barcode');?></th><!--Barcode-->
                        <?php } ?>
                        <?php if ($seconeryItemCode) { ?>
                            <th rowspan="3"><?php echo $this->lang->line('erp_item_master_secondary_code');?></th><!--Secondary Code-->
                        <?php } ?>
                        <?php if ($partNo) { ?>
                            <th rowspan="3"><?php echo $this->lang->line('transaction_part_no');?></th><!--Part No-->
                        <?php } ?>
                        <th rowspan="3">UOM</th>
                        <th rowspan="3">Qty</th>

                        <?php if ($islocal) { ?>
                            <th colspan="4"><?php echo $this->common_data["company_data"]["company_default_currency"] ?></th>
                        <?php } ?>
                        <?php if ($isRptCost) { ?>
                            <th colspan="4"><?php echo $this->common_data["company_data"]["company_reporting_currency"] ?></th>
                        <?php } ?>

                        <?php
                        if (!empty($agingcolumn)) {
                            foreach ($agingcolumn as $val2) {
                                echo "<th colspan='2'>" . $val2 . "</th>";
                            }
                        }
                        ?>

                    </tr>

                    <tr>
                        <th colspan="2">WAC</th>
                        <th colspan="2">Total</th>
                        <?php
                        if (!empty($aging)) {

                            foreach ($aging as $val2) {
                                echo "<th colspan='1'>Qty</th>";
                                echo "<th colspan='1'>Value</th>";
                            }
                        }
                        ?>

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
                                $subTotal = array();
                                $grandtotal2 = 0;
                                foreach ($subCategory as $key3 => $subsubCategory) {
                                    echo "<tr><td colspan='" . $count . "'><div class='subCategoryHead'>Sub Sub Category:- " . $key3 . "</div></td></tr>";
                                    $totallocal = array();
                                    $localAssetvalue = 0;
                                    $rptAssetvalue = 0;
                                    $rptAssetvalue = 0;

                                    foreach ($subsubCategory as $item) {
                                        $total = 0;
                                        $grandTotal["totalss"][] = $item["total"];
                                        $grandtotal2 += $item["total"];
                                        if($itemSecondaryCodePolicy){
                                            $itemCode=$item["secondaryItemCode"];
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
                                        echo "<td>" . $item["UnitShortCode"] . "</td>";
                                        echo "<td style='text-align: right'>" . (((int)$item["totQty"] - (int)$item['outputtoalqty'])) . "</td>";
                                        if ($islocal) {
                                            echo "<td style='text-align: right'>" . number_format((float)$item['WacAmount'], $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($isRptCost) {
                                            echo "<td style='text-align: right'>" . number_format((float)$item['WacAmount'], $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }


                                        echo "<td> </td>";
                                        if ($islocal) {
                                            echo "<td style='text-align: right'>" . number_format((float)$item['total'], $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($isRptCost) {
                                            echo "<td style='text-align: right'>" . number_format((float)$item['total'], $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }

                                        echo "<td> </td>";
                                        foreach ($aging as $value) {
                                            $subTotal[$value][] = $item['valueaging' . $value];
                                            echo '<td class="text-right">' . ($item['qtyaging' . $value]) . '</td>';
                                            if ($islocal) {
                                                echo '<td class="text-right">' . number_format((float)$item['valueaging' . $value], $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                            }
                                            if ($isRptCost) {
                                                echo '<td class="text-right">' . number_format((float)$item['valueaging' . $value], $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                            }

                                        }
                                        echo "</tr>";


                                    }


                                }
                                $colspan= 6+ $extraColumn;
                                echo "<td colspan='$colspan'><strong>Total<!--Grant Total--></strong></td>";

                                if ($islocal) {
                                    echo '<td class="reporttotal text-right"><strong>' . format_number($grandtotal2, $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                                }
                                if ($isRptCost) {
                                    echo '<td class="reporttotal text-right"><strong>' . format_number($grandtotal2, $this->common_data['company_data']['company_reporting_decimal']) . '</strong></td>';
                                }

                                echo '<td class="text-right"> </td>';
                                foreach ($aging as $value) {
                                    echo '<td class="text-right"> </td>';
                                    if ($islocal) {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal[$value]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    if ($isRptCost) {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal[$value]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }


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
                        $grandtot = $this->lang->line('common_grand_total');
                        echo "<td colspan='$colspan'><strong>$grandtot<!--Grant Total--></strong></td>";
                        if ($islocal) {
                            echo '<td class="reporttotal text-right"><strong>' . format_number(array_sum($grandTotal["totalss"]), $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                        }
                        if ($isRptCost) {
                            echo '<td class="reporttotal text-right"><strong>' . format_number(array_sum($grandTotal["totalss"]), $this->common_data['company_data']['company_reporting_decimal']) . '</strong></td>';
                        }

                        echo '<td class="text-right"> </td>';
                        foreach ($aging as $valuenew) {
                            echo '<td class="text-right"> </td>';
                            if ($islocal) {
                                echo '<td class="reporttotal text-right"><strong>' . number_format($grandtotal[$valuenew], $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                            }
                            if ($isRptCost) {
                                echo '<td class="reporttotal text-right"><strong>' . number_format($grandtotal[$valuenew], $this->common_data['company_data']['company_reporting_decimal']) . '</strong></td>';

                            }

                        }
                        ?>
                    </tr>
                </table>
                <?php
            } else {
                $norecfound = $this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>
<script>
    var extraColumn = <?php echo $extraColumn; ?>;
    $('#stock_aging_detail').tableHeadFixer({
        head: true,
        foot: true,
        left: 4+extraColumn,
        right: 0,
        'z-index': 10
    });
    applyAlternateColor();

    function applyAlternateColor() {
        const rows = document.querySelectorAll("#stock_aging_detail tbody tr");
        let toggleClass = false;

        rows.forEach(function(row) {
            if (row.classList.contains("hoverTr")) {
                toggleClass = !toggleClass;
                row.style.backgroundColor = toggleClass ? "#efeffc" : "";
            } else {
                row.style.backgroundColor = "";
                toggleClass = false;
            }
        });
    }
</script>