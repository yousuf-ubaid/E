
<div id="versionSection">

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$assignBuyersPolicy = getPolicyValues('ABFC', 'All');

///print_r($extra['master']['approvedYN']);

//print_r($extra['master']['confirmedYN']);exit;
if(isset($version)){
    echo fetch_account_review(false,true,$approval,$versionID);
}else{
    echo fetch_account_review(false,true,$approval);
}
 ?>

<?php if($html && $approval != 1 && !$versionhide){ ?>

    <div class="row">
        <div class="col-md-7">
            &nbsp;
            </div>
            <div class="col-md-2 text-center">
                &nbsp;
            </div>
            <div class="col-md-3 text-right">
            <?php echo form_dropdown('versionID', $version_drop, isset($versionID) ? $versionID : 0 , 'class="form-control select2" onchange="load_version_confirmation_PR(this)" id="versionID" '); ?>
        </div>
    </div>

<?php } ?>




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
                        <td><?php echo add_version_code($extra['master']['purchaseRequestCode'] , isset($extra['master']['versionNo']) ? $extra['master']['versionNo'] : 0); ?></td>
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
                    <?php } ?>
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
        <tr>
            <td style="width:15%;vertical-align: top"><strong>Severity </strong></td>
            <td style="width:2%;vertical-align: top"><strong>:</strong></td>
            <td style="width:33%;">
            <?php echo $extra['master']['severityType']; ?>
            </td>
           
        </tr>
        </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="pr_confirm_table">
        <thead class='thead'>
            <tr>
                <th style="min-width: 50%" class='theadtr' colspan="7"> <?php echo $this->lang->line('procurement_approval_item_details'); ?><!--Item Details--></th>
                <th style="min-width: 50%" class='theadtr' colspan="4">
                    <?php echo $this->lang->line('common_cost'); ?><!--Cost--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
            </tr>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--></th>
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <th style="min-width: 11%" class='theadtr'><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                <?php if ($show_attachment_header){ ?>
                <th style="min-width: 15%" class='theadtr'><?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></th>
                <?php }?>
                <?php if ($isPrint == 1) { ?>
                    <?php if ($assignBuyersPolicy == 1) { ?>
                        <?php if ($extra['master']['approvedYN'] == 0 && $extra['master']['confirmedYN'] == 1) { ?>
                            <th style="min-width: 15%" class='theadtr'></th>
                        <?php } else { ?>
                            <?php if ($extra['master']['approvedYN'] == 1 && $extra['master']['confirmedYN'] == 1) { ?>
                                <th style="min-width: 15%" class='theadtr'></th>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
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
                        <td class="text-center"><?php echo add_version_code($val['itemSystemCode'],isset($val['versionNo']) ? $val['versionNo'] : 0); ?></td>
                        <td class="text-center"><?php echo $val['expectedDeliveryDate']; ?></td>
                        <td><?php echo $val['itemDescription'] . ' - ' . $val['Itemdescriptionpartno']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right"><?php echo $val['requestedQty']; ?></td>
                        <td class="text-right"><?php echo number_format(($val['unitAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . ' (' . $val['discountPercentage'] . '%)'; ?></td>
                        <td class="text-right"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo $val['comment']; ?></td>
                        <td class="text-center">
                            <span class="pull-right">
                            <a onclick="fetch_attachment('<?php echo $val['purchaseRequestID']; ?>','<?php echo $val['purchaseRequestDetailsID']; ?>')" >
                                <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                            </a>
                            </span>
                        </td>
                        <?php if ($isPrint == 1) { ?>
                            <td>
                                <span class="pull-right">
                                    <?php if ($extra['master']['confirmedYN'] == 1 && !empty($val['activityCodeID'])) { ?>
                                        <a onclick="allocateCost(<?php echo $val['purchaseRequestDetailsID'] ?>, <?php echo $val['purchaseRequestID'] ?>, 'PRQ', <?php echo $val['activityCodeID'] ?>)">
                                            <span class="glyphicon glyphicon-cog" rel="tooltip"></span>
                                        </a>
                                    <?php } ?>
                                    <?php if ($assignBuyersPolicy == 1 && $extra['master']['approvedYN'] == 0 && $extra['master']['confirmedYN'] == 1) { ?>
                                        &nbsp;&nbsp;<a onclick="view_buyersViewAssignModel(<?php echo $val['purchaseRequestID'] ?>, <?php echo $val['purchaseRequestDetailsID'] ?>, '', 2, 1)">
                                            <span title="Add Buyers" class="glyphicon glyphicon-user" rel="tooltip"></span>
                                        </a>
                                    <?php } else { ?>
                                        <?php if ($assignBuyersPolicy == 1 && $extra['master']['approvedYN'] == 1 && $extra['master']['confirmedYN'] == 1) { ?>
                                            &nbsp;&nbsp;<a onclick="view_buyersViewAssignModel(<?php echo $val['purchaseRequestID'] ?>, <?php echo $val['purchaseRequestDetailsID'] ?>, '', 2, 1)">
                                                <span title="Add Buyers" class="glyphicon glyphicon-user" rel="tooltip"></span>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($extra['master']['approvedYN'] == 1) { ?>
                                        <?php if ($val['isClosedYN'] == 0) { ?>
                                            &nbsp;&nbsp;<a onclick="close_Document_details_line_wise('PRQ', <?php echo $val['purchaseRequestID'] ?>, <?php echo $val['purchaseRequestDetailsID'] ?>, 'srp_erp_purchaserequestdetails', 'purchaseRequestDetailsID')" title="Close Document" rel="tooltip"><i title="Close Item" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>
                                        <?php } else { ?>
                                            &nbsp;&nbsp;<a onclick="close_Document_details_view_line_wise('PRQ', <?php echo $val['purchaseRequestID'] ?>, <?php echo $val['purchaseRequestDetailsID'] ?>, 'srp_erp_purchaserequestdetails', 'purchaseRequestDetailsID', 0)" title="View closed details" rel="tooltip"><i title="View closed details" rel="tooltip" class="fa fa-ban" aria-hidden="true"></i></a>
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
                echo '<tr class="danger"><td colspan="12" class="text-center">' . $NoRecordsFound . '</td></tr>';
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <td style="min-width: 15% !important" class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>

</div><br>
<div class="table-responsive">
    <h5 class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['master']['transactionCurrency']; ?> )
        : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
</div>

<!--Detail Attachment Modal -->
<div class="modal fade" id="pop_purchase_attachement" tabindex="-1" role="dialog" aria-labelledby="pop_purchaseOrder_attachment_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="pop_close()" aria-label="Close">
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
                            <input type="hidden" class="form-control" id="documentID" value="PRQ" name="documentID">
                            <input type="hidden" class="form-control" id="document_name" value="Purchase Request" name="document_name">
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

    <?php if(isset($version)){ ?>
        a_link=  "<?php echo site_url('PurchaseRequest/load_purchase_request_version'); ?>/<?php echo $extra['master']['purchaseRequestID'] ?>/<?php echo $versionID ?>/<?php echo 'pdf' ?>";
        $("#a_link").attr("href",a_link);
    <?php  } else{ ?>
        a_link=  "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>/<?php echo $extra['master']['purchaseRequestID'] ?>";
        $("#a_link").attr("href",a_link);
    <?php } ?>

    function fetch_attachment(purchaseRequestID,purchaseRequestDetailsID){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("PurchaseRequest/fetch_PR_Attachments"); ?>',
            dataType: 'json',
            data: { 'deatilID': purchaseRequestDetailsID, 'PurchaseId': purchaseRequestID },
            success: function (data) {
                $('#purchaseOrder_attachment_pop').empty();
                $('#purchaseOrder_attachment_pop').append('' +data+ '');
                $("#pop_purchase_attachement").modal({ backdrop: "static", keyboard: true });

                

                $('#detailID').val(purchaseRequestDetailsID);
                $("#purchaseID").val(purchaseRequestID);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error('Error fetching attachments:', xhr.responseText);
                alert('An error occurred while fetching attachments. Please try again.');
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
                    fetch_attachment(purchaseRequestID,detailID);
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
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
        $('#pop_purchase_attachement').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');  // Restore scroll functionality to the first modal
        });
    }

   
</script>

</div>

