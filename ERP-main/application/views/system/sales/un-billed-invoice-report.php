<!---- =============================================
-- File Name : erp_inventory_unbilled_grv_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Unbilled GRV.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$isRptCost = false;
$isLocCost = false;

$as_of = $this->lang->line('transaction_as_of');
$no_rec_found = $this->lang->line('common_no_records_found');

if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("transactionAmount", $fieldName)) {
        $isLocCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if($type == 'html') { echo export_buttons('tbl_unbilled_grv', 'Unbilled GRV',true,true); } ?>
    </div>
</div>
<div id="tbl_unbilled_grv">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor"><strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong></div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('common_un_billed_invoice');?></div>
            <div class="text-center reportHeaderColor"> <?php echo "<strong>$as_of<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th rowspan="2"><?php echo $this->lang->line('transaction_doc_number');?></th><!--Doc Number-->
                        <th rowspan="2"><?php echo $this->lang->line('common_reference_number');?></th><!--Reference Number-->
                        <th rowspan="2"><?php echo $this->lang->line('transaction_doc_date');?> </th><!--Doc Date-->
                        <?php
                        if (!empty($caption)) {
                            foreach ($caption as $val) {
                                echo '<th colspan="4">' . $val . '</th>';
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        $currency=$this->lang->line('common_currency');
                        $grnvalue=$this->lang->line('common_do_value');
                        $invoiceval=$this->lang->line('transaction_invoice_value');
                        $balance=$this->lang->line('transaction_balance');
                        if (!empty($fieldName)) {
                            foreach ($fieldName as $val) {
                                echo '<th>'.$currency.'<!--Currency--></th>';
                                echo '<th>'.$grnvalue.'<!--GRN Value--></th>';
                                echo '<th>'.$invoiceval.'<!--Invoice Value--></th>';
                                echo '<th>'.$balance.'<!--Balance--></th>';
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <?php
                    $count = 6;
                    $category = array();
                    foreach ($output as $val) {
                        $category[$val["customerSystemCode"] . " - " . $val["customerName"]][] = $val;
                    }
                    $grandtotal = array();
                    if (!empty($category)) {
                        foreach ($category as $key => $supplierName) {
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                            $subtotal = array();
                            foreach ($supplierName as $key2 => $val) {
                                echo "<tr class='hoverTr'>";
                                if($type == 'html') {
                                    echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'DO\',' . $val["DOAutoID"] . ')">' . $val["DOCode"] . '</a></td>';
                                }
                                else{
                                    echo '<td>'. $val["DOCode"] . '</td>';
                                }
                                echo "<td>" . $val["referenceNo"] . "</td>";
                                echo "<td>" . $val["DODate"] . "</td>";
                                if (!empty($fieldName)) {
                                    foreach ($fieldName as $val2) {
                                        $subtotal['GRNValue' . $val2][] = (float)$val[$val2];
                                        $subtotal['invoiceValue' . $val2][] = (float)$val["sumOf" . $val2];
                                        $subtotal['Balance' . $val2][] = (float)$val["balance" . $val2];

                                        $grandtotal['GRNValue' . $val2][] = (float)$val[$val2];
                                        $grandtotal['invoiceValue' . $val2][] = (float)$val["sumOf" . $val2];
                                        $grandtotal['Balance' . $val2][] = (float)$val["balance" . $val2];
                                        echo "<td>" . $val["currency" . $val2] . "</td>";
                                        echo "<td class='text-right'>" . format_number($val[$val2],$val[$val2."DecimalPlaces"]) . "</td>";
                                        echo "<td class='text-right'>" . format_number($val["sumOf" . $val2],$val[$val2."DecimalPlaces"]) . "</td>";
                                        echo "<td class='text-right'>" . format_number($val["balance" . $val2],$val[$val2."DecimalPlaces"]) . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td colspan='3'></td>";
                            if (!empty($fieldName)) {
                                foreach ($fieldName as $val2) {
                                    echo "<td></td>";
                                    if($val2 == "companyLocalAmount") {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["GRNValue" . $val2]),$this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["invoiceValue" . $val2]),$this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["Balance" . $val2]),$this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    if($val2 == "companyReportingAmount") {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["GRNValue" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["invoiceValue" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal["Balance" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                }
                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr>
                        <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                    </tr>
                    <tr>
                        <?php
                        if ($isLocCost && $isRptCost) { ?>
                            <td colspan='7'><strong><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--></strong></td>
                        <?php } else if ($isRptCost) { ?>
                            <td colspan='3'><strong><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--></strong></td>
                        <?php }
                        if (!empty($fieldName)) {
                            foreach ($fieldName as $val2) {
                                if ($val2 == "companyReportingAmount") {
                                    echo "<td></td>";
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal["GRNValue" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal["invoiceValue" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal["Balance" . $val2]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                }
                            }
                        }
                        ?>
                    </tr>
                </table>
                <?php
            } else {
                echo warning_message($no_rec_found);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>

<?php
