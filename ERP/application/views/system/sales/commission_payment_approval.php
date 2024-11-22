<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_sales_commission_payment_approval');
echo head_page($title, false);

/*echo head_page('Commission Payment Approval', false);*/ ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>

                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                    <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_approved');?>
                    <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending') /*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="payment_voucher_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_vocher_code');?></th><!--PV Code-->
            <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_vocher_date');?></th><!--Voucher Date-->
            <th style="min-width: 30%"><?php echo $this->lang->line('sales_markating_narration');?></th><!--Narration-->
            <th style="min-width: 20%"><?php echo $this->lang->line('sales_markating_party_name');?></th><!--Party Name-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?></th><!--Level-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_vocher_approval');?></h4><!--Payment Voucher Approval-->
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>
                                        <!--View-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment');?>
                                        <!-- Attachment-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('sales_markating_sales_item_master_sub');?>
                                        <!--Item Master Sub-->
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
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' =>$this->lang->line('common_approved') /*'Approved'*/, '2' =>$this->lang->line('common_refer_back') /*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="payVoucherAutoId" id="payVoucherAutoId">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?></label><!--Comments-->

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">

                                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">

                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_payment_voucher_attachments');?></strong><!--Payment Voucher Attachments-->
                                        <br><br>
                                        <table class="table table-striped table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                                <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                                <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                                <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                            </tr>
                                            </thead>
                                            <tbody id="pv_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="messages">
                                    <h4><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_sub_item_configuration');?></h4><!--Sub Item Configuration-->
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
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
            fetchPage('system/sales/commission_payment_approval', '', 'Payment Voucher Approval');
        });
        payment_voucher_table();
        $('#pv_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_credit_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                payVoucherAutoId: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_payment_voucher_id_is_required');?>.'}}},/*Payment Voucher ID is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
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
                        Otable.draw();
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
        Otable = $('#payment_voucher_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Payment_voucher/fetch_commission_payment_approval'); ?>",
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
                {"mData": "referenceNo"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "total_value_search"}
            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [10], "orderable": false}, {"targets": [0,7,8,9], "searchable": false}],
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


</script>