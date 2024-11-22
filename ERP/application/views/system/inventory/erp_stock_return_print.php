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
                                <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                                <h4><?php echo $this->lang->line('transaction_purchase_return');?> </h4><!--Purchase Return-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_purchase_return_number');?> </strong></td><!--Purchase Return Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['stockReturnCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_purchase_return_date');?> </strong></td><!--Purchase Return Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['returnDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_supplier_name');?> </strong></td><!--Supplier Name-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['supplierSystemCode'].' ( '.$extra['master']['supplierName'].' )'; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?> </strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['referenceNo']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_common_warehouse_location');?> </strong></td><!--Warehouse Location-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['wareHouseLocation']; ?></td>
                        </tr>
                    </table>
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
                <th class='theadtr' colspan="4"><?php echo $this->lang->line('transaction_common_item_details');?></th><!--Item Details-->
                <th class='theadtr' colspan="3"><?php echo $this->lang->line('transaction_common_qty');?> </th><!--Qty-->
                <th class='theadtr' colspan="3"><?php echo $this->lang->line('common_amount');?> </th><!--Qty-->
                <!-- <th class='theadtr' >&nbsp; </th> -->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('common_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_cost');?></th><!--Cost-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_return');?></th><!--Return-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_tax');?></th><!--Tax-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_tax_total');?></th><!--Tax Total-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$total_count = 0;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <?php if($val['type'] == 'GRV'){ ?>
                    <td style="text-align:center;"><a target="_blank" onclick="requestPageView_model('GRV',<?php echo $val['grvAutoID']; ?>)"><?php echo $val['itemSystemCode']; ?></td>
                    <?php }else{ ?>
                        <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                    <?php } ?>

                    <td><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['currentlWacAmount']; ?></td>
                    <td style="text-align:right;"><?php echo $val['return_QtyFormated']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($val['totalValue'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo $val['taxDescription']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($val['taxAmount'] ?? 0, $extra['master']['transactionCurrencyDecimalPlaces'] ?? 0); ?></td>
                    <td style="text-align:right;"><?php echo number_format(($val['totalValue'] + $val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $total_count += ($val['totalValue'] + $val['taxAmount']);
                }
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="9"><?php echo $this->lang->line('transaction_common_item_total');?> </td><!--Item Total-->
                <td class="text-right total"><?php echo number_format($total_count, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
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
                <td><b><?php echo $this->lang->line('common_confirmed_by');?><!--Confirmed By--> </b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn'];?></td>

            </tr>
        <?php } ?>
            <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By -->
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
    a_link=  "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/<?php echo $extra['master']['stockReturnAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + <?php echo $extra['master']['stockReturnAutoID'] ?> + '/SR';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

    </script>
