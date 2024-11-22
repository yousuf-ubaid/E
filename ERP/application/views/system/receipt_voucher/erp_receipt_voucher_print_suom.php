<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$no=$this->lang->line('common_no_records_found'); /*No recored found Arabic Tranilation*/

echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="text-align: center">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 60px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center">
                <table>
                    <tr>
                        <td colspan="5">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><b>Email :</b><?php echo $this->common_data['company_data']['company_email']; ?></p>
                            <p><b>Phone :</b><?php echo $this->common_data['company_data']['company_phone']; ?></p>
                            <h4><?php echo ($extra['master']['RVType']=='Direct' ? $extra['master']['RVType'] : 'Customer'); ?> <?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher');?><!--Receipt Voucher--></h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:40%;"><?php echo (empty($extra['master']['customersys'])) ? $extra['master']['customerName'] : $extra['master']['customerName'] . ' ( ' . $extra['master']['customersys'] . ' )'; ?></td>
            <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_number');?><!--Receipt Voucher Number--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['RVcode']; ?></td>
        </tr>
        <tr>
            <?php if($extra['master']['RVType'] != 'Direct'){?>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_customer_address');?> <!--Customer Address--> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php if (!empty($extra['master']['customeradd'])) echo $extra['master']['customeradd']; ?></td>
                <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date');?><!--Receipt Voucher Date--></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['RVdate']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <?php if($extra['master']['RVType'] != 'Direct'){?>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_telephone_fax');?><!--Telephone / Fax--></strong></td>
                <td><strong>:</strong></td>
                <td><?php if (!empty($extra['master']['customertel'])) echo $extra['master']['customertel'] . ' / ' . $extra['master']['customerfax']; ?></td>
            <?php } else { ?>
                <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date');?><!--Receipt Voucher Date--></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['RVdate']; ?></td>
            <?php } ?>
            <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referanceNo']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td><strong>Sales Person</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['SalesPersonCode']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['RVNarration']; ?></td>
            <td><strong>Segment</strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['segDescription']; ?> (<?php echo $extra['master']['segmentCode']; ?>)</td>
        </tr>
        </tbody>
    </table>
