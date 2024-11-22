<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_user_management');
echo head_page('$title', false);

/*echo head_page('User Management', false)*/
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
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
        height: 16px;
        margin-top: -8px;
        position: absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }

    .TriSea-technologies-Switch > label::after {
        background: #00a65a;
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <!--    <div class="col-md-3" id="" style="/*border: 1px solid #f4f4f5; margin-left: 20px*/">

            <div class="input-group" style="">
                <input type="text" class="form-control employeeSearch" onkeyup="search(this.value)">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
            </div>

            <div id="emp" style=""> </div>
        </div>-->


    <div class="col-md-12">


        <button style="margin: 5px;" class="btn btn-primary btn-xs pull-right" id="passwordsavebtn"
                onclick="updateEmployee()"><?php echo $this->lang->line('common_update');?><!--Update-->
        </button>
        <div class="tab-content">
            <div class="tab-pane active disabled" id="accTab"> <!-- /.tab-pane -->

                <div class="input-group"><span class="input-group-addon"><?php echo $this->lang->line('common_filter');?><!--Filter--></span>

                    <input id="filter" type="text" class="form-control" placeholder="Type here...">
                </div>
                <hr>
                <form id="employeeUpdate" name="employeeUpdate">
                    <table class="table table-bordered" id="bankAccDetTb">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('config_emp_id');?><!--EmpID--></th>
                            <th> <?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                            <th> <?php echo $this->lang->line('config_username');?><!--Username--></th>
                            <th> <?php echo $this->lang->line('config_password');?><!--Password--></th>
                            <th> <?php echo $this->lang->line('config_user_type');?><!--User Type--></th>
                            <th> <?php echo $this->lang->line('config_password_changed');?><!--Password Changed--></th>
                            <th> <?php echo $this->lang->line('config_password_login_attempt');?><!--Login Attempt--></th>
                            <th> <?php echo $this->lang->line('config_password_login_active');?><!--Login Active--></th>
                          
                        </tr>
                        </thead>
                        <tbody id="searchable">
                        <?php $all_employees = all_employees(true);
                        $i = 0;

                        if (!empty($all_employees)) {
                            foreach ($all_employees as $value) {
                                $i++;
                                ?>
                                <tr>
                                <tr id="acctTr_'<?php echo $i; ?>'">
                                    <td><?php echo $value['ECode'] ?></td>
                                    <td> <?php echo $value['Ename2'] ?></td>
                                    <td><input type="text" id="UserName<?php echo $value['EIdNo'] ?>"
                                               value="<?php echo $value['UserName'] ?>"
                                               name="UserName" onchange="changeUsername(<?php echo $value['EIdNo'] ?>)">
                                    </td>
                                    </td>
                                    <td><input type="hidden" class="empID" name="empID[]"
                                               value="<?php echo $value['EIdNo'] ?>">
                                        <input type="hidden" id="btndisablepwd_<?php echo $value['EIdNo'] ?>"
                                               class="btndisablepwd">
                                        <input type="password" class="password"
                                               onkeyup="validatepwsStrength(<?php echo $value['EIdNo'] ?>)"
                                               id="password_<?php echo $value['EIdNo'] ?>" value="***********"
                                               name="password[]">
                                        <span id="message_<?php echo $value['EIdNo'] ?>"></span>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('usertype', array('0'=>'Basic User','1'=>'Module Users'), $value['userType'], 'class="form-control select2" id="usertype_'.$value['EIdNo'].' " onchange="userconfiguration_usertype(this,'.$value['EIdNo'].')" required');?>
                                    </td>
                                    <td>
                                        <?php
                                        $checkedPassword = '';
                                        if ($value['isChangePassword'] == 1) {

                                            $checkedPassword = "checked";
                                        }
                                        ?>

                                        <input class="active" type="hidden" name="isChangePassword[]"
                                               value=<?php echo $value['isChangePassword'] ?>/>
                                        <input onchange="setisChangePassword(this,<?php echo $value['EIdNo'] ?>)"
                                               type="checkbox" class="isChangePassword" value="1"
                                               name="ChangePassword[]" <?php echo $checkedPassword ?> >

                                    </td>
                                    <td>

                                        <?php
                                        $check = '';
                                        if ($value['NoOfLoginAttempt'] == 4) {

                                            $check = "checked";
                                        }
                                        ?>
                                        <!--<input onchange="updateLoginAttempt(this)" type="checkbox" class="isActive" value="1"
                                               name="LoginAttempt[]" <?php /*echo $check */ ?> >-->

                                        <div class="TriSea-technologies-Switch" style="margin-left: 30%;">
                                            <input onchange="updateLoginAttempt(<?php echo $value['EIdNo'] ?>)"
                                                   id="employeeID_<?php echo $value['EIdNo'] ?>"
                                                   name="employeeID" <?php echo $check ?> type="checkbox"/>
                                            <label for="employeeID_<?php echo $value['EIdNo'] ?>"
                                                   class="label-danger"></label>
                                        </div>


                                    </td>
                                    <?php
                                    $checked = '';
                                    if ($value['isActive'] == 1) {

                                        $checked = "checked";
                                    }
                                    ?>
                                    <td><input class="active" type="hidden" name="isActive[]"
                                               value=<?php echo $value['isActive'] ?>/>
                                        <input onchange="setval(this,<?php echo $value['EIdNo'] ?>)" type="checkbox"
                                               class="isActive" value="1"
                                               name="Active[]" <?php echo $checked ?> ></td>
                                </tr>

                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </form>
            </div>


        </div>

    </div>
</div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script>
    //$(".mySwitch").bootstrapSwitch();
    (function ($) {

        $('#filter').keyup(function () {

            var rex = new RegExp($(this).val(), 'i');
            $('#searchable tr').hide();
            $('#searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();

        })

    }(jQuery));

    function updateEmployee() {
        var that = $("#employeeUpdate"),
            url = that.attr('action'),
            type = that.attr('method'),
            data = {};
        var serializedData = that.serialize();
        that.find('[name]').each(function (index, value) {
            var that = $(this),
                name = that.attr('name'),
                value = that.val();

            data[name] = value;
        });

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_update_this');?>",/*You want to update this !*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_update');?>",/*Update*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: serializedData,
                    url: "<?php echo site_url('Employee/updateEmployeeDetails'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("", "Error in process", "error");
                    }
                });
            });


    }


    function setval(thisID, empID) {

        var a = $(thisID).closest('tr').find('input[type="hidden"]').val();
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
            url: "<?php echo site_url('Employee/save_company_active'); ?>",
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
        if ($("#employeeID_" + id).is(':checked')) {
            var chkdVal = 4
        } else {
            var chkdVal = 0
        }
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: chkdVal, empID: id},
            url: "<?php echo site_url('Employee/unloackUser'); ?>",
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
        /*
         $('#isPax_'+id+extraID).bootstrapSwitch('toggleState', true, true);
         */

    }

    function changeUsername(id) {
        var username = $('#UserName' + id).val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('config_you_want_to_update_this_record');?>",/*You want to Update this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "<?php echo $this->lang->line('common_update');?>",/*Update*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'EIdNo': id, 'UserName': username},
                    url: "<?php echo site_url('Employee/update_userName'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function setisChangePassword(thisID, empID) {
        var a = $(thisID).closest('tr').find('.btndisablepwd').val();
        if ($(thisID).is(':checked')) {
            $(thisID).closest('tr').find('.btndisablepwd').val(1);
        }
        else {
            $(thisID).closest('tr').find('.btndisablepwd').val(0);
        }
        var checked = $(thisID).closest('tr').find('.btndisablepwd').val();

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

    function validatepwsStrength(empID) {
        var passwordComplexityExist = '<?php echo $passwordComplexityExist; ?>';

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
            $('#passwordsavebtn').attr('disabled', true);
            $('#btndisablepwd_' + empID).val(1);

            var minimumLength = '<?php echo $passwordComplexity['minimumLength'] ?>';
            if (minimumLength <= lengt) {
                conditions = conditions + 1;
                Score = Score + 1;
                $('#message').html(' ');
                var isCapitalLettersMandatory = '<?php echo $passwordComplexity['isCapitalLettersMandatory'] ?>';
                var isSpecialCharactersMandatory ='<?php echo $passwordComplexity['isSpecialCharactersMandatory'] ?>';
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
                    $('#btndisablepwd_' + empID).val(0);
                    $('#message_' + empID).html('<label class="label label-success"><?php echo $this->lang->line('config_strong');?><!--Strong--></label>');
                } else if ((conditions % Score) > 0) {
                    $('#btndisablepwd_' + empID).val(1);
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#message_' + empID).html('<label class="label label-warning"><?php echo $this->lang->line('config_medium');?><!--Medium--></label>');
                } else {
                    $('#btndisablepwd_' + empID).val(1);
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#message_' + empID).html('<label class="label label-danger"><?php echo $this->lang->line('config_weak');?><!--Weak--></label>');
                }
                $('#passwordsavebtn').attr('disabled', false);

                $('.btndisablepwd').each(function () {
                    if ($(this).val() == 1) {
                        // alert("hi")
                        $('#passwordsavebtn').attr('disabled', true);
                        return false
                    } else if ($(this).val() == 0) {
                        //alert("oi")
                        $('#passwordsavebtn').attr('disabled', false);
                    }
                });


            }
        } else {
            var word = $('#password_' + empID).val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#btndisablepwd_' + empID).val(1);
                $('#passwordsavebtn').attr('disabled', true);
                $('#message_' + empID).html('<label class="label label-danger"><?php echo $this->lang->line('config_weak');?><!--Weak--></label>');
            } else {
                $('#passwordsavebtn').attr('disabled', false);
                $('#btndisablepwd_' + empID).val(0);
                $('#message_' + empID).html('<label class="label label-success"><?php echo $this->lang->line('config_strong');?><!--Strong--></label>');
            }

        }

    }
    function userconfiguration_usertype(type,EIdNo) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'type': type.value,'EIdNo':EIdNo},
            url: "<?php echo site_url('Employee/update_userType'); ?>",
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
                // swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


</script>
