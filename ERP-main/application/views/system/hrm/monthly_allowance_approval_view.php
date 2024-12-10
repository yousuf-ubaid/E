<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$current_date = format_date($this->common_data['current_date']);

$leaveTypeArr = leavetype_bygroup($leaveGroupID);
$half_day_show = ($leaveDet['ishalfDay'] == 1)? '': 'hidden';
?>

    <div class="panel-body">
        <div class="row" style="margin-bottom: 3px; text-align: center;">
            <h3 style="margin: 1px !important;"><?php echo $this->lang->line('common_document_code'); ?> - <?php echo $leaveDet['documentCode'];?></h3>
        </div>
        <div class="row" style="margin-bottom: 3px">
            <hr style="margin: 8px">
        </div>
        <div class="row" style="margin-bottom: 3px">
            <div class="col-xs-4 col-sm-2">
                <label><?php echo $this->lang->line('hrms_leave_management_employee_name'); ?><!--Employee Name--> </label>
            </div>
            <div class="col-xs-7 col-sm-4">
                <select id="empName" name="empName" class="form-control empName frm_input select2" disabled>
                    <option value="<?php echo $empDet['EIdNo'] ?>"><?php echo $empDet['ECode'].' | '.$empDet['employee'] ?></option>
                </select>
            </div>

            <div class="col-xs-4 col-sm-3"><label> <?php echo $this->lang->line('common_date'); ?><!--Date--></label></div>
            <div class="col-xs-7 col-sm-3">
                <span id="dateSpan" class="frm_input"><?php echo $leaveDet['entryDate'] ?></span>
            </div>
        </div>
        <div class="row" style="">
            <div class="col-xs-4 col-sm-2">
                <label> <?php echo $this->lang->line('hrms_leave_management_employee1_code'); ?><!--Employee Code--></label>
            </div>
            <div class="col-xs-7 col-sm-4"><span id="empCodeSpan" class="frm_input"><?php echo $empDet['EmpSecondaryCode'] ?></span></div>

            <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('common_designation'); ?><!--Designation--></label></div>
            <div class="col-xs-7 col-sm-3"><span id="designationSpan" class="frm_input"><?php echo $empDet['DesDescription'] ?></span></div>
        </div>
        <div class="row" style="margin-bottom: 3px">

            <div class="col-xs-4 col-sm-2">
                <label><?php echo $this->lang->line('common_department');?></label>
            </div>
            <div class="col-xs-7 col-sm-4" id="">
                <span id="department" class="frm_input"><?php echo $empDet['department'] ?></span>
            </div>

            <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('common_reporting_manager');?><!--Reporting Manager--></label></div>
            <div class="col-xs-7 col-sm-3">
                <span id="reportingManager" class="frm_input"><?php echo $empDet['manager'] ?></span>
            </div>
        </div>
        <div class="row" style="margin-bottom: 3px">
            <div class="col-xs-4 col-sm-2">
                <label>
                    <?php echo $this->lang->line('hrms_leave_management_please_select__a_type'); ?><!--Leave Type--></label>
            </div>
            <div class="col-xs-7 col-sm-4" id="leaveTypeDropDown">
                <select id="leaveTypeID" name="leaveTypeID" class="form-control leaveType frm_input "  disabled>
                    <option value="<?php echo $leaveDet['leaveTypeID'] ?>"><?php echo $leaveDet['description'] ?></option>
                </select>
            </div>

            <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_leave_management_date_of_Join');?><!--Date of Join--></label></div>
            <div class="col-xs-7 col-sm-3">
                <span id="dateofJoin" class="frm_input"><?php echo $empDet['DateAssumed'] ?></span>
            </div>
        </div>

        <?php if($entitleDet['reasonApplicableYN'] == 1) { ?>
            <div class="row " style="margin-bottom: 3px" id="leaveReasonSection">
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
                    <input type="text" name="leaveReasonText" id="leaveReason" value="<?php echo $leaveDet['leaveReasonText'] ?>">
                </div>
            </div>

        <?php } ?>

        <?php if($entitleDet['isAnnualLeave'] == 1) { ?>
            <div class="row " style="margin-bottom: 3px" id="leaveAnnualCommentSection">
                <div class="col-xs-4 col-sm-2">
                    <label>
                        <?php echo $this->lang->line('hrms_leave_management_contact_details'); ?></label>
                </div>
                
                <div class="col-xs-7 col-sm-4" id="leaveReasonText">
                    <textarea name="annualComment" id="annualComment" rows="4"><?php echo $leaveDet['annualComment'] ?></textarea>
                    <!-- <input type="text" name="annualComment" id="annualComment" value=""> -->
                </div>
            </div>
        <?php } ?>
       
        <div class="row" style="margin-top: 8px">
            <div class="col-xs-4 col-sm-2">
                <label>
                    <?php echo $this->lang->line('hrms_leave_management_covering_emp'); ?><!--Covering Employee--></label>
            </div>
            <div class="col-xs-7 col-sm-4" id="coveringEmpID_div">
                <select id="coveringEmpID" name="coveringEmpID" class="form-control coveringEmp frm_input " multiple="multiple" disabled>
                    <option value="<?php echo $covering_emp['EIdNo'] ?>"><?php echo (!empty($covering_emp['EIdNo']) )? $covering_emp['ECode'].' | '.$covering_emp['Ename2']: ''; ?></option>
                </select>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-6">
            <div class="panel-body" style="padding-bottom: 0;padding-top: 2%;">

                <div class="input-daterange input-group" id="datepicker">
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                            <?php echo $this->lang->line('hrms_leave_management_start_date'); ?><!--Start Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startDate" value="<?php echo $leaveDet['startDate'];?>" id="startDate"  disabled class="form-control dateFields frm_input">
                        </div>
                    </div>
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                            <?php echo $this->lang->line('hrms_leave_management_end_date'); ?><!--End Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate" value="<?php echo $leaveDet['endDate'];?>" id="endDate" disabled class="form-control dateFields frm_input">
                        </div>
                    </div>
                </div>
                <div class="col-xs-5" <?php echo $half_day_show ?> style="margin-bottom: 5px">
                    <label><?php echo $this->lang->line('hrms_leave_management_half_day'); ?><!--Half Day--></label>
                </div>
                <div class="col-xs-7" <?php echo $half_day_show ?> style="margin-bottom: 5px">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" value="1" id="halfDay" name="halfDay"  class="form-check-input" disabled checked>
                        </label>
                    </div>
                </div>

                <div class="col-xs-5 shiftcls" <?php echo $half_day_show ?> style="margin-bottom: 5px">
                    <label>Shift</label>
                </div>
                <div class="col-xs-7 shiftcls" <?php echo $half_day_show ?> style="margin-bottom: 5px">
                    <select id="shift" name="shift" class="form-control" disabled>
                        <option value="0" <?php echo ($leaveDet['shift'] == 0)? 'selected': ''; ?>>Select a shift</option>
                        <option value="1" <?php echo ($leaveDet['shift'] == 1)? 'selected': ''; ?>>Morning shift</option>
                        <option value="2" <?php echo ($leaveDet['shift'] == 2)? 'selected': ''; ?>>Evening shift</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-xs-1 col-sm-1"></div>

        <div class="col-xs-5 col-sm-5">
            <div class="panel-body" style="padding-top: 3%; padding-bottom: 2%">
                <?php if($applicationType != 2){ ?>
                    <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                        <label>
                            <?php echo $this->lang->line('hrms_leave_management_leave_available'); ?><!--Leave Entitled--></label>
                        <span class="pull-right frm_input" id="entitleSpan"><?php echo $balance;?></span>
                    </div>
                <?php } ?>
                <div class="col-xs-12 col-sm-12">
                    <label>
                        <?php echo $this->lang->line('hrms_leave_management_leave_applied'); ?><!--Leave Applied--></label>
                    <span class="pull-right frm_input" id="takenSpan"><?php echo $leaveDet['days'];?></span>
                </div>

                <?php if($applicationType != 2){ ?>
                    <div id="workingDaysHide" class="col-xs-12 col-sm-12 hide">
                        <label>
                            <?php echo $this->lang->line('hrms_leave_management_working_days'); ?><!--Working Days--></label>
                        <span class="pull-right frm_input" id="workingDaysSpan"><?php echo $leaveDet['workingDays'];?></span>
                    </div>

                    <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                        <label><?php echo $this->lang->line('hrms_leave_management_balance'); ?><!--Balance--></label>
                        <span style="font-size: 11px" id="baltext"></span>
                        <span class="pull-right frm_input" id="balanceSpan"><?php echo ( $balance - $leaveDet['days'] );?></span>
                    </div>
                <?php } ?>
                <div class="overlay" style="display: none">
                    <i class="fa fa-refresh fa-spin myOverlay-spin"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
        <textarea name="comment" class="form-control comment" style="border-radius: 3px" disabled
                  placeholder="<?php echo $this->lang->line('common_comment'); ?>"><?php echo $leaveDet['comments'];?></textarea>
        </div>
    </div>

    <div style="width: 50%; margin-top: 15px;" class="pull-right">
        <table class="table table-striped table-condensed table-hover" style="">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="View_attachment_modal_body" class="no-padding">
            <tr class="danger">
                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div id="confirmedBy" class="col-xs-6" style="padding-bottom: 0;padding-top: 2%;">
            <?php echo '<b>'.$this->lang->line('common_confirmed_by').' : </b>'. $leaveDet['confirmedByName'].' &nbsp; &nbsp; &nbsp; <b>'.$this->lang->line('common_confirmed_on').' :</b>'.$leaveDet['confirmedDate'];?>
        </div>
        <div id="approvedBy" class="col-xs-6" style="padding-bottom: 0;padding-top: 2%;">
            <?php
            if(!empty($leaveDet['approvedbyEmpName'])){
                echo '<b>'.$this->lang->line('common_approved_by').' : </b>'. $leaveDet['approvedbyEmpName'].' &nbsp; &nbsp; &nbsp; <b>'.$this->lang->line('common_confirmed_on').' :</b>'.$leaveDet['approvedDate'];
            }
            ?>
        </div>
    </div>
<?php
