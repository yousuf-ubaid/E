<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$itemBatch_policy = getPolicyValues('IB', 'All');
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval && $extra['master']['approvedYN']);
$advanceCostCapturing = getPolicyValues('ACC', 'All');
?>
<div class="table-responsive tb-responsive-main">
    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
    ?>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;" class="layer-1">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" class="main-logo"
                                 src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>" style="max-height: 100px;max-width:250px">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;" class="layer-1">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('sales_markating_sales_payment_voucher'); ?></h4>
                            <!--Payment Voucher-->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong> <?php echo $this->lang->line('sales_markating_sales_payment_voucher_number'); ?></strong>
                        </td><!--Payment Voucher Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVcode']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php echo $this->lang->line('sales_markating_sales_payment_voucher_date'); ?></strong>
                        </td><!--Payment Voucher Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVdate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number'); ?></strong></td>
                        <!--Reference Number-->
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
<?php
}else{
    ?>
    <div class="res-height">&nbsp;</div>
    
    <!--<table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>

                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">

                            <h4><?php /*echo $this->lang->line('sales_markating_sales_payment_voucher'); */?></h4>-->
                            <!--Payment Voucher-->
                        <!--</td>
                    </tr>
                    <tr>
                        <td>
                            <strong> <?php /*echo $this->lang->line('sales_markating_sales_payment_voucher_number'); */?></strong>
                        </td>--><!--Payment Voucher Number-->
                        <!--<td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['PVcode']; */?></td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php /*echo $this->lang->line('sales_markating_sales_payment_voucher_date'); */?></strong>
                        </td>--><!--Payment Voucher Date-->
                        <!--<td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['PVdate']; */?></td>
                    </tr>
                    <tr>
                        <td><strong><?php /*echo $this->lang->line('common_reference_number'); */?></strong></td>-->
                        <!--Reference Number-->
                       <!-- <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['referenceNo']; */?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>-->
    <h4 class="text-center"><strong> <?php echo $this->lang->line('sales_markating_sales_payment_voucher');?></strong></h4>
    <?php
}
?>

