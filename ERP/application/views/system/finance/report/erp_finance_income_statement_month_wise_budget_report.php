<!---- =============================================
-- File Name : erp_finance_income_statement_month_wise_budget_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 27 - February 2017
-- Description : This file contains Income Statement Month Wise budget.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$currency=$this->lang->line('common_currency');
$totalc=$this->lang->line('finance_common_total');
$grossprofit=$this->lang->line('finance_common_gross_profit');
$netproloss=$this->lang->line('finance_common_net_profit_loss');


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
            echo export_buttons('tbl_finance_tb', 'Income Statement Month Wise Budget');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_is_income_statement_month_wise_budget');?><!--Income Statement Month Wise Budget--></div>
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
                echo '<div class="col-md-12"><strong>'.$currency.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_reporting_currency'] . '</div>';

            }
            if ($isLocCost) {
                echo '<div class="col-md-12"><strong>'.$currency.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_default_currency'] . '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="table-responsive">
                    <table class="borderSpace report-table-condensed" id="tbl_report"
                           style="border-collapse: collapse;">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2">
                                <div style='width: 10%'><?php echo $this->lang->line('common_description');?><!--Description--></div>
                            </th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th colspan="2">' . $val . '</th>';
                                }
                            }
                            ?>
                            <th rowspan="2"><?php echo $this->lang->line('finance_rs_is_total_actual');?><!--Total Actual--></th>
                            <th rowspan="2"><?php echo $this->lang->line('finance_rs_is_total_budget');?><!--Total Budget--></th>
                        </tr>
                        <tr>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    $actual =$this->lang->line('finance_rs_is_actual');
                                    $budget=$this->lang->line('finance_rs_is_budget');
                                    echo ' <th>'.$actual.'<!--Actual--></th>';
                                    echo ' <th>'.$budget.'<!--Budget--></th>';
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $base_category = array();
                        $category = array();
                      
                        foreach ($output as $val) {
                            $base_category[$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }

                        $category['Income'] = isset($base_category['Income']) ? $base_category['Income'] : array();
                        $category['Cost of Goods Sold'] = isset($base_category['Cost of Goods Sold']) ? $base_category['Cost of Goods Sold'] : array();
                        $category['Expense'] = isset($base_category['Expense']) ? $base_category['Expense'] : array();
                        $category['Other Income'] = isset($base_category['Other Income']) ? $base_category['Other Income'] : array();
                        $category['Other Expense'] = isset($base_category['Other Expense']) ? $base_category['Other Expense'] : array();

                        $globalcount = (count($month) * 2)+3;
                        if (!empty($base_category)) {
                            $grandTotal = array();
                            $grandTotalBudget = array();
                            $grandTotalHorizontal = array();
                            $grandTotalHorizontalBud = array();
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . strtoupper($key) . "</div></td></tr>";
                                $maintotal = array();
                                $maintotalBudget = array();
                                $maintotalHorizontal = array();
                                $maintotalHorizontalBud = array();
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                                    $subtotal = array();
                                    $subtotalBudget = array();
                                    $subtotalHorizontal = array();
                                    $subtotalHorizontalBud = array();
                                    $i = 1;
                                    $count = count($subCategory);
                                    foreach ($subCategory as $item) {
                                        $total = 0;
                                        $totalBudget = 0;
                                        echo "<tr class='hoverTr'>";
                                        echo "<td><div style='margin-left: 60px;white-space: nowrap'>" . $item["GLDescription"] . "</div></td>";
                                        foreach ($fieldNameDetails as $key4 => $value) {
                                            foreach ($month as $key5 => $value2) {
                                                $total += $item[$key5];
                                                $totalBudget += $item['budget' . $key5];
                                                $subtotal[$key5][] = (float)$item[$key5];
                                                $maintotal[$key5][] = (float)$item[$key5];
                                                $grandTotal[$key5][] = (float)$item[$key5];

                                                $subtotalBudget[$key5][] = (float)$item['budget' . $key5];
                                                $maintotalBudget[$key5][] = (float)$item['budget' . $key5];
                                                $grandTotalBudget[$key5][] = (float)$item['budget' . $key5];

                                                if ($type == "html") {
                                                    echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["GLAutoID"] . '\',\'' . $item["masterCategory"] . '\',\'' . $item["GLDescription"] . '\',\'' . $value["fieldName"] . '\',\'' . $key5 . '\')">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</a></td>';
                                                } else {
                                                    echo '<td class="text-right">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                }
                                                echo '<td class="text-right">' . number_format($item['budget' . $key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                            }
                                        }
                                        $subtotalHorizontal[] = $total;
                                        $maintotalHorizontal[] = $total;
                                        $grandTotalHorizontal[] = $total;

                                        $subtotalHorizontalBud[] = $totalBudget;
                                        $maintotalHorizontalBud[] = $totalBudget;
                                        $grandTotalHorizontalBud[] = $totalBudget;
                                        if ($isRptCost) {
                                            echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class='text-right'>" . number_format($totalBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($isLocCost) {
                                            echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class='text-right'>" . number_format($totalBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        echo "</tr>";
                                        $i++;
                                    }
                                        $tot= $this->lang->line('common_total');
                                    echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total--> " . $key2 . "</strong></div></td>"; /*display total of each sub category*/
                                    foreach ($fieldNameDetails as $key6 => $value) {
                                        foreach ($month as $key7 => $value2) {
                                            $sum = array_sum($subtotal[$key7]);
                                            $sumBudget = array_sum($subtotalBudget[$key7]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($subtotalHorizontal);
                                    $sumBudget = array_sum($subtotalHorizontalBud);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                                if ($key == "Cost of Goods Sold") { /*display total of Cost of Goods Sold*/
                                    echo "<tr><td colspan='".$globalcount."'>&nbsp;</td></tr>";
                                    echo "<tr><td class=''><div><strong>$totalc<!--TOTAL--> " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($maintotal[$key9]);
                                            $sumBudget = array_sum($maintotalBudget[$key9]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    $sumBudget = array_sum($maintotalHorizontalBud);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";

                                    echo "<tr><td colspan='".$globalcount."'>&nbsp;</td></tr>"; /*display gross profit*/
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$grossprofit<!--GROSS PROFIT--></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($grandTotal[$key9]);
                                            $sumBudget = array_sum($grandTotalBudget[$key9]);
                                            if ($isRptCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($grandTotalHorizontal);
                                    $sumBudget = array_sum($grandTotalHorizontalBud);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                } else if ($key == "Expense") { /*display expense total*/
                                    echo "<tr><td colspan='".$globalcount."'>&nbsp;</td></tr>";
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$totalc<!--TOTAL--> " . strtoupper($key) . "</div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($maintotal[$key9]);
                                            $sumBudget = array_sum($maintotalBudget[$key9]);
                                            if ($isRptCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    $sumBudget = array_sum($maintotalHorizontalBud);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo " </tr > ";
                                } else {
                                    echo "<tr><td colspan='".$globalcount."'>&nbsp;</td></tr>";
                                    echo "<tr><td class=''><div><strong>$totalc<!--TOTAL--> " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($maintotal[$key9]);
                                            $sumBudget = array_sum($maintotalBudget[$key9]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    $sumBudget = array_sum($maintotalHorizontalBud);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            echo "<tr><td colspan='".$globalcount."'>&nbsp;</td></tr>";
                            echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$netproloss<!--NET PROFIT / LOSS--></div></td>";
                            foreach ($fieldNameDetails as $key10 => $value) {
                                foreach ($month as $key11 => $value2) {
                                    $sum = array_sum($grandTotal[$key11]);
                                    $sumBudget = array_sum($grandTotalBudget[$key11]);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                }
                            }
                            $sum = array_sum($grandTotalHorizontal);
                            $sumBudget = array_sum($grandTotalHorizontalBud);
                            if ($isRptCost) {
                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                            if ($isLocCost) {
                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                $norec= $this->lang->line('common_no_records_found');
                echo warning_message($norec);/*"No Records Found!"*/
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