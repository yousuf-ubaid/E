<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);

$designation = fetch_emp_designation();
$currency_arr = all_currency_new_drop();  //all_currency_drop();
$employeeConType = fetch_empContractType();
$segment_arr = segment_drop();
$grade_arr = grade_drop();
$scheme_arr = commission_scheme_drop();
$gratuity_arr = gratuity_drop();
$familyStatus_arr = familyStatus_drop();
$location_arr = all_company_location_code_drop();
$tFrequency_arr = travel_frequency_drop();
$sponser_arr = sponser_drop();
$date_format_policy = date_format_policy();
$disable = '';
$setBlank = '';
$readonly = ($isSalaryDeclared > 0) ? 'readonly' : '';

if( !empty($employmentData) ){
    $disable = ( $employmentData['typeID'] != 2 )? 'disabled' : '';
    $setBlank = ( $employmentData['typeID'] != 2 )? 'Y' : '';
}


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
                        <label for="empCurrency">
                            <?php echo $this->lang->line('emp_currency'); ?><!--Currency --><?php echo required_mark(); ?></label>
                        <?php
                        //$this->common_data['company_data']['company_default_currency'];
                        echo form_dropdown('empCurrency', $currency_arr, $employmentData['payCurrencyID'], 'class="form-control select2" id="empCurrency" ');
                        ?> <!--onchange="currency_validation_modal(this.value)"-->
                    </div>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="empSegment">
                            <?php echo $this->lang->line('emp_segment'); ?><!--Segment --><?php required_mark(); ?></label>
                        <?php echo form_dropdown('empSegment', $segment_arr, $employmentData['segmentID'], 'class="form-control select2" id="empSegment" '); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if(IS_OMAN_OIL == false){
                    ?>
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="empLocation">
                            <?php echo $this->lang->line('common_Location'); ?></label>
                        <?php echo form_dropdown('empLocation', $location_arr, $employmentData['locationID'], 'class="form-control select2" id="empLocation" '); ?>
                    </div>
                    <?php } ?>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="probationPeriod">
                            <?php echo $this->lang->line('emp_probation_period'); ?><!--Probation End date--></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="probationPeriod" value="<?php echo $employmentData['probationPeriodCnvt']; ?>"
                                   id="probationPeriod" class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                        </div>
                    </div>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="reportingManager">
                            <?php echo $this->lang->line('emp_reporting_manager'); ?><!--Reporting Manager--></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="reportingManagerDisplay" name=""
                                   value="<?php echo $employmentData['managerName']; ?>" readonly>
                            <div class="input-group-addon" onclick="fetchReportingManagerHistory()">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </div>
                        </div>
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
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="pass_portNo">
                            <?php echo $this->lang->line('emp_passport_no'); ?><!--Passport No--></label>
                        <input type="text" class="form-control" id="pass_portNo" name="pass_portNo"
                               value="<?php echo $employmentData['EPassportNO'] ?>" placeholder="Passport No"
                               style="width:100%;">
                    </div>

                    <!--<div class="form-group col-sm-3 col-xs-6">
                        <label for="passPort_expiryDate">
                            <?php /*echo $this->lang->line('emp_passport_expiry_date'); */?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="passPort_expiryDate"
                                   value="<?php /*echo $employmentData['EPassportExpiryDate'] */?>"
                                   id="passPort_expiryDate"
                                   class="form-control"
                                   data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'">
                        </div>
                    </div>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="passPort_expiryDate">
                           ID Expiry Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="visa_expiryDate"
                                   value="<?php /*echo $employmentData['EVisaExpiryDate'] */?>" id="visa_expiryDate"
                                   class="form-control"
                                   data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'">
                        </div>
                    </div>-->

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="airport_destination">
                            <?php echo $this->lang->line('emp_airport_destination'); ?><!--Airport Destination--></label>
                        <input type="text" name="airport_destination" id="airport_destination" class="form-control"
                               value="<?php echo $employmentData['AirportDestination'] ?>"
                               placeholder="<?php echo $this->lang->line('emp_airport_destination'); ?>">
                        <!--Airport Destination-->
                    </div>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="trFrequency">
                            <?php echo $this->lang->line('common_travel_frequency'); ?></label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="add_frequency()"
                                        style="height: 27px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>
                            <?php echo form_dropdown('trFrequency', $tFrequency_arr, $employmentData['travelFrequencyID'], 'class="form-control select2" id="trFrequency" '); ?>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="commissionSchemeID"><?php echo $this->lang->line('emp_master_commission_scheme');?> <!--Grade--></label>
                        <?php
                        echo form_dropdown('commissionSchemeID', $scheme_arr, $employmentData['commissionSchemeID'], 'class="form-control select2" id="commissionSchemeID" ');
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-2 col-xs-6">
                        <label for=""><?php echo $this->lang->line('emp_employee_man_power_no'); ?><!--Man Power No--></label>
                        <input type="text" class="form-control" id="manPowerNo" name="manPowerNo"
                               value="<?php echo $employmentData['manPowerNo'] ?>" placeholder="<?php echo $this->lang->line('emp_employee_man_power_no'); ?>"
                               style="width:100%;">
                    </div>
                    <div class="form-group col-sm-2 col-xs-6">
                        <label for="grade"><?php echo $this->lang->line('emp_grade');?> <!--Grade--></label>
                        <?php
                        echo form_dropdown('gradeID', $grade_arr, $employmentData['gradeID'], 'class="form-control select2" id="gradeID" ');
                        ?>
                    </div>

                    <div class="form-group col-sm-2 col-xs-6">
                        <label for="pos_barCode"><?php echo $this->lang->line('emp_employee_bar_code'); ?><!--Barcode--></label>
                        <input type="text" class="form-control" id="pos_barCode" name="pos_barCode"
                               value="<?php echo $employmentData['pos_barCode'] ?>" placeholder="<?php echo $this->lang->line('emp_employee_bar_code'); ?>"
                               style="width:100%;">
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="gratuityID"><?php echo $this->lang->line('emp_employee_gratuity'); ?></label>
                        <?php
                        echo form_dropdown('gratuityID', $gratuity_arr, $employmentData['gratuityID'], 'class="form-control select2" id="gratuityID" ');
                        ?>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="familyStatusID"><?php echo $this->lang->line('emp_employment_status'); ?></label>
                        <?php
                        echo form_dropdown('familyStatusID', $familyStatus_arr, $employmentData['familyStatusID'], 'class="form-control select2" id="familyStatusID" ');
                        ?>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-2 col-xs-6">
                        <label for=""><?php echo $this->lang->line('common_mobile_credit_limit'); ?></label>
                        <input type="text" class="form-control numeric" id="mobileCreditLimit" name="mobileCreditLimit"
                               value="<?php echo $employmentData['mobileCreditLimit'] ?>" placeholder="<?php echo $this->lang->line('common_mobile_credit_limit'); ?>"
                               style="width:100%;">
                    </div>

                    <?php
                    /*******************************************************************************************************************************************
                    Company id 373 OMAN OIL for urgent release as discussed with Hisham decided to hard cord the company and implement the logic
                     *******************************************************************************************************************************************/

                    if(IS_OMAN_OIL){
                    ?>
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="trFrequency"> Sponsor</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="add_sponser()"
                                        style="height: 27px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>
                            <?php echo form_dropdown('sponserID', $sponser_arr,$employmentData['sponsorID'], 'class="form-control select2" id="sponserID" '); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="clearfix">&nbsp;</div>
            <div class="row" style="margin: 15px;">
                <hr style="margin: 0px 0px 10px;">
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_employmentData()">
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
            <legend> <?php echo $this->lang->line('emp_contract_details'); ?> <!--Contract Details--> </legend>
            <div class="row" style="margin: 0px 15px;">
                <button type="button" class="btn btn-primary btn-xs pull-right navdisabl" onclick="fetchContractHistory()">
                    <i class="fa fa-bars"></i> Contract History</button>
            </div>
            <?php echo form_open('', 'role="form" id="employeeVisa_form" '); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="contractStartDate">
                            <?php echo $this->lang->line('emp_contract_start_date'); ?><!--Contract Start Date--></label>
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
                    <button type="button" class="btn btn-primary btn-sm contractData" id="contract-re-new" onclick="contractReNew()"
                            style="<?php echo $isDisplay;?>"> Renew </button>
                    <button type="button" class="btn btn-primary btn-sm contractData" <?php echo $disable ?> onclick="update_visaDetails()">
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
                    <?php echo $this->lang->line('emp_reporting_manager_history');?><!--Reporting Manager History-->
                </h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <fieldset>
                                <legend><?php echo $this->lang->line('emp_add_reporting_manager');?><!--Add Reporting Manager--></legend>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="reportingManager">Reporting
                                            Manager</label>
                                        <div class="col-sm-5">
                                            <input type="hidden" value="" id="managerID" name="managerID"/>
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
                    <button class="btn btn-pdf btn-xs pull-right" id="btn-pdf" type="button" onclick="load_contractPdf()" style="margin-right: 1%;">
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

<div class="modal fade" id="travel_frequency-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_add_travel_frequency');?> </h3>
            </div>
            <form role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?> </label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add_travel_frequency" name="add_travel_frequency">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_new_travel_frequency()"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="modal fade" id="sponser-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Add Sponser</h3>
                </div>
                <form role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?> </label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="add_sponser" name="add_sponser">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_new_sponser()"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    </div>
                </form>
            </div>
        </div>
    </div>


<script type="text/javascript">
    var isEmploymentTypeFilledWithContract = '<?php echo ( !empty($employmentData['contractID']) )? 1 : 0; ?>';
    var historyTable = '';
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

    function fetchReportingManagerHistory() {
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
                {"mData": "Ename2"},
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


    reportingManagerObj.keyup(function (e) {
        if(e.which != 13){
            $('#managerID').val('');
        }
    });

    function save_employmentData() {
        var formData = $('#employmentData_form').serializeArray();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: formData,
            url: '<?php echo site_url('/Employee/save_employmentData_tibian/?empID=' . $this->input->post('empID'));?>',
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
        var requestUrl = '<?php echo site_url('Employee/update_contact_details/tibian'); ?>';

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

    function add_frequency(){
        $('#add_travel_frequency').val('');
        $('#travel_frequency-modal').modal('show');
    }

    function save_new_travel_frequency(){
        var travel_frequency = $('#add_travel_frequency').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'travel_frequency': travel_frequency},
            url: '<?php echo site_url("Employee/new_travel_frequency"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var trFrequency_obj = $('#trFrequency');
                if (data[0] == 's') {
                    trFrequency_obj.select2('destroy');
                    trFrequency_obj.append('<option value="' + data[2] + '">' + travel_frequency + '</option>');
                    trFrequency_obj.val(data[2]);
                    trFrequency_obj.select2();
                    $('#travel_frequency-modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    function add_sponser(){
        $('#add_sponser').val('');
        $('#sponser-modal').modal('show');
    }
    function save_new_sponser() {
        var add_sponser = $('#add_sponser').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'sponser': add_sponser},
            url: '<?php echo site_url("Employee/new_sponser_frequency"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var sponser_obj = $('#sponserID');
                if (data[0] == 's') {
                    sponser_obj.select2('destroy');
                    sponser_obj.append('<option value="' + data[2] + '">' + add_sponser + '</option>');
                    sponser_obj.val(data[2]);
                    sponser_obj.select2();
                    $('#sponser-modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
</script>

<?php
