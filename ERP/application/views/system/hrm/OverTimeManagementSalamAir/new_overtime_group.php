<!--Translation added by Naseek-->

<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_overtime_group_details');
echo head_page($title, false);

$ot_systemInput = ot_systemInput();
$groupID = $this->input->post('page_id');
$ot_slab = ot_slabDrop_down($groupID);
$designation_arr = getDesignationDrop(true, false);
?>
<style>
    #slab-div, #OT-hours-div{ display: none; }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" onclick="overtime_GroupDetail_table()" data-toggle="tab"><?php echo $this->lang->line('common_details');?><!--Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="overtime_GroupEmployee_table()" data-toggle="tab"><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employee--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary pull-right" onclick="new_overTimeInput()">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_over_time_input_type');?><!--Input Types-->
                </button>
                <input type="text" class="pull-left" id="otGroupDescriptionedit" name="otGroupDescriptionedit">
                <button type="button" style="margin-left: 5px;" class="btn-xs btn btn-success pull-left" onclick="editGroupDescription()">
                    <i class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="overtime_GroupDetail_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th style="width: 100px"><?php echo $this->lang->line('hrms_over_time_hourly_rate');?><!--Hourly Rate--></th>
                    <th style="width: auto"><?php echo $this->lang->line('hrms_over_time_slab');?><!--Slab--></th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary pull-right" onclick="open_add_employees()">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_over_time_add_employee');?><!--Add Employees-->
                </button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="overtime_GroupEmployees_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 10px">#</th>
                   <!-- <th style="width: auto">Group</th>-->
                    <th style="width: 120px"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="width: auto"><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employee--></th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
</div>