<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;"><strong> <?php echo $this->lang->line('common_name'); ?> </strong></td><!--Name-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:40%;"> <?php echo (empty($extra['master']['supsyscode'])) ? $extra['master']['partyName'] : $extra['master']['partyName']; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_bank'); ?> </strong></td><!--Bank-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"> <?php echo $extra['master']['PVbank'] . ' / ' . $extra['master']['PVbankBranch']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_address'); ?>  </strong></td><!--Address-->
            <td><strong>:</strong></td>
            <td> <?php  echo $extra['master']['partyAddresss']; ?>

               </td>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_bank_account'); ?> </strong></td>
            <!--Bank Account-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVbankAccount'] ?> </td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_telephone'); ?>
                    / <?php echo $this->lang->line('common_fax'); ?> </strong></td><!--Telephone / Fax-->
            <td><strong>:</strong></td>
            <td><?php echo  $extra['master']['parttelfax']; ?>
            </td>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_bank_swift_code'); ?></strong></td>
            <!--Bank Swift Code-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['PVbankSwiftCode']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_currency'); ?> </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php
            if ($extra['master']['paymentType']==1) {
            ?>
            <td><strong><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_number'); ?></strong>
            </td><!--Cheque Number-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVchequeNo']; ?></td>
                <?php
            }
            if ($extra['master']['paymentType']==2) {
                ?>
                <td><strong><strong>Mode of payment</strong>
                </td>
                <td><strong>:</strong></td>
                <td> Bank Transfer </td>
                    <?php
                }
            ?>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong><?php echo $this->lang->line('sales_markating_narration'); ?> </strong></td><!--Narration -->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['PVNarration']);?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['PVNarration']; ?>
            </td>
            <?php
            if ($extra['master']['paymentType']==1) {
                ?>
            <td><strong><?php echo $this->lang->line('sales_markating_sales_payment_cheque_date'); ?></strong></td>
            <!--Cheque Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['PVchequeDate']; ?></td>
            <?php
            }
            ?>
        </tr>
        <?php
        if (!empty($extra['master']['nameOnCheque']) && $extra['master']['paymentType']==1 && ($extra['master']['pvType']=='Supplier' || $extra['master']['pvType']=='SupplierAdvance' || $extra['master']['pvType']=='SupplierInvoice' || $extra['master']['pvType']=='SupplierItem' || $extra['master']['pvType']=='SupplierExpense')) {
            ?>
            <tr>
                <td><strong><?php echo $this->lang->line('common_segment'); ?><!--Segment--></strong></td>
                <td>:</td>
                <td> <?php echo $extra['master']['segDescription']; ?> (<?php echo $extra['master']['segmentCode']; ?>)</td>
                <td><strong><?php echo $this->lang->line('sales_markating_name_on_check'); ?><!--Name on Cheque--></strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['nameOnCheque']; ?></td>
            </tr>
            <?php
        }else{
            ?>
            <tr>
                <td><strong><?php echo $this->lang->line('common_segment'); ?><!--Segment--></strong></td>
                <td>:</td>
                <td> <?php echo $extra['master']['segDescription']; ?> (<?php echo $extra['master']['segmentCode']; ?>)</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php
        }
        ?>

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
                    colspan="<?php echo ($itemBatch_policy == 1 ? '6':'5')?>"><?php echo $this->lang->line('sales_markating_view_invoice_item_details'); ?></th>
                <!--Item Details-->
                <?php if($isGroupByTax ==1) { ?>
                    <th class='theadtr' colspan="3"><?php echo $this->lang->line('common_amount'); ?>
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th><!--Amount-->
                <?php } else {?>
                    <th class='theadtr' colspan="2"><?php echo $this->lang->line('common_amount'); ?>
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th><!--Amount-->
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code'); ?></th>
                <!--Item Code-->
                <th class='theadtr'
                    style="min-width: 45%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description'); ?></th>
                <!--Item Description-->
                <?php if($advanceCostCapturing == 1){ ?>
                    <th class="theadtr" style="min-width: 10%">Activity Code</th>
                <?php } ?>
                <?php if ($itemBatch_policy == 1) { ?>
                    <th class='theadtr' style="min-width: 10%">Batch Number</th>
                <?php }?>
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom'); ?></th>
                <!--UOM-->
                <th class='theadtr'
                    style="min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty'); ?></th>
                <!--Qty-->
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit'); ?></th>
                <!--Unit-->
                <?php if($isGroupByTax ==1) { ?>
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_tax'); ?></th>
                <?php } ?>
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
                        <td>
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
                        <td style="text-align:center;"><?php echo $val['activityCodeName']; ?></td>
                        <?php if($itemBatch_policy==1){?>
                        <td><?php echo $val['batchNumber']; ?></td>
                        <?php }?>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax ==1) { ?>

                            <td style="text-align:right;"><?php
                                if($val['taxAmount'] > 0) {
                                    echo ' <a onclick="open_tax_dd(null,'.$val['payVoucherAutoId'].',\'PV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['payVoucherDetailAutoID'].', \'srp_erp_paymentvoucherdetail\',\'payVoucherDetaulAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                }else {
                                    echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                }?></td>


                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'] + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];

                    if($isGroupByTax ==1) {
                        $item_total += $val['taxAmount'];
                        $grand_total += $val['taxAmount'];
                        $tax_transaction_total += $val['taxAmount'];
                    }
                }
            } else {

                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <?php if($isGroupByTax ==1) { ?>
                    <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('sales_markating_view_invoice_item_total'); ?>(<?php echo $extra['master']['transactionCurrency']; ?>)</td><!--Item Total-->
                <?php } else {?>
                    <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_total'); ?>(<?php echo $extra['master']['transactionCurrency']; ?>)</td><!--Item Total-->
                <?php } ?>
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
    <?php if( $extra['supplierBankMaster']['paymentType'] == 2){ ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th style="text-align:left;" class='theadtr' colspan="7">Supplier bank details</th>
            </tr>
            <tr>
                <th class='theadtr' style="width: 3%">#</th>
                <th class='theadtr' style="width: 30%">Supplier Bank Master ID</th>
                <th class='theadtr' style="width: 27%">Account Name</th>
                <th class='theadtr' style="width: 10%">Account Number</th>
                <th class="theadtr" style="width: 10%">Swift Code</th>
                <th class='theadtr' style="width: 10%">Swift Code</th>
                <th class='theadtr' style="width: 10%">Iban Code</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['supplierBankMaster'])) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['bankName']; ?></td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['bankAddress']; ?></td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['accountName']; ?></td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['accountNumber']; ?></td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['swiftCode']; ?></td>
                        <td style="text-align:center;"><?php echo $extra['supplierBankMaster']['IbanCode']; ?></td>
                    </tr>
                    <?php
                    $num++;
            }else{ ?>
                <tr><td colspan="7" style="text-align:center;">No Record Found</td></tr>
            <?php }  ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <br>
    <?php } ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('common_expense').' '.$this->lang->line('sales_markating_sales_payment_gl_details'); ?></th>
                <!--GL Details-->
                <?php if($isGroupByTax ==1) { ?>
                    <th colspan="4" class='theadtr'><?php echo $this->lang->line('common_expense').' '.$this->lang->line('common_amount'); ?>  </th><!--Amount-->
                <?php } else {?>
                    <th colspan="3" class='theadtr'><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
                <?php } ?>
                <?php if(isset($extra['master']['subInvoiceList']) && $extra['master']['subInvoiceList']){ ?>
                    <th class='theadtr'></th>
                <?php } ?>

            </tr>
            <tr>
                <th class='theadtr' style="width: 3%">#</th>
                <th class='theadtr' style="width: 16%"><?php echo $this->lang->line('common_gl_code'); ?></th>
                <!--GL Code-->
                <th class='theadtr' style="width: 30%"><?php echo $this->lang->line('sales_markating_sales_payment_gl_descriptions'); ?></th>
                <!--GL Code Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment'); ?></th>
                <!--Segment-->
                <?php if($advanceCostCapturing == 1){ ?>
                    <th class="theadtr" style="min-width: 10%">Activity Code</th>
                <?php } ?>
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_transaction'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th><!--Transaction-->
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <?php if($isGroupByTax ==1) { ?>
                    <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_tax'); ?>  </th><!--Tax-->
                <?php } ?>
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                <?php if(isset($extra['master']['subInvoiceList']) && $extra['master']['subInvoiceList']){ ?>
                    <th></th>
                <?php } ?>

            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail'])) {
                foreach ($extra['gl_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <?php if($val['type'] == 'EC') { ?>
                            <td style="text-align:center;"><a href="#" class="drill-down-cursor" onclick="requestPageView_model('EC', <?php echo $val['expenseClaimMasterAutoID']; ?>)"><?php echo $val['expenseClaimCode'] . '</a><br> <strong>GL Code : </strong>' . $val['GLCode']; ?></td>
                        <?php } else {?>
                            <td style="text-align:center;"><?php echo $val['GLCode']; ?></td>
                        <?php } ?>
                        <td><?php echo $val['GLDescription'] . ($val['comment'] ? ' - ' . $val['comment']  : '').' - '. $val['desc']; ?></td>
                        <td style="text-align:center;"><?php echo $val['ecsegmentcode']; ?></td>
                        <?php if($advanceCostCapturing == 1){ ?>
                            <td style="text-align:center;"><?php echo $val['activityCodeName']; ?></td>
                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;">(<?php echo format_number($val['discountPercentage'], 2) ?> %) <?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax ==1) { ?>
                                <td style="text-align:right;"><?php
                                    if($val['taxAmount'] > 0) {
                                        echo ' <a onclick="open_tax_dd(null,'.$val['payVoucherAutoId'].',\'PV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['payVoucherDetailAutoID'].', \'srp_erp_paymentvoucherdetail\',\'payVoucherDetaulAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                    }else {
                                        echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    }?></td>
                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'] + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if(isset($extra['master']['subInvoiceList']) && $extra['master']['subInvoiceList']){ ?>
                            <td>
                                <a onclick="load_sub_invoices('<?php echo $extra['master']['subInvoiceList'] ?>')" style="color:black"><i class="fa fa-cog"></i></a>
                            </td>
                        <?php } ?>
                        
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total        +=$val['companyLocalAmount'];
                    //$party_total        +=$val['partyAmount'];
                    $grand_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                    if($isGroupByTax ==1) {
                        $transaction_total += $val['taxAmount'];
                        $grand_total += $val['taxAmount'];
                        $tax_transaction_total += $val['taxAmount'];
                    }
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <?php if($isGroupByTax ==1) { ?>
                    <td class="text-right sub_total"
                            colspan="7"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td>
                        <!--GL Total-->
                <?php } else {?>
                    <td class="text-right sub_total"
                        colspan="6"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td>
                    <!--GL Total-->
                <?php } ?>
                <td class="text-right total"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($party_total,$extra['master']['partyCurrencyDecimalPlaces']); ?></td> -->
            </tr>
            </tfoot>
        </table>
    </div>
    
<?php } ?>

