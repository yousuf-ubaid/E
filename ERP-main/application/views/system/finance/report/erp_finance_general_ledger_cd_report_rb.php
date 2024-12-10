<!---- =============================================
-- File Name : erp_finance_general_ledger_cd_report_rb.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 17 - April 2019
-- Description : This file contains General Ledger (Template for Buyback (Credit/Debit/running Balance)).

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');

$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');

$amount=$this->lang->line('common_amount');
$balance=$this->lang->line('finance_common_balance');

$subtot=$this->lang->line('finance_common_sub_total');
$tot=$this->lang->line('common_total');
$totcap=$this->lang->line('finance_common_total');

$deb=$this->lang->line('accounts_payable_reports_vs_debit'); /*Debit Language Transalation*/
$cre=$this->lang->line('accounts_payable_reports_vs_credit'); /*Credit Language translation*/


ini_set("max_execution_time","-1");
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
            echo export_buttons('tbl_finance_tb', 'General Ledger');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_gl_general_ledger');?><!--General Ledger--></div>
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
            <!-- <div style="overflow: auto;height: 400px">-->
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <?php
                            if (!empty($caption)) {
                                foreach ($caption as $val) {
                                    if ($val == "Local Currency") {
                                      /*  echo "<th>$amount<!--Amount-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";*/
                                        echo "<th>$deb<!--Amount-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>$cre<!--Amount-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>$balance<!--Balance-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                    } else if ($val == "Reporting Currency") {
                                       /* echo "<th>$amount<!--Amount-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";*/
                                        echo "<th>$deb<!--Amount-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$cre<!--Amount-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$balance<!--Balance-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                    } else if ($val == "Narration") {
                                        if($type == 'html'){
                                            echo "<th class='hide'>" . $val . "</th>";
                                        }
                                        echo "<th>" . $val . "</th>";
                                    } else {
                                        echo "<th>" . $val . "</th>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        $locKey = array_search('companyLocalAmount', array_column($fieldNameDetails, 'fieldName'));
                        $rptKey = array_search('companyReportingAmount', array_column($fieldNameDetails, 'fieldName'));
                        foreach ($output as $val) {
                            $category[$val["GLDescription"]][] = $val;
                        }
                        if (!empty($category)) {
                            $grandTotal = 0;
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td colspan='4'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                $subtotalloc = 0;
                                $subtotallocal = array();
                                $subtotalreporting = array();
                                $subtotalrpt = 0;
                                $carryForwardBSrpt = 0;
                                $carryForwardPLrpt = 0;
                                $carryForwardBSloc = 0;
                                $carryForwardPLloc = 0;
                                //$z = 1;
                                foreach ($mainCategory as $item) {
                                    $i = 1;
                                    $z = 1;
                                    echo "<tr class='hoverTr'>";
                                    foreach ($fieldNameDetails as $key2 => $value) {
                                        if ($i == 1) {
                                            if ($value["fieldName"] == "documentSystemCode") {
                                                if ($item["documentNarration"] == "CF Balance") {
                                                    echo "<td class='" . $value["textAlign"] . "'> <div style='margin-left: 30px'>" . $item[$value["fieldName"]] . "</div></td>";
                                                } else {
                                                    if ($type == 'html') {
                                                        echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $item["documentCode"] . '\',' . $item["documentMasterAutoID"] . ')">' . $item[$value["fieldName"]] . '</div></a></td>';
                                                    } else {
                                                        echo '<td><div style="margin-left: 30px">' . $item[$value["fieldName"]] . '</div></td>';
                                                    }
                                                }
                                            } else {
                                                echo "<td class='" . $value["textAlign"] . "'> <div style='margin-left: 30px'>" . $item[$value["fieldName"]] . "</div></td>";
                                            }
                                        } else {
                                            if ($value["fieldName"] == "documentNarration") {
                                                if($type == 'html') {
                                                    echo "<td class='" . $value["textAlign"] . " hide'>" . $item[$value["fieldName"]] . "</td>";
                                                }
                                                if($type == 'pdf'){
                                                    echo "<td class='" . $value["textAlign"] . " '>" . $item[$value["fieldName"]] . "</td>";
                                                }else{
                                                    echo "<td class='" . $value["textAlign"] . "'>" . trim_value($item[$value["fieldName"]], 40) . "</td>";
                                                }
                                            } else {
                                                if ($value["fieldName"] == "companyLocalAmount") {
                                                    $subtotalloc += $item[$value["fieldName"]];
                                                    $subtotallocal[] = $item[$value["fieldName"]];
                                                    echo print_debit_credit($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]);
                                                    /*echo "<td class='" . $value["textAlign"] . "'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";*/
                                                    if ($item["masterCategory"] == 'BS') {
                                                        if ($z == 1) {
                                                            if ($item["documentNarration"] == "CF Balance") {
                                                                $carryForwardBSloc = $item[$value["fieldName"]];
                                                            }
                                                            echo "<td class='text-right'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                        } else {
                                                            echo "<td class='text-right'>" . number_format(($item[$value["fieldName"]] + $carryForwardBSloc), $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardBSloc += $item[$value["fieldName"]];
                                                        }
                                                    } else if ($item["masterCategory"] == 'PL') {
                                                        if ($z == 1) {
                                                            echo "<td class='text-right'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardPLloc = $item[$value["fieldName"]];
                                                        } else {
                                                            echo "<td class='text-right'>" . number_format(($item[$value["fieldName"]] + $carryForwardPLloc), $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardPLloc += $item[$value["fieldName"]];
                                                        }
                                                    }
                                                } else if ($value["fieldName"] == "companyReportingAmount") {
                                                    $subtotalrpt += $item[$value["fieldName"]];
                                                    $subtotalreporting[] = $item[$value["fieldName"]];
                                                    echo print_debit_credit($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]);
                                                   /* echo "<td class='" . $value["textAlign"] . "'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";*/
                                                    if ($item["masterCategory"] == 'BS') {
                                                        if ($z == 1) {
                                                            if ($item["documentNarration"] == "CF Balance") {
                                                                $carryForwardBSrpt = $item[$value["fieldName"]];
                                                            }
                                                            echo "<td class='text-right'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                        } else {
                                                            echo "<td class='text-right'>" . number_format(($item[$value["fieldName"]] + $carryForwardBSrpt), $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardBSrpt += $item[$value["fieldName"]];
                                                        }
                                                    } else if ($item["masterCategory"] == 'PL') {
                                                        if ($z == 1) {
                                                            echo "<td class='text-right'>" . number_format($item[$value["fieldName"]], $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardPLrpt = $item[$value["fieldName"]];
                                                        } else {
                                                            echo "<td class='text-right'>" . number_format(($item[$value["fieldName"]] + $carryForwardPLrpt), $item[$value["fieldName"] . "DecimalPlaces"]) . "</td>";
                                                            $carryForwardPLrpt += $item[$value["fieldName"]];
                                                        }
                                                    }
                                                } else {
                                                    if ($value["fieldName"] == "documentSystemCode") {
                                                        echo '<td><a href="#" onclick="documentPageView_modal(\'' . $item["documentCode"] . '\',' . $item["documentMasterAutoID"] . ')">' . $item[$value["fieldName"]] . '</a></td>';
                                                    } else {
                                                        echo "<td class='" . $value["textAlign"] . "'>" . $item[$value["fieldName"]] . "</td>";
                                                    }
                                                }
                                            }

                                        }
                                        $i++;
                                        $z++;
                                    }

                                    echo "</tr>";
                                }

                                echo "<tr>";
                                if ($isLocCost) {
                                    echo "<td colspan='" . ($locKey) . "'></td>";
                                } else if ($isRptCost) {
                                    echo "<td colspan='" . ($rptKey) . "'></td>";
                                }
                                if ($isLocCost) {
                                    $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($subtotallocal as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);


                                    echo "<td class='reporttotal text-right'>" . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                 /*   echo "<td class='reporttotal text-right'>" . number_format($subtotalloc, $this->common_data['company_data']['company_default_decimal']) . "</td>";*/
                                    echo "<td></td>";
                                }
                                if ($isRptCost) {  $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($subtotalreporting as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);


                                    echo "<td class='reporttotal text-right'>" . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . "</td>";

                                   /* echo "<td class='reporttotal text-right'>" . number_format($subtotalrpt, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";*/
                                    echo "<td></td>";
                                }
                                echo "</tr>";



                                echo "<tr>";
                                if ($isLocCost) {
                                    echo "<td colspan='" . ($locKey) . "'></td>";
                                } else if ($isRptCost) {
                                    echo "<td colspan='" . ($rptKey) . "'></td>";
                                }
                                if ($isLocCost) {
                                    $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($subtotallocal as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);
                                    $balance = $negativeAmount + $positiveAmount;

                                    if ($balance < 0) {
                                        echo "<td class='text-right'></td><td class='reporttotal text-right'>" . number_format(abs($balance), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    } else {
                                        if ($balance > 0) {
                                            echo "<td class='reporttotal text-right'>" . number_format($balance, $this->common_data['company_data']['company_default_decimal']) . "</td><td class='text-right'></td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                    }
                                }
                                if ($isRptCost) {  $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($subtotalreporting as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);


                                    echo "<td class='reporttotal text-right'>" . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . "</td>";

                                   /* echo "<td class='reporttotal text-right'>" . number_format($subtotalrpt, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";*/
                                    echo "<td></td>";
                                }
                                echo "</tr>";
                            }
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
                $norecfoud=$this->lang->line('common_no_records_found');
                echo warning_message($norecfoud);/*"No Records Found!"*/
            }
            ?>
            <!--</div>-->
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        /*$('#tbl_report').tableHeadFixer({
            head: true,
            foot: false,
            left: 0,
            right: 0,
            'z-index': 0
        });*/
    });
</script>








<?php
/**
 * Created by PhpStorm.
 * User: safeena
 * Date: 4/17/2019
 * Time: 4:05 PM
 */