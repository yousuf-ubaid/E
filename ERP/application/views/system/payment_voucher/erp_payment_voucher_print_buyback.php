<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 ><?php echo $this->lang->line('sales_markating_sales_payment_voucher');?></h4><!--Payment Voucher -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif'; width: 100%">
        <tbody>
        <tr>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:15%;"><strong> <?php echo $this->lang->line('common_name'); ?> </strong></td><!--Name-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:35%;"> <?php echo (empty($extra['master']['supsyscode'])) ? $extra['master']['partyName'] : $extra['master']['partyName']; ?></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:25%;"><strong> Payment Voucher </strong></td><!--Name-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:25%;"> <?php echo $extra['master']['PVcode']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('common_address'); ?>  </strong></td><!--Address-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php  echo $extra['master']['partyAddresss']; ?></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:15%;"><strong> <?php echo $this->lang->line('sales_markating_sales_payment_voucher_date'); ?> </strong></td><!--Payment Voucher Date-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:40%;"> <?php echo $extra['master']['PVdate']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('common_telephone'); ?> / <?php echo $this->lang->line('common_fax'); ?> </strong></td><!--Telephone / Fax-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><?php echo  $extra['master']['parttelfax']; ?></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:15%;"><strong> <?php echo $this->lang->line('common_reference_number'); ?> </strong></td><!--Reference Number-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:40%;"> <?php echo $extra['master']['referenceNo']; ?></td>
        </tr>
        <tr>
           
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:15%;"><strong><?php echo $this->lang->line('common_bank'); ?> </strong></td><!--Bank-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; width:33%;"> <?php echo $extra['master']['PVbank'] . ' / ' . $extra['master']['PVbankBranch']; ?></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('sales_markating_sales_payment_bank_account'); ?> </strong></td>
            <!--Bank Account-->
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php echo $extra['master']['PVbankAccount'] ?> </td>
       
        </tr>
        <tr>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('common_segment'); ?><!--Segment--></strong></td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; ">:</td>
            <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php echo $extra['master']['segDescription']; ?> (<?php echo $extra['master']['segmentCode']; ?>)</td>
            
        </tr>
        <tr>
            <td style="font-size: 14px;  height: 8px; padding: 1px; vertical-align: top"><strong><?php echo $this->lang->line('sales_markating_narration'); ?> </strong></td><!--Narration -->
            <td style="font-size: 14px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
            <td>
                <table style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
                    <tr>
                        <td style="font-size: 14px;  height: 8px; padding: 1px;"><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['PVNarration']);?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['PVNarration']; ?>
            </td>
           
        </tr>
        <tr>
            <?php
            if ($extra['master']['paymentType']==1) {
                ?>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_number'); ?></strong>
                </td><!--Cheque Number-->
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php echo $extra['master']['PVchequeNo']; ?></td>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_date'); ?></strong></td>
                <!--Cheque Date-->
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php echo $extra['master']['PVchequeDate']; ?></td>
                <?php
            } ?>
        </tr>
        <?php
        if (!empty($extra['master']['nameOnCheque']) && $extra['master']['paymentType']==1 && ($extra['master']['pvType']=='Supplier' || $extra['master']['pvType']=='SupplierAdvance' || $extra['master']['pvType']=='SupplierInvoice' || $extra['master']['pvType']=='SupplierItem' || $extra['master']['pvType']=='SupplierExpense')) {
            ?>
            <tr>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong><?php echo $this->lang->line('sales_markating_name_on_check'); ?><!--Name on Cheque--></strong></td>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "><strong>:</strong></td>
                <td style="font-size: 14px;  height: 8px; padding: 1px; "> <?php echo $extra['master']['nameOnCheque']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php $grand_total = 0;
$tax_transaction_total = 0;
if (!empty($extra['item_detail'])) { ?><br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details'); ?></th><!--Item Details-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="2"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?>)</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code'); ?></th><!--Item Code-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description'); ?></th><!--Item Description-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom'); ?></th><!--UOM-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty'); ?></th><!--Qty-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit'); ?></th><!--Unit-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_total'); ?></th><!--Total-->
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
                        <td style="font-size: 14px;"><?php echo $val['itemSystemCode']; ?></td>
                        <td style="font-size: 14px;">
                            <?php echo $val['itemDescription']; ?>

                            <?php if(!empty($val['comment']) && empty($val['partNo']))
                            {
                                echo ' - ' .  $val['comment'];
                            }else if(!empty($val['comment']) && !empty($val['partNo']))
                            {
                                echo ' - ' .  $val['comment'] . ' - ' .'Part No : ' .$val['partNo'];
                            }
                            else if(!empty($val['partNo']))
                            {
                                echo  ' - ' . 'Part No : ' .$val['partNo'];
                            }
                            ?>
                        </td>
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
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="6" style="font-size: 14px;"><?php echo $this->lang->line('sales_markating_view_invoice_item_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td><!--Item Total-->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['gl_detail'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_sales_payment_gl_details'); ?></th><!--GL Details-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 16%"><?php echo $this->lang->line('common_gl_code'); ?></th><!--GL Code-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 30%"><?php echo $this->lang->line('sales_markating_sales_payment_gl_descriptions'); ?></th><!--GL Code Description-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 15%"><?php echo $this->lang->line('common_segment'); ?></th><!--Segment-->
                <!-- <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 12%"><?php echo $this->lang->line('common_transaction'); ?></th> --><!--Transaction-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 12%"><?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail'])) {
                foreach ($extra['gl_detail'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <?php if($val['type'] == 'EC') { ?>
                            <td style="font-size: 14px;"><a href="#" class="drill-down-cursor" onclick="requestPageView_model('EC', <?php echo $val['expenseClaimMasterAutoID']; ?>)"><?php echo $val['expenseClaimCode'] . '</a><br> <strong>GL Code : </strong>' . $val['GLCode']; ?></td>
                        <?php } else {?>
                            <td style="font-size: 14px;"><?php echo $val['GLCode']; ?></td>
                        <?php } ?>
                        <td style="font-size: 14px;"><?php echo $val['GLDescription'] . ($val['comment'] ? ' - ' . $val['comment']  : '').' - '. $val['desc']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['ecsegmentcode']; ?></td>
                        <!-- <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;"
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td><!--GL Total-->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['advance'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_sales_purachase_order_details'); ?></th><!--PO Details-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_code'); ?></th><!--PO Code-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_description'); ?></th><!--PO Description-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_date'); ?></th><!--PO Date-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%"><?php echo $this->lang->line('common_transaction'); ?> (<?php echo $extra['master']['transactionCurrency']; ?>)</th><!--Transaction-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['advance'])) {
                foreach ($extra['advance'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;">
                            <?php if ($extra['master']['pvType'] == 'Supplier' || $extra['master']['pvType'] == 'SupplierAdvance' || $extra['master']['pvType'] == 'SupplierInvoice' || $extra['master']['pvType'] == 'SupplierItem' || $extra['master']['pvType'] == 'SupplierExpense') { ?>
                            <a  onclick="requestPageView_model('PO',<?php echo $val['purchaseOrderID'] ?>)"><?php echo $val['POCode']; ?>
                                <?php }else{
                                    echo $val['POCode'];
                                } ?>
                        </td>
                        <td style="font-size: 14px;"><?php echo $val['PODescription'] . ($val['comment'] ? ' - ' . $val['comment'] : ''); ?></td>
                        <td style="font-size: 14px;"><?php echo $val['PODate']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?><!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_order_total'); ?> </td><!--PO Total-->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['sales_commission'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_details'); ?> </th><!--Commission Details-->
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                <th class='text-left' style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th><!--Reference-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 11%"><?php echo $this->lang->line('common_date'); ?></th><!--Date-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_com'); ?> </th><!--Commission-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th><!--Due-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"> <?php echo $this->lang->line('sales_markating_sales_purachase_commission_paid'); ?> </th><!--Paid-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?> </th><!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['sales_commission'])) {
                foreach ($extra['sales_commission'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['bookingInvCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['referenceNo']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['bookingDate']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('sales_markating_sales_purachase_commission_com');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?><!--No Records Found-->
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right sub_total" style="font-size: 14px;" colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</td><!--Invoice Total-->
                    <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right sub_total" style="font-size: 14px;">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['invoice'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_details'); ?> </th><!--Invoice Details-->
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                <th class='text-left' style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th><!--Reference-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th><!--Date-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th><!--Invoice-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th><!--Due-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_paid'); ?> </th><!--Paid-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?></th><!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['invoice'])) {
                foreach ($extra['invoice'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;">
                            <?php if ($extra['master']['pvType'] == 'Supplier' || $extra['master']['pvType'] == 'SupplierAdvance' || $extra['master']['pvType'] == 'SupplierInvoice' || $extra['master']['pvType'] == 'SupplierItem' || $extra['master']['pvType'] == 'SupplierExpense'){  ?>
                                <a  onclick="requestPageView_model('BSI',  <?php echo $val['InvoiceAutoID'] ?>)"><?php echo $val['bookingInvCode'] ?></a>
                            <?php }
                            else{
                                echo $val['bookingInvCode'];
                            } ?>
                        </td>
                        <td style="font-size: 14px;"><?php echo $val['referenceNo']; ?></td>
                        <td style="font-size: 14px;">
                            <?php echo $val['bookingDate']; ?>
                        </td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');

                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total-->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php  if (!empty($extra['PRQ'])) { ?><br>
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="5" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_view_invoice_item_details'); ?></th><!--Item Details-->
                <th colspan="2" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;">Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_code'); ?></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description'); ?></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom'); ?></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty'); ?></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit'); ?></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_total'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['PRQ'])) {
                foreach ($extra['PRQ'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:left;">
                            <?php
                            if ($extra['master']['pvType'] == 'PurchaseRequest') { ?>
                                <a  onclick="requestPageView_model('PRQ',  <?php echo $val['prMasterID'] ?>)"><?php echo $val['purchaseRequestCode']?></a> - <?php echo $val['itemSystemCode']; ?>
                            <?php }else{
                                echo $val['purchaseRequestCode']; ?> - <?php echo $val['itemSystemCode'];
                            }?>
                        </td>
                        <td style="font-size: 14px;">
                            <?php echo $val['itemDescription']; ?>
                            <?php if(!empty($val['comment']) && empty($val['partNo']))
                            {
                                echo ' - ' .  $val['comment'];
                            }else if(!empty($val['comment']) && !empty($val['partNo']))
                            {
                                echo ' - ' .  $val['comment'] . ' - ' .'Part No : ' .$val['partNo'];
                            }
                            else if(!empty($val['partNo']))
                            {
                                echo  ' - ' . 'Part No : ' .$val['partNo'];
                            }
                            ?>
                        </td>
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
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?><!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_total'); ?> (<?php echo $extra['master']['transactionCurrency']; ?>)</td><!--Item Total-->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['debitnote'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_debit_note_details'); ?> </th><!--Debit note Details-->
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                <th class='text-left' style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th><!--Reference-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th><!--Date-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th><!--Invoice-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th><!--Due-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_matched'); ?>  </th><!--Matched-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?>  </th><!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['debitnote'])) {
                foreach ($extra['debitnote'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['bookingInvCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['referenceNo']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['bookingDate']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
                <td class="text-right sub_total" style="font-size: 14px;"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total -->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php if (!empty($extra['SR'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
            <thead>
            <tr>
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;">Stock Return Details </th><!--Debit note Details-->
                <th colspan="4" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                <th class='text-left' style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th><!--Reference-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th><!--Date-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th><!--Invoice-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th><!--Due-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_matched'); ?>  </th><!--Matched-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?>  </th><!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['SR'])) {
                foreach ($extra['SR'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['bookingInvCode']; ?></td>
                        <td style="font-size: 14px; "><?php echo $val['referenceNo']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['bookingDate']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    $grand_total -= $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?><!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?> (<?php echo $extra['master']['transactionCurrency']; ?> )</td><!--Invoice Total -->
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:40%;">
                &nbsp;
            </td>
            <td style="width:60%;padding: 0;">
                <?php
                if (!empty($extra['tax_detail'])) { ?>
                    <table class="table-striped" style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif'; margin-top: 10px;">
                        <thead>
                        <tr>
                            <td colspan="5" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details'); ?><!--Tax Details--></strong></td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;">#</th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_details'); ?> </th><!--Detail-->
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_view_invoice_tax'); ?></th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_transaction'); ?> (<?php echo $extra['master']['transactionCurrency']; ?>)</th><!--Transaction-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x = 1;
                        $tr_total_amount = 0;
                        $cu_total_amount = 0;
                        $loc_total_amount = 0;
                        foreach ($extra['tax_detail'] as $value) {
                            echo '<tr>';
                            echo '<td style="font-size: 14px;">' . $x . '.</td>';
                            echo '<td style="font-size: 14px;">' . $value['taxShortCode'] . '</td>';
                            echo '<td style="font-size: 14px;">' . $value['taxDescription'] . '</td>';
                            echo '<td class="text-right" style="font-size: 14px;">' . $value['taxPercentage'] . ' % </td>';
                            echo '<td class="text-right" style="font-size: 14px;">' . format_number((($value['taxPercentage'] / 100) * $tax_transaction_total), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '</tr>';
                            $x++;
                            $grand_total += (($value['taxPercentage'] / 100) * $tax_transaction_total);
                            $tr_total_amount += (($value['taxPercentage'] / 100) * $tax_transaction_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total"  style="font-size: 14px;">
                                <?php echo $this->lang->line('sales_markating_view_invoice_tax_total'); ?><!--Tax Total--> </td>
                            <td class="text-right sub_total"  style="font-size: 14px;"><?php echo format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
    <h5 class="text-right" style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';font-weight: bold"><?php echo $this->lang->line('common_grand_total'); ?><!--Grand Total-->
        (<?php echo $extra['master']['transactionCurrency']; ?> ) : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

<br>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Segoe,Roboto,Helvetica,arial,sans-serif'; padding: 0px;">
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
    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/<?php echo $extra['master']['payVoucherAutoId'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + <?php echo $extra['master']['payVoucherAutoId'] ?> +'/PV';
    $("#a_link").attr("href", a_link);
    $(".de_link").attr("href", de_link);

</script>