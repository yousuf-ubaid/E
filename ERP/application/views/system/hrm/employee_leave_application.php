<!--Translation added by Naseek-->


<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_application');
echo head_page($title, true);
$employeeDrop =  fetch_my_attendees_and_reporting_self();
$employee_arr = all_employee_drop(false);
$leaveTypes   = leaveTypes_drop();
$current_date = format_date($this->common_data['current_date']);
//$employeeDrop = leaveApplicationEmployee();
$date_format_policy = date_format_policy();
$current_date_filter = convert_date_format(date('Y-01-01'));
$current_date_filter2 = convert_date_format(date('Y-12-31'));
$filterStatus = [
    'all' =>$this->lang->line('common_all') /*'All'*/,
    'draft' =>$this->lang->line('common_draft')/* 'Draft'*/,
    'confirmed' =>$this->lang->line('common_confirmed') /*'Confirmed'*/,
    'approved' =>$this->lang->line('common_approved') /*'Approved'*/,
    'canReq' =>$this->lang->line('common_canceled_req') /*'Cancellation Request'*/,
    'canApp' =>$this->lang->line('common_canceled') /*'Canceled'*/
];
$attendee_filter_arr=[
    'myLeave' =>'My Leave' /*'All'*/,
    'myEmployee' =>'My Employee Leave'/* 'Draft'*/,
    'all' =>'All'
];

$applicationType_filter_arr = [
    1 =>'Applied Leave',
    2 =>'Leave Plan'
  ];

