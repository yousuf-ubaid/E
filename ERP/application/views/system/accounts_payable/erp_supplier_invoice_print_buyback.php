<?php echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$gran_total=0;
$tax_transaction_total = 0;
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
                            <h4 ><?php echo $this->lang->line('accounts_payable_supplier_invoice');?><!--Supplier Invoice -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
        <tbody>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 2px; width:15%;"><strong> <?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px; width:2%;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px; width:33%;"> <?php echo $extra['supplier']['supplierName'].' ( '.$extra['supplier']['supplierSystemCode'].' )'; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('accounts_payable_supplier_invoice_number');?><!--Supplier Invoice Number--></strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><?php echo $extra['master']['bookingInvCode']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong> <?php echo $this->lang->line('accounts_payable_supplier_address');?><!--Supplier Address--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"> <?php echo $extra['supplier']['supplierAddress1']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><?php echo $extra['master']['bookingDate']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('accounts_payable_supplier_invoice_no');?><!--Supplier Invoice No--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"> <?php echo $extra['master']['supplierInvoiceNo']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><?php echo $extra['master']['RefNo']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('accounts_payable_invoice_due_date');?><!--Invoice Due Date--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"> <?php echo $extra['master']['invoiceDueDate']; ?></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong><?php echo $this->lang->line('common_invoice_date');?><!--Invoice Date--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px;"> <?php echo $extra['master']['invoiceDate']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 12px;  height: 8px; padding: 2px; vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 2px; vertical-align: top"><strong>:</strong></td>
                <td>
                    <table>
                        <tr>
                            <td style="font-size: 12px;  height: 8px; padding: 2px;"><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['comments']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['comments']; ?>
                </td>
       </tbody>
    </table>
