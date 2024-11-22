<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = 'Request Monthly Allowance Claim';
echo head_page($title, false);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_decPlace = $this->common_data['company_data']['company_default_decimal'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();

$segment_arr = fetch_segment(true, false);
$currency_arr = all_currency_new_drop(false);
$masterType_drop = system_salary_cat_drop('VPG', 1);
$pGroups_drop = payroll_group_drop();

$type = 'A';
$isNonPayroll = 1;
$dropDownData = declaration_drop_MAC($type, $isNonPayroll);
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

<div class="col-sm-12" style="margin-bottom: 10px;">
    <fieldset>
    <div class="col-sm-12">
        <legend> <?php echo $this->lang->line('common_header');?> </legend>
        <div class="form-group col-sm-2">
            <label for="documentCode" class="control-label"><?php echo $this->lang->line('hrms_payroll_employee_document_code');?><!--Document Code--></label>
            <input type="text" name="documentCode" class="form-control"  id="documentCode" readonly>
        </div>

        <div class="form-group col-sm-2" id="payroll-grp-div" style="display: none">
            <label class="control-label" for="payroll_grp"><?php echo $this->lang->line('common_payroll_group');?></label>
            <?=form_dropdown('payroll_grp', $pGroups_drop, null, 'class="form-control" id="payroll_grp" disabled')?>
        </div>

        <div class="form-group col-sm-2">
            <label class="control-label" for="payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></label>
            <select name="payrollType" id="payrollType" class="form-control" readonly>
                <option value="N" selected><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                <!-- <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?></option> -->
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label class="control-label" for="systemType"><?php echo $this->lang->line('common_type');?></label>
            <select name="systemType" class="form-control" id="systemType" readonly>
                <option value="0" selected>Monthly Addition</option>
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
            <label for="desDate" class="control-label">Document Date</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="desDate_edit" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="desDate_edit"
                       class="form-control">
            </div>
        </div>

        <div class="form-group col-sm-4" id="head-des-dive">
            <label for="monthDescription" class="control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
            <input type="text" name="monthDescription_edit" class="form-control" id="monthDescription_edit">
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group col-sm-2">
            <label for="fromDate" class="control-label">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="fromDate_edit" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="fromDate_edit"
                       class="form-control">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="toDate" class="control-label">Date to</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="toDate_edit" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="toDate_edit"
                       class="form-control">
            </div>
        </div>
        <div class="form-group col-sm-2 pull-right" style="padding-top:20px;">
            <button type="button" class="btn btn-primary btn-sm saveBtn pull-right" data-value="0" onclick="update_header()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
        </div>
    </div>
    </fieldset>
</div>
<?php echo form_open('', 'id="emp_monthlyAddFrm" autocomplete="off"') ?>
<div id="detail-table-container">
                    <table class="table table-bordered table-condensed no-color" id="allowance_details_table">
                        <thead>
                        <tr>
                            <th style="width: 20px;">#</th>
                            <th style="width: 100px;" class="text-left"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Emp Code--></th>
                            <th style="width: 100px;" class="text-left"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                            <th style="width: 100px;" class="text-left"><?php echo $this->lang->line('hrms_payroll_grouping_type');?><!--Grouping Type--></th>
                            <th style="width: 100px;" class="text-left"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                            <th style="width: 100px" class="text-left"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                            <th style="width: 100px;" class="text-left"><?php echo $this->lang->line('common_description');?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_material_request()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="allowance_details_table_body">
                        <tr>
                            <td></td>
                            <td><input type="text" name="ECode" id="ECode" class="form-control" style="width:100%;"  readonly></td>
                            <td><input type="text" name="empName" id="empName" class="form-control" style="width:100%;"  readonly></td>
                            <td>
                                <!-- <?php  foreach($dropDownData as $row){ ?> -->
                                    <!-- <input type="hidden" name="chartofaccounts_GLAutoID[]" id="chartofaccounts_GLAutoID" val="<?php echo $row['GLAutoID'] ?>">
                                    <input type="hidden" name="chartofaccounts_GLSecondaryCode[]" id="chartofaccounts_GLSecondaryCode" val="<?php echo $row['GLSecondaryCode'] ?>">
                                    <input type="hidden" name="chartofaccounts_GLDescription[]" id="chartofaccounts_GLDescription" val="<?php echo $row['GLDescription'] ?>">
                                    <input type="hidden" name="chartofaccounts_salaryCategoryID[]" id="chartofaccounts_salaryCategoryID" val="<?php echo $row['salaryCategoryID'] ?>"> -->
                                <!-- <?php } ?> -->
                                <select name="declarationID[]" id="declarationID" class="form-control">
                                    <?php foreach($dropDownData as $row){ ?>
                                        <option value="<?php echo $row['monthlyDeclarationID'] ?>"><?php echo $row['monthlyDeclaration'] ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="tdCol">
                                <input type="text" name="empCurrency[]" id="empCurrency" style="width:100%;" readonly>
                            </td>
                            <td class="tdCol">
                                <input type="text" name="amount[]" class="trInputs number form-control text-right amount" id="amount"  value="" 
                                 onchange="formatAmount(this)"> <!--onkeyup="empAmount(this)"-->
                            </td>
                            <td style="text-align:right;">
                                <textarea class="form-control" name="description[]" style="width:100%;"></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            <!-- <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search"
                                       name="search[]"
                                       placeholder="Item ID,Item Description..."
                                       id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock">
                            </td> -->
                        </tr>
                        </tbody>
                    </table>
</div>
<div class="row"> <!--modal-footer-->
    <hr>
    <div class="col-sm-6 pull-left">
        <input type="hidden" name="document_date" id="document_date" value="0">
        <input type="hidden" id="rowCount" value="0">
        <input type="hidden" id="isConform" name="isConform" value="0">
        <input type="hidden" name="temp_empHiddenID[]"  class="modal_empID">
        <input type="hidden" name="temp_empCurrencyDPlace[]"  class="modal_dPlaces">
        <input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID">
        <input type="hidden" name="temp_segment[]" class="modal_segment">
        <input type="hidden" name="type_m" value="MAC"/>
        <input type="hidden" name="empCurrencyID" id="empCurrencyID">
        <input type="hidden" name="empCurrencyCode" id="empCurrencyCode">
    </div>
    <div class="col-sm-3 pull-right">
        <button type="button" class="btn btn-success btn-sm saveBtn submitBtn pull-right" data-value="1" style=""><?php echo $this->lang->line('common_save_and_confirm');?><!--Save & Confirm--></button>
        <button type="button" class="btn btn-primary btn-sm saveBtn submitBtn pull-right" data-value="0" style="margin-right: 5px;"><?php echo $this->lang->line('common_save_as_draft');?><!--Save As Draft--></button>
    </div>
</div>
<?php echo form_close();?>

<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">
    var isVariablePay = 0;
    var empTB = $('#empTB');
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({ "bPaginate": false });
    var declarationCombo = '<?php echo json_encode(declaration_drop('A', $this->input->post('data_arr')) ); ?>';
    var empTempory_arr = [];

    var search_id = 1;

    var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    var monthlyClaimMasterID = p_id;
    var is_period_base_process = false;
    var payroll_group = 0;

    $(document).ready(function() {

        let masterPage = 'system/hrm/monthly_allowance_claim';

        if( monthlyClaimMasterID != 0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data : { 'editID': monthlyClaimMasterID},
                url: "<?php echo site_url('Employee/editmonthAddition'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#ECode').val(data.empCode);
                    $('#empName').val(data.empName);
                    $('#empCurrency').val(data.empCurrency);
                    $('#empCurrencyID').val(data.empCurrencyID);
                    $('#empCurrencyCode').val(data.empCurrency);
                
                    $('#isConform').val(data.master.confirmedYN);
                    $("#documentCode").val(data.master.monthlyClaimCode);
                    
                    $('#desDate_edit').val(data.master.documentDate); 
                    $('#document_date').val(data.master.documentDate);
                    $('#fromDate_edit').val(data.master.dateFrom); 
                    $('#toDate_edit').val(data.master.dateTo); 
                    $('#monthDescription_edit').val(data.master.description);
                    $('#payrollType').val(data.master.isNonPayroll);
                    $('#systemType').val(data.master.typeID);

                    
                    if( data['confirmedYN'] == 1){
                        disableAllFields();
                    }

                    // payroll_group = null;
                    is_period_base_process = (payroll_group > 0);
                    if(is_period_base_process){
                        masterPage = 'system/hrm/monthly_salary_addition_period_base';
                        $('#payroll-grp-div').show();
                        $('#head-des-dive').removeClass('col-sm-4').addClass('col-sm-2');
                        $('#payroll_grp').val(payroll_group);
                    }

                    // $('#allowance_details_table_body').empty();
                    x = 1;

                    if (jQuery.isEmptyObject(data['details'])) {
                        //$('#allowance_details_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                        myAlert('w', 'Details not added for this Claim');
                    } else {
                        $('#allowance_details_table_body').empty();

                        $.each(data['details'], function(i, item) 
                        {
                            var groupingType_Drop = '<select name="declarationID[]" id="editdeclarationID_' + i + '" class="form-control declarationID">';
                            <?php foreach($dropDownData as $row): ?>
                                if(<?php echo $row['monthlyDeclarationID'];?> == item['declarationID']){
                                    var is_selected = 'selected';
                                }else{
                                    var is_selected = '';
                                }
                                groupingType_Drop += '<option value="<?php echo $row['monthlyDeclarationID'];?>" '+ is_selected +'><?php echo $row['monthlyDeclaration']; ?></option>';
                            <?php endforeach; ?>
                            groupingType_Drop += '</select>';

                            //var amt = commaSeparateNumber( item['transactionAmount'], <?php echo $com_decPlace; ?>);

                            $('#allowance_details_table_body').append(
                                '<tr>' +
                                    '<input type="hidden" name="monthlyClaimDetailID[]" id="monthlyClaimDetailID" value="'+ item['monthlyClaimDetailID'] +'">' +
                                    '<td>' + x + '</td>' +
                                    '<td><input type="text" name="ECode" id="ECode" value="' + data.empCode + '" class="form-control" style="width:100%;"  readonly></td>' +
                                    '<td><input type="text" name="empName" id="empName" value="' + data.empName + '" class="form-control" style="width:100%;"  readonly></td>' +
                                    '<td>' + groupingType_Drop + '</td>' +
                                    '<td class="text-left">' + data.empCurrency + '</td>' +
                                    '<td><input type="text" name="amount[]" class="trInputs number form-control text-right amount" id="editamount_' + i + '" value="' + item['transactionAmount'] + '" onchange="formatAmount(this)"></td>' +
                                    '<td><textarea class="form-control" name="description[]" style="width:100%;">'+item['description']+'</textarea></td>' +
                                    '<td><a onclick="delete_claimDetail(' + item['monthlyClaimDetailID'] + ')"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td>' +
                                '</tr>'
                            );
                            x++;
                        });
                    }

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        $('.headerclose').click(function(){
            fetchPage(masterPage, monthlyClaimMasterID,'HRMS');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

    });

    function update_header()
    {
        var payrollType = $('#payrollType').val();
        var fromDate_edit = $('#fromDate_edit').val();
        var desDate_edit = $('#desDate_edit').val();
        var toDate_edit = $('#toDate_edit').val();
        var monthDescription_edit = $('#monthDescription_edit').val();
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'toDate_edit':toDate_edit,'fromDate_edit':fromDate_edit,'desDate_edit':desDate_edit,'monthDescription_edit':monthDescription_edit,'monthlyClaimMasterID':monthlyClaimMasterID},
                url: "<?php echo site_url('Employee/update_monthly_allowance_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        setTimeout(function(){
                            if(systemType == 0){
                                fetchPage('system/hrm/monthly_allowance_claim_edit', monthlyClaimMasterID, 'HRMS','', payrollType);
                            }
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
    }

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


    // function load_employeeForModal()
    // {
    //     let url = "<?//=site_url('Employee/employee_LastWorkingDay_validation_C'); ?>" ;

    //             $.ajax({
    //                 async: true,
    //                 type: 'GET',
    //                 dataType: 'json',
    //                 url: url,
    //                 beforeSend: function () {
    //                     startLoad();
    //                 },
    //                 success: function (data) {
    //                     stopLoad();
    //                     if(data){
    //                         $('.modal_empID').val(data[0]['EIdNo']);
    //                         $('.modal_CurrencyID').val(data[0]['currencyID']);
    //                         $('.modal_CurrencyCode').val(data[0]['CurrencyCode']);
    //                         $('.modal_dPlaces').val(data[0]['DecimalPlaces']);
    //                         $('.modal_accGroupID').val(data[0]['accGroupID']);
    //                         $('.modal_segment').val(data[0]['segTBCode']).change();
    //                     }

    //                     if($('.modal_empID').val() != null || $('.modal_empID').val() != '') {
    //                         addAllRows();
    //                     }
                       
    //                 }, error: function () {
    //                     myAlert('e', 'An Error Occurred! Please Try Again.');
    //                     stopLoad();
    //                 }
    //             });
    // }

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

    function save(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        data.push({'name':'monthlyClaimMasterID', 'value':monthlyClaimMasterID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/save_empMonthly_Allowance'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    fetchPage('system/hrm/monthly_allowance_claim','Test','HRMS');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
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

    function addAllRows(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        var postData = $('#tempTB_form').serializeArray();
        var allData = postData.concat(data);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: allData,
            url: "<?php echo site_url('Employee/saveemployeeAsTemp'); ?>",
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
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
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
                data.push({'name':'detailID', 'value':detailID}, {'name':'type_m', 'value':'MAC'});

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/remove_Single_emp'); ?>",
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
                            // if( $('#details_table tbody>tr').length == 0){
                            //     $('#removeAll_emp').hide();
                            // }
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

    function disableAllFields(){
        $('#desDate_edit').prop('disabled', true);
        $('#monthDescription_edit').prop('disabled', true);
        $('#fromDate_edit').prop('readonly', true);
        $('#toDate_edit').prop('readonly', true);

        $('.saveBtn').prop('disabled', true);
        $('.trInputs').prop('disabled', true);
    }

    function saveInline(obj){

    }

    function loadDetail_table(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'masterID' :monthlyClaimMasterID, 'type_m' : 'MAC' },
            url: "<?php echo site_url('Employee/loadAllowanceDetail_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#detail-table-container').html(data);
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_uploadModal(){
        //var desDate = $('#desDate').val();
        //$('#docDate').val(desDate);
        $('#excelUpload_Modal').modal('show');
    }

    function openDownloadTemplate_modal(){
        $("#segment-arr").multiselect2('selectAll', false);
        $("#segment-arr").multiselect2('updateButtonText');
        $('#downloadTemplate_modal').modal('show');
    }

    function downloadTemplate(){
        if($('#segment-arr').val() == null){
            bootbox.alert('<div class="alert alert-danger" style="margin-top: 20px;">Please select at least one segment to proceed.</div>');
            return false;
        }
        var form= document.getElementById('downloadTemplate_form');
        form.target='_blank';
        form.action='<?php echo site_url('Employee/download_csv'); ?>';
        form.submit();
    }

    function excel_upload(){
        var formData = new FormData($("#employeeUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/monthly_Allowance_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 's' || data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }

                if (data[0] == 'm') {
                    $('#excelUploadMsg_Modal').modal('show');
                    $('#upload-msg-div').html(data[1]);
                }

                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        loadDetail_table()
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function add_more_material_request() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#allowance_details_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.amount').val('');
        appendData.find('textarea').val('');
        appendData.find('#monthlyClaimDetailID').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#allowance_details_table').append(appendData);
        var lenght = $('#allowance_details_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
       // initializeitemTypeahead(type, search_id);
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }

    }

    function delete_claimDetail(monthlyClaimDetailID){
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
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'monthlyClaimDetailID' : monthlyClaimDetailID, 'monthlyClaimMasterID' : monthlyClaimMasterID},
                    url: "<?php echo site_url('Employee/delete_claimDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        // if(data[0] == 's'){
                        //     setTimeout(function(){
                        //         loadDetail_table();
                        //     }, 300);
                        // }
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }
</script>

<?php

