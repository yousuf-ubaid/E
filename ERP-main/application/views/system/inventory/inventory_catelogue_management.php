<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_inventory_catelogue');
echo head_page($title, true);

$location_arr       = all_delivery_location_drop(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode">Date </label><br><!--Date-->
                <label for="supplierPrimaryCode">From </label>
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?> &nbsp&nbsp</label><!--To-->
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode">Warehouse Location </label><br>
            <?php echo form_dropdown('location[]', $location_arr, '', 'class="form-control" id="location" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode">Status </label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>'All', '1' => 'Draft', '2' =>'Confirmed', '3' =>'Approved','4'=>' Refer-back','5'=>' Closed'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i>Clear
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span><?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?> <!--Confirmed / Approved-->

                    </td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?> / <?php echo $this->lang->line('common_not_approved');?> <!--Not Confirmed  /  Not Approved-->

                    </td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!-- Refer-back-->
                    </td>
                </tr>
            </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/inventory/inventory_catalogue_request',null,' <?php echo $this->lang->line('common_add_new_inventory_catalogue');?>','MIC');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_inventory_catlogue');?><!--Create Material Request--></button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="material_request_master_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('transaction_material_receipt_code');?><!--MR Code--></th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?><!--Details--> </th>
                <!--<th style="min-width: 15%">Total Value </th>-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> </th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved --></th>
                <th style="min-width: 120px;"><?php echo $this->lang->line('common_action');?><!--Action--> </th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="mr_close_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Close Material Request</h4>
            </div>
            <form class="form-horizontal" id="mr_close_form">
                <div class="modal-body">
                    <div id="mr_close"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_date'); ?><!--Date--></label>
                        <div class="col-sm-4">

                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="closedDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="closedDate" class="form-control" required>
                            </div>
                            <input type="hidden" name="mrAutoID" id="mrAutoID">
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
                    <button type="submit" class="btn btn-primary">Close Document</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="closed_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="closed_user_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt>Document code</dt>
                    <dd id="c_document_code">...</dd>
                    <dt>Document Date</dt>
                    <dd id="c_document_date">...</dd>
                    <dt>Confirmed Date</dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="6">Approved Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th>Approved Date</th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="approved_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="6" class="text-center">
                            <?php echo $this->lang->line('footer_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
                <br><br>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">Closed Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th>Closed Date</th>
                        <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody id="closed_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="4" class="text-center">Document not closed yet.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;"><?php echo $this->lang->line('common_document_tracing'); ?><!--Document Tracing--> <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i> </button></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
var grvAutoID;
var Otable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/inventory/inventory_catalogue_management','Test','Inventory');
    });
    grvAutoID = null;
    number_validation();
    fetch_inventory_catalogue();

    $('#location').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
    });

    Inputmask().mask(document.querySelectorAll("input"));

    $('#mr_close_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            // contractAutoID: {validators: {notEmpty: {message: 'contractAutoID is required.'}}},
            comments: {validators: {notEmpty: {message: 'comments is required.'}}},
            closedDate: {validators: {notEmpty: {message: 'Date is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'mrAutoID', 'value': $('#mrAutoID').val()});
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to close this document!",/*You want to close this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Inventory/close_material_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        $("#mr_close_modal").modal('hide');
                        stopLoad();
                        Otable.draw();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    });
});

function fetch_inventory_catalogue(selectedID=null){
     Otable = $('#material_request_master_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Inventory/fetch_inventory_catalogue'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                if( parseInt(oSettings.aoData[x]._aData['itemIssueAutoID']) == selectedRowID ){
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');
                }
                x++;
            }
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');
        },
        "aoColumns": [
            {"mData": "mrAutoID"},
            {"mData": "MRCode"},
            {"mData": "MR_detail"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"}
            // {"mData": "requestedDate"},
            // {"mData": "tot_value"},
            // {"mData": "referenceNo"}

        ],
        //"columnDefs": [{"targets": [2], "orderable": false}],
        "columnDefs": [],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
            aoData.push({"name": "status", "value": $("#status").val()});
            aoData.push({"name": "location", "value": $("#location").val()});
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

function delete_item(id,value){
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
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'mrAutoID':id},
                url :"<?php echo site_url('Inventory/delete_material_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    Otable.draw();
                    stopLoad();
                    refreshNotifications(true);
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        });        
}

