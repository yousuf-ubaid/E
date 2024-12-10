<!---- =============================================
-- File Name : erp_accounts_receivable_customer_outstanding_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Receivable
-- Create date : 10 - March 2020
-- Description : This file contains Customer Outstanding.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$asof= $this->lang->line('accounts_receivable_common_as_of');
$currency= $this->lang->line('common_currency');
$amountLang= $this->lang->line('common_net_amount');
$debit= $this->lang->line('accounts_receivable_common_debit');
$credit= $this->lang->line('accounts_receivable_common_credit');
$tot=$this->lang->line('common_total');
$netbal=$this->lang->line('accounts_receivable_common_net_balance');
$grandto=$this->lang->line('common_grand_total');

$isRptCost = false;
$isLocCost = false;
$isTransCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }

    if (in_array("transactionAmount", $fieldName)) {
        $isTransCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if($type == 'html') {
            echo export_buttons('tbl_customer_outstanding', 'Customer Outstanding');


          } ?>
    </div>
</div>
<div id="tbl_customer_outstanding">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Customer Outstanding</div>
            <div


                class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As Of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_receivable_common_doc_date');?><!--Doc Date--></th>

                            <th rowspan="2"><?php echo $this->lang->line('accounts_receivable_common_doc_type');?><!--Doc Type--></th>


                            <th rowspan="2"><?php echo $this->lang->line('accounts_receivable_common_doc_number');?><!--Doc Number--></th>
                            <th rowspan="2"> Doc Due Date<!--Doc Due Date--></th>
                            <th rowspan="2"> Reference No<!--Reference No--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                            <?php
                            if (!empty($caption)) {
                                foreach ($caption as $val) {
                                    if ($val == "Transaction Currency") {
                                        echo '<th colspan="4">' . $val . '</th>';
                                    } else {
                                        if ($val == "Reporting Currency") {
                                            echo '<th colspan="3">' . $val . '(' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        }
                                        if ($val == "Local Currency") {
                                            echo '<th colspan="3">' . $val . '(' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>

                        <tr>
                            <?php
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $val) {
                                    if ($val['fieldName'] == 'transactionAmount') {
                                        echo '<th>'.$currency.'<!--Currency--></th>';
                                        echo '<th>Gross Amount</th>';
                                        echo '<th>Rebate Amount</th>';
                                        echo '<th>'.$amountLang.'<!--Debit--></th>';
                                    } else {
                                        echo '<th>Gross Amount</th>';
                                        echo '<th>Rebate Amount</th>';
                                        echo '<th>'.$amountLang.'<!--Debit--></th>';                                       
                                    }

                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <?php
                        $count = 9;
                        $category = array();
                        $customers_arr = array_column($customers, 'customerAutoID');
                        $customersall_arr = array_column($customersall, 'customerAutoID');
                        if ($isTransCost && !$isRptCost && !$isLocCost) {
                            foreach ($output as $val) {
                                $cus_id = $val["customerID"];
                                $cus_sys_code = $val["customerSystemCode"];
                                $cus_name = $val["customerName"];

                                $key = array_search($cus_id, $customers_arr);
                                if($groupbycus==1){
                                    if($key !== false){
                                        if(in_array($customers[$key]["masterID"], $customersall_arr)){
                                            $cus_sys_code = $customers[$key]["customerSystemCode"];
                                            $cus_name = $customers[$key]["customerName"];
                                        }
                                    }
                                }

                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$cus_sys_code . " - " . $cus_name][$val["transactionAmountcurrency"]][] = $val;
                            }
                        } else {
                            foreach ($output as $val) {
                                $cus_id = $val["customerID"];
                                $cus_sys_code = $val["customerSystemCode"];
                                $cus_name = $val["customerName"];

                                $key = array_search($cus_id, $customers_arr);
                                if($groupbycus==1){
                                    if($key !== false){
                                        if(in_array($customers[$key]["masterID"], $customersall_arr)){
                                            $cus_sys_code = $customers[$key]["customerSystemCode"];
                                            $cus_name = $customers[$key]["customerName"];
                                        }
                                    }
                                }

                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$cus_sys_code . " - " . $cus_name][] = $val;
                            }
                        }
                        $grandtotal = array();
                        if ($isTransCost && !$isRptCost && !$isLocCost) {
                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $currency) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        foreach ($currency as $key3 => $customers) {
                                            $subtotal = array();
                                            foreach ($customers as $key4 => $val) {
                                                if($val['rowCheck'] != 0) {
                                                    echo "<tr class='hoverTr'>";
                                                    echo "<td><div style='margin-left: 30px'>" . $val["bookingDate"] . "</div></td>";

                                                    echo "<td>" . $val["document"] . "</td>";


                                                    echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["invoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></td>';
                                                    echo "<td>" . $val["invoiceDueDate"] . "</td>";
                                                    echo "<td>" . $val["referenceNo"] . "</td>";
                                                    echo "<td>" . $val["comments"] . "</td>";
                                                    if (!empty($fieldNameDetails)) {
                                                        foreach ($fieldNameDetails as $val2) {
                                                          
                                                            
                                                            
                                                            if( $val['documentID'] == 'SLR'){ 
                                                                $subtotal['rebateAmount'][] = $val['rebateAmount']*-1;
                                                                $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]] - ( $val['rebateAmount']*-1);
                                                            }else { 
                                                                $subtotal['rebateAmount'][] = $val['rebateAmount'];
                                                                $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                              }
                                                        

                                                            $grandtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                            $grandtotal['rebateAmount'][] = $val['rebateAmount'];
                                                            if ($val2["fieldName"] == 'transactionAmount') {
                                                                if( $val['documentID'] == 'SLR'){ 
                                                                    echo "<td>" . $val[$val2["fieldName"] . "currency"] . "</td>";
                                                               
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]]), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format(($val['rebateAmount']*-1), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]]+ $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
    
                                                                }else { 
                                                                    echo "<td>" . $val[$val2["fieldName"] . "currency"] . "</td>";
                                                               
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]] + $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format($val['rebateAmount'], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
    
                                                                }
                                                             

                                                            } else {
                                                                if( $val['documentID'] == 'SLR'){ 
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]]), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format(($val['rebateAmount']*-1), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]] + $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                }else { 
                                                                    echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]] + $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format($val['rebateAmount'], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                    echo '<td  class="text-right">' . number_format($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                                }
                                                               
                                                            
                                                            
                                                            }
                                                        }
                                                    }
                                                    echo "</tr>";
                                                }
                                            }
                                            echo "<tr>";
                                            echo "<td colspan='7'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            if (!empty($fieldNameDetails)) {
                                                foreach ($fieldNameDetails as $key => $val2) {
                                                    $positiveAmount = array_sum( $subtotal[$val2["fieldName"]]);
                                                    print_r($subtotal['rebateAmount']);
                                                    $rebate = array_sum($subtotal['rebateAmount']);
                                                    if ($val2['fieldName'] == "transactionAmount") {
                                                        echo '<td class="text-right reporttotal">' . number_format(($positiveAmount + $rebate), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                        echo '<td class="text-right reporttotal">' . number_format($rebate, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                    }
                                                }
                                                exit();
                                               
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $customers) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        $subtotal = array();
                                        foreach ($customers as $key3 => $val) {
                                            if($val['rowCheck'] != 0){
                                                echo "<tr class='hoverTr'>";
                                                echo "<td><div style='margin-left: 30px'>" . $val["bookingDate"] . "</div></td>";
                                                // echo "<td>" . $val["invoiceDueDate"] . "</td>";
                                                /*  echo "<td>" . $val["customerAddress"] . "</td>";*/
                                                echo "<td>" . $val["document"] . "</td>";
                                                echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["invoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></td>';
                                                echo "<td>" . $val["invoiceDueDate"] . "</td>";
                                                echo "<td>" . $val["referenceNo"] . "</td>";


                                                echo "<td>" . $val["comments"] . "</td>";
                                                if (!empty($fieldNameDetails)) {
                                                    foreach ($fieldNameDetails as $val2) {
                                                        $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                        $subtotal['rebateAmount'][] = $val['rebateAmount'];
                                                        $grandtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                        $grandtotal['rebateAmount'][] = $val['rebateAmount'];
                                                        if ($val2["fieldName"] == 'transactionAmount') {
                                                            echo "<td>" . $val[$val2["fieldName"] . "currency"] . "</td>";
                                                            echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]] + $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                            echo '<td  class="text-right">' . number_format($val['rebateAmount'], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                            echo '<td  class="text-right">' . number_format($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                        } else {
                                                            echo '<td  class="text-right">' . number_format(($val[$val2["fieldName"]] + $val['rebateAmount']), $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                            echo '<td  class="text-right">' . number_format($val['rebateAmount'], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                            echo '<td  class="text-right">' . number_format($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                        }
                                                    }
                                                }
                                                echo "</tr>";
                                            }
                                        }
                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {
                                            if ($isTransCost) {
                                                echo "<td colspan='9'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            } else {
                                                echo "<td colspan='6'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            }
                                        }
                                        if (!empty($fieldNameDetails)) {
                                            foreach ($fieldNameDetails as $key => $val2) {
                                                $positiveAmount = array_sum($subtotal[$val2['fieldName']]);
                                                $rebate = array_sum($subtotal['rebateAmount']);
                                                if ($val2['fieldName'] == "companyLocalAmount") {
                                                    echo '<td class="text-right reporttotal">' . number_format(($positiveAmount + $rebate), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format($rebate, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                }
                                                if ($val2['fieldName'] == "companyReportingAmount") {
                                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format($rebate, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format(($positiveAmount + $rebate), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                                }
                                            }
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

                        <?php
                        echo "<tr>";
                        if ($isLocCost || $isRptCost) {
                            if ($isTransCost) {
                                echo "<td colspan='9'><div style='margin-left: 30px' class='pull-right'><strong>$grandto<!--Grand Total--></strong></div></td>";
                            } else {
                                echo "<td colspan='6'><div style='margin-left: 30px' class='pull-right'><strong>$grandto<!--Grand Total--></strong></div></td>";
                            }
                        }
                    

                        if (!empty($fieldNameDetails)) {
                            foreach ($fieldNameDetails as $key => $val2) {
                                $newArray2 = $grandtotal[$val2['fieldName']];
                                $pos_arr = array();
                                $neg_arr = array();
                                foreach ($newArray2 as $val) {
                                    ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                }
                                $positiveAmount = array_sum($grandtotal[$val2['fieldName']]);
                                $grandRebate = array_sum($grandtotal['rebateAmount']);
                                if ($val2['fieldName'] == "companyLocalAmount") {
                                    echo '<td class="text-right reporttotal">' . number_format(($positiveAmount + $grandRebate), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format($grandRebate, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                }
                                if ($val2['fieldName'] == "companyReportingAmount") {
                                    echo '<td class="text-right reporttotal">' . number_format(($positiveAmount + $grandRebate), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format($grandRebate, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                }
                            }
                        }
                        echo "</tr>";
                        ?>

                    </table>
                </div>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*"No Records Found!"*/
            }
            ?>
        </div>
    </div>
</div>

<script>
    /* $(document).ready(function() {
     $('#demo').dragtable();
     });*/
    /*$('#tbl_report').tableHeadFixer({
        head: true,
        foot: false,
        left: 0,
        right: 0,
        'z-index': 0
    });*/
</script>