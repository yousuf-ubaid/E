<!---- =============================================
-- File Name : erp_finance_balance_sheet_month_wise_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 15 - September 2016
-- Description : This file contains Balance Sheet.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');
$currency=$this->lang->line('common_currency');
$tot=$this->lang->line('common_total');
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
        <?php echo export_buttons('tbl_finance_tb', 'Balance Sheet'); ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_bs_balance_sheet_month_wise');?><!--Balance Sheet Month Wise--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
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
                <div class="fixHeader_Div">
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        foreach ($output as $val) {
                            $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][] = $val;
                        }
                        if (!empty($category)) {
                            $grandTotal = array();
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                $maintotal = array();
                                
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                                    
                                    foreach ($subCategory as $key3 => $subsubCategory) {
                                        echo "<tr><td><div style='margin-left: 60px' class='subsubCategoryHead'>" . $key3 . "</div></td></tr>";
                                        $subtotal = array();
                                        $i = 1;
                                        $count = count($subsubCategory);
                                        foreach ($subsubCategory as $item) {
                                            echo "<tr class='hoverTr'>";
                                            echo "<td><div style='margin-left: 90px'>" . $item["GLDescription"] . "</div></td>";
                                            foreach ($fieldNameDetails as $key4 => $value) {
                                                foreach ($month as $key5 => $value2) {
                                                    $subtotal[$key5][] = (float)$item[$key5];
                                                    $maintotal[$key5][] = (float)$item[$key5];
                                                    $grandTotal[$key5][] = (float)$item[$key5];
                                                    if ($item["GLDescription"] == 'Retained Earnings') {
                                                        echo '<td class="text-right">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</td>';
                                                    } else {
                                                        echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $item["GLAutoID"] . '\',\'' . $item["masterCategory"] . '\',\'' . $item["GLDescription"] . '\',\'' . $value["fieldName"] . '\',\'' . $key5 . '\')">' . number_format($item[$key5], $item[$value["fieldName"] . "DecimalPlaces"]) . '</a></td>';
                                                    }
                                                }
                                            }

                                            echo "</tr>";
                                            $i++;
                                           
                                        }
                                        echo "<tr><td class=''><div style='margin-left: 60px'><strong>$tot<!--Total-->  " . $key3 . "</strong></div></td>";
                                        foreach ($fieldNameDetails as $key6 => $value) {
                                            foreach ($month as $key7 => $value2) {
                                                $sum = array_sum($subtotal[$key7]);
                                                if ($value['fieldName'] == "companyLocalAmount") {
                                                    echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                }
                                                if ($value['fieldName'] == "companyReportingAmount") {
                                                    echo "<td class='reportsubtotal text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                }
                                            }
                                        }
                                        echo "</tr>";
                                    }
                                }
                                echo "<tr><td colspan='10'>&nbsp;</td></tr>";
                                echo "<tr><td class='reporttotalblack'><div style='margin-left: 90px'>$totc<!--TOTAL--> " . $key . "</div></td>";
                                foreach ($fieldNameDetails as $key8 => $value) {
                                    foreach ($month as $key9 => $value2) {
                                        $sum = array_sum($maintotal[$key9]);
                                        if ($value['fieldName'] == "companyLocalAmount") {
                                            echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if ($value['fieldName'] == "companyReportingAmount") {
                                            echo "<td class='reporttotalblack text-right'>" . number_format($sum, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                    }
                                }
                                echo "</tr>";
                            }
                            /*echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                            echo "<tr><td class='reporttotal'><div style='margin-left: 90px'>Grant Total</div></td>";
                            foreach ($fieldNameDetails as $key10 => $value) {
                                foreach ($month as $key11 => $value2) {
                                    $sum = array_sum($grandTotal[$key11]);
                                    echo "<td class='reporttotal text-right'>" . number_format($sum) . "</td>";
                                }
                            }
                            echo " </tr > ";*/
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
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*"No Records Found!"*/
            }
            ?>
        </div>
    </div>
</div>
<script>

    $('#tbl_report').tableHeadFixer({
        head: true,
        foot: false,
        left: 1,
        right: 0,
        'z-index': 0
    });
</script>