<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$current_date = current_format_date();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_customer_invoice');
echo head_page($title, true);

/*echo head_page('Customer Invoice',true);*/
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?> </label><br><!--Date-->
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?> </label><!--From-->
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small datepic">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?>&nbsp&nbsp</label><!--To-->
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small datepic">
            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?> </label> <br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>$this->lang->line('common_all') /*'All'*/, '1' =>$this->lang->line('sales_markating_transaction_customer_draft') /*'Draft'*/, '2' =>$this->lang->line('common_confirmed')/*'common_confirmed'*/, '3' =>$this->lang->line('common_approved') /*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>
                    <!--Confirmed--> <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                    /<?php echo $this->lang->line('common_not_approved');?>                      <!-- Not Confirmed--><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?> <!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal"
                style="background-color: #7b72e9;border-color: #7b72e9;color: white;"><i class="far fa-clone"></i>Day Close
        </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="invoice_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></th><!--Invoice Code-->
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_total_value');?></th><!--Total Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Item Master From ERP"
     id="itemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Customer Invoice Day Close</h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <label class="title">Date From</label>
                        </div>
                        <div class="form-group col-sm-2" style="margin-left: -10%">
                            <div class="input-group datepicDayClose">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="deliveredDateFrom" onchange="Otables.draw()"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="" id="deliveredDateFrom" class="form-control" required>
                            </div>

                        </div>
                        <div class="form-group col-sm-2" style="margin-left: -1%">
                            <label class="title">To</label>
                        </div>
                        <div class="form-group col-sm-2" style="margin-left: -14%">
                            <div class="input-group datepicDayClose">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="deliveredDateTo" onchange="Otables.draw()"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="" id="deliveredDateTo" class="form-control" required>
                            </div>

                        </div>

                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></th><!--Invoice Code-->
                                <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
                                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_total_value');?></th><!--Total Value-->
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                                <th style="min-width: 5%; text-align: center !important;">Is Eliminated</th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addInvoice()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Add Invoice
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invalid Invoices</h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th >Invoice Code</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    var invoiceAutoID;
    var Otable;
    var Otables;
    var selectedItemsSync = [];
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/invoices/invoices_dayClosing','','Customer Invoices');
        });
        invoiceAutoID = null;
        number_validation();
        invoice_table();
        invoice_table_dayClose();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.datepicDayClose').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            Otables.draw();
        });

        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));

        $("#btnSync_fromErp").click(function () {
            Otables.draw();
            $("#itemMasterFromERP").modal('show');
        });
    });

    function invoice_table(selectedID=null){
        Otable = $('#invoice_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('InvoicesPercentage/fetch_invoices_buyback_dayClose'); ?>",
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
                    if( parseInt(oSettings.aoData[x]._aData['invoiceAutoID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "invoiceAutoID"},
                {"mData": "invoiceCode"},
                {"mData": "invoice_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "invoiceNarration"},
                {"mData": "customermastername"},
                {"mData": "invoiceDate"},
                {"mData": "invoiceDueDate"},
                {"mData": "invoiceType"},
                {"mData": "referenceNo"},
                {"mData": "total_value_search"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [7,8,9,10,11,12,13] },{"searchable": false,"targets": [0,2,3,4,5,6] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
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

    function invoice_table_dayClose(selectedID=null){
        Otables = $('#item_table_sync').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('InvoicesPercentage/fetch_invoices_buyback_dayClose_modelView'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                selectedItemsSync = [];
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('.columnSelected').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('.columnSelected').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });

                $('.columnSelectedEliminate').on('ifChecked', function (event) {
                    InvoiceEliminate(this);
                });
                $('.columnSelectedEliminate').on('ifUnchecked', function (event) {
                    InvoiceEliminate(this);
                });

            },
            "aoColumns": [
                {"mData": "invoiceAutoID"},
                {"mData": "invoiceCode"},
                {"mData": "invoice_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "eliminate"},
                {"mData": "edit"},
                {"mData": "invoiceNarration"},
                {"mData": "customermastername"},
                {"mData": "invoiceDate"},
                {"mData": "invoiceDueDate"},
                {"mData": "invoiceType"},
            ],
            "columnDefs": [{"targets": [6,7], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10,11,12] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#deliveredDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#deliveredDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
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
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
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
                    data : {'invoiceAutoID':id},
                    url :"<?php echo site_url('InvoicesPercentage/delete_invoice_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        Otable.draw();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_customer_invoice(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*/!*Are you sure?*!/*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
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
                    data : {'invoiceAutoID':id},
                    url :"<?php echo site_url('InvoicesPercentage/referback_customer_invoice'); ?>",
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
        $('#customerCode').multiselect2('deselectAll', false);
        $('#customerCode').multiselect2('updateButtonText');
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
                    data : {'invoiceAutoID':id},
                    url :"<?php echo site_url('InvoicesPercentage/re_open_invoice'); ?>",
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

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function InvoiceEliminate(item) {
        var value = $(item).val();
        var checkedval=0;
        if ($(item).is(':checked')) {
            checkedval=1
        }
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'checkedval':checkedval,'invoiceId':value},
            url :"<?php echo site_url('InvoicesPercentage/update_isEliminated'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0],data[1]);
            },error : function(){
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });



    }
    function addInvoice(){
        if (selectedItemsSync.length > 0) {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'selectedInvoices':selectedItemsSync},
                url :"<?php echo site_url('InvoicesPercentage/day_close_invoice'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0],data[1]);
                    $("#itemMasterFromERP").modal('hide');
                    Otables.draw();

                    if (jQuery.isEmptyObject(data[2])) {

                    } else {
                        $('#errormsg').empty();
                        $.each(data[2], function (key, value) {
                            $('#errormsg').append('<tr><td>' + value['itemcode'] + '</td><td>' + value['itemDescription'] + '</td></tr>');
                        });
                        $('#invalidinvoicemodal').modal('show');
                    }

                },error : function(){
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }else{
            myAlert('e','Select Invoice');
        }

    }
</script>