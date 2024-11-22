<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$transfer_type_arr = transferType();
$transfer_term_arr = transferTerm();

$employee_arr = fetch_all_employees();
// $groups = all_group_drop();
$designations_arr = all_designation_drop();

$manager_arr = all_managers_drom();
$grades_arr = employee_grade_drop();
$locations_arr = all_location_drom();
$group_arr = all_group_drop_PAA();
$company_arr = all_company_drom();
$segment_arr = all_segment_arr_PAA();
$sub_segment_arr = all_sub_segment_arr_PAA();
$division_arr = all_division_drop_PAA();

$status_arr = array(
    '' => 'Select Status',
    '0' => 'Single',
    '1' => 'Family',
);

$overtime_entitlment = array(
    '' => 'Select Overtime Entitlement',
    '0' => 'No',
    '1' => 'Yes',
);

$activityCode_arr = get_activity_codes();
$allowance_arr = load_allowances_for_personalAction_mse();
$currency_arr = load_currency_deop();
$leavegroup_arr = load_leavegroup_drop();
$department_arr = load_department_arr();

$company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
$company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
?>

<style>
    fieldset {
        /*border: 1px solid silver;*/
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 15px;
        margin: auto;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .right-align{ text-align: right; }

    .more-info-btn{
        border-radius: 0px;
        font-size: 11px;
        line-height: 1.5;
        padding: 1px 7px;
    }

        /* Add this CSS to your stylesheet */
    .shadow-box {
        border: 2px solid #ccc;
        padding: 10px;
        background-color: #fff; /* Change background color to white */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Set shadow underneath */
    }


    .myCheckbox {
            width: 20px;  /* Adjust width as needed */
            height: 20px;  /* Adjust height as needed */
            /*appearance: none;*/  /* Remove default styles */
            border: 1px solid #ccc;  /* Optional: Add border */
            border-radius: 3px;  /* Optional: Add border radius */
            position: relative;
            cursor: pointer;
        }

    .myCheckbox_d{
            width: 20px;  /* Adjust width as needed */
            height: 20px;  /* Adjust height as needed */
            /*appearance: none;*/  /* Remove default styles */
            border: 1px solid #ccc;  /* Optional: Add border */
            border-radius: 3px;  /* Optional: Add border radius */
            position: relative;
            cursor: pointer;
        }
</style>

<input type="hidden" value="<?php echo $id ?>" id="pa_action_ID">
    <div class="row">
        <div class="row col-sm-12" style="text-align:center;margin-left:15px;">
            <div class="col-sm-1"></div>
            <div class="col-sm-2">
                <img class="" alt="Logo" style="height: 130px" src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
            </div>
            <div class="col-sm-8" style="text-align:left;"><h2 style="padding-top:70px;padding-bottom:0px;">PERSONAL ACTION / PAYROLL AUTHORIZATION FORM</h2></div>
            </div>
            <div class="col-sm-1"></div>
        </div>

        <hr>

        <div class="row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-10" style="background-color: white;">
                <div class="panel-body" id="transfer">
                    <div class="animated pulse">
                        
                        <legend></legend>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-8" style="font-size:medium">
                                    <label class="title ">COMPANY</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                    <span><?php echo isset($template_data['transfer_details']['Company']['currentText']) ? $template_data['transfer_details']['Company']['currentText'] : ''; ?></span>
                            </div>
                            <div class="form-group col-sm-4" style="font-size:medium">
                                    <label class="title ">DIVISION</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                    <span><?php echo isset($template_data['transfer_details']['Division']['currentText']) ? $template_data['transfer_details']['Division']['currentText'] : ''; ?></span>
                            </div>
                        </div>
    
                        <fieldset style="padding: 0; margin-bottom: 10px; border: 1px solid #ccc; background-color: white;">
                            <div class="row" style="margin: 10px;background-color:#D3D3D3">
                                <div class="form-group col-sm-12">
                                    <div class="form-group col-sm-5" style="padding-left:10px;">
                                        <label class="title ">ACTION TYPE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Action Type']['currentText']) ? $template_data['transfer_details']['Action Type']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12" >
                                    <div class="form-group col-sm-4" style="padding-left:10px;">
                                        <label class="title ">EMP NO</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EmpCODE']['currentText']) ? $template_data['transfer_details']['EmpCODE']['currentText'] : ''; ?></span>
                                    </div>
                                    <div class="form-group col-sm-4" style="padding-right:10px;">
                                        <label class="title ">NAME</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Name']['currentText']) ? $template_data['transfer_details']['Name']['currentText'] : ''; ?></span>
                                    </div>
                                    <div class="form-group col-sm-4" style="padding-right:10px;">
                                        <label class="title ">DATE OF JOIN</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EDOJ']['currentText']) ? $template_data['transfer_details']['EDOJ']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12" >
                                    <div class="form-group col-sm-4" style="padding-left:10px;">
                                        <label class="title ">LAST PROMOTION DATE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Last Promotion Date']['currentText']) ? $template_data['transfer_details']['Last Promotion Date']['currentText'] : ''; ?></span>
                                    </div>
                                    <div class="form-group col-sm-4" style="padding-right:10px;">
                                        <label class="title ">LAST INCREMENT DATE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Last Review Date']['currentText']) ? $template_data['transfer_details']['Last Review Date']['currentText'] : ''; ?></span>
                                    </div>
                                    <div class="form-group col-sm-4" style="padding-right:10px;">
                                        <label class="title ">NEXT PERIODICAL MEDICAL DATE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Periodical Medical Date']['currentText']) ? $template_data['transfer_details']['Periodical Medical Date']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!--<div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-12" style="font-size:medium;margin-top:0px;">
                                        <div class="col-sm-6" style="padding-left:40px;font-size:medium;margin-top:0px;">
                                            <div class="col-sm-6"><label class="title ">Overtime Entitlement</label></div>
                                            <div class="col-sm-6"><input type="text" name="overTimeEntitlement" id="overTimeEntitlement" style="width:100%;" disabled="true" ></div>
                                        </div>
                                        <div class="form-group col-sm-6" style="font-size:medium;margin-top:0px;">
                                            <div class="col-sm-6">
                                                <div class="col-sm-2"><input type="checkbox" class="myCheckbox" id="<?php echo $id ?>" name="overTimeEntitlement" value="1" data-text="overTimeEntitlementYes" <?php 
                                                 echo ($template_data['transfer_details']['overTimeEntitlement']['NewValueText'] == 'overTimeEntitlementYes' && $template_data['transfer_details']['overTimeEntitlement']['NewValue'] == 'true') ? 'checked' : ''; ?>></div>
                                                <div class="col-sm-4"><label class="title ">Yes</label></div>
                                                <div class="col-sm-2"><input type="checkbox" class="myCheckbox" id="<?php echo $id ?>" name="overTimeEntitlement" value="1" data-text="overTimeEntitlementNo" <?php 
                                                 echo ($template_data['transfer_details']['overTimeEntitlement']['NewValueText'] == 'overTimeEntitlementNo' && $template_data['transfer_details']['overTimeEntitlement']['NewValue'] == 'true') ? 'checked' : '';?>></div>
                                                <div class="col-sm-4"><label class="title ">No</label></div>
                                            </div>
                                            <div class="col-sm-6">&nbsp;</div>
                                        </div>
                                    </div>
                            </div>

                            <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-12" style="font-size:medium;margin-top:0px;">
                                        <div class="form-group col-sm-6" style="padding-left:40px;font-size:medium;margin-top:0px;">
                                            <div class="col-sm-4"><label class="title ">On Budget</label></div>
                                            <div class="col-sm-8"><input type="text" name="onBudget" id="onBudget" style="width:100%;" disabled="true"></div>
                                        </div>
                                        <div class="form-group col-sm-6" style="font-size:medium;margin-top:0px;">
                                            <div class="col-sm-6">
                                                <div class="col-sm-2"><input type="checkbox" class="myCheckbox_d" id="<?php echo $id ?>" name="onBudget" value="1" data-text="onBudgetYes" <?php 
                                                echo ($template_data['transfer_details']['onBudget']['NewValueText'] == 'onBudgetYes' && $template_data['transfer_details']['onBudget']['NewValue'] == 'true') ? 'checked' : ''; ?>></div> 

                                                <div class="col-sm-4"><label class="title ">Yes</label></div>

                                                <div class="col-sm-2"><input type="checkbox" class="myCheckbox_d" id="<?php echo $id ?>" name="onBudget" value="1" data-text="onBudgetNo" <?php 
                                                echo ($template_data['transfer_details']['onBudget']['NewValueText'] == 'onBudgetNo' && $template_data['transfer_details']['onBudget']['NewValue'] == 'true') ? 'checked' : ''; ?>></div> 

                                                <div class="col-sm-4"><label class="title ">No</label></div>
                                            </div>
                                            <div class="col-sm-6">&nbsp;</div>
                                        </div>
                                    </div>
                            </div>
                            <?php if($template_data['transfer_details']['onBudget']['NewValueText'] == 'onBudgetYes' && $template_data['transfer_details']['onBudget']['NewValue'] == 'true'){ ?>
                            <div class="row budgetreferenceNumber" style="margin-top: 10px;">
                                    <div class="form-group col-sm-12" style="padding-left:55px;padding-right:30px;font-size:medium;margin-top:0px;">
                                        <div class="col-sm-4"><label class="title ">Budget Reference Number</label></div>
                                        <div class="col-sm-8"><input type="text" name="budgetreferenceNumber" id="<?php echo $id ?>" data-current-value="budgetreferenceNumber" onchange="onkeyupchangeValue(this)" style="width:100%;"  value="<?php echo isset($template_data['transfer_details']['budgetreferenceNumber']['NewValueText']) ? $template_data['transfer_details']['budgetreferenceNumber']['NewValueText'] : ''; ?>"></div>
                                    </div>
                            </div>
                            <?php } ?>

                            <?php if($template_data['transfer_details']['onBudget']['NewValueText'] == 'onBudgetNo' && $template_data['transfer_details']['onBudget']['NewValue'] == 'true'){ ?>
                            <div class="row ifNotOnBudget" style="margin-top: 10px;">
                                    <div class="form-group col-sm-12" style="padding-left:55px;padding-right:30px;font-size:medium;margin-top:0px;">
                                        <div class="col-sm-4"><label class="title ">If Not On Budget (Explain)</label></div>
                                        <div class="col-sm-8"><textarea class="form-control" style="word-wrap: break-word; overflow-wrap: break-word;" name="ifNotOnBudget" id="<?php echo $id ?>" data-current-value="ifNotOnBudget" onchange="onkeyupchangeValue(this)" rows="2"><?php echo empty($template_data['transfer_details']['ifNotOnBudget']['NewValueText']) ? '': trim($template_data['transfer_details']['ifNotOnBudget']['NewValueText']); ?></textarea></div>
                                    </div>
                            </div>
                            <?php } ?>
                            <hr> -->

                            <hr>

                            <div class="row" style="margin: 10px;">
                                    <div class="col-sm-12" style="padding:10px; font-size:medium">
                                        <div class="form-group col-sm-6" style="font-size:medium;">
                                            <div class="col-sm-4"><label class="title ">Effective Date</label></div>
                                            <div class="input-group datepic col-sm-8">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="effectiveDate"
                                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="<?php echo $id ?>" data-current-value="effectiveDate" onchange="onkeyupchangeValue(this)"
                                                        value="<?php echo isset($template_data['transfer_details']['effectiveDate']['NewValueText']) ? $template_data['transfer_details']['effectiveDate']['NewValueText'] : ''; ?>" class="form-control" style="font-size:medium !important" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2"></div>
                                    <div class="col-md-8 animated pulse">
                                        <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                                            <legend></legend>
                                                <table class="table table-bordered table-striped table-condensed mx-auto" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="min-width: 20%">DESCRIPTION</th>
                                                            <th style="min-width: 20%">FROM</th>
                                                            <th style="min-width: 10%">TO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="table_body">
                                                         <tr><td>DIVISION</td><td><?php echo isset($template_data['transfer_details']['Division']['currentText']) ? $template_data['transfer_details']['Division']['currentText']: '';?></td><td>
                                                            <?php echo form_dropdown('division', $department_arr, isset($template_data['transfer_details']['Division']['NewValue']) ? $template_data['transfer_details']['Division']['NewValue']: '' , 'class="form-control select2" id="division" onchange="changeValue(\'Division\', this)" required'); ?>
                                                        </td></tr>
                                                        <tr><td>DESIGNATION</td><td><?php echo isset($template_data['transfer_details']['Designation']['currentText']) ? $template_data['transfer_details']['Designation']['currentText']: ''; ?></td><td>
                                                            <?php echo form_dropdown('designationID', $designations_arr, isset($template_data['transfer_details']['Designation']['NewValue']) ? $template_data['transfer_details']['Designation']['NewValue']:'', 'class="form-control select2" id="designationID" onchange="changeValue(\'Designation\', this)" required'); ?>
                                                        </td></tr>
                                                        <tr><td>GRADE</td><td><?php echo isset($template_data['transfer_details']['Grade']['currentText']) ? $template_data['transfer_details']['Grade']['currentText']: '';?></td><td>
                                                            <?php echo form_dropdown('grade', $grades_arr, isset($template_data['transfer_details']['Grade']['NewValue']) ? $template_data['transfer_details']['Grade']['NewValue']: '', 'class="form-control select2" id="grade" onchange="changeValue(\'Grade\', this)" required'); ?>
                                                        </td></tr>
                                                        <tr><td>BASIC SALARY&nbsp;&nbsp;:&nbsp;&nbsp;(<?php echo isset($template_data['transfer_details']['currency']['currentText']) ? $template_data['transfer_details']['currency']['currentText']: ''; ?>)</td><td  class="text-right"><?php echo isset($template_data['transfer_details']['basicSalary']['currentText']) ? number_format($template_data['transfer_details']['basicSalary']['currentText'],$company_reporting_DecimalPlaces): '-';?></td><td>
                                                            <input type="text"  class="text-right" style="width:100%;" name="basicSalary" id="basicSalary" value="<?php echo isset($template_data['transfer_details']['basicSalary']['NewValue']) ? number_format($template_data['transfer_details']['basicSalary']['NewValue'],$company_reporting_DecimalPlaces): '' ?>" onchange="changeValue('basicSalary', this)">
                                                        </td></tr>
                                                        <tr><td>CURRENCY</td><td><?php echo isset($template_data['transfer_details']['currency']['currentText']) ? $template_data['transfer_details']['currency']['currentText']: '';?></td><td>
                                                            <?php echo form_dropdown('currency', $currency_arr, isset($template_data['transfer_details']['currency']['NewValue']) ? $template_data['transfer_details']['currency']['NewValue']: '', 'class="form-control select2" id="currency" onchange="changeValue(\'currency\', this)" required'); ?></td>
                                                        </tr>
                                                        <tr><td>STATUS</td><td><?php echo isset($template_data['transfer_details']['Status']['currentText']) ? $template_data['transfer_details']['Status']['currentText']: '';?></td><td>
                                                            <?php echo form_dropdown('location', $status_arr, isset($template_data['transfer_details']['Status']['NewValue']) ? $template_data['transfer_details']['Status']['NewValue']: '', 'class="form-control select2" id="location" onchange="changeValue(\'Status\', this)" required'); ?></td>
                                                        </tr>
                                                        <tr><td>OVERTIME ENTITLEMENT</td><td><?php echo isset($template_data['transfer_details']['overtime_entitlment']['currentText']) ? $template_data['transfer_details']['overtime_entitlment']['currentText']: '';?></td><td>
                                                            <?php echo form_dropdown('overtime_entitlment', $overtime_entitlment, isset($template_data['transfer_details']['overtime_entitlment']['NewValue']) ? $template_data['transfer_details']['overtime_entitlment']['NewValue']: '', 'class="form-control select2" id="location" onchange="changeValue(\'overtime_entitlment\', this)" required'); ?></td>
                                                        </tr>
                                                        <tr><td>WORK/LEAVE SCHEDULE</td><td><?php echo isset($template_data['transfer_details']['Leave Group']['currentText']) ? $template_data['transfer_details']['Leave Group']['currentText']: '';?></td><td>
                                                        <?php echo form_dropdown('workLeaveSchedule', $leavegroup_arr, isset($template_data['transfer_details']['Leave Group']['NewValue']) ? $template_data['transfer_details']['Leave Group']['NewValue']: '', 'class="form-control select2" id="workLeaveSchedule" onchange="changeValue(\'Leave Group\', this)" required'); ?></td>
                                                        </td></tr>
                                                    </tbody>
                                                </table> 
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="form-group col-sm-12" style="font-size:medium;">
                                        <div class="col-sm-2"><label class="title ">Remarks&nbsp;&nbsp;&nbsp;&nbsp;:</label></div>
                                        <div class="col-sm-10"><textarea class="form-control remark1" style="word-wrap: break-word; overflow-wrap: break-word;" id="<?php echo $id ?>" name="remark1" rows="2"  data-current-value="remark1" onchange="onkeyupchangeValue(this)" ><?php echo empty($template_data['transfer_details']['remark1']['NewValueText']) ? '': trim($template_data['transfer_details']['remark1']['NewValueText']); ?></textarea></div>
                                    </div>
                            </div>

                            <hr>

                            <div class="row" style="margin: 10px;">
                                    <div class="form-group col-sm-12" style="padding:10px;font-size:medium">
                                        <div class="form-group col-sm-2">
                                            <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="allowance" value="1" data-text="allowance"  <?php echo ($template_data['transfer_details']['allowance']['NewValue'] == 'true') ? 'checked' : ''; ?>>  
                                            &nbsp;&nbsp;&nbsp;&nbsp;<label class="title ">Allowance</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12" style="font-size:medium">
                                        <div class="col-sm-3"><label class="title ">Add new Allowance</label></div>
                                        <div class="form-group col-sm-6" style="font-size:medium">
                                            <?php echo form_dropdown('newAllowance', $allowance_arr, '', 'class="form-control select2" id="newAllowance" onchange="change_newAllowance(this.value)" '); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-8 animated pulse">
                                        <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                                        <legend></legend>
                                            <table class="table table-bordered table-striped table-condensed mx-auto" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="min-width: 10%">DESCRIPTION</th>
                                                        <!-- <th class="text-center" style="min-width: 10%">CURRENCY</th> -->
                                                        <th class="text-center" style="min-width: 10%">FROM&nbsp;&nbsp;(<?php echo isset($template_data['transfer_details']['currency']['currentText']) ? $template_data['transfer_details']['currency']['currentText']: ''; ?>)</th> 
                                                        <th class="text-center" style="min-width: 10%">TO&nbsp;&nbsp;(<?php echo isset($template_data['transfer_details']['currency']['NewValueText']) ? $template_data['transfer_details']['currency']['NewValueText']: ''; ?>)</th> 
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                        <?php  
                                                        $x=1;
                                                        foreach ($details as $val) {
                                                            
                                                            ?>
                                                            <tr>
                                                                <?php if(!empty($val['salaryCategoryID']) && $val['fieldType'] != 'Basic Salary' && $val['fieldType'] != 'Designation' && $val['fieldType'] != 'Grade' && $val['fieldType'] != 'Leave Group' && $val['fieldType'] != 'Status' && $val['fieldType'] != 'JD ATTACHED' && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Last Review Date' && $val['fieldType'] != 'DEPARTMENT' && $val['fieldType'] != 'Sub Segment' && $val['fieldType'] != 'Segment' && $val['fieldType'] != 'Location' && $val['fieldType'] != 'Name' && $val['fieldType'] != 'EmpCODE' && $val['fieldType'] != 'EDOJ' && $val['fieldType'] != 'Division' && $val['fieldType'] != 'Reporting Manager' && $val['fieldType'] != 'Company' && $val['fieldType'] != 'Justification' && $val['fieldType'] != 'New job description' && $val['fieldType'] != 'Reporting Structure' && $val['fieldType'] != 'KPI' && $val['fieldType'] != 'Performance Appraisal form'){ ?>
                                                                <!--1st column -->
                                                                <?php if($actionType != 3){ ?>
                                                                    <?php if(!empty($val['salaryCategoryID'])){ ?>
                                                                        <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                                            <td class="text-left"><?php echo $val['fieldType']; ?></td>
                                                                        <?php } ?>
                                                                    <?php }else{ ?>
                                                                        <td class="text-left"><?php echo $val['fieldType']; ?></td>
                                                                    <?php } ?>
                                                                <?php }else{ 
                                                                    if(!empty($val['monthlyDeclarationID'])){ ?>
                                                                        <td class="text-left"><?php echo $val['fieldType']; ?></td>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                                    
                                                                
                                                                <?php if($actionType !=3 ){
                                                                    if(!empty($val['salaryCategoryID'])){ ?> <!--2nd column -->
                                                                        <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                                            <td><input type="text" class="text-right" style="width:100%;" placeholder="Enter Value Here" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['currentText']) ? 0 : floatval($val['currentText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)" readonly></td>
                                                                        <?php } ?>
                                                                    <?php }else{ ?>
                                                                        <td class="text-left" readonly><?php echo !empty($val['currentText']) ? $val['currentText'] : '-'; ?></td> <!--2nd column -->
                                                                    <?php } 
                                                                }?>

                                                                <!-- 3rd column -->
                                                                <?php if($actionType != 3){
                                                                    if(!empty($val['salaryCategoryID'])){ ?>
                                                                        <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                                            <td><input type="text" class="text-right" style="width:100%;" placeholder="Enter Value Here" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['NewValueText']) ? floatval($val['currentText']) : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)"></td>
                                                                        <?php } ?>
                                                                    <?php }
                                                                    else{
                                                                        if($val['fieldType'] == 'Grade'){ ?>
                                                                            <td><?php echo form_dropdown('grade', $grades_arr, empty($template_data['transfer_details']['Grade']['NewValue']) ? $template_data['transfer_details']['Grade']['currentValue'] : $template_data['transfer_details']['Grade']['NewValue'], 'class="form-control select2" id="grade" onchange="changeValue(\'Grade\', this)" required'); ?></td>
                                                                        <?php }else if($val['fieldType'] == 'Status'){ ?> 
                                                                            <td><?php echo form_dropdown('status', $status_arr, empty($template_data['transfer_details']['Status']['NewValue']) ? $template_data['transfer_details']['Status']['currentValue'] : $template_data['transfer_details']['Status']['NewValue'] , 'class="form-control select2" id="status" onchange="changeValue(\'Status\', this)" required'); ?></td>
                                                                        <?php }
                                                                        else if($val['fieldType'] == 'Designation'){?>
                                                                            <td><?php echo form_dropdown('designationID', $designations_arr, empty($template_data['transfer_details']['Designation']['NewValue']) ? $template_data['transfer_details']['Designation']['currentValue'] : $template_data['transfer_details']['Designation']['NewValue'], 'class="form-control select2" id="designationID" onchange="changeValue(\'Designation\', this)" required'); ?></td>
                                                                        <?php }
                                                                        else if($val['fieldType'] == 'Leave Group'){ ?>
                                                                            <td><?php echo form_dropdown('leaveGroup', $leaveGroup_arr, empty($template_data['transfer_details']['Leave GroupP']['NewValue']) ? $template_data['transfer_details']['Leave Group']['currentValue'] : $template_data['transfer_details']['Leave Group']['NewValue'] , 'class="form-control select2" id="leaveGroup" onchange="changeValue(\'Leave Group\', this)" required'); ?></td>
                                                                        <?php }
                                                                    }
                                                                }else{?>
                                                                    <?php if(!empty($val['monthlyDeclarationID'])){ ?>
                                                                        <td><input type="text" class="text-right" style="width:100%;" placeholder="<?php echo  number_format(0, $company_reporting_DecimalPlaces) ;?>" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['NewValueText']) ? 0 : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)"></td>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </tr>
                                                            <?php $x++;
                                                            }
                                                        } ?>
                                                </tbody>
                                            </table>
                                        </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-2">&nbsp;</div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="col-sm-1">&nbsp;</div>
        </div>

        <div class="row" >
            <div class="col-sm-1"></div>
            <div class="form-group col-sm-10" style="font-size:medium;">
                <div class="col-sm-2"><label class="title ">Remarks</label></div>
                <div class="col-sm-10"><textarea class="form-control remark2" style="word-wrap: break-word; overflow-wrap: break-word;" id="<?php echo $id ?>" name="remark2" rows="2"  data-current-value="remark2" onchange="onkeyupchangeValue(this)" ><?php echo empty($template_data['transfer_details']['remark2']['NewValueText']) ? '': trim($template_data['transfer_details']['remark2']['NewValueText']); ?></textarea></div>
            </div>
            <div class="col-sm-1"></div>
        </div>
        <br></br>
        <!-- <div class="row" >
            <div class="col-sm-1"></div>
            <div class="form-group col-sm-10" style="padding-left:30px;font-size:medium;">
                <div class="col-sm-2">
                    <span>........................</span>
                    <label class="title ">Management</label>
                </div>
                <div class="col-sm-8">&nbsp;</div>
                <div class="col-sm-2 text-right">
                    <span>........................</span>
                    <label class="title ">Div. Manager</label>
                </div>
            </div>
            <div class="col-sm-1"></div>
        </div> -->
        <br>
        <hr>


    </div>
    


    
