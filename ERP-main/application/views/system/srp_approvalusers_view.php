<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_approval_setup');
echo head_page($title, true);

/*echo head_page('Approval Setup',true);*/
$employee_arr       = array(''=>'Select Employee');//all_employee_drop();
$segment_arr = fetch_segment();
$emp=travelapplicationemployee();
$expenseCategory=get_Expense_Claim_Category();
$group_arr          = all_group_drop();
$document_code_arr  = all_document_code_drop();
$employee_filter_arr = all_employees_drop(false);
$documents_drop_arr = all_document_code_drop(false);
$ApprovalforItemMaster= getPolicyValues('AIM', 'All');
$ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
$singleSource= getPolicyValues('SSPR', 'All');

if($ApprovalforItemMaster==NULL){
    $ApprovalforItemMaster=0;
}
if($ApprovalforSupplierMaster==NULL){
    $ApprovalforSupplierMaster=0;
}
if($ApprovalforItemMaster==1){
    $document_code_arr['INV'] = ('INV | Inventory');
    $documents_drop_arr['INV'] = ('INV | Inventory');
}
if($ApprovalforSupplierMaster==1){
    $document_code_arr['SUP'] = ('SUP | Supplier');
    $documents_drop_arr['SUP'] = ('SUP | Supplier');
}
$DocAotoAppYN = getPolicyValues('DAA', 'All');
$amountBasedApproval = getPolicyValues('ABA', 'All');
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('config_document_id');?><!--Document ID--></label><br>
            <?php echo form_dropdown('documentID[]', $documents_drop_arr, '', 'class="form-control" id="documentID_filter" onchange="approvaluser_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></label><br>
            <?php echo form_dropdown('employeeID[]', $employee_filter_arr, '', 'class="form-control" id="employeeID_filter" onchange="approvaluser_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active');?><!--Active--> </td>
                    <td><span class="label label-danger">&nbsp;</span>   <?php echo $this->lang->line('config_not_active');?><!--Not Active--> </td>
                </tr>
            </table>
    </div>
    <div class="col-md-5 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="approvel_user_model()"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('config_create_approval_user');?><!--Create Approval User--></button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="approvaluser_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 10px">#</th>
                <th style="width: 120px"><?php echo $this->lang->line('config_document_id');?><!--Document ID--></th>
                <th style=""><?php echo $this->lang->line('common_document');?><!--Document--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('config_level_no');?><!--Level No--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div aria-hidden="true" role="dialog" id="approvel_user_model" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('config_add_new_user_approval');?><!--Add New User Approval--></h5>
            </div>
            <form role="form" id="approvel_user_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" class="form-control" id="approvalUserID" name="approvalUserID">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_document');?><!--Document--></label>
                            <div class="col-sm-5">
                        <?php echo form_dropdown('documentid', $document_code_arr, '','class="form-control select2" id="documentid" onchange="fetch_emploee_using_group();add_as_checklist_level(this.value);personal_application_type(this.value);check_type_EC(this.value)" required'); ?>
                                <!-- <input type="text" class="form-control form1" id="" name="documentid"> -->
                            </div>
                        </div>

                         <!-- expenseClaimCategory -->
                         <div class="form-group hide" id="expenseClaimCategorydiv">
                            <input type="hidden" id="expenseCategorycheck" name="expenseCategorycheck">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_category');?></label>
                            <div class="col-sm-5" >
                                <?php echo form_dropdown('expenseClaimCategory', $expenseCategory, '','class="form-control select2" id="expenseClaimCategory" '); ?>
                            </div>
                        </div>


                        <!-- /** added : almansoori chnges for personal application */ -->
                        <div class="form-group hide" id="actionTypes">
                            <label class="col-sm-4 control-label">Type</label>
                            <div class="col-sm-5" style="padding-right:30px;">
                                <?php echo form_dropdown('type', array('' => 'Select Type'), '','class="form-control select2" id="type" onchange="" '); ?>
                            </div>
                        </div>
                        <!-- /** added for: almansoori changes on personal application */ -->
                        <div class="form-group hide" id="criteriafield">
                            <label class="col-sm-4 control-label">Criteria</label>
                            <div class="col-sm-5" style="padding-right:30px;">
                                <?php echo form_dropdown('criteria', array('' => 'Select Criteria'), '','class="form-control select2" id="criteria" onchange="" '); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('config_level_no');?><!--Level No--></label>
                            <div class="col-sm-5">
                                <?php echo form_dropdown('levelno',array('' => 'Select Level'), '','class="form-control select2" id="levelno" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_group');?><!--Group--></label>
                            <div class="col-sm-5">
                                <?php echo form_dropdown('userGroupID', $group_arr, '','class="form-control select2" id="userGroupID"  onchange="fetch_emploee_using_group()"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_employee');?><!--Employee--></label>
                            <div class="col-sm-5">
                                <?php echo form_dropdown('employeeid', $employee_arr, '','class="form-control select2" id="employeeid"'); ?>
                            </div>
                        </div>

                        <div class="form-group segment-div hide" id ="seg_div">
                            <label class="col-sm-4 control-label">Segment</label>
                            <div class="col-sm-5">
                               
                                <?php echo form_dropdown('segmentid', $segment_arr, '','class="form-control select2" id="segmentid"  onchange=""'); ?>
                                
                            </div>
                        </div>

                        <div class="form-group segment-div hide" id ="doc_cat_div">
                            <label class="col-sm-4 control-label">Category</label>
                            <div class="col-sm-5">

                            <select name="docCategoryType" class="form-control select2 select2-hidden-accessible" id="docCategoryType" >

                            </select>

                            </div>

                        </div>

                        <?php if($singleSource==1){ ?>
                        <div class="form-group criteria-div hide" id ="pr_single_source">
                            <label class="col-sm-4 control-label">Single Source<!--Check Criteria--></label>
                            <div class="col-sm-5">
                                <input type="checkbox" id="criteria_chk1" name="criteria_chk1">
                                <input type="hidden" name="pr_single_source_val" id="pr_single_source_val" value ="0">
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group criteria-div1 hide" id ="grv_inspection">
                            <label class="col-sm-4 control-label">Inspection<!--Check Criteria--></label>
                            <div class="col-sm-5">
                                <input type="checkbox" id="criteria_chk2" name="criteria_chk2">
                                <input type="hidden" name="grv_inspection_val" id="grv_inspection_val" value ="0">
                            </div>
                        </div>
                        <div class="transactionAmount form-group hide" id="transactionAmount_from">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_from');?> <?php echo $this->lang->line('common_amount') . '(' . $this->common_data['company_data']['company_default_currency'] . ')';?><!--To Amount--></label>
                            <div class="col-sm-5">
                                <input class="form-control" id="fromAmount" name="fromAmount">
                            </div>
                        </div>
                        <div class="transactionAmount form-group hide" id="transactionAmount_to">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_to');?> <?php echo $this->lang->line('common_amount') . '(' . $this->common_data['company_data']['company_default_currency'] . ')';?><!--To Amount--></label>
                            <div class="col-sm-5">
                                <input class="form-control" id="toAmount" name="toAmount">
                            </div>
                        </div>

                        <div class="form-group approvalChecklistYNID hide" id ="approvalChecklistYNID">
                            <label class="col-sm-4 control-label">Approval Checklist<!--Check Criteria--></label>
                            <div class="col-sm-5">
                                <input type="checkbox" id="approvalChecklistYN" name="approvalChecklistYN">
                                <input type="hidden" name="approvalChecklistYN_val" id="approvalChecklistYN_val" value ="0">
                            </div>
                        </div>

                        <div class="specialUser form-group hide" id="specialUser">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_special_user');?> <!--Special User--></label>
                            <div class="col-sm-2">
                                <input type="checkbox" id="specialUserCheck" name="specialUserCheck" value="1" onclick="checkSpecialUser(this)">
                                <input type="hidden" id="isSpecialUser" name="isSpecialUser">
                            </div>
                            <button type="button" id="btnSpecialUser" name="btnSpecialUser" class="btn btn-primary hide btnSpecialUser" onclick="showSpecialUserModal()">
                                <i class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_add_special_User') ?>
                            </button>
                        </div>

                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> <?php echo $this->lang->line('config_save_approva_user');?><!--Save Approval User--> </button>
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="special_User_Modal" class="modal fade" style="display:none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" onclick="closeSpecialUser()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('common_special_user');?><!--Add Specail User--></h5>
            </div>
            <form id="specialUserForm" class="horizontal" role="form">
                <div class="modal-body">
                    <input type="hidden" id="specialApproavalID" name="specialApproavalID">
                    <input type="hidden" id="empApprove" name="empApprove">

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> <?php echo $this->lang->line('common_Employeee');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('specialUseremp', $emp, '', 'class="form-control specialUseremp" required id="specialUseremp"'); ?>
                            </div>
                            <div class="form-group col-sm-2 d-flex align-items-end">
                                <button onclick="save_special_user()" class="btn btn-primary" type="button">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> 
                                    <?php echo $this->lang->line('common_save'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr style="margin-top: 15px; margin-bottom: 10px;">

                    <div class="row" style="margin: 20px;">
                        <table class="table table-bordered table-condensed no-color" id="specialUsertable">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('common_Employeee'); ?></th>
                                    <th><?php echo $this->lang->line('common_action'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="specialUsertbody">
                                <tr>
                                    <td>
                                    <?php echo $this->lang->line('common_no_records_found'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button onclick="closeSpecialUser()" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script type="text/javascript">
    var type_id = null;
    var criteria_id = null;
    var employee = null;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/srp_approvalusers_view','Test','Approval User');
        });

        $('.select2').select2();
        $('.specialUseremp').select2();

        $('#employeeID_filter').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.transactionAmount').addClass('hide');
        $('.specialUser').addClass('hide');
      

        $('#documentID_filter').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        approvaluser_table();
        $('#approvel_user_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //levelno: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('config_level_no_is_required');?>.'}}},/*Level No is required*/
                documentid: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_is_required');?>.'}}},/*Document is required*/
                //employeeid: {validators: {notEmpty: {message: '<?php // echo $this->lang->line('common_employee_is_required');?>.'}}},/*Employee is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            // var specialUserFormData = $("#specialUserForm").serializeArray();
            data.push({'name' : 'document', 'value' : $('#documentid option:selected').text()});
            data.push({'name' : 'employee', 'value' : $('#employeeid option:selected').text()});
            // data = data.concat(specialUserFormData);

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Approvel_user/save_approveluser'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data==true){
                        $("#approvel_user_model").modal("hide");
                        approvaluser_table();
                    }
                }, 
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        $("#documentid").change(function (){
            fetch_approval_level($(this).val());
        });
    });

    function approvaluser_table() {
        var Otable = $('#approvaluser_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Approvel_user/load_approvel_user'); ?>",
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
                {"mData": "approvalUserID"},
                {"mData": "documentID"},
                {"mData": "document"},
                {"mData": "employeeName"},
                {"mData": "levelNo"},
                {"mData": "action"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "documentID", "value": $("#documentID_filter").val()});
                aoData.push({"name": "employeeID", "value": $("#employeeID_filter").val()});
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

    function approvel_user_model(){
        $('#approvel_user_form')[0].reset();
        $(".transactionAmount").addClass("hide");
        $(".specialUser").addClass('hide');
        $('.btnSpecialUser').addClass('hide');
        $("#documentid").val(null).trigger("change");
        $("#levelno").val(null).trigger("change");
        $("#userGroupID").val(null).trigger("change");
        $('#approvel_user_form').bootstrapValidator('resetForm', true);
        $("#approvel_user_model").modal({backdrop: "static"});
        $('#approvalUserID').val('');
    }

    $('#approvel_user_model').on('hidden.bs.modal', function (e) {
        if ($(e.target).attr('id') === 'approvel_user_model') {
            $('#isSpecialUser').val(''); 
        }
    });



    function openapprovelusermodel(id){
        var mySelect = $('#employeeid');
        approvel_user_model();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id':id},
            url: "<?php echo site_url('Approvel_user/edit_approveluser'); ?>",
            success: function (data) {
                $('#approvalUserID').val(id);
                $('#approvalUserID').val(id);
                
		        if(data['documentID']=='PAA'){
                    criteria_id = data['criteriaID'];
                    type_id = data['typeID'];
                }

                employee = data['employeeID'];

                $('#documentid').val(data['documentID']).change();
                $('#userGroupID').val(data['groupID']).change();

                $('#toAmount').val(data['toAmount']);
                $('#fromAmount').val(data['fromAmount']);
                if (data['specificUser'] == 1) {
                    $('#specialUserCheck').prop('checked', true);
                    $('#isSpecialUser').val('1');
                    $('.btnSpecialUser').removeClass('hide');
                    // checkSpecialUser($('#specialUserCheck')[0]);
                    // setSpecialUser(id,data['employeeID']);
                    $('#specialApproavalID').val(id);
                    $('#empApprove').val(data['employeeID']);
                } else {
                    $('#specialUserCheck').prop('checked', false);
                    $('.btnSpecialUser').addClass('hide');
                    $('#isSpecialUser').val('');
                }
                

                if(data['checkListYN']==1){
                    $('#approvalChecklistYN').iCheck('check');
                    $('#approvalChecklistYN_val').val(1);
                }else{
                    $('#approvalChecklistYN_val').val(0);
                }
              
              if(data['documentID'] == 'GRV' || data['documentID'] == 'PRQ')
                {
                    if(data['criteriaID'] == 1){
                        $('#criteria_chk1').iCheck('check');
                        $('#pr_single_source_val').val(1);
                    } 
                    else if(data['criteriaID'] == 2){
                        $('#grv_inspection_val').val(2);
                        $('#criteria_chk2').iCheck('check');
                    }else{
                        $('#pr_single_source_val').val(0);
                        $('#grv_inspection_val').val(0);
                    }
                }

                if(data['documentID'] == 'SAR' || data['documentID'] == 'EC')
                {
                        mySelect.append($('<option ></option>').val(-1).html('Reporting Manager'));
                        mySelect.append($('<option></option>').val(-2).html('Head Of Department'));
                        mySelect.append($('<option></option>').val(-3).html('Top manager'));
                }


                 if(data['documentID']=='EC'){
                    if(data['typeID']){
                        $('#expenseClaimCategorydiv').removeClass('hide');
                        $('#expenseCategorycheck').val('EC');
                        $('#expenseClaimCategory').val(data['typeID']).change();
                    }
                    else{
                        $('#expenseClaimCategorydiv').addClass('hide');
                        $('#expenseCategorycheck').val('');
                        $('#expenseClaimCategory').val('').change();
                    }
                }

                setTimeout(function(){ $('#levelno').val(data['levelNo']).change(); $('#employeeid').val(data['employeeID']).change();}, 1000);

            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

            }
        });
    }

    function deleteapproveluser(id){
        swal({   title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                closeOnConfirm: true },

            function(){
                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {id:id},
                        url: "<?php echo site_url('Approvel_user/delete_approveluser'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data){
                                fetchPage('system/srp_approvalusers_view','Test','Approval User');
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    }

    function fetch_emploee_using_group(id){
        var id = $('#userGroupID').val();
        var documentid = $('#documentid').val();

        if(documentid == 'PRQ')
        {            
              $('#pr_single_source').removeClass('hide'); //show checkbox criteriaID
        } else{
              $('#pr_single_source').addClass('hide'); //show checkbox criteriaID
        }

        if(documentid == 'GRV')
        {            
            $('#grv_inspection').removeClass('hide'); //show checkbox criteriaID
        } else{
            $('#grv_inspection').addClass('hide'); //show checkbox criteriaID
        }

        if(documentid){
            getApprovalTypeID(documentid);
        }
        
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id':id},
            url: "<?php echo site_url('Approvel_user/fetch_emploee_using_group'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#employeeid').empty();
                // $('#segmentid').empty();
              
                var mySelect = $('#employeeid');
                var mySelect2 = $('#segmentid');
                mySelect.append($('<option></option>').val('').html('Select Employee'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['EIdNo']).html(text['ECode'] + ' | ' + text['Ename1']+ ' | ' + text['Ename2']));
                    });
                }
                if(documentid == 'PRQ')
                {
                    mySelect.append($('<option></option>').val(-1).html('Reporting Manager'));                   
                }

                if(documentid == 'PRQ' || documentid == 'SAR' || documentid == 'WFH' || documentid == 'PAA' || documentid == 'EC' || documentid == 'SAR'  )
                {
                    mySelect.append($('<option></option>').val(-1).html('Reporting Manager'));
                    mySelect.append($('<option></option>').val(-2).html('Head Of Department'));
                    mySelect.append($('<option></option>').val(-3).html('Top manager'));
                }
              
                if(documentid == 'ATT')
                {
                    mySelect.append($('<option></option>').val(-1).html('Reporting Manager/ HR Admin'));
                    mySelect.append($('<option></option>').val(-2).html('Head Of Department'));
                }
                // if(documentid == 'PO')
                // {
                //     $('.transactionAmount').removeClass('hide');    

                // } else{
                //     $('.transactionAmount').addClass('hide'); //show checkbox criteriaID
                //     $('.segment-div').addClass('hide');
                // }

                /*if(documentid == 'PO')
                {

                    $('.transactionAmount').removeClass('hide');

                    //alert(<?php //echo $amountBasedApproval?>//)
                    <?php /*if($amountBasedApproval==1)
                    {
                    ?>
                        $('.transactionAmount').removeClass('hide');
                    <?php } else { ?>
                        $('.transactionAmount').addClass('hide');
                <?php } */?>
                }*/

                setTimeout(() => {
                    $('#employeeid').val(employee).change();
                }, 1000);

            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

 function getApprovalTypeID(id){
        documentID = id;
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {'documentID':documentID},
            url: "<?php echo site_url('Approvel_user/getApprovalTypeID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['approvalType'] == '1'){
                    //No changes
                   
                    $('#seg_div').addClass('hide');
                    $('#transactionAmount_from').addClass('hide');
                    $('#transactionAmount_to').addClass('hide');
                    $('#doc_cat_div').addClass('hide');
                } else if(data['approvalType'] == '2'){
                    $('#transactionAmount_from').removeClass('hide');
                    $('#transactionAmount_to').removeClass('hide');  
                    $('#doc_cat_div').addClass('hide');
                    $('#seg_div').addClass('hide');     
                } else if(data['approvalType'] == '3'){
                    $('#seg_div').removeClass('hide');    
                    $('#transactionAmount_from').addClass('hide');
                    $('#transactionAmount_to').addClass('hide');
                    $('#doc_cat_div').addClass('hide');
                } else if(data['approvalType'] == '4'){ //approval type 4
                    $('#seg_div').removeClass('hide');    
                    $('#transactionAmount_from').removeClass('hide');
                    $('#transactionAmount_to').removeClass('hide');
                    $('#doc_cat_div').addClass('hide');
                }else if(data['approvalType'] == '5'){ //approval type 5
                    $('#seg_div').addClass('hide');    
                    $('#transactionAmount_from').removeClass('hide');
                    $('#transactionAmount_to').removeClass('hide');

                    fetch_document_category_drop(documentID);
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
        
    }

    function fetch_document_category_drop(code){

        if(code == 'PO' || code == 'PRQ')
        {            
            $('#doc_cat_div').removeClass('hide');
        } else{
            $('#doc_cat_div').addClass('hide');
        }


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'documentID':code},
            url: "<?php echo site_url('Approvel_user/all_types_drop'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#docCategoryType').empty();
                // $('#segmentid').empty();

                var mySelect = $('#docCategoryType');
                //var mySelect2 = $('#segmentid');
                mySelect.append($('<option></option>').val('').html('Select Category'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['documentCategoryID']).html(text['categoryDescription']));
                    });
                }

            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function fetch_approval_level(documentID){
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {'documentID':documentID},
            url: "<?php echo site_url('Approvel_user/fetch_approval_level'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#levelno').empty();
                var mySelect = $('#levelno');
                mySelect.append($('<option></option>').val('').html('Select Level'));
                if (!jQuery.isEmptyObject(data)) {
                   for (i = 1; i <= data.approvalLevel; i++) {
                        mySelect.append($('<option></option>').val(i).html("Level - "+i));
                    }
                    <?php if($DocAotoAppYN==1){
                    ?>
                    mySelect.append($('<option></option>').val(0).html('No Approval'));
                    <?php } ?>

                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function clear_all_filters(){
        $('#documentID_filter').multiselect2('deselectAll', false);
        $('#documentID_filter').multiselect2('updateButtonText');

        $('#employeeID_filter').multiselect2('deselectAll', false);
        $('#employeeID_filter').multiselect2('updateButtonText');

        approvaluser_table();
    }

    $('#criteria_chk1').on('change', function(){
        if($('#criteria_chk1').is(":checked")){
            $('#pr_single_source_val').val(1);
        }else {
            $('#pr_single_source_val').val(0);
        }

    });

    $('#criteria_chk2').on('change', function(){
        if($('#criteria_chk2').is(":checked")){
            $('#grv_inspection_val').val(2);
        }else {
            $('#grv_inspection_val').val(0);
        }

    });

    $('#approvalChecklistYN').on('change', function(){
        if($('#approvalChecklistYN').is(":checked")){
            $('#approvalChecklistYN_val').val(1);
        }else {
            $('#approvalChecklistYN_val').val(0);
        }

    });

    function add_as_checklist_level(documentID){

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {'documentID':documentID},
            url: "<?php echo site_url('Approvel_user/getApprovalDocumentDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data){
                    if(data['approvalChecklistYN'] == 1){
                        $('#approvalChecklistYNID').removeClass('hide');

                    }else{
                        $('#approvalChecklistYNID').addClass('hide');
                    }
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    /** added : almansoori chnges for personal application */
    function all_types_drop(documentID){
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {'documentID':documentID},
            url: "<?php echo site_url('Approvel_user/all_types_drop'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#type').empty();
                var mySelect = $('#type');
                mySelect.append($('<option></option>').val('').html('Select Type'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['documentCategoryID']).html(text['categoryDescription']));
                    });
                }

                setTimeout(() => {
                    $('#type').val(type_id).change();
                    $('#criteria').val(criteria_id).change();
               }, 500);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    /** added : almansoori chnges for personal application */
    function criteria_drop(){
        $('#criteria').empty();
        var mySelect = $('#criteria');
        mySelect.append($('<option></option>').val('').html('Select criteria'));
        mySelect.append($('<option></option>').val(1).html('Less than 1 year'));

        //$('#criteria').val(criteria_id).change();
    }

    /** added : almansoori chnges for personal application */
    function personal_application_type(doc_id)
    {
        if(doc_id == 'PAA')
        {   all_types_drop(doc_id);
            $('#actionTypes').removeClass('hide');
            criteria_drop();
            $('#criteriafield').removeClass('hide');
        } 
        else if(doc_id == 'TRQ'){
            all_types_drop(doc_id);
            $('#actionTypes').removeClass('hide');
        }
        else{
            $('#actionTypes').addClass('hide'); 
            $('#criteriafield').addClass('hide');
        }
    }

    function check_type_EC(id){

        if(id==='EC'){
            $.ajax({
                url:"<?php echo site_Url('Approvel_user/get_company_approval_type') ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    var approval = JSON.parse(data);
                    
                    if (approval.length > 0) {
                        if (approval[0]['approvalType'] == 5) {
                            $('#expenseClaimCategorydiv').removeClass('hide');
                            $('#expenseCategorycheck').val('EC');
                        }

                        if (approval[0]['specificUserYN'] == 1) {
                            $(".specialUser").removeClass('hide');
                        } else {
                            $(".specialUser").addClass('hide');
                        }
                    }
                },
                error: function () {
                    myAlert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
                
            });
        }
        else{
            $('#expenseClaimCategorydiv').addClass('hide');
            $('#expenseCategorycheck').val('');
            $('#expenseClaimCategory').val('').change();
            $(".specialUser").addClass('hide');
        }
    }

    function checkSpecialUser(checkBox) {
        var isSpecialUser = $('#isSpecialUser').val();
        if (isSpecialUser == 1) {
            if (checkBox.checked) {
                $('.btnSpecialUser').removeClass('hide');
            } else {
                $('.btnSpecialUser').addClass('hide');
            }
        }
    }

    function showSpecialUserModal(){
        $("#special_User_Modal").modal({backdrop: "static"});   
        getSpecialUser();
    }

    function closeSpecialUser(){
        $('#special_User_Modal').modal('hide');
    }

    function save_special_user(){
        var specialUserFormData = $("#specialUserForm").serializeArray();
        $.ajax({
            async: true,
                type: 'post',
                dataType: 'json',
                data: specialUserFormData,
                url: "<?php echo site_url('Approvel_user/save_special_user'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data==true){
                        getSpecialUser();
                    }
                    
                }, 
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    refreshNotifications(true);
                }
        });
    }

    function getSpecialUser(){
        var empID=$('#empApprove').val();
        var appraovalID=$('#specialApproavalID').val();
        $.ajax({
            url:'<?php echo site_url('Approvel_user/getSpecialUser') ?>',
            data:{'approvalID':appraovalID,'empID':empID},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#specialUsertbody').empty();

                if (data.length === 0) {
                    $('#specialUsertbody').append('<tr><td colspan="2"><?php echo $this->lang->line('common_no_records_found'); ?></td></tr>');
                } else {
                    $.each(data, function(index, item) {
                        var row = '<tr>' +
                            '<td>' + item.Ename2 + '</td>' +
                            '<td><center><a href="#" onclick="deleteSpecialUser(' + item.id + '); return false;">' +
                            '<i class="fa fa-trash" style="color:rgb(209, 91, 71);"></i></a></center></td>' +
                            '</tr>';
                        $('#specialUsertbody').append(row);
                    });
                }
            },
            error: function () {
                myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function deleteSpecialUser(id){
        swal({   title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                closeOnConfirm: true 
            },
            function(){
                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {id:id},
                        url: "<?php echo site_url('Approvel_user/delete_specialUser'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data){
                                getSpecialUser();
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    }

</script>