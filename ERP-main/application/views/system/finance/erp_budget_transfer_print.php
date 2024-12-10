<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_expanse_claim', $primaryLanguage);
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('finance_budget_transfer'); ?> <!--Budget Transfer--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('finance_budget_transfer_number'); ?> <!--Budget Transfer Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentSystemCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('finance_budget_transfer_date'); ?> <!--Budget Transfer Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['createdDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_financial_year'); ?> <!--Financial Year--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['financeYear']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration'); ?> <!--Narration--></strong></td>
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
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th colspan="3"><?php echo $this->lang->line('finance_transfer_from'); ?> <!--Transfer From--></th>
            <th colspan="2"><?php echo $this->lang->line('finance_transfer_To'); ?> <!--Transfer To--></th>
            <th>&nbsp;</th>
        </tr>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_narration'); ?> <!--Segment--></th>
            <th style="min-width: 10%" class="theadtr"><?php echo $this->lang->line('common_gl_code'); ?> <!--GL Code--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_narration'); ?> <!--Segment--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_gl_code'); ?> <!--GL Code--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('finance_transfer_amount'); ?> <!--Transfer Amount--> (<?php echo $extra['master']['CurrencyCode']; ?>)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $value) {
                $total=0;
                $decimal=2;
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td><?php echo $value['fsegment']; ?></td>
                        <td><?php echo $value['fGLC']; ?></td>
                        <td><?php echo $value['tsegment']; ?></td>
                        <td><?php echo $value['tGLC']; ?></td>
                        <td class="text-right"><?php echo number_format($value['transferAmount'], $extra['master']['DecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<br>
<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong><?php echo $this->lang->line('common_electronically_approved_by'); ?> <!--Electronically Approved By--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_electronically_approved_date'); ?> <!--Electronically Approved Date --></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>/<?php echo $extra['master']['budgetTransferAutoID'] ?>";
    $("#a_link").attr("href", a_link);
</script>



