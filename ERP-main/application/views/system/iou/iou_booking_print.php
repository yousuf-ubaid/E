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
                            <h4> <?php echo $this->lang->line('iou_expenses'); ?> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('iou_document_number'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['bookingCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_date'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['bookingDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_employee_name'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['employeename']; ?> </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_currency'); ?></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['currencyid']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration'); ?></strong></td>
                        <td style="vertical-align: top"><strong>:</strong></td>
                        <td>
                            <table>
                                <tr>
                                    <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['comments']);?></td>
                                </tr>
                            </table>
                            <?php //echo $extra['master']['comments']; ?>
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

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr'>#</th>
            <th class='theadtr'><?php echo $this->lang->line('iou_voucher_code'); ?></th>
            <th class='theadtr'><?php echo $this->lang->line('iou_expense_category'); ?></th>
            <th class='theadtr'><?php echo $this->lang->line('common_segment'); ?></th>
            <th class='theadtr'><?php echo $this->lang->line('common_description'); ?></th>
            <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?> <span class="currency"> (<?php echo $extra['master']['currencyid']; ?>)</span>
            <th class='theadtr'><?php echo $this->lang->line('common_attachment'); ?></th></span>
            </th>
            <th class='theadtr'></th>
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
                    <td style="text-align:left"><?php echo $val['iouCode']; ?></td>
                    <td style="text-align:left"><?php echo $val['categoryDescription']; ?></td>
                    <td style="text-align:left"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:left"><?php echo $val['bookingdescription']; ?></td>
                    <td style="text-align:right"><?php echo format_number($val['bookingAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right"><span><a onclick="attachment_modal(<?php echo $val['bookingDetailsID']?>,'<?php echo $this->lang->line('iou_iou_voucher_expenses'); ?>','IOUE',<?php echo $extra['master']['confirmedYN']?>)"><span title="<?php $this->lang->line('common_attachment')?>" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a></span></td>
                    <?php if ($val['isRequiredFields'] == 1): ?><!--SMSD-->
                        <td style="text-align:right"><a onclick="required_field_details(<?php echo $val['bookingDetailsID']; ?>,<?php echo $val['bookingMasterID'];?>, <?php echo $extra['master']['confirmedYN']?>)"><span title="Required Fields" rel="tooltip" class="glyphicon glyphicon-cog"></span></a></td><!--SMSD-->
                    <?php endif; ?><!--SMSD-->
                </tr>
                <?php
                $num++;
                $total += $val['bookingAmount'];
            }
        } else { ?>
            <tr class="danger"><td colspan="7" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?></td></tr>
       <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total'); ?><span
                        class="currency"> (<?php echo $extra['master']['currencyid']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td> </td>
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
                    <b><?php echo $this->lang->line('common_electronically_approved_by'); ?> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;">
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