<!--Translation added by Naseek-->

<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_salary_declaration');
echo head_page($title, false);


$date_format_policy = date_format_policy();
$current_date = current_format_date();
$masterID = $this->input->post('page_id');

$salaryCategories_drop = salaryCategories_drop($masterID,1);
$segment_arr = fetch_segment(true, false);


?>

<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
        margin: 10px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .row-centered {
        text-align:center;
    }

    .col-centered {
        display:inline-block;
        float:none;
        /* reset the text-align */
        text-align:left;
        /* inline-block space fix */
        margin-right:-4px;
        text-align: center;
    }

    .file-browse{
        border: 1px solid #ccc;
    }
</style>

<div id="outputSalaryDeclaration_detail"></div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="bulkDetails_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" style="width: 95%" role="document">
        <div class="modal-content">
            <form class="" id="bulk_form" autocomplete="off">
                <input type="hidden" name="masterID" id="masterID" value="<?php echo $masterID; ?>"/>
                <input type="hidden" name="isVariable" id="isVariable" value="1"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_add_detail');?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <fieldset style="margin-top: -10px;">
                            <legend><?php echo $this->lang->line('emp_employment_details');?> Criteria </legend>
                            <div class="container">
                                <div class="row row-centered">
                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('hrms_payroll_effective_date');?><!--Effective Date--></label>
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" class="form-control" id="bulk_effectiveDate" name="bulk_effectiveDate" value="<?php echo $current_date; ?>"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                                        </div>
                                    </div>


                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('hrms_payroll_pay_date');?><!--Pay Date--></label>
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" class="form-control" id="payDate" name="payDate" value="<?php echo $current_date; ?>"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                                        </div>
                                    </div>

                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('common_category');?><!--Category--></label>
                                        <div class="input-group">
                                            <select name="category[]" class="form-control" id="bulk_salarySubCatID" multiple="multiple">
                                                <?php
                                                $option = '';
                                                if(!empty($salaryCategories_drop)){
                                                    foreach($salaryCategories_drop as $salCast){
                                                        $option .= "<option value='{$salCast['salaryCategoryID']}|{$salCast['salaryCategory']}' >{$salCast['salaryDescription']}</option>";
                                                    }
                                                }
                                                echo $option;
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-1 col-xs-6 col-centered" style="/*padding-top: 24px;*/">
                                        <label>&nbsp;</label>
                                        <div class="input-group">
                                            <button class="btn btn-primary btn-sm pull-right" style="font-size:12px;" onclick="addAllRows()" type="button">
                                                <?php echo $this->lang->line('common_proceed');?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <input type="hidden" id="isEmpLoad" value="0" >
                                <div class="col-sm-2 col-xs-4 select-container">
                                    <label for="segment"> <?php echo $this->lang->line('common_segment');?><!--Segment--> </label>

                                </div>
                                <div class="col-sm-4 col-xs-4 select-container">
                                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID"  multiple="multiple"'); ?>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right" style="/*padding-top: 24px;*/">
                                    <button class="btn btn-primary btn-sm pull-right" id="selectAllBtn" style="font-size:12px;" onclick="selectAllRows()" type="button">
                                        <?php echo $this->lang->line('hrms_payroll_select_all');?><!--Select All-->
                                    </button>
                                    <button type="button" onclick="load_employeeForBulk_refresh()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px">
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
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP CODE--></th>
                                            <th style="max-width: 95%"><?php echo $this->lang->line('hrms_payroll_emp_name');?><!--EMP NAME--></th>
                                            <th style="width: 40px">
                                                <span class="glyphicon glyphicon-trash" onclick="clearAllRows()"
                                                  style="color:rgb(209, 91, 71);" title="<?php echo $this->lang->line('common_clear_all');?>"></span>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addAllRows()" >
                        <?php echo $this->lang->line('common_proceed');?>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="excelUpload_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_employee_upload_form'); ?><!--Employee upload form--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="employeeUpload_form" class="form-horizontal"'); ?>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-6 control-label">Effective Date</label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="up_effectiveDate" data-inputmask="'alias': '<?= $date_format_policy ?>'"  id="up_effectiveDate"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-6">
                        <label class="col-sm-6 control-label">Pay Date</label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="up_payDate" data-inputmask="'alias': '<?= $date_format_policy ?>'"  id="up_payDate"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 10px; margin-bottom: 10px;">
                        <input type="file" class="file-browse" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                    </div>
                    <input type="hidden" name="masterID" value="<?=$masterID;?>">

                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?= $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?= $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>

                    <div class="col-sm-12">
                        <div class="alert alert-danger" id="upload-msg-div"> </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="excel_upload()">
                    <i class="fa fa-upload" aria-hidden="true"></i> Upload
                </button>

                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="downloadTemplate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=$this->lang->line('hrms_payroll_excel_download');?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open('', 'id="downloadTemplate_form" class=""'); ?>
                    <div class="col-sm-12" style="">
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="s_category"> &nbsp; <?php echo $this->lang->line('common_category');?> &nbsp; </label>
                            <select name="s_category[]" class="form-control" id="down_salaryID" multiple="multiple">
                                <?php
                                $option = '';
                                if(!empty($salaryCategories_drop)){
                                    foreach($salaryCategories_drop as $salCast){
                                        $option .= "<option value='{$salCast['salaryCategoryID']}' >{$salCast['salaryDescription']}</option>";
                                    }
                                }
                                echo $option;
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="control-label">Segments</label>
                            <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="filter_segmentID" multiple="multiple" onchange="loadEmployees()"'); ?>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="control-label">Employees</label>
                            <div id="div-employee-container">
                                <?php echo form_dropdown('empID[]', [], '', ' class="form-control" id="filter_empDrop" multiple="multiple"'); ?>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="downloadTemplate()">
                    <i class="fa fa-cloud-download" aria-hidden="true"></i> <?=$this->lang->line('hrms_payroll_excel_download')?>
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var SD_masterID  = <?php echo json_encode(trim($masterID)); ?>;
    var emp_modalTB = $('#emp_modalTB');
    var empTemporary_arr = [];
    var tempTB = $('#tempTB').DataTable({
        "bPaginate": false,
        "columnDefs": [ {
            "targets": [2],
            "orderable": false
        } ]
    });

    var segmentDrop = $('#filter_segmentID');
    var empDrop = $('#filter_empDrop');
    var policy_id = '<?=$this->input->post('policy_id')?>';
    var is_period_base_process = false; /*Initializing in hrm/ajax/salary_declaration_multiple_insertion_ajax_details */
    var payroll_group = 0; /*Initializing in hrm/ajax/salary_declaration_multiple_insertion_ajax_details */

    $(document).ready(function () {

        $('.headerclose').click(function () {
            let page = '';

            switch (policy_id) {
                case 'standard':
                    page = 'system/hrm/salary_variable_declaration';
                    break;
                case 'period_base':
                    page = 'system/hrm/salary_declaration_multiple_insertion_period_base';
                    break;
                default :
                    page = 'system/hrm/salary_declaration_multiple_insertion';
            }
            fetchPage(page, SD_masterID , 'Salary Declaration');
        });

        load_SalaryDeclarationMaster(SD_masterID );

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#bulk_salarySubCatID, #down_salaryID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        })
        .on('dp.change', function (ev, obj) {
            if( $(this).attr('id') == 'effectiveDate' ){

            }

            if( $(obj).attr('id') == 'documentDate' ){

            }
        });

        $("input.numeric").numeric();

    });

    function load_SalaryDeclarationMaster(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/load_SalaryDeclarationMaster') ?>",
            data: {id: id, 'isVariable': 1},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#declaration_form').hide();
                $("#outputSalaryDeclaration_detail").html(data);
                $("#declarationMasterID").val(id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
        return false;
    }

    function confirmSalaryDeclaration() {
        bootbox.confirm("Are you sure want to confirm this variable salary declaration?", function (confirmed) {
            if (confirmed) {
                confirm_salaryDeclaration();
            }
        });
    }

    function confirm_salaryDeclaration(salaryControlCheckPass=0){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/VariableConfirmSalaryDeclaration'); ?>",
            data: {'masterID': SD_masterID, 'salaryControlCheckPass': salaryControlCheckPass,'isVariable':'1'},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'm'){
                    bootbox.confirm({
                        message: data[1],
                        size: 'large',
                        callback: function(confirmed) {
                            if (confirmed) {
                                confirm_salaryDeclaration(1);
                            }
                        }
                    });
                    return false;
                }

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.headerclose').click();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function delete_item(detailID) {
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
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID, 'masterID': SD_masterID,'isVariable':'1'},
                    url: "<?php echo site_url('Employee/delete_salary_declaration'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_SalaryDeclarationMaster(SD_masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            });
    }

    function delete_employee(empID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete_all_records_of_this_employee');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empID': empID, 'masterID': SD_masterID,'isVariable':'1'},
                    url: "<?php echo site_url('Employee/delete_salary_declaration_single_employee'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_SalaryDeclarationMaster(SD_masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            }
        );
    }

    function delete_all_item() {
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
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': SD_masterID,'isVariable':'1'},
                    url: "<?php echo site_url('Employee/delete_salary_declaration_all_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    }, success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_SalaryDeclarationMaster(SD_masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            });
    }

    function getNewAmount(obj){
        var dPlace = $('#employee :selected').attr('data-decimal-places');
        var currentAmount = getNumberAndValidate($('#currentAmount').val(), dPlace);
        var newAmount = getNumberAndValidate($(obj).val(), dPlace);
        var amount = newAmount-currentAmount;

        $('#amount').val( commaSeparateNumber(amount, dPlace) );
    }

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = parseFloat(thisVal.replace(/,/g, ""));
        thisVal = thisVal.toFixed(dPlace);

        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    function open_bulkDetailsModal(){
        $("#bulk_salarySubCatID").multiselect2("deselectAll", false);
        $("#bulk_salarySubCatID").multiselect2("refresh");

        $('#bulkDetails_modal').modal('show');
        $('#bulk_effectiveDate, #payDate').val(docDate);

        if(disableDate == 1){
            $('#bulk_effectiveDate, #payDate').attr('disabled', true);
        }

        emp_modalTB.DataTable().destroy();
        load_employeeForBulk(docDate);
    }

    function load_employeeForBulk_refresh(){
        var bulk_effectiveDate = $('#payDate').val();
        load_employeeForBulk(bulk_effectiveDate);
    }

    function load_employeeForBulk(effectiveDate=null){
        var req_url = "<?=site_url('Employee/getEmployeesDataTable_salaryDeclaration'); ?>?entryDate="+effectiveDate;

        if(is_period_base_process){
            req_url = "<?=site_url('Employee/getEmployeesDataTable_withLastWorkingDay_validation_period_base'); ?>?entryDate="+effectiveDate;
        }

        emp_modalTB.DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": req_url,
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
                aoData.push({'name':'isNonPayroll', 'value': $('#payrollType').val()});
                aoData.push({'name':'segmentID', 'value': $('#segmentID').val()});
                aoData.push({'name':'currencyFilter', 'value': $('#docCurrency').val()});
                aoData.push({'name':'isFromSalaryDeclaration', 'value': 1});
                aoData.push({'name':'payroll_group', 'value': payroll_group});

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

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTemporary_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]"  class="modal_empID" value="' + details.EIdNo + '">';
            empDet += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + details.accGroupID + '">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet,
                3: empID
            }]).draw();
            empTemporary_arr.push(empID);
        }
    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTemporary_arr);

            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]" class="modal_empID" value="' + data.EIdNo + '">';
                empDet1 += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + data.accGroupID + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTemporary_arr.push(empID);
            }
        } );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTemporary_arr = $.grep(empTemporary_arr, function(data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        empTemporary_arr = [];
        table.clear().draw();
    }

    function addAllRows(){
        var postData = $('#bulk_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Employee/save_salaryDeclarationMultipleEmployee'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }
                else if( data[0] == 'm'){
                    bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                }else{
                    $('#bulkDetails_modal').modal('hide');
                    setTimeout(function(){
                        load_SalaryDeclarationMaster(SD_masterID);
                    }, 300);

                    clearAllRows();
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    segmentDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '100%',
        maxHeight: '30px'
    });
    segmentDrop.multiselect2('selectAll', false);
    segmentDrop.multiselect2('updateButtonText');

    empDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        enableFiltering: true,
        maxHeight: 200,
        numberDisplayed: 2,
        buttonWidth: '100%'
    });
    empDrop.multiselect2('selectAll', false);
    empDrop.multiselect2('updateButtonText');

    function loadEmployees(){
        let segmentID = segmentDrop.val();
        let docCurrency = $('#docCurrency').val();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_list_by_segment') ?>",
            data: {
                'segmentID': segmentID, 'status':0, 'dropID': 'filter_empDrop',
                'currency_filter': docCurrency, 'isFromSalaryDeclaration': true
            },
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){

                    $("#div-employee-container").html( data[1] );

                    empDrop = $('#filter_empDrop');

                    empDrop.multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '100%'
                    });
                    empDrop.multiselect2('selectAll', false);
                    empDrop.multiselect2('updateButtonText');

                }
                else{
                    empDrop.empty();
                    empDrop.multiselect('refresh');
                    empDrop.multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    empDrop.multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

</script>
<?php
