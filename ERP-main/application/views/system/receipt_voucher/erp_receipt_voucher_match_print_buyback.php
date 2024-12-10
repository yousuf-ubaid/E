<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:50%;">
                    <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'] ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4><?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching'); ?><!-- Receipt Matching--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_number'); ?><!--Receipt Voucher Number--></strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['matchSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date'); ?><!--Receipt Voucher Date--></strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['matchDate']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_reference_number'); ?><!--Reference Number--></strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['refNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table class="table table-bordered table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';"> 
        <thead>
            <tr>
                <th style="min-width: 5%;font-size: 12px;  height: 8px; padding: 1px;">#</th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 15%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 15%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('accounts_receivable_tr_rm_match'); ?><!--Match--> </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$item_total = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) {  ?>
                <tr>
                    <td style="text-align:right;font-size: 14px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;font-size: 14px;"><a target="_blank" onclick="requestPageView_model('RV',<?php echo $val['receiptVoucherAutoId']; ?>)"><?php echo $val['RVcode']; ?></a></td>
                    <td style="text-align:right;font-size: 14px;"><?php $convertFormat=convert_date_format(); echo format_date($val['RVdate'],$convertFormat) ; ?></td>
                    <td style="text-align:center;font-size: 14px;"><a target="_blank" onclick="requestPageView_model('CINV',<?php echo $val['InvoiceAutoID']; ?>)"><?php echo $val['invoiceCode']; ?></a></td>
                    <td style="text-align:right;font-size: 14px;"><?php $convertFormat=convert_date_format(); echo format_date($val['invoiceDate'],$convertFormat) ; ?></td>
                    <td style="text-align:right;font-size: 14px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $item_total         +=$val['transactionAmount'];
                } 
            }else{
                $norecordsfound=$this->lang->line('common_no_records_found');
                echo '<tr style="font-size: 12px;  height: 8px; padding: 1px;" class="danger"><td colspan="6" class="text-center">'.$norecordsfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px;" class="text-right sub_total" colspan="5"><?php echo $this->lang->line('accounts_receivable_common_item_total'); ?><!--Item Total--> (<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;" class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php if($extra['master']['confirmedYN']){ ?>
<div class="table-responsive">
    <table style="width: 100%;font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
            <tr>
                <td style="width:30%;font-size: 11px;"><b><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By--> </b></td>
                <td ><strong>:</strong></td>
                <td style="width:70%;font-size: 11px;"><?php echo $extra['master']['confirmedByName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;font-size: 11px;"><b><?php echo $this->lang->line('common_confirmed_date'); ?><!--Confirmed Date--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;font-size: 11px;"><?php echo $extra['master']['confirmedDate']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;font-size: 11px;">&nbsp;</td>
                <td><strong>&nbsp;</strong></td>
                <td style="width:70%;font-size: 11px;">&nbsp;</td>
            </tr>

            <tr>
                <td style="width:30%;font-size: 11px;"><b><?php echo $this->lang->line('accounts_receivable_common_received_by'); ?> </b></td><!--Received By-->
                <td><strong>:</strong></td>
                <td style="width:70%;font-size: 11px;">_____________________</td>
            </tr>
        </tbody>
    </table>
</div>
<?php } ?>