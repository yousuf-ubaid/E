<?php
$emp_document = emp_document_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();

$company_drop = group_company_drop();
if(empty($company_drop)){
    $company_drop[] = [
        'company_id' => current_companyID(),
        'cName' => current_companyName()
    ];
}
$document_sys_sub_type = emp_document_sys_sub_type_data();

if(!empty($document_sys_sub_type)){
    $document_sys_sub_type = array_group_by($document_sys_sub_type, 'system_type_id');
}

$counties = fetch_emp_countries();
$county_drop_str = '';
if (!empty($counties)) {
    function language_string_conversion2($string){
        $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), trim($string)));
        return $outputString;
    }

    foreach ($counties as $key => $val) {
        $output = language_string_conversion2('country_' . $val);
        $translation = $this->lang->line($output);

        $showDescription = (!empty(trim($translation)))? $translation: $val;
        $county_drop_str .= '<option value="'.$key.'"> '.$showDescription.'</option>';
    }
}
?>

<style type="text/css">
    .thumbnail{
        width:100px;
        height:140px;
        text-align:center;
        display:inline-block;
        margin:0 10px 10px 0;
        float: left;
    }
    .required-img{ width: 10px; height: 10px; }

    .com_name{ color: #0088cc; }

    .com_name:hover{ cursor: pointer; }

</style>


<div class="modal fade" data-backdrop="static" id="addDoc_modal" role="dialog">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('emp_documents_add_new');?></h4>
            </div>

            <?php echo form_open('','role="form" class="form-horizontal" id="empDoc_form" '); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_document');?><!--Document--></label>
                    <div class="col-sm-5">
                        <select name="document" class="form-control select2 " id="document" onchange="make_drop_down()">
                        <?php
                        $str = '<option value="" selected="selected">Select Document</option>';
                        if (!empty($emp_document)) {
                            foreach ($emp_document as $row) {
                                $str .= '<option value="'.trim($row['DocDesID'] ?? '').'" data-type="'.trim($row['systemTypeID'] ?? '').'" ';
                                $str .= 'data-drop="'.trim($row['issuedByType'] ?? '').'">'.trim($row['DocDescription'] ?? '').'</option>';
                            }
                        }
                        echo $str;
                        ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="sub_document_type_container" style="display: none;">
                    <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_sub_document_type');?></label>
                    <div class="col-sm-5">
                        <?php echo form_dropdown('sub_document_type', [], '','class="form-control select2" id="sub_document_type"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="doc_no" class="col-sm-4 control-label"><?php echo $this->lang->line('common_documents_no');?></label>
                    <div class="col-sm-5">
                        <input type="text" name="doc_no" class="form-control" id="doc_no">
                    </div>
                </div>
                <div class="form-group">
                    <label for="doc_file" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_file');?><!--File--></label>
                    <div class="col-sm-5">
                        <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here">
                    </div>
                </div>
                <div class="form-group">
                    <label for="issueDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></label>
                    <div class="col-sm-3">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="issueDate" value=""
                                   class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expireDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></label>
                    <div class="col-sm-3">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="expireDate" value=""
                                   class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="issuedBy" class="col-sm-4 control-label"><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></label>
                    <div class="col-sm-5">
                        <div class="input-group issuedBy-new-div" style="display: none">
                            <div class="input-group-addon" onclick="issueBy_text_hide('issuedBy-new')"><i class="fa fa-pencil"></i></div>
                            <input type="text" name="issuedByText" value="" class="form-control">
                        </div>
                        <select name="issuedBy" class="form-control select2" id="issuedBy-new" onchange="issueBy_text_show('issuedBy-new')">
                            <option value="">Select a option</option>
                            <?php
                            foreach($company_drop as $c_row){
                                echo '<option value="'.$c_row['company_id'].'"> '.$c_row['cName'].'</option>';
                            }
                            ?>
                            <option value="-1">Others</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="isCTC" class="col-sm-4 control-label"><?php echo $this->lang->line('common_isCTC');?><!--Cost to Company--></label>
                    <div class="col-sm-5">
                        <input type="checkbox" name="isCTC" class="checkbox" id="isCTC">
                    </div>
                </div>

                <div class="form-group" id="ctcCostGroup" style="display: none;">
                    <label for="ctcCost" class="col-sm-4 control-label"><?php echo $this->lang->line('common_ctccost');?></label>
                    <div class="col-sm-5">
                        <input type="text" name="ctcCost" class="form-control" id="ctcCost">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="docEmpID" id="docEmpID" value="">
                <button type="button" class="btn btn-primary btn-sm" onclick="emp_documentSave()"><?php echo $this->lang->line('common_save');?></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody><tr>
                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_submited');?></td>
                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_submited');?></td>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_active');?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="open_addDocModal()" ><i class="fa fa-plus-square"></i>&nbsp;
            <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div><hr>
<div class="row table-responsive">
    <table id="emp_documents_tb" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: 200px"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
            <th style="width: 200px"><?php echo $this->lang->line('common_other_details');?></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_isCTC');?></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_ctccost');?></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_status');?></th>
            
            <th style="width: 150px"></th>

            <!--Below column used for search-->
            
            <th style="width: 1px"><?php echo $this->lang->line('common_issued_by');?></th>
            <th style="width: 1px"><?php echo $this->lang->line('common_documents_no');?></th>
            <th style="width: 1px"><?php echo $this->lang->line('common_document');?></th>
            <th style="width: 1px"><?php echo $this->lang->line('emp_sub_document_type');?></th>
        </tr>
        </thead>
    </table>
