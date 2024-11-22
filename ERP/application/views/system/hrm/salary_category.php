<!--Translation added by Naseek-->

<?php
$expenseGL = expenseGL_drop();
$liabilityGL = liabilityGL_drop();
$defaultTypes = defaultPayrollCategories_drop();
?>
<style type="text/css">
    fieldset{
        border: 1px solid silver;
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 0px;
        margin:auto;
    }
    legend{
        width:auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }
</style>

<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_salary_category');
echo head_page($title, false);



?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <!--<button type="button" class="btn btn-primary btn-flat pull-right" onclick="new_cat()" >+</button>-->
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_cat()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>

    </div>
</div><hr>
<div class="table-responsive">
    <table id="categoryTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 3%">#</th>
            <th style="width: 20%"><?php echo $this->lang->line('hrms_payroll_category_description');?><!--Category Description--></th>
            <th style="width: 8%"><?php echo $this->lang->line('hrms_payroll_transaction_type');?><!--Transaction Type--></th>
            <th style="width: 10%"><?php echo $this->lang->line('hrms_payroll_default_dategory');?></th>
            <th style="width: 25%"><?php echo $this->lang->line('hrms_payroll_gl_description');?><!--GL Description--></th>
            <th style="width: 10%"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></th>
            <th style="width: 10%"><?php echo $this->lang->line('hrms_payroll_is_basic');?></th>
            <th style="width: 10%"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="newCatModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salary-cat-title"><?php echo $this->lang->line('hrms_payroll_new_salary_gategory');?><!--New Salary Category--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="newCat_form" autocomplete="off"' ); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                                <div class="col-sm-8"><input type="text" name="description"  id="description" class="form-control"></div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="defaultTypes"><?php echo $this->lang->line('hrms_payroll_default_dategory');?><!--Default Category --></label>
                                <div class="col-sm-8">
                                    <?php /*echo form_dropdown('defaultTypes', $defaultTypes, '', 'class="form-control select2" id="defaultTypes"'); */?>
                                    <select name="defaultTypes" id="defaultTypes" class="form-control select2" onchange="change_glCode_requirement(this)">
                                        <option></option>
                                        <?php
                                        foreach($defaultTypes as $key=>$row){
                                            echo '<option value="'.$row['id'].'" data-is-gl-required="'.$row['isGLCodeRequired'].'"> '.$row['description'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="category"><?php echo $this->lang->line('hrms_payroll_transaction_type');?><!--Transaction Type--> <?php required_mark(); ?></label>
                                <input type="hidden" name="h_category" id="h_category">
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="form-control">
                                        <option value="A"><?php echo $this->lang->line('hrms_payroll_addition');?><!--Addition--></option>
                                        <option value="D"><?php echo $this->lang->line('hrms_payroll_deduction');?><!--Deduction--></option>
                                        <!--<option value="D">Deduction with contribution</option>-->
                                    </select>
                                </div>
                            </div>

                            <div class="form-group gl-code-div">
                                <label class="control-label col-sm-4" for="glCode" ><span id="glTypeSpan"><?php echo $this->lang->line('hrms_payroll_expense');?><!--Expense--></span> <?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></label>
                                <div class="col-sm-8">
                                    <select name="glCode" id="glCode" class="form-control select2"></select>
                                    <!--Option load with glDrop_make()-->
                                </div>
                            </div>

                            <div class="form-group deduct-setup-div" style="display: none">
                                <label class="control-label col-sm-4" for="percentage"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employee--> % <?php required_mark(); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="percentage"  id="percentage" class="form-control number perCls">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="isPayrollCategory"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> <?php required_mark(); ?></label>
                                    <div class="col-sm-8">
                                    <input type="hidden" name="isPayrollCategory_hidden" id="isPayrollCategory_hidden" value="">
                                    <select name="isPayrollCategory" id="isPayrollCategory" class="form-control">
                                        <option value="1"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                        <option value="2"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="is_returned"><?php echo $this->lang->line('hrms_payroll_is_basic');?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" name="is_basic" id="is_basic" value="1"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row deduct-setup-div" style="display: none">
                        <!--<div class="col-md-12">
                            <h4>Employer Contribution Details</h4>
                            <hr style="margin-top: 2px">
                        </div>-->

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="glCode-company"><?php echo $this->lang->line('hrms_payroll_expense_gl_code');?><!--Expense GL Code--></label>
                                <div class="col-sm-8">
                                    <select name="glCode-company" id="glCode-company" class="form-control select2">
                                        <option></option>
                                        <?php
                                        foreach($expenseGL as $key=>$row){
                                            echo '<option value="'.$row['GLAutoID'].'" > '.$row['GLSecondaryCode'].' | '.$row['GLDescription'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="percentage-company"><?php echo $this->lang->line('common_company');?><!--Company--> %</label>
                                <div class="col-sm-8">
                                    <input type="text" name="percentage-company"  id="percentage-company" class="form-control number perCls">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="catEditID"  id="catEditID" />
                    <input type="hidden" name="url"  id="url" />
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
    var modal_title = $('#salary-cat-title');
    var modalBtn = $('.modalBtn');
    var catEditID = $('#catEditID');
    var catDrop = $('#category');
    var glTypeSpan = $('#glTypeSpan');

    $('#is_basic').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/salary_category','Test','HRMS');
        });
        $('.select2').select2();
        categoryTB();

        newCat_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                category: {validators: {notEmpty: {message: 'Category is required.'}}},
                /*glCode: {validators: {notEmpty: {message: 'GL code is required.'}}},*/
                /*percentage: {
                    validators: {
                        callback: {
                            message: 'Percentage is required',
                            callback: function(value) {
                                //var category = $('#category').val();
                                var percentage = $.trim($('#percentage').val());

                                if( value == 'D' && percentage == '' ){
                                   return false;
                                }else {
                                    return true;
                                }
                            }
                        }
                    }
                },*/
            },
        }).on('success.form.bv', function (e) {
            $('.modalBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');

            var requestUrl = $.trim( newCat_form.attr('action') );
            var postData = $('#newCat_form').serialize();

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
                        $('#newCatModal').modal('hide');
                        categoryTB($('#catEditID').val());
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });

            return false;
        });

    });

    function categoryTB(selectedRowID=null){
        $('#categoryTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/fetch_salaryCategory'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['salaryCategoryID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')

            },
            "columnDefs": [
                { "targets": [0,7], "orderable": false }, {"searchable": false, "targets": [0,6]}
            ],
            "aoColumns": [
                {"mData": "salaryCategoryID"},
                {"mData": "salaryDescription"},
                {"mData": "salaryCategoryType"},
                {"mData": "sys_description"},
                {"mData": "glData"},
                {"mData": "isPayrollCategoryStr"},
                {"mData": "is_basic_str"},
                {"mData": "edit"}
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

    function new_cat(){
        catDrop.prop('disabled', false);
        modal_title.text('<?php echo $this->lang->line('hrms_payroll_new_salary_gategory');?>');/*New Salary Category*/
        modalBtn.hide();
        $('.deduct-setup-div').fadeOut();

        $('#saveBtn').show();
        newCat_form.attr('action', '<?php echo site_url('Salary_category/saveCategory'); ?>');
        newCat_form[0].reset();
        $('#glCode').change();
        $('#glCode-company').change();
        $('#defaultTypes').change().prop('disabled', false);

        newCat_form.bootstrapValidator('resetForm', true);
        glTypeSpan.text('<?php echo $this->lang->line('hrms_payroll_expense');?>');/*Expense*/
        catEditID.val('');
        $('#isPayrollCategory').val(1).prop('disabled', false);
        $('#is_basic').iCheck('update');
        $('#newCatModal').modal({backdrop: "static"});
    }

    function editCat(id, des, type, per, glCode, comPercentage, comGLCode, payrollCatID, isPayrollCategory, is_basic_val){
        modal_title.text('<?php echo $this->lang->line('hrms_edit_salary_catergory')?>');/*Edit Salary Category*/
        catDrop.prop('disabled', true);
        catEditID.val('');
        newCat_form.attr('action', '<?php echo site_url('Salary_category/editCategory'); ?>');
        newCat_form[0].reset();
        $('#glCode').change();
        $('#glCode-company').change();
        $('#defaultTypes').change();

        newCat_form.bootstrapValidator('resetForm', true);

        modalBtn.hide();
        $('#updateBtn').show();

        $('#category').val( $.trim(type) );
        $('#h_category').val( $.trim(type) );

        /*if( type == 'D' ){
            $('.deduct-setup-div').fadeIn();
        }
        else {
            $('.deduct-setup-div').fadeOut();
        }*/

        catDrop.change();

        $('#description').val( $.trim(des) );

        $('#percentage').val(  $.trim(per) );
        $('#glCode').val(  $.trim(glCode) ).change();
        $('#glCode-company').val(  $.trim(comGLCode) ).change();
        $('#defaultTypes').val(  $.trim(payrollCatID) ).change().prop('disabled', true);
        $('#percentage-company').val(  $.trim(comPercentage) );
        $('#catEditID').val(  $.trim(id) );

        $('#isPayrollCategory').val(  $.trim(isPayrollCategory)).attr('disabled', 'disabled');
        $('#isPayrollCategory_hidden').val($.trim(isPayrollCategory));

        $('#is_basic').iCheck('update');
        is_basic_val = (is_basic_val == 1)? 'check': 'uncheck';
        $('#is_basic').iCheck(is_basic_val);

        $('#newCatModal').modal({backdrop: "static"});

    }

    function delete_cat(id, description){
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
                    url :"<?php echo site_url('Salary_category/delete_salCat'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'catID':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        //refreshNotifications(true);
                        if( data[0] == 's'){ categoryTB(); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    catDrop.change(function(){
        glDrop_make($(this).val());

        $('#percentage').val('');

    });

    function glDrop_make(catType,selectedID=null){
        /*** Addition and Deduction without contributions => Expense GL ***/
        /*** Deduction => Liability GL ***/
        var declarationCombo = (catType == 'D')?  JSON.stringify(<?php echo json_encode($liabilityGL) ?>) : JSON.stringify(<?php echo json_encode($expenseGL) ?>);

        var row = JSON.parse(declarationCombo);
        var h_glCode = '';
        var glCodeDrop = $('#glCode');
        glCodeDrop.empty();


        var drop = '<option value=""></option>';

        $.each(row, function(i, obj){
            var selected = ( selectedID == obj.GLAutoID )? 'selected' : '';
            if( selectedID == obj.GLAutoID ){  h_glCode = obj.GLAutoID; }

            drop += '<option value="'+obj.GLAutoID+'" '+selected+' >'+obj.GLSecondaryCode+' | '+obj.GLDescription+'</option>';
        });

        glCodeDrop.append(drop);
        glCodeDrop.fadeOut();
        glCodeDrop.fadeIn();

        //newCat_form.bootstrapValidator('resetField', 'glCode');

    }

    $('.perCls').keyup(function(){
        var per =  parseFloat( $.trim($(this).val()) );
        if( per < 0 || per > 100 ){
            $(this).val('');
            myAlert('e', 'Percentage must be 0-100');
        }
        //newCat_form.bootstrapValidator('revalidateField', 'percentage');
    });

    function change_glCode_requirement(obj){
        var isGLRequired = $(obj).find(':selected').attr('data-is-gl-required');
        var gl_code_div = $('.gl-code-div');

        if( isGLRequired == 'N' ){
            gl_code_div.hide();
        }
        else{
            gl_code_div.show();
        }
    }

    $('.number').keypress(function (event) {
         if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
             event.preventDefault();
         }
     });

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSk
 * Date: 5/11/2016
 * Time: 3:56 PM
 */