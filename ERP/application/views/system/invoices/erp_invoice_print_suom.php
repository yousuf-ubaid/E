<?php echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']);

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$policyPIE = getPolicyValues('PIE', 'All');
?>
<div class="table-responsive">
    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
    ?>
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
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>

                        </td>
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
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <h4 class="text-center"><strong><?php
    if($policyPIE && $policyPIE == 1 && $extra['master']['approvedYN'] != 1) {
        echo 'Preliminary Invoice';
    } else {
        echo $this->lang->line('sales_markating_view_invoice_sales_invoice');
    }
    ?></strong></h4><!--Sales Invoice -->
    <?php
}
?>

<div class="table-responsive">

    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

            <td><strong><?php echo $this->lang->line('common_invoice_number');?></strong></td><!--Invoice Number-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['invoiceCode']; ?></td>
        </tr>
        <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
            <tr>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerAddress1']; ?></td>

                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['invoiceDate']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong> Customer Telephone</strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['customer']['customerTelephone']; ?></td>

            <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNo']; ?></td>
        </tr>

        <tr>
            <td><strong> Contact Person</strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['contactPersonName']; ?></td>

            <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
        </tr>

        <tr>

            <td><strong>Contact Person Tel</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonNumber']; ?></td>

            <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_invoice_date');?></strong></td><!--Invoice Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['customerInvoiceDate']; ?></td>
        </tr>

        <tr>
            <?php if (!empty($extra['master']['salesPersonID'])) { ?>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_sales_person');?></strong></td><!--Sales Person -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['SalesPersonName']; ?> (<?php echo $extra['master']['SalesPersonCode']; ?>)</td>
            <?php }else { ?>
                <td><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['invoiceNarration']; ?></td>
            <?php } ?>
            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_invoice_due_date');?></strong></td><!--Invoice Due Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['invoiceDueDate']; ?></td>


        </tr>

        <tr>
            <?php if (!empty($extra['master']['salesPersonID'])) { ?>

                <td><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['invoiceNarration']; ?></td>
            <?php }else { ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            <?php } ?>

        </tr>

        </tbody>
    </table>
