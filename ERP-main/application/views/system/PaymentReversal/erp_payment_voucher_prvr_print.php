<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);



echo fetch_account_review(true,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $extra['master']['pvType']; ?> <?php echo $this->lang->line('sales_markating_sales_payment_voucher');?></h4><!--Payment Voucher-->
                        </td>
                    </tr>
                    <tr>
                        <td><strong> <?php echo $this->lang->line('sales_markating_sales_payment_voucher_number');?></strong></td><!--Payment Voucher Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVcode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_voucher_date');?></strong></td><!--Payment Voucher Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVdate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNo']; ?></td>
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
            <td style="width:15%;"><strong> <?php echo $this->lang->line('common_name');?> </strong></td><!--Name-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:40%;"> <?php echo (empty($extra['master']['partyCode'])) ? $extra['master']['partyName'] : $extra['master']['partyName'].' ( '.$extra['master']['partyCode'].' )'; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_bank');?> </strong></td><!--Bank-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"> <?php echo $extra['master']['PVbank'].' / '.$extra['master']['PVbankBranch']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_address');?>  </strong></td><!--Address-->
            <td><strong>:</strong></td>
            <td> <?php if (!empty($extra['master']['partyCode'])) echo $extra['master']['partyAddress']; ?></td>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_bank_account');?> </strong></td><!--Bank Account-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVbankAccount'] ?> </td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_telephone');?> / <?php echo $this->lang->line('common_fax');?> </strong></td><!--Telephone / Fax-->
            <td><strong>:</strong></td>
            <td><?php if (!empty($extra['master']['partyCode'])) echo $extra['master']['partyTelephone'].' / '.$extra['master']['partyFax']; ?></td>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_bank_swift_code');?></strong></td><!--Bank Swift Code-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['PVbankSwiftCode']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
            <td><strong><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_number');?></strong></td><!--Cheque Number-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVchequeNo']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration -->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVNarration']; ?></td>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_date');?></strong></td><!--Cheque Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVchequeDate']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<?php $grand_total = 0;
