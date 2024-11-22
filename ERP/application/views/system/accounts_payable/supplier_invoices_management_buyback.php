<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_trans_supplier_invoices');
echo head_page($title, true);


/*echo head_page('Supplier Invoices', true);*/
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();

$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$chequeRegister = getPolicyValues('CRE', 'All');
$pID = $this->input->post('page_id');
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo" class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_supplier_name');?> <!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>$this->lang->line('common_all') /*'All'*/, '1' =>$this->lang->line('common_draft')/* 'Draft'*/, '2' =>$this->lang->line('common_confirmed') /*'Confirmed'*/, '3' =>$this->lang->line('common_approved') /*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
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

                </td><!--Confirmed--> <!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                    / <?php echo $this->lang->line('common_not_approved');?>
                </td><!--Not Confirmed--><!--Not Approved-->
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('accounts_payable_trans_refer_back');?>
                </td><!--Refer-back-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/accounts_payable/erp_supplier_invoices_buyback',null,'<?php echo $this->lang->line('accounts_payable_trans_add_new_supplier_invoice');?>','SI');"><!--Add New Supplier Invoices-->
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_trans_create_supplier_invoice');?><!--Create Supplier Invoice-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="supplier_invoices_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_trans_bsi_code');?><!--BSI Code--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?><!--Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value');?><!--Total Value--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>


<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i> </button></h4>
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

