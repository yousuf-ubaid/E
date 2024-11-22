<!---- =============================================
-- File Name : erp_finance_income_statement_month_wise_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 15 - September 2016
-- Description : This file contains Income Statement Month Wise.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$curr=$this->lang->line('common_currency');
$tot=$this->lang->line('common_total');



$isRptCost = false;
$isLocCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_finance_tb', 'Income Statement');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_is_income_statement_month_wise');?><!--Income Statement Month Wise--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$datefrom<!--Date From-->: </strong>" . $from . " - <strong>$dateto<!--Date To-->: </strong>" . $to ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Segment:</i></strong> <?php echo join(",", $segmentfilter) ?>
        </div>
    </div>
    <div class="row">
        <div class="pull-right">
            <?php
            if ($isRptCost) {
                echo '<div class="col-md-12"><strong>'.$curr.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_reporting_currency'] . '</div>';

            }
            if ($isLocCost) {
                echo '<div class="col-md-12"><strong>'.$curr.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_default_currency'] . '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output_custom)) { ?>
                <div class="fixHeader_Div" style="overflow: auto">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th>' . $val . '</th>';
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        $base_header_Total = array();
                        $base_group_Total = array();
                        $base_group_group_Total = array();

                        foreach ($output as $val) {
                            $category[$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }

                      
                        if (!empty($output_custom)) {
                     
                            foreach ($output_custom as $key => $mainCategory) {

                                $grandTotal = array();
                                $grandTotalHorizontal = array();
                              
                                foreach ($mainCategory as $key => $mainCategory2) {
                                   // $key = ($key == '1') ? "Income" : ($key == '2') ?  "Expense" : ($key == '3') ? 'Group Total' : 'Group Group Total';
                                    
                                    $key = get_report_sub_category_type($key);
                                    echo "<tr><td><div class='mainCategoryHead'>" . strtoupper($key) . "</div></td></tr>";
                                   
                                    $maintotal = array();
                                    $maintotalHorizontal = array();

                                    $subtotal = array();
                                    $subtotalHorizontal = array();
                                    foreach ($mainCategory2 as $key2 => $subCategory) {

                                        echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                                        
                                        $header_type_1 = $subCategory['header_type1'];
                        
                                        if($header_type_1 == 1){
                                            
                                            $charts_of_acc = $subCategory['chart_of_accounts'];
                                            $i = 1;
                                            $count = count($charts_of_acc);
                                            foreach ($charts_of_acc as $item) {
                                                $total = 0;
                                                echo "<tr class='hoverTr'>";
                                                echo "<td><div style='margin-left: 60px'>" . $item["GLDescription"] . "</div></td>";
                                                foreach ($fieldNameDetails as $key4 => $value) {
                                                    foreach ($month as $key5 => $value2) {
                                                        $total += $item[$key5];
                                                        $subtotal[$key5][] = (float)$item[$key5];

                                                        if(isset($base_header_Total[$subCategory['id']][$key5])){
                                                            $base_header_Total[$subCategory['id']][$key5] = (float)$item[$key5];
                                                        }else{
                                                            $base_header_Total[$subCategory['id']][$key5] = (float)$item[$key5];
                                                        }
                                                       
                                                        $maintotal[$key5][] = (float)$item[$key5];
                                                        $grandTotal[$key5][] = (float)$item[$key5];
                                                        if ($type == 'html') {
                                                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["GLAutoID"] . '\',\'' . $item["masterCategory"] . '\',\'' . $item["GLDescription"] . '\',\'' . $value["fieldName"] . '\',\'' . $key5 . '\')">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</a></td>';
                                                        } else {
                                                            echo '<td class="text-right">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                        }
                                                    }
                                                }
                                                $subtotalHorizontal[] = $total;
                                                $maintotalHorizontal[] = $total;
                                                $grandTotalHorizontal[] = $total;

                                                if(isset($base_header_Total[$subCategory['id']]['hr_total'])){
                                                    $base_header_Total[$subCategory['id']]['hr_total'] = $total;
                                                }else{
                                                    $base_header_Total[$subCategory['id']]['hr_total'] = $total;
                                                }

                                                if ($isRptCost) {
                                                    echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                }
                                                if ($isLocCost) {
                                                    echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                }
                                                echo "</tr>";
                                                $i++;
                                            }

                                            

                                        } elseif($header_type_1 == 2) {

                                            $total_category = $subCategory['plus_minus'];
                                            //print_r($total_category); exit;

                                            echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total--> " . $key2 . "</strong></div></td>"; /*display total of each sub category*/
                                            foreach ($fieldNameDetails as $key6 => $value) {
                                                foreach ($month as $key7 => $value2) {
                                                    $sum = 0;

                                                    foreach($total_category as $cat_detail){
                                                        $row = $cat_detail['mapped_row_id'];
                                                        if($cat_detail['value'] == 1){
                                                            // if(!isset($base_header_Total[$row][$key7])){
                                                            //     print_r($base_header_Total); exit;
                                                            // }
                                                            $sum += (float)($base_header_Total[$row][$key7]);
                                                        }else{
                                                            $sum -= (float)($base_header_Total[$row][$key7]);
                                                        }
                                                      
                                                    }

                                                    if(isset($base_header_Total[$subCategory['id']][$key7])){
                                                        $base_group_Total[$subCategory['id']][$key7] = (float)$sum;
                                                    }else{
                                                        $base_group_Total[$subCategory['id']][$key7] = (float)$sum;
                                                    }
                                                   // $sum = array_sum($subtotal[$key7]);

                                                    if ($isRptCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    }
                                                    if ($isLocCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    }
                                                }
                                            }

                                            $sum = 0;
                                            foreach($total_category as $cat_detail){
                                                $row = $cat_detail['mapped_row_id'];
                                                if($cat_detail['value'] == 1){
                                                    $sum += (float)($base_header_Total[$row]['hr_total']);
                                                }else{
                                                    $sum -= (float)($base_header_Total[$row]['hr_total']);
                                                }
                                            }

                                            if(isset($base_header_Total[$subCategory['id']][$key7])){
                                                $base_group_Total[$subCategory['id']]['hr_total'] = (float)$sum;
                                            }else{
                                                $base_group_Total[$subCategory['id']]['hr_total'] = (float)$sum;
                                            }
                                           
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                            echo "</tr>";

                                        } elseif($header_type_1 == 3){
                                           
                                         
                                            $total_category_group = $subCategory['plus_minus'];

                                            echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total--> " . $key2 . "</strong></div></td>"; /*display total of each sub category*/
                                            foreach ($fieldNameDetails as $key6 => $value) {
                                                foreach ($month as $key7 => $value2) {
                                                    $sum = 0;

                                                    foreach($total_category_group as $cat_detail){
                                                        $row = $cat_detail['mapped_row_id'];
                                                        if($cat_detail['value'] == 1){
                                                            // if(!isset($base_header_Total[$row][$key7])){
                                                            //     print_r($base_header_Total); exit;
                                                            // }
                                                            $sum += (float)($base_group_Total[$row][$key7]);
                                                        }else{
                                                            $sum -= (float)($base_group_Total[$row][$key7]);
                                                        }
                                                      
                                                    }

                                                    if(isset($base_header_Total[$subCategory['id']][$key7])){
                                                        $base_group_group_Total[$subCategory['id']][$key7] = (float)$sum;
                                                    }else{
                                                        $base_group_group_Total[$subCategory['id']][$key7] = (float)$sum;
                                                    }
                                                   // $sum = array_sum($subtotal[$key7]);

                                                    if ($isRptCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    }
                                                    if ($isLocCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    }
                                                }
                                            }

                                            $sum = 0;
                                            foreach($total_category_group as $cat_detail){
                                                $row = $cat_detail['mapped_row_id'];
                                                if($cat_detail['value'] == 1){
                                                    $sum += (float)($base_group_Total[$row]['hr_total']);
                                                }else{
                                                    $sum -= (float)($base_group_Total[$row]['hr_total']);
                                                }
                                            }

                                            if(isset($base_header_Total[$subCategory['id']][$key7])){
                                                $base_group_group_Total[$subCategory['id']]['hr_total'] = (float)$sum;
                                            }else{
                                                $base_group_group_Total[$subCategory['id']]['hr_total'] = (float)$sum;
                                            }
                                           
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                            echo "</tr>";
                                          
                                        } elseif($header_type_1 == 4){
                                           
                                         
                                            $total_category_group_group = $subCategory['plus_minus'];

                                            echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total--> " . $key2 . "</strong></div></td>"; /*display total of each sub category*/
                                            foreach ($fieldNameDetails as $key6 => $value) {
                                                foreach ($month as $key7 => $value2) {
                                                    $sum = 0;

                                                    foreach($total_category_group_group as $cat_detail){
                                                        $row = $cat_detail['mapped_row_id'];
                                                        if($cat_detail['value'] == 1){
                                                            $sum += (float)($base_group_group_Total[$row][$key7]);
                                                        }else{
                                                            $sum -= (float)($base_group_group_Total[$row][$key7]);
                                                        }
                                                      
                                                    }

                                                    if ($isRptCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    }
                                                    if ($isLocCost) {
                                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    }
                                                }
                                            }

                                            $sum = 0;
                                            foreach($total_category_group_group as $cat_detail){
                                                $row = $cat_detail['mapped_row_id'];
                                                if($cat_detail['value'] == 1){
                                                    $sum += (float)($base_group_group_Total[$row]['hr_total']);
                                                }else{
                                                    $sum -= (float)($base_group_group_Total[$row]['hr_total']);
                                                }
                                            }

                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                            echo "</tr>";
                                          
                                        }
                                        
                                    }
                                   

                                    // if ($key == "Cost of Goods Sold") { /*display total of Cost of Goods Sold*/
                                    //     echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    //     echo "<tr><td class=''><div><strong>$tot<!--TOTAL--> " . strtoupper($key) . "</strong></div></td>";
                                    //     foreach ($fieldNameDetails as $key8 => $value) {
                                    //         foreach ($month as $key9 => $value2) {
                                    //             $sum = array_sum($maintotal[$key9]);
                                    //             if ($isRptCost) {
                                    //                 echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //             }
                                    //             if ($isLocCost) {
                                    //                 echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //             }
                                    //         }
                                    //     }
                                    //     $sum = array_sum($maintotalHorizontal);
                                    //     if ($isRptCost) {
                                    //         echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //     }
                                    //     if ($isLocCost) {
                                    //         echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //     }
                                    //     echo "</tr>";
                                    //     $grosspro= $this->lang->line('finance_common_gross_profit');

                                    //     echo "<tr><td colspan='10'>&nbsp;</td></tr>"; /*display gross profit*/
                                    //     echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$grosspro<!--GROSS PROFIT--></div></td>";
                                    //     foreach ($fieldNameDetails as $key8 => $value) {
                                    //         foreach ($month as $key9 => $value2) {
                                    //             $sum = array_sum($grandTotal[$key9]);
                                    //             if ($isRptCost) {
                                    //                 echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //             }
                                    //             if ($isLocCost) {
                                    //                 echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //             }
                                    //         }
                                    //     }
                                    //     $sum = array_sum($grandTotalHorizontal);
                                    //     if ($isRptCost) {
                                    //         echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //     }
                                    //     if ($isLocCost) {
                                    //         echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //     }
                                    //     echo "</tr>";
                                    // } else if ($key == "Expense") { /*display expense total*/
                                    //     echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    //     $totalca= $this->lang->line('finance_common_total');
                                    //     echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$totalca<!--TOTAL--> " . strtoupper($key) . "</div></td>";
                                        
                                        
                                    //     foreach ($fieldNameDetails as $key8 => $value) {
                                    //         foreach ($month as $key9 => $value2) {
                                    //             $sum = array_sum($maintotal[$key9]);
                                    //             if ($isRptCost) {
                                    //                 echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //             }
                                    //             if ($isLocCost) {
                                    //                 echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //             }
                                    //         }
                                    //     }
                                    //     $sum = array_sum($maintotalHorizontal);
                                    //     if ($isRptCost) {
                                    //         echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //     }
                                    //     if ($isLocCost) {
                                    //         echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //     }
                                    //     echo " </tr > ";
                                    // } else {
                                    //     echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    //     echo "<tr><td class=''><div><strong>TOTAL " . strtoupper($key) . "</strong></div></td>";
                                    //     foreach ($fieldNameDetails as $key8 => $value) {
                                    //         foreach ($month as $key9 => $value2) {
                                    //             $sum = array_sum($maintotal[$key9]);
                                    //             if ($isRptCost) {
                                    //                 echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //             }
                                    //             if ($isLocCost) {
                                    //                 echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //             }
                                    //         }
                                    //     }
                                    //     $sum = array_sum($maintotalHorizontal);
                                    //     if ($isRptCost) {
                                    //         echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    //     }
                                    //     if ($isLocCost) {
                                    //         echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    //     }
                                    //     echo "</tr>";
                                    // }
                                }

                            }

                            exit;
                           
                            $sum = array_sum($grandTotalHorizontal);
                            if ($isRptCost) {
                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                            if ($isLocCost) {
                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            }
                            echo " </tr > ";
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
            } else {
                $norecfound= $this->lang->line('common_no_records_found');
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