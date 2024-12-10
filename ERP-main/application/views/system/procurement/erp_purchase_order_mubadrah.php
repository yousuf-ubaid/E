<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false,true,$approval); ?>
<div class="table-responsive">
    <?php
    if(($printHeaderFooterYN == 1) || ($printHeaderFooterYN == 2)){
        ?>
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
                <td style="width:60%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                <!--<h4><?php /*echo $this->lang->line('common_purchase_order');*/?><!--Purchase Order--> </h4>
                            </td>
                        </tr>

                    </table>
                </td>

            </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
            <tr>
                <td> </td>
                <td><strong><?php echo $this->lang->line('procurement_approval_purchase_order_number');?><!--Purchase Order Number--></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['purchaseOrderCode']; ?></td>
            </tr>
            <tr>
                <td> </td>
                <td><strong><?php echo $this->lang->line('procurement_approval_purchase_order_date');?><!--Purchase Order Date--></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['documentDate']; ?></td>
            </tr>
            <tr>

                <td style="width:46%;padding-left: 4%;"><strong style="font-size: 17px;"><?php echo $this->lang->line('common_purchase_order');?><!--Purchase Order--></strong></td>
                <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['referenceNumber']; ?></td>
            </tr>
            <?php 

                if($isGroupBasedTaxEnable == 1){ ?>

                    <tr>
                        <td></td>
                        <td><strong><?php echo 'Company VAT Number'; ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['supplier']['companyvaNumber']; ?></td>
                    </tr>

            <?php 
                } 
            ?>
            </tbody>
        </table>

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
        <h4 class="text-center"><strong style="font-size: 17px;"><?php echo $this->lang->line('common_purchase_order');?><!--Purchase Order--></strong></h4>
    <?php } ?>
</div>


