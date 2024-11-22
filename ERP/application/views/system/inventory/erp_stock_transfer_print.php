<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$itemBatch_policy = getPolicyValues('IB', 'All');
echo fetch_account_review(true,true,$approval);
$advanceCostCapturing = getPolicyValues('ACC', 'All');
?>

<?php if($type == true){?>
<div class="row">
    <input class="hidden" id="jobID" name="jobID" value="<?php echo $extra['master']['jobID']?>">
    <input class="hidden" id="jobClosed" name="jobClosed" value="<?php echo $extra['jobClosed']?>">
</div>
<?php }?>

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
                                <h4><?php echo $this->lang->line('transaction_stock_transfer');?></h4><!--Stock Transfer-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_stock_transfer_number');?> </strong></td><!--Stock Transfer Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['stockTransferCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_stock_transfer_date');?> </strong></td><!--Stock Transfer Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['tranferDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['referenceNo']; ?></td>
                        </tr>
                        <?php if ($extra['master']['jobID']) { ?>
                          <!--  <tr>
                                <td><strong> Job Number </strong></td>
                                <td><strong>:</strong></td>
                                <td><?php /*echo $extra['master']['jobNumber'];*/?></td>
                            </tr>-->
                        <?php } ?>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%;font-size:12px;">
        <tbody>
            <tr>
                <td style="width:20%;"><strong><?php echo $this->lang->line('transaction_common_from_warehouse');?> </strong></td><!--Form Warehouse-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $extra['master']['form_wareHouseDescription'].' ( '.$extra['master']['form_wareHouseCode'].' ) '.$extra['master']['form_wareHouseLocation']; ?></td>
            </tr>
            <tr>
                <td style="width:20%;"><strong><?php echo $this->lang->line('transaction_common_to_warehouse');?> </strong></td><!--To Warehouse-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $extra['master']['to_wareHouseDescription'].' ( '.$extra['master']['to_wareHouseCode'].' ) '.$extra['master']['to_wareHouseLocation']; ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
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
<div class="table-responsive">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <?php if ($extra['master']['transferType'] == 'materialRequest') { ?>
                    <th  class='theadtr' >&nbsp;</th>
                <?php }?>
                <th class='theadtr' colspan="<?php echo ($itemBatch_policy == 1 ? '6':'5')?>"><?php echo $this->lang->line('transaction_common_item_details');?> </th>
                <th class='theadtr' colspan="1"><?php echo $this->lang->line('common_qty');?> </th>
                <th  class='theadtr' colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <?php if ($extra['master']['transferType'] == 'materialRequest') { ?>
                    <th class='theadtr' style="min-width: 15%">MR Code</th><!--MR Code-->
                <?php }?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
                <th class='theadtr' style="min-width: 30%"><?php echo $this->lang->line('common_item_description');?> </th><!--Item Description-->
                <th class='theadtr' style="min-width: 30%">Bin Location </th>
                <?php if($advanceCostCapturing == 1){ ?>
                    <th class='theadtr' style="min-width: 10%">Activity Code</th>
                <?php } ?>
                <?php if ($itemBatch_policy == 1) { ?>
                    <th class='theadtr' style="min-width: 10%">Batch Number</th>
                <?php }?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?> </th><!--UOM-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_transfer');?> </th><!--Transfer-->
                <!--<th class='theadtr' style="min-width: 10%">Received</th>-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?></th><!--WAC-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_total_value');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Value-->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$total_count = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <?php if ($extra['master']['transferType'] == 'materialRequest') { ?>
                        <td style="text-align:center;"><?php echo $val['MRCode']; ?></td>
                    <?php }?>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <td><?php echo $val['binlocation']; ?></td>
                    <?php if($advanceCostCapturing == 1){ ?>
                    <td><?php echo $val['activityCodeName']; ?></td>
                    <?php }?>
                    <?php if($itemBatch_policy==1){?>
                        <td><?php echo $val['batchNumber']; ?></td>
                    <?php }?>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['transfer_QTY']; ?></td>
                    <!--<td style="text-align:right;"><?php /*echo $val['received_QTY']; */?></td>-->
                    <td style="text-align:right;"><?php echo format_number($val['currentlWacAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $total_count +=$val['totalValue'];
                }
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="8" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr> <?php if ($extra['master']['transferType'] == 'materialRequest') { ?>
                <td class="text-right sub_total" colspan="8"><?php echo $this->lang->line('transaction_common_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td><!--Item Total-->
                <?php }else{?>
                <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('transaction_common_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td><!--Item Total-->
                <?php }?>
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
                <td style="width:30%;"><b>Confirmed By </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedYNn'];?></td>
            </tr>
        <?php } ?>
            <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
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
    a_link=  "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/<?php echo $extra['master']['stockTransferAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + <?php echo $extra['master']['stockTransferAutoID'] ?> + '/ST';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

</script>
