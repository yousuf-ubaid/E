<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(true,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                                <h4><?php echo $this->lang->line('transaction_stock_adjustment');?></h4><!--Stock Adjustment-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_adjustment_code');?> </strong></td><!--Adjustment Code-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['stockAdjustmentCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_adjustment_date');?> </strong></td><!--Adjustment Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['stockAdjustmentDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
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
    <table style="width: 100%;font-size:12px;">
        <tbody>
            <tr>
                <td style="width:20%;"><strong><?php echo $this->lang->line('common_warehouse');?> </strong></td><!--Warehouse-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $extra['master']['wareHouseDescription'].' ( '.$extra['master']['wareHouseCode'].' ) '.$extra['master']['wareHouseLocation']; ?></td>
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
        <!-- <thead>
            <tr>
                <th class='theadtr' colspan="4">Item Details</th>
                <th class='theadtr' >Qty </th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%">Item Code</th>
                <th class='theadtr' style="min-width: 55%">Item Description</th>
                <th class='theadtr' style="min-width: 10%">UOM</th>
                <th class='theadtr' style="min-width: 15%">Adjustment</th>
            </tr>
        </thead> -->
        <thead>
            <tr>
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('transaction_common_item_details');?> </th><!--Item Details-->
                <th class='theadtr' colspan="2"><?php echo $this->lang->line('common_previous');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Previous-->
                <th class='theadtr' colspan="2"><?php echo $this->lang->line('transaction_common_currenct');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Current-->
                <?php
                if($extra['master']['adjustmentType']!=1){
                ?>
                <th class='theadtr' colspan="6"><?php echo $this->lang->line('transaction_common_adjusted');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Adjusted-->
                <?php } else { ?>
                <th class='theadtr' colspan="3"><?php echo $this->lang->line('transaction_common_adjusted');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)
                <?php }?>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 3%">#</th>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
                <th class='theadtr' style="min-width: 20%"><?php echo $this->lang->line('common_item_description');?> </th><!--Item Description-->
                <th class='theadtr' style="min-width: 7%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
              <!--  <th class='theadtr' style="min-width: 10%">Net Qty </th><!--Wac-->

                <?php
                if($extra['master']['adjustmentType']!=1){
                ?>

                  <th class='theadtr' style="min-width: 5%">  Gross Qty</th><!--Stock-->
                <th class='theadtr' style="min-width: 10%">Buckets</th><!--Wac-->
                <th class='theadtr' style="min-width: 10%"> B weight </th><!--Amount-->

                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_amount');?> </th><!--Amount-->
                <?php } else {?>

                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_amount');?> </th><!--Amount-->
                <?php }?>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$total_count = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) {
                    $this->db->select('currentStock');
                    $this->db->where('itemAutoID', $val['itemAutoID']);
                    $this->db->from('srp_erp_itemmaster');
                    $itemMAster = $this->db->get()->row_array();
                    ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <?php
                    if($extra['master']['adjustmentType']==1){
                        ?>
                        <td style="text-align:right;"><?php echo $val['previousStock']; ?></td>
                        <?php
                    }else{
                        ?>
                        <td style="text-align:right;"><?php echo $val['previousWareHouseStock']; ?></td>
                        <?php
                    }
                    ?>
                    <td style="text-align:right;"><?php echo format_number($val['previousWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <?php
                    if($extra['master']['adjustmentType']==1){
                        ?>
                        <td style="text-align:right;"><?php echo $itemMAster['currentStock']; ?></td>
                        <?php
                    }else{
                        ?>
                        <td style="text-align:right;"><?php echo $val['currentWareHouseStock']; ?></td>
                        <?php
                    }
                    ?>
                    <td style="text-align:right;"><?php echo format_number($val['currentWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                 <!--   <td style="text-align:right;"><?php /*echo ($val['grossQty'] - $val['deduction']); */?></td>-->
                <?php  if($extra['master']['adjustmentType']!=1){?>
                    <td style="text-align:right;"> <?php echo $val['grossQty']; ?></td>
                    <td style="text-align:right;">  <?php echo $val['noOfUnits']; ?></td>
                    <td style="text-align:right;">  <?php echo $val['deduction']; ?></td>
                        <?php }?>
                    <?php
                    if($extra['master']['adjustmentType']==1){
                        ?>
                        <td style="text-align:right;">0</td>
                        <?php
                    }else{
                        ?>
                        <td style="text-align:right;"><?php echo $val['adjustmentWareHouseStock']; ?></td>
                        <?php
                    }
                    ?>
                    <td style="text-align:right;"><?php echo format_number($val['adjustmentWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $total_count +=$val['totalValue'];
                } 
            }else{
                $norec=$this->lang->line('common_no_records_found');

                    if($extra['master']['adjustmentType']!=1) {

                        echo '<tr class="danger"><td colspan="14" class="text-center">' . $norec . '<!--No Records Found--></td></tr>';
                    }else {
                        echo '<tr class="danger"><td colspan="11" class="text-center">' . $norec . '<!--No Records Found--></td></tr>';
                    }

            } ?>
        </tbody>
        <tfoot>
            <tr>
                <?php if ($extra['master']['adjustmentType']!=1){?>
                <td class="text-right sub_total" colspan="13"><?php echo $this->lang->line('transaction_adjustment_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td><!--Adjustment Item Total-->
                <?php } else {?>
                <td class="text-right sub_total" colspan="10"><?php echo $this->lang->line('transaction_adjustment_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td>
                <?php } ?>
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
            <td style="width:30%;"><b>Confirmed By </b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"> <?php echo $extra['master']['confirmedYNn']; ?></td>
        </tr>
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
        </tbody>
    </table>
</div>
<br>
<br>
<br>

<div class="table-responsive">
    <table style="width: 100%; font-family:'Times New Roman'; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 13px;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 13px;">
                Approved By
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php if($extra['master']['approvedYN']){ ?>
<?php } ?>

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
    a_link=  "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/<?php echo $extra['master']['stockAdjustmentAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + <?php echo $extra['master']['stockAdjustmentAutoID'] ?> + '/SA';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

</script>