<div class="modal fade" id="payment_voucher_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="paymentvoucher_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Payment Voucher</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vouchertype" name="vouchertype" value="Supplier">
                    <input type="hidden" id="invoicID" name="invoicID">
                    <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="partyID" name="partyID">
                    <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">
                    <div class="row">
                        <div class="col-sm-4"><span style="color: black;font-family: sans-serif;" id="invoiceBal"></span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher_date');?><!--Payment Voucher Date--> <?php required_mark(); ?></label>

                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="PVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="PVdate"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                            <input type="text" name="referenceno" id="referenceno" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="PVbankCode"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_bank_or_cash');?><!--Bank or Cash--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('PVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="PVbankCode" onchange="fetch_cheque_number(this.value)"'); ?>
                        </div>
                        <?php
                        if($financeyearperiodYN==1){
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_payable_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_payable_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4 paymentType hide">
                            <label>Payment Type</label>
                            <?php echo form_dropdown('paymentType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '1' => 'Cheque ', '2' =>'Bank Transfer'), ' ', 'class="form-control select2" id="paymentType" onchange="show_payment_method(this.value)"'); ?>
                        </div>
                        <div class="form-group col-sm-4 banktrans hide">
                            <label>Supplier Bank</label>
                            <select class="form-control" id="supplierBankMasterID" name="supplierBankMasterID">

                            </select>
                        </div>
                        <?php
                        if($chequeRegister==1){
                            ?>
                            <div class="form-group col-sm-4 paymentmoad">
                                <label for="PVbankCode"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_no');?>  <?php required_mark(); ?></label>
                                <?php echo form_dropdown('chequeRegisterDetailID', array('' => 'Select Cheque no'), '', 'class="form-control" id="chequeRegisterDetailID"'); ?>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="form-group col-sm-4 paymentmoad">
                                <label><!--Cheque Number--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_no');?> <?php required_mark(); ?></label>
                                <input type="text" name="PVchequeNo" id="PVchequeNo" class="form-control">
                            </div>
                            <?php
                        }
                        ?>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><!--Cheque Date--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_date');?> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="PVchequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="PVchequeDate"
                                       class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="form-group col-sm-4">
                            <label><!--Memo--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_memo');?> </label>
                            <textarea class="form-control" rows="3" name="narration" id="narration" ></textarea>
                        </div>
                        <div class="form-group col-sm-1 paymentmoad" style="padding-right: 0px;">
                            <label class="title">Payee Only
                        </div>
                        <div class="form-group col-sm-1 paymentmoad" style="padding-left: 0px;">
                            <div class="col-sm-1">
                                <div class="skin skin-square">
                                    <div class="skin-section extraColumns"><input id="accountPayeeOnly" type="checkbox"
                                                                                  data-caption="" class="columnSelected"
                                                                                  name="accountPayeeOnly" value="1"><label
                                                for="checkbox">&nbsp;</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-8 hide" id="employeerdirect">
                            <label>Bank Transfer Details </label>
                            <textarea class="form-control" rows="3" name="bankTransferDetails" id="bankTransferDetails"></textarea>
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
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier Invoice Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="InvoiceAutoID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page'), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_bsi_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var InvoiceAutoID;
    var Otable;
    var fromedit;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/accounts_payable/supplier_invoices_management_buyback', '', 'Supplier Invoices');
        });
        InvoiceAutoID = null;
        number_validation();
        supplier_invoices_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
        $('.select2').select2();
        $(".paymentmoad").hide();


        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#paymentvoucher_form').bootstrapValidator('revalidateField', 'PVdate');
        });

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#paymentvoucher_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                PVdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_date_is_required');?>.'}}},/*Payment Voucher Date is required*/
                PVbankCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_bank_or_cash_is_required');?>.'}}},/*Bank or Cash is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
            data.push({'name': 'bank', 'value': $('#PVbankCode option:selected').text()});
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
                        url: "<?php echo site_url('Payable/save_paymentvoucher_from_BSI_header'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            if(data[0]=='s'){
                                $("#segment").val('');
                                $("#partyID").val('');
                                $("#invoicID").val('');
                                $("#transactionCurrencyID").val('');
                                $("#referenceno").val('');
                                $("#PVbankCode").val('').change();
                                $("#PVchequeNo").val('');
                                $("#payment_voucher_modal").modal('hide');
                                confirmpaymentVoucher(data[2])
                            }else{
                                stopLoad();
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

    function supplier_invoices_table(selectedID=null) {
         Otable = $('#supplier_invoices_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Payable/fetch_supplier_invoices_buyback'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['InvoiceAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "InvoiceAutoID"},
                {"mData": "bookingInvCode"},
                {"mData": "detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "comments"},
                {"mData": "suppliermastername"},
                {"mData": "supplierInvoiceNo"},
                {"mData": "bookingDate"},
                {"mData": "transactionCurrency"},
                {"mData": "invoiceType"},
                {"mData": "invoiceDueDate"},
                {"mData": "total_value_search"},
                {"mData": "RefNo"}
                //{"mData": "edit"},
            ],
             "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [7,8,9,10,11,12,13,14,15] },{"visible":true,"searchable": false,"targets": [0,2,3,4,5,6] }],
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

    function delete_supplier_invoice(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('accounts_payable_trans_are_you_want_to_delete');?>",/*You want to delete this file!*/
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
                    data: {'InvoiceAutoID': id},
                    url: "<?php echo site_url('Payable/delete_supplier_invoice'); ?>",
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

    function referbacksupplierinvoice(id,isSysgen) {
        if(isSysgen!=1)
        {
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
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'InvoiceAutoID': id},
                        url: "<?php echo site_url('Payable/referback_supplierinvoice'); ?>",
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
        else
            {
                swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
            }


    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
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
                    data : {'InvoiceAutoID':id},
                    url :"<?php echo site_url('Payable/re_open_supplier_invoice'); ?>",
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

    function confirmSupplierInvoicefront(invoiceAutoID) {
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
                    url: "<?php echo site_url('Payable/supplier_invoice_confirmation'); ?>",
                    type: 'post',
                    data: {InvoiceAutoID: invoiceAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data)
                        {
                            Otable.draw();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }


    function traceDocument(bsiID,DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'InvoiceAutoID': bsiID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/trace_bsi_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(bsiID,DocumentID);
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
    function issystemgenerateddoc() {
        swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
    }

    function open_payent_voucher_modal(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': id},
            url: "<?php echo site_url('Payable/open_payment_voucher_modal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                fromedit=1;
                $(".paymentmoad").hide();
                voucher_type("Supplier", data['master']['supplierID']);
                $("#invoicID").val(id);
                $("#segment").val(data['master']['segmentID']);
                $("#partyID").val(data['master']['supplierID']);
                $("#transactionCurrencyID").val(data['master']['transactionCurrencyID']);
                $("#referenceno").val(data['master']['bookingInvCode']);
                $("#RVchequeNo").val('');
                if(!jQuery.isEmptyObject(data['GL'])){
                    $("#PVbankCode").val(data['GL']['GLAutoID']).change();
                }
                $('#invoiceBal').html('Invoice Balance :- '+ data['balance'] + ' ('+ data['master']['transactionCurrency'] +')' );

                $("#payment_voucher_modal").modal('show');
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function show_payment_method(){
        if ($("#paymentType").val() == 1) {
            $(".paymentmoad").show();
            $('.banktrans').addClass('hide');
            $('#employeerdirect').addClass('hide');
        }else if ($("#paymentType").val() == 2 && $('#vouchertype').val()=='PurchaseRequest' && (!jQuery.isEmptyObject($('#partyID').val()) && $('#partyID').val()>0)) {
            $(".paymentmoad").hide();
            $('#employeerdirect').addClass('hide');
            $('#supplierBankMasterID').removeClass('hide');
            var supplierID= $("#partyID").val();
            if(supplierID){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierID': supplierID},
                    url: "<?php echo site_url('Payment_voucher/get_supplier_banks'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            $('#supplierBankMasterID').empty();
                            $.each(data, function (key, value) {
                                $('#supplierBankMasterID').append('<option value="' + value['supplierBankMasterID'] + '">' + value['bankName'] + '</option>');
                            });
                            $('.banktrans').removeClass('hide');
                        }else{
                            $('#supplierBankMasterID').empty();
                            $('#supplierBankMasterID').append('<option value="">No Records Found</option>');
                            $('.banktrans').removeClass('hide');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }else{
                //myAlert('w','Select Supplier')
            }
        }else if ($("#paymentType").val() == 2 && $('#vouchertype').val()=='PurchaseRequest' && (jQuery.isEmptyObject($('#partyID').val()) || $('#partyID').val()==0)) {
            $('#supplierBankMasterID').addClass('hide');
            $('#employeerdirect').removeClass('hide');
            $(".paymentmoad").hide();
            var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            if (p_id) {

            }else{
                $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
            }
        } else if($("#paymentType").val() == 2 && $('#vouchertype').val()=='Supplier') {
            $(".paymentmoad").hide();
            $('#employeerdirect').addClass('hide');
            $('#supplierBankMasterID').removeClass('hide');
            var supplierID= $("#partyID").val();
            if(supplierID){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierID': supplierID},
                    url: "<?php echo site_url('Payment_voucher/get_supplier_banks'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            $('#supplierBankMasterID').empty();
                            $.each(data, function (key, value) {
                                $('#supplierBankMasterID').append('<option value="' + value['supplierBankMasterID'] + '">' + value['bankName'] + '</option>');
                            });
                            $('.banktrans').removeClass('hide');
                        }else{
                            $('#supplierBankMasterID').empty();
                            $('#supplierBankMasterID').append('<option value="">No Records Found</option>');
                            $('.banktrans').removeClass('hide');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }else{
                //myAlert('w','Select Supplier')
            }

        }else if($("#paymentType").val() == 2 && ($('#vouchertype').val()=='Direct' || $('#vouchertype').val()=='Employee')) {
            $('#supplierBankMasterID').addClass('hide');
            $('#employeerdirect').removeClass('hide');
            $(".paymentmoad").hide();

            if($('#vouchertype').val()=='Employee'){
                update_bank_transferDet();
            }
            else{
                var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
                if (p_id) {

                }else{
                    $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
                }
            }
        } else{
            $('#employeerdirect').addClass('hide');
            $('.banktrans').addClass('hide');
            $(".paymentmoad").hide();
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

    function fetch_cheque_number(GLAutoID) {
        if (!jQuery.isEmptyObject(GLAutoID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'GLAutoID': GLAutoID},
                url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
                success: function (data) {
                    if (data['master']) {
                        if(data['master']['bankCheckNumber']!='NaN')
                        {
                            $("#PVchequeNo").val((parseFloat(data['master']['bankCheckNumber']) + 1));
                        }else
                        {
                            $("#PVchequeNo").val(" ");
                        }

                        /*if($('#vouchertype').val()=='Supplier'){*/
                        if (data['master']['isCash'] == 1) {
                            $("#employeerdirect").addClass('hide');
                            $(".paymentmoad").hide();
                            $('.paymentType').addClass('hide');
                            $('.banktrans').addClass('hide');
                        } else {
                            $('.paymentType').removeClass('hide');
                            show_payment_method();
                            //$(".paymentmoad").show();
                        }
                    }
                    if (data['detail']) {
                        $('#chequeRegisterDetailID').empty();
                        var mySelect = $('#chequeRegisterDetailID');
                        mySelect.append($('<option></option>').val('').html('Select Cheque no'));
                        if (!jQuery.isEmptyObject(data['detail'])) {
                            $.each(data['detail'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['chequeRegisterDetailID']).html(text['chequeNo'] + ' - ' + text['description']));
                            });
                            ;
                        }
                    }
                }
            });
        }else{
            $('.paymentType').addClass('hide');
            $('.banktrans').addClass('hide');
        }

    }


    function voucher_type(value, select_value,supID) {
        $("#employeerdirect").addClass('hide');

        $('#bankTransferDetails').wysihtml5({
            toolbar: {
                "font-styles": false,
                "emphasis": false,
                "lists": false,
                "html": false,
                "link": false,
                "image": false,
                "color": false,
                "blockquote": false
            }
        });

        if (select_value == 'undefined') {
            select_value = '';
        }
        if (value == 'Direct') {
            $('.startmark').removeClass('hidden');
            $('#party_text').text('Payee Name');
            $('#party_textbox').html('<input type="text" name="partyName" id="partyName" value="" class="form-control" >');
        }else if(value == 'PurchaseRequest'){
            $('#prqpartyID').val(select_value).change();
            if (select_value == 'undefined') {
                select_value = '';
            }else{
                set_partyID_details()
            }
            $('.startmark').removeClass('hidden');
            $('#party_text').text('Payee Name');
            $('#party_textbox').html(' <div class="input-group"> <input type="text" class="form-control" id="partyName" name="partyName" onkeyup="clear_partyID_req_id()" placeholder="Supplier Name" required> <input type="hidden" class="form-control" id="partyID" name="partyID">  <span class="input-group-btn"> <button class="btn btn-default" type="button" title="Clear" rel="tooltip" onclick="clear_partyID_req_details()" style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button> <button class="btn btn-default" type="button" title="Add Community Member" rel="tooltip" onclick="link_partyID_irq_modal()"style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button> </span> </div> <input type="hidden" name="contactID" id="contactID_edit">');
        }else {
            $('.startmark').removeClass('hidden');
            $('#party_text').text('Payee Name');
            $('#party_textbox').html('<input type="text" name="partyName" id="partyName" value="" class="form-control" >');
        }
        if(value=='Direct'||value=='Employee'){
            $('.paymentType').addClass('hide');
            $('.banktrans').addClass('hide');
        }
        if(value=='Supplier' && $('#PVbankCode').val()>0 && fromedit==1){
            $('#PVbankCode').val('').change();
        }
        if((value=='Direct'||value=='Employee' || value=='PurchaseRequest') && fromedit==1){
            $('#PVbankCode').val('').change();
        }
        if(fromedit==2){
            var PVbankCode=$('#PVbankCode').val();
            $('#PVbankCode').val(PVbankCode).change();
        }
    }


    function confirmpaymentVoucher(payVoucherAutoId){
        if (payVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'PayVoucherAutoId': payVoucherAutoId},
                url: "<?php echo site_url('Payment_voucher/payment_confirmation'); ?>",
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
                        swal("Success", "PAyment Voucher " + data['code'] + " Created Successfully " , "success");
                    }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }
        ;
    }

    function load_printtemp(InvoiceAutoID)
            {
        $('#printSize').val(1);
        $('#InvoiceAutoID').val(InvoiceAutoID);
        $('#print_temp_modal').modal('show');
    }
    function print_bsi_temp(){
        var printSize =  $('#printSize').val();
        var invAutoID = $('#InvoiceAutoID').val();

        if(invAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Payable/load_supplier_invoice_conformation') ?>" +'/'+ invAutoID +'/'+ printSize +'/'+1);
        }
    }    
</script>