$tax_transaction_total = 0;
if (!empty($extra['item_detail'])) { ?><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details'); ?></th>
                <!--Item Details-->
                <th class='theadtr' colspan="2"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code'); ?></th>
                <!--Item Code-->
                <th class='theadtr'
                    style="min-width: 45%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description'); ?></th>
                <!--Item Description-->
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom'); ?></th>
                <!--UOM-->
                <th class='theadtr'
                    style="min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty'); ?></th>
                <!--Qty-->
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit'); ?></th>
                <!--Unit-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total'); ?></th>
                <!--Total-->
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
                        <td><?php echo $val['itemDescription'] . ($val['comment'] ? ' - ' . $val['comment'] : ''); ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
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

                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </td><!--Item Total-->
                <td class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_payment_gl_details'); ?></th>
                <!--GL Details-->
                <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 3%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code'); ?></th>
                <!--GL Code-->
                <th class='theadtr'
                    style="min-width: 40%"><?php echo $this->lang->line('sales_markating_sales_payment_gl_descriptions'); ?></th>
                <!--GL Code Description-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_segment'); ?></th>
                <!--Segment-->
                <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_transaction'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th><!--Transaction-->
                <!-- <th class='theadtr' style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 10%">Party (<?php //echo $extra['master']['partyCurrency']; ?>)</th> -->
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
                        <td><?php echo $val['GLDescription'] . ($val['comment'] ? ' - ' . $val['comment']  : '').' - '. $val['description']; ?></td>
                        <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <!-- <td style="text-align:right;"><?php //echo format_number($val['companyLocalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php //echo format_number($val['partyAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total        +=$val['companyLocalAmount'];
                    //$party_total        +=$val['partyAmount'];
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
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td>
                <!--GL Total-->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($party_total,$extra['master']['partyCurrencyDecimalPlaces']); ?></td> -->
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
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_order_details'); ?></th>
                <!--PO Details-->
                <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 3%">#</th>
                <th class='theadtr'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_code'); ?></th>
                <!--PO Code-->
                <th class='theadtr'
                    style="min-width: 40%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_description'); ?></th>
                <!--PO Description-->
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_purachase_order_date'); ?></th>
                <!--PO Date-->
                <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_transaction'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th><!--Transaction-->
                <!-- <th class='theadtr' style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 10%">Party (<?php //echo $extra['master']['partyCurrency']; ?>)</th> -->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['advance'])) {
                foreach ($extra['advance'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['POCode']; ?></td>
                        <td><?php echo $val['PODescription'] . ($val['comment'] ? ' - ' . $val['comment'] : ''); ?></td>
                        <td style="text-align:center;"><?php echo $val['PODate']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <!-- <td style="text-align:right;"><?php //echo format_number($val['companyLocalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php //echo format_number($val['partyAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total        +=$val['companyLocalAmount'];
                    //$party_total        +=$val['partyAmount'];
                    $grand_total += $val['transactionAmount'];
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>.<!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_order_total'); ?> </td>
                <!--PO Total-->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($party_total,$extra['master']['partyCurrencyDecimalPlaces']); ?></td> -->
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
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_details'); ?> </th>
                <!--Commission Details-->
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
                <!--Code-->
                <th class='theadtr text-left'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th>
                <!--Reference-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?></th>
                <!--Date-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_com'); ?> </th>
                <!--Commission-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th>
                <!--Due-->
                <th class='theadtr'
                    style="min-width: 13%"> <?php echo $this->lang->line('sales_markating_sales_purachase_commission_paid'); ?> </th>
                <!--Paid-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?> </th>
                <!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['sales_commission'])) {
                foreach ($extra['sales_commission'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['bookingInvCode']; ?></td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;"><?php echo $val['bookingDate']; ?></td>
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
                $norecordsfound = $this->lang->line('sales_markating_sales_purachase_commission_com');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total-->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
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
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_details'); ?> </th>
                <!--Invoice Details-->
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
                <!--Code-->
                <th class='theadtr text-left'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th>
                <!--Reference-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th>
                <!--Date-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th>
                <!--Invoice-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th>
                <!--Due-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_paid'); ?> </th>
                <!--Paid-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?></th>
                <!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['invoice'])) {
                foreach ($extra['invoice'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['bookingInvCode']; ?></td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;"><?php echo $val['bookingDate']; ?></td>
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
                $norecordsfound = $this->lang->line('common_no_records_found');

                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total-->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<br>
<?php if (!empty($extra['debitnote'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_debit_note_details'); ?> </th>
                <!--Debit note Details-->
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
                <!--Code-->
                <th class='theadtr text-left'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th>
                <!--Reference-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th>
                <!--Date-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th>
                <!--Invoice-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th>
                <!--Due-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_matched'); ?>  </th>
                <!--Matched-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?>  </th>
                <!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['debitnote'])) {
                foreach ($extra['debitnote'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['bookingInvCode']; ?></td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;"><?php echo $val['bookingDate']; ?></td>
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
                <td class="text-right sub_total"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total -->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<br>
<?php if (!empty($extra['SR'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4">Stock Return Details </th>
                <!--Debit note Details-->
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
                <!--Code-->
                <th class='theadtr text-left'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th>
                <!--Reference-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th>
                <!--Date-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice'); ?> </th>
                <!--Invoice-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_due'); ?></th>
                <!--Due-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_matched'); ?>  </th>
                <!--Matched-->
                <th class='theadtr'
                    style="min-width: 13%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_balance'); ?>  </th>
                <!--Balance-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['SR'])) {
                foreach ($extra['SR'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['bookingInvCode']; ?></td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;"><?php echo $val['bookingDate']; ?></td>
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
                <td class="text-right sub_total"
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total -->
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>

<br>
<div class="table-responsive">
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
                            <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?><!--Tax Details--></strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                            <th class='theadtr'><?php echo $this->lang->line('common_details');?> </th><!--Detail-->
                            <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                            <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_transaction');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Transaction-->
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
                            <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?><!--Tax Total--> </td>
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
    <h5 class="text-right"><?php echo $this->lang->line('common_grand_total'); ?><!--Grand Total-->
        (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<?php if($extra['master']['approvedYN']){ ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?></b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;">&nbsp;</td>
                <td><strong>&nbsp;</strong></td>
                <td style="width:70%;">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_sales_purachase_commission_collected_by');?></b></td><!--Collected By-->
                <td><strong>:</strong></td>
                <td style="width:70%;">_____________________</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/<?php echo $extra['master']['payVoucherAutoId'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher_prvr'); ?>/" + <?php echo $extra['master']['payVoucherAutoId'] ?> + '/PV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

</script>
