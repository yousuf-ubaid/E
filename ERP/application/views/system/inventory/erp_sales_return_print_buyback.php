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
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong>
                                    <?php echo $this->common_data['company_data']['company_name']; ?>
                                </strong></h3>
                            <h4> <?php echo $this->lang->line('sales_markating_sales_return');?> </h4><!--Sales Return-->
                        </td>
                    </tr>
                    <tr>
                        <td><strong> <?php echo $this->lang->line('sales_markating_sales_return_number');?></strong></td><!--Sales Return Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['salesReturnCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_sales_return_date');?></strong></td><!--Sales Return Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['returnDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNo']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_sales_return_warehouse_location');?></strong></td><!--Warehouse Location-->
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
    <br>
    <table class="table table-bordered table-striped  table-condensed">
        <thead>
       <!-- <tr>
            <th class='theadtr' colspan="4">Item Details</th>
            <th class='theadtr' colspan="2">Qty</th>
            <th class='theadtr'>&nbsp; </th>
        </tr>-->
        <tr>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 5%">#</th>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%">Invoice Code</th>
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%">No of Birds</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%">Gross Weight</th><!--UOM-->

            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%">No of Buckets</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%">Bucket Size</th><!--UOM-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_price');?></th><!--Sales Price-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_qty');?></th><!--Return Qty-->
            <th <?php if ($html) { echo "class='theadtr'"; }?> style="min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
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
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['mas_code']; ?></td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:center;"><?php echo $val['noOfItems']; ?></td>
                    <td style="text-align:center;"><?php echo $val['grossQty']; ?></td>
                    <td style="text-align:center;"><?php echo $val['noOfUnits']; ?></td>
                    <td style="text-align:center;"><?php echo $val['deduction']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($val['salesPrice'] + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo $val['return_Qty']; ?></td>
<!--                    <td style="text-align:right;">--><?php //echo number_format(($val['salesPrice'] + $taxAmount) * $val['return_Qty'], $extra['master']['transactionCurrencyDecimalPlaces']); ?><!--</td>-->
                    <td style="text-align:right;"><?php echo number_format(ROUND(($val['salesPrice'] + $val['taxAmount']),$extra['master']['transactionCurrencyDecimalPlaces']) * $val['return_Qty'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total_count += ROUND(($val['salesPrice'] + $val['taxAmount']),$extra['master']['transactionCurrencyDecimalPlaces']) * $val['return_Qty'] /*$val['totalValue']*/;
            }
        } else {

            $norecordsfound=$this->lang->line('common_no_records_found');

            echo '<tr class="danger"><td colspan="13" class="text-center">'.$norecordsfound.'</td></tr>';
        } ?>
        <!--No Records Found-->

        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="11"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?></td><!--Item Total-->
            <td class="text-right total"><?php echo number_format($total_count, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
    <div class="table-responsive">
        <hr>
        <table style="width: 100%">
            <tbody>
            <?php if($extra['master']['confirmedYN']==1){ ?>
                <tr>
                    <td style="width:30%;"><b>Confirmed By</b></td>
                    <td><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['confirmedYNn'];?></td>
                </tr>
            <?php } ?>
            <?php if ($extra['master']['approvedYN']) { ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?></b></td><!--Electronically Approved Date -->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
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
    a_link = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/<?php echo $extra['master']['salesReturnAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/"+ <?php echo $extra['master']['salesReturnAutoID'] ?>+'/SLR';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);

</script>
