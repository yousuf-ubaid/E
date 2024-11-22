<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval);
?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Segoe,Roboto,Helvetica,arial,sans-serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 ><?php echo $this->lang->line('accounts_payable_debit_note');?><!--Debit Note -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
        <tbody>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%"><strong> <?php echo $this->lang->line('accounts_payable_debit_note_number');?><!--Debit Note Number--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:33%"> <?php echo $extra['master']['debitNoteCode']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:15%"><strong> <?php echo $this->lang->line('accounts_debit_note_date');?><!--Debit Note Date--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; width:33%"> <?php echo $extra['master']['debitNoteDate']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> <?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"> <?php echo $extra['supplier']['supplierName'] . ' ( ' . $extra['supplier']['supplierSystemCode'] . ' )'; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> <?php echo $this->lang->line('common_reference_number');?><!--Reference No--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"> <?php echo $extra['master']['docRefNo']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> <?php echo $this->lang->line('accounts_payable_supplier_address');?><!--Supplier Address--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"> <?php echo $extra['supplier']['supplierAddress1']; ?></td>
                <td style="vertical-align: top; font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
                <td style="vertical-align: top; font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;" colspan="4">
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comments']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['comments']; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<?php
$grand_total = 0;
if (isset($extra['detail']) && !empty($extra['detail'])) {
    ?>
    <div class="table-responsive">
        <table id="add_new_grv_table" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black" colspan="5"><?php echo $this->lang->line('accounts_payable_invoice_details');?><!--Invoice Details--></th>
                <th style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black"><?php echo $this->lang->line('common_amount');?> <!--Amount--></th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('accounts_payable_invoice_code');?></th><!--Invoice Code-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_gl_code');?> </th><!--GL Code-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 30%"><?php echo $this->lang->line('accounts_payable_gl_code_description');?></th><!--GL Code Description-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_segment');?> </th><!--Segment-->
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_transaction');?> (<?php echo $extra['master']['transactionCurrency']; ?>)<!--Transaction--></th>
            </tr>
            </thead>
            <tbody id="grv_table_body">
            <?php $supplier_total = 0;
            $Local_total = 0;
            $rporting_total = 0;
            if (!empty($extra['detail'])) {
                $i = 0;
                foreach ($extra['detail'] as $val) {
                    echo '<tr>';
                    echo '<td style="font-size: 14px;">' . ($i + 1) . '</td>';
                    echo '<td style="font-size: 14px;">'; ?>
                    <?php  if ($extra['master']['documentID'] == 'DN') { ?>
                        <a  onclick="requestPageView_model('BSI', <?php echo $val['InvoiceAutoID'] ?>)"> <?php echo $val['bookingInvCode']; ?></a>
                    <?php  }
                    else{  ?>
                        <?php echo $val['bookingInvCode'];
                    }
                    echo  '</td>';
                    echo '<td style="font-size: 14px;">' . $val['GLCode'] . '</td>';
                    echo '<td style="font-size: 14px;">' . $val['GLDescription'] . ' ' . $val['description'] . '</td>';
                    echo '<td style="font-size: 14px;">' . $val['segmentCode'] . '</td>';
                    echo '<td style="font-size: 14px;" class="text-right">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';

                    echo '</tr>';
                    $supplier_total += ($val['transactionAmount']);
                    $grand_total += $val['transactionAmount'];
                    $i++;
                }

            } else {
                $norec= $this->lang->line('common_no_records_found');

                echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <td class="text-right sub_total"  style="font-size: 14px;"><?php echo format_number($supplier_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>

    </div>
    <br>
<?php } ?>
<?php if (isset($extra['detail_glCode']) && !empty($extra['detail_glCode'])) { ?>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 30%"><?php echo $this->lang->line('accounts_payable_gl_code_description');?><!--GL Code Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_segment');?> <!--Segment--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_transaction');?> (<?php echo $extra['master']['transactionCurrency']; ?>)<!--Transaction--></th>
            </tr>
            </thead>
            <tbody id="grv_table_body">
            <?php $supplier_total = 0;
            $Local_total = 0;
            $rporting_total = 0;
            if (!empty($extra['detail_glCode'])) {

                $i = 0;
                foreach ($extra['detail_glCode'] as $val) {
                    echo '<tr>';
                    echo '<td style="font-size: 14px;">' . ($i + 1) . '</td>';
                    echo '<td style="font-size: 14px;">' . $val['GLCode'] . '</td>';
                    echo '<td style="font-size: 14px;">' . $val['GLDescription'] . ' :  ' . $val['description'] . '</td>';
                    echo '<td style="font-size: 14px;">' . $val['segmentCode'] . '</td>';
                    echo '<td style="font-size: 14px;" class="text-right">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    echo '</tr>';
                    $supplier_total += ($val['transactionAmount']);
                    $grand_total += $val['transactionAmount'];
                    $i++;
                }
            } else {
                $norec= $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($supplier_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>
    </div>
    <br>
<?php } ?>
<br>
<h5 class="text-right" style="font-family:'Arial, Sans-Serif, Times, Serif'; font-size: 14px; font-weight: bold;"><!--Grand Total--><?php echo $this->lang->line('common_grand_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )
    : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
<div class="table-responsive">
    <br>
    <table style="width: 100%" style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
        <?php if ($extra['master']['confirmedYN']==1) { ?>
        <tr>
            <td style="font-size: 12px;"><b>Confirmed By</b></td>
            <td><strong>:</strong></td>
            <td style="font-size: 12px;"><?php echo $extra['master']['confirmedYNn']; ?></td>
        </tr>
           <?php }?>
        <?php if ($extra['master']['approvedYN']) { ?>
        <tr>
            <td style="font-size: 12px; width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
            <td><strong>:</strong></td>
            <td style="font-size: 12px; width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px; width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
            <td><strong>:</strong></td>
            <td style="font-size: 12px; width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Times New Roman'; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center">
                ____________________________
            </td>
            <td style="text-align: center">
                ____________________________
            </td>
            <td style="text-align: center">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center">
                Approved By
            </td>
            <td style="font-size: 12px; text-align: center">
                Checked By
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/<?php echo $extra['master']['debitNoteMasterAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + <?php echo $extra['master']['debitNoteMasterAutoID'] ?> +'/DN';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>