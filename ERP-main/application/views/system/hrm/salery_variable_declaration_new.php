<!--Translation added by Naseek-->

<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_salary_declaration');
echo head_page($title, false);

$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();
if( !empty($this->input->post('page_id')) ){
    $employee_drop = getEmployeesDeclaration($this->input->post('page_id'),1);
    $salaryCategories_drop = salaryCategories_drop($this->input->post('page_id'),1);
}else{
    $employee_drop = null;
    $salaryCategories_drop = null;
}

?>
<form class="form-horizontal" id="declaration_form">
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></label>
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
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></label>
        </div>
        <div class="form-group col-sm-4">
            <select name="isPayrollCategory" id="isPayrollCategory" class="form-control">
                <option value="1"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                <option value="2"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_payroll_initial_declaration');?><!--Initial Declaration--></label>
        </div>
        <div class="form-group col-sm-4">
            <select name="isInitialDeclaration" id="isInitialDeclaration" class="form-control">
                <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
                <option value="1"><?php echo $this->lang->line('common_yes');?><!--Yes--></option>
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

<div class="modal fade" id="declarationDetailModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 99%" role="document">
        <div class="modal-content">
            <form class="" id="declaration_save_detail_form" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_variable_salary_declaration_detail');?><!--Salary Declaration Detail--></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" style="color: red; margin-left: 1%">
                            <?php echo $this->lang->line('common_note');?><!--Note--> : <?php echo $this->lang->line('hrms_payroll_deduction_amount_should_be_entered_with_a');?><!--Deduction amount should be entered with a--> ( - )
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <table class="<?php echo table_class(); ?>" style="min-width:1000px;">
                                    <thead>
                                    <tr>
                                        <th width=""><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                                        <th width="130px"><?php echo $this->lang->line('hrms_payroll_effective_date');?><!--Effective Date--></th>
                                        <th width=""><?php echo $this->lang->line('common_category');?><!--Category--></th>
                                        <th width="100px"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_payroll_current_amount');?><!--Current Amount--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_payroll_new_amount');?><!--New Amount--></th>
                                        <th width="10%"><?php echo $this->lang->line('hrms_payroll_adjustment');?><!--Adjustment--></th>
                                        <th width="130px"><?php echo $this->lang->line('hrms_payroll_pay_date');?><!--Pay Date--></th>
                                        <th width="150px"><?php echo $this->lang->line('hrms_payroll_narration');?><!--Narration--></th>
                                        <th width="5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td style="max-width: 240px">
                                            <div class="form-group">
                                                <select name="employee" class="select2 form-control" id="employee" required onchange="getEffectiveDate()">
                                                <?php
                                                $option = "<option value> Select Employee </option>";
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
                                            <input type="hidden" name="declarationMasterID" id="declarationMasterID">
                                        </td>
                                        <td style="width: 130px;">
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
                                                <select name="cat[]" class="form-control select2" id="salarySubCatID" required onchange="getMasterCatType(this)">
                                                    <?php
                                                    $option = "<option value=''> Select Category </option>";
                                                    if(!empty($salaryCategories_drop)){
                                                        foreach($salaryCategories_drop as $salCast){
                                                            $option .= "<option value='{$salCast['salaryCategoryID']}'
                                                                        data-value='{$salCast['salaryCategoryType']}'>{$salCast['salaryDescription']}
                                                                    </option>";

                                                        }
                                                    }
                                                    echo $option;
                                                    ?>
                                                </select>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <?php echo form_dropdown('salaryType1', array('' => '', 'A' => 'Addition', 'D' => 'Deduction'), '',
                                                    'class="form-control salaryType" id="salaryType" disabled'); ?>
                                                <input type="hidden" name="salaryType" class="salaryType" value="" />
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text"  name="currentAmount"  class="form-control number" id="currentAmount" readonly/>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" name="newAmount" class="form-control numeric" id="newAmount"
                                                   onkeyup="getNewAmount(this)" autocomplete="off" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control numeric" name="amount[]" id="amount" readonly />
                                            </div>
                                        </td>
                                        <td style="width: 130px;">
                                            <div class="form-group">
                                                <div class="input-group datepic">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="payDate"  id="payDate" class="form-control"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" name="narration" class="form-control" id="narration" />
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="empJoinDate" id="empJoinDate"/>
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
                                        <th style="min-width: 30%"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_effective_date');?><!--Effective Date--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_pay_date');?><!--Pay Date--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_salary_type');?><!--Salary Type--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_salary_sub_type');?><!--Salary Sub Type--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_narration');?><!--Narration--></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                                        <th>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="salary-declaration-multiple-tbody">
                                    </tbody>
                                    <tfoot id="salary-declaration-multiple-foot">
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
            fetchPage('system/hrm/salery_declaration', p_id, 'Salary Declaration');
        });

        if (p_id) {
            LoadSalaryDeclarationMaster(p_id);
            //getEmployeesCurrency_edit(p_id); 
        }

        $('.select2').select2();

        $('#amount_1').bind("cut copy paste",function(e) {
            e.preventDefault();
        });

        salary_declaration_table();

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
        });


        $("input.numeric").numeric();


        $('#declaration_save_detail_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    employee: {validators: {notEmpty: {message: 'Employee is required.'}}},
                    effectiveDate: {validators: {notEmpty: {message: 'Effective Date is required.'}}},
                    payDate: {validators: {notEmpty: {message: 'Pay Date is required.'}}},
                    newAmount: {validators: {notEmpty: {message: 'New amount is required.'}}},
                    salarySubCatID: {validators: {notEmpty: {message: 'Salary Sub Type is required.'}}}
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
                    url: "<?php echo site_url('Employee/save_salary_declaration_detail'); ?>",
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
                            var payDateObj = $('#payDate');

                            effectiveDateObj.attr('data-value', effectiveDateObj.val());
                            payDateObj.attr('data-value', payDateObj.val());

                            $("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
                            $("#amount, #currentAmount, #newAmount, #narration, .salaryType").val('');

                            $("#declaration_save_detail_form").data('bootstrapValidator').resetForm();

                            effectiveDateObj.val(effectiveDateObj.attr('data-value'));
                            payDateObj.val(payDateObj.attr('data-value'));
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
            url: "<?php echo site_url('Employee/Load_Salary_Declaration_Master') ?>",
            data: {id: id,'isVariable':'1'},
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
                $("#declarationMasterID").val(id);
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
        bootbox.confirm("Are you sure want to confirm this salary declaration?", function (confirmed) {
            if (confirmed) {
                confirm_salaryDeclaration();
            }
        });
    }

    function confirm_salaryDeclaration(salaryControlCheckPass=0){
        let masterID = $('#declarationMasterID').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/ConfirmSalaryDeclaration'); ?>",
            data: {'masterID': masterID, 'salaryControlCheckPass': salaryControlCheckPass},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'm'){
                    bootbox.confirm({
                        message: data[1],
                        size: 'large',
                        callback: function(confirmed) {
                            if (confirmed) {
                                confirm_salaryDeclaration(1);
                            }
                        }
                    });
                    return false;
                }

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetchPage('system/hrm/salery_declaration', '', 'HRMS');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
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
                    data: {'detailID': detailID, 'masterID': masterID},
                    url: "<?php echo site_url('Employee/delete_salary_declaration'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            LoadSalaryDeclarationMaster(masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            });
    }

    function delete_salary_drilldown(detailID, masterID, employee) {
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
                    data: {'detailID': detailID, 'masterID': masterID},
                    url: "<?php echo site_url('Employee/delete_salary_declaration'); ?>",
                    beforeSend: function () {
                        startLoad();
                    }, success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            getDrilldownTableData(masterID, employee);
                            LoadSalaryDeclarationMaster(masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            });
    }


    function getDrilldownTableData(masterID, employeeID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/load_declaration_drilldown_table'); ?>",
            data: {masterID: masterID, employeeID: employeeID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad()
            },
            success: function (options) {
                stopLoad();
                $('#salary-declaration-multiple-tbody').empty();
                $('#salary-declaration-multiple-foot').empty();
                if (jQuery.isEmptyObject(options)) {
                    $('#salary-declaration-multiple-tbody').append('<tr style="text-align: center"><td colspan="8" align="center"><?php echo $this->lang->line('common_no_records_available');?></td></tr>');<!--No Records Available-->
                }
                else {
                    var a = 0;
                    var trDPlace = 2;
                    $.each(options, function (key, value) {
                        trDPlace = value['trDPlace'];
                        if (value['salaryCategoryType'] == 'A') {
                            category = 'Addition';
                        } else if (value['salaryCategoryType'] == 'D') {
                            category = 'Deduction';
                        }
                        var appStr = '<tr><td>' + value['ECode'] + ' | ' + value['Ename2'] + '</td>';
                        appStr += '<td>' + value['effectiveDate2'] + '</td><td>' + value['payDate2'] + '</td>';
                        appStr += '<td>' + category + '</td><td>' + value['salaryDescription'] + '</td>';
                        appStr += '<td>' + value['narration'] + '</td>';
                        appStr += '<td style="text-align: right">' + commaSeparateNumber(value['transactionAmount'], trDPlace) + '</td>';
                        appStr += '<td style="text-align: right">';
                        appStr += '<a onclick="delete_salary_drilldown(' + value['declarationDetailID'] + ',' + value['declarationMasterID'] + ',' + value['employeeNo'] + ')">';
                        appStr += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        appStr += '</td></tr>';

                        $('#salary-declaration-multiple-tbody').append(appStr);
                        a += parseFloat(value['transactionAmount']);
                    });

                    var noRecordStr = '<tr><td colspan="6" style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_totals');?></td>';<!--Total-->
                    noRecordStr += '<td class="text-right total">' + commaSeparateNumber(a, trDPlace) + '</td></tr>';
                    $('#salary-declaration-multiple-foot').html(noRecordStr);
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
        $("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
        $("#amount, #newAmount, #currentAmount, #narration, #empJoinDate").val('');

        $("#declaration_save_detail_form").data('bootstrapValidator').resetForm();
        $('#addBtn').prop('disabled', false);
        $('#salary-declaration-multiple-tbody').html('<tr style="text-align: center"><td colspan="8" align="center"><?php echo $this->lang->line('common_no_records_available');?></td></tr>');<!--No Records Available-->

        var noRecordStr = '<tr><td colspan="6" style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_total');?></td>';<!--Total-->
        noRecordStr += '<td class="text-right total">' + commaSeparateNumber(0) + '</td></tr>';
        $('#salary-declaration-multiple-foot').html(noRecordStr);

    }

    function getMasterCatType(obj){
        $('.salaryType').val($('#salarySubCatID :selected').attr('data-value'));
        $('#currentAmount').val('');
        $('#amount').val('');
        $('#newAmount').val('');

        if( $(obj).val() != ''){
            var empID = $('#employee').val();
            var effectiveDate = $('#effectiveDate').val();
            var isPayrollCategory_hidden = $('#isPayrollCategory_hidden').val();
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
                    data: {'empID':empID, 'catID':catID, 'effectiveDate':effectiveDate, 'payrollType' : isPayrollCategory_hidden},
                    url: "<?php echo site_url('Employee/get_empSalaryInCategory'); ?>",
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
