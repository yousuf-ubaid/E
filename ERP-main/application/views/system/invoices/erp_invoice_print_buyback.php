<?php echo fetch_account_review(true,true,$approval);

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td>
                <table>
                    <tr>
                        <td style="text-align: center;">
                            <h4 ><?php echo $this->lang->line('sales_markating_view_invoice_sales_invoice');?></h4><!--Sales Invoice -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td ><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td ><strong>:</strong></td>
            <td> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

            <td><strong><?php echo $this->lang->line('common_invoice_number');?></strong></td><!--Invoice Number-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['invoiceCode']; ?></td>
        </tr>
        <tr>
            <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerAddress1']; ?></td>
            <?php } ?>
            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['invoiceDate']; ?></td>

        </tr>

        <tr>
            <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
            <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNo']; ?></td>
        </tr>

        <tr>
            <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_invoice_date');?></strong></td><!--Invoice Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['customerInvoiceDate']; ?></td>
            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_invoice_due_date');?></strong></td><!--Invoice Due Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['invoiceDueDate']; ?></td>
        </tr>
        <tr>
            <td><strong> Total Outstanding </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo round($extra['outstandingamt']['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
            <td style="vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['invoiceNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['invoiceNarration']; ?>
            </td>
        </tr>
        <tr>
            <?php if (isset($extra['driver']['driverName'])) { ?>
                <td><strong> Driver Name </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['driver']['driverName'] ; ?></td>
            <?php } ?>
            <?php if (isset($extra['vehicle']['VehicleNo'])) { ?>
                <td><strong> Vehicle No </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['vehicle']['VehicleNo']; ?></td>
            <?php } ?>
        </tr>
        <tr>

            <td><strong> WareHouse </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['warehouse']['wareHouseCode'] . ' | ' . $extra['warehouse']['wareHouseDescription']; ?></td>

            <?php if (!empty($extra['master']['SalesPersonCode'])) { ?>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_sales_person');?></strong></td><!--Sales Person-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['SalesPersonName']; ?></td>
            <?php } ?>
        </tr>

        </tbody>
    </table>
</div><br>
<?php $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if(!empty($extra['item_detail'])){


    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="9"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                <th class='theadtr' colspan="5"><?php echo $this->lang->line('common_price');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Price-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="max-width: 3%"> No of Birds</th>
                <th class='theadtr' > Gross Weight</th>
                <th class='theadtr'> No of Buckets</th>
                <th class='theadtr'> Bucket Size</th>
                <th class='theadtr'> Bucket Weight</th>
                <th class='theadtr' style="min-width: 5%">Net Weight</th><!--Qty-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_discount');?></th><!--Discount-->
                <th class='theadtr' style="<?php if (!$html){ echo "max-width: "; } ?>min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_net_unit_price');?></th><!--Net Unit Cost-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_net');?></th><!--Net-->
                <th class='theadtr' style="min-width: 10%"> </th><!--Net-->

            </tr>
            </thead>
            <tbody>
            <?php

            if ($extra['item_detail']) {
                $num =1;$item_total = 0;$is_item_active  = 1;
                $extra['item_detail'] = array_group_by($extra['item_detail'], 'itemAutoID');
                $pdfNo = 1;
                foreach ($extra['item_detail'] as $value) {
                    $noofitems = 0;
                    $grossqty = 0;
                    $Units =0;
                    $deduction =0;
                    $requestedQty = 0;
                    $Unitbuyback = 0;
                    $discount = 0;
                    $netunitprice = 0;
                    $unitprice = 0;
                    $netprice = 0;
                    $bucketweight = 0;
                    $unitAmountNew = 0;
                    $discountNew = 0;
                    foreach ($value as $val) { ?>
                            <tr>
                                <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                                <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                                <td><?php echo ($val['contractCode'] ? $val['contractCode'] . ' - ' : '') . $val['itemDescription']; ?></td>
                                <td style="text-align:right;"><?php echo $val['noOfItems']; ?></td>
                                <td style="text-align:right;"><?php echo format_number(($val['grossQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo $val['noOfUnits']; ?></td>
                                <td style="text-align:right;"><?php echo $val['deduction']; ?></td>
                                <td style="text-align:right;"><?php echo format_number(($val['noOfUnits'] * $val['deduction']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo format_number(($val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo format_number(($val['unittransactionAmount'] + $val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo format_number(($val['unittransactionAmount'] - $val['discountAmount']) + ($val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                                <?php if ($extra['isDayClosed'] == 0) {
                                    if (($html == 'true') && ($val['printTagYN'] == 1)) { ?>
                                        <td style="text-align:right;">

                                            <a target="_blank"
                                               href="<?php echo site_url('InvoicesPercentage/print_tageline_buyback/') . '/' . $val['invoiceDetailsAutoID'] ?>"><span
                                                        title="Print" rel="tooltip"
                                                        class="glyphicon glyphicon-print"></span></a>
                                            <?php /*echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); */ ?>
                                        </td>
                                    <?php }
                                }?>
                            </tr>

                        <?php
                        $num ++;
                        $itemCodePDF = $val['itemSystemCode'];
                        $itemDescriptionPDF = ($val['contractCode'] ? $val['contractCode'] . ' - ' : '') . $val['itemDescription'];
                        $gran_total += $val['transactionAmount'];
                        $item_total += $val['transactionAmount'];
                        $p_total    += $val['transactionAmount'];
                        $grossqty  += $val['grossQty'];
                        $Units  += $val['noOfUnits'];
                        $deduction  += $val['deduction'];
                        $bucketweight  += $val['noOfUnits'] * $val['deduction'];
                        $requestedQty  += $val['requestedQty'];
                        //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                        $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                        $Unitbuyback += ($val['unittransactionAmount']);
                        $discount += ($val['discountAmount']);
                        $netunitprice +=($val['unittransactionAmount']-$val['discountAmount'])+($val['taxAmount']);
                        $unitprice +=$val['unittransactionAmount']+($val['taxAmount']);
                        $netprice +=($val['transactionAmount']);

                        $noofitems  += $val['noOfItems'];
                        $unitAmountNew += $val['requestedQty'] * ROUND(($val['unittransactionAmount']+ $val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']);
                        $discountNew += $val['discountAmount'] * $val['requestedQty'];
                    }
                    $netPriceNew = ($unitAmountNew / $requestedQty) - ($discountNew / $requestedQty);
                    ?>
                    <tr>
                        <td colspan="1"> </td>
                        <td colspan="2" class="sub_total" style="text-align: right;"><b>Total</b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $noofitems;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format(($grossqty),  $extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $Units;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $deduction;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format($bucketweight,2);?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format(($requestedQty), $extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format(($unitAmountNew / $requestedQty),$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format(($discountNew / $requestedQty),$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right; width: 10%"><b><?php echo number_format($netPriceNew,$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format(($netPriceNew * $requestedQty),$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>

                    </tr>

                    <?php
                }
                $pdfNo +=1;
            }?>
            </tbody>
        </table>
    </div>
<?php  } ?>
<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['gl_detail'])){  ?>
    <br>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="min-width: 45%;text-align: left;"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment');?></th><!--Segment-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Amount-->
                <!-- <th class='theadtr' style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 13%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            foreach ($extra['gl_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td><?php echo $val['description']; ?></td>
                    <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <!-- <td style="text-align:right;"><?php //echo format_number($val['companyLocalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php //echo format_number($val['customerAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                //$Local_total        += $val['companyLocalAmount'];
                //$party_total        += $val['customerAmount'];
                $p_total            += $val['transactionAmount'];

                //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                // $tax_Local_total += ($val['companyLocalAmount']-$val['totalAfterTax']);
                // $tax_customer_total += ($val['customerAmount']-$val['totalAfterTax']);
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="3"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <td class="text-right sub_total"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right sub_total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total"><?php //echo format_number($party_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php  if (!empty($extra['tax'])) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td  class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th  class='theadtr'>#</th>
                            <th  class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                            <th  class='theadtr'> <?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail-->
                            <th  class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                            <th  class='theadtr'><?php echo $this->lang->line('common_transaction');?><!--Transaction -->(<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                            <!-- <th class='theadtr'>Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th class='theadtr'>Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                        $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                        $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                        foreach ($extra['tax'] as $value) {
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['taxShortCode'].'</td>';
                            echo '<td>'.$value['taxDescription'].'</td>';
                            echo '<td class="text-right">'.$value['taxPercentage'].' % </td>';
                            echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_customer_total),$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                            $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                            //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                            //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                            <td class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <!-- <td class="text-right sub_total"><?php //echo format_number($loc_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right sub_total"><?php //echo format_number($cu_total_amount,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>

<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:50%;">
                <?php  if (!empty($extra['taxledger']) && $extra['master']['showTaxSummaryYN']==1) { ?>
                    <table style="width: 100%;" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td  class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'> Short Code</th>
                            <th class='theadtr'>Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1;
                        $tax_totl=0;
                        foreach ($extra['taxledger'] as $value) {
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['taxShortCode'].'</td>';
                            echo '<td class="text-right">'.format_number($value['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $tax_totl += $value['amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                            <td class="text-right sub_total"><?php echo format_number($tax_totl,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
            <td style="width:50%;vertical-align: top;">
                <?php  if (!empty($extra['discount'])) { ?>
                    <table style="width: 100%; " class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'>Description</th>
                            <th class='theadtr'>Percentage</th>
                            <th class='theadtr'>Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1;
                        $disc_nettot=0;
                        foreach ($extra['discount'] as $value) {
                            $disc_total=0;
                            $disc_total= ($gran_total*$value['discountPercentage'])/100;
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['discountDescription'].'</td>';
                            echo '<td>'.$value['discountPercentage'].'</td>';
                            echo '<td class="text-right">'.format_number($disc_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $disc_nettot += $disc_total;
                        }
                        $gran_total=$gran_total-$disc_nettot;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total">Total</td>
                            <td class="text-right sub_total"><?php echo format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>


<?php  if (!empty($extra['extracharge'])) { ?>

    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:50%;">
                    &nbsp;
                </td>
                <td style="width:50%;padding: 0;">
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'>Description</th>
                            <th class='theadtr'>Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1;
                        $extra_nettot=0;
                        foreach ($extra['extracharge'] as $value) {
                            $extra_total=0;
                            $extra_total= $value['transactionAmount'];
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['extraChargeDescription'].'</td>';
                            echo '<td class="text-right">'.format_number($extra_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $extra_nettot += $extra_total;
                        }
                        $gran_total=$gran_total+$extra_nettot;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-right sub_total">Total</td>
                            <td class="text-right sub_total"><?php echo format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>

<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<?php if ($extra['master']['bankGLAutoID']) { ?>
    <div class="table-responsive">
        <h6><?php echo $this->lang->line('sales_markating_view_invoice_remittance_details');?></h6><!--Remittance Details-->
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 18%"><strong><?php echo $this->lang->line('common_bank');?></strong></td><!--Bank-->
                <td style="width: 2%"><strong>:</strong></td>
                <td style="width: 80%"><?php echo $extra['master']['invoicebank']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_branch');?></strong></td><!--Branch-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['invoicebankBranch']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_swift_code');?></strong></td><!--Swift Code-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['invoicebankSwiftCode']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_account');?></strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['invoicebankAccount']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><b>Confirmed by</b></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['confirmedYNn']; ?></td>
        </tr>
        <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($extra['master']['invoiceNote']) { ?>
<div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('sales_markating_view_invoice_notes');?></h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['invoiceNote']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>

        <pagebreak />
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <!-- <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php /*echo mPDFImage.$this->common_data['company_data']['company_logo']; */?>">
                        </td>
                    </tr>
                </table>
            </td>-->
                    <td>
                        <table>
                            <tr>
                                <td style="text-align: center;">
                                    <!--<h3><strong><?php /*echo $this->common_data['company_data']['company_name']; */?>.</strong></h3>
                            <p><?php /*echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; */?></p>
                            <br>-->
                                    <h4><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note');?></h4><!--Delivery note-->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style=""><strong><?php echo $this->lang->line('common_customer_name');?> </strong></td><!--Customer Name-->
                    <td style=""><strong>:</strong></td>
                    <td style=""> <?php echo (empty($extra['master']['customerSystemCode'])) ? $extra['master']['customerName'] : $extra['master']['customerName'].' ( '.$extra['master']['customerSystemCode'].' )'; ?></td>


                    <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_number');?></strong></td><!--DN Number-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoiceCode']; ?></td>
                </tr>
                <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['master']['customerAddress']; ?></td>

                        <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_date');?></strong></td><!--DN Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['invoiceDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_telephone');?>/<?php echo $this->lang->line('common_fax');?></strong></td><!--Telephone / Fax -->
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax']; ?></td>

                        <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNo']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
                    <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php } else{ ?>
                        <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_date');?></strong></td><!--DN Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['invoiceDate']; ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="vertical-align: top"><strong><?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                    <td style="vertical-align: top"><strong>:</strong></td>
                    <td>
                        <table>
                            <tr>
                                <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['invoiceNarration']);?></td>
                            </tr>
                        </table>
                        <?php //echo $extra['master']['invoiceNarration']; ?>
                    </td>
                    <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php }else{ ?>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNo']; ?></td>
                    <?php } ?>

                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_date');?></strong></td><!--Delivery Date-->
                    <td><strong>:</strong></td>
                    <td colspan="3"> <?php echo $extra['master']['invoiceDueDate']; ?></td>

                </tr>

                <tr>
                    <?php if (isset($extra['driver']['driverName'])) { ?>
                        <td><strong> Driver Name </strong></td>
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['driver']['driverName'] ; ?></td>
                    <?php } ?>
                    <?php if (isset($extra['vehicle']['VehicleNo'])) { ?>
                        <td><strong> Vehicle No </strong></td>
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['vehicle']['VehicleNo']; ?></td>
                    <?php } ?>
                </tr>

                </tbody>
            </table>
        </div><br>
        <?php $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0; if(!empty($extra['item_detail'])){ ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class='theadtr' colspan="9"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                    </tr>
                    <tr>
                        <th class='theadtr' style="min-width: 5%;">#</th>
                        <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                        <th class='theadtr' style="min-width: 65%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom');?></th><!--UOM-->
                        <th class='theadtr' style="min-width: 5%"> No of Birds</th>
                        <th class='theadtr' style="min-width: 5%"> Gross Weight</th>
                        <th class='theadtr' style="min-width: 5%"> No of Buckets</th>
                        <th class='theadtr' style="min-width: 5%"> Bucket Size</th>
                        <th class='theadtr' style="min-width: 5%"> Net Weight</th><!--Qty-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if ($extra['buyback_detail_delivery']) {
                        $num =1;$item_total = 0;$is_item_active  = 1;
                        $extra['buyback_detail_delivery'] = array_group_by($extra['buyback_detail_delivery'], 'itemAutoID');

                        $tot_NoItem = 0;
                        $tot_Units =  0;
                        foreach ($extra['buyback_detail_delivery'] as $value) {

                            $noofitems = 0;
                            $grossqty = 0;
                            $Units =0;
                            $deduction =0;
                            $requestedQty = 0;
                            $Unitbuyback = 0;
                            $discount = 0;
                            $netunitprice = 0;
                            $netprice = 0;
                            $qty = 0;
                            foreach ($value as $val) { ?>
                                <tr>
                                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                                    <td><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription'].' - '.$val['remarks']; ?></td>
                                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                                    <td style="text-align:right;"><?php echo $val['noOfItems']; ?></td>
                                    <td style="text-align:right;"><?php echo number_format(($val['grossQty']),  $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                    <td style="text-align:right;"><?php echo $val['noOfUnits']; ?></td>
                                    <td style="text-align:right;"><?php echo $val['deduction']; ?></td>
                                    <td style="text-align:right;"><?php echo number_format(($val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                                </tr>
                                <?php
                                $num ++;
                                $noofitems  += $val['noOfItems'];
                                $grossqty  += $val['grossQty'];
                                $Units  += $val['noOfUnits'];
                                $deduction  += $val['deduction'];
                                $qty  += $val['requestedQty'];
                                $tot_NoItem += $val['noOfItems'];
                                $tot_Units += $val['noOfUnits'];
                                // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                                // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                            }
                            ?>
                            <tr>
                                <td colspan="2"> </td>
                                <td colspan="2" class="sub_total" style="text-align: right;"><b>Total</b></td>
                                <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $noofitems;?></b></td>
                                <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo format_number(($grossqty), $extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                                <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $Units;?></b></td>
                                <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $deduction;?></b></td>
                                <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo format_number(($qty), $extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                            </tr>

                            <?php
                        }
                    }?>
                    <tr>
                        <td colspan="2" > </td>
                        <td colspan="2" class="sub_total" style="text-align: right;"><b>Net Total</b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $tot_NoItem;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $tot_Units;?></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b></b></td>
                        <td class="sub_total reporttotal" style="text-align: right;"><b></td>
                    </tr>
                    </tbody>
                    <tfoot>

                    </tfoot>

                </table>
            </div>

            <div class="table-responsive"><br>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td><b>Confirmed by</b></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['confirmedYNn']; ?></td>
                    </tr>
                    <?php if($extra['master']['approvedYN']){ ?>
                        <tr>
                            <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?></b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['approvedDate']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php }  ?>
    <br>
    <br>
    <br>
    <?php if($extra['master']['approvedYN']){ ?>
        <?php
        if ($signature) { ?>

            <?php
            if ($signature['approvalSignatureLevel'] <= 2) {
                $width = "width: 50%";
            } else {
                $width = "width: 100%";
            }
            ?>
            <div class="table-responsive">
                <table style="<?php echo $width ?>">
                    <tbody>
                    <tr>
                        <?php
                        for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {
                            ?>
                            <td>
                                <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                            </td>

                            <?php
                        }
                        ?>
                    </tr>

                    </tbody>
                </table>
            </div>
        <?php } ?>
    <?php } ?>
    <script>
        $('.review').removeClass('hide');
        a_link=  "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/<?php echo $extra['master']['invoiceAutoID'] ?>";
        de_link="<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + <?php echo $extra['master']['invoiceAutoID'] ?> + '/HCINV';
        $("#a_link").attr("href",a_link);
        $("#de_link").attr("href",de_link);
    </script>