<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top;"><strong><?php echo $this->lang->line('common_supplier');?><!--Supplier--></strong></td>
            <td style="width:2%;vertical-align: top;"><strong>:</strong></td>
            <td style="width:33%;vertical-align: top;"><?php echo $extra['supplier']['supplierName'] . ' (' . $extra['supplier']['supplierSystemCode'] . ').<br>' . $extra['supplier']['supplierAddress1']; ?></td>

            <td style="width:15%;vertical-align: text-top"><strong><?php echo $this->lang->line('procurement_approval_ship_to');?><!--Ship To--></strong></td>
            <td style="width:2%;vertical-align: text-top"><strong>:</strong></td>
            <td style="width:33%;vertical-align: text-top"><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').<br>' . $extra['master']['shippingAddressDescription']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_contact');?><!--Contact--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierName']; ?></td>

            <td><strong><?php echo $this->lang->line('procurement_approval_ship_contact');?><!--Ship Contact--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonID']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_telephone');?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierTelephone']; ?></td>

            <td><strong><?php echo $this->lang->line('common_telephone');?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonTelephone']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierFax']; ?></td>

            <td><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonFaxNo']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierEmail']; ?></td>

            <td><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonEmail']; ?></td>
        </tr>
        <?php if($isGroupBasedTaxEnable == 1){?>
            <tr>
                <td><strong><?php echo 'Supplier VAT Number'; ?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['supplier']['vatNumber']; ?></td>
            </tr>
        <?php }?>
        <?php if($isRcmDocument == 1){?>
            <tr>
                <td><span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:15%;"><strong><?php echo $this->lang->line('procurement_approval_expected_date');?><!--Expected Date--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

        </tr>
        <tr>
            <td style="width:15%;vertical-align: top"><strong><?php echo $this->lang->line('procurement_approval_narration');?><!--Narration--> </strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:33%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['narration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['narration']; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_segment');?><!--Segment--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['segmentCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <!-- <tr>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="5">--> <?php //echo $this->lang->line('procurement_approval_item_details');?><!--Item Details--><!--</th>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="4">-->
        <?php //echo $this->lang->line('common_cost');?>  <!--Cost--> <?php //echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?><!--</th>-->
        <!--</tr>-->
        <tr>
            <th style="min-width: 5%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit_price');?><!-- Unit Price --></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_discount');?><!--Discount--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_net_unit_price');?><!--Net Unit Price--></th>
            <?php
            if($extra['master']['documentTaxType']==1){
                ?>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('procurement_tax_amount');?><!-- Tax amount --></th>
                <?php
            }
            ?>
            <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_total');?><!--Total--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $gen_disc_total = 0;
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
                    <td class="text-center" style="font-size: 12px;"><?php echo $purchaseRequestCode . $val['itemSystemCode']; ?></td>
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
                    <td class="text-center" style="font-size: 12px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right" style="font-size: 12px;"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right" style="font-size: 12px;"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right" style="font-size: 12px;"><?php echo number_format($val['discountAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . round($val['discountPercentage'] ,2) . '%)'; ?></td>
                    <td class="text-right" style="font-size: 12px;"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php
                    if($extra['master']['documentTaxType']==1){
                        ?>
                        <td class="text-right" style="font-size: 12px;"><?php echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php
                        $totamnt=$val['totalAmount']+$val['taxAmount'];
                    }else{
                        $totamnt=$val['totalAmount'];
                    }
                    ?>
                    <td class="text-right" style="font-size: 12px;"><?php echo number_format($totamnt, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total += $totamnt;
                $gran_total += $totamnt;
                $tax_transaction_total += $val['totalAmount'];

            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="9" class="text-center" style="font-size: 12px;">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <?php
            if($extra['master']['documentTaxType']==1){
                ?>
                <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="9">
                    <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php
            }else{
                ?>
                <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="8">
                    <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php
            }
            ?>

            <td style="min-width: 15% !important; font-size: 12px;"
                class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div><br>

<div class="table-responsive">
    <table style="width: 100%">
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
                            <td class='theadtr' colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('common_discount_details');?><!-- Discount Details --></strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'><?php echo $this->lang->line('common_discount_percentagae');?><!-- Discount Percentage --></th>
                            <th class='theadtr'>Discount Amount (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-right" style="font-size: 12px;"><?php echo number_format($extra['master']['generalDiscountPercentage'], $extra['master']['transactionCurrencyDecimalPlaces']); ?> %</td>
                            <td class="text-right" style="font-size: 12px;"><?php echo format_number(($extra['master']['generalDiscountPercentage']/100)*$tax_transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php
                            $gen_disc_total=($extra['master']['generalDiscountPercentage']/100)*$tax_transaction_total;
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
                                <td class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('procurement_approval_tax_details');?><!-- Tax Details --></strong>
                                </td>
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('common_details');?><!-- Detail --></th>
                                <th class='theadtr'><?php echo $this->lang->line('common_amount');?><!-- Amount --> <span
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
                                echo '<td>' . $x . '.</td>';
                                echo '<td>' . $value['taxDescription'] . '</td>';
                                echo '<td class="text-right">' . format_number($value['amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += $value['amount'];
                                $tr_total_amount += $value['amount'];
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2" class="text-right sub_total">Tax Total</td>
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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

<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total-$gen_disc_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:28%;vertical-align: top"><strong><?php echo $this->lang->line('procurement_approval_delivery_terms');?><!--Delivery Terms--> </strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['deliveryTerms']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['deliveryTerms']; ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top" ><strong><?php echo $this->lang->line('procurement_approval_payment_terms');?><!--Payment Terms--> </strong></td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['paymentTerms']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['paymentTerms']; ?></td>
        </tr>
        <tr>
            <td style="width:28%;vertical-align: top"><strong><?php echo $this->lang->line('procurement_penalty_terms');?><!--Penalty Terms--></strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['penaltyTerms']);?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['penaltyTerms']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<?php if ($extra['master']['termsandconditions']) { ?>
<div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('common_notes');?><!-- Notes --></h6>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['termsandconditions']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
    <br>
    <br>

    <?php

    $data['documentCode'] = 'PO';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['purchaseOrderID'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);



    ?>

    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <?php if ($extra['master']['approvedYN']) { ?>
                <tr>
                    <td><strong><?php echo $this->lang->line('procurement_approval_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['approvedDate']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
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
                                <span>____________________________</span><br><br><span><b>&nbsp;<?php echo $this->lang->line('common_authorized_signature');?> <!-- Authorized Signature --></b></span>
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
        a_link=  "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/<?php echo $extra['master']['purchaseOrderID'] ?>";
        $("#a_link").attr("href",a_link);
    </script>