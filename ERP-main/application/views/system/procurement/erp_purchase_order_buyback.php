<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('procurement_approval_manage_purchase_order');
echo head_page($title, true);

$segment_arr = fetch_segment(true, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false); ?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<!--<style>
    .hortree-wrapper *, .hortree-wrapper *:before, .hortree-wrapper *:after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .hortree-wrapper {
        position: relative;
        font-family: sans-serif, Verdana, Arial;
        font-size: 0.9em;
    }
    .hortree-branch {
        position: relative;
        margin-left: 180px;
    }
    .hortree-branch:first-child { margin-left: 0; }
    .hortree-entry {
        position: relative;
        margin-bottom: 50px;
    }
    .hortree-label {
        display: block;
        width: 150px;
        padding: 2px 5px;
        line-height: 30px;
        text-align: center;
        border: 2px solid #4b86b7;
        border-radius: 3px;
        position: absolute;
        left: 0;
        z-index: 10;
        background: #fff;
    }
</style>-->
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date'); ?><!--Date--></label><br>
            <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                   class="input-small">
            <label for="supplierPrimaryCode">&nbsp;&nbsp;<?php echo $this->lang->line('common_to'); ?><!--To-->&nbsp;&nbsp;</label>
            <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                   class="input-small">
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">
                <?php echo $this->lang->line('common_supplier_name'); ?><!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-2">
            <label for="">Status</label><br>

            <div style="width: 80%;">
                <?php echo form_dropdown('isReceived', array('all' => $this->lang->line('common_all')/*'All'*/, '0' => 'Not Received', '1' => 'Partially Received', '2' => 'Fully Received', '3' => 'Closed'), '', 'class="form-control" id="isReceived" onchange="Otable.draw()"'); ?>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">Document Status</label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' => $this->lang->line('common_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i>
                <?php echo $this->lang->line('common_clear'); ?><!--Clear-->
            </button>
        </div>
        <div class="form-group col-sm-3">
            <label for="Segment">Segment</label><br>
            <div style="width: 60%;">
                <?php echo form_dropdown('segmentID[]', $segment_arr, '', ' class="form-control" multiple="multiple" id="segmentID" onchange="Otable.draw()" '); ?></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved--></td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed--> /
                    <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved--></td>
                <td><span class="label label-warning">&nbsp;</span>
                    <?php echo $this->lang->line('common_refer_back'); ?><!--Refer-back--></td>
                <td><span class="label label-info">&nbsp;</span>
                    <?php echo $this->lang->line('common_closed'); ?><!--Closed--> </td>
            </tr>
        </table>
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/procurement/erp_purchase_order_new_buyback',null,'<?php echo $this->lang->line('procurement_approval_add_purchase_order'); ?>','PO');">
            <!--/*Add Purchase Order*/--><i
                    class="fa fa-plus"></i>
            <?php echo $this->lang->line('procurement_approval_create_purchase_order'); ?><!--Create Purchase Order-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="purchase_order_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 15%">
                <?php echo $this->lang->line('procurement_approval_po_number'); ?><!--PO Number--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?><!--Total Value--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 5%">Status</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            <th style="min-width: 1%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="approvel_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_approval'); ?><!--Approval--></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="approval_table" class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_approval_level'); ?><!--Approval Level--></th>
                            <th>
                                <?php echo $this->lang->line('common_document_confirmed_by'); ?><!--Document Confirmed By--></th>
                            <th><?php echo $this->lang->line('common_company_id'); ?><!--Company ID--></th>
                            <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="purchase_order_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('procurement_approval_close_purchase_order'); ?><!--Close Purchase Order--></h4>
            </div>
            <form class="form-horizontal" id="po_close_form">
                <div class="modal-body">
                    <div id="conform_body"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_date'); ?><!--Date--></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="closedDate" value="<?php echo date('Y-m-d'); ?>"
                                       id="closedDate" class="form-control" required>
                            </div>
                            <input type="hidden" name="purchaseOrderID" id="purchaseOrderID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $this->lang->line('procurement_approval_close_purchase_order'); ?><!--Close Purchase Order--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 35%">
        <form method="post" id="Send_Email_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="purchaseOrderID" id="email_purchaseOrderID" value="">
                    <h4 class="modal-title" id="EmailHeader">Email</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="emailContent">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <div class="append_data_nw">
                        <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                            <div class="col-sm-1">
                            </div>
                            <div class="col-sm-8">
                                <input type="email" name="email" id="email" class="form-control"
                                       placeholder="example@example.com" style="margin-left: -10px">
                            </div>
                            <div class="col-sm-1 remove-tdnw">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="SendPoMail()">Send Email</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 70%">
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>-->
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="tracingId" name="tracingId">
                    <input type="hidden" id="tracingCode" name="tracingCode">
                    <div id="mcontainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
                </div>
            </div>
    </div>