<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';



        $('.datepic').datetimepicker({ 
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                //var fieldType = ev.name;
                // var formatedValue = ev.date.format(ev.date._f);
                // changeValue('effectiveDate1', formatedValue, 1);

                 var inputElement = $(ev.currentTarget).find('input'); // Get the input element within the datepicker
                 var fieldName = inputElement.attr('name'); // Get the name attribute

                // Optional: format the date if needed
                var formattedValue = ev.date.format(date_format_policy);
                changeValue(fieldName, formattedValue, 1);
            });

            //chechboxes
            $('.myCheckbox').change(function() {
                var id = $(this).attr('id');
                var fieldText = $(this).attr('data-text');
                var isChecked = $(this).prop('checked');
                var type = $(this).attr('name');
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id, 'fieldValue': isChecked, 'fieldType': type, 'fieldText': fieldText, 'type' : 1},
                    url: "<?php echo site_url('Employee/update_persional_action_details_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                        success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    });
    
    function changeValue(fieldType, ev, date = null){
        if(date){
            var fieldText = ev;
            var fieldValue = '';
        }else{
            var fieldText = $(ev).find('option:selected').text();
            var fieldValue = $(ev).val();
        }

        var pa_action_ID = $('#pa_action_ID').val();

        $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType, 'fieldText': fieldText, 'type' : 1},
                    url: "<?php echo site_url('Employee/update_persional_action_details_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();                        
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
    }

    
    function change_newAllowance(NewValue)
    {
        var fieldType = $('#newAllowance').find('option:selected').text();
        var fieldValue = NewValue;
        var pa_action_ID = $('#pa_action_ID').val();

        $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType},
                    url: "<?php echo site_url('Employee/add_new_allowanse_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();                        
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
    }


    function changeValue_activecodeType(fieldType, ev){
        var newActiveCodeName = $(ev).find('option:selected').text();
        var newActiveCodeID = $(ev).val();
        var paID = $('#pa_action_ID').val();
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': paID, 'fieldType': fieldType, 'fieldValue': newActiveCodeID,  'fieldText': newActiveCodeName},
                    url: "<?php echo site_url('Employee/update_persional_action_details_actionCodeType_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if(data[0] == "s"){
                            fetch_persional_view();  
                        }
                        
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
    }

    
    function onkeyupchangeValue(inputElement, checkAttr = null, pid = null){
        var fieldValue = inputElement.value;

        if(checkAttr){
            var fieldType = checkAttr;
            var pa_action_ID = pid; 
        }else{
            var pa_action_ID = inputElement.getAttribute('id');
            var fieldType = inputElement.getAttribute('data-current-value'); 
        }

        $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType, 'type' : 2},
                    url: "<?php echo site_url('Employee/update_persional_action_details_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
        });
    }
    
    // function onInputChange(inputElement) {
    // var inputValue = inputElement.value;
    // var inputId = inputElement.id;

    //     // Send AJAX request
    //     $.ajax({
    //         url: 'your_server_endpoint.php',  // Change to your server endpoint
    //         type: 'POST',
    //         data: {
    //             effectiveDate: inputValue,
    //             id: inputId
    //         },
    //         success: function(response) {
    //             // Handle success response
    //         },
    //         error: function(xhr, status, error) {
    //             // Handle error response
    //             console.error('AJAX request failed:', status, error);
    //         }
    //     });
    // }

    $('.myCheckbox_d').change(function() {
                var id = $(this).attr('id');
                var fieldText = $(this).attr('data-text');
                var isChecked = $(this).prop('checked');
                var type = $(this).attr('name');

                if(fieldText == 'onBudget' && isChecked == false){
                    $('.ifNotOnBudget').addClass('hide');
                }else{
                    $('.ifNotOnBudget').removeClass('hide');
                }

                if(fieldText == 'onBudget' && isChecked == true){
                    $('.budgetreferenceNumber').removeClass('hide');
                }else{
                    $('.budgetreferenceNumber').addClass('hide');
                }
    
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id, 'fieldValue': isChecked, 'fieldType': type, 'fieldText': fieldText, 'type' : 1},
                    url: "<?php echo site_url('Employee/update_persional_action_details_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                        success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
</script>