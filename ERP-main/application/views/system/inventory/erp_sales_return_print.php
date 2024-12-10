<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

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
                        <td><strong><?php echo $this->lang->line('common_customer_name');?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>
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
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_document_code');?></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
            <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_price');?></th><!--Sales Price-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_return_sales_qty');?></th><!--Return Qty-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_tax_total');?></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $total_count = 0;
        $total_tax = 0;
        $total_rebate = 0;
        if (!empty($extra['detail'])) {
            // echo '<pre>';print_r($extra['detail']);
            foreach ($extra['detail'] as $val) { 
                $taxPercentage = $val['taxPer'];
                $discountPercentage = $val['discountPer'];
                $salesPrice = (($val['salesPrice'] / 100) * (100 - $discountPercentage));
                $totalVaule = (($val['totalValue'] / 100) * (100 - $discountPercentage));
                $totalTax = (((($val['totalValue'] / 100) * (100 - $discountPercentage))/100) * ($taxPercentage));?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><a target="_blank" onclick="requestPageView_model('CINV',<?php echo $val['invoiceAutoID']; ?>)"><?php echo $val['mas_code']; ?></a></td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $salesPrice; ?></td>
                    <td style="text-align:right;"><?php echo $val['return_Qty']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <!-- <td style="text-align:right;"><?php echo number_format($totalTax, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
                    <td style="text-align:right;"><?php echo number_format($totalVaule + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total_count += $totalVaule + $val['taxAmount'];
                $total_tax += $totalTax;
                $total_rebate += $val['returnRebateAmount'];
            }
        } else {

            $norecordsfound=$this->lang->line('common_no_records_found');

            echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
        } ?>
        <!--No Records Found-->

        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="8"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?></td><!--Item Total-->
            <td class="text-right total"><?php echo number_format($total_count, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;"> <?php echo $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' ) : ' . format_number($total_count, $extra['master']['transactionCurrencyDecimalPlaces']) ;?></h5>
</div>
<?php 
    if ($total_rebate > 0) { ?>
    <div class="table-responsive"> 
        <h5 class="text-right" style="text-align:right;">Rebate ( <?php echo $extra['master']['transactionCurrency'] . ' ) : ' . format_number($total_rebate, $extra['master']['transactionCurrencyDecimalPlaces']);?></h5>
    </div>
    
    <div class="table-responsive">
    <h5 class="text-right" style="text-align:right;">Net Total (<?php echo $extra['master']['transactionCurrency'] . ' ) : ' . format_number(($total_count - $total_rebate), $extra['master']['transactionCurrencyDecimalPlaces']);?></h5>
</div>
    <?php
    }
?>
<?php 
    if ($total_tax > 0) { ?>
    <div class="table-responsive"> 
        <h5 class="text-right" style="text-align:right;">Tax ( <?php echo $extra['master']['transactionCurrency'] . ' ) : ' . format_number($total_tax, $extra['master']['transactionCurrencyDecimalPlaces']);?></h5>
    </div>
    
    <div class="table-responsive">
    <h5 class="text-right" style="text-align:right;">Net Total (<?php echo $extra['master']['transactionCurrency'] . ' ) : ' . format_number(($total_count + $total_tax), $extra['master']['transactionCurrencyDecimalPlaces']);?></h5>
</div>
    <?php
    }
?>
<br>


<?php

$data['documentCode'] = 'SLR';
$data['transactionCurrency'] = $extra['master']['transactionCurrency'];
$data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
$data['documentID'] = $extra['master']['salesReturnAutoID'];
echo $this->load->view('system/tax/tax_detail_view.php',$data,true);



?>





    <div class="table-responsive">
        <hr>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width:30%;"><b>
                            <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
                </tr>
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
    a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/<?php echo $extra['master']['salesReturnAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/"+ <?php echo $extra['master']['salesReturnAutoID'] ?>+'/SLR';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);

</script>
