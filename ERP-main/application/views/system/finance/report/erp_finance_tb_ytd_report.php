<!---- =============================================
-- File Name : erp_finance_tb_ytd_report.php
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
$ytd=$this->lang->line('finance_rs_tb_ytd');

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
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_tb_trial_balance_ytd');?><!--Trial Balance YTD--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('finance_common_primary_code');?><!--Primary Code--></th>
                            <th rowspan="2"><?php echo $this->lang->line('finance_common_secondary_code');?><!--Secondary Code--></th>
                            <th rowspan="2"><?php echo $this->lang->line('finance_rs_tb_account_description');?><!--Account Description--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <?php
                            foreach ($fieldName as $val) {
                                if ($val == "companyReportingAmount") {
                                    echo '<th colspan="2">'.$ytd.'<!--YTD-->(' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                }
                                if ($val == "companyLocalAmount") {
                                    echo '<th colspan="2">'.$ytd.'<!--YTD-->(' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <?php
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $val) {
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
                        foreach ($output as $val) {
                            echo "<tr class='hoverTr'>";
                            echo "<td>" . $val["systemAccountCode"] . "</td>";
                            echo "<td>" . $val["GLSecondaryCode"] . "</td>";
                            echo '<td>' . $val['GLDescription'] . "</td>";
                            echo "<td>" . $val["masterCategory"] . "</td>";
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $key => $val2) {
                                    $newArray[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                    if ($type == "html") {
                                        echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $val2["fieldName"], true);
                                    }else{
                                        echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"], $val['GLAutoID'], $val['masterCategory'], $val['GLDescription'], $val2["fieldName"], false);
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
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $key => $val2) {
                                    $newArray[$val2["fieldName"]][] = ($retain[$val2["fieldName"]] * -1);
                                    echo print_debit_credit($retain[$val2["fieldName"]] * -1, $retain[$val2["fieldName"] . "DecimalPlaces"]);
                                }
                            }
                            ?>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <?php
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $key => $val2) {
                                    $newArray2 = $newArray[$val2['fieldName']];
                                    $pos_arr = array();
                                    $neg_arr = array();
                                    foreach ($newArray2 as $val) {
                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                    }
                                    $positiveAmount = array_sum($pos_arr);
                                    $negativeAmount = array_sum($neg_arr);
                                    if ($val2['fieldName'] == "companyLocalAmount") {
                                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    }
                                    if ($val2['fieldName'] == "companyReportingAmount") {
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