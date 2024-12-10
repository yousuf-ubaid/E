<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_pay_declaration');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$masterID = $this->input->post('page_id');

$salaryCategories_drop = system_salary_cat_drop('VPG', 1);
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
</style>

<div id="out-put-vp-detail"></div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="bulkDetails_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" style="width: 95%" role="document">
        <div class="modal-content">
            <form class="" id="bulk_form" autocomplete="off">
                <input type="hidden" name="masterID" id="masterID" value="<?php echo $masterID; ?>"/>
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
                                        <label><?php echo $this->lang->line('hrms_payroll_effective_date');?> <span id="effDate_reqMark"><?php required_mark(); ?></span></label>
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" class="form-control" id="bulk_effectiveDate" name="bulk_effectiveDate" value="<?php echo $current_date; ?>"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                                        </div>
                                    </div>


                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label for="bulk_salarySubCatID"><?php echo $this->lang->line('common_category').' '; required_mark(); ?></label>
                                        <div class="input-group">
                                            <select name="category[]" class="form-control" id="bulk_salarySubCatID" multiple="multiple">
                                                <?php
                                                $option = '';
                                                if(!empty($salaryCategories_drop)){
                                                    foreach($salaryCategories_drop as $key=>$val){
                                                        $option .= "<option value='{$key}' >{$val}</option>";
                                                    }
                                                }
                                                echo $option;
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('common_amount');?><!--Effective Date--></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-dollar"></i></div>
                                            <input type="text" class="form-control amountTxt right-align" id="bulk_amount" name="bulk_amount" value="" />
                                        </div>
                                    </div>

                                    <div class="col-sm-3 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('common_narration');?><!--Effective Date--></label>
                                        <div class="input-group" style="width: 99%">
                                            <input type="text" class="form-control" id="bulk_narration" name="bulk_narration" value="" />
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



<script type="text/javascript">
    var VD_masterID  = <?php echo json_encode(trim($masterID)); ?>;
    var emp_modalTB = $('#emp_modalTB');
    var empTemporary_arr = [];
    var tempTB = $('#tempTB').DataTable({
        "bPaginate": false,
        "columnDefs": [ {
            "targets": [2],
            "orderable": false
        } ]
    });

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/hrm/variable_pay_declaration_master', VD_masterID , 'Variable Pay Declaration');
        });

        load_variable_pay_declaration_master(VD_masterID );

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#bulk_salarySubCatID').multiselect2({
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
        }).on('dp.change', function (ev, obj) {
            if( $(this).attr('id') == 'effectiveDate' ){

            }
        });

        $("input.numeric").numeric();

    });

    function load_variable_pay_declaration_master(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/load_variable_pay_declaration_master') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#declaration_form').hide();
                $("#out-put-vp-detail").html(data);
                $("#declarationMasterID").val(id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
        return false;
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
                    data: {'detailID': detailID, 'masterID': VD_masterID},
                    url: "<?php echo site_url('Employee/delete_variable_pay_declaration_single_row'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_variable_pay_declaration_master(VD_masterID);
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
                    data: {'empID': empID, 'masterID': VD_masterID},
                    url: "<?php echo site_url('Employee/delete_variable_pay_declaration_single_employee'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_variable_pay_declaration_master(VD_masterID);
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
                    data: {'masterID': VD_masterID},
                    url: "<?php echo site_url('Employee/delete_variable_pay_declaration_all_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    }, success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_variable_pay_declaration_master(VD_masterID);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete salary declaration of employee');
                    }
                });
            });
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
        $('#bulk_effectiveDate').val(docDate);

        if(disableDate == 1){
            $('#bulk_effectiveDate').attr('disabled', true);
        }

        emp_modalTB.DataTable().destroy();
        load_employeeForBulk(docDate);
    }

    function load_employeeForBulk_refresh(){
        var bulk_effectiveDate = $('#bulk_effectiveDate').val();
        load_employeeForBulk(bulk_effectiveDate);
    }

    function load_employeeForBulk(effectiveDate=null){
        emp_modalTB.DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTable_salaryDeclaration'); ?>?entryDate="+effectiveDate,
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () { },
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
                aoData.push({'name':'currencyFilter', 'value':$('#docCurrency').val()});
                aoData.push({'name':'isFromSalaryDeclaration', 'value':'1'});
                aoData.push({'name':'isVPDeclaration', 'value':'1'});

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
            url: "<?php echo site_url('Employee/save_variable_pay_declaration_employee'); ?>",
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
                        load_variable_pay_declaration_master(VD_masterID);
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

</script>

<?php
