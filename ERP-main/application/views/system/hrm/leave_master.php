
<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_type_master');
echo head_page($title, false);



?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_leave()" ><i class="fa fa-plus"></i>
                <?php echo $this->lang->line('common_add');?><!--Add-->
            </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="leaveMaster" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 18%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('hrms_leave_management_leave_plan_applicable');?><!--Leave Plan Applicable--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('hrms_leave_management_leave_reason_applicable');?><!--Leave Plan Applicable--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('hrms_leave_management_is_annual_leave');?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('hrms_leave_management_is_sick_leave');?><!--Is sick leave--></th>
                <th style="min-width: 8%"><?php echo  $this->lang->line('hrms_leave_management_isshortleave')?> </th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_sort_order');?><!--Sort order--></th>
                <th style="min-width: 6%"></th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="leaveType_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_leave_management_new_leave_type');?><!--New Leave Type--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="newLeave_form" method="get"'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="leaveDescription" name="leaveDescription">
                    </div>
                </div>

                <div class="form-group" style="display: none">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_no_pay');?><!--Is Paid Leave--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isPaidLeave" class="isPaidLeave" id="isPaid" value="1" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isPaidLeave" class="isPaidLeave" id="isNotPaid" value="0">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-group annual-leave-div">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_is_annual_leave');?></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isAnnualLeave" class="isAnnualLeave" id="isAnnualLeave" value="1" onclick="" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isAnnualLeave" class="isAnnualLeave" id="isNotAnnualLeave" value="0">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group sick-leave-div">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_is_sick_leave');?><!--Is sick leave--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isSickLeave" class="isSickLeave" id="isSickLeave" value="1" onclick="checkshortleavevalidate(this,'isNotSickLeave')">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isSickLeave" class="isSickLeave" id="isNotSickLeave" value="0" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_attachment');?><!--Attachment--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="attachmentRequired" class="attachmentRequired" id="attachment" value="1" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="attachmentRequired" class="attachmentRequired" id="notattachment" value="0">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_leave_plan_applicable');?><!--Leave Plan Applicable--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leavePlanApplicable" class="leavePlanApp" id="leavePlanApplicable" value="1" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leavePlanApplicable" class="leavePlanApp" id="no_leavePlanApplicable" value="0">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo  $this->lang->line('hrms_leave_management_isshortleave')?></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isShortLeave" class="isShortLeave" id="isShortLeave" onclick="checkshortleavevalidate(this,'no_isShortLeave')" value="1" >
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isShortLeave" class="isShortLeave" id="no_isShortLeave" onclick="checkshortleavevalidate(this,'no_isShortLeave')" value="0" checked >
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_leave_reason_applicable');?><!--Leave Plan Applicable--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leaveReasonApplicable" class="leavePlanApp" id="leaveReasonApplicable" value="1" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leaveReasonApplicable" class="leavePlanApp" id="no_leaveReasonApplicable" value="0">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_final_settlement');?><!--Leave Final Settlement--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leaveFinalSettlement" class="leavePlanApp" id="leaveFinalSettlement" value="1">
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="leaveFinalSettlement" class="leavePlanApp" id="noLeaveFinalSettlement" value="0" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group slmaxhrmin hidden" >
                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('hrms_leave_management_shortleavehoursminuites')?><!--Short Leave Hours Minuites--></label>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="shortLeaveMaxHours" name="shortLeaveMaxHours"  placeholder="HH">
                    </div>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="shortLeaveMaxMins" name="shortLeaveMaxMins" placeholder="MM">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="updateBtn" ><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <input type="hidden" id="editID" name="editID">
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveSetup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
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
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" id="sortOrder" name="sortOrder" style="text-align: right">
                    </div>
                </div>

                <div class="form-group setup-confirm-div">
                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_is_confirmed');?><!--Is Confirmed--></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isConfirmed" class="isConfirmed" id="isConfirmed" value="1" >
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_yes');?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name="isConfirmed" class="isConfirmed" id="isNotConfirmed" value="0" checked>
                            </span>
                            <input type="text" class="form-control inputText" value="<?php echo $this->lang->line('common_no');?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_leaveSetup()" >
                    <?php echo $this->lang->line('common_save_change');?><!--Save Changes-->
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <input type="hidden" id="leaveEditID" name="leaveEditID">
            <?php echo form_close();?>
        </div>
    </div>
</div>
<div class="modal fade" id="new_reason" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_leave_management_leave_reason');?><!--Add reason--></h4>
            </div>
            <form class="form-horizontal" id="add-reason_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="reason-add-tb">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_reason');?><!--reason--></th>
                                <th>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" id="description" name="description[]" class="form-control saveInputs new-items" />
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_reason()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <input type="hidden" id="reasonEditID" name="reasonEditID">
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

