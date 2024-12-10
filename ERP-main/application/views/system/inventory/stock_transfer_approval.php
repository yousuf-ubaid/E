<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('transaction_stock_transfer_approval');
echo head_page($title, false);

/*echo head_page('Stock Transfer Approval', false); */ ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?>
                </td><!-- Approved-->
                <td><span
                        class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved'); ?>
                </td><!--Not Approved-->
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
    <table id="stock_transfer_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('transaction_stock_transfer_code'); ?> </th>
            <!--SR Code-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date'); ?> </th><!--Date-->
            <th style="min-width: 30%"><?php echo $this->lang->line('transaction_common_warehouse_description'); ?> </th><!--Warehouse Description-->
            <th style="min-width: 30%"><?php echo $this->lang->line('common_reference_no');?></th><!--Reference No-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_confirmed_by'); ?> </th><!--Confirmed By-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level'); ?> </th><!--Level-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="stock_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"><?php echo $this->lang->line('transaction_stock_transfer_approval'); ?> </h4>
                <!--Stock Transfer Approval-->
            </div>
            <form class="form-horizontal" id="approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="st_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                         data-toggle="tab"
                                                                                         onclick="tabView()"><?php echo $this->lang->line('common_view'); ?> </a>
                            </li><!--View-->
                            <li id="st_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab"
                                                                          onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment'); ?> </a>
                                <!--Attachment-->
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3"
                                           class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?>  </label>
                                    <!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="stockTransferAutoID" id="stockTransferAutoID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3"
                                           class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments'); ?> </label>
                                    <!--Comments-->

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                                    <!--Close-->
                                    <button type="submit"
                                            class="btn btn-primary"><?php echo $this->lang->line('common_submit'); ?>  </button>
                                    <!--Submit-->
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp
                                    <strong><?php echo $this->lang->line('transaction_stock_transfer_attachments'); ?>  </strong>
                                    <!--Stock Transfer Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name'); ?>  </th>
                                            <!--File Name-->
                                            <th><?php echo $this->lang->line('common_description'); ?>  </th>
                                            <!--Description-->
                                            <th><?php echo $this->lang->line('common_type'); ?>  </th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="st_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5"
                                                class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                                            <!--No Attachment Found-->
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
            fetchPage('system/inventory/stock_transfer_approval', 'Test', 'Stock Transfer Approval');
        });
        stock_transfer_table();
        $('#approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}}, /*Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                stockTransferAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_stock_transfer_id_required');?>.'}}}, /*Stock Transfer ID is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
            }
        }).on('success.form.bv', function (e) {
            var jobclosed = $('#jobClosed').val();
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            if(jobclosed == 1) {
                swal(
                    {
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                        text: "The Assigned Job Is Already Closed",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Approve",
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('Inventory/save_stock_transfer_approval'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                //refreshNotifications(true);
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    $("#stock_transfer_modal").modal('hide');
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
                                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                                /*An Error Occurred! Please Try Again*/
                                stopLoad();
                                //refreshNotifications(true);
                            }
                        });
                    });
            } else {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Inventory/save_stock_transfer_approval'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //refreshNotifications(true);
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#stock_transfer_modal").modal('hide');
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
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        //refreshNotifications(true);
                    }
                });
            }

        });
    });

    function stock_transfer_table() {
        Otable = $('#stock_transfer_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Inventory/fetch_stock_transfer_approval'); ?>",
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
                {"mData": "stockTransferAutoID"},
                {"mData": "stockTransferCode"},
                {"mData": "tranferDate"},
                {"mData": "detail"},
                {"mData": "referenceNo"},
                {"mData": "confirmedByName"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "form_wareHouseCode"},
                {"mData": "form_wareHouseLocation"},
                {"mData": "form_wareHouseDescription"},
                {"mData": "to_wareHouseCode"},
                {"mData": "to_wareHouseLocation"},
                {"mData": "to_wareHouseDescription"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"targets": [8], "orderable": false}, {"targets": [0,3,7,8], "searchable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [9, 10, 11, 12, 13, 14]
            }],
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

    function fetch_approval(stockTransferAutoID, documentApprovedID, Level) {
        if (stockTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'stockTransferAutoID': stockTransferAutoID, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#stockTransferAutoID').val(stockTransferAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#stock_transfer_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    stockTransfer_attachment_View_modal('ST', stockTransferAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function stockTransfer_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#st_attachement_approval_Tabview_a").removeClass("active");
        $("#st_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#st_attachment_body').empty();
                    $('#st_attachment_body').append('' +data+ '');

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
</script>