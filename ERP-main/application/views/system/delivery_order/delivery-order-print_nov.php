<?php echo
fetch_account_review(true,true,$approval && $extra['master']['approvedYN']);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$hideSalesPrice= getPolicyValues('HPD', 'All');
$show_price_delivery_order = getPolicyValues('HPDO', 'All');
if(!isset($hideSalesPrice)) {
    $hideSalesPrice = 0;
}
if ($printHeaderFooterYN == 1 || $printHeaderFooterYN == 2) {
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 75px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name'];?>.</strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php } else { ?>
<div style="height: 90px;"></div>
</br>
<?php } ?>
<hr>
<div class="table-responsive">
    <div style="text-align: center"><h4>Delivery Note</h4></div>

    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="font-weight: bold"><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
                <td style="font-weight: bold"><strong>:</strong></td>
                <td><?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>

                <td style="font-weight: bold"><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
                <td style="font-weight: bold"><strong>:</strong></td>
                <td><?php echo $extra['master']['DODate']; ?></td>
            </tr>
            <tr>
            <tr>
                <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
                    <td style="font-weight: bold"><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td> <?php echo $extra['customer']['customerAddress1']; ?></td>
                <?php } else { ?>
                    <td style="font-weight: bold"><strong> <strong><?php echo $this->lang->line('common_contact_person');?><!--Contact Person--></strong></td>
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td> <?php echo $extra['master']['contactPersonName']; ?></td>
                <?php } ?>
                <td style="font-weight: bold"><strong>DN No.</strong></td><!--DN Number-->
                <td style="font-weight: bold"><strong>:</strong></td>
                <td><?php echo $extra['master']['DOCode']; ?></td>
            </tr>
            <tr>
                <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
                    <td style="font-weight: bold"><strong><strong><?php echo $this->lang->line('common_customer_telephone');?><!-- Customer Telephone--></strong></td>
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td> <?php echo $extra['customer']['customerTelephone']; ?></td>
                <?php } else { ?>
                    <td style="font-weight: bold"><strong><strong><?php echo $this->lang->line('sales_marketing_contact_person_tel');?></strong></td><!--Contact Person Tel-->
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td><?php echo $extra['master']['contactPersonNumber']; ?></td>
                <?php } 
                $view_ref = 0;
                if ($extra['master']['referenceNo'] != null) { ?>
                    <td style="font-weight: bold"><strong>Client PO</strong></td><!--Reference Number-->
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td><?php echo $extra['master']['referenceNo']; ?></td>
                <?php } else if (!empty($extra['contactperson_detail'])){?>
                    <td style="vertical-align: top; font-weight: bold"><strong><strong>Client PO</strong></td><!--Reference Number-->
                    <td style="vertical-align: top; font-weight: bold"><strong>:</strong></td>
                    <td>
                        <table>
                            <?php if (!empty($extra['contactperson_detail'])){
                                foreach($extra['contactperson_detail'] as $val) {
                                    if(!empty($val['referenceNo'])) {
                                        $view_ref = 1;?>
                                        <tr>
                                            <td><?php echo $val['referenceNo']; ?></td>
                                        </tr>
                                    <?php }
                                }
                            } if ($view_ref == 0) { ?>
                                <tr>
                                    <td><?php echo $extra['master']['referenceNo']; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <?php if (empty($extra['customer']['customerSystemCode'])) { ?>
                <td style="font-weight: bold"><strong> <strong><?php echo $this->lang->line('common_contact_person');?><!--Contact Person--></strong></td>
                <td style="font-weight: bold"><strong>:</strong></td>
                <td> <?php echo $extra['master']['contactPersonName']; ?></td>

                <td style="font-weight: bold"><strong><strong><?php echo $this->lang->line('sales_marketing_contact_person_tel');?></strong></td><!--Contact Person Tel-->
                <td style="font-weight: bold"><strong>:</strong></td>
                <td><?php echo $extra['master']['contactPersonNumber']; ?></td>
            <?php } ?>
           
            <?php if(!empty($extra['master']['salesPersonID'])) { ?>
                <tr>
                    <td style="font-weight: bold"><strong><strong><?php echo $this->lang->line('sales_markating_transaction_sales_person');?> </strong></td><!--Sales Person -->
                    <td style="font-weight: bold"><strong>:</strong></td>
                    <td> <?php echo $extra['master']['SalesPersonName']; ?> (<?php echo $extra['master']['SalesPersonCode']; ?>)</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<br>
<?php $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if(!empty($extra['item_detail'])){ ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                <?php if($show_price_delivery_order == 1) { ?>
                    <?php if($hideSalesPrice == 0) { ?>
                        <th class='theadtr' colspan="6"><?php echo $this->lang->line('common_price');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Price-->
                    <?php } ?>
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%">Part No</th>
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                
                <?php if($show_price_delivery_order == 1) { ?>
                    <?php if($hideSalesPrice == 0) { ?>
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_discount');?></th><!--Discount-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_net_unit_price');?></th><!--Net Unit Cost-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_net');?></th><!--Net-->
                    <?php } ?>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;$item_total = 0;
            $is_item_active = 1;
            foreach ($extra['item_detail'] as $val) {

                if($val['contractCode'])
                {
                    $link = '<a  onclick="requestPageView_model(\'' . $val['documentID'] . '\','.$val['contractAutoID'].')">'.$val['contractCode'].'</a>';
                }
                ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php 
                    if(!empty($val['partNo']))
                    {
                        echo $val['partNo'];
                    } else{
                        echo "";
                    }
                    ?></td>
                    <td>
                        <?php if($extra['master']['DOType'] == 'Sales Order'){
                            ?> <?php echo $val['itemDescription'];?> - <?php echo $val['remarks']; ?>
                        <?php }elseif ($extra['master']['DOType'] == 'Contract'){  ?>
                            <?php echo $val['itemDescription'];?> - <?php echo $val['remarks']; ?>
                        <?php }elseif ($extra['master']['DOType'] == 'Quotation'){  ?>
                            <?php echo $val['itemDescription'];?> - <?php echo $val['remarks']; ?>
                        <?php }else{
                            echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription'];
                        } ?>



                       
                    </td>
                    
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                    <?php if($show_price_delivery_order == 1) { ?>
                        <?php if($hideSalesPrice == 0) { ?>
                            <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php if($isGroupByTax == 1) { ?>
                                <td style="text-align:right;"><?php echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php } else { ?>
                                <td style="text-align:right;"><?php echo format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php } ?>
                            <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <?php
                $num ++;
                $gran_total += $val['transactionAmount'];
                $item_total += $val['transactionAmount'];
                $p_total    += $val['transactionAmount'];

                //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
            } ?>
            </tbody>
            <tfoot>
            <tr>
            <?php if($show_price_delivery_order == 1) { ?>
                <?php if($hideSalesPrice == 0) { ?>
                    <td class="text-right sub_total" colspan="11"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?><!--Item Total -->(<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                    <td class="text-right sub_total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <?php } ?>
            <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>
<?php  } ?>
<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['gl_detail'])){ ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="min-width: 45%;text-align: left;"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment');?></th><!--Segment-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Amount-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            foreach ($extra['gl_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td><?php echo $val['description']; ?></td>
                    <td style="text-align:center;"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="3"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <td class="text-right sub_total"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php  
if($hideSalesPrice == 0) { 
    if (!empty($extra['tax'])) { ?>
        <div class="table-responsive">
            <table style="width: 100%">
                <tr>
                    <td style="width:40%;">
                        &nbsp;
                    </td>
                    <td style="width:60%;padding: 0;">
                        <table style="width: 100%" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                <th class='theadtr'> <?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail-->
                                <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                                <th class='theadtr'><?php echo $this->lang->line('common_transaction');?><!--Transaction -->(<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                            $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                echo '<tr>';
                                echo '<td>'.$x.'.</td>';
                                echo '<td>'.$value['taxShortCode'].'</td>';
                                echo '<td>'.$value['taxDescription'].'</td>';
                                echo '<td class="text-right">'.$value['taxPercentage'].' % </td>';
                                echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    <?php } ?>
    <?php if($show_price_delivery_order == 1) { ?>                         
        <div class="table-responsive">
            <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
                : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
        </div>
    <?php } ?>
<?php } ?>
    <br>
    <div class="table-responsive hide" style="display:none;">
        <table style="width: 100%">
            <tbody>
                
            <?php if ($extra['master']['confirmedYN']==1) { ?>
                <tr>
                    <td><b>Confirmed By</b></td>
                    <td style="font-weight: bold;"><strong>:</strong></td>
                    <td><?php echo $extra['master']['confirmedYNn'];?></td>

                </tr>
            <?php } ?>
            <?php if($extra['master']['approvedYN']){?>
                <tr>
                    <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                    <td style="font-weight: bold;"><strong>:</strong></td>
                    <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                </tr>
                <tr>
                    <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                    <td style="font-weight: bold;"><strong>:</strong></td>
                    <td><?php echo $extra['master']['approvedDate']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                    <tr>                        
                        <td style="width:50%;">
                            <table style="width:100%;">
                                <tbody>
                                        <tr>      
                                            <td style="font-size:12px">
                                            <b>Prepared By</b>
                                            </td>
                                        </tr>
                                        <tr><td style="height:10px"></td></tr>
                                        <tr>
                                            <td style="width:35%;font-size:12px"><b>
                                                    Name </b></td>
                                            <td style="width:5%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:60%;font-size:12px"><span>...........................</span></td>
                                        </tr>               
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Phone Number </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span></td>
                                        </tr>     
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Date </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span></td>
                                        </tr>   
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Signature </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span> </td>
                                        </tr>  
                                </tbody>
                            </table> 
                            <table>
                                <tr><td height="50px" style="height:50px">&nbsp;</td></tr>
                            </table>
                            <table>
                                <tr><td height="50px" style="height:50px">Company seal/stamp</td></tr>
                            </table>
                        </td>
                        <td style="width:50%;">
                        <table>
                                <tbody>
                                        <tr>      
                                            <td style="font-size:12px">
                                            <b>Received By</b>
                                            </td>
                                        </tr>
                                        <tr><td style="height:10px"></td></tr>
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                                    Name </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span></td>
                                        </tr>               
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Phone Number </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span></td>
                                        </tr>     
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Date </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span></td>
                                        </tr>   
                                        <tr>
                                            <td style="width:27%;font-size:12px"><b>
                                            Signature </b></td>
                                            <td style="width:3%;font-size:12px"><strong>:</strong></td>
                                            <td style="width:70%;font-size:12px"><span>...........................</span> </td>
                                        </tr>   
                                </tbody>
                            </table> 
                            <table>
                                <tr><td height="50px" style="height:50px">&nbsp;</td></tr>
                            </table>
                            <table>
                                <tr><td height="50px" style="height:50px">Company seal/stamp</td></tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>


<?php if ($extra['master']['note']) { ?>
    <div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('sales_markating_view_invoice_notes');?></h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['note']; ?></td>
        </tr>
        </tbody>
    </table>
<?php } ?>


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
        a_link=  "<?php echo site_url('Delivery_order/load_order_confirmation_view/').$extra['master']['DOAutoID'].'/'. $doc_code ?>";
        de_link="<?php echo site_url('Delivery_order/delivery_order_account_review/').$extra['master']['DOAutoID'].'/'. $doc_code ?>";
        $("#a_link").attr("href",a_link);
        $("#de_link").attr("href",de_link);
    </script>

<?php
