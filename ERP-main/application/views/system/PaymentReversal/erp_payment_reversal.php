<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_trans_pr_payment_reversal');
echo head_page($title, false);


/*echo head_page('Payment Reversal', false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false); ?>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?> <!--Date--></label><br>
            <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                   class="input-small">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_to');?> <!--To-->&nbsp;&nbsp;</label>
            <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                   class="input-small">
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' => $this->lang->line('common_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <!-- <table class="<?php /*echo table_class(); */ ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Confirmed / Approved</td>
                <td><span class="label label-danger">&nbsp;</span> Not Confirmed / Not Approved</td>
                <td><span class="label label-warning">&nbsp;</span> Refer-back</td>
                <td><span class="label label-info">&nbsp;</span> Closed </td>
            </tr>
        </table>-->
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <!--<button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/PaymentReversal/erp_payment_reversal_new',null,'Add Payment Reversal','PRVR');"><i
                class="fa fa-plus"></i> Create Payment Reversal
        </button>-->
    </div>
</div>
<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active"><a href="#attendanceTab" id="accountsTab" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('accounts_payable_trans_pr_payment_reversal');?><!--Payment Reversal--></a>
        </li>
        <li class=""><a href="#pullingTab" id="pulling" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('accounts_payable_trans_pr_payment_reveresed');?><!--Reversed Payment--></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="attendanceTab">
            <div class="table-responsive">
                <table id="payment_reversal_table" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 4%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_pv_pv_code');?><!--PV Code--></th>
                        <th style="min-width:10%"><?php echo $this->lang->line('accounts_payable_trans_pm_pv_date');?><!--PV Date--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_trans_pr_cheque_no');?><!--Cheque No--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_trans_pr_cheque_date');?><!--Cheque Date--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                        <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="pullingTab">
            <div class="table-responsive">
                <table id="payment_reversed_table" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 4%">#</th>
                        <th style="min-width:10%">PRVR Code</th>
                        <th style="min-width:10%">PRVR Date</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_pv_pv_code');?><!--PV Code--></th>
                        <th style="min-width:10%"><?php echo $this->lang->line('accounts_payable_trans_pm_pv_date');?><!--PV Date--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_trans_pr_cheque_no');?><!--Cheque No--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('accounts_payable_trans_pr_cheque_date');?><!--Cheque Date--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                        <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
                                        <?php echo $this->lang->line('accounts_payable_pv_sub_item');?>  <!--Sub Item-->
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
                                    <div class="row reversed">
                                        <div class="form-group col-sm-4">
                                            <label><?php echo $this->lang->line('common_date');?><!--Date--> <?php required_mark(); ?></label>
                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="reversalDate"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                       value="<?php echo $current_date; ?>" id="reversalDate"
                                                       class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row reversed">
                                        <div class="form-group col-sm-8">
                                            <label for="inputPassword3"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                                            <div class="">
                                                <input type="hidden" name="payVoucherAutoId" id="payVoucherAutoId">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pull-right reversed">

                                        <button type="button" onclick="payment_revers()" class="btn btn-primary"><?php echo $this->lang->line('accounts_payable_trans_pr_payment_reverese_paymet_voucher');?><!--Reverse Payment Voucher-->
                                        </button>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">

                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong><?php echo $this->lang->line('accounts_payable_trans_pr_payment_paymet_voucher_attachments');?><!--Payment Voucher Attachments--></strong>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    var Otabled;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/PaymentReversal/erp_payment_reversal', 'Test', 'Payment Reversal');
        });
        payment_reversal_table();
        payment_reversed_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#pv_approval_form').bootstrapValidator('revalidateField', 'reversalDate');
        });

        $('#po_close_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                closedDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_trans_pr_purchase_order_date_is_required');?>.'}}},/*Purchase Order Date is required*/
                comments: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_comments_are_required');?>.'}}},/*Comments are required*/
                purchaseOrderID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_trans_pr_purchase_order_id_is_required');?>.'}}},/*Purchase Order ID is required*/
            }
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
                url: "<?php echo site_url('Procurement/save_purchase_order_close'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    $("#purchase_order_modal").modal('hide');
                    stopLoad();
                    Otable.draw();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function payment_reversal_table(selectedID=null) {
        Otable = $('#payment_reversal_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('PaymentReversal/fetch_payment_reversal'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                {}
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['payVoucherAutoId']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "payVoucherAutoId"},
                {"mData": "PVcode"},
                {"mData": "PVdate"},
                {"mData": "PVchequeNo"},
                {"mData": "PVchequeDate"},
                {"mData": "referenceNo"},
                {"mData": "totamount"},
                {"mData": "edit"}
                /* {"mData": "detTransactionAmount"}*/
            ],
            "columnDefs": [{"targets": [7], "orderable": false}, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    function payment_reversed_table(selectedID=null) {
        Otabled = $('#payment_reversed_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('PaymentReversal/fetch_reversed_payment'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                {}
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['payVoucherAutoId']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "payVoucherAutoId"},
                {"mData": "documentSystemCode"},
                {"mData": "documentDate"},
                {"mData": "PVcode"},
                {"mData": "PVdate"},
                {"mData": "PVchequeNo"},
                {"mData": "PVchequeDate"},
                {"mData": "referenceNo"},
                {"mData": "totamount"},
                {"mData": "edit"}
                /* {"mData": "detTransactionAmount"}*/
            ],
            "columnDefs": [{"targets": [9], "orderable": false}, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'paymentReversalAutoID': id},
                    url: "<?php echo site_url('PaymentReversal/delete_payment_reversal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function procu(id) {
        $("#approvel_model").modal("show");
        approvalview(id);
    }

    function approvalview(id) {
        var Otable = $('#approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "Processing": true,
            "ServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Procurement/load_approvel'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'asc']],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [
                {"mData": "approvalLevelID"},
                {"mData": "empname"},
                {"mData": "companyID"},
                {"mData": "documentDate"}
            ],
            "columnDefs": [{
                "targets": [],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "porderid", "value": id});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function fetch_payment_reversal_view(payVoucherAutoId,reversed) {
        if(reversed==1){
            $('.reversed').addClass('hidden');
        }else{
            $('.reversed').removeClass('hidden');
        }
        if (payVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': payVoucherAutoId, 'html': true, 'approval': 1},
                url: "<?php echo site_url('PaymentReversal/load_prvr_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#payVoucherAutoId').val(payVoucherAutoId);
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


    function payment_revers() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('accounts_payable_trans_pr_you_want_to_reverse_this_document');?>",/*You want reverse this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var data = $('#pv_approval_form').serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('PaymentReversal/reverse_paymentVoucher'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#pv_modal").modal('hide');
                            Otable.draw();
                            payment_reversed_table();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

</script>