<div class="fixHeader_Div" style="max-width: 100%; height: 390px; border: 1px solid #c0c0c0">
    <?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('employee_non_payroll_bank', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);

    ?>
    <table class="<?php echo table_class(); ?>" id="salary-account-table" style="margin-top: -3px">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
            <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
            <th><?php echo $this->lang->line('common_account_no'); ?><!--Account No--></th>
            <th><?php echo $this->lang->line('common_holder'); ?><!--Holder--></th>
            <th><?php echo $this->lang->line('common_bank'); ?><!--Bank--></th>
            <th><?php echo $this->lang->line('common_branch'); ?><!--Branch--></th>
            <th>%</th>
            <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="z-index: 10"></th>
        </tr>
        </thead>

        <tbody>
        <?php
        if (!empty($accountData)) {
            foreach ($accountData as $key => $row) {

                $empID = $row['EIdNo'];
                $empName = $row['empName'];
                $empCode = $row['ECode'];
                $accountNo = $row['accountNo'];
                $accountHolder = $row['accountHolderName'];
                $bankID = $row['bankID'];
                $branchID = $row['branchID'];
                $accountID = $row['accountID'];
                $percentage = $row['toBankPercentage'];
                if( $accountID == null || $accountID == '0' ){
                    $status = '';
                    $action = '<a onclick="addNewBank(' . $empID . ',\'' . $empName . '\',\'' . $empCode . '\')" title="Add" rel="tooltip">';
                    $action .= '<i class="fa fa-plus-square" aria-hidden="true"></i></a>';
                }
                else{
                    $status = ($row['isActive'] == 1) ? 'success' : 'danger';

                    $editFn = 'edit_bankAccount(' . $empID . ',\'' . $empName . '\',\'' . $empCode . '\',\'' . $accountNo . '\',\'' . $accountHolder . '\',';
                    $editFn .= '\''.$accountID.'\', \''.$bankID.'\', \''.$branchID.'\', \''.$percentage.'\', \''.$status.'\')';

                    $action = '<a onclick="addNewBank(' . $empID . ',\'' . $empName . '\',\'' . $empCode . '\')" title="Add" rel="tooltip">';
                    $action .= '<i class="fa fa-plus-square" aria-hidden="true"></i></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
                    $action .= '<a onclick="'.$editFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>';
                    $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_bankAccount('.$empID.', '.$accountID.')" title="Delete" rel="tooltip">';
                    $action .= '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                }



                echo '<tr>
                    <td>' . ($key + 1) . '</td>
                    <td>' . $empCode . '</td>
                    <td>' . $empName . '</td>
                    <td>' . $accountNo . '</td>
                    <td>' . $row['accountHolderName'] . '</td>
                    <td>' . $row['bankName'] . '</td>
                    <td>' . $row['branchName'] . '</td>
                    <td align="right">' . $percentage . '</td>
                    <td align="center"><span class="label label-' . $status . '" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></td>
                    <td align="right">' . $action . '</td>
                </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="bankAccModal" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title bankAccModalTitle" id="myModalLabel"><?php echo $this->lang->line('emp_non_payroll_emp_bank_setup'); ?><!--Employee Bank Setup--></h4>
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
                        <label class="col-sm-4 control-label" for="accHolder"><?php echo $this->lang->line('emp_non_payroll_acc_hol_name'); ?><!--Account Holder Name--></label>
                        <div class="col-sm-6">
                            <input type="text" name="accHolder" id="accHolder" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="bank_id"><?php echo $this->lang->line('common_bank'); ?><!--Bank--></label>
                        <div class="col-sm-6">
                            <select name="bank_id" id="bank_id" class="form-control select2 bankSelect" onchange="get_bankBranches(this.value)">
                                <option></option>
                                <?php
                                    $banks = all_banks_drop();
                                    foreach ($banks as $bank) {
                                        echo '<option value="' . $bank->bankID . '">' . $bank->bankCode . ' | ' . $bank->bankName. ' | ' . $bank->bankSwiftCode . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="branch_id"><?php echo $this->lang->line('common_branch'); ?><!--Branch--></label>
                        <div class="col-sm-6">
                            <select name="branch_id" id="branch_id" class="form-control select2 bankAccSave_input branch_id">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accountNo"><?php echo $this->lang->line('common_account_no'); ?><!--Account No--></label>
                        <div class="col-sm-6">
                            <input type="text" name="accountNo" id="accountNo" class="form-control bankAccSave_input number"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="salPerc"><?php echo $this->lang->line('emp_non_payroll_salry_trans'); ?><!--Salary Transfer--> %</label>
                        <div class="col-sm-6">
                            <input type="text" name="salPerc" id="salPerc" class="form-control bankAccSave_input number" />
                        </div>
                    </div>

                    <div class="form-group accountStatusContainer">
                        <label class="col-sm-4 control-label" for="accStatus"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="accStatus" id="accStatusAct" value="1">
                                    </span>
                                        <input type="text" class="form-control" disabled value="Active" >
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="accStatus" id="accStatusInAct" value="0">
                                    </span>
                                        <input type="text" class="form-control" disabled value="In Active">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="bnkDetSaveBtn" onclick="saveBankAcc()">
                        <?php echo $this->lang->line('common_save'); ?><!-- Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var bankAcc_form = $('#bankAcc_form');

    $(document).ready(function () {
        $('#salary-account-table').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 0
        });

        $("[rel=tooltip]").tooltip();
        $('.select2').select2();
    });

    function addNewBank(empID, empName, empCode){
        bankAcc_form[0].reset();
        bankAcc_form.attr('action', '<?php echo site_url('Employee/save_nonPayBankAccount') ?>');

        $('#empDetailsShow').html(empCode+' &nbsp; - &nbsp; '+empName);
        $('#accHolder').val(empName);
        $('#empID').val(empID);
        $('#bank_id').val('').attr('onChange', '').change().attr('onChange', 'get_bankBranches(this.value)');
        $('#branch_id').empty().change();
        $('.accountStatusContainer').hide();
        $('#bankAccModal').modal('show');
    }

    function edit_bankAccount(empID, empName, empCode, accountNo, holderName, accountID, bankID, branchID, percentage, status) {
        bankAcc_form[0].reset();
        bankAcc_form.attr('action', '<?php echo site_url('Employee/update_nonPayBankAccount') ?>');

        $('#empDetailsShow').html(empCode+' &nbsp; - &nbsp; '+empName);
        $('#empID').val(empID);
        $('#accountID').val(accountID);
        $('#accHolder').val(holderName);
        $('#bank_id').val(bankID).attr('onChange', '').change();
        $('#accountNo').val(accountNo);
        $('#salPerc').val(percentage);
        get_bankBranches(bankID, branchID);
        $('.accountStatusContainer').show();
        $('#bank_id').attr('onChange', 'get_bankBranches(this.value)');

        if( status == 'danger' ){
            $('#accStatusInAct').prop('checked', true);
        }else{
            $('#accStatusAct').prop('checked', true);
        }

        $('#bankAccModal').modal('show');
    }

    function saveBankAcc(){
        var url = bankAcc_form.attr('action');
        var postData = bankAcc_form.serializeArray();

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

                if( data[0] == 's' ){
                    $('#bankAccModal').modal('hide');

                    setTimeout(function(){
                        load_nonPayrollEmployees();
                    },300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function delete_bankAccount(empID, accountID){
        swal({
                title: "<?php echo $this->lang->line('emp_non_payroll_aler_msg_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('emp_non_payroll_aler_msg_you_want_delete_this'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>"
            },
            function () {
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('Employee/delete_nonPayBankAccount') ?>',
                    data: {'empID': empID, 'accountID': accountID},
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's' ){
                            setTimeout(function(){
                                load_nonPayrollEmployees();
                            },300);
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
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
                        branch.append('<option value="' + val['branchID'] + '" ' + isSelected + '>' + val['branchCode'] + ' | ' + val['branchName'] + '</option>');
                    });
                    branch.val(thisBranchID).change();
                    branch.css('border-color', '#d2d6de');

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        }
    }

    $(document).on('keypress', '.number',function (event) {
        if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>
<?php
