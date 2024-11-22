<?php
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
?>
<div id="salesReturnManagement_div">
    <?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_transaction', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $title = $this->lang->line('sales_markating_transaction_sales_return');
    echo head_page($title, true);
/*echo head_page('Sales Return', true);*/ ?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

    <div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-4">
                <div class="custom_padding">
                    <label for="customerPrimaryCode"><?php echo $this->lang->line('common_date');?> </label><br><!--Date-->
                    <label for="customerPrimaryCode"><?php echo $this->lang->line('common_from');?></label><!--From-->
                    <input type="text" name="IncidateDateFrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16"
                           onchange="Otable.draw()" value="" id="IncidateDateFrom"
                           class="input-small">
                    <label for="customerPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?> &nbsp&nbsp</label><!--To-->
                    <input type="text" name="IncidateDateTo"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16"
                           onchange="Otable.draw()" value="" id="IncidateDateTo"
                           class="input-small">

                </div>

            </div>
            <div class="form-group col-sm-4">
                <label for="customerPrimaryCode"><?php echo $this->lang->line('common_customer_name');?>  </label><br><!--Customer Name-->
                <?php echo form_dropdown('customerPrimaryCode[]', $customer_arr, '', 'class="form-control" id="customerPrimaryCode" onchange="Otable.draw()" multiple="multiple" style="height: 30px"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerPrimaryCode"><?php echo $this->lang->line('common_status');?>  </label><br><!--Status-->

                <div style="width: 60%;">
                    <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' => $this->lang->line('sales_markating_transaction_customer_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' =>$this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
                <button type="button" class="btn btn-primary pull-right"
                        onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
                </button><!--Clear-->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>

                    </td><!--Confirmed--><!--Approved-->
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                        / <?php echo $this->lang->line('common_not_approved');?>
                    </td><!--Not Confirmed--><!-- Not Approved-->
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?>
                    </td><!--Refer-back-->
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary-new size-sm pull-right"
                    onclick="fetchPage('system/inventory/erp_sales_return',null,'Add New Sales Return','SLR');"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create_sales_return');?>
            </button><!--Create Sales Return-->
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="sales_return_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"> <?php echo $this->lang->line('common_code');?> </th><!--Code-->
                <th><?php echo $this->lang->line('common_customer_name');?> </th><!--Customer Name-->
                <th style="min-width: 15%"><?php echo $this->lang->line('common_warehouse');?> </th><!--Warehouse-->
                <th><?php echo $this->lang->line('common_reference_no');?> </th><!--Reference No-->
                <!--<th style="min-width: 10%">Currency</th>-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?> </th><!--Date-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?> </th><!--Confirmed-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>
    <?php echo footer_page('Right foot', 'Left foot', false); ?>
</div>

<div id="salesReturnCreateNew_div">

</div>

<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;"><?php echo $this->lang->line('common_document_tracing');?><!--Document Tracing-->   <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i></button></h4>
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

<script type="text/javascript">
    /**

     function hideAllDiv() {
        $("#salesReturnCreateNew_div").hide();
        $("#salesReturnManagement_div").hide();
    }
     function createNewSalesReturn(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'salesReturnID': id},
            url: "<?php echo site_url('Inventory/createNewSalesReturn'); ?>",
            beforeSend: function () {
                startLoad();
                hideAllDiv();
            },
            success: function (data) {
                stopLoad();
                $("#salesReturnManagement_div").show();
                $("#salesReturnManagement_div").html(data);

            }, error: function () {
                $("#salesReturnManagement_div").html('<div class="alert alert-danger">An error has occured</div>');
            }
        });
    }*/

    var grvAutoID;
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            /* fetchPage('system/inventory/stock_return_management', 'Test', 'Purchase Return');*/
            fetchPage('system/invoices/sales_return', '', 'Sales Return ')
        });
        grvAutoID = null;
        number_validation();
        sales_return_table();

        $('#customerPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function sales_return_table(selectedID=null) {

        Otable = $('#sales_return_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Inventory/fetch_sales_return_table'); ?>",
            "aaSorting": [[0, 'desc']],
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
                    if (parseInt(oSettings.aoData[x]._aData['salesReturnAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "salesReturnAutoID"},
                {"mData": "salesReturnCode"},
                {"mData": "customerName"},
                {"mData": "sr_detail"},
                {"mData": "referenceNo"},
                /*{"mData": "transactionCurrency"},*/
                {"mData": "returnDate"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "wareHouseLocation"}
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [8], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [9]
            }, {"searchable": false, "targets": [0,3,6,7]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "customerPrimaryCode", "value": $("#customerPrimaryCode").val()});
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
                type: "warning",/*warning*/
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
                    data: {'salesReturnAutoID': id},
                    url: "<?php echo site_url('Inventory/delete_sales_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                        stopLoad();
                    }
                });
            });
    }

    function referback_sales_return(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'salesReturnAutoID': id},
                    url: "<?php echo site_url('Inventory/referback_sales_return'); ?>",
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
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#customerPrimaryCode').multiselect2('deselectAll', false);
        $('#customerPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }

    function reOpen_contract(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'salesReturnAutoID':id},
                    url :"<?php echo site_url('Inventory/re_open_inventory'); ?>",
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

    function traceDocument(salesReturnAutoID, DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'salesReturnAutoID': salesReturnAutoID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/trace_slr_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(salesReturnAutoID,DocumentID);
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
        var salesReturnAutoID=$("#tracingId").val();
        var DocumentID=$("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': salesReturnAutoID,'DocumentID': DocumentID},
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