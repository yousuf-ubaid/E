<!---- =============================================
-- File Name : erp_finance_ytd_budget_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 19 - December 2018
-- Description : This file contains Budget.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$totc=$this->lang->line('finance_common_total');

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
            echo export_buttons('tbl_finance_tb', 'Budget YTD');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Budget YTD </div>
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
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($fieldName)) {
                                foreach ($fieldName as $val) {
                                    if ($val == "companyLocalAmount") {
                                        $actualtot=$this->lang->line('finance_common_actual_total');
                                        $budgettot=$this->lang->line('finance_common_budget_total');
                                        $variance=$this->lang->line('finance_common_budget_variance');

                                        echo "<th>$budgettot<!--Budget Total-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>Consumption Total (" . $this->common_data['company_data']['company_default_currency'] . ")</th>";
                                        echo "<th>$variance<!--Variance-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>$variance<!--Variance-->(%) </th>";
                                    }
                                    if ($val == "companyReportingAmount") {
                                        $actualtot=$this->lang->line('finance_common_actual_total');
                                        $budgettot=$this->lang->line('finance_common_budget_total');
                                        $variance=$this->lang->line('finance_common_budget_variance');
                                        echo "<th>$budgettot<!--Budget Total-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>Consumption Total(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$variance<!--Varience-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$variance<!--Variance-->(%) </th>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        /*foreach ($output as $val) {
                            $category[$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }*/
                        $category = array_group_by($output, 'mainCategory', 'subCategory');
                        if (!empty($category)) {
                            $grandTotal = array();
                            $grandTotalBudget = array();
                            $grandTotalVariance = array();
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . strtoupper($key) . "</div></td></tr>";
                                $maintotal = array();
                                $maintotalbudget = array();
                                $maintotalvariance = array();
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                                    $subtotal = array();
                                    $subtotalbudget = array();
                                    $subtotalVariance = array();
                                    $i = 1;
                                    $count = count($subCategory);
                                    foreach ($subCategory as $item) {
                                        echo "<tr class='hoverTr'>";
                                        echo '<td><div style="margin-left: 60px">' . $item["GLDescription"] . '</a></div></td>';
                                        foreach ($fieldNameDetails as $key4 => $value) {
                                            $subtotal[$value["fieldName"]][] = (float)$item[$value["fieldName"]];
                                            $maintotal[$value["fieldName"]][] = (float)$item[$value["fieldName"]];
                                            $grandTotal[$value["fieldName"]][] = (float)$item[$value["fieldName"]];
                                            $subtotalbudget[$value["fieldName"]][] = (float)$item['budget' . $value["fieldName"]];
                                            $maintotalbudget[$value["fieldName"]][] = (float)$item['budget' . $value["fieldName"]];
                                            $grandTotalBudget[$value["fieldName"]][] = (float)$item['budget' . $value["fieldName"]];
                                            echo '<td class="text-right">' . number_format($item['budget' . $value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                            if ($type == "html") {
                                                echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["GLAutoID"] . '\',\'' . $item["masterCategory"] . '\',\'' . $item["GLDescription"] . '\',\'' . $value["fieldName"] . '\')">' . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . '</a></td>';
                                            } else {
                                                echo '<td class="text-right">' . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                            }

                                            /*$subtotalvariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                            $maintotalvariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                            $grandTotalVariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                            echo '<td class="text-right">' . number_format(($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]), $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                            echo '<td class="text-right">' . round((($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]])/ $item['budget' . $value["fieldName"]])*100,2)  . '%</td>';*/

                                            if ($item["chartsubcat"] == "PLI") {
                                                $subtotalvariance[$value["fieldName"]][] = (float)($item[$value["fieldName"]] - $item['budget' . $value["fieldName"]]);
                                                $maintotalvariance[$value["fieldName"]][] = (float)($item[$value["fieldName"]] - $item['budget' . $value["fieldName"]]);
                                                $grandTotalVariance[$value["fieldName"]][] = (float)($item[$value["fieldName"]] - $item['budget' . $value["fieldName"]]);
                                                echo '<td class="text-right">'. number_format(($item[$value["fieldName"]] - $item['budget' . $value["fieldName"]]), $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                echo '<td class="text-right">' . ($item['budget' . $value["fieldName"]] != 0 ? round((($item[$value["fieldName"]] - $item['budget' . $value["fieldName"]]) / $item['budget' . $value["fieldName"]]) * 100, 2) : 0) . '%</td>';
                                            } else {
                                                $subtotalvariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                                $maintotalvariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                                $grandTotalVariance[$value["fieldName"]][] = (float)($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]);
                                                echo '<td class="text-right">' . number_format(($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]), $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                if($item['budget' . $value["fieldName"]])
                                                {
                                                    echo '<td class="text-right">' . ($item[$value["fieldName"]] != 0 ? round((($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]]) / $item['budget' . $value["fieldName"]]) * 100, 2) : 0) . '%</td>';
                                                }else
                                                {
                                                    echo '<td class="text-right">' . ($item[$value["fieldName"]] != 0 ? round((($item['budget' . $value["fieldName"]] - $item[$value["fieldName"]])) * 100, 2) : 0) . '%</td>';
                                                }

                                            }
                                        }

                                        echo "</tr>";
                                        $i++;
                                    }
                                }
                                if ($key == "Cost of Goods Sold") { /*display total of Cost of Goods Sold*/
                                    echo "<tr><td class=''><div><strong>TOTAL " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key5 => $value) {
                                        $sum = array_sum($maintotal[$value["fieldName"]]);
                                        $sumBudget = array_sum($maintotalbudget[$value["fieldName"]]);
                                        $sumVariance = array_sum($maintotalvariance[$value["fieldName"]]);
                                        if ($value['fieldName'] == "companyLocalAmount") {
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumVariance, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($value['fieldName'] == "companyReportingAmount") {
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumVariance, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        echo "<td class=' text-right reportsubtotal'>" . ($sum != 0 ? round(($sumVariance / $sum) * 100, 2) : 0) . "%</td>";
                                    }
                                    echo "</tr>";
                                    echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                                    $grosspro=$this->lang->line('finance_common_gross_profit');
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 90px'>$grosspro<!--GROSS PROFIT--></div></td>"; /*display gross profit*/
                                    foreach ($fieldNameDetails as $key5 => $value) {
                                        $sum = array_sum($grandTotal[$value["fieldName"]]);
                                        $sumBudget = array_sum($grandTotalBudget[$value["fieldName"]]);
                                        $sumVariance = array_sum($grandTotalVariance[$value["fieldName"]]);
                                        if ($value['fieldName'] == "companyLocalAmount") {
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumVariance, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($value['fieldName'] == "companyReportingAmount") {
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumVariance, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        echo "<td class=' text-right reporttotalblack'>" . ($sumBudget != 0 ? round(($sumVariance / $sumBudget) * 100, 2) : 0) . "%</td>";
                                    }
                                    echo "</tr>";

                                } else if ($key == "Expense") { /*display expense total*/
                                    echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 90px'>$totc<!--TOTAL--> " . strtoupper($key) . "</div></td>";
                                    foreach ($fieldNameDetails as $key5 => $value) {
                                        $sum = array_sum($maintotal[$value["fieldName"]]);
                                        $sumBudget = array_sum($maintotalbudget[$value["fieldName"]]);
                                        $sumVariance = array_sum($maintotalvariance[$value["fieldName"]]);
                                        if ($value['fieldName'] == "companyLocalAmount") {
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumVariance, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($value['fieldName'] == "companyReportingAmount") {
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reporttotalblack'>" . number_format($sumVariance, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        echo "<td class=' text-right reporttotalblack'>" . ($sum != 0 ? round(($sumVariance / $sum) * 100, 2) : 0) . "%</td>";
                                    }
                                    echo "</tr>";
                                } else {
                                    echo "<tr><td class=''><div><strong>$totc<!--TOTAL--> " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key5 => $value) {
                                        $sum = array_sum($maintotal[$value["fieldName"]]);
                                        $sumBudget = array_sum($maintotalbudget[$value["fieldName"]]);
                                        $sumVariance = array_sum($maintotalvariance[$value["fieldName"]]);
                                        if ($value['fieldName'] == "companyLocalAmount") {
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumVariance, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($value['fieldName'] == "companyReportingAmount") {
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            echo "<td class=' text-right reportsubtotal'>" . number_format($sumVariance, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($key == 'INCOME' || $key == 'OTHER INCOME') {
                                            echo "<td class=' text-right reportsubtotal'>" . ($sumBudget != 0 ? round(($sumVariance / $sumBudget) * 100, 2) : 0) . "%</td>";
                                        } else {
                                            echo "<td class=' text-right reportsubtotal'>" . ($sum != 0 ? round(($sumVariance / $sum) * 100, 2) : 0) . "%</td>";
                                        }
                                    }
                                    echo "</tr>";
                                }

                            }
                            echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                            $netpro=$this->lang->line('finance_common_net_profit_loss');
                            echo "<tr><td class='reporttotalblack'><div style='margin-left: 90px'>$netpro<!--NET PROFIT / LOSS--></div></td>";
                            foreach ($fieldNameDetails as $key6 => $value) {
                                $sum = array_sum($grandTotal[$value["fieldName"]]);
                                $sumBudget = array_sum($grandTotalBudget[$value["fieldName"]]);
                                $sumVariance = array_sum($grandTotalVariance[$value["fieldName"]]);
                                if ($value['fieldName'] == "companyLocalAmount") {
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sumVariance, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                }
                                if ($value['fieldName'] == "companyReportingAmount") {
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sumBudget, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td class='reporttotalblack text-right'>" . number_format($sumVariance, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                }
                                echo "<td class=' text-right reporttotalblack'>" . ($sumBudget != 0 ? round(($sumVariance / $sumBudget) * 100, 2) : 0) . "%</td>";
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
                $norecfo=$this->lang->line('common_no_records_found');
                echo warning_message($norecfo);/*"No Records Found!"*/
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