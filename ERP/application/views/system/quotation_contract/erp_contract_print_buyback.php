<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


echo fetch_account_review(false,true,$approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
    ?>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <h5 class="text-center"><strong><?php echo $extra['master']['contractType']; ?></strong></h5>
</div>
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
    <h4 class="text-center"><strong><?php echo $extra['master']['contractType']; ?></strong></h4>
    <?php
}
?>

<div class="table-responsive">
    <div style="text-align: center"></div>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

            <td width="20%"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('common_number');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contractCode'].($extra['master']['versionNo'] ? '/V'.$extra['master']['versionNo'] : ''); ?></td>
        </tr>
            <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
            <tr>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerAddress1']; ?></td>

                <td width="20%"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('common_date');?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['contractDate']; ?></td>
            </tr>
            <tr>
                <td><strong> <?php echo $this->lang->line('common_telephone');?>  / <?php echo $this->lang->line('common_fax');?>  </strong></td><!--Telephone/Fax-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerTelephone'].' / '.$extra['customer']['customerFax']; ?></td>

                <td width="20%"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['referenceNo']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td><strong><?php echo $this->lang->line('common_currency');?> </strong><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>

                <td width="20%"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('sales_markating_erp_contract_expiry_date');?> </strong></td><!--Expiry Date -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['contractExpDate']; ?></td>
            </tr>

            <tr>
                <td><strong>Total Outstanding</strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td><?php echo round($extra['outstandingamt']['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces'])  ?></td>

                <td width="20%;vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td>
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['contractNarration']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['contractNarration']; ?></td>
            </tr>

       </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 50%" <?php if($html) { echo "class='theadtr'"; } ?> colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?> </th><!--Item Details-->
            <th style="min-width: 50%" <?php if($html) { echo "class='theadtr'"; } ?> colspan="4">
                <?php echo $this->lang->line('common_price');?><!--Price --><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
        </tr>
        <tr>
            <th style="min-width: 4%" <?php if($html) { echo "class='theadtr'"; } ?>>#</th>
            <th style="min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_code');?></th><!--Code-->
            <th style="min-width: 30%" <?php if($html) { echo "class='theadtr'"; } ?> class="text-left"><?php echo $this->lang->line('common_description');?></th><!--Description-->
            <th style="min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th style="min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('sales_marketing_no_of_item');?></th><!--Qty-->
            <th style="min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
            <th style="min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_unit');?></th><!--Unit-->
            <th style="min-width: 11%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_discount');?></th><!--Discount-->
            <th style="min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('sales_markating_erp_contract_net_unit_price');?></th><!--Net Unit Price-->
            <th style="min-width: 15%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_total');?></th><!--Total-->
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
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription'] . ' - ' . $val['comment']; ?></td>
                    <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-center"><?php echo $val['noOfItems']; ?></td>
                    <td class="text-right"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right"><?php echo number_format(($val['unittransactionAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '(' . $val['discountPercentage'] . '%)'; ?></td>
                    <td class="text-right" style="width: 12%"><?php echo number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"><?php echo number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total += $val['transactionAmount'];
                $gran_total += $val['transactionAmount'];
                $tax_transaction_total += $val['transactionAmount'];
            }
        } else {
            $norecordsfound= $this->lang->line('common_no_records_found');;

            echo '<tr class="danger"><td colspan="10" class="text-center">'.$norecordsfound.'</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9">
                <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="min-width: 15% !important"
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
            if (!empty($extra['tax'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail -->
                                <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                                <th class='theadtr'><?php echo $this->lang->line('common_transaction');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Transaction -->
                                <!-- <th class='theadtr'>Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th class='theadtr'>Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //$tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                            //$tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                echo '<tr>';
                                echo '<td>'.$x.'.</td>';
                                echo '<td>'.$value['taxShortCode'].'</td>';
                                echo '<td>'.$value['taxDescription'].'</td>';
                                echo '<td class="text-right">'.$value['taxPercentage'].' % </td>';
                                echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_customer_total),$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                                //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                                //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total -->
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <!-- <td class="text-right sub_total"><?php //echo format_number($loc_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right sub_total"><?php //echo format_number($cu_total_amount,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
                            </tr>
                        </tfoot>
                    </table>
            <?php } ?>
           </td>
        </tr>
    </table>
</div>
<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
<br>
<?php if ($extra['master']['Note']) { ?>
<div class="table-responsive"><br>
    <h6>Notes</h6>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['Note']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
           <td style="width:57%;">

                    <table style="width: 100%">
                        <tbody>
                        <?php if($extra['master']['confirmedYN']==1){ ?>
                            <tr>
                                <td><b>Confirmed By</b></td>
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['confirmedYNn']?></td>
                            </tr>
                        <?php } ?>
                            <?php if($extra['master']['approvedYN']){ ?>
                            <tr>
                                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?></b></td><!--Electronically Approved By-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                            </tr>
                            <tr>
                                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['approvedDate']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

           </td>
           <td style="width:60%;">
                &nbsp;
           </td>
        </tr>
    </table>
</div>
    <br>
    <br>
    <br>
    <br>
<?php if($extra['master']['approvedYN']){ ?>
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
                        <span>____________________________</span><br><br><span><b>&nbsp;&nbsp; Authorized Signature</b></span>
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
     a_link=  "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/<?php echo $extra['master']['contractAutoID'] ?>/<?php echo $extra['master']['contractAutoID'] ?>";
    $("#a_link").attr("href",a_link);
</script>