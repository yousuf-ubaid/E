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
            echo export_buttons('tbl_customer_ledger', 'Farm Ledger');
        } ?>
    </div>
</div>
<div id="tbl_customer_ledger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Farm Ledger</div>
            <div
                class="text-center reportHeaderColor"> <?php echo "<strong>As of : </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12" style="height: 380px; overflow: auto;">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th>#</th>
                            <th>Farm</th>
                            <th>Batch Code</th>
                            <th>Doc Date</th>
                            <th>Doc Type</th>
                            <th>Doc Number</th>
                            <th>Narration</th>
                            <?php
                            if (!empty($caption)) {
                                echo '<th>' . $currency . '<!--Currency--></th>';
                                foreach ($caption as $val) {
                                    if ($val == "Transaction Currency") {
                                        echo '<th>Debit</th>';
                                        echo '<th>Credit</th>';
                                    } else {
                                        if ($val == "Reporting Currency") {
                                            echo '<th>Debit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                            echo '<th>Credit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        }
                                        if ($val == "Local Currency") {
                                            echo '<th>Debit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                            echo '<th>Credit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 10;
                        $category = array();
                        $date_format = date_format_policy();
                        foreach ($output as $val) {
                            $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["farmSystemCode"] . " - " . $val["farmName"]][] = $val;
                        }
                        if (!empty($category)) {
                            $a = 1;
                            foreach ($category as $key => $glcodes) {
                                $grandtotal = array();
                                echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                foreach ($glcodes as $key2 => $customer) {
                                    $subtotal = array();
                                    foreach ($customer as $key3 => $val) {
                                        if($val["documentCode"] == 'BBSV' || $val["documentCode"] == 'BBRV' || $val["documentCode"] == 'BBJV')
                                        {
                                            $val["documentCode"] = 'BBPV';
                                        }
                                        echo "<tr class='hoverTr'>";
                                        echo "<td>" . $a . "</td>";
                                        echo "<td>" . $key2 . "</td>";
                                        echo "<td>";
                                        if($val["batchCode"]){
                                            echo $val["batchCode"];
                                        } else {
                                            echo '-';
                                        }
                                        echo "</td>";
                                        if (input_format_date($val["documentDate"], $date_format) == '1970-01-01') {
                                            echo "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0'>" . $val["documentDate"] . "</div></td>";
                                        } else {
                                            echo "<td><div style='margin-left: 30px'>" . $val["documentDate"] . "</div></td>";
                                        }
                                        echo "<td>" . $val["documentName"] . "</td>";
                                        echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentCode"] . '\',\'' . $val["documentMasterAutoID"] . '\',\' '. $val["batchMasterID"] .'\')">' . $val["documentSystemCode"] . '</a></td>';
                                        echo "<td>" . $val["documentNarration"] . "</td>";
                                        if (!empty($fieldName)) {
                                            echo "<td>" . $val["transactionCurrency"] . "</td>";
                                            foreach ($fieldName as $val2) {
                                                $subtotal[$val2 . 'Credit'][] = (float)$val[$val2 . 'Credit'];
                                                $subtotal[$val2 . 'Debit'][] = (float)$val[$val2 . 'Debit'];
                                                $grandtotal[$val2 . 'Credit'][] = (float)$val[$val2 . 'Credit'];
                                                $grandtotal[$val2 . 'Debit'][] = (float)$val[$val2 . 'Debit'];
                                                if ($val2 == 'transactionAmount') {
                                                    echo "<td class='text-right'>" . format_number($val[$val2. 'Debit'], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                    echo "<td class='text-right'>" . format_number($val[$val2 . 'Credit'], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                } else {
                                                    echo "<td class='text-right'>" . format_number($val[$val2 . 'Debit'], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                    echo "<td class='text-right'>" . format_number($val[$val2 . 'Credit'], $val[$val2 . "DecimalPlaces"]) . "</td>";
                                                }
                                            }
                                        }
                                        echo "</tr>";
                                        $a++;
                                    }

                                    echo "<tr>"; /*Net Total for Credit And Debit*/
                                    if ($isLocCost || $isRptCost) {
                                        if ($isTransCost) {
                                            echo "<td colspan='10'><div style='margin-left: 30px'></div></td>";
                                        } else {
                                            echo "<td colspan='8'><div style='margin-left: 30px'></div></td>";
                                        }
                                    }
                                    if (!empty($fieldName)) {
                                        foreach ($fieldName as $val2) {
                                            if ($val2 == "companyLocalAmount") {
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2 . 'Debit']), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2 . 'Credit']), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            }
                                            if ($val2 == "companyReportingAmount") {
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2 . 'Debit']), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($subtotal[$val2 . 'Credit']), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                            }
                                        }
                                    }
                                    echo "</tr>";

                                    echo "<tr>"; /*Sum of net total for credit and debit amounts*/
                                    if ($isLocCost || $isRptCost) {
                                        if ($isTransCost) {
                                            echo "<td colspan='10'><div style='margin-left: 30px'>$netbalance<!--Net Balance--></div></td>";
                                        } else {
                                            echo "<td colspan='8'><div style='margin-left: 30px'>$netbalance<!--Net Balance--></div></td>";
                                        }
                                    }
                                    if (!empty($fieldName)) {
                                        foreach ($fieldName as $val2) {
                                            if ($val2 == "companyLocalAmount") {
                                                echo "<td class='text-right reporttotal'>" . format_number((array_sum($subtotal[$val2 . 'Debit']) + array_sum($subtotal[$val2 . 'Credit'])), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class=''></td>";
                                            }
                                            if ($val2 == "companyReportingAmount") {
                                                echo "<td class='text-right reporttotal'>" . format_number((array_sum($subtotal[$val2 . 'Debit']) + array_sum($subtotal[$val2 . 'Credit'])), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                echo "<td class=''></td>";
                                            }
                                        }
                                    }
                                    echo "</tr>";
                                }
                                echo "<tr><td colspan='" . $count . "'>&nbsp;</td></tr>";
                                echo "<tr>";
                                if ($isLocCost || $isRptCost) {
                                    if ($isTransCost) {
                                        echo "<td colspan='10'><div style='margin-left: 30px'><strong>$subtot<!--Sub Total--></strong></div></td>";
                                    } else {
                                        echo "<td colspan='8'><div style='margin-left: 30px'><strong>$subtot<!--Sub Total--></strong></div></td>";
                                    }
                                }
                                if (!empty($fieldName)) {
                                    foreach ($fieldName as $val2) {
                                        if ($val2 == "companyLocalAmount") {
                                            echo "<td class='text-right reporttotal'>" . format_number((array_sum($grandtotal[$val2 . 'Debit']) + array_sum($grandtotal[$val2 . 'Credit'])), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                            echo "<td></td>";
                                        }
                                        if ($val2 == "companyReportingAmount") {
                                            echo "<td class='text-right reporttotal'>" . format_number((array_sum($grandtotal[$val2 . 'Debit']) + array_sum($grandtotal[$val2 . 'Credit'])), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                    }
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                        </tbody>
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