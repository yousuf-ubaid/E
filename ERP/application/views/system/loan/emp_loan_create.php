<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_loan_employee_loan');
echo head_page($title  , false);
$date_format_policy = date_format_policy();
$loanTypes = load_loan_types();
$current_date = current_format_date();

?>
<style type="text/css">


.empDisTbTR {
    width: 110px;
    padding-left: 10px;
    font-weight: 700;
}

.empDetailDisplay {
    padding-left: 10px;
    font-weight: 700;
}

.empDetailDiv{
    margin-top: 30px;
}

.skipCheckbox{
    margin: 0px !important;
}

.dynamic_datePicker{
    font-size: 11px;
    height: 22px;
}

.myInputGroup{
    height: 20px;
    padding: 0px 8px;
    padding-bottom: 3px;
}

.myFa-calender{
    font-size: 10px;
}

#skipTB{
    width: 90% !important;
}
/*.tt-dropdown-menu { top: 58% !important; }*/
</style>

<div class="m-b-md" id="wizardControl">
    <a class="btn btn-wizard btn-primary" href="#step1" id="loanHeader" onclick="tabShow(this.id)" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step -->1 - <?php echo $this->lang->line('hrms_loan_loan_header');?><!--Loan Header--></a>
    <a class="btn btn-wizard btn-default schedule" href="#step2"  id="loanSchedule" onclick="tabShow(this.id)" data-value="0" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 2 - <?php echo $this->lang->line('hrms_loan_loan_schedule');?><!--Loan Schedule--></a>
    <a class="btn btn-wizard btn-default schedule" href="#step3"  id="loanConform" onclick="tabShow(this.id)" data-value="0"  data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 3 - <?php echo $this->lang->line('hrms_loan_loan_confirmation');?><!--Loan Confirmation--></a>
</div>

