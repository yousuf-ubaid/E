<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helpers('logistics');
$servcetype = all_logistic_servicetype_drop();
echo head_page('Uploads', false);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">

    </div>
</div>

<div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-3 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="upload_logistics()"><i class="fa fa-plus"></i> Upload</button><!--Create-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="logistic_upload_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th>#</th>
            <th>Service Type</th>
            <th>Declaration Number</th>
            <th>Version</th>
            <th>Regime</th>
            <th>Type</th>
            <th>Importer</th>
            <th>Exporter</th>
            <th>Transport Document</th>
            <th>partial Released</th>
            <th>Submission Date</th>
            <th>Declaration Status</th>
            <th>Processing Status</th>
            <th>Payment Status</th>
            <th>Reveiw Status</th>
            <th>Released Date</th>
            <th>Invoice</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="excelUpload_Modal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">upload form</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart('', 'id="logisticUpload_form" class="form-inline"'); ?>
                <div class="row">
                    <div class="form-group col-sm-6 col-md-offset-1">
                        <label class="title">Service Type</label> &nbsp;
                        <?php echo form_dropdown('servicetype',$servcetype, '', 'class="form-control select2" id="servicetype"'); ?>
                    </div>
                </div>
                <div class="row">


                    <div class="col-sm-12 col-md-offset-1" style="margin-left: 8%">
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
                        <button type="button" class="btn btn-default" onclick="excel_upload_logistics()">
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
<div class="modal fade" id="logistic_upload_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>

            <form class="form-horizontal" id="insufficient_form">
                <div class="modal-body">
                    <div id="insufficient_item">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Error Description</th>
                            </tr>
                            </thead>
                            <tbody id="logistic_upload_body">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="upload_releasedate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Released Date</h4>
            </div>
            <?php echo form_open('', 'role="form" id="upload_releasedate_frm"'); ?>
            <div class="modal-body">
                <input type="hidden" id="uploadId" name="uploadId">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Released Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                  <input type="text" required class="form-control input-sm releaseddatepic" name="releasedate"
                         id="releasedate"
                         value="<?php echo date('d-m-Y 00:00:00') ?>">
                <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="update_reldate()" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?><!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var contractAutoID;
    var Otable;

    $(document).ready(function() {
        $('.select2').select2();
        $('.releaseddatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY HH:mm",
            sideBySide: false,
            widgetPositioning: {

            }
        }).on('dp.change', function (ev) {
        });

        $('.headerclose').click(function(){
            fetchPage('system/logistics/uploads','','Uploads');
        });
        contractAutoID = null;
        number_validation();
        logisticupload();

        Inputmask().mask(document.querySelectorAll("input"));

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;


        function logisticupload(selectedID=null){
            Otable = $('#logistic_upload_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Logistics/logistics_fetchuploads'); ?>",
                "aaSorting": [[0, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {

                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if( parseInt(oSettings.aoData[x]._aData['contractAutoID']) == selectedRowID ){
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');

                        }
                        x++;
                    }
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');

                },
                "aoColumns": [
                   {"mData": "uploadID"},
                   {"mData": "serviceType"},
                   {"mData": "declarationno"},
                   {"mData": "version"},
                   {"mData": "regime"},
                   {"mData": "uploadType"},
                   {"mData": "customerSystemCode"},
                   {"mData": "exporter"},
                   {"mData": "transportDocument"},
                   {"mData": "partial"},
                   {"mData": "submissionDate"},
                   {"mData": "declaration"},
                   {"mData": "processing"},
                   {"mData": "payment"},
                   {"mData": "reveiw"},
                   {"mData": "edit"},
                   {"mData": "uploaddocview"},
                    /*{"mData": "approved"},
                    {"mData": "edit"},
                    {"mData": "contractNarration"},
                    {"mData": "customerMasterName"},
                    {"mData": "contractDate"},
                    {"mData": "contractExpDate"},
                    {"mData": "contractType"},
                    {"mData": "isDeleted"},
                    {"mData": "detTransactionAmount"},
                    {"mData": "referenceNo"}*/
                ],
                "columnDefs": [{"targets": [16], "orderable": false}],
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



    });
    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function upload_logistics() {
        $("#servicetype").val(null).trigger("change");
        $('#excelUpload_Modal').modal('show')
    }
    function excel_upload_logistics() {
        var formData = new FormData($("#logisticUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Logistics/logistics_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');
                    Otable.draw();
                }
                if(!$.isEmptyObject(data['customercode'])){
                    $('#logistic_upload_body').html('');
                    var x = 1
                    $.each(data['customercode'], function (item, value) {
                        $('#logistic_upload_body').append('<tr><td>'+x+'</td> <td>' + value + '</td></tr>')
                        x++;
                    });

                    $("#logistic_upload_modal").modal({backdrop: "static"});
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'Error in Upload');
            }
        });
    }
    function update_relasedate(detailid) {
        $('#uploadId').val(detailid);
        $('#upload_releasedate').modal({backdrop: "static"});
    }

    function update_reldate() {
        var data = $('#upload_releasedate_frm').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Logistics/logisticupdate_reldate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's')
                {
                    Otable.draw();
                    $('#upload_releasedate').modal('hide');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', 'error');
                stopLoad();

            }
        });

    }
    function generatecustomerinvoce(detailid)
    {
        swal({
                title: "Are you sure?",
                text: "You want to Generate invoice!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {uploaddetailid: detailid},
                    url: "<?php echo site_url('Logistics/create_customerinvoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            Otable.draw();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }



















</script>