<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);

$designation = fetch_emp_designation();
$currency_arr = all_currency_new_drop();  //all_currency_drop();
$employeeConType = fetch_empContractType();
$segment_arr = segment_drop();
$grade_arr = grade_drop();
$gratuity_arr = gratuity_drop();
$payee_emp_type_arr = payee_emp_type_drop();
$date_format_policy = date_format_policy();
$supplier_arr = visa_supplier_drop(true);
$visaType_arr = visa_supplier_type_drop();
$disable = '';
$setBlank = '';
$readonly = ($isSalaryDeclared > 0) ? 'readonly' : '';
$empdatevalue=getPolicyValues('LNG', 'All');
$hrmsFlow=getPolicyValues('HRFW','All');
$airportDestination=load_airportdestination_drop();
$airportDestination_arr = array(
    '' => 'Select a destination' 
);
foreach($airportDestination as $destination){
    $airportDestination_arr[$destination['destinationID']] = $destination['City'];
}
$activityCode_arr = get_activity_codes();
$emp_Burden_Rate=getPolicyValues('MANFL', 'All');
$emp_Link_Clinent=getPolicyValues('HRO', 'All');
$customer = all_customer_drop(true);


if( !empty($employmentData) ){
    $disable = ( $employmentData['typeID'] != 2 )? 'disabled' : '';
    $setBlank = ( $employmentData['typeID'] != 2 )? 'Y' : '';
}

$advancedCostCapturing = getPolicyValues('ACC', 'All');
$dateTime = current_date();