<div class="col-md-12 empDetailDiv">
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-1">
                <div class="">
                    <a href="#" class="thumbnail"> <img src="<?php echo base_url(); ?>images/default.gif" id="empImg" alt=""> </a>
                </div>
            </div>

            <div class="col-md-5">
                <table border="0px">
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_loan_employee_name');?><!--Employee Name--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="empNameDis"></td>
                    </tr>
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_loan_employee_code');?><!--Employee Code--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="empCodeDis"></td>
                    </tr>
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_loan_Designation');?><!--Designation--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="empDisgnationDis"></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-5">
                <table border="0px">
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_loan_loan_code');?><!--Loan Code--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="disLoanCode">-</td>
                    </tr>
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('common_approved_by');?><!--Approved By--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="empCodeDis">-</td>
                    </tr>
                    <tr>
                        <td class="empDisTbTR"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                        <td class="" width="10px" align="center"> :</td>
                        <td class="empDetailDisplay" id="loanStatus">-</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal-body stepBody" id="loanHeaderTab">
    <?php echo form_open('', 'role="form" class="" id="empLoanForm" autocomplete="off"'); ?>
    <div class="row">
        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_employee_name');?><!--Employee Name--></label>
            <input type="text" name="empName" id="empName" class="form-control inputControl" placeholder="<?php echo $this->lang->line('hrms_loan_enter_minimum_two_letters');?>"><!--Enter minimum two letters-->
            <input type="hidden" name="empID" id="empID" >
        </div>

        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_loan_type');?><!--Loan Type--></label>
            <?php echo form_dropdown('loanType', $loanTypes, '', 'class="form-control inputControl" id="loanTypeID"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label>Int.<?php echo $this->lang->line('common_percentage');?><!--Int. Percentage--></label>
            <input type="text" class="form-control number inputControl" name="intPer" id="intPer" placeholder="" readonly>
        </div>

        <div class="form-group col-sm-3 ">
            <label><?php echo $this->lang->line('common_salary_advance_request');?></label>
            <?php echo form_dropdown('salary_advanceID', null, '', 'class="form-control inputControl" id="salary_advanceID" disabled onchange="load_advance_amount()"'); ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_date');?><!--Loan Date--></label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="loanDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="loanDate"
                       class="form-control">
            </div>
        </div>
        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_amount');?><!--Loan Amount--></label>
            <div class="input-group">
                <span class="input-group-addon" id="empCurrencySpan">&nbsp;&nbsp;&nbsp;</span>
                <input type="text" class="form-control number inputControl" name="loanAmount" id="loanAmount" onchange="formatAmount(this)" placeholder="">
                <input type="hidden" name="payCurrencyCode" id="payCurrencyCode">
                <input type="hidden" name="payCurrencyDPlace" id="payCurrencyDPlace">
            </div>
        </div>

        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_no_of_installment');?><!--No. of Installment--></label>
            <input type="text" class="form-control number inputControl" name="noOfInstallment" id="noOfInstallment" placeholder="">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_deduction_start_date');?><!--Deduction Start Date--></label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="deductionDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="deductionDate"
                       class="form-control">
            </div>
        </div>

        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('hrms_loan_loan_description');?><!--Loan Description--></label>
            <textarea class="form-control inputControl" name="loanDescription" id="loanDescription" rows="3" placeholder=""></textarea>
        </div>
    </div>


    <hr>
    <input type="hidden" name="hiddenLoanID" id="hiddenLoanID">
    <input type="hidden" name="hiddenLoanCode" id="hiddenLoanCode">
    <div class="text-right m-t-xs">
        <button class="btn btn-primary btn-sm inputControl" id="submitBtn" type="submit" ><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        <button class="btn btn-primary btn-sm inputControl" id="updateBtn"  type="submit" style="display:none" ><?php echo $this->lang->line('common_update');?><!--Update--></button>
        <button class="btn btn-default btn-sm previousNextBtn" id="loanHeaderBtn" data-tab="loanSchedule" type="button" style="display:none" ><?php echo $this->lang->line('common_next');?><!--Next--></button>
    </div>

    <?php echo form_close();?>
</div>

<div class="modal-body stepBody" style="display: none" id="loanScheduleTab" >
    <div class="row">
        <div class="col-md-5" style="margin-bottom: 1%">
            <table class="table table-bordered table-striped table-condensed ">
                <tbody><tr>
                    <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('hrms_loan_settled');?><!--Settled--> </td>
                    <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_pending');?><!--Pending--> </td>
                    <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_skipped');?><!--Skipped--> </td>
                </tr>
                </tbody></table>
        </div>
    </div>
    <table id="" class="<?php echo table_class(); ?> loanScheduleTB">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_deduction_date');?><!--Deduction Date--> </th>
                <th style="min-width: 5%"><?php echo $this->lang->line('hrms_loan_installment_no');?><!--Installment No--> </th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> &nbsp;&nbsp;<span class="dataTableCurrency"></span></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                <th class="loanScheduleAction"><?php echo $this->lang->line('hrms_loan_reschedule');?><!--Reschedule--></th>
            </tr>
        </thead>
    </table>

    <hr>

    <div class="text-right m-t-xs">
        <button class="btn btn-primary btn-sm" id="reschedule" type="button"><?php echo $this->lang->line('hrms_loan_reschedule');?><!--Reschedule--></button>
        <button class="btn btn-default btn-sm previousNextBtn" data-tab="loanHeader" type="button" ><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
        <button class="btn btn-default btn-sm previousNextBtn" data-tab="loanConform" type="button" ><?php echo $this->lang->line('common_next');?><!--Next--></button>
    </div>
</div>



<div class="modal-body stepBody" style="display: none" id="loanConformTab" >
    <div> <!--<div class="table-responsive">-->
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td><b><?php echo $this->lang->line('hrms_loan_type');?><!--Loan Type--></b></td>
                    <td>:</td>
                    <td id="con_loanType"> </td>
                    <td><b>Int.<?php echo $this->lang->line('common_percentage');?><!-- Percentage--></b></td>
                    <td>:</td>
                    <td id="con_intPer"> </td>
                </tr>

                <tr>
                    <td><b><?php echo $this->lang->line('hrms_loan_date');?><!--Loan Date--></b></td>
                    <td>:</td>
                    <td id="con_loanDate"> </td>
                    <td><b><?php echo $this->lang->line('hrms_loan_amount');?><!--Loan Amount--></b></td>
                    <td>:</td>
                    <td id="con_amount"> </td>
                </tr>

                <tr>
                    <td><b><?php echo $this->lang->line('hrms_loan_no_of_installment');?><!--No. of Installment--></b></td>
                    <td>:</td>
                    <td id="con_noOfIns"> </td>
                    <td><b><?php echo $this->lang->line('hrms_loan_deduction_start_date');?><!--Deduction Start Date--></b></td>
                    <td>:</td>
                    <td id="con_dedStartDate"> </td>
                </tr>

                <tr>
                    <td><b><?php echo $this->lang->line('hrms_loan_loan_description');?><!--Loan Description--></b></td>
                    <td>:</td>
                    <td colspan="4" id="con_loanDes"> </td>
                </tr>
                <tr><td colspan="6">&nbsp;</td></tr>
            </tbody>
        </table>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-5" style="margin-bottom: 1%">
            <table class="table table-bordered table-striped table-condensed ">
                <tbody><tr>
                    <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('hrms_loan_settled');?><!--Settled--> </td>
                    <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_pending');?><!--Pending--> </td>
                    <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_skipped');?><!--Skipped--> </td>
                </tr>
                </tbody></table>
        </div>
    </div>

    <table id="scheduleConfirmTB" class="<?php echo table_class(); ?> loanScheduleTB">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_deduction_date');?><!--Deduction Date--> </th>
            <th style="min-width: 5%"><?php echo $this->lang->line('hrms_loan_installment_no');?><!--Installment No--> </th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> &nbsp;&nbsp;<span class="dataTableCurrency"></span></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th class="loanScheduleAction"></th>
        </tr>
        </thead>
    </table>

    <div style="margin:1%">&nbsp;</div>
    <hr>

    <div class="text-right m-t-xs">
        <button class="btn btn-default btn-sm previousNextBtn" data-tab="loanSchedule" type="button" ><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
        <button class="btn btn-success btn-sm inputControl" id="ConformBtn" type="button" onclick="loan_conformation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
    </div>
</div>



<div id="loanSetupDet"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="skipModal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_loan_loan_schedule_skip_setup');?><!--Loan Schedule Skip Setup--> <span id="loanCode_skipModal"></span> </h3>
            </div>

            <div class="row" style="margin-left: 15px; margin-top: 1%">
                <div class="col-sm-6" style="font-weight: bold;"> <?php echo $this->lang->line('hrms_loan_schedule_end_date');?><!--Schedule End Date--> &nbsp;&nbsp; :&nbsp; <span id="lastScheduleDate"></span></div>
            </div>

            <form role="form" id="scheduleForm_skip" class="form-horizontal">
                <div class="modal-body">
                    <div class="row" style="margin-left: 13px;">
                        <div class="col-sm-2" >
                            <div class="form-group">
                                <label for="">Reshedule By</label>
                            </div>
                        </div>
                        <div class="col-sm-3" >
                            <div class="form-group">
                                        <label for="isDate">  <?php echo $this->lang->line('common_date'); ?><!--date--></label>
                                        <div class="input-group">
                                            <input type="checkbox" value="date" id="isDate" name="isDate" onclick="unCheckAmount()" checked>
                                        </div>
                                    </div>
                                 
                        </div>
                        <div class="col-sm-3" >
                            <div class="form-group">
                                        <label for="isAmount"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></label>
                                        <div class="input-group">
                                            <input type="checkbox" value="amount" id="isAmount" name="isAmount" onclick="show_Amount_input()" >
                                        </div>
                                    </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <table id="skipTB" class="<?php echo table_class(); ?>" align="center">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="5%"><?php echo $this->lang->line('hrms_loan_int_no');?><!--Int.No--></th>
                                    <th width="15%"><?php echo $this->lang->line('hrms_loan_schedule_date');?><!--Schedule Date--></th>
                                    <th width="20%" id="amount_th" class="amount"  style="display: none"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                                    <th width="10%" id="" class="amount"  style="display: none">New Amount</th>
                                    <th width="20%"id="" class="amount"  style="display: none">Remaining Amount</th>
                                    <th width="15%"><?php echo $this->lang->line('hrms_loan_new_date');?><!--New Date--></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                        <div class="form-group" style="margin-top: 5%">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_loan_processing_date');?><!--Processing Date--></label>
                            <div class="col-sm-6">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="skipProcessDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="sk"
                                           class="form-control" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <input type="text" name="skipDescription" class="form-control" id="skipDescription">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="isConform_skip" name="isConform_skip" >
                    <input type="hidden" id="requestLink_skip" name="requestLink_skip" >
                    <input type="hidden" id="hiddenLoanID_skip" name="hiddenLoanID_skip" >
                    <input type="hidden" id="hiddenLoanCode_skip" name="hiddenLoanCode_skip" >

                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="button" class="btn btn-primary btn-sm saveBtn skip_submitBtn" data-value="0"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        fetchLoanCategory();
        //number_validation();
        initializeitemTypeahead();

        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            var thisDate = $(this).attr('id');
            $(this).datepicker('hide');
            $('#empLoanForm').bootstrapValidator('revalidateField', thisDate);
        });

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        $('#empLoanForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                empName     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_employee_is_required');?>.'}}},/*Employee is required*/
                loanType     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_loan_loan_type_is_required');?>.'}}},/*Loan Type is required*/
                loanDate     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_loan_loan_date_is_required');?>.'}}},/*Loan Date is required*/
                loanAmount     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_loan_loan_amount_is_required');?>.'}}},/*Loan Amount is required*/
                noOfInstallment  : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_loan_no_of_installment_is_required');?>.'}}},/*No Of Installment is required*/
                deductionDate     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_loan_deduction_date_is_required');?>.'}}}/*Deduction Date is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form       = $(e.target);
            var bv          = $form.data('bootstrapValidator');
            var postData    = $form.serializeArray();
            var loanAmount  =  $('#loanAmount').val();
            loanAmount      =  loanAmount.replace(/,/g , "");

            for (var index = 0; index < postData.length; ++index) {
                if (postData[index].name == "loanAmount") {
                    postData[index].value = loanAmount;
                    break;
                }
            }

            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Loan/createLoan'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        var loanID  = data[2];
                        var loanDet = ['edit', loanID, 'changeTab'];

                        fetchPage('system/loan/emp_loan_create',0,'Employee Loan Create','', loanDet);
                        /*tabShow('loanSchedule');
                        showUpdateBtn();

                        $('#hiddenLoanID').val(data[2]);
                        $('#hiddenLoanCode').val(data[3]);
                        $('#disLoanCode').text(data[3]);
                        loadLoanSchedule( data[2] );

                        var btnWizard = $('.btn-wizard');
                        btnWizard.attr('data-value', '1');
                        btnWizard.removeClass('disabled');*/

                    }
                },
                error : function() {
                    stopLoad();
                    myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

        });

        var loadDet = <?php echo json_encode($this->input->post('data_arr')); ?>;
        var autoID = null;

        if (loadDet) {

            var loadType = loadDet[0];
            var loanID   = loadDet[1];
            autoID = loanID;

            showUpdateBtn();
            load_emp_loanDet(loanID);

            //if(loadType == 'view'){  $('.inputControl').attr('disabled', true); }


        }else{
            $('.btn-wizard').not(':first').addClass('disabled');
        }

        $('.headerclose').click(function(){
            fetchPage('system/loan/emp_loan',autoID,'HRMS');
        });
    });

    $('#loanSchedule').click(function(){
        var dataVal = $(this).attr('data-value');

        if(dataVal == 0){
            $(this).attr('data-value', '1');
            $('#loanConform').attr('data-value', '1');
            loadLoanSchedule( $('#hiddenLoanID').val() );
        }
    });

    $('#loanConform').click(function(){
        var dataVal = $(this).attr('data-value');

        if(dataVal == 0){
            $('#loanSchedule').attr('data-value', '1');
            $(this).attr('data-value', '1');
            loadLoanSchedule( $('#hiddenLoanID').val() );
        }
    });

    function fetchLoanCategory(){
        $('#loanSetupDet').html();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Loan/fetchLoanCategory'); ?>',
            data: '',
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                $.each(data, function(elm, val){
                    var setupDet   = 'loanTypCls_'+ val['loanID'];
                    var isIntBase  = val['isInterestBased'];
                    var intPercent = val['interestPercentage'];
                    var isSalaryAdvance = val['isSalaryAdvance'];

                    $('#loanSetupDet').append( '<input type="hidden" class="'+setupDet+'" value="'+isIntBase+'" data-int="'+intPercent+'" data-advance="'+isSalaryAdvance+'"/>' );
                });
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Load this page again*/
            }
        });
    }

    function initializeitemTypeahead() {
        var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>employee/searchInEmpLoan/?keyword=%QUERY"
        });
        item.initialize();
        $('#empName').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#loanTypeID').val('');
            $('#salary_advanceID').empty().prop('disabled', true);

            $('#empID').val(datum.EIdNo);
            //$('#empName').val(datum.Ename1 + ' ' + datum.Ename2 + ' ' + datum.Ename3  + ' ' + datum.Ename4);
            $('#empName').val( datum.Ename2 );
            $('#empNameDis').text( datum.Ename2 );
            $('#empCodeDis').text(datum.ECode);
            $('#empDisgnationDis').text(datum.DesDescription);
            $('#empCurrencySpan').text(datum.payCurrency);
            $('#payCurrencyCode').val(datum.payCurrency);
            $('.dataTableCurrency').text('[ '+datum.payCurrency+' ]');
            $('#payCurrencyDPlace').val(datum.DecimalPlaces);

            $('.empDetailDiv').hide();
            $('.empDetailDiv').fadeIn(500);


        });
    }

    /*$('#empName').autocomplete({
        serviceUrl: '  echo site_url();?>/Employee/fetch_employees_typeAhead/',
        onSelect: function (suggestion) {
            $('#managerID').val(suggestion.data);
        }
    });*/

    $('#empName').on('change blur', function () {
        $(this).val($.trim($('#empNameDis').text()));
    });

    $('#loanTypeID').change(function() {
        $('#salary_advanceID').empty().prop('disabled', true);
        $('#loanAmount').prop('disabled', false);
        var intPerTxt = $('#intPer');
        intPerTxt.val('');

        var loanID    = $('#loanTypeID').val();
        var isIntBase = $('.loanTypCls_'+loanID).val();

        if(isIntBase == 1){
            var intPer    = $('.loanTypCls_'+loanID).attr('data-int');
            intPerTxt.val(intPer);
        }else{ intPerTxt.val(0); }

        if( $('.loanTypCls_'+loanID).attr('data-advance') == 1 ){
            $('#loanAmount').prop('disabled', true);
             load_advance_salary();
        }
    });

    function load_advance_salary(){
        var empID = $('#empID').val();

        if(empID == ''){
            $('#loanTypeID').val(0).change();
            myAlert('e', 'Please select the employee first');
            return false;
        }

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {empID :empID},
            url: "<?php echo site_url('Loan/load_advance_salary_drop'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#salary_advanceID').append(data['drop_list']).prop('disabled', false);
                }
            },
            error : function() {
                stopLoad();
                myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function load_advance_amount(){
        var amount = $('#salary_advanceID :selected').attr('data-amount');
        $('#loanAmount').val(amount).change();
    }

    function loanSave(){
        var postData = $('#empLoanForm').serialize();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Loan/createLoan'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {

                }
            },
            error : function() {
                stopLoad();
                myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function formatAmount(id) {
        id = id.id;
        if ($('#' + id).val() == '') {
            $('#' + id).val(0);
        }
        var amount = $('#' + id).val().replace(/,/g, "");
        amount = parseFloat(amount).toFixed(2);

        var dPlace = $('#payCurrencyDPlace').val();
        $('#' + id).val(commaSeparateNumber(amount, dPlace));
    }

    $('.number').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    function load_emp_loanDet(loanID , loadType = null){
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/load_emp_loanDet') ?>',
            data: {'loanID': loanID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                var intPer = ( data['interestPer'] == 0 ) ? '' : data['interestPer'];

                $('#hiddenLoanID').val(data['ID']);
                $('#hiddenLoanCode').val(data['loanCode']);
                $('#empID').val(data['EIdNo']);
                $('#empName').val(data['Employee']).prop('disabled', true);
                $('#loanTypeID').val(data['loanCatID']).prop('disabled', true);
                $('#intPer').val(intPer);
                $('#loanDate').val( data['loanDate']);
                $('#empCurrencySpan').text(data['payCurrency']);
                $('.dataTableCurrency').text('[ '+data["payCurrency"]+' ]');
                $('#payCurrencyCode').val(data['payCurrency']);
                $('#payCurrencyDPlace').val(data['DecimalPlaces']);
                $('#loanAmount').val(commaSeparateNumber(data['amount'], data['DecimalPlaces']));
                $('#noOfInstallment').val(data['numberOfInstallment']);
                $('#deductionDate').val( data['deductionStartingDate']);
                $('#loanDescription').val(data['loanDescription']);
                $('#empNameDis').text(data['Employee']);
                $('#empCodeDis').text(data['ECode']);
                $('#empDisgnationDis').text(data['DesDescription']);
                $('#disLoanCode').text(data['loanCode']);

                if(data['salaryAdvanceRequestID'] > 0){
                    var str = '<option value="'+data['salaryAdvanceRequestID']+'"> '+data['ad_document_code']+' | '+data['request_amount']+' </option>';
                    $('#salary_advanceID').append( str );

                    setTimeout(function(){
                        $('#salary_advanceID').val( data['salaryAdvanceRequestID'] );
                        $('#loanAmount').prop('disabled', true);
                    }, 100);
                }


                //values for conformation tab
                intPer = ( intPer == '' )? '-' : intPer;
                $('#con_loanType').text( $('#loanTypeID').find(':selected').text() );
                $('#con_intPer').text(intPer);
                $('#con_loanDate').text(data['loanDate']);
                $('#con_amount').text(commaSeparateNumber(data['amount']));
                $('#con_noOfIns').text(data['numberOfInstallment']);
                $('#con_dedStartDate').text(data['deductionStartingDate']);
                $('#con_loanDes').text(data['loanDescription']);

                //if loan is confirmed disable all fields.
                if( data['confirmedYN'] == 1 ){ $('.inputControl').attr('disabled', true); }

                var data_arr = '<?php echo json_encode($this->input->post('data_arr')); ?>';
                data_arr = $.parseJSON(data_arr);

                if( data_arr.length > 2 && data_arr[2] == 'changeTab'){
                    tabShow('loanSchedule');
                    $('#loanHeaderBtn').click();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function loadLoanSchedule(loanID){
        var Otable = $('.loanScheduleTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/load_empLoanSchedule?loanID='); ?>"+loanID,
            "aaSorting": [[2, 'asc']],
            "fnInitComplete": function () {
                setTimeout(function(){
                    $('.loanScheduleAction').css('width' , '30px');
                },200);

                var scheduleConfirmTB = $('#scheduleConfirmTB').DataTable();
                scheduleConfirmTB.columns( '.loanScheduleAction' ).visible( false );

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "scheduleID"},
                {"mData": "scheduleDate1"},
                {"mData": "installmentNo"},
                {"mData": "amount"},
                {"mData": "status"},
                {"mData": "action"},
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

    function loan_conformation(){
        var loanID = $('#hiddenLoanID').val();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/loan_conformation') ?>',
            data: {'loanID': loanID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#ConformBtn').addClass('disabled');
                    $('.inputControl').attr('disabled', true);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    $('.previousNextBtn').click(function () {
        var tab = $(this).attr('data-tab');
        tabShow(tab);
    });

    function wizardStyle(btnID){
        var wizardBtn = $('.btn-wizard');
        wizardBtn.removeClass('btn-primary');
        wizardBtn.addClass('btn-default');
        wizardBtn.css('color', '#333');

        var btn = $('#'+btnID);
        btn.addClass('btn-primary');
        btn.css('color', '#fff');
    }

    function tabShow(tabShowBtn){
        $('.stepBody').hide();
        wizardStyle(tabShowBtn);
        var tab = $('#'+tabShowBtn+'Tab');
        tab.show();

        $('html, body').animate({
            scrollTop: $('#ajax_body_container').offset().top
        }, 300);
    }

    function showUpdateBtn(){
        $('#submitBtn').hide();
        $('#updateBtn').show();
        $('#loanHeaderBtn').show();
    }

    $('#loanHeaderBtn').click(function(){
        loadLoanSchedule( $('#hiddenLoanID').val() );
    });

    $('.schedule').click(function(){
        $('.loanScheduleAction').css('width' , '30px');
    });

    $('#reschedule').click(function(){
        var loanID = $('#hiddenLoanID').val();
        var count = 0;
        $('.skipCheckbox:checked').each(function(){
            count++;
        });

        if( count > 0 ) {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('loan/getLastScheduleDate') ?>',
                data: {'loanID': loanID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    Inputmask().mask(document.querySelectorAll("input"));
                    if (data[0] == 's') {
                        loadSkipProcess(data);
                        show_Amount_input();
                        number_validation();
                    }
                    else {
                        myAlert('e', '<?php echo $this->lang->line('common_please_contac_support_team');?>');/*Please contact support team*/
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('hrms_loan_please_select_at_least_one_installment_to_proceed');?>')/*Please select at least one installment to proceed*/
        }

    });

    function loadSkipProcess(scheduleDet){
        var lastScheduleDate = scheduleDet[1];

        $('#hiddenLoanID_skip').val( $('#hiddenLoanID').val() );
        $('#hiddenLoanCode_skip').val( $('#hiddenLoanCode').val() );
        $('#loanCode_skipModal').text( '[ '+ $('#hiddenLoanCode').val() +' ]');
        var ScheduleDate = moment(lastScheduleDate);

        var lstScheduleDate=   ScheduleDate.format("<?php echo strtoupper($date_format_policy)  ?>");

        $('#lastScheduleDate').text( lstScheduleDate );
        $("#skipModal").modal({backdrop: "static"});
        $('#skipTB tbody').empty();
        var skipTB = $('#skipTB');
        var skipDet = '';
        var j = 1;

        $('.skipCheckbox:checked').each(function(){
            var skipID = $(this).val();
            var intDate = $(this).attr('data-date');
            var intNo = $(this).attr('data-intno');
            var amount = $(this).attr('data-ramount');
            var newSchedule = new Date(intDate);
            var nextSchedule = addMonths( newSchedule , 1);
            var month = nextSchedule.getMonth() + 1;
            var date = nextSchedule.getDate();
            var monthLeadZero = ( month.toString().length == 1 )? '0'+month : month;
            var dateLeadZero = ( date.toString().length == 1 )? '0'+date : date;
            var nextDate = nextSchedule.getFullYear()+'-'+ monthLeadZero +'-'+ dateLeadZero;

            var currentDate = moment(intDate);
            var futureMonth = moment(currentDate).add(1, 'M');
            var nextDates=   futureMonth.format("<?php echo strtoupper($date_format_policy)  ?>");

            var skipDate = '<div class="input-group datepicker1"><div class="input-group-addon myInputGroup"><i class="fa fa-calendar myFa-calender"></i></div>';

            //skipDate += '<input type="text" name="skipDate[]" value="'+nextDate+'"  class="form-control dynamic_datePicker" data-schedule="'+intDate+'" data-intno="'+intNo+'"></div>';
            skipDate += '<input type="text" name="skipDate[]" style="font-size: 11px;height: 22px;" data-inputmask="\'alias\': \'<?php echo $date_format_policy ?>\'" value="'+nextDates+'" data-schedule="'+intDate+'" data-intno="'+intNo+'" class="form-control" ></div>';


            var skipHidden = '<input type="hidden" name="skipID[]" value="'+skipID+'">';
            var convertDate= moment(intDate, "YYYY-MM-DD").format("<?php echo strtoupper($date_format_policy)  ?>");

            
             //if($("#isAmount").is(':checked')){
            installmentAmountField = '<input type="text" name="amount[]" class="number" value="'+amount+'" readonly>';
            
            amountField='<input id="resheduleAmount'+skipID+'" onkeyup="resheduleAmountValidation(this.value,'+amount+','+skipID+')" class="number  resheduleAmount " type="text" name="resheduleAmount[]" value="0"  >';
            remainingAmountField='<input id="remainingAmount'+skipID+'"  class="number  remainingAmount " type="text" name="remainingAmount[]" value="0" readonly >';
                
            // }

            skipDet += '<tr><td>'+j+'</td><td>'+intNo+'</td><td align="center">'+convertDate+' </td><td class="amount" style="display: none" >'+installmentAmountField+'</td><td class="amount" style="display: none">'+amountField+'</td><td class="amount" style="display: none" >'+remainingAmountField+'</td><td>'+skipDate+' '+skipHidden+'</td> </tr>';
            j++;

        });

        skipTB.append(skipDet);

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepicker1').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });
    }

    $(document).on("focus", ".dynamic_datePicker", function(){
        $(this).datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');

            var newDate = new Date($(this).val());
            var scheduleDate = new Date($(this).attr('data-schedule'));
            var scheduleIntNo = $(this).attr('data-intno');
            var nextDate = addMonths(scheduleDate, 1);

            if( newDate < nextDate  ){
                $(this).val('');
                var month = nextDate.getMonth() + 1;
                var date = nextDate.getDate();
                var monthLeadZero = ( month.toString().length == 1 )? '0'+month : month;
                var dateLeadZero = ( date.toString().length == 1 )? '0'+date : date;
                nextDate = nextDate.getFullYear()+'-'+ monthLeadZero +'-'+ dateLeadZero;

                myAlert('e', 'Please select the date greater than or equal to <p>'+nextDate+' on Int.No '+scheduleIntNo);
            }


        });
    });

    //Date validation function
    function addMonths(date, value) {
        var d = new Date(date),
            n = date.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth() + value);
        d.setDate(Math.min(n, getDaysInMonth(d.getFullYear(), d.getMonth())));
        return d;
    }

    function isLeapYear(year) {
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
    }

    function getDaysInMonth(year, month) {
        return [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }
    //end of Date validation function

    $('.skip_submitBtn').click(function(){
        var postData = $('#scheduleForm_skip').serializeArray();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/skipLoanSchedule') ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#skipModal').modal('hide');
                    var loanID = $('#hiddenLoanID').val();
                    loadLoanSchedule(loanID);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });

    });

    function show_Amount_input(){
        if($("#isAmount").is(':checked')){
            $("#isDate"). prop("checked", false);
            $('.amount').removeClass('hidden');
            $('.amount').show();
           
        }else{
            $('.amount').hide();
        }
    }

    function unCheckAmount(){
        if($("#isDate").is(':checked')){
            $("#isAmount"). prop("checked", false);
            $('.amount').hide();
        }else{
            $('.amount').show();
        }
    }
    function resheduleAmountValidation(resheduledAmount,amount,skipID){
        $('#remainingAmount'+skipID).val(parseFloat(amount)-parseFloat(resheduledAmount));
        
        if(parseFloat(resheduledAmount)>parseFloat(amount)){
            $('#resheduleAmount' + skipID).val(amount);
            $('#remainingAmount'+skipID).val(0);
            myAlert('w', 'Resheduled amount cannot be greater than amount');
        }
        
    }
    
</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/22/2016
 * Time: 1:52 PM
 */