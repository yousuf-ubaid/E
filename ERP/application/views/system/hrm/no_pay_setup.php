<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_no_pay_setup');
echo head_page($title, false);


$salary_categories_arr = salary_categories(array('A', 'D'));
$getNoPaySystemTb = getNoPaySystemTableRecords();
$nopaydescription = getNoPaySystemTableDrop();


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
    <button type="button" class="btn btn-primary pull-right"  onclick="openSalaryCategoryModel()"><i
    class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
    </button>
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
        $decodeUrl = site_url('Employee/formulaDecode/is_noPaySetup');

        if(!empty($getNoPaySystemTb)){
            foreach($getNoPaySystemTb as $key=>$det){
                
                $description = $det['description'];
                $noPaySystemID = $det['nopaySystemID'];
                $formulaTBID = $det['formulaTBID'];
                $salaryCategoryID = $det['salaryCategoryID'];
                $isNonPayroll = ( $det['isNonPayroll'] != 'Y' )? 'Yes' : 'No';
                $isNonPayrollEdit = ( $det['isNonPayroll'] != 'Y' )? 1 : 2;

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
                                } else {
                                    $catArr = explode('!', $formula_row);
                                    $description = '';
                                    if($catArr[1] == 'NMD'){
                                        $description = 'Days In Month';
                                    }

                                    $formulaText .= $description.' ';
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
                            <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit" style="color:#3c8dbc;"
                            onclick="edit_salaryCategory(\''.$noPaySystemID.'\', \''.$salaryCategoryID.'\', '.$isNonPayrollEdit.')" ></span>
                        </td>
                     </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>



<div class="modal fade" id="salaryCategoryLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'class="form-group" role="form" id="addSalaryCategoryForm"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--></h4>
            </div>
            <div class="modal-body" style="margin-left: 20px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="nopaySystemID" class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <select name="nopaySystemID" class="form-control select2" id="nopaySystemID" onchange="get_salaryCat()" required>
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
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="salaryCategoryID" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('salaryCategoryID', [],'', 'class="form-control select2" id="salaryCategoryID"  required'); ?>
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
            <?php echo form_close(); ?>
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
    'only_salCat_payGroup' => false,
    'noPay' => true
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var urlSave = '<?php echo site_url('Employee/save_noPayFormula') ?>';
    var isPaySheetGroup = 0;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/no_pay_setup', 'Test', 'HRMS');
        });


        $('.number').keypress(function (event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });


        $('#addSalaryCategoryForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                nopaySystemID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                salaryCategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_salary_category_is_required');?>.'}}}/*Salary Category is required*/
            },
        })
            .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            //data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_salary_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $('#salaryCategoryLinkModal').modal('hide');
                        setTimeout(function(){
                            fetchPage('system/hrm/no_pay_setup', 'Test', 'HRMS');
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    // myAlert(data[0], data[1]);
                }
            });
        });


        $('#addSalaryCategoryFormEdit').bootstrapValidator({
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
            //data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/edit_salary_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $('#salaryCategoryLinkModalEdit').modal('hide');
                        setTimeout(function(){
                            fetchPage('system/hrm/no_pay_setup', 'Test', 'HRMS');
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    // myAlert(data[0], data[1]);
                }
            });
        });
    });

    function openSalaryCategoryModel(){
        $('#addSalaryCategoryForm')[0].reset();
        $('#addSalaryCategoryForm').bootstrapValidator('resetForm', true);
        $("#salaryCategoryLinkModal").modal({backdrop: "static"});
    }

    function get_salaryCat(type1=null, selectedID=null){
        var payType = (type1 == null)? $('#nopaySystemID :selected').attr('data-type') : type1;


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
                var objID = (type1 == null)? 'salaryCategoryID' : 'salaryCategoryIDEdit';
                $('#'+objID).html(data);

                if( type1 == null){
                    $('#addSalaryCategoryForm').bootstrapValidator('resetField', 'salaryCategoryID');
                }else{
                    $('#addSalaryCategoryFormEdit').bootstrapValidator('resetField', 'salaryCategoryID');
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function edit_salaryCategory(nopaySystemID,salaryCategoryID, isNonPayroll){
        get_salaryCat(isNonPayroll, salaryCategoryID);
        $('#addSalaryCategoryFormEdit')[0].reset();
        $("#salaryCategoryLinkModalEdit").modal({backdrop: "static"});
        $("#nopaySystemIDEdit, #noPaySystemIDHidden").val(nopaySystemID);
        $("#salaryCategoryIDEdit").val(salaryCategoryID);

    }
</script>

<?php
