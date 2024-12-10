<!--Translation added by Naseek-->

<?php $policyID = $master['isMonthly'];
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);



?>
<button class="btn btn-primary btn-xs pull-right" onclick="modalleaveDetail()"><?php echo $this->lang->line('common_add');?><!--Add--></button>

<table class="<?php echo table_class() ?>">
    <thead>
    <tr style="white-space: nowrap">
        <th><?php echo $this->lang->line('hrms_leave_management_leave_type');?><!--Leave Type--></th>
        <th><?php echo $this->lang->line('hrms_leave_management_policy');?><!--Policy--></th>

        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_rotation_working_days');?><!--No of Hours--></th>

        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_no_of_day');?><!--No of days--></th>
        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_is_calender_days');?><!--Is Calender Days--></th>

        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_is_allow_minus');?><!--Is Allow minus--></th>
        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_is_carry_forward');?><!--Is carry forward--></th>
        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_is_rotation_leave');?><!--Is carry forward--></th>
        <th style="text-align: right"><?php echo $this->lang->line('hrms_leave_management_max_carry_forward')?></th>
        <th style="text-align: right">Max Consecetive Days</th>
        <th style="text-align: right">Accrual After Month</th>
        <th style="text-align: right">Provision Month</th>
        <?php if($isAnnual){
            if($isAnnual == 1){ ?>
            <th style="text-align: right">action</th>
        <?php   }
        } ?>
        <th></th>
        <?php if (empty($set)) { ?>
            <th style="text-align: right"></th>
        <?php } ?>

    </tr>
    </thead>
    <tbody>

    <?php if ($details) {
        foreach ($details as $val) {


            $CI =& get_instance();
            $set = $CI->db->query("SELECT * FROM srp_employeesdetails WHERE leaveGroupID={$val['leaveGroupID']}")->row_array();

            ?>
            <tr id="row_<?php echo $val['leaveGroupDetailID'] ?>">
                <td><?php echo $val['description'] ?></td>
                <td><?php echo $val['policyDescription'] ?></td>

                    <td style="text-align: right;width: 100px"><?php echo $val['noOfDaysCompleted'] ?></td>

                    <td style="text-align: right;width: 100px"><?php echo $val['noOfDays'] ?></td>
                    <td style="text-align: right;width: 100px"><?php echo($val['isCalenderDays'] == 1 ? 'Yes' : 'No') ?></td>


                <td style="text-align: right;width: 100px"><?php echo($val['isAllowminus'] == 1 ? 'Yes' : 'No') ?></td>
                <td style="text-align: right;width: 100px"><?php echo($val['isCarryForward'] == 1 ? 'Yes' : 'No') ?></td>
                <td style="text-align: right;width: 100px"><?php echo($val['isRotationLeave'] == 1 ? 'Yes' : 'No') ?></td>
                <td style="text-align: right;width: 100px"><?php echo $val['maxCarryForward']  ?></td>
                <td style="text-align: right;width: 100px"><input type="number" name="maxConsecetiveDays" step="any" class="form-control" id="maxConsecetiveDays" value="<?php echo $val['maxConsecetiveDays']  ?>" onchange="updateValues('maxConsecetiveDays', this.value, <?php echo $val['leaveGroupDetailID'] ?>)"></td>
                <td style="text-align: right;width: 100px"><input type="number" name="accrualAfterMonth" step="any" class="form-control" id="accrualAfterMonth" value="<?php echo $val['accrualAfterMonth']  ?>" onchange="updateValues('accrualAfterMonth', this.value, <?php echo $val['leaveGroupDetailID'] ?>)"></td>
                <td style="text-align: right;width: 100px"><input type="number" name="provisionAfterMonth" step="any" class="form-control" id="provisionAfterMonth" value="<?php echo $val['provisionAfterMonth']  ?>" onchange="updateValues('provisionAfterMonth', this.value, <?php echo $val['leaveGroupDetailID'] ?>)"></td>

                <?php if($isAnnual){
                    if($isAnnual == 1){ ?>
                        <td style="text-align: right;width: 100px"><a onclick="model_maxElig(<?php echo $val['leaveGroupDetailID'] ?>)"><span title="Setup" rel="tooltip"><i class="fa fa-cogs" aria-hidden="true" style="color:black"></i></span></a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <?php }
                } ?>

                <td>
                    <?php if (empty($set)) { ?><a
                        onclick="deleteLeavedeltails(<?php echo $val['leaveGroupDetailID'] ?>);"><span
                                    class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>
                        </a><?php } ?></td>

            </tr>
            <?php

        }
    }
    ?>

    </tbody>
</table>


<div class="modal fade" id="upload_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b style="font-size: 14px;" id="upload_type">Add</b></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="upload_form" '); ?>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="uploadID" id="uploadID" value="">
                    <div class="col-sm-2">
                        <label for="max_encashment" class="col-sm-4 control-label">Max Encashment</label>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" name="max_encashment" class="form-control" id="max_encashment" placeholder="Brows Here">
                    </div>
                    <div class="col-sm-2">
                        <label for="isEligible" class="col-sm-4 control-label">Is Eligible</label>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('isEligible', array('' => "select", '0' => "No", '1' => "yes"), '', 'class="form-control" id="isEligible" required'); ?>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="uploadDocID" id="uploadDocID" value="">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveMaxElig()"><?php echo $this->lang->line('common_save');?></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<script type="text/javascript">

    function model_maxElig(leaveGroupDetailID) {
        $('#uploadID').val(leaveGroupDetailID);
        $('#max_encashment').val('');
        $('#isEligible').val('');
        $("#upload_modal").modal({backdrop: "static", keyboard: true});       
    }

    function saveMaxElig(){
        var max_encashment = $('#max_encashment').val();
        var eligibleforencashment = $('#isEligible').val();
        var leaveGroupDetailID = $('#uploadID').val();
                    $.ajax({
                        async: false,//true
                        type: 'post',
                        dataType: 'json',
                        data: {'leaveGroupDetailID': leaveGroupDetailID, 'max_encashment': max_encashment, 'eligibleforencashment' : eligibleforencashment},
                        url: "<?php echo site_url('Employee/save_maxEncash_and_eligible'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);


                        }, error: function () {
                            swal("Cancelled", "", "error");
                        }
                    });
    }

    function updateValues(fieldName, value,leaveGroupDetailID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'leaveGroupDetailID': leaveGroupDetailID, 'fieldName' : fieldName, 'value': value},
                url: "<?php echo site_url('Employee/updateValues'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }


</script>