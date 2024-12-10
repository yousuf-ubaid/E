<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$employee_arr = all_employees_drop(false);
$segment_arr = fetch_segment(true,false);

echo head_page($this->lang->line('emp_employee_employee_master'), true);
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
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('emp_segment');?> <!--Segment--></label><br>
                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment" onchange="fetchEmployees(),loadEmployees()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-3" id="employeedrp">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('emp_employee_name');?> <!--Employee Name--></label><br>
                <?php echo form_dropdown('employeeCode[]', $employee_arr, '', 'class="form-control" id="employeeCode" onchange="fetchEmployees()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2" id="discharged-container">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('emp_employee_status');?><!--Employee Status--></label><br>
                <select name="isDischarged" id="isDischarged" class="form-control select2" onchange="fetchEmployees()">
                    <option><?php echo $this->lang->line('emp_all');?><!--All--></option>
                    <option value="Y"><?php echo $this->lang->line('emp_discharged');?><!--Discharged--></option>
                    <option value="N"><?php echo $this->lang->line('emp_active');?><!--Active--></option>
                </select>
            </div>
        </div>

        <div class="row">
            <fieldset>
                <legend>Columns<?php //echo $this->lang->line('emp_designation'); ?></legend>
                <div class="form-group col-sm-12">
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EmpSecondaryCode|Employee Code"   checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Employee Code" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="ECode|Secondary Code"   checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Secondary Code" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="Ename2|Employee Name"  checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Employee Name" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="DesDescription|Designation"  checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Designation" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-12">
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="segment|Segment"  checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Segment" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="columns[]" value="EpTelephone|Employee Tel"  checked="">
                            </span>
                            <input type="text" name="header[]" class="form-control" value="Employee Tel" readonly>
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
        <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/hrm/employee_create_arb','','HRMS', '', '', '<?php echo $page_url; ?>')"
                class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('emp_employee_create_new');?><!-- Create New-->
        </button>
    </div>
</div>
<hr>

<div class="table-responsive">
    <table id="employeeTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 10px">#</th>
            <th style="width: 30px"></th>
            <th style="width: 120px;"><?php echo $this->lang->line('emp_employee_code');?><!--Code--></th>
            <th style="width: 110px;"><?php echo $this->lang->line('emp_secondary_code');?><!--Secondary Code--></th>
            <th style="width: 190px;"><?php echo $this->lang->line('emp_employee_full_name');?><!--Employee Full Name--></th>
            <th style="width: 164px"><?php echo $this->lang->line('emp_designation');?><!--Designation--></th>
            <th style="width: 164px"><?php echo $this->lang->line('emp_segment');?><!--Segment--></th>
            <th style="width: 150px"><?php echo $this->lang->line('emp_employee_employee_tel');?><!--Employee Tel--></th>
            <th style="width: 40px"></th>
        </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/employee_master_arb', 'Test', 'HRMS');
        });
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
    });

    function fetchEmployees() {
        $('#employeeTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_employees'); ?>",
            "aaSorting": [[2, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [1,7]}],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                /*if (oSettings.bSorted || oSettings.bFiltered) {
                 for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                 $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                 if( parseInt(oSettings.aoData[i]._aData['EIdNo']) == selectedRowID ){
                 var thisRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
                 $(thisRow).addClass('dataTable_selectedTr');
                 }
                 }
                 }*/

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
                aoData.push({"name": "employeeCode", "value": $("#employeeCode").val()});
                aoData.push({"name": "segment", "value": $("#segment").val()});
                aoData.push({"name": "isDischarged", "value": $("#isDischarged").val()});
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
        fetchPage('system/hrm/employee_create_arb', empID, 'HRMS', '', '', '<?php echo $page_url; ?>');
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function loadEmployees(){
        var segmentID =$('#segment').val();
       if(segmentID!=null){
           var id=0;
           $.ajax({
               async: true,
               type: 'post',
               dataType: 'html',
               data: {'segmentID': segmentID,'id':id},
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
               }, error: function () {
                   myAlert('e', 'An Error Occurred! Please Try Again.');
                   stopLoad();
               }
           });
       }else {
           var id=1;
           $.ajax({
               async: true,
               type: 'post',
               dataType: 'html',
               data: {'id': id},
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
               }, error: function () {
                   myAlert('e', 'An Error Occurred! Please Try Again.');
                   stopLoad();
               }
           });
       }

    }

    function excelDownload(){
        var form = document.getElementById('filterForm');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#filterForm').serializeArray();
        form.action = '<?php echo site_url('Employee/export_excel'); ?>';
        form.submit();
    }
</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-30
 * Time: 4:12 PM
 */