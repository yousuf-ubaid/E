

<!--Translation added by naseek
-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('hrms_payroll_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_leave_approval');
$titleTab2 = $this->lang->line('hrms_payroll_general_cancellation_approval');
echo head_page($title, false);

$status_arr = [
    '0' => $this->lang->line('common_pending'),
    '1' => $this->lang->line('common_approved')
];
$leaveTypes = leaveTypes_drop();
?>

<style type="text/css">
.frm_input{
    height: 28px;
    font-size: 12px;
}

.panel-body {
    margin-bottom: 20px;
    background-color: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
}
</style>

<div id="filter-panel" class="collapse filter-panel"></div>


<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#approvelTab" data-toggle="tab" aria-expanded="true"><?php echo $title;?> </a>
        </li>
        <li class="">
            <a href="#cancellationAppTab" data-toggle="tab" aria-expanded="false"><?php echo $titleTab2;?></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="approvelTab">
            <div class="row">
                <div class="col-md-5">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td>
                                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                            </td>
                            <td>
                                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-center">
                    <?php echo form_dropdown('approvedYN', $status_arr, '','class="form-control" id="approvedYN" onchange="leave_table_approval()"'); ?>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="leave_table_approval" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th style="min-width: 40%"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                        <th style="min-width: 7%">From</th>
                        <th style="min-width: 8%">To</th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('hrms_payroll_level');?><!--Level--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="tab-pane" id="cancellationAppTab">
            <div class="row">
                <div class="col-md-5">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td>
                                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                            </td>
                            <td>
                                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-center">
                    <?php echo form_dropdown('approvedCancelYN', $status_arr, '','class="form-control" id="approvedCancelYN" onchange="leave_cancel_approval()"'); ?>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="leave_cancel_approval" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th style="min-width: 40%"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--></th>
                        <th style="min-width: 7%">From</th>
                        <th style="min-width: 8%">To</th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('hrms_payroll_level');?><!--Level--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="leaveApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span id="approval-title"></span> <span id="levelText"></span>
                </h4>
            </div>
            <form class="form-horizontal" id="leave_approval_form">
                <div class="modal-body">
                    <div id="app-chk"></div>
                    <hr class="approved">
                    <div class="form-group approved">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array(''=>$this->lang->line('common_please_select')/*'Please Select'*/,'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '','class="form-control controlCls" id="status" required'); ?>
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="hiddenLeaveID" id="hiddenLeaveID">
                            <input type="hidden" name="isFromCancelYN" id="isFromCancelYN">
                        </div>
                    </div>
                    <div class="form-group approved">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm approved"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var entitleSpan = $('#entitleSpan');
    var takenSpan = $('#takenSpan');
    var balanceSpan = $('#balanceSpan');
    var policySpan = $('#policySpan');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave_approval','','HRMS');
        });

        leave_table_approval();

        leave_cancel_approval();

        $('#leave_approval_form').bootstrapValidator({
            live            : 'enabled',
            message         : 'This value is not valid.',
            excluded        : [':disabled'],
            fields          : {
                //status     			    : {validators : {notEmpty:{message:'Status is required.'}}},
                //Level                   : {validators : {notEmpty:{message:'Level Order Status is required.'}}},
                //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
                //hiddenLeaveID    		: {validators : {notEmpty:{message:'Leave ID is required.'}}}
            },
        })
            .on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Employee/leaveApproval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#leaveApprove_modal").modal('hide');

                        if( $('#isFromCancelYN').val() == 1){
                            leave_cancel_approval();
                        }
                        else{
                            leave_table_approval();
                        }

                        $('#comments').val('');
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function leave_table_approval(){
        var Otable = $('#leave_table_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_leave_conformation'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "leaveMasterID"},
                {"mData": "documentCode"},
                {"mData": "empName"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "levelNo"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,6,7], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "approvedYN","value": $("#approvedYN :selected").val()});
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

    function leave_cancel_approval(){
        var Otable = $('#leave_cancel_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_leave_cancellation_approval'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }


            },
            "aoColumns": [
                {"mData": "leaveMasterID"},
                {"mData": "documentCode"},
                {"mData": "empName"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "levelNo"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,6,7], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "approvedYN","value": $("#approvedCancelYN :selected").val()});
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

    function load_emp_leaveDet(leaveID , approval, level, isFromCancel){
        $('#leave_approval_form').bootstrapValidator('resetForm', true);
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employeeLeave_detailsOnApproval') ?>',
            data: {'masterID': leaveID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#leaveApprove_modal").modal({backdrop: "static"});
                $('#comments').val('');
                var empDet = data['empDet'];
                var leaveDet = data['leaveDet'];
                var entitleDet = data['entitleDet'];

                if($.isEmptyObject(entitleDet)){
                    entitleDet = {balance : 0 };
                }

                $('#leaveCode').text(leaveDet['documentCode']);
                $('#empID').val(empDet['EIdNo']);
                $('#empNameSpan').text( empDet['ECode']+" | "+empDet['employee'] );
                $('#empCodeSpan').text(empDet['EmpSecondaryCode']);
                $('#designationSpan').text(empDet['DesDescription']);
                $('#department').text(empDet['department']);
                $('#reportingManager').text(empDet['manager']);

                var leaveType = $('#leaveType');
                leaveType.val(leaveDet['leaveTypeID']);
                $('#leaveTypeSpan').text(leaveDet['description']);
                if(leaveDet['approvedYN']==1){  /*if approved set leaveavailable column leave master*/
                    entitleDet['balance']=leaveDet['leaveAvailable'];
                }
                if(leaveDet['policyMasterID']==2){
                    var l_taken = entitleDet['leaveTaken'];
                    var l_entitle = entitleDet['balance'];
                    entitleSpan.text(display(l_entitle));
                    takenSpan.text(display(leaveDet['hours']));
                    bal=   entitleDet['balance']-leaveDet['hours'];
                    balanceSpan.text( display(bal) );
                    policySpan.text(entitleDet['policyDescription']);
                }
                else{



             /*   if( isPaidLeave == 0 ){
                    entitleSpan.text(' None ');
                    takenSpan.text(' None ');
                    balanceSpan.text(' None ');
                    policySpan.text(' None ');
                }
                else{*/
                    var l_taken = entitleDet['leaveTaken'];
                    var l_entitle = entitleDet['balance'];
                    entitleSpan.text(l_entitle);
                    takenSpan.text(l_taken);
                    bal =   entitleDet['balance']-leaveDet['days'];
                    if (bal != parseInt(bal)){
                        bal = bal.toFixed(1);
                    }
                    balanceSpan.text( bal );
                    policySpan.text(entitleDet['policyDescription']);
              /*  }*/
                }
                $('#startDateSpan').text(leaveDet['startDate']);
                $('#endDateSpan').text(leaveDet['endDate']);
                $('#commentSpan').text(leaveDet['comments']);
                $('#dateSpan').text(leaveDet['entryDate']);
                $('#entryDate').text(leaveDet['entryDate']);
                $('#days').text(leaveDet['days']);


                $('#hiddenLeaveID').val(leaveID);
                $('#level').val(level);
                $('#status').val(1);

                var approvalTitle = (isFromCancel == 1)? 'Cancellation Approval' : '<?php echo $this->lang->line('hrms_payroll_leave_approval');?>';
                $('#approval-title').html(approvalTitle);
                $('#levelText').html('&nbsp;&nbsp;&nbsp; - Level '+level);
                $('#isFromCancelYN').val(isFromCancel);

                if(approval==1){
                    $('.approved').addClass("hidden");
                }else{
                    $('.approved').removeClass("hidden");
                }

                var current_userID = '<?php echo current_userID() ?>';
                if(current_userID == leaveDet['coveringEmpID']){
                    $('#entitleSpan, #balanceSpan').text('-');
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function load_emp_leaveDet_new(leaveID , approval, level, isFromCancel){
        $('#leave_approval_form').bootstrapValidator('resetForm', true);
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/leave_approval_view') ?>',
            data: {'masterID': leaveID,'approvalView': 1},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                    return false;
                }

                $("#leaveApprove_modal").modal({backdrop: "static"});
                $('#app-chk').html(data['view']);

                $('#hiddenLeaveID').val(leaveID);
                $('#level').val(level);
                $('#status').val(1);
                $('#comments').val('');
                $('#annualComment').attr('disabled',true);
                $('#leaveReason').attr('disabled',true);

                var approvalTitle = (isFromCancel == 1)? 'Cancellation Approval' : '<?php echo $this->lang->line('hrms_payroll_leave_approval');?>';
                $('#approval-title').html(approvalTitle);
                $('#levelText').html('&nbsp;&nbsp;&nbsp; - Level '+level);
                $('#isFromCancelYN').val(isFromCancel);

           
                if(approval == 1){
                    $('.approved').addClass("hidden");
                }else{
                    $('.approved').removeClass("hidden");
                }

                attachment_View_modal('LA', leaveID);

                var current_userID = '<?php echo current_userID() ?>';
                if(current_userID == data['coveringEmpID']){
                    $('#entitleSpan, #balanceSpan').text('-');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function display(a){
        if (a >= 0) {
            // Do Something
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return hours +"h :"+ minutes+"m";
        }
        else{
            a=Math.abs(a);
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return "-"+hours +"h :"+ minutes+"m";
        }


    }

</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-09-06
 * Time: 3:36 PM
 */