</div>
<br>
<?php if($extra['master']['invoiceType']!='GRV Base'){
    $gran_total=0; $tax_Local_total = 0;$tax_supplier_total = 0;
    $transaction_total = 0;$Local_total = 0;$supplier_total = 0;
    ?>
    <?php if (!empty($extra['detail'])) { ?>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="3"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 14%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 32%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 15%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 12%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 12%"><?php echo $this->lang->line('common_discount');?><!--Discount--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 12%"><?php echo $this->lang->line('common_net_amount');?><!--Net Amount--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
        </thead>
        <tbody id="grv_table_body">
            <?php
            if (!empty($extra['detail'])) {
                for ($i=0; $i < count($extra['detail']); $i++) { 
                    echo '<tr>';
                    echo '<td style="font-size: 14px;">'.($i+1).'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLCode'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['GLDescription'].' - '.$extra['detail'][$i]['description'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['detail'][$i]['segmentCode'].'</td>';
                    echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['detail'][$i]['transactionAmount']+$extra['detail'][$i]['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    echo '<td style="font-size: 14px;" class="text-right">('.format_number($extra['detail'][$i]['discountPercentage'], 2).'%) '.format_number($extra['detail'][$i]['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';

                    echo '</tr>';

                    $gran_total             += ($extra['detail'][$i]['transactionAmount']);
                    $transaction_total      += ($extra['detail'][$i]['transactionAmount']);
                    //$Local_total            += ($extra['detail'][$i]['companyLocalAmount']);
                    //$supplier_total         += ($extra['detail'][$i]['supplierAmount']);
                    $tax_transaction_total  += ($extra['detail'][$i]['transactionAmount']);
                    $tax_Local_total        += ($extra['detail'][$i]['companyLocalAmount']);
                    $tax_supplier_total     += ($extra['detail'][$i]['supplierAmount']);
                }
            }else{
                $NoRecordsFound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="9" class="text-center"><b>'.$NoRecordsFound.'<!--No Records Found--></b></td></tr>';
            }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total"  style="font-size: 14px;" colspan="6"><?php echo $this->lang->line('common_gl_total');?><!--GL Total--> </td>
                <td class="text-right sub_total"  style="font-size: 14px;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($supplier_total,$extra['master']['supplierCurrencyDecimalPlaces']); ?></td> -->
                <!-- <td class="sub_total"> &nbsp; </td> -->
            </tr>
        </tfoot>
    </table>
</div>
    <?php }?>
<?php } if($extra['master']['invoiceType']=='GRV Base'){ ?>
    <?php if (!empty($extra['grv_detail'])) { ?>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4"><?php echo $this->lang->line('accounts_payable_grv_details');?><!--GRV Details--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 3%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_grv_code');?><!--GRV Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_grv_date');?><!--GRV Date--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 40%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 12%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                <!-- <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 10%">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" style="min-width: 10%">Supplier (<?php //echo $extra['master']['supplierCurrency']; ?>)</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $transaction_total = 0;$Local_total = 0;$supplier_total = 0;
            if (!empty($extra['grv_detail'])) {
                $gran_total=0;

                $tax_Local_total=0;
                $tax_supplier_total=0;
                for ($i=0; $i < count($extra['grv_detail']); $i++) {
                    echo '<tr>';
                    echo '<td style="font-size: 14px;">'.($i+1).'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['grv_detail'][$i]['grvPrimaryCode'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['grv_detail'][$i]['grvDate'].'</td>';
                    echo '<td style="font-size: 14px;">'.$extra['grv_detail'][$i]['description'].'</td>';
                    //echo '<td class="text-center">'.$extra['grv_detail'][$i]['segmentCode'].'</td>';
                    echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['grv_detail'][$i]['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    //echo '<td class="text-right">'.format_number($extra['grv_detail'][$i]['companyLocalAmount'],$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                    //echo '<td class="text-right">'.format_number($extra['grv_detail'][$i]['supplierAmount'],$extra['master']['supplierCurrencyDecimalPlaces']).'</td>';
                    echo '</tr>';
                    $gran_total             += ($extra['grv_detail'][$i]['transactionAmount']);
                    $transaction_total      += ($extra['grv_detail'][$i]['transactionAmount']);
                    //$Local_total            += ($extra['grv_detail'][$i]['companyLocalAmount']);
                    //$supplier_total         += ($extra['grv_detail'][$i]['supplierAmount']);
                    $tax_transaction_total  += ($extra['grv_detail'][$i]['transactionAmount']);
                    $tax_Local_total        += ($extra['grv_detail'][$i]['companyLocalAmount']);
                    $tax_supplier_total     += ($extra['grv_detail'][$i]['supplierAmount']);
                }
            }else{
                $norecordfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="7" class="text-center"><b>'.$norecordfound.'<!--No Records Found--></b></td></tr>';
            }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="4"><?php echo $this->lang->line('accounts_payable_grv_total');?><!--GRV Total--> </td>
                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right total"><?php //echo format_number($supplier_total,$extra['master']['supplierCurrencyDecimalPlaces']); ?></td> -->
                <!--<td class="sub_total"> &nbsp; </td>-->
                <!--<td class="sub_total"> &nbsp; </td>-->
            </tr>
        </tfoot>
    </table>
</div>
<?php } ?>
<?php } ?>

<?php

if (!empty($extra['Itemdetail'])) { ?>
    <br><br>
    <div class="table-responsive">
        <table class="table table-condensed" style="font-family:Arial, Sans-Serif, Times, Serif;">
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="5">Item Details</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="3"><?php echo $this->lang->line('common_amount'); ?> (<?php echo $extra['master']['transactionCurrency']; ?>)</th><!--Amount-->
            </tr>
            <tr>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%"><?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%"><?php echo $this->lang->line('common_item_description'); ?><!--Item Description--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $item_total = 0;
            $discountAmount = 0;
            if (!empty($extra['Itemdetail'])) {
                foreach ($extra['Itemdetail'] as $val) { ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px;"><?php echo $val['itemSystemCode']; ?></td>
                        <td style="font-size: 14px;"><?php echo $val['itemDescription']?>
                            <?php if(!empty($val['description']) && empty($val['partNo']))
                            {
                                echo ' - ' .  $val['description'];
                            }else if(!empty($val['description']) && !empty($val['partNo']))
                            {
                                echo ' - ' .  $val['description'] . ' - ' .'Part No : ' .$val['partNo'];
                            }
                            else if(!empty($val['partNo']))
                            {
                                echo  ' - ' . 'Part No : ' .$val['partNo'];
                            }
                            ?>

                        </td>
                        <td style="font-size: 14px;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo $val['requestedQty']; ?></td>

                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($val['discountAmount']){ ?>
                            <td style="font-size: 14px; text-align: right;"><?php echo  format_number(($val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'( '.format_number($val['discountPercentage'],2). '% )' ?></td>
                        <?php  } else { ?>
                            <td><?php echo '0' ?></td>
                        <?php }?>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number(($val['transactionAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $item_total += $val['transactionAmount'];
                    $discountAmount += $val['discountAmount'];
                    $gran_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            } else {

                $norecordsfound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="8" class="text-center">' . $norecordsfound . '</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="font-size: 14px;" colspan="7">Item Total (<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                <td class="text-right total" style="font-size: 14px;"><?php echo format_number(($item_total), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>

<?php if (!empty($extra['podetail'])) { ?>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-condensed" style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="4">PO Details</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="3">Ordered Item <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="3">Received Item <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></th>
        </tr>
        <tr>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Item Code</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%">Item Description </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">UOM</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">Qty</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Unit Cost </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Net Amount </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">Qty </th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%">Unit Cost</th>
            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 13%">Net Amount</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;$received_total = 0;
        if (!empty($extra['podetail'])) {
            for ($i=0; $i < count($extra['podetail']); $i++) {
                echo '<tr>';
                echo '<td style="font-size: 14px;">'.($i+1).'</td>';
                echo '<td style="font-size: 14px;">'.$extra['podetail'][$i]['itemSystemCode'].'</td>';
                echo '<td style="font-size: 14px;">'.$extra['podetail'][$i]['purchaseOrderCode'];

                    if(!empty($extra['podetail'][$i]['itemDescription']) && empty($extra['podetail'][$i]['partNo']))
                    {
                      echo  ' - '. $extra['podetail'][$i]['itemDescription'];
                    }
                    else if(!empty($extra['podetail'][$i]['itemDescription']) && !empty($extra['podetail'][$i]['partNo']))
                    {
                        echo  ' - '. $extra['podetail'][$i]['itemDescription'] . ' - ' . 'Part No : ' . $extra['podetail'][$i]['partNo'];
                    }else if(!empty($extra['podetail'][$i]['partNo']))
                    {
                        echo 'Part No : ' . $extra['podetail'][$i]['partNo'];
                    }
                    '</td>';
                echo '<td style="font-size: 14px;">'.$extra['podetail'][$i]['unitOfMeasure'].'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.$extra['podetail'][$i]['orderedQty'].'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['podetail'][$i]['orderedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.format_number(($extra['podetail'][$i]['orderedQty']*$extra['podetail'][$i]['orderedAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.$extra['podetail'][$i]['requestedQty'].'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.format_number($extra['podetail'][$i]['unittransactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                echo '<td style="font-size: 14px;" class="text-right">'.format_number(($extra['podetail'][$i]['transactionAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                echo '</tr>';
                $requested_total += ($extra['podetail'][$i]['orderedQty']*$extra['podetail'][$i]['orderedAmount']);
                $received_total += ($extra['podetail'][$i]['transactionAmount']);
                $gran_total += $extra['podetail'][$i]['transactionAmount'];
                $tax_transaction_total += $extra['podetail'][$i]['transactionAmount'];
            }
        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="10" class="text-center"><b>'.$norecfound.'</b></td></tr>';
        }
        ?>
        <!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" style="font-size: 14px;" colspan="6"><?php echo $this->lang->line('transaction_ordered_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Ordered Item Total-->
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($requested_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right sub_total" style="font-size: 14px;" colspan="2"><?php echo $this->lang->line('transaction_recived_item_total');?> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td><!--Received Item Total-->
            <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<?php } ?>

<?php if ($extra['master']['generalDiscountPercentage']>0) { ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%;">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%; " class="table table-condensed" style="font-family:Arial, Sans-Serif, Times, Serif;">
                        <thead>
                        <tr>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">Type </th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">Discount </th>
                            <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</tr>
                        </thead>
                        <tbody>
                        <?php
                        $discountamnt=($gran_total*$extra['master']['generalDiscountPercentage'])/100;
                        echo '<tr>';
                        echo '<td style="font-size: 14px;">Discount.</td>';
                        echo '<td style="font-size: 14px;">'. number_format($extra['master']['generalDiscountPercentage'], 2).'%</td>';
                        echo '<td style="font-size: 14px;" class="text-right">'. number_format($discountamnt,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        echo '</tr>';


                        $gran_total=$gran_total-$discountamnt;
                        $tax_transaction_total=$tax_transaction_total-$discountamnt;
                        ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php } ?>


<div class="table-responsive">
    <table style="width: 100%">
        <tr>

           <td style="width:60%;">
            <?php  
            if (!empty($extra['tax'])) { ?>
                    <table style="width: 100%" class="table table-condensed" style="font-family:Arial, Sans-Serif, Times, Serif;">
                        <thead>
                            <tr>
                                <td style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('accounts_payable_tax_details');?><!--Tax Details--></strong></td>
                            </tr>
                            <tr>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">#</th>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_supplier');?><!--Supplier--></th>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                                <!-- <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black">Supplier (<?php //echo $extra['master']['supplierCurrency']; ?>)</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $x=1; $tr_total_amount=0;$cu_total_amount=0;$su_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                echo '<tr>';
                                echo '<td style="font-size: 14px;">'.$x.'.</td>';
                                echo '<td style="font-size: 14px;">'.$value['taxShortCode'].'</td>';
                                echo '<td style="font-size: 14px;">'.$value['supplierName'].'</td>';
                                echo '<td style="font-size: 14px;" class="text-right">'.$value['taxPercentage'].' % </td>';
                                echo '<td style="font-size: 14px;" class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_supplier_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total     +=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                                $su_total_amount+=(($value['taxPercentage']/ 100) * $tax_supplier_total);
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="font-size: 14px;" class="text-right sub_total"><?php echo $this->lang->line('common_tax_total');?><!--Tax Total--> </td>
                                <td class="text-right sub_total" style="font-size: 14px;"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <!-- <td class="text-right total"><?php //echo format_number($cu_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right total"><?php //echo format_number($su_total_amount,$extra['master']['supplierCurrencyDecimalPlaces']); ?></td> -->
                            </tr>
                        </tfoot>
                    </table>
            <?php } ?>           
           </td>
        </tr>
    </table>
</div>
<div class="table-responsive" style="font-family:'Arial, Sans-Serif, Times, Serif';">
    <h5 class="text-right" style="font-size: 14px;font-weight: bold;"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

<br>
<br>
<div class="table-responsive">
    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
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
                Checked By
            </td>
            <td style="font-size: 12px; text-align: center">
                Approved By
            </td>
        </tr>

        </tbody>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/<?php echo $extra['master']['InvoiceAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + <?php echo $extra['master']['InvoiceAutoID'] ?> + '/BSI';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>