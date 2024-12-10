<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true,true,$approval); ?>
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
                            <h4 ><?php echo $this->lang->line('transaction_stock_transfer');?></h4><!--Stock Transfer -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:20%;"><strong><?php echo $this->lang->line('transaction_common_from_warehouse');?> </strong></td><!--Form Warehouse-->
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:30%;"><?php echo $extra['master']['form_wareHouseDescription'].' ( '.$extra['master']['form_wareHouseCode'].' ) '.$extra['master']['form_wareHouseLocation']; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('transaction_stock_transfer_number');?> </strong></td><!--Stock Transfer Number-->
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['stockTransferCode']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:20%;"><strong><?php echo $this->lang->line('transaction_common_to_warehouse');?> </strong></td><!--To Warehouse-->
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:30%;"><?php echo $extra['master']['to_wareHouseDescription'].' ( '.$extra['master']['to_wareHouseCode'].' ) '.$extra['master']['to_wareHouseLocation']; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('transaction_stock_transfer_date');?> </strong></td><!--Stock Transfer Date-->
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['tranferDate']; ?></td>

        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
            <td style="font-size: 12px;  height: 8px; padding: 1px; vertical-align: top"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;">
                <table>
                    <tr>
                        <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comment']);?></td>
                    </tr>
                </table>
            </td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['referenceNo']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <br>
    <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
        <thead>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('transaction_common_item_details');?> </th><!--Item Details-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('common_qty');?> </th><!--Qty-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" >&nbsp;</th>
        </tr>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 35%"><?php echo $this->lang->line('common_item_description');?> </th><!--Item Description-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?> </th><!--UOM-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Gross Qty </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Buckets </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">B weight </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('transaction_common_transfer');?> </th><!--Transfer-->
            <!--<th style="min-width: 10%">Received</th>-->
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('common_value');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</th><!--Value-->
        </tr>
        </thead>
        <tbody>
        <?php
        $num =1;$total_count = 0;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 14px;"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 14px; "><?php echo $val['itemDescription']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['grossQty']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['noOfUnits']; ?></td>
                    <td style="font-size: 14px;"><?php echo $val['deduction']; ?></td>
                    <td style="font-size: 14px; text-align:right;"><?php echo $val['transfer_QTY']; ?></td>
                    <!--<td style="text-align:right;"><?php /*echo $val['received_QTY']; */?></td>-->
                    <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['totalValue'],$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $total_count +=$val['totalValue'];
            }
        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="10" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" style="font-size: 14px;" colspan="8"><?php echo $this->lang->line('transaction_common_item_total');?> (<?php echo $extra['master']['companyLocalCurrency']; ?>)</td><!--Item Total-->
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($total_count,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<?php if($extra['master']['approvedYN']){ ?>
    <div class="table-responsive"><br>
        <table style="font-family:'Arial, Sans-Serif, Times, Serif'; width: 100%">
            <tbody>
            <tr>
                <td style="width:30%; font-size:12px"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%; font-size:12px"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%; font-size:12px"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td style="width:70%; font-size:12px"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
<?php } ?>

<br>
<br>

<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
        <tbody>
        <tr>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
            <td style="text-align: center; font-size: 12px;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
                Prepared By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center; font-size: 12px;">
            Approved By
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>/<?php echo $extra['master']['stockTransferAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + <?php echo $extra['master']['stockTransferAutoID'] ?> + '/ST';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>