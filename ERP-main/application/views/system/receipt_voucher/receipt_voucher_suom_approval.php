<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_approval');
echo head_page($title, false);
/*echo head_page('Receipt Voucher Approval', false); */?>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span>
                        <?php echo $this->lang->line('common_approved');?><!--Approved-->
                    </td>
                    <td><span class="label label-danger">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-center">
            <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="receipt_voucher_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><!--RV Code--> <?php echo $this->lang->line('accounts_receivable_common_rv_code');?></th>
                <th style="min-width: 10%"><!--Document Date--><?php echo $this->lang->line('common_document_date');?></th>
                <th style="min-width: 30%"><!--Narration--><?php echo $this->lang->line('common_narration');?></th>
                <th style="min-width: 20%"><!--Customer Name--><?php echo $this->lang->line('common_customer_name');?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_value');?><!--Value--></th>
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
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_approval');?><!--Receipt voucher Approval--></h4>
                </div>
                <form class="form-horizontal" id="pv_approval_form">
                    <div class="modal-body">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                <li id="rv_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                             data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                <li id="rv_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-11">
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="Tab-home-v">
                                    <div id="conform_body"></div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back') /*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="receiptVoucherAutoId" id="receiptVoucherAutoId">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                        <div class="col-sm-8">
                                            <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                    </div>
                                </div>
                                <div class="tab-pane hide" id="Tab-profile-v">
                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_attachments');?><!--Receipt Voucher Attachments--></strong>
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
                                            <tbody id="rv_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/receipt_voucher/receipt_voucher_suom_approval', '', 'Receipt Voucher Approval');
            });
            receipt_voucher_table();
            $('#pv_approval_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                    Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_ap_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                    //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                    receiptVoucherAutoId: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_payment_voucher_id_is_required');?>.'}}},/*Payment Voucher ID is required*/
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
                    url: "<?php echo site_url('Receipt_voucher/save_rv_approval_suom'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //refreshNotifications(true);
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#pv_modal").modal('hide');
                            Otable.draw();
                            $form.bootstrapValidator('disableSubmitButtons', false);
                        } else if ($.isArray(data[2])) {
                            $('#insufficient_item_body').html('');
                            $.each(data[2], function (item, value) {
                                $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                            });
                            $("#insufficient_item_modal").modal({backdrop: "static"});
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
        });

        function receipt_voucher_table() {
            Otable = $('#receipt_voucher_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Receipt_voucher/fetch_Receipt_voucher_approval_suom'); ?>",
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
                    {"mData": "receiptVoucherAutoId"},
                    {"mData": "RVcode"},
                    {"mData": "RVdate"},
                    {"mData": "RVNarration"},
                    {"mData": "customerName"},
                    {"mData": "total_value"},
                    {"mData": "confirmed"},
                    {"mData": "approved"},
                    {"mData": "edit"},
                    {"mData": "detTransactionAmount"},
                    {"mData": "transactionCurrency"}
                    //{"mData": "edit"},
                ],
                "columnDefs": [{"targets": [7,8], "orderable": false},{"targets": [0,5,7,8], "searchable": false},{"targets": [9,10], "searchable": true,"visible": false, "orderable": false}],
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

        function fetch_approval(receiptVoucherAutoId, documentApprovedID, Level) {
            if (receiptVoucherAutoId) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'html': true,'approval': 1},
                    url: "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#receiptVoucherAutoId').val(receiptVoucherAutoId);
                        $('#documentApprovedID').val(documentApprovedID);
                        $('#Level').val(Level);
                        $("#pv_modal").modal({backdrop: "static"});
                        $('#conform_body').html(data);
                        $('#comments').val('');
                        receiptVoucher_attachment_View_modal('RV', receiptVoucherAutoId);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
        }
        function receiptVoucher_attachment_View_modal(documentID, documentSystemCode) {
            $("#Tab-profile-v").removeClass("active");
            $("#Tab-home-v").addClass("active");
            $("#rv_attachement_approval_Tabview_a").removeClass("active");
            $("#rv_attachement_approval_Tabview_v").addClass("active");
            if (documentSystemCode) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                    dataType: 'json',
                    data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                    success: function (data) {
                        $('#rv_attachment_body').empty();
                        $('#rv_attachment_body').append('' +data+ '');

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#ajax_nav_container').html(xhr.responseText);
                    }
                });
            }
        }

        function tabAttachement(){
            $("#Tab-profile-v").removeClass("hide");
        }
        function tabView(){
            $("#Tab-profile-v").addClass("hide");
        }

    </script>

<?php
