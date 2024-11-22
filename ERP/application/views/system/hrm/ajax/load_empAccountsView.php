<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_loan_lang', $primaryLanguage);

$empName = preg_replace('/[^\da-z ]/i', ' ', $empDetail['Ename2']);
?>
<div class="row">
    <div class="col-md-6 pull-left">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td><span class="label label-success"
                          style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('emp_is_active');?><!--Active-->
                </td>
                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                    <?php echo $this->lang->line('emp_in_active');?><!--In Active-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6 pull-right">
        <button type="button" class="btn btn-primary size-sm pull-right"
                onclick="add_bnkAccount()"><i class="fa fa-plus"></i>&nbsp; <?php echo $this->lang->line('emp_add');?><!--Add-->
        </button>
    </div>
</div>

<input type="hidden" id="hiddenEmpName">
<div class="row">
    <div class="col-sm-12">
        <fieldset>
            <legend><?php echo $this->lang->line('emp_bank_payroll');?><!--Payroll--></legend>
            <table class="table table-bordered" id="bankAccDetTb" >
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('emp_bank');?> <!--Bank--></th>
                    <th><?php echo $this->lang->line('emp_bank_branch');?> <!--Branch Name--></th>
                    <th><?php echo $this->lang->line('emp_swift_code');?></th>
                    <th><?php echo $this->lang->line('emp_bank_account_no');?><!-- Account No--></th>
                    <th><?php echo $this->lang->line('emp_bank_account_holder_name');?> <!--Account Holder--></th>
                    <th style="width:30px"> %</th>
                    <th style="width:50px"><?php echo $this->lang->line('common_primary');?> <!--Primary--></th>
                    <th style="width:50px"><?php echo $this->lang->line('emp_status');?> <!--Status--></th>
                    <th style="width:100px" class="hidbtn"> &nbsp; </th>
                </tr>
                </thead>
                <tbody>
                <?php

                //echo '<pre>'; print_r($accountDetails); echo '</pre>';
                if(!empty($accountDetails)){
                    $empID = $this->input->post('empID');
                    foreach($accountDetails as $row){
                        $accountID = $row['id'];
                        $bankID = $row['bankID'];
                        $branchID = $row['branchID'];
                        $accountNo = $row['accountNo'];
                        $accountHolderName = $row['accountHolderName'];
                        $percentage = $row['toBankPercentage'];
                        $status = $row['isActive'];
                        $flag = ($status == 1)? 'success' : 'danger';
                        $check = ($row['isPrimary'] == 1)? 'checked': '';
                        $ibancode = $row['ibancode'];
                        $swiftcode = $row['swiftcode'];

                        $editFn = 'bankAccDetEdit(\'' . $accountNo . '\',\'' . $accountHolderName . '\',';
                        $editFn .= '\''.$accountID.'\', \''.$bankID.'\', \''.$branchID.'\', \''.$percentage.'\', \''.$status.'\', 1, \'Payroll\',\''.$ibancode.'\',\''.$swiftcode.'\')';
                        $attachFn = 'fetchAttachments(\'' . $accountID . '\')';

                        $action = '<a onclick="'.$editFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>';
                        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_bankAccount('.$accountID.', 1)" title="Delete" rel="tooltip">';                       
                        $action .= '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="'.$attachFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-paperclip"></span></a>';

                        echo '<tr>
                                <td>'.$row['bankName'].'</td>
                                <td>'.$row['branchName'].'</td>
                                <td>'.$row['swiftcode'].'</td>
                                <td>'.$accountNo.'</td>
                                <td>'.$accountHolderName.'</td>
                                <td align="right">'.$percentage.'</td>
                                <td align="center">
                                    <div class="">
                                        <input name="employeeID" '. $check . ' type="checkbox" onchange="updatePrimaryBank(' . $accountID . ', '.$empID.')"/>
                                    </div>
                                </td>
                                <td align="center"><span class="label label-'.$flag.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></td>
                                <td align="right" class="hidbtn">' . $action . '</td>
                            </tr>';
                    }
                }
                else{
                    echo '<tr><td colspan="8">&nbsp;</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </fieldset>
    </div>

    <div style="height: 1%">&nbsp;</div>

    <div class="col-sm-12">
        <fieldset>
            <legend><?php echo $this->lang->line('emp_bank_non_payroll');?><!--Non Payroll--></legend>
            <table class="table table-bordered" id="bankAccDetTb" >
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('emp_bank');?> <!--Bank--></th>
                    <th><?php echo $this->lang->line('emp_bank_branch');?> <!--Branch Name--></th>
                    <th><?php echo $this->lang->line('emp_swift_code');?></th>
                    <th><?php echo $this->lang->line('emp_bank_account_no');?> <!--Account No--></th>
                    <th><?php echo $this->lang->line('emp_bank_account_holder_name');?> <!--Account Holder--></th>
                    <th style="width:30px"> %</th>
                    <th style="width:50px"><?php echo $this->lang->line('emp_status');?> <!--Status--></th>
                    <th style="width:100px" class="hidbtn"> &nbsp; </th>
                </tr>
                </thead>
                <tbody>
                <?php
                //echo '<pre>'; print_r($accountDetails); echo '</pre>';
                if(!empty($accountDetails_nonPayroll)){
                    $empID = $this->input->post('empID');
                    foreach($accountDetails_nonPayroll as $row){
                        $accountID = $row['id'];
                        $bankID = $row['bankID'];
                        $branchID = $row['branchID'];
                        $accountNo = $row['accountNo'];
                        $accountHolderName = $row['accountHolderName'];
                        $percentage = $row['toBankPercentage'];
                        $status = $row['isActive'];
                        $flag = ($status == 1)? 'success' : 'danger';
                        $ibancode = $row['ibancode'];
                        $swiftcode = $row['swiftcode'];

                        $editFn = 'bankAccDetEdit(\'' . $accountNo . '\',\'' . $accountHolderName . '\',';
                        $editFn .= '\''.$accountID.'\', \''.$bankID.'\', \''.$branchID.'\', \''.$percentage.'\', \''.$status.'\', 2, \'Non payroll\',\''.$ibancode.'\',\''.$swiftcode.'\')';
                        $attachFn = 'fetchAttachments(\'' . $accountID . '\')';

                        $action = '<a onclick="'.$editFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>';
                        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_bankAccount('.$accountID.', 2)" title="Delete" rel="tooltip">';
                        $action .= '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="'.$attachFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-paperclip"></span></a>';

                        echo '<tr>
                        <td>'.$row['bankName'].'</td>
                        <td>'.$row['branchName'].'</td>
                        <td>'.$row['swiftcode'].'</td>
                        <td>'.$accountNo.'</td>
                        <td>'.$accountHolderName.'</td>
                        <td align="right">'.$percentage.'</td>
                        <td align="center"><span class="label label-'.$flag.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></td>
                        <td align="right" class="hidbtn">' . $action . '</td>
                    </tr>';
                    }
                }
                else{
                    echo '<tr><td colspan="8">&nbsp;</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </fieldset>
    </div>

    <div style="height: 1%">&nbsp;</div>

    <div class="col-sm-12">
        <fieldset>
            <legend><?php echo $this->lang->line('common__pending_Loan');?><!--Loan--></legend>
            <table class="table table-bordered" id="loan" >
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('Loan_No');?> <!--Loan No--></th>
                    <th><?php echo $this->lang->line('common_employee_name');?> <!--Employee Name--></th>
                    <th><?php echo $this->lang->line('common_type');?> <!--Loan Type--></th>
                    <th><?php echo $this->lang->line('common_loan_amount');?> <!--Loan Amount--></th>
                    <th><?php echo $this->lang->line('common_date');?> <!--Date--></th>
                    <th><?php echo $this->lang->line('common_total_intallmanets');?> <!--Date--></th>
                    <th><?php echo $this->lang->line('common_pending_intallmanets');?> <!--Date--></th>
                    <th><?php echo $this->lang->line('common_confirmed');?> <!--Confirmed--></th>
                    <th><?php echo $this->lang->line('common_approved');?> <!--Approved--></th>
                    <th style="width:70px" class="hidbtn"> &nbsp; </th>
                </tr>
                </thead>
                <tbody>
                <?php
                //echo '<pre>'; print_r($accountDetails); echo '</pre>';
                if(!empty($loan)){
                    $empID = $this->input->post('empID');
                     $x=0;
                    foreach($loan as $row){
                        $x=$x+1;
                        $loanNo = $row['loanCode'];
                        $empName = $row['Ename2'];
                        $loanType = $row['loantype'];
                        $amount = $row['amount'];
                        $date = $row['loanDate'];
                        $confirmed = $row['confirmedYN'];
                        $approve = $row['approvedYN'];
                        $flag = ($approve == 1)? 'success' : 'danger';
                        $check = ($row['confirmedYN'] == 1)? 'success' : 'danger';

                        $action = '<a onclick="viewDetails('.$row['ID'].')" title="View Details" rel="tooltip">';
                        $action .= '<span class="glyphicon glyphicon-eye-open"></span></a>';

                        echo '<tr>
                                <td>'.$x.'</td>
                                <td>'.$row['loanCode'].'</td>
                                <td>'.$row['Ename2'].'</td>
                                <td>'.$row['loantype'].'</td>
                                <td>'.$amount.'</td>
                                <td>'.$date.'</td>
                                <td>'.$row['numberOfInstallment'].'</td>
                                <td>'.$row['settledInstallments'].'</td>
                                <td align="center"><span class="label label-'.$check.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></td>
                                <td align="center"><span class="label label-'.$flag.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></td>
                                  <td align="right" class="hidbtn"> '.$action.'</td>
                            </tr>';
                    }
                }
                else{
                    echo '<tr><td colspan="8" style="color:green"><center>No Record Found</center></td></tr>';
                }
                ?>
                </tbody>
            </table>
        </fieldset>
    </div>
</div>

<div class="modal fade" id="bankAccModal" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title bankAccModalTitle" id="myModalLabel"><?php echo $this->lang->line('emp_bank_employee_bank_setup');?><!--Employee Bank Setup--></h4>
            </div>
            <form class="form-horizontal" id="bankAcc_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12"><label id="empDetailsShow"></label></div>
                    </div>

                    <hr style="margin-top: 7px; margin-bottom: 7px">

                    <input type="hidden" name="empID" id="empID">
                    <input type="hidden" name="accountID" id="accountID">

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accHolder"><?php echo $this->lang->line('emp_bank_account_holder_name');?><!--Account Holder Name--></label>
                        <div class="col-sm-6">
                            <input type="text" name="accHolder" id="accHolder" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="bank_id"><?php echo $this->lang->line('emp_bank');?><!--Bank--></label>
                        <div class="col-sm-6">
                            <select name="bank_id" id="bank_id" class="form-control select2 bankSelect" onchange="get_bankBranches(this.value)">
                                <option></option>
                                <?php
                                $banks = all_banks_drop();
                                foreach ($banks as $bank) {
                                    echo '<option value="' . $bank->bankID . '">' . $bank->bankCode . ' | ' . $bank->bankName . ' | ' . $bank->bankSwiftCode . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="branch_id"><?php echo $this->lang->line('emp_bank_branch');?><!--Branch--></label>
                        <div class="col-sm-6">
                            <select name="branch_id" id="branch_id" class="form-control select2 bankAccSave_input branch_id">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accountNo"><?php echo $this->lang->line('emp_bank_account_no');?><!--Account No--></label>
                        <div class="col-sm-6">
                            <input type="text" name="accountNo" id="accountNo" class="form-control bankAccSave_input number"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="salPerc"><?php echo $this->lang->line('emp_bank_salary_transfer');?><!--Salary Transfer--> %</label>
                        <div class="col-sm-6">
                            <input type="text" name="salPerc" id="salPerc" class="form-control bankAccSave_input number" onkeyup="validatePer(this)" />
                        </div>
                    </div>

                    <div class="form-group payrollTypeContainer">
                        <label class="col-sm-4 control-label" for="accStatus"><?php echo $this->lang->line('emp_bank_payroll_type');?><!--Payroll Type--></label>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="payrollType[]" id="payroll" value="1">
                                    </span>
                                        <input type="text" class="form-control" disabled value="<?php echo $this->lang->line('emp_bank_payroll');?>" >
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="payrollType[]" id="nonPayroll" value="2">
                                    </span>
                                        <input type="text" class="form-control" disabled value="<?php echo $this->lang->line('emp_bank_non_payroll');?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="IBANnumber"><?php echo $this->lang->line('emp_IBANCode');?><!--IBAN No--></label>
                        <div class="col-sm-6">
                            <input type="text" name="ibanNumber" id="ibanNumber" class="form-control IBAN_input "/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="swiftcode"><?php echo $this->lang->line('emp_swiftcode');?><!--swiftcode--></label>
                        <div class="col-sm-6">
                            <input type="text" name="swiftcode" id="swiftcode" class="form-control swiftcode_input"/>
                        </div>
                    </div>

                    <div class="form-group accountStatusContainer">
                        <label class="col-sm-4 control-label" for="accStatus"><?php echo $this->lang->line('common_status');?> </label><!--Status-->
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="accStatus" id="accStatusAct" value="1">
                                    </span>
                                        <input type="text" class="form-control" disabled value="<?php echo $this->lang->line('common_active');?>" ><!--Active-->
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="accStatus" id="accStatusInAct" value="0">
                                    </span>
                                        <input type="text" class="form-control" disabled value="<?php echo $this->lang->line('common_in_active');?>"><!--In Active-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group payrollType-in-update-container">
                        <label class="col-sm-4 control-label" for="accStatus"><?php echo $this->lang->line('emp_bank_payroll_type');?> </label><!--Payroll Type-->
                        <div class="col-sm-6">
                            <input type="text" id="payrollType-in-update-text" class="form-control bankAccSave_input" readonly="" />
                            <input type="hidden" name="payrollType-in-update" id="payrollType-in-update" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="bnkDetSaveBtn" onclick="saveBankAcc()">
                        <?php echo $this->lang->line('emp_save');?><!-- Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="loanScheduleTab" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title loanTitle" id="myModalLabel"><?php echo $this->lang->line('common_loan_details');?><!--Employee Bank Setup--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5" style="margin-bottom: 1%">
                        <table class="table table-bordered table-striped table-condensed ">
                            <tbody><tr>
                                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('hrms_loan_settled');?><!--Settled--> </td>
                                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_pending');?><!--Pending--> </td>
                                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_skipped');?><!--Skipped--> </td>
                            </tr>
                            </tbody></table>
                    </div>
                </div>
                <table id="" class="<?php echo table_class(); ?> loanScheduleTB">
                    <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_deduction_date');?><!--Deduction Date--> </th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('hrms_loan_installment_no');?><!--Installment No--> </th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> &nbsp;&nbsp;<span class="dataTableCurrency"></span></th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        </tr>
                    </thead>
                    <tbody id="loanScheduleTableBody"></tbody>
                </table>
                <hr>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attachment -->
<div class="modal fade" id="attachementModel" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="width: 100%">
                    <div class="col-md-12">
                        <span class="pull-right">
                        <form id="attachment_form" class="form-inline" enctype="multipart/form-data" method="post">
                            <input type="hidden" class="form-control" id="documentSystemCode" name="documentSystemCode">
                            <input type="hidden" class="form-control" id="documentID" value="emp_Bank" name="documentID">
                            <input type="hidden" class="form-control" id="document_name" value="Employee Bank" name="document_name">
                            <div class="form-group">
                                <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                            </div>
                            <div class="form-group ">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename set-w-file-name"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                        data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="uplode_attachment()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                        </span>
                    </div>
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                        </thead>
                        <tbody id="attachment_pop" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var bankAcc_form = $('#bankAcc_form');
    var newEmpID = <?php echo $empID ?>;

    $('.select2').select2();

    function add_bnkAccount(){
        $('.bankAccModalTitle').html('<?php echo $this->lang->line('emp_bank_employee_bank_setup');?>');
        bankAcc_form[0].reset();
        bankAcc_form.attr('action', '<?php echo site_url('Employee/save_empBankAccounts') ?>');

        var empDisplayName = $.trim("<?php echo $empName; ?>");
        var empDisplayCode = $.trim('<?php echo $empDetail['ECode']; ?>');
        $('#empDetailsShow').html(empDisplayCode+'&nbsp; - &nbsp;'+empDisplayName);

        $('#accHolder').val(empDisplayName);
        $('#empID').val(newEmpID);
        $('#bank_id').val('').attr('onChange', '').change().attr('onChange', 'get_bankBranches(this.value)');
        $('#branch_id').empty().change();
        $('.accountStatusContainer, .payrollType-in-update-container').hide();
        $('.payrollTypeContainer').show();

        $('#bankAccModal').modal('show');
    }

    function viewDetails(loanID){
        $.ajax({
                type: 'post',
                url: '<?php echo site_url('Loan/load_LoanSchedule') ?>',
                data: {'loanID': loanID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    var tableBody = $('#loanScheduleTableBody');
                    tableBody.empty();

                    data.forEach((item, index) => {
                        var statusLabel = '';
                        if (item.isSetteled==1) {
                            statusLabel = '<span class="label label-success">Settled</span>';
                        } else if (item.skipedInstallmentID==0) {
                            statusLabel = '<span class="label label-danger">Pending</span>';
                        } else {
                            statusLabel = '<span class="label label-warning">Skipped</span>';
                        }
                        var row = `<tr>
                            <td>${index + 1}</td>
                            <td>${item.scheduleDate1}</td>
                            <td>${item.installmentNo}</td>
                            <td>${item.amount}</td>
                            <td>${statusLabel}</td>
                        </tr>`;
                        tableBody.append(row);
                    });
                    $('#loanScheduleTab').modal('show');
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
            });

    }


    function bankAccDetEdit(accountNo, holderName, accountID, bankID, branchID, percentage, status, payrollType, payrollTypeText,swiftcode,ibancode) {
        $('.bankAccModalTitle').html('<?php echo $this->lang->line('emp_bank_employee_bank_setup');?>');
        bankAcc_form[0].reset();
        bankAcc_form.attr('action', '<?php echo site_url('Employee/update_empBankAccounts') ?>');

        var empDisplayName = $.trim("<?php echo $empName; ?>");
        var empDisplayCode = $.trim('<?php echo $empDetail['ECode']; ?>');

        $('#empDetailsShow').html(empDisplayCode+'&nbsp; - &nbsp;'+empDisplayName);


        $('#empID').val(newEmpID);
        $('#accountID').val(accountID);
        $('#accHolder').val(holderName);
        $('#bank_id').val(bankID).attr('onChange', '').change().attr('onChange', 'get_bankBranches(this.value)');
        $('#accountNo').val(accountNo);
        $('#salPerc').val(percentage);
        $('#payrollType-in-update').val(payrollType);
        $('#payrollType-in-update-text').val(payrollTypeText);
        $('#swiftcode').val(swiftcode);
        $('#ibanNumber').val(ibancode);

        get_bankBranches(bankID, branchID);
        $('.accountStatusContainer, .payrollType-in-update-container').show();
        $('.payrollTypeContainer').hide();


        if( status == 0 ){
            $('#accStatusInAct').prop('checked', true);
        }else{
            $('#accStatusAct').prop('checked', true);
        }

        $('#bankAccModal').modal('show');
    }

    function get_bankBranches(bankID, branchID=null) {
        if(bankID != '') {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/bankBranches') ?>',
                data: {'bankID': bankID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    var isSelected = '';
                    var branch = $('#branch_id');
                    var thisBranchID = null;
                    branch.empty();

                    branch.append('<option value=""> </option>');
                    $.each(data, function (elm, val) {
                        if (branchID != null && $.trim(val['branchID']) == $.trim(branchID) && thisBranchID == null) {
                            thisBranchID = val['branchID'];
                        }
                        branch.append('<option value="' + val['branchID'] + '" ' + isSelected + '>' + val['branchCode'] + ' | ' + val['branchName'] +'</option>');
                    });
                    branch.val(thisBranchID).change();
                    branch.css('border-color', '#d2d6de');

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function saveBankAcc() {
        var postData = bankAcc_form.serialize();
        var url = bankAcc_form.attr('action');
        $.ajax({
            type: 'post',
            url: url,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if ( data[0] == 's') {
                    $('#bankAccModal').modal('hide');
                    setTimeout(function(){
                        fetch_accounts();
                    }, 300);

                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });

    }

    function delete_bankAccount(accountID, payrollType){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure ?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record ?*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('Employee/delete_empBankAccounts') ?>',
                    data: {'accountID': accountID, 'payrollType':payrollType},
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's' ){
                            setTimeout(function(){
                                fetch_accounts();
                            },300);
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                });
            }
        );
    }

    function validatePer(obj){
        var thisVal = ( $.isNumeric($.trim(obj.value)) ) ? parseFloat($.trim(obj.value)) : parseFloat(0);

        if( thisVal > 100 ){
            $(obj).val('');
        }
    }
    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.hidbtn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
    }

    function updatePrimaryBank(accountID, empID)
    {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_make_this_primary');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'accountID': accountID, 'empID': empID},
                    url: "<?php echo site_url('Employee/change_empPrimaryBankAccount'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's' ){
                            setTimeout(function(){
                                fetch_accounts();
                            },300);
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetchAttachments(accountID)
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
            dataType: 'json',
            data:  {'documentSystemCode': accountID, 'documentID': 'emp_Bank', 'confirmedYN': 0},
            success: function (data) {
                $('#attachment_pop').empty();
                $('#attachment_pop').append('' +data+ '');
                $("#attachementModel").modal({ backdrop: "static", keyboard: true });
                $('#documentSystemCode').val(accountID)       
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });
    }

    function uplode_attachment(){
        var salAccID=$('#documentSystemCode').val();
        var formData = new FormData($('#attachment_form')[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    fetchAttachments(salAccID);
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-30
 * Time: 12:11 PM
 */