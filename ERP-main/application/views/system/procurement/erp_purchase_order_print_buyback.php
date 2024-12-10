<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false,true,$approval); ?>
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
                            <h4 >Purchase Order</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
    <hr>

<div class="table-responsive"><br>
<table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
      
        <tr>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_purchase_order_number');?></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['purchaseOrderCode']; ?></td>

        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_purchase_order_date');?></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
         <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
      
        <tr>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_supplier');?><!--Supplier--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['supplier']['supplierName'] . ' (' . $extra['supplier']['supplierSystemCode'] . ').<br>' . $extra['supplier']['supplierAddress1']; ?></td>

        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_ship_to');?><!--Ship To--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
         <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').<br>' . $extra['master']['shippingAddressDescription']; ?></td>
        </tr>
        <tr>



        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_contact');?><!--Contact--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['supplier']['supplierName']; ?></td>

        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_ship_contact');?><!--Ship Contact--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['shipTocontactPersonID']; ?></td>
        </tr>
        <tr>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_telephone');?><!--Phone--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['supplier']['supplierTelephone']; ?></td>

        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_telephone');?><!--Phone--></strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['shipTocontactPersonTelephone']; ?></td>
        </tr>
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['supplier']['supplierFax']; ?></td>

             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['shipTocontactPersonFaxNo']; ?></td>
        </tr>
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['supplier']['supplierEmail']; ?></td>

             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['shipTocontactPersonEmail']; ?></td>
        </tr>
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>Credit Period</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['creditPeriod']; ?></td>

             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>Driver Name</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['driverName']; ?></td>
        </tr>
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>&nbsp;</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>&nbsp;</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px">&nbsp;</td>

             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>Vehicle No</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['vehicleNo']; ?></td>
        </tr>
        <tr>
        <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['referenceNumber']; ?></td>

        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_expected_date');?><!--Expected Date--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
           
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_narration');?><!--Narration--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['narration']; ?></td>
        </tr>
        <tr>
            
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_segment');?><!--Segment--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['segmentCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
<table class="table table-striped" style="font-family: 'Arial, Sans-Serif, Times, Serif';">
        <thead class='thead'>
        <!-- <tr>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="5">--> <?php //echo $this->lang->line('procurement_approval_item_details');?><!--Item Details--><!--</th>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="4">-->
        <?php //echo $this->lang->line('common_cost');?>  <!--Cost--> <?php //echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?><!--</th>-->
        <!--</tr>-->
        <tr>
            <th style="min-width: 4%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;">#</th>
            <th style="min-width: 10%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 30%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;" class="text-left"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;" ><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th style="min-width: 5%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;" ><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th style="min-width: 10%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;;"><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
            <th style="min-width: 11%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;"><?php echo $this->lang->line('common_discount');?><!--Discount--></th>
            <th style="min-width: 10%; border-bottom: 1px solid black;font-size: 12px;  font-weight:normal;"><?php echo $this->lang->line('common_net_cost');?><!--Net Cost--></th>
            <?php
            if($extra['master']['documentTaxType']==1){
                ?>
                <th style="min-width: 10%; font-weight:normal;">Tax amount</th>
                <?php
            }
            ?>
            <th style="min-width: 15%; border-bottom: 1px solid black; font-weight:normal;" ><?php echo $this->lang->line('common_total');?><!--Total--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $gen_disc_total=0;
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <?php
                    $purchaseRequestCode='';
                    if(!empty($val['purchaseRequestCode'])){
                        $purchaseRequestCode=$val['purchaseRequestCode'].' - ';
                    }
                    ?>
                    <td style="font-size: 14px;;"><?php echo $purchaseRequestCode . $val['itemSystemCode']; ?></td>
                    <td style="font-size: 12px;"><?php echo $val['itemDescription'] ?>
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
                    <td style="font-size: 14px;;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right" style="font-size: 14px;;"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right" style="font-size: 14px;;"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right" style="font-size: 14px;;"><?php echo  number_format($val['discountAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . $val['discountPercentage'] . '%)'; ?></td>
                    <td class="text-right" style="font-size: 14px;;"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php
                    if($extra['master']['documentTaxType']==1){
                        ?>
                        <td class="text-right" style="font-size: 14px;;"><?php echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php
                        $totamnt=$val['totalAmount']+$val['taxAmount'];
                    }else{
                        $totamnt=$val['totalAmount'];
                    }
                    ?>
                    <td class="text-right" style="font-size: 14px;;"><?php echo number_format($totamnt, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total += $totamnt;
                $gran_total += $totamnt;
                $tax_transaction_total += $val['totalAmount'];

            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="9" class="text-center" style="font-size: 14px;">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <?php
            if($extra['master']['documentTaxType']==1){
                ?>
                <td style="min-width: 85%  !important;font-size: 14px; font-weight:bold" class="text-right sub_total" colspan="9">
                    <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php
            }else{
                ?>
                <td style="min-width: 85%  !important;font-size: 14px; font-weight:bold" class="text-right" colspan="8">
                    <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php
            }
            ?>

            <td style="min-width: 15% !important; font-size: 14px;font-weight:bold"
                class="text-right "><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div><br>

<div class="table-responsive">
<table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <tr>
           <td style="width:40%;">
                &nbsp;
           </td>
           <td style="width:60%;padding: 0;">
            <?php
            if ($extra['master']['generalDiscountPercentage']>0) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <td class='theadtr' style="font-size: 12px;font-weight:normal;" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount Details</strong></td>
                            </tr>
                            <tr>
                                <th class='theadtr' style="font-size: 12px;font-weight:normal;">Discount Percentage</th>
                                <th class='theadtr' style="font-size: 12px;font-weight:normal;">Discount Amount (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-right" style="font-size: 14px;"><?php echo $extra['master']['generalDiscountPercentage']; ?> %</td>
                                <td class="text-right" style="font-size: 14px;"><?php echo format_number(($extra['master']['generalDiscountPercentage']/100)*$gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <?php
                                $gen_disc_total=($extra['master']['generalDiscountPercentage']/100)*$gran_total;
                                ?>
                            </tr>
                        </tbody>
                    </table>
            <?php } ?>
           </td>
        </tr>
    </table>
</div>
<br>
<?php
if($extra['master']['documentTaxType']==0) {
    ?>
    <div class="table-responsive">
    <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
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
                                <td style="font-size: 12px;" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tax Details</strong>
                                </td>
                            </tr>
                            <tr>
                                <th style="font-size: 12px;font-weight:normal;" >#</th>
                                <th style="font-size: 12px;font-weight:normal;" >Detail</th>
                                <th style="font-size: 12px;font-weight:normal;" >Amount <span
                                        class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>
                                        )</span></th>
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
                                echo '<td style="font-size: 14px;" >' . $x . '.</td>';
                                echo '<td style="font-size: 14px;" >' . $value['taxDescription'] . '</td>';
                                echo '<td style="font-size: 14px;"  class="text-right">' . format_number($value['amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += $value['amount'];
                                $tr_total_amount += $value['amount'];
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td style="font-size: 14px;"  colspan="2" class="text-right sub_total">Tax Total</td>
                                <td style="font-size: 14px;"  class="text-right sub_total"><?php echo format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif'; padding:5px;">
    <h5 class="text-right" style="font-weight: bold;"> <?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total-$gen_disc_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
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
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_delivery_terms');?><!--Delivery Terms--> </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['deliveryTerms']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_approval_payment_terms');?><!--Payment Terms--> </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['paymentTerms']; ?></td>
        </tr>
        <tr>
            <td style="width:28%;font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('procurement_penalty_terms');?><!--Penalty Terms--></strong></td>
            <td style="width:2%;font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="width:70%;font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['penaltyTerms']; ?></td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Procurement/load_purchase_order_conformation_buyback'); ?>/<?php echo $extra['master']['purchaseOrderID'] ?>";
    $("#a_link").attr("href",a_link);
</script>



