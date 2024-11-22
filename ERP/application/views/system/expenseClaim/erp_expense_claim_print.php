<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_expanse_claim', $primaryLanguage);
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

?>
<div class="table-responsive">

    <input type="hidden" name="Level" id="Level" value="<?php echo  $extra['master']['currentLevelNo'] ?>" />

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
                            <h4><?php echo $this->lang->line('common_expense_claim');?><!--Expense Claim--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_expanse_claim_claimed_by_emp_name');?><!--Claimed By Emp Name--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['claimedByEmpName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_expanse_claim_claimed_emp_code');?><!--Claimed Emp Code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ECode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_expanse_claim_expense_claim_number');?><!--Expense Claim Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['expenseClaimCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_expanse_claim_expanse_claim_date');?><!--Expense Claim Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['expenseClaimDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_comments');?><!--Comments--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['comments']; ?></td>
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
            <th style="min-width: 4%" class='theadtr'>#</th>
            <?php if($approval == 1 ){?> <th style="min-width: 4%" class='theadtr'></th><?php } ?>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('hrms_expanse_claim_expanse_claim_category');?><!--Expense Claim Category--></th>
            
            <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('hrms_expanse_claim_expanse_doc_ref');?><!--Doc Ref--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('profile_segment');?><!--Segment--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_amount');?><!--Amount--></th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $test = array_group_by($extra['detail'], 'transactionCurrency');
        if (!empty($test)) {
            foreach ($test as $value) {
                $total=0;
                $decimal=2;
                foreach ($value as $val) {
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <?php if($approval == 1 ){?>
                        <td>
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input
                                    id="selectedYN_<?php echo $val['expenseClaimDetailsID'] ?>" type="checkbox"
                                    data-caption="" class="columnSelected expenseclaim_checkbox"
                                    name="" onclick="expense_claim_selected_check(this)" <?php echo $val['selectedYN'] == 1 ? 'checked' : ''; ?>
                                    value="<?php echo $val['expenseClaimDetailsID'] ?>"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                        <!-- <input type="checkbox" id="selectedYN_<?php echo $val['expenseClaimDetailsID'] ?>" 
                        name="" onclick="assign_checklist_selected_check(this)"
                         value="<?php echo $val['expenseClaimDetailsID']?>"> -->

                        <!-- <input type="checkbox" id="selectedYN_<?php echo $val['expenseClaimDetailsID']; ?>" name="selectedYN[<?php echo $val['expenseClaimDetailsID']; ?>]" value="1" <?php echo $val['selectedYN'] == 1 ? 'checked' : ''; ?>> -->
                    </td>
                    <?php } ?>

                        <td class="text-center"><?php echo $val['claimcategoriesDescription']; ?></td>
                        <td class="text-center"><?php echo $val['description']; ?></td>
                        <td class="text-center"><?php echo $val['referenceNo']; ?></td>
                        <td class="text-center"><?php echo $val['segmentCode']; ?></td>
                        <td class="text-center"><?php echo $val['transactionCurrency']; ?></td>
                        <td class="text-right"><?php echo number_format($val['transactionAmount'], $val['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $total +=round($val['transactionAmount'], $val['transactionCurrencyDecimalPlaces']);
                    $decimal=$val['transactionCurrencyDecimalPlaces'];
                    $num++;
                }
                ?>
                <tr>
                    <td class="text-right" colspan="6"><b><?php echo $this->lang->line('common_total');?><!--Total--></b></td>
                    <td class="text-right"><b><?php echo number_format($total,$decimal); ?></b></td>
                </tr>
                <?php
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div>

<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedByEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
//     function assign_checklist_selected_check(selected_YN) {
       
//        var value = $(selected_YN).val();
//        if ($(selected_YN).is(':checked')) {
//            var inArray = $.inArray(value, assignCheckListSync);
//            if (inArray == -1) {
//                assignCheckListSync.push(value);
//            }
//        }
//        else {
//            var i = assignCheckListSync.indexOf(value);
//            if (i != -1) {
//                assignCheckListSync.splice(i, 1);
//            }
//        }
//    }
$(document).ready(function () {
 
 $('.extraColumns input').iCheck({
     checkboxClass: 'icheckbox_square_relative-blue',
     radioClass: 'iradio_square_relative-blue',
     increaseArea: '20%'
 });

 $('.expenseclaim_checkbox').on('ifChecked', function (event) {
     expense_claim_selected_check(this);
 });
 $('.expenseclaim_checkbox').on('ifUnchecked', function (event) {
     expense_claim_selected_check(this);
 });



});
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>/<?php echo $extra['master']['expenseClaimMasterAutoID'] ?>";
    $("#a_link").attr("href", a_link);
</script>



