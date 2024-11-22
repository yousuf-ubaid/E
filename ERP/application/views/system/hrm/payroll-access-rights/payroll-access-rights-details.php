<?php

$masterID = trim($this->input->post('page_id'));
$description = trim($this->input->post('data_arr'));

$segment_arr = fetch_segment(true, false);
$designation_arr = getDesignationDrop();

function translate_fn($key, $rType=null){
    $primaryLanguage = getPrimaryLanguage();
    $CI =& get_instance();
    $CI->lang->load('hrms_payroll', $primaryLanguage);
    $CI->lang->load('common', $primaryLanguage);

    if($rType == 1){
        return $CI->lang->line($key);
    }
    echo $CI->lang->line($key);
}
$title = translate_fn('hrms_payroll_access_rights_title', 1);
echo head_page($title, false);
?>

    <style>
        legend{
            font-size: 16px !important;
        }

        .select-container .btn-group{
            width: 155px !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">

    <div class="masterContainer">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                    <tr>
                        <td width="85px"><?php translate_fn('common_description');?> : <!--Description--></td>
                        <td class="bgWhite" colspan="2">
                            <a href="#" data-type="text" data-placement="bottom" data-title="Edit Description" data-pk="<?php echo $description?>"
                               id="description_xEditable" data-value="<?php echo $description; ?>">
                                <?php echo $description?>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-7">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php translate_fn('hrms_payroll_employee');?></legend>
                <div class="row" style="margin-bottom: 4px;">
                    <div class="form-group col-sm-4 col-xs-4 pull-right">
                        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openEmployeeModal('employee')">
                            <i class="fa fa-fw fa-user"></i> <?php translate_fn('hrms_payroll_add_employee');?><!--Add Employee-->
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table class="<?php echo table_class(); ?>" id="group_employeeTB">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="width:auto"><?php translate_fn('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                <th style="width:auto"><?php translate_fn('common_segment');?><!--Segment--></th>
                                <th style="width:auto"><?php translate_fn('common_designation');?><!--Designation--></th>
                                <th style="width: 5%; padding-right: 6px;">
                                    <div class="pull-right">
                                        <span class="glyphicon glyphicon-trash" onclick="removeAll_employee('employees')" style="color:#d15b47;"></span>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-md-5">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php translate_fn('hrms_payroll_access_rights_in_charges'); ?></legend>
                <div class="row" style="margin-bottom: 4px;">
                    <div class="form-group col-sm-4 col-xs-4 pull-right">
                        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openEmployeeModal('in-charge')">
                            <i class="fa fa-fw fa-user"></i> <?php translate_fn('hrms_payroll_add_employee');?><!--Add Employee-->
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <table class="<?php echo table_class(); ?>" id="in-charge-employeeTB">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="width:auto"><?php translate_fn('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                <th style="width:auto"><?php translate_fn('common_designation');?><!--Designation--></th>
                                <th style="width: 5%; padding-right: 6px;"">
                                    <div class="pull-right">
                                        <span class="glyphicon glyphicon-trash" onclick="removeAll_employee('in-charge')" style="color:#d15b47;"></span>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999"  >
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php translate_fn('hrms_payroll_employee');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <input type="hidden" id="isEmpLoad" value="0" >
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="segment"> <?php translate_fn('common_segment');?><!--Segment--> </label>
                                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID"  multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-5 col-xs-4 select-container">
                                <label for="currency"> <?php translate_fn('common_designation');?><!--Designation--> </label>
                                <?php echo form_dropdown('currency[]', $designation_arr, '', 'class="form-control" id="designationID" multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-3 col-xs-3 pull-right">
                                <label for="currency" class="visible-sm visible-xs">&nbsp;</label>
                                <button class="btn btn-primary btn-sm pull-right" id="selectAllBtn" style="font-size:12px;" onclick="selectAllRows()">
                                    <?php translate_fn('hrms_payroll_select_all');?><!--Select All-->
                                </button>
                                <button type="button" onclick="openEmployeeModal()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px" id="load-btn">
                                    <?php translate_fn('common_load');?><!--Load-->
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
                                        <th style="min-width: 25%"><?php translate_fn('hrms_payroll_emp_code');?><!--EMP Code--></th>
                                        <th style="width:auto"><?php translate_fn('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                        <th style="width:auto"><?php translate_fn('common_segment');?><!--Segment--></th>
                                        <th style="width:auto"><?php translate_fn('common_designation');?><!--Designation--></th>
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
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;" onclick="addAllRows()">
                                        <?php translate_fn('hrms_payroll_add_all');?><!--Add All-->
                                    </button>
                                    <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;" onclick="clearAllRows()">
                                        <?php translate_fn('common_clear_all');?><!--Clear All-->
                                    </button>
                                </div>
                                <hr style="margin-top: 7%">
                                <form id="tempTB_form">
                                    <input type="hidden" name="masterID" id="masterID"/>
                                    <input type="hidden" name="type_m" value="MA"/>
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%"><?php translate_fn('hrms_payroll_emp_code');?><!--EMP CODE--></th>
                                            <th style="max-width: 95%"><?php translate_fn('hrms_payroll_emp_name');?><!--EMP NAME--></th>
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
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;"><?php translate_fn('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var p_id = <?php echo json_encode($masterID); ?>;
    var description = <?php echo json_encode($description); ?>;
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({ "bPaginate": false });
    var empTempory_arr = [];

    $('#designationID').multiselect2({
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

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/payroll-access-rights/payroll-access-rights', p_id, 'Slab Master');
        });

        $('#description_xEditable').editable({
            url: '<?php echo site_url('Employee/ajax_update_groupMaster?masterID='.$masterID) ?>',
            send: 'always',
            ajaxOptions: {
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if( data[0] == 's'){
                        var description_xEditable = $('#description_xEditable');
                        setTimeout(function (){
                            description_xEditable.attr('data-pk', description_xEditable.html());
                            description = $.trim(description_xEditable.html());
                        },400);

                    }else{
                        var oldVal = $('#description_xEditable').data('pk');
                        setTimeout(function (){
                            $('#description_xEditable').editable('setValue', oldVal );
                        },300);
                    }
                },
                error: function (xhr) {
                    myAlert('e', xhr.responseText);
                }
            }
        });

        load_group_employee();
        load_in_charge_employee();
    });

    function load_group_employee(empType){
        $('#group_employeeTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_group_employee'); ?>",
            "aaSorting": [[1, 'asc']],
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': [4]
            }],
            "fnInitComplete": function () {

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
                {"mData": "empName"},
                {"mData": "segTBCode"},
                {"mData": "DesDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'groupID', 'value':p_id});

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

    function load_in_charge_employee(empType){
        $('#in-charge-employeeTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "paging":   false,
            "sAjaxSource": "<?php echo site_url('Employee/load_in_charge_employee'); ?>",
            "aaSorting": [[1, 'asc']],
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': [3]
            }],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                $('#in-charge-employeeTB_wrapper .row:first').css('padding-right', '20px');
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "empName"},
                {"mData": "DesDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'groupID', 'value':p_id});

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

    function openEmployeeModal(empType){
        $('#employee_model').modal({backdrop:'static'});
        clearAllRows();
        emp_modalTB.DataTable().destroy();
        load_employeeForModal(empType);
        $('#load-btn').attr('onclick', 'load_employeeForModal(\''+empType+'\')');
        $('#addAllBtn').attr('onclick', 'addAllRows(\''+empType+'\')');
    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function load_employeeForModal(empType){

        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/get_employees_for_access_rights'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

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
                {"mData": "designationStr"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'groupID', 'value':p_id});
                aoData.push({'name':'empType', 'value':empType});
                aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});
                aoData.push({'name':'designationFilter', 'value':$('#designationID').val()});
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

        var details = table.row(  thisRow.parents('tr') ).data()  ;
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" ';
            empDet += 'style="color:#d15b47;"></span> </a></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet,
                3: empID
            }]).draw();

            empTempory_arr.push(empID);
        }
    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                var empDet1 = '<div class="pull-right"><span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" ';
                empDet1 += 'style="color:#d15b47;"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTempory_arr.push(empID);
            }
        } );
    }

    function addAllRows(empType){
        var saveUrl = (empType == 'employee')? "<?php echo site_url('Employee/pull_employee_group'); ?>" : "<?php echo site_url('Employee/pull_in_charge_group'); ?>";

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: { empList:empTempory_arr, masterID:p_id },
            url: saveUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }else{
                    myAlert(data[0], data[1]);
                    if(empType == 'employee'){
                        load_group_employee();
                    }else{
                        load_in_charge_employee();
                    }

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

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(thisRow.parents('tr')).data();
        var empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function (data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function removeEmployee(empID, removeType){

        swal({
                title: "<?php translate_fn('common_are_you_sure');?>",
                text: "<?php translate_fn('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php translate_fn('common_delete');?>",
                cancelButtonText: "<?php translate_fn('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {empID:empID, masterID:p_id, removeType:removeType},
                    url: "<?php echo site_url('Employee/removeSingle_emp_payrollGroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            myAlert(data[0], data[1]);
                            (removeType == 'employees')? load_group_employee() : load_in_charge_employee();
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

    function removeAll_employee(removeType){

        swal({
                title: "<?php translate_fn('common_are_you_sure');?>",
                text: "<?php translate_fn('common_you_want_to_delete_all');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php translate_fn('common_delete');?>",
                cancelButtonText: "<?php translate_fn('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {masterID:p_id, removeType:removeType},
                    url: "<?php echo site_url('Employee/remove_all_emp_payrollGroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            myAlert(data[0], data[1]);
                            (removeType == 'employees')? load_group_employee() : load_in_charge_employee();
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
</script>
<?php
