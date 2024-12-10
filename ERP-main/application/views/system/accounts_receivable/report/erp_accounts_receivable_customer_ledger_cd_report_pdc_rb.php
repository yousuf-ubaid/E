<!---- =============================================
-- File Name : erp_accounts_receivable_customer_ledger_cd_report_pdc_rb.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Receivable
-- Create date : 18 - April 2019
-- Description : This file contains Customer Ledger (Template for Buyback (Credit/Debit/running Balance)).

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$datefrom = $this->lang->line('accounts_receivable_common_date_from');
$dateto = $this->lang->line('accounts_receivable_common_date_to');
$currency = $this->lang->line('common_currency');
$netbalance = $this->lang->line('accounts_receivable_common_net_balance');
$subtot = $this->lang->line('accounts_receivable_common_sub_tot');


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
        <?php if ($type == 'html') {
            echo export_buttons('tbl_customer_ledger', 'Customer Ledger');
        } ?>
    </div>
</div>
<div id="tbl_customer_ledger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('accounts_receivable_rs_cl_customer_ledger'); ?>
                <!--Customer Ledger--></div>
            <div
                class="text-center reportHeaderColor"> <?php echo "<strong>$datefrom<!--Date From-->: </strong>" . $from . " - <strong>$dateto<!--Date To-->: </strong>" . $to ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <th rowspan="2">
                            <?php echo $this->lang->line('accounts_receivable_common_doc_date'); ?><!--Doc Date--></th>
                        <th rowspan="2">
                            <?php echo $this->lang->line('accounts_receivable_common_doc_type'); ?><!--Doc Type--></th>
                        <th rowspan="2">
                            <?php echo $this->lang->line('accounts_receivable_common_doc_number'); ?><!--Doc Number--></th>
                        <th rowspan="2"><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
                        <?php
                        if (!empty($caption)) {
                            foreach ($caption as $val) {
                                if ($val == "Transaction Currency") {
                                    echo '<th>' . $currency . '<!--Currency--></th>';
                                    // echo '<th>' . $val . '</th>';
                                    echo '<th>Debit</th>';
                                    echo '<th>Credit</th>';
                                    echo '<th>Balance</th>';
                                } else {
                                    if ($val == "Reporting Currency") {
                                        //echo '<th>' . $val . '(' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        echo '<th>Debit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        echo '<th>Credit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        echo '<th>Balance (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                    }
                                    if ($val == "Local Currency") {
                                        //echo '<th>' . $val . '(' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        echo '<th>Debit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        echo '<th>Credit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        echo '<th>Balance (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                    }
                                }
                            }
                        }
                        ?>
                        </thead>
                        <?php
                        $count = 10;
                        $pdctotal = '';
                        $category = array();
                        $date_format = date_format_policy();
                        foreach ($output as $val) {
                            $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["customerSystemCode"] . " - " . $val["customerName"]][$val["type"]][] = $val;
                        }
                        if (!empty($category)) {
                            foreach ($category as $key => $glcodes) {
                                $grandtotal = array();
                                echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                foreach ($glcodes as $key2 => $customer) {
                                    echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                    $subtotal = array();
                                    $subtotalcred = array();
                                    $subtotaldeb = array();
                                    $runningBalTrans = 0;
                                    $runningBalLoc = 0;
                                    $runningBalReprt = 0;
                                    foreach ($customer as $key3 => $type) {
                                        if($key3 == 2)
                                        {
                                            echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'><b><u>Post Dated Cheques</u></b></div></td></tr>";
                                        }
                                        foreach ($type as $key4 => $val) {
                                            echo "<tr class='hoverTr'>";
                                            if (input_format_date($val["documentDate"], $date_format) == '1970-01-01') {
                                                echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0'>" . $val["documentDate"] . "</div></td>";
                                            } else {
                                                echo "<td><div style='margin-left: 30px'>" . $val["documentDate"] . "</div></td>";
                                            }
                                            echo "<td>" . $val["document"] . "</td>";
                                            echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',' . $val["documentMasterAutoID"] . ')">' . $val["documentSystemCode"] . '</a></td>';
                                            echo "<td>" . $val["documentNarration"] . "</td>";
                                            if (!empty($fieldName)) {
                                                foreach ($fieldName as $val2) {
                                                    if($key3 == 2)
                                                    {
                                                        $subtotal[$val2][] = (float)$val[$val2]*-1;
                                                        $grandtotal[$val2][] = (float)$val[$val2]*-1;
                                                    }else
                                                    {
                                                        $subtotal[$val2][] = (float)$val[$val2];
                                                        $grandtotal[$val2][] = (float)$val[$val2];
                                                    }


                                                    if ($val2 == 'transactionAmount') {
                                                        echo "<td>" . $val["transactionCurrency"] . "</td>";
                                                        //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                       /* if($key3 == 2)
                                                        {
                                                            $transammnt=$val[$val2]*-1;
                                                        }else
                                                        {
                                                            $transammnt=$val[$val2];
                                                        }*/

                                                        if($val[$val2]<0){
                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            echo "<td class='text-right'>" . format_number($val[$val2] * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                        }else{
                                                            echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                        }
                                                        echo "<td class='text-right'>" . format_number(($val[$val2] +$runningBalTrans), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                        $runningBalTrans += $val[$val2];
                                                    } else {
                                                        //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                        if ($val2 == 'companyLocalAmount') {
                                                           /* if($key3 == 2)
                                                            {
                                                                $locammnt=$val[$val2]*-1;
                                                            }else
                                                            {
                                                                $locammnt=format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                            }*/

                                                            if($val[$val2]<0){
                                                                if($key3 == 2)
                                                                {
                                                                    $pdctotal= (float)$val[$val2]*-1;
                                                                }else
                                                                {

                                                                    $pdctotal= (float)$val[$val2];
                                                                }
                                                                $subtotalcred[$val2][] = $pdctotal;
                                                                $subtotaldeb[$val2][] = (float)0;
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                echo "<td class='text-right'>" . format_number($val[$val2] * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            }else{
                                                                $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                $subtotalcred[$val2][] = (float)0;
                                                                echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            }
                                                            echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalLoc), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            $runningBalLoc += $val[$val2];
                                                        } else {
                                                           /* if($key3 == 2)
                                                            {
                                                                $locammnt=format_number($val[$val2]*-1, $val[$val2 . "DecimalPlaces"]);
                                                            }else
                                                            {
                                                                $locammnt=format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                            }*/

                                                            if($val[$val2]<0){
                                                                if($key3 == 2)
                                                                {
                                                                    $pdctotal= (float)$val[$val2]*-1;
                                                                }else
                                                                {

                                                                    $pdctotal= (float)$val[$val2];
                                                                }
                                                                $subtotalcred[$val2][] = $pdctotal;
                                                                $subtotaldeb[$val2][] = (float)0;
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                echo "<td class='text-right'>" . format_number($val[$val2] * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            }else{
                                                                $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                $subtotalcred[$val2][] = (float)0;
                                                                echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            }
                                                            echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalReprt), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            $runningBalReprt += $val[$val2];
                                                        }
                                                    }
                                                }
                                            }
                                            echo "</tr>";
                                        }
                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {
                                            if ($isTransCost) {
                                                echo "<td colspan='8'><div style='margin-left: 30px'>&nbsp;</div></td>";
                                            } else {
                                                echo "<td colspan='4'><div style='margin-left: 30px'>&nbsp;</div></td>";
                                            }
                                        }
                                        if (!empty($fieldName)) {
                                            foreach ($fieldName as $val2) {
                                                if ($val2 == "companyLocalAmount") {
                                                    //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]) * -1, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    echo "<td class='text-right'>&nbsp;</td>";
                                                }
                                                if ($val2 == "companyReportingAmount") {
                                                    //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]) *-1, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                }
                                            }
                                        }
                                        echo "</tr>";


                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {
                                            if ($isTransCost) {
                                                echo "<td colspan='8'><div style='margin-left: 30px'>$netbalance<!--Net Balance--></div></td>";
                                            } else {
                                                echo "<td colspan='4'><div style='margin-left: 30px'>$netbalance<!--Net Balance--></div></td>";
                                            }
                                        }
                                        if (!empty($fieldName)) {
                                            foreach ($fieldName as $val2) {
                                                $netbal=0;

                                                if ($val2 == "companyLocalAmount") {
                                                    $dbamnt=abs(array_sum($subtotaldeb[$val2]));
                                                    $crdamnt=abs(array_sum($subtotalcred[$val2]));
                                                    $netbal=$dbamnt-$crdamnt;
                                                    if($netbal>0){
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                    }else{
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal * -1, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                    }
                                                }
                                                if ($val2 == "companyReportingAmount") {
                                                    $dbamnt=abs(array_sum($subtotaldeb[$val2]));
                                                    $crdamnt=abs(array_sum($subtotalcred[$val2]));
                                                    $netbal=$dbamnt-$crdamnt;
                                                    if($netbal>0){
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                    }else{
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal * -1, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    }
                                                }
                                            }
                                        }
                                        echo "</tr>";
                                    }
                                }
                                echo "<tr><td colspan='" . $count . "'>&nbsp;</td></tr>";
                                echo "<tr>";
                                if ($isLocCost || $isRptCost) {
                                    if ($isTransCost) {
                                        echo "<td colspan='8'><div style='margin-left: 30px'><strong>$subtot<!--Sub Total--></strong></div></td>";
                                    } else {
                                        echo "<td colspan='4'><div style='margin-left: 30px'><strong>$subtot<!--Sub Total--></strong></div></td>";
                                    }
                                }
                                if (!empty($fieldName)) {
                                    foreach ($fieldName as $val2) {
                                        if ($val2 == "companyLocalAmount") {
                                            //echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            if(array_sum($grandtotal[$val2])<0){
                                                echo "<td class=''>&nbsp;</td>";
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class=''>&nbsp;</td>";
                                            }else{
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class=''>&nbsp;</td>";
                                                echo "<td class=''>&nbsp;</td>";
                                            }
                                        }
                                        if ($val2 == "companyReportingAmount") {
                                            //echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            if(array_sum($grandtotal[$val2])<0){
                                                echo "<td class=''>&nbsp;</td>";
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }else{
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class=''>&nbsp;</td>";
                                            }
                                        }
                                    }
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </table>
                </div>
                <?php
            } else {
                $norecfound = $this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
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

<?php
