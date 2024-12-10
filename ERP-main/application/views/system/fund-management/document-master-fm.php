<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #setup-add-tb td, #master-setup-add-tb td{ padding: 2px; }
    #setup-edit-tb td{ padding: 2px; }
    .number{ width: 90px !important;}
    legend{ font-size: 16px !important;}

    #filter_div .select2-container{
        width: 180px !important;
    }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_document_setup');
echo head_page($title  , false);

$system_documents_arr = system_documents_drop();
$system_documents_arr2 = system_documents_drop(1);
$invType_arr = investmentType_drop();
?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row" style="margin-bottom: 10px">
    <div class="col-md-12">
        <div class="col-md-8">
            <form class="form-inline">
                <div class="form-group" id="filter_div">
                    <label for="filter_sys_docID"><?php echo $this->lang->line('fn_man_document_type');?>&nbsp;: &nbsp; &nbsp; </label>
                    <?php echo form_dropdown('filter_sys_docID', $system_documents_arr2, '', ' class="form-control" id="filter_sys_docID" required onchange="load_documents_setup()"'); ?>
                </div>
            </form>
        </div>
        <div class="col-md-4 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openSetup_modal()" ><i class="fa fa-cog"></i>&nbsp;
                <?php echo $this->lang->line('fn_man_document_setup');?>
            </button>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="document_setup_tb" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('fn_man_document_type');?></th>
            <th style="width: auto"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th style="width: auto"><?php echo $this->lang->line('fn_man_investment_types');?><!--Document--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_mandatory');?></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
            <th style="width: 80px"><?php echo $this->lang->line('fn_man_expiry_alert_before');?></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>


