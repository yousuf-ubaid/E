<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('finance_common_journal_voucher');
echo head_page($title, true);
$date_format_policy = date_format_policy();
/*echo head_page('Journal Voucher',false); */?>
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
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('finance_common_refer_back');?><!--Refer-back-->
                    </td>
                </tr>
            </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">

        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/finance/journal_entry_new',null,'<?php echo $this->lang->line('finance_tr_jv_add_new_journal_entry');?>'/*'Add New Journal Entry'*/,'Journal Entry');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('finance_tr_jv_create_journal_voucher');?><!--Create Journal Voucher--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="journal_entry_table2" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 12%"><?php echo $this->lang->line('finance_common_jv_code');?><!--JV Code--></th>
                <th style="min-width: 45%"><?php echo $this->lang->line('common_details');?><!--Details--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                <th style="min-width: 13%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="attachment_modal_JV" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="attachment_modal_labels"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> <?php echo $this->lang->line('finance_common_journal_voucher');?><!--Journal Voucher--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="attachment_uplode_form_JV" class="form-inline"'); ?>
                            <div class="form-group">
                                <input type="text" class="form-control" id="attachmentDescriptionJV"
                                       name="attachmentDescriptionJV"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                <!--Description-->
                                <input type="hidden" class="form-control" id="documentSystemCodeJV"
                                       name="documentSystemCodeJV">
                                <input type="hidden" class="form-control" id="documentIDJV" name="documentIDJV">
                                <input type="hidden" class="form-control" id="document_nameJV" name="document_nameJV">
                                <input type="hidden" class="form-control" id="confirmYNaddJV" name="confirmYNaddJV">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                          class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                          class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_fileJV" id="document_fileJV"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_jv"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="document_uplode_JV()"><span
                                  class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="attachment_modal_body_jv" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
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
<script type="text/javascript">
    var Otable
$(document).ready(function() {
    Inputmask().mask(document.querySelectorAll("input"));
    $('.headerclose').click(function(){
        fetchPage('system/finance/Journal_entry_management','','Journal Entry');
    });
    journal_entry_table();
});

function journal_entry_table(){
     Otable = $('#journal_entry_table2').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Journal_entry/fetch_journal_entry'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');
        },
        "aoColumns": [
            {"mData": "JVMasterAutoId"},
            {"mData": "JVcode"},
            {"mData": "detail"},
            {"mData": "JVdate"},
            {"mData": "total_value"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "action"},
            {"mData": "JVType"},
            {"mData": "JVNarration"},
            {"mData": "total_value_search"}
            //{"mData": "edit"},
        ],
         "columnDefs": [{"targets": [7], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10] },{"visible":true,"searchable": true,"targets": [1,3] },{"visible":true,"searchable": false,"targets": [0] }],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
            aoData.push({"name": "status", "value": $("#status").val()});
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

function delete_journal_entry(id){
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",/*You want to delete this record!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'JVMasterAutoId':id},
                url :"<?php echo site_url('Journal_entry/delete_Journal_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    Otable.draw();
                    stopLoad();
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });        
}

    function referback_journal_entry(id,isSystemGenerated){
        if(isSystemGenerated!=1)
        {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back'); ?>",/*You want to refer back!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'JVMasterAutoId':id},
                        url :"<?php echo site_url('Journal_entry/referback_journal_entry'); ?>",
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
        }else {
            swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
        }

    }

    function reOpen_contract(id,isSystemGenerated){
        if(isSystemGenerated!=1)
        {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_re_open'); ?>",/*You want to re open!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'JVMasterAutoId':id},
                        url :"<?php echo site_url('Journal_entry/re_open_journal_entry'); ?>",
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
        }else {
            swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
        }

    }
    function recurring_attachment_modal(documentSystemCode, document_name, documentID, confirmedYN) {
        $('#attachmentDescriptionJV').val('');
        $('#documentSystemCodeJV').val(documentSystemCode);
        $('#document_nameJV').val(document_name);
        $('#documentIDJV').val(documentID);
        $('#confirmYNaddJV').val(confirmedYN);
        $('#remove_id_jv').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Journal_entry/fetch_attachmentsJV"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#attachment_modal_body_jv').empty();
                    $('#attachment_modal_body_jv').append('' +data+ '');

                    $("#attachment_modal_JV").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function document_uplode_JV() {
        var formData = new FormData($("#attachment_uplode_form_JV")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Journal_entry/do_upload_jv'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    recurring_attachment_modal($('#documentSystemCodeJV').val(), $('#document_nameJV').val(), $('#documentIDJV').val(), $('#confirmYNaddJV').val());
                    $('#remove_id_jv').click();
                    $('#attachmentDescriptionJV').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        Otable.draw();
    }
    function issystemgenerateddoc() {
        swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
    }

    function templateClone(id){
        var id=id;
        $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {id:id},
                url: "<?php echo site_url('Journal_entry/cloneJV'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    data.forEach(function(response) {
                if (response.status === 'success') {
                    myAlert('s',response.message);
                } else {
                    myAlert('e',response.message);
                }
                // journal_entry_table();
            });
                },
                error : function() {
                    stopLoad();
                    myAlert('e','An Error Occurred! Please Try Again.');
                }
            });

    }
</script>