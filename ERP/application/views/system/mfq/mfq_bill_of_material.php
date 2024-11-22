<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$company = getPolicyValues('LNG', 'All'); 

if($company == 'FlowServe'){
    $title = 'Budget';
}else{
    $title = $this->lang->line('manufacturing_bill_of_material_head');
}

echo head_page($title, false);
$gl_code_arr = dropdown_all_overHead_gl();
$unit_of_messure = all_umo_new_drop();
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #bill_of_material_table th{
        text-transform: uppercase;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-5 text-right">
        <button type="button" style="margin-right: 17px;" class="btn btn-success pull-right"
            onclick="openDocument_modal()"><i
                    class="fa fa-plus"></i> Upload Excel
        </button>
        <a type="button" style="margin-right: 17px;" href="<?php echo site_url('MFQ_BillOfMaterial/downloadExcel'); ?>" class="btn btn-info pull-right"
            ><i
                    class="fa fa-plus"></i> Download Excel
        </a>
        <button type="button" style="margin-right: 17px;" class="btn btn-primary pull-right"
                onclick="fetchPage('system/mfq/mfq_add_new_bill_of_material',null,'<?php echo $this->lang->line('manufacturing_add_new_bill_of_material_head');?>','BOM');"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_new_bill_of_material');?>
        </button>
    </div>
</div>
<hr style="margin-top: 5px;margin-bottom: 5px;">
<div id="">
    <div class="table-responsive">
        <table id="bill_of_material_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 12%"> <?php echo $this->lang->line('manufacturing_bom_code');?></th>
                <th style="min-width: 12%"> <?php echo $this->lang->line('manufacturing_description');?></th>
                <th style="min-width: 12%"> <?php echo $this->lang->line('manufacturing_product_name');?></th>
                <!--<th style="min-width: 12%"> <?php /*echo $this->lang->line('manufacturing_industry_type');*/?></th>-->
                <th style="min-width: 3%">&nbsp;</th>
            </tr>
            </thead>
        </table>
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_bill_of_material', 'Test', 'Bill Of Material');
        });
        bom_table();
    });

    function bom_table() {
        oTable = $('#bill_of_material_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_BillOfMaterial/fetch_bom'); ?>",
            //"aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "bomMasterID"},
                {"mData": "documentCode"},
                {"mData": "bomDescription"},
                {"mData": "description"},
               /* {"mData": "industryTypeDescription"},*/
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2], "orderable": false}, {"targets": [0], "searchable": false}],
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

    function deleteBOM(bomMasterID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText:"<?php echo $this->lang->line('common_cancel');?>",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_BillOfMaterial/deleteBOM'); ?>",
                    type: 'post',
                    data: {bomMasterID: bomMasterID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            oTable.draw();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });

    }

    function openDocument_modal(){
        $('#migrationExcelUpload_Modal').modal({backdrop: "static"});
    }

    function excel_upload_migration_config(){

        var formData = new FormData($("#excelUpload_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_BillOfMaterial/bom_excelUpload'); ?>",
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
                    $('#migrationExcelUpload_Modal').modal('hide');
                    bom_table();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }


</script>