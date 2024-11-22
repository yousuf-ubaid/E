<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('salesOrderReport', 'Vendor Balance Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('accounts_payable_supplier_balance_report'); ?></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('accounts_payable_sm_supplier_code'); ?></th>
                        <th><?php echo $this->lang->line('accounts_payable_sm_secondary_code'); ?></th>
                        <th><?php echo $this->lang->line('common_supplier_name'); ?></th>
                        <th><?php echo $this->lang->line('accounts_payable_ma_supplier_category'); ?></th>
                        <?php
                        if($currency==1){
                        ?>
                        <!--<th>Currency</th>-->
                        <th><?php echo $this->lang->line('accounts_payable_local_amount'); ?> (<span><?php echo $loccurr; ?></span>)</th>
                        <!--<th>Currency</th>-->
                        <th><?php echo $this->lang->line('accounts_payable_reporting_amount'); ?> (<span><span><?php echo $repcurr; ?></span></span>)</th>
                            <?php
                        }else{
                        ?>
                        <th><?php echo $this->lang->line('common_currency'); ?></th>
                        <th><?php echo $this->lang->line('accounts_payable_transaction_amount'); ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if($currency==1){
                        $count = 5;
                    } else {
                        $count = 5;
                    }
                    $category = array();
                    $date_format = date_format_policy();
                    foreach ($details as $val) {
                        $category[$val["systemGLCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][] = $val;
                    }
                    if (!empty($category)) {
                        foreach ($category as $key => $glcodes) {
                            $decimalPlacel = $loccurrDec;
                            $decimalPlacer = $repcurrDec;
                            $transtot=0;
                            $comploctot=0;
                            $reporttot=0;
                            $grandtotal = array();
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                            foreach ($glcodes as $key2 => $customer) {
                                $subtotal = array();
                                    foreach ($customer as $key3 => $val) {
                                        echo "<tr class='hoverTr'>";

                                        echo "<td>" . $val["supplierSystemCode"] . "</td>";
                                        echo '<td>' . $val["secondaryCode"] . '</td>';
                                        echo "<td>" . $val["supplierName"] . "</td>";
                                        echo "<td>" . $val["categoryDescription"] . "</td>";
                                        if ($currency == 1) {
                                            echo '<td align="right">' . number_format((float)$val['companyLocalAmount'] * -1, (int)$val['companyLocalCurrencyDecimalPlaces']) . '</td>';
                                            echo '<td align="right">' . number_format((float)$val['companyReportingAmount'] * -1, (int)$val['companyReportingCurrencyDecimalPlaces']) . '</td>';
                                        } else {
                                            echo '<td>' . $val['transactionCurrency'] . '</td>';
                                            echo '<td align="right">' . number_format((float)$val['transactionAmount'] * -1, (int)$val['transactionCurrencyDecimalPlaces']) . '</td>';

                                        }
                                        echo "</tr>";
                                        $transtot += $val['transactionAmount'];
                                        $comploctot += $val['companyLocalAmount'];
                                        $reporttot += $val['companyReportingAmount'];
                                    }
                        }
                            echo "<tr>";
                            if($currency==1){
                                echo "<td class='' colspan='4' style='font-weight: bold;'>" . $this->lang->line('accounts_payable_reports_vs_sub_total') . "</td>";
                                echo "<td class='reporttotal' align='right' style='font-weight: bold;'>".number_format((float)$comploctot*-1, $decimalPlacel)."<!--Net Balance--></td>";
                                echo "<td class='reporttotal' align='right' style='font-weight: bold;'>".number_format((float)$reporttot*-1, $decimalPlacer)."<!--Net Balance--></td>";
                            } /*else {
                                echo "<td class='' colspan='4' style='font-weight: bold;'>Sub Total</td>";
                                echo "<td class='reporttotal' align='right' style='font-weight: bold;'>".number_format($transtot, $decimalPlace)."<!--Net Balance--></td>";
                            }*/
                        }

                        echo "</tr>";
                    }
                    ?>


                    </tbody>

                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>