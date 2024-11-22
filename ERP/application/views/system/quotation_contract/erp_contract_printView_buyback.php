<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
    ?>
    <div class="table-responsive"  style="margin-bottom: -10px">
        <table style="width: 100%;">
            <tr>
                <td>
                    <table style="font-family:'Times New Roman';">
                        <tr>
                            <td style="text-align: center;">
                                <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                                <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                                <h4 ><?php echo $extra['master']['contractType'];?></h4><!--Sales Invoice -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
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
    <table style="width: 100%; font-family:'Times New Roman';">
        <tbody>
        <tr>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('common_number');?></strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><?php echo $extra['master']['contractCode'].($extra['master']['versionNo'] ? '/V'.$extra['master']['versionNo'] : ''); ?></td>
        </tr>
        <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
            <tr>
                <td style="font-size: 13px;  height: 8px; padding: 1px"><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 13px;  height: 8px; padding: 1px"> <?php echo $extra['customer']['customerAddress1']; ?></td>

                <td style="font-size: 13px;  height: 8px; padding: 1px"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('common_date');?></strong></td>
                <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 13px;  height: 8px; padding: 1px"><?php echo $extra['master']['contractDate']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><?php echo $extra['master']['referenceNo']; ?></td>
        
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('sales_markating_erp_contract_expiry_date');?> </strong></td><!--Expiry Date -->
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"> <?php echo $extra['master']['contractExpDate']; ?></td>
        </tr>

        <tr>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>Total Outstanding</strong></td><!--Narration-->
            <td style="font-size: 13px;  height: 8px; padding: 1px"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px"><?php echo round($extra['outstandingamt']['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces'])  ?></td>

            <td style="font-size: 13px;  height: 8px; padding: 1px; vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
            <td style="font-size: 13px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
            <td style="font-size: 13px;  height: 8px; padding: 1px">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['contractNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['contractNarration']; ?></td>
        </tr>

        </tbody>
    </table>
</div><hr><br><br>
<div class="table-responsive">
    <table class="table table-striped" style="font-family:'Times New Roman';  margin-left:-0.5cm; margin-right:-0.5cm;">
        <thead <?php if($html) { echo "class='thead'"; } ?>>
        <tr>
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 50%" <?php if($html) { echo "class='theadtr'"; } ?> colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?> </th><!--Item Details-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;min-width: 50%" <?php if($html) { echo "class='theadtr'"; } ?> colspan="4">
                <?php echo $this->lang->line('common_price');?><!--Price --><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
        </tr>
        <tr>
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 4%" <?php if($html) { echo "class='theadtr'"; } ?>>#</th>
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_code');?></th><!--Code-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 30%" <?php if($html) { echo "class='theadtr'"; } ?> class="text-left"><?php echo $this->lang->line('common_description');?></th><!--Description-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('sales_marketing_no_of_item');?></th><!--Qty-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_unit');?></th><!--Unit-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 11%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_discount');?></th><!--Discount-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('sales_markating_erp_contract_net_unit_price');?></th><!--Net Unit Price-->
            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%" <?php if($html) { echo "class='theadtr'"; } ?>><?php echo $this->lang->line('common_total');?></th><!--Total-->
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
                    <td style="font-size: 13px;" class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 13px;" class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 13px;"><?php echo $val['itemDescription'] . ' - ' . $val['comment']; ?></td>
                    <td style="font-size: 13px;" class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="font-size: 13px;" class="text-center"><?php echo $val['noOfItems']; ?></td>
                    <td style="font-size: 13px;" class="text-right"><?php echo $val['requestedQty']; ?></td>
                    <td style="font-size: 13px;" class="text-right"><?php echo number_format(($val['unittransactionAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="font-size: 13px;" class="text-right"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '(' . $val['discountPercentage'] . '%)'; ?></td>
                    <td style="font-size: 13px;" class="text-right" style="width: 12%"><?php echo number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="font-size: 13px;" class="text-right"><?php echo number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
            <td style="font-size: 13px;font-weight:normal; min-width: 85%  !important" class="text-right sub_total" colspan="9">
                <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="font-size: 13px;font-weight:normal; min-width: 15% !important"
                class="text-right sub_total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div><br>
<?php if (!empty($extra['tax'])) { ?>
    <div class="table-responsive">
        <table style="width: 100%; margin-left:-0.5cm; margin-right:-0.5cm;">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%" class="table table-striped" style="font-family:'Times New Roman'">
                        <thead>
                        <tr>
                            <td style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;">#</th>
                            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_type');?></th><!--Type-->
                            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail -->
                            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                            <th style="font-size: 13px;font-weight:normal; border-bottom: 1px solid black;"><?php echo $this->lang->line('common_transaction');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Transaction -->
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
                            echo '<td style="font-size: 13px;">'.$x.'.</td>';
                            echo '<td style="font-size: 13px;">'.$value['taxShortCode'].'</td>';
                            echo '<td style="font-size: 13px;">'.$value['taxDescription'].'</td>';
                            echo '<td style="font-size: 13px;" class="text-right">'.$value['taxPercentage'].' % </td>';
                            echo '<td style="font-size: 13px;" class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
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
                            <td style="font-size: 13px;font-weight:normal;" colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total -->
                            <td style="font-size: 13px;font-weight:normal;" class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <!-- <td class="text-right sub_total"><?php //echo format_number($loc_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right sub_total"><?php //echo format_number($cu_total_amount,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>
<div class="table-responsive" style="font-family:'Times New Roman'">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

<?php if ($extra['master']['Note']) { ?>
    <br>
<div class="table-responsive" style="font-family:'Times New Roman'"><br>
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
    <table style="width: 100%; font-family:'Times New Roman'; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
            Approved By
            </td>
        </tr>
        </tbody>
    </table>
</div>

    <script>
        $('.review').removeClass('hide');
        a_link=  "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/<?php echo $extra['master']['contractAutoID'] ?>/<?php echo $extra['master']['contractAutoID'] ?>";
        $("#a_link").attr("href",a_link);
    </script>