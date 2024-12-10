<?php echo head_page('Employee Master', true);
$employee_arr = all_employees_drop(false);
$segment_arr = fetch_segment(true,false);

$isPendingDataAvailable = 0;
$isAuthenticated = emp_master_authenticate();
if( $isAuthenticated == 0){
    $isPendingDataAvailableData = isPendingDataAvailable();
    $isPendingDataAvailable = count($isPendingDataAvailableData);
}
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

    <style>
        fieldset {
            border: 1px solid silver;
            border-radius: 5px;
            padding: 1%;
            padding-bottom: 15px;
            margin: 10px 15px;
        }

        legend {
            width: auto;
            border-bottom: none;
            margin: 0px 10px;
            font-size: 20px;
            font-weight: 500
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel">
        <form id="filterForm">
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="supplierPrimaryCode"> Segment</label><br>
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment" onchange="loadEmployees(), callOTable(\'segment\')"
                    multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-3" id="employeedrp">
                    <label for="supplierPrimaryCode"> Employee Name</label><br>
                    <?php echo form_dropdown('employeeCode[]', $employee_arr, '', 'class="form-control" id="employeeCode" onchange="callOTable(\'employeeCode\')" multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-2" id="discharged-container">
                    <label for="supplierPrimaryCode">Employee Status</label><br>
                    <select name="isDischarged" id="isDischarged" class="form-control select2" onchange="callOTable('isDischarged')">
                        <option>All</option>
                        <option value="Y">Discharged</option>
                        <option value="N" selected="selected">Active</option>
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style="margin-top: 7%;">
                        <i class="fa fa-paint-brush"></i> Clear
                    </button>
                </div>
            </div>

            <div class="row">
                <fieldset>
                    <legend>Columns <?php //echo $this->lang->line('emp_designation'); ?></legend>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EmpSecondaryCode|Employee Number"  checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Employee Number" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="TitleDescription|Title" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Title" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EmpShortCode|Calling Name" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Calling Name" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Ename3|Surname" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Surname" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Ename2|Names with Initials" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Names with Initials" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Ename1|Employee Full name" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Employee Full name" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="NIC|NIC Number" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="NIC Number" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EDOB|Birthday" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Birthday" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Gender|Gender" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Gender" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="CountryDes|Nationality" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Nationality" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="BloodDescription|Blood Group" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Blood Group" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="empMaritialStatus|Marital Status" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Marital Status" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="DesDescription|Designation" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Designation" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="managerCode|Reporting Manager ID" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Reporting Manager ID" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="segment|Segment" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Segment" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EDOJ|Join Date" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Join Date" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="empConfirmedYN|Confirmed" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Confirmed" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="employeeType|Employment Type" checked="false">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Employment Type" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="isDischarged|Status" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Status" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="contractStartDate|Contract Start Date" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Contract Start Date" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="contractEndDate|Contract End Date" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Contract End Date" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="socialInsuranceNumber|EPF Number" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="EPF Number" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpAddress1|House No" checked="false">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="House No" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpAddress2|Street" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Street" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpAddress3|City/Town" checked="false">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="City/Town" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EcPOBox|Postal Code" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Postal Code" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpTelephone|Personal Telephone" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Personal Telephone" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EcMobile|Personal Mobile" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Personal Mobile" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="CountryDes|Country" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="Country" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
    <div class="row pc-1">
        <div class="col-md-5">&nbsp;</div>
        <div class="col-md-3 pull-right">
            <a href="#" type="button" class="btn btn-success-new size-sm pull-right" onclick="excelDownload()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/hrm/employee_create_envoy','','HRMS', '', '', '<?php echo $page_url; ?>')"
                    class="btn btn-primary-new size-sm pull-right">
                <i class="fa fa-plus"></i> Create New
            </button>
        </div>
    </div>
   

    <div>
        <table id="employeeTB_envoy" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 10px">#</th>    
                <th style="width: auto">Photo</th>
                <th style="width: auto">Employee&nbsp;Number</th>
                <th style="width: auto">Employee&nbsp;Number</th>
                <th style="width: auto;">Title</th>
                <th style="width: auto;">Calling&nbsp;Name</th>
                <th style="width: auto">Surname</th>
                <th style="width: auto">Names&nbsp;with&nbsp;Initials</th>
                <th style="width: auto">Employee&nbsp;Full&nbsp;name</th>
                <th style="width: auto">NIC&nbsp;Number</th>
                <th style="width: auto">Birthday</th>
                <th style="width: auto">Gender</th>
                <th style="width: auto">Nationality</th>
                <th style="width: auto">Blood&nbsp;Group</th>
                <th style="width: auto">Marital&nbsp;Status</th>
                <th style="width: auto">Designation</th>
                <th style="width: auto">Reporting&nbsp;Manager ID</th>
                <th style="width: auto">Segment</th>
                <th style="width: auto">Join&nbsp;Date</th>
                <th style="width: auto">Confirmed</th>
                <th style="width: auto">Employment&nbsp;Type</th>
                <th style="width: auto">Status</th>
                <th style="width: auto">Contract&nbsp;Start&nbsp;Date</th>
                <th style="width: auto">Contract&nbsp;End&nbsp;Date</th>
                <th style="width: auto">EPF&nbsp;Number</th>
                <th style="width: auto">House&nbsp;No</th>
                <th style="width: auto">Street</th>
                <th style="width: auto">City/Town</th>
                <th style="width: auto">Postal&nbsp;Code</th>
                <th style="width: auto">Personal&nbsp;Telephone</th>
                <th style="width: auto">Personal&nbsp;Mobile</th>
                <th style="width: auto">Country</th>
                <th style="width: 40px"></th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="approval-pending-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Pending Employee Data</h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <table class="<?php echo table_class() ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('emp_employee_name'); ?></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            if(!empty($isPendingDataAvailableData)){
                                $l = 1;
                                foreach ($isPendingDataAvailableData as $emp){
                                    echo '<tr>
                                           <td style="width: 25px; text-align: right">'.$l.'</td> 
                                           <td>'.$emp['empShtrCode'].' - '.$emp['Ename2'].'</td> 
                                           <td style="width: 60px"> <button onclick="edit_empDet_with_pending_approval('.$emp['EIdNo'].')">Load</button></td> 
                                       </tr>';
                                    $l++;
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">

    var oTable;
    var isPendingDataAvailable = '<?php echo $isPendingDataAvailable ?>';

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/employee_masters_envoy', 'Test', 'HRMS');
        });

        if(isPendingDataAvailable > 0 ){
            $('.page-minus').before('<button class="btn btn-box-tool" style=""><i class="fa fa-bell" aria-hidden="true" onclick="openPendingDataModal()"></i></button>');
        }

        fetchEmployees();

        $('#employeeCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#segment').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.select2').select2();


        if (localStorage.isDischarged != undefined) {
            if( localStorage.isDischarged != 'null' ){
                $('#isDischarged').val(localStorage.isDischarged).change();
            }else{
                $('#isDischarged').val('N').change();
            }
        }else{
            $('#isDischarged').val('N').change();
        }

        var storedEmp = localStorage.employeeCode;
        var storedSegments = localStorage.segment;

        if (storedSegments != undefined) {
            if(storedSegments != 'null'){
                var storedSegmentsArray = storedSegments.split(',');
                $('#segment').multiselect2('select', storedSegmentsArray).multiselect2("refresh");

                if (storedEmp == 'null' || storedEmp == undefined) {
                    loadEmployees();
                }
            }
        }

        if (storedEmp != undefined) {
            if (storedSegments != 'null' && storedSegments != undefined && storedEmp != 'null') {
                loadEmployees();
            }
            else{
                var storedEmpArray = storedEmp.split(',');
                $('#employeeCode').val(storedEmpArray).multiselect2("refresh");
            }
        }

    });

    function fetchEmployees() {
        oTable = $('#employeeTB_envoy').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "scrollX": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_employeeEnvoy'); ?>",
            "aaSorting": [[2, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0,6]},{ "bVisible": false, "aTargets": [2] }],
            "fixedColumns":   {
                left: 2
            },
            /*"search": {
                "caseInsensitive": false
            },*/
            "scrollCollapse": true,
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if (parseInt(oSettings.aoData[x]._aData['EIdNo']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "EIdNo"},                
                {"mData": "EmpImage"},
                {"mData": "secondaryCodeStr"},
                {"mData": "EmpSecondaryCode"},
                {"mData": "TitleDescription"},
                {"mData": "EmpShortCodeStr"},
                {"mData": "Ename3"},
                {"mData": "Ename2"},
                {"mData": "Ename1"},
                {"mData": "NIC"},
                {"mData": "dob"},
                {"mData": "genderStr"},
                {"mData": "CountryDes"},
                {"mData": "BloodDescription"},
                {"mData": "empMaritialStatus"},
                {"mData": "DesDescription"},
                {"mData": "managerCode"},
                {"mData": "segment"},
                {"mData": "doj"},
                {"mData": "confirmedStr"},
                {"mData": "employeeType"},
                {"mData": "empStatus"},
                {"mData": "contractStart"},
                {"mData": "contractEnd"},
                {"mData": "socialInsuranceNumber"},
                {"mData": "EpAddress1"},
                {"mData": "EpAddress2"},
                {"mData": "EpAddress3"},
                {"mData": "EcPOBox"},
                {"mData": "EpTelephone"},
                {"mData": "EcMobile"},
                {"mData": "CountryDes"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "employeeCode", "value": localStorage.employeeCode});
                aoData.push({"name": "segment", "value": localStorage.segment});
                aoData.push({"name": "isDischarged", "value": localStorage.isDischarged});

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

    function edit_empDet(empID) {
        fetchPage('system/hrm/employee_create_envoy', empID, 'HRMS', '', '', '<?php echo $page_url; ?>');
    }

    function edit_empDet_with_pending_approval(empID){
        $('#approval-pending-modal').modal('hide');
        setTimeout(function(){
            edit_empDet(empID);
        }, 300);
    }

    function openPendingDataModal(){
        $('#approval-pending-modal').modal('show');
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        /*$('.table-row-select tr').removeClass('dataTable_selectedTr, selected');
        $(this).toggleClass('dataTable_selectedTr, selected');*/
        $('.table-row-select tr').removeClass('selected');
        $(this).toggleClass('selected');
    });

    function loadEmployees(){
        var segmentID = $('#segment').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'segmentID': segmentID},
            url: '<?php echo site_url("Employee/loadEmployees"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#employeedrp').html(data);
                $('#employeeCode').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    buttonWidth: '180px',
                    maxHeight: '30px'
                });

                //if(isInitial == 1){
                    var storedEmp = localStorage.employeeCode;
                    if (storedEmp != null || storedEmp != undefined) {
                        var storedEmpArray = storedEmp.split(',');
                        $('#employeeCode').val(storedEmpArray).multiselect2("refresh");
                    }
                //}
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function excelDownload(){
        var form = document.getElementById('filterForm');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#filterForm').serializeArray();
        form.action = '<?php echo site_url('Employee/export_excel'); ?>';
        form.submit();
    }

    function clear_all_filters() {
        $('#isDischarged').val("");
        $('#segment').multiselect2('deselectAll', false);
        $('#segment').multiselect2('updateButtonText');
        $('#employeeCode').multiselect2('deselectAll', false);
        $('#employeeCode').multiselect2('updateButtonText');
        window.localStorage.removeItem("isDischarged");
        window.localStorage.removeItem("employeeCode");
        window.localStorage.removeItem("segment");

        callOTable();
    }

    function callOTable(name=''){
        if(name != ''){
            window.localStorage.setItem(name, $('#' + name).val());
        }

        oTable.draw();
    }
</script>


<?php
