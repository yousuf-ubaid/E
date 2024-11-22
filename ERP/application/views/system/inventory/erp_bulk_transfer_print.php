<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true,true,$approval);?>
<div class="col-md-12" style="margin-bottom: 15px">
    <div class="">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:60%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php
                                echo mPDFImage.$this->common_data['company_data']['company_logo'];  ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').';  ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4><?php echo $this->lang->line('sales_maraketing_masters_customer_sales_prices_details');?><!--Customer Sales Price Details--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_code');?><!--Document Code--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['stockTransferCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['tranferDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['referenceNo']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('transaction_common_from_warehouse');?><!--From Warehouse--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['form_wareHouseDescription'] . ' | ' . $master['form_wareHouseCode']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <hr style="margin-top: 0%">
    <div class="table-responsive">
        <table id="warehoouseTranferTbl" class="<?php echo table_class(); ?>" style="width: 100%">
            <thead>
            <tr>
                <th style="min-width: 10px" rowspan="2">#</th>
                <th style="min-width: 100px;" rowspan="2"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
                <th style="min-width: 150px" rowspan="2"><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                <th style="min-width: 10%" rowspan="2"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->

                <th style="min-width: 50px">From Location</th><!--UOM-->
                <th colspan="<?php echo count($extra['toWarehouse'])?>">Issue Qty to Location</th>
                <th style="min-width: 20px" rowspan="2">Total Issue Qty</th>
                <th style="min-width: 20px" rowspan="2">Balance In Hand</th>
            </tr>
            <tr>
                <th style="min-width: 5%">In hand Qty</th>
                <?php if($extra['toWarehouse']) {
                    foreach ($extra['toWarehouse'] as $warehouseid) {
                        $warehouse = load_warehouses($warehouseid);
                        echo '<th style="min-width: 100px" title="' . $warehouse['wareHouseDescription'] . ' | ' . $warehouse['wareHouseLocation'] . '">' . $warehouse['wareHouseCode'] . '</th>';
                    }
                }?>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <?php if(!empty($extra['details'])) {
                $a = 1;
                foreach ($extra['details'] as $det) {
                    echo '<tr>';
                    echo '<td>' . $a . '</td>';
                    echo '<td>' . $det['itemSystemCode'] . '</td>';
                    echo '<td>' . $det['itemDescription'] . '</td>';
                    echo '<td>' . $det['unitOfMeasure'] . '</td>';
                    echo '<td>' . $det['currentWareHouseStock'] . '</td>';
                    if ($extra['toWarehouse']) {
                        $b = 1;
                        $totalQty = 0;
                        foreach ($extra['toWarehouse'] as $items) { ?>
                            <td style="text-align:center;"><?php echo $det[$items . '_qty'] ?></td>
                            <?php
                            $totalQty += $det[$items . '_qty'];
                            $b++;
                        }
                    }
                    echo '<td>' . $totalQty . '</td>';
                    echo '<td>' . ($det['currentWareHouseStock'] - $totalQty) . '</td>';
                    echo '</tr>';

                    $a++;
                }

            } else { ?>
                <tr class="danger">
                    <td colspan="<?php echo count($extra['toWarehouse']) + 7?>" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="table-responsive">
    <hr>
    <table style="width: 100%">
        <tbody>
        <?php if ($ALD_policyValue == 1) { 
            $created_user_designation = designation_by_empid($master['createdUserID']);
            $confirmed_user_designation = designation_by_empid($master['confirmedByEmpID']);
            ?>
                <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $master['createdDateTime']; ?></td>
            </tr>
        <?php if($master['confirmedYN']==1){ ?>
            <tr>
                <td style="width:30%;"><b>Confirmed By </b></td>
                <td><strong>: </strong></td>
                <td style="width:70%;"><?php echo $master['confirmedYNn'] . ' (' . $created_user_designation['DesDescription'] . ')';?></td>
            </tr>
        <?php } ?>
            <?php if(!empty($approver_details)) {
                foreach ($approver_details as $val) {
                    echo '<tr>
                            <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                            <td><strong>:</strong></td>
                            <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                        </tr>';
                }
            }
        } else {?>
                <tr>
                    <td style="width:30%;"><b>
                            <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $master['createdUserName']; ?> on <?php echo $master['createdDateTime']; ?></td>
                </tr>
            <?php if($master['confirmedYN']==1){ ?>
                <tr>
                    <td style="width:30%;"><b>Confirmed By </b></td>
                    <td><strong>: </strong></td>
                    <td style="width:70%;"><?php echo $master['confirmedYNn'];?></td>
                </tr>
            <?php } ?>
            <?php if($master['approvedYN']){ ?>
                <tr>
                    <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                    <td><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $master['approvedbyEmpName']; ?></td>
                </tr>
                <tr>
                    <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                    <td><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $master['approvedDate']; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<br>
<br>
<br>
<?php if($master['approvedYN']){ ?>
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
    var leftAlign = 0;
    if(<?php echo count($extra['toWarehouse'])?> > 4){
        leftAlign = 5;
    }

    $('#warehoouseTranferTbl').tableHeadFixer({
        head: true,
        foot: true,
        left: leftAlign,
        right: 0,
        'z-index': 10
    });

    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/<?php echo $stockTransferAutoID ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + <?php echo $stockTransferAutoID ?> + '/STB';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);

</script>
