<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_split_salary');
echo head_page($title, false);

$page_id=trim($this->input->post('page_id'));
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<style>
    .datepicker {
        z-index: 0 !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<?php $CI=get_instance();
$convertFormat = convert_date_format_sql();
$companyID=current_companyID();
$data=$CI->db->query("SELECT splitSalaryMasterID, splitSalaryCode, noOfMonths, description, srp_erp_splitsalarymaster.currencyID,
                                DATE_FORMAT(srp_erp_splitsalarymaster.createdDateTime,' $convertFormat ') AS documentDate, 
                                DATE_FORMAT(startDate,' $convertFormat ') AS startDate, 
                                DATE_FORMAT(endDate,' $convertFormat ') AS endDate, CurrencyCode, CurrencyName
                FROM srp_erp_splitsalarymaster
                LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_splitsalarymaster.currencyID
                WHERE splitSalaryMasterID = {$page_id} AND CompanyID = {$companyID} ")->row_array();

$emp_array = employee_list_by_currency($data['currencyID']);
?>
<div class="row">
    <div class="col-sm-12" id="">
        <table style="" class=" table-condendsed">
            <tr>
                <td style=""><strong><?php echo $this->lang->line('common_document_code');?><!--Document Code--></strong></td>
                <td style="">: <?php echo $data['splitSalaryCode']?></td>
                <td style=""><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                <td style="">: <?php echo 'From ' . $data['startDate'] . ' To ' . $data['endDate'];?></td>
                <td style=""><strong><?php echo $this->lang->line('hrms_payroll_no_of_months');?><!--No of Months--></strong></td>
                <td style="">: <?php echo $data['noOfMonths']?></td>
            </tr>
            <tr>
                <td style=""><strong><?php echo $this->lang->line('common_created_date');?><!--Created Date--></strong></td>
                <td style="">: <?php echo $data['documentDate']?></td>
                <td style=""><strong><?php echo $this->lang->line('common_description');?><!--Description--></strong></td>
                <td style="">: <?php echo $data['description']?></td>
                <td style=""><strong><?php echo $this->lang->line('common_currency');?><!--Currency--></strong></td>
                <td style="">: <?php echo $data['CurrencyName'] . '(' . $data['CurrencyCode'] . ')';?></td>
            </tr>
        </table>
        <hr>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-5">&nbsp;</div>
        <div class="col-md-4 text-center">&nbsp;</div>
        <div class="col-md-2 text-right">
            <button type="button" class="btn btn-primary pull-right" onclick="add_splitSalary_details('<?php echo $page_id ?>');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_detail');?><!--Add Details--> </button>
        </div>
    </div>

    <hr>

    <div class="col-sm-12" id="">

        <div class="table-responsive">
            <table id="load_split_salary_details_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
                    <th><?php echo $this->lang->line('common_account_no');?><!--Account No--></th>
                    <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                    <th><?php echo $this->lang->line('hrms_payroll_gross_salary');?><!--Gross Salary--></th>
                    <th><?php echo $this->lang->line('common__monthly_deduction');?><!--Monthly Deduction--></th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
            </table>
    </div>
</div>
    <br>
    <hr>
    <br>
    <div class="row" style="margin-right: 10px">
        <div class="text-right m-t-xs">
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>

</div>

    <div aria-hidden="true" role="dialog" id="salary_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width: 95%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"><?php echo $this->lang->line('hrms_payroll_split_salary_details');?><!--Split Salary Detail--></h5>
                </div>
                <form role="form" id="salary_detail_form" class="form-horizontal" autocomplete="off">
                    <input type="text"  class="form-control hidden fetch_startdate" value="<?php echo $data['startDate']?>"/>
                    <input type="text" class="form-control hidden fetch_enddate" value="<?php echo $data['endDate']?>"/>
                    <div class="modal-body">
                        <div>
                            <div class="col-sm-6">
                                <table style="" class="table-condendsed">
                                    <tr>
                                        <td style="width: 40px"><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                                        <td style="width: 100px">: <?php echo 'From ' . $data['startDate'] . ' To ' . $data['endDate'];?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px"><strong><?php echo $this->lang->line('common_currency');?><!--No of Months--></strong></td>
                                        <td style="width: 100px">: <?php echo $data['CurrencyCode']?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br>
                        <table class="table table-bordered table-striped table-condesed" id="salary_add_table">
                            <thead>
                            <tr>
                                <th style="width: 20% !important;"><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
                                <th class="hidden"><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
                                <th class="hidden"><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
                                <th style=""><?php echo $this->lang->line('hrms_payroll_gross_salary');?><!--Gross Salary--></th>
                                <th style=""><?php echo $this->lang->line('common__monthly_deduction');?><!--Monthly Deduction--></th>
                                <th class="hidden"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                                <th style=""><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                                <th style=""><?php echo $this->lang->line('common_account_no');?><!--Account No--></th>
                                <th style=""><?php echo $this->lang->line('common_bank');?><!--Bank--></th>
                                <th style=""><?php echo $this->lang->line('common_branch');?><!--Branch--></th>
                                <th style="width: 5%;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()"><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('customerID[]', $emp_array, '', 'class="form-control customerID select2" id="customerID" onchange="fetch_emp_details(this)"'); ?>
                                </td>
                                <td class="hidden">
                                    <input type="text"  name="startdate[]" class="form-control startdate" value="<?php echo $data['startDate']?>" disabled/>
                                </td>
                                <td class="hidden">
                                    <input type="text"  name="enddate[]" class="form-control enddate" value="<?php echo $data['endDate']?>" disabled/>
                                </td>
                                <td>
                                    <input class="grossSalary number" style="text-align:right" id="grossSalary number" name="grossSalary[]" disabled>
                                </td>
                                <td>
                                    <input class="monthlyDeduction" style="text-align:right" id="monthlyDeduction number" name="monthlyDeduction[]" onkeyup="validateAmount(this)">
                                </td>
                                <td class="hidden">
                                    <input class="currency" id="currency" name="currency[]" value="<?php echo $data['CurrencyCode'];?>" disabled>
                                </td>
                                <td>
                                    <input type="text" name="comment[]" class="form-control comment" id="comment"/>
                                </td>
                                <td>
                                    <select name="accountNo[]" id="accountNo" class="form-control accountNo select2" onchange="fetchAccountDetails(this)">
                                        <option value="">Select Bank</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="bank" id="bank" name="bank[]" disabled/>
                                </td>
                                <td>
                                    <input type="text" name="branch[]" class="form-control branch" id="branch" disabled/>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center;display: block;"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close');?> </button><!--Close-->
                        <button class="btn btn-primary btn-adddetails pull-right" type="submit"> <?php echo $this->lang->line('common_save');?>  <!--Save-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="edit_salary_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width: 95%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"><?php echo $this->lang->line('hrms_payroll_edit_split_salary_details');?><!--Edit Split Salary Detail--></h5>
                </div>
                <form role="form" id="edit_salary_detail_form" class="form-horizontal" autocomplete="off">
                    <input type="text" class="form-control hidden edit_splitSalaryID" id="edit_splitSalaryID"/>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <table style="" class="table-condendsed">
                                    <tr>
                                        <td style="width: 40px"><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                                        <td style="width: 100px">: <?php echo 'From ' . $data['startDate'] . ' To ' . $data['endDate'];?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px"><strong><?php echo $this->lang->line('common_currency');?><!--No of Months--></strong></td>
                                        <td style="width: 100px">: <?php echo $data['CurrencyCode']?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br>
                        <table class="table table-bordered table-striped table-condesed" id="salary_edit_table">
                            <thead>
                            <tr>
                                <th style="width: 20% !important;"><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
                                <th class="hidden" style=""><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
                                <th class="hidden" style=""><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
                                <th style=""><?php echo $this->lang->line('hrms_payroll_gross_salary');?><!--Gross Salary--></th>
                                <th style=""><?php echo $this->lang->line('common__monthly_deduction');?><!--Monthly Deduction--></th>
                                <th class="hidden" style="width: 10% !important;"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                                <th style=""><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                                <th style=""><?php echo $this->lang->line('common_account_no');?><!--Account No--></th>
                                <th style=""><?php echo $this->lang->line('common_bank');?><!--Bank--></th>
                                <th style=""><?php echo $this->lang->line('common_branch');?><!--Branch--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('customerID', $emp_array, '', 'class="form-control select2 edit_customerID" id="edit_customerID" onchange="fetch_emp_details_edit(this.value)"'); ?>
                                </td>
                                <td class="hidden">
                                    <input type="text"  name="startdate" class="form-control edit_startdate" value="<?php echo $data['startDate']?>" disabled/>
                                </td>
                                <td class="hidden">
                                    <input type="text"  name="enddate" class="form-control edit_enddate" value="<?php echo $data['endDate']?>" disabled/>
                                </td>
                                <td>
                                    <input class="edit_grossSalary number" style="text-align:right" id="edit_grossSalary" name="grossSalary" disabled>
                                </td>
                                <td>
                                    <input class="edit_monthlyDeduction number" style="text-align:right" id="edit_monthlyDeduction" name="monthlyDeduction" onkeyup="validateAmount_edit()">
                                </td>
                                <td class="hidden">
                                    <input class="edit_currency" id="edit_currency" name="currency" value="<?php echo $data['CurrencyCode'];?>" disabled>
                                </td>
                                <td>
                                    <input type="text" name="comment" class="form-control edit_comment" id="edit_comment"/>
                                </td>
                                <td>
                                    <select name="accountNo" id="edit_accountNo" class="form-control edit_accountNo select2" onchange="fetchAccountDetails_edit(this.value)">
                                        <option value="">Select Bank</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="edit_bank" id="edit_bank" name="bank" disabled/>
                                </td>
                                <td>
                                    <input type="text" name="branch" class="form-control edit_branch" id="edit_branch" disabled/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="update_split_salary_details()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div aria-hidden="true" role="dialog" id="salaryLimiExceed_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width: 40%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Gross Salary Limit Exceed<!--Edit Split Salary Detail--></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                    </div>
                    <br>
                    <table class="table table-bordered table-striped table-condesed" id="salaryLimiExceed_tbl">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
                            <th><?php echo $this->lang->line('hrms_payroll_gross_salary');?><!--Gross Salary--></th>
                            <th><?php echo $this->lang->line('common_total') . ' ' . $this->lang->line('common__monthly_deduction');?><!--Total Monthly Deduction--></th>
                        </tr>
                        </thead>
                        <tbody id="salaryLimiExceed">
                        <tr>
                            <td colspan="3"><?php echo $this->lang->line('common_no_records_found');?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>



<script type="text/javascript">
    var otable;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/split_salary','','<?php echo $this->lang->line('hrms_payroll_split_salary'); ?>');
        });
        $('.select2').select2();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        number_validation();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        splitSalaryMasterID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        fetch_split_salary_details();

        $('#salary_detail_form').bootstrapValidator({
            live            : 'enabled',
            message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded        : [':disabled'],
            fields          : {},
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            $(".startdate").prop('disabled', false);
            $(".grossSalary").prop('disabled', false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name' : 'splitSalaryMasterID', 'value' : splitSalaryMasterID });
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Employee/save_split_salary_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $(".startdate").prop('disabled', true);
                    $(".grossSalary").prop('disabled', true);
                    if (data[0] == 's') {
                        otable.draw();
                        $('#salary_detail_modal').modal('hide');
                        $('#salary_detail_form')[0].reset();
                    }
                    $(".btn-adddetails").prop("disabled", false);
                },error : function(){
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function add_splitSalary_details(id){
        $('#salary_detail_form')[0].reset();
        $('#salary_detail_modal').modal({backdrop:"static"});
    }

    function fetch_split_salary_details() {
        otable = $('#load_split_salary_details_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/split_salary_details'); ?>",
            "aaSorting": [[3, 'asc']],
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
                {"mData": "splitSalaryID"},
                {"mData": "Ename2"},
                {"mData": "bank"},
                {"mData": "CurrencyCode"},
                {"mData": "grossSalary"},
                {"mData": "monthlyDeduction"},
                {"mData": "edit"}

            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "splitSalaryMasterID","value": splitSalaryMasterID});
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

    function add_more_item() {
        $('select.select2').select2('destroy');
        var appendData = $('#salary_add_table tbody tr:first').clone();
        appendData.find('.customerID').val('').change();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.number').val('0');
        appendData.find('.startdate').val($('.fetch_startdate').val());
        appendData.find('.enddate').val($('.fetch_enddate').val());
        appendData.find('.currency').val('<?php echo $data['CurrencyCode']?>');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#salary_add_table').append(appendData);
        var lenght = $('#salary_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function fetchAccountDetails(element) {
        var acc_id = $(element).closest('tr').find('.accountNo').val();
        if(acc_id){
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'acc_id':acc_id},
                url :"<?php echo site_url('Employee/fetch_emp_account_details_SS'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    if (data) {
                        $(element).closest('tr').find('.bank').val(data['bankName']);
                        $(element).closest('tr').find('.branch').val(data['branchName']);
                    }
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function fetch_emp_details(element) {
        var id = $(element).closest('tr').find('.customerID').val();
        if(id){
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'empID':id, 'currency': <?php echo $data['currencyID']?>},
                url :"<?php echo site_url('Employee/fetch_emp_details_SS'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    if (data) {
                        $(element).closest('tr').find('.grossSalary').val(data['grossSalary']);

                        $(element).closest('tr').find('.accountNo').empty();
                        var mySelect = $(element).closest('tr').find('.accountNo');
                        mySelect.append($('<option></option>').val('').html('Select Bank'));
                        $.each(data['accountDetails'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['id']).html(text['accountNo']));
                        });
                    }
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function fetch_emp_details_edit(id) {
        if(id){
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'empID':id, 'currency': <?php echo $data['currencyID']?>},
                url :"<?php echo site_url('Employee/fetch_emp_details_SS'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    if (data) {
                        $('.edit_grossSalary').val(data['grossSalary']);

                        $('.edit_accountNo').empty();
                        var mySelect = $('.edit_accountNo');
                        mySelect.append($('<option></option>').val('').html('Select Bank'));
                        $.each(data['accountDetails'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['id']).html(text['accountNo']));
                        });
                    }
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function fetchAccountDetails_edit(acc_id) {
        if(acc_id){
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'acc_id':acc_id},
                url :"<?php echo site_url('Employee/fetch_emp_account_details_SS'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    if (data) {
                        $('.edit_bank').val(data['bankName']);
                        $('.edit_branch').val(data['branchName']);
                    }
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function delete_split_salary_details(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'splitSalaryID':id},
                    url :"<?php echo site_url('Employee/delete_split_salary_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            otable.draw();
                        }
                    },error : function(){
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            });
    }

    function edit_split_salary_details(splitSalaryID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'splitSalaryID':splitSalaryID},
            url :"<?php echo site_url('Employee/fetch_split_salary_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                $('#edit_splitSalaryID').val(data['splitSalaryID']);
                $('#edit_customerID').val(data['empID']).change();
                // fetch_emp_details_edit(data['empID']);
                $('#edit_startdate').val(data['startFrom']);
                $('#edit_enddate').val(data['endDate']);
                $('#edit_grossSalary').val(data['grossSalary']);
                $('#edit_monthlyDeduction').val(data['monthlyDeduction']);
                $('#edit_currency').val(data['CurrencyCode']);
                $('#edit_comment').val(data['comments']);
                $("#edit_salary_detail_modal").modal('show');
                setTimeout(function(){
                    $('#edit_accountNo').val(data['employeeBankDetailsID']).change();
                    fetchAccountDetails_edit(data['employeeBankDetailsID']);
                },400);
                stopLoad();
            },error : function(){
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function update_split_salary_details() {
        $(".edit_grossSalary").prop('disabled', false);
        var data = $("#edit_salary_detail_form").serializeArray();
        data.push({'name': 'splitSalaryMasterID', 'value': splitSalaryMasterID});
        data.push({'name': 'splitSalaryID', 'value': $('#edit_splitSalaryID').val()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/edit_split_salary_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);
                $(".edit_grossSalary").prop('disabled', true);
                if (data[0] == 's') {
                    otable.draw();
                    $('#edit_salary_detail_modal').modal('hide');
                    $('#edit_salary_detail_form')[0].reset();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_draft() {
        if (splitSalaryMasterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/hrm/split_salary','','<?php echo $this->lang->line('hrms_payroll_split_salary'); ?>');
                });
        }
    }

    function confirmation() {
        if (splitSalaryMasterID) {
            swal({

                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'splitSalaryMasterID': splitSalaryMasterID},
                        url: "<?php echo site_url('Employee/split_salary_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data['exceed']) {
                                $('#salaryLimiExceed').empty();
                                if (jQuery.isEmptyObject(data)) {
                                    $('#salaryLimiExceed').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                                } else {
                                    $.each(data['exceed'], function (key, value) {
                                        var string = '<tr>';
                                        string += '<td>' + value['employee'] + '</td>';
                                        string += '<td style="text-align: right">' + value['grossSalary'] + '</td>';
                                        string += '<td style="text-align: right">' + value['monthlyDeduction'] + '</td>';
                                        string += '</tr>';

                                        $('#salaryLimiExceed').append(string);
                                    });
                                    $("#salaryLimiExceed_modal").modal({backdrop: "static"});
                                }
                            }else if (data) {
                                fetchPage('system/hrm/split_salary','','<?php echo $this->lang->line('hrms_payroll_split_salary'); ?>');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function validateAmount(element) {
        var grossSalary = $(element).closest('tr').find('.grossSalary').val();
        var monthlyDeduction = $(element).closest('tr').find('.monthlyDeduction').val();
        if(Number(grossSalary) < Number(monthlyDeduction))
        {
            myAlert('w', 'Decuction cannot be greater than Gross Salary!');
            $(element).closest('tr').find('.monthlyDeduction').val('');
        }
    }
    function validateAmount_edit() {
        var grossSalary = $('.edit_grossSalary').val();
        var monthlyDeduction = $('.edit_monthlyDeduction').val();
        if(Number(grossSalary) < Number(monthlyDeduction))
        {
            myAlert('w', 'Decuction cannot be greater than Gross Salary!');
            $('.edit_monthlyDeduction').val('');
        }
    }
</script>