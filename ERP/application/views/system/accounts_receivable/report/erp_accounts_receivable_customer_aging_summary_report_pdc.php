<!--
-- =============================================
-- File Name : erp_accounts_receivable_customer_aging_summary_report.php
-- Project Name : SME ERP
-- Module Name : Report - Account receivable
-- Create date : 16 - November 2016
-- Description : This file contains customer aging summary report.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('accounts_receivable_common_as_of');
$currency=$this->lang->line('common_currency');
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
            echo export_buttons('tbl_customer_aging_summary', 'Customer Aging Summary');
        } ?>
    </div>
</div>
<div id="tbl_customer_aging_summary">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('accounts_receivable_rs_cs_customer_aging_summary');?><!-- Customer Aging Summary--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
                        <?php
                        if ($isCustCost || $isTransCost) {
                            echo "<th>$currency<!--Currency--></th>";
                        }
                        ?>
                        <th><?php echo $this->lang->line('accounts_receivable_common_currenct');?><!--Current--></th>
                        <?php
                        if (!empty($aging)) {
                            foreach ($aging as $val2) {
                                echo "<th>" . $val2 . "</th>";
                            }
                        }
                        ?>
                        <th><?php echo 'Unallocated / Advance';?><!--Total--></th>
                        <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        <th>PDC Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $count = count($aging);
                    $category = array();
                    foreach ($output as $val) {
                        $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][] = $val;
                    }
                    $grandTotal = array();
                    $grandTotal_pdc = array();
                    $unallocated = array();
                    if (!empty($category)) {
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                            foreach ($mainCategory as $key2 => $customer) {
                                $total = 0;
                                $total += $customer["current"];
                                echo "<tr class='hoverTr'>";
                                echo "<td><div style='margin-left: 15px'>" . $customer["customerSystemCode"] . " - " . $customer["customerName"] . "</div></td>";
                                if ($isCustCost || $isTransCost) {
                                    echo "<td>" . $customer["currency"] . "</td>";
                                }
                                echo "<td class='text-right'>" . number_format($customer["current"], $customer["DecimalPlaces"]) . "</td>";
                                $grandTotal["current"][] = $customer["current"];
                                if($isRptCost)
                                {
                                    $grandTotal_pdc["totalpdc"][] = $customer["companyReportingAmountPDCT"];
                                }
                                    if($isLocCost)
                                    {
                                        $grandTotal_pdc["totalpdc"][] = $customer["companyLocalAmountPDCT"];
                                    }


                                $i = 1;
                                $customerName = htmlspecialchars($customer["customerName"], ENT_QUOTES);

                                
                                $unallocate = 0;
                                
                         
                                foreach ($aging as $value) {
                                    if($customer[$value] >= 0){
                                        $total += $customer[$value];
                                        $grandTotal[$value][] = $customer[$value];
                                        if ($i == $count) {
                                            if ($type == 'html') {
                                                echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $customer["customerID"] . '\',\'' . htmlspecialchars($customerName) . '\',\'' . $fieldName[0] . '\',\'' . $this->input->post('through') . '\')">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</a></td>';
                                            } else {
                                                echo '<td class="text-right">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</td>';
                                            }
                                        } else {
                                            if ($type == 'html') {
                                                echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $customer["customerID"] . '\',\'' . htmlspecialchars($customerName) . '\',\'' . $fieldName[0] . '\',\'' . $value . '\')">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</a></td>';
                                            } else {
                                                echo '<td class="text-right">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</td>';
                                            }
                                        }

                                        $unallocate = $unallocate + 0;

                                    }else{
                                        echo '<td class="text-right">' . number_format(0 , $customer["DecimalPlaces"]) . '</td>';
                                        $unallocate = $unallocate + $customer[$value];
                                        
                                    }
                                    
                                    $i++;
                                }
                       

                                if(isset($unallocate)){
                                    $total += $unallocate;
                                    $unallocated[] = $unallocate;
                                    echo '<td class="text-right">' . number_format($unallocate , $customer["DecimalPlaces"]) . '</td>';
                                }

                                $grandTotal["total"][] = $total;
                                echo "<td class='text-right'>" . number_format($total, $customer["DecimalPlaces"]) . "</td>";
                                   
                            
                               
                                
                                if($isRptCost)
                                {
                                    echo "<td class='text-right'>" . number_format($customer['companyReportingAmountPDCT'], $customer["DecimalPlaces"]) . "</td>";
                                }
                                if($isLocCost)
                                {
                                    echo "<td class='text-right'>" . number_format($customer['companyLocalAmountPDCT'], $customer["DecimalPlaces"]) . "</td>";
                                }
                                if($isTransCost)
                                {
                                    echo "<td class='text-right'>" . number_format($customer['transactionAmountPDCT'], $customer["DecimalPlaces"]) . "</td>";
                                }



                                echo "</tr>";
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
                        echo "<td><strong>$grandt<!--Grand Total--></strong></td>";
                        if ($isRptCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        if ($isLocCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
//                        if ($isRptCost) {
//                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
//                        }
//                        if ($isLocCost) {
//                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
//                        }
                        $gr_total = 0;
                        if (!empty($aging)) {
                            foreach ($aging as $value) {
                                if ($isRptCost) {
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    $gr_total += array_sum($grandTotal[$value]);
                                }
                                if ($isLocCost) {
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    $gr_total += array_sum($grandTotal[$value]);
                                }
                            }
                        }

                        if(!empty($unallocated)){
                            if ($isRptCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($unallocated), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                $gr_total += array_sum($unallocated);
                            }
                            if ($isLocCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($unallocated), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                $gr_total += array_sum($unallocated);
                            }
                        }

                        if ($isRptCost) {
                            echo "<td class='text-right reporttotal'>" . format_number($gr_total, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        if ($isLocCost) {
                            echo "<td class='text-right reporttotal'>" . format_number($gr_total, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
                        if ($isRptCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal_pdc["totalpdc"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        if ($isLocCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal_pdc["totalpdc"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                    </tfoot>
                </table>
                <?php
            } else {
                $norecfoound= $this->lang->line('common_no_records_found');
                echo warning_message($norecfoound);/*"No Records Found!"*/
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