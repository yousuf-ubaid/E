<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$designation = fetch_emp_designation();
$currency_arr = all_currency_new_drop();  //all_currency_drop();
$employeeConType = fetch_sysEmpContractType();
$segment_arr = segment_drop();
$grade_arr = grade_drop();
$gratuity_arr = gratuity_drop();
$date_format_policy = date_format_policy();
$readonly = ($isSalaryDeclared > 0) ? 'readonly' : '';


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
                                <?php echo $this->lang->line('emp_date_assumed'); ?><!--Date Assumed --><?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateAssumed"
                                       value="<?php echo $employmentData['DateAssumed']; ?>" id="dateAssumed"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" <?php echo $readonly; ?>>
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="employeeConType">
                                <?php echo $this->lang->line('emp_type'); ?><!--Employee Type--><?php echo required_mark(); ?></label>
                            <?php echo form_dropdown('employeeConType', $employeeConType, $employmentData['EmployeeConType'], 'class="form-control select2" id="employeeConType" '); ?>
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
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="designation">
                                <?php echo $this->lang->line('emp_segment'); ?><!--Segment --><?php required_mark(); ?></label>
                            <?php echo form_dropdown('empSegment', $segment_arr, $employmentData['segmentID'], 'class="form-control select2" id="empSegment" '); ?>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="probationPeriod">
                                <?php echo $this->lang->line('emp_probation_period'); ?><!--Probation Period--></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo $this->lang->line('emp_in_month'); ?><!-- In Month --></div>
                                <input type="text" class="form-control number" id="probationPeriod"
                                       name="probationPeriod"
                                       value="<?php echo $employmentData['probationPeriodMonth']; ?>">
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
                            <label for=""><?php echo $this->lang->line('emp_employee_man_power_no'); ?><!--Man Power No--></label>
                            <input type="text" class="form-control" id="manPowerNo" name="manPowerNo"
                                   value="<?php echo $employmentData['manPowerNo'] ?>" placeholder="<?php echo $this->lang->line('emp_employee_man_power_no'); ?>"
                                   style="width:100%;">
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="grade"><?php echo $this->lang->line('emp_grade');?>  </label><!--Grade-->
                            <?php
                            echo form_dropdown('gradeID', $grade_arr,$employmentData['gradeID'] , 'class="form-control select2" id="gradeID" ');
                            ?>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
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

                        <div class="form-group col-sm-2 col-xs-6">
                            <label for=""><?php echo $this->lang->line('common_mobile_credit_limit'); ?></label>
                            <input type="text" class="form-control numeric" id="mobileCreditLimit" name="mobileCreditLimit"
                                   value="<?php echo $employmentData['mobileCreditLimit'] ?>" placeholder="<?php echo $this->lang->line('common_mobile_credit_limit'); ?>"
                                   style="width:100%;">
                        </div>
                    </div>
                </div>

                <div class="clearfix">&nbsp;</div>
                <div class="row" style="margin: 15px;">
                    <hr style="margin: 0px 0px 10px;">
                    <button type="button" class="btn btn-primary size-sm pull-right" onclick="save_employmentData()">
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
                <?php echo form_open('', 'role="form" id="employeeVisa_form" '); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="contractStartDate">
                                <?php echo $this->lang->line('emp_contract_start_date'); ?><!--Contract Start Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="contractStartDate"
                                       value="<?php echo $employmentData['contractStartDate'] ?>" id="contractStartDate"
                                       class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="contractEndDate">
                                <?php echo $this->lang->line('emp_contract_end_date'); ?><!--Contract End Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="contractEndDate"
                                       value="<?php echo $employmentData['contractEndDate'] ?>" id="contractEndDate"
                                       class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="airport_destination">
                                <?php echo $this->lang->line('emp_contract_ref_no'); ?><!--Contract Ref No.--></label>
                            <input type="text" name="contractRefNo" id="contractRefNo" class="form-control"
                                   value="<?php echo $employmentData['contractRefNo'] ?>" placeholder="">
                        </div>

                    </div>

                    <div class="col-md-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="pass_portNo">
                                <?php echo $this->lang->line('emp_passport_no'); ?><!--Passport No--></label>
                            <input type="text" class="form-control" id="pass_portNo" name="pass_portNo"
                                   value="<?php echo $employmentData['EPassportNO'] ?>" placeholder="<?php echo $this->lang->line('common_passport_number_no'); ?>"
                                   style="width:100%;">
                        </div><!--Passport No-->

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

                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="airport_destination">
                                <?php echo $this->lang->line('emp_airport_destination'); ?><!--Airport Destination--></label>
                            <input type="text" name="airport_destination" id="airport_destination" class="form-control"
                                   value="<?php echo $employmentData['AirportDestination'] ?>"
                                   placeholder="<?php echo $this->lang->line('emp_airport_destination'); ?>">
                            <!--Airport Destination-->
                        </div>
                    </div>

                </div>

                <div class="clearfix">&nbsp;</div>
                <div class="row" style="margin: 15px;">
                    <hr style="margin: 0px 0px 10px;">
                    <button type="button" class="btn btn-primary btn-sm pull-right"
                            onclick="update_visaDetails()"><?php echo $this->lang->line('emp_save'); ?>
                        <!--Save Changes--></button>
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
                        <?php echo $this->lang->line('emp_reporting_manager_history'); ?><!--Reporting Manager History-->
                    </h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <fieldset>
                                    <legend>
                                        <?php echo $this->lang->line('emp_add_reporting_manager'); ?><!--Add Reporting Manager--></legend>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="reportingManager"><?php echo $this->lang->line('emp_reporting_manager'); ?><!--Reporting Manager--></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="reportingManager"
                                                       name="reportingManager"
                                                       value="<?php echo $employmentData['managerName']; ?>"
                                                       data-value="<?php echo $employmentData['managerName']; ?>">
                                                <input type="hidden" value="" id="managerID" name="managerID"/>
                                            </div>
                                            <div class="clearfix visible-xs visible-sm">&nbsp;</div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-primary btn-sm hidden-sm hidden-xs"
                                                        onclick="save_reportingManager('Print')"><?php echo $this->lang->line('common_save'); ?><!--Save-->
                                                </button>
                                                <button type="button"
                                                        class="btn btn-primary btn-sm hidden-lg pull-right"
                                                        onclick="save_reportingManager('Print')"><?php echo $this->lang->line('common_save'); ?><!--Save-->
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
                                    <th><?php echo $this->lang->line('emp_manager_name'); ?><!--Manager Name--></th>
                                    <th><?php echo $this->lang->line('emp_modified_date'); ?><!--Modified Date--></th>
                                    <th><?php echo $this->lang->line('emp_modified_user'); ?><!--Modified User--></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
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


        $('#reportingManager').autocomplete({
            serviceUrl: '<?php echo site_url();?>Employee/fetch_employees_typeAhead/?empID='+<?php echo trim($this->input->post('empID')); ?>,
            onSelect: function (suggestion) {
                $('#managerID').val(suggestion.data);
            }
        });

        $('#reportingManager').keyup(function () {
            $('#managerID').val('');
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
                url: '<?php echo site_url('/Employee/save_employmentData/?empID=' . $this->input->post('empID'));?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {

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
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
            else {
                myAlert('e', '<?php echo $this->lang->line('emp_selected_manager_id_not_valid');?>.<br/><?php echo $this->lang->line('emp_please_select_again');?>');<!--Selected manager id is not valid-->/*Please select again*/
            }

        }

        function update_visaDetails() {
            var data = $('#employeeVisa_form').serializeArray();
            var requestUrl = '<?php echo site_url('Employee/visaDetails_update'); ?>';

            data.push({'name': 'updateID', 'value':<?php echo json_encode(trim($this->input->post('empID'))); ?>});

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
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', errorThrown);
                }
            });

        }

        $('.number').keypress(function (event) {

            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });
    </script>
<?php