<?php if (!empty($extra['gl_detail_income'])) {
    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="4"><?php echo $this->lang->line('common_income').' '.$this->lang->line('sales_markating_sales_payment_gl_details'); ?></th>
                <!--GL Details-->
                <?php if($isGroupByTax ==1) { ?>
                    <th colspan="4" class='theadtr'><?php echo $this->lang->line('common_income').' '.$this->lang->line('common_amount'); ?>  </th><!--Amount-->
                <?php } else {?>
                    <th colspan="3" class='theadtr'><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="width: 3%">#</th>
                <th class='theadtr' style="width: 16%"><?php echo $this->lang->line('common_gl_code'); ?></th>
                <!--GL Code-->
                <th class='theadtr' style="width: 30%"><?php echo $this->lang->line('sales_markating_sales_payment_gl_descriptions'); ?></th>
                <!--GL Code Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment'); ?></th>
                <!--Segment-->
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_transaction'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th><!--Transaction-->
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <?php if($isGroupByTax ==1) { ?>
                    <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_tax'); ?>  </th><!--Tax-->
                <?php } ?>
                <th class='theadtr' style="width: 12%"><?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['gl_detail_income'])) {
                $transaction_total = 0;
                foreach ($extra['gl_detail_income'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <?php if($val['type'] == 'EC') { ?>
                            <td style="text-align:center;"><a href="#" class="drill-down-cursor" onclick="requestPageView_model('EC', <?php echo $val['expenseClaimMasterAutoID']; ?>)"><?php echo $val['expenseClaimCode'] . '</a><br> <strong>GL Code : </strong>' . $val['GLCode']; ?></td>
                        <?php } else {?>
                            <td style="text-align:center;"><?php echo $val['GLCode']; ?></td>
                        <?php } ?>
                        <td><?php echo $val['GLDescription'] . ($val['comment'] ? ' - ' . $val['comment']  : '').' - '. $val['desc']; ?></td>
                        <td style="text-align:center;"><?php echo $val['ecsegmentcode']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;">(<?php echo format_number($val['discountPercentage'], 2) ?> %) <?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($isGroupByTax ==1) { ?>
                                <td style="text-align:right;"><?php
                                    if($val['taxAmount'] > 0) {
                                        echo ' <a onclick="open_tax_dd(null,'.$val['payVoucherAutoId'].',\'PV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['payVoucherDetailAutoID'].', \'srp_erp_paymentvoucherdetail\',\'payVoucherDetaulAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                    }else {
                                        echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    }?></td>
                        <?php } ?>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'] + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total += $val['transactionAmount'];
                    //$Local_total        +=$val['companyLocalAmount'];
                    //$party_total        +=$val['partyAmount'];
                    $grand_total += $val['transactionAmount'] * -1;
                    $tax_transaction_total += $val['transactionAmount'];
                    if($isGroupByTax ==1) {
                        $transaction_total += $val['taxAmount'];
                        $grand_total += $val['taxAmount'] * -1;
                        $tax_transaction_total += $val['taxAmount'] * -1;
                    }
                }
            } else {
                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <?php if($isGroupByTax ==1) { ?>
                    <td class="text-right sub_total"
                            colspan="7"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td>
                        <!--GL Total-->
                <?php } else {?>
                    <td class="text-right sub_total"
                        colspan="6"><?php echo $this->lang->line('sales_markating_sales_payment_gl_total'); ?> </td>
                    <!--GL Total-->
                <?php } ?>
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
                        <td style="text-align:center;">
                            <?php
                            if ($extra['master']['pvType'] == 'Supplier' || $extra['master']['pvType'] == 'SupplierAdvance' || $extra['master']['pvType'] == 'SupplierInvoice' || $extra['master']['pvType'] == 'SupplierItem' || $extra['master']['pvType'] == 'SupplierExpense') { ?>
                            <a  onclick="requestPageView_model('PO',<?php echo $val['purchaseOrderID'] ?>)"><?php echo $val['POCode']; ?>

                           <?php }else{
                                    echo $val['POCode'];
                                }
                            ?>
                            </td>
                        <td><?php echo $val['PODescription'] . ($val['comment'] ? ' - ' . $val['comment'] : ''); ?></td>
                        <td style="text-align:center;"><?php echo $val['documentDate']; ?></td>
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
    $transaction_total_cus = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="6"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_details'); ?> </th>
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
                <!--date-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th>
                <!--Supplier Invoice No-->
                <th class='theadtr' style="min-width: 11%">Supplier Invoice No </th>
                <!--Supplier Invoice Date-->
                <th class='theadtr' style="min-width: 11%">Supplier Invoice Date</th>
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
                        <td style="text-align:center;">
                            <?php if ($extra['master']['pvType'] == 'Supplier' || $extra['master']['pvType'] == 'SupplierAdvance' || $extra['master']['pvType'] == 'SupplierInvoice' || $extra['master']['pvType'] == 'SupplierItem' || $extra['master']['pvType'] == 'SupplierExpense'){  ?>
                                <a  onclick="requestPageView_model('BSI',  <?php echo $val['InvoiceAutoID'] ?>)"><?php echo $val['bookingInvCode'] ?></a>
                            <?php }
                            else{
                                echo $val['bookingInvCode'];
                            } ?>
                        </td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;">
                            <?php echo $val['bookingDate']; ?>
                        </td>
                        <td><?php echo $val['supplierInvoiceNo']; ?></td>
                        <td><?php echo $val['invoiceDate']; ?></td>
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
                    colspan="8"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
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

<?php if (!empty($extra['customer_invoice'])) {
    $transaction_total = 0;
    $transaction_total_cus = 0;
    $Local_total = 0;
    $party_total = 0; ?>
    <br>
   
    
    <br><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="6"><?php echo 'Customer '.$this->lang->line('sales_markating_sales_purachase_commission_invoice_details'); ?> </th>
                <!--Invoice Details-->
                <th class='theadtr' colspan="3"><?php echo $this->lang->line('common_amount'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </th><!--Amount-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
                <!--Code-->
                <th class='theadtr text-left'
                    style="min-width: 15%"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_reference'); ?></th>
                <!--date-->
                <th class='theadtr' style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?> </th>
                <!--Supplier Invoice No-->
                <th class='theadtr' style="min-width: 11%">Supplier Invoice No </th>
                <!--Supplier Invoice Date-->
                <th class='theadtr' style="min-width: 11%">Supplier Invoice Date</th>
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
              
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['customer_invoice'])) {
                foreach ($extra['customer_invoice'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;">
                            <?php if ($extra['master']['pvType'] == 'Supplier' || $extra['master']['pvType'] == 'SupplierAdvance' || $extra['master']['pvType'] == 'SupplierInvoice' || $extra['master']['pvType'] == 'SupplierItem' || $extra['master']['pvType'] == 'SupplierExpense'){  ?>
                                <a  onclick="requestPageView_model('BSI',  <?php echo $val['InvoiceAutoID'] ?>)"><?php echo $val['bookingInvCode'] ?></a>
                            <?php }
                            else{
                                echo $val['bookingInvCode'];
                            } ?>
                        </td>
                        <td><?php echo $val['referenceNo']; ?></td>
                        <td style="text-align:center;">
                            <?php echo $val['bookingDate']; ?>
                        </td>
                        <td><?php echo $val['supplierInvoiceNo']; ?></td>
                        <td><?php echo $val['invoiceDate']; ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['Invoice_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $transaction_total_cus += $val['transactionAmount'];
                    $grand_total += $val['transactionAmount'] * -1;
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
                    colspan="8"><?php echo $this->lang->line('sales_markating_sales_purachase_commission_invoice_total'); ?>
                    (<?php echo $extra['master']['transactionCurrency']; ?> )
                </td><!--Invoice Total-->
                <td class="text-right total"><?php echo format_number($transaction_total_cus, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right sub_total">&nbsp;</td> -->
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
    <br>


 <?php  if (!empty($extra['PRQ'])) { ?><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr'
                    colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details'); ?></th>
                <!--Item Details-->
                <th class='theadtr' colspan="2">Amount
                    (<?php echo $extra['master']['transactionCurrency']; ?>)
                </th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?></th>
                <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description'); ?></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom'); ?></th>
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty'); ?></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit'); ?></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            if (!empty($extra['PRQ'])) {
                foreach ($extra['PRQ'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:left;">
                            <?php
                            if ($extra['master']['pvType'] == 'PurchaseRequest') { ?>
                                <a  onclick="requestPageView_model('PRQ',  <?php echo $val['prMasterID'] ?>)"><?php echo $val['purchaseRequestCode']?></a> - <?php echo $val['itemSystemCode']; ?>
                            <?php }else{
                                     echo $val['purchaseRequestCode']; ?> - <?php echo $val['itemSystemCode'];
                            }?>
                        </td>
                        <td>
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
                            <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                    <?php echo $this->lang->line('sales_markating_view_invoice_tax_details'); ?><!--Tax Details--></strong>
                            </td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                            <th class='theadtr'><?php echo $this->lang->line('common_details'); ?> </th><!--Detail-->
                            <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax'); ?></th>
                            <!--Tax-->
                            <th class='theadtr'><?php echo $this->lang->line('common_transaction'); ?>
                                (<?php echo $extra['master']['transactionCurrency']; ?>)
                            </th><!--Transaction-->
                            <!-- <th class='theadtr'>Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th class='theadtr'>Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        //$tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                        //$tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                        $x = 1;
                        $tr_total_amount = 0;
                        $cu_total_amount = 0;
                        $loc_total_amount = 0;
                        foreach ($extra['tax_detail'] as $value) {
                            echo '<tr>';
                            echo '<td>' . $x . '.</td>';
                            echo '<td>' . $value['taxShortCode'] . '</td>';
                            echo '<td>' . $value['taxDescription'] . '</td>';
                            echo '<td class="text-right">' . $value['taxPercentage'] . ' % </td>';
                            echo '<td class="text-right">' . format_number((($value['taxPercentage'] / 100) * $tax_transaction_total), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_customer_total),$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $grand_total += (($value['taxPercentage'] / 100) * $tax_transaction_total);
                            $tr_total_amount += (($value['taxPercentage'] / 100) * $tax_transaction_total);
                            //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                            //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total">
                                <?php echo $this->lang->line('sales_markating_view_invoice_tax_total'); ?><!--Tax Total--> </td>
                            <td class="text-right sub_total"><?php echo format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
        : <?php if($extra['master']['rrvrID'] && $grand_total == 0){
            echo format_number($extra['master']['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
        }else{
            echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']);
        } ?></h5>
</div>

<?php
    $data['documentCode'] = 'PV';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['payVoucherAutoId'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
?>




    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
                <?php if ($ALD_policyValue == 1) { 
                    $created_user_designation = designation_by_empid($extra['master']['createdUserID']);
                    $confirmed_user_designation = designation_by_empid($extra['master']['confirmedByEmpID']);
                    ?>
                        <tr>
                        <td style="width:30%;"><b>
                                <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['createdUserName'] ?? '' . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['createdDateTime'] ?? ''; ?></td>
                    </tr>
                <?php if($extra['master']['confirmedYN']==1){ ?>
                    <tr>
                        <td style="width:30%;"><b>Confirmed By </b></td>
                        <td><strong>: </strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['confirmedByName'] ?? '' . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['confirmedDate'] ?? '';?></td>
                    </tr>
                <?php } ?>
                    <?php if(!empty($approver_details)) {
                        foreach ($approver_details as $val) {
                            echo '<tr>
                                    <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                                    <td><strong>:</strong></td>
                                    <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                                </tr>';
                        }
                    }
                } else {?>
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
                        <td><?php echo $extra['master']['confirmedYNn'];?></td>

                    </tr>
                <?php } ?>
                <?php if($extra['master']['approvedYN']){?>
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
                <?php }
                } ?>

            <?php if ($extra['master']['approvedYN']) { ?>
                <tr>
                    <td style="width:30%;">&nbsp;</td>
                    <td><strong>&nbsp;</strong></td>
                    <td style="width:70%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:30%;">
                        <b><?php echo $this->lang->line('sales_markating_sales_purachase_commission_collected_by'); ?></b>
                    </td><!--Collected By-->
                    <td><strong>:</strong></td>
                    <td style="width:70%;">_____________________</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="res-height">&nbsp;</div>
<?php if ($extra['master']['approvedYN']) { ?>
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
    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/<?php echo $extra['master']['payVoucherAutoId'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + <?php echo $extra['master']['payVoucherAutoId'] ?> +'/PV';
    $("#a_link").attr("href", a_link);
    $(".de_link").attr("href", de_link);

</script>
