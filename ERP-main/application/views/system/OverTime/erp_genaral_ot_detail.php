<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->lang->line('hrms_attendance_summary'), false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$segment_arr = fetch_segment(true, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <button type="button" style="margin-right: 15px"  class="btn btn-primary btn-sm pull-right"
            onclick="openEmployeeModal()"><?php echo $this->lang->line('hrms_attendance_add_employees'); ?><!-- Add Employees-->
    </button>
</div>
<hr>
<div class="row" >
    <div class="col-sm-12" id="over_time_template_table" >

    </div>
</div>

<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"
     style="z-index: 999999">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_employees') ?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="isEmpLoad" value="0">
                    <div class="table-responsive col-md-7">
                        <div class="pull-left" id="segment-container">
                            <label for="segmentID" class=""><?php echo $this->lang->line('common_segment') ?><!--Segment--></label>
                            <?php echo form_dropdown('segmentID[]', $segment_arr, '', ' class="form-control" onchange="load_employeeForModal()" multiple="multiple" id="segmentID" '); ?>
                            <!--onchange="load_template()"-->
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" id="selectAllBtn" style="font-size:12px;"
                                    onclick="selectAllRows()"><?php echo $this->lang->line('common_select_all') ?> <!--Select All-->
                            </button>
                        </div>
                        <hr style="margin-top: 5%">
                        <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_employee_name'); ?><!--Employee Name--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_designation'); ?><!--Designation--></th>
                                <th style="width: 5%">
                                    <div id="dataTableBtn"></div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="table-responsive col-md-5">
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;"
                                    onclick="addAllRows()"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('hrms_attendance_add_employees'); ?><!--Add Employees-->
                            </button>
                            <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;"
                                    onclick="clearAllRows()"> <!--Clear All--><?php echo $this->lang->line('common_clear_all'); ?>
                            </button>
                        </div>
                        <hr style="margin-top: 7%">
                        <form id="tempTB_form">

                            <input type="hidden" name="generalOTMasterID" id="generalOTMasterID" value="<?php echo trim($this->input->post('page_id')) ?>">
                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="max-width: 5%"><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP CODE--></th>
                                    <th style="max-width: 95%"><?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--EMP NAME--></th>
                                    <th>
                                        <div id="removeBtnDiv"></div>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var Otable;
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({"bPaginate": false});
    var empTempory_arr = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/OverTime/erp_genaral_ot_template', 'Test', 'Attendance Summary');
        });
        over_time_templates();

    });

    function over_time_templates() {
        var generalOTMasterID = $('#generalOTMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {generalOTMasterID: generalOTMasterID, All: 'true'},
            url: "<?php echo site_url('OverTime/fetch_over_time_templates'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#over_time_template_table').html(data);
                //$('.select2').select2();
            }, error: function () {

            }
        });
    }

    function openEmployeeModal() {
        $('#employee_model').modal('show');
        load_employeeForModal();
    }

    function load_employeeForModal() {
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('OverTime/getEmployeesDataTableShift'); ?>",
            "aaSorting": [[1, 'asc']],
            aLengthMenu: [
                [25, 50, 100, 200,500, -1],
                [25, 50, 100, 200,500, "All"]
            ],
            iDisplayLength: -1,
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

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
                {"mData": "DesDescription"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'generalOTMasterID', 'value': $('#generalOTMasterID').val()});
                aoData.push({"name": "segmentID", "value": $("#segmentID").val()});
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

    function selectAllRows() {
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="empHiddenID[]" class="modal_empID" value="' + empID + '">';
                empDet1 += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + data.last_ocGrade + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTempory_arr.push(empID);
            }
        });
    }

    function addTempTB(det) {

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(thisRow.parents('tr')).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="empHiddenID[]"  class="modal_empID" value="' + empID + '">';
            //empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + details.last_ocGrade + '">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet,
                3: empID
            }]).draw();

            empTempory_arr.push(empID);
        }

    }

    function clearAllRows() {
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function removeTempTB(det) {
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(thisRow.parents('tr')).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function (data) {
            return parseInt(data) != empID
        });

        table.row(thisRow.parents('tr')).remove().draw();
    }

    function addAllRows() {

        var postData = $('#tempTB_form').serializeArray();
        $('#addAllBtn').attr('disabled',true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('OverTime/add_employees_to_ot'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#employee_model').modal('hide');
                    $('#addAllBtn').attr('disabled',false);
                    clearAllRows();
                    over_time_templates();
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    var segmentDrop = $('#segmentID');

    segmentDrop.multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 1
    });





</script>