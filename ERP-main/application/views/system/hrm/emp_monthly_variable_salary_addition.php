<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_employee_monthly_addition_variable');
echo head_page($title, false);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_decPlace = $this->common_data['company_data']['company_default_decimal'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();

$segment_arr = fetch_segment(true, false);
$currency_arr = all_currency_new_drop(false);
$masterType_drop = system_salary_cat_drop('VPG', 1);
$pGroups_drop = payroll_group_drop();
$monthlyAdditionVaribale = get_monthlyaddition_deduction_varibles('MA');

?>

<style type="text/css">
    .trInputs{
        width: 100%;
        padding: 2px 2px;
        height: 20px;
    }
    .empCurrencySelect{
        padding-top: 3px ;
        padding-bottom: 3px ;
        width: auto;
    }

    .dateFields {
        z-index: 100 !important;
    }

    .empAddBtn{
        font-size: 10px;
        padding: 3px 10px;
    }

    .required-class{ border: 1px solid #e20b1f;}

    .remove-required-class{ border: 1px solid #a9a9a9;}

    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single{
        height: 22px;
        padding: 0px 5px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{ height: 18px !important;}

    .hideTr{ display: none }

    .oddTR td{ background: #f9f9f9 !important; }

    .evenTR td{ background: #ffffff !important; }

    .select-container .btn-group{
        width: 150px !important;
    }

    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500;
        padding: 0px 4px !important;
    }
</style>

<?php echo form_open('', 'id="emp_monthlyAddFrm" autocomplete="off"') ?>
<div class="col-sm-12" style="margin-bottom: 10px;">
    <fieldset>
        <legend> <?php echo $this->lang->line('common_header');?> </legend>
        <div class="form-group col-sm-2">
            <label for="documentCode" class="control-label"><?php echo $this->lang->line('hrms_payroll_employee_document_code');?><!--Document Code--></label>
            <input type="text" name="documentCode" class="form-control"  id="documentCode" disabled>
        </div>

        <div class="form-group col-sm-2" id="payroll-grp-div" style="display: none">
            <label class="control-label" for="payroll_grp"><?php echo $this->lang->line('common_payroll_group');?></label>
            <?=form_dropdown('payroll_grp', $pGroups_drop, null, 'class="form-control" id="payroll_grp" disabled')?>
        </div>

        <div class="form-group col-sm-2">
            <label class="control-label" for="payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></label>
            <select name="payrollType" id="payrollType" class="form-control" disabled>
                <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label class="control-label" for="systemType"><?php echo $this->lang->line('common_type');?></label>
            <select name="systemType" class="form-control" id="systemType" disabled>
                <option value="0" selected>Monthly Addition</option>
                <option value="1" selected>Variable Addition</option>
                <?php
                $option = '';
                if(!empty($masterType_drop)){
                    foreach($masterType_drop as $key=>$val){
                        $option .= "<option value='{$key}' >{$val}</option>";
                    }
                }
                echo $option;
                ?>
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label for="desDate" class="control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="dateDesc" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="desDate"
                       class="form-control" required readonly>
            </div>
        </div>

        <div class="form-group col-sm-4" id="head-des-dive">
            <label for="description" class="control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
            <input type="text" name="monthDescription" class="form-control" id="description" placeholder="">
        </div>
        <div class="form-group col-sm-4" id="attandancePullDiv">
            <label for="description" class="control-label"><?php echo 'Pull From'?><!--Description--></label>
            <input type="text" name="pullType" class="form-control" id="pullType" value="" disabled>
        </div>
    </fieldset>
</div>


<div id="detail-table-container"> </div>

<div class="row"> <!--modal-footer-->
    <hr>
    <div class="col-sm-6 pull-left"> &nbsp; </div>

    <div class="col-sm-3 pull-right">
        <input type="hidden" id="rowCount" value="0">
        <input type="hidden" id="isConform" name="isConform" value="0">
        <input type="hidden" id="updateID" name="updateID" value="">
        <input type="hidden" id="updateCode" name="updateCode" value="">

        <button type="button" class="btn btn-success btn-sm saveBtn submitBtn pull-right" data-value="1" style=""><?php echo $this->lang->line('common_save_and_confirm');?><!--Save & Confirm--></button>
        <button type="button" class="btn btn-primary btn-sm saveBtn submitBtn pull-right" data-value="0" style="margin-right: 5px;"><?php echo $this->lang->line('common_save_as_draft');?><!--Save As Draft--></button>
    </div>
</div>




<?php echo form_close();?>

<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999"  >
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_payroll_employee');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <input type="hidden" id="isEmpLoad" value="0" >
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="segment"> <?php echo $this->lang->line('common_segment');?><!--Segment--> </label>
                                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID"  multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="currency">  <?php echo $this->lang->line('common_currency');?><!--Currency--> </label>
                                <?php echo form_dropdown('currency[]', $currency_arr, '', 'class="form-control" id="currencyID" multiple="multiple"'); ?>
                            </div>
                         
                            <div class="form-group col-sm-4 col-xs-4 pull-right">
                                <label for="currency" class="visible-sm visible-xs">&nbsp;</label>
                                <button class="btn btn-primary btn-sm pull-right" id="selectAllBtn" style="font-size:12px;" onclick="selectAllRows()">
                                    <?php echo $this->lang->line('hrms_payroll_select_all');?><!--Select All-->
                                </button>
                                <button type="button" onclick="openEmployeeModal(1)" class="btn btn-primary btn-sm pull-right" style="margin-right:10px">
                                    <?php echo $this->lang->line('common_load');?><!--Load-->
                                </button>
                            </div>
                        </div>

                        <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 5%">#</th>
                                            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP Code--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                                            <th style="width: 5%"><div id="dataTableBtn"></div></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="row">
                            <div class="table-responsive">
                                <!-- <div class="col-sm-2 col-xs-6 col-centered">
                                    <label><?php // echo $this->lang->line('common_category');?></label>
                                    <div class="input-group">
                                        <select name="monthlyTypes[]" class="form-control" id="monthlyTypes" multiple="multiple">
                                            <?php
                                            // $option = '';
                                            // if(!empty($monthlyAdditionVaribale)){
                                            //     foreach($monthlyAdditionVaribale as $salCast){
                                            //         $option .= "<option value='{$salCast['monthlyDeclarationID']}' >{$salCast['monthlyDeclaration']}</option>";
                                            //     }
                                            // }
                                            // echo $option;
                                            ?>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;" onclick="addAllRows()">
                                        <?php echo $this->lang->line('hrms_payroll_add_all');?><!--Add All-->
                                    </button>
                                    <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;" onclick="clearAllRows()">
                                        <?php echo $this->lang->line('common_clear_all');?><!--Clear All-->
                                    </button>
                                </div>
                                <hr style="margin-top: 7%">
                                <form id="tempTB_form">
                                <input type="hidden" name="masterID" id="masterID"/>
                                <input type="hidden" name="type_m" value="MA"/>
                                <table class="<?php echo table_class(); ?>" id="tempTB">
                                    <thead>
                                        <tr>
                                            <th style="max-width: 5%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP CODE--></th>
                                            <th style="max-width: 95%"><?php echo $this->lang->line('hrms_payroll_emp_name');?><!--EMP NAME--></th>
                                            <th><div id="removeBtnDiv"> </div></th>
                                        </tr>
                                    </thead>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    var isVariablePay = 0;
    var empTB = $('#empTB');
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({ "bPaginate": false });
    var declarationCombo = '<?php echo json_encode(declaration_drop('A', $this->input->post('data_arr')) ); ?>';
    var empTempory_arr = [];

    $('#currencyID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#monthlyTypes').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });



    var documentID = '<?=$this->input->post('page_id');?>';
    var is_period_base_process = false;
    var payroll_group = 0;

    $( document ).ready(function() {

        let masterPage = 'system/hrm/monthly_salary_addition';


        if( documentID != 0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data : { 'editID': documentID},
                url: "<?php echo site_url('Employee/edit_monthAddition'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#isConform').val(data['confirmedYN']);
                    $("#documentCode").val(data['monthlyAdditionsCode']);
                    $("#updateCode").val(data['monthlyAdditionsCode']);
                    $('#updateID').val( documentID );
                    $('#masterID').val( documentID );
                    $('#desDate').val(data['dateMA']);
                    $('#description').val(data['description']);
                    $('#payrollType').val(data['isNonPayroll']);
                    $('#systemType').val(data['typeID']);

                    isVariablePay = data['typeID'];

                    var pullCaption = (data['pullType'] == 0) ? 'Standard' : 'Pull From Attendance';

                    $('#pullType').val(pullCaption);

                    if(isVariablePay > 0){
                        $('#desDate').prop('readonly', true);
                    }

                    if( data['confirmedYN'] == 1){
                        disableAllFields();
                        $('#removeAll_emp').hide();
                    }

                    payroll_group = data['payrollGroup'];
                    is_period_base_process = (data['payrollGroup'] > 0);
                    if(is_period_base_process){
                        masterPage = 'system/hrm/monthly_salary_addition_period_base';
                        $('#payroll-grp-div').show();
                        $('#head-des-dive').removeClass('col-sm-4').addClass('col-sm-2');
                        $('#payroll_grp').val(payroll_group);
                    }


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }

        $('.headerclose').click(function(){
            fetchPage(masterPage, documentID,'HRMS');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        setTimeout(function(){
            
            loadDetail_table();
        }, 500);
       
    });

    $(document).on('keypress', '.number',function (event) {
        var amount = $(this).val();
        if(amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }

    });

    function gropingDrop(selectedID){
        var row = JSON.parse(declarationCombo);
        var h_glCode = '';
        var drop = '<select name="declarationID[]" class="trInputs select2" onchange="changeGLCode(this)">';
        drop += '<option value=""><?php echo $this->lang->line('hrms_payroll_select_grouping_type');?></option>';<!--Select Grouping Type-->
        $.each(row, function(i, obj){
            var selected = ( selectedID == obj.monthlyDeclarationID )? 'selected' : '';
            if( selectedID == obj.monthlyDeclarationID ){  h_glCode = obj.GLAutoID; }

            drop += '<option value="'+obj.monthlyDeclarationID+'" '+selected+' data-gl="'+obj.GLAutoID+'">'+obj.monthlyDeclaration+' | '+obj.GLSecondaryCode+'</option>';
        });
        drop += '</select>';

        drop += '<input type="hidden" name="h-glCode[]" class="h-glCode" value="'+h_glCode+'">';

        return drop;
    }

    function changeGLCode(thisCombo){
        var thisGLCode = $(thisCombo).find(':selected').attr('data-gl');
        var thisCat = $(thisCombo).find(':selected').attr('data-cat');
       
        $(thisCombo).closest('tr').find('.h-glCode').val(thisGLCode);
        $(thisCombo).closest('tr').find('.h-categoryID').val(thisCat);

        //Call function
        changeVariableValues(thisCombo);
    }

    function changeVariableValues(thisCombo){

        var thisCategory = $(thisCombo).find(':selected').val();
        var thisEmpCode = $(thisCombo).closest('tr').find('td:eq(1)').text();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'category':thisCategory,'emp_code':thisEmpCode},
            url: "<?php echo site_url('Employee/get_variable_salary_declaration'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data.amount > 0){
                    $(thisCombo).closest('tr').find('.amountrate').val(data.amount).change();
                    $(thisCombo).closest('tr').find('.amountrate').attr('readonly',true);
                }else{
                    $(thisCombo).closest('tr').find('.amountrate').val(0).change();
                    $(thisCombo).closest('tr').find('.amountrate').attr('readonly',false);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function formatAmount(obj, decimal=2) {
        id = obj.id;
        if ($('#' + id).val() == '') {
            $('#' + id).val(0);
        }
        var amount = $('#' + id).val().replace(/,/g, "");
        amount = parseFloat(amount).toFixed(decimal);
        $('#' + id).val(commaSeparateNumber(amount, decimal));

        saveInline(obj);
    }

    function openEmployeeModal(isRefresh=null){
        var desDate = $('#desDate').val();
        if( desDate != '' ){
            $('#employee_model').modal({backdrop:'static'});
            /*var isEmpLoad = $('#isEmpLoad');

            if( isEmpLoad.val() == 0 ){
                isEmpLoad.val(1);

                emp_modalTB.DataTable().destroy();
                load_employeeForModal(desDate);

            }*/
            if(isRefresh != 1){
                clearAllRows();
            }
            emp_modalTB.DataTable().destroy();
            load_employeeForModal(desDate);
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('hrms_payroll_please_select_a_date_to_load_employee');?>');/*Please select a date to load employee*/
        }
    }

    function load_employeeForModal(desDate){
        $('#emp_modalTB').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'colvis',
                'excel',
                'print'
            ]
        } );

        let url = "<?=site_url('Employee/getEmployeesDataTable_withLastWorkingDay_validation'); ?>?entryDate="+desDate;
        if(is_period_base_process){
            url = "<?=site_url('Employee/getEmployeesDataTable_withLastWorkingDay_validation_period_base'); ?>?entryDate="+desDate;
        }

        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": url,
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {
                /*$('#selectAllBtn').remove();
                var addAll = '<button class="btn btn-primary btn-xs" id="selectAllBtn" style="font-size:12px; margin-left: 16%" onclick="selectAllRows()"> ADD ALL </button>';
                $('#dataTableBtn').append(addAll);*/
            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "segTBCode"},
                {"mData": "CurrencyCode"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'isNonPayroll', 'value':$('#payrollType').val()});
                aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});
                aoData.push({'name':'currencyFilter', 'value':$('#currencyID').val()});
                aoData.push({'name':'systemType', 'value':$('#systemType').val()});
                aoData.push({'name':'masterID', 'value':'<?=$this->input->post('page_id');?>'});

                if(is_period_base_process){
                    aoData.push({'name':'payroll_group', 'value': payroll_group});
                }

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

    /* $('.number').keypress(function (event) {
         if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
             event.preventDefault();
         }
     });*/

    $('.submitBtn').click(function(){
        var isConform = $('#isConform');
        isConform.val( $(this).attr('data-value') );


        if( isConform.val() == 0 ){
            save();
        }
        else{
            var count = 0;
            $('.trInputs').each(function(){
                if( $.trim($(this).val()) == '' ){
                    count += 1;
                    //$(this).css('border-color', 'red');
                    $(this).addClass('required-class');
                }
            });

            if( count == 0) {
                getConformation();
            }else{
                myAlert('e', '<?php echo $this->lang->line('hrms_payroll_please_fill_all_required_fields');?>');/*Please fill all required fields*/
            }

        }

    });

    function save(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/save_empMonthlyAddition'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    fetchPage('system/hrm/monthly_salary_addition','Test','HRMS');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function getConformation(){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                save();
            }
        );
    }

    $(document).on('keypress change','.trInputs', function(){
        //$(this).css('border-color', '#d2d6de');
        $(this).removeClass('required-class');
        $(this).addClass('remove-required-class');
    });

    function empAmount(det, info, exchangeRate){
        //formatAmount(det, dPlace);

        var amount = det.value;
        amount = amount.replace(/,/g , "");

        var localAmount = amount / parseFloat(exchangeRate);
        $('#amountSpan_'+info).text( commaSeparateNumber( localAmount, <?php echo $com_decPlace; ?>) );
        setTimeout(function(){  getTotalAmount(); },100);
    }

    function getTotalAmount(){
        var total = 0;
        $('.localAmount').each(function(elm, val){
            if( !$(this).closest('tr').hasClass('hideTr') ) {
                var amount = ( $.trim($(this).text()) !== '' ) ? $.trim($(this).text()) : 0;
                if (amount != 0) {
                    amount = amount.replace(/,/g, "");
                    total += parseFloat(amount);
                }
            }
        });

        $('#totalAmount').text( commaSeparateNumber( total, <?php echo $com_decPlace ?>) );
    }

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data()  ;
        var empID = details.EIdNo;

        var inArray = -1;
        if(isVariablePay != 0){
            inArray = $.inArray(empID, empTempory_arr);
        }

        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]"  class="modal_empID" value="'+empID+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyID[]" class="modal_CurrencyID" value="'+details.currencyID+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyCode[]" class="modal_CurrencyCode" value="'+details.CurrencyCode+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyDPlace[]"  class="modal_dPlaces" value="'+details.DecimalPlaces+'">';
            empDet += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="'+details.accGroupID+'">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0:  details.ECode,
                1:  details.empName,
                2:  empDet,
                3:  empID
            }]).draw();

            if(isVariablePay != 0){
                empTempory_arr.push(empID);
            }

        }

    }

    function selectAllRows(){
        var inArray = -1;
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            if(isVariablePay != 0){
                inArray = $.inArray(empID, empTempory_arr);
            }

            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]" class="modal_empID" value="' + empID + '">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyID[]" class="modal_CurrencyID" value="' + data.currencyID + '">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyCode[]" class="modal_CurrencyCode" value="' + data.CurrencyCode + '">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyDPlace[]" class="modal_dPlaces" value="' + data.DecimalPlaces + '">';
                empDet1 += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + data.accGroupID + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                if (isVariablePay != 0) {
                    empTempory_arr.push(empID);
                }
            }
        } );
    }

    function addAllRows(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        var postData = $('#tempTB_form').serializeArray();
        // data.push({'name':'monthlyTypes', 'value':$('#monthlyTypes').val()});
        var allData = postData.concat(data);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: allData,
            url: "<?php echo site_url('Employee/save_employeeAsTemp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }else{
                    setTimeout(function(){
                        empTempory_arr = [];
                        loadDetail_table();
                    }, 300);
                    $('#employee_model').modal('hide');
                    clearAllRows();
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        table.clear().draw();

        empTempory_arr = [];
    }

    function removeEmpTB(det, detailID){

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
                var data = $('#emp_monthlyAddFrm').serializeArray();
                data.push({'name':'detailID', 'value':detailID}, {'name':'type_m', 'value':'MA'});

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/removeSingle_emp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            $(det).closest('tr').remove();
                            $('#searchItem').keyup();
                            $('#totalRowCount').text($('#details_table tbody>tr').length);
                            if( $('#details_table tbody>tr').length == 0){
                                $('#removeAll_emp').hide();
                            }
                            getTotalAmount();
                        }
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            }
        );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function(data) {
            return parseInt(data) != empID
        });
        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function disableAllFields(){
        $('#desDate').prop('disabled', true);
        $('#description').prop('disabled', true);
        $('.saveBtn').prop('disabled', true);
        $('.trInputs').prop('disabled', true);
    }

    function removeAll_emp(){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var updateID = $('#updateID').val();

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID' :updateID, 'type_m' : 'MA' },
                    url: "<?php echo site_url('Employee/removeAll_emp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            setTimeout(function(){
                                loadDetail_table();
                            }, 300);
                        }
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function saveInline(obj){

    }

    function loadDetail_table(){

        var documentID = '<?=$this->input->post('page_id');?>';
       
        var emp_arr = $('#emp_arr').val();
        var groups = $('#dropdown_arr').val();

        $.ajax({
           // async: true,
            type: 'post',
            dataType: 'html',
            data: {'masterID' :documentID, 'type_m' : 'MA','isVariable':1,'emp_arr':emp_arr,'groups':groups},
            url: "<?php echo site_url('Employee/loadDetail_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#detail-table-container').html(data);
                stopLoad();
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }
</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 6/1/2016
 * Time: 10:29 AM
 */