$leaveType_filter_arr = leaveTypes_filter_drop();
?>
<style type="text/css">
    .cancel-pop-up:hover{ cursor: pointer; }

    .frm_input {
        height: 28px;
        font-size: 12px;
    }

    .panel-body {
        margin-bottom: 20px;
        background-color: #ffffff;
        border: 1px solid #dddddd;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    }

    .overlay {
        z-index: 50;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 3px;
    }

    .panel-body > .overlay {
        position: relative;
    / / top: 5 px;
    / / left: 5 px;
        width: 100%;
        height: 100%;
    }

    .myOverlay-spin {
        color: #FFFFFF;
        position: relative;
        left: 50%;
        margin-top: 0px;
        margin-bottom: 0px;
        font-size: 20px;
    }

    /* Testimonials */
    .testimonials blockquote {
        background: #f8f8f8 none repeat scroll 0 0;
        border: medium none;
        color: #666;
        display: block;
        font-size: 14px;
        line-height: 20px;
        padding: 15px;
        position: relative;
    }

    .testimonials blockquote::before {
        width: 0;
        height: 0;
        right: 0;
        bottom: 0;
        content: " ";
        display: block;
        position: absolute;
        border-bottom: 20px solid #fff;
        border-right: 0 solid transparent;
        border-left: 15px solid transparent;
        border-left-style: inset; /*FF fixes*/
        border-bottom-style: inset; /*FF fixes*/
    }

    .testimonials blockquote::after {
        width: 0;
        height: 0;
        right: 0;
        bottom: 0;
        content: " ";
        display: block;
        position: absolute;
        border-style: solid;
        border-width: 20px 20px 0 0;
        border-color: #e63f0c transparent transparent transparent;
    }

    .testimonials .carousel-info img {
        border: 1px solid #f5f5f5;
        border-radius: 150px !important;
        height: 75px;
        padding: 3px;
        width: 75px;
    }

    .testimonials .carousel-info {
        overflow: hidden;
    }

    .testimonials .carousel-info img {
        margin-right: 15px;
    }

    .testimonials .carousel-info span {
        display: block;
    }

    .testimonials span.testimonials-name {
        color: #e6400c;
        font-size: 16px;
        font-weight: 300;
        margin: 23px 0 7px;
    }

    .testimonials span.testimonials-post {
        color: #656565;
        font-size: 11px;
    }

    #menu ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;

    }

    #menu li {
        float: left;
    }

    #menu li div {
        display: block;
        color: black;
        text-align: center;

        text-decoration: none;
        border: 1px solid #efefef;
    }

    #menu li a:hover {
        cursor: pointer;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input type="text" name="filterDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" value="<?php echo $current_date_filter ?>"
                       data-int="<?php echo $current_date_filter ?>" id="filterDateFrom" class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="filterDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" value="<?php echo $current_date_filter2 ?>"
                       data-int="<?php echo $current_date_filter2 ?>" id="filterDateTo" class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-2">
            <label for="empFilter"><?php echo $this->lang->line('hrms_leave_management_employee_name');?> <!--Employee Name--></label><br>
            <select name="empFilter[]" class="form-control" id="empFilter" multiple="multiple" disabled>
                <?php
                foreach ($employeeDrop as $empD){
                    $selected = ($empD['EIdNo'] == current_userID())? 'selected' : '';
                    echo '<option value="'.$empD['EIdNo'].'" '.$selected.'>'.$empD['ECode'].' - '.$empD['employee'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group col-sm-2">
            <label for="applicationType_filter">Application Type</label><br>
            <div>
                <?php echo form_dropdown('applicationType_filter[]', $applicationType_filter_arr, '', 'class="form-control" id="applicationType_filter" multiple="multiple"'); ?>
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="leaveType_filter">Leave Type</label><br>
            <div>
                <?php echo form_dropdown('leaveType_filter[]', $leaveType_filter_arr, '', 'class="form-control" id="leaveType_filter" multiple="multiple"'); ?>
            </div>
                
        </div>
        <div class="form-group col-sm-3">
            <label for="empFilter">Type</label><br>
            <?php echo form_dropdown('attendee_filter', $attendee_filter_arr, 'myLeave', 'class="form-control" id="attendee_filter" onchange="masterTable.draw()"'); ?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3 pull-right">
            <label for="status"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>
            <div style="width: 60%;">
                <?php echo form_dropdown('status', $filterStatus, '', 'class="form-control" id="status"'); ?></div>
            <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style="margin-top: -10%;">
                <i class="fa fa-times-circle-o"></i>
            </button>
            <button type="button" class="btn btn-primary pull-right" onclick="search_leave_app()" style="margin-top: -9%; margin-left: 250px; position: absolute;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="margin-bottom: 10px" id="divBalance"></div>
</div>
<div class="row">
    <div class="col-md-7">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--> /
                  <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed-->
                    / <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                </td>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_refer_back'); ?><!--Refer-back-->
                </td>
                <td><span class="label label-info">&nbsp;</span> <?php echo $this->lang->line('common_canceled');?><!--Canceled--> </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-3 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_leaveForm()">
            <i class="fa fa-plus"></i>
          <?php echo $this->lang->line('hrms_leave_management_new_leave'); ?><!--New Leave-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="leaveDetailTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('hrms_leave_management_document_code'); ?><!--Document Code--></th>
            <th style="min-width: 23%"> <?php echo $this->lang->line('hrms_leave_management_employee_name'); ?><!--Employee Name--></th>
            <th style="min-width: 12%"> <?php echo $this->lang->line('hrms_leave_management_leave_type'); ?><!--Leave Type--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('hrms_leave_management_application_type'); ?><!--Application Type--></th>
            <th style="min-width: 7%"><?php echo $this->lang->line('common_from'); ?><!--From--></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_to'); ?><!--To--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>

<div class="modal fade" id="newLeave_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <?php echo form_open_multipart('', 'id="empNewLeave" class="horizontal"'); ?>
            <input type="hidden" name="leaveMasterID" id="leaveMasterID" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                  <?php echo $this->lang->line('hrms_leave_management_employee_leave_application'); ?><!--Employee Leave Application-->
                    <span id="leaveCode"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row" style="margin-bottom: 3px">
                        <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                            <div class="col-xs-4 col-lg-3 col-md-4 col-sm-3">
                                <label><?php echo $this->lang->line('hrms_leave_management_application_type'); ?><!--Application Type--> </label>
                            </div>
                            <div class="col-xs-7 col-lg-4 col-md-4 col-sm-4">
                                <select id="applicationType" name="applicationType" class="form-control frm_input select2" onchange="change_applicationType()" required>
                                    <option value="1" >Apply Leave</option>
                                    <option value="2">Leave Plan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 3px">
                        <hr style="margin: 8px">
                    </div>
                    <div class="row" style="margin-bottom: 3px">
                        <div class="col-xs-4 col-sm-2">
                            <label><?php echo $this->lang->line('hrms_leave_management_employee_name'); ?><!--Employee Name--></label>
                        </div>
                        <div class="col-xs-7 col-sm-4">
                            <select id="empName" name="empName" class="form-control empName frm_input select2" required onchange="getEmpDet(this)">
                                <option data-leaveGroupID="" data-designation="-" data-policy="" data-ecode="-" value=""></option>
                                <?php if ($employeeDrop) {
                                foreach ($employeeDrop as $value) {
                                //   if ((current_userID() == $value['EIdNo'])) {

                                    echo "<option date-leaveGroupID='" . $value['leaveGroupID'] . "'  data-policy='" . $value['policyMasterID'] . "' data-designation='" . $value['DesDescription'] . "' data-ecode='" . $value['ECode'] . "' data-department='" . $value['DepartmentDes'] . "' value='" . $value['EIdNo'] . "'>" . $value['employee'] . "</option>";
                                  //}
                                }
                              } ?>
                            </select>

                            <input type="hidden" name="empID" class="form-control frm_input" id="empID"/>
                        </div>

                        <div class="col-xs-4 col-sm-3"><label>
                            <?php echo $this->lang->line('common_date'); ?><!--Date--></label></div>
                        <div class="col-xs-7 col-sm-3">
                            <span id="dateSpan" class="frm_input"><?php echo $current_date ?></span>
                            <input type="hidden" name="entryDate" id="entryDate"
                                   data-value="<?php echo $current_date ?>">
                        </div>


                    </div>
                    <div class="row" style="">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                              <?php echo $this->lang->line('hrms_leave_management_employee1_code'); ?><!--Employee Code--></label>
                        </div>
                        <div class="col-xs-7 col-sm-4"><span id="empCodeSpan" class="frm_input"></span></div>

                        <div class="col-xs-4 col-sm-3"><label>
                            <?php echo $this->lang->line('common_designation'); ?><!--Designation--></label></div>
                        <div class="col-xs-7 col-sm-3"><span id="designationSpan" class="frm_input"></span></div>
                    </div>
                    <div class="row" style="margin-bottom: 3px">

                        <div class="col-xs-4 col-sm-2">
                            <label><?php echo $this->lang->line('common_department'); ?><!--Department--></label>
                        </div>
                        <div class="col-xs-7 col-sm-4" id="">
                            <span id="department" class="frm_input"> - </span>
                        </div>

                        <!-- Add the extra clearfix for only the required viewport -->


                        <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_leave_management_reporting_manager'); ?><!--Reporting Manager--></label></div>
                        <div class="col-xs-7 col-sm-3">
                            <span id="reportingManager" class="frm_input">-</span>
                        </div>

                    </div>
                    <div class="row" style="margin-bottom: 3px">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                              <?php echo $this->lang->line('hrms_leave_management_please_select__a_type'); ?><!--Leave Type--></label>
                        </div>
                        <div class="col-xs-7 col-sm-4" id="leaveTypeDropDown">
                            <select id="leaveTypeID" name="leaveTypeID" class="form-control leaveType frm_input "
                                    required>
                                <option></option>
                            </select>
                        </div>
                        <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_leave_management_date_of_Join'); ?><!--Date of Join--></label></div>
                        <div class="col-xs-7 col-sm-3">
                            <span id="dateofJoin" class="frm_input">-</span>
                        </div>
                    </div>
                    
                    <div class="row hide" style="margin-bottom: 3px" id="leaveReasonSection">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                              <?php echo $this->lang->line('hrms_leave_management_leave_reason'); ?></label>
                        </div>
                        <div class="col-xs-7 col-sm-4 hide" id="leaveReasonDropDown">
                            <select id="leaveReasonID" name="leaveReasonID" class="form-control frm_input">
                                <option></option>
                            </select>
                            <!-- <input type="text" name="leaveReasonID" id="leaveReasonID" value=""> -->
                        </div>
                        <div class="col-xs-7 col-sm-4" id="leaveReasonText">
                            <input type="text" name="leaveReasonText" id="leaveReason" value="">
                        </div>
                    </div>

                    <div class="row hide" style="margin-bottom: 3px" id="leaveAnnualCommentSection">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                              <?php echo $this->lang->line('hrms_leave_management_contact_details'); ?></label>
                        </div>
                       
                        <div class="col-xs-7 col-sm-4" id="leaveReasonText">
                            <textarea name="annualComment" id="annualComment" rows="4"></textarea>
                            <!-- <input type="text" name="annualComment" id="annualComment" value=""> -->
                        </div>
                    </div>

                    <div class="row" style="margin-top: 8px">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                                <?php echo $this->lang->line('hrms_leave_management_covering_emp'); ?><!--Covering Employee--></label>
                        </div>
                        <input type="hidden" name="coveringValidated" id="coveringValidated" value="0">
                        <input type="hidden" name="coveringAvailabilityValidated" id="coveringAvailabilityValidated" value="0">
                        <div class="col-xs-7 col-sm-4" id="coveringEmpID_div">
                            <select id="coveringEmpID" name="coveringEmpID" class="form-control coveringEmp frm_input " required>
                                <option></option>
                            </select>
                        </div>

                        <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('common_notify_to');?></label></div>
                        <div class="col-xs-7 col-sm-3">
                            <select name="notify-list[]" id="notify-list" class="form-control" multiple="multiple" required>
                                <?php
                                foreach ($employee_arr as $item){
                                    echo '<option value="'.$item['EIdNo'].'">'.$item['ECode'].' - '.$item['Ename2'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row hidden" style="margin-top:10px;" id="getTravelDiv">
                        <div class="col-xs-4 col-sm-2">
                            <label>
                              <?php echo $this->lang->line('common_get_travel_request'); ?><!--Travel Request--></label>
                        </div>
                        <div class="col-xs-7 col-sm-4" >
                            <select name="getTravel" id="getTravel" class="form-control">
                                <option value="">select</option>
                                <option value="1">yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="emp_leave_page">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="testimonials">
                                <div class="active item">
                                    <blockquote><p>
                                        <?php echo $this->lang->line('hrms_leave_management_please_select_an_employee_and_leave_type_continue'); ?><!--Please select an employee to continue--></p>
                                    </blockquote>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <input type="hidden" name="isConfirmed" id="isConfirmed" value="0">
                <button onclick="submitform(this)" type="button" class="btn btn-primary btn-sm submitBtn proceedBtn"
                        data-value="0"
                        data-fn="save"><?php echo $this->lang->line('common_save'); ?><!--Save-->
                </button>
                <button onclick="submitform(this)" type="button" class="btn btn-primary btn-sm submitBtn proceedBtn confirmBtn"
                        data-value="1"
                        data-fn="save"><?php echo $this->lang->line('common_save_and_confirm'); ?><!--Save & Confirm-->
                </button>
                <button onclick="submitform(this)" type="button" class="btn btn-primary btn-sm updateBtn proceedBtn"
                        data-value="0"
                        data-fn="update"><?php echo $this->lang->line('common_update'); ?><!--Update-->
                </button>
                <button onclick="submitform(this)" type="button" class="btn btn-primary btn-sm updateBtn proceedBtn confirmBtn"
                        data-value="1"
                        data-fn="update">
                  <?php echo $this->lang->line('common_update_and_Confirm'); ?><!--Update & Confirm-->
                </button>
                <button onclick="" type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                  <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                </button>
            </div>
          <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveBalanceHistory" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="leavehistory">
                  <?php echo $this->lang->line('hrms_leave_management_leave_balance'); ?><!--Employee Leave Application-->
                    <span id="leaveCode"></span>
                </h5>
            </div>

            <div class="modal-body">
            <div id="divleaveBalanceHistory">

            </div>

            </div>
            <div class="modal-footer">

                <button onclick="" type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                  <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                </button>
            </div>
          <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="leave_cancellation_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Leave Cancellation
                </h4>
            </div>
            <form class="form-horizontal" id="leave_cancellation_form">
                <div class="modal-body">
                    <div class="panel-body" style="padding: 0px;padding-left: 15px;"><h4 ><?php echo $this->lang->line('hrms_payroll_decu_code');?>
                            <!--Document Code--> - <span id="leaveCode_cancel"></span> </h4></div>
                    <div class="panel-body">
                        <div class="row" style="margin-bottom: 3px">
                            <div class="col-xs-4 col-sm-2"><label ><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></label></div>
                            <div class="col-xs-7 col-sm-4">: <span id="empNameSpan_cancel" class="frm_input"></span></div>

                            <div class="col-xs-4 col-sm-3"><label ><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Employee Code--></label></div>
                            <div class="col-xs-7 col-sm-3">: <span id="empCodeSpan_cancel" class="frm_input"></span></div>
                        </div>

                        <div class="row" style="">
                            <div class="col-xs-4 col-sm-2"><label><?php echo $this->lang->line('hrms_payroll_designation');?><!--Designation--></label></div>
                            <div class="col-xs-7 col-sm-4">: <span id="designationSpan_cancel" class="frm_input"></span></div>

                            <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('common_date');?><!--Date--></label></div>
                            <div class="col-xs-7 col-sm-3">: <span id="dateSpan_cancel" class="frm_input"></span>
                            </div>
                        </div>

                        <div class="row" style="">
                            <div class="col-xs-4 col-sm-2"> <label ><label><?php echo $this->lang->line('hrms_payroll_leave_type');?><!--Leave Type--></label></div>
                            <div class="col-xs-7 col-sm-4">
                                : <span id="leaveTypeSpan_cancel" class="frm_input"></span>
                            </div>

                            <div class="col-xs-4 col-sm-3"> <label ><?php echo $this->lang->line('hrms_payroll_leave_no_of_days');?><!--No. of Days--></label></div>
                            <div class="col-xs-7 col-sm-3">: <span id="days_cancel" class="frm_input"></span></div>
                        </div>


                        <div class="row" style="">
                            <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_leave_starting_date');?><!--Start Date--></label></div>
                            <div class="col-xs-7 col-sm-4">: <span id="startDateSpan_cancel" class="frm_input"></span></div>

                            <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_payroll_leave_ending_date');?><!--End Date--></label></div>
                            <div class="col-xs-7 col-sm-3">: <span id="endDateSpan_cancel" class="frm_input"></span></div>
                        </div>

                        <div class="row" style="">
                            <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Comment--></label></div>
                            <div class="col-xs-7 col-sm-4">: <span id="commentSpan_cancel" class="frm_input"></span></div>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group approved">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <input type="hidden" name="cancelID" id="cancelID">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm approved"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    toastr.clear();
    $('.empName').select2();
    var masterTable;
    var newLeave_modal = $('#newLeave_modal');
    var submitBtn = $('.submitBtn');
    var updateBtn = $('.updateBtn');
    Inputmask().mask(document.querySelectorAll("input"));
    leaveBalance();

    $(document).ready(function() {
        /*Filter panel expand in page load*/
        $('#filter-panel').addClass('in');

        var cancelID = $('#cancelID').val();

        $('#empFilter, #notify-list, #applicationType_filter, #leaveType_filter').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 1,
            buttonWidth: '180px'
        });

        $('#leave_cancellation_form').bootstrapValidator({
                live            : 'enabled',
                message         : 'This value is not valid.',
                excluded        : [':disabled'],
                fields          : {
                    comments     : {validators : {notEmpty:{message:'Comments field is required.'}}}
                },
            })
            .on('success.form.bv', function(e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();

                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : data,
                    url :"<?php echo site_url('Employee/cancel_leave'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's') {
                            $("#leave_cancellation_modal").modal('hide');
                            setTimeout(function () {
                                fetchPage('system/hrm/employee_leave_application', cancelID, 'HRMS')
                            }, 300);
                        }

                    },error : function(){
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            });
    });


    $('.headerclose').click(function () {
        fetchPage('system/hrm/employee_leave_application', 'Test', 'HRMS');
    });

    window.masterTable = $('#leaveDetailTB').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Employee/fetch_employee_leave'); ?>",
        "aaSorting": [[1, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = parseFloat('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;

            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                if (parseFloat(oSettings.aoData[x]._aData['leaveMasterID']) == selectedRowID) {
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');
                }

                x++;
            }
        },
        "aoColumns": [
            {"mData": "leaveMasterID"},
            {"mData": "documentCode"},
            //{"mData": "ECode"},
            {"mData": "empName"},
            {"mData": "description"},
            {"mData": "appDes"},
            {"mData": "startDate"},
            {"mData": "endDate"},
            {"mData": "confirm"},
            {"mData": "approved"},
            {"mData": "action"}
        ],
        "columnDefs": [{"targets": [0,7,8,9], "searchable": false}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name:'filterDateFrom', value: $('#filterDateFrom').val()});
            aoData.push({name:'filterDateTo', value: $('#filterDateTo').val()});
            aoData.push({"name": "empFilter[]", "value": <?php echo current_userID() ?>});
            aoData.push({"name": "attendee_filter", "value": $('#attendee_filter').val()});
            aoData.push({name:'status', value: $('#status').val()});
            aoData.push({name:'applicationType_filter', value: $('#applicationType_filter').val()});
            aoData.push({name:'leaveType_filter', value: $('#leaveType_filter').val()});
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        }
    });

    function loadleaveEmployees() {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/leaveApplicationEmployee_self'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'empID':<?php echo current_userID() ?>},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#empName').empty();

                var select = $('#empName');
                /*        var option = $('<option></option>').attr('selected', true).attr('data-leaveGroupID', '').attr('data-designation', '').attr('data-policy', '').attr('data-ecode', '').text('Please select').val('');
                        option.appendTo(select);*/
                $.each(data, function (index, value) {
                    var option = $('<option></option>').attr('data-manager', value['manager']).attr('data-DateAssumed', value['DateAssumed']
                    ).attr('data-department', value['department']).attr('data-leaveGroupID', value['leaveGroupID']).attr('data-designation', value['DesDescription']).attr('data-policy', value['policyMasterID']).attr('data-ecode', value['EmpSecondaryCode']).text(value['ECode']+' | '+value['employee']).val(value['EIdNo']);
                    option.appendTo(select);
                });
                $('#empName').val('<?php echo current_userID(); ?>').change();

                stopLoad();
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }

    function leaveBalance() {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/loadLeaveBalance'); ?>",
            type: 'post',
            dataType: 'html',
            data: {},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#divBalance').html(data);

                stopLoad();
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }

    function leaveBalanceModal(balance, leaveTypeID) {
        $('#leaveBalanceHistory').modal('show');
        $('#leavehistory').html(balance+' <span id="balance-span"> </span>');
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/loadLeaveBalanceHistory'); ?>",
            type: 'post',
            dataType: 'html',
            data: {'leaveTypeID': leaveTypeID},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#divleaveBalanceHistory').html(data);

                stopLoad();
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }

    function getEmpDet(obj) {
        var element = $(obj).find('option:selected');
        var designation = element.attr("data-designation");
        var ecode = element.attr("data-ecode");
 /*       var policyMasterID = element.attr("data-policyMasterID");*/
        var leaveGroupID = element.attr("date-leaveGroupID");
        var empID = element.val();

        var manager  = element.attr("data-manager");
        var DateAssumed  = element.attr("data-DateAssumed");
        var department = element.attr("data-department");

        $('#empCodeSpan').html(ecode);
        $('#designationSpan').html(designation);
        $('#reportingManager').html(manager);
        $('#dateofJoin').html(DateAssumed);
        $('#department').html(department);


        loadLeaveTypeDropDown(empID);
        /*default */
        //  load_leave_page(empID, policyMasterID, leaveGroupID);


    }

    function get_covering_employee_list(coveringEmp = 0, confirmedYN = 0){

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/get_covering_employee_list'); ?>",
            type: 'post',
            dataType: 'html',
            data: {'empID':$('#empName').val(), coveringEmp:coveringEmp, confirmedYN:confirmedYN},
            beforeSend: function () {

            },
            success: function (data) {
                $('#coveringEmpID_div').html(data);
                $('#coveringEmpID').select2();

            }, error: function () {
                myAlert('e', 'Error in covering employee loading');
            }
        });
    }

    function getemplate(obj) {
        leave = $(obj).find('option:selected');
        isCalenderDays = leave.attr("data-isCalenderDays");
        isAllowminus = leave.attr("data-isAllowminus");
        leaveTypeID = leave.val();
        element = $('#empName').find('option:selected');
        empID = element.val();
        policyMasterID = leave.attr("data-policyMasterID");
        leaveGroupID = leave.attr("data-leavegroupid");
        leaveTypeID = leave.attr("data-leaveTypeID");

        load_leave_page(empID, policyMasterID, leaveGroupID, leaveTypeID, 1);

    }


    function loadLeaveTypeDropDown(empID, confirmedYN=null) {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/loadLeaveTypeDropDown'); ?>",
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, confirmedYN: confirmedYN},
            beforeSend: function () {
                /*  startLoad();*/
            },
            success: function (data) {
                $('#leaveTypeDropDown').html(data);
                $('#leaveTypeID').select2();
                /*   stopLoad();*/
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }

    function openLeaveDetails(id, code) {
        /*drowpdown*/
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/employeeLeave_details'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            beforeSend: function () {
                startLoad();
                new_leaveFormclean();

            },
            success: function (data) {
                var empDet = data['empDet'];
                var leaveDet = data['leaveDet'];
                var entitleDet = data['entitleDet'];
                let notify_list = $('#notify-list');
                notify_list.multiselect2('destroy');

                if($.isEmptyObject(entitleDet)){
                    entitleDet = {balance : 0 };
                }

                $("#empName").removeAttr("onchange");
                /*remove onchange*/
                $('#empName').empty();
                /*remove option*/
                var select = $('#empName');
                /* appened */
                var option = $('<option></option>').attr('selected', true).attr('data-manager', empDet['manager']).attr('data-DateAssumed', empDet['DateAssumed']
                ).attr('data-department', empDet['department']).attr('data-leaveGroupID', leaveDet['leaveGroupID']).attr('data-designation', empDet['DesDescription']).attr('data-ecode', empDet['EmpSecondaryCode']).text(empDet['ECode']+' | '+empDet['employee']).val(empDet['EIdNo']);
                option.appendTo(select);

                var designation = empDet['DesDescription'];
                var ecode = empDet['ECode'];
                var policyMasterID = leaveDet['policyMasterID'];
                var leaveGroupID = leaveDet['leaveGroupID'];
                var empID = empDet['EIdNo'];
                var leaveTypeID = leaveDet['leaveTypeID'];

                $('#applicationType').val(leaveDet['applicationType']);
                $('#department').html(empDet['department']);
                $('#reportingManager').html(empDet['manager']);
                $('#dateofJoin').html(empDet['DateAssumed']);
                $('#empCodeSpan').html(ecode);
                $('#designationSpan').html(designation);
                $('#coveringValidated, #coveringAvailabilityValidated').val(0);
                $('#getTravel').val(leaveDet['isTravelRequest']).change();
                if(leaveDet['isAnnualLeave']==1){
                    $('#getTravelDiv').removeClass('hidden');
                }else{
                    $('#getTravel').val('').change();
                }

                /*load dropDown*/
                $('#empName').attr('onchange', 'getEmpDet(this)');
                /*initate onchange*/
                loadLeaveTypeDropDown(empDet['EIdNo'], leaveDet['confirmedYN']);
                load_leave_page(empID, policyMasterID, leaveGroupID, leaveTypeID, 0);
                get_covering_employee_list(leaveDet['coveringEmpID'], leaveDet['confirmedYN']);

               
                $('#dateSpan').html(leaveDet['entryDate']);
                $('#entryDate').val(leaveDet['entryDate']);


                setTimeout(function () {
                    $('.comment').val(leaveDet['comments']);
                    $("#leaveTypeID").removeAttr("onchange");
                    $('#leaveTypeID').val(leaveDet['leaveTypeID']).change();

                    $('#leaveReasonSection').addClass('hide');
                    $('#leaveAnnualCommentSection').addClass('hide');

                    if (leaveDet['approvedYN'] == 1) {  /*if approved set leaveavailable column leave master*/
                        entitleDet['balance'] = leaveDet['leaveAvailable'];
                    }

                    //add comment
                    $('#annualComment').val(leaveDet['annualComment']);
                    $('#leaveReason').val(leaveDet['leaveReasonText']);


                    if (policyMasterID == 2) {
                        $('#startDatetime').val(leaveDet['startDate']);
                        $('#endDatetime').val(leaveDet['endDate']);

                        $('#workingDaysHide').addClass('hide');
                        $('#takenSpan').text(leaveDet['days']);

                        balance = entitleDet['balance'];
                        entitleSpan.text(display(entitleDet['balance']));

                        appliedLeave = leaveDet['hours'];

                        leavebalance = entitleDet['balance'] - leaveDet['hours'];


                        takenSpan.text(display(appliedLeave));
                        balanceSpan.text(display(leavebalance));
                    }
                    else {
                        $('#startDate').val(leaveDet['startDate']);
                        $('#endDate').val(leaveDet['endDate']);

                        if (leaveDet['ishalfDay'] == 1) {
                            $('#halfDay').prop('checked', true);
                            show_shieft_field()
                        }
                        $('#shift').val(leaveDet['shift']);
                        isCalenderDays = leaveDet['isCalenderDays'];

                        $('#takenSpan').text(leaveDet['days']);
                        $('#entitleSpan').text(entitleDet['balance']);
                        bal = parseFloat(entitleDet['balance']) - parseFloat(leaveDet['days']);
                        if (bal != parseInt(bal)) {
                            bal = bal.toFixed(1);
                        }
                        if (leaveDet['isCalenderDays'] == 1) {


                            $('#workingDaysHide').addClass('hide');
                            $('#takenSpan').text(leaveDet['days']);

                        } else {
                            $('#workingDaysHide').removeClass('hide');
                            $('#workingDaysSpan').text(leaveDet['workingDays']);
                            $('#takenSpan').text(leaveDet['nonWorkingDays']);

                        }


                        $('#balanceSpan').html(bal);
                        leaveTypeID = leaveDet['leaveTypeID'];
                    }
                    $('#leaveCode').text(' [ ' + code + ' ] ');
                    $('#leaveMasterID').val(id);

                    if(leaveDet['confirmedYN'] ==3 ){
                        $('#confirmedBy').html('<b>Referred back by : </b>'+leaveDet['approvedbyEmpName']);
                        $('#approvedBy').html('<b> Comment: </b>'+leaveDet['approvalComments']);
                    }

                    else if(leaveDet['confirmedYN'] ==1){
                        $('#confirmedBy').html('<b>Confirmed By : </b>'+leaveDet['confirmedByName']+' &nbsp; On : '+leaveDet['confirmedDate']);
                    }
                    if(leaveDet['approvedYN'] ==1){
                        $('#approvedBy').html('<b>Approved By : </b>'+leaveDet['approvedbyEmpName']+' &nbsp; On : '+leaveDet['approvedDate']);
                    }

                    submitBtn.hide();
                    updateBtn.show();

                    if (leaveDet['confirmedYN'] == 1) {
                        $('.frm_input').attr('disabled', true);
                        $('.comment').attr('disabled',true);
                        updateBtn.hide();
                    }
                    $('#leaveTypeID').attr('onchange', 'getemplate(this)');

                    getLeave_summary(empID,leaveTypeID,policyMasterID,1);

                    if(leaveDet['confirmedYN'] != 1){
                        validateCalender();
                    }

                    notify_list.val(data['notifyListEmpID']);
                    notify_list.multiselect2({
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 1,
                        buttonWidth: '180px'
                    });
                    Leaveattachment_modal(id, 'Leave Management', 'LA', leaveDet['confirmedYN']);

                    if( leaveDet['applicationType'] == 2 ){
                        $('.confirmBtn').hide();
                    }
                    stopLoad();
                    /*initiate onchange*/
                }, 1200);


            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function cancel_leave(cancelID, des) {
        $('#leave_cancellation_form').bootstrapValidator('resetForm', true);
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employeeLeave_detailsOnApproval') ?>',
            data: {'masterID': cancelID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#leaveApprove_modal").modal({backdrop: "static"});
                $('#comments').val('');
                var empDet = data['empDet'];
                var leaveDet = data['leaveDet'];
                var entitleDet = data['entitleDet'];

                $('#leaveCode_cancel').text(leaveDet['documentCode']);
                $('#empNameSpan_cancel').text( empDet['ECode']+" | "+empDet['employee'] );
                $('#empCodeSpan_cancel').text(empDet['EmpSecondaryCode']);
                $('#designationSpan_cancel').text(empDet['DesDescription']);
                $('#leaveTypeSpan_cancel').text(leaveDet['description']);

                $('#days_cancel').text(leaveDet['days']);
                $('#startDateSpan_cancel').text(leaveDet['startDate']);
                $('#endDateSpan_cancel').text(leaveDet['endDate']);
                $('#commentSpan_cancel').text(leaveDet['comments']);
                $('#dateSpan_cancel').text(leaveDet['entryDate']);

                $('#cancelID').val(cancelID);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
        $('#leave_cancellation_modal').modal({backdrop: "static"});
    }

    function change_applicationType(){
        var empID = $('#empName').val();
        var leave = $('#leaveTypeID').find('option:selected');
        var policyMasterID = leave.attr("data-policymasterid");
        var leaveGroupID = leave.attr("data-leavegroupid");
        var leaveTypeID = leave.attr("data-leaveTypeID");
        var showYN = 1;
        var isDateSet = 1;


        load_leave_page(empID, policyMasterID, leaveGroupID, leaveTypeID, showYN, isDateSet);
    }

    function xxopenLeaveDetails(id, code) {
        /*drowpdown*/
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/employeeLeave_details'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            beforeSend: function () {
                startLoad();
                new_leaveFormclean();

            },
            success: function (data) {
                stopLoad();

                var empDet = data['empDet'];
                var leaveDet = data['leaveDet'];
                var entitleDet = data['entitleDet'];
                $('#empName').val(leaveDet['empID']).change();


                setTimeout(function () {

                    $("#leaveTypeID").removeAttr("onchange");
                    $('#leaveTypeID').val(leaveDet['leaveTypeID']).change();
                    $('#leaveTypeID').attr('onchange', 'getemplate(this)');

                    element = $('#empName').find('option:selected');
                    leave = $('#leaveTypeID').find('option:selected');
                    empID = element.val();
                    policyMasterID = leave.attr("data-policymasterid");
                    leaveGroupID = leave.attr("data-leavegroupid");
                    leaveTypeID = leave.attr("data-leaveTypeID");
                    load_leave_page(empID, policyMasterID, leaveGroupID, leaveTypeID, 0);


                }, 1500);

                setTimeout(function () {
                    $('.comment').val(leaveDet['comments']);
                    $('#dateSpan').html(leaveDet['entryDate']);
                    $('#entryDate').val(leaveDet['entryDate']);
                    if (leaveDet['approvedYN'] == 1) {  /*if approved set leaveavailable column leave master*/
                        entitleDet['balance'] = leaveDet['leaveAvailable'];
                    }

                  
                    $('#leaveTypeID').val(leaveDet['leaveTypeID']);

                    if (policyMasterID == 2) {
                        $('#startDatetime').val(leaveDet['startDate']);
                        $('#endDatetime').val(leaveDet['endDate']);

                        $('#workingDaysHide').addClass('hide');
                        $('#takenSpan').text(leaveDet['days']);

                        balance = entitleDet['balance'];
                        entitleSpan.text(display(entitleDet['balance']));

                        appliedLeave = leaveDet['hours'];

                        leavebalance = entitleDet['balance'] - leaveDet['hours'];


                        takenSpan.text(display(appliedLeave));
                        balanceSpan.text(display(leavebalance));
                    }
                    else {
                        $('#startDate').val(leaveDet['startDate']);
                        $('#endDate').val(leaveDet['endDate']);

                        if (leaveDet['ishalfDay'] == 1) {
                            $('#halfDay').prop('checked', true);
                            show_shieft_field()
                        }
                        $('#shift').val(leaveDet['shift']);
                        isCalenderDays = leaveDet['isCalenderDays'];

                        $('#takenSpan').text(leaveDet['days']);
                        $('#entitleSpan').text(entitleDet['balance']);
                        bal = parseFloat(entitleDet['balance']) - parseFloat(leaveDet['days']);
                        if (bal != parseInt(bal)) {
                            bal = bal.toFixed(1);
                        }
                        if (leaveDet['isCalenderDays'] == 1) {


                            $('#workingDaysHide').addClass('hide');
                            $('#takenSpan').text(leaveDet['days']);
                        } else {

                            $('#workingDaysHide').removeClass('hide');
                            $('#workingDaysSpan').text(leaveDet['workingDays']);
                            $('#takenSpan').text(leaveDet['nonWorkingDays']);
                        }


                        $('#balanceSpan').html(bal);
                        leaveTypeID = leaveDet['leaveTypeID'];
                    }
                    $('#leaveCode').text(' [ ' + code + ' ] ');
                    $('#leaveMasterID').val(id);
                    submitBtn.hide();
                    updateBtn.show();
                    if (leaveDet['confirmedYN'] == 1) {
                        $('.frm_input').attr('disabled', true);
                        updateBtn.hide();
                    }
                    stopLoad();

                }, 2800);


            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function refer_leave(refID, des) {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?> [ " + des + " ]!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_refer_back');?> ",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/refer_back_empLeave'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'refID': refID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            newLeave_modal.modal('hide');
                            setTimeout(function () {

                                fetchPage('system/hrm/employee_leave_application', refID, 'HRMS')
                            }, 300);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function refer_leave_cancellation(refID, des) {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back_cancellation');?> [ " + des + " ]!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_refer_back');?> ",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/refer_back_empLeave_cancellation'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'refID': refID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            newLeave_modal.modal('hide');
                            setTimeout(function () {
                                fetchPage('system/hrm/employee_leave_application', refID, 'HRMS')
                            }, 300);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function delete_leave(delID, des) {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/delete_empLeave'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'deleteID': delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            newLeave_modal.modal('hide');
                            setTimeout(function () {
                                masterTable.ajax.reload();
                                leaveBalance();
                            }, 300);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function load_leave_page(empID, policyMasterID, leaveGroupID, leaveTypeID, showYN, isDateSet=0) {
        var applicationType = $('#applicationType').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employee_leave_page') ?>',
            data: {
                applicationType: applicationType,
                empID: empID,
                policyMasterID: policyMasterID,
                leaveGroupID: leaveGroupID,
                leaveTypeID: leaveTypeID,
                showYN: showYN
            },
            dataType: 'html',
            beforeSend: function () {
               /* startLoad();*/
            },
            success: function (data) {
                /*stopLoad();*/
                if (empID != '') {
                    $('.proceedBtn').removeClass('hide');
                }
                $('#emp_leave_page').html(data);

                if(isDateSet == 1){ // isDateSet will be one only the application type change
                    setTimeout(function(){
                        $('#startDate').val( startDate );
                        $('#endDate').val( endDate).change();
                    }, 100);
                }

                if(applicationType == 1){
                    var masterID = $.trim($('#leaveMasterID').val());
                    var confirmHideBtns = (masterID == '') ? 'updateBtn' : 'submitBtn';
                    $('.confirmBtn:not(.'+confirmHideBtns+')').show();
                }else{
                    $('.confirmBtn').hide();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function new_leaveForm() {
        let notify_list = $('#notify-list');
        notify_list.multiselect2('destroy');
        notify_list.val(0);
        var formInputs = $('#empNewLeave input,#empNewLeave select, #empNewLeave textarea');
        formInputs.prop('value', '');
        formInputs.prop('disabled', false);
        notify_list.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 1,
            buttonWidth: '180px'
        });

        loadleaveEmployees();
        load_leave_page('<?php echo current_userID(); ?>', 0, 0, 0, 0);
        // $('.frm_input:not(#leaveType)').text('-');
        $('#isConfirmed').val(0);

        $('#leaveCode').text('');
        $('#startDate').val('');
        $('#endDate').val('');
        $('#getTravel').val('').change();
        $('.frm_input').attr('disabled', false);
        $('.proceedBtn').addClass('hide');
        var d = new Date();
        var currDate = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
        $('#dateSpan').html(currDate);
        $('#entryDate').val(currDate);
        $('#coveringValidated, #coveringAvailabilityValidated').val(0);
        $('#confirmedBy').html('');
        // $('#getTravel').prop('checked', false);
        $('#approvedBy').html('');
        $('#empCodeSpan').html('');
        $('#department').html('');
        $('#designationSpan').html('');
        $('#reportingManager').html('');
        $('#dateofJoin').html('');
        $('#empName').val('<?php echo current_userID(); ?>').change();

        get_covering_employee_list();

        newLeave_modal.modal({backdrop: 'static'});
        submitBtn.show();
        updateBtn.hide();

        $('#leaveReasonSection').addClass('hide');
        $('#leaveAnnualCommentSection').addClass('hide');

    }

    function new_leaveFormclean() {
        var formInputs = $('#empNewLeave input,#empNewLeave select, #empNewLeave textarea');
        formInputs.prop('value', '');
        formInputs.prop('disabled', false);
        $("#empName").removeAttr("onchange");
        $('#empName').val('').change();
        $('#getTravel').val('').change();
        $('#empName').attr('onchange', 'getEmpDet(this)');
        // $('.frm_input:not(#leaveType)').text('-');
        $('#isConfirmed').val(0);
        $('#leaveCode').text('');
        $('#startDate').val('');
        $('#endDate').val('');
        $('.frm_input').attr('disabled', false);
        $('.proceedBtn').addClass('hide');

        newLeave_modal.modal({backdrop: 'static'});
        submitBtn.show();
        updateBtn.hide();
        $('#entitleSpan').text('-');
        $('#takenSpan').text('-');
        $('#balanceSpan').text('-');
        $('#no_of_days').text('-');
        $('#workingDaysSpan').text('-');
        $('#confirmedBy').html('');
        $('#approvedBy').html('');
    }

    function overlay_show() {
        $('.overlay').show();
    }

    function overlay_hide() {
        setTimeout(function () {
            $('.overlay').hide();
        }, 300);
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function search_leave_app(){
        var filterDateFrom = $('#filterDateFrom').val();
        var filterDateTo = $('#filterDateTo').val();

        var txt = '';
        if( isDateInputMaskNotComplete(filterDateFrom) ){
            txt = 'Date from is incomplete<br/>';
        }

        if( isDateInputMaskNotComplete(filterDateTo) ){
            txt += 'Date to is incomplete';
        }

        if(txt != ''){
            myAlert('e', txt);
            return false;
        }

        masterTable.draw()
    }

    function clear_all_filters(){
        var filterDateFrom = $('#filterDateFrom').attr('data-int');
        var filterDateTo = $('#filterDateTo').attr('data-int');

        $('#filterDateFrom').val(filterDateFrom);
        $('#filterDateTo').val(filterDateTo);

        $('#status').val('all');

        $('#applicationType_filter').val([]).trigger('change');
        $('#applicationType_filter').multiselect2('refresh');

        $('#leaveType_filter').val([]).trigger('change');
        $('#leaveType_filter').multiselect2('refresh');

        setTimeout(function(){
            search_leave_app();
        }, 150);
    }

    function isAnnual() {
        var leave = $('#leaveTypeID').val();
        $.ajax({
            method:'post',
            url:'<?php echo site_Url('Employee/isAnnualLeave') ?>',
            data:{id:leave},
            beforeSend:function(){
                // startLoad();
            },
            success:function(data){
                var response = JSON.parse(data);

                if (response.isAnnualLeave == 1) {
                    $('#getTravelDiv').removeClass('hidden');
                } else {
                    $('#getTravelDiv').addClass('hidden');
                }
            },
            error:function(){
                myAlert('e','Something went worng');
            }
        });
    }


</script>

