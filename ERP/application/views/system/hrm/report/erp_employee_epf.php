<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_report');
echo head_page('EPF'. $title  , false);

$isEPF_Configured = isReportMasterConfigured('EPF');
$isEPF_Employee_Configured = isReportEmployeeConfigured(1);

if( $isEPF_Configured == 'Y' && $isEPF_Employee_Configured == 'Y') {
?>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                    <?php echo $this->lang->line('common_confirmed').'/'.$this->lang->line('common_approved');?><!--Confirmed / Approved-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_confirmed').'/'.$this->lang->line('common_not_approved');?><!--Not Confirmed / Not Approved-->
                </td>
                <td>
                    <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                    <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-2 pull-right">
        <button type="button" onclick="open_additionModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_reports_create_new');?><!--Create New--> </button>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <table id="epfReport_tb" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 20%"><?php echo $this->lang->line('common_code')?><!--Code--></th>
                    <th style="width: 10%"><?php echo $this->lang->line('hrms_reports_submissionid')?><!--Submission ID--></th>
                    <th style="width: 20%"><?php echo $this->lang->line('common_month')?><!--Month--></th>
                    <th style="width: 20%"><?php echo $this->lang->line('common_comment')?><!--Comment--></th>
                    <th style="width: 10%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php
}
else{
    $errorMsg = '';
    if($isEPF_Employee_Configured == 'N'){
        $errorMsg .= 'EPF employee report configuration is not done.</br>';
    }
    if($isEPF_Configured == 'N'){
        $errorMsg .= 'EPF report configuration is not done.</br>';
    }

    ?>
    <div class="alert alert-warning">
        <strong>Warning!</strong></br>
        <?php echo $errorMsg; ?>
        Please complete the report configuration and try again.
    </div>
<?php
}
?>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="addMaster_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_reports_epf_newepfreport')?><!--New EPF Report--></h3>
            </div>
            <form role="form" id="reportCreate_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="payrollMonth"><?php echo $this->lang->line('hrms_reports_payroll_month')?><!--Payroll Month--></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('payrollMonth', payrollMonth_dropDown(), '', 'class="form-control select2" id="payrollMonth" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="submissionID"><?php echo $this->lang->line('hrms_reports_submissionid')?><!--Submission ID--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="submissionID" name="submissionID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="comments"><?php echo $this->lang->line('common_comments')?><!--Comments--></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="comments" name="comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm" ><?php echo $this->lang->line('common_save')?><!--Save--></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var reportCreateForm = $('#reportCreate_form');

    $(document).ready(function (e) {
        $('.select2').select2();

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/erp_employee_epf', '', 'EPF Reports');
        });

        reportCreateForm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                payrollMonth: {validators: {notEmpty: {message: 'Payroll month is required.'}}},
                submissionID: {validators: {notEmpty: {message: 'Submission ID is required.'}}}
            },
        }).
        on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var postData   = $form.serializeArray();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: postData,
                url: '<?php echo site_url('Report/save_epfReportMaster'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if( data[0] == 's'){
                        $("#addMaster_model").modal('hide');

                        setTimeout(function(){
                            generate_newReport(data[2]);
                        },300);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        });

        load_epfReports();
    });

    function generate_newReport(id){
        fetchPage('system/hrm/report/erp_employee_epf_report_generate', id, 'EPF Reports');
    }

    function open_additionModel() {
        reportCreateForm[0].reset();
        reportCreateForm.bootstrapValidator('resetForm', true);

        $("#addMaster_model").modal({backdrop: "static"});
    }

    function load_epfReports(){
        /*var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);*/
        $('#epfReport_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Report/fetch_epfReport'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "documentCode"},
                {"mData": "submissionID"},
                {"mData": "payMonth"},
                {"mData": "comment"},
                {"mData": "action"}
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

    function delete_epfReport(deleteID){
        swal({
                title: "Are you sure ?",
                text: "You want to delete this report ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'deleteID' : deleteID},
                    url: "<?php echo site_url('Report/delete_epfReport'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            load_epfReports();
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
    }

    function get_detFile(id) {
        window.open("<?php echo site_url('Report/epf_reportGenerate') ?>" + '/' + id, '_blank');
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<?php
