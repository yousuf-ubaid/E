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
                            <h4>Stock Counting</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Code </strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['stockCountingCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Document Date </strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['stockCountingDate']; ?></td>
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
            <td><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
            <td><strong>:</strong></td>
            <td colspan="4"><?php echo $extra['master']['comment']; ?></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class='theadtr' colspan="3"><?php echo $this->lang->line('transaction_common_item_details');?> </th><!--Item Details-->
            <th class='theadtr' colspan="3"><?php echo $this->lang->line('common_previous');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Previous-->
            <th class='theadtr' colspan="2">Secondary UOM</th>
            <th class='theadtr' colspan="2"><?php echo $this->lang->line('transaction_common_currenct');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Current-->
            <th class='theadtr' colspan="3">Adjusted (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Adjusted-->
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 3%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
            <th class='theadtr' style="min-width: 20%"><?php echo $this->lang->line('common_item_description');?> </th><!--Item Description-->
            <th class='theadtr' style="min-width: 7%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->

            <th class='theadtr' style="min-width: 5%">UOM </th>
            <th class='theadtr' style="min-width: 10%">QTY </th>

            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock');?> </th><!--Stock-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?> </th><!--Wac-->
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_amount');?> </th><!--Amount-->
        </tr>
        </thead>
        <tbody>
        <?php
        $num =1;$total_count = 0;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['previousWareHouseStock'],2);?></td>
                    <td style="text-align:right;"><?php echo format_number($val['previousWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:center;"><?php echo $val['secuom']; ?></td>
                    <td style="text-align:right;"><?php echo $val['SUOMQty']; ?></td>
                    <td style="text-align:right;"><?php echo $val['currentWareHouseStock']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['currentWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['adjustmentWareHouseStock'],2); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['adjustmentWac'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $total_count +=$val['totalValue'];
            }
        }else{
            $norec=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="14" class="text-center">'.$norec.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="12">Adjusted Item Total (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td>
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
                <td style="width:70%;"> <?php echo $extra['master']['confirmedYNn']; ?></td>
            </tr>
        <?php }?>
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
    a_link=  "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/<?php echo $extra['master']['stockCountingAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + <?php echo $extra['master']['stockCountingAutoID'] ?> + '/SCNT';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

</script>
