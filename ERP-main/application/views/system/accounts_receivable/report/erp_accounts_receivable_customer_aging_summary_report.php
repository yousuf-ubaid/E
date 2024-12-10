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
$isSeNumber = false;
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

    if (in_array("seNumber", $fieldName)) {
        $isSeNumber = true;
    }
}

if($isSeNumber==true) {
    $col_count =10;
}else{
    $col_count =9;
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
                        <?php if($isSeNumber==true) { ?>
                            <th rowspan="2"> Secondary Inv. No</th>
                            <?php } ?>
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
                        <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $count = count($aging);
                    $category = array();
                    $maincat = array();
                    foreach ($output as $val) {
                        $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][] = $val;
                    }
                    $grandTotal = array();
                    if (!empty($category)) {
                        foreach ($category as $key => $mainCategory) {
                            $customerSub = array();
                            echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                            $customers_arr = array_column($customers, 'customerAutoID');//get customers which has a masterID
                            $customersall_arr = array_column($customersall, 'customerAutoID');//get all selected customers
                            $maincat_arr_search = array_column($mainCategory, 'customerID'); //get customerID from query


                            foreach ($mainCategory as $key1 => $vl){
                                $i = 0;
                                $keycus = array_search($vl['customerID'], $customers_arr); //check if masterID exist

                                /*if($groupbycus==1){
                                    if($keycus !== false){
                                        $ms_id = $customers[$keycus]["masterID"];//set master ID
                                        $keyc = array_search($ms_id, $maincat_arr_search);//check in which key does the master ID exist

                                        if(in_array($ms_id, $customersall_arr)){

                                            if(!empty($customerSub[$ms_id])) {
                                                $customerSub[$ms_id] .= '-' . $vl['customerID'];
                                            } else {
                                                $customerSub[$ms_id] = $vl['customerID'];
                                            }
a
                                            if(in_array($ms_id, array_column($mainCategory,'customerID') )){
                                                $maincat[$ms_id . '_' . $i] = $mainCategory[$keyc];
                                            }

                                            foreach ($aging as $ag) {
                                                //echo '<pre>'; print_r($mainCategory[$key1][$ag]);
                                                $maincat[$ms_id . '_' . $i][$ag] =  ( ($maincat[$ms_id . '_' . $i][$ag]) + ($mainCategory[$key1][$ag]));

                                            }

                                        }

                                    }else{

                                        $maincat[$vl['customerID'] . '_' . $i]=$vl;
                                        $customerSub[$vl['customerID']] = null;
                                    }
                                }*//*else{*/

                                /*}*/
                                $maincat[$vl['customerID'] . '_' . $i]=$vl;
                                $customerSub[$vl['customerID']] = null;
                                $i++;
                            }
                            foreach ($mainCategory as $key2 => $customer) {
                                // echo '<pre>'; print_r($customer);
                                //$mainCategory
                                $total = 0;
                                $total += $customer["current"];
                                echo "<tr class='hoverTr'>";
                                echo "<td><div style='margin-left: 15px'>" . $customer["customerSystemCode"] . " - " . $customer["customerName"] . "</div></td>";
                                if($isSeNumber==true) {
                                    echo "<td>" . $customer["seNumber"] . "</td>";
                                }
                                $docCurrency = "";
                                if ($isCustCost || $isTransCost) {
                                    $docCurrency = $customer["currency"];
                                    echo "<td>" . $customer["currency"] . "</td>";
                                }
                                echo "<td class='text-right'>" . number_format($customer["current"], $customer["DecimalPlaces"]) . "</td>";
                                $grandTotal["current"][] = $customer["current"];
                                $i = 1;
                                $customerName = htmlspecialchars($customer["customerName"], ENT_QUOTES);
                                foreach ($aging as $value) {

                                    $total += $customer[$value];
                                    $grandTotal[$value][] = $customer[$value];
                                    if ($i == $count) {
                                        if ($type == 'html') {
//                                            echo '<pre>'; print_r($customerSub[$customer["customerID"]]);
                                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $customer["customerID"] . '\',\'' . htmlspecialchars($customerName) . '\',\'' . $fieldName[0] . '\',\'' . $this->input->post('through') . '\',\'' . $customerSub[$customer["customerID"]] .'\',\'' . $groupbycus .'\',\'' . $docCurrency .'\')">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</a></td>';
                                        } else {
                                            echo '<td class="text-right">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</td>';
                                        }
                                    } else {
                                        if ($type == 'html') {
                                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $customer["customerID"] . '\',\'' . htmlspecialchars($customerName) . '\',\'' . $fieldName[0] . '\',\'' . $value . '\',\'' . $customerSub[$customer["customerID"]] .'\',\'' . $groupbycus .'\',\'' . $docCurrency .'\')">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</a></td>';
                                        } else {
                                            echo '<td class="text-right">' . number_format($customer[$value], $customer["DecimalPlaces"]) . '</td>';
                                        }
                                    }
                                    $i++;
                                }
                                $grandTotal["total"][] = $total;
                                echo "<td class='text-right'>" . number_format($total, $customer["DecimalPlaces"]) . "</td>";
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
                        if($isSeNumber==true) {
                            echo "<td colspan='2'><strong>$grandt<!--Grand Total--></strong></td>";
                        }else{
                            echo "<td><strong>$grandt<!--Grand Total--></strong></td>";
                        }
                        
                        if ($isRptCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        if ($isLocCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                        }

                        if (!empty($aging)) {
                            foreach ($aging as $value) {
                                if ($isRptCost) {
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                }
                                if ($isLocCost) {
                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                }
                            }
                        }
                        if ($isRptCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["total"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        if ($isLocCost) {
                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["total"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
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