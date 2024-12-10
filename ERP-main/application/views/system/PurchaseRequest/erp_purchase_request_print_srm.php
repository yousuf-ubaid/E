<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$assignBuyersPolicy = getPolicyValues('ABFC', 'All');

///print_r($extra['master']['approvedYN']);

//print_r($extra['master']['confirmedYN']);exit;
echo fetch_account_review(false,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('procurement_approval_purchase_request');?><!--Purchase Request--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('procurement_approval_purchase_request_number');?><!--Purchase Request Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['purchaseRequestCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_name');?><!--Name--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['requestedByName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('procurement_approval_purchase_request_date');?><!--Purchase Request Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNumber']; ?></td>
                    </tr>
                    <?php if($jobNumberMandatory){?>
                    <tr>
                        <td><strong>Job No</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['jobNumber']; ?></td>
                    </tr>
                    <?php }?>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:15%;"><strong><?php echo $this->lang->line('procurement_approval_expected_date');?><!--Expected Date--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['expectedDeliveryDate']; ?></td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

        </tr>
        <tr>
            <td style="width:15%;vertical-align: top"><strong><?php echo $this->lang->line('procurement_approval_narration');?><!--Narration--> </strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:33%;">
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ', $extra['master']['narration']);?></td>
                    </tr>
                </table>
            </td>
            <td style="width:15%;"><strong><?php echo $this->lang->line('common_segment');?><!--Segment--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['segmentCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="pr_confirm_table">
        <thead class='thead'>
        <tr>
            <th style="min-width: 50%" class='theadtr' colspan="6"> <?php echo $this->lang->line('procurement_approval_item_details');?><!--Item Details--></th>
            <th style="min-width: 50%" class='theadtr' colspan="4">
                <?php echo $this->lang->line('common_cost');?> <!--Cost--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
        </tr>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('procurement_approval_expected_delivery_date');?><!--Expected Delivery Date--></th>
            <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit');?><!--Unit--></th>
            <th style="min-width: 11%" class='theadtr'><?php echo $this->lang->line('common_discount');?><!--Discount--></th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_net_cost');?><!--Net Cost--></th>
            <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_total');?><!--Total--></th>
            <?php if($isPrint ==1){ ?>
                <?php if($assignBuyersPolicy==1 ){ ?>
                    <?php if($extra['master']['approvedYN']==0 && $extra['master']['confirmedYN']==1){ ?>
                        <th style="min-width: 15%" class='theadtr'></th>
                    <?php }else{ ?>
                        <?php if( $extra['master']['approvedYN']==1 && $extra['master']['confirmedYN']==1){ ?>
                        <th style="min-width: 15%" class='theadtr'></th>
                        <?php } ?>
                    <?php } ?>
                <?php }else{ ?>
                    <th style="min-width: 15%" class='theadtr'></th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                    <td class="text-center"><?php echo $val['expectedDeliveryDate']; ?></td>
                    <td><?php echo $val['itemDescription'] . ' - ' .$val['Itemdescriptionpartno']; ?></td>
                    <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                    <td class="text-right"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-right"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"><?php echo  number_format($val['discountAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . $val['discountPercentage'] . '%)'; ?></td>
                    <td class="text-right"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php if( $isPrint ==1){ ?>
                        <td >
                            <span class="pull-right">
                                <?php if($assignBuyersPolicy==1 && $extra['master']['approvedYN']==0 && $extra['master']['confirmedYN']==1 ){ ?>
                               
                                        <a onclick="view_buyersViewAssignModel(<?php echo $val['purchaseRequestID'] ?>,<?php echo $val['purchaseRequestDetailsID'] ?>,'',2,1)">
                                            <span title="Add Buyers" class="glyphicon glyphicon-user" style="" rel="tooltip"></span>
                                        </a>
                                  
                                <?php }else{ ?>
                                    <?php if($assignBuyersPolicy==1 && $extra['master']['approvedYN']==1 && $extra['master']['confirmedYN']==1 ){ ?>
                                    
                                            <a onclick="view_buyersViewAssignModel(<?php echo $val['purchaseRequestID'] ?>,<?php echo $val['purchaseRequestDetailsID'] ?>,'',2,1)">
                                                <span title="Add Buyers" class="glyphicon glyphicon-user" style="" rel="tooltip"></span>
                                            </a>
                                     
                                    <?php } ?>
                                <?php } ?>

                                <?php if($extra['master']['approvedYN']==1 ){ ?>

                                    <?php if($val['isClosedYN']==0 ){ ?>
                               
                                        <!-- &nbsp;&nbsp;<a onclick="close_Document_details_line_wise('PRQ',<?php echo $val['purchaseRequestID'] ?>,<?php echo $val['purchaseRequestDetailsID'] ?>,'srp_erp_purchaserequestdetails','purchaseRequestDetailsID')" title="Close Document" rel="tooltip"><i title="Close Item" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a> -->
                                
                                    <?php }else{ ?>
                                        &nbsp;&nbsp;<a onclick="close_Document_details_view_line_wise('PRQ',<?php echo $val['purchaseRequestID'] ?>,<?php echo $val['purchaseRequestDetailsID'] ?>,'srp_erp_purchaserequestdetails','purchaseRequestDetailsID',1)" title="View closed details" rel="tooltip"><i title="View closed details" rel="tooltip" class="fa fa-ban" aria-hidden="true"></i></a>
                                    <?php } ?>
                                <?php } ?>
                            </span>
                        </td>
                    <?php } ?>
                </tr>
                <?php


                $num++;
                $total += $val['totalAmount'];
                $gran_total += $val['totalAmount'];
                $tax_transaction_total += $val['totalAmount'];
            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="10" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9">
                <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="min-width: 15% !important"
                class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div><br>
<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>
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
        <?php if ($extra['master']['confirmedYN']==1) { ?>
        <tr>
            <td style="width:30%;"><b><?php echo $this->lang->line('common_confirmed_by');?><!--Confirmed By-->  </b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"><?php echo $extra['master']['confirmedYNn'];?></td>
        </tr>
        <?php } ?>
        <?php if ($extra['master']['approvedYN']) { ?>
        <tr>
            <td style="width:28%;"><strong><?php echo $this->lang->line('procurement_approval_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('procurement_approval_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
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
                            <span>____________________________</span><br><br><span><b>&nbsp;<?php echo $this->lang->line('common_authorized_signature');?> <!-- Authorized Signature --></b></span>
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
    a_link=  "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>/<?php echo $extra['master']['purchaseRequestID'] ?>";
    $("#a_link").attr("href",a_link);
</script>



