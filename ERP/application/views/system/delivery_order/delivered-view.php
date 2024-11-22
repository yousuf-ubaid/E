<?php
echo
fetch_account_review(true,false,$approval && $extra['master']['approvedYN']);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

?>
    <div class="table-responsive">
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
                                <br>
                                <h4><?php echo $this->lang->line('sales_marketing_delivery_order');?></h4><!--Delivery Order-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_code');?></strong></td><!--Invoice Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['DOCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['DODate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['referenceNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <hr>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:23%;"><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:75%;"> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>
            </tr>
            <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
                <tr>
                    <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                    <td><strong>:</strong></td>
                    <td> <?php echo $extra['customer']['customerAddress1']; ?></td>
                </tr>
                <tr>
                    <td><strong> Customer Telephone</strong></td>
                    <td><strong>:</strong></td>
                    <td> <?php echo $extra['customer']['customerTelephone']; ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td><strong> Contact Person</strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['contactPersonName']; ?></td>
            </tr>
            <tr>
                <td><strong>Contact Person Tel</strong></td><!--Reference Number-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['contactPersonNumber']; ?></td>
            </tr>
            <?php if(!empty($extra['master']['salesPersonID'])) { ?>
                <tr>
                    <td><strong> Sales Person</strong></td><!--Sales Person -->
                    <td><strong>:</strong></td>
                    <td> <?php echo $extra['master']['SalesPersonName']; ?> (<?php echo $extra['master']['SalesPersonCode']; ?>)</td>
                </tr>
            <?php } ?>

            <tr>
                <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
            </tr>
            <tr>
                <td><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['narration']; ?></td>
            </tr>
            </tbody>
        </table>
    </div><br>
<?php $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if(!empty($extra['item_detail'])){ ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' colspan="6"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                <th class='theadtr' colspan="6"><?php echo $this->lang->line('common_price');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Price-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="min-width: 35%">Warehouse</th><!--Item Description-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_discount');?></th><!--Discount-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_net_unit_price');?></th><!--Net Unit Cost-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_net');?></th><!--Net-->
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;$item_total = 0;
            $is_item_active = 1;
            foreach ($extra['item_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode'] . ' - ' . $val['seconeryItemCode']; ?></td>
                    <td><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription']; ?>

                        <?php if(!empty($val['remarks']) && empty($val['partNo']))
                        {
                            echo ' - ' .  $val['remarks'];
                        }else if(!empty($val['remarks']) && !empty($val['partNo']))
                        {
                            echo ' - ' .  $val['remarks'] . ' - ' .'Part No : ' .$val['partNo'];
                        }
                        else if(!empty($val['partNo']))
                        {
                            echo  ' - ' . 'Part No : ' .$val['partNo'];
                        }
                        ?>

                    </td>
                    <td style="text-align:center;"><?php echo $val['warehouse'] ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['deliveredQty']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['deliveredQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php if($isGroupByTax == 1) { ?>
                        <?php if($val['taxAmount'] > 0) {
                            echo '<td class="text-right">
 
                                   <a onclick="open_tax_dd(null,'.$val['DOAutoID'].',\'DO\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['DODetailsAutoID'].', \'srp_erp_deliveryorderdetails\',\'DODetailsAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>   
                                  </td>';
                        }else {
                            echo '<td style="text-align:right;">'.format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        }?>


                    <?php } else { ?>
                        <td style="text-align:right;"><?php echo format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php } ?>
                    <td style="text-align:right;"><?php echo format_number($val['deliveredTransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total += $val['deliveredTransactionAmount'];
                $item_total += $val['deliveredTransactionAmount'];
                $p_total    += $val['deliveredTransactionAmount'];

                //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                $tax_transaction_total += ($val['deliveredTransactionAmount']-$val['totalAfterTax']);
                // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="11"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?><!--Item Total -->(<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td class="text-right sub_total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php  } ?>
<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['gl_detail'])){  ?>
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
<?php  if (!empty($extra['tax'])) { ?>
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
    <div class="table-responsive">
        <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
            : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
    </div>
    <br>
    <div class="table-responsive">
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
                <?php if ($extra['master']['confirmedYN']==1) { ?>
                    <tr>
                        <td><b>Confirmed By</b></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['confirmedYNn'];?></td>

                    </tr>
                <?php } ?>
                <?php if($extra['master']['approvedYN']){?>
                    <tr>
                        <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['approvedDate']; ?></td>
                    </tr>
                <?php } 
            }?>
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
