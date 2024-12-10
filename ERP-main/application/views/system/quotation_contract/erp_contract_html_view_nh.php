<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


echo fetch_account_review(false,true,$approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <?php
    if($printHeaderFooterYN==1){
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
</div>
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

    <?php
}
?>
<hr>
<div class="table-responsive">
    <div style="text-align: center"><h4><?php echo $extra['master']['contractType']; ?></h4></div>
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
                <td> <?php echo $extra['master']['contactPersonNumber'].' / '.$extra['customer']['customerFax']; ?></td>

                <td width="20%"><strong><?php echo $this->lang->line('common_reference_number');?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['referenceNo']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td><strong><?php echo $this->lang->line('common_currency');?> </strong><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
                <?php if(!empty($extra['master']['segmentcodemaster'])){?>
                <td><strong>Segment</strong><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['segmentcodemaster']; ?></td>
                <?php }?>

            </tr>
            <tr>
                <td><strong><?php echo $extra['master']['contractType']; ?> <?php echo $this->lang->line('sales_markating_erp_contract_expiry_date');?> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['contractExpDate']; ?></td>
                <td><strong><strong>Warehouse</strong><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['wareHouseDescription']; ?></td>



            </tr>
            <tr>
                <td style="vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4">
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['contractNarration']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['contractNarration']; ?>
                </td>
            </tr>
       </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <?php
            if($extra['master']['showImageYN']==1){
            ?>
            <th style="min-width: 50%" class='theadtr' colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?> </th><!--Item Details-->
                <?php
            }else{
            ?>
            <th style="min-width: 50%" class='theadtr' colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?> </th><!--Item Details-->
            <?php
            }
            ?>
            <!-- <th style="min-width: 50%" class='theadtr' colspan="4"><?php echo $this->lang->line('common_price');?><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th> -->
            <?php  if($extra['master']['isGroupBasedTax'] == 1){ ?>
                <th style="min-width: 50%" class='theadtr' colspan="6"><?php echo $this->lang->line('common_price');?><!--Price --><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
            <?php }else{ ?>
                <th style="min-width: 50%" class='theadtr' colspan="4"><?php echo $this->lang->line('common_price');?><!--Price --><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
            <?php  }  ?>
        </tr>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <?php
            if($extra['master']['showImageYN']==1){
            ?>
            <th style="min-width: 7%">Item Image</th>
                <?php
            }
            ?>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_code');?></th><!--Code-->
            <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description');?></th><!--Description-->
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit');?></th><!--Unit-->
            <th style="min-width: 11%" class='theadtr'><?php echo $this->lang->line('common_discount');?></th><!--Discount-->
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('sales_markating_erp_contract_net_unit_price');?></th><!--Net Unit Price-->
            <?php  if($extra['master']['isGroupBasedTax'] == 1){ ?>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                <th style="min-width: 10%" class='theadtr'>Tax Amount<!--Tax Amount--></th>
            <?php } ?>
            <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_total');?></th><!--Total-->
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
                    <?php
                    if($extra['master']['showImageYN']==1){
                        if(!empty($val['itemImage'])){
                            ?>
                            <td class="text-center"><a class="thumbnail_custom"><img style="width:250px;" src="<?php echo $this->s3->createPresignedRequest('uploads/itemMaster/'.$val['itemImage'], '1 hour')  ?>" class="imgThumb img-rounded"/></a></td>
                            <?php
                        }else{
                            ?>
                            <td class="text-center"><a class="thumbnail_custom"><img style="width:250px;" src="<?php echo $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour') ?>" class="imgThumb img-rounded"/></a></td>
                            <?php
                        }

                    }
                    ?>
                    <td class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription'] . ' - ' . $val['comment']; ?></td>
                    <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right"><?php echo $val['requestedQtyformated']; ?></td>
                    <td class="text-right"><?php echo number_format(($val['unittransactionAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '(' . $val['discountPercentage'] . '%)'; ?></td>
                    <td class="text-right"><?php echo number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php  if($extra['master']['isGroupBasedTax'] == 1){ ?>
                        <td class="text-center"><?php echo $val['taxDescription']; ?></td>
                        <td class="text-right"><?php echo ' <a onclick="open_tax_dd(null,'.$val['contractAutoID'].',\'CNT\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['contractDetailsAutoID'].', \'srp_erp_contractdetails\',\'contractDetailsAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>'; ?></td>
                    <?php 
                        $val['transactionAmount'] = $val['transactionAmount'] + $val['taxAmount'];
                    } ?>
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

            if($extra['master']['showImageYN']==1){
                echo '<tr class="danger"><td colspan="19" class="text-center">'.$norecordsfound.'</td></tr>';
            }else{
                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
            }
        } ?>
        <!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
        <?php 
            if($extra['master']['showImageYN']==1){ 
                if($extra['master']['isGroupBasedTax'] == 1){?>
                    <td style="min-width: 85%  !important" class="text-right sub_total" colspan="11"><?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php 
                }else{ ?>
                <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9"><?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <?php 
                } 
            }else{ 
                if($extra['master']['isGroupBasedTax'] == 1){?>
                    <td style="min-width: 85%  !important" class="text-right sub_total" colspan="10"><?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php 
                } else { ?>
                    <td style="min-width: 85%  !important" class="text-right sub_total" colspan="8"><?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php
                }
            } ?>
                <td style="min-width: 15% !important" class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
<!--             <?php
            if($extra['master']['showImageYN']==1){
                ?>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9"><?php echo $this->lang->line('common_total');?> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
        <?php
            }else{
            ?>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="8"><?php echo $this->lang->line('common_total');?><?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php
             }
            ?>
            <td style="min-width: 15% !important"
                class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td> -->
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

<?php
    $data['documentCode'] = 'CNT';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['contractAutoID'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
?>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
           <td style="width:57%;">

                    <table style="width: 100%">
                        <tbody>
                        <?php if ($ALD_policyValue == 1) { 
                            $created_user_designation = designation_by_empid($extra['master']['createdUserID']);
                            $confirmed_user_designation = designation_by_empid($extra['master']['confirmedByEmpID']);
                            ?>
                                <tr>
                                <td style="width:30%;"><b>
                                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                                <td style="width:2%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $extra['master']['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['createdDateTime']; ?></td>
                            </tr>
                        <?php if($extra['master']['confirmedYN']==1){ ?>
                            <tr>
                                <td style="width:30%;"><b>Confirmed By </b></td>
                                <td><strong>: </strong></td>
                                <td style="width:70%;"><?php echo $extra['master']['confirmedByName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['confirmedDate'];?></td>
                            </tr>
                        <?php } ?>
                            <?php if(!empty($approver_details)) {
                                foreach ($approver_details as $val) {
                                    echo '<tr>
                                            <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                                            <td><strong>:</strong></td>
                                            <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                                        </tr>';
                                }
                            }
                        } else {?>
                            <tr>
                                <td style="width:30%;"><b>
                                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                                <td style="width:2%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
                            </tr>
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
                            <?php }
                        } ?>
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
     a_link=  "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>/<?php echo $extra['master']['contractAutoID'] ?>/<?php echo $extra['master']['contractAutoID'] ?>";
    $("#a_link").attr("href",a_link);
</script>