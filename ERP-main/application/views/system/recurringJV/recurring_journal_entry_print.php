<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(false,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo. $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('finance_rj_recurring_journal_voucher_view');?><!--Recurring Journal Voucher--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('finance_rj_recurring_journal_voucher_number');?><!--Recurring Journal Voucher Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['RJVcode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_start_date');?><!--Start Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['RJVStartDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_end_date');?><!--End Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['RJVEndDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
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
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;" colspan="4">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['RJVNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['RJVNarration']; ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class='theadtr' colspan="4"><?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
            <th class='theadtr' colspan="2"> <?php echo $this->lang->line('common_amount');?><!--Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('finance_common_debit');?><!--Debit--></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('finance_common_credit');?><!--Credit--></th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $creditAmount = 0;
        $debitAmount = 0;
        $rporting_total = 0;
        if (!empty($extra['detail'])) {
            for ($i = 0; $i < count($extra['detail']); $i++) {
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . $extra['detail'][$i]['GLCode'] . '</td>';
                echo '<td>' . $extra['detail'][$i]['GLDescription'] . '</td>';
                echo '<td class="text-center">' . $extra['detail'][$i]['segmentCode'] . '</td>';
                echo '<td class="text-right">' . format_number($extra['detail'][$i]['debitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . format_number($extra['detail'][$i]['creditAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '</tr>';
                $creditAmount += ($extra['detail'][$i]['creditAmount']);
                $debitAmount += ($extra['detail'][$i]['debitAmount']);
            }
        } else {
            $norec=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?>
                )
            </td>
            <td class="text-right total"><?php echo format_number($debitAmount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right total"><?php echo format_number($creditAmount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

        </tr>
        </tfoot>
    </table>
</div>
<br>
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
        <?php if($extra['master']['confirmedYN']==1){ ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn']; ?></td>
            </tr>
        <?php } ?>
        <?php if($extra['master']['approvedYN']){ ?>
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
    a_link = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/<?php echo $extra['master']['RJVMasterAutoId'] ?>";

    $("#a_link").attr("href", a_link);

</script>