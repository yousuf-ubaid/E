<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$segment_arr = fetch_segment();
echo fetch_account_review(false, true, $approval); ?>
<?php if($extra['master']['versionNo']==0){ ?>

<?php } else{ ?>

    <div class="row">
        <div class="col-md-7">
        &nbsp;
        </div>
        <div class="col-md-2 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
        <?php echo form_dropdown('versionID', $version_drop, '' , 'class="form-control select2" onchange="load_version_confirmation_po(this)" id="versionID" '); ?>
        </div>
    </div>

<?php } ?>
<div class="table-responsive">
    <?php
    if ($printHeaderFooterYN == 1) {
        ?>
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="max-height: 100px;max-width:250px"
                                     src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                                <!--<h4><?php /*echo $this->lang->line('common_purchase_order');*/?><!--Purchase Order--> </h4>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
            </tbody>
        </table>

        <?php
    } else {
        ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    <?php } ?>
</div>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td></td>
            <td><strong>
                    <?php echo $this->lang->line('procurement_approval_purchase_order_number'); ?><!--Purchase Order Number--></strong>
            </td>
            <td><strong>:</strong></td>
            <?php if($extra['master']['versionNo']==0){ ?>
                <td><?php echo $extra['master']['purchaseOrderCode']; ?></td>
            <?php }else{ ?>
                <td><?php echo $extra['master']['purchaseOrderCode']. ' (V' . $extra['master']['versionNo'] . ')'; ?></td>
            <?php } ?>
        </tr>
        <tr>
        <td style="width:46%;padding-left: 4%;"><strong style="font-size: 17px;">
                    <?php echo $this->lang->line('common_purchase_order'); ?><!--Purchase Order--></strong></td>
            <td><strong>
                    <?php echo $this->lang->line('procurement_approval_purchase_order_date'); ?><!--Purchase Order Date--></strong>
            </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        

        </tbody>
    </table>
</div>

