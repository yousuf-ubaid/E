<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_loan_loan_categories');
echo head_page($title  , false);



$loanGL = loanCatGL_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_loanCat()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="categoryTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 20px !important;">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('hrms_loan_loan_description');?><!--Loan Description--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_type');?><!--Type--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_percentage');?><!--Percentage--></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_is_salary_advance');?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_gl_description');?><!--GL Description--></th>
            <th style="min-width: 7%"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="newCatModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_loan_new_loan_category');?><!--New Loan Category--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="loanCat_form"'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="description"  id="description" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="intType"><?php echo $this->lang->line('common_type');?><!--Type--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="intType" id="intType" class="form-control">
                                <option></option>
                                <option value="0"><?php echo $this->lang->line('hrms_loan_new_interest_free');?><!--Interest Free--></option>
                                <option value="1"><?php echo $this->lang->line('hrms_loan_new_interest_base');?><!--Interest Based--></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group percentageDiv" style="display: none">
                        <label class="col-sm-4 control-label" for="percentage"><?php echo $this->lang->line('common_percentage');?><!--Percentage--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="percentage"  id="percentage" class="form-control number perCls">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="glCode"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="glCode" id="glCode" class="form-control select2">
                                <option></option>
                                <?php //systemAccountCode,GLSecondaryCode,GLDescription,
                                foreach($loanGL as $key=>$row){
                                    echo '<option value="'.$row['GLAutoID'].'" > '.$row['GLSecondaryCode'].' | '.$row['GLDescription'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group " style="">
                        <label class="col-sm-4 control-label" for="is_salary_advance"><?php echo $this->lang->line('common_is_salary_advance');?></label>
                        <div class="col-sm-6" style="padding-top: 8px;">
                            <input type="checkbox" name="is_salary_advance"  id="is_salary_advance" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="editID" name="editID">
                    <input type="hidden" name="url" id="url" />
                    <button type="submit" class="btn btn-primary btn-sm modalBtn" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="submit" class="btn btn-primary btn-sm modalBtn" id="loan_updateBtn" style="display: none"><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<script>
    var loanCat_form = $('#loanCat_form');
    var modalBtn = $('.modalBtn');
    var perDiv = $('.percentageDiv');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/loan/loan_category','Test','HRMS');
        });
        $('.select2').select2();

        loadLoanCat();

        loanCat_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                category: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_category_is_required');?>.'}}},/*Category is required*/
                glCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gl_code_is_required');?>.'}}},/*GL code is required*/
                /*percentage: {
                    validators: {
                        callback: {
                            message: 'Percentage is required',
                            callback: function() {
                                var intType = $('#intType').val();
                                var percentage = $.trim($('#percentage').val());

                                if( intType == 1 && percentage == '' ){ return false; }
                                else{ return true; }
                            }
                        }
                    }
                },*/
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');

            var requestUrl = $.trim($('#url').val());
            saveLoanCat(requestUrl);


        });

        /*loanCat_form.bootstrapValidator({ destroy: 'true' });
        loanCat_form.bootstrapValidator({

            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                category: {validators: {notEmpty: {message: 'Category is required.'}}},
                glCode: {validators: {notEmpty: {message: 'GL code is required.'}}},
                intType: {validators: {notEmpty: {message: 'Type is required.'}}},
                percentage: {
                    validators: {
                        callback: {
                            message: 'Percentage is required',
                            callback: function() {
                                var intType = $('#intType').val();
                                var percentage = $.trim($('#percentage').val());

                                if( intType == 1 && percentage == '' ){
                                    return false;
                                }
                                else{
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('.submitBtn').prop('disabled', false);
            var requestUrl = $.trim($('#url').val());
            saveLoanCat(requestUrl);
        });*/
    });

    function loadLoanCat(selectedRowID=null){
        var Otable = $('#categoryTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/loadLoanCat'); ?>",
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


                    if( parseInt(oSettings.aoData[x]._aData['loanID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "loanID"},
                {"mData": "description"},
                {"mData": "isInterestBased"},
                {"mData": "per"},
                {"mData": "isSalaryAdvance_str"},
                {"mData": "GLSecondaryCode"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,3,7]}],
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

    function new_loanCat(){
        perDiv.hide();
        $('#glCode').val('').change();
        loanCat_form[0].reset();
        loanCat_form.bootstrapValidator('resetForm', true);
        $('.modalBtn').hide();
        $('#saveBtn').show();

        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_loan_new_loan_category');?>');
        $('#newCatModal').modal({backdrop: "static"});
    }

    $('#intType').change(function(){
        if( $(this).val() == '1'){
            perDiv.fadeIn();
        }else{
            perDiv.fadeOut();
        }

        $('#percentage').val('');
        //loanCat_form.bootstrapValidator('revalidateField', 'percentage');
    });

    function saveLoanCat(requestUrl){

        var postData = $('#loanCat_form').serialize();

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
                    loadLoanCat( $('#editID').val() );
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function editCat( editID, des, isIntBase, intPer, glCode, isSalaryAdvance ){
        perDiv.hide();
        loanCat_form[0].reset();
        loanCat_form.bootstrapValidator('resetForm', true);

        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_loan_edit_loan_category');?>');
        $('#description').val(des);
        $('#editID').val(editID);
        $('#intType').val(isIntBase);
        $('#percentage').val(intPer);
        $('#is_salary_advance').prop('checked', (isSalaryAdvance == 1));

        $('#glCode').val(glCode).change();
        $('.modalBtn').hide();
        $('#loan_updateBtn').show();


        if( isIntBase == 1){
            perDiv.show();
        }

        loanCat_form.bootstrapValidator('resetField', 'glCode');

        $('#newCatModal').modal({backdrop: "static"});
    }

    function delete_cat(delID, des){
        swal(
            {

                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('loan/delete_loanCat'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'catID':delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        //refreshNotifications(true);
                        if( data[0] == 's'){ loadLoanCat() };
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                        //swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }
        );
    }

    modalBtn.click(function(){
        var thisID = $(this).attr('id');
        var url = '';

        if( thisID == 'saveBtn' ){
            url = '<?php echo site_url('Loan/saveLoanCategory'); ?>';
        }
        else if(thisID == 'loan_updateBtn' ){
            url = '<?php echo site_url('Loan/updateLoanCategory'); ?>';
        }
        $('#url').val( url );
    });

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
 * User: NSK
 * Date: 5/19/2016
 * Time: 4:16 PM
 */