<script>
        var reason_tb = $('#reason-add-tb');
    var urlSave = '<?php echo site_url('Employee/save_sickLeaveFormula') ?>';
    var isPaySheetGroup = 0;

    var modalBtn = $('.modalBtn');

    $(document).ready(function() {
        load_leaveMaster();
        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave_master','Test','HRMS');
        });

        $('#newLeave_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                leaveDescription: {validators: {notEmpty: {message: 'Description is required.'}}},
                policy: {validators: {notEmpty: {message: 'Policy is required.'}}},
            },
        })
            .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();

            var editID  = $('#editID').val();
            if( editID == ''){
                save();
            }else{
                update();
            }

        });
    });
    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });
    function add_more(){
        var appendData = '<tr><td><input type="text" name="description[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        reason_tb.append(appendData);
    }

    function load_leaveMaster(selectedRowID=null){
        $('#leaveMaster').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_leaveTypes'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "columnDefs": [ {
                "targets": [0,2,3,4,5,6,7],
                "orderable": false
            }, {"searchable": false, "targets": [0]} ],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    var planAppStr = parseInt(oSettings.aoData[x]._aData['planAppStr']);
                    var planStr = 'No';
                    if(planAppStr == 1){ planStr = 'Yes'; }
                    $('td:eq(2)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html('<div style="text-align: center">'+planStr+'</div>');

                    var reasonAppStr = parseInt(oSettings.aoData[x]._aData['reasonAppStr']);
                    var reasonStr = 'No';
                    if(reasonAppStr == 1){ reasonStr = 'Yes'; }
                    $('td:eq(3)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html('<div style="text-align: center">'+reasonStr+'</div>');


                    var isAnnualLeave = parseInt(oSettings.aoData[x]._aData['isAnnualLeave']);
                    var annualLeave = (isAnnualLeave == 1)? 'Yes': 'No';
                    $('td:eq(4)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html('<div style="text-align: center">'+annualLeave+'</div>');

                    var sickLeaveStr = parseInt(oSettings.aoData[x]._aData['sickLeaveStr']);
                    var sickLeave = 'No';
                    if(sickLeaveStr == 1){ sickLeave = 'Yes'; }
                    $('td:eq(5)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html('<div style="text-align: center">'+sickLeave+'</div>');

                    var isShortLeave = parseInt(oSettings.aoData[x]._aData['isShortLeave']);
                    var isShortLeaves = 'No';
                    if(isShortLeave == 1){ isShortLeaves = 'Yes'; }
                    $('td:eq(6)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html('<div style="text-align: center">'+isShortLeaves+'</div>');


                    if( parseInt(oSettings.aoData[x]._aData['ID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "ID"},
                {"mData": "description"},
                {"mData": "planAppStr"},
                {"mData": "reasonAppStr"},
                {"mData": "isAnnualLeave"},
                {"mData": "sickLeaveStr"},
                {"mData": "isShortLeave"},
                {"mData": "sortOrderStr"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function new_leave(){
        $('.sick-leave-div').show();
        modalBtn.hide();
        modalBtn.removeAttr('disabled');
        $('#saveBtn').show();

        $('.isPaidLeave').prop('checked', false);
        $('#isAnnualLeave, #isPaid, #attachment , #leavePlanApplicable,#leaveReasonApplicable, #isNotSickLeave,#no_isShortLeave').prop('checked', true);
        $('#newLeave_form input, #newLeave_form select').not('.isPaidLeave, .attachmentRequired, .leavePlanApp, .isSickLeave, .inputText, .isShortLeave, .isAnnualLeave').prop('value', '');
        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_leave_management_new_leave_type');?>');
        if($('#isShortLeave').is(':checked')){
            $('.slmaxhrmin').removeClass('hidden');
        }else{
            $('.slmaxhrmin').addClass('hidden');
        }
        $('#leaveType_modal').modal({backdrop: "static"});
    }

    function save(){

        var postData = $('#newLeave_form').serializeArray();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_leaveTypes'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#leaveType_modal').modal('hide');
                    load_leaveMaster();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }

    function edit_LeaveType( editID, des, isPaidLeave, isAnnualLeave, attachmentRequired, isPlanApplicable,finalSettlementYN,isShortLeave,shortLeaveMaxHours,shortLeaveMaxMins,isSickLeave,reasonApplicableYN = 0){
        // $('.sick-leave-div').hide();
        $('#newLeave_form input, #newLeave_form select').not('.isPaidLeave, .attachmentRequired, .leavePlanApp, .isSickLeave,.isShortLeave, .inputText, .isAnnualLeave').prop('value', '');
        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_leave_management_edit_leave_type');?>');
        $('#editID').val(editID);
        $('#leaveDescription').val(des);
       /* $('#policy').val(policy);*/
        modalBtn.hide();
        modalBtn.removeAttr('disabled');
        $('#updateBtn').show();
        isNotSickLeave
        $('.isPaidLeave').prop('checked', false);

        if( finalSettlementYN == 1 ){
            $('#leaveFinalSettlement').prop('checked', true);
        }
        else{
            $('#noLeaveFinalSettlement').prop('checked', true);
        }

        if( isPaidLeave == 1 ){
            $('#isPaid').prop('checked', true);
        }
        else{
            $('#isNotPaid').prop('checked', true);
        }

        if( isSickLeave == 1 ){
            $('#isSickLeave').prop('checked', true);
        }
        else{
            $('#isNotSickLeave').prop('checked', true);
        }

        if( isAnnualLeave == 1 ){
            $('#isAnnualLeave').prop('checked', true);
        }
        else{
            $('#isNotAnnualLeave').prop('checked', true);
        }

        if( attachmentRequired == 1 ){
            $('#attachment').prop('checked', true);
        }
        else{
            $('#notattachment').prop('checked', true);
        }

        if( isPlanApplicable == 1 ){
            $('#leavePlanApplicable').prop('checked', true);
        }
        else{
            $('#no_leavePlanApplicable').prop('checked', true);
        }
        if( reasonApplicableYN == 1 ){
            $('#leaveReasonApplicable').prop('checked', true);
        }
        else{
            $('#no_leaveReasonApplicable').prop('checked', true);
        }
        if( isShortLeave == 1 ){
            $('#isShortLeave').prop('checked', true);
            $('#no_isShortLeave').prop('checked', false);
        }
        else{
            $('#no_isShortLeave').prop('checked', true);
            $('#isShortLeave').prop('checked', false);
        }
        $('#shortLeaveMaxHours').val(shortLeaveMaxHours);
        $('#shortLeaveMaxMins').val(shortLeaveMaxMins);
        if($('#isShortLeave').is(':checked')){
            $('.slmaxhrmin').removeClass('hidden');
        }else{
            $('.slmaxhrmin').addClass('hidden');
        }
        $('#leaveType_modal').modal({backdrop: "static"});
    }

    function update(){
        var postData = $('#newLeave_form').serialize();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/update_leaveTypes'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#leaveType_modal').modal('hide');
                    load_leaveMaster(  $('#editID').val() );
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })
    }

    function delete_LeaveType(delID, des){
        swal(
            {
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_leaveTypes'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'deleteID':delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_leaveMaster() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function leaveSetup(id, description, obj){
        var table = $('#leaveMaster').DataTable();
        var thisRow = $(obj);
        var details = table.row(  thisRow.parents('tr') ).data();

        $('#leaveEditID').val(id);
        $('#leaveDes').val(description);
        $('#sortOrder').val(details.sortOrder);
        $('.setup-confirm-div').show();
        if(details.typeConfirmed == 1){
            $('.setup-confirm-div').hide();
            $('#isConfirmed').prop('checked', true);
        }
        else{
            $('#isNotConfirmed').prop('checked', true);
        }

        $('#leaveSetup_modal').modal('show');
    }
    function viewReason(id, description, obj){
        var table = $('#leaveMaster').DataTable();
        var thisRow = $(obj);
        var details = table.row(  thisRow.parents('tr') ).data();

        $('#reasonEditID').val(id);
        $('#description').val(description);
        
        
        $('#new_reason').modal('show');
    }


    function save_leaveSetup(){

        var postData = $('#leaveSetup_form').serializeArray();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_sickLeaveSetup'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#leaveSetup_modal').modal('hide');
                    load_leaveMaster();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function checkshortleavevalidate(ds,det){
        var isShortLeave =$('#isShortLeave').val();
        var isSickLeave =$('#isSickLeave').val();

        if($('#isShortLeave').is(':checked') && $('#isSickLeave').is(':checked')){
            $(ds).prop('checked', false);
            $('#'+det).prop('checked', true);
            myAlert('w','If Sick leave is YES short leave canot be YES')
        }

        if($('#isShortLeave').is(':checked')){
            $('.slmaxhrmin').removeClass('hidden');
        }else{
            $('.slmaxhrmin').addClass('hidden');
        }


    }
    function save_reason(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#add-reason_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/saveReason'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_reason').modal('hide');
                        // load_religions();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');/*Please fill all fields*/
        }
    }

</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-25
 * Time: 1:10 PM
 */