<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        
        <tr>
            <td style="width:15%;vertical-align: top;"><strong>
            Vendor Name & Address</strong></td>
            <td style="width:2%;vertical-align: top;"><strong>:</strong></td>
            <td style="width:33%;vertical-align: top;"><?php echo $extra['supplier']['supplierName'] . ' (' . $extra['supplier']['supplierSystemCode'] . ').<br>' . $extra['supplier']['supplierAddress1']; ?></td>

            <td style="width:15%;vertical-align: text-top"><strong>
            Purchaser Name & Address</strong></td>
            <td style="width:2%;vertical-align: text-top"><strong>:</strong></td>
            <td style="width:33%;vertical-align: text-top"><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').<br>' . $extra['master']['shippingAddressDescription']; ?></td>
        </tr>
        <tr>
            <td><strong>Sales Person</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonName']; ?></td>

            <td><strong>
                    Buyer</strong>
            </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonID']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_telephone'); ?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonNumber']; ?></td>

            <td><strong><?php echo $this->lang->line('common_telephone'); ?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonTelephone']; ?></td>
        </tr>
        
        <tr>
            <td><strong>Supplier Group</strong></td>
            <td><strong>:</strong></td>
            <td>UAE Vendor - Registered</td>

            <td><strong>Our Ref.No.</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNumber']; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier TRN</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['vatIdNo']; ?></td>

            <td><strong>Company TRN</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['textIdentificationNo']; ?></td>
        </tr>
        
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:15%;"><strong>Delivery Date</strong>
            </td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
            
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_currency'); ?><!--Currency--> </strong>
            </td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

        </tr>
        <tr>

            <td style="width:15%;vertical-align: top"><strong>
                   Shipping Address</strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:33%;">
                <table>
                    <tr>
                        <td><?php echo $extra['master']['shippingAddressDescription']; ?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['narration']; ?>
            </td>
        </tr>
        
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <!-- <tr>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="5">-->
        <?php //echo $this->lang->line('procurement_approval_item_details');?><!--Item Details--><!--</th>-->
        <!--<th style="min-width: 50%" class='theadtr' colspan="4">-->
        <?php //echo $this->lang->line('common_cost');?> <!--Cost-->
        <?php //echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?><!--</th>-->
        <!--</tr>-->
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Part No</th>
            
            <?php if ($extra['master']['purchaseOrderType'] == "PR") { ?>
            <th style="min-width: 30%" class="text-left theadtr">
                Purchase Request<!--Purchase Order Request--></th>
            <?php } ?>

            <th style="min-width: 30%" class="text-left theadtr">
                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>           
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit_price'); ?><!--Unit Price--></th>
            <th style="min-width: 11%" class='theadtr'>
                <?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
            <th style="min-width: 10%" class='theadtr'>
                <?php echo $this->lang->line('common_net_unit_price'); ?><!--Net Unit Price--></th>
            <th style="min-width: 10%" class='theadtr'>
                <?php echo $this->lang->line('common_total'); ?><!--Total Price--></th>
            <?php
            if ($extra['master']['documentTaxType'] == 1) {
                ?>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('procurement_tax_amount'); ?><!-- Tax amount --></th>
                <?php
            }
            ?>
            <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_net_total'); ?><!--Total--></th>

            <?php if($isPrint ==1){ ?>
                
                    <?php if( $extra['master']['approvedYN']==1){ ?>
                        <th style="min-width: 15%" class='theadtr'></th>
                    <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $gen_disc_total = 0;
        $total_commission = 0;
        $vat_on_add_amount = 0;
        $num = 1;
        $totamnt_without_tax = 0;
        $tax_amount = 0;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) {
                $total_commission += $val['commision_value'];

               

                ?>
                <tr>
                    <td style="font-size: 10px;" class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <?php
                    $purchaseRequestCode = '';
                    if (!empty($val['purchaseRequestCode'])) {
                        $purchaseRequestCode = $val['purchaseRequestCode'] . ' - ';
                    }
                    ?>
                    <td class="text-center" style="font-size: 10px;">
                        <?php echo $val['partNo'] ?>
                    </td>

                    <?php if ($extra['master']['purchaseOrderType'] == "PR") { ?>
                    <td style="font-size: 10px;">
                        <a onclick="requestPageView_model('PRQ', <?php echo $val['purchaseRequestID'] ?>)"><?php echo rtrim($purchaseRequestCode, ' -'); ?></a>
                        <?php //echo $val['itemSystemCode']; ?>
                    </td>
                    <?php } ?>

                    <td style="font-size: 10px;"><?php echo $val['itemDescription'] ?>
                        
                    </td>
                    <td class="text-center" style="font-size: 10px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right" style="font-size: 10px;"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right"
                        style="font-size: 10px;"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"
                        style="font-size: 10px;"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . round($val['discountPercentage'], 2) . '%)'; ?></td>
                    <td class="text-right"
                        style="font-size: 12px;"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"
                        style="font-size: 12px;"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php
                    if ($extra['master']['documentTaxType'] == 1) {
                        ?>
                        <td class="text-right"
                            style="font-size: 10px;">
                            <?php if ($isGroupBasedTaxEnable == 1) {
                                if ($val['taxAmount'] > 0) {
                                    // if ($isRcmDocument == 1 && $type!=true) {
                                    //     echo '<a onclick="open_tax_dd(null,'.$val['purchaseOrderID'].',\'PO\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['purchaseOrderDetailsID'].', \'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\',0,1) ">'. number_format(($val['taxAmount'] - $val['taxamountVat']), $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                    // } else {
                                        echo '<a onclick="open_tax_dd(null,'.$val['purchaseOrderID'].',\'PO\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['purchaseOrderDetailsID'].', \'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                    // }


                                } else {
                                    echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);

                                } ?>
                            <?php } else { ?>

                                <?php echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?>

                            <?php } ?>


                        </td>
                        <?php
                        //  if ($isRcmDocument == 1 && $type!=true) {
                        //      $totamnt = (($val['totalAmount'] + $val['taxAmount']) - $val['taxamountVat']);
                        //  }else {
                             $totamnt = $val['totalAmount'] + $val['taxAmount'];
                        //  }

                    } else {
                        $totamnt = $val['totalAmount'];
                    }

                    
                    ?>
                    <td class="text-right"
                        style="font-size: 10px;"><?php echo number_format($totamnt, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php if($isPrint ==1){ ?>
                        <td class="text-right"
                        style="font-size: 10px;">
                            <?php if($extra['master']['approvedYN']==1 ){ ?>

                                <?php if($val['isClosedYN']==0 ){ ?>

                                    &nbsp;&nbsp;<a onclick="close_Document_details_line_wise('PO',<?php echo $val['purchaseOrderID'] ?>,<?php echo $val['purchaseOrderDetailsID'] ?>,'srp_erp_purchaseorderdetails','purchaseOrderDetailsID')" title="Close Document" rel="tooltip"><i title="Close Item" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>

                                <?php }else{ ?>
                                    &nbsp;&nbsp;<a onclick="close_Document_details_view_line_wise('PO',<?php echo $val['purchaseOrderID'] ?>,<?php echo $val['purchaseOrderDetailsID'] ?>,'srp_erp_purchaseorderdetails','purchaseOrderDetailsID',0)" title="View closed details" rel="tooltip"><i title="View closed details" rel="tooltip" class="fa fa-ban" aria-hidden="true"></i></a>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php
                $num++;
                $total += $totamnt;
                $totamnt_without_tax += $val['totalAmount'];
                $gran_total += $totamnt;
                $tax_amount += $val['taxAmount'];
                $tax_transaction_total += $val['totalAmount'] + $val['taxAmount'];

            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="9" class="text-center" style="font-size: 12px;">' . $NoRecordsFound . '<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <?php
            if ($extra['master']['documentTaxType'] == 1) {

                if ($extra['master']['purchaseOrderType'] == "PR") { ?>
                    <td style="min-width: 85%  !important;height:15px;font-size: 12px;" class="text-right sub_total" colspan="12">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php } else { ?>
                    <td style="min-width: 85%  !important;font-size: 12px;height:15px;" class="text-right sub_total" colspan="11">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php } 
               
            } else {
                ?>
                <td style="min-width: 85%  !important;font-size: 12px;height:15px;" class="text-right sub_total" colspan="8">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php
            }
            ?>

            <td style="min-width: 15% !important; height:15px; font-size: 12px;"
                class="text-right"><?php echo number_format($totamnt_without_tax, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
               
        </tr>
        <tr>

            <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                        <?php echo 'VAT on Material'; ?> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="min-width: 15% !important; font-size: 12px;"
                    class="text-right"><?php echo number_format($tax_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

        </tr>

        <?php if($extra['master']['purchaseOrderType'] == 'BQUT') {
            $net_total += $total;
            $net_total += ($total_commission * -1);
            ?>
            <tr>
                <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                        <?php echo 'Less Commission '.$this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <td style="min-width: 15% !important; font-size: 12px;"
                    class="text-right"><?php echo '-'.number_format($total_commission, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>

            <?php foreach($extra['extraCharges'] as $value){
                $net_total += $value['extraCostValue'];
                $vat_on_add_amount += $value['tax_value'];
                ?>
                <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                            <?php echo $value['extraCostName'] ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <td style="min-width: 15% !important; font-size: 12px;"
                        class="text-right "><?php echo number_format($value['extraCostValue'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
            <?php } ?>

            <?php
                $net_total += $vat_on_add_amount;
            ?>

            <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                            <?php echo 'VAT on additional charges'?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <td style="min-width: 15% !important; font-size: 12px;"
                        class="text-right "><?php echo number_format($vat_on_add_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>

            <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                            <?php echo 'Net Amount'?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <td style="min-width: 15% !important; font-size: 12px;"
                        class="text-right "><?php echo number_format($net_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>

        <?php } ?>
        </tfoot>
    </table>
</div><br>

<?php
     $gran_total += $total_commission;
?>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:40%;">
                &nbsp;
            </td>
            <td style="width:60%;padding: 0;">
                <?php
                if ($extra['master']['generalDiscountPercentage'] > 0) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                    <?php echo $this->lang->line('common_discount_details'); ?><!-- Discount Details --></strong>
                            </td>
                        </tr>
                        <tr>
                            <th class='theadtr'>
                                <?php echo $this->lang->line('common_discount_percentagae'); ?><!-- Discount Percentage --></th>
                            <th class='theadtr'>Discount Amount (<?php echo $extra['master']['transactionCurrency']; ?>
                                )
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-right"
                                style="font-size: 12px;"><?php echo number_format($extra['master']['generalDiscountPercentage'], $extra['master']['transactionCurrencyDecimalPlaces']); ?>
                                %
                            </td>
                            <td class="text-right"
                                style="font-size: 12px;"><?php echo format_number(($extra['master']['generalDiscountPercentage'] / 100) * $tax_transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php
                            $gen_disc_total = ($extra['master']['generalDiscountPercentage'] / 100) * $tax_transaction_total;
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
if ($extra['master']['documentTaxType'] == 0) {
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
                                <td class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                        <?php echo $this->lang->line('procurement_approval_tax_details'); ?><!-- Tax Details --></strong>
                                </td>
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'>
                                    <?php echo $this->lang->line('common_details'); ?><!-- Detail --></th>
                                <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?><!-- Amount -->
                                    <span
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

<?php if($extra['master']['purchaseOrderType'] != 'BQUT') { ?>
    <div class="table-responsive">
        <h5 class="text-right"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
            (<?php echo $extra['master']['transactionCurrency']; ?> )
            : <?php echo format_number($gran_total - $gen_disc_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
    </div>
<?php } ?>
<br>
<?php
$data['documentCode'] = 'PO';
$data['transactionCurrency'] = $extra['master']['transactionCurrency'];
$data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
$data['documentID'] = $extra['master']['purchaseOrderID'];
$data['isRcmDocument'] = (($isRcmDocument == 1 && $type!=true)?1:0) ;
//echo $this->load->view('system/tax/tax_detail_view.php', $data, true);
?>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:28%;vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_approval_delivery_terms'); ?><!--Delivery Terms--> </strong>
            </td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['deliveryTerms']); ?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['deliveryTerms']; ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_approval_payment_terms'); ?><!--Payment Terms--> </strong>
            </td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['paymentTerms']); ?></td>
                    </tr>
                </table>
                <?php //echo  $extra['master']['paymentTerms']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:28%;vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_penalty_terms'); ?><!--Penalty Terms--></strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['penaltyTerms']); ?></td>
                    </tr>
                </table>


            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>


<?php if ($extra['master']['termsandconditions']) { ?>
<div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('common_notes'); ?><!-- Notes --></h6>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['termsandconditions']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
    
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Initiated By</td>
                        </tr>
                        <tr>
                            <td>Name: <?php echo $extra['master']['confirmedYNn']; ?></td>
                        </tr>
                    </table>
                </td>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Verified By</td>
                        </tr>
                        <tr>
                            <td>Name: </td>
                        </tr>
                    </table>
                </td>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Approved By</td>
                        </tr>
                        <tr>
                            <td>Name: </td>
                        </tr>
                    </table>
                </td>
                
            </tr>
        </table>
    </div>
    
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
                                <span>____________________________</span><br><br><span><b>&nbsp;<?php echo $this->lang->line('common_authorized_signature'); ?> <!-- Authorized Signature --></b></span>
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
        a_link = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/<?php echo $extra['master']['purchaseOrderID'] ?>";
        $("#a_link").attr("href", a_link);
    </script>



