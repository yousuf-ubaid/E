<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:50%;" >
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
                                <h4><?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching');?><!--Payment Matching--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_payable_trans_pm_payment_voucher_number');?><!--Payment Voucher Number--></strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['matchSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('accounts_payable_trans_pm_payment_voucher_date');?><!--Payment Voucher Date--></strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['matchDate']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
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
<div class="table-responsive">
    <table class="table table-bordered table-striped"  style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <thead>
            <tr>
                <th style="min-width: 5%;font-size: 12px;  height: 8px; padding: 1px;">#</th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="min-width: 15%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="min-width: 15%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;"><?php echo $this->lang->line('accounts_payable_trans_pm_match');?><!--Match--> </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$item_total = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) {  ?>
                <tr>
                    <td style="text-align:right;font-size: 14px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;font-size: 14px;">
                        <?php  if ($extra['master']['documentID'] == 'PVM') { ?>
                            <a  onclick="requestPageView_model('PV', <?php echo $val['payVoucherAutoId'] ?>)"> <?php echo $val['pvCode']; ?></a>
                        <?php  }else{  ?>
                        <?php echo $val['pvCode'];
                        } ?>
                    </td>
                    <td style="text-align:right;font-size: 14px;"><?php echo $val['PVdate']; ?></td>
                    <td style="text-align:center;font-size: 14px;">
                        <?php  if ($extra['master']['documentID'] == 'PVM') { ?>
                            <a  onclick="requestPageView_model('BSI', <?php echo $val['InvoiceAutoID'] ?>)"> <?php echo $val['bookingInvCode']; ?></a>
                        <?php  }else{  ?>
                        <?php echo $val['bookingInvCode'];
                        } ?>
                    </td>
                    <td style="text-align:right;font-size: 14px;"><?php echo $val['bookingDate']; ?></td>
                    <td style="text-align:right;font-size: 14px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $item_total         +=$val['transactionAmount'];
                } 
            }else{
                $norecordfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;" class="text-center">'.$norecordfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;" class="text-right sub_total" colspan="5"><?php echo $this->lang->line('accounts_payable_trans_pm_item_total');?><!--Item Total--> (<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td style="min-width: 20%;font-size: 12px;  height: 8px; padding: 1px;" class="text-right total"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="table-responsive">
    <table style="font-family:'Arial, Sans-Serif, Times, Serif';width: 100%;">
        <tbody>

<?php if($extra['master']['confirmedYN']){ ?>
            <tr>
                <td style="width:30%;font-size: 11px;"><b><!--Confirmed By--><?php echo $this->lang->line('common_confirmed_by');?> </b></td>
                <td style="font-size: 11px;"><strong>:</strong></td>
                <td style="width:70%;font-size: 11px;"><?php echo $extra['master']['confirmedByName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;font-size: 11px;"><b><!--Confirmed Date--><?php echo $this->lang->line('common_confirmed_date');?> </b></td>
                <td style="font-size: 11px;"><strong>:</strong></td>
                <td style="width:70%;font-size: 11px;"><?php echo $extra['master']['confirmedDate']; ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</div>