</div>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Purchase Order Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="purchaseOrder_ID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page'), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_po_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    var segmentDrop = $('#segmentID');
    $(document).ready(function () {

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('.headerclose').click(function () {
            fetchPage('system/procurement/erp_purchase_order', 'Test', 'Purchase Order');
        });
        purchase_order_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));

        $('#po_close_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                closedDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('procurement_approval_purchase_order_date_is_required');?>.'}}}, /*Purchase Order Date is required*/
                comments: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_comments_are_required');?>.'}}}, /*Comments are required*/
                purchaseOrderID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('procurement_approval_purchase_order_id_is_required');?>.'}}}, /*Purchase Order ID is required*/
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
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function purchase_order_table(selectedID=null) {
        Otable = $('#purchase_order_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Procurement/fetch_purchase_order_buyback'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                {
                    "targets": [7],
                    "visible": false,
                    "searchable": false
                }
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
                    if (parseInt(oSettings.aoData[x]._aData['purchaseOrderID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "purchaseOrderID"},
                {"mData": "purchaseOrderCode"},
                {"mData": "po_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "isReceivedlbl"},
                {"mData": "edit"},
                {"mData": "purchaseOrderID"},
                {"mData": "narration"},
                {"mData": "supliermastername"},
                {"mData": "expectedDeliveryDate"},
                {"mData": "transactionCurrency"},
                {"mData": "purchaseOrderType"},
                {"mData": "detTransactionAmount"},
                {"mData": "documentDate"},
                {"mData": "documentDatepofilter"}
            ],
            "columnDefs": [{"targets": [7], "orderable": false},{"targets": [1,0,3], "visible": true,"searchable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [8, 9, 10, 11, 12, 13, 14,15,16]
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
                aoData.push({"name": "isReceived", "value": $("#isReceived").val()});
                aoData.push({"name": "segmentID", "value": $("#segmentID").val()});
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
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': id},
                    url: "<?php echo site_url('Procurement/delete_purchase_order'); ?>",
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

    function po_close(purchaseOrderID) {
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseOrderID': purchaseOrderID, 'html': true},
                url: "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#purchaseOrderID').val(purchaseOrderID);
                    $("#purchase_order_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function procu(id) {
        $("#approvel_model").modal("show");
        approvalview(id);
    }

    function approvalview(id) {
        var Otable = $('#approval_table').DataTable({
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
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
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

    function referbackprocument(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>", /*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': id},
                    url: "<?php echo site_url('Procurement/referback_procurement'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#isReceived').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }


    function reOpen_contract(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': id},
                    url: "<?php echo site_url('Procurement/re_open_procurement'); ?>",
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

    function sendemail(id) {
        $('#email_purchaseOrderID').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': id},
            url: "<?php echo site_url('Procurement/loademail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#Email_modal").modal();
                if (!jQuery.isEmptyObject(data)) {
                    $('#email').val(data['supplierEmail']);
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }
    function SendPoMail() {
        var form_data = $("#Send_Email_form").serialize();
        swal({
                title: "Are You Sure?",
                text: "You Want To Send This Mail",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: form_data,
                    url: "<?php echo site_url('Procurement/send_po_email'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#Email_modal").modal('hide');

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function traceDocument(poID,DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': poID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/trace_po_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(poID,DocumentID);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_document_tracing(id,DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'purchaseOrderID': id,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#mcontainer").empty();
                $("#mcontainer").html(data);
                $("#tracingId").val(id);
                $("#tracingCode").val(DocumentID);

                $("#tracing_modal").modal('show');

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function deleteDocumentTracing(){
        var purchaseOrderID=$("#tracingId").val();
        var DocumentID=$("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#tracing_modal").modal('hide');
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
    function load_printtemp(purchaseOrder_ID)
    {
        $('#printSize').val(1);
        $('#purchaseOrder_ID').val(purchaseOrder_ID);
        $('#print_temp_modal').modal('show');


    }
    function print_po_temp(){
        var printSize =  $('#printSize').val();
        var purchaseOrder_ID =   $('#purchaseOrder_ID').val();

        if(purchaseOrder_ID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Procurement/load_purchase_order_conformation_buyback') ?>" +'/'+ purchaseOrder_ID +'/'+ printSize +'/'+1);
        }


    }


</script>