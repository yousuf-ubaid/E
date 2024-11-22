<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true,true,$approval); ?>
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
                            <h4 ><?php echo $this->lang->line('transaction_common_grv_voucher');?></h4><!--Goods Received Voucher -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table>
        <tr>
            <td style="width:50%;">
                <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tbody>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" class="td"><strong><?php echo $this->lang->line('common_supplier');?> </strong></td><!--Supplier-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" class="td"><?php echo $extra['supplier']['supplierName'].' ('.$extra['supplier']['supplierSystemCode'].' ) '; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%;" class="td"><strong><?php echo $this->lang->line('common_address');?> </strong></td><!--Address-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:83%;" class="td"><?php echo $extra['supplier']['supplierAddress1']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['grvDocRefNo']; ?></td>
                    </tr>
                    <tr>
                        <td class="td" style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
                        <td class="td">
                            <table>
                                <tr>
                                    <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['grvNarration']);?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tbody>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:30%;" class="td"><strong><?php echo $this->lang->line('transaction_common_grv_number');?> </strong></td><!--GRV Number-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:68%;" class="td"><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:30%;" class="td"><strong><?php echo $this->lang->line('transaction_common_grv_date');?> </strong></td><!--GRV Date-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:68%;" class="td"><?php echo $extra['master']['grvDate']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_Location');?> </strong></td><!--Location-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['wareHouseLocation']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:30%;" class="td"><strong><?php echo $this->lang->line('transaction_common_delivered_date');?> </strong></td><!--Delivered Date-->
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px; width:68%;" class="td"><?php echo $extra['master']['deliveredDate']; ?></td>
                    </tr>
                   
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div><br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <thead>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('transaction_common_item_details');?></th><!--Item Details-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="3"><?php echo $this->lang->line('transaction_common_ordered_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Ordered Item-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="6"><?php echo $this->lang->line('transaction_common_recived_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Received Item-->
        </tr>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('transaction_common_item_description');?> </th><!--Item Description-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('transaction_common_qty');?></th><!--Qty-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%"><?php echo $this->lang->line('common_unit_cost');?> </th><!--Unit Cost-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%"><?php echo $this->lang->line('common_net_amount');?> </th><!--Net Amount-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Gross Qty</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Buckets</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">B weight</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('transaction_common_qty');?> </th><!--Qty-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('common_unit_cost');?></th><!--Unit Cost-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%"><?php echo $this->lang->line('common_net_amount');?></th><!--Net Amount-->
        </tr>
        </thead>

        <tbody id="grv_table_body">
        <?php
          $qtytotal  = 0;
          $requested_total  = 0;
          $received_total  = 0;
          $qtytotal2  = 0;
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
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['itemSystemCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['itemdes']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo $val['requestedQty']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['requestedQty'] * $val['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['grossQty']) ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['noOfUnits']) ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo $val['deduction'] ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo $val['receivedQty'] ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['receivedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['receivedTotalAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
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
                    <td colspan="2" class="sub_total" style="text-align: right; font-size: 12px;"><b>Total</b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo $noofitems;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo number_format($UnitCost,$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo number_format($netamountorder,$extra['master']['transactionCurrencyDecimalPlaces']) ;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo $grossqty;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo $buckets;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo $bucketweight;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo $qtyrcv;?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo number_format($unitrcv,$extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                    <td class="sub_total reporttotal" style="text-align: right; font-size: 12px;"><b><?php echo number_format($netamount,$extra['master']['transactionCurrencyDecimalPlaces']);?></b></td>
                </tr>
                <?php
            }
        }?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" style="font-size: 14px;" colspan="4">Qty Total</td>
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo number_format($qtytotal,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo 'Total'; //$this->lang->line('transaction_ordered_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Ordered Item Total-->
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo number_format($requested_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="sub_total" colspan="2"></td>
            <td class="text-right sub_total" style="font-size: 14px;">Qty Total</td>
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo number_format($qtytotal2,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo 'Total'; //$this->lang->line('transaction_recived_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Received Item Total-->
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo number_format($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
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
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center;">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center;">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center;">
                Approved By
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/<?php echo $extra['master']['grvAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + <?php echo $extra['master']['grvAutoID'] ?> + '/GRV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>