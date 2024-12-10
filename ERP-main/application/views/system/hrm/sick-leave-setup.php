<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_sick_leave_setup');
echo head_page($title, false);

$salary_categories_arr = salary_categories(array('A', 'D'));
$sickLeaveData = sickLeave_setupData();

?>
<style type="text/css">
    .saveInputs {
        height: 25px;
        font-size: 11px
    }

    #otCat-add-tb td {
        padding: 2px;
    }

    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        height: 25px;
        padding: 0px 5px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 18px !important;
    }

    #groupData-div {
        height: 185px;
        border: 1px solid #cbd6cc;
    }

    @media (max-width: 767px) {
        #groupData-div {
            border: 0px
        }
    }
</style>
<div class="col-sm-12">
    <!--<button type="button" class="btn btn-primary pull-right"  onclick="openSalaryCategoryModel()"><i
    class="fa fa-plus"></i> <?php /*echo $this->lang->line('common_add');*/?>
    </button>-->
</div>
<hr>
<div class="table-responsive">
    <table id="load_socialInsurance" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 8%"><?php echo $this->lang->line('hrms_attendance_is_payroll');?><!--Is Payroll--></th>
            <th style="width: 40%"><?php echo $this->lang->line('hrms_attendance_formula');?><!--Formula--></th>
            <th style="width: 20%"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--></th>
            <th style="width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $operand_arr = array('+', '*', '/', '-', '(', ')');
        $decodeUrl = site_url('Employee/formulaDecode/is_sickLeaveSetup');

        if(!empty($sickLeaveData)){
            foreach($sickLeaveData as $key=>$det){
                $description = $det['description'];
                $leaveID = $det['leaveTypeID'];
                $formulaTBID = $det['setupID'];
                $formulaTBID = (!empty($formulaTBID))? $formulaTBID : 0;
                $salaryCategoryID = $det['salaryCategoryID']; //$det['salaryCategoryID'];
                $isNonPayroll = ( $det['isNonPayroll'] != 'Y' )? 'Yes' : 'No';
                $isPayrollType = $det['isNonPayroll'];

                $formulaText = '';
                $salaryDescription1 = $det['salaryDescription'];
                $formula = trim($det['formulaString'] ?? '');


                if (!empty($formula) && $formula != null) {

                    $formula_arr = explode('|', $formula); // break the formula

                    foreach ($formula_arr as $formula_row) {

                        if (trim($formula_row) != '') {
                            if (in_array($formula_row, $operand_arr)) { //validate is a operand
                                $formulaText .= $formula_row.' ';
                            }
                            else {
                                $elementType = $formula_row[0];
                                if ($elementType == '_') {
                                    /*** Number ***/
                                    $numArr = explode('_', $formula_row);
                                    $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                                    $formulaText .= $num.' ';

                                }
                                else if ($elementType == '#') {
                                    /*** Salary category ***/
                                    $catArr = explode('#', $formula_row);
                                    $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                                    $new_array = array_map(function ($k) use ($salary_categories_arr) {
                                        return $salary_categories_arr[$k];
                                    }, $keys);

                                    $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                                    $formulaText .= $salaryDescription.' ';
                                }

                            }
                        }

                    }
                }


                echo '<tr>
                        <td>'.($key+1).'</td>
                        <td>'.$description.'</td>
                        <td>'.$isNonPayroll.'</td>
                        <td id="row_'.$key.'">'.$formulaText.'</td>
                        <td>'.$salaryDescription1.'</td>
                        <td align="right">
                            <i class="fa fa-superscript" aria-hidden="true" title="formula" style="color:#3c8dbc;"
                            onclick="formulaModalOpen(\''.$description.'\', \''.$formulaTBID.'\', \''.$decodeUrl.'\', \'row_'.$key.'\')" ></i> &nbsp; | &nbsp;

                            <span title="Setup" rel="tooltip" onclick="leaveSetup(\''.$leaveID.'\',  \''.$description.'\', \''.$isPayrollType.'\', \''.$salaryCategoryID.'\')" >
                            <i class="fa fa-cogs" aria-hidden="true"></i></span>
                        </td>
                     </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>



<div class="modal fade" id="leaveSetup_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_leave_management_sick_leave_setup');?><!--Sick Leave Setup--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="leaveSetup_form" method="get"'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="leaveDes" name="leaveDes" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="isNonPayroll"><?php echo $this->lang->line('hrms_attendance_payroll_type');?><!--Payroll Type--> &nbsp;</label>
                    <div class="col-sm-6">
                        <input type="hidden" name="isNonPayroll" class="form-control isNonPayroll">
                        <select class="form-control isNonPayroll" disabled>
                            <option value="N"><?php echo $this->lang->line('hrms_attendance_payroll');?><!--Payroll--></option>
                            <option value="Y"><?php echo $this->lang->line('hrms_attendance_non_payroll');?><!--Non payroll--></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_salary_category');?><!--Salary category--></label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('salaryCategoryID', [],'', 'class="form-control select2" id="salaryCategoryID"  required'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm"> <?php echo $this->lang->line('common_save_change');?><!--Save Changes--> </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <input type="hidden" id="leaveEditID" name="leaveEditID">
            <?php echo form_close();?>
        </div>
    </div>
</div>


<div class="modal fade" id="salaryCategoryLinkModalEdit" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'class="form-group" role="form" id="addSalaryCategoryFormEdit"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--></h4>
            </div>
            <div class="modal-body" style="margin-left: 20px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="currency" class="col-sm-4"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                            <input type="hidden" name="noPaySystemIDHidden" id="noPaySystemIDHidden">
                            <div class="col-sm-6">
                                <select name="nopaySystemID" class="form-control select2" id="nopaySystemIDEdit"  required disabled>
                                    <option value=""><?php echo $this->lang->line('common_select_description');?><!--Select Description--></option>
                                    <?php
                                    if(!empty($nopaydescription)){
                                        foreach($nopaydescription as $key=>$rowDes){
                                            echo '<option value="'.$rowDes['id'].'" data-type="'.$rowDes['payType'].'" >'.$rowDes['description'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="currency" class="col-sm-4"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('salaryCategoryID', [],'', 'class="form-control select2" id="salaryCategoryIDEdit"  required'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php
$items = [
    'MA_MD' => false,
    'balancePay' => false,
    'SSO' => false,
    'payGroup' => false,
    'only_salCat_payGroup' => false
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var urlSave = '<?php echo site_url('Employee/save_sickLeaveFormula') ?>';
    var isPaySheetGroup = 0;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/sick-leave-setup', 'Test', 'HRMS');
        });


        $('.number').keypress(function (event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });


        $('#leaveSetup_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                salaryCategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_salary_category_is_required');?>.'}}}/*Salary Category is required*/
            },
        })
        .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_sickLeaveCategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $('#leaveSetup_modal').modal('hide');
                        setTimeout(function(){
                            fetchPage('system/hrm/sick-leave-setup', 'Test', 'HRMS');
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        });

    });

    function leaveSetup(id, description, isNonPayroll, salaryCategoryID){
        $('#leaveSetup_form')[0].reset();
        $('#leaveSetup_form').bootstrapValidator('resetForm', true);

        $('#leaveEditID').val(id);
        $('#leaveDes').val(description);
        $('.isNonPayroll').val(isNonPayroll);

        isNonPayroll = (isNonPayroll == 'Y')? 2 : 1;
        get_salaryCat(isNonPayroll, salaryCategoryID);

        $('#leaveSetup_modal').modal('show');
    }


    function get_salaryCat(payType, selectedID=null){

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/get_noPaySalaryCategories'); ?>',
            data: {'payType':payType, 'selectedID':selectedID},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#salaryCategoryID').html(data);
                $('#leaveSetup_form').bootstrapValidator('resetField', 'salaryCategoryID');

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

</script>

<?php
