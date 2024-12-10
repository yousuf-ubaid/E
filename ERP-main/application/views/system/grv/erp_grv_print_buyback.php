<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                            echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('transaction_common_grv_voucher');?> </h4><!--Goods Received Voucher-->
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('transaction_common_grv_number');?> </strong></td><!--GRV Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('transaction_common_grv_date');?> </strong></td><!--GRV Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['grvDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['grvDocRefNo']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_Location');?> </strong></td><!--Location-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['wareHouseLocation']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <hr>
    <table>
        <tr>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_supplier');?> </strong></td><!--Supplier-->
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['supplier']['supplierName'].' ('.$extra['supplier']['supplierSystemCode'].' ) '; ?></td>
                    </tr>
                    <tr>
                        <td style="width:15%;" class="td"><strong><?php echo $this->lang->line('common_address');?> </strong></td><!--Address-->
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:83%;" class="td"><?php echo $extra['supplier']['supplierAddress1']; ?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_phone');?> </strong></td><!--Phone-->
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['supplier']['supplierTelephone']; ?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_fax');?> </strong></td><!--Fax-->
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['supplier']['supplierFax']; ?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_email');?> </strong></td><!--Email-->
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['supplier']['supplierEmail']; ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width:20%;" class="td"><strong><?php echo $this->lang->line('transaction_common_delivered_date');?> </strong></td><!--Delivered Date-->
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:78%;" class="td"><?php echo $extra['master']['deliveredDate']; ?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
                    </tr>
                    <tr>
                        <td class="td" style="vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
                        <td style="vertical-align: top"><strong>:</strong></td>
                        <td class="td">
                            <table>
                                <tr>
                                    <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['grvNarration']);?></td>
                                </tr>
                            </table>
                            <?php //echo $extra['master']['grvNarration']; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div><br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' colspan="4"><?php echo $this->lang->line('transaction_common_item_details');?></th><!--Item Details-->
            <th class='theadtr' colspan="3"><?php echo $this->lang->line('transaction_common_ordered_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Ordered Item-->
            <th class='theadtr' colspan="6"><?php echo $this->lang->line('transaction_common_recived_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Received Item-->
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('transaction_common_item_description');?> </th><!--Item Description-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->
            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_qty');?></th><!--Qty-->
            <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost');?> </th><!--Unit Cost-->
            <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_net_amount');?> </th><!--Net Amount-->
            <th class='theadtr' style="min-width: 10%">Gross Qty</th>
            <th class='theadtr' style="min-width: 10%">Buckets</th>
            <th class='theadtr' style="min-width: 10%">B weight</th>
            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_qty');?> </th><!--Qty-->
            <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('common_unit_cost');?></th><!--Unit Cost-->
            <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('common_net_amount');?></th><!--Net Amount-->
        </tr>
        </thead>

        <tbody id="grv_table_body">
        <?php

        if ($extra['detail']) {
            $num =1;
            $extra['detail'] = array_group_by($extra['detail'], 'itemAutoID');
            $received_total  = 0;
            $requested_total  = 0;
            $qtytotal  = 0;
            $qtytotal2  = 0;

            foreach ($extra['detail'] as $value) {

                $noofitems = 0;
                $UnitCost  = 0;
                $netamountorder  = 0;
                $grossqty  = 0;
                $buckets  = 0;
                $bucketweight  = 0;
                $qtyrcv  = 0;
                $unitrcv  = 0;
                $netamount  = 0;



                foreach ($value as $val) {


                    ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                        <td style="text-align:center;"><?php echo $val['itemdes']; ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['requestedQty'] * $val['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['grossQty']) ; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['noOfUnits']) ; ?></td>
                        <td style="text-align:right;"><?php echo $val['deduction'] ; ?></td>
                        <td style="text-align:right;"><?php echo $val['receivedQty'] ; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['receivedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="text-align:right;"><?php echo number_format($val['receivedTotalAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>


                    </tr>
                    <?php
                    $num ++;
                    $noofitems  += $val['requestedQty'];
                    $UnitCost  += $val['requestedAmount'];
                    $netamountorder  += ($val['requestedQty'] * $val['requestedAmount']);
                    $grossqty  += $val['grossQty'];
                    $buckets  += $val['noOfUnits'];
                    $bucketweight  += $val['deduction'];
                    $qtyrcv  += $val['receivedQty'];
                    $unitrcv += $val['receivedAmount'];
                    $netamount += $val['receivedTotalAmount'];
                    $requested_total += ($val['requestedQty']*$val['requestedAmount']);
                    $received_total += $val['receivedTotalAmount'];
                    $qtytotal += $val['requestedQty'];
                    $qtytotal2 += $val['receivedQty'];
                }
                ?>
                <tr>
                    <td colspan="2"> </td>
                    <td colspan="2" class="sub_total" style="text-align: right;"><b>Total</b></td>
                  <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $noofitems;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format($UnitCost,$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo number_format($netamountorder,$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $grossqty;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $buckets;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $bucketweight;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $qtyrcv;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $unitrcv;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right;"><b><?php echo $netamount;?></b></td>
                </tr>
                <?php



            }

        }?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="4">Qty Total</td>
            <td class="text-right total"><?php echo format_number($qtytotal,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            <td class="text-right sub_total"><?php echo $this->lang->line('transaction_ordered_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Ordered Item Total-->
            <td class="text-right total"><?php echo format_number($requested_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
   <td colspan="2"></td>
            <td class="text-right sub_total">Qty Total</td>
            <td class="text-right total"><?php echo format_number($qtytotal2,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right sub_total" ><?php echo $this->lang->line('transaction_recived_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Received Item Total-->
            <td class="text-right total"><?php echo format_number($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

        </tr>
        </tfoot>

    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:30%;">
                <?php if($extra['master']['approvedYN']){ ?>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo $this->lang->line('transaction_common_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['approvedDate']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
            <td style="width:70%;">
                <?php
                if (!empty($extra['addon'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('transaction_common_add_on_details');?></strong></td><!--Addons Details-->
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'><?php echo $this->lang->line('transaction_common_add_on_category');?></th><!--Addon Catagory-->
                            <th class='theadtr'><?php echo $this->lang->line('common_supplier');?></th><!--Supplier-->
                            <th class='theadtr'><?php echo $this->lang->line('transaction_common_referenc_no');?> </th><!--Reference No-->
                            <th class='theadtr'><?php echo $this->lang->line('transaction_common_booking_amount');?></th><!--Booking Amount-->
                            <th class='theadtr'><?php echo $this->lang->line('common_amount');?> ( <?php echo $extra['master']['transactionCurrency'];?> )</th><!--Amount-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php $x=1; $total_amount=0;
                        foreach ($extra['addon'] as $value) {
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['addonCatagory'].'</td>';
                            echo '<td>'.$value['supplierName'].'</td>';
                            echo '<td>'.$value['referenceNo'].'</td>';
                            echo '<td class="text-right">'.$value['bookingCurrency'].' : '.format_number($value['bookingCurrencyAmount'],$value['bookingCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right">'.format_number($value['total_amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $total_amount+=$value['total_amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5" class="text-right sub_total"><?php echo $this->lang->line('common_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Total-->
                            <td class="text-right total"><?php echo format_number($total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
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
            $width = "width: 40%";
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
    a_link=  "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/<?php echo $extra['master']['grvAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + <?php echo $extra['master']['grvAutoID'] ?> + '/GRV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>