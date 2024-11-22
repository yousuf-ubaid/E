<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo fetch_account_review(false, true, $approval); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                            echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4> <?php echo $this->lang->line('iou_voucher'); ?> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('iou_iou_number'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['iouCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('iou_voucherdate'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['voucherDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_employee_name'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['empnameiou']; ?> </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_currency'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['transactioncurrency']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration'); ?></strong></td>
                        <td style="vertical-align: top"><strong>:</strong></td>
                        <td>
                            <table>
                                <tr>
                                    <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['narration']);?></td>
                                </tr>
                            </table>
                            <?php //echo $extra['master']['narration']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_segment'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['segmentCode']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="table-responsive">
    <hr>
    <table>
        <tr>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('common_bank'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['master']['bankname']; ?> </td>
                    </tr>
                    <tr>
                        <td style="width:15%;" class="td"><?php echo $this->lang->line('common_bank'); ?> <?php echo $this->lang->line('common_account'); ?><strong></strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:83%;" class="td"><?php echo $extra['master']['bankacount']; ?> </td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('iou_bank_swift_code'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['master']['bankSwiftCode']; ?> </td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('iou_cheque_number'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['master']['ChequeNo']; ?> </td>
                    </tr>
                    <tr>
                        <td class="td"><strong><?php echo $this->lang->line('iou_cheque_date'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php echo $extra['master']['chequeDate']; ?> </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr'>#</th>
            <th class='theadtr'><?php echo $this->lang->line('common_description'); ?></th>
            <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?> <span class="currency"> (<?php echo $extra['master']['CurrencyCode']; ?>)</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $total = 0;
        if (!empty($extra['detail'])) {

            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right"><?php echo $num; ?>.</td>
                    <td style="text-align:left"><?php echo $val['description']; ?></td>
                    <td style="text-align:right"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total += $val['transactionAmount'];
            }
        } else { ?>
            <tr class='danger'><td colspan='3' class='text-center'><?php $this->lang->line('common_no_records_found') ?></td></tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="2"> <?php echo $this->lang->line('common_total'); ?><span
                        class="currency"> (<?php echo $extra['master']['CurrencyCode']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
        <br>
    </table>
</div>
<br>
<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:30%;">
                    <b><?php $this->lang->line('common_electronically_approved_by') ?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;">
                    <b><?php $this->lang->line('common_electronically_approved_date') ?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
<?php } ?>

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
                            <span>____________________________</span><br><br><span><b>&nbsp; <?php $this->lang->line('iou_authorized_signature') ?></b></span>
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
    a_link = "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>/<?php echo $extra['master']['voucherAutoID'] ?>";
    $("#a_link").attr("href", a_link);
</script>