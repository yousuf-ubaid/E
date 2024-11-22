<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_approval_setup');
echo head_page($title, FALSE);

$leaveApprovalWithGroup = getPolicyValues('LAG', 'All');
$empDrop = load_employee_drop();
$setupData = getLeaveApprovalSetup('Y');
$approvalLevel = $setupData['approvalLevel'];
$approvalSetup = $setupData['approvalSetup'];
$approvalEmp = $setupData['approvalEmp'];
$appTypeArr = $setupData['appSystemValues'];
$leaveGroup = monthlyleavegroup_drop();


?>

<style>
    .empDiv{ display: none; }

    legend{
        font-size: 16px !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">

<div class="row">
    <div class="col-md-12">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <fieldset class="scheduler-border" style="margin-top: 10px">
            <legend class="scheduler-border"><?php echo $this->lang->line('hrms_leave_management_leave_approval_levels')?></legend>
                <div style="margin-top: 10px">
                    <div class="row">
                    <div class="col-md-12">
                    <label for="level-txt" class="col-md-2"> <?php echo $this->lang->line('hrms_leave_management_levels')?></label>
                        <input type="number" name="level-txt" id="level-txt" class="col-md-4 number" value="<?php echo $approvalLevel; ?>">
                        <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-bottom: 10px"
                                onclick="save_approval_levels()"><?php echo $this->lang->line('common_save')?> <!--Save-->
                        </button>
                    </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <?php if($leaveApprovalWithGroup == 1) { ?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php echo $this->lang->line('hrms_leave_management_leave_group')?></legend>
                    <div style="margin-top: 10px">
                        <div class="row">
                        <div class="col-md-12">
                            <?php echo form_dropdown('leaveGroupID', $leaveGroup, '', 'class="form-control select2" id="leaveGroupID" onchange="change_leave_group()" '); ?>
                        </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        <?php } ?>
        <?php if($leaveApprovalWithGroup == 1) { ?>
                <div id="approval_setup_section"></div>
            <?php } else { ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('hrms_leave_management_leave_approval_setup')?><!--Leave Approval Setup--></legend>
                        <div class="">
                            <div class="clearfix visible-sm visible-xs">&nbsp;</div>
                            <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-bottom: 10px" onclick="saveSetup()"><?php echo $this->lang->line('common_save')?></button>

                            <form id="setup-form">
                                <table class="<?php echo table_class() ?>">
                                    <thead>
                                    <tr>
                                        <td><?php echo $this->lang->line('common_level')?><!--Level--></td>
                                        <td><?php echo $this->lang->line('hrms_leave_management_Approval_Type')?><!--Approval Type--></td>
                                        <td><?php echo $this->lang->line('hrms_leave_management_employee')?><!--Employee--></td>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    $x = 0;
                                    while($x < $approvalLevel){
                                        $level = $x+1;
                                        $keys = array_keys( array_column($approvalSetup, 'approvalLevel'), $level );
                                        $arr = array_map(function ($k) use ($approvalSetup) {
                                            return $approvalSetup[$k];
                                        }, $keys);

                                        $approvalType = (!empty($arr[0]))? $arr[0]['approvalType'] : '';

                                        $empID = '';
                                        if($approvalType == 3){
                                            if(array_key_exists($level, $approvalEmp)){
                                                $empID = array_column($approvalEmp[$level], 'empID');
                                            }
                                        }

                                        $style = ($approvalType == 3)? 'display:block !important' : '';

                                        echo '<tr>
                                                <td>
                                                    Level '.$level.'
                                                    <input type="hidden" name="appLevel[]" value="'.$level.'" />
                                                </td>
                                                <td style="width: 300px !important;">
                                                    <div style="">
                                                        '.form_dropdown('appType[]', $appTypeArr, $approvalType, 'class="form-control select2" onchange="load_employee(this)" ') .'
                                                    </div>
                                                </td>
                                                <td style="width: 210px !important;">
                                                    <div class="empDiv" style="width: 200px;'.$style.'">
                                                        '.form_dropdown('empID_'.$level.'[]', $empDrop, $empID, 'class="form-control empID_drop" multiple') .'
                                                    </div>
                                                </td>
                                        </tr>';
                                        $x++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </fieldset>
                </div>

            <?php } ?>
       
    </div>
</div>

<script>

    $(document).ready(function(){
        
        load_elements();

        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave-approval-setup','Test','HRMS');
        });

    });

    function load_elements(){
        $('.select2').select2();

        $('.empID_drop').multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
    }

    function saveSetup(){
        
        var postData = $('#setup-form').serializeArray();
        var leaveGroupID = $('#leaveGroupID :selected').val();
        
        postData.push({'name': 'leaveGroupID', 'value': leaveGroupID});

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/setup_leaveApproval') ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function save_approval_levels(){
        var levelTxt = $('#level-txt').val();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_leave_approval_levels') ?>',
            data: {level: levelTxt},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                setTimeout(function(){
                    fetchPage('system/hrm/leave-approval-setup','Test','HRMS');
                }, 300);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function load_employee(obj){
        var appType = $(obj).val();
        var empDropDiv = $(obj).closest('tr').find('td:eq(2) .empDiv');

        if(appType == 3){
            empDropDiv.show();
        }
        else{
            empDropDiv.hide();
        }
    }

    function change_leave_group(){

        var leaveGroupID = $('#leaveGroupID :selected').val();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/get_leave_approval_setup_on_group') ?>',
            data: {leaveGroupID: leaveGroupID},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#approval_setup_section').empty();
                $('#approval_setup_section').html(data);

                load_elements();
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }
</script>

<?php
