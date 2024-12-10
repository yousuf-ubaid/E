<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px; margin-right: 1px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_invoice_overdue', 'Invoice Overdue Report', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_invoice_overdue">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Invoice Overdue Report</strong></div>
            <div style="">
                <div> <!--style="height: 600px"-->
                    <table id="tbl_rpt_invoice_overdue" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th style="width: 2%;">#</th>
                            <th style="width: 10%;"><?php echo $this->lang->line('common_invoice_code'); ?></th>
                            <th style="width: 5%;"><?php echo $this->lang->line('common_invoice_date'); ?></th>
                            <th style="width: 5%;">Invoice Due Date</th>
                            <th style="width: 10%;"><?php echo $this->lang->line('common_customer'); ?></th>
                            <th style="width: 5%;"><?php echo $this->lang->line('common_currency'); ?></th>
                            <th style="width: 6%;"><?php echo $this->lang->line('common_amount'); ?></th>
                            <th style="width: 6%;">Settled Amount</th>
                            <th style="width: 6%;"><?php echo $this->lang->line('common_balance'); ?><?php echo $this->lang->line('common_amount'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 1;
                        if($details){
                        $totalAmount = 0;
                        $totalSettledAmount = 0;
                        $totalBalanceAmount = 0;
                        $currenyDecimal = 0;

                        if($currency == 'transactionAmount'){
                            foreach ($details as $val){
                                $category[$val[$currency . 'currency']][] = $val;
                            }
                            if(!empty($category)) {
                                    foreach ($category as $key =>$currency_arr) {
                                        $TRtotalAmount = 0;
                                        $TRtotalSettledAmount = 0;
                                        $TRtotalBalanceAmount = 0;
                                        $TRcurrenyDecimal = 0;
                                        $b = 1;
                                        foreach ($currency_arr AS $curr) {
                                            ?>
                                            <tr>
                                                <td style=""><?php echo $b ?></td>
                                                <td><a href="#" class="drill-down-cursor"
                                                       onclick="documentPageView_modal('CINV',<?php echo $curr["invoiceAutoID"] ?>)"><?php echo $curr['bookingInvCode'] ?></a>
                                                </td>
                                                <td><?php echo $curr['bookingDate'] ?></td>
                                                <td><?php echo $curr['invoiceDueDate'] ?></td>
                                                <td><?php echo $curr['customer'] ?></td>
                                                <td><?php echo $curr[$currency . 'currency'] ?></td>
                                                <td class="text-right"><?php echo number_format($curr[$currency], $curr[$currency . 'DecimalPlaces']) ?></td>
                                                <td class="text-right">
                                                    <?php if ($curr['paid' . $currency] > 0) {?>
                                                    <a href="#" class="drill-down-cursor"
                                                       onclick="invoice_overdue_drilldown(<?php echo $curr["invoiceAutoID"] ?>)"><?php echo number_format($curr['paid' . $currency], $curr[$currency . 'DecimalPlaces']) ?></a>
                                                    <?php } else {
                                                        echo number_format($curr['paid' . $currency], $curr[$currency . 'DecimalPlaces']);
                                                    }?>
                                                </td>
                                                <td class="text-right"><?php echo number_format($curr['balance' . $currency], $curr[$currency . 'DecimalPlaces']) ?></td>
                                            </tr>
                                            <?php
                                            $TRtotalAmount += $curr[$currency];
                                            $TRtotalSettledAmount += $curr['paid' . $currency];
                                            $TRtotalBalanceAmount += $curr['balance' . $currency];
                                            $TRcurrenyDecimal = $curr[$currency . 'DecimalPlaces'];
                                            $b++;
                                        }
                                        echo '<tr>';
                                        echo '<td></td>';
                                        echo '<td colspan="5"><b>Total</b></td>';
                                        echo '<td class="text-right reporttotal">' . number_format($TRtotalAmount, $TRcurrenyDecimal) . '</td>';
                                        echo '<td class="text-right reporttotal">' . number_format($TRtotalSettledAmount, $TRcurrenyDecimal) . '</td>';
                                        echo '<td class="text-right reporttotal">' . number_format($TRtotalBalanceAmount, $TRcurrenyDecimal) . '</td>';
                                        echo '</tr>';

                                    }
                                }
                            } else {
                                foreach ($details as $val){?>
                                <tr>
                                    <td style=""><?php echo $a ?></td>
                                    <td><a href="#" class="drill-down-cursor"
                                           onclick="documentPageView_modal('CINV',<?php echo $val["invoiceAutoID"] ?>)"><?php echo $val['bookingInvCode'] ?></a>
                                    </td>
                                    <td><?php echo $val['bookingDate'] ?></td>
                                    <td><?php echo $val['invoiceDueDate'] ?></td>
                                    <td><?php echo $val['customer'] ?></td>
                                    <td><?php echo $val[$currency . 'currency'] ?></td>
                                    <td class="text-right"><?php echo number_format($val[$currency], $val[$currency . 'DecimalPlaces']) ?></td>
                                    <td class="text-right">
                                         <?php if ($val['paid' . $currency] > 0) {?>
                                        <a href="#" class="drill-down-cursor"
                                           onclick="invoice_overdue_drilldown(<?php echo $val["invoiceAutoID"] ?>)"><?php echo number_format($val['paid' . $currency], $val[$currency . 'DecimalPlaces']) ?></a>
                                         <?php } else {
                                                        echo number_format($val['paid' . $currency], $val[$currency . 'DecimalPlaces']);
                                                    }?>
                                    </td>
                                    <td class="text-right"><?php echo number_format($val['balance' . $currency], $val[$currency . 'DecimalPlaces']) ?></td>
                                </tr>
                                <?php
                                $totalAmount += $val[$currency];
                                $totalSettledAmount += $val['paid' . $currency];
                                $totalBalanceAmount += $val['balance' . $currency];
                                $currenyDecimal = $val[$currency . 'DecimalPlaces'];
                            }
                            $a++;
                        } ?>
                        </tbody>
                        <?php }
                        if($currency == 'companyLocalAmount' || $currency == 'companyReportingAmount') {
                            echo '<tfoot>';
                            echo '<tr>';
                            echo '<td></td>';
                            echo '<td colspan="5">Total</td>';
                            echo '<td class="text-right reporttotal">' . number_format($totalAmount, $currenyDecimal) . '</td>';
                            echo '<td class="text-right reporttotal">' . number_format($totalSettledAmount, $currenyDecimal) . '</td>';
                            echo '<td class="text-right reporttotal">' . number_format($totalBalanceAmount, $currenyDecimal) . '</td>';
                            echo '</tr>';
                            echo '</tfoot>';
                        }?>
                    </table>
                </div>
            </div>
            <br>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row" style="margin: 5px">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_invoice_overdue').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>