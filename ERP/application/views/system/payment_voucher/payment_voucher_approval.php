<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_pv_payment_voucher');
echo head_page($title, false);

/*echo head_page('Payment Voucher Approval', false); */?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!-- Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved');?>  <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' =>$this->lang->line('common_approved') /*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="payment_voucher_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="payment_voucher_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_pv_pv_code');?><!--PV Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_pv_voucher_date');?> </th><!--Voucher Date-->
            <th style="min-width: 30%"><?php echo $this->lang->line('common_narration');?> </th><!--Narration-->
            <th style="min-width: 20%"><?php echo $this->lang->line('accounts_payable_pv_party_name');?> </th><!--Party Name-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="pv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_pv_payment_voucher');?><!--Payment Voucher Approval--></h4>
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>  <!--View-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment');?>  <!--Attachment-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('accounts_payable_pv_sub_item');?> <!--Sub Item-->
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab panes -->
                        <div class="col-sm-11">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home">

                                    <div id="conform_body"></div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' =>$this->lang->line('common_please_select')/*'Please Select'*/,'1' =>$this->lang->line('common_approved') /*'Approved'*/, '2' =>$this->lang->line('common_referred_back') /*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="payVoucherAutoId" id="payVoucherAutoId">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>

                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">

                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong><?php echo $this->lang->line('accounts_payable_pv_payment_voucher_attachments');?> </strong><!--Payment Voucher Attachments-->
                                        <br><br>
                                        <table class="table table-striped table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                                <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                                <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                                <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                            </tr>
                                            </thead>
                                            <tbody id="pv_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="messages">
                                    <h4><?php echo $this->lang->line('accounts_payable_pv_sub_item_configuration');?><!--Sub Item Configuration--></h4>
                                    <div id="itemMasterSubDiv">

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!--<div class="col-sm-1">

                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="pv_attachement_approval_Tabview_v" class="active">
                                <a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">View</a>
                            </li>
                            <li id="pv_attachement_approval_Tabview_a">
                                <a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()">Attachment</a>
                            </li>
                            <li id="pv_subItem_approval_Tabview_a">
                                <a href="#Tab-subItem-v" data-toggle="tab" onclick="tabSubItem()">Attachment</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v"></div>
                            <div class="tab-pane hide" id="Tab-profile-v"></div>
                        </div>
                    </div>-->
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modify_payments" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
        
            </div>
            <form class="form-horizontal" id="invoice_list_payment_form">
                <div id="invoice_list_payment"></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modify_invoice_allocation" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
        
            </div>
            <form class="form-horizontal" id="invoice_list_payment_form">
                <div id="allocated_payments"></div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    function load_itemMasterSub_approval(receivedDocumentID, grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                receivedDocumentID: receivedDocumentID,
                grvAutoID: grvAutoID
            },
            url: "<?php echo site_url('Grv/load_itemMasterSub_approval'); ?>",
            beforeSend: function () {
                $("#itemMasterSubDiv").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubDiv").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubDiv").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/payment_voucher/payment_voucher_approval', '', 'Payment Voucher Approval');
        });
        payment_voucher_table();
        $('#pv_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_pv_level_order_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                payVoucherAutoId: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_pv_payment_voucher_id_is_required');?>.'}}},/*Payment Voucher ID is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_pv_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data == true) {
                        $("#pv_modal").modal('hide');
                        payment_voucher_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function payment_voucher_table() {
        var Otable = $('#payment_voucher_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Payment_voucher/fetch_payment_voucher_approval'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "PayVoucherAutoId"},
                {"mData": "PVcode"},
                {"mData": "PVdate"},
                {"mData": "PVNarration"},
                {"mData": "partyName"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function fetch_approval(payVoucherAutoId, documentApprovedID, Level) {
        if (payVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': payVoucherAutoId, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#payVoucherAutoId').val(payVoucherAutoId);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#pv_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    paymentVoucher_attachment_View_modal('PV', payVoucherAutoId);
                    stopLoad();
                    load_itemMasterSub_approval('PV', payVoucherAutoId);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function paymentVoucher_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#pv_attachement_approval_Tabview_a").removeClass("active");
        $("#pv_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#pv_attachment_body').empty();
                    $('#pv_attachment_body').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function tabAttachement() {
        $("#Tab-profile-v").removeClass("hide");
    }
    function tabView() {
        $("#Tab-profile-v").addClass("hide");
    }
    function tabSubItem() {
        $("#Tab-profile-v").removeClass("hide");

    }
    var Otable;
    function load_sub_invoices(invoices){

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'sub_invoices':invoices},
                url: "<?php echo site_url('Payment_voucher/fetch_sub_paymentvoucher'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);

                    $('#invoice_list_payment').empty();
                    $('#invoice_list_payment').html(data);

                    // fetch_vendor_allocations();

                    $('#modify_payments').modal('toggle');
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
        });
    }

    function load_payment_wise_invoices(payment_id){
        //
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payment_id':payment_id},
                url: "<?php echo site_url('Payment_voucher/fetch_allocated_payments'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);

                    $('#allocated_payments').empty();
                    $('#allocated_payments').html(data);

                    // fetch_vendor_allocations();

                    $('#modify_invoice_allocation').modal('toggle');
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
        });
    }


</script>