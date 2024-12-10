<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if($extra['VAT_exist'] > 0) {
    echo fetch_account_review(true,false);
} ?>
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
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'] ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4><?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching'); ?><!-- Receipt Matching--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_number'); ?><!--Receipt Voucher Number--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['matchSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date'); ?><!--Receipt Voucher Date--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['matchDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number'); ?><!--Reference Number--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['refNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('accounts_receivable_tr_rm_match'); ?><!--Match--> </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$item_total = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) {  ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><a target="_blank" onclick="requestPageView_model('RV',<?php echo $val['receiptVoucherAutoId']; ?>)"><?php echo $val['RVcode']; ?></a></td>
                    <td style="text-align:right;"><?php $convertFormat=convert_date_format(); echo format_date($val['RVdate'],$convertFormat) ; ?></td>
                    <td style="text-align:center;"><a target="_blank" onclick="requestPageView_model('CINV',<?php echo $val['InvoiceAutoID']; ?>)"><?php echo $val['invoiceCode']; ?></a></td>
                    <td style="text-align:right;"><?php $convertFormat=convert_date_format(); echo format_date($val['invoiceDate'],$convertFormat) ; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $item_total         +=$val['transactionAmount'];
                }
            }else{
                $norecordsfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="6" class="text-center">'.$norecordsfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"><?php echo $this->lang->line('accounts_receivable_common_item_total'); ?><!--Item Total--> (<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
            </tr>
        <?php if($extra['master']['confirmedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedByName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_confirmed_date'); ?><!--Confirmed Date--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedDate']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;">&nbsp;</td>
                <td><strong>&nbsp;</strong></td>
                <td style="width:70%;">&nbsp;</td>
            </tr>

            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('accounts_receivable_common_received_by'); ?> </b></td><!--Received By-->
                <td><strong>:</strong></td>
                <td style="width:70%;">_____________________</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $('.review').removeClass('hide');
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_receipt_match'); ?>/" + <?php echo $extra['master']['matchID'] ?> + '/RVM';
    $("#de_link").attr("href",de_link);
</script>