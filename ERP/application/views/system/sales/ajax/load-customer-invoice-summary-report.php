<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <!--<div class="col-md-12">
            <?php
/*            if ($type == 'html') {
                echo export_buttons('salesOrderReport', 'Revenue Details', True, True);
            } */?>
        </div>-->
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Revenue Details</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name--></th>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th> <?php echo $this->lang->line('common_segment'); ?><!--Segment --></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <th>Invoice Amount</th>
                       <th>Return Amount</th>
                       <th>Receipt/Credit Note Amount</th>
                        <th>Balance</th>
                        <!--<th>Balance Amount</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        if($currency==1){
                            $details = array_group_by($details, 'transactionCurrency');
                        }elseif($currency==2){
                            $details = array_group_by($details, 'companyLocalCurrency');
                        }else{
                            $details = array_group_by($details, 'companyReportingCurrency');
                        }
                        foreach ($details as $value) {
                            $salesOrder = 0;
                            $invoice = 0;
                            $receipt = 0;
                            $returnamt = 0;
                            $creditamount = 0;
                            $rvmatchamount = 0;
                            $receiptamount = 0;
                            $credittot = 0;
                            $creditnettot = 0;
                            $bal = 0;
                            $balttot = 0;
                            $decimalPlace = 2;
                            foreach ($value as $val) {
                                if($currency==1){
                                    $decimalPlace = $val["transactionCurrencyDecimalPlaces"];
                                    $curr=$val["transactionCurrency"];
                                    $exchangerate=$val["transactionExchangeRate"];
                                }elseif($currency==2){
                                    $decimalPlace = $val["companyLocalCurrencyDecimalPlaces"];
                                    $curr=$val["companyLocalCurrency"];
                                    $exchangerate=$val["companyLocalExchangeRate"];
                                }else{
                                    $decimalPlace = $val["companyReportingCurrencyDecimalPlaces"];
                                    $curr=$val["companyReportingCurrency"];
                                    $exchangerate=$val["companyReportingExchangeRate"];
                                }

                                ?>
                                <tr>
                                    <td width="200px"><?php echo $val["customermastername"] ?></td>
                                    <?php
                                    if ($type == 'html') {
                                        ?>
                                        <td><a href="#" class="drill-down-cursor"
                                               onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["invoiceAutoID"] ?>)"><?php echo $val["invoiceCode"] ?></a>
                                        </td>
                                        <?php
                                    } else{
                                        ?>
                                        <td><?php echo $val["invoiceCode"] ?></td>
                                        <?php
                                    }
                                    ?>

                                    <td><?php echo $val["segid"] ?></td>
                                    <td><?php echo $val["invoiceDate"] ?></td>
                                    <td><?php echo $curr ?></td>
                                    <!--<td style="text-align: right"><?php /*echo number_format($val["total_value"], $val["transactionCurrencyDecimalPlaces"]) */?></td>-->
                                    <?php
                                        //$balance=$val["total_value"]-$val["returnAmount"];
                                        $balance=($val["total_value"]/$exchangerate);
                                        $return=($val["returnAmount"]/$exchangerate);
                                    if($currency==1){
                                        $creditamount = $val["credittransactionAmount"];
                                        $receiptamount=$val["receipttransactionAmount"];
                                        $rvmatchamount=$val["rvmatchtransactionAmount"];
                                    }elseif($currency==2){
                                        $creditamount = $val["creditcompanyLocalAmount"];
                                        $receiptamount=$val["receiptcompanyLocalAmount"];
                                        $rvmatchamount=$val["rvmatchcompanyLocalAmount"];
                                    }else{
                                        $creditamount = $val["creditcompanyReportingAmount"];
                                        $receiptamount=$val["receiptcompanyReportingAmount"];
                                        $rvmatchamount=$val["rvmatchcompanyReportingAmount"];
                                    }
                                    $credittot = $creditamount + $receiptamount + $rvmatchamount;
                                    ?>
                                    <td style="text-align: right"><?php echo number_format($balance, $decimalPlace) ?></td>
                                    <?php
                                    if($return>0){
                                        ?>
                                        <td style="text-align: right"><a href="#" class="drill-down-cursor"
                                                                         onclick="openreturnDD(<?php echo $val["invoiceAutoID"] ?>)"><?php echo number_format($return, $decimalPlace) ?></a>
                                        </td>
                                        <?php
                                    }else{
                                        ?>
                                        <td style="text-align: right"><?php echo number_format($return, $decimalPlace) ?></td>
                                        <?php
                                    }
                                    ?>


                                    <?php
                                    if($credittot>0){
                                        ?>
                                        <td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="openrecreditDD(<?php echo $val["invoiceAutoID"] ?>)"><?php echo number_format($credittot, $decimalPlace) ?></a></td>
                                        <?php
                                    }else{
                                        ?>
                                        <td style="text-align: right"><?php echo number_format($credittot, $decimalPlace) ?></td>
                                        <?php
                                    }
                                    if($val["documentID"]=='SLR')
                                    {
                                        $bal=$return ;
                                    }else
                                    {
                                        $bal=$balance-($return+$credittot) ;
                                    }
                                    ?>
                                    <td style="text-align: right"><?php  echo number_format($bal, $decimalPlace) ?></td>
                                </tr>
                                <?php
                                /*$salesOrder += $val["total_value"];
                                $invoice += $val["returnAmount"];*/
                                $receipt += $balance;
                                $returnamt += $return;
                                $creditnettot += $credittot;
                                $balttot += $bal;
                            }
                            ?>
                            <tr>
                                <td colspan="5"><b>Total</b></td>
                                <!--<td class="text-right reporttotal"><?php /*echo number_format($salesOrder,$decimalPlace) */?></td>
                                <td class="text-right reporttotal"><?php /*echo number_format($invoice,$decimalPlace) */?></td>-->
                                <td class="text-right reporttotal"><?php echo number_format($receipt,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($returnamt,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($creditnettot,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($balttot,$decimalPlace) ?></td>
                            </tr>
                            <?php
                        }
                    } ?>

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