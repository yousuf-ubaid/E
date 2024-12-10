<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$itemBatchPolicy = getPolicyValues('IB', 'All');

echo fetch_account_review(true,true,$approval); ?>
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
                                <h4><?php echo $this->lang->line('transaction_material_receipt_note');?></h4><!--Material Receipt Note-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_material_receipt_number');?> </strong></td><!--Material Receipt Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['mrnCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_material_receipt_date');?> </strong></td><!--Material Receipt Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['receivedDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['RefNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;font-size:12px;">
        <tbody>
            <tr>
                <td style="width:20%;"><strong><?php echo $this->lang->line('transaction_common_received_by');?> </strong></td><!--Received By-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $extra['master']['employeeName'];if(!empty($extra['master']['employeeCode'])){ echo ' ( '.$extra['master']['employeeCode'].' ) ';} ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('transaction_common_phone');?> </strong></td><!--Phone-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['employeePhone']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_fax');?> </strong></td><!--Fax-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['employeeFax']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_email');?> </strong></td><!--Email-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['employeeEmail']; ?></td>
            </tr>
            <tr>
                <td style="width:15%;"><strong><?php echo $this->lang->line('common_warehouse');?> </strong></td><!--Warehouse-->
                <td><strong>:</strong></td>
                <td style="width:85%;"><?php echo $extra['master']['wareHouseDescription'].' ( '.$extra['master']['wareHouseCode'].' )'; ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?>  </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4">
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comment']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['comment']; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php
$mrClass = '';
$colspan = 6;
if ($extra['master']['receiptType'] != 'Direct Issue') {
    $colspan = 7;
}?>
<div class="table-responsive">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
            <!-- <tr>
                <th class='theadtr' colspan="4">Item Details</th>
                <th class='theadtr' colspan="1">Qty </th>
                <th>&nbsp;</th>
            </tr> -->
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>

                <?php if($extra['master']['receiptType'] != 'Direct Issue'){?>
                <th class='theadtr' style="min-width: 10%">Document Code<!--MI Code--></th>
                <?php }?>

                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
                <th class='theadtr' style="min-width: 40%"><?php echo $this->lang->line('common_item_description');?> </th><!--Item Description-->
                
                <?php if($itemBatchPolicy == 1){ ?>
                    <th class='theadtr' style="min-width: 10%"><?php echo 'Batch Number' ?>  </th><!--UOM-->
                    <th class='theadtr' style="min-width: 10%"><?php echo 'Batch Expiry' ?>  </th><!--UOM-->
                <?php } ?>
                
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?>  </th><!--UOM-->
                <!--<th class='theadtr' style="min-width: 10%">Requested</th>-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_received');?> </th><!--Received-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('inventory_wac');?> </th><!--Unit Cost-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_total_value');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Value-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_comment');?> </th><!--Unit Cost-->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$total_count = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <?php if($extra['master']['receiptType'] != 'Direct Issue'){?>
                    <td class=""><?php echo $val['itemIssueCode']; ?></td>
                    <?php }?>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <?php if($itemBatchPolicy == 1){ ?>
                        <td style="text-align:center;"><?php echo $val['batchNumber']; ?></td>
                        <td style="text-align:center;"><?php echo $val['batchExpireDate']; ?></td>
                    <?php } ?>

                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <!--<td style="text-align:right;"><?php /*echo $val['qtyRequested']; */?></td>-->
                    <td style="text-align:right;"><?php echo $val['qtyReceived']; ?></td>
                    <td style="text-align:right;"><?php echo $val['unitCost']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo $val['comments']; ?></td>
                </tr>
                <?php
                    $num ++;
                    $total_count +=$val['totalValue'];
                } 
            }else{
                $norec=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="'.($colspan + 1) .'" class="text-center">'.$norec.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="<?php echo $colspan; ?>"><?php echo $this->lang->line('transaction_common_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td><!--Item Total-->
                <td class="text-right total"><?php echo format_number($total_count,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="table-responsive">
    <hr>
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
                <td><b>Confirmed By </b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn'];?></td>
            </tr>
        <?php } ?>
            <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?></b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
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
    a_link=  "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>/<?php echo $extra['master']['mrnAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_material_receipt'); ?>/" + <?php echo $extra['master']['mrnAutoID'] ?> + '/MRN';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>
