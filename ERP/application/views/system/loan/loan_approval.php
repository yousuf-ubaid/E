
<!--Translation added by naseek
-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_loan_approval');
echo head_page($title, false);
?>
<style type="text/css">
.empDisTbTR {
    width: 110px;
    padding-left: 10px;
    font-weight: 700;
}

.empDetailDisplay {
    padding-left: 10px;
    font-weight: 700;
}
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tbody>
                <tr>
                    <td>
                        <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!-- Approved-->
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
        <?php echo form_dropdown('approvedYN', array('0'=>$this->lang->line('common_pending'),'1'=>$this->lang->line('common_approved')), '','class="form-control" id="approvedYN" required onchange="loan_table_approval()"'); ?>
    </div>
</div><hr>

<div class="table-responsive">
    <table id="loan_table_approval" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_loan_date');?><!--Loan Date--></th>
            <th style="min-width: 30%"><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Narration--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('hrms_payroll_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="loanApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_loan_approval');?><!--Loan Approval--></h4>
            </div>
            <form class="form-horizontal" id="loan_approval_form">
                <div class="modal-body">
                    <div id="conform_body">
                        <div class="box-body">
                            <div class="col-md-1">
                                <div class="">
                                    <a href="#" class="thumbnail"> <img src="<?php echo base_url(); ?>images/default.gif" id="empImg" alt=""> </a>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <table border="0px">
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empNameDis"></td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Employee Code--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empCodeDis"></td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('common_designation');?><!--Designation--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empDisgnationDis"></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-5">
                                <table border="0px">
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('hrms_payroll_loan_code');?><!--Loan Code--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="disLoanCode">-</td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('common_approved_by');?><!--Approved By--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empCodeDis">-</td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="loanStatus">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <table style="width: 100%">
                            <tbody>
                            <tr>
                                <td><b><?php echo $this->lang->line('hrms_payroll_loan_type');?><!--Loan Type--></b></td>
                                <td>:</td>
                                <td id="con_loanType"> </td>
                                <td><b><?php echo $this->lang->line('common_percentage');?><!--Int. Percentage--></b></td>
                                <td>:</td>
                                <td id="con_intPer"> </td>
                            </tr>

                            <tr>
                                <td><b><?php echo $this->lang->line('hrms_payroll_loan_date');?><!--Loan Date--></b></td>
                                <td>:</td>
                                <td id="con_loanDate"> </td>
                                <td><b><?php echo $this->lang->line('hrms_payroll_loan_amount');?><!--Loan Amount--></b></td>
                                <td>:</td>
                                <td id="con_amount"> </td>
                            </tr>

                            <tr>
                                <td><b><?php echo $this->lang->line('hrms_payroll_no_of_installment');?><!--No. of Installment--></b></td>
                                <td>:</td>
                                <td id="con_noOfIns"> </td>
                                <td><b><?php echo $this->lang->line('hrms_payroll_deduction_start_date');?><!--Deduction Start Date--></b></td>
                                <td>:</td>
                                <td id="con_dedStartDate"> </td>
                            </tr>

                            <tr>
                                <td><b><?php echo $this->lang->line('hrms_payroll_loan_description');?><!--Loan Description--></b></td>
                                <td>:</td>
                                <td id="con_loanDes">
                                <td class="salary_advance_container"><b><?php echo $this->lang->line('common_salary_advance_request');?></b></td>
                                <td class="salary_advance_container">:</td>
                                <td class="salary_advance_container" id="salary_advance_code"> </td>
                            </tr>
                            <tr><td colspan="6">&nbsp;</td></tr>
                            </tbody>
                        </table>

                        <hr>
                        <div style="margin:1%">&nbsp;</div>

                        <table id="" class="<?php echo table_class(); ?> loanScheduleTB">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('common_date');?><!--Deduction Date--> </th>
                                <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                            </tr>
                            </thead>
                        </table>

                    </div><hr>
                    <div class="form-group  form_items">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array(''=> $this->lang->line('common_please_select'),'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '','class="form-control controlCls" id="status" required'); ?>
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="hiddenLoanID" id="hiddenLoanID">
                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                        </div>
                    </div>
                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm controlCls form_items"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/loan/loan_approval','','HRMS');
        });
        loan_table_approval();
        $('#loan_approval_form').bootstrapValidator({
            live            : 'enabled',
            message         : 'This value is not valid.',
            excluded        : [':disabled'],
            fields          : {
                status     			    : {validators : {notEmpty:{message:'Loan Status is required.'}}},
                Level                   : {validators : {notEmpty:{message:'Level Order Status is required.'}}},
                //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
                hiddenLoanID    		: {validators : {notEmpty:{message:'Loan ID is required.'}}},
                documentApprovedID      : {validators : {notEmpty:{message:'Document Approved ID is required.'}}}
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('loan/loanApproval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                   /* refreshNotifications(true);
                    $("#loanApprove_modal").modal('hide');
                    loan_table_approval();*/
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#loanApprove_modal").modal('hide');
                        loan_table_approval();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function loan_table_approval(){
        var Otable = $('#loan_table_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/fetch_loan_conformation'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

            },
            "aoColumns": [
                {"mData": "loanID"},
                {"mData": "loanCode"},
                {"mData": "loanDate"},
                {"mData": "loanDescription"},
                {"mData": "empName"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
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

    function load_emp_loanDet(loanID , documentID, approvalLevel,appYN){
        $('.salary_advance_container').hide();
        $('.form_items').show();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/load_emp_loanDet') ?>',
            data: {'loanID': loanID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(appYN==1){
                    $('.form_items').hide();
                }
                else{$('.form_items').show();}
                stopLoad();
                loadLoanSchedule(loanID);

                $("#loanApprove_modal").modal({backdrop: "static"});
                var intPer = ( data['interestPer'] == 0 ) ? '' : data['interestPer'];

                $('#hiddenLoanID').val(data['ID']);
                $('#documentApprovedID').val(documentID);
                $('#level').val(approvalLevel);
                $('#hiddenLoanCode').val(data['loanCode']);
                $('#empID').val(data['EIdNo']);
                $('#empName').val(data['Employee']);
                $('#empNameDis').text(data['Employee']);
                $('#empCodeDis').text(data['ECode']);
                $('#empDisgnationDis').text(data['DesDescription']);
                $('#disLoanCode').text(data['loanCode']);

                //values for conformation tab
                intPer = ( intPer == '' )? '-' : intPer;
                $('#con_loanType').text( data['description'] );
                $('#con_intPer').text(intPer);
                $('#con_loanDate').text(data['loanDate']);
                $('#con_amount').text(commaSeparateNumber(data['amount']));
                $('#con_noOfIns').text(data['numberOfInstallment']);
                $('#con_dedStartDate').text(data['deductionStartingDate']);
                $('#con_loanDes').text(data['loanDescription']);

                if(data['salaryAdvanceRequestID'] > 0){
                    $('#salary_advance_code').text( data['ad_document_code'] );
                    $('.salary_advance_container').show();
                }

                $('.controlCls').prop('disabled', false);
                if( approvalLevel > 1 ){
                    isPreviousLevelsApproved(loanID , documentID, approvalLevel);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function loadLoanSchedule(loanID){
        var Otable = $('.loanScheduleTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/load_empLoanSchedule?loanID='); ?>"+loanID,
            "aaSorting": [[2, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

            },
            "aoColumns": [
                {"mData": "scheduleID"},
                {"mData": "scheduleDate1"},
                {"mData": "amount"},
                {"mData": "status"}
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

    function isPreviousLevelsApproved(loanID , documentID, approvalLevel){
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/isPreviousLevelsApproved') ?>',
            data: {'loanID': loanID, 'docID': documentID, 'appLevel': approvalLevel},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data == false){
                    myAlert('e', 'Previous Level Approval is Still Pending');
                    $('.controlCls').prop('disabled', true);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/26/2016
 * Time: 5:53 PM
 */