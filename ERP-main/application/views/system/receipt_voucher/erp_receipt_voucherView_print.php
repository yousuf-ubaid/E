<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$no=$this->lang->line('common_no_records_found');
echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']); ?>

<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4 style=""><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p style=""><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p style=""><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 style=" "><?php if($extra['master']['RVType'] == 'DirectItem' || $extra['master']['RVType'] == 'DirectIncome'){
                                    echo 'Direct ';
                                }else {
                                    echo 'Customer ';
                                }
                                echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher');?><!--Receipt Voucher--></h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%;"><strong><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--> </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:40%;"><?php echo (empty($extra['master']['customersys'])) ? $extra['master']['customerName'] : $extra['master']['customerName'] . ' ( ' . $extra['master']['customersys'] . ' )'; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%;"><strong>Receipt Number <!--Receipt Voucher Number--></strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:33%;"> <?php echo $extra['master']['RVcode'];  ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('accounts_receivable_common_customer_address');?> <!--Customer Address--> </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; "><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; "> <?php if (!empty($extra['master']['customeradd'])) echo $extra['master']['customeradd']; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%;"><strong>Receipt Date</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:33%;"> <?php echo $extra['master']['RVdate'];  ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['RVNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['RVNarration']; ?>
            </td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Reference Number</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['referanceNo']; ?></td>
        </tr>
        <tr>
            <?php if($extra['master']['modeOfPayment'] == 2 && $extra['master']['paymentType'] == 1) {?>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_receivable_common_cheque_number');?><!--Cheque Number--></strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"> <?php echo $extra['master']['RVchequeNo']; ?></td>
            <?php } ?>

            <?php if($extra['master']['modeOfPayment'] == 2 && $extra['master']['paymentType'] == 1) {?>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_receivable_common_cheque_date');?><!--Cheque Date--></strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"> <?php echo $extra['master']['RVchequeDate']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%;"><strong><?php echo $this->lang->line('common_bank');?> <!--Bank--></strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:33%;"> <?php echo $extra['master']['RVbank']; ?><?php if(!empty($extra['master']['RVbankBranch']) && $extra['master']['RVbankBranch']!='-'){ echo ' / ' . $extra['master']['RVbankBranch'];}  ?></td>
        </tr>
        </tbody>
    </table>
</div>
<hr><br>
<?php $grand_total = 0;$tax_transaction_total=0;
if (!empty($extra['item_detail'])) { ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;" colspan="5"><?php echo $this->lang->line('accounts_receivable_common_item_details');?><!--Item Details--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; " colspan="2"><?php echo $this->lang->line('common_price');?><!--Price--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%; ">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%; "><?php echo $this->lang->line('accounts_receivable_common_item_code');?><!--Item Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%; "><?php echo $this->lang->line('common_item_description');?><!--Item Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%; "><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%; "><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%; "><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%; "><?php echo $this->lang->line('common_total');?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['item_detail'])) {
                foreach ($extra['item_detail'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:left;"><?php echo $val['itemSystemCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['Itemdescriptionpartno'] ?></td>
                        <td style="font-size: 14px;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo $val['requestedQty']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px;" class="text-right sub_total" colspan="6"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td>
                <td style="font-size: 14px;font-weight: bold" class="text-right sub_total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>


<?php if (!empty($extra['prvr_detail'])) { ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';  margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%;">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%;"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%;"><?php echo $this->lang->line('common_total');?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['prvr_detail'])) {
                foreach ($extra['prvr_detail'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $this->lang->line('accounts_receivable_ap_rv_reversel_of_payment_voucher');?><!--Reversal of payment voucher--></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norec=$this->lang->line('common_no_records_found');
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="3" class="text-center">'.$norec.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px;" class="text-right sub_total" colspan="2"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td>
                <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>


<?php if (!empty($extra['gl_detail'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $customer_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';  margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
            <!-- <tr>
                <th class='theadtr' colspan="4">GL Details</th>
                <th class='theadtr'> Amount</th>
            </tr> -->
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 5%; ">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 17%;"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 34%; "><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 13%;"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 14%; "><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>)<!--Amount--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 14%; ">Discount</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 14%;">Net Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail'])) {
                foreach ($extra['gl_detail'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:left;"><?php echo $val['GLCode']; ?></td>
                        <td style="font-size: 14px; "><?php echo $val['GLDescription'].' '.$val['comment']. ' - ' .$val['description']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['segmentCode']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;">(<?php echo format_number($val['discountPercentage'], 2)  ?> %) <?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total += $val['companyLocalAmount'];
                    //$customer_total += $val['customerAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norecord=$this->lang->line('common_no_records_found');
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">'.$norecord.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px;" class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total, $extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($customer_total, $extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['invoice'])) {
$transaction_total = 0;
$Local_total = 0;
$customer_total = 0; ?>
<br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';  margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; " colspan="4"><?php echo $this->lang->line('accounts_receivable_common_invoice_details');?> <!--Invoice Details--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;" colspan="4"><?php echo $this->lang->line('common_amount');?><!--Amount--> ( <?php echo $extra['master']['transactionCurrency']; ?> )</th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 5%; ">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 15%;"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th class='text-left' style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 15%;"><?php echo $this->lang->line('common_reference');?><!--Reference--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 11%; "><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%; "><?php echo $this->lang->line('accounts_receivable_common_invoice');?><!--Invoice--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%;"><?php echo $this->lang->line('accounts_receivable_common_due');?><!--Due--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%;"><?php echo $this->lang->line('accounts_receivable_common_paid');?><!--Paid--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%; "><?php echo $this->lang->line('accounts_receivable_common_balance');?><!--Balance--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['invoice'])) {
                foreach ($extra['invoice'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px;text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;text-align:left;"><?php echo $val['invoiceCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['referenceNo'].' '.$val['comment']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['invoiceDate']; ?></td>
                        <td style="font-size: 14px;text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px;text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px;text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px;text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {

                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">'.$no.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px;" class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td style="font-size: 14px;" class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div><br>
<?php //}
} ?>
<?php if(!empty($extra['advance'])){ $transaction_total = 0;$Local_total = 0;$customer_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
                <tr>
                    <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 5%; ">#</th>
                    <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 50%; "><?php echo $this->lang->line('accounts_receivable_ap_rv_advance_description');?><!--Advance Description--></th>
                    <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%; "><?php echo $this->lang->line('common_amount');?><!--Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            if (!empty($extra['advance'])) {
                foreach ($extra['advance'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['comment']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num ++;
                    $transaction_total  +=$val['transactionAmount'];
                    $grand_total        +=$val['transactionAmount'];
                }
            }else{
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="5" class="text-center">'.$no.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px;" class="text-right sub_total" colspan="2"><?php echo $this->lang->line('accounts_receivable_common_advance_total');?><!--Advance Total--> </td>
                <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['creditnote'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="margin-left:-0.5cm; margin-right:-0.5cm;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;" colspan="4">Credit Note </th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;" colspan="4">Amount
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th>
            </tr>
            <tr>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 5%;">#</th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 15%;">Code</th>
                <th class='text-left' style="font-size: 14px; border-bottom: 1px solid black;min-width: 15%;">Reference</th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 11%; ">Date </th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 13%; ">Invoice </th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 13%; ">Due</th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 13%; ">Paid </th>
                <th style="font-size: 14px; border-bottom: 1px solid black;min-width: 13%;">Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['creditnote'])) {
                foreach ($extra['creditnote'] as $val) { ?>
                    <tr>
                        <td style="font-size: 12px;font-weight:normal; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 12px;font-weight:normal; text-align:left;"><?php echo $val['invoiceCode']; ?></td>
                        <td style="font-size: 12px;font-weight:normal;"><?php echo $val['referenceNo']; ?></td>
                        <td style="font-size: 12px;font-weight:normal;"><?php echo $val['invoiceDate']; ?></td>
                        <td style="font-size: 12px;font-weight:normal; text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 12px;font-weight:normal; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 12px;font-weight:normal; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 12px;font-weight:normal; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total -= $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');

                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
                <tr>
                    <td style="font-size: 14px;" class="text-right sub_total" colspan="6"> Credit Note Total (<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                    <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="font-size: 14px;" class="text-right sub_total">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<div class="table-responsive">
    <table style="width: 100%; margin-left:-0.5cm; margin-right:-0.5cm;">
        <tr>
            <td style="width:40%;">
                &nbsp;
            </td>
            <td style="width:60%;padding: 0;">
                <?php
                if (!empty($extra['tax_detail'])) { ?>
                    <table class="table table-striped" style="width: 100%; margin-top:10px;">
                        <thead>
                        <tr>
                            <td colspan="5" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; ">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('accounts_receivable_ap_rv_tax_detail');?><!--Tax Details--></strong></td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;">#</th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('accounts_receivable_common_detail');?><!--Detail--> </th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                        foreach ($extra['tax_detail'] as $value) {
                            echo '<tr>';
                            echo '<td style="font-size: 14px;">'.$x.'.</td>';
                            echo '<td style="font-size: 14px;">'.$value['taxShortCode'].'</td>';
                            echo '<td style="font-size: 14px;">'.$value['taxDescription'].'</td>';
                            echo '<td style="font-size: 14px;" class="text-right">'.$value['taxPercentage'].' % </td>';
                            echo '<td style="font-size: 14px;" class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $grand_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                            $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="font-size: 14px;" colspan="4" class="text-right sub_total"><?php echo $this->lang->line('common_tax_total');?><!--Tax Total--> </td>
                                <td style="font-size: 14px;" class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif'; font-size: 14px;">
    <h5 style="font-size: 14px; font-weight:bold" class="text-right"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<br>
<br>

<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
            Approved By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
            Received By
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/<?php echo $extra['master']['receiptVoucherAutoId'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + <?php echo $extra['master']['receiptVoucherAutoId'] ?> + '/RV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>
