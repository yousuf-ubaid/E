<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_others_master', $primaryLanguage);
echo head_page($this->input->post('page_name'), false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <?php echo form_open('', 'role="form" id="migration_doc_form" style="padding: 5px;"'); ?>
    <div class="col-sm-12 form-inline">
        <div class="form-group">
            <label>Document</label>
            <?php echo form_dropdown('documentID', drop_down_migration_config_document(), '', 'class="" onchange="fetch_document_type()" id="documentID"  required"'); ?>
        </div>

        <div class="form-group hide" id="open_doc_type">
            <label>Document Type</label>
            <input type="hidden" name="isdocumentType" id="isdocumentType">
            <select name="documentType" id="documentType" class="form-control select2 ">
                            <!--Select Category-->
            </select>
        </div>

        <div class="form-group" id="">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary-new size-sm"
                    onclick="download_migration_excel_file()"> Download<!--Search-->
            </button>
        </div>
        <div class="form-group hide" id="upload_btn_mig">
            <button type="button" style="    margin-top: 24px;" class="btn btn-primary-new size-sm pull-right "
                onclick="openDocument_modal()"> Upload<!--Add Employees-->
            </button>
        </div>
        
    </div>
    </form>
    
</div>


<hr>
    <div class="table-responsive">
        <table id="empBankTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">Document ID</th>
                <th style="min-width: 10%">Document Type</th>
                <th style="min-width: 25%">Created DateTime</th>
                <th style="min-width: 10%">No Of Records</th>
                <!-- <th style="min-width: 10%">Status</th> -->
                <th style="min-width: 5%">Action</th>
            </tr>
            </thead>
        </table>
    </div>

<!-- <div class="row">
    <div class="table-responsive" style="padding: 0px !important;">
        <?php echo form_open('', 'role="form" class="" id="attendanceReview_form" autocomplete="off"'); ?>
        <div class="fixHeader_Div" style="max-width: 100%;">
            <table id="attendanceReview" class="table tb "
                    style="max-width: 1750px !important;">
                <thead class="">
                <tr style="white-space: nowrap">
                    <th style="width: 15px;">#</th>
                    <th style="width: 100px;">
                    DocumentDate</th>
                    <th style="min-width: 120px;">
                    supplierCode</th>

                 
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td colspan="21">
                        No data available in table
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php echo form_close(); ?>
    </div>
</div> -->


<div class="modal fade" id="bankTransactionModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_navigation_access');?><!--Navigation Access--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo form_open('', 'role="form" id="save_employee_access"'); ?>
                        <div class="form-group"><label style="width: 100px"
                                                       for=""><?php echo $this->lang->line('common_company');?><!--Company--> </label> <?php echo form_dropdown('companyID', Drop_down_group_of_companies(), '', 'class="" onchange="loaddropdown(),userGroupDropdown()" id="companyID" style="width:250px" required"'); ?>
                        </div>
                        <div class="form-group " id="loaddropdown"></div>
                        <div class="form-group " id="usergroup_dropdown"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_employees()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new_documents" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload document</h4>
            </div>
            <form class="form-horizontal" id="add-documents-form" >
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label for="doc_file" class="col-sm-4 control-label">
                            <?php echo $this->lang->line('emp_documents_file');?><!--File-->
                        </label>
                        <div class="col-sm-8">
                            <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here" accept=".xls,.xlsx">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('emp_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="migrationExcelUpload_Modal" style="z-index:10000000;"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Excel upload form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="excelUpload_form" class="form-inline"'); ?>
                    <input type="hidden" name="docID" id="docID">
                    <input type="hidden" name="isdocTypeID" id="isdocTypeID">
                    <input type="hidden" name="docTypeID" id="docTypeID">
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput" style="min-width: 200px; width: 100%;
                                    border-bottom-left-radius: 3px !important; border-top-left-radius: 3px !important; ">
                                    <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>
                                    <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span>
                                    <input type="file" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                                </span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="excel_upload_migration_config()">
                            <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
            <form role="form" id="downloadTemplate_form">
            </form>

        </div>
    </div>
</div>

<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
     var empBankTbl = $('#empBankTB');
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/migration/load_applicable_document','','Load Document');
        });

        $('#documentID').select2();
        $('#documentType').select2();
 
        fetch_migration_submission();

    });

    function fetch_document_type(){
        var docID =$('#documentID').val();

        if(!docID){
            $('#open_doc_type').addClass('hide');
            myAlert('e', 'Please select document');
        }else{
            $('#upload_btn_mig').addClass('hide');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {documentID: docID},
                url: "<?php echo site_url('MigrationDocument/fetch_document_type'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                   
                    if(data['status']==true){
                        $('#open_doc_type').removeClass('hide');

                        var result=data['result'];
                        $('#documentType').empty();
                        var mySelect = $('#documentType');
                        $.each(result, function(key,v) {
                            mySelect.append($('<option></option>').val(v.documentType).html(v.documentTypeName));
                        });

                        $('#isdocumentType').val(1);
                        
                    }else{
                        $('#open_doc_type').addClass('hide');
                        $('#isdocumentType').val(0);
                    }

                }, error: function () {

                }
            });
        }
    }

    function fetch_migration_submission() {
        empBankTbl = $('#empBankTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MigrationDocument/fetch_migration_submission'); ?>",
            "aaSorting": [[2, 'desc']],
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
            },
            "aoColumns": [
                {"mData": "migrationHeaderMasterID"},
                {"mData": "documentID"},
                {"mData": "doc_type"},
                {"mData": "createdDateTime"},
                {"mData": "noOfRecords"},
              
                {"mData": "edit"},
            ],
            "columnDefs": [ {
                "targets": [0,4],
                "orderable": false
            }, {"searchable": false, "targets": [0]} ],
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

    function download_migration_excel_file() {
       var docID =$('#documentID').val();
       var isDocType =$('#isdocumentType').val();

       if(!docID){
        $('#upload_btn_mig').addClass('hide');
        myAlert('e', 'Please select document');
       }else{

            if(isDocType==1){

                var documentType =$('#documentType').val();

                if(!documentType){
                    myAlert('e', 'Please select document type');
                }else{
                    $('#upload_btn_mig').removeClass('hide');
                    $('#docID').val(docID);
                    $('#docTypeID').val(documentType);
                    $('#isdocTypeID').val(isDocType);
                    var form = document.getElementById('migration_doc_form');
                    form.target = '_blank';
                    form.method = 'post';
                    form.post = $('#migration_doc_form').serializeArray();
                
                    form.action = '<?php echo site_url('MigrationDocument/downloadExcel'); ?>';
                    form.submit();
                }

            }else{
                $('#upload_btn_mig').removeClass('hide');
                $('#docID').val(docID);
                $('#isdocTypeID').val(isDocType);
                var form = document.getElementById('migration_doc_form');
                form.target = '_blank';
                form.method = 'post';
                form.post = $('#migration_doc_form').serializeArray();
            
                form.action = '<?php echo site_url('MigrationDocument/downloadExcel'); ?>';
                form.submit();
            }
           
       }

    }

    function openDocument_modal(){
      //  $('#setup-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
      //  $('.saveInputs').val('');
        $('#migrationExcelUpload_Modal').modal({backdrop: "static"});
    }

    function excel_upload_migration_config(){

        var formData = new FormData($("#excelUpload_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MigrationDocument/migration_master_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                   // $('#excelUpload_Modal').modal('hide');
                    //$('#attendanceReview >tbody').html(data['tBody']);
                   fetch_migration_submission();
                    setTimeout(function(){
                       // $('#sendEmail').submit();
                    }, 1500);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    /////////////////////////////////////////////////

    function delete_migration_recode(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'migrationHeadeID': id},
                    url: "<?php echo site_url('MigrationDocument/delete_migration_recode'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        empBankTbl.ajax.reload();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }


</script>