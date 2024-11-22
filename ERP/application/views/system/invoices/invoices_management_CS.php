<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_customer_invoice');
echo head_page($title, true);
$financeyear_arr = all_financeyear_drop(true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$financeyearperiodYN = getPolicyValues('FPC', 'All');

/*echo head_page('Customer Invoice',true);*/
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?> </label><br><!--Date-->
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?> </label><!--From-->
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?>&nbsp&nbsp</label><!--To-->
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">
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
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/invoices/erp_invoices_cs',null,'<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice');?>','PV');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create_invoice');?></button><!--Create Invoice-->
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
<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <form method="post" id="Send_Email_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="invoiceid" id="email_invoiceid" value="">
                    <h4 class="modal-title" id="EmailHeader">Email</h4>
                </div>
                <div class="modal-body">
                    <div id="emailview"> </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing  <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i> </button>
            </div></h4>
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



<div class="modal fade" id="receipt_voucher_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="receiptvoucher_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Receipt Voucher</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vouchertype" name="vouchertype" value="Invoices">
                    <input type="hidden" id="invoicID" name="invoicID">
                    <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="customerID" name="customerID">
                    <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">
                    <div class="row">
                        <div class="col-sm-4"><span style="color: black;font-family: sans-serif;" id="invoiceBal"></span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date');?><!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="RVdate"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                            <input type="text" name="referenceno" id="referenceno" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if($financeyearperiodYN==1){
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="RVbankCode"><?php echo $this->lang->line('accounts_receivable_common_bank_or_cash');?><!--Bank or Cash--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="RVbankCode" onchange="set_payment_method()" required'); ?>
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_number');?><!--Cheque Number--></label>
                            <input type="text" name="RVchequeNo" id="RVchequeNo" class="form-control">
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_date');?><!--Cheque Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVchequeDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="RVchequeDate"
                                       class="form-control">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
var invoiceAutoID;
var Otable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/invoices/invoices_management_CS','','Customer Invoices');
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
    Inputmask().mask(document.querySelectorAll("input"));
    $(".paymentmoad").hide();
    $('.select2').select2();

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        $('#receiptvoucher_form').bootstrapValidator('revalidateField', 'RVdate');
    });
    FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
    periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
    fetch_finance_year_period(FinanceYearID, periodID);



    $('#receiptvoucher_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            RVdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_receipt_voucher_date_is_required');?>.'}}},/*Receipt Voucher Date is required*/
            RVbankCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_bank_or_cash_is_required');?>.'}}}/*Bank or Cash is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
        data.push({'name': 'bank', 'value': $('#RVbankCode option:selected').text()});
        data.push({'name': 'invoiceAutoID', 'value': $('#invoicID').val()});
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to create this document!",/*You want to confirm this document!*/
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
                    url: "<?php echo site_url('Invoices/save_receiptvoucher_from_CINV_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if(data[0]=='s'){
                            $("#segment").val('');
                            $("#customerID").val('');
                            $("#invoicID").val('');
                            $("#transactionCurrencyID").val('');
                            $("#referenceno").val('');
                            $("#RVbankCode").val('').change();
                            $("#RVchequeNo").val('');
                            $("#receipt_voucher_modal").modal('hide');
                            confirmReceiptVoucher(data[2])
                        }else{
                            stopLoad();
                            myAlert(data[0],data[1]);
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
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
        "sAjaxSource": "<?php echo site_url('Invoices/fetch_invoices_commission'); ?>",
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
            {"mData": "total_value_search"},
            {"mData": "transactionCurrency"}
        ],
        "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [7,8,9,10,11,12,13,14] },{"targets": [0,3], "visible": true,"searchable": false}],
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
                url :"<?php echo site_url('Invoices/delete_invoice_master'); ?>",
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

    function referback_customer_invoice(id,isSytemGenerated){
    if(isSytemGenerated!=1)
    {
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
                    url :"<?php echo site_url('Invoices/referback_customer_invoice'); ?>",
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
    }else
    {
        swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
    }

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
                url :"<?php echo site_url('Invoices/re_open_invoice'); ?>",
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
function sendemail(id) {
    $('#email_invoiceid').val(id);
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {'invoiceAutoID': id},
        url: "<?php echo site_url('Invoices/invoiceloademail'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $("#emailview").html(data);
            $("#Email_modal").modal();
         /*   alert(data['customerEmail']);
            $("#Email_modal").modal();
            if (!jQuery.isEmptyObject(data)) {
                $('#email').val(data['customerEmail']);
            }*/
            load_mail_history();
            stopLoad();
            refreshNotifications(true);
        }, error: function () {
            stopLoad();
            alert('An Error Occurred! Please Try Again.');
            refreshNotifications(true);
        }
    });
}