$airTicketEligible_arr = [
    '' => 'Select Eligibility',
    0 =>'Not Eligible',
    1 =>'Eligible for Employee',
    2 =>'Eligible for Employee & Family'
  ];
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <legend><?php echo $this->lang->line('emp_employment_details'); //Employment Details?> </legend>
                <?php echo form_open('', 'role="form" id="employmentData_form" autocomplete="off"'); ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="empDoj">
                                <?php echo $this->lang->line('emp_date_joined'); ?><!--Date Joined --><?php required_mark(); ?></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="empDoj" value="<?php echo $employmentData['EDOJ']; ?>"
                                        id="empDoj" class="form-control dateFields"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        onchange="update_dateAssume(this)" <?php echo $readonly; ?>>
                                </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                        <label for="dateAssumed">
                            <?php //Language Changes Based on policy
                            if (in_array($empdatevalue, ['MSE', 'SOP', 'GCC', 'Flowserve','Micoda','NOV'])) {
                                echo $this->lang->line('emp_date_confirmed');
                            } else {
                                
                                echo $this->lang->line('emp_date_assumed');
                            }
                            ?><!--Date Assumed/confirmed -->
                            <?php if($empdatevalue!="MSE"){
                                required_mark();
                            } ?>
                        </label>

                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateAssumed"
                                       value="<?php echo $employmentData['DateAssumed']; ?>" id="dateAssumed"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" <?php echo $readonly; ?>>
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="employeeConType"><?php echo $this->lang->line('emp_type'); ?><!--Employee Type--><?php echo required_mark(); ?></label>
                            <select name="employeeConType" class="form-control select2" id="employeeConType" onchange="calculateDate()">
                                <option value=""></option>
                                <?php
                                if(count($employeeConType) > 0) {
                                    foreach ($employeeConType as $conType) {
                                        $selected = ($employmentData['EmployeeConType'] == $conType['EmpContractTypeID'])? 'selected' : '';
                                        echo '<option value="'.$conType['EmpContractTypeID'].'" data-type="'.$conType['employeeTypeID'].'" ';
                                        echo 'data-period="'.$conType['period'].'" data-pr-period="'.$conType['probation_period'].'" '.$selected;
                                        echo '>'.$conType['Description'].'</option>';
                                    }
                                }
                                ?>
                                </select>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="designation">
                                <?php echo $this->lang->line('emp_currency'); ?><!--Currency --><?php echo required_mark(); ?></label>
                            <?php
                            //$this->common_data['company_data']['company_default_currency'];
                            echo form_dropdown('empCurrency', $currency_arr, $employmentData['payCurrencyID'], 'class="form-control select2" id="empCurrency" ');
                            ?> <!--onchange="currency_validation_modal(this.value)"-->
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group col-sm-4 col-xs-6">
                            <label for="designation">
                                <?php echo $this->lang->line('emp_segment'); ?><!--Segment --><?php required_mark(); ?></label>
                            <?php echo form_dropdown('empSegment', $segment_arr, $employmentData['segmentID'], 'class="form-control select2" id="empSegment" '); ?>
                        </div>

                        <div class="form-group col-sm-4 col-xs-6">
                            <label for="probationPeriod">
                                <?php echo $this->lang->line('emp_probation_period'); ?><!--Probation End date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="probationPeriod" value="<?php echo $employmentData['probationPeriodCnvt']; ?>"
                                       id="probationPeriod" class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                            </div>
                        </div>

                        <?php if($advancedCostCapturing == 1){ ?>
                        <div class="form-group col-sm-4 col-xs-6">
                            <label for="designation">Activity Code<?php required_mark(); ?></label>
                            <?php echo form_dropdown('activityCode', $activityCode_arr, $employmentData['activityCodeID'], 'class="form-control select2" id="activityCode" onchange="change_employee_activityCodeType(this, this.value)" '); ?>
                        </div>
                        <?php } ?>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                    <?php if(!in_array($empdatevalue, ['MSE', 'SOP', 'GCC', 'FlowServe','Micoda','NOV'])){?>
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="pass_portNo">
                                <?php echo $this->lang->line('emp_passport_no'); ?><!--Passport No--></label>
                            <input type="text" class="form-control" id="pass_portNo" name="pass_portNo"
                                   value="<?php echo $employmentData['EPassportNO'] ?>" placeholder="Passport No"
                                   style="width:100%;">
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="passPort_expiryDate">
                                <?php echo $this->lang->line('emp_passport_expiry_date'); ?><!--Passport Expiry Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="passPort_expiryDate"
                                       value="<?php echo $employmentData['EPassportExpiryDate'] ?>"
                                       id="passPort_expiryDate"
                                       class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="passPort_expiryDate">
                                <?php echo $this->lang->line('emp_visa_expiry_date'); ?><!--Visa Expiry Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="visa_expiryDate"
                                       value="<?php echo $employmentData['EVisaExpiryDate'] ?>" id="visa_expiryDate"
                                       class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    <?php } ?>
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="airport_destination">
                                <?php echo $this->lang->line('emp_airport_destination'); ?><!--Airport Destination--></label>
                            <!--Airport Destination-->
                            <?php echo form_dropdown('airport_destination', $airportDestination_arr,  $employmentData['AirportDestinationID'], 'class="form-control select2" id="airport_destination"'); ?>
                        </div>

                        <?php if($hrmsFlow=='Standard'){?>
                            <div class="form-group col-sm-3 col-xs-6">
                                <label for="labourCode">Labour Code</label>
                                <input type="text" class="form-control" id="labourCode" name="labourCode" value="<?php echo $employmentData['labourCode']; ?>" style="width:100%;"> <!--disabled=""-->
                            </div>
                        
                        <?php }?>

                        <div class="form-group col-sm-3 col-xs-6">
                                <label for="airTicketEligible"><?php echo $this->lang->line('emp_airTicketEligible'); ?></label>
                                <?php echo form_dropdown('airTicketEligible', $airTicketEligible_arr, $employmentData['airTicketEligible'], 'class="form-control select2" id="airTicketEligible" onchange="checkStatus(this.value)" '); ?> <!--onchange="change_airTicketEligible(this, this.value)" -->
                        </div>
                        
                    </div>

                    <div class="col-md-12">
                        <?php if($hrmsFlow=='Standard'){?>
                            <div class="form-group col-sm-2 col-xs-6">
                                <label for=""><?php echo $this->lang->line('emp_employee_man_power_no'); ?><!--Man Power No--></label>
                                <input type="text" class="form-control" id="manPowerNo" name="manPowerNo"
                                    value="<?php echo $employmentData['manPowerNo'] ?>" placeholder="<?php echo $this->lang->line('emp_employee_man_power_no'); ?>" style="width:100%;">
                            </div>
                        <?php }?>
                        <div class="form-group col-sm-2 col-xs-6">
                            <label for="grade"><?php echo $this->lang->line('emp_grade');?> <!--Grade--></label>
                            <?php
                            echo form_dropdown('gradeID', $grade_arr, $employmentData['gradeID'], 'class="form-control select2" id="gradeID" ');
                            ?>
                        </div>
                        <?php if($hrmsFlow=='Standard'){?>
                            <div class="form-group col-sm-2 col-xs-6">
                                <label for="pos_barCode"><?php echo $this->lang->line('emp_employee_bar_code'); ?><!--Barcode--></label>
                                <input type="text" class="form-control" id="pos_barCode" name="pos_barCode"
                                    value="<?php echo $employmentData['pos_barCode'] ?>" placeholder="<?php echo $this->lang->line('emp_employee_bar_code'); ?>"
                                    style="width:100%;">
                            </div>
                        <?php }?>
                        <div class="form-group col-sm-2 col-xs-6">
                            <label for="gratuityID"><?php echo $this->lang->line('emp_employee_gratuity'); ?></label>
                            <?php
                            echo form_dropdown('gratuityID', $gratuity_arr, $employmentData['gratuityID'], 'class="form-control select2" id="gratuityID" ');
                            ?>
                        </div>
                        <div class="form-group col-sm-2 col-xs-6">
                            <label for="payee_emp_type"><?php echo $this->lang->line('emp_employee_payee_employee_type'); ?></label>
                            <?php
                            echo form_dropdown('payee_emp_type', $payee_emp_type_arr, $employmentData['payee_emp_type'], 'class="form-control select2" id="payee_emp_type" ');
                            ?>
                        </div>

                        <div class="form-group col-sm-2 col-xs-6">
                            <label for=""><?php echo $this->lang->line('common_mobile_credit_limit'); ?></label>
                            <input type="text" class="form-control numeric" id="mobileCreditLimit" name="mobileCreditLimit"
                                   value="<?php echo $employmentData['mobileCreditLimit'] ?>" placeholder="<?php echo $this->lang->line('common_mobile_credit_limit'); ?>"
                                   style="width:100%;">
                        </div>
                    </div>
                    <div class="col-md-12">

                        <div class="form-group col-sm-4 col-xs-6">
                            <label for="gratuityID"><?php echo 'Visa Type'//$this->lang->line('emp_employee_gratuity'); ?></label>
                            <?php
                                echo form_dropdown('visaPartyType', $visaType_arr, $employmentData['visaPartyType'], 'class="form-control select2" id="visaPartyType" onchange="visaType_select($(this))" ');
                            ?>
                        </div>

                        <!-- Visa Number -->
                        <?php if(!in_array($empdatevalue, ['MSE', 'SOP', 'GCC', 'Flowserve','Micoda','NOV'])){?>
                            <div class="form-group col-sm-4 col-xs-6">
                                <label for="visaNumber"><?php echo $this->lang->line('emp_employee_visa_Number'); ?></label>
                            <input type="text" id="visaNumber" name="visaNumber" class="form-control " placeholder="Visa Number" value="<?php echo $employmentData['visaNumber'] ?>">
                            </div>
                        <?php } ?>

                         <!-- Company Accomodation -->
                         <div class="form-group col-sm-4 col-xs-6">
                            <label for="transportationProvided"><?php echo $this->lang->line('emp_employee_transportationProvided'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="transportationProvided" id="transportationProvided" value="1"
                                        <?php echo ($employmentData['transportationProvided'] == 1) ? 'checked' : ''; ?>>
                                </span>
                                <input type="text" class="form-control" disabled="" value="<?php echo $this->lang->line('emp_employee_transportationProvided'); ?>">
                            </div>
                        </div>

                         <!-- Employee personal number -->
                         <div class="form-group col-sm-4 col-xs-6">
                            <label for="empPersonalNumber"><?php echo $this->lang->line('emp_employee_personal_number'); ?></label>
                            <input type="text" id="empPersonamNumber" name="empPersonamNumber" class="form-control " placeholder="Employee Personal Number" value="<?php echo $employmentData['employeePersonalNumber'] ?>">
                        </div>

                        <!-- Family Status -->
                        <div class="form-group col-sm-3">
                            <label class="control-label" for="fam_status">
                                <?php echo $this->lang->line('emp_family_status'); ?>    
                            </label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="fam_status" value="1" id="fam_status" <?php echo ($employmentData['familyStatusID'] == 1) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    
                        <!-- Register as a supplier -->
                        <div class="form-group col-sm-4 col-xs-6 <?php if($employmentData['visaPartyType'] != '2') { echo 'hide'; } ?>" id="visaPartyID_sec">
                            <label for="gratuityID"><?php echo 'Visa Sponsorer Company'//$this->lang->line('emp_employee_gratuity'); ?></label>
                            <?php
                                echo form_dropdown('visaPartyID', $supplier_arr, $employmentData['visaPartyID'], 'class="form-control select2" id="visaPartyID" onchange="" ');
                            ?>
                        </div>

                        <!-- Link Client -->
                        <?php if($employmentData['typeID'] == 5){ ?>
                            <div class="form-group col-sm-4 col-xs-6">
                                <label for="emp_Link_Clinent"><?php echo $this->lang->line('emp_Link_Clinent'); ?></label>
                                    <!-- <select name="customerID" class="form-control" id="customerID">
                                        <?php /*
                                            if (!empty($customer)) {
                                                foreach ($customer as $key => $val) {
                                                    $selected = (htmlspecialchars($key) == htmlspecialchars($employmentData['ClientID'])) ? 'selected' : '';
                                                    echo '<option id="customerID' . htmlspecialchars($key) . '" value="' . htmlspecialchars($key) . '" ' . $selected . '>' . htmlspecialchars($val) . '</option>';
                                                }
                                            } */
                                        ?>
                                    </select> -->
                                <?php
                                    echo form_dropdown('customerID', $customer, $employmentData['ClientID'], 'class="form-control select2" id="customerID" ');
                                ?>
                            </div>
                        <?php } ?>

                        <!-- Client Commision % -->
                        <?php if($employmentData['typeID'] == 5){ ?>
                            <div class="form-group col-sm-4 col-xs-6">
                                <label for="emp_Client_commision"><?php echo $this->lang->line('emp_Client_commision'); ?></label>
                                <input type="text" class="form-control numeric" id="emp_Client_commision" name="emp_Client_commision" value="<?php echo $employmentData['clientCommisionPerentage'] ?>"
                                placeholder="<?php echo $this->lang->line('emp_Client_commision'); ?>"style="width:100%;">
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-12">
                         <!-- emp_Burden_Rate -->
                         <div class="form-group col-sm-4 col-xs-6 <?php if($emp_Burden_Rate !== 'FlowServe') { echo 'hide'; } ?>" id="emp_Burden_Rate">
                            <label for="emp_Burden_Rate"><?php echo $this->lang->line('emp_Burden_Rate'); ?></label>
                            <input type="text" class="form-control numeric" id="emp_Burden_Rate" name="emp_Burden_Rate"
                            value="<?php echo $employmentData['burdenRate'] ?>"  placeholder="<?php echo $this->lang->line('emp_Burden_Rate'); ?>"style="width:100%;">
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                                <label for="isPayrollEmployee">&nbsp;</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="isPayrollEmployee" id="isPayrollEmployee" value="1"
                                            <?php echo ($employmentData['isPayrollEmployee'] == 1) ? 'checked' : ''; ?>>
                                    </span>
                                    <input type="text" class="form-control" disabled=""
                                        value="<?php echo $this->lang->line('emp_is_payroll_employee'); ?>">
                                    <!--Is Payroll Employee-->
                                </div>
                            </div>

                            <!-- slary stop detail button -->
                            <?php // if ($employmentData['isPayrollEmployee'] != 1) { ?>
                                <div class="form-group col-sm-1 col-xs-1 salaryStop_modal_button">
                                    <label>&nbsp;</label>
                                    <span class="input-group-addon">
                                        <a onclick="salaryStop_modal()"><span title="Add salary Stop details" rel="tooltip" class="glyphicon glyphicon-cog"></span></a>
                                    </span>
                                </div>
                            <?php // } ?>

                    </div>
                    
                </div>


                <div class="clearfix">&nbsp;</div>
                <div class="row" style="margin: 15px;">
                    <hr style="margin: 0px 0px 10px;">
                    <button type="button" class="btn btn-default-new size-lg pull-right" onclick="save_employmentData()">
                        <?php echo $this->lang->line('emp_save'); ?><!--Save Changes--></button>
                </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="row">
        <div class="col-md-12">
        <fieldset>
        <legend><?php echo $this->lang->line('emp_reporting_manager'); ?><!--reporting manager --></legend>
        <form method="POST" id="" class="form-horizontal" action="" name="">
            <input type="hidden" name="empID" value="<?php echo $empID ?>">
        </form>
        <div class="row" style="margin-top: -20px;">
            <div class="col-md-5">&nbsp;</div>
            <div class="col-md-7 pull-right" style="margin-right: 15px;">
                <button type="button" class="btn btn-primary-new size-sm pull-right"
                        onclick="fetch_repManagersHistory_to_addModel()"><i
                        class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add'); ?> <!--Add-->
                </button>
                <!-- &nbsp;
                <button class="btn btn-pdf btn-danger-new size-sm pull-right" id="btn-pdf" type="button" onclick="" style="margin-right: 1%;">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                </button> -->

            </div>
        </div>

        <div class="table-responsive" style="margin-top: 1%;">
            <table id="load_reporting_manager_history" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="width: auto"><?php echo $this->lang->line('emp_reporting_manager'); ?><!--Designation--></th>
                    <th style="width: 150px">Modified User</th>
                    <th style="width: 150px">Modified Date</th>
                    <th style="width: 150px"><?php echo $this->lang->line('emp_is_primary'); ?><!--Is Primary--></th>
                    <th style="width: 150px"><?php echo $this->lang->line('emp_is_active'); ?><!--Is Active--></th>
                    <th style="width: 100px"></th>
                </tr>
                </thead>
            </table>
        </div>
    </fieldset>
        </div>
    </div>
<br>



    <div class="clearfix">&nbsp;</div>
    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <legend> <?php echo $this->lang->line('emp_contract_details'); ?> <!--Contract Details--> </legend>
                <div class="row" style="margin: 0px 15px;">
                    <button type="button" class="btn btn-primary size-sm pull-right navdisabl" onclick="fetchContractHistory()">
                    <i class="fa fa-bars"></i> Contract History</button>
                </div>
                <?php echo form_open('', 'role="form" id="employeeVisa_form" '); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="contractStartDate">
                                <?php echo $this->lang->line('emp_contract_start_date'); ?><!--Contract Start Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="contractStartDate" value="<?php echo ($setBlank != 'Y')? $employmentData['contractStartDate']: ''; ?>"
                                    <?php echo $disable ?> id="contractStartDate" class="form-control contractData"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" onchange="getDate_contract()">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="contractEndDate">
                                <?php echo $this->lang->line('emp_contract_end_date'); ?><!--Contract End Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="contractEndDate" value="<?php echo ($setBlank != 'Y')? $employmentData['contractEndDate'] :'' ?>"
                                       id="contractEndDate" class="form-control contractData"  <?php echo $disable ?>
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="airport_destination">
                                <?php echo $this->lang->line('emp_contract_ref_no'); ?><!--Contract Ref No.--></label>
                            <input type="text" name="contractRefNo" id="contractRefNo" class="form-control contractData"  <?php echo $disable ?>
                                   value="<?php echo ($setBlank != 'Y')? $employmentData['contractRefNo'] : ''; ?>" >
                        </div>
                    </div>
                </div>

                <div class="clearfix">&nbsp;</div>
                <div class="row" style="margin: 15px;">
                    <hr style="margin: 0px 0px 10px;">
                    <div class="pull-right">
                        <input type="hidden" name="contractID" id="contractID" value="<?php echo $employmentData['contractID']; ?>" />
                        <?php $isDisplay = ( !empty($employmentData['contractID']) )? '' : 'display:none';?>
                        <button type="button" class="btn btn-success-new size-lg contractData" id="contract-re-new" onclick="contractReNew()"
                                style="<?php echo $isDisplay;?>"> Renew </button>
                        <button type="button" class="btn btn-default-new size-lg contractData" <?php echo $disable ?> onclick="update_visaDetails()">
                            <?php echo $this->lang->line('emp_save'); ?> <!--Save Changes-->
                        </button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="modal fade" id="reportingManagerHistory" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">
                    <?php echo $this->lang->line('emp_add_reporting_manager');?><!--Add Reporting Manager-->
                    </h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <fieldset>
                                    <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label" for="reportingManager">Reporting
                                                    Manager
                                            </label>

                                            <div class="col-sm-5">
                                                <input type="hidden" value="<?php echo $employmentData['managerID']; ?>" id="managerID" name="managerID"/>
                                                <input type="text" class="form-control" id="reportingManager" name="reportingManager"
                                                       value="<?php echo $employmentData['managerName']; ?>" data-value="<?php echo $employmentData['managerName']; ?>">
                                            </div>
                                            <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-primary btn-sm hidden-sm hidden-xs"
                                                        onclick="save_reportingManager('Print')">Save
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm hidden-lg pull-right"
                                                        onclick="save_reportingManager('Print')">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div style="margin-top: 3%">
                            <table class="<?php echo table_class(); ?>" id="reportingManagerHistoryTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Manager Name</th>
                                    <th>Modified Date</th>
                                    <th>Modified User</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="contractHistory" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"> Contract History </h3>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <button class="btn btn-pdf size-xs pull-right" id="btn-pdf" type="button" onclick="load_contractPdf()" style="margin-right: 1%;">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                        </button>
                        <button class="btn btn-excel btn-xs pull-right" id="btn-pdf" type="button" onclick="load_contractExcel()" style="margin-right: 1%;">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Excel
                        </button>
                    </div>
                    <div style="margin-top: 1%">
                        <table class="<?php echo table_class(); ?>" id="contractHistoryTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('emp_contract_start_date'); ?></th>
                                <th><?php echo $this->lang->line('emp_contract_end_date'); ?></th>
                                <th><?php echo $this->lang->line('emp_contract_ref_no'); ?></th>
                                <th>Is Current</th>
                                <th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

       <!-- slary stop detail modal -->
       <div class="modal fade" id="salaryStop_modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><strong> Salary Stop Details </strong></h4>
                </div>
                <?php echo form_open('', 'role="form" id="salaryStop_form" '); ?>
                <div class="modal-body">
                        <div class="row form-group col-sm-12 col-xs-6">
                            <label for="salaryStopDate">Salary Stop Date<?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="salaryStopDate" value="<?php echo ($setBlank == 'Y')? $employmentData['salaryStopDate'] : $dateTime ?>"
                                       id="salaryStopDate" class="form-control contractData"  
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" > 
                            </div>
                        </div>
                        <div class="row form-group col-sm-12 col-xs-6" style="margin-top:10px;">
                            <label for="salaryStopReason">Salary Stop Reason<?php required_mark(); ?></label>
                            <textarea class="form-control"  name="salaryStopReason" id="salaryStopReason" placeholder="Add reason here" rows="2"> <?php echo ($setBlank == 'Y')? $employmentData['salaryStopReason'] :'' ?> </textarea>
                        </div>
                </div>
                <hr>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_salaryStopDetail()">Save</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>



    <script type="text/javascript">
        var isEmploymentTypeFilledWithContract = '<?php echo ( !empty($employmentData['contractID']) )? 1 : 0; ?>';
        var historyTable = '';


        $(document).ready(function () {
            load_reporting_manager_history();

            /**set appear of salary stop detail button*/
            $('#isPayrollEmployee').on('change', function () {
                if ($(this).is(':checked')) {
                    $('.salaryStop_modal_button').addClass('hide');
                } else {
                    $('.salaryStop_modal_button').removeClass('hide');
                }
            });
            if($('#isPayrollEmployee').prop('checked',true)){
              $('.salaryStop_modal_button').addClass('hide');
            }else{
                $('.salaryStop_modal_button').removeClass('hide');
            }


        });


        var reportingManagerObj = $('#reportingManager');
        $('.select2').select2();


        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $('#mobileCreditLimit').numeric({
            negative: false,
            decimalPlaces: 3
        });
        $('#emp_Burden_Rate').numeric({
            negative: false,
            decimalPlaces: 3
        });

        function fetch_repManagersHistory_to_addModel() {
            reportingManagerObj.val(reportingManagerObj.attr('data-value'));
            $('#reportingManagerHistory').modal({backdrop: 'static'});

            historyTable = $('#reportingManagerHistoryTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_reporting_manager_history'); ?>",
                "aaSorting": [[0, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [0]}],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    if (oSettings.bSorted || oSettings.bFiltered) {

                        var x = 0;
                        for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                            x++;
                        }
                    }
                },
                "aoColumns": [
                    {"mData": "employeeManagersID"},
                    {"mData": "managerName"},
                    {"mData": "modifiedDate"},
                    {"mData": "modifiedUser"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "empId",
                        "value": <?php echo json_encode(trim($this->input->post('empID'))); ?>
                    });
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


        reportingManagerObj.autocomplete({
            serviceUrl: '<?php echo site_url();?>Employee/fetch_employees_typeAhead/?empID='+<?php echo trim($this->input->post('empID')); ?>,
            onSelect: function (suggestion) {
                $('#managerID').val(suggestion.data);
            }
        });

        function load_reporting_manager_history() {
            var Otable = $('#load_reporting_manager_history').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/load_reporting_manager_history'); ?>",
                "aaSorting": [[0, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [4, 5, 6]}],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $(".switch-chk").bootstrapSwitch();
                    if(fromHiarachy==1){
                        Otable.column( 6 ).visible( false );
                        //$(".switch-chk").attr('disabled',true);
                        $(".switch-chk").bootstrapSwitch("disabled",true);
                    }
                },
                "aoColumns": [
                    {"mData": "employeeManagersID"},
                    {"mData": "managerName"},
                    {"mData": "modifiedUser"},
                    {"mData": "modifiedDate"},
                    {"mData": "isprimary"},
                    {"mData": "isActive"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name': 'empID', 'value': empID});
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


        function delete_reportingManagers(id, name) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Employee/delete_reportingManagers'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'managerAutoID': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                setTimeout(function () {
                                    load_reporting_manager_history();
                                }, 400);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }


    
        function changePrimaryStatus_repManager(obj, id) {
            var msg, postPrimary;
            if ($(obj).prop('checked')) {
                msg = 'Primary';
                postPrimary = 1;
            } else {
                msg = 'Not Primary';
                postPrimary = 0;
            }
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to change this Reporting Manager as " + msg + "Reporting Manager",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            url: "<?php echo site_url('Employee/change_rep_manager_primary_status'); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {'managerAutoID': id, 'empID': empID, 'isprimary': postPrimary},
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] != 's') {
                                    var thisChk = $('#isprimarystatus' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                                }else{
                                    setTimeout(function () {
                                        load_reporting_manager_history();
                                    }, 400);
                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', 'error');
                            }
                        });
                    }
                    else {
                        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                        $('#isprimarystatus' + id).prop('checked', changeStatus).change();
                    }
                }
            );
        }


        function changeActiveStatus_repManager(obj, id) {
            var msg, postStatus;
            if ($(obj).prop('checked')) {
                msg = 'activate';
                postStatus = 1;
            } else {
                msg = 'inactivate';
                postStatus = 0;
            }

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_are_you_sure_you_want_to');?> " + msg + " Reporting Manager",/*You want to*/ /*designation!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            url: "<?php echo site_url('Employee/change_rep_manager_Active_status'); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {'managerAutoID': id, 'empID': empID, 'status': postStatus},
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] != 's') {

                                    var thisChk = $('#isActiveStatus' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', 'error');

                                var thisChk = $('#isActiveStatus' + id);
                                var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                var changeFn = thisChk.attr('onchange');

                                thisChk.removeAttr('onchange');
                                thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                            }
                        });
                    }
                    else {
                        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                        $('#isActiveStatus' + id).prop('checked', changeStatus).change();
                    }
                }
            );
        }


        reportingManagerObj.keyup(function (e) {
            if(e.which != 13){
                $('#managerID').val('');
            }
        });

        function update_dateAssume(obj) {
            $('#dateAssumed').val($(obj).val());
        }

        function save_employmentData() {
            var formData = $('#employmentData_form').serializeArray();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: formData,
                url: '<?php echo site_url('/Employee/save_employmentData_envoy/?empID=' . $this->input->post('empID'));?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if( 'record' in data ){
                            var record = data['record'];
                            $('#joinDate-display').html(record['joinDate-display']);
                            $('#period-display').html(record['period-display']);
                            $('#employmentTypeDisplay').html(record['employmentTypeDisplay']);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    function change_employee_activityCodeType(element, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_are_you_sure_you_want_to');?> Update employee Activity Code ? it might update the Employee Reporting Structure..!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {'empID': empID, 'activityCodeID': value},
                        url: '<?php echo site_url('Employee/change_employee_activityCodeType');?>',
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data.status, data.message);

                            if (data.status === 's' && data.record) {
                                var activityCodeID = data.record.activityCodeID;
                                update_employee_reporting_structure(activityCodeID);
                            }else{
                                }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    });
                }
            }
        );
    }

    function update_employee_reporting_structure(actiID){
        var activityID = actiID;
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: { 'empID': empID }, 
            url: '<?php echo site_url('Employee/update_employee_reporting_structure');?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                $('#activityCode').val(activityID);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


        function save_reportingManager() {
            var empID = '<?php echo $this->input->post('empID');?>';
            var managerID = $.trim($('#managerID').val());

            if (managerID != '') {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {'empID': empID, 'managerID': managerID},
                    url: '<?php echo site_url('/Employee/save_reportingManager');?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            historyTable.ajax.reload();
                            reportingManagerObj.attr('data-value', reportingManagerObj.val());
                            $('#reportingManagerDisplay').val(reportingManagerObj.val());
                            $('#reportingManagerHistory').modal('hide');
                            load_reporting_manager_history();

                            if( 'record' in data ){
                                var record = data['record'];

                                $('#managerName').html(record['managerName']).attr('onclick', 'edit_empDet('+record['managerId']+')');
                                $('#managerDesignation').html(record['managerDesignation']);
                                $('#managerImg').attr('src',record['managerImg']);
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
            else {
                myAlert('e', 'Selected manager id is not valid.<br/> Please select again');
            }

        }

        function update_visaDetails(isRenew=0) {
            var data = $('#employeeVisa_form').serializeArray();
            var requestUrl = '<?php echo site_url('Employee/update_contact_details'); ?>';

            data.push({'name': 'updateID', 'value':<?php echo json_encode(trim($this->input->post('empID'))); ?>});
            data.push({'name': 'isRenew', 'value':isRenew});

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: data,
                url: requestUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        isEmploymentTypeFilledWithContract = 1;
                        $('#contractID').val(data[2]);
                        $('#contract-re-new').show();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', errorThrown);
                }
            });

        }

        function getDate_contract(){
            var period =  $('#employeeConType :selected').attr('data-period');
            var startDate = $.trim($('#contractStartDate').val());

            if( startDate != '' && period != ''){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {'pr_period': period, 'empDoj': startDate},
                    url: '<?php echo site_url('/Employee/getDate');?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#contractEndDate').val(data[0]);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        }

        function calculateDate(){
            var valType = $('#employeeConType :selected').attr('data-type');
            var pr_period =  $('#employeeConType :selected').attr('data-pr-period');
            var period =  $('#employeeConType :selected').attr('data-period');
            var empDoj = $.trim($('#empDoj').val());

            $('.contractData').attr('disabled', (valType != 2));

            if( valType != 4 ){

                if( empDoj != '' ){
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {'valType':valType, 'period': period, 'pr_period': pr_period, 'empDoj': empDoj},
                        url: '<?php echo site_url('/Employee/getDate');?>',
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();

                            $('#probationPeriod').val(data[0]).hide().fadeIn('slow');

                            if( valType == 2 ){
                                if( isEmploymentTypeFilledWithContract != 1){
                                    $('#contractStartDate').val(empDoj).hide().fadeIn('slow');
                                    $('#contractEndDate').val(data[1]).hide().fadeIn('slow');
                                }

                            }else{
                                $('#contractStartDate, #contractEndDate, #contractRefNo').val('').hide().fadeIn('slow');
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    });
                }
            }
        }

        function visaType_select(ev){
            startLoad();
            
            var visaType = ev.val();
            if(visaType == 2){
                $('#visaPartyID_sec').removeClass('hide');
            }else{
                $('#visaPartyID_sec').addClass('hide');
            }

            stopLoad();
        }

        function fetchContractHistory(){

            $('#contractHistory').modal({backdrop: 'static'});

            $('#contractHistoryTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_contractHistory'); ?>",
                "aaSorting": [[1, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [0]}],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    if (oSettings.bSorted || oSettings.bFiltered) {

                        var x = 0;
                        for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                            x++;
                        }
                    }
                },
                "aoColumns": [
                    {"mData": "contractID"}, //, , contractEndDate, isCurrent
                    {"mData": "contractStartDate"},
                    {"mData": "contractEndDate"},
                    {"mData": "contractRefNo"},
                    {"mData": "isCurrentStr"},
                    {"mData": "action"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "empID",
                        "value": <?php echo json_encode(trim($this->input->post('empID'))); ?>
                    });
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

        function contractReNew(){
            swal({
                    title: "Are you sure?",
                    text: "You want to renew the contract!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                }, function () {
                    update_visaDetails(1)
                }
            );
        }

        function delete_contract(contractID){
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this contract!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                }, function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: {'contractID':contractID},
                    url: '<?php echo site_url('Employee/delete_empContract'); ?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            fetchContractHistory();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });
                }
            );
        }

        function load_contractExcel(){
            window.open('<?php echo site_url('Employee/export_excelContractHistory').'/'.$this->input->post('empID'); ?>','_blank');
        }

        function load_contractPdf(){
            var empCode = $('#empCode').val();
            window.open('<?php echo site_url('Employee/print_contractHistory').'/'.$this->input->post('empID'); ?>/'+empCode,'_blank');
        }

        $('.number').keypress(function (event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });

        function salaryStop_modal(){
           // $('#salaryStopReason').val('');
            $('#salaryStop_modal').modal({backdrop: 'static'});
        }

        function save_salaryStopDetail(){
            var data = $('#salaryStop_form').serializeArray();
            data.push({'name': 'empID', 'value': empID});
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: '<?php echo site_url('Employee/save_salaryStopDetail');?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if(data[0]=='s'){
                            $('#salaryStopDate').val('<?php echo current_date(); ?>').change();
                            $('#salaryStopReason').val('');

                            $('#salaryStop_modal').modal('hide');
                        }else{
                            $('#salaryStopDate').val('').change();
                            $('#salaryStopReason').val('');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });

        }

        function checkStatus(airTicketEligibility){
            if(airTicketEligibility==2){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: { 'empID': empID }, 
                    url: '<?php echo site_url('Employee/checkStatus');?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if(data['MaritialStatus']!=2){
                            myAlert('e','Your material status is Single');
                            $('#airTicketEligible').val('').change();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        }

       
    </script>
<?php
