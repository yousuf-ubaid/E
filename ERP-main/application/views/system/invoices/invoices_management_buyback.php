<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_customer_invoice');
echo head_page($title, true);


/*echo head_page('Customer Invoice',true);*/
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
$customerCategory    = party_category(1, false);
$current_date = current_format_date();
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
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?> </label> <br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3" style="padding-left: 5%">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_category');?> </label><br><!--Category-->
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>$this->lang->line('common_all') /*'All'*/, '1' =>$this->lang->line('sales_markating_transaction_customer_draft') /*'Draft'*/, '2' =>$this->lang->line('common_confirmed')/*'common_confirmed'*/, '3' =>$this->lang->line('common_approved') /*'Approved'*/,'4' => 'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
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
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/invoices/erp_invoices_buyback',null,'Add New Customer Invoice','PV');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create_invoice');?></button><!--Create Invoice-->
    </div>
</div><hr>
<div class="table-responsive">
    <input class="hidden" id="invoiceBuybackVal" value="buybackInvTable">
    <table id="invoice_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></th><!--Invoice Code-->
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_total_value');?></th><!--Total Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
            <th style="min-width: 5%">Status</th><!--Approved-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="documentView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="loaddocument" class="col-md-12"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                <h4 class="modal-title" id="myModalLabel">Invoice Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inviceautoid">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <select name="isPrintDN" id="isPrintDN" class="form-control select2 ">
                            <!--Select Category-->
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_invoicetemp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog" aria-labelledby="myLargeModalLabel" id="buyback_deliverystatus_drilldown">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Delivery Status</h4>
            </div>
            <?php echo form_open('', 'role="form" id="delivery_order_Status"'); ?>
            <input type="hidden" class="form-control" id="invoiceautoid" name="invoiceautoid">
            <input type="hidden" class="form-control" id="confirmedYN" name="confirmedYN">
            <input type="hidden" class="form-control" id="approvedYN" name="approvedYN">
            <div class="modal-body">
                <div class="row" style="margin-top: 10px; margin-left: 15px" id="statusdelivery">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Status<?php required_mark(); ?> :</label>
                    </div>
                    <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('statuschq', array('0'=>'Not Delivered', '1'=>'Delivered'), '', 'class="form-control select2" id="statuschq" required'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                    </div>
                </div>

                <div class="row hide delivereddate" style="margin-top: 10px; margin-left: 15px" id="delivereddate">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Delivered Date<?php required_mark(); ?> :</label>
                    </div>
                    <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="delivereddatebb"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="delivereddatebb" class="form-control">
                                </div>
                                <span class="input-req-inner"></span>
                            </span>
                    </div>
                </div>
                <div class="row hide delivereddate" style="margin-top: 10px; margin-left: 15px" id="comment_label">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment :</label>
                    </div>
                    <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <textarea class="form-control" rows="3" id="comment" name="comment"></textarea>
                                <span class="input-req-inner"></span>
                            </span>
                    </div>
                </div>

                <div class="modal-footer" id="update_status">
                    <button type="button" class="btn btn-sm btn-primary" onclick="updatedeliverystatus()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Update </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var  formDateFilter = window.localStorage.getItem('iFromDate-filter');
    var  toDateFilter = window.localStorage.getItem('iToDate-filter');
    var  customerCodeFilter = window.localStorage.getItem('iCustomerName-filter');
    var  categoryFilter= window.localStorage.getItem('iCategory-filter');
    var  statusFilter = window.localStorage.getItem('istatus-filter');

    formDateFilter = (formDateFilter == null)? '': formDateFilter;
    toDateFilter = (toDateFilter == null)? '': toDateFilter;
    statusFilter = (statusFilter == null)? 'all' : statusFilter;

    $("#IncidateDateFrom").val(formDateFilter);
    $("#IncidateDateTo").val(toDateFilter);
    $("#status").val(statusFilter);

    if(customerCodeFilter != null){
        $("#customerCode").val(customerCodeFilter);
    }

    if(categoryFilter != null){
        $("#category").val(categoryFilter);
    }


    var invoiceAutoID;
    var Otable;
    var InvoiceAutoID = 0;
    var  type = 'BuybackInv';
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        Otable.draw();
    });

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/invoices/invoices_management_buyback','','Customer Invoices');
        });
        invoiceAutoID = null;
        number_validation();
        invoice_table();

        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#category').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        Inputmask().mask(document.querySelectorAll("input"));

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
            "sAjaxSource": "<?php echo site_url('InvoicesPercentage/fetch_invoices_buyback'); ?>",
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
                {"mData": "status"},
                {"mData": "edit"},
                {"mData": "edit"},
                {"mData": "invoiceNarration"},
                {"mData": "customermastername"},
                {"mData": "invoiceDate"},
                {"mData": "invoiceDueDate"},
                {"mData": "invoiceType"},
                {"mData": "total_value_search"},
                {"mData": "invoiceDateformat"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false}, {"visible":false,"searchable": true,"targets": [8,9,10,11,12,13,14,15] }, {"searchable": false,"targets": [0,2,3,4,5,6,7] }],
            "fnServerData": function (sSource, aoData, fnCallback) {

                var fromDate = $("#IncidateDateFrom").val();
                var toDate = $("#IncidateDateTo").val();
                var customerCode = $("#customerCode").val();
                var category = $("#category").val();
                var status = $("#status").val();
                window.localStorage.setItem('iFromDate-filter', fromDate);
                window.localStorage.setItem('iToDate-filter', toDate);
                window.localStorage.setItem('iCustomerName-filter', customerCode);
                window.localStorage.setItem('iCategory-filter', category);
                window.localStorage.setItem('istatus-filter', status);


                aoData.push({"name": "datefrom", "value": fromDate});
                aoData.push({"name": "dateto", "value": toDate});
                aoData.push({"name": "status", "value": status});
                aoData.push({"name": "customerCode", "value": customerCode});
                aoData.push({"name": "category", "value": category});
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
        var rowData = Otable.row(this).data();
        InvoiceAutoID = rowData['invoiceAutoID'];

    });

    if (typeof checkKeyPressed !== 'function'){
        function checkKeyPressed(evt) {

            if (evt.altKey && evt.keyCode==79) {
                evt.preventDefault();
                var table = $('#invoiceBuybackVal').val();
                if((InvoiceAutoID !=0) && table == 'buybackInvTable')
                {
                    isdayClosed(InvoiceAutoID);
                }

            }
        }
    }
    window.addEventListener("keydown", checkKeyPressed, false);

    function isdayClosed(id) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'invoiceAutoID':id},
            url :"<?php echo site_url('InvoicesPercentage/check_dayClose_buybackInvoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
               if(data[0] == 's'){
                   DayClosed_documentView(InvoiceAutoID);
               } else {
                   swal("Cancelled", "Day Not Closed For This Document", "error");
               }
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

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
        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');
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

    function DayClosed_documentView(id) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {'invoiceAutoID':id, 'html' : true},
            url :"<?php echo site_url('InvoicesPercentage/dayClosed_invoices'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                $('#loaddocument').html(data);
                $('#documentView').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');
                stopLoad();
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function load_printtemp(invoiceautoID)
    {
        $('#inviceautoid').val(invoiceautoID);

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {'invoiceAutoID':invoiceautoID},
            url :"<?php echo site_url('InvoicesPercentage/fetch_dn_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){

                stopLoad();
                $('#isPrintDN').empty();
                var mySelect = $('#isPrintDN');
                if(data[0]== 1){
                    mySelect.append($('<option></option>').val(0).html('Print Invoice Only - Full'));
                    mySelect.append($('<option></option>').val(1).html('Print Invoice & Delivery note - Full'));
                    mySelect.append($('<option></option>').val(2).html('Print Delivery note only - Full'));
                    mySelect.append($('<option></option>').val(3).html('Print Invoice Only - Half'));
                    mySelect.append($('<option></option>').val(4).html('Print Invoice & Delivery note - Half'));
                    mySelect.append($('<option></option>').val(5).html('Print Delivery note Only - Half'));
                }else{
                    var mySelect = $('#isPrintDN');
                    mySelect.append($('<option></option>').val(0).html('Print Invoice - Full'));
                    mySelect.append($('<option></option>').val(3).html('Print Invoice - Half'));
                }
                $('#print_temp_modal').modal('show');
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function print_invoicetemp(){
        var printtype =  $('#isPrintDN').val();
        var invoiceID =   $('#inviceautoid').val();

       if(invoiceID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback') ?>" +'/'+ invoiceID +'/'+ printtype +'/'+1);
        }


    }


    function generate_buybackdeliverystatus_drilldown_buyback(invoiceAutoID,deliveredStatus, approvedYN, confirmedYN) {
        $('#invoiceautoid').val(invoiceAutoID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('invoicesPercentage/fetch_deliveryorderstaus'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#statuschq').val(data['deliveryStatus']);
                if(data['deliveryStatus']==1)
                {
                    $('#delivereddatebb').val(data['deliveredDate']);
                    $('#comment').val(data['DeliveryComment']);
                    $('.delivereddate').removeClass('hide');
                }else
                {
                    $('.delivereddate').addClass('hide');
                }
                $('#buyback_deliverystatus_drilldown').modal("show");
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function updatedeliverystatus()
    {
        var data = $('#delivery_order_Status').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('invoicesPercentage/update_deliveryorder_collectiondetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if (data[0] == "s") {
                    Otable.draw();
                    $('#buyback_deliverystatus_drilldown').modal("hide");
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
    $("#statuschq").change(function(){
         if($(this).val() == 1)
         {
            $('.delivereddate').removeClass('hide');

         }else if($(this).val() == 2)
         {
             if(($('#confirmedYN').val() == 1) && ($('#approvedYN').val() != 1)){
                 $('.delivereddate').removeClass()('hide');

             }
         }else {
             $('.delivereddate').addClass()('hide');
         }
    });

</script>