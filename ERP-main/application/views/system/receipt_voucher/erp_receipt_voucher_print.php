<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$no=$this->lang->line('common_no_records_found'); /*No recored found Arabic Tranilation*/
$itemBatch_policy = getPolicyValues('IB', 'All');

echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php if($extra['master']['RVType'] == 'DirectItem' || $extra['master']['RVType'] == 'DirectIncome'){
                                echo 'Direct ';
                                }else {
                                    echo 'Customer ';
                                }
//                            echo ($extra['master']['RVType']=='Direct' ? $extra['master']['RVType'] : 'Customer');
                            echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher');?><!--Receipt Voucher--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_number');?><!--Receipt Voucher Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['RVcode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date');?><!--Receipt Voucher Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['RVdate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referanceNo']; ?></td>
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
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_bank');?> <!--Bank--></strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"> <?php echo $extra['master']['RVbank']; ?><?php if(!empty($extra['master']['RVbankBranch']) && $extra['master']['RVbankBranch']!='-'){ echo ' / ' . $extra['master']['RVbankBranch'];}  ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('accounts_receivable_common_customer_address');?> <!--Customer Address--> </strong></td>
            <td><strong>:</strong></td>
            <td> <?php if (!empty($extra['master']['customeradd'])) echo $extra['master']['customeradd']; ?></td>
            <td><strong> <?php echo $this->lang->line('accounts_receivable_common_bank_account');?><!--Bank Account--></strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['RVbankAccount'] ?> </td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('accounts_receivable_common_telephone_fax');?><!--Telephone / Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php if (!empty($extra['master']['customertel'])) echo $extra['master']['customertel'] . ' / ' . $extra['master']['customerfax']; ?></td>
            <td><strong> <?php echo $this->lang->line('accounts_receivable_common_bank_swift_code');?><!--Bank Swift Code--> </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['RVbankSwiftCode']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php if($extra['master']['modeOfPayment'] == 2 && $extra['master']['paymentType'] == 1) {?>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_cheque_number');?><!--Cheque Number--></strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['RVchequeNo']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['RVNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['RVNarration']; ?>
            </td>
            <?php if($extra['master']['modeOfPayment'] == 2 && $extra['master']['paymentType'] == 1) {?>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_cheque_date');?><!--Cheque Date--></strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['RVchequeDate']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td><strong>Segment </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['segDescription']; ?> (<?php echo $extra['master']['segmentCode']; ?>)</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
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
                <th class='theadtr' colspan="<?php echo ($itemBatch_policy == 1 ? '8':'6')?>"><?php echo $this->lang->line('accounts_receivable_common_item_details');?><!--Item Details--></th>
                <?php if($isGroupByTax == 1) { ?>
                    <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_price');?><!--Price--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                <?php } else { ?>
                    <th class='theadtr' colspan="2"><?php echo $this->lang->line('common_price');?><!--Price--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('accounts_receivable_common_item_code');?><!--Item Code--></th>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_item_description');?><!--Item Description--></th>
                <?php if ($itemBatch_policy == 1) { ?>
                    <th class='theadtr' style="min-width: 10%">Batch Number</th>
                <?php }?>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_remarks');?><!--Remarks--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?><!--Total--></th>
                <?php if($isGroupByTax == 1) { ?>
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_tax');?><!--Unit--></th>
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_net_amount');?><!--Total--></th>
                <?php } ?>
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
                        <td style="text-align:left;"><?php echo $val['itemSystemCode']; ?></td>
                        <td><?php echo $val['Itemdescriptionpartno']; ?></td>
                        <?php if($itemBatch_policy==1){?>
                        <td><?php echo $val['batchNumber']; ?></td>
                        <?php }?>
                        <td><?php echo $val['remarks']; ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax == 1) { ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php
                                if($val['taxAmount'] > 0) {
                                    echo ' <a onclick="open_tax_dd(null,'.$val['receiptVoucherAutoId'].',\'RV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['receiptVoucherDetailAutoID'].', \'srp_erp_customerreceiptdetail\',\'receiptVoucherDetailAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                } else {
                                    echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                }
                                ?></td>
                        <?php } ?>
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
                <?php if($isGroupByTax == 1) { ?>
                    <td class="text-right sub_total" colspan="9"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->(<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('accounts_receivable_common_item_total');?><!--Item Total-->(<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                <?php } ?>
               
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
                <tr>
                    <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_income');?> GL Details</th>
                    <th class='theadtr' colspan="3"> Amount</th>
                </tr>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="width: 17%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th class='theadtr' style="width: 34%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th class='theadtr' style="width: 14%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>)<!--Amount--></th>
                <th class='theadtr' style="width: 14%">Discount</th>
                <?php if($isGroupByTax == 1) { ?>
                    <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                <?php } ?>
                <th class='theadtr' style="width: 14%">Net Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail'])) {
                foreach ($extra['gl_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:left;"><?php echo $val['GLCode']; ?></td>
                        <td><?php echo $val['GLDescription'].' '.$val['comment']. ' - ' .$val['description']; ?></td>
                        <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                        <?php if($isGroupByTax == 1) { ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } else { ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } ?>
                        <td style="text-align:right;">(<?php echo format_number($val['discountPercentage'], 2)  ?> %) <?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax == 1) { ?>
                            <td style="text-align:right;"><?php
                                if($val['taxAmount'] > 0) {
                                    echo ' <a onclick="open_tax_dd(null,'.$val['receiptVoucherAutoId'].',\'RV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['receiptVoucherDetailAutoID'].', \'srp_erp_customerreceiptdetail\',\'receiptVoucherDetailAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                } else {
                                    echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                }
                            ?></td>
                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

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
                echo '<tr class="danger"><td colspan="7" class="text-center">'.$norecord.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <?php if($isGroupByTax == 1) { ?>
                    <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <?php } ?>
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
                            <td style="text-align:left;"><?php echo $val['invoiceCode']; ?></td>
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


<?php //}
} ?>


<?php if (!empty($extra['sup_invoice'])) { 

        $sup_transaction_total = 0;
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
                if (!empty($extra['sup_invoice'])) {
                    foreach ($extra['sup_invoice'] as $val) { ?>
                        <tr>
                            <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="text-align:left;"><?php echo $val['invoiceCode']; ?></td>
                            <td><?php echo $val['referenceNo'].' '.$val['comment']; ?></td>
                            <td style="text-align:center;"><?php echo $val['invoiceDate']; ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $sup_transaction_total += $val['transactionAmount'];
                        $grand_total += $val['transactionAmount']*-1;
                    }
                } else {

                    echo '<tr class="danger"><td colspan="7" class="text-center">'.$no.'<!--No Records Found--></td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                    <td class="text-right total"><?php echo format_number($sup_transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
                <?php if($isGroupByTax == 1) { ?>
                    <th class='theadtr' style="min-width: 15%">Document Code</th>
                    <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_tax');?><!--Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <?php } ?>
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
                    <?php if($isGroupByTax == 1) { ?>
                        <td><a  onclick="requestPageView_model( '<?php echo $val['contractDocID']; ?>', <?php echo $val['contractAutoID']; ?>)"><?php echo $val['contractCode']; ?></a></td>
                        <td style="text-align:right;"><?php
                            if($val['taxAmount'] > 0) {
                                echo ' <a onclick="open_tax_dd(null,'.$val['receiptVoucherAutoId'].',\'RV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['receiptVoucherDetailAutoID'].', \'srp_erp_customerreceiptdetail\',\'receiptVoucherDetailAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                            } else {
                                echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                            }
                        ?></td>
                    <?php } ?>
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
                <?php if($isGroupByTax == 1) { ?>
                    <td class="text-right sub_total" colspan="4"><?php echo $this->lang->line('accounts_receivable_common_advance_total');?><!--Advance Total--> </td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="2"><?php echo $this->lang->line('accounts_receivable_common_advance_total');?><!--Advance Total--> </td>
                <?php } ?>
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
                        <td style="text-align:left;"><?php echo $val['invoiceCode']; ?></td>
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


<?php if (!empty($extra['expense_gl_detail'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $customer_total = 0; ?>
    <br>
    <div class="table-responsive">
        <!-- <p class="text-danger">*** Expense GL going as minus. </p> -->
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_expense');?> GL Details</th>
                <th class='theadtr' colspan="4"> Amount</th>
            </tr>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="width: 17%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th class='theadtr' style="width: 34%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th class='theadtr' style="width: 14%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>)<!--Amount--></th>
                <th class='theadtr' style="width: 14%">Discount</th>
                <?php if($isGroupByTax == 1) { ?>
                    <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                <?php } ?>
                <th class='theadtr' style="width: 14%">Net Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['expense_gl_detail'])) {
                foreach ($extra['expense_gl_detail'] as $val) { 
                   
                    ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:left;"><?php echo $val['GLCode']; ?></td>
                        <td><?php echo $val['GLDescription'].' '.$val['comment']. ' - ' .$val['description']; ?></td>
                        <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                        <?php if($isGroupByTax == 1) { ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } else { ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } ?>
                        <td style="text-align:right;">(<?php echo format_number($val['discountPercentage'], 2)  ?> %) <?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax == 1) { ?>
                            <td style="text-align:right;"><?php
                                if($val['taxAmount'] > 0) {
                                    echo ' <a onclick="open_tax_dd(null,'.$val['receiptVoucherAutoId'].',\'RV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['receiptVoucherDetailAutoID'].', \'srp_erp_customerreceiptdetail\',\'receiptVoucherDetailAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                } else {
                                    echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                }
                            ?></td>
                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total += $val['companyLocalAmount'];
                    //$customer_total += $val['customerAmount'];
                    $grand_total += $val['transactionAmount'] * -1;
                    $tax_transaction_total += $val['transactionAmount'] * -1;
                }
            } else {
                $norecord=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">'.$norecord.'<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <?php if($isGroupByTax == 1) { ?>
                    <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('common_total');?> <!--Total--></td>
                <?php } ?>
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total, $extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($customer_total, $extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
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
<div class="table-responsive">
    <h5 class="text-right"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<br>
<?php
    $data['documentCode'] = 'RV';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['receiptVoucherAutoId'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
?>

    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width:30%;"><b>
                            <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
                </tr>
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
                <tr>
                    <td style="width:30%;">&nbsp;</td>
                    <td><strong>&nbsp;</strong></td>
                    <td style="width:70%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:30%;"><b><?php echo $this->lang->line('accounts_receivable_common_received_by');?><!--Received by--> </b></td>
                    <td><strong>:</strong></td>
                    <td style="width:70%;">_____________________</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

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
    a_link=  "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/<?php echo $extra['master']['receiptVoucherAutoId'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + <?php echo $extra['master']['receiptVoucherAutoId'] ?> + '/RV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>
