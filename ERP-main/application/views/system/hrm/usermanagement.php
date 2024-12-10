<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_user_management');
echo head_page($title, false);

$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();

$user_type = ['0'=>'Basic User','1'=>'Module Users'];
$is_QHSE_integrated = is_QHSE_integrated();
$intType = ($is_QHSE_integrated == 'Y')? 'QHSE': '';
$all_employees = all_employees(true, 'QHSE');
$tot_employees = count($all_employees);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<style>
    .employDiv:hover {
        background: #1b6d85;
        color: #f1f1f1;
        cursor: pointer;
    }

    .employDivSelected {
        background: #23527c;
        color: #f1f1f1;
    }

    #errorDiv, #bankSave_errorDiv {
        text-align: center;
        color: red;
        font-weight: bold;
    }

    /*.datepicker{
        z-index: 100000 !important;
    }*/

    .error-input {
        border-color: red;
    }

    div#emp::-webkit-scrollbar {
        width: 5px;
        margin-left: 30px;
    }

    div#emp::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        margin-left: 30px;
    }

    div#emp::-webkit-scrollbar-thumb {
        margin-left: 30px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        width: 3px;
        position: absolute;
        top: 0px;
        opacity: 0.4;
    / / display: none;
        border-radius: 7px;
        z-index: 99;
        right: 1px;
        height: 188.679px;
    / / background: rgb(0, 0, 0);
    }

    .salaryPercntageMSg {
        color: red;
        font-weight: bolder;
        text-align: center;
    }

    #emp {
        padding: 2%;
        height: 450px;
        overflow-y: scroll;
        margin-bottom: 20px;
        margin-top: 20px;
        border: 1px solid #e6e6e6;
        background: rgba(171, 168, 164, 0.32);
    . TriSea-technologies-Switch > label:: after
    }

    @media (max-width: 767px) {
        #currency-div {
        / / float: left !important;
        }
    }

    .TriSea-technologies-Switch > input[type="checkbox"] {
        display: none;
    }

    .TriSea-technologies-Switch > label {
        cursor: pointer;
        height: 0px;
        position: relative;
        width: 40px;
    }

    .TriSea-technologies-Switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 12px;
        margin-top: -8px;
        position: absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 32px;
    }

    .TriSea-technologies-Switch > label::after {
        background: #00a65a;
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 20px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 20px;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }

    .hide-row{
        display: none;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class="tab-content">
            <div class="tab-pane active disabled" id="accTab">
                <form id="employeeUpdate" name="employeeUpdate">
                    <div class="row">
                        <div class="col-sm-7 col-xs-6">
                            <div class="dataTables_info" id="attendanceMasterTB_info" role="status" aria-live="polite">
                                <b>Showing <span id="display-count"><?=$tot_employees?></span> of <?=$tot_employees?> entries</b>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-6">
                            <div class="dataTables_filter">
                            <label>
                                <b><?=$this->lang->line('common_filter');?> :</b>
                                <input type="search" class="form-control input-sm" id="filter" value="" placeholder="Type here...">
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="fixHeader_Div" style="height: 480px">
                        <table class="table table-bordered" id="user-det-tbl">
                            <thead>
                            <tr>
                                <th> <?php echo $this->lang->line('config_emp_id');?><!--EmpID--></th>
                                <th> <?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                                <th> <?php echo $this->lang->line('config_username');?><!--Username--></th>
                                <th> <?php echo $this->lang->line('config_password');?><!--Password--></th>
                                <th> <?php echo $this->lang->line('config_user_type');?><!--User Type--></th>
                                <th> <?php echo $this->lang->line('config_password_changed');?><!--Password Changed--></th>
                                <th style="z-index: 999999"> <?php echo $this->lang->line('config_password_login_attempt');?><!--Login Attempt--></th>
                                <th> <?php echo $this->lang->line('config_password_login_active');?><!--Login Active--></th>
                                <th> <?php echo $this->lang->line('config_super_admin');?><!--Super Admin--></th>
                               
                                <?php
                                if($is_QHSE_integrated == 'Y'){
                                    echo '<th>'.$this->lang->line('common_QHSE_login').'</th>';
                                }
                                ?>
                            </tr>
                            </thead>
                              
                            <tbody id="searchable">
                            <?php
                            $i = 0;
                                

                            if (!empty($all_employees)) {
                                foreach ($all_employees as $value) {
                                    $i++; $emp_id = $value['EIdNo'];
                                    ?>
                                    <tr id="acctTr_'<?=$i;?>'">
                                        <td><?=$value['ECode']?></td>
                                        <td><?=$value['Ename2']?></td>
                                        <td>
                                            <input type="text" id="UserName<?=$emp_id?>" value="<?=$value['UserName']?>"
                                                   name="UserName" onchange="changeUsername(<?=$emp_id?>)">
                                        </td>
                                        <td>
                                            <input type="hidden" class="empID" name="empID[]" value="<?=$emp_id?>">
                                            <input type="hidden" id="btn-disable-pwd-<?=$emp_id?>" class="btn-disable-pwd">
                                            <input type="password" class="password" onkeyup="validate_pwsStrength(<?=$emp_id?>)"
                                                   onchange="changePassword(this,<?=$emp_id?>)"
                                                   id="password_<?=$emp_id?>" value="***********" name="password[]">
                                            <span id="message_<?=$emp_id?>"></span>
                                        </td>
                                        <td>
                                            <?=form_dropdown('userType', $user_type, $value['userType'], 'class="form-control select2" id="userType_'.$emp_id.' " 
                                                onchange="update_userType(this,'.$emp_id.')" required');?>
                                        </td>
                                        <td>
                                            <?php $checkedPassword = ($value['isChangePassword'] == 1)? 'checked': '';?>
                                            <input class="active" type="hidden" name="isChangePassword[]"
                                                   value=<?=$value['isChangePassword']?>/>
                                            <input onchange="setIsChangePassword(this,<?=$emp_id?>)"
                                                   type="checkbox" class="isChangePassword" value="1"
                                                   name="ChangePassword[]" <?= $checkedPassword ?> >
                                        </td>
                                        <td>
                                            <?php $check = ($value['NoOfLoginAttempt'] == 4)? 'checked': ''; ?>
                                            <div class="TriSea-technologies-Switch" style="margin-left: 30%; margin-top: 7px">
                                                <input id="employeeID_<?=$emp_id?>" name="employeeID" <?= $check ?> type="checkbox"
                                                       onchange="updateLoginAttempt(<?=$emp_id?>)"/>
                                                <label for="employeeID_<?=$emp_id?>" class="label-danger"> </label>
                                            </div>
                                        </td>
                                        <?php $checked = ($value['isActive'] == 1)? 'checked': ''; ?>
                                        <td>
                                            <input class="active" type="hidden" name="isActive[]" value=<?=$value['isActive']?>/>
                                            <input onchange="loginActive(this,<?=$emp_id?>)" type="checkbox"
                                                   class="isActive" value="1" name="Active[]" <?=$checked?>>
                                        </td>
                                        
                                        <td>
                                            <?php $superAdmin = ($value['isSystemAdmin'] == 1)? 'checked': '';?>
                                            <input class="active" type="hidden" name="isSystemAdmin[]" value=<?=$value['isSystemAdmin']?>/>
                                            <input onchange="setIsSuperAdmin(this,<?=$emp_id?>)"
                                            type="checkbox" class="isSystemAdmin" value="1"
                                            name="ChangeAdmin[]" <?= $superAdmin ?> >
                                        </td>
                                        
                                        <?php
                                        if($is_QHSE_integrated == 'Y'){
                                            $integratedUserID = $value['integratedUserID'];
                                            $str = '<span class="label label-success">Created</span>';

                                            if(empty($integratedUserID)){
                                                $empName = str_replace("'", "", $value['Ename2']);
                                                $empName .= ' ( '.$value['ECode'].' )';
                                                $str = '<button type="button" class="btn btn-primary btn-xs" 
                                                onclick="create_user(\''.$empName.'\',\'QHSE\', '.$emp_id.')">Create</button>';
                                            }
                                            echo '<td align="center" id="qhse-log-str-'.$emp_id.'">'.$str.'</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript" src="<?= base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script>

    $('#user-det-tbl').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

    (function ($) {

        $('#filter').keyup(function () {
            let searchText = new RegExp($(this).val(), 'i');
            $('#searchable tr').addClass('hide-row');

            $('#searchable tr').filter(function () {
                return searchText.test($(this).text());
            }).removeClass('hide-row');

            $('#display-count').text( $('#searchable > tr:not(.hide-row').length );
        })

    }(jQuery));

    function loginActive(thisID, empID) {
        if ($(thisID).is(':checked')) {
            $(thisID).closest('tr').find('input[type="hidden"]').val(1);
        }
        else {
            $(thisID).closest('tr').find('input[type="hidden"]').val(0);
        }
        var checked = $(thisID).closest('tr').find('input[type="hidden"]').val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: checked, empID: empID},
            url: "<?= site_url('Employee/save_company_active'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] != 's') {
                    $(thisID).prop('checked', false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function updateLoginAttempt(id) {
        let chk_status = ($("#employeeID_" + id).is(':checked'))? 4: 0;
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: chk_status, empID: id},
            url: "<?= site_url('Employee/unloackUser'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    myAlert('s', data[1]);
                } else {
                    myAlert('e', data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function changeUsername(userID) {
        let username = $('#UserName' + userID).val();
        swal({
                title: "<?= $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?= $this->lang->line('config_you_want_to_update_this_record');?>",/*You want to Update this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "<?= $this->lang->line('common_update');?>",/*Update*/
                cancelButtonText: "<?= $this->lang->line('common_cancel');?>"/*cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'EIdNo': userID, 'UserName': username},
                    url: "<?= site_url('Employee/update_userName'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            if(parseInt(data['is_logout']) == 1){
                                /**** if logged in user`s user name changed then, user have to logout ****/
                                check_session_status();
                            }
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            }
        );
    }

    function setIsChangePassword(obj, empID) {
        let checked = ($(obj).prop('checked'))? 1: 0;

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: checked, empID: empID},
            url: "<?php echo site_url('Employee/save_user_change_password'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function setIsSuperAdmin(obj, empID) {
        let checked = ($(obj).prop('checked'))? 1: 0;

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: checked, empID: empID},
            url: "<?php echo site_url('Employee/save_user_change_super_admin'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function changePassword(obj, empID) {
        let isValidPass = $(obj).closest('tr').find('.btn-disable-pwd').val();
        if(isValidPass){
            let pas =$(obj).val();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {empID: empID, 'password': pas},
                url: "<?= site_url('Employee/update_userPassword'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function validate_pwsStrength(empID) {
        var passwordComplexityExist = '<?= $passwordComplexityExist; ?>';

        if (passwordComplexityExist == 1) {
            var word = $('#password_' + empID).val();
            var Score = 0;
            var conditions = 0;
            var iscapital = 0;
            var isspecial = 0;
            var lengt = word.length;
            var Capital = word.match(/[A-Z]/);
            var format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (format.test(word) == true) {
                isspecial = 1
            } else {
                isspecial = 0
            }
            if (jQuery.isEmptyObject(Capital)) {
                iscapital = 0
            } else {
                iscapital = 1
            }
            $('#message_' + empID).html('<label class="label label-danger">Weak</label>');
            $('#password-save-btn').attr('disabled', true);
            $('#btn-disable-pwd-' + empID).val(1);

            var minimumLength = '<?= $passwordComplexity['minimumLength'] ?? '' ?>';
            if (minimumLength <= lengt) {
                conditions = conditions + 1;
                Score = Score + 1;
                $('#message').html(' ');
                var isCapitalLettersMandatory = '<?= $passwordComplexity['isCapitalLettersMandatory'] ?? '' ?>';
                var isSpecialCharactersMandatory ='<?= $passwordComplexity['isSpecialCharactersMandatory'] ?? '' ?>';
                if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 2;
                    if (iscapital == 1) {

                        Score = Score + 1;
                    }
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 0) {
                    conditions = conditions + 1;
                    if (iscapital == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 1;
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 0) {

                }


                if (conditions == Score) {
                    $('#btn-disable-pwd-' + empID).val(0);
                    $('#message_' + empID).html('<label class="label label-success"><?= $this->lang->line('config_strong');?><!--Strong--></label>');
                } else if ((conditions % Score) > 0) {
                    $('#btn-disable-pwd-' + empID).val(1);
                    $('#password-save-btn').attr('disabled', true);
                    $('#message_' + empID).html('<label class="label label-warning"><?= $this->lang->line('config_medium');?><!--Medium--></label>');
                } else {
                    $('#btn-disable-pwd-' + empID).val(1);
                    $('#password-save-btn').attr('disabled', true);
                    $('#message_' + empID).html('<label class="label label-danger"><?= $this->lang->line('config_weak');?><!--Weak--></label>');
                }
                $('#password-save-btn').attr('disabled', false);

                $('.btn-disable-pwd').each(function () {
                    if ($(this).val() == 1) {
                        // alert("hi")
                        $('#password-save-btn').attr('disabled', true);
                        return false
                    } else if ($(this).val() == 0) {
                        //alert("oi")
                        $('#password-save-btn').attr('disabled', false);
                    }
                });


            }
        } else {
            var word = $('#password_' + empID).val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#btn-disable-pwd-' + empID).val(1);
                $('#password-save-btn').attr('disabled', true);
                $('#message_' + empID).html('<label class="label label-danger"><?= $this->lang->line('config_weak');?><!--Weak--></label>');
            } else {
                $('#password-save-btn').attr('disabled', false);
                $('#btn-disable-pwd-' + empID).val(0);
                $('#message_' + empID).html('<label class="label label-success"><?= $this->lang->line('config_strong');?><!--Strong--></label>');
            }

        }

    }

    function update_userType(type,EIdNo) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'type': type.value,'EIdNo':EIdNo},
            url: "<?= site_url('Employee/update_userType'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] != 's') {
                    $(type).val('0');
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });
    }

    function create_user(empName, intType, empID){
        swal({
                title: "<?= $this->lang->line('common_are_you_sure');?>",
                text: "<?= $this->lang->line('common_you_want_to_proceed_with');?> "+empName,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "<?= $this->lang->line('common_proceed');?>",
                cancelButtonText: "<?= $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empID': empID, 'intType': intType},
                    url: "<?= site_url('Employee/create_user_for_integration'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            $('#qhse-log-str-'+empID).html('<span class="label label-success">Created</span>');
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            }
        );
    }
</script>