// function show_addon(id){
//     //$('#addon_form')[0].reset();
//     //$('#addon_form').bootstrapValidator('resetForm', true);
//     grvAutoID = id;
//     fetch_addons(id);
// }

// function fetch_addons(id){
    
// }

// function addon_form_reset(){
//     $('#description').val('');
//     $('#uom').val('Each');
//     $('#qty').val(0);
//     $('#supplier').val('');
//     $('#unit_cost').val(0);
//     $('#sub_total').html(0.00);
// }

// $(".number").keyup(function(){
//     var qty         = parseFloat($('#qty').val());
//     var unit_cost   = parseFloat($('#unit_cost').val());
//     $('#sub_total').html(parseFloat(qty*unit_cost).toFixed(2));
// });

    function referbackgrv(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'mrAutoID':id},
                    url :"<?php echo site_url('Inventory/referback_materialrequest'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

function clear_all_filters(){
    $('#IncidateDateFrom').val("");
    $('#IncidateDateTo').val("");
    $('#status').val("all");
    $('#location').multiselect2('deselectAll', false);
    $('#location').multiselect2('updateButtonText');
    Otable.draw();
}

function reOpen_contract(id){
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'mrAutoID':id},
                url :"<?php echo site_url('Inventory/re_open_material_request'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    Otable.draw();
                    stopLoad();
                    refreshNotifications(true);
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function materialRequest_close(mrAutoID) {
    if(mrAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'mrAutoID': mrAutoID, 'html': true},
            url: "<?php echo site_url('Inventory/load_material_request_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#mrAutoID').val(mrAutoID);
                $("#mr_close_modal").modal({backdrop: "static"});
                $('#mr_close').html(data);
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

function fetch_approval_closed_user_modal(documentID, documentSystemCode) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
        url: '<?php echo site_url('Approvel_user/fetch_document_closed_users_modal'); ?>',
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $('#closed_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Closed User');
             /* Approval user */
            $('#closed_user_body').empty();
            $('#approved_user_body').empty();
            /** Approval Details*/
            x = 1;
            if (jQuery.isEmptyObject(data['approved'])) {
                $('#approved_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                /* No Records Found */
            } else {
                $.each(data['approved'], function (key, value) {
                    comment = ' - ';
                    if (value['approvedComments'] !== null) {
                        comment = value['approvedComments'];
                    }
                    approvalDate = ' - ';
                    if (value['approveDate'] !== null) {
                        approvalDate = value['approveDate'];
                    }
                    bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                    $('#approved_user_body').append('<tr><td>' + x + '</td><td>' +  value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">' + approvalDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                    x++;
                });
            }

            /** Closed Details*/
            if (data['closedYN'] == 1) {
                $('#closed_user_body').append('<tr><td> 1 </td><td>' + data['closedBy'] + '</td><td class="text-center">' + data['closedDate'] + '</td><td class="text-center">' + data['closedReason'] + '</td></tr>');
            } else {
                $('#closed_user_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
               /*  No Records Found */
            }

            /** confirmed Details*/
            $("#closed_user_modal").modal({backdrop: "static", keyboard: true});
            $("#c_document_code").html(data['document_code']);
            $("#c_document_date").html(data['document_date']);
            $("#c_confirmed_date").html(data['confirmed_date']);
            $("#c_conformed_by").html(data['conformed_by']);

            if (documentID == 'LA' && (data.requestForCancelYN !== undefined)) {
                $('#closed_user_label').append(' - Cancellation');
            }
            stopLoad();
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            /*An Error Occurred! Please Try Again*/
            stopLoad();
            refreshNotifications(true);
        }
    });
}


function traceDocument(poID,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'materialRequestID': poID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/trace_mr_document'); ?>",
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
</script>