</div>

<select style="display: none" id="company-drop" >
    <option value="">Select a option</option>
    <?php
    foreach($company_drop as $c_row){
        echo '<option value="'.$c_row['company_id'].'"> '.$c_row['cName'].'</option>';
    }
    ?>
    <option value="-1">Others</option>
</select>

<select style="display: none" id="country-drop" >
    <?=$county_drop_str?>
    <option value="-1">Others</option>
</select>

<?php
if($isFromProfile == 'Y'){
    echo '<script type="text/javascript"> var fromHiarachy = 0; </script>';
    echo '<script type="text/javascript"> $(\'#docEmpID\').val( \''. current_userID().'\' ); </script>';
}
else{
    echo '<script type="text/javascript"> $(\'#docEmpID\').val( $(\'#updateID\').val() ); </script>';
}
?>

<script type="text/javascript">
    var history_tb = null;
    var emp_doc_tb = null;
    var sub_type = <?php echo json_encode($document_sys_sub_type) ?>;

    function issueBy_text_show(obj){
        var thisObj = $('#'+obj);
        if( thisObj.val() == -1 ){
            thisObj.select2('destroy');
            thisObj.hide();
            $('.'+obj+'-div').show();
            $('#issuedByText').val('');
        }
    }

    function issueBy_text_hide(obj){
        var thisObj = $('#'+obj);
        thisObj.select2();
        thisObj.val('').change();
        $('.'+obj+'-div').hide();
    }

    $("[rel=tooltip]").tooltip();

    $('#issuedBy-new').select2();

    if(fromHiarachy == 1){
        $('.btn, .fa-times-circle').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
        $('.form-control:not([type="search"], #parentCompanyID)').attr('disabled', true);
    }

    $(document).ready(function () {
        // When the checkbox is clicked
        $("#isCTC").change(function () {
            // If the checkbox is checked, show the input field; otherwise, hide it
            if (this.checked) {
                $("#ctcCostGroup").show();
            } else {
                $("#ctcCostGroup").hide();
            }
        });
    });

    $(document).ready(function () {
    // When the checkbox is clicked
    $("#edit_isCTC").change(function () {
        // If the checkbox is checked, show the input field; otherwise, hide it
        if (this.checked) {
            $("#ctcCostGroupedit").show();
        } else {
            $("#ctcCostGroupedit").hide();
        }
    });
});


    $(document).ready(function() {
        load_emp_documents();
    });

    function load_emp_documents(selectedRowID=null){
        emp_doc_tb = $('#emp_documents_tb').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_emp_document'); ?>",
            "aaSorting": [[2, 'desc']],
            "columnDefs": [
                { "targets": [0,1,4,7,8], "orderable": false },
                { "visible": false, "targets": [5,6,9,10,11,12] }
               
            ],
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
                {"mData": "DocDesFormID"},
                {"mData": "fullDescription"},
                {"mData": "issueDate"},
                {"mData": "expireDate"},
                {"mData": "otherDetails"},
                {"mData": "isCTC", "visible": false},
                {"mData": "ctcCost", "visible": false},
                {"mData": "docs_status"},
                {"mData": "edit"},
                {"mData": "issueDet"},
                {"mData": "documentNo"},
                {"mData": "DocDescription"},
                {"mData": "sub_typesDes"}
                
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'empID', 'value': '<?= $empID ?>'});
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

    $('.select2').select2();

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    function emp_documentSave(){
        var formData = new FormData($("#empDoc_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Employee/emp_documentSave'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    emp_doc_tb.ajax.reload();
                    $('#addDoc_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#empDoc_form1').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            document: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_is_required');?>.'}}},/*Document is required*/
            doc_file: {
                validators: {
                    file: {
                        maxSize: 5120 * 1024,   // 5 MB
                        message: '<?php echo $this->lang->line('common_the_selected_file_is_not_valid');?>'/*The selected file is not valid*/
                    },
                    notEmpty: {message: '<?php echo $this->lang->line('common_file_is_required');?>.'}/*File is required*/
                }
            }
        },
    })
    .on('success.form.bv', function (e) {
        e.preventDefault();

        var formData = new FormData($("#empDoc_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Employee/emp_documentSave'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    load_emp_documents();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    });

    function open_addDocModal(){
        $('#empDoc_form')[0].reset();
        $('#document, #issuedBy-new').change();

        $('#addDoc_modal').modal({backdrop: "static"});
    }

    function removeDocument(removeID, from){
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
                    async : true,
                    url :"<?php echo site_url('Employee/delete_empDocument'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':removeID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            load_emp_documents();
                            if(from == 'his'){
                                history_tb.ajax.reload();
                            }
                        }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function editDocument(id, thisTR){
        var table = $('#emp_documents_tb').DataTable();
        var thisRow = $(thisTR);
        var details = table.row(  thisRow.parents('tr') ).data();
        var dropType = details.issuedByType;
        dropType = (dropType == 1)? 'company': 'country';
        var dropType_options = $('#'+dropType+'-drop').html();
        var thisObj = $('#issuedBy');
        thisObj.empty().append(dropType_options);

        $('#editModal').modal({backdrop: "static"});

        $('#editID-doc').val(id);
        $('#documentType').val(details.DocDesID);
        $('#docDescription').val(details.DocDescription);
        $('#issueDate').val(details.issueDate);
        $('#expireDate').val(details.expireDate);
        $('#edit_documentNo').val(details.documentNo);
        $('#edit_isCTC').prop('checked', details.isCTC == 1); // Set the checkbox based on isCTC value
        $('#edit_ctcCost').val(details.ctcCost);
       
        var issuedBy = details.issuedBy;


        if(issuedBy == -1){
            thisObj.select2();
            thisObj.select2('destroy');
            thisObj.val(issuedBy);
            thisObj.hide();
            $('.issuedBy-div').show();
        }
        else{
            thisObj.select2();
            thisObj.val(issuedBy).change();
            $('.issuedBy-div').hide();
        }

        var sub_document_type_container = $('#edit_sub_document_type_container');
        sub_document_type_container.hide();
        if( details.sub_typesDes != '' && details.sub_typesDes != null){
            $('#edit_sub_document_type').val(details.sub_typesDes);
            sub_document_type_container.show();
        }
        
        $('#issuedByText').val(details.issuedByText);

      
    }

    function updateDocumentDetails(){
        var formData = new FormData($("#edit-documents_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Employee/emp_documentUpdate'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_emp_documents();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function downloadDoc(url) {
        window.open(url, '_blank');
    }

    function load_inactiveDocs(docType, desc){
        $('#history-doc-type').text(desc);
        $('#inactiveDocs_modal').modal('show');
        inactiveDocs_table(docType);
    }

    function inactiveDocs_table(docType){
        history_tb = $('#emp_documents_history_tb').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_emp_document_history'); ?>",
            "aaSorting": [[1, 'asc']],
            "columnDefs": [ {
                "targets": [0,7,8],
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
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "DocDesFormID"},
                {"mData": "DocDescription"},
                {"mData": "sub_typesDes"},
                {"mData": "documentNo"},
                {"mData": "issueDate"},
                {"mData": "expireDate"},
                {"mData": "issueDet"},
                {"mData": "docs_status"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'docType', 'value': docType});
                aoData.push({'name':'empID', 'value': '<?= $empID ?>'});
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

    function documentUpload(id, desc){
        $('#upload_form')[0].reset();
        $('#uploadDocID').val(id);
        $('#upload-doc-type').text(desc);
        $('#uploadDocs_modal').modal('show');
    }

    function file_upload(){
        var formData = new FormData($("#upload_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Employee/emp_documentUpload'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    emp_doc_tb.ajax.reload();
                    $('#uploadDocs_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function make_drop_down(){
        var systemID = $('#document :selected').attr('data-type');
        var dropType = $('#document :selected').attr('data-drop');

        var sub_document_type_container = $('#sub_document_type_container');
        var sub_document_type = $('#sub_document_type');
        sub_document_type.empty();

        $('<option />', {value: '', text: 'Select sub type'}).appendTo(sub_document_type);
        sub_document_type_container.hide();

        if(systemID in sub_type){
            $(sub_type[systemID]).each(function(key, arr){
                $('<option />', {value: arr['sub_id'], text: arr['description']}).appendTo(sub_document_type);
            });

            sub_document_type_container.show();
        }

        dropType = (dropType == 1)? 'company': 'country';
        var dropType_options = $('#'+dropType+'-drop').html();
        $('#issuedBy-new').empty().append(dropType_options);

    }
</script>

<div class="modal fade" id="uploadDocs_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_document_upload');?> - <b style="font-size: 14px;" id="upload-doc-type"></b></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="upload_form" '); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="doc_file" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_file');?><!--File--></label>
                    <div class="col-sm-5">
                        <input type="file" name="doc_file" class="form-control" id="up_doc_file" placeholder="Brows Here">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="uploadDocID" id="uploadDocID" value="">
                <button type="button" class="btn btn-primary btn-sm" onclick="file_upload()"><?php echo $this->lang->line('common_save');?></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="inactiveDocs_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_history');?> - <b style="font-size: 14px;" id="history-doc-type"></b></h4>
            </div>

            <div class="modal-body">
                <div class="row table-responsive">
                    <table id="emp_documents_history_tb" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="width: auto"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                            <th style="width: auto"><?php echo $this->lang->line('emp_sub_document_type');?></th>
                            <th style="width: 100px"><?php echo $this->lang->line('common_documents_no');?></th>
                            <th style="width: 80px"><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></th>
                            <th style="width: 80px"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></th>
                            <th style="width: auto"><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></th>
                            <th style="width: 80px"><?php echo $this->lang->line('common_status');?></th>
                            <th style="width: 60px"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('emp_documents_edit');?><!--Document Details Edit--></h4>
            </div>

            <form class="form-horizontal" id="edit-documents_form" autocomplete="off">
                <div class="modal-body">
                    <div class="box-body" style="background: #ffffff;">
                        <input type="hidden" name="editID" id="editID-doc" value="">
                        <input type="hidden" name="documentType" id="documentType" value="">
                        <div class="form-group">
                            <label for="expireDate" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_document');?><!--Document--></label>
                            <div class="col-sm-6">
                                <input type="text" name="docDescription" value="" id="docDescription" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group" id="edit_sub_document_type_container" style="display: none;">
                            <label for="edit_sub_document_type" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_sub_document_type');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="edit_sub_document_type" value="" id="edit_sub_document_type" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_documentNo" class="col-sm-4 control-label"><?php echo $this->lang->line('common_documents_no');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="edit_documentNo" value="" id="edit_documentNo" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="issueDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_issue_date');?><!--Issue Date--></label>
                            <div class="col-sm-3">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="issueDate" value="" id="issueDate"
                                           class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="expireDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></label>
                            <div class="col-sm-3">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expireDate" value="" id="expireDate"
                                           class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="issuedBy" class="col-sm-4 control-label"><?php echo $this->lang->line('common_issued_by');?><!--Issued By--></label>
                            <div class="col-sm-6">
                                <div class="input-group issuedBy-div" style="display: none">
                                    <div class="input-group-addon" onclick="issueBy_text_hide('issuedBy')"><i class="fa fa-pencil"></i></div>
                                    <input type="text" name="issuedByText" id="issuedByText" value="" class="form-control">
                                </div>
                                <select name="issuedBy" class="form-control select2" id="issuedBy" onchange="issueBy_text_show('issuedBy')">
                                    <option value="">Select a option</option>
                                    <?php
                                    foreach($company_drop as $c_row){
                                        echo '<option value="'.$c_row['company_id'].'"> '.$c_row['cName'].'</option>';
                                    }
                                    ?>
                                    <option value="-1">Others</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_isCTC" class="col-sm-4 control-label"><?php echo $this->lang->line('common_isCTC');?><!--Cost to Company--></label>
                            <div class="col-sm-5">
                                <input type="checkbox" name="edit_isCTC" class="checkbox" id="edit_isCTC">
                            </div>
                        </div>

                        <div class="form-group" id="ctcCostGroupedit" style="display: none;">
                            <label for="editctcCost" class="col-sm-4 control-label"><?php echo $this->lang->line('common_ctccost');?></label>
                            <div class="col-sm-5">
                                <input type="text" name="edit_ctcCost" class="form-control" id="edit_ctcCost">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateDocumentDetails()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-08
 * Time: 5:57 PM
 */