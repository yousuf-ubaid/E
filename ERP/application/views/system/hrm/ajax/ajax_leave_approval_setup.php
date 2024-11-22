<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('hrms_leave_management', $primaryLanguage);

?>

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