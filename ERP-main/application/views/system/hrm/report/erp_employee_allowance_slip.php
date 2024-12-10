<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_allowance_slip');
echo head_page($title  , false);



?>
<style>
    .bgc{
        background-color: #e1f1e1;
    }

    #div_employee .btn-group, #div_employee .btn-group .multiselect2{
        width: 100% !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' id="frm_allowanceSlip" role="form"'); ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="inputCodforn" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_month');?><!--Month--></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <?php echo form_dropdown('payrollMonth', payrollMonth_dropDown_with_visible_date('Y'), '', 'onchange="loadEmployees()" class="form-control select2"
                        id="payrollMonth" required'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_segment');?><!--Segment-->:</label>
                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6">
                    <?php echo form_dropdown('segmentID', fetch_segment(), '', ' onchange="loadEmployees()" class="form-control select2" id="segmentID" required'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label">
                    <?php echo $this->lang->line('hrms_reports_employee');?><!--Employee-->
                </label>
                <div class="col-lg-4 col-md-2 col-sm-4 col-xs-6" id="div_employee">
                    <select name="empID[]" id="empID" class="form-control" multiple="multiple"  required></select>
                </div>

                <label for="inputData" class="col-md-1 control-label"></label>
                <div class="col-lg-12">
                    <input type="hidden" name="isNonPayroll" id="isNonPayroll" value="Y">
                    <button type="button" onclick="get_nonPaySlips()" class="btn btn-xs btn-primary pull-right">
                        <?php echo $this->lang->line('common_submit');?><!--Submit-->
                    </button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<br>
<div id="div_nonPay_slips"></div>


<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    $(document).ready(function (e) {
        $('.select2').select2();

        $('#empID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#empID").multiselect2('selectAll', false);
        $("#empID").multiselect2('updateButtonText');

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/erp_employee_allowance_slip', '', 'Pay Slip');
        });
    });

    function loadEmployees(){
        var segmentID  = $('#segmentID').val();
        var payrollMonth  = $('#payrollMonth').val();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/dropdown_payslipemployees') ?>",
            data: {segmentID:segmentID, payrollMonth:payrollMonth, isNonPayroll:'Y'},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $('#empID').multiselect('refresh');
                    $("#div_employee").html( data[1] );
                    $('#empID').multiselect2({
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    $("#empID").multiselect2('selectAll', false);
                    $("#empID").multiselect2('updateButtonText');

                    $("#div_nonPay_slips").html('');
                }
                else{
                    $('#empID').multiselect('refresh');
                    $('#empID').multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#empID").multiselect2('updateButtonText');

                    $("#div_nonPay_slips").html(data[1]);
                }


            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_nonPaySlips() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/get_paySlip_report') ?>",
            data: $("#frm_allowanceSlip").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_nonPay_slips").html(data);
                $("#pay-slip-report").tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 4,
                    right: 0,
                    'z-index': 0
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    /*call report content pdf*/
    function generateReportPdf() {
        var monthSegment = $('#payrollMonth :selected').text();
        var form= document.getElementById('frm_allowanceSlip');
        form.target='_blank';
        form.action='<?php echo site_url('Template_paysheet/pay_slip_selected_employee/Allowance_Slip'); ?>/'+monthSegment;
        form.submit();
    }

</script>


<?php
