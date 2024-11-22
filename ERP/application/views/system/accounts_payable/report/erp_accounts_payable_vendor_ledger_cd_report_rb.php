<!---- =============================================
-- File Name : erp_accounts_payable_vendor_ledger_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Payable
-- Create date : 18 - April 2019
-- Description : This file contains Vendor Ledger (Template for Buyback (Credit/Debit/running Balance)).

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$datefrom=$this->lang->line('accounts_payable_reports_vl_date_from'); /*Date from language*/
$dateto=$this->lang->line('accounts_payable_reports_vl_date_to');  /*Date To language*/

$PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+

$this->lang->load('common', $primaryLanguage);
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
<?php if($PostDatedChequeManagement == 1){?>
    <div class="row">
        <div class="col-md-12">
            <?php if ($type == 'html') {
                echo export_buttons('tbl_vendor_ledger', 'Vendor Ledger');
            } ?>
        </div>
    </div>
    <div id="tbl_vendor_ledger">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center reportHeaderColor">
                    <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                </div>
                <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('accounts_payable_reports_vl_vendor_ledger');?><!--Vendor Ledger--></div>
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
                            <tr>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_date');?><!--Doc Date--></th>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_type');?><!--Doc Type--></th>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_number');?><!--Doc Number--></th>
                                <th>Doc Due Date</th>
                                <th>Supplier Invoice Code</th>
                                <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                                <?php
                                if (!empty($caption)) {
                                    foreach ($caption as $val) {
                                        if ($val == "Transaction Currency") {
                                            $currency=$this->lang->line('common_currency');
                                            echo '<th>'.$currency.'<!--Currency--></th>';
                                            //echo '<th>' . $val . '</th>';
                                            echo '<th>Debit</th>';
                                            echo '<th>Credit</th>';
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
                            </tr>
                            </thead>
                            <?php
                            $count = 10;
                            $pdctotal = '';
                            $category = array();
                            foreach ($output as $val) {
                                if ($isTransCost) {
                                    $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["type"]][$val["transactionCurrency"]][] = $val;
                                } else {
                                    $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["type"]][] = $val;
                                }
                            }

                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    $grandtotal = array();
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $suppliers) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        $subtotal = array();
                                        $subtotalcred = array();
                                        $subtotaldeb = array();
                                        $runningBalLoc = 0;
                                        $runningBalReprt = 0;
                                        foreach ($suppliers as $key3 => $type1) {
                                            {
                                                if($key3 == 2)
                                                {
                                                    echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'><b><u>Post Dated Cheques</u></b></div></td></tr>";
                                                }

                                                if ($isTransCost) {
                                                    foreach ($type1 as $key4 => $currencyArr) {
                                                        $Transsubtotaldeb = array();
                                                        $Transsubtotalcred = array();
                                                        $decimalPlace = 2;
                                                        foreach ($currencyArr as $key5 => $val) {
                                                            echo "<tr class='hoverTr'>";
                                                            if ($val["documentDate"] == "1970-01-01") {
                                                                echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0; '>" . $val["documentDate"] . "</div></td>";
                                                            } else {
                                                                echo "<td><div style='margin-left: 30px;'>" . $val["documentDate"] . "</div></td>";
                                                            }
                                                            echo "<td>" . $val["documentCode"] . "</td>";

                                                            if ($type == 'html') {
                                                                echo '<td><a href="#"  class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',' . $val["documentMasterAutoID"] . ')">' . $val["documentSystemCode"] . '</a></td>';
                                                            } else {
                                                                echo '<td>' . $val["documentSystemCode"] . '</td>';

                                                            }
                                                            if(isset($val["supplierInvoiceDate"]) || (!empty($val["supplierInvoiceDate"])))
                                                            {
                                                                echo "<td>" .$val["supplierInvoiceDate"]."</td>";
                                                            }else
                                                            {
                                                                echo "<td>".' '."</td>";
                                                            }

                                                            if(isset($val["supplierInvoiceNo"]) || (!empty($val["supplierInvoiceNo"])))
                                                            {
                                                                echo "<td>" .$val["supplierInvoiceNo"]."</td>";
                                                            }else
                                                            {
                                                                echo "<td>".' '."</td>";
                                                            }

                                                            if(isset($val["documentNarration"]) || (!empty($val["documentNarration"])))
                                                            {
                                                                echo "<td>" . $val["documentNarration"] . "</td>";
                                                            }else
                                                            {
                                                                echo "<td>".' '."</td>";
                                                            }
                                                            /*  echo "<td>" . $val["supplierInvoiceDate"] . "</td>";
                                                              echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                                              echo "<td>" . $val["documentNarration"] . "</td>";*/
                                                            if (!empty($fieldName)) {
                                                                foreach ($fieldName as $val2) {
                                                                    if($key3 == 2)
                                                                    {
                                                                        $subtotal[$val2][] = (float)$val[$val2]*-1;
                                                                    }else
                                                                    {
                                                                        $subtotal[$val2][] = (float)$val[$val2];
                                                                    }
                                                                    if($key3 == 2)
                                                                    {
                                                                        $grandtotal[$val2][] = (float)$val[$val2]*-1;
                                                                    }else
                                                                    {
                                                                        $grandtotal[$val2][] = (float)$val[$val2];
                                                                    }

                                                                    /*  $subtotal[$val2][] = (float)$val[$val2];
                                                                      $grandtotal[$val2][] = (float)$val[$val2];*/
                                                                    if ($val2 == 'transactionAmount') {
                                                                        $decimalPlace = $val[$val2 . "DecimalPlaces"];
                                                                        echo "<td>" . $val["transactionCurrency"] . "</td>";
                                                                        //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";

                                                                        if($key3 == 2)
                                                                        {
                                                                            $transammnt=($val[$val2]*-1);
                                                                        }else
                                                                        {
                                                                            $transammnt=($val[$val2]);
                                                                        }
                                                                        if($transammnt<0){
                                                                            if($key3 == 2)
                                                                            {
                                                                                $pdctotal= (float)$val[$val2]*-1;
                                                                            }else
                                                                            {

                                                                                $pdctotal= (float)$val[$val2];
                                                                            }
                                                                            $Transsubtotalcred[$val2][] =$pdctotal;
                                                                            $Transsubtotaldeb[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            echo "<td class='text-right'>" . format_number($transammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        }else{
                                                                            $Transsubtotaldeb[$val2][] = (float)$val[$val2];
                                                                            $Transsubtotalcred[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . $transammnt . "</td>";
                                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        }
                                                                    } else {
                                                                        //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        if ($val2 == 'companyLocalAmount') {

                                                                            if($key3 == 2)
                                                                            {
                                                                                $locammnt=($val[$val2]*-1);
                                                                            }else
                                                                            {
                                                                                $locammnt=($val[$val2]);
                                                                            }

                                                                            if($locammnt<0){
                                                                                if($key3 == 2)
                                                                                {

                                                                                    $pdctotal= (float)$val[$val2]*-1;
                                                                                }else
                                                                                {

                                                                                    $pdctotal= (float)$val[$val2];
                                                                                }
                                                                                $subtotalcred[$val2][] =$pdctotal;
                                                                                $subtotaldeb[$val2][] = (float)0;
                                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                                echo "<td class='text-right'>" . format_number($locammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            }else{
                                                                                $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                                $subtotalcred[$val2][] = (float)0;
                                                                                echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            }
                                                                            echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalLoc), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            $runningBalLoc += $val[$val2];
                                                                        } else {
                                                                            if($key3 == 2)
                                                                            {
                                                                                $locammnt=($val[$val2]*-1);
                                                                            }else
                                                                            {
                                                                                $locammnt=($val[$val2]);
                                                                            }

                                                                            if($locammnt<0){
                                                                                if($key3 == 2)
                                                                                {
                                                                                    $pdctotal= (float)$val[$val2]*-1;
                                                                                }else
                                                                                {

                                                                                    $pdctotal= (float)$val[$val2];
                                                                                }
                                                                                $subtotalcred[$val2][] =$pdctotal;
                                                                                $subtotaldeb[$val2][] = (float)0;
                                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                                echo "<td class='text-right'>" . format_number($locammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            }else{
                                                                                $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                                $subtotalcred[$val2][] = (float)0;
                                                                                echo "<td class='text-right'>" . $locammnt . "</td>";
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
                                                        $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                        echo "<td colspan='7'><div style='margin-left: 30px'><b>Sub Total </b> &nbsp;</div></td>";
                                                        if (!empty($fieldName)) {
                                                            foreach ($fieldName as $val2) {
                                                                if ($val2 == "transactionAmount") {
                                                                    //$netbal=0;
                                                                    // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($Transsubtotaldeb[$val2]), $decimalPlace) . "</td>";
                                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($Transsubtotalcred[$val2]), $decimalPlace) . "</td>";
                                                                    echo "<td>&nbsp;</td>";
                                                                    //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                                }
                                                            }
                                                        }
                                                        echo "</tr>";

                                                    }
                                                    echo "<tr>";
                                                    if ($isLocCost || $isRptCost) {
                                                        $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                        echo "<td colspan='9'><div style='margin-left: 30px'>&nbsp;</div></td>";
                                                    }
                                                    if (!empty($fieldName)) {
                                                        foreach ($fieldName as $val2) {
                                                            if ($val2 == "companyLocalAmount") {
                                                                //$netbal=0;
                                                                // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                               echo "<td>&nbsp;</td>";
                                                                //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                            }
                                                            if ($val2 == "companyReportingAmount") {
                                                                //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td>&nbsp;</td>";
                                                                //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                            }

                                                        }
                                                    }
                                                    echo "</tr>";

                                                } else {
                                                    foreach ($type1 as $key4 => $val) {
                                                        echo "<tr class='hoverTr'>";
                                                        if ($val["documentDate"] == "1970-01-01") {
                                                            echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0; '>" . $val["documentDate"] . "</div></td>";
                                                        } else {
                                                            echo "<td><div style='margin-left: 30px;'>" . $val["documentDate"] . "</div></td>";
                                                        }
                                                        echo "<td>" . $val["documentCode"] . "</td>";

                                                        if ($type == 'html') {
                                                            echo '<td><a href="#"  class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',' . $val["documentMasterAutoID"] . ')">' . $val["documentSystemCode"] . '</a></td>';
                                                        } else {
                                                            echo '<td>' . $val["documentSystemCode"] . '</td>';

                                                        }
                                                        if(isset($val["supplierInvoiceDate"]) || (!empty($val["supplierInvoiceDate"])))
                                                        {
                                                            echo "<td>" .$val["supplierInvoiceDate"]."</td>";
                                                        }else
                                                        {
                                                            echo "<td>".' '."</td>";
                                                        }

                                                        if(isset($val["supplierInvoiceNo"]) || (!empty($val["supplierInvoiceNo"])))
                                                        {
                                                            echo "<td>" .$val["supplierInvoiceNo"]."</td>";
                                                        }else
                                                        {
                                                            echo "<td>".' '."</td>";
                                                        }

                                                        if(isset($val["documentNarration"]) || (!empty($val["documentNarration"])))
                                                        {
                                                            echo "<td>" . $val["documentNarration"] . "</td>";
                                                        }else
                                                        {
                                                            echo "<td>".' '."</td>";
                                                        }
                                                        /*  echo "<td>" . $val["supplierInvoiceDate"] . "</td>";
                                                          echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                                          echo "<td>" . $val["documentNarration"] . "</td>";*/
                                                        if (!empty($fieldName)) {
                                                            foreach ($fieldName as $val2) {
                                                                if($key3 == 2)
                                                                {
                                                                    $subtotal[$val2][] = (float)$val[$val2]*-1;
                                                                }else
                                                                {
                                                                    $subtotal[$val2][] = (float)$val[$val2];
                                                                }
                                                                if($key3 == 2)
                                                                {
                                                                    $grandtotal[$val2][] = (float)$val[$val2]*-1;
                                                                }else
                                                                {
                                                                    $grandtotal[$val2][] = (float)$val[$val2];
                                                                }

                                                                /*  $subtotal[$val2][] = (float)$val[$val2];
                                                                  $grandtotal[$val2][] = (float)$val[$val2];*/
                                                                if ($val2 == 'transactionAmount') {
                                                                    echo "<td>" . $val["transactionCurrency"] . "</td>";
                                                                    //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";

                                                                    if($key3 == 2)
                                                                    {
                                                                        $transammnt=($val[$val2]*-1);
                                                                    }else
                                                                    {
                                                                        $transammnt=($val[$val2]);
                                                                    }
                                                                    if($transammnt<0){
                                                                        echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        echo "<td class='text-right'>" . format_number($transammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    }else{
                                                                        echo "<td class='text-right'>" . $transammnt . "</td>";
                                                                        echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    }
                                                                } else {
                                                                    //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    if ($val2 == 'companyLocalAmount') {
                                                                        if($key3 == 2)
                                                                        {
                                                                            $locammnt=($val[$val2]*-1);
                                                                        }else
                                                                        {
                                                                            $locammnt=($val[$val2]);
                                                                        }

                                                                        if($locammnt<0){
                                                                            if($key3 == 2)
                                                                            {
                                                                                $pdctotal= (float)$val[$val2]*-1;
                                                                            }else
                                                                            {

                                                                                $pdctotal= (float)$val[$val2];
                                                                            }
                                                                            $subtotalcred[$val2][] =$pdctotal;
                                                                            $subtotaldeb[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            echo "<td class='text-right'>" . format_number($locammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        }else{
                                                                            $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                            $subtotalcred[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        }
                                                                        echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalLoc), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        $runningBalLoc += $val[$val2];
                                                                    } else {
                                                                        if($key3 == 2)
                                                                        {
                                                                            $locammnt=($val[$val2]*-1);
                                                                        }else
                                                                        {
                                                                            $locammnt=($val[$val2]);
                                                                        }

                                                                        if($locammnt<0){
                                                                            if($key3 == 2)
                                                                            {
                                                                                $pdctotal= (float)$val[$val2]*-1;
                                                                            }else
                                                                            {

                                                                                $pdctotal= (float)$val[$val2];
                                                                            }
                                                                            $subtotalcred[$val2][] =$pdctotal;
                                                                            $subtotaldeb[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                            echo "<td class='text-right'>" . format_number($locammnt * -1, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        }else{
                                                                            $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                            $subtotalcred[$val2][] = (float)0;
                                                                            echo "<td class='text-right'>" . $locammnt . "</td>";
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
                                                        $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                        echo "<td colspan='6'><div style='margin-left: 30px'>&nbsp;</div></td>";
                                                    }
                                                    if (!empty($fieldName)) {
                                                        foreach ($fieldName as $val2) {
                                                            if ($val2 == "companyLocalAmount") {
                                                                //$netbal=0;
                                                                // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                               echo "<td>&nbsp;</td>";
                                                                //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                            }
                                                            if ($val2 == "companyReportingAmount") {
                                                                //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                                echo "<td>&nbsp;</td>";
                                                                //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                            }

                                                        }
                                                    }
                                                    echo "</tr>";
                                                }


                                                echo "<tr>";
                                                if ($isLocCost || $isRptCost) {

                                                    if ($isTransCost) {
                                                        $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                        echo "<td colspan='9'><div style='margin-left: 30px'>Net balance</div></td>";
                                                    } else {
                                                        $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                        echo "<td colspan='6'><div style='margin-left: 30px'>Net balance</div></td>";
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
                                                                echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                                                                echo "<td class='text-right '>&nbsp;</td>";
                                                                echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                            }
                                                        }
                                                    }
                                                }
                                                echo "</tr>";

                                            }
                                        }
                                    }
                                    echo "<tr><td colspan='" . $count . "'>&nbsp;</td></tr>";
                                    echo "<tr>";
                                    if ($isLocCost || $isRptCost) {

                                        $gran=$this->lang->line('common_grand_total'); /*grand total language*/

                                        if ($isTransCost) {

                                            echo "<td colspan='9'><div style='margin-left: 30px'><strong>$gran<!--Grand Total--></strong></div></td>";
                                        } else {
                                            echo "<td colspan='6'><div style='margin-left: 30px'><strong>$gran<!--Grand Total--></strong></div></td>";
                                        }
                                    }
                                    if (!empty($fieldName)) {
                                        foreach ($fieldName as $val2) {
                                            if ($val2 == "companyLocalAmount") {
                                                //echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                if(array_sum($grandtotal[$val2])<0){
                                                    echo "<td class=''>&nbsp;</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                    $norecfound=$this->lang->line('common_no_records_found');
                    echo warning_message($norecfound);/*No Records Found!*/
                }
                ?>
            </div>
        </div>
    </div>

<?php } else {?>


    <div class="row">
        <div class="col-md-12">
            <?php if ($type == 'html') {
                echo export_buttons('tbl_vendor_ledger', 'Vendor Ledger');
            } ?>
        </div>
    </div>
    <div id="tbl_vendor_ledger">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center reportHeaderColor">
                    <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                </div>
                <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('accounts_payable_reports_vl_vendor_ledger');?><!--Vendor Ledger--></div>
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
                            <tr>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_date');?><!--Doc Date--></th>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_type');?><!--Doc Type--></th>
                                <th><?php echo $this->lang->line('accounts_payable_reports_vl_doc_number');?><!--Doc Number--></th>
                                <th>Doc Due Date</th>
                                <th>Supplier Invoice Code</th>
                                <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                                <?php
                                if (!empty($caption)) {
                                    foreach ($caption as $val) {
                                        if ($val == "Transaction Currency") {
                                            $currency=$this->lang->line('common_currency');
                                            echo '<th>'.$currency.'<!--Currency--></th>';
                                            //echo '<th>' . $val . '</th>';
                                            echo '<th>Debit</th>';
                                            echo '<th>Credit</th>';
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
                            </tr>
                            </thead>
                            <?php
                            $count = 10;
                            $category = array();
                            foreach ($output as $val) {
                                if ($isTransCost) {
                                    $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["transactionCurrency"]][] = $val;
                                } else {
                                    $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][] = $val;
                                }
                            }

                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    $grandtotal = array();
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $suppliers) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        $subtotal = array();
                                        $subtotalcred = array();
                                        $subtotaldeb = array();
                                        $runningBalLoc = 0;
                                        $runningBalReprt = 0;
                                        if ($isTransCost) {
                                            foreach ($suppliers as $key3 => $currencyS) {
                                                $transsubtotaldeb = array();
                                                $transsubtotalcred = array();
                                                $decimalPlace = 2;
                                                foreach ($currencyS as $key4 => $val) {
                                                    echo "<tr class='hoverTr'>";
                                                    if ($val["documentDate"] == "1970-01-01") {
                                                        echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0; '>" . $val["documentDate"] . "</div></td>";
                                                    } else {
                                                        echo "<td><div style='margin-left: 30px;'>" . $val["documentDate"] . "</div></td>";
                                                    }
                                                    echo "<td>" . $val["documentCode"] . "</td>";
                                                    if ($type == 'html') {
                                                        echo '<td><a href="#"  class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',' . $val["documentMasterAutoID"] . ')">' . $val["documentSystemCode"] . '</a></td>';
                                                    } else {
                                                        echo '<td>' . $val["documentSystemCode"] . '</td>';
                                                    }
                                                    echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                                    echo "<td>" . $val["documentNarration"] . "</td>";
                                                    echo "<td>" . $val["documentNarration"] . "</td>";
                                                    if (!empty($fieldName)) {
                                                        foreach ($fieldName as $val2) {
                                                            $subtotal[$val2][] = (float)$val[$val2];
                                                            $grandtotal[$val2][] = (float)$val[$val2];
                                                            if ($val2 == 'transactionAmount') {
                                                                $decimalPlace = $val[$val2 . "DecimalPlaces"];
                                                                echo "<td>" . $val["transactionCurrency"] . "</td>";
                                                                //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                $transammnt = format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                                if ($transammnt < 0) {
                                                                    $transsubtotalcred[$val2][] = (float)$val[$val2];
                                                                    $transsubtotaldeb[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    echo "<td class='text-right'>" . $transammnt . "</td>";
                                                                } else {
                                                                    $transsubtotaldeb[$val2][] = (float)$val[$val2];
                                                                    $transsubtotalcred[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . $transammnt . "</td>";
                                                                    echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                }
                                                            } else {
                                                                //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                if ($val2 == 'companyLocalAmount') {
                                                                    $locammnt = format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                                    if ($locammnt < 0) {
                                                                        $subtotalcred[$val2][] = (float)$val[$val2];
                                                                        $subtotaldeb[$val2][] = (float)0;
                                                                        echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                    } else {
                                                                        $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                        $subtotalcred[$val2][] = (float)0;
                                                                        echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                        echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    }
                                                                    echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalLoc), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    $runningBalLoc += $val[$val2];
                                                                } else {
                                                                    $locammnt = format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                                    if ($locammnt < 0) {
                                                                        $subtotalcred[$val2][] = (float)$val[$val2];
                                                                        $subtotaldeb[$val2][] = (float)0;
                                                                        echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                        echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                    } else {
                                                                        $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                        $subtotalcred[$val2][] = (float)0;
                                                                        echo "<td class='text-right'>" . $locammnt . "</td>";
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
                                                $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                echo "<td colspan='7'><div style='margin-left: 30px'><b>Sub Total </b> &nbsp;</div></td>";
                                                if (!empty($fieldName)) {

                                                    foreach ($fieldName as $val2) {
                                                        if ($val2 == "transactionAmount") {
                                                            //$netbal=0;
                                                            // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($transsubtotaldeb[$val2]), $decimalPlace) . "</td>";
                                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($transsubtotalcred[$val2]), $decimalPlace) . "</td>";
                                                            echo "<td>&nbsp;</td>";
                                                            //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                        }

                                                    }
                                                }
                                                echo "</tr>";
                                            }
                                            echo "<tr>";
                                            if ($isLocCost || $isRptCost) {
                                                $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                echo "<td colspan='9'><div style='margin-left: 30px'> &nbsp;</div></td>";
                                            }
                                            if (!empty($fieldName)) {
                                                foreach ($fieldName as $val2) {
                                                    if ($val2 == "companyLocalAmount") {
                                                        //$netbal=0;
                                                        // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td>&nbsp;</td>";
                                                        //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                    }
                                                    if ($val2 == "companyReportingAmount") {
                                                        //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td>&nbsp;</td>";
                                                        //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                    }

                                                }
                                            }
                                            echo "</tr>";


                                        } else {
                                            foreach ($suppliers as $key3 => $val) {
                                                echo "<tr class='hoverTr'>";
                                                if ($val["documentDate"] == "1970-01-01") {
                                                    echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0; '>" . $val["documentDate"] . "</div></td>";
                                                } else {
                                                    echo "<td><div style='margin-left: 30px;'>" . $val["documentDate"] . "</div></td>";
                                                }
                                                echo "<td>" . $val["documentCode"] . "</td>";
                                                if ($type == 'html') {
                                                    echo '<td><a href="#"  class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',' . $val["documentMasterAutoID"] . ')">' . $val["documentSystemCode"] . '</a></td>';
                                                } else {
                                                    echo '<td>' . $val["documentSystemCode"] . '</td>';
                                                }
                                                echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                                echo "<td>" . $val["documentNarration"] . "</td>";
                                                echo "<td>" . $val["documentNarration"] . "</td>";
                                                if (!empty($fieldName)) {
                                                    foreach ($fieldName as $val2) {
                                                        $subtotal[$val2][] = (float)$val[$val2];
                                                        $grandtotal[$val2][] = (float)$val[$val2];
                                                        if ($val2 == 'transactionAmount') {
                                                            echo "<td>" . $val["transactionCurrency"] . "</td>";
                                                            //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            $transammnt=format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                            if($transammnt<0){
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                echo "<td class='text-right'>" . $transammnt . "</td>";
                                                            }else{
                                                                echo "<td class='text-right'>" . $transammnt . "</td>";
                                                                echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            }
                                                        } else {
                                                            //echo "<td class='text-right'>" . format_number($val[$val2], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                            if ($val2 == 'companyLocalAmount') {
                                                                $locammnt=format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                                if($locammnt<0){
                                                                    $subtotalcred[$val2][] = (float)$val[$val2];
                                                                    $subtotaldeb[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                }else{
                                                                    $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                    $subtotalcred[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                    echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                }
                                                                echo "<td class='text-right'>" . format_number(($val[$val2] + $runningBalLoc), $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                $runningBalLoc += $val[$val2];
                                                            } else {
                                                                $locammnt=format_number($val[$val2], $val[$val2 . "DecimalPlaces"]);
                                                                if($locammnt<0){
                                                                    $subtotalcred[$val2][] = (float)$val[$val2];
                                                                    $subtotaldeb[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                                    echo "<td class='text-right'>" . $locammnt . "</td>";
                                                                }else{
                                                                    $subtotaldeb[$val2][] = (float)$val[$val2];
                                                                    $subtotalcred[$val2][] = (float)0;
                                                                    echo "<td class='text-right'>" . $locammnt . "</td>";
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
                                                    $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/

                                                    echo "<td colspan='9'><div style='margin-left: 30px'> &nbsp;</div></td>";
                                                } else {
                                                    $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/

                                                    echo "<td colspan='6'><div style='margin-left: 30px'>&nbsp;</div></td>";
                                                }
                                            }
                                            if (!empty($fieldName)) {

                                                foreach ($fieldName as $val2) {
                                                    if ($val2 == "companyLocalAmount") {
                                                        //$netbal=0;
                                                        // echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                        echo "<td>&nbsp;</td>";
                                                        //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                    }
                                                    if ($val2 == "companyReportingAmount") {
                                                        //echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                        echo "<td>&nbsp;</td>";
                                                        //$netbal=array_sum($subtotaldeb[$val2])-$array_sum($subtotalcred[$val2]);
                                                    }

                                                }
                                            }
                                            echo "</tr>";
                                        }

                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {

                                            if ($isTransCost) {
                                                $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                echo "<td colspan='9'><div style='margin-left: 30px'>Net balance</div></td>";
                                            } else {
                                                $netbalance=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net balance language*/
                                                echo "<td colspan='6'><div style='margin-left: 30px'>Net balance</div></td>";
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
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                                                        echo "<td class='text-right '>&nbsp;</td>";
                                                        echo "<td class='text-right reporttotal'>" . format_number($netbal, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    }
                                                }
                                            }
                                        }
                                        echo "</tr>";

                                    }
                                    echo "<tr><td colspan='" . $count . "'>&nbsp;</td></tr>";
                                    echo "<tr>";
                                    if ($isLocCost || $isRptCost) {

                                        $gran=$this->lang->line('common_grand_total'); /*grand total language*/

                                        if ($isTransCost) {

                                            echo "<td colspan='9'><div style='margin-left: 30px'><strong>$gran<!--Grand Total--></strong></div></td>";
                                        } else {
                                            echo "<td colspan='6'><div style='margin-left: 30px'><strong>$gran<!--Grand Total--></strong></div></td>";
                                        }
                                    }
                                    if (!empty($fieldName)) {
                                        foreach ($fieldName as $val2) {
                                            if ($val2 == "companyLocalAmount") {
                                                //echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                if(array_sum($grandtotal[$val2])<0){
                                                    echo "<td class=''>&nbsp;</td>";
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandtotal[$val2]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                    $norecfound=$this->lang->line('common_no_records_found');
                    echo warning_message($norecfound);/*No Records Found!*/
                }
                ?>
            </div>
        </div>
    </div>
<?php }?>
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
