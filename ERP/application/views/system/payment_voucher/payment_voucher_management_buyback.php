<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$current_date = current_format_date();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_tr_pv_payment_voucher');
echo head_page($title, true);

/*echo head_page('Payment Voucher', true);*/
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
?>
<style>
    .chkcolsm6 {
        border-style: groove;
        border-width: 1px;
        text-align: center;
        padding: 0px;
        width: 24px;
    }

    .chkcolsm6d {
        text-align: center;
        padding: 0px;
        width: 24px;
        font-size: 0.8em;
    }

    .paycls {
        border-style: groove;
        border-width: 1px;
        width: 78.5%;
        margin-left: 0px;
        border-top: none;
        border-right: none;
        border-left: none;
    }
    .mainboderdiv{
        width: 672px;
        padding: 10px;
        border: 1px solid black;
        height: 336px;
        margin-right: 24px;
    }
    .chkamntbx{
        border-style: groove;
        border-width: 1px;
        text-align: center;
        width: 252px;
        height: 36px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                       size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                       size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_supplier_name');?> <!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' =>$this->lang->line('common_draft') /*'Draft'*/, '2' =>$this->lang->line('common_confirmed') /*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?>
        </div>



        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode">Cheque Status <!--Supplier Name--></label><br>
            <div style="width: 50%;">
                <?php echo form_dropdown('collectionstatus',array(''=>'Select Collection Status','3'=>'Not Collected','2'=>'On Hold','1'=>'Collected') , '', 'class="form-control" id="collectionstatus" onchange="Otable.draw()"'); ?>
            </div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?>
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
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('accounts_payable_trans_refer_back');?><!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/payment_voucher/erp_payment_voucher',null,'Add New Payment Voucher','PV');">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_tr_pv_create_payment_voucher');?><!--Create Payment Voucher-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="payment_voucher_master_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_pv_pv_code');?><!--PV Code--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?><!--Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value');?><!--Total Value--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="supplierinvoice_drilldown" role="dialog" tabindex="2" style="z-index: 1000000001;"aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier Invoices<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="drilldownModal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="paymentcollection_drilldown">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Collection Details </h4>
            </div>
            <?php echo form_open('', 'role="form" id="payment_vocher_Status"'); ?>
            <div class="modal-body">
                <input type="hidden" id="payVoucherAutoIdpv" name="payVoucherAutoIdpv">
                <div class="row" style="margin-top: 10px;" id="statusvoucher">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('statuspv', array('0'=>'Not Collected','1'=>'Collected','2'=>'On Hold'), '', 'class="form-control select2" id="statuspv" required'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="employeename">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Collected By</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control" id="colectedbyemp" name="colectedbyemp">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="collectiondate">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Collected Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                      <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="collectiondatepv"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="collectiondatepv" class="form-control">
                        </div>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="comment">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                 <textarea class="form-control" rows="3" id="commentpv" name="commentpv"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="commentonhold">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                 <textarea class="form-control" rows="3" id="commentpvonhold" name="commentpvonhold"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" onclick="updatepvstatus()"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                          aria-hidden="true"></span> Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="cheque_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_tr_pv_select_template');?><!--Select Template--></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="payVoucherAutoIdchk">
                <div class="row" id="chequeteplatedrop">


                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_cheque()"><?php echo $this->lang->line('accounts_payable_tr_pv_print');?><!--Print--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
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
                <h4 class="modal-title" id="myModalLabel">Payment Voucher Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="paymentvoucher_ID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page'), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_pv_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var payVoucherAutoId;
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/payment_voucher/payment_voucher_management', '', 'Payment Voucher');
        });
        $('.select2').select2();
        $('.modal').on('hidden.bs.modal', function () {
            setTimeout(function () {
                if ($('.modal').hasClass('in')) {
                    $('body').addClass('modal-open');
                }
            }, 500);
        });
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        payVoucherAutoId = null;
        number_validation();
        payment_voucher_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });


    function payment_voucher_table(selectedID=null) {
        Otable = $('#payment_voucher_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Payment_voucher/fetch_payment_voucher_buyback'); ?>",
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
                {"mData": "pv_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "PVNarration"},
                {"mData": "partyName"},
                {"mData": "PVdate"},
                {"mData": "transactionCurrency"},
                {"mData": "pvType"},
                {"mData": "total_value_search"},
                {"mData": "referenceNo"},
                {"mData": "PVchequeNo"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"targets": [6], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [7, 8, 9, 10, 11, 12, 13, 14]
            },{"visible":true,"searchable": true,"targets": [1] },{"visible":true,"searchable": false,"targets": [0] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "collectionstatus", "value": $("#collectionstatus").val()});
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

    function delete_pv_item(id, value) {
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
                    data: {'payVoucherAutoId': id},
                    url: "<?php echo site_url('Payment_voucher/delete_payment_voucher'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        Otable.draw();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referbackgrv(id,isSystemGenerated) {
        if(isSystemGenerated!=1)
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
                        data: {'payVoucherAutoId': id},
                        url: "<?php echo site_url('Payment_voucher/referback_payment_voucher'); ?>",
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
        }else
        {
            swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
        }

    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#collectionstatus').val("");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }

    function reOpen_contract(id) {
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
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'payVoucherAutoId': id},
                    url: "<?php echo site_url('Payment_voucher/re_open_payment_voucher'); ?>",
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

    function cheque_print_modal(id,count,coaChequeTemplateID) {
        if(count == 1){
            var payVoucherAutoId=id;
            var coaChequeTemplateID=coaChequeTemplateID;
            if(coaChequeTemplateID==''){
                myAlert('e', 'Select Template');
            }else{
                window.open("<?php echo site_url('Payment_voucher/cheque_print') ?>" +'/'+ payVoucherAutoId +'/'+ coaChequeTemplateID);
            }
        }else{
            $('#cheque_modal').modal('show');
            $('#payVoucherAutoIdchk').val(id);
            load_Cheque_templates(id)
        }

    }
    function load_Cheque_templates(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'payVoucherAutoId': id},
            url: "<?php echo site_url('Payment_voucher/load_Cheque_templates'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#chequeteplatedrop').html(data);
            }, error: function () {
                stopLoad();
            }
        });
    }

    function print_cheque(){
        var payVoucherAutoId=$('#payVoucherAutoIdchk').val();
        var coaChequeTemplateID=$('#coaChequeTemplateID').val();
        if(coaChequeTemplateID==''){
            myAlert('e', 'Select Template');
        }else{
            window.open("<?php echo site_url('Payment_voucher/cheque_print') ?>" +'/'+ payVoucherAutoId +'/'+ coaChequeTemplateID);
        }

    }
    function generatesupplierinvouce_drilldown_payment_voucher(InvoiceAutoID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {InvoiceAutoID:InvoiceAutoID,'html':'html'},
            url: '<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#drilldownModal").html(data);
                $('#supplierinvoice_drilldown').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    $("#statuspv").change(function () {
        if (this.value == 1) {
            $('#employeename').removeClass('hide');
            $('#collectiondate').removeClass('hide');
            $('#comment').removeClass('hide');
            $('#commentonhold').addClass('hide');
        } else if (this.value == 2) {
            $('#comment').removeClass('hide');
            $('#employeename').addClass('hide');
            $('#collectiondate').addClass('hide');
            $('#comment').addClass('hide');
            $('#commentonhold').removeClass('hide');
        } else {
            $('#employeename').addClass('hide');
            $('#collectiondate').addClass('hide');
            $('#comment').addClass('hide');
            $('#commentonhold').addClass('hide');
        }
    });
    function generatepaymentcollection_drilldown(code,autoID,collectedStatus) {
        $('#statuspv').val(null).trigger('change');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'autoID': autoID},
            url: "<?php echo site_url('Payment_voucher/paymentvoucher_collectionheader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#statuspv').val(data['collectedStatus']).change();
                $('#payVoucherAutoIdpv').val(data['payVoucherAutoId']);
                $('#colectedbyemp').val(data['collectedByName']);
                $('#collectiondatepv').val(data['collectedDate']);

                if(data['collectedStatus']== 1)
                {
                    $('#employeename').removeClass('hide');
                    $('#collectiondate').removeClass('hide');
                    $('#comment').removeClass('hide');
                    $('#commentonhold').addClass('hide');
                    $('#commentpv').val(data['collectionComments']);
                    $('#commentpvonhold').val(' ');
                }else if(data['collectedStatus']== 2)
                {
                    $('#comment').addClass('hide');
                    $('#commentpv').val(' ')
                    $('#employeename').addClass('hide');
                    $('#collectiondate').addClass('hide');
                    $('#commentonhold').removeClass('hide');
                    $('#commentpvonhold').val(data['collectionComments']);
                }else
                {
                    $('#employeename').addClass('hide');
                    $('#collectiondate').addClass('hide');
                    $('#comment').addClass('hide');
                    $('#commentonhold').addClass('hide');
                    $('#commentpvonhold').val(' ');
                    $('#commentpv').val(' ')
                }

                $('#paymentcollection_drilldown').modal("show");
                stopLoad();
                refreshNotifications(true);Report
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function updatepvstatus()
    {
        var data = $('#payment_vocher_Status').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Payment_voucher/update_paymentvoucher_collectiondetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    Otable.draw();
                    $('#paymentcollection_drilldown').modal("hide");
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
    function issystemgenerateddoc() {
        swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
    }
    function load_printtemp(pvAutoID)
            {
        $('#printSize').val(1);
        $('#paymentvoucher_ID').val(pvAutoID);
        $('#print_temp_modal').modal('show');


         }
        
    function print_pv_temp(){
        var printSize =  $('#printSize').val();
        var pvAutoID = $('#paymentvoucher_ID').val();

        if(pvAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Payment_voucher/load_pv_conformation_buyback') ?>" +'/'+ pvAutoID +'/'+ printSize +'/'+1);
        }
    }
</script>