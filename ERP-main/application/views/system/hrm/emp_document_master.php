
<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #setup-add-tb td{ padding: 2px; }
    #setup-edit-tb td{ padding: 2px; }
    .number{ width: 90px !important;}
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_document_master');
echo head_page($title  , false);

$docType_drop = emp_document_sys_type_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDocument_modal()" ><i class="fa fa-plus-square"></i>&nbsp;
            <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_documents" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_type');?><!--Type--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_mandatory');?><!--Mandatory--></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<?php echo form_dropdown('dummy_drop', $docType_drop, '', 'id="dummy_drop" style="display: none"'); ?>

<div class="modal fade" id="new_documents" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_add_documents_descriptions');?><!--Add Documents Descriptions--></h4>
            </div>
            <form class="form-horizontal" id="add-documents_form" autocomplete="off">
                <div class="modal-body">
                    <table class="table table-bordered" id="setup-add-tb">
                        <thead>
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th rowspan="2" style="width: 70px"><?php echo $this->lang->line('hrms_others_master_is_mandatory');?><!--Is&nbsp;Mandatory--></th>
                            <th rowspan="2" style="width: 75px"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
                            <th colspan="3"><?php echo $this->lang->line('common_is_required');?><!--Is required--></th>
                            <th rowspan="2">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>

                        <tr>
                            <th><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></th>
                            <th><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
                            <th><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('sysType[]', $docType_drop, '', 'class="form-control saveInputs new-items"'); ?>
                            </td>
                            <td>
                                <input type="text" name="description[]" class="form-control saveInputs new-items" />
                            </td>
                            <td align="center">
                                <input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" checked >
                                <input type="hidden" name="isRequired[]" class="changeMandatory-req" value="1">
                            </td>
                            <td align="center">
                                <input type="text" name="sortOrder[]" class="form-control number saveInputs new-items" style="width: 60px !important;"/>
                            </td>
                            <td align="center">
                                <input type="checkbox" name="chk_issueDate[]" class="requiredCheckbox" data-value="issueDate" onchange="changeMandatory(this)">
                                <input type="hidden" name="is_issueDate[]" class="changeMandatory-issueDate" value="0">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="chk_expireDate[]" class="requiredCheckbox" data-value="expireDate" onchange="changeMandatory(this)">
                                <input type="hidden" name="is_expireDate[]" class="changeMandatory-expireDate" value="0">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="chk_issuedBy[]" class="requiredCheckbox" data-value="issuedBy" onchange="changeMandatory(this)">
                                <input type="hidden" name="is_issuedBy[]" class="changeMandatory-issuedBy" value="0">
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_documents()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_edit_document_description');?><!--Edit Document Description--></h4>
            </div>

            <form class="form-horizontal" id="edit-documents_form" autocomplete="off">
                <div class="modal-body">
                    <table class="table table-bordered" id="setup-edit-tb">
                        <thead>
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th rowspan="2" style="width: 95px"><?php echo $this->lang->line('hrms_others_master_is_mandatory');?><!--Is&nbsp;Mandatory--></th>
                            <th rowspan="2" style="width: 95px"><?php echo $this->lang->line('common_sort_order');?><!--Sort&nbsp;Order--></th>
                            <th colspan="3"><?php echo $this->lang->line('common_is_required');?><!--Is required--></th>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></th>
                            <th><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
                            <th><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('edit_sysType', $docType_drop, '', 'class="form-control saveInputs new-items" id="edit_sysType" disabled'); ?>
                            </td>
                            <td>
                                <input type="text" name="edit_description" id="edit_description" class="form-control saveInputs new-items" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_isMandatory" id="edit_isMandatory" class="requiredCheckbox" />
                            </td>
                            <td>
                                <input type="type" name="edit_sortOrder" id="edit_sortOrder" class="form-control number saveInputs new-items" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_issueDate" id="edit_issueDate" class="requiredCheckbox" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_expireDate" id="edit_expireDate" class="requiredCheckbox" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_issuedBy" id="edit_issuedBy" class="requiredCheckbox" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateDocument()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var setup_tb = $('#setup-add-tb');
    var sysType_drop = '';


    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/emp_document_master','Test','HRMS');
        });

        load_documents();

        $('.requiredCheckbox').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
        });

        sysType_drop = '<select name="sysType[]" class="form-control saveInputs new-items">';
        sysType_drop += $('#dummy_drop').html();
        sysType_drop += '</select>';
    });

    function load_documents(selectedRowID=null){
        $('#load_documents').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_documentDescriptionMaster'); ?>",
            "aaSorting": [[4, 'asc']],
            "columnDefs": [ {
                "targets": [0,3,5,6,7,8],
                "searchable": false,
                "orderable": false
            } ],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['doc_ID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }


            },
            "aoColumns": [
                {"mData": "doc_ID"},
                {"mData": "sysDes"},
                {"mData": "doc_Description"},
                {"mData": "mandatory"},
                {"mData": "SortOrder"},
                {"mData": "st_issueDate_req"},
                {"mData": "st_expireDate_req"},
                {"mData": "st_issuedBy_req"},
                {"mData": "edit"}
            ],
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

    function openDocument_modal(){
        $('#setup-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_documents').modal({backdrop: "static"});
    }

    function save_documents(){
        var errorCount=0;
        $('#setup-add-tb .new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#add-documents_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_documentDescriptions'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_documents').modal('hide');
                        load_documents();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');/*Please fill all Document fields*/
        }
    }

    function edit_docSetup(id, thisTR){
        var table = $('#load_documents').DataTable();
        var thisRow = $(thisTR);
        var details = table.row(  thisRow.parents('tr') ).data();

        $('#editModal').modal({backdrop: "static"});


        $('#hidden-id').val( $.trim(id) );
        $('#edit_sysType').val( $.trim(details.systemTypeID) );
        $('#edit_description').val( $.trim(details.doc_Description) );
        $('#edit_sortOrder').val( $.trim(details.SortOrder) );
        var edit_isMandatory = $('#edit_isMandatory');
        var edit_issueDate = $('#edit_issueDate');
        var edit_expireDate = $('#edit_expireDate');
        var edit_issuedBy = $('#edit_issuedBy');

        edit_isMandatory.prop('checked', false)//.val(0);
        edit_issueDate.prop('checked', false);
        edit_expireDate.prop('checked', false);
        edit_issuedBy.prop('checked', false);

        if( $.trim(details.isMandatory) == 1 ){
            edit_isMandatory.prop('checked', true)//.val(1);
        }
        edit_isMandatory.iCheck('update');

        if( $.trim(details.issueDate_req) == 1 ){
            edit_issueDate.prop('checked', true);
        }
        edit_issueDate.iCheck('update');

        if( $.trim(details.expireDate_req) == 1 ){
            edit_expireDate.prop('checked', true);
        }
        edit_expireDate.iCheck('update');

        if( $.trim(details.issuedBy_req) == 1 ){
            edit_issuedBy.prop('checked', true);
        }
        edit_issuedBy.iCheck('update');




    }

    function delete_docSetup(id, description){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_documentDescription'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_documents() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });

    function add_more(){
        var appendData = '<tr><td>'+sysType_drop+'</td>';
        appendData += '<td><input type="text" name="description[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center"><input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory()">';
        appendData += '<input type="hidden" name="isRequired[]" class="changeMandatory-req" value="0"></td>';
        appendData += '<td align="center"><input type="text" name="sortOrder[]" class="form-control number saveInputs new-items" style="width: 60px !important;"/></td>';
        appendData += '<td align="center"><input type="checkbox" name="chk_issueDate[]" class="requiredCheckbox" data-value="issueDate">';
        appendData += '<input type="hidden" name="is_issueDate[]" class="changeMandatory-issueDate" value="0"></td>';
        appendData += '<td align="center"><input type="checkbox" name="chk_expireDate[]" class="requiredCheckbox" data-value="expireDate">';
        appendData += '<input type="hidden" name="is_expireDate[]" class="changeMandatory-expireDate" value="0"></td>';
        appendData += '<td align="center"><input type="checkbox" name="chk_issuedBy[]" class="requiredCheckbox" data-value="issuedBy">';
        appendData += '<input type="hidden" name="is_issuedBy[]" class="changeMandatory-issuedBy" value="0"></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        setup_tb.append(appendData);
        $('.requiredCheckbox').iCheck({ checkboxClass: 'icheckbox_minimal-blue' });
        $('.select2').select2();

        $('input').on('ifChanged', function(){
            changeMandatory(this);
        });

    }

    function updateDocument(){
        var postData = $('#edit-documents_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/edit_documentDescription'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_documents($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }

    $(document).on('keypress', '.number',function (event) {
        if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    $('input').on('ifChanged', function(){
        changeMandatory(this);
    });

    function changeMandatory(obj, str){
        var status = ($(obj).is(':checked')) ? 1 : 0;
        var str = $(obj).attr('data-value');
        $(obj).closest('tr').closest('tr').find('.changeMandatory-'+str).val(status);
    }

</script>



<?php
