<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$jobNumberMandatory = getPolicyValues('JNP', 'All');
echo fetch_account_review(false,true,$approval); ?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif;">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 >Purchase Request</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div><hr>

<div class="table-responsive">
<table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif;">
        <tbody>

      <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('procurement_approval_purchase_request_number');?><!--Purchase Request Number--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['purchaseRequestCode']; ?></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_name');?><!--Currency--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['requestedByName'];?></td>

        </tr>

        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('procurement_approval_purchase_request_date');?><!--Purchase Request Number--></strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['documentDate']; ?></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_reference_number');?><!--Currency--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['referenceNumber'];?></td>

        </tr>
        
        
        <tr>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('procurement_approval_expected_date');?><!--Expected Date--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('procurement_approval_narration');?><!--Narration--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['narration']);?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
           
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_segment');?><!--Segment--> </strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
             <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['segmentCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table class="table table-striped" style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif;">
        <thead class=''>
        <tr>
            <th style="font-weight:normal; min-width: 50%;border-bottom: 1px solid black;font-size: 12px"  colspan="6"> <?php echo $this->lang->line('procurement_approval_item_details');?><!--Item Details--></th>
            <th style="font-weight:normal; min-width: 50%;border-bottom: 1px solid black;font-size: 12px"  colspan="4">
                <?php echo $this->lang->line('common_cost');?> <!--Cost--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
        </tr>
        <tr>
            <th style="font-weight:normal; min-width: 4%; border-bottom: 1px solid black;font-size: 12px" >#</th>
            <th style="font-weight:normal; min-width: 10%; border-bottom: 1px solid black;font-size: 12px" ><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="font-weight:normal; min-width: 10%; border-bottom: 1px solid black;font-size: 12px" ><?php echo $this->lang->line('procurement_approval_expected_delivery_date');?><!--Expected Delivery Date--></th>
            <th style="font-weight:normal; min-width: 30%; border-bottom: 1px solid black;font-size: 12px" class="text-left"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="font-weight:normal; min-width: 5%; border-bottom: 1px solid black;font-size: 12px" ><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th style="font-weight:normal; min-width: 5%; border-bottom: 1px solid black;font-size: 12px" ><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th style="font-weight:normal; min-width: 10%; border-bottom: 1px solid black;font-size: 12px" ><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
            <th style="font-weight:normal; min-width: 11%; border-bottom: 1px solid black;font-size: 12px"><?php echo $this->lang->line('common_discount');?><!--Discount--></th>
            <th style="font-weight:normal; min-width: 10%; border-bottom: 1px solid black;font-size: 12px"><?php echo $this->lang->line('common_net_cost');?><!--Net Cost--></th>
            <th style="font-weight:normal; min-width: 15%; border-bottom: 1px solid black;font-size: 12px"><?php echo $this->lang->line('common_total');?><!--Total--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td class="text-right" style="font-size: 14px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 14px;"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['expectedDeliveryDate']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['itemDescription'] . ' - ' .$val['Itemdescriptionpartno']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right" style="font-size: 14px;"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right" style="font-size: 14px;"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right" style="font-size: 14px;"><?php echo  number_format($val['discountAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . $val['discountPercentage'] . '%)'; ?></td>
                    <td class="text-right" style="font-size: 14px;"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right" style="font-size: 14px;"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php


                $num++;
                $total += $val['totalAmount'];
                $gran_total += $val['totalAmount'];
                $tax_transaction_total += $val['totalAmount'];
            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="10" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85% !important;font-size: 12px;" class="text-right sub_total" colspan="9">&nbsp;</td>
            <td style="min-width: 15% !important;font-size: 12px;" class="text-right sub_total">&nbsp;</td>
        </tr>
        </tfoot>
    </table>
</div><br>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif'; padding-right: 5px;">
    <h5 class="text-right" style="font-weight: bold;"> <?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

    <br>
    <br>
    <div class="table-responsive">
    <table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center; font-size: 12px">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center; font-size: 12px">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px">
            Approved By
            </td>
        </tr>
        </tbody>
    </table>
</div>
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
    a_link=  "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>/<?php echo $extra['master']['purchaseRequestID'] ?>";
    $("#a_link").attr("href",a_link);
</script>