<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="new_documents" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('fn_man_document_setup');?></h4>
            </div>
            <form class="form-horizontal" id="add-documents_form" autocomplete="off">
                <div class="modal-body">
                    <div class="" style="margin-bottom: 5px">
                        <div class="col-md-12">
                            <label for="inputData" class="col-md-4 control-label"> <?php echo $this->lang->line('fn_man_document_type'); ?> </label>
                            <div class="col-md-4">
                                <?php echo form_dropdown('sys_docID', $system_documents_arr, '', ' class="form-control select2" id="sys_docID" onchange="display_inv_type_drop(this)"'); ?>
                            </div>
                        </div>

                        <div class="col-md-12 invType" style="margin-top: 10px; display: none">
                            <label class="col-sm-4 control-label" for="invType"><?php echo $this->lang->line('fn_man_investment_types');?></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('invType', $invType_arr, '', 'class="form-control select2" id="invType"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="" style="margin-bottom: 5px">

                    </div>

                    <div class="" style="margin-bottom: 5px">&nbsp;</div>

                    <table class="table table-bordered" id="setup-add-tb">
                        <thead>
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th rowspan="2" style="width: 70px"><?php echo $this->lang->line('hrms_others_master_is_mandatory');?><!--Is&nbsp;Mandatory--></th>
                            <th colspan="2"><?php echo $this->lang->line('common_is_required');?><!--Is required--></th>
                            <th rowspan="2">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_setup()" ><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>

                        <tr>
                            <th><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
                            <th> <abbr title="<?php echo $this->lang->line('fn_man_expiry_alert_before');?>">EAB</abbr> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="description[]" class="form-control saveInputs" value="">
                            </td>
                            <td align="center">
                                <input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" checked >
                                <input type="hidden" name="isRequired[]" class="changeMandatory-req" value="1">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="chk_expireDate[]" class="requiredCheckbox" data-value="expireDate" onchange="changeMandatory(this)">
                                <input type="hidden" name="is_expireDate[]" class="changeMandatory-expireDate" value="0">
                            </td>
                            <td align="center">
                                <input type="text" name="expiry_alert[]" class="form-control number" id="expiry_alert_txt" value="0">
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_document_setup()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('fn_man_edit_document_setup');?></h4>
            </div>

            <form class="form-horizontal" id="edit-documents_form" autocomplete="off">
                <div class="modal-body">

                    <div class="" style="margin-bottom: 5px">
                        <div class="col-md-12">
                            <label for="inputData" class="col-md-4 control-label"> <?php echo $this->lang->line('fn_man_document_type'); ?></label>
                            <div class="col-md-4">
                                <?php echo form_dropdown('edit_sys_docID', $system_documents_arr, '', ' class="form-control select2" id="edit_sys_docID" disabled '); ?>
                            </div>
                        </div>

                        <div class="col-md-12 edit_invType" style="margin-top: 10px; display: none">
                            <label class="col-sm-4 control-label" for="invType"><?php echo $this->lang->line('fn_man_investment_types');?></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('invType', $invType_arr, '', 'class="form-control select2" id="edit_invType" disabled'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="" style="margin-bottom: 5px">&nbsp;</div>

                    <table class="table table-bordered" id="setup-edit-tb">
                        <thead>
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th rowspan="2" style="width: 95px"><?php echo $this->lang->line('hrms_others_master_is_mandatory');?><!--Is&nbsp;Mandatory--></th>
                            <th colspan="2"><?php echo $this->lang->line('common_is_required');?><!--Is required--></th>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
                            <th> <?php echo $this->lang->line('fn_man_expiry_alert_before');?> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="edit_docDescription" id="edit_docDescription" class="form-control" value="">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_isMandatory" id="edit_isMandatory" class="requiredCheckbox" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="edit_expireDate" id="edit_expireDate" class="requiredCheckbox" />
                            </td>
                            <td align="center">
                                <input type="text" name="expiry_alert" id="edit_expiry_alert" class="form-control number" value="0">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden_setupID" name="setupID" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateDocumentSetup()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var master_setup_tb = $('#master-setup-add-tb');
    var setup_tb = $('#setup-add-tb');
    $('.select2').select2();

    $('#filter_sys_docID').select2();


    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/fund-management/document-master-fm','Test','HRMS');
        });

        load_documents_setup();

        $('.requiredCheckbox').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
        });
    });

    function display_inv_type_drop(obj){
        var dType = $(obj).val();

        $('.invType').hide();
        if(dType == 'FMIT'){
            $('.invType').show();
            $('#invType').val('').change();
        }
    }

    function load_documents_setup(selectedRowID=null){
        $('#document_setup_tb').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Fund_management/fetch_document_setup'); ?>",
            "aaSorting": [[1, 'asc']],
            "columnDefs": [ {
                "targets": [0,4,5,7],
                "orderable": false
            }, {"searchable": false, "targets": [0]}],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();


                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['docSetupID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "docSetupID"},
                {"mData": "syDescription"},
                {"mData": "docDescription"},
                {"mData": "invDescription"},
                {"mData": "st_isMandatory"},
                //{"mData": "st_issueDate_req"},
                {"mData": "st_expireDate_req"},
                {"mData": "st_sendNot"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                var sys_docID = $('#filter_sys_docID').val();
                aoData.push({'name': 'sys_docID', 'value':sys_docID});
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

    function openSetup_modal(){
        $('#setup-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#expiry_alert_txt').val(0);
        $('#sys_docID').val('').change();
        $('#new_documents').modal({backdrop: "static"});
    }

    function add_more_setup(){

        var appendData = '<tr><td><input type="text" name="description[]" class="form-control" value=""></td>';
        appendData += '<td align="center"><input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory()">';
        appendData += '<input type="hidden" name="isRequired[]" class="changeMandatory-req" value="0"></td>';
        appendData += '<td align="center"><input type="checkbox" name="chk_expireDate[]" class="requiredCheckbox" data-value="expireDate">';
        appendData += '<input type="hidden" name="is_expireDate[]" class="changeMandatory-expireDate" value="0"></td>';
        appendData += '<td align="center"><input type="text" name="expiry_alert[]" class="form-control number" value="0"></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        setup_tb.append(appendData);

        $('.requiredCheckbox').iCheck({ checkboxClass: 'icheckbox_minimal-blue' });
        $('.select2').select2();

        $('input').on('ifChanged', function(){
            changeMandatory(this);
        });

    }

    function save_document_setup(){
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
                url: '<?php echo site_url('Fund_management/save_document_setup'); ?>',
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
                        load_documents_setup();
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');/*Please fill all Document fields*/
        }
    }

    function edit_docSetup(id, thisTR){
        var table = $('#document_setup_tb').DataTable();
        var thisRow = $(thisTR);
        var details = table.row(  thisRow.parents('tr') ).data()  ;

        $('#editModal').modal({backdrop: "static"});

        $('#hidden_setupID').val( $.trim(id) );
        $('#edit_docDescription').val( $.trim(details.docDescription) );
        $('#edit_sys_docID').val( $.trim(details.systemDocumentID) ).change();
        $('#edit_expiry_alert').val( $.trim(details.sendExpiryAlertBefore) );
        var edit_invType = $('.edit_invType');
        var edit_isMandatory = $('#edit_isMandatory');
        var edit_expireDate = $('#edit_expireDate');

        edit_invType.hide();

        if($.trim(details.systemDocumentID) == 'FMIT'){
            edit_invType.show();
            $('#edit_invType').val( $.trim(details.documentSubID) ).change();
        }


        edit_isMandatory.prop('checked', false)//.val(0);
        edit_expireDate.prop('checked', false);

        if( $.trim(details.isMandatory) == 1 ){
            edit_isMandatory.prop('checked', true)//.val(1);
        }
        edit_isMandatory.iCheck('update');


        if( $.trim(details.expireDate_req) == 1 ){
            edit_expireDate.prop('checked', true);
        }
        edit_expireDate.iCheck('update');

    }

    function updateDocumentSetup(){
        var postData = $('#edit-documents_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Fund_management/edit_documentDocumentSetup'); ?>',
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
                    load_documents_setup($('#hidden_setupID').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

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
                    url :"<?php echo site_url('Fund_management/delete_documentSetup'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_documents_setup() }
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

    $('.number').keypress(function (event) {
        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });
</script>

<?php
