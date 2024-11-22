<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
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
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'] ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4><?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching');?><!--Payment Matching--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('accounts_payable_trans_pm_payment_voucher_number');?><!--Payment Voucher Number--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['matchSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('accounts_payable_trans_pm_payment_voucher_date');?><!--Payment Voucher Date--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['matchDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
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
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('accounts_payable_trans_pm_match');?><!--Match--> </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$item_total = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) {  ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;">
                        <?php  if ($extra['master']['documentID'] == 'PVM') { ?>
                            <a  onclick="requestPageView_model('PV', <?php echo $val['payVoucherAutoId'] ?>)"> <?php echo $val['pvCode']; ?></a>
                        <?php  }else{  ?>
                        <?php echo $val['pvCode'];
                        } ?>
                    </td>
                    <td style="text-align:right;"><?php echo $val['PVdate']; ?></td>
                    <td style="text-align:center;">
                        <?php  if ($extra['master']['documentID'] == 'PVM') { ?>
                            <a  onclick="requestPageView_model('BSI', <?php echo $val['InvoiceAutoID'] ?>)"> <?php echo $val['bookingInvCode']; ?></a>
                        <?php  }else{  ?>
                        <?php echo $val['bookingInvCode'];
                        } ?>
                    </td>
                    <td style="text-align:right;"><?php echo $val['bookingDate']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $item_total         +=$val['transactionAmount'];
                } 
            }else{
                $norecordfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center">'.$norecordfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"><?php echo $this->lang->line('accounts_payable_trans_pm_item_total');?><!--Item Total--> (<?php echo $extra['master']['transactionCurrency']; ?>) </td>
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
                <td style="width:30%;"><b><!--Confirmed By--><?php echo $this->lang->line('common_confirmed_by');?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedByName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><!--Confirmed Date--><?php echo $this->lang->line('common_confirmed_date');?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedDate']; ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</div>
