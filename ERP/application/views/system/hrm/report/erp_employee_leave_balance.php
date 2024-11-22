<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_employee_leave_balance_report');
echo head_page($title, false);

$current_date = current_format_date();
$date_format_policy = date_format_policy();

$policyDrop = [
    1=>'Annually/Monthly',
    2=>'Hourly'
];

?>
<style>
    .multiselect-container {
        width: 200px;
    }

    .multiselect-container > li > a {
        white-space: normal;
    }
    .control-label{
        margin-top: -2px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
</div>

<div class="jumbotron" style="padding: 10px;background-color: rgba(238, 238, 238, 0.31)">
    <?php echo form_open('login/loginSubmit', ' class="form-horizontal" id="formleave" role="form"'); ?>
        <div class="row">
            <div class="col-md-12 ">
                <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf">
                <input type="hidden" id="captionChkpdf" name="captionChkpdf">
                <label for="inputData" class="col-md-1 control-label" style="width: 70px;    text-align: left;"><?php echo $this->lang->line('hrms_reports_as_of');?><!--As of-->
                </label>
                <div class="col-md-2" style="width: 140px">
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" class="form-control" id="empDob" name="asOfDate"  value="<?php echo $current_date; ?>"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                    </div>
                </div>
                <label for="inputCodforn" class="col-md-2 control-label" style=" width: 105px;   text-align: left;"><?php echo $this->lang->line('hrms_reports_employee_policy_type');?><!--Policy Type--></label>
                <div class="col-md-2" style="width: 130px">
                    <?php
                        echo form_dropdown('policyType', $policyDrop, '', 'onchange="get_policyType()" id="policyType" class="form-control select2"');
                    ?>
                </div>
                <label for="inputCodforn" class="col-md-1 control-label" style="width: 100px;    text-align: left;"><?php echo $this->lang->line('hrms_reports_employee_leave_type');?><!--Leave Type--></label>
                <div class="col-md-2">
                    <?php // echo form_dropdown('leaveType', leavemaster_dropdown(false,true), '', 'id="leaveType" class="form-control select2"'); ?>
                    <?php echo form_dropdown('leaveType[]', leavemaster_dropdown(false,true), '', 'id="leaveType" class="form-control" multiple="multiple"'); ?>

                </div>

                <label for="inputData" class="col-md-1 control-label"
                       style="width: 80px;    text-align: left;"><?php echo $this->lang->line('hrms_reports_employee');?><!--Employee--></label>
                <div class="col-md-2 " id="empDropdown">


                    <?php
                    $customer = all_employee_drop(False);
                    if (isset($customer)) {
                        foreach ($customer as $row) {

                            $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                        }
                    }
                   // echo form_dropdown('empID[]', $customer_arr, '', 'id="empID" multiple="multiple" class="form-control mid-width wrapItems "'); ?>
                </div>

            </div>
         <!--   <label for="inputData" class="col-md-1 control-label"
                   style="width: 90px;    text-align: left;">Group by</label>
            <div class="col-md-2" style="width: 130px">
                <?php /*echo form_dropdown('groupType', array('2' => 'Employee', '1' => 'Leave Type'), '', 'id="groupType" class="form-control select2"'); */?>
            </div>-->

            <input type="hidden" id="groupType" name="groupType" value="2">

            <div class="col-md-12" style="margin-top: 10px">


                <button id="btn_search" type="button" class="btn btn-primary btn-sm"><?php echo $this->lang->line('hrms_reports_payroll_generate');?><!--Generate Report--></button>
                <button id="btn_clear" type="button" class="btn btn-default btn-sm"><?php echo $this->lang->line('common_clear');?><!--Clear--></button>


            </div>
        </div>
    <?php echo form_close(); ?>


</div>
<hr>


        <div id="loadthis"></div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    
    $('#leaveType').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: false,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        maxHeight: 200,
        numberDisplayed: 1
    });


    $('.skin-square input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('#empID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        maxHeight: 200,
        numberDisplayed: 1
    });

    $('#empID2').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        allSelectedText: 'All Selected',
        buttonWidth: '120px',
        maxHeight: '300px'

    });

    $("#empID").multiselect2('selectAll', false);
    $("#empID").multiselect2('updateButtonText');

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/erp_employee_leave_balance', '', 'Employee Leave Balance');
    });
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',

    });

    $(".select2").select2();
    get_policyType();

    //  loadItemAnalysis($("#groupType").val(), $('#leaveType').val(), $('#empID').val(), $('#asOfDate').val());

    $("#btn_search").click(function () {
        var groupType = $("#groupType").val();
        var leaveType = $('#leaveType').val();
        var empID = $('#empID').val();
        var asOfDate = $('#asOfDate').val();

        loadItemAnalysis(groupType, leaveType, empID, asOfDate);
    });

    $("#btn_clear").click(function () {

        var groupType = $('#groupType').val(2);
        var leaveType = $('#leaveType').val('').change();
        $("#empID").multiselect2('selectAll', false);
        $("#empID").multiselect2('updateButtonText');
        $("#asOfDate").val('<?php echo $current_date ?>');

        $('#loadthis').html('');
    });

    function get_policyType(){
            policyType=$('#policyType').val();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employeesByLeavepolicy'); ?>',
            data: {policyType: $('#policyType').val()},
            dataType: 'html',
            beforeSend: function () {
startLoad();
            },
            success: function (data) {

                $('#empDropdown').html(data);
                $('#empID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#empID").multiselect2('selectAll', false);
                $("#empID").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function () {

                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function loadLeaveTypeDropDown(leavegroupID) {
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/leaveTypebyleaveGroup'); ?>',
            data: {'leavegroupID': leavegroupID},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#leaveTypeDropDown').html(data);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }


    /*loadtablesearch();*/
    function loadItemAnalysis(groupType, leaveType, empID, asOfDate) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_leave_balance_report') ?>",
            data: $('#formleave').serializeArray(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#loadthis').html('');
                $('#loadthis').show();
            },
            success: function (data) {
                stopLoad();
                if(data!=''){
                    $('#loadthis').html(data);
                }
                refreshNotifications();

                //  getMonthlyAdditionDetailList(id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#loadthis').html('<div class="alert alert-danger">' + textStatus + '<br/>' + errorThrown + '</div>');
            }
        });
        return false;
    }

    function loadItemAnalysisPagination(item) {

        var empID = $("#empID").val();
        var leaveType = $('#leaveTypeID').val();

        var current = $(item).attr('data-index');


        loadItemAnalysis(empID, leaveType, current);
    }


</script>
