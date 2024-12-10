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
            <div class="text-center reportHeader reportHeaderColor"> Income Statement Segment Wise</div>
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
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div" style="overflow: auto">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($segment)) {
                                foreach ($segment as $key) {
                                    echo ' <th>' . $key['segmentCode'] . '</th>';
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        foreach ($output as $val) {
                            $category[$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }
                        if (!empty($category)) {
                            $grandTotal = array();
                            $grandTotalHorizontal = array();
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . strtoupper($key) . "</div></td></tr>";
                                $maintotal = array();
                                $maintotalHorizontal = array();
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                                    $subtotal = array();
                                    $subtotalHorizontal = array();
                                    $i = 1;
                                    $count = count($subCategory);
                                    foreach ($subCategory as $item) {
                                        $total = 0;
                                        echo "<tr class='hoverTr'>";
                                        echo "<td><div style='margin-left: 60px'>" . $item["GLDescription"] . "</div></td>";
                                        foreach ($fieldNameDetails as $key4 => $value) {
                                            foreach ($segment as $key5) {
                                                $total += $item[$key5['segmentID']];
                                                $subtotal[$key5['segmentID']][] = (float)$item[$key5['segmentID']];
                                                $maintotal[$key5['segmentID']][] = (float)$item[$key5['segmentID']];
                                                $grandTotal[$key5['segmentID']][] = (float)$item[$key5['segmentID']];
                                                if ($type == 'html') {
                                                    $segment_id = ($rptType == 9)? ",'',".$key5['segmentID']: 0;
                                                    echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["GLAutoID"] . '\',\'' . $item["masterCategory"] . '\',\'' . $item["GLDescription"] . '\',\'' . $value["fieldName"] . '\''.$segment_id.')">' . number_format($item[$key5['segmentID']], $item[$value["fieldName"] . "DecimalPlaces"]) . '</a></td>';
                                                } else {
                                                    echo '<td class="text-right">' . number_format($item[$key5['segmentID']], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                }
                                            }
                                        }
                                        $subtotalHorizontal[] = $total;
                                        $maintotalHorizontal[] = $total;
                                        $grandTotalHorizontal[] = $total;
                                        if ($isRptCost) {
                                            echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($isLocCost) {
                                            echo "<td class='text-right'>" . number_format($total, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        echo "</tr>";
                                        $i++;
                                    }

                                    echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total--> " . $key2 . "</strong></div></td>"; /*display total of each sub category*/
                                    foreach ($fieldNameDetails as $key6 => $value) {
                                        foreach ($segment as $key7) {
                                            $sum = array_sum($subtotal[$key7['segmentID']]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($subtotalHorizontal);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                                if ($key == "Cost of Goods Sold") { /*display total of Cost of Goods Sold*/
                                    echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    echo "<tr><td class=''><div><strong>$tot<!--TOTAL--> " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($segment as $key9) {
                                            $sum = array_sum($maintotal[$key9['segmentID']]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                    $grosspro= $this->lang->line('finance_common_gross_profit');

                                    echo "<tr><td colspan='10'>&nbsp;</td></tr>"; /*display gross profit*/
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$grosspro<!--GROSS PROFIT--></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($segment as $key9) {
                                            $sum = array_sum($grandTotal[$key9['segmentID']]);
                                            if ($isRptCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($grandTotalHorizontal);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                } else if ($key == "Expense") { /*display expense total*/
                                    echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    $totalca= $this->lang->line('finance_common_total');
                                    echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$totalca<!--TOTAL--> " . strtoupper($key) . "</div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($segment as $key9) {
                                            $sum = array_sum($maintotal[$key9['segmentID']]);
                                            if ($isRptCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo " </tr > ";
                                } else {
                                    echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                    echo "<tr><td class=''><div><strong>TOTAL " . strtoupper($key) . "</strong></div></td>";
                                    foreach ($fieldNameDetails as $key8 => $value) {
                                        foreach ($segment as $key9) {
                                            $sum = array_sum($maintotal[$key9['segmentID']]);
                                            if ($isRptCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                            if ($isLocCost) {
                                                echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    $sum = array_sum($maintotalHorizontal);
                                    if ($isRptCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                            $netpro= $this->lang->line('finance_common_net_profit_loss');
                            echo "<tr><td class='reporttotalblack'><div style='margin-left: 60px'>$netpro<!--NET PROFIT / LOSS--></div></td>";
                            foreach ($fieldNameDetails as $key10 => $value) {
                                foreach ($segment as $key11) {
                                    $sum = array_sum($grandTotal[$key11['segmentID']]);
                                    if ($isRptCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                }
                            }
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