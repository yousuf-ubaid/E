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
            <td style="width:55%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <!--<p><?php /*echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; */?></p>-->
                            <h4><?php echo $this->lang->line('transaction_common_grv_voucher');?> </h4><!--Goods Received Voucher-->
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma;"><strong><?php echo $this->lang->line('transaction_common_grv_number');?> </strong></td><!--GRV Number-->
                        <td><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; font-weight: bold;" ><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma; vertical-align: top; padding-top: -3px;" ><strong><?php echo $this->lang->line('transaction_common_grv_date');?> </strong></td><!--GRV Date-->
                        <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['grvDate']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                        <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['grvDocRefNo']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><strong><?php echo $this->lang->line('common_Location');?> </strong></td><!--Location-->
                        <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['wareHouseLocation']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <hr>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="font-size: 15px;font-family: tahoma;" ><strong><?php echo $this->lang->line('common_supplier');?> </strong></td>
            <td ><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma;"><?php echo $extra['supplier']['supplierName'].' ('.$extra['supplier']['supplierSystemCode'].' ) '; ?></td>

            <td style="font-size: 15px;font-family: tahoma;"><strong><?php echo $this->lang->line('common_address');?></td>
            <td><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma;"><?php echo $extra['supplier']['supplierAddress1']; ?></td>
        </tr>

        <tr>
            <td style="font-size: 15px;font-family: tahoma;vertical-align: top; padding-top: -3px;" ><strong><?php echo $this->lang->line('transaction_common_narration');?></strong></td>
            <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['grvNarration']; ?></td>


            <td style="font-size: 15px;font-family: tahoma;vertical-align: top; padding-top: -3px;" ><strong><?php echo $this->lang->line('transaction_common_delivered_date');?></strong></td>
            <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma;vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['deliveredDate']; ?></td>

        </tr>

        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' colspan="4" style="font-size: 14px;"><?php echo $this->lang->line('transaction_common_item_details');?></th><!--Item Details-->
            <th class='theadtr' colspan="3" style="font-size: 14px;"><?php echo $this->lang->line('transaction_common_recived_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Received Item-->
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 1%">#</th>
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('transaction_common_item_description');?> </th><!--Item Description-->
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('transaction_common_qty');?></th><!--Qty-->
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('common_unit_cost');?> </th><!--Unit Cost-->
            <th class='theadtr' style="min-width: 10%;font-size: 14px;"><?php echo $this->lang->line('common_net_amount');?> </th><!--Net Amount-->
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;$received_total = 0;
        if (!empty($extra['detail'])) {
        for ($i=0; $i < count($extra['detail']); $i++) {
            echo '<tr>';
            echo '<td style="font-size: 11px;">'.($i+1).'</td>';
            echo '<td style="font-size: 11px;">'.$extra['detail'][$i]['itemSystemCode'].'</td>';
            echo '<td style="font-size: 11px;">'.$extra['detail'][$i]['itemDescription'].'</td>';
            echo '<td class="text-center" style="font-size: 12px;">'.$extra['detail'][$i]['unitOfMeasure'].'</td>';
            echo '<td class="text-right" style="font-size: 12px;">'.$extra['detail'][$i]['receivedQty'].'</td>';
            echo '<td class="text-right" style="font-size: 12px;">'.format_number($extra['detail'][$i]['receivedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            echo '<td class="text-right" style="font-size: 12px;">'.format_number(($extra['detail'][$i]['receivedTotalAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            echo '</tr>';
            $received_total += ($extra['detail'][$i]['receivedTotalAmount']);

        }
        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="10" class="text-center"><b>'.$norecfound.'</b></td></tr>';
        }
        ?>
        <!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="6" style="font-size: 12px;"><?php echo $this->lang->line('transaction_recived_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Received Item Total-->
            <td class="text-right total" style="font-size: 12px;"><?php echo format_number($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
  <!--  <table style="width: 100%">
        <tr>
            <td style="width:100%;">-->

            <!--</td>-->
            <td style="width:70%;">
                <?php
                if (!empty($extra['addon'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="6" style="font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('transaction_common_add_on_details');?></strong></td><!--Addons Details-->
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr' style="font-size: 14px;"><?php echo $this->lang->line('transaction_common_add_on_category');?></th><!--Addon Catagory-->
                            <th class='theadtr' style="font-size: 14px;"><?php echo $this->lang->line('common_supplier');?></th><!--Supplier-->
                            <th class='theadtr' style="font-size: 14px;"><?php echo $this->lang->line('transaction_common_referenc_no');?> </th><!--Reference No-->
                            <th class='theadtr' style="font-size: 14px;"><?php echo $this->lang->line('transaction_common_booking_amount');?></th><!--Booking Amount-->
                            <th class='theadtr' style="font-size: 14px;"><?php echo $this->lang->line('common_amount');?> ( <?php echo $extra['master']['transactionCurrency'];?> )</th><!--Amount-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php $x=1; $total_amount=0;
                        foreach ($extra['addon'] as $value) {
                            echo '<tr style="font-size: 12px;">';
                            echo '<td style="font-size: 12px;">'.$x.'.</td>';
                            echo '<td style="font-size: 12px;">'.$value['addonCatagory'].'</td>';
                            echo '<td style="font-size: 12px;">'.$value['supplierName'].'</td>';
                            echo '<td style="font-size: 12px;">'.$value['referenceNo'].'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.$value['bookingCurrency'].' : '.format_number($value['bookingCurrencyAmount'],$value['bookingCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right" style="font-size: 12px;">'.format_number($value['total_amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $total_amount+=$value['total_amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5" class="text-right sub_total" style="font-size: 12px;"><?php echo $this->lang->line('common_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Total-->
                            <td class="text-right total" style="font-size: 12px;"><?php echo format_number($total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<br>

    <table style="width: 100%">
        <tbody>

        <?php if($extra['master']['approvedYN']){ ?>
        <tr>
            <td style="font-size: 14px;font-family: tahoma;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
            <td><strong>:</strong></td>
            <td style="font-size: 14px;font-family: tahoma;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>

            <td style="font-size: 14px;font-family: tahoma; padding-left:110px;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
            <td><strong>:</strong></td>
            <td style="font-size: 14px;font-family: tahoma;"><?php echo $extra['master']['approvedDate']; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

<?php /*if($extra['master']['approvedYN']){ */?><!--
    <?php
/*    if ($signature) { */?>
        <?php
/*        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 40%";
        } else {
            $width = "width: 100%";
        }
        */?>
        <div class="table-responsive">
            <table style="<?php /*echo $width */?>">
                <tbody>
                <tr>
                    <?php
/*                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                        */?>

                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                        </td>

                        <?php
/*                    }
                    */?>
                </tr>


                </tbody>
            </table>
        </div>
    <?php /*} */?>
--><?php /*} */?>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Grv/load_grv_conformation'); ?>/<?php echo $extra['master']['grvAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + <?php echo $extra['master']['grvAutoID'] ?> + '/GRV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>