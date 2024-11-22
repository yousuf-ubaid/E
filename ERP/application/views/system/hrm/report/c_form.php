<style>
    tfoot td{
        background-color: #dedede;
    }
</style>

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_form');
echo head_page('C'. $title  , false);

$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

$isEPF_Configured = isReportMasterConfigured('EPF');
$isEPF_Employee_Configured = isReportEmployeeConfigured(1);
if( $isEPF_Configured == 'Y' && $isEPF_Employee_Configured == 'Y') {
    $segment_arr = fetch_segment(true,false);

?>

<form role="form" id="reportCreate_form" class="form-horizontal">
    <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="payrollMonth"><?php echo $this->lang->line('hrms_reports_payroll_month');?><!--Payroll Month--> </label>
                    <div class="col-sm-2">
                        <?php echo form_dropdown('payrollMonth', payrollMonth_dropDown(), '', 'class="form-control select2" id="payrollMonth" required'); ?>
                    </div>
                    <label class="col-sm-2 control-label" for="segmentID">Segment</label>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple"'); ?>
                        <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                    </div>
                    <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-primary btn-sm generateBtn" onclick="isGenerateORPrint('Generate')"><?php echo $this->lang->line('common_generate');?><!--Generate--></button>
                        <button type="button" class="btn btn-primary btn-sm generateBtn" onclick="isGenerateORPrint('Print')"><?php echo $this->lang->line('common_print');?><!--Print--></button>
                        <input type="hidden"  id="eventType" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<div id="epfReport" class="col-sm-12" style="display: none"></div>


<?php
}
else{
    ?>
    <div class="alert alert-warning">
        <strong><?php echo $this->lang->line('hrms_reports_warning');?><!--Warning-->!</strong>
        </br>EPF <?php echo $this->lang->line('hrms_reports_payroll_employee_report_report_configuration_is_not_done');?><!--report configuration is not done-->.
        </br><?php echo $this->lang->line('hrms_reports_Please_complete_the_report_configuration_and_try_again');?><!--Please complete the report configuration and try again-->.
    </div>

    <?php
}
?>


<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    var reportCreateForm = $('#reportCreate_form');

    $(document).ready(function (e) {
        $('.select2').select2();

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/c_form', '', 'C Form');
        });

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        reportCreateForm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                payrollMonth: {validators: {notEmpty: {message: 'Payroll month is required.'}}}
            },
        }).
        on('success.form.bv', function (e) {
            $('.generateBtn').prop('disabled', false);
            e.preventDefault();

            var eventType = $('#eventType').val();

            if(eventType == 'Print'){
                PrintData();
            }
            else{
                generateData();
            }

        });

    });

    function isGenerateORPrint(eventType){
        $('#eventType').val(eventType);
        reportCreateForm.submit();
    }

    function generateData(){
        var postData = reportCreateForm.serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Report/cFrom_reportGenerate') ?>/view/',
            data : postData,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#epfReport').html(data).css('display' , 'block');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function PrintData(){
        var form = document.getElementById('reportCreate_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = reportCreateForm.serializeArray();
        form.action = '<?php echo site_url('Report/cFrom_reportGenerate/print/C-Form'); ?>';
        form.submit();
    }
</script>

<?php
