<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('accounts_receivable', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    echo fetch_account_review(true,true,$approval); 
?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 ><?php echo $this->lang->line('accounts_receivable_ap_credit_note');?><!--Credit Note -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
        <tbody>
            <tr>
                <td style="width:15%; font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_customer_name');?> <!--Customer Name--> </strong></td>
                <td style="width:2%; font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="width:33%; font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>
                <td style="width:15%; font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('accounts_receivable_ap_credit_note_number');?> <!--Credit Note Number--> </strong></td>
                <td style="width:2%; font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="width:33%; font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['creditNoteCode']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('accounts_receivable_common_customer_address');?> <!--Customer Address--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['customer']['customerAddress1']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('accounts_receivable_ap_credit_note_date');?><!--Credit Note Date--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo $extra['master']['creditNoteDate']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong><?php echo $this->lang->line('common_reference_number');?> <!--Reference Number--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px"> <?php echo $extra['master']['docRefNo']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
                <td colspan="4">
                    <table>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 1px"><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comments']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['comments']; ?>
                </td>
            </tr>
       </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
            <tr>
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black" colspan="5">Invoice Details</th>
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
            </tr>
            <tr>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 8%">Invoice Code</th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 35%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <!-- <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black" style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th  style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black" style="min-width: 15%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $cus_total = 0;$Local_total = 0;$rporting_total = 0;$grand_total = 0;
            if (!empty($extra['detail'])) {
                    for ($i=0; $i < count($extra['detail']); $i++) {
                        if($extra['detail'][$i]['isFromInvoice']==1){
                        echo '<tr>';
                        echo '<td style="font-size: 14px;">'.($i+1).'</td>';
                        echo '<td style="font-size: 14px;"><a target="_blank" onclick="requestPageView_model(\'CINV\','.$extra['detail'][$i]['invoiceAutoID'].')">'.$extra['detail'][$i]['invoiceSystemCode'].'</td>';
                        echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLCode'].'</td>';
                        echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLDescription'].' '.$extra['detail'][$i]['description'].'</td>';
                        echo '<td style="font-size: 14px;" class="text-left">'.$extra['detail'][$i]['segmentCode'].'</td>';
                        echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        //echo '<td class="text-right">'.format_number($extra['detail'][$i]['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                        //echo '<td class="text-right">'.format_number($extra['detail'][$i]['customerAmount'],$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                        echo '</tr>';
                        $cus_total   += ($extra['detail'][$i]['transactionAmount']);
                        $grand_total += $extra['detail'][$i]['transactionAmount'];
                        //$Local_total      += ($extra['detail'][$i]['companyLocalAmount']);
                        //$rporting_total   += ($extra['detail'][$i]['customerAmount']);
                    }
                }
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="9" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
            }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($cus_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($rporting_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table_gl" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
        <tr>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
        </tr>
        <tr>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 35%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
            <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
            <!-- <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 15%">Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
        </tr>
        </thead>
        <tbody>
        <?php $cus_total_gl = 0;$Local_total = 0;$rporting_total = 0;
        if (!empty($extra['detail'])) {
            for ($i=0; $i < count($extra['detail']); $i++) {
                if($extra['detail'][$i]['isFromInvoice']==0){
                    echo '<tr>';
                    echo '<td style="font-size: 14px;">'.($i+1).'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLCode'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLDescription'].' '.$extra['detail'][$i]['description'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['segmentCode'].'</td>';
                    echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    echo '</tr>';
                    $cus_total_gl   += ($extra['detail'][$i]['transactionAmount']);
                    $grand_total += $extra['detail'][$i]['transactionAmount'];
                    //$Local_total      += ($extra['detail'][$i]['companyLocalAmount']);
                    //$rporting_total   += ($extra['detail'][$i]['customerAmount']);
                }
            }
        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" style="font-size: 14px;" colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--> </td>
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($cus_total_gl,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<h5 class="text-right" style="font-family:'Arial, Sans-Serif, Times, Serif'; font-weight: bold;"><!--Grand Total--><?php echo $this->lang->line('common_grand_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )
    : <?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
<br>
    <div class="table-responsive">
        <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
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
    a_link=  "<?php echo site_url('Receivable/load_cn_conformation'); ?>/<?php echo $extra['master']['creditNoteMasterAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + <?php echo $extra['master']['creditNoteMasterAutoID'] ?> + '/CN';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>