</div><br>
<?php $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if(!empty($extra['item_detail'])){ ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_discount');?></th><!--Discount-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_net_unit_price');?></th><!--Net Unit Cost-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_net');?></th><!--Net-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;$item_total = 0;
            $is_item_active = 1;
            foreach ($extra['item_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;font-size: 12px;"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 12px;"><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription']; ?> -  <?php echo $val['remarks']; ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo $val['unitOfMeasure']; ?> &nbsp; <?php echo $val['requestedQty']; ?> <br> <?php echo $val['secuom']; ?> &nbsp; <?php echo $val['SUOMQty']; ?> </td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total += $val['transactionAmount'];
                $item_total += $val['transactionAmount'];
                $p_total    += $val['transactionAmount'];

                //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="9"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?><!--Item Total -->(<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php  } ?>
<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; $disc_nettot=0; if(!empty($extra['gl_detail'])){  ?>
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
                    <td style="text-align:right;font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;"><?php echo $val['description']; ?></td>
                    <td style="text-align:center;font-size: 12px;"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;font-size: 12px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right sub_total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total"><?php //echo format_number($party_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>


<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['delivery_order'])){  ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th colspan="4" class='theadtr'><?php echo $this->lang->line('sales_marketing_delivery_order_based');?></th>
                <th colspan="4" class='theadtr'>
                    <?php echo $this->lang->line('common_amount');?>
                    <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?> )</span>
                </th>
            </tr>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%;text-align: left;"><?php echo $this->lang->line('common_code');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_date');?></th>
                <th class='theadtr' style="width: 10%"><?php echo $this->lang->line('common_reference_no');?></th>
                <th class='theadtr' style="width: 10%"><?php echo $this->lang->line('common_order_total');?></th>
                <th class='theadtr' style="width: 10%"><?php echo $this->lang->line('common_due');?></th>
                <th class='theadtr' style="width: 10%"><?php echo $this->lang->line('common_balance');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
            foreach ($extra['delivery_order'] as $val) { ?>
                <tr>
                    <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;"><?php echo $val['DOCode']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['DODate']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['referenceNo']; ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['do_tr_amount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['due_amount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['transactionAmount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['balance_amount'], $dPlace); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];

            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="7"> <?php echo $this->lang->line('common_total');?> </td>
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($transaction_total, $dPlace); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>

<?php  if (!empty($extra['discount'])) { ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
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
                        foreach ($extra['discount'] as $value) {
                            $disc_total=0;
                            $disc_total= ($gran_total*$value['discountPercentage'])/100;
                            echo '<tr>';
                            echo '<td style="font-size: 12px;">'.$x.'.</td>';
                            echo '<td style="font-size: 12px;">'.$value['discountDescription'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.format_number($value['discountPercentage'],2).'%</td>';
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
                            <td colspan="3" class="text-right sub_total" style="font-size: 12px;">Total</td>
                            <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php } else{?>

    <?php
    }
?>
<?php $t_extraCharge=0;  if (!empty($extra['extracharge'])) { ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width:50%;padding: 0;">
                                <table style="width: 100%" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <td class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                                    </tr>
                                    <tr>
                                        <th class='theadtr' >#</th>
                                        <th class='theadtr' >Description</th>
                                        <th class='theadtr' >Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
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
                                        echo '<td style="font-size: 12px;">'.$x.'.</td>';
                                        echo '<td style="font-size: 12px;">'.$value['extraChargeDescription'].'</td>';
                                        echo '<td class="text-right" style="font-size: 12px;">'.format_number($extra_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                        echo '</tr>';
                                        $x++;
                                        $extra_nettot += $extra_total;
                                        if($value['isTaxApplicable']==1){
                                            $t_extraCharge += $extra_total;
                                        }
                                    }
                                    $gran_total=$gran_total+$extra_nettot;
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right sub_total" style="font-size: 12px;">Total</td>
                                        <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
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
                            <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                            <th class='theadtr'> <?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail-->
                            <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                            <th class='theadtr'><?php echo $this->lang->line('common_transaction');?><!--Transaction -->(<?php echo $extra['master']['transactionCurrency']; ?>) </th>
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
                            echo '<td style="font-size: 12px;">'.$x.'.</td>';
                            echo '<td style="font-size: 12px;">'.$value['taxShortCode'].'</td>';
                            echo '<td style="font-size: 12px;">'.$value['taxDescription'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.$value['taxPercentage'].' % </td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.format_number((($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge)),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_customer_total),$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $gran_total += (($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
                            $tr_total_amount+=(($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
                            //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                            //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total" style="font-size: 12px;"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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

<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<?php if ($extra['master']['bankGLAutoID']) {
    $a=$this->load->library('NumberToWords');
    $numberinword= $this->numbertowords->convert_number($gran_total);
    $point=format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']);
    $str_arr = explode('.',$point);
    $str1='';
    if($str_arr[1]>0){
        if($extra['master']['transactionCurrency']=="OMR"){
            $str1=' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=' and '.$str_arr[1].' / 100 Only';
        }
    }

    ?>
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
            <tr>
                <td><strong>Amount in words</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td><?php echo $numberinword.$str1; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<br>
<?php if($extra['master']['approvedYN']){ ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
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
            </tbody>
        </table>
    </div>
<?php } ?>
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
    <?php if ($extra['master']['isPrintDN']==1 && $html!=1 && $is_item_active==1) { ?>
        <pagebreak />

    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
        ?>
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
                                    <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                                    <h4><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note');?></h4><!--Delivery note-->
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_number');?></strong></td><!--DN Number-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['deliveryNoteSystemCode']; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_date');?></strong></td><!--DN Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['invoiceDate']; ?></td>
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

    <?php   } else{
        ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <h4 class="text-center"><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note');?></strong></h4><!--Delivery Note -->
        <?php
    }
        ?>


        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="width:23%;"><strong><?php echo $this->lang->line('common_customer_name');?> </strong></td><!--Customer Name-->
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:75%;"> <?php echo (empty($extra['master']['customerSystemCode'])) ? $extra['master']['customerName'] : $extra['master']['customerName'].' ( '.$extra['master']['customerSystemCode'].' )'; ?></td>
                </tr>
                <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['master']['customerAddress']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_telephone');?>/<?php echo $this->lang->line('common_fax');?></strong></td><!--Telephone / Fax -->
                        <td><strong>:</strong></td>
                        <td> <?php echo $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                    <td><strong>:</strong></td>
                    <td colspan="4"> <?php echo $extra['master']['invoiceNarration']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_date');?></strong></td><!--Delivery Date-->
                    <td><strong>:</strong></td>
                    <td colspan="4"> <?php echo $extra['master']['invoiceDueDate']; ?></td>
                </tr>
                </tbody>
            </table>
        </div><br>
        <?php $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0; if(!empty($extra['item_detail'])){ ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class='theadtr' colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                    </tr>
                    <tr>
                        <th class='theadtr' style="min-width: 5%">#</th>
                        <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                        <th class='theadtr' style="min-width: 65%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom');?></th><!--UOM-->
                        <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty');?></th><!--Qty-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $norecordfound =    $this->lang->line('common_no_records_found');
                    $num =1;$item_total = 0;
                    if (!empty($extra['item_detail'])) {
                    foreach ($extra['item_detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                        <td><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription'].' - '.$val['remarks']; ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                    </tr>
                    <?php
                    $num ++;
                    }
                    }else{
                        echo '<tr class="danger"><td colspan="5" class="text-center">'.$norecordfound.'</td></tr>';
                    } ?><!--No Records Found-->
                    </tbody>
                </table>
            </div>

            <?php if($extra['master']['approvedYN']){ ?>
                <div class="table-responsive"><br>
                    <table style="width: 100%">
                        <tbody>
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
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        <?php } } ?>
        <?php if($policyPIE && $policyPIE == 1 && !empty($extra['approvallevels'])){ ?>
            <div class="table-responsive"><br>
                <table style="width: 60%">
                    <tbody>
            <?php foreach ($extra['approvallevels'] as $val) { ?>
                        <tr>
                            <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . ' (' . $this->lang->line('common_level') . '  ' . $val['approvalLevelID']; ?>)</b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td><?php echo $val['Ename2']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date') . ' (' . $this->lang->line('common_level') . '  ' . $val['approvalLevelID']; ?>)</b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td><?php echo $val['ApprovedDate']; ?></td>
                        </tr>
            <?php } ?>
            
                    </tbody>
                </table>
            </div>
        <?php } ?>
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
        a_link=  "<?php echo site_url('invoices/load_invoices_conformation'); ?>/<?php echo $extra['master']['invoiceAutoID'] ?>";
        de_link="<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + <?php echo $extra['master']['invoiceAutoID'] ?> + '/CINV';
        $("#a_link").attr("href",a_link);
        $("#de_link").attr("href",de_link);
    </script>