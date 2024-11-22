<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$hideCost = getPolicyValues('HCG', 'All');
$itemBatch_policy = getPolicyValues('IB', 'All');

$show_cost_grv = getPolicyValues('HCGRV', 'All');
if($show_cost_grv == 1){
    $ele = '';
    $hidden = '';
}else {
    $ele = 'hide-elements-td2';
    $hidden = 'hide-ele';
}

echo fetch_account_review(true,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:60%;">
                        <table>
                            <tr>
                                <td>
                                    <img alt="Logo" style="height: 130px" src="<?php
                                    echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:40%;">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h3>
                                    <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                    <h4><?php echo $this->lang->line('transaction_common_grv_voucher');?> </h4><!--Goods Received Voucher-->
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('transaction_common_grv_number');?> </strong></td><!--GRV Number-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('transaction_common_grv_date');?> </strong></td><!--GRV Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvDate']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Client PO Number </strong></td><!--Reference Number-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvDocRefNo']; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('common_Location');?> </strong></td><!--Location-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['wareHouseLocation']; ?></td>
                            </tr>
                            <?php if($extra['master']['jobID']) {?>
                               <!-- <tr>
                                    <td><strong>Job Number</strong></td>
                                    <td><strong>:</strong></td>
                                    <td><?php /*echo $extra['master']['jobNo']; */?></td>
                                </tr>-->
                            <?php } ?>
                        </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <hr>
    <table>
        <tr>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td class="td"><strong><?php echo $this->lang->line('common_supplier');?> </strong></td><!--Supplier-->
                            <td><strong>:</strong></td>
                            <td class="td"><?php echo $extra['supplier']['supplierName'].' ('.$extra['supplier']['supplierSystemCode'].' ) '; ?></td>
                        </tr>
                        <tr>
                            <td style="width:15%;" class="td"><strong><?php echo $this->lang->line('common_address');?> </strong></td><!--Address-->
                            <td style="width:2%;"><strong>:</strong></td>
                            <td style="width:83%;" class="td"><?php echo $extra['supplier']['supplierAddress1']; ?></td>
                        </tr>
                        <tr>
                            <td class="td"><strong><?php echo $this->lang->line('common_phone');?> </strong></td><!--Phone-->
                            <td><strong>:</strong></td>
                            <td class="td"><?php echo $extra['supplier']['supplierTelephone']; ?></td>
                        </tr>
                        <tr>
                            <td class="td"><strong><?php echo $this->lang->line('common_fax');?> </strong></td><!--Fax-->
                            <td><strong>:</strong></td>
                            <td class="td"><?php echo $extra['supplier']['supplierFax']; ?></td>
                        </tr>
                        <tr>
                            <td class="td"><strong><?php echo $this->lang->line('common_email');?> </strong></td><!--Email-->
                            <td><strong>:</strong></td>
                            <td class="td"><?php echo $extra['supplier']['supplierEmail']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width:20%;" class="td"><strong><?php echo $this->lang->line('transaction_common_delivered_date');?> </strong></td><!--Delivered Date-->
                            <td style="width:2%;"><strong>:</strong></td>
                            <td style="width:78%;" class="td"><?php echo $extra['master']['deliveredDate']; ?></td>
                        </tr>
                        
                        <tr>
                            <td class="td"  style="width:15%;vertical-align: top"><strong><?php echo $this->lang->line('transaction_common_narration');?> </strong></td><!--Narration-->
                            <td  style="vertical-align: top"><strong>:</strong></td>
                            <td class="td" >
                                <table>
                                    <tr>
                                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['grvNarration']);?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div><br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
            <?php if ($show_cost_grv == 1) { ?>
            <tr>
                <th class='theadtr' colspan="<?php echo ($itemBatch_policy == 1 ? '6':'4')?>"><?php echo $this->lang->line('transaction_common_item_details');?></th><!--Item Details-->
                <?php if ( $hideCost == 0) { ?>
                    <th class='theadtr' colspan="3"><?php echo $this->lang->line('transaction_common_ordered_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Ordered Item-->
                    <th class='theadtr' colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? '5':'3')?>"><?php echo  $this->lang->line('transaction_common_recived_item');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th><!--Received Item-->
                <?php } else {?>
                    <th class='theadtr' colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? '4':'2')?>"> </th><!--Ordered Item-->
                <?php } ?>
            </tr>
            <?php } ?>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 10%">Part No</th>
                <!--<th class='theadtr' style="min-width: 10%">Warehouse Code</th>
                <th class='theadtr' style="min-width: 10%">Warehouse Description</th>-->
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('transaction_common_item_description');?> </th><!--Item Description-->
                <?php if ($itemBatch_policy == 1) { ?>
                    <th class='theadtr' style="min-width: 10%">Batch Number</th>
                    <th class='theadtr' style="min-width: 10%">Batch Expire Date</th>
                <?php }?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('transaction_common_uom');?></th><!--UOM-->

                <?php if ($hideCost == 0) { ?>
                   
                       
                    <?php if ($show_cost_grv == 1) { ?>
                        <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost');?> </th><!--Net Amount-->                    
                        <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_net_amount');?> </th><!--Qty-->
                    <?php } ?>
                    <th class='theadtr' style="min-width: 5%">Received <?php echo $this->lang->line('transaction_common_qty');?></th><!--Qty-->
                <?php } else {?>
                    <th class='theadtr' style="min-width: 5%">PO Qty</th><!--Qty-->

                    <th class='theadtr' style="min-width: 5%">GRV Qty</th><!--Qty-->
                <?php }?>
                <?php if ($show_cost_grv == 1) { ?>
                    <?php if ($hideCost == 0) { ?>
                        <th class='theadtr' style="min-width: 13%"><?php echo $this->lang->line('common_unit_cost');?></th><!--Unit Cost-->
                        <?php if($isGroupBasedTaxEnable == 1){ ?>
                            <th style="min-width: 5%" class="groupByTaxEnable">Tax </th><!--Qty-->
                            <th style="min-width: 5%" class="groupByTaxEnable">Tax Amount</th><!--Qty-->
                        <?php }?>
                        <th class='theadtr' style="min-width: 13%"><?php //echo $this->lang->line('common_net_amount');?></th><!--Net Amount-->
                    <?php } ?>
                <?php } ?>
            </tr>
        </thead>
        <tbody id="grv_table_body">
            <?php $requested_total = 0;$received_total = 0;
            if (!empty($extra['detail'])) {
                for ($i=0; $i < count($extra['detail']); $i++) {
                    echo '<tr>';
                    echo '<td>'.($i+1).'</td>';

                    echo '<td>'.$extra['detail'][$i]['partNo'].'</td>';
                    //echo '<td>'.$extra['detail'][$i]['wareHouseCode'].'</td>';
                    //echo '<td>'.$extra['detail'][$i]['wareHouseDescription'].'</td>';
                    echo '<td>'.$extra['detail'][$i]['itemDescription'];
                    if($itemBatch_policy==1){
                        echo '<td class="text-center">'.$extra['detail'][$i]['batchNumber'].'</td>';
                        echo '<td class="text-center">'.$extra['detail'][$i]['batchExpireDate'].'</td>';
                    }

                    if(!empty($extra['detail'][$i]['comment']) && empty($extra['detail'][$i]['partNo']))
                    {
                        echo  ' - '. $extra['detail'][$i]['comment'];
                    }
                    else if(!empty($extra['detail'][$i]['comment']) && !empty($extra['detail'][$i]['partNo']))
                    {
                        echo  ' - '. $extra['detail'][$i]['comment'] . ' - ' . 'Part No : ' . $extra['detail'][$i]['partNo'];
                    }else if(!empty($extra['detail'][$i]['partNo']))
                    {
                        echo ' Part No : ' . $extra['detail'][$i]['partNo'];
                    }


                    '</td>';
                    echo '<td class="text-center">'.$extra['detail'][$i]['unitOfMeasure'].'</td>';
                   
                    if ($show_cost_grv == 1) {
                        if ($hideCost == 0) {
                            echo '<td class="text-right">'.format_number($extra['detail'][$i]['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right">'.format_number(($extra['detail'][$i]['requestedQty']*$extra['detail'][$i]['requestedAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        }
                    }

                    if($approval == 1){

                        if($isPrint == 0){
                            if($inspection_level_access ==1){
                                
                                echo '<td class="text-right"> <a onclick="edit_item_grv_inspection(' . $extra['detail'][$i]['grvDetailsID'] . ',\'' . $extra['detail'][$i]['purchaseOrderMastertID'] . '\');"><span class="glyphicon glyphicon-pencil"></span></a>'.$extra['detail'][$i]['receivedQty'].'</td>';
                            }
                        }

                    }else{
                        echo '<td class="text-right">'.$extra['detail'][$i]['receivedQty'].'</td>';
                    }
                    

                    if ($show_cost_grv == 1) {
                    if ($hideCost == 0) {
                        echo '<td class="text-right">' . format_number($extra['detail'][$i]['receivedAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        
                           
                    if($isGroupBasedTaxEnable == 1){ 
                        echo ' <td style="min-width: 5%" class="groupByTaxEnable">'.$extra['detail'][$i]['Description'].'</td>';

                        if($extra['detail'][$i]['taxAmount'] > 0) {
                            echo ' <td style="min-width: 5%;text-align:right" class="groupByTaxEnable">
                                <a onclick="open_tax_dd(null,'.$extra['detail'][$i]['grvAutoID'].',\'GRV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $extra['detail'][$i]['grvDetailsID'].', \'srp_erp_grvdetails\',\'grvDetailsID\',0,1) ">'. number_format($extra['detail'][$i]['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>          
                            </td>';
                        }else {
                            echo '<td class="text-right">' . format_number(($extra['detail'][$i]['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }



                        echo '<td class="text-right">' . format_number((($extra['detail'][$i]['receivedTotalAmount']))+($extra['detail'][$i]['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }else { 
                            echo '<td class="text-right">' . format_number((($extra['detail'][$i]['receivedTotalAmount'])), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }
                    
                        }
                    }
                    echo '</tr>';
                    $requested_total += ($extra['detail'][$i]['requestedQty']*$extra['detail'][$i]['requestedAmount']);
                    if($isGroupBasedTaxEnable == 1){ 
                    $received_total += (($extra['detail'][$i]['receivedTotalAmount'])+($extra['detail'][$i]['taxAmount']));
                    }else{ 
                        $received_total += ($extra['detail'][$i]['receivedTotalAmount']);
                    }
                }
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="10" class="text-center"><b>'.$norecfound.'</b></td></tr>';
            }
            ?>
            <!--No Records Found-->
        </tbody>
        <?php if($show_cost_grv == 1){ ?>
        <?php if ($hideCost == 0) { ?>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6"><?php echo $this->lang->line('transaction_ordered_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Ordered Item Total-->
                <td class="text-right total"><?php echo format_number($requested_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total" colspan="<?php echo ($isGroupBasedTaxEnable == 1?'4':'2')?>"><?php echo $this->lang->line('transaction_recived_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Received Item Total-->
                <td class="text-right total"><?php echo format_number($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
        <?php } ?>
        <?php } ?>
    </table>
</div>
<br>
<?php
    $document = array('GRV','GRV-ADD');
    $rcmApplicableYN = 0;
    $data['documentCode'] ="".join("' , '", $document)."";
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['grvAutoID'];
    
    if($extra['rcmApplicableYnpolicy'] ==1){
        $rcmApplicableYN = 1;
    }
    
    $data['isRcmDocument'] = $rcmApplicableYN;

    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);

?>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
           
           <td style="width:70%;">
            <?php
            if (!empty($extra['addon'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <td class='theadtr' colspan="<?php echo ($isGroupBasedTaxEnable ==1 ? '8':'6')?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('transaction_common_add_on_details');?></strong></td><!--Addons Details-->
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('transaction_common_add_on_category');?></th><!--Addon Catagory-->
                                <th class='theadtr'><?php echo $this->lang->line('common_supplier');?></th><!--Supplier-->
                                <th class='theadtr'><?php echo $this->lang->line('transaction_common_referenc_no');?> </th><!--Reference No-->
                                <?php   if($isGroupBasedTaxEnable == 1){ ?>
                                    <th class='theadtr'>Tax</th>
                                    <th class='theadtr'>Tax Amount</th>
                                <?php }?>
                                <th class='theadtr'><?php echo $this->lang->line('transaction_common_booking_amount');?></th><!--Booking Amount-->
                                <th class='theadtr'><?php echo $this->lang->line('common_amount');?> ( <?php echo $extra['master']['transactionCurrency'];?> )</th><!--Amount-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $x=1; $total_amount=0;
                            foreach ($extra['addon'] as $value) {
                                echo '<tr>';
                                echo '<td>'.$x.'.</td>';
                                echo '<td>'.$value['addonCatagory'].'</td>';
                                echo '<td>'.$value['supplierName'].'</td>';
                                echo '<td>'.$value['referenceNo'].'</td>';
                                if ($isGroupBasedTaxEnable == 1) {
                                    echo '<td>' . $value['Description'] . '</td>';
                                    if($value['taxAmount'] > 0) {
                                        echo '<td style="text-align: right">

  <a onclick="open_tax_dd(null,'.$value['grvAutoID'].',\'GRV-ADD\','. $value['bookingCurrencyDecimalPlaces'].' ,'. $value['id'].', \'srp_erp_grv_addon\',\'id\',0,1) ">'. number_format($value['taxAmount'],$value['bookingCurrencyDecimalPlaces']).'</a></td>';
                                    }else {
                                        echo '<td style="text-align: right">'.format_number($value['taxAmount'],$value['bookingCurrencyDecimalPlaces']).'</td>';
                                    }



                                }
                                echo '<td class="text-right">'.$value['bookingCurrency'].' : '.format_number($value['bookingCurrencyAmount'],$value['bookingCurrencyDecimalPlaces']).'</td>';
                            if ($isGroupBasedTaxEnable == 1) {
                                echo '<td class="text-right">' . format_number($value['total_amount'] + $value['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            }else {
                                echo '<td class="text-right">' . format_number($value['total_amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            }
                                echo '</tr>';
                                $x++;
                            if ($isGroupBasedTaxEnable == 1) {
                                $total_amount += ($value['total_amount']+$value['taxAmount']);
                            }else {
                                $total_amount += $value['total_amount'];
                            }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo ($isGroupBasedTaxEnable ==1 ? '7':'5')?>" class="text-right sub_total"><?php echo $this->lang->line('common_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Total-->
                                <td class="text-right total"><?php echo format_number($total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                        </tfoot>
                    </table>
            <?php } ?>
           </td>
        </tr>
    </table>
</div>
<br>


<?php if($extra['master']['approvedYN']){ ?>
<?php
if ($signature) { ?>
    <?php
    if ($signature['approvalSignatureLevel'] <= 2) {
        $width = "width: 40%";
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
    a_link=  "<?php echo site_url('Grv/load_grv_conformation'); ?>/<?php echo $extra['master']['grvAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + <?php echo $extra['master']['grvAutoID'] ?> + '/GRV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>