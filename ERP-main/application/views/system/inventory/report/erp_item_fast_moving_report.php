<!---- =============================================
-- File Name : erp_item_fast_moving_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item fast moving.

-- REVISION HISTORY
-- Modified : 11-02-2020 Server Side excel generation
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$isRptCost = false;
$isLocCost = false;
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
$extraColumn = 0;
$datefrom=$this->lang->line('transaction_date_from');
$dateto=$this->lang->line('transaction_date_to');
$norec=$this->lang->line('common_no_records_found');

if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("companyLocalAmount", $fieldName)) {
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
?>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($type == 'html') { ?>
            <a href="#" type="button" class="btn btn-excel btn-xs pull-right" style="margin-left: 2px" onclick="excel_export()">
                <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
            </a>
            <?php
            echo export_buttons('tbl_fast_moving_item', 'Item fast moving', false, true);
        } ?>
    </div>
</div>

<div id="tbl_fast_moving_item">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_fast_moving');?></div><!--Fast Moving Item-->
            <div

                    class="text-center reportHeaderColor"> <?php echo "<strong>$datefrom<!--Date From-->: </strong>" . $from . " - <strong>$dateto<!--Date To-->: </strong>" . $to ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong><?php echo $this->lang->line('common_filters'); ?> <i class="fa fa-filter"></i></strong><br>
            <strong><i><?php echo $this->lang->line('common_segment'); ?>:</i></strong> <?php echo join(",", $segmentfilter) ?>
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
                        <th><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
                        <th><?php echo $this->lang->line('transaction_item_name');?></th><!--Item Name-->
                        <?php if ($barcode) { ?>
                            <th><?php echo $this->lang->line('transaction_barcode');?></th><!--transaction_barcode-->
                        <?php } ?>
                        <?php if ($seconeryItemCode) { ?>
                            <th><?php echo $this->lang->line('erp_item_master_secondary_code');?></th><!--Secondary Code-->
                        <?php } ?>
                        <?php if ($partNo) { ?>
                            <th><?php echo $this->lang->line('transaction_part_no');?></th><!--Part No-->
                        <?php } ?>
                        <th><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->
                        <th><?php echo $this->lang->line('transaction_common_qty');?></th><!--Qty-->
                        <?php if ($isLocCost) { ?>
                            <th><?php echo $this->lang->line('transaction_total_sales');?>(<?php echo $this->common_data['company_data']['company_default_currency'] ?>
                                )<!--Total Sales-->
                            </th>
                        <?php } ?>
                        <?php if ($isRptCost) { ?>
                            <th><?php echo $this->lang->line('transaction_total_sales');?>(<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)<!--Total
                                Sales-->
                            </th>
                        <?php } ?>
                        <th><?php echo $this->lang->line('transaction_qty_in_hand');?></th><!--Qty in Hand-->
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
                            echo "<tr><td colspan='7'><div class='mainCategoryHead'>" . $this->lang->line('transaction_main_category') . ":- " . $key . "</div></td></tr>";
                            $subtotal = array();
                            foreach ($mainCategory as $key2 => $subCategory) {
                                echo "<tr><td colspan='7'><div class='mainCategoryHead' style='margin-left:15px'>" . $this->lang->line('transaction_sub_category') . ":- " . $key2 . "</div></td></tr>";
                                foreach ($subCategory as $key3 => $subsubCategory) {
                                    echo "<tr><td colspan='7'><div class='subCategoryHead' style='margin-left:15px'>" . $this->lang->line('erp_item_master_sub_sub_category') . ":- " . $key3 . "</div></td></tr>";
                                if (!empty($subCategory)) {
                                    foreach ($subsubCategory as $val) {
                                        if($itemSecondaryCodePolicy){
                                            $itemCode=$val["seconeryItemCode"];
                                        }else{
                                            $itemCode=$val["itemSystemCode"];
                                        }
                                        echo "<tr>";
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
                                        echo "<td>" . $val["UOM"] . "</td>";
                                        echo "<td class='text-right'>" . $val["transactionQTY"] . "</td>";
                                        if (!empty($fieldNameDetails)) {
                                            foreach ($fieldNameDetails as $val2) {
                                               //var_dump($val2["fieldName"]);
                                                if($val2["fieldName"] == "companyLocalAmount" || $val2["fieldName"] == "companyReportingAmount"){
                                                     $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                    echo "<td class='text-right'>" . format_number($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . "</td>";

                                                }
                                               // $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"][""]];
                                                //echo "<td class='text-right'>" . format_number($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . "</td>";
                                            }
                                        }

                                        echo "<td class='text-right'>" . $val["currentStock"] . "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }}
                            echo "<tr>";
                            $colspan=4+$extraColumn;
                            echo "<td colspan='$colspan'></td>";
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $val2) {
                                    if ($val2["fieldName"] == "companyLocalAmount") {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2["fieldName"]]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    if ($val2["fieldName"] == "companyReportingAmount") {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2["fieldName"]]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                }
                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                    </tbody>
                </table>

                <?php
            } else {
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
</script>