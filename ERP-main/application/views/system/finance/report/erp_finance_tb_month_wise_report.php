<!---- =============================================
-- File Name : erp_finance_tb_month_wise_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 15 - September 2016
-- Description : This file contains Trial Balance.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');
$curre=$this->lang->line('common_currency');
$debit=$this->lang->line('finance_common_debit');
    $credit=$this->lang->line('finance_common_credit');

$amount = '';
$isRptCost = false;
$isLocCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
        $amount = "companyReportingAmount";
    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
        $amount = "companyLocalAmount";
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_finance_tb', 'Trial Balance');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_tb_trial_balance_month_wise');?><!--Trial Balance Month Wise--></div>
            <div

                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row">
        <div class="pull-right">
            <?php
            if ($isRptCost) {
                echo '<div class="col-md-12"><strong>'.$curre.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_reporting_currency'] . '</div>';

            }
            if ($isLocCost) {
                echo '<div class="col-md-12"><strong>'.$curre.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_default_currency'] . '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed hoverTable1" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('finance_common_primary_code');?><!--Primary Code--></th>
                            <th rowspan="2"><?php echo $this->lang->line('finance_common_secondary_code');?><!--Secondary Code--></th>
                            <th rowspan="2">
                                <div style="width: 300px"><?php echo $this->lang->line('finance_rs_tb_account_description');?><!--Account Description--></div>
                            </th>
                            <th rowspan="2"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th colspan="2"><?php echo $this->lang->line('finance_common_opening_balance');?><!--Opening Balance--></th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th colspan="2">' . $val . '</th>';
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finance_common_debit');?><!--Debit--></th>
                            <th><?php echo $this->lang->line('finance_common_credit');?><!--Credit--></th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $val) {
                                    echo '<th>'.$debit.'<!--Debit--></th>';
                                    echo '<th>'.$credit.'<!--Credit--></th>';
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $newArray = array();
                        $openingBalanceCredit = 0;
                        $openingBalanceDebit = 0;
                        foreach ($output as $val) {
                            if ($val["openingBalance"] < 0) {
                                $openingBalanceCredit += $val["openingBalance"];
                            } else {
                                $openingBalanceDebit += $val["openingBalance"];
                            }
                            echo "<tr class='hoverTr'>";
                            echo "<td>" . $val["systemAccountCode"] . "</td>";
                            echo "<td>" . $val["GLSecondaryCode"] . "</td>";
                            echo "<td>" . $val["GLDescription"] . "</td>";
                            echo "<td>" . $val["masterCategory"] . "</td>";
                            if ($isLocCost) {
                                echo print_debit_credit($val["openingBalance"], $val["companyLocalAmountDecimalPlaces"]);
                            }
                            if ($isRptCost) {
                                echo print_debit_credit($val["openingBalance"], $val["companyReportingAmountDecimalPlaces"]);
                            }

                            if (!empty($month)) {
                                foreach ($month as $key => $val2) {
                                    $newArray[$key][] = +$val[$key];
                                    if ($isLocCost) {
                                        if ($type == "html") {
                                            echo print_debit_credit($val[$key], $val["companyLocalAmountDecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $amount, true, $key);
                                        } else {
                                            echo print_debit_credit($val[$key], $val["companyLocalAmountDecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $amount, false, $key);
                                        }
                                    }
                                    if ($isRptCost) {
                                        if ($type == "html") {
                                            echo print_debit_credit($val[$key], $val["companyReportingAmountDecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $amount, true, $key);
                                        }else{
                                            echo print_debit_credit($val[$key], $val["companyReportingAmountDecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $amount, false, $key);
                                        }
                                    }
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                        <tr>
                            <td>-</td>
                            <td><?php echo $this->lang->line('finance_rs_tb_retained_earning');?><!--Retained Earnings--></td>
                            <td>-</td>
                            <td>-</td>
                            <?php
                            $openingBalanceRetain = ($openingBalanceDebit - abs($openingBalanceCredit)) * -1;
                            if ($openingBalanceRetain < 0) {
                                $openingBalanceCredit += $openingBalanceRetain;
                            } else {
                                $openingBalanceDebit += $openingBalanceRetain;
                            }
                            if ($isLocCost) {
                                echo print_debit_credit($openingBalanceRetain, $this->common_data['company_data']['company_default_decimal']);
                            }
                            if ($isRptCost) {
                                echo print_debit_credit($openingBalanceRetain, $this->common_data['company_data']['company_reporting_decimal']);
                            }

                            if (!empty($month)) {
                                foreach ($month as $key => $val2) {
                                    $newArray2 = $newArray[$key];
                                    $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($newArray2 as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);
                                    $amount = ($positiveAmount - abs($negativeAmount)) * -1;
                                    if ($isLocCost) {
                                        echo print_debit_credit($amount, $this->common_data['company_data']['company_default_decimal']);
                                    }
                                    if ($isRptCost) {
                                        echo print_debit_credit($amount, $this->common_data['company_data']['company_reporting_decimal']);
                                    }
                                }
                            }
                            ?>
                        </tr>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <?php
                            if ($isLocCost) {
                                echo '<td class="text-right reporttotal">' . number_format($openingBalanceDebit, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                echo '<td class="text-right reporttotal">' . number_format(abs($openingBalanceCredit), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                            }
                            if ($isRptCost) {
                                echo '<td class="text-right reporttotal">' . number_format($openingBalanceDebit, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                echo '<td class="text-right reporttotal">' . number_format(abs($openingBalanceCredit), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                            }

                            if (!empty($month)) {
                                foreach ($month as $key => $val2) {
                                    $newArray2 = $newArray[$key];
                                    $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($newArray2 as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);
                                    $amount = ($positiveAmount - abs($negativeAmount)) * -1;
                                    if ($amount < 0) {
                                        $negativeAmount += $amount;
                                    } else {
                                        $positiveAmount += $amount;
                                    }
                                    if ($isLocCost) {
                                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    }
                                    if ($isRptCost) {
                                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                    }
                                }
                            }
                            ?>
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

    $('#tbl_report').tableHeadFixer({
        head: true,
        foot: false,
        left: 4,
        right: 0,
        'z-index': 0
    });
</script>
