

<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_monthly_addition_deduction_type');
echo head_page($title, false);

$department =  floors_drop(1,1,1);
$expenseGL = monthly_additionDeductionGL_drop();

$locationArr = $department; //array('1'=>'location 1','2'=>'location 2','3'=>'location 3');

?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_cat()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="monthlyDeclarationTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="min-width: 8%"><?php echo $this->lang->line('hrms_payroll_transaction_type');?><!--Transaction Type--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_payroll_gl_description');?><!--GL Description--></th>
                <th style="width: 12%"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></th>
                <th style="min-width: 7%"></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="declarationModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="title-declarationModal"></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="newCat_form"'); ?>
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="description"  id="description" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="isPayrollCategory"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <select name="isPayrollCategory" id="isPayrollCategory" class="form-control select2" onchange="getSubCategory()">
                            <option value="1"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                            <option value="2"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="category"><?php echo $this->lang->line('hrms_payroll_transaction_type');?><!--Transaction Type --><?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <select name="category" id="category" class="form-control select2" onchange="change_Category(this.value)">
                            <option value=""><?php echo $this->lang->line('common_select_type');?><!--Select Type--></option>
                            <option value="A"><?php echo $this->lang->line('hrms_payroll_addition');?><!--Addition--></option>
                            <option value="D"><?php echo $this->lang->line('hrms_payroll_deduction');?><!--Deduction--></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_payroll_link_to_fixed_allowance');?><!--Link to Fixed Allowance--></label>

                    <div class="col-sm-6">
                        <?php echo form_dropdown('salarySubCatID', '', '', 'class="form-control" id="salarySubCatID" onchange="LoadFixedAllowanceGLCode(this)"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="glCode"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <select name="glCode" id="glCode" class="form-control select2">
                            <option></option>
                            <?php
                            foreach($expenseGL as $key=>$row){
                                echo '<option value="'.$row['GLAutoID'].'" > '.$row['GLSecondaryCode'].' | '.$row['GLDescription'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="isVariable"><?php echo $this->lang->line('common_is_variable');?><!--Description--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="checkbox" name="isVariable"  id="isVariable" value="1" onchange="changeIsVariable(this)">
                    </div>
                </div>

                <div class="form-group hide" id="dev_linkType"> 
                    <label class="col-sm-4 control-label" for="linkType"><?php echo $this->lang->line('hrms_payroll_link_type');?><!--Transaction Type --><?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <select name="linkType" id="linkType" class="form-control select2" onchange="change_linkType(this)">
                            <option value=""><?php echo $this->lang->line('common_select_type');?><!--Select Type--></option>
                            <option value="1" selected><?php echo $this->lang->line('hrms_payroll_link_type_not_link');?><!--Addition--></option>
                            <option value="2"><?php echo $this->lang->line('hrms_payroll_link_type_link_with_leaves');?><!--Deduction--></option>
                            <option value="3"><?php echo $this->lang->line('hrms_payroll_link_type_link_without_leaves');?><!--Deduction--></option>
                        </select>
                    </div>
                </div>

                <div class="form-group hide" id="dev_calType"> 
                    <label class="col-sm-4 control-label" for="calType"><?php echo $this->lang->line('hrms_payroll_cal_type');?><!--Transaction Type --><?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <select name="calType" id="calType" class="form-control select2">
                            <option value="1" selected><?php echo $this->lang->line('hrms_payroll_cal_hours');?><!--Addition--></option>
                            <option value="2"><?php echo $this->lang->line('hrms_payroll_cal_days');?><!--Deduction--></option>
                        </select>
                    </div>
                </div>

                <div class="form-group hide" id="dev_location"> 
                    <label class="col-sm-4 control-label" for="locationArr"><?php echo $this->lang->line('hrms_payroll_location');?><!--Transaction Type --><?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('empLocation[]', $locationArr, '', 'class="form-control empLocation" id="empLocation" multiple="multiple"'); ?>
                    </div>
                </div>

                <div class="form-group hide" id="dev_employeeClaimYN">
                    <label class="col-sm-4 control-label" for="employeeClaimYN">ESR<!--is for ESR--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="checkbox" name="employeeClaimYN"  id="employeeClaimYN" value="1">
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <input type="hidden" name="catEditID"  id="catEditID" />
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="updateBtn" style="display: none"><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>



<script type="text/javascript">
    var newCat_form = $('#newCat_form');
    var modalBtn = $('.modalBtn');
    var catEditID = $('#catEditID');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/monthly_add_deduction_type','Test','HRMS');
        });
        $('.select2').select2();
        number_validation();
        monthlyDeclarationTB();

        $('.empLocation').select2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 0
        });

        newCat_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                category: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}},/*Type is required*/
                /*salarySubCatID: {validators: {notEmpty: {message: 'Fixed Allowance is required.'}}},*/
                glCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gl_code_is_required');?>.'}}}/*GL code is required*/
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            $("#glCode").prop("disabled", false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');

            var requestUrl = $.trim( $form.attr('action') );
            catSave(requestUrl);
        });


        $('#locationArr').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
    });

    function monthlyDeclarationTB(selectedRowID=null){
        var Otable = $('#monthlyDeclarationTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/fetch_monthlyDeclarationSalaryCategory'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['monthlyDeclarationID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')

            },
            "aoColumns": [
                {"mData": "monthlyDeclarationID"},
                {"mData": "monthlyDeclaration"},
                {"mData": "monthlyDeclarationType"},
                {"mData": "GLSecondaryCode"},
                {"mData": "GLDescription"},
                {"mData": "isPayrollCategoryStr"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function changeIsVariable(ev){
        var select_status = $(ev).is(":checked");

        if(select_status === true){
            $('#dev_linkType').removeClass('hide');
            $('#dev_employeeClaimYN').addClass('hide');
        }else{
            $('#dev_linkType').addClass('hide');
            $('#dev_employeeClaimYN').removeClass('hide');
        }   
    }

    function change_linkType(ev){
        var linkType = $(ev).val();

        if(linkType == 2 || linkType == 3){
            $('#dev_calType').removeClass('hide');
            $('#dev_location').removeClass('hide');
            $('#dev_location').removeClass('hide');
        }else{
            $('#dev_calType').addClass('hide');
            $('#dev_location').addClass('hide');
            $('#dev_location').addClass('hide');
        }   

    }

    function change_Category(value){
        if(value == 'A'){
            $('#dev_employeeClaimYN').removeClass('hide');
        }else{
            $('#dev_employeeClaimYN').addClass('hide');
        }   
    }

    function new_cat(){
        $('#title-declarationModal').text('<?php echo $this->lang->line('hrms_payroll_new_monthly_declaration');?>');/*New Monthly Declaration*/
        modalBtn.hide();
        $('#saveBtn').show();
        newCat_form[0].reset();
        $('#category').val('').change().prop("disabled", false);
        $('#glCode').change().prop("disabled", false);
        $('#isPayrollCategory').change().prop("disabled", false);

        $('#dev_linkType').addClass('hide');
        $('#dev_calType').addClass('hide');
        $('#dev_location').addClass('hide');
        $('#empLocation').val('').change();
        $('#employeeClaimYN').val('').change();

        newCat_form.bootstrapValidator('resetForm', true);
        newCat_form.attr('action', '<?php echo site_url('Salary_category/saveMonthlyDeclarationCategory'); ?>');
        catEditID.val('');
        $('#declarationModal').modal({backdrop: "static"});
    }

    function catSave(requestUrl){
        var postData = $('#newCat_form').serializeArray();
        // Remove the existing `employeeClaimYN` if it has already serialized
        postData = postData.filter(function(item) {
            return item.name !== 'employeeClaimYN';
        });
        var employeeClaimYN = $('#employeeClaimYN').is(":checked") ? 1 : 0;
        postData.push({ "name": "employeeClaimYN","value": employeeClaimYN});
        $.ajax({
            type: 'post',
            url: requestUrl,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#declarationModal').modal('hide');
                    monthlyDeclarationTB( $('#catEditID').val() );
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }

    function editCat(id, des, type, per, glCode,CategoryID, isPayrollYN, isVariable, linkType, calType, location, employeeClaimYN){
        $('#title-declarationModal').text('<?php echo $this->lang->line('hrms_payroll_edit_monthly_declaration');?>');/*Edit Monthly Declaration*/
        catEditID.val('');
        newCat_form[0].reset();
        $('#glCode').change();

        modalBtn.hide();
        $('#updateBtn').show();

        newCat_form.bootstrapValidator('resetForm', true);
        newCat_form.attr('action', '<?php echo site_url('Salary_category/editMonthlyDeclarationCategory'); ?>');

        $('#description').val( $.trim(des) );
        $('#category').val( $.trim(type) ).change().prop('disabled', true);
        //$('#isPayrollCategory').val(isPayrollYN).change().prop("disabled", true);
        $('#isPayrollCategory').removeAttr('onchange').val(isPayrollYN).change().prop("disabled", true);
        $('#catEditID').val(  $.trim(id) );

        if(isVariable == 1){
            $('#isVariable').prop('checked',true);
        }

        var location_arr = location.split(',');
  
        $('#isVariable').change();
        $('#linkType').val(linkType).change();
        $('#calType').val(calType).change();
        $('#empLocation').val(location_arr).trigger('change');

        if(employeeClaimYN == 1){
            $('#employeeClaimYN').prop('checked',true);
        }

        getSubCategory(CategoryID);
        setTimeout(function () {
            //$('#salarySubCatID').val( $.trim(CategoryID) );
            $('#glCode').val(glCode).change().prop("disabled", true);

        }, 500);

        $('#declarationModal').modal({backdrop: "static"});

    }

    function delete_cat(id, description, declarationType){
        swal(
            {
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
                    url :"<?php echo site_url('Salary_category/delete_monthlyDeclarationSalCat'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'declarationID':id, 'declarationType':declarationType},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ monthlyDeclarationTB() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');

                    }
                });
            }
        );
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function getSubCategory(categoryID=null) {
        $("#glCode").val(null).trigger("change");
        var masterCategory = 'A';
        var isPayrollCategoryYN = $('#isPayrollCategory').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/getSalarySubType'); ?>",
            data: {'masterCategory': masterCategory, 'isPayrollCategoryYN' : isPayrollCategoryYN, 'isFromMA-D':'Y'},
            dataType: "html",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if( categoryID != null ){
                    $('#salarySubCatID').html(data).val( $.trim(categoryID));
                    $('#isPayrollCategory').attr('onChange', 'getSubCategory()');
                }else{
                    $('#salarySubCatID').html(data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', errorThrown);
            }
        });
    }

    function LoadFixedAllowanceGLCode(id) {
        $("#glCode").prop("disabled", false);
        var masterID = id.value;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/Load_GLCode_for_fixed_allowance') ?>",
            data: {masterID: masterID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['GLCode'] != ''){
                    $('#glCode').val(data['GLCode']).change().prop("disabled", true);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
        return false;
    }
</script>