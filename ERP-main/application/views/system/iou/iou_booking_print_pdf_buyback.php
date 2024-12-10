<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo fetch_account_review(true, true, $approval); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                            echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4> <?php echo $this->lang->line('iou_expenses'); ?> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong><?php echo $this->lang->line('iou_document_number'); ?> </strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo $extra['master']['bookingCode']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong><?php echo $this->lang->line('common_document_date'); ?></strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo $extra['master']['bookingDate']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong><?php echo $this->lang->line('common_employee_name'); ?></strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo $extra['master']['employeename']; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong><?php echo $this->lang->line('common_currency'); ?></strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo $extra['master']['currencyid']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_narration'); ?></strong></td>
                        <td style="vertical-align: top;font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                        <td>
                            <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                                <tr>
                                    <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['comments']);?></td>
                                </tr>
                            </table>
                            <?php //echo $extra['master']['comments']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong><?php echo $this->lang->line('common_segment'); ?></strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><strong>:</strong></td>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;" ><?php echo $extra['master']['segmentCode']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>" style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <thead>
        <tr>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'>#</th>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'><?php echo $this->lang->line('iou_voucher_code'); ?></th>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'><?php echo $this->lang->line('iou_expense_category'); ?></th>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'><?php echo $this->lang->line('common_segment'); ?></th>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'><?php echo $this->lang->line('common_description'); ?></th>
            <th style="font-size: 12px;  height: 8px; padding: 1px;" class='theadtr'><?php echo $this->lang->line('common_amount'); ?> <span class="currency"> (<?php echo $extra['master']['currencyid']; ?>)</span>
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
                    <td style="font-size: 14px; text-align:right"><?php echo $num; ?>.</td>
                    <td style="font-size: 14px; text-align:left"><?php echo $val['iouCode']; ?></td>
                    <td style="font-size: 14px; text-align:left"><?php echo $val['categoryDescription']; ?></td>
                    <td style="font-size: 14px; text-align:left"><?php echo $val['segmentCode']; ?></td>
                    <td style="font-size: 14px; text-align:left"><?php echo $val['bookingdescription']; ?></td>
                    <td style="font-size: 14px; text-align:right"><?php echo format_number($val['bookingAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total += $val['bookingAmount'];
            }
        } else { ?>
            <tr class="danger"><td colspan="7" style="font-size: 12px;  height: 8px; padding: 1px;" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?></td></tr>
       <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="font-size: 12px" class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total'); ?><span
                        class="currency"> (<?php echo $extra['master']['currencyid']; ?>)</span></td>
            <td style="font-size: 12px" class="text-right total"><?php echo format_number($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
        <br>
    </table>
</div>
<br>
<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%" style="font-family:'Arial, Sans-Serif, Times, Serif';" >
            <tbody>
            <tr>
                <td style="width:30%;font-size: 11px;">
                    <b><?php echo $this->lang->line('common_electronically_approved_by'); ?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;font-size: 11px;">
                    <b><?php echo $this->lang->line('common_electronically_approved_date'); ?></b></td>
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

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/<?php echo $extra['master']['bookingMasterID'] ?>";
    de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + <?php echo $extra['master']['bookingMasterID'] ?> +'/IOUB';
    $("#a_link").attr("href", a_link);
    $(".de_link").attr("href", de_link);
    $("#a_link").attr("href", a_link);
</script>