<div class="modal fade" id="systemInputModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="salary-cat-title"> <?php echo $this->lang->line('hrms_over_time_ot_group_master_detail');?><!--OT Group Master Detail--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="overTimeInput_forms" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-xs-4" for="systemInputID"> <?php echo $this->lang->line('hrms_over_time_input_type');?><!--Input Type--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8">
                                <select id="systemInputID" name="systemInputID" class="form-control" onchange="showDiv(this)">
                                    <option value=""><?php echo $this->lang->line('hrms_over_time_select_input_type');?><!--Select Input Type--></option>
                                    <?php if(!empty($ot_systemInput)){
                                        foreach($ot_systemInput as $val){
                                            echo '<option value="'.$val['systemInputID'].'" data-input="'.$val['inputType'].'">'.$val['inputDescription'].'</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group sub-div" id="OT-hours-div">
                            <label class="control-label col-sm-4 col-xs-4" for="rate"><?php echo $this->lang->line('hrms_over_time_rate');?><!--Rate--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8">
                                <input type="text" name="rate" id="rate" class="form-control number">
                            </div>
                        </div>
                        <div class="form-group sub-div" id="slab-div">
                            <label class="control-label col-sm-4 col-xs-4" for="slabID"><?php echo $this->lang->line('hrms_over_time_slab');?><!--Slab--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8">
                                <select id="slabID" name="slabID" class="form-control">
                                    <option value=""></option>
                                    <?php if(!empty($ot_slab)){
                                        foreach($ot_slab as $val_slab){
                                            echo '<option value="'.$val_slab['otSlabsMasterID'].'" >'.$val_slab['Description'].'</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="otGroupID" value="<?php echo $groupID; ?>">
                <input type="hidden" id="hiddenID" name="hiddenID" value="">
                <input type="hidden" name="inputType" id="inputType" value="">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveOT_groups()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="sales_target_modal" role="dialog" data-keyboard="false" data-backdrop="static"
     style="z-index: 999999">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-4 col-xs-4 select-container">
                        <label for="segment"> <?php echo $this->lang->line('common_designation');?><!--Designation--> </label>
                        <?php echo form_dropdown('designation[]', $designation_arr, '', 'class="form-control" id="designation" onchange="loaddropdown();"  multiple="multiple"'); ?>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" id="isEmpLoad" value="0">
                    <div class="table-responsive col-md-7">
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" id="selectAllBtn" style="font-size:12px;"
                                    onclick="selectAllRows()"> <?php echo $this->lang->line('common_select_all');?><!--Select All-->
                            </button>
                        </div>
                        <hr style="margin-top: 5%">
                        <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_over_time_emp_code');?><!--EMP Code--></th>
                                <th style="width:auto"><?php echo $this->lang->line('hrms_over_time_employee_name');?><!--Employee Name--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
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
                                    onclick="addAllRows()"> <?php echo $this->lang->line('common_add_all');?><!--Add All-->
                            </button>
                            <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;"
                                    onclick="clearAllRows()"> <?php echo $this->lang->line('common_clear_all');?><!--Clear All-->
                            </button>
                        </div>
                        <hr style="margin-top: 7%">
                        <form id="over_time_slab_Detail_form">
                            <input type="hidden" id="otGroupIDhn" name="otGroupIDhn" value="<?php echo $groupID; ?>">
                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="max-width: 5%"><?php echo $this->lang->line('hrms_over_time_emp_code');?><!--EMP CODE--></th>
                                    <th style="max-width: 95%"><?php echo $this->lang->line('hrms_over_time_emp_name');?><!--EMP NAME--></th>
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
                    <?php echo $this->lang->line('common_Close');?><!--Close-->
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({"bPaginate": false});
    var empTempory_arr = [];
    $(document).ready(function() {
        loadOTGroupDescription();

        $('.headerclose').click(function(){
            fetchPage('system/hrm/OverTimeManagementSalamAir/over-time-group','Test','HRMS');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        overtime_GroupDetail_table();
        overtime_GroupEmployee_table();

        $('#designation').multiselect2({
            enableFiltering: true,
            /* filterBehavior: 'value',*/
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200
        });
    });

    function overtime_GroupDetail_table() {
        $('#overtime_GroupDetail_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/table_overtimeDetail_group'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "otGroupDetailID"},
                {"mData": "inputDescription"},
                {"mData": "hourlyRateStr"},
                {"mData": "slabMasterStr"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "otGroupID","value": '<?php echo $groupID; ?>'});
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

    function saveOT_groups(){
        var formObj = $('#overTimeInput_forms');
        var url = formObj.attr('action');
        var data = formObj.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    overtime_GroupDetail_table();
                    $('#systemInputModal').modal('hide');
                }
            }, error: function () {
                myAlert('e','An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function showDiv(obj){
        var inputType = $('option:selected', obj).attr('data-input');
        $('.sub-div').hide();
        if(inputType==1){
            $('#slab-div').show();
        }
        else{
            $('#OT-hours-div').show();
        }
        $('#inputType').val(inputType);
    }

    function new_overTimeInput(){
        $('#overTimeInput_forms').attr('action', '<?php echo site_url('Salary_category/saveInputRates'); ?>');
        $('.sub-div').hide();
        $('#rate').val('');
        $('#systemInputID').val('');
        $('#systemInputModal').modal('show');
    }

    function edit_overTimeGroupDetail(detailID, systemInputID, hourlyRate, slabMasterID, inputType){
        $('#overTimeInput_forms').attr('action', '<?php echo site_url('Salary_category/editInputRates'); ?>');
        $('#systemInputID').val(systemInputID);
        $('.sub-div').hide();
        if(inputType==1){
            $('#slabID').val(slabMasterID);
            $('#slab-div').show();
        } else{
            $('#rate').val(hourlyRate);
            $('#OT-hours-div').show();
        }
        $('#hiddenID').val(detailID);
        $('#systemInputModal').modal('show');
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


    function overtime_GroupEmployee_table() {
        $('#overtime_GroupEmployees_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/table_overtimeEmployees_group'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "otGroupEmpID"},
                /*{"mData": "otGroupDescription"},*/
                {"mData": "ECode"},
                {"mData": "Ename2"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "otGroupID","value": '<?php echo $groupID; ?>'});
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

    function open_add_employees(){
        $("#sales_target_modal").modal({backdrop: "static"});
        loaddropdown()
    }

    function loaddropdown() {
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/load_dropdown_unassigned_employees'); ?>",
            "aaSorting": [[1, 'asc']],
            "aLengthMenu": [[10, 25, 50, 75, 100,200], [10, 25, 50, 75, 100,200]],
            "iDisplayLength": 200,
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
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "DesDescription"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'otGroupID', 'value': <?php echo $groupID; ?>});
                aoData.push({'name': 'designation', 'value':$('#designation').val()});
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

    function delete_item(id){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'otGroupEmpID':id},
                    url :"<?php echo site_url('Salary_category/delete_ot_group_emp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        overtime_GroupEmployee_table();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
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
            empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + details.last_ocGrade + '">';
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

        var postData = $('#over_time_slab_Detail_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Salary_category/save_assigned_OT_employees'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    clearAllRows();
                    $('#sales_target_modal').modal('hide');
                    overtime_GroupEmployee_table();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_are_you_sure');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function delete_ot_group_detail(id){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'otGroupDetailID':id},
                    url :"<?php echo site_url('Salary_category/delete_ot_group_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        overtime_GroupDetail_table();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function loadOTGroupDescription(){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'otGroupID':<?php echo $groupID; ?>},
            url :"<?php echo site_url('Salary_category/load_ot_group_description'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                $('#otGroupDescriptionedit').val(data['otGroupDescription'])
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function editGroupDescription(){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'otGroupID':<?php echo $groupID; ?>,'otGroupDescription':$('#otGroupDescriptionedit').val()},
            url :"<?php echo site_url('Salary_category/edit_group_description'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
</script>