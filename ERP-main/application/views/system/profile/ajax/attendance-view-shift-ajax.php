<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine_attendance_management');

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$week_arr = fetch_weekDays();
$shifts = fetch_shifts(1);


?>
<style>
    .attendanceReview .table>tbody>tr>td {
        padding: 4px;
    }

    #attendanceReview tr:hover > td {
        background: rgba(14, 191, 70, 0.31) !important;
    }

    #attendanceReview thead tr {
        background: rgb(178, 203, 230);
    }

    #attendanceReview table {
        border-collapse: separate;
        border-spacing: 0 5px;
        padding: 2px;
        line-height: 2;
        padding-left: 5px;
    }

    #attendanceReview thead th {
        background-color: rgb(197, 215, 253);;
    }


    .inputdisabled{
        background-color: white;
    }
    .trInputs {
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
        border: 0px solid #ccc;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="row">
    <div class="col-sm-4">
        <table class="table table-bordered table-striped table-condensed table-row-select" style="">
            <!-- <tbody>
            <tr>
                <td>
                    <label style="margin: 2px;">   <i class="fa fa-filter"></i> -->  <?php //echo  $this->lang->line('common_filter')?> <!--Filter--></label>
                <!-- </td>
                <td> -->
                    <!-- <input class="prod_capacity" checked rel="approvedYN1" type="checkbox" value="approvedYN1">
                    <label for="filter_1"></label><i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> --> <?php //echo  $this->lang->line('common_approved')?><!--Approved-->
                <!-- </td>
                <td> -->
                    <!-- <input class="prod_capacity" checked rel="approvedYN2" type="checkbox" value="approvedYN2">
                    <label for="filter_2"></label><i class="fa fa-times" style="color: #990000" aria-hidden="true"></i> --> <?php //echo  $this->lang->line('common_not_approved')?><!--Not Approved-->
                <!-- </td>
            </tr>
            </tbody> -->
        </table>
    </div>
    <div class="col-sm-6">&nbsp;</div>
    <div class="col-sm-2 pull-right">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>
                <td>
                    <span class="label" style="">&nbsp;</span>&nbsp;<button class="btn btn-success" onclick="openEmployeeModal()"> Add Employee</button>&nbsp; <!--Shift Weekend-->
                </td>
            </tr>
        </table>
    </div>
</div>
<br>
<span class="empDetail"></span>
<div style="max-height: 400px">
    <table id="employee_shift_tbl" class="table first attendanceReview">
        <thead>
        <tr style="white-space: nowrap">
            <th style=""><?php echo $this->lang->line('common_status'); ?><!-- Status --></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--Emp Name--></th>`
            <th style=""><?php echo $this->lang->line('hrms_attendance_shift'); ?><!--Emp Name--></th>`
            <th style=""><?php echo $this->lang->line('common_start_date'); ?><!--Date--></th>
            <th style=""><?php echo $this->lang->line('common_end_date'); ?><!--Date--></th>
            <th style=""><?php echo $this->lang->line('common_action'); ?><!--Date--></th>
        </tr>

        </thead>
        <tbody id="StatusTable">
        <?php
        if (!empty($att_rec)) {

            foreach($att_rec as $record){
                $temp = ($state == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
                echo '<tr>';
                echo '<td>'.$temp.'</td>';
                echo '<td><span> '.$record['ECode'].' </span> </td>';
                echo '<td><span> '.$record['Ename1'].' </span> </td>';
                echo '<td><span> '.$record['Description'].' </span> </td>';
                echo '<td><span> '.$record['startDate'].' </span> </td>';
                echo '<td><span> '.$record['endDate'].' </span> </td>';
                echo '<td><span> <button class="btn btn-danger" onclick="deleteShift('.$record['autoID'].')"><i class="fa fa-trash"></i></button> </span> </td>';
                echo '</tr>';
            }
           
        }else{
            echo '<tr><td >No records found.</td></tr>';
        } ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="false">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_employees');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="isEmpLoad" value="0">
                    <div class="table-responsive col-md-7">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <button class="btn btn-primary btn-sm" id="selectAllBtn" style="font-size:12px;"
                                        onclick="selectAllRows()"> <?php // echo $this->lang->line('common_select_all');?>
                                </button> -->
                                <label for="inputData" class="col-md-3 control-label"><?php echo 'Shift to Assign';?></label>
                                <div class="col-md-6" id="">
                                    <?php
                                        echo form_dropdown('shiftID[]', $shifts, '', 'id="shiftID" onchange="shift_employee(this)" class="form-control mid-width wrapItems"');
                                    ?>
                                </div>

                            </div>
                        </div>
                     
                        <hr style="margin-top: 5%">
                        <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP Code--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
                                <th style="width: 5%">
                                    <div id="dataTableBtn"></div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class=" col-md-5">
                        <div class="pull-right">
                            <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;"
                                    onclick="clearAllRows()"> <?php echo $this->lang->line('common_clear_all');?><!--Clear All-->
                            </button>
                        </div>
                        <hr style="margin-top: 7%">
                        <form id="tempTB_form">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?php echo $this->lang->line('common_start_date');?><!--Start Date--> </label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="startDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="startDate"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label><?php echo $this->lang->line('common_end_date');?><!--End Date--> </label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="endDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="endDate"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="shieftIDhn" id="shieftIDhn" value=""/>
                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="max-width: 5%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP CODE--></th>
                                    <th style="max-width: 95%"><?php echo $this->lang->line('hrms_attendance_employee_name');?><!--EMP NAME--></th>
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
                <button class="btn btn-success btn-sm" id="addAllBtn" style="font-size:12px;"
                        onclick="addAllRows()"> <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">
                    <?php echo $this->lang->line('common_Close');?><!--Close-->
                </button>
            </div>
        </div>
    </div>
</div>
<span class="empDetail"></span>

<script>
     var empTempory_arr = [];
     var tempTB = $('#tempTB').DataTable({"bPaginate": false});
     var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    
     $('.select2').select2();

     $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {

        });

    $('.timeTxt').timepicker({

        defaultTime: false, showMeridian: true
    }).on('changeTime.timepicker', function (e) {

        value = e.time.value;
        trID = $(this).closest('tr').attr('data-id');
        masterID = $(this).closest('tr').attr('data-masterid');
        name = $(this).attr('name');

        updatefields(trID, masterID, value, name);
    });

    $(".timeTxt").change(function () {
        var input = $(this);
        if (input.val() == '') {
            trID = input.closest('tr').attr('data-id');
            masterID = input.closest('tr').attr('data-masterid');
            name = input.attr('name');
            value = input.val();

            updatefields(trID, masterID, value, name);
        }

    });

    $(".wrapper").click(function(e) {
        if (e.target.id == "attendanceReview" || $(e.target).parents("#attendanceReview").size()) {
            $('#attendanceReview').on('click', 'tbody tr', function (event) {
                $('.emptitle').html($(this).attr('data-code'));
            });
        } else {
            $('.emptitle').html('');
        }
    });

    $('.attendanceReview').tableHeadFixer({
        head: true,
        foot: true,
        left: 4,
        right: 0,
        'z-index': 10
    });

    $('#attendanceReview').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
    });

    $("input:checkbox").click(function () {
        var display = $(this).attr("rel");

        if ( $(this).is(':checked')){
            $("#attendanceReview tr."+display).show();
        }
        else{
            $("#attendanceReview tr."+display).hide();
        }
    });
    
    async function load_swal_input(trID, masterID, value, name){
        const { value: text } = await Swal.fire({
            input: 'textarea',
            inputLabel: 'Confirmation',
            inputPlaceholder: 'Please add a reason for change.',
            inputAttributes: {
                'aria-label': 'Please add a reason for change'
            },
            showCancelButton: true
        });

        if (text) {

            update_comment(text,masterID);

            $.ajax({
               async: true,
               type: 'post',
               dataType: 'json',
               data: {trID: trID, masterID: masterID, value: value, name: name},
               url: "<?php echo site_url('Employee/update_attendance'); ?>",
               beforeSend: function () {
                   /* startLoad();*/
               },
               success: function (data) {
                   /* stopLoad();*/

                   if (data['error'] == 1) {
                       myAlert('e', data['message']);
                   }

                   if (data['error'] == 0) {
                        $("tr[data-id='" + trID + "']").find(".realTime").text(data['data']['realTime']);
                        $("tr[data-id='" + trID + "']").find(".attType").val(data['data']['presentTypeID']);
                        $("tr[data-id='" + trID + "']").find(".lateHours").text(data['data']['h_lateHours']+':'+data['data']['m_lateHours']);
                        $("tr[data-id='" + trID + "']").find(".OTHours").text(data['data']['h_OTHours']+':'+data['data']['m_lateHours']);
                        $("tr[data-id='" + trID + "']").find(".attendhours").html(data['data']['attendhours']);
                        $("tr[data-id='" + trID + "']").find(".earlyHours").text(data['data']['h_earlyHours']+':'+data['data']['m_earlyHours']);
                        $("tr[data-id='" + trID + "']").find(".OTHours").text(data['data']['h_OTHours']+':'+data['data']['m_OTHours']);
                        $("tr[data-id='" + trID + "']").find(".weekend").text(data['data']['weekend']);
                        $("tr[data-id='" + trID + "']").find(".holiday").text(data['data']['holiday']);
                        $("tr[data-id='" + trID + "']").find(".normalDay").text(data['data']['normalDay']);
                        $("tr[data-id='" + trID + "']").find(".NDaysOT").text(data['data']['h_NDaysOT']+':'+data['data']['m_NDaysOT']);
                        $("tr[data-id='" + trID + "']").find(".holidayOTHours").text(data['data']['h_holidayOTHours']+':'+data['data']['m_holidayOTHours']);
                        $("tr[data-id='" + trID + "']").find(".weekendOTHours").text(data['data']['h_weekendOTHours']+':'+data['data']['m_weekendOTHours']);
                        $("tr[data-id='" + trID + "']").find(".totWorkingHours").html(data['data']['totWorkingHours']);

                        myAlert('s', 'Successfully Updated');
                    }

               }, error: function () {
                   myAlert('e', 'An Error Occurred! Please Try Again.');
                   stopLoad();
               }
           });
        }else{
            myAlert('e', 'You need to add a Confirmation Message to Proceed.');
            employee_data_load();
            stopLoad();
        }

    }

    function updatefields(trID, masterID, value, name) {

        var text = load_swal_input(trID, masterID, value, name);
        
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
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTableShift'); ?>",
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
                aoData.push({'name': 'is_manager', 'value': '1'});
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

    function clearAllRows() {
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function addAllRows() {

        var postData = $('#tempTB_form').serializeArray();

        postData.push({name:'shift_select',value:'1'});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Employee/add_employees_to_shift'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                $('#employee_model').modal('hide');

                if (data[0] == 's') {
                    clearAllRows();
                    employee_data_load_shift();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function deleteShift(autoID){

        Swal.fire({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",
            text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55 ",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                async: true,
                url: "<?php echo site_url('Employee/deleteEmpAssignedShift'); ?>",
                type: 'post',
                dataType: 'json',
                data: {'autoID': autoID},
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        employee_data_load_shift();
                    }
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'error');
                }
            });
            }
        });
       
    }

    function shift_employee(ev){
        var shiftID = $(ev).val();
        $('#shieftIDhn').val(shiftID);
    }

</script>
<?php
