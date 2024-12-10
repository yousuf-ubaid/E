

<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_paysheets');
echo head_page($title, false);



$paySheetTemplate_arr = paySheetTemplate_drop('N');
$postData = $this->input->post('data_arr');
$default_template = getDefault_template('N');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$visibleDate = convert_date_format(date('Y-01-t'));
$segment_arr = fetch_segment(true, false);
$currency_arr = all_currency_new_drop(false); //all_currency_drop(false, 'ID');>
$pGroups_drop[] = 'Select Group';
$pGroups_drop2 = payroll_group_drop();
$pGroups_drop = array_merge($pGroups_drop, $pGroups_drop2);



if ($postData != null) {
    $payrollCalender_arr = payrollCalender(date('Y'), 1);

} else {
    $payrollCalender_arr = payrollCalender(date('Y'), 0);
}

$companyName = $this->common_data['company_data']['company_name'];
?>
    <style type="text/css">
        /*.templateId{ width: 99%; }*/
        #payYear {
            width: 50px;
            padding: 0px;
            border: none;
            height: 27px;
        }

        #processingDate {
            z-index: 100 !important;
        }

        #curWiseSum {
            float: left !important;
        }

        .boldFont {
            font-weight: bold
        }

        .payTemplateDiv {
            display: none
        }

        .pull-right-btn{
            float: right;
            margin-right: 3px;
        }

        .fixHeader_Div {
            height: 450px;
            border: 1px solid #c0c0c0;
        }

        .t-foot{ background: #c0c0c0 }


        @media only screen and (max-width: 768px) {
            /* For mobile phones: */
            #date-container {
                padding-right: 15px !important;
            }
        }

        .select-container .btn-group{
            width: 150px !important;
        }
    </style>

    <div class="m-b-md" id="wizardControl">
        <button class="btn btn-wizard btn-primary showBtnDiv" href="#step1" id="paySheetHeader"
                onclick="tabShow(this.id)" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 1 - <?php echo $this->lang->line('hrms_payroll_paysheet_header');?><!--Paysheet Header-->
        </button>
        <button class="btn btn-wizard btn-default hideBtnDiv" href="#step2" id="bankTransfer" onclick="tabShow(this.id)"
                data-value="0" data-toggle="tab" disabled>
            <?php echo $this->lang->line('common_step');?><!--Step--> 2 - <?php echo $this->lang->line('hrms_payroll_bank_transfer');?><!--Bank Transfer-->
        </button>
        <button class="btn btn-wizard btn-default hideBtnDiv" href="#step2" id="emp_withoutBank"
                onclick="tabShow(this.id)" data-value="0" data-toggle="tab" disabled>
            <?php echo $this->lang->line('common_step');?><!--Step--> 3 - <?php echo $this->lang->line('hrms_payroll_employees_without_bank_details');?><!--Employees without Bank Details-->
        </button>
        <div class="clearfix visible-xs">&nbsp;</div>
        <div class="pull-right">
            <label><span id="payrollHeaderDet" style="display: none;"> </span> </label>
            <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="payrollAccountReview" target="_blank" href="" style="display: none;">
            <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;<?php echo $this->lang->line('hrms_payroll_account_review_entries');?><!--Account Review Entries--> </a>
            </span>
        </div>
        <div class="clearfix visible-xs">&nbsp;</div>
    </div>

    <input type="hidden" name="isBankTransferProcessed" id="isBankTransferProcessed" value="0">

    <div class="m-b-lg  ">
        <?php echo form_open('', ' id="paySheetForm" role="form" autocomplete="off"'); ?>
            <div class="row" style="/*margin: 2%; margin-bottom: 1%*/ margin-top: 1%;">
                <div class="col-sm-12" style="padding:0px">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="p_group" class=""> <?=$this->lang->line('common_payroll_group');?></label>
                            <?=form_dropdown('p_group', $pGroups_drop, null, 'class="form-control" id="p_group" onchange="load_periods_drops()"')?>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="p_group" class=""> <?=$this->lang->line('common_period');?></label>
                            <span id="period-drop-container">
                                <?=form_dropdown('period_id', [], null, 'class="form-control" id="period_drop" onchange=" "')?>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="processingDate" class=""><?php echo $this->lang->line('hrms_payroll_processing_date');?><!--Processing Date--></label>
                            <div class="input-group filterDate" style=";">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="processingDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                  value="<?php echo $current_date; ?>" id="processingDate" class="form-control formInput" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="visibleDate" class=""><?php echo $this->lang->line('hrms_payroll_visible_date');?><!--Processing Date--></label>
                            <div class="input-group filterDate" style=";">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="visibleDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $visibleDate; ?>" id="visibleDate" class="form-control formInput" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="payNarration" class=""> <?php echo $this->lang->line('hrms_payroll_narration');?><!--Narration--> </label>
                            <input type="text" name="payNarration" id="payNarration" class="form-control formInput escapeDoubleQuotes" />
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="templateId" class=""> <?php echo $this->lang->line('hrms_payroll_paysheet_template');?><!--Paysheet Template--></label>
                            <?php echo form_dropdown('templateId', $paySheetTemplate_arr, $default_template, 'class="form-control templateId" id="templateId" required'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="btnDiv" style="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group" id="segment-container">
                                <label for="segmentID" class=""><?php echo $this->lang->line('common_segment');?><!--Segment--></label>
                                <?php echo form_dropdown('segmentID[]', $segment_arr, '', ' class="form-control" multiple="multiple" id="segmentID" '); ?>
                                <!--onchange="load_template()"-->
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <div class="input-group" id="hide-zero-column">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="hideZeroColumn" id="hideZeroColumn" value="Y">
                                </span>
                                <input type="text" class="form-control" disabled="" value="<?php echo $this->lang->line('hrms_payroll_hide_zero_column');?>"><!--Hide zero column-->
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="clearfix">&nbsp;</div>
                            <input type="hidden" name="hidden_payrollID" id="hidden_payrollID" value="">
                            <input type="hidden" name="isConfirm" id="isConfirm" value="0">
                            <input type="hidden" name="isNonPayroll" id="isNonPayroll" value="N">

                            <?php echo export_buttons('paysheet-tb', 'paysheet', true, false, 'btn-sm hideBtn'); ?>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm hideBtn pull-right-btn" id="printBtn" onclick="print_btn()"
                                    style=""> <?php echo $this->lang->line('common_print');?><!--Print-->
                            </button>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm hideBtn pull-right-btn pay-save-btn" onclick="confirm_payroll(1)"
                                    style=""> <?php echo $this->lang->line('common_save_and_confirm');?><!--Save & Confirm-->
                            </button>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm hideBtn pull-right-btn pay-save-btn" onclick="confirm_payroll(0)"
                                    style=""> <?php echo $this->lang->line('common_save_change');?><!--Save Changes-->
                            </button>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm pull-right-btn" id="loadBtn" onclick="load_btn()" style="">
                                <?php echo $this->lang->line('common_load');?><!--Load-->
                            </button>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm pull-right-btn" id="empPullBtn" onclick="openEmployeeModal()" style="">
                                <?php echo $this->lang->line('hrms_payroll_add_employee');?><!--Add Employee-->
                            </button>
                            <div class="clearfix visible-xs">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>

    <div class="stepBody" id="paySheetHeader_Tab">
        <div class="col-sm-12 table-responsive payTemplateDiv">
            <hr>
            <div class="col-md-4 boldFont">  <?php echo $this->lang->line('common_confirmed_by');?><!--Confirm By--> : &nbsp; <span class="" id="confirmByName"></span></div>
            <div class="col-md-4">&nbsp;</div>
            <div class="col-md-4 pull-right boldFont"> <?php echo $this->lang->line('common_approved_by');?><!-- Approved By--> : &nbsp; <span class="pull-right" id="approvedByName"
                                                                                  style="margin-right: 10%"></span>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 table-responsive payTemplateDiv" id="payTemplateDivTable"></div>
        </div>

    </div>

    <div class="stepBody" id="bankTransfer_Tab" style="display: none">
        <input type="hidden" name="isBankTransferProcessed" id="isBankTransferProcessed" value="0">
        <div id="loadBankTransferData" style="padding-top: 2% ">

        </div>
    </div>

    <div class="stepBody" id="emp_withoutBank_Tab" style="display: none">
        <div id="loadEmp_withoutBankData" style="padding-top: 2% ">

        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="/*z-index: 999999*/"  >
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employees--></h3>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="segment"> <?php echo $this->lang->line('common_segment');?><!--Segment--> </label>
                                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment"  multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="currency">  <?php echo $this->lang->line('common_currency');?><!--Currency--> </label>
                                <?php echo form_dropdown('currency[]', $currency_arr, '', 'class="form-control" id="currency" multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-4 col-xs-4 pull-right">
                                <label for="currency" class="visible-sm visible-xs">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="selectAllRows()" ><?php echo $this->lang->line('hrms_payroll_select_all');?><!--Select All--></button>
                                <button type="button" onclick="load_employeeForModal()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px"><?php echo $this->lang->line('common_load');?><!--Load--></button>
                            </div>
                        </div>

                        <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">

                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 5%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                                        <th style="width:auto"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                        <th style="width:auto"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
                                        <th style="width:8%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                                        <th style="width:8%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                                        <th style="width: 5%"><div id="dataTableBtn"></div></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <!--<div class="row">
                            <div class="col-md-12">
                                <div class="pull-right">
                                    <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;" onclick="clearAllRows()"> Clear All </button>
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">-->

                        <div class="row">
                            <div class="table-responsive col-md-12" >
                                <form id="tempTB_form">
                                    <!--<input type="hidden" name="masterID" value="<?php /*echo $epfMasterID; */?>"/>-->
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP CODE--></th>
                                            <th style="max-width: 90%"><?php echo $this->lang->line('hrms_payroll_emp_name');?><!--EMP NAME--></th>
                                            <th style="width: 50px">
                                                <button type="button" class="btn btn-default btn-xs" id="clearAllBtn"
                                                        style="font-size:12px;" onclick="clearAllRows()">
                                                    <?php echo $this->lang->line('common_clear_all');?><!--Clear All-->
                                                </button>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" style="font-size:12px;" onclick="load_employeeToPayroll()"><?php echo $this->lang->line('common_load');?><!--Load--></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expense_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999"  >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_payroll_pending_claim_no_pay');?><!--Pending Claims / No-pay--></h3>
            </div>
            <div class="modal-body">
                <h3 style="margin-top: -10px; font-size: 16px; font-weight: bold;" id="pending-claims-title">
                    <?php echo $this->lang->line('hrms_payroll_pending_claim');?><!--Pending Claims-->
                </h3>
                <table class="<?php echo table_class();?>" id="expenseClaimData">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code');?><!--Code--></th>
                            <th><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                            <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                            <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                            <th>
                                <input type="checkbox" onclick="changeExpStatus(this, '.expCls')" id="allCheckBox"/>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>

                <h3 style="font-size: 16px; font-weight: bold;" id="pending-no-pay-title">
                    <?php echo $this->lang->line('hrms_payroll_pending_no_pay');?><!--Pending No-pay-->
                </h3>
                <table class="<?php echo table_class();?>" id="noPayData">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                        <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                        <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                        <th>
                            <input type="checkbox" onclick="changeExpStatus(this, '.noPayCls')" id="allCheckBox1"/>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" style="font-size:12px;" onclick="load_employeeToPayroll('withExpenseClaim')">
                    <?php echo $this->lang->line('common_load');?><!--Load-->
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">
                    <?php echo $this->lang->line('common_Close');?><!--Close-->
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var empTempory_arr = [];
    var visibleDate = null;
    var segmentDrop = $('#segmentID');

    segmentDrop.multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 1
    });

    segmentDrop.multiselect2('updateButtonText');

    $('#segment-container .btn-group').css('width', '100%'); /*Set the multi select width to 100% */
    $('#segment-container .btn-group .multiselect2').css('width', '100%');

    var payrollAccountReview = $('#payrollAccountReview');
    var payYear = $('#payYear');
    var payrollDet = null;
    var autoID = null;
    var period_drop = $('#period_drop');
    period_drop.select2();

    payrollDet = <?php echo json_encode($this->input->post('data_arr')) ?>;


    if (payrollDet != null) {
        autoID = $.trim(payrollDet[1]);
        getPayrollDet(payrollDet);
    }
    else {
        $('.hideBtn, #loadBtn, #hide-zero-column').hide(); //#segment-container
        payYear.css('background', '#fff');
    }

    var payTemplateDivTable = $('#payTemplateDivTable');


    $('.headerclose').click(function () {
        fetchPage('system/hrm/all_pay_sheets_period_base', autoID, 'HRMS');
    });

    $('.modal').on('hidden.bs.modal', function (e) {
        if ($('.modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });


    function load_periods_drops() {
        let p_group = $('#p_group').val();
        period_drop.empty();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'groupID': p_group},
            url: "<?php echo site_url('Template_paysheet/load_periods_drops'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 'e') {
                    myAlert('e', data[1]);
                }
                else{
                    period_drop.append($('<option></option>').attr('value', '').text( 'select period' ));

                    $.each(data['drop'], function(key, val){
                       period_drop.append($('<option></option>').attr('value', val['id']).text( val['datePr'] ));
                    });
                    //$('#period-drop-container').html(data['drop']);
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function print_btn() {
        var templateId = $('#templateId').val().trim();
        var payrollID = $('#hidden_payrollID').val().trim();

        if (templateId == '') {
            myAlert('e', '<?php echo $this->lang->line('hrms_payroll_please_select_a_template');?>.');/*Please select a template*/
        }

        if (payrollID == '') {
            myAlert('e', 'Please reload the page and try again.');
        }

        if (templateId != '' && payrollID != '') {
            var payrollHeaderDet = $('#payrollHeaderDet').text();

            /*with form  submit we can access segment selection on controller*/
            var form= document.getElementById('paySheetForm');
            form.target='_blank';
            form.method='post';
            form.action='<?php echo site_url('Template_paysheet/paySheetPrint_segmentWise/'); ?>'+payrollID + "/" + templateId + "/N/" + payrollHeaderDet;
            form.submit();
        }

    }

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $(document).ready(function () {

        Inputmask().mask(document.querySelectorAll("input"));

        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

    });

    function getPayrollDet(payrollDet) {
        //var payrollLoadType = payrollDet[0];
        var payrollID = $.trim(payrollDet[1]);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payrollID': payrollID},
            url: "<?php echo site_url('Template_paysheet/getPayrollDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 'e') {
                    myAlert('e', data[1]);
                }
                else {
                    $('#hidden_payrollID').val(payrollID);
                    $('#payYear').val(data['payrollYear']);
                    $('#payMonth').val(data['payrollMonth']);
                    $('#payNarration').val(data['narration']);
                    $('#processingDate').val(data['processDate']);
                    $('#visibleDate').val(data['visibleDate']);
                    if( $.trim(data['templateID']).length != 0){
                        $('#templateId').val(data['templateID']);
                    }
                    $('#confirmByName').text(data['confirmedByName']);
                    $('#approvedByName').text(data['approvedbyEmpName']);
                    var printUrl = '<?php echo site_url('Template_paysheet/payrollAccountReview'); ?>';
                    payrollAccountReview.attr('href', printUrl+'/' + payrollID + '/N/' + data['documentCode'] + '-' + data['payrollYear']);
                    payYear.css('background', '#eeeeee');
                    payrollAccountReview.show();

                    $('.payDate').attr('disabled', 'disabled');
                    $('.hideBtn, #loadBtn').show();
                    $('#empPullBtn').hide();
                    $('#payrollHeaderDet').html(getMonthName(data['payrollMonth']) + " " + data['payrollYear']);
                    var headerData = '"<h3><?php echo $companyName; ?></h3>Payroll Month &nbsp; : &nbsp;'+ data['payrollYear'] + ' ' + getMonthName(data['payrollMonth']);
                    headerData += "<br/>Narration &nbsp; : &nbsp;"+data['narration']+'"';
                    $('#btn-excel').attr('download', data['payrollYear'] + " " + getMonthName(data['payrollMonth'])+".xls")
                        .attr('onclick', 'var file = tableToExcel(\'paysheet-tb\', \'paysheet\', '+headerData+'); $(this).attr(\'href\', file);');

                    if (data['confirmedYN'] == 1) {
                        $('.formInput').attr('disabled', 'disabled');
                        $('.hideBtn').hide();
                    }

                    if( data['approvedYN'] == 1 ){

                        $('.formInput').attr('disabled', 'disabled');
                        $('.bankTransferBtn').show();
                        $('#bankTransfer').removeAttr('disabled');
                        $('#emp_withoutBank').removeAttr('disabled');
                        $('#isBankTransferProcessed').val(data['isBankTransferProcessed']);
                    }


                    if (data['confirmedYN'] == 1 || data['approvedYN'] == 1) {
                        visibleDate = data['visibleDate'];
                        $('#visibleDate').attr('disabled', false);


                        $('.filterDate').datetimepicker({
                             useCurrent: false,
                             format: date_format_policy
                        }).on('dp.change', function (ev) {

                            setTimeout(function(){ // To avoid first call in date picker selection
                                var tempDate = $('#visibleDate').val();
                                if(tempDate !== visibleDate){
                                    update_payslipVisibleDate();
                                    visibleDate = tempDate;
                                }
                            }, 200);

                        });
                    }

                    $('#printBtn').show();
                    $('#btn-excel').show();
                    load_template();
                }

            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function load_template() {
        var templateId = $('#templateId').val();
        var hidden_payrollID = $('#hidden_payrollID').val();
        var segmentID = $('#segmentID').val();
        var hideZeroColumn = ($('#hideZeroColumn').prop('checked'))? 'Y' : 'N';

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'hidden_payrollID': hidden_payrollID, 'templateId': templateId, 'segmentID':segmentID, 'hideZeroColumn':hideZeroColumn},
            url: "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var isError = data.split('|||');
                if (isError[0] == 'e') {
                    myAlert(isError[0], isError[1]);
                } else {
                    payTemplateDivTable.html(data);
                    payTemplateDivTable.show();

                    $('#paysheet-tb').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function create_payroll(withExpenseClaim=null) {
        var postData = $('#paySheetForm').serializeArray();
        postData.push({'name':'selectedEmployees', 'value':empTempory_arr});

        if(withExpenseClaim == 'withExpenseClaim'){
            postData.push({'name':'sendWithExp', 'value':1});

            var selectedExpClam = [];
            $('.expCls:checked').each(function(){
                selectedExpClam.push( $(this).val() );
            });
            postData.push({'name':'selectedExpClam', 'value':selectedExpClam});


            var selectedNoPay = [];
            $('.noPayCls:checked').each(function(){
                selectedNoPay.push( $(this).val() );
            });
            postData.push({'name':'selectedNoPay', 'value':selectedNoPay});

            $('#expense_model').modal('hide');

        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Template_paysheet/loadPaySheetData_period_base'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#employee_model').modal('hide');
                    $('#segment-container, #hide-zero-column').show();
                    autoID = data[2];
                    var payData = [];
                    payData.push('');
                    payData.push(autoID);

                    getPayrollDet(payData);
                }
                else{
                    if('2' in data){
                        if( data[2].trim() == 'isNeed_model_error'){
                            get_bootBoxErrorMsg(data[1]);
                        }
                        else if( data[2].trim() == 'pendingExpenseClaims'){
                            $('#allCheckBox, #allCheckBox1').prop('checked', false);
                            $('#expenseClaimData > tbody').empty();
                            if( data['expenseClaim'] != ''){
                                $('#pending-claims-title').show();
                                $('#expenseClaimData').append(data['expenseClaim']).show();
                            }else{
                                $('#expenseClaimData, #pending-claims-title').hide();
                            }

                            
                            $('#noPayData > tbody').empty();
                            if( data['noPay'] != ''){
                                $('#pending-no-pay-title').show();
                                $('#noPayData').append(data['noPay']).show();
                            }else{
                                $('#noPayData, #pending-no-pay-title').hide();
                            }

                            $('#expense_model').modal('show');
                        }
                        else{
                            myAlert(data[0], data[1]);
                        }
                    }
                    else{
                        myAlert(data[0], data[1]);
                    }
                }

            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function get_bootBoxErrorMsg(msg){
        var errorMsg = '<h3 style="color: #c03920; margin-top: 1px"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Error </h3>';
        errorMsg += '<div style="color: #c03920;">'+msg+'</div>';
        bootbox.alert( errorMsg );
    }

    function confirm_payroll(isConfirm) {
        $('#isConfirm').val(isConfirm);
        if( isConfirm == 1) {
            swal({
                    title: "Are you sure ?",
                    text: "You want to confirm this payroll ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    $('#isConfirm').val(1);
                    update_payroll();
                }
            );
        }
        else{
            update_payroll();
        }
    }

    function update_payslipVisibleDate() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('you_want_to_change_payslip_visible_date');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                var visibleDate = $('#visibleDate').val();
                var payrollID = $('#hidden_payrollID').val();

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'visibleDate':visibleDate, 'payrollID': payrollID, 'isNonPayroll': 'N'},
                    url: "<?php echo site_url('Template_paysheet/update_payslipVisibleDate'); ?>",
                    beforeSend: function () {
                        startLoad();

                    }, success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {

                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function notConfirm_payroll(){
        $('#isConfirm').val(0);
        update_payroll();
    }

    function update_payroll() {
        var postData = $('#paySheetForm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Template_paysheet/update_PaySheet'); ?>",
            beforeSend: function () {
                startLoad();

            }, success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    var payrollID = $('#hidden_payrollID').val();
                    var payrollDetail_arr = ['', payrollID];
                    fetchPage('system/hrm/pay_sheet', 0, 'Load', '', payrollDetail_arr);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function load_btn(withExpenseClaim=null) {
        var templateId = $('#templateId').val();
        var payrollID = $('#hidden_payrollID').val();
        if (templateId != '') {
            var payrollDet = null;
            payrollDet = <?php echo json_encode($this->input->post('data_arr')) ?>;

            if (payrollID != '') {
                load_template();
            }
            else {
                create_payroll(withExpenseClaim);
            }
        }
        else {
            myAlert('e', 'Please select a Pay sheet Template');
        }
    }

    function refresh_btn() {
        var payID = $('#hidden_payrollID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'payrollID': payID},
            url: "<?php echo site_url('Template_paysheet/payroll_refresh'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    var paysheetID = data[2];
                    var paysheetDet = ['', paysheetID];
                    fetchPage('system/hrm/pay_sheet', 0, 'Load', '', paysheetDet);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function bankTransfer() {
        var payID = $('#hidden_payrollID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'payrollID': payID, 'isNonPayroll':'N'},
            url: "<?php echo site_url('Template_paysheet/payroll_bankTransfer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#isBankTransferProcessed').val(1);
                    $('#bankTransfer').click();
                }
                return data[0];
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function tabShow(tabShowBtn) {

        if( tabShowBtn == 'paySheetHeader'){ $('#segment-container').show(); }
        else{ $('#segment-container').hide(); }

        $('.stepBody').hide();
        wizardStyle(tabShowBtn);
        var tab = $('#' + tabShowBtn + '_Tab');
        tab.show();

        $('html, body').animate({
            scrollTop: $('#ajax_body_container').offset().top
        }, 300);
    }

    function wizardStyle(btnID) {
        var wizardBtn = $('.btn-wizard');
        wizardBtn.removeClass('btn-primary');
        wizardBtn.addClass('btn-default');
        wizardBtn.css('color', '#333');

        var btn = $('#' + btnID);
        btn.addClass('btn-primary');
        btn.css('color', '#fff');
    }

    $('#bankTransfer').click(function () {

        var isBankTransferProcessed = $('#isBankTransferProcessed').val();

        if (isBankTransferProcessed == 1) {
            var payID = $('#hidden_payrollID').val();
            var loadBankTransferData = $('#loadBankTransferData');
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'html',
                data: {'payrollID': payID, 'isNonPayroll':'N'},
                url: "<?php echo site_url('Template_paysheet/load_bankTransferPage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if (data[0] == 'e') {
                        myAlert(data[0], data[1]);
                    }
                    else {
                        loadBankTransferData.html('');
                        loadBankTransferData.html(data);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    return 'e';
                }
            });
        }
        else {
            bankTransfer();
            /*var isSuccess = bankTransfer();
            if (isSuccess == 's') {
                $('#bankTransfer').click();
            }*/
        }

    });

    $('#emp_withoutBank').click(function () {
        var isBankTransferProcessed = $('#isBankTransferProcessed').val();

        if (isBankTransferProcessed == 1) {
            var payID = $('#hidden_payrollID').val();
            var loadEmp_withoutBankData = $('#loadEmp_withoutBankData');
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'html',
                data: {'payrollID': payID, 'isNonPayroll':'N'},
                url: "<?php echo site_url('Template_paysheet/load_empWithoutBankPage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if (data[0] == 'e') {
                        myAlert(data[0], data[1]);
                    }
                    else {
                        loadEmp_withoutBankData.html('');
                        loadEmp_withoutBankData.html(data);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    return 'e';
                }
            });
        }
        else {
             bankTransfer();
        }
    });

    function getVisibleDate(){
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'year': $('#payYear').val(), 'month': $('#payMonth').val()},
            url: "<?php echo site_url('Template_paysheet/getVisibleDate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#visibleDate').val(data);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
                return 'e';
            }
        });
    }

    function getNotPayrollProcessedMonths(obj) {
         $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'year': obj.value},
            url: "<?php echo site_url('Template_paysheet/getNotPayrollProcessedMonths'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                var payMonth = $('#payMonth');
                payMonth.empty();
                $.each(data, function (elm, val) {
                    payMonth.append('<option value="' + val['monthNo'] + '">' + val['monthDescription'] + '</option>');
                });


            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
                return 'e';
            }
        });
    }

    $('.showBtnDiv').click(function () {
        $('#btnDiv').show();
        $('#templateId').prop('disabled', false);
    });

    $('.hideBtnDiv').click(function () {
        $('#btnDiv').hide();
        $('#templateId').prop('disabled', true);
    });

    function delete_PayrollEmp(obj, empID, payrollID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'payrollID' : payrollID, 'empID': empID, 'isNonPayroll':'N'},
                    url: "<?php echo site_url('Template_paysheet/delete_PayrollEmp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            setTimeout(function(){
                                load_btn();
                            },300)
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

    function commentUpdate(obj, id){
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'payrollHeaderDetID' : id, 'comment': obj.value, 'isNonPayroll':'N'},
            url: "<?php echo site_url('Template_paysheet/update_payrollEmpComment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error : function() {
                stopLoad();
                myAlert('e','An Error Occurred! Please Try Again.');
            }
        });
    }

    /** Employee add functions **/


    $('#currency').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#segment').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });



    var tempTB = $('#tempTB').DataTable({
        "bPaginate": false,
        "aoColumnDefs": [{"bSortable": false, "aTargets": [2]}]
    });

    function openEmployeeModal(){
        var msg = '';
        var load_payYear = $('#payYear').val();
        var load_payMonth = $('#payMonth').val();
        var load_processingDate = $('#processingDate').val();
        var load_payNarration = $('#payNarration').val();
        var load_templateId = $('#templateId').val();

        if( load_payYear == '' || load_payMonth == ''){
            msg += 'Payroll Month <br/>';
        }
        if( load_processingDate == ''){
            msg += 'Processing Date <br/>';
        }
        if( load_payNarration == ''){
            msg += 'Narration <br/>';
        }
        if( load_templateId == ''){
            msg += 'Paysheet Template <br/>';
        }

        if(msg == '' ){
            $('#employee_model').modal('show');
            load_employeeForModal();
        }
        else{
            myAlert('e', 'Fill the following details and try again <br/>'+msg);
        }

    }

    function load_employeeForModal(){
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            //"bPaginate": false,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Template_paysheet/getEmployeesDataTable_period_base'); ?>",
            "aaSorting": [[1, 'asc']],
            "aLengthMenu": [[10, 25, 50, 100,200, -1], [10, 25, 50, 100,200, "All"]],
            "pageLength": 200,
            "iTotalRecords": 10000,
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0,4,5,6]}],
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
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "DesDescription"},
                {"mData": "segmentCode"},
                {"mData": "CurrencyCode"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'segment', 'value':$('#segment').val()});
                aoData.push({'name':'currency', 'value':$('#currency').val()});
                aoData.push({'name':'payGroup', 'value': $('#p_group').val()});
                aoData.push({'name':'period_drop', 'value': period_drop.val()});
                aoData.push({'name':'isNonPayroll', 'value':'N'});

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

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="empHiddenID[]"  class="modal_empID" value="'+empID+'">';
            empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="'+details.last_ocGrade+'">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0:  details.ECode,
                1:  details.empName,
                2:  empDet,
                3:  empID
            }]).draw();

            empTempory_arr.push(empID);
        }

    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="empHiddenID[]" class="modal_empID" value="' + empID + '">';
                empDet1 += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + data.last_ocGrade + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTempory_arr.push(empID);
            }
        } );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function(data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function load_employeeToPayroll(withExpenseClaim=null){
        if( empTempory_arr.length > 0 ){
            load_btn(withExpenseClaim);
        }
        else{
            myAlert('e', 'Please select at least one employee');
        }
    }

    function changeExpStatus(obj, boxses){
        $(boxses).prop('checked', $(obj).is(':checked'));
    }

    function checkTotalChecked(chkBox, allChkBox){
        var numItems = $(chkBox).length;
        var totalChecked = $(chkBox+':checked').length;
        var status = (numItems == totalChecked);
        $(allChkBox).prop('checked', status);
    }

    $(document).on('keypress', '.escapeDoubleQuotes',function (event) {
        if (String.fromCharCode(event.which) == '"') {
            event.preventDefault();
        }
    });

    $(document).on('change', '.escapeDoubleQuotes',function () {
        var myString = $(this).val();
        var newVal = myString.replace(/"/g , '');
        $(this).val(newVal);
    });

</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-06-22
 * Time: 11:38 AM
 */
