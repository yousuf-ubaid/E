<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(true,true,$approval); ?>
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
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <?php if($is_tax_cn == 1) { ?>
                                <h4><?php echo $this->lang->line('accounts_receivable_tax_credit_note');?><!--Tax Credit Note--></h4>
                            <?php } else { ?>
                                <h4><?php echo $this->lang->line('accounts_receivable_ap_credit_note');?><!--Credit Note--></h4>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_receivable_ap_credit_note_number');?><!--Credit Note Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['creditNoteCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_receivable_ap_credit_note_date');?><!--Credit Note Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['creditNoteDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['docRefNo']; ?></td>
                    </tr>
                    <?php if($isGroupByTax == 1) { ?>
                        <tr>
                            <td><strong><?php echo 'VAT IN';?><!--VAT IN--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['companyVatNumber']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:15%;"><strong><?php echo $this->lang->line('common_customer_name');?> <!--Customer Name--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"> <?php echo $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>
                <td style="width:15%;"><strong>Customer VAT No</strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"><?php echo $extra['master']['vatIdNo']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_customer_address');?> <!--Customer Address--> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerAddress1']; ?></td>
                <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('accounts_receivable_common_telephone_fax');?> <!--Telephone / Fax--> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['customer']['customerTelephone'].' / '.$extra['customer']['customerFax']; ?></td>
                <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4">
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comments']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['comments']; ?>
                </td>
            </tr>
       </tbody>
    </table>
</div>
<br>
<?php  $columnHide = $columnHide_gl = 0; ?>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class='theadtr' colspan="5">Invoice Details</th>
                <?php  if($isGroupByTax == 1) { ?>
                    <th class='theadtr otherTax_inv_header' colspan="5"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                <?php } else { ?>
                    <th class='theadtr'> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 8%">Invoice Code</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <?php if($isGroupByTax == 1) { ?>
                    <th class="theadtr" style="min-width: 10%">Tax<br>Applicable<br>Amount</th>
                    <!--<th class="theadtr" style="min-width: 10%">VAT %</th>
                    <th class="theadtr" style="min-width: 10%">VAT<br>Amount</th>-->
                    <th class="theadtr otherTax_inv" style="min-width: 10%">Taxes</th>
                <?php } ?>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <!-- <th class='theadtr' style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 15%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $cus_total = 0;$Local_total = 0;$rporting_total = 0;$grand_total = 0;
            if (!empty($extra['detail'])) {
                    for ($i=0; $i < count($extra['detail']); $i++) {
                        if($extra['detail'][$i]['isFromInvoice']==1){
                        echo '<tr>';
                        echo '<td>'.($i+1).'</td>';
                        echo '<td><a target="_blank" onclick="requestPageView_model(\'CINV\','.$extra['detail'][$i]['invoiceAutoID'].')">'.$extra['detail'][$i]['invoiceSystemCode'].'</td>';
                        echo '<td>'.$extra['detail'][$i]['GLCode'].'</td>';
                        echo '<td>'.$extra['detail'][$i]['GLDescription'].' '.$extra['detail'][$i]['description'].'</td>';
                        echo '<td class="text-center">'.$extra['detail'][$i]['segmentCode'].'</td>';
                        if($isGroupByTax == 1) {
                            echo '<td class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'] - $extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            //echo '<td class="text-right">'.format_number((($extra['detail'][$i]['amount']/($extra['detail'][$i]['transactionAmount'] - $extra['detail'][$i]['taxAmount']))*100), 2).'</td>';
                            //echo '<td class="text-right">'.format_number($extra['detail'][$i]['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right otherTax_inv">'.format_number($extra['detail'][$i]['taxAmount'] - $extra['detail'][$i]['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        }
                        echo '<td class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        //echo '<td class="text-right">'.format_number($extra['detail'][$i]['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                        //echo '<td class="text-right">'.format_number($extra['detail'][$i]['customerAmount'],$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                        echo '</tr>';
                        if(ROUND($extra['detail'][$i]['taxAmount'] - $extra['detail'][$i]['amount'], 2) > 0) {
                            $columnHide = 1;
                        }
                        $cus_total   += ($extra['detail'][$i]['transactionAmount']);
                        $grand_total += $extra['detail'][$i]['transactionAmount'];
                        //$Local_total      += ($extra['detail'][$i]['companyLocalAmount']);
                        //$rporting_total   += ($extra['detail'][$i]['customerAmount']);
                    }
                }

            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="9" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
            }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <?php  if($isGroupByTax == 1) { ?>
                    <td class="text-right sub_total otherTax_inv_footer" colspan="7"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
                <?php } ?>
                <td class="text-right total"><?php echo format_number($cus_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($rporting_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>
        </tfoot>
    </table>
</div>
<br>

<div class="table-responsive">
    <table id="add_new_grv_table_gl" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
            <?php if($isGroupByTax == 1) { ?>
                <th class='theadtr otherTax_gl_header' colspan="5"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
            <?php } else { ?>
                <th class='theadtr'> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
            <?php } ?>
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
            <?php if($isGroupByTax == 1) { ?>
                <th class="theadtr" style="min-width: 10%">Tax<br>Applicable<br>Amount</th>
                <!--<th class="theadtr" style="min-width: 10%">VAT %</th>
                <th class="theadtr" style="min-width: 10%">VAT<br>Amount</th>-->
                <th class="theadtr otherTax_gl" style="min-width: 10%">Taxes</th>
                <!--<th class='theadtr' style="min-width: 35%"><?php /*echo $this->lang->line('common_total');*/?></th>
                <th class='theadtr' style="min-width: 15%"><?php /*echo $this->lang->line('common_tax');*/?></th>-->
            <?php } ?>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
            <!-- <th class='theadtr' style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th class='theadtr' style="min-width: 15%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
        </tr>
        </thead>
        <tbody>
        <?php $cus_total_gl = 0;$Local_total = 0;$rporting_total = 0;
        if (!empty($extra['detail'])) {

            for ($i=0; $i < count($extra['detail']); $i++) {
                if($extra['detail'][$i]['isFromInvoice']==0){
                    echo '<tr>';
                    echo '<td>'.($i+1).'</td>';
                    echo '<td>'.$extra['detail'][$i]['GLCode'].'</td>';
                    echo '<td>'.$extra['detail'][$i]['GLDescription'].' '.$extra['detail'][$i]['description'].'</td>';
                    echo '<td class="text-center">'.$extra['detail'][$i]['segmentCode'].'</td>';
                    /*if($isGroupByTax == 1) {
                        echo '<td class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'] - $extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        if($extra['detail'][$i]['taxAmount'] > 0){
                            echo '<td class="text-right">
                                    <a onclick="open_tax_dd(null,'.$extra['detail'][$i]['creditNoteMasterAutoID'].',\'CN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $extra['detail'][$i]['creditNoteDetailsID'].', \'srp_erp_creditnotedetail\',\'creditNoteDetailsID\',0,1) ">'. number_format($extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>
                                  </td>';
                        }else{
                            echo '<td class="text-right">'.format_number($extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        }
                    }*/
                    if($isGroupByTax == 1) {
                        echo '<td class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'] - $extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        //echo '<td class="text-right">'.format_number(($extra['detail'][$i]['taxpercentageLedger']), 2).'</td>';
                        //echo '<td class="text-right"><a onclick="open_tax_dd(null,'.$extra['detail'][$i]['creditNoteMasterAutoID'].',\'CN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $extra['detail'][$i]['creditNoteDetailsID'].', \'srp_erp_creditnotedetail\',\'creditNoteDetailsID\',0,1) ">'.format_number($extra['detail'][$i]['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a></td>';
                        echo '<td class="text-right otherTax_gl"><a onclick="open_tax_dd(null,'.$extra['detail'][$i]['creditNoteMasterAutoID'].',\'CN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $extra['detail'][$i]['creditNoteDetailsID'].', \'srp_erp_creditnotedetail\',\'creditNoteDetailsID\',0,1) ">'.format_number($extra['detail'][$i]['taxAmount'] - $extra['detail'][$i]['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a></td>';
                    }
                    echo '<td class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    echo '</tr>';
                    if(ROUND($extra['detail'][$i]['taxAmount'] - $extra['detail'][$i]['amount'], 2) > 0) {
                        $columnHide_gl = 1;
                    }
                    $cus_total_gl   += ($extra['detail'][$i]['transactionAmount']);
                    $grand_total += $extra['detail'][$i]['transactionAmount'];
                    //$Local_total      += ($extra['detail'][$i]['companyLocalAmount']);
                    //$rporting_total   += ($extra['detail'][$i]['customerAmount']);
                }
            }

        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <?php if($isGroupByTax == 1) { ?>
                <td class="text-right sub_total otherTax_gl_footer" colspan="6"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
            <?php } else { ?>
                <td class="text-right sub_total" colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
            <?php } ?>
            <td class="text-right total"><?php echo format_number($cus_total_gl,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
    <table>
        <tr>
            <td align="right"><h5><!--Grand Total--><?php echo $this->lang->line('common_grand_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5></td>
        </tr>
    </table>
</div>

<?php
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
                                <td class="theadtr" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tax Details</strong></td><!--Tax Details-->
                            </tr>
                            <tr>
                                <th class="theadtr">#</th>
                                <th class="theadtr">Type</th><!--Type-->
                                <th class="theadtr">Detail</th><!--Detail-->
                                <th class="theadtr">Tax Percentage %</th><!--Tax Percentage-->
                                <th class="theadtr">Transaction(<?php echo $extra['master']['transactionCurrency']; ?>)</th>                            
                            </tr>
                        </thead>

                        <?php 
                            $tax_transaction_total = 0;
                            $tax_Local_total = 0;
                            $tax_customer_total = 0;
                            $gran_total = 0;
                            $disc_nettot = 0;
                            $transactionAmount=0;
                            $taxAmount=0;
                            //var_dump($extra); exit;
                        ?>

                        <tbody>
                            <?php
                            $tax_Local_total += ($tax_transaction_total / $extra['master']['companyLocalExchangeRate']);
                            $tax_customer_total += ($tax_transaction_total / $extra['master']['customerCurrencyExchangeRate']);
                            $x = 1;
                            $tr_total_amount = 0;
                            $cu_total_amount = 0;
                            $loc_total_amount = 0;
                            $t_extraCharge = 0;
                            $tax_total_new=0;
                            foreach ($extra['tax'] as $value) { ?>
                                <tr>
                                        <td style="font-size: 12px;"><?php echo $x; ?></td>
                                        <td style="font-size: 12px;"><?php echo $value['taxShortCode']; ?></td>
                                        <td style="font-size: 12px;"><?php echo $value['taxDescription']; ?></td>
                                        <td class="text-right" style="font-size: 12px; text-align:right;"><?php echo $value['taxPercentage']; ?> % </td>
                                        <td class="text-right" style="font-size: 12px; text-align:right;"><?php echo format_number((($value['taxAmount'])), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                        </tr>
                                <?php
                                $x++;
                                $tax_total_new += (($value['taxAmount']));                               
                                
                            } ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right sub_total" style="font-size: 12px; text-align:right;"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total') ; ?></td><!--Tax Total-->
                                <td class="text-right sub_total" style="font-size: 12px; text-align:right;"><?php echo format_number($tax_total_new, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                        </tfoot>

                    </table>    
                </td>
            </tr>
        </table>            
    </div>            
<?php } ?>


<?php

$data['documentCode'] = 'CN';
$data['transactionCurrency'] = $extra['master']['transactionCurrency'];
$data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
$data['documentID'] = $extra['master']['creditNoteMasterAutoID'];
//echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
?>





<div class="table-responsive">
    <br>
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
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn']; ?></td>
            </tr>
        <?php } ?>
        <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
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
    a_link=  "<?php echo site_url('Receivable/load_cn_conformation'); ?>/<?php echo $extra['master']['creditNoteMasterAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + <?php echo $extra['master']['creditNoteMasterAutoID'] ?> + '/CN';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

    <?php if($columnHide == 0) { ?>
        $('.otherTax_inv').addClass('hide');
        $('.otherTax_inv_header').attr('colspan', 4);
        $('.otherTax_inv_footer').attr('colspan', 8);
    <?php } else {?>
        $('.otherTax_inv').removeClass('hide');
        $('.otherTax_inv_header').attr('colspan', 5);
        $('.otherTax_inv_footer').attr('colspan', 7);
    <?php }
    if($columnHide_gl == 0) { ?>
        $('.otherTax_gl').addClass('hide');
        $('.otherTax_gl_header').attr('colspan', 3);
        $('.otherTax_gl_footer').attr('colspan', 5);
    <?php } else {?>
        $('.otherTax_gl').removeClass('hide');
        $('.otherTax_gl_header').attr('colspan', 5);
        $('.otherTax_gl_footer').attr('colspan', 8);
    <?php } ?>
</script>