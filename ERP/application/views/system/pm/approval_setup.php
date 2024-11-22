<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
echo head_page('Timesheet Approval', FALSE);
$empDrop = load_employee_drop();
$projectmanagementapprovaldet = getprojectmanagementApprovalSetup('Y');
$approvalLevel = $projectmanagementapprovaldet['approvalLevel'];
$approvalSetup = $projectmanagementapprovaldet['approvalSetup'];
$approvalEmp = $projectmanagementapprovaldet['approvalEmp'];
$appTypeArr = $projectmanagementapprovaldet['appSystemValues'];
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
                    <legend class="scheduler-border">Timesheet Approval Levels</legend>
                    <div style="margin-top: 10px">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="level-txt" class="col-md-2"> <?php echo $this->lang->line('hrms_leave_management_levels')?></label>
                                <input type="number" name="level-txt" id="level-txt" class="col-md-4 number" value="<?php echo $approvalLevel; ?>">
                                <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-bottom: 10px"
                                        onclick="save_approval_levels()"><?php echo $this->lang->line('common_save')?>
                                </button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border">Timesheet Approval setup</legend>
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
                                        <td style="width: 150px !important;">
                                            <div style="width: 140px;">
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
        </div>
    </div>

    <script>

        $(document).ready(function(){
            $('.select2').select2();

            $('.empID_drop').multiselect2({
                enableCaseInsensitiveFiltering: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $('.headerclose').click(function(){
                fetchPage('system/pm/approval_setup','Test','PM');
            });

        });

        function saveSetup(){
            var postData = $('#setup-form').serializeArray();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Boq/setup_timesheetApproval') ?>',
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
                url: '<?php echo site_url('Boq/save_pm_approval_levels') ?>',
                data: {level: levelTxt},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    setTimeout(function(){
                        fetchPage('system/pm/approval_setup','Test','PM-T');
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
    </script>

<?php
