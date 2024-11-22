<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_debit_note');
echo head_page($title, true);

/*echo head_page('Debit Note', true);*/
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
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
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_supplier_name');?> <!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>$this->lang->line('common_all')/*'All'*/, '1' =>$this->lang->line('common_draft') /*'Draft'*/, '2' =>$this->lang->line('common_confirmed') /*'Confirmed'*/, '3' =>$this->lang->line('common_approved') /*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i> Clear
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> <!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved');?>  <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed-->
                    / <?php echo $this->lang->line('common_not_approved');?> <!--Not Approved-->
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
                onclick="fetchPage('system/accounts_payable/erp_debit_note',null,'Add New Debit Note','DN');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_create_debit_note');?><!--Create Debit Note-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="debit_note_master_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_dn_code');?><!--DN Code--></th>
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

<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Debit Note Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="debitNoteMasterAutoID" name="debitNoteMasterAutoID">
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var grvAutoID;
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/accounts_payable/debit_note_management', '', 'Debit Note');
        });
        grvAutoID = null;
        number_validation();
        debit_note_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function debit_note_table(selectedID=null) {
       Otable = $('#debit_note_master_table').DataTable({
           "language": {
               "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
           },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Payable/fetch_debit_note_buyback'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['debitNoteMasterAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "debitNoteMasterAutoID"},
                {"mData": "debitNoteCode"},
                {"mData": "dn_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approve"},
                {"mData": "edit"},
                {"mData": "comments"},
                {"mData": "suppliername"},
                {"mData": "debitNoteDate"},
                {"mData": "transactionCurrency"},
                {"mData": "detTransactionAmount"},
                {"mData": "docRefNo"}
            ],
           "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": false,"targets": [7,8,9,10,11] },{"visible":true,"searchable": false,"targets": [0,3] },{"visible":false,"searchable":true,"targets": [12] }],
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


    function delete_item(id, value) {
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
                    data: {'debitNoteMasterAutoID': id},
                    url: "<?php echo site_url('Payable/delete_dn'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if(data.error==1){
                            myAlert('e', data.message);
                        }else if(data.error==0){
                            myAlert('s', data.message);
                            Otable.draw();
                        }

                    }, error: function () {
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

    function referbackdn(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
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
                    data: {'debitNoteMasterAutoID': id},
                    url: "<?php echo site_url('Payable/referback_dn'); ?>",
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
                    data : {'debitNoteMasterAutoID':id},
                    url :"<?php echo site_url('Payable/re_open_dn'); ?>",
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

    function confirmDebitNotefront(invoiceAutoID) {
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
                    url: "<?php echo site_url('Payable/dn_confirmation'); ?>",
                    type: 'post',
                    data: {debitNoteMasterAutoID: invoiceAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1])
                        if(data[0]=='s')
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

    function traceDocument(debitNoteMasterAutoID,DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'debitNoteMasterAutoID': debitNoteMasterAutoID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/trace_dn_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(debitNoteMasterAutoID,DocumentID);
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

    function load_printtemp_DN(debitNoteMasterAutoID)
    {
        $('#printSize').val(1).change();
        $('#debitNoteMasterAutoID').val(debitNoteMasterAutoID);
        $('#print_temp_modal').modal('show');
    }

    function print_po_temp(){
        var printSize =  $('#printSize').val();
        var debitNoteMasterAutoID =   $('#debitNoteMasterAutoID').val();

        if(debitNoteMasterAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Payable/load_dn_conformation') ?>" +'/'+ debitNoteMasterAutoID +'/'+ printSize +'/'+1);
        }
    }
</script>