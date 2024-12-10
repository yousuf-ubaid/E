<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('operationngo_document_setup');
echo head_page($title, false);

/*echo head_page('Document Setup', false);*/
$this->load->helper('operation_ngo_helper');
$documentsDrop = load_all_ngo_documentsBeneficiarySetup();
$projectDrop = fetch_project_donor_drop();
$ngo_docDrop = load_all_ngo_upload_documentsTypes();
?>
<style type="text/css">
    .saveInputs {
        height: 25px;
        font-size: 11px
    }

    #setup-add-tb td {
        padding: 2px;
    }

    #setup-edit-tb td {
        padding: 2px;
    }

    .number {
        width: 90px !important;
    }

    .icheckbox_minimal-blue {
        margin-top: 3px;
    }

    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        height: 25px;
        padding: 1px 5px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 25px !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDocument_modal()"><i
                class="fa fa-plus-square"></i>&nbsp;<?php echo $this->lang->line('common_add');?> <!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="load_docSetup" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('operationngo_ngo_document');?><!--NGO Document--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_project');?><!--Project--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_mandatory');?><!--Mandatory--></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="new_documents" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('operationngo_add_documents');?> <!--Add Documents--></h4>
            </div>
            <form class="form-horizontal" id="add-documents_form">
                <div class="modal-body">
                    <table class="table table-bordered" id="setup-add-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('operationngo_ngo_document');?><!--NGO Document--></th>
                            <th><?php echo $this->lang->line('common_project');?><!--Project--></th>
                            <th><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th><?php echo $this->lang->line('common_is_required');?><!--Is&nbsp;Required--></th>
                            <th><?php echo $this->lang->line('common_sort_order');?><!--Sort&nbsp;Order--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('documentID[]', $documentsDrop, 5, 'class="form-control saveInputs new-items select2"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('ngoProjectID[]', $projectDrop, '', 'class="form-control saveInputs new-items select2"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('descriptionID[]', $ngo_docDrop, '', 'class="form-control saveInputs new-items select2"'); ?>
                            </td>
                            <td align="center">
                                <input type="checkbox" name="isRequired[]" class="requiredCheckbox" value="1"
                                       checked>
                            </td>
                            <td>
                                <input type="type" name="sortOrder[]"
                                       class="form-control number saveInputs new-items"/>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_documents()">
                        <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                       <?php echo $this->lang->line('common_Close');?> <!--Close-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('operationngo_edit_document_setup');?><!--Edit Document Setup--></h4>
            </div>

            <form class="form-horizontal" id="edit-documents_form">
                <div class="modal-body">
                    <table class="table table-bordered" id="setup-edit-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('operationngo_ngo_document');?><!--NGO Document--></th>
                            <th><?php echo $this->lang->line('common_project');?><!--Project--></th>
                            <th><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th><?php echo $this->lang->line('common_is_required');?><!--Is&nbsp;Required--></th>
                            <th><?php echo $this->lang->line('common_sort_order');?><!--Sort&nbsp;Order--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('edit_documentID', $documentsDrop, '', 'class="form-control select2" id="edit_documentID"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('edit_ngoProjectID', $projectDrop, '', 'class="form-control select2" id="edit_ngoProjectID"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('edit_descriptionID', $ngo_docDrop, '', 'class="form-control select2" id="edit_descriptionID"'); ?>
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_isRequired" id="edit_isRequired"
                                       class="requiredCheckbox" value="1" checked>
                            </td>
                            <td>
                                <input type="type" name="edit_sortOrder" id="edit_sortOrder"
                                       class="form-control number saveInputs new-items"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateDocument()">
                        <?php echo $this->lang->line('common_update'); ?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var setup_tb = $('#setup-add-tb');
    $('.select2').select2();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/ngo_document_setup', '', 'Document Setup');
        });
        load_docSetup();


        /*        $('.requiredCheckbox').iCheck({
         checkboxClass: 'icheckbox_minimal-blue'
         });*/
    });

    function load_docSetup(selectedRowID=null) {
        var Otable = $('#load_docSetup').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_ngo_document_Setup'); ?>",
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


                    if (parseInt(oSettings.aoData[x]._aData['DocDesSetupID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "DocDesSetupID"},
                {"mData": "ngoDocument"},
                {"mData": "projectName"},
                {"mData": "docMasterName"},
                {"mData": "mandatory"},
                {"mData": "SortOrder"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function openDocument_modal() {
        $('#setup-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('.saveInputs').change();
        $('#new_documents').modal({backdrop: "static"});
    }

    function save_documents() {
        var postData = $('#add-documents_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('OperationNgo/save_ngo_document_setupMaster'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#new_documents').modal('hide');
                    load_docSetup();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
            }
        })

    }

    function edit_docSetup(id, thisTR) {
        var table = $('#load_docSetup').DataTable();
        var thisRow = $(thisTR);
        var details = table.row(thisRow.parents('tr')).data();
        var chkReq = $('#edit_isRequired');
        $('#editModal').modal({backdrop: "static"});
        chkReq.iCheck('uncheck');
        $('#hidden-id').val($.trim(id));
        $('#edit_documentID').val($.trim(details.ngoDocumentID)).change();
        $('#edit_ngoProjectID').val($.trim(details.ngoprojectID)).change();
        $('#edit_descriptionID').val($.trim(details.DocDesID)).change();
        $('#edit_sortOrder').val($.trim(details.SortOrder));
        if (details.isMandatory == 1) {
            chkReq.iCheck('check');
        }

    }

    function delete_doc_ngoSetup(id, description) {
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
                    url: "<?php echo site_url('OperationNgo/delete_ngo_document_setup'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'DocDesSetupID': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_docSetup()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#setup-add-tb tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('checkbox').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        setup_tb.append(appendData);
        var lenght = $('#setup-add-tb tbody tr').length - 1;
        $('.select2').select2();
    }

    function updateDocument() {
        var postData = $('#edit-documents_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('OperationNgo/update_ngo_document_setup'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#editModal').modal('hide');
                    load_docSetup($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
            }
        })

    }

    $(document).on('keypress', '.number', function (event) {
        var amount = $(this).val();
        if (amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }

    });

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>
