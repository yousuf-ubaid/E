<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_receivable_ap_credit_note');
echo head_page($title, true);
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$createmasterrecords = getPolicyValues('CMR','All');
$customer_arr_masterlevel = array('' => 'Select Customer');
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
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></label><br>
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' =>  $this->lang->line('common_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('accounts_receivable_common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/accounts_receivable/erp_credit_note',null,'<?php echo $this->lang->line('accounts_receivable_tr_cn_add_new_credit_note');?>','CN');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_receivable_tr_cn_tax_detail');?><!--Create Credit Note-->
        </button>
    </div><!--Add New Credit Note-->
</div>
<hr>
<div class="table-responsive">
    <table id="credit_note_master_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('accounts_receivable_common_cn_code');?><!--CN Code--></th>
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
                <h4 class="modal-title" id="myModalLabel">Credit Note Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="creditNoteMasterAutoID" name="creditNoteMasterAutoID">
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
            fetchPage('system/accounts_receivable/credit_note_management', '', 'Credit Note');
        });
        grvAutoID = null;
        number_validation();
        credit_note_table();

        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function credit_note_table(selectedID=null) {
         Otable = $('#credit_note_master_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Receivable/fetch_credit_note_buyback'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['creditNoteMasterAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "creditNoteMasterAutoID"},
                {"mData": "creditNoteCode"},
                {"mData": "dn_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approve"},
                {"mData": "edit"},
                {"mData": "comments"},
                {"mData": "customermastername"},
                {"mData": "creditNoteDate"},
                {"mData": "detTransactionAmount"},
                {"mData": "docRefNo"}
            ],
             "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable":true,"targets": [11] },{"visible":false,"searchable": false,"targets": [7,8,9,10] },{"visible":true,"searchable": false,"targets": [0,1,3] }],
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

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
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
                    data: {'creditNoteMasterAutoID': id},
                    url: "<?php echo site_url('Receivable/delete_creditNote_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        Otable.draw();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                        stopLoad();
                    }
                });
            });
    }


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
                    data: {'creditNoteMasterAutoID': id},
                    url: "<?php echo site_url('Receivable/referback_credit_note'); ?>",
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
        $('#customerCode').multiselect2('deselectAll', false);
        $('#customerCode').multiselect2('updateButtonText');
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
                    data : {'creditNoteMasterAutoID':id},
                    url :"<?php echo site_url('Receivable/re_open_credit_note'); ?>",
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

    function traceDocument(creditNoteMasterAutoID,DocumentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'creditNoteMasterAutoID': creditNoteMasterAutoID,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/trace_cn_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(creditNoteMasterAutoID,DocumentID);
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

    function load_printtemp_CN(creditNoteMasterAutoID)
    {
        $('#printSize').val(1).change();
        $('#creditNoteMasterAutoID').val(creditNoteMasterAutoID);
        $('#print_temp_modal').modal('show');
    }

    function print_po_temp(){
        var printSize =  $('#printSize').val();
        var creditNoteMasterAutoID =   $('#creditNoteMasterAutoID').val();

        if(creditNoteMasterAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Receivable/load_cn_conformation') ?>" +'/'+ creditNoteMasterAutoID +'/'+ printSize +'/'+1);
        }
    }
</script>