function SendQuotationMail() {
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
                url: "<?php echo site_url('Invoices/send_invoice_email'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#Email_modal").modal('hide');
                        save_document_email_history(data[2],'CINV',data[3]);
                    }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function confirmCustomerInvoicefront(invoiceAutoID) {
    swal({
            title: "Are you sure?",
            text: "You want to confirm this document?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        },
        function () {
            $.ajax({
                url: "<?php echo site_url('Invoices/invoice_confirmation'); ?>",
                type: 'post',
                data: {invoiceAutoID: invoiceAutoID},
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function(){  Otable.draw(); }, 500);
                    }
                    setTimeout(function(){  stopLoad(); }, 500);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                    myAlert('e', xhr.responseText);
                }
            });
        });
}


function traceDocument(cinvID,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'invoiceAutoID': cinvID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/trace_cinv_document'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            //myAlert(data[0], data[1]);
            $(window).scrollTop(0);
            load_document_tracing(cinvID,DocumentID);
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


function load_mail_history(){
    var Otables = $('#mailhistory').DataTable({"language": {
        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
    },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Invoices/load_mail_history'); ?>",
        aaSorting: [[0, 'desc']],
        "bFilter": false,
        "bInfo": false,
        "bLengthChange": false,
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }
        },
        "aoColumns": [
            {"mData": "autoID"},
            {"mData": "invoiceCode"},
            {"mData": "ename"},
            {"mData": "toEmailAddress"},
            {"mData": "sentDateTime"}

        ],
        //"columnDefs": [{"targets": [0], "visible": false,"searchable": true}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "invoiceAutoID", "value": $("#email_invoiceid").val()});
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
function issystemgenerateddoc() {
    swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
}


function open_receipt_voucher_modal(id){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'invoiceAutoID': id},
        url: "<?php echo site_url('Invoices/open_receipt_voucher_modal'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $(".paymentmoad").hide();

            $("#invoicID").val(id);
            $("#segment").val(data['master']['segmentID']);
            $("#customerID").val(data['master']['customerID']);
            $("#transactionCurrencyID").val(data['master']['transactionCurrencyID']);
            $("#referenceno").val(data['master']['invoiceCode']);
            $("#RVchequeNo").val('');
            if(!jQuery.isEmptyObject(data['GL'])){
                $("#RVbankCode").val(data['GL']['GLAutoID']).change();
            }
            $('#invoiceBal').html('Invoice Balance :- '+ data['balance'] + ' ('+ data['master']['transactionCurrency'] +')' );

            $("#receipt_voucher_modal").modal('show');
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function set_payment_method() {
    val = $('#RVbankCode option:selected').text();
    res = val.split(" | ")
    if (res[5] == 'Cash') {
        $(".paymentmoad").hide();
    } else {
        $(".paymentmoad").show();
    }
}

function fetch_finance_year_period(companyFinanceYearID, select_value) {
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'companyFinanceYearID': companyFinanceYearID},
        url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
        success: function (data) {
            $('#financeyear_period').empty();
            var mySelect = $('#financeyear_period');
            mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
            if (!jQuery.isEmptyObject(data)) {
                $.each(data, function (val, text) {
                    mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                });
                if (select_value) {
                    $("#financeyear_period").val(select_value);
                }
                ;
            }
        }, error: function () {
            swal("Cancelled", "Your " + value + " file is safe :)", "error");
        }
    });
}


function confirmReceiptVoucher(receiptVoucherAutoId){
    if (receiptVoucherAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId},
            url: "<?php echo site_url('Receipt_voucher/receipt_confirmation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['error']==1){
                    myAlert('e',data['message']);
                }else if(data['error']==2){
                    myAlert('w',data['message']);
                }
                else {
                    //myAlert('s',data['message']);
                    swal("Success", "Receipt Voucher " + data['code'] + " Created Successfully " , "success");
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
    ;
}
</script>