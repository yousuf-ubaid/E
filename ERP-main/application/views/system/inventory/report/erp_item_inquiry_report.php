<!---- =============================================
-- File Name : erp_item_inquiry_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 12 - February 2017
-- Description : This file contains Item inquiry.

-- REVISION HISTORY
-- Modified : 12-02-2020 Server Side excel generation
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$asof = $this->lang->line('transaction_as_of');
$tot = $this->lang->line('common_total');
$norecfo = $this->lang->line('common_no_records_found');
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
if (isset($fieldName)) {

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
            echo export_buttons('tbl_item_inquiry_report', 'Item Inquiry Report', false, true);
        } ?>
    </div>
</div>
<div id="tbl_item_inquiry_report">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_inquiry_report'); ?></div>
            <!--Item Inquiry Report-->
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . current_date(false) ?></div>
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
                <div class="table-responsive">
                    <table class="borderSpace report-table-condensed table-responsive" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                            <!--Item Code-->
                            <th rowspan="2"><?php echo $this->lang->line('common_description'); ?></th>
                            <!--Description-->
                            <?php if ($barcode) { ?>
                                <th rowspan="2"><?php echo $this->lang->line('transaction_barcode');?></th><!--barcode-->
                            <?php } ?>
                            <?php if ($seconeryItemCode) { ?>
                                <th rowspan="2"><?php echo $this->lang->line('erp_item_master_secondary_code');?></th><!--Secondary Code-->
                            <?php } ?>
                            <?php if ($partNo) { ?>
                                <th rowspan="2"><?php echo $this->lang->line('transaction_part_no');?></th><!--Part No-->
                            <?php } ?>
                            <th rowspan="2"><?php echo $this->lang->line('common_uom'); ?></th><!--UoM-->
                            <th colspan="<?php echo count($warehouse) + 1; ?>">
                                <?php echo $this->lang->line('transaction_qty_in_hand'); ?><!--Qty in Hand--></th>
                            <th rowspan="2"><?php echo $this->lang->line('transaction_item_on_order'); ?> </th>
                            <!--On Order-->
                            <th rowspan="2"><?php echo $this->lang->line('transaction_commited'); ?></th><!--Commited-->
                            <th rowspan="2"><?php echo $this->lang->line('transaction_in_un_approved_documnet'); ?></th>
                            <!--In un approved document-->
                            <th rowspan="2"><?php echo $this->lang->line('transaction_net_stock'); ?></th>
                            <!--Net Stock-->
                            <th rowspan="2"><?php echo $this->lang->line('transaction_min_stock'); ?></th>
                            <!--Min Stock-->
                            <th rowspan="2"><?php echo $this->lang->line('transaction_item_below_stock'); ?> </th>
                            <!--Item below Stock-->
                            <th rowspan="2"><?php echo $this->lang->line('erp_item_master_recorder_level'); ?> </th>
                            <!--Reorder Level-->
                        </tr>
                        <tr>

                            <?php
                            if (!empty($warehouse)) {
                                foreach ($warehouse as $val) {
                                    echo "<th>" . $val["wareHouseCode"] . " | " . $val["wareHouseDescription"] . "</th>";
                                }
                                echo "<th>$tot<!--Total--></th>";
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        foreach ($output as $val) {
                            $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }
                        if (!empty($category)) {
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td colspan='7'><div class='mainCategoryHead'>Main category:- " . $key . "</div></td></tr>";
                                $subtotal = array();
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td colspan='7'><div class='mainCategoryHead' style='margin-left:15px'>Sub category:- " . $key2 . "</div></td></tr>";
                                    foreach ($subCategory as $key3 => $subsubCategory) {
                                        echo "<tr><td colspan='7'><div class='subCategoryHead' style='margin-left:15px'>Sub Sub category:- " . $key3 . "</div></td></tr>";
                                    if (!empty($subsubCategory)) {
                                        foreach ($subsubCategory as $val) {
                                            if($itemSecondaryCodePolicy){
                                                $itemCode=$val["seconeryItemCode"];
                                            }else{
                                                $itemCode=$val["itemSystemCode"];
                                            }
                                            echo "<tr class='hoverTr'>";
                                            echo "<td> <div style='margin-left:30px'>" . $itemCode . "</div></td>";
                                            echo "<td>" . $val["itemDescription"] . "</td>";
                                            if ($barcode) {
                                                echo "<td>" . $val["barcode"] . "</td>";
                                            }
                                            if ($seconeryItemCode) {
                                                echo "<td>" . $val["seconeryItemCode"] . "</td>";
                                            }
                                            if ($partNo) {
                                                echo "<td>" . $val["partNo"] . "</td>";
                                            }
                                            echo "<td>" . $val["defaultUnitOfMeasure"] . "</td>";
                                            if (!empty($warehouse)) {
                                                foreach ($warehouse as $val2) {
                                                    echo "<td class='text-right'>" . number_format($val[$val2["wareHouseCode"]],4) . "</td>";
                                                }
                                                echo "<td class='text-right'>" . number_format($val["total"],4) . "</td>";
                                            }
                                            if ($type == 'html') {
                                                echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $val["itemAutoID"] . '\',\'' . $val["itemSystemCode"] . '\',\'PO\')">' . round($val["poCurrentStock"], 2) . '</a></td>';
                                            } else {
                                                echo '<td class="text-right">' . number_format($val["poCurrentStock"],4) . '</td>';
                                            }
                                            echo "<td class='text-right'>" . number_format($val["coCurrentStock"], 4) . "</td>";
                                            if ($type == 'html') {
                                                echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $val["itemAutoID"] . '\',\'' . $val["itemSystemCode"] . '\',\'All\')">' . round($val["unapprovedDoc"], 2) . '</a></td>';
                                            } else {
                                                echo '<td class="text-right">' . round($val["unapprovedDoc"], 2) . '</td>';
                                            }
                                            echo '<td class="text-right">' . number_format($val["netStock"]) . '</td>';
                                            echo '<td class="text-right">' . number_format($val["minimumQty"]) . '</td>';
                                            echo '<td class="text-right">' . number_format($val["netStock"] - $val["minimumQty"]) . '</td>';
                                            echo '<td class="text-right">' . number_format($val["reorderPoint"]) . '</td>';
                                            echo "</tr>";
                                        }
                                    }
                                }}
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } else {
                echo warning_message($norecfo);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>