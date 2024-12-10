<?php echo fetch_account_review(true,true,$approval);

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>
<?php if(($printtype!=2)){?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 ><?php echo $this->lang->line('sales_markating_view_invoice_sales_invoice');?></h4><!--Sales Invoice -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>

<div class="table-responsive">
    <table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_invoice_number');?></strong></td><!--Invoice Number-->
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['invoiceCode']; ?></td>
        </tr>
        <tr>
            <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['customer']['customerAddress1']; ?></td>
            <?php } ?>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['invoiceDate']; ?></td>

        </tr>

        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['referenceNo']; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> <?php echo $this->lang->line('sales_markating_view_invoice_invoice_date');?></strong></td><!--Invoice Date-->
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['customerInvoiceDate']; ?></td>
        </tr>

        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> WareHouse </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['warehouse']['wareHouseCode'] . ' | ' . $extra['warehouse']['wareHouseDescription']; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
            <td style="font-size: 12px;  height: 8px; padding: 1px;vertical-align: top"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px">
                <table>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><label style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif;"> <?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['invoiceNarration']);?></label></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['invoiceNarration']; ?>
            </td>
        </tr>
        <tr>
            <?php if (!empty($extra['master']['SalesPersonCode'])) { ?>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> <?php echo $this->lang->line('sales_markating_view_invoice_sales_person');?></strong></td><!--Sales Person-->
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['SalesPersonName']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <?php if (isset($extra['driver']['driverName'])) { ?>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> Driver Name </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['driver']['driverName']; ?></td>
            <?php } ?>
            <?php if (isset($extra['vehicle']['VehicleNo'])) { ?>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>Vehicle No</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['vehicle']['VehicleNo']; ?></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
</div>
<hr><br>
<?php
$is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if((!empty($extra['item_detail']))) {
    ?>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
            <thead>
            <tr>
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">#</th>
                <th  style="font-size: 12px;font-weight:normal;min-width: 20%; border-bottom: 1px solid black">
                    <?php echo $this->lang->line('sales_markating_view_invoice_item_code');?>
                </th><!--Item Code-->
                <th  style="font-size: 12px;font-weight:normal;min-width: 40%; border-bottom: 1px solid black">
                    <?php echo $this->lang->line('sales_markating_view_invoice_item_description');?>
                </th><!--Item Description-->
                <th  style="font-size: 12px;font-weight:normal; max-width: 5%; border-bottom: 1px solid black">
                    No Item
                </th>
                <th  style="font-size: 12px;font-weight:normal;min-width: 5%; border-bottom: 1px solid black">
                    <?php echo $this->lang->line('common_qty');?>
                </th><!--Qty-->
                <th  style="font-size: 12px;font-weight:normal;min-width: 5%; border-bottom: 1px solid black">
                    Price
                </th><!--Unit-->
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">
                    <?php echo $this->lang->line('sales_markating_view_invoice_discount');?>
                </th><!--Discount-->
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">
                    Net Price
                </th>
                <th  style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">
                    Net Amount
                </th><!--Net-->
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
                    $txAmnt=0;
                    $Units =0;
                    $deduction =0;
                    $requestedQty = 0;
                    $Unitbuyback = 0;
                    $discount = 0;
                    $netunitprice = 0;
                    $netprice = 0;
                    $unitAmountNew = 0;
                    $discountNew = 0;
                    foreach ($value as $val) {
                        if($val['taxAmount']>0){
                            $txAmnt= $val['totalAfterTax']/$val['requestedQty'];
                        }
                        $num ++;
                        $itemCodePDF = $val['seconeryItemCode'];
                        $itemDescriptionPDF = $val['itemDescription'];
                        $gran_total += $val['transactionAmount'];
                        $item_total += $val['transactionAmount'];
                        $p_total    += $val['transactionAmount'];
                        $noofitems  += $val['noOfItems'];
                        $grossqty  += $val['grossQty'];
                        $Units  += $val['noOfUnits'];
                        $deduction  += $val['deduction'];
                        $requestedQty  += $val['requestedQty'];
                        $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                        $Unitbuyback += ($val['unittransactionAmount']);
                        $discount += ($val['discountAmount']);
                        $netunitprice +=($val['unittransactionAmount']-$val['discountAmount'])+($txAmnt);
                        $netprice +=($val['transactionAmount']);
                        $unitAmountNew += $val['requestedQty'] * ROUND(($val['unittransactionAmount']+ $val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']);
                        $discountNew += $val['discountAmount'] * $val['requestedQty'];
                    }
                    $netPriceNew = ($unitAmountNew / $requestedQty) - ($discountNew / $requestedQty);
                    ?>
                    <tr>
                        <?php
                        echo '<td style="font-size: 14px;">' . $pdfNo . '</td>';
                        echo '<td style="font-size: 14px;">' . $itemCodePDF . '</td>';
                        echo '<td style="font-size: 14px;">' . $itemDescriptionPDF . '</td>';
                        ?>
                        <td style="font-size: 14px; width: 10%; text-align: right;"><b><?php echo $noofitems;?></b></td>
                        <td style="font-size: 14px; text-align: right;"><b><?php echo format_number($requestedQty, 2);?></b></td>
                        <td style="font-size: 14px; text-align: right;"><b><?php echo number_format($unitAmountNew / $requestedQty ,$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                        <td style="font-size: 14px; text-align: right;"><b><?php echo number_format(($discountNew / $requestedQty),$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>
                        <td style="font-size: 14px; text-align: right; width: 10%;"><b><?php echo number_format($netPriceNew,$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>
                        <td style="font-size: 14px; text-align: right;"><b><?php echo number_format(($netPriceNew * $requestedQty),$extra['master']['transactionCurrencyDecimalPlaces']) ;;?></b></td>
                    </tr>

                    <?php
                    $pdfNo +=1;
                }
            }?>
            </tbody>
        </table>
        <hr style="color: black">
    </div>
<?php  } ?>
<?php $transaction_total = 0;
$Local_total = 0;
$party_total = 0;
if(!empty($extra['gl_detail'])){  ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
            <thead>
            <tr>
                <th  style="font-size: 12px;font-weight:normal; width: 5%">#</th>
                <th  style="font-size: 12px;font-weight:normal; min-width: 45%;text-align: left;"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th  style="font-size: 12px;font-weight:normal; width: 15%"><?php echo $this->lang->line('common_segment');?></th><!--Segment-->
                <th  style="font-size: 12px;font-weight:normal; width: 15%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Amount-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            foreach ($extra['gl_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td><?php echo $val['description']; ?></td>
                    <td><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="3" style="font-size: 12px;font-weight:normal;"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <td class="text-right sub_total" style="font-size: 12px;font-weight:normal;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
                    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td   colspan="5" style="font-weight:normal; ">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th   style="font-size: 12px;font-weight:normal; ">#</th>
                            <th   style="font-size: 12px;font-weight:normal; "><?php echo $this->lang->line('common_type');?></th><!--Type-->
                            <th   style="font-size: 12px;font-weight:normal; "> <?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail-->
                            <th   style="font-size: 12px;font-weight:normal; "><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                            <th   style="font-size: 12px;font-weight:normal; "><?php echo $this->lang->line('common_transaction');?><!--Transaction -->(<?php echo $extra['master']['transactionCurrency']; ?>) </th>
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
                            echo '</tr>';
                            $x++;
                            $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                            $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total" style="font-size: 12px;font-weight:normal;"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 12px;font-weight:normal;"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:30%;">
                <?php  if (!empty($extra['taxledger']) && $extra['master']['showTaxSummaryYN']==1) { ?>
                    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';" class="table">
                        <thead>
                        <tr>
                            <td  colspan="3" style="font-weight:normal; border-bottom: 1px solid black; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1;
                        $tax_totl=0;
                        foreach ($extra['taxledger'] as $value) {
                            echo '<tr>';
                            echo '<td style="font-size: 12px;">'.$x.'</td>';
                            echo '<td style="font-size: 12px;">'.$value['taxShortCode'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.format_number($value['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $tax_totl += $value['amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-right sub_total"  style="font-size: 11px; font-weight:normal;"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 11px; font-weight:normal;"><?php echo format_number($tax_totl,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
            <td style="width:20%;"> </td>
            <td style="width:20%;"> </td>
            <td style="width:30%;vertical-align: top;">
                <?php  if (!empty($extra['discount'])) { ?>
                    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';" class="table">
                        <thead>
                        <tr>
                            <td  colspan="4" style="font-weight:normal; border-bottom: 1px solid black; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
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
                            echo '<td style="font-size: 12px;">'.$x.'.</td>';
                            echo '<td style="font-size: 12px;">'.$value['discountDescription'].'</td>';
                            echo '<td style="font-size: 12px;">'.$value['discountPercentage'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.format_number($disc_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $disc_nettot += $disc_total;
                        }
                        $gran_total=$gran_total-$disc_nettot;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total" style="font-size: 11px; font-weight:normal;">Total</td>
                            <td class="text-right sub_total" style="font-size: 11px; font-weight:normal;"><?php echo format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
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
                <td style="width:70%;">
                    &nbsp;
                </td>
                <td style="width:30%;">
                    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';" class="table">
                        <thead>
                        <tr>
                            <td  colspan="3" style="font-weight:normal; border-bottom: 1px solid black; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
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
                            echo '<td style="font-size: 12px">'.$x.'.</td>';
                            echo '<td style="font-size: 12px">'.$value['extraChargeDescription'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px">'.format_number($extra_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $extra_nettot += $extra_total;
                        }
                        $gran_total=$gran_total+$extra_nettot;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-right sub_total" style="font-size: 11px; font-weight:normal">Total</td>
                            <td class="text-right sub_total" style="font-size: 11px; font-weight:normal"><?php echo format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>

<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
    <h5 class="text-right" style="font-size: 14px; font-weight: bold;"><?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

<hr>
<br><br>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
        <tr>
            <td style="text-align: center">
                ____________________________
            </td>
            <td style="text-align: center">
                ____________________________
            </td>
            <td style="text-align: center">
                ____________________________
            </td>
            <td style="text-align: center">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center">
                Approved By
            </td>
            <td style="font-size: 12px; text-align: center">
                Customer
            Signature
            </td>
            <td style="font-size: 12px; text-align: center">
                Driver Signature
            </td>
        </tr>

        </tbody>
    </table>
</div>
<?php }?>
<!-- Delivery note Print View -->
<?php if ($extra['master']['invoiceNote']) { ?>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif';"><br>
    <h6><?php echo $this->lang->line('sales_markating_view_invoice_notes');?></h6><!--Notes-->
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['invoiceNote']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>

<?php
$is_item_active = 0;

if($printtype !=0) { ?>
    <?php if ($extra['item_detail'] && (($printtype == 1)||$printtype == 2) ) {
        $num = 1;$item_total = 0; $is_item_active = 1;
    } ?>

        <?php if ((($printtype == 1)||($printtype == 2)) && $html !=1 && $is_item_active==1) { ?>

        <?php if($printtype == 1){?>
        <pagebreak />
        <?php }?>

        <div class="table-responsive">
            <table style="font-family:'Arial, Sans-Serif, Times, Serif'; width: 100%">
                <tbody>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td style="font-family:'Arial, Sans-Serif, Times, Serif'; text-align: center;">
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
            <table style="font-family:'Arial, Sans-Serif, Times, Serif'; width: 100%">
                <tbody>
                <tr>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_customer_name');?> </strong></td><!--Customer Name-->
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo (empty($extra['master']['customerSystemCode'])) ? $extra['master']['customerName'] : $extra['master']['customerName'].' ( '.$extra['master']['customerSystemCode'].' )'; ?></td>

                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_number');?></strong></td><!--DN Number-->
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['invoiceCode']; ?></td>
                </tr>
                <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['customerAddress']; ?></td>

                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_date');?></strong></td><!--DN Date-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['invoiceDate']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_telephone');?>/<?php echo $this->lang->line('common_fax');?></strong></td><!--Telephone / Fax -->
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax']; ?></td>

                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['referenceNo']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['invoiceNarration']; ?></td>
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_date');?></strong></td><!--Delivery Date-->
                    <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                    <td colspan="3" style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['invoiceDueDate']; ?></td>
                </tr>
                <tr>
                    <?php if (isset($extra['driver']['driverName'])) { ?>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong> Driver Name </strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['driver']['driverName']; ?></td>
                    <?php } ?>
                    <?php if (isset($extra['vehicle']['VehicleNo'])) { ?>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>Vehicle No</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['vehicle']['VehicleNo']; ?></td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </div><hr><br>
        <?php $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0; if(!empty($extra['item_detail'])){ ?>
            <div class="table-responsive">
                <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <thead>
                    <tr>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black">#</th>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 15%; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                        <th  style="font-weight: normal; font-size: 12px; min-width: 65%; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                        <th  style="font-weight: normal; font-size: 12px; min-width: 10%; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_view_invoice_uom');?></th><!--UOM-->
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black"> No Item</th>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black"> Gross Qty</th>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black"> Units</th>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black"> Deduction</th>
                        <th  style="font-weight: normal; font-size: 12px; min-width: 5%; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_view_invoice_qty');?></th><!--Qty-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if ($extra['buyback_detail_delivery']) {
                        $num =1;$item_total = 0;$is_item_active  = 1;
                        $extra['buyback_detail_delivery'] = array_group_by($extra['buyback_detail_delivery'], 'itemAutoID');

                        foreach ($extra['buyback_detail_delivery'] as $value) {

                            $noofitems = 0;
                            $grossqty = 0;
                            $txAmnt=0;
                            $Units =0;
                            $deduction =0;
                            $requestedQty = 0;
                            $Unitbuyback = 0;
                            $discount = 0;
                            $netunitprice = 0;
                            $netprice = 0;
                            $qty = 0;
                            foreach ($value as $val) {
                                if($val['taxAmount']>0){
                                    $txAmnt= $val['totalAfterTax']/$val['requestedQty'];
                                }

                                ?>
                                <tr>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                                    <td style="font-size: 11px;"><?php echo $val['itemSystemCode']; ?></td>
                                    <td style="font-size: 11px;"><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription'].' - '.$val['remarks']; ?></td>
                                    <td style="font-size: 11px;"><?php echo $val['unitOfMeasure']; ?></td>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $val['noOfItems']; ?></td>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $val['grossQty']; ?></td>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $val['noOfUnits']; ?></td>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $val['deduction']; ?></td>
                                    <td style="font-size: 11px; text-align:right;"><?php echo $val['requestedQty']; ?></td>
                                </tr>
                                <?php
                                $num ++;
                                $noofitems  += $val['noOfItems'];
                                $grossqty  += $val['grossQty'];
                                $Units  += $val['noOfUnits'];
                                $deduction  += $val['deduction'];
                                $qty  += $val['requestedQty'];
                            }
                            ?>
                            <tr>
                                <td colspan="4" class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b>Total</b></td>
                                <td class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b><?php echo $noofitems;?></b></td>
                                <td class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b><?php echo $grossqty;?></b></td>
                                <td class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b><?php echo $Units;?></b></td>
                                <td class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b><?php echo $deduction;?></b></td>
                                <td class="" style="font-size: 11px; font-weight: normal; text-align: right; border-top: 1px solid #A5A3A3"><b><?php echo $qty;?></b></td>
                            </tr>
                            <?php
                        }
                    }?>
                    </tbody>

                </table>
            </div>

            <div class="table-responsive"><br>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif'; width: 100%">
                    <tbody>
                    <?php if($extra['master']['confirmedYNn'] == 1){ ?>
                        <tr>
                            <td style="font-size: 11px;"><b>Confirmed by</b></td>
                            <td><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['master']['confirmedYNn']; ?></td>
                        </tr>
                    <?php } ?>
                    <?php if($extra['master']['approvedYN']){ ?>
                        <tr>
                            <td style="font-size: 11px;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?></b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 11px;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                            <td><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['master']['approvedDate']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <hr style="color: gray; height: 1.5px;">
            <br><br>
            <div class="table-responsive">
                <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
                    <tbody>
                    <tr>
                        <td style="text-align: center">
                            ____________________________
                        </td>
                        <td style="text-align: center">
                            ____________________________
                        </td>
                        <td style="text-align: center">
                            ____________________________
                        </td>
                        <td style="text-align: center">
                            ____________________________
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px; text-align: center">
                            Prepared By
                        </td>
                        <td style="font-size: 12px; text-align: center">
                            Approved By
                        </td>
                        <td style="font-size: 12px; text-align: center">
                            Customer Signature
                        </td>
                        <td style="font-size: 12px; text-align: center">
                            Driver Signature
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php
        }

    } ?>
    <?php }?>


    <script>
        $('.review').removeClass('hide');
        a_link=  "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/<?php echo $extra['master']['invoiceAutoID'] ?>";
        de_link="<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + <?php echo $extra['master']['invoiceAutoID'] ?> + '/HCINV';
        $("#a_link").attr("href",a_link);
        $("#de_link").attr("href",de_link);
    </script>