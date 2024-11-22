<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$employee_arr = all_employees_drop(false);
$segment_arr = fetch_segment(true, false);

$isPendingDataAvailable = 0;
$isAuthenticated = emp_master_authenticate();
if( $isAuthenticated == 0){
    $isPendingDataAvailableData = isPendingDataAvailable();
    $isPendingDataAvailable = count($isPendingDataAvailableData);
}

echo head_page($this->lang->line('emp_employee_employee_master'), true);
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

//$isAuthenticateNeed = emp_master_authenticate();
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
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('emp_segment'); ?>
                        <!--Segment--></label><br>
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment" onchange="fetchEmployees(\'segment\'),loadEmployees()" multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-3" id="employeedrp">
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('emp_employee_name'); ?>
                        <!--Employee Name--></label><br>
                    <?php echo form_dropdown('employeeCode[]', $employee_arr, '', 'class="form-control" id="employeeCode" onchange="fetchEmployees(\'employeeCode\')" multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-2" id="discharged-container">
                    <label for="supplierPrimaryCode">
                        <?php echo $this->lang->line('emp_employee_status'); ?><!--Employee Status--></label><br>
                    <select name="isDischarged" id="isDischarged" class="form-control select2"
                            onchange="fetchEmployees('isDischarged')">
                        <option><?php echo $this->lang->line('emp_all'); ?><!--All--></option>
                        <option value="Y"><?php echo $this->lang->line('emp_discharged'); ?><!--Discharged--></option>
                        <option value="N" selected="selected"><?php echo $this->lang->line('emp_active'); ?><!--Active--></option>
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style="margin-top: 7%;">
                        <i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
                    </button>
                </div>
            </div>

            <div class="row">
                <fieldset>
                    <legend><?php echo $this->lang->line('emp_employee_columns');?><!--Columns--><?php //echo $this->lang->line('emp_designation'); ?></legend>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EmpSecondaryCode|Employee Code"
                                       checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('emp_employee_code');?>" readonly><!--Employee Code-->
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="ECode|Secondary Code" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('emp_secondary_code');?>" readonly><!--Secondary Code-->
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Ename2|Employee Name" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('emp_employee_name');?>" readonly><!--Employee Name-->
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="DesDescription|Designation" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('common_designation');?>" readonly><!--Designation-->
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="segment|Segment" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('common_segment');?>" readonly><!--Segment-->
                            </div>
                        </div>
                        <div class="form-group col-sm-3 col-xs-6">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpTelephone|Employee Tel" checked="">
                            </span>
                                <input type="text" name="header[]" class="form-control" value="<?php echo $this->lang->line('emp_employee_employee_tel');?>" readonly><!--Employee Tel-->
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-5">&nbsp;</div>
        <div class="col-md-3 pull-right">
            <a href="#" type="button" class="btn btn-success btn-sm pull-right" onclick="excelDownload()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <?php //if($isAuthenticateNeed == 0){?>
            <button style="margin-right: 2px;" type="button" class="btn btn-primary btn-sm pull-right"
                onclick="fetchPage('system/hrm/employee_create','','HRMS', '', '', '<?php echo $page_url; ?>')" >
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('emp_employee_create_new'); ?><!-- Create New-->
            </button>
            <?php //} ?>
        </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table id="employeeTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 10px">#</th>
                <th style="width: 30px"></th>
                <th style="width: 120px;"><?php echo $this->lang->line('emp_employee_code'); ?><!--Code--></th>
                <th style="width: 110px;">
                    <?php echo $this->lang->line('emp_secondary_code'); ?><!--Secondary Code--></th>
                <th style="width: 190px;"><?php echo $this->lang->line('emp_employee_name'); ?><!--Employee Name--></th>
                <th style="width: 164px"><?php echo $this->lang->line('emp_designation'); ?><!--Designation--></th>
                <th style="width: 164px"><?php echo $this->lang->line('emp_segment'); ?><!--Segment--></th>
                <th style="width: 150px">
                    <?php echo $this->lang->line('emp_employee_employee_tel'); ?><!--Employee Tel--></th>
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
        var isPendingDataAvailable = '<?php echo $isPendingDataAvailable ?>';

        $(document).ready(function () {

            $('.headerclose').click(function () {
                fetchPage('system/hrm/employee_master', 'Test', 'HRMS');
            });

            if(isPendingDataAvailable > 0 ){
                $('.page-minus').before('<button class="btn btn-box-tool" style=""><i class="fa fa-bell" aria-hidden="true" onclick="openPendingDataModal()"></i></button>');
            }

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


            if (localStorage.isDischarged != null || localStorage.isDischarged != undefined) {
                $('#isDischarged').val(localStorage.isDischarged).change();
            }
            else{
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

            fetchEmployees();
        });

        function fetchEmployees(name) {
            if (name != undefined) {
                window.localStorage.setItem(name, $('#' + name).val());
            }

            $('#employeeTB').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_employees'); ?>",
                "aaSorting": [[2, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [1, 7]}],
                /*"language": {
                 processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                 },*/
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
                    {"mData": "img"},
                    {"mData": "ECode"},
                    {"mData": "EmpSecondaryCode"},
                    {"mData": "Ename2"},
                    {"mData": "DesDescription"},
                    {"mData": "segment"},
                    {"mData": "EpTelephone"},
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
            fetchPage('system/hrm/employee_create', empID, 'HRMS', '', '', '<?php echo $page_url; ?>');
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
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });

        function loadEmployees() {
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


                    var storedEmp = localStorage.employeeCode;
                    if (storedEmp != null || storedEmp != undefined) {
                        var storedEmpArray = storedEmp.split(',');
                        $('#employeeCode').val(storedEmpArray).multiselect2("refresh");
                    }

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function excelDownload() {
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
            fetchEmployees();
        }

        function callOTable(name){
            fetchEmployees(name);
        }
    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-30
 * Time: 4:12 PM
 */