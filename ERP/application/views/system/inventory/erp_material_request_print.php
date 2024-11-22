<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

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
                                <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                                <h4><?php echo $this->lang->line('transaction_material_request')?><!--Material Request--> </h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_material_request_number')?><!--Material Request Number--> </strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['MRCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_material_request_date')?><!--Material Request Date--> </strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['requestedDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number')?><!--Reference Number--></strong></td>
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
<div class="table-responsive">
    <table style="width: 100%;font-size:12px;">
        <tbody>
            <tr>
                <td style="width:20%;"><strong><?php echo $this->lang->line('transaction_material_requested_by')?><!--Requested By--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $extra['master']['employeeName'].' ('.$extra['master']['employeeCode'].' ) '; ?></td>
            </tr>
            <tr>
                <td style="width:15%;"><strong><?php echo $this->lang->line('common_warehouse')?><!--Warehouse--> </strong></td>
                <td><strong>:</strong></td>
                <td style="width:85%;"><?php echo $extra['master']['wareHouseDescription'].' ( '.$extra['master']['wareHouseCode'].' )'; ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration')?><!--Narration--> </strong></td>
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
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code')?><!--Item Code--></th>
                <th class='theadtr' style="min-width: 40%"><?php echo $this->lang->line('common_item_description')?><!--Item Description--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom')?><!--UOM--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_current_qty')?><!--Current Qty--></th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_requested_qty')?><!--Requested Qty--></th>
                <!--<th class='theadtr' style="min-width: 15%">Value (<?php /*echo $extra['master']['companyLocalCurrency']; */?>)</th>-->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$total_count = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) { 
                    $comments = '';
                    if($val['comments']){
                        $comments=' - '.$val['comments'];
                    }
                    ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription'].''.$comments ; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['currentWareHouseStock']; ?></td>
                    <td style="text-align:right;"><?php echo $val['qtyRequested']; ?></td>
<!--                    <td style="text-align:right;"><?php /*echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); */?></td>-->
                </tr>
                <?php
                    $num ++;
                    $total_count +=$val['totalValue'];
                } 
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="6" class="text-center">'.$this->lang->line('common_no_records_found').'</td></tr>';
            } ?>
        </tbody>
<!--        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="4">Item Total (<?php /*echo $extra['master']['companyLocalCurrency']; */?>)</td>
                <td class="text-right total"><?php /*echo format_number($total_count,$extra['master']['companyLocalCurrencyDecimalPlaces']); */?></td>
            </tr>
        </tfoot>-->
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
                <td><b><?php echo $this->lang->line('common_confirmed_by')?><!--Confirmed By--> </b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn'];?></td>
            </tr>
        <?php } ?>
        <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by')?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date')?><!--Electronically Approved Date --></b></td>
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
    a_link=  "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/<?php echo $extra['master']['mrAutoID'] ?>";
    $("#a_link").attr("href",a_link);
</script>