</div>
<?php $grand_total = 0;$tax_transaction_total=0;
if (!empty($extra['item_detail'])) { ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('accounts_receivable_common_item_code');?><!--Item Code--></th>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_item_description');?><!--Item Description--></th>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_warehouse');?><!--Item Description--></th>
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['item_detail'])) {
                foreach ($extra['item_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                        <td><?php echo $val['Itemdescriptionpartno'] ?></td>
                        <td><?php echo $val['wareHouseCode'] ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?> &nbsp; <?php echo $val['requestedQty']; ?>
                            <?php if(!empty($val['secuom'])) { ?>
                                <br> <?php echo $val['secuom']; ?> &nbsp; <?php echo $val['SUOMQty']; ?></td>
                            <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td>
                <td class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>


<?php if (!empty($extra['prvr_detail'])) { ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['prvr_detail'])) {
                foreach ($extra['prvr_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $this->lang->line('accounts_receivable_ap_rv_reversel_of_payment_voucher');?><!--Reversal of payment voucher--></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norec=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="3" class="text-center">'.$norec.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="2"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td>
                <td class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
        <table class="table table-bordered table-striped">
            <thead>
            <!-- <tr>
                <th class='theadtr' colspan="4">GL Details</th>
                <th class='theadtr'> Amount</th>
            </tr> -->
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th class='theadtr' style="min-width: 40%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_amount');?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)<!--Amount-->
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail'])) {
                foreach ($extra['gl_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['GLCode']; ?></td>
                        <td><?php echo $val['GLDescription'].' '.$val['comment']. ' - ' .$val['description']; ?></td>
                        <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <!-- <td style="text-align:right;"><?php //echo format_number($val['companyLocalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php //echo format_number($val['customerAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
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
                echo '<tr class="danger"><td colspan="5" class="text-center">'.$norecord.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="4"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('accounts_receivable_common_invoice_details');?> <!--Invoice Details--></th>
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_amount');?><!--Amount--> ( <?php echo $extra['master']['transactionCurrency']; ?> )</th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th class='theadtr text-left' style="min-width: 15%"><?php echo $this->lang->line('common_reference');?><!--Reference--></th>
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('accounts_receivable_common_invoice');?><!--Invoice--></th>
                <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('accounts_receivable_common_due');?><!--Due--></th>
                <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('accounts_receivable_common_paid');?><!--Paid--></th>
                <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('accounts_receivable_common_balance');?><!--Balance--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['invoice'])) {
                foreach ($extra['invoice'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['invoiceCode']; ?></td>
                        <td><?php echo $val['referenceNo'].' '.$val['comment']; ?></td>
                        <td style="text-align:center;"><?php echo $val['invoiceDate']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {

                echo '<tr class="danger"><td colspan="7" class="text-center">'.$no.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div><br>
<?php } ?>
<?php if(!empty($extra['advance'])){ $transaction_total = 0;$Local_total = 0;$customer_total = 0; ?>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <!-- <tr>
                <th class='theadtr' colspan="2">Advance Details</th>
                <th class='theadtr' colspan="3"> Amount </th>
            </tr> -->
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 50%"><?php echo $this->lang->line('accounts_receivable_ap_rv_advance_description');?><!--Advance Description--></th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <!-- <th class='theadtr' style="min-width: 15%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 15%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;
            if (!empty($extra['advance'])) {
                foreach ($extra['advance'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td><?php echo $val['comment']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <!-- <td style="text-align:right;"><?php //echo format_number($val['companyLocalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php //echo format_number($val['customerAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                </tr>
                <?php
                    $num ++;
                    $transaction_total  +=$val['transactionAmount'];
                    //$Local_total        +=$val['companyLocalAmount'];
                    //$customer_total     +=$val['customerAmount'];
                    $grand_total        +=$val['transactionAmount'];
                } 
            }else{
                echo '<tr class="danger"><td colspan="5" class="text-center">'.$no.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="2"><?php echo $this->lang->line('accounts_receivable_common_advance_total');?><!--Advance Total--> </td>
                <td class="text-right total"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($customer_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
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
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="4">Credit Note </th>
                <th class='theadtr' colspan="4">Amount
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%">Code</th>
                <th class='theadtr text-left' style="min-width: 15%">Reference</th>
                <th class='theadtr' style="min-width: 11%">Date </th>
                <th class='theadtr' style="min-width: 13%">Invoice </th>
                <th class='theadtr' style="min-width: 13%">Due</th>
                <th class='theadtr' style="min-width: 13%">Paid </th>
                <th class='theadtr' style="min-width: 13%">Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['creditnote'])) {
                foreach ($extra['creditnote'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['invoiceCode']; ?></td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;"><?php echo $val['invoiceDate']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total -= $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');

                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6"> Credit Note Total
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td>
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<br><div class="table-responsive">
    <table style="width: 100%">
        <tr>
           <td style="width:40%;">
                &nbsp;
           </td> 
           <td style="width:60%;padding: 0;">
            <?php  
            if (!empty($extra['tax_detail'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('accounts_receivable_ap_rv_tax_detail');?><!--Tax Details--></strong></td>
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                <th class='theadtr'><?php echo $this->lang->line('accounts_receivable_common_detail');?><!--Detail--> </th>
                                <th class='theadtr'><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                                <th class='theadtr'><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                                <!-- <th class='theadtr'>Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th class='theadtr'>Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            //$tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                            //$tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax_detail'] as $value) {
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
                                $grand_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                                //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                                //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('common_tax_total');?><!--Tax Total--> </td>
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <!-- <td class="text-right sub_total"><?php //echo format_number($loc_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right sub_total"><?php //echo format_number($cu_total_amount,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
                            </tr>
                        </tfoot>
                    </table>
            <?php } ?>           
           </td>
        </tr>
    </table>
</div>
<?php if ($grand_total > 0){ ?>
<div class="table-responsive" style="margin-top: -45px">
    <h5 class="text-right"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<?php } ?>

<hr><br><br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:20%;">
                &nbsp;
            </td>
            <td style="width:80%;padding: 0;">
                <?php
                $payment_grand_total = 0;
                if (!empty($extra['payment_detail'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="7">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Payment Details</strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr' style="min-width: 20%">Bank Name</th>
                            <th class='theadtr'><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th class='theadtr'><?php echo $this->lang->line('accounts_receivable_common_cheque_number'); ?><!--Cheque Number--> </th>
                            <th class='theadtr'> <?php echo $this->lang->line('accounts_receivable_common_cheque_date'); ?><!--Cheque Date-->  </th>
                            <th class='theadtr'> <?php echo $this->lang->line('accounts_receivable_common_reference_no'); ?><!--Reference No-->  </th>
                            <th class='theadtr'> <?php echo $this->lang->line('common_amount'); ?><!--amount-->  </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1; $payment_total=0;
                        foreach ($extra['payment_detail'] as $value) {
                            echo '<tr>';
                            echo '<td>' . $x . '.</td>';
                            echo '<td>' . $value['bankDetails'] . '</td>';
                            echo '<td>' . $value['paymentMode'] . '</td>';
                            echo '<td class="">' . $value['chequeNo'] . '</td>';
                            echo '<td class="">' . $value['chequeDate'] . '</td>';
                            echo '<td class="">' . $value['memo'] . '</td>';
                            echo '<td class="text-right">' . format_number((($value['amount'])), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '</tr>';
                            $x++;
                            $payment_total += $value['amount'];
                            $payment_grand_total += $value['amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6" class="text-right sub_total"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
                            <td class="text-right sub_total"><?php echo format_number($payment_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<?php if (!empty($extra['payment_detail'])) { ?>
<div class="table-responsive" style="">
    <h5 class="text-right"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($payment_grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<?php } ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <?php if ($extra['master']['confirmedYN']==1) { ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn']; ?></td>
            </tr>
            <?php }?>
            <?php if ($extra['master']['approvedYN']) { ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            <!--<tr>
                <td style="width:30%;">&nbsp;</td>
                <td><strong>&nbsp;</strong></td>
                <td style="width:70%;">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php /*echo $this->lang->line('accounts_receivable_common_received_by');*/?></b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;">_____________________</td>
            </tr>-->
            <?php } ?>
            </tbody>
        </table>
    </div>


<br>
<?php if($extra['master']['approvedYN']){ ?>
<?php }
if ($html == false){ ?>
    <br>
    <br>
    <br>
    <div class="col-sm-12">
        <table border="0px">
            <tr>
                <td class="text-center">----------------------------------------</td>
                <td class="text-center">----------------------------------------</td>
            </tr>
            <tr>
                <td class="text-center">Customer Signature</td>
                <td class="text-center">Sales Executive</td>
            </tr>
        </table>
    </div>
<br>
    <?php echo '<div class="text-center"><b>APPROVED BY NATIONAL GEM & JEWELLERY AUTHORITY & SRI LANKA TOURIST BOARD</b></div>';  } ?>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>/<?php echo $extra['master']['receiptVoucherAutoId'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher_suom'); ?>/" + <?php echo $extra['master']['receiptVoucherAutoId'] ?> + '/RV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>
