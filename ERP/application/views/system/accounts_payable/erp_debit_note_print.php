<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval);

$advanceCostCapturing = getPolicyValues('ACC', 'All');
?>
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
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('accounts_payable_debit_note');?><!--Debit Note--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_payable_debit_note_number');?><!--Debit Note Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['debitNoteCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('accounts_debit_note_date');?><!--Debit Note Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['debitNoteDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['docRefNo']; ?></td>
                    </tr>
                    <?php if($isTaxGroupPolicyEnable == 1) { ?>
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
            <td style="width:15%;"><strong> <?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"> <?php echo $extra['supplier']['supplierName'] . ' ( ' . $extra['supplier']['supplierSystemCode'] . ' )'; ?></td>
            <td style="width:15%;"><strong>&nbsp; </strong></td>
            <td style="width:2%;"><strong>&nbsp;</strong></td>
            <td style="width:33%;">&nbsp;</td>
        </tr>
        <tr>
            <td><strong> <?php echo $this->lang->line('accounts_payable_supplier_address');?><!--Supplier Address--> </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['supplier']['supplierAddress1']; ?></td>
            <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
        </tr>
        <tr>
            <td><strong> <?php echo $this->lang->line('common_telephone');?><!--Telephone--> / <?php echo $this->lang->line('common_fax');?><!--Fax--> </strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['supplier']['supplierTelephone'] . ' / ' . $extra['supplier']['supplierFax']; ?></td>
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
<?php  $columnHide = $columnHide_gl = 0;
$grand_total = 0;
if (isset($extra['detail']) && !empty($extra['detail'])) {
    ?>
    <div class="table-responsive">
        <table id="add_new_grv_table" class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr' colspan="5"><?php echo $this->lang->line('accounts_payable_invoice_details');?><!--Invoice Details--></th>
                <?php  if($isTaxGroupPolicyEnable == 1) { ?>
                    <th class='theadtr otherTax_inv_header'> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                <?php } else { ?>
                    <th class='theadtr'><?php echo $this->lang->line('common_amount');?> <!--Amount--></th>
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 3%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_invoice_code');?></th><!--Invoice Code-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?> </th><!--GL Code-->
                <th class='theadtr' style="min-width: 30%"><?php echo $this->lang->line('accounts_payable_gl_code_description');?></th><!--GL Code Description-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_segment');?> </th><!--Segment-->
                <?php if($isTaxGroupPolicyEnable == 1) { ?>
                    <th class="theadtr" style="min-width: 10%">Tax<br>Applicable<br>Amount</th>
                    <th class="theadtr" style="min-width: 10%">VAT %</th>
                    <th class="theadtr" style="min-width: 10%">VAT<br>Amount</th>
                    <th class="theadtr otherTax_inv" style="min-width: 10%">Other<br>Tax</th>
                <?php } ?>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?>(<?php echo $extra['master']['transactionCurrency']; ?>)<!--Transaction-->
                </th>

            </tr>
            </thead>
            <tbody id="grv_table_body">
            <?php $supplier_total = 0;
            $Local_total = 0;
            $rporting_total = 0;
            if (!empty($extra['detail'])) {
                $i = 0;
                foreach ($extra['detail'] as $val) {
                    echo '<tr>';
                    echo '<td>' . ($i + 1) . '</td>';
                    echo '<td>'; ?>
                    <?php  if ($extra['master']['documentID'] == 'DN') { ?>
                        <a  onclick="requestPageView_model('BSI', <?php echo $val['InvoiceAutoID'] ?>)"> <?php echo $val['bookingInvCode']; ?></a>
                    <?php  }
                    else{  ?>
                        <?php echo $val['bookingInvCode'];
                    }
                    echo  '</td>';
                    echo '<td>' . $val['GLCode'] . '</td>';
                    echo '<td>' . $val['GLDescription'] . ' ' . $val['description'] . '</td>';
                    echo '<td class="text-center">' . $val['segmentCode'] . '</td>';
                    if($isTaxGroupPolicyEnable == 1) {
                        echo '<td class="text-right">'.format_number($val['transactionAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        echo '<td class="text-right">'.format_number((($val['amount']/($val['transactionAmount'] - $val['taxAmount']))*100), 2).'</td>';
                        echo '<td class="text-right">'.format_number($val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        echo '<td class="text-right otherTax_inv">'.format_number($val['taxAmount'] - $val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    }
                    echo '<td class="text-right">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    echo '</tr>';
                    if(ROUND($val['taxAmount'] - $val['amount'], 2) > 0) {
                        $columnHide = 1;
                    }
                    $supplier_total += ($val['transactionAmount']);
                    $grand_total += $val['transactionAmount'];
                    $i++;
                }

            } else {
                $norec= $this->lang->line('common_no_records_found');

                echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <?php  if($isTaxGroupPolicyEnable == 1) { ?>
                    <td class="text-right sub_total otherTax_inv_footer" colspan="9"> <?php echo $this->lang->line('common_total');?><!--Total--> </td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <?php } ?>
                <td class="text-right total"><?php echo format_number($supplier_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>

    </div>
    <br>
<?php } ?>
<?php if (isset($extra['detail_glCode']) && !empty($extra['detail_glCode'])) { ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th class='theadtr' style="min-width: 3%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th class='theadtr' style="min-width: 25%"><?php echo $this->lang->line('accounts_payable_gl_code_description');?><!--GL Code Description--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_segment');?> <!--Segment--></th>
                <?php if($advanceCostCapturing == 1){ ?>
                <th class="theadtr" style="min-width: 10%">Activity Code</th>
                <?php } 
                if($isTaxGroupPolicyEnable == 1) { ?>
                    <th class="theadtr" style="min-width: 10%">Tax<br>Applicable<br>Amount</th>
                    <th class="theadtr" style="min-width: 10%">VAT %</th>
                    <th class="theadtr" style="min-width: 10%">VAT<br>Amount</th>
                    <th class="theadtr otherTax_gl" style="min-width: 10%">Other<br>Tax</th>
                <?php } ?>
                <?php //if($isTaxGroupPolicyEnable == 1) { ?>
                    <!--<th class='theadtr' style="min-width: 15%">Amount </th>
                    <th class='theadtr' style="min-width: 15%">Tax </th>
                    <th class='theadtr' style="min-width: 15%">Tax Amount </th>-->
                <?php //} ?>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?>
                    (<?php echo $extra['master']['transactionCurrency']; ?>)<!--Transaction-->
                </th>
            </tr>
            </thead>
            <tbody id="grv_table_body">
                <?php $supplier_total = 0;
                $Local_total = 0;
                $rporting_total = 0;
                if (!empty($extra['detail_glCode'])) {
                    $i = 0;
                    foreach ($extra['detail_glCode'] as $val) {
                        echo '<tr>';
                        echo '<td>' . ($i + 1) . '</td>';
                        echo '<td>' . $val['GLCode'] . '</td>';
                        echo '<td>' . $val['GLDescription'] . ' :  ' . $val['description'] . '</td>';
                        echo '<td class="text-center">' . $val['segmentCode'] . '</td>';
                        if($advanceCostCapturing == 1){
                            echo '<td class="text-center">' . $val['activityCodeName'] . '</td>';
                        }
                        if($isTaxGroupPolicyEnable == 1) {
                            echo '<td class="text-right">'.format_number($val['transactionAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right">'.format_number(($val['taxpercentageLedger']), 2).'</td>';
                            echo '<td class="text-right"><a onclick="open_tax_dd(null,'.$val['debitNoteMasterAutoID'].',\'DN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['debitNoteDetailsID'].', \'srp_erp_debitnotedetail\',\'debitNoteDetailsID\',0,1) ">'.format_number($val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a></td>';
                            echo '<td class="text-right otherTax_gl"><a onclick="open_tax_dd(null,'.$val['debitNoteMasterAutoID'].',\'DN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['debitNoteDetailsID'].', \'srp_erp_debitnotedetail\',\'debitNoteDetailsID\',0,1) ">'.format_number($val['taxAmount'] - $val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a></td>';
                        }
                        /*if ($isTaxGroupPolicyEnable == 1) {
                            echo '<td class="text-right">' . format_number($val['transactionAmount']-$val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '<td class="text-left">' . $val['TaxDescription'] . '</td>';
                            if($val['taxAmount'] > 0) {
                                echo '<td class="text-right">
                                    <a onclick="open_tax_dd(null,'.$val['debitNoteMasterAutoID'].',\'DN\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['debitNoteDetailsID'].', \'srp_erp_debitnotedetail\',\'debitNoteDetailsID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>   
                               </td>';
                            }else {
                                echo '<td class="text-right">' . format_number($val['taxAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            }
                        }*/
                        echo '<td class="text-right">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        echo '</tr>';
                        $supplier_total += ($val['transactionAmount']);
                        $grand_total += $val['transactionAmount'];
                        if(ROUND($val['taxAmount'] - $val['amount'], 2) > 0) {
                            $columnHide_gl = 1;
                        }
                        $i++;
                    }
                } else {
                    $norec= $this->lang->line('common_no_records_found');
                    echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
                }
                ?>
            </tbody>
            <tfoot>
            <tr>
                <?php if($isTaxGroupPolicyEnable == 1) { ?>
                    <td class="text-right sub_total otherTax_gl_footer" colspan="8"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <?php } ?>
                <td class="text-right total"><?php echo format_number($supplier_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <br>
<?php } ?>
<br>
<h5 class="text-right"><!--Grand Total--><?php echo $this->lang->line('common_grand_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> ) : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>

<?php
    $data['documentCode'] = 'DN';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['debitNoteMasterAutoID'];
//    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
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
        <?php if ($extra['master']['confirmedYN']==1) { ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn']; ?></td>
            </tr>
           <?php }?>
        <?php if ($extra['master']['approvedYN']) { ?>
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
                    <?php for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) { ?>
                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/<?php echo $extra['master']['debitNoteMasterAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + <?php echo $extra['master']['debitNoteMasterAutoID'] ?> +'/DN';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);

    <?php if($columnHide == 0) { ?>
        $('.otherTax_inv').addClass('hide');
        $('.otherTax_inv_header').attr('colspan', 4);
        $('.otherTax_inv_footer').attr('colspan', 8);
    <?php } else {?>
        $('.otherTax_inv').removeClass('hide');
        $('.otherTax_inv_header').attr('colspan', 5);
        $('.otherTax_inv_footer').attr('colspan', 9);
    <?php }
    if($columnHide_gl == 0) { ?>
        $('.otherTax_gl').addClass('hide');
        $('.otherTax_gl_footer').attr('colspan', 7);
    <?php } else {?>
        $('.otherTax_gl').removeClass('hide');
        $('.otherTax_gl_footer').attr('colspan', 8);
    <?php } ?>
</script>