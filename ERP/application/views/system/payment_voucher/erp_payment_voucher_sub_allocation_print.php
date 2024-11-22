<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$itemBatch_policy = getPolicyValues('IB', 'All');
$this->lang->load('common', $primaryLanguage);
$printHeaderFooterYN = 0;

ini_set('max_execution_time', '-1');

echo fetch_account_review(true, true, $approval && $extra['master']['approvedYN']);

?>
<div class="table-responsive tb-responsive-main">
    <?php
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
    ?>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;" class="layer-1">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" class="main-logo"
                                 src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>" style="max-height: 100px;max-width:250px">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;" class="layer-1">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('sales_markating_sales_payment_voucher'); ?></h4>
                            <!--Payment Voucher-->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong> <?php echo $this->lang->line('sales_markating_sales_payment_voucher_number'); ?></strong>
                        </td><!--Payment Voucher Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVcode']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php echo $this->lang->line('sales_markating_sales_payment_voucher_date'); ?></strong>
                        </td><!--Payment Voucher Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['PVdate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number'); ?></strong></td>
                        <!--Reference Number-->
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
<?php
}else{
    ?>
    <div class="res-height">&nbsp;</div>
    
    <!--<table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>

                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">

                            <h4><?php /*echo $this->lang->line('sales_markating_sales_payment_voucher'); */?></h4>-->
                            <!--Payment Voucher-->
                        <!--</td>
                    </tr>
                    <tr>
                        <td>
                            <strong> <?php /*echo $this->lang->line('sales_markating_sales_payment_voucher_number'); */?></strong>
                        </td>--><!--Payment Voucher Number-->
                        <!--<td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['PVcode']; */?></td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php /*echo $this->lang->line('sales_markating_sales_payment_voucher_date'); */?></strong>
                        </td>--><!--Payment Voucher Date-->
                        <!--<td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['PVdate']; */?></td>
                    </tr>
                    <tr>
                        <td><strong><?php /*echo $this->lang->line('common_reference_number'); */?></strong></td>-->
                        <!--Reference Number-->
                       <!-- <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['referenceNo']; */?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>-->
    <?php
}
?>


<?php if (!empty($extra['invoices'])) {
   
    $Local_total = 0;
    $party_total = 0;
    $grand_total = 0; ?>
    <br>
  

    <?php
        foreach ($extra['invoices'] as $val) {  ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                    <tr>
                        <th class='theadtr'
                            colspan="3"><?php echo 'Recipient details'; ?></th>
                        <!--GL Details-->
                        <th colspan="2" class='theadtr'><?php echo $this->lang->line('common_amount'); ?>  </th><!--Amount-->
                    </tr>
                    <tr>
                        <th class='theadtr' style="width: 3%">#</th>
                        <th class='theadtr' style="width: 16%"><?php echo 'Payment Recipient'; ?></th>

                        <th class='theadtr' style="width: 16%"><?php echo 'Recipient No'; ?></th>
                        <!--GL Code-->
                        <th class='theadtr' style="width: 30%"><?php echo 'Invoice No' ?></th>
                        <!--GL Code Description-->
                        <th class='theadtr' style="width: 15%"><?php echo 'INV Date'; ?></th>
                        <!--GL Code Description-->
                        <th class='theadtr' style="width: 15%"><?php echo 'PO'; ?></th>
                        <!--GL Code Description-->
                        <th class='theadtr' style="width: 15%"><?php echo 'Amount'; ?></th>

                    </tr>
                    </thead>
                <tbody>
                <?php
                $num = 1;
                $transaction_total = 0;
                    foreach ($val['invoices'] as $invoice) { 
                        // echo '<pre>'; print_r($invoice); exit;/
                        ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $num ?></td>
                                <td style="text-align:right;"><?php echo $val['master']['vendor_name']; ?>.&nbsp;</td>
                                <td style="text-align:right;"><?php echo $invoice['supplierInvoiceNo']; ?>.&nbsp;</td>
                                <td style="text-align:center;"><?php echo $invoice['bookingInvCode']; ?></td>
                                <td style="text-align:center;"><?php echo $invoice['invoiceDate']; ?></td>
                                <td style="text-align:center;"><?php echo $invoice['RefNo']; ?></td>
                                <td style="text-align:right;"><?php echo format_number($invoice['allocation_amount'],$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                
                                
                            </tr>
                        <?php
                        $num++;
                        $transaction_total += $invoice['allocation_amount'];
                        // $Local_total        +=$val['companyLocalAmount'];
                        // $party_total        +=$val['partyAmount'];
                        $grand_total += $invoice['allocation_amount'];
                        // $tax_transaction_total += $val['allocation'];
                    
                    } ?>
                
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;font-size:10px;"><b><?php echo $this->lang->line('common_total'); ?></b></td>
                        <td class="total" style="text-align:right;font-size:10px;"><?php echo format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                </tfoot>
            </table> <br><br><br>
        <?php 
        }    
        
        ?>       
        </div>
<?php } ?>

<br><br>

<div class="table-responsive">
    <h5 class="text-right"><?php echo $this->lang->line('common_grand_total'); ?><!--Grand Total-->
        (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php if($extra['master']['rrvrID'] && $grand_total == 0){
            echo format_number($extra['master']['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
        }else{
            echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']);
        } ?></h5>
</div>

<?php
    $data['documentCode'] = 'PV';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['payVoucherAutoId'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);
?>




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
                } ?>

            <?php if ($extra['master']['approvedYN']) { ?>
                <tr>
                    <td style="width:30%;">&nbsp;</td>
                    <td><strong>&nbsp;</strong></td>
                    <td style="width:70%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:30%;">
                        <b><?php echo $this->lang->line('sales_markating_sales_purachase_commission_collected_by'); ?></b>
                    </td><!--Collected By-->
                    <td><strong>:</strong></td>
                    <td style="width:70%;">_____________________</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="res-height">&nbsp;</div>
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
    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/<?php echo $extra['master']['payVoucherAutoId'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + <?php echo $extra['master']['payVoucherAutoId'] ?> +'/PV';
    $("#a_link").attr("href", a_link);
    $(".de_link").attr("href", de_link);

</script>
