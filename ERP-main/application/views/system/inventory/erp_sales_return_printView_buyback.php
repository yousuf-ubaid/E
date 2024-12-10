<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$html = '';
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:100%;">
                <table style="font-family:'Times New Roman';">
                    <tr>
                        <td style="text-align: center;">
                            <h4 style="font-weight: bold;"><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p style="font-weight: bold;"><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p style="font-weight: bold;"><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 style="font-weight: bold;"><?php echo $this->lang->line('sales_markating_sales_return');?><!--Sales Return--></h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Times New Roman'; ">
        <tbody>
        <tr>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:23%;"><strong><?php echo $this->lang->line('sales_markating_sales_return_number');?> </strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:25%;"><?php echo $extra['master']['salesReturnCode']; ?></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:23%;"><strong><?php echo $this->lang->line('sales_markating_sales_return_date');?></strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:25%;"> <?php echo $extra['master']['returnDate'];  ?></td>
        </tr>
        <tr>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:23%;"><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:25%;"><?php echo $extra['master']['referenceNo']; ?></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:23%;"><strong><?php echo $this->lang->line('sales_markating_sales_return_warehouse_location');?></strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px; width:25%;"> <?php echo $extra['master']['wareHouseLocation'];  ?></td>
        </tr>
        </tbody>
    </table>
</div><hr>
<div class="table-responsive">
    <br>
    <table class="table table-striped" style="font-family:'Times New Roman'; margin-left:-0.5cm; margin-right:-0.5cm;">
        <thead>
       <!-- <tr>
            <th class='theadtr' colspan="4">Item Details</th>
            <th class='theadtr' colspan="2">Qty</th>
            <th class='theadtr'>&nbsp; </th>
        </tr>-->
        <tr>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black; min-width: 5%">#</th>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%">Invoice Code</th>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%">No of Birds</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%">Gross Weight</th><!--UOM-->

            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%">No of Buckets</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%">Bucket Size</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_price');?></th><!--Sales Price-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_qty');?></th><!--Return Qty-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="font-size: 12px; border-bottom: 1px solid black;min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $total_count = 0;
        $taxAmount = 0;
        if (!empty($extra['detail'])) {
            //print_r($extra['detail']);
            foreach ($extra['detail'] as $val) {
                if(!empty($val['invRequestedQty'])){
                    $taxAmount = $val['totalAfterTax'] / $val['invRequestedQty'];
                } ?>
                <tr>
                    <td style="text-align:right;font-size: 13px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['mas_code']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 13px;"><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['noOfItems']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['grossQty']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['noOfUnits']; ?></td>
                    <td style="text-align:center;font-size: 13px;"><?php echo $val['deduction']; ?></td>
                    <td style="text-align:right;font-size: 13px;"><?php echo number_format($val['salesPrice'] + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;font-size: 13px;"><?php echo $val['return_Qty']; ?></td>
<!--                    <td style="text-align:right;">--><?php //echo number_format(($val['salesPrice'] + $taxAmount) * $val['return_Qty'], $extra['master']['transactionCurrencyDecimalPlaces']); ?><!--</td>-->
                    <td style="text-align:right;font-size: 13px;"><?php echo number_format(ROUND(($val['salesPrice'] + $val['taxAmount']),$extra['master']['transactionCurrencyDecimalPlaces']) * $val['return_Qty'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total_count += ROUND(($val['salesPrice'] + $val['taxAmount']),$extra['master']['transactionCurrencyDecimalPlaces']) * $val['return_Qty'] /*$val['totalValue']*/;
            }
        } else {

            $norecordsfound=$this->lang->line('common_no_records_found');

            echo '<tr class="danger"><td style="font-size: 13px;" colspan="13" class="text-center">'.$norecordsfound.'</td></tr>';
        } ?>
        <!--No Records Found-->

        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="11" style="font-size: 13px;"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?></td><!--Item Total-->
            <td class="text-right sub_total" style="font-size: 13px;"><?php echo number_format($total_count, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
    <br>
    <br>
    <div class="table-responsive">
        <table style="width: 100%; font-family:'Times New Roman'; padding: 0px;">
            <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold;">
                    ____________________________
                </td>
                <td style="text-align: center; font-weight: bold;">
                    ____________________________
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; text-align: center; font-weight: bold;">
                    Prepared By
                </td>
                <td style="font-size: 12px; text-align: center; font-weight: bold;">
                    Approved By
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/<?php echo $extra['master']['salesReturnAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/"+ <?php echo $extra['master']['salesReturnAutoID'] ?>+'/SLR';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);

</script>
