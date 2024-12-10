<?php echo head_page('Employee Master', true);
$employee_arr = all_employees_drop(false);
$segment_arr = fetch_segment(true, false);
?>

    <div id="filter-panel" class="collapse filter-panel">
        <!-- <div class="row">
            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode"> Employee Name</label><br>
                <?php /*echo form_dropdown('employeeCode[]', $employee_arr, '', 'class="form-control" id="employeeCode" onchange="fetchEmployees()" multiple="multiple"'); */ ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode"> Segment</label><br>
                <?php /*echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment" onchange="fetchEmployees()" multiple="multiple"'); */ ?>
            </div>
        </div>-->
        <div class="row">
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col1" onclick="loadTableColoum(1)" checked>Employee
                    Number</label>
                <label class="col-sm-3"><input type="checkbox" id="col2" onclick="loadTableColoum(2)" checked>
                    Title</label>
                <label class="col-sm-3"><input type="checkbox" id="col3" onclick="loadTableColoum(3)" checked> Calling
                    Name</label>
            </div>
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col4" onclick="loadTableColoum(4)" checked>
                    Surname</label>
                <label class="col-sm-3"><input type="checkbox" id="col5" onclick="loadTableColoum(5)" checked> Names
                    with Initials</label>
                <label class="col-sm-3"><input type="checkbox" id="col6" onclick="loadTableColoum(6)" checked> Employee
                    Full name</label>
                <label class="col-sm-3"><input type="checkbox" id="col7" onclick="loadTableColoum(7)" checked> NIC
                    Number</label>
            </div>
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col8" onclick="loadTableColoum(8)" checked> Birthday</label>
                <label class="col-sm-3"><input type="checkbox" id="col9" onclick="loadTableColoum(9)" checked> Gender</label>
                <label class="col-sm-3"><input type="checkbox" id="col10" onclick="loadTableColoum(10)" checked>
                    Nationality</label>
                <label class="col-sm-3"><input type="checkbox" id="col11" onclick="loadTableColoum(11)" checked> Blood
                    Group</label>
            </div>
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col12" onclick="loadTableColoum(12)" checked> Marital
                    Status</label>
                <label class="col-sm-3"><input type="checkbox" id="col13" onclick="loadTableColoum(13)" checked>
                    Designation</label>
                <label class="col-sm-3"><input type="checkbox" id="col14" onclick="loadTableColoum(14)" checked>
                    Immediate Manager EMP
                    Number</label>
                <label class="col-sm-3"><input type="checkbox" id="col15" onclick="loadTableColoum(15)" checked> Cost
                    Centre</label>
            </div>
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col16" onclick="loadTableColoum(16)" checked> Join
                    Date</label>
                <label class="col-sm-3"><input type="checkbox" id="col17" onclick="loadTableColoum(17)" checked>
                    Employment Type</label>
                <label class="col-sm-3"><input type="checkbox" id="col18" onclick="loadTableColoum(18)" checked>
                    Personal Telephone</label>
                <label class="col-sm-3"><input type="checkbox" id="col19" onclick="loadTableColoum(19)" checked>
                    Personal Mobile</label>
            </div>
            <div class="form-group col-sm-12">
                <label class="col-sm-3"><input type="checkbox" id="col20" onclick="loadTableColoum(20)" checked> Country</label>
                <label class="col-sm-3"><input type="checkbox" id="col21" onclick="loadTableColoum(21)" checked>
                    Personal Email</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <!--<table class="table table-bordered table-striped table-condensed ">
                <tbody><tr>
                    <td>
                        <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Confirmed /Approved
                    </td>
                    <td>
                        <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Not Confirmed/ Not Approved
                    </td>
                    <td>
                        <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Refer-back
                    </td>
                </tr>
                </tbody>
            </table>-->
        </div>
        <div class="col-md-3 pull-right">
            <a href="<?php echo site_url('Employee/export_excel'); ?>" type="button"
               class="btn btn-success btn-sm pull-right">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/hrm/employee_create','','HRMS')"
                    class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-plus"></i> Create New
            </button>
        </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table id="employeeTBtemplate1" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width:10px">#</th>
                <th style="width:40px">Employee Number</th>
                <th style="width:40px">Title</th>
                <th style="width:40px">Calling Name</th>
                <th style="width:40px">Surname</th>
                <th style="width:40px">Names with Initials</th>
                <th style="width:40px">Employee Full name</th>
                <th style="width:40px">NIC Number</th>
                <th style="width:40px">Birthday</th>
                <th style="width:40px">Gender</th>
                <th style="width:40px">Nationality</th>
                <th style="width:40px">Blood Group</th>
                <th style="width:40px">Marital Status</th>
                <th style="width:40px">Designation</th>
                <th style="width:40px">Immediate Manager EMP Number</th>
                <th style="width:40px">Cost Centre</th>
                <th style="width:40px">Join Date</th>
                <th style="width:40px">Employment Type</th>
                <th style="width:40px">Personal Telephone</th>
                <th style="width:40px">Personal Mobile</th>
                <th style="width:40px">Country</th>
                <th style="width:40px">Personal Email</th>
                <th style="width:10px">Action</th>
            </tr>
            </thead>
        </table>
    </div>

    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            fetchEmployees();
        });
        function loadTableColoum(id) {
            if ($('#col' + id).prop("checked") == true) {
                Otable.column( id ).visible( true );
                //$('.col' + id).removeClass("hidden");
            } else {
                Otable.column( id ).visible( false );
                //$('.col' + id).addClass("hidden");
            }
        }

        function fetchEmployees() {
            Otable = $('#employeeTBtemplate1').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_employees_template1'); ?>",
                "aaSorting": [[0, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [22]}],
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
                    {"mData": "ECode"},
                    {"mData": "title"},
                    {"mData": "EmpShortCode"},
                    {"mData": "Ename3"},
                    {"mData": "Ename2"},
                    {"mData": "Ename1"},
                    {"mData": "NIC"},
                    {"mData": "EDOB"},
                    {"mData": "gender"},
                    {"mData": "Nationality"},
                    {"mData": "BloodDescription"},
                    {"mData": "MaritialStatus"},
                    {"mData": "DesDescription"},
                    {"mData": "managerName"},
                    {"mData": "segment"},
                    {"mData": "EDOJ"},
                    {"mData": "employeeType"},
                    {"mData": "EpTelephone"},
                    {"mData": "EcMobile"},
                    {"mData": "CountryDes"},
                    {"mData": "EEmail"},
                    {"mData": "action"}
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

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-30
 * Time: 4:12 PM
 */