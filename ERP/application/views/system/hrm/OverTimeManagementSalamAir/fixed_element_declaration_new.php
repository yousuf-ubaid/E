
<!--Translation added by Naseek-->





<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_eixed_element_declaration');
echo head_page($title, false);


$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();
if( !empty($this->input->post('page_id')) ){
    $employee_drop = getEmployeesFixedElementDeclaration($this->input->post('page_id'));
    $salaryCategories_drop = salaryCategories_drop($this->input->post('page_id'));
}else{
    $employee_drop = null;
    $salaryCategories_drop = null;
}
$fixedElemant_arr = fetch_fixed_element_master();
?>
<form class="form-horizontal" id="declaration_form">
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_over_time_document_date');?><!--Document Date--></label>
        </div>
        <div class="form-group col-sm-4">
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type='text' class="form-control" id="documentDate" name="documentDate" value="<?php echo $current_date; ?>"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
            </div>
        </div>
    </div>
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
        </div>
        <div class="form-group col-sm-4">
            <?php echo form_dropdown('MasterCurrency', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="MasterCurrency" required');?>
        </div>
    </div>
    <div class="row hide" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_over_time_payroll_type');?><!--Payroll Type--></label>
        </div>
        <div class="form-group col-sm-4">
            <select name="isPayrollCategory" id="isPayrollCategory" class="form-control">
                <option value="1"><?php echo $this->lang->line('hrms_over_time_payroll');?><!--Payroll--></option>
                <option value="2"><?php echo $this->lang->line('hrms_over_time_non_payroll');?><!--Non payroll--></option>
            </select>
        </div>
    </div>
    <div class="row hide" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_over_time_initial_declaration');?><!--Initial Declaration--></label>
        </div>
        <div class="form-group col-sm-4">
            <select name="isInitialDeclaration" id="isInitialDeclaration" class="form-control">
                <option value="1"><?php echo $this->lang->line('common_yes');?><!--Yes--></option>
                <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
        </div>
        <div class="form-group col-sm-8">
            <textarea class="form-control" id="salary_description" name="salary_description"
                      rows="2"></textarea>
        </div>
    </div>
    <div class="text-right m-t-xs">
        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
    </div>
</form>
<div id="outputSalaryDeclaration_detail"></div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="fixedElementDeclarationDetailModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 99%" role="document">
        <div class="modal-content">
            <form class="" id="declaration_save_detail_form" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_over_time_fixed_element_declaration_detail');?><!--Fixed Element Declaration Detail--></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" style="color: red; margin-left: 1%">
                            <?php echo $this->lang->line('common_note');?><!--Note--> : <?php echo $this->lang->line('hrms_over_time_deduction_amount_should_be_entered_with_a');?><!--Deduction amount should be entered with a--> ( - )
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <table class="<?php echo table_class(); ?>" style="min-width:1000px;">
                                    <thead>
                                    <tr>
                                        <th width=""><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employee--></th>
                                        <th width="130px"><?php echo $this->lang->line('hrms_over_time_effective_date');?><!--Effective Date--></th>
                                        <th width=""><?php echo $this->lang->line('common_category');?><!--Category--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_over_time_current_amount');?><!--Current Amount--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_over_time_new_amount');?><!--New Amount--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_over_time_adjustment');?><!--Adjustment--></th>
                                        <th width="150px"><?php echo $this->lang->line('hrms_over_time_narration');?><!--Narration--></th>
                                        <th width="5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="max-width: 240px">
                                            <div class="form-group">
                                                <select name="employee" class="select2 form-control" id="employee" required onchange="getEffectiveDate()">
                                                    <?php
                                                    $select_emp = $this->lang->line('hrms_over_time_select_employee');
                                                    $option = "<option value> $select_emp<!--Select Employee--> </option>";
                                                    if (!empty($employee_drop)) {
                                                        foreach ($employee_drop as $row) {
                                                            $employeeData = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                                                            $option .= "<option value='{$row['EIdNo']}' data-value='{$row['EDOJ']}' data-decimal-places='{$row['dPlace']}'>
                                                                       {$employeeData}</option>";
                                                        }
                                                    }
                                                    echo $option;
                                                    ?>
                                                </select>
                                            </div>
                                            <input type="hidden" name="feDeclarationMasterID" id="feDeclarationMasterID">
                                        </td>
                                        <td style="min-width: 100px;">
                                            <div class="form-group">
                                                <div class="input-group datepic">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="effectiveDate"  id="effectiveDate" class="form-control"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <?php echo form_dropdown('cat[]', $fixedElemant_arr, '', 'class="form-control" id="salarySubCatID"
                                                onchange="getMasterCatType(this)"'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text"  name="currentAmount"  class="form-control number" id="currentAmount" readonly/>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" name="newAmount" class="form-control numeric number" id="newAmount"
                                                       onkeyup="getNewAmount(this)" autocomplete="off" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control numeric number" name="amount[]" id="amount" readonly />
                                            </div>
                                            <input type="hidden" name="saveDeclarationType" id="saveDeclarationType" value="amount"/>
                                        </td>
                                        <td>
                                           <div class="form-group">
                                                <input type="text" name="narration" class="form-control" id="narration" />
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm" id="addBtn"><?php echo $this->lang->line('common_add');?><!--Add--></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="response-div"></div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="clearfix">&nbsp;</div>

                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 30%"><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employee--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_over_time_effective_date');?><!--Effective Date--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('common_category');?><!--Category--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_over_time_narration');?><!--Narration--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                                        <th>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="salary-fixed-element-declaration-tbody">
                                    </tbody>
                                    <tfoot id="salary-fixed-element-declaration-foot">
                                    <tr>
                                        <td colspan="4"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                                        <td style="text-align: right"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="clearRecords()" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    var n;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/hrm/OverTimeManagementSalamAir/fixed_element_declaration', p_id, 'Salary Declaration');
        });

        if (p_id) {
            LoadSalaryDeclarationMaster(p_id);
            //getEmployeesCurrency_edit(p_id); 
        }

        $('.select2').select2();

        $('#amount_1').bind("cut copy paste",function(e) {
            e.preventDefault();
        });

        fixed_element_declaration_table();

        /*$('#documentDate').datepicker({
         format: 'yyyy-mm-dd'
         }).on('changeDate', function (ev) {
         $('#declaration_form').bootstrapValidator('revalidateField', 'documentDate');
         $(this).datepicker('hide');
         });*/

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev, obj) {
            if( $(this).attr('id') == 'effectiveDate' ){
                $('#declaration_save_detail_form').bootstrapValidator('revalidateField', 'effectiveDate');
            }

            if( $(obj).attr('id') == 'documentDate' ){
                $('#declaration_form').bootstrapValidator('revalidateField', 'effectiveDate');
            }
        });


        $("input.numeric").numeric();

        $('#declaration_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                MasterCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_are_you_sure');?>.'}}},/*Currency is required*/
                salary_description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_are_you_sure');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#MasterCurrency option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_fixed_element_salaryDeclaration'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        //LoadSalaryDeclarationMaster(data[2]);
                        setTimeout(function(){
                            fetchPage('system/hrm/OverTimeManagementSalamAir/fixed_element_declaration_new',data[2],'HRMS');
                        }, 400);

                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    myAlert('e', 'Error');
                }
            });
        });

        $('#declaration_save_detail_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    employee: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_employee_is_required');?>.'}}},/*Employee is required*/
                    effectiveDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_effective_date_is_required');?>.'}}},/*Effective Date is required*/
                    //payDate: {validators: {notEmpty: {message: 'Pay Date is required.'}}},
                    newAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_new_amount_is_required');?>.'}}},/*New amount is required*/
                    salarySubCatID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_category_is_required');?>.'}}}/*Category is required*/
                }
            })
            .on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var  employeeObj = $('#employee');
                var  employee = employeeObj.val();
                employeeObj.prop("disabled", false);
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/save_fixed_element_declaration_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //$('#response-div').html(data);
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            LoadSalaryDeclarationMaster(data[2]);
                            getDrilldownTableData(data[2], employee);

                            $("#employee").prop("disabled", true);

                            var effectiveDateObj = $("#effectiveDate");
                            //var payDateObj = $('#payDate');

                            effectiveDateObj.attr('data-value', effectiveDateObj.val());
                            //payDateObj.attr('data-value', payDateObj.val());

                            $("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
                            $("#amount, #currentAmount, #newAmount, #narration").val('');

                            $("#declaration_save_detail_form").data('bootstrapValidator').resetForm();

                            effectiveDateObj.val(effectiveDateObj.attr('data-value'));
                            //payDateObj.val(payDateObj.attr('data-value'));
                        }
                        $('#addBtn').prop('disabled', false)
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown)
                        $('#addBtn').prop('disabled', false)
                    }
                });
            });

        //LoadSalaryDeclarationMaster();
    });

    function LoadSalaryDeclarationMaster(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/fetch_fixedElementDeclaration_Master') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#declaration_form').hide();
                $("#outputSalaryDeclaration_detail").show();
                $("#outputSalaryDeclaration_detail").html(data);
                $("#feDeclarationMasterID").val(id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
        return false;
    }

    function getEmployeesCurrency_edit(masterID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/getDeclarationmasterCurrency_edit'); ?>",
            data: {masterID: masterID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                $('#MasterCurrency').val(data['transactionCurrencyID']);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function confirmSalaryDeclaration() {
        bootbox.confirm("Are you sure want to confirm this fixed element declaration?", function (confirmed) {
            if (confirmed) {
                var masterID = $('#feDeclarationMasterID').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Employee/ConfirmFixedElementDeclaration'); ?>",
                    data: {masterID: masterID},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/hrm/OverTimeManagementSalamAir/fixed_element_declaration', '', 'HRMS');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            }
        });

    }

    function delete_item(detailID, masterID) {
        swal({
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
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID},
                    url: "<?php echo site_url('Employee/delete_fixedElement_declaration_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        LoadSalaryDeclarationMaster(masterID);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_fixedElementDetail(detailID, masterID, employee) {
        swal({
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
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID},
                    url: "<?php echo site_url('Employee/delete_fixedElement_declaration_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        getDrilldownTableData(masterID, employee);
                        LoadSalaryDeclarationMaster(masterID);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function getDrilldownTableData(masterID, employeeID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/load_fixedElement_declaration_drilldown_table'); ?>",
            data: {masterID: masterID, employeeID: employeeID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad()
            },
            success: function (options) {
                stopLoad();
                $('#salary-fixed-element-declaration-tbody').empty();
                $('#salary-fixed-element-declaration-foot').empty();
                if (jQuery.isEmptyObject(options)) {
                    $('#salary-fixed-element-declaration-tbody').append('<tr style="text-align: center"><td colspan="6" align="center"><?php echo $this->lang->line('common_no_records_available');?></td></tr>');<!--No Records Available-->
                }
                else {
                    var a = 0;
                    $.each(options, function (key, value) {
                        var appStr = '<tr><td>' + value['ECode'] + ' | ' + value['Ename2'] + '</td>';
                        appStr += '<td>' + value['effectiveDate2'] + '</td><td>' + value['fixedElementDescription'] + '</td>';
                        appStr += '<td>' + value['narration'] + '</td>';
                        appStr += '<td style="text-align: right">' + commaSeparateNumber(value['transactionAmount']) + '</td>';
                        appStr += '<td style="text-align: right">';
                        appStr += '<a onclick="delete_fixedElementDetail(' + value['feDeclarationDetailID'] + ',' + value['feDeclarationMasterID'] + ',' + value['employeeNo'] + ')">';
                        appStr += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        appStr += '</td></tr>';

                        $('#salary-fixed-element-declaration-tbody').append(appStr);
                        a += parseFloat(value['transactionAmount']);
                    });

                    var noRecordStr = '<tr><td colspan="5" style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_no_records_available');?></td>';<!--Total-->
                    noRecordStr += '<td class="text-right total">' + commaSeparateNumber(a) + '</td></tr>';
                    $('#salary-fixed-element-declaration-foot').html(noRecordStr);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown)
            }
        });
    }

    function clearRecords(){

        $("#employee").val('').change().prop("disabled", false);

        // data-value assigned in  employee_salary_declaration_detail.php page
        var effectiveDateObj = $("#effectiveDate");
        effectiveDateObj.val(effectiveDateObj.attr('data-value'));
        $('#payDate').val(effectiveDateObj.attr('data-value'));
        //$("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
        $("#amount, #newAmount, #currentAmount, #narration").val('');

        $("#declaration_save_detail_form").data('bootstrapValidator').resetForm();
        $('#addBtn').prop('disabled', false);
        $('#salary-fixed-element-declaration-tbody').html('<tr style="text-align: center"><td colspan="7" align="center"><?php echo $this->lang->line('common_no_records_found');?></td></tr>');<!--No Records Available-->

        var noRecordStr = '<tr><td colspan="5" style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_total');?></td>';<!--Total-->
        noRecordStr += '<td class="text-right total">' + commaSeparateNumber(0) + '</td></tr>';
        $('#salary-fixed-element-declaration-foot').html(noRecordStr);

    }

    function getMasterCatType(obj){
        $('.salaryType').val($('#salarySubCatID :selected').attr('data-value'));
        $('#currentAmount').val('');
        $('#amount').val('');
        $('#newAmount').val('');

        if( $(obj).val() != ''){
            var empID = $('#employee').val();
            var effectiveDate = $('#effectiveDate').val();
            var catID = $(obj).val();

            if(empID == ''){
                myAlert('e', 'Select the employee first');
                $(obj).val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
            }else if(effectiveDate == ''){
                myAlert('e', 'Select the effective date first');
                $(obj).val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
            }else{
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empID':empID, 'catID':catID, 'effectiveDate':effectiveDate},
                    url: "<?php echo site_url('Employee/get_empFixedElementTotal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#currentAmount').val(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown)
                    }
                });
            }
        }
        else{
            $('#currentAmount').val('');
        }
    }

    function getNewAmount(obj){
        var dPlace = $('#employee :selected').attr('data-decimal-places');
        var currentAmount = getNumberAndValidate($('#currentAmount').val(), dPlace);
        var newAmount = getNumberAndValidate($(obj).val(), dPlace);
        var amount = newAmount-currentAmount;

        $('#amount').val( commaSeparateNumber(amount, dPlace) );
    }

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = parseFloat(thisVal.replace(/,/g, ""));
        thisVal = thisVal.toFixed(dPlace);

        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }
</script>