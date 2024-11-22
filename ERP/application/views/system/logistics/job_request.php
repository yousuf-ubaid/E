<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = "Job Request";
echo head_page($title, true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
//$chequeRegister = getPolicyValues('CRE', 'All');
$pID = $this->input->post('page_id');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5"></div>
    <div class="col-md-4 text-center">&nbsp;  </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/logistics/erp_job_request',null,'Add New Job Request');">
            <i class="fa fa-plus"> </i>Add New Job Request
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="job_request_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 5%">Document Code</th>
            <th style="width: 30%">Customer Name</th>
            <th style="width: 20%">House BL No</th>
            <th style="width: 8%;"> Ref No</th>
            <th style="width: 5%">Container No</th>
            <th style="width: 5%">Shipping Line</th>
            <th style="width: 5%">Type of Service</th>
            <th style="width: 5%">Arrival Date</th>
            <th style="width: 5%">Booking No</th>
            <th style="width: 10%">Bayan System Status</th>
            <th style="width: 5%">Encode By</th>
            <th style="width: 2%">Reminder(Days)</th>
            <th style="width: 5%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="view_jobrequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Job Request</h4>
            </div>
            <div class="modal-body" id="job_request_details">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>






<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var jobID;
    var Otable;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/logistics/job_request', '', 'Job Request');
        });
        jobID = null;
        number_validation();
        job_request_table();
    });

    function job_request_table(selectedID=null) {
         Otable = $('#job_request_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Logistics/fetch_job_request'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['jobID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "jobID"},
                {"mData": "Documentcode"},
                {"mData": "CustomerName"},
                {"mData": "internalRefNo"},
                {"mData": "BLLogisticRefNo"},
                {"mData": "containerNo"},
                {"mData": "shippingLine"},
                {"mData": "serviceType"},
                {"mData": "arrivalDate"},
                {"mData": "bookingNumber"},
                {"mData": "bayanSystemStatus"},
                {"mData": "encodeBy"},
                {"mData": "reminderInDays"},

                {"mData": "edit"}
            ],
             "columnDefs": [{"targets": [12], "orderable": false},{"visible":true,"searchable": false,"targets": [0,12] },{"visible":true,"searchable": true,"targets": [2,10] }],
             "fnServerData": function (sSource, aoData, fnCallback) {
               // aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                //aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                //aoData.push({"name": "status", "value": $("#status").val()});
               // aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    function delete_job_request(id, value) {
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
                    data: {'jobID': id},
                    url: "<?php echo site_url('Logistics/delete_job_request'); ?>",
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

    function load_job_request_view(jobID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'jobID': jobID},
            url: '<?php echo site_url("Logistics/load_job_request_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#view_jobrequest').modal({backdrop: "static"});
               // $('#Documentcode').val( $.trim(jobID) );

                 $('#job_request_details').html(data);
                fetch_logistic_attachments_2(jobID);

                stopLoad();

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
        //$('#job_request_details').html('data');
    }
    function logistic_document_uplode_2() {
        var formData = new FormData($("#logistic_attachment_form_2")[0]);
         var jobID = $('#jobID').val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Logistics/attachement_upload'); ?>",
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
                    //alert(jobID);
                    fetch_logistic_attachments_2(jobID)
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
    }
    function fetch_logistic_attachments_2(jobID) {
        if (jobID) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Logistics/fetch_logistic_attachments_2"); ?>',
                dataType: 'json',
                data: {'jobID': jobID},
                success: function (data) {
                    $('#job_request_attachment_2').empty().append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }
    function delete_job_attachments_2(id,fileName)
    {
        jobID = $('#jobID').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('Logistics/delete_attachments_AWS_s3_logistics'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                            alert(jobID);
                            fetch_logistic_attachments_2(jobID)
                        } else {
                            myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                            /*Deletion Failed*/
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

