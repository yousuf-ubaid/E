<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_salary_provision_configuration');
echo head_page($title, FALSE);

//$gl_code2 =fetch_provision_Gl2();
$gl_code =fetch_provision_and_expense_Gl();
$salaryCat = salary_categories_for_salaryProvision();

$provision_record = get_provisioned_record();

?>

<style>
    legend{
        font-size: 16px !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">

<div class="row">
    <div class="col-md-12">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <fieldset class="scheduler-border" style="margin-top: 10px">
            <legend class="scheduler-border"><?php echo $this->lang->line('hrms_leave_management_gl_setup'); ?><!--GL Setup--></legend>
                <div style="margin-top: 10px">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo form_open('', 'role="form" id="provision_gl_form"'); ?>
                                <div class="row" style="margin-top: 5px; margin-right: 5px;">
                                <label for="provision_gl" class="col-sm-4 control-label"><?php echo "Provision GL" ?><!--Provision GL--></label>
                                    <div class="form-group col-sm-8">
                                        <?php echo form_dropdown('provision_gl', $gl_code, isset($provision_record['GlAutoID']) ? $provision_record['GlAutoID'] : '', 'class="form-control select2" id="provision_gl"'); ?>  
                                    </div> 
                                </div>

                                <div class="row" style="margin-top: 5px; margin-right: 5px;">
                                <label for="expenseGl" class="col-sm-4 control-label"><?php echo "Expense GL" ?><!--Expense GL--></label>
                                    <div class="form-group col-sm-8">
                                        <?php echo form_dropdown('expenseGl', $gl_code, isset($provision_record['expenseGLAutoID']) ? $provision_record['expenseGLAutoID'] : '', 'class="form-control select2" id="expenseGl"'); ?>  
                                    </div> 
                                </div>
                            
                                <div class="row" style="margin-top: 5px; margin-right: 5px;">
                                    <label for="salary_provision_months" class="col-sm-4 control-label"><?php echo "Salary Provision Months" ?><!--Salary provision months--></label>
                                    <div class="form-group col-sm-8">
                                        <input class="form-control" type="text" style="padding-left: 20px;" value="<?php echo isset($provision_record['salaryProvisionMonths']) ? $provision_record['salaryProvisionMonths'] : '' ?>" name="salary_provision_months" id="salary_provision_months" placeholder="<?php echo "add salary provision months" ?>">
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 5px; margin-right: 5px;">
                                    <label for="eligible_after_months" class="col-sm-4 control-label"><?php echo "Eligible After Months" ?><!--Eligible after months--></label>
                                    <div class="form-group col-sm-8">
                                        <input class="form-control" type="text" style="padding-left: 20px;" name="eligible_after_months" id="eligible_after_months" value="<?php echo isset($provision_record['eligibleAfterMonths']) ? $provision_record['eligibleAfterMonths'] : '' ?>" placeholder="<?php echo "add eligible after months" ?>">
                                    </div>
                                </div>
                                <br>
                                <button type="button" id="update_btn" class="btn btn-primary btn-sm pull-right" style="margin-bottom: 10px" onclick="save_gl_setup()"><?php echo 'Save';?></button>
                            </form>   
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php echo $this->lang->line('hrms_leave_management_salary_categories'); ?><!--Salary Categories--></legend>
                <div class="">
                    <div class="clearfix visible-sm visible-xs">&nbsp;</div>
                    
                    <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-bottom: 10px" onclick="add_Salary_provision()"><?php echo 'ADD';?></button>
                    <br>
                    <div class="table-responsive" style="margin-top: 40px">
                        <table id="empBankTB" class="<?php echo table_class(); ?>">
                            <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 5%">Salary Category</th>
                                    <!--<th style="min-width: 10%">GL Code</th>-->
                                    <th style="min-width: 5%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </fieldset>
        </div>
    </div>
</div>



<div class="modal fade" id="SalaryProvisionConfigModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_leave_management_add_salary_category'); ?><!--Add Salary Category--></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="salary_provision_config_form"'); ?>
                    
                    <!-- Salary category -->
                    <div class="row" style="margin-top: 10px;">
                        <label for="salarycategoryid" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_salary_category'); ?><!--salary category--></label>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('salarycategoryid', $salaryCat, '', 'class="form-control select2" id="salarycategoryid"'); ?>
                        </div>
                    </div>


            </div>
            </form>

            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="add_salary_categories()"  id="save_btn"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>




<script>
    $(document).ready(function(){
        
        $('.select2').select2();

        $('#empBankTB').DataTable();
        
        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave_salary_provision_configuration');
        });
        fetch_salary_provision_submission()
    });


//save GL Setup
    function save_gl_setup(){
        var postData = $('#provision_gl_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_gl_setup') ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if( data[0] == 's'){ 
                    fetchPage('system/hrm/leave_salary_provision_configuration');
                };
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }




// Add salary category
    function add_salary_categories(){
        var postData = $('#salary_provision_config_form').serializeArray();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/add_salary_categories') ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if( data[0] == 's'){ 
                    fetch_salary_provision_submission() 
                };
                $('#SalaryProvisionConfigModal').modal('hide');
                fetchPage('system/hrm/leave_salary_provision_configuration');
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }


// Table
    function fetch_salary_provision_submission() {
       // var selectedValue = $('#glcode1').val();

        empBankTbl = $('#empBankTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_leave_salary_provision_configuration'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "Id"},
                {"mData": "SLDes"},
                //{"mData": "GLSCode"},
                {"mData": "action"},
            ],
            "columnDefs": [ {
                "targets": [0,2],
                "orderable": false
            }, {"searchable": false, "targets": [0]} ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ name: 'selectedValue', value: selectedValue });
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

// Delete
function delete_salary_provision_config(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_salaryProvision_config'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ fetch_salary_provision_submission() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

// add model
    function add_Salary_provision(){
        $("#SalaryProvisionConfigModal").modal({backdrop: "static"}); // static | true | false
        $('#salarycategoryid').val(null).trigger("change");
        $('#glcode2').val(null).trigger("change"); 
    }

</script>

