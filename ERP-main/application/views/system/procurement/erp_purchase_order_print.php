<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$flowserveLanguagePolicy =  getPolicyValues('LNG', 'All');
echo fetch_account_review(false, true, $approval); ?>
<?php if($extra['master']['versionNo']==0){ ?>

<?php } else{ ?>
    <div class="row">
        <div class="col-md-7">
        &nbsp;
        </div>
        <div class="col-md-2 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
        <?php echo form_dropdown('versionID', $version_drop, '' , 'class="form-control select2" onchange="load_version_confirmation_po(this)" id="versionID" '); ?>
        </div>
    </div>
<?php } ?>
<div class="table-responsive">
    <?php
    if ($printHeaderFooterYN == 1) {
        ?>
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="max-height: 100px;max-width:250px"
                                     src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                                <!--<h4><?php /*echo $this->lang->line('common_purchase_order');*/?><!--Purchase Order--> </h4>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
            </tbody>
        </table>

        <?php
    } else {
        ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    <?php } ?>
</div>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td></td>
            <td><strong>
                    <?php echo $this->lang->line('procurement_approval_purchase_order_number'); ?><!--Purchase Order Number--></strong>
            </td>
            <td><strong>:</strong></td>
            <?php if($extra['master']['versionNo']==0){ ?>
                <td><?php echo $extra['master']['purchaseOrderCode']; ?></td>
            <?php }else{ ?>
                <td><?php echo $extra['master']['purchaseOrderCode']. ' (V' . $extra['master']['versionNo'] . ')'; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td></td>
            <td><strong>
                    <?php echo $this->lang->line('procurement_approval_purchase_order_date'); ?><!--Purchase Order Date--></strong>
            </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
            <td style="width:46%;padding-left: 4%;"><strong style="font-size: 17px;">
                    <?php echo $this->lang->line('common_purchase_order'); ?><!--Purchase Order--></strong></td>
            <td><strong><?php echo $this->lang->line('common_reference_number'); ?><!--Reference Number--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNumber']; ?></td>
        </tr>
        <?php 

            if($isGroupBasedTaxEnable == 1){ ?>

                <tr>
                    <td></td>
                    <td><strong><?php echo 'VAT IN'; ?></strong></td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['supplier']['companyvaNumber']; ?></td>
                </tr>

        <?php 
            } 
        ?>

        </tbody>
    </table>
</div>

<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top;"><strong>
                    <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--></strong></td>
            <td style="width:2%;vertical-align: top;"><strong>:</strong></td>
            <td style="width:33%;vertical-align: top;"><?php echo $extra['supplier']['supplierName'] . ' (' . $extra['supplier']['supplierSystemCode'] . ').<br>' . $extra['supplier']['supplierAddress1']; ?></td>

            <td style="width:15%;vertical-align: text-top"><strong>
                    <?php echo $this->lang->line('procurement_approval_ship_to'); ?><!--Ship To--></strong></td>
            <td style="width:2%;vertical-align: text-top"><strong>:</strong></td>
            <td style="width:33%;vertical-align: text-top"><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').<br>' . $extra['master']['shippingAddressDescription']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_contact'); ?><!--Contact--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonName']; ?></td>

            <td><strong>
                    <?php echo $this->lang->line('procurement_approval_ship_contact'); ?><!--Ship Contact--></strong>
            </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonID']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_telephone'); ?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonNumber']; ?></td>

            <td><strong><?php echo $this->lang->line('common_telephone'); ?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonTelephone']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_fax'); ?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierFax']; ?></td>

            <td><strong><?php echo $this->lang->line('common_fax'); ?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonFaxNo']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_email'); ?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['supplier']['supplierEmail']; ?></td>

            <td><strong><?php echo $this->lang->line('common_email'); ?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['shipTocontactPersonEmail']; ?></td>
        </tr>
        <?php if ($isGroupBasedTaxEnable == 1) { ?>
            <tr>
                <td><strong><?php echo 'Supplier VAT Number'; ?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['supplier']['vatNumber']; ?></td>
            </tr>
        <?php } ?>
        <?php if ($isRcmDocument == 1) { ?>
            <tr>
                <td><span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span>
                </td>
            </tr>
        <?php } ?>
        
        <?php if($flowserveLanguagePolicy == 'FlowServe' || $flowserveLanguagePolicy == 'GCC' || $flowserveLanguagePolicy == 'SOP'){ ?>

            <tr>
                <td><strong><?php echo 'Job Number'; ?></strong></td>
                <td><strong>:</strong></td>
                <td><?php echo ltrim($extra['jobNumberStr'],','); ?></td>
            </tr>


        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:15%;"><strong>
                    <?php echo $this->lang->line('procurement_approval_expected_date'); ?><!--Expected Date--> </strong>
            </td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_currency'); ?><!--Currency--> </strong>
            </td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

        </tr>
        <tr>
            <td style="width:15%;vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_approval_narration'); ?><!--Narration--> </strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:33%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['narration']); ?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['narration']; ?>
            </td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_segment'); ?><!--Segment--> </strong>
            </td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['segmentCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <?php
    $total = 0;
    $gran_total = 0;
    $tax_transaction_total = 0;
    $gen_disc_total = 0;
    $total_commission = 0;
    $net_total = 0;
    $total_amount = 0;
    if('LOG' === $extra['master']['purchaseOrderType']) { ?>
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 5%" class="theadtr">#</th>
            <th style="min-width: 7%" class="theadtr">PO Code<!--Code--></th>
            <th style="min-width: 5%" class="theadtr">Logistic Amount</th>
            <th style="min-width: 10%" class="theadtr">Logistic Balance</th>
            <th style="min-width: 10%" class="theadtr">Matching Amount</th>
            <th style="min-width: 10%" class="theadtr">Actual Logistic Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($extra['detail'])) {
            for ($i = 0;
                 $i < count($extra['detail']);
                 $i++) {
                $gran_total += $extra['detail'][$i]['actualLogisticAmount'];
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . $extra['detail'][$i]['purchaseOrderCode'] . '</td>';
                echo '<td class="text-right">' . number_format($extra['detail'][$i]['addonAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . number_format($extra['detail'][$i]['addonBalance'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . number_format($extra['detail'][$i]['matchedAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . number_format($extra['detail'][$i]['actualLogisticAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $this->lang->line('common_no_records_found') . '<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>

        </tfoot>
    </table>
    <?php } else { ?>
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_code'); ?><!--Code--></th>

                <?php if ($extra['master']['purchaseOrderType'] == "PR") { ?>
                    <th style="min-width: 30%" class="text-left theadtr">
                        Purchase Request<!--Purchase Order Request--></th>
                <?php } ?>

                <th style="min-width: 30%" class="text-left theadtr">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit_price'); ?><!--Unit Price--></th>
                <th style="min-width: 11%" class='theadtr'>
                    <?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('common_net_unit_price'); ?><!--Net Unit Price--></th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('common_total'); ?><!--Total Price--></th>
              
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('procurement_tax_amount'); ?><!-- Tax amount --></th>
               
                <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_net_total'); ?><!--Total--></th>
                <?php if ($show_attachment_header){ ?>
                <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></th>
                <?php }?>

                <?php if($isPrint ==1){ ?>

                    <?php if( $extra['master']['approvedYN']==1){ ?>
                        <th style="min-width: 15%" class='theadtr'></th>
                    <?php } ?>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($extra['detail'])) { 
                foreach ($extra['detail'] as $val) {
                    $total_commission += $val['commision_value'];
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <?php
                        $purchaseRequestCode = '';
                        if (!empty($val['purchaseRequestCode'])) {
                            $purchaseRequestCode = $val['purchaseRequestCode'] . ' - ';
                        }
                        ?>
                        <td class="text-center" style="font-size: 12px;">
                            <?php if ($extra['master']['purchaseOrderType'] == 'PR') { ?>
                                <?php echo $val['itemSystemCode']; ?>
                            <?php } else { ?>
                                <?php echo $purchaseRequestCode . $val['itemSystemCode']; ?>
                            <?php }

                            ?>
                        </td>

                        <?php if ($extra['master']['purchaseOrderType'] == "PR") { ?>
                            <td>
                                <a onclick="requestPageView_model('PRQ', <?php echo $val['purchaseRequestID'] ?>)"><?php echo rtrim($purchaseRequestCode, ' -'); ?></a>
                                <?php //echo $val['itemSystemCode']; ?>
                            </td>
                        <?php } ?>

                        <td style="font-size: 12px;"><?php echo $val['itemDescription'] ?>
                            <?php if (!empty($val['comment']) && empty($val['partNo'])) {
                                echo ' - ' . $val['comment'];
                            } else if (!empty($val['comment']) && !empty($val['partNo'])) {
                                echo ' - ' . $val['comment'] . ' - ' . 'Part No : ' . $val['partNo'];
                            } else if (!empty($val['partNo'])) {
                                echo ' - ' . 'Part No : ' . $val['partNo'];
                            }
                            ?>
                        </td>
                        <td class="text-center" style="font-size: 12px;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right" style="font-size: 12px;"><?php echo $val['requestedQty']; ?></td>
                        <td class="text-right"
                            style="font-size: 12px;"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"
                            style="font-size: 12px;"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . round($val['discountPercentage'], 2) . '%)'; ?></td>
                        <td class="text-right"
                            style="font-size: 12px;"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"
                            style="font-size: 12px;"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                   
                            <td class="text-right"
                                style="font-size: 12px;">
                                <?php if ($isGroupBasedTaxEnable == 1) {
                                    if ($val['taxAmount'] > 0) {
                                        // if ($isRcmDocument == 1 && $type!=true) {
                                        //     echo '<a onclick="open_tax_dd(null,'.$val['purchaseOrderID'].',\'PO\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['purchaseOrderDetailsID'].', \'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\',0,1) ">'. number_format(($val['taxAmount'] - $val['taxamountVat']), $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                        // } else {
                                        echo '<a onclick="open_tax_dd(null,'.$val['purchaseOrderID'].',\'PO\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['purchaseOrderDetailsID'].', \'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a>';
                                        // }


                                    } else {
                                        echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);

                                    } ?>
                                <?php } else { ?>

                                    <?php echo number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?>

                                <?php } ?>


                            </td>
                            <?php
                            //  if ($isRcmDocument == 1 && $type!=true) {
                            //      $totamnt = (($val['totalAmount'] + $val['taxAmount']) - $val['taxamountVat']);
                            //  }else {
                            $totamnt = $val['totalAmount'] + $val['taxAmount'];
                            //  }

                        // } else {
                        //     $totamnt = $val['totalAmount'];
                        // }
                        ?>
                        <td class="text-right"
                            style="font-size: 12px;"><?php echo number_format($totamnt, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                        <td class="text-center">
                            <span class="pull-right">
                            <a onclick="fetch_attachment('<?php echo $val['purchaseOrderID']; ?>','<?php echo $val['purchaseOrderDetailsID']; ?>')" target="_blank">
                                <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                            </a>
                            </span>
                        </td>

                        <?php if($isPrint ==1){ ?>
                            <td class="text-right"
                                style="font-size: 12px;">
                                <?php if($extra['master']['approvedYN']==1 ){ ?>

                                    <?php if($val['isClosedYN']==0 ){ ?>

                                        &nbsp;&nbsp;<a onclick="close_Document_details_line_wise('PO',<?php echo $val['purchaseOrderID'] ?>,<?php echo $val['purchaseOrderDetailsID'] ?>,'srp_erp_purchaseorderdetails','purchaseOrderDetailsID')" title="Close Document" rel="tooltip"><i title="Close Item" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>

                                    <?php }else{ ?>
                                        &nbsp;&nbsp;<a onclick="close_Document_details_view_line_wise('PO',<?php echo $val['purchaseOrderID'] ?>,<?php echo $val['purchaseOrderDetailsID'] ?>,'srp_erp_purchaseorderdetails','purchaseOrderDetailsID',0)" title="View closed details" rel="tooltip"><i title="View closed details" rel="tooltip" class="fa fa-ban" aria-hidden="true"></i></a>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php 
                    $num++;
                    $total += $totamnt;
                    $gran_total += $totamnt;
                    $total_amount += $val['totalAmount'];
                    $tax_transaction_total += $val['totalAmount'] + $val['taxAmount'];
                }
            } else {
                $NoRecordsFound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="9" class="text-center" style="font-size: 12px;">' . $NoRecordsFound . '<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <?php
                if ($extra['master']['documentTaxType'] == 1) {

                    if ($extra['master']['purchaseOrderType'] == "PR") { ?>
                    
                        <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                            <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <?php } else { ?>
                        <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="12">
                            <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <?php }

                } else {
                    ?>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="10">
                        <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <?php
                }
                ?>

                <td style="min-width: 15% !important; font-size: 12px;"
                    class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>

            <?php if($extra['master']['purchaseOrderType'] == 'BQUT') {

                ?>
                
                <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="10">
                        <?php echo 'Less Commission '.$this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <td style="min-width: 15% !important; font-size: 12px;"
                        class="text-right total"><?php echo '-'.number_format($total_commission, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>

            <?php foreach($extra['extraCharges'] as $value){
                $net_total += $value['extraCostValue'];
                ?>
                <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="11">
                            <?php echo $value['extraCostName'] ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                        <td style="min-width: 15% !important; font-size: 12px;"
                            class="text-right total"><?php echo number_format($value['extraCostValue'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td style="min-width: 85%  !important;font-size: 12px;" class="text-right sub_total" colspan="10">
                        <?php echo 'Net Amount'?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    <td style="min-width: 15% !important; font-size: 12px;"
                        class="text-right total"><?php echo number_format($net_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>

            <?php } ?>
            </tfoot>
        </table>
    <?php } ?>

</div><br>

<?php
     $gran_total += $total_commission;
?>

<!--Detail Attachment Modal -->
<div class="modal fade" id="pop_purchase_attachement" tabindex="-1" role="dialog" aria-labelledby="pop_purchaseOrder_attachment_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  onclick="pop_close()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="pop_purchaseOrder_attachment_label">Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="width: 100%">
                    <div class="col-md-12">
                        <span class="pull-right">
                        <form id="purchase_form" class="form-inline" enctype="multipart/form-data" method="post">
                            <input type="hidden" name="detailID" id="detailID">
                            <input type="hidden" class="form-control" id="purchaseID" name="purchaseID">
                            <input type="hidden" class="form-control" id="documentID" value="PO" name="documentID">
                            <input type="hidden" class="form-control" id="document_name" value="Purchase Order" name="document_name">
                            <div class="form-group">
                                <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                            </div>
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename set-w-file-name"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                        data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="uplode_purchase()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                        </span>
                    </div>
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                        </thead>
                        <tbody id="purchaseOrder_attachment_pop" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="pop_close()"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:40%;">
                &nbsp;
            </td>
            <td style="width:60%;padding: 0;">
                <?php
                if ($extra['master']['generalDiscountPercentage'] > 0) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                    <?php echo $this->lang->line('common_discount_details'); ?><!-- Discount Details --></strong>
                            </td>
                        </tr>
                        <tr>
                            <th class='theadtr'>
                                <?php echo $this->lang->line('common_discount_percentagae'); ?><!-- Discount Percentage --></th>
                            <th class='theadtr'>Discount Amount (<?php echo $extra['master']['transactionCurrency']; ?>
                                )
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-right"
                                style="font-size: 12px;"><?php echo number_format($extra['master']['generalDiscountPercentage'], $extra['master']['transactionCurrencyDecimalPlaces']); ?>
                                %
                            </td>
                            <td class="text-right"
                                style="font-size: 12px;"><?php echo format_number(($extra['master']['generalDiscountPercentage'] / 100) * $tax_transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <?php
                            $gen_disc_total = ($extra['master']['generalDiscountPercentage'] / 100) * $tax_transaction_total;
                            ?>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<br>
<?php
if ($extra['master']['documentTaxType'] == 0) {
    ?>
    <div class="table-responsive hide">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <?php
                    if (!empty($extra['tax_detail'])) { ?>
                        <table style="width: 100%" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <td class='theadtr' colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                        <?php echo $this->lang->line('procurement_approval_tax_details'); ?><!-- Tax Details --></strong>
                                </td>
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'>
                                    <?php echo $this->lang->line('common_details'); ?><!-- Detail --></th>
                                <th class='theadtr'><?php echo $this->lang->line('common_amount'); ?><!-- Amount -->
                                    <span
                                            class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>
                                        )</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $x = 1;
                            $tr_total_amount = 0;
                            $cu_total_amount = 0;
                            $loc_total_amount = 0;
                            foreach ($extra['tax_detail'] as $value) {
                                echo '<tr>';
                                echo '<td>' . $x . '.</td>';
                                echo '<td>' . $value['taxDescription'] . '</td>';
                                echo '<td class="text-right">' . format_number($value['amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += $value['amount'];
                                $tr_total_amount += $value['amount'];
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2" class="text-right sub_total">Tax Total</td>
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>

<?php if($extra['master']['purchaseOrderType'] != 'BQUT') { ?>
    <?php if($flowserveLanguagePolicy == 'FlowServe'){ ?>
        <div class="table-responsive">
            <h5 class="text-left"> <?php echo 'Budget Value'; ?><!--Total-->
                (<?php echo $extra['master']['transactionCurrency']; ?> )
                : <?php echo format_number($extra['estimate'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
        </div>
    <?php } ?>
    <?php if($flowserveLanguagePolicy == 'FlowServe'){ ?>
        <div class="table-responsive">
            <h5 class="text-left"> <?php echo 'Consumed Value'; ?><!--Total-->
                (<?php echo $extra['master']['transactionCurrency']; ?> )
                : <?php echo format_number($total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
        </div>
    <?php } ?>

    <div class="table-responsive">
        <?php if($flowserveLanguagePolicy == 'FlowServe'){ ?>
            <h5 class="text-left"> <?php echo 'Balance Value'; ?><!--Total-->
                (<?php echo $extra['master']['transactionCurrency']; ?> )
                : <?php echo format_number(($extra['estimate'] - ($total_amount)), $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
        <?php } else { ?>
            <h5 class="text-right"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
                (<?php echo $extra['master']['transactionCurrency']; ?> )
                : <?php echo format_number($gran_total - $gen_disc_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
        <?php }?>
    </div>

   
 
<?php } ?>
<br>
<?php
$data['documentCode'] = 'PO';
$data['transactionCurrency'] = $extra['master']['transactionCurrency'];
$data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
$data['documentID'] = $extra['master']['purchaseOrderID'];
$data['isRcmDocument'] = (($isRcmDocument == 1 && $type!=true)?1:0) ;
echo $this->load->view('system/tax/tax_detail_view.php', $data, true);
?>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:28%;vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_approval_delivery_terms'); ?><!--Delivery Terms--> </strong>
            </td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['deliveryTerms']); ?></td>
                    </tr>
                </table>
                <?php // echo $extra['master']['deliveryTerms']; ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_approval_payment_terms'); ?><!--Payment Terms--> </strong>
            </td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['paymentTerms']); ?></td>
                    </tr>
                </table>
                <?php //echo  $extra['master']['paymentTerms']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:28%;vertical-align: top"><strong>
                    <?php echo $this->lang->line('procurement_penalty_terms'); ?><!--Penalty Terms--></strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:70%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['penaltyTerms']); ?></td>
                    </tr>
                </table>


            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>


<?php if ($extra['master']['termsandconditions']) { ?>
<div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('common_notes'); ?><!-- Notes --></h6>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><?php echo $extra['master']['termsandconditions']; ?></td>
        </tr>
        </tbody>
    </table>
    <?php } ?>


    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <?php if ($ALD_policyValue == 1) { 
                    $created_user_designation = designation_by_empid($extra['master']['createdUserID']);
                    $confirmed_user_designation = designation_by_empid($extra['master']['confirmedByEmpID']);
                    ?>
                        <tr>
                        <td style="width:30%;"><b>
                                <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['createdDateTime']; ?></td>
                    </tr>
                <?php if($extra['master']['confirmedYN']==1){ ?>
                    <tr>
                        <td style="width:30%;"><b>Confirmed By </b></td>
                        <td><strong>: </strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['confirmedbyName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['confirmedDate'];?></td>
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
                        <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
                    </tr>
                    <?php if ($extra['master']['confirmedYN'] == 1) { ?>
                    <tr>
                        <td style="width:30%;"><b>
                                <?php echo $this->lang->line('common_confirmed_by'); ?><!-- Confirmed By --> </b></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['confirmedYNn']; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if ($extra['master']['approvedYN']) { ?>
                    <tr>
                        <td style="width:28%;"><strong>
                                <?php echo $this->lang->line('procurement_approval_electronically_approved_by'); ?><!--Electronically Approved By--> </strong>
                        </td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>
                                <?php echo $this->lang->line('procurement_approval_electronically_approved_date'); ?><!--Electronically Approved Date--> </strong>
                        </td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['approvedDate']; ?></td>
                    </tr>
                <?php }
            	} ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
    <?php if ($extra['master']['approvedYN']) { ?>
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
                                <span>____________________________</span><br><br><span><b>&nbsp;<?php echo $this->lang->line('common_authorized_signature'); ?> <!-- Authorized Signature --></b></span>
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
        a_link = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/<?php echo $extra['master']['purchaseOrderID'] ?>";
        $("#a_link").attr("href", a_link);


        function fetch_attachment(purchaseRequestID,purchaseRequestDetailsID){
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Procurement/fetch_PO_attachments"); ?>',
                dataType: 'json',
                data: {'deatilID':purchaseRequestDetailsID,'PurchaseId':purchaseRequestID},
                success: function (data) {
                    $('#purchaseOrder_attachment_pop').empty();
                    $('#purchaseOrder_attachment_pop').append('' +data+ '');
                    $("#pop_purchase_attachement").modal({ backdrop: "static", keyboard: true });
                    $('#detailID').val(purchaseRequestDetailsID);
                    $("#purchaseID").val(purchaseRequestID);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }

         function uplode_purchase(){
            var detailID=$('#detailID').val();
            var purchaseRequestID=$('#purchaseID').val();
            var formData = new FormData($('#purchase_form')[0]);
            
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Attachment/uplode_Purchase_Attachment'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        $('#remove_id').click();
                        $('#attachmentDescription').val('');
                    }
                    fetch_attachment(purchaseRequestID,detailID);
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function pop_close(){
            $('#pop_purchase_attachement').modal('hide');
        }


    </script>



