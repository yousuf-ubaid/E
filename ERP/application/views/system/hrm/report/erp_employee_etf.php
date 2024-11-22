<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_report');
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

echo head_page('ETF'. $title  , false);
$isETF_Configured = isReportMasterConfigured('ETF');
$isETF_Head_Configured = isReportMasterConfigured('ETF-H');
$isETF_Employee_Configured = isReportEmployeeConfigured(2);
$segment_arr = fetch_segment(true,false);


if( $isETF_Configured == 'Y' && $isETF_Head_Configured == 'Y' && $isETF_Employee_Configured == 'Y') {
    ?>

    <form role="form" id="reportCreate_form" class="form-horizontal" xmlns="http://www.w3.org/1999/html">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="payrollMonth"><?php echo $this->lang->line('hrms_reports_payroll_month');?>
                            <!--Payroll Month--></label>
                        <div class="col-sm-2">
                            <?php echo form_dropdown('payrollMonth', payrollMonth_dropDown(), '', 'class="form-control select2" id="payrollMonth" required'); ?>
                            <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                        </div>
                        <label class="col-sm-2 control-label" for="segmentID">Segment</label>
                        <div class="col-sm-3">
                            <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple"'); ?>
                            <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary btn-sm generateBtn" onclick="isGenerateORDownload('View')">
                                <?php echo $this->lang->line('common_view');?><!--View--></button>
                            <button type="button" class="btn btn-primary btn-sm generateBtn" onclick="isGenerateORDownload('Generate')">
                                <?php echo $this->lang->line('hrms_reports_payroll_generate');?><!--Generate--></button>
                            <button type="button" class="btn btn-primary btn-sm generateBtn" onclick="isGenerateORDownload('Download')">
                                 Download File</button>
                            <input type="hidden"  id="eventType" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <pre id="epfReport" class="col-sm-12 epfReport-containers" style="display: none"></pre>
    <div id="epfReportView" class="col-sm-12 epfReport-containers" style="display: none; height: 550px"></div>


<?php
}
else{
    $errorMsg = '';
    if($isETF_Employee_Configured == 'N'){
        $tran = $this->lang->line('hrms_reports_payroll_employee_report_configuration_is_not_done');
        $errorMsg .= 'ETF '.$tran.'.</br>'; /*<!--employee report configuration is not done-->*/
    }
    if($isETF_Head_Configured== 'N'){
        $tran = $this->lang->line('hrms_reports_payroll_employee_report_header_configuration_is_not_done');
        $errorMsg .= 'ETF '.$tran.' .</br>';/*<!--header report configuration is not done-->*/
    }
    if($isETF_Configured  == 'N'){
        $tran = $this->lang->line('hrms_reports_payroll_employee_report_report_configuration_is_not_done');
        $errorMsg .= 'ETF '.$tran.'.</br>';/*<!--report configuration is not done-->*/
    }
    
?>
    <div class="alert alert-warning">
        <strong><?php echo $this->lang->line('hrms_reports_warning');?><!--Warning-->!</strong></br>

        <?php echo $errorMsg; ?>
        <?php echo $this->lang->line('hrms_reports_Please_complete_the_report_configuration_and_try_again.');?><!--Please complete the report configuration and try again.-->
    </div>

<?php
}
?>


<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    var reportCreateForm = $('#reportCreate_form');

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/erp_employee_etf', '', 'EPF Reports');
        });

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.select2').select2();

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

            if(eventType == 'Download'){
                downloadData();
            }
            else{
                generateData(eventType);
            }

        });

    });

    function isGenerateORDownload(eventType){
        $('#eventType').val(eventType);
        reportCreateForm.submit();
    }

    function generateData(eventType){
        let postData = reportCreateForm.serializeArray();
        postData.push({'name': 'req_type', 'value': eventType});
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Report/etf_reportGenerate') ?>',
            data : postData,
            beforeSend: function () {
                $('.epfReport-containers').html('').hide();
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(eventType == 'View'){
                    $('#epfReportView').html(data).css('display' , 'block');
                    $('#rpt_table').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 10
                    });
                }
                else{
                    $('#epfReport').html(data).css('display' , 'block');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function downloadData(){
        var form = document.getElementById('reportCreate_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = reportCreateForm.serializeArray();
        form.action = '<?php echo site_url('Report/etf_reportGenerate'); ?>';
        form.submit();
    }
</script>


<?php
