<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_machine_attendance_approval');
echo head_page($title, false);
$current_date = format_date($this->common_data['current_date']);
$floors_arr = floors_drop();
$lateattendancePolicy = getPolicyValues('ELA', 'All');
if(is_null($lateattendancePolicy)) { $lateattendancePolicy = 0; }
?>
    <style>
        .control-label {
            margin-top: 5px;
            padding: 0px;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tbody>
                <tr>
                    <td>
                        <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                        <?php echo $this->lang->line('common_approved'); ?><!-- Approved-->
                    </td>
                    <td>
                        <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                        <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                    </td>
                    <td>
                        <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                        <?php echo $this->lang->line('common_partially_approved'); ?><!--Partially Approved-->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-center">
            <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="filterAttendanceTable()"'); ?>
        </div>
    </div>
    <hr>

    <div class="col-md-12">
        <label for="inputData" class="col-md-1 control-label"><?php echo $this->lang->line('common_Location'); ?>
            :</label>
        <div class="col-md-2" style="padding-left: 0px;width:120px">
            <?php echo form_dropdown('filterfloor', $floors_arr, '', ' onchange="filterAttendanceTable();" class="form-control select2" id="filterfloor" '); ?>
        </div>

        <label for="inputCodforn" class="col-md-2 control-label"
               style="width:95px"><?php echo $this->lang->line('common_date'); ?>
            <?php echo $this->lang->line('common_from'); ?><!--Date From--></label>
        <div class="col-md-2" style="padding-left: 0px;">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="datefrom" id="datefrom" class="form-control dateField /">
            </div>
        </div>
        <label for="inputCodforn" class="col-md-2 control-label"
               style="width:80px"><?php echo $this->lang->line('common_date'); ?>
            <?php echo $this->lang->line('common_to'); ?><!--Date To--></label>
        <div class="col-md-2" style="padding-left: 0px;">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="dateto" id="dateto" class="form-control dateField /">
            </div>
        </div>
        <label for="inputData" class="col-md-1 control-label">
            <?php echo $this->lang->line('hrms_payroll_group_by'); ?><!--Group By-->:</label>
        <div class="col-md-2" style="padding-left: 0px;">
            <?php
            $groupBy_arr = [
                '0' => $this->lang->line('common_Location'),
                '1' => $this->lang->line('common_location_and_date')
            ];
            echo form_dropdown('groupby', $groupBy_arr,'', ' onchange="filterAttendanceTable();" class="form-control select2" id="groupby" '); ?>
        </div>
    </div>
    <div class="col-md-12">
        <button onclick="clearfilter()" class="btn btn-primary btn-xs">
            <?php echo $this->lang->line('common_clear'); ?><!--Clear--></button>
        <hr>
    </div>

    <div class="clearfix">&nbsp;</div>
    <div class="table-responsive">
        <table id="attendanceMasterTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th>#</th>
                <th>Location in</th>
                <th>Location out</th>
                <th><?php echo $this->lang->line('common_Location'); ?></th>
                <th>
                    <span id="coltitle"><?php echo $this->lang->line('hrms_payroll_attendance_date'); ?><!--Attendance Date-->
                </th>

                <!-- <th>Attendance Date</th>
                 <th>Attendance Time</th>
                 <th>Is Closed</th>-->


                <th style="width: 80px;text-align: center">
                    <?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                <th style="width: 10px"></th>
            </tr>
            </thead>
        </table>
    </div>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="newAttendance" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
         data-keyboard="false">
        <div class="modal-dialog" style="width: 95%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="salary-cat-title">
                        <?php echo $this->lang->line('hrms_payroll_attendance_list'); ?><!--Attendance List--></h4>
                </div>
                <?php echo form_open('', 'role="form" class="" id="newAttendance_form" autocomplete="off"'); ?>

                <input type="hidden" name="approveType" value="Manual" />
                <div class="modal-body" id="">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="divLoadPage"></div>
                            <hr>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" onclick="approvaldata()"
                            class="btn btn-primary hideremove  btn-sm controlCls">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>

                </form>
            </div>
        </div>
    </div>

    <div id="modalleave" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="width: 50%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" onclick="leaveClose()" class="close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('hrms_payroll_leave_application'); ?><!--Leave Application--> </h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="">
                        <div class="col-md-12">
                            <label for="inputCodforn" id="employeeName" class="col-md-12 control-label"
                                   style=""></label>


                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="inputCodforn" class="col-md-3 control-label" style="">
                                <?php echo $this->lang->line('hrms_payroll_leave_type'); ?><!--leave Type--></label>
                            <div class="col-md-2" style="">
                                <input type="hidden" name="trID" id="trID">
                                <input type="hidden" name="trempID" id="trempID">
                                <input type="hidden" name="attendanceDate" id="attendanceDatehn">
                                <input type="hidden" name="appliedLeave" id="appliedLeavehn">
                                <input type="hidden" name="workingDays" id="workingDayshn">
                                <input type="hidden" name="leavebalance" id="leavebalancehn">
                                <input type="hidden" name="policyMasterID" id="policyMasterIDhn">
                                <input type="hidden" name="leaveGroupID" id="leaveGroupIDhn">
                                <div class="" id="leaveType">
                                    <!-- <input type="text" name="dateto" id="dateto" c <input type="hidden" name="trID" id="trID">lass="form-control dateField /">-->
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped table-condensed table-row-select">
                                <tr style="">
                                    <td style="font-size: 11px" width="150px"><strong>
                                            <?php echo $this->lang->line('hrms_payroll_balance'); ?><!--Leave Balance--></strong>
                                    </td>
                                    <td>
                                        <div id="leavebalance">0</div>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                    <i><?php echo $this->lang->line('common_note'); ?><!--Note--> :
                        <?php echo $this->lang->line('hrms_payroll_this_will_generate_automatic_leave_application_for_this_day'); ?><!--This will generate automatic leave application for this day--></i>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveLeaves()" class="btn btn-primary">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script type="text/javascript">
        var newAttendance = $('#newAttendance');
        var attendanceDetail = $('#attendanceDetail');
        var newAttendance_form = $('#newAttendance_form');
        window.attMasterTB;
        var attendanceReview = $('#attendanceReview');

        $(document).ready(function () {
            $('.select2').select2();
            $('.headerclose').click(function () {
                fetchPage('system/hrm/machine_attendance_approval', '', 'HRMS');
            });

            $('#attendanceTime').timepicker({
                minuteStep: 1,
                template: 'dropdown',
                appendWidgetTo: 'body',
                showSeconds: false,
                showMeridian: true
            });


            $('.dateField').datepicker({format: 'yyyy-mm-dd'}).on('changeDate', function (ev) {
                $(this).datepicker('hide');

                filterAttendanceTable();
            });


            $('#attFetching_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    fromDate: {validators: {notEmpty: {message: 'Date is required.'}}},
                    toDate: {validators: {notEmpty: {message: 'Date is required.'}}}
                }
            })
                .on('success.form.bv', function (e) {
                    $('#loadBtn').prop('disabled', false);
                    e.preventDefault();
                    loadDataFromTemptable();
                });

            load_attendanceTB();
        });

        function clearfilter() {
            $('#datefrom').val('');
            $('#dateto').val('');

            $('.select2').removeAttr('onchange');

            $('#filterfloor').val('').change();
            $('#groupby').val('0').change();


            $('.select2').attr('onchange', 'filterAttendanceTable()');

            filterAttendanceTable();
        }

        function filterAttendanceTable() {
            attMasterTB.ajax.reload();
        }

        function approvaldata() {
            var policy = <?php echo $lateattendancePolicy; ?>;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: $('#newAttendance_form').serializeArray(),
                url: "<?php echo site_url('Employee/approveattendlist'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 's') {
                        newAttendance.modal('hide');
                        myAlert('s', data['message']);
                        if (data['message1']) {
                            myAlert('w', data['message1']);
                        }

                    } else {
                        myAlert('e', data['message']);
                    }
                    attMasterTB.ajax.reload();
                    if(policy) {
                        if(data['error'] == 's' && policy == 1) {
                            send_late_attendance_email(data['updatedIDs']);
                        }
                    }
                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });

        }

        function confirm_att() {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: $('#attFetching_form').serializeArray(),
                        url: "<?php echo site_url('Employee/attendance_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if (data['error'] == 0) {
                                myAlert('s', data['message']);
                                fetchPage('system/hrm/attendance_management', '', 'HRMS');
                            } else {
                                myAlert('e', data['message']);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function removeTr(obj) {
            masterID = $(obj).closest('tr').attr('data-masterid');
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {

                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {masterID: masterID},
                        url: "<?php echo site_url('Employee/delete_attendance'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 0) {
                                $(obj).closest('tr').remove();
                                attMasterTB.ajax.reload();

                                myAlert('s', data['message']);
                            } else {
                                myAlert('e', data['message']);
                            }

                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });


                }
            );
        }

        function load_attendanceTB(selectedRowID = null) {
            var selectedRowID = (selectedRowID == null) ? '<?php echo $this->input->post('page_id'); ?>' : selectedRowID;

            attMasterTB = $('#attendanceMasterTB').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/attendanceMachineTableApprovalManual'); ?>",
                "aaSorting": [[1, 'ASC']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                        if (parseInt(oSettings.aoData[x]._aData['EmpAttMasterID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }

                },
                "columnDefs": [{"targets": [0,3,4], "orderable": false}],
                "aoColumns": [
                    {"mData": "attendanceDate"},
                    {"mData": "clockinFloorDescription"},
                    {"mData": "clockoutFloorDescription"},
                    {"mData": "attendanceDate"},
                    {"mData": "approved"},
                    {"mData": "edit"}

                    /*  {"mData": "AttTime"},
                     {"mData": "isClosed"},
                     {"mData": "action"}*/
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "date_from", "value": $('#datefrom').val()});
                    aoData.push({"name": "date_to", "value": $('#dateto').val()});
                    aoData.push({"name": "group_by", "value": $('#groupby').val()});
                    aoData.push({"name": "filter_location", "value": $('#filterfloor').val()});
                    aoData.push({"name": "approvedYN", "value": $('#approvedYN').val()});
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

        function open_attendanceModal() {
            newAttendance_form[0].reset();
            newAttendance_form.bootstrapValidator('resetForm', true);
            newAttendance.modal('show');
        }

        function edit_attendance(attendanceDate, floorID, col) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    attendanceDate: attendanceDate,
                    floorID: floorID,
                    col: col,
                    type: 'Manual',
                    approvedYN: $('#approvedYN').val(),
                    datefrom: $('#datefrom').val(),
                    dateto: $('#dateto').val()
                },
                url: "<?php echo site_url('Employee/AttendanceApprovalList'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {
                    $('#divLoadPage').html(data);
                    newAttendance.modal('show');
                    if ($('#approvedYN').val() == 0) {
                        $('.hideremove').removeClass('hide');
                    } else {
                        $('.hideremove').addClass('hide');
                    }

                    stopLoad();

                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

                }
            });
        }

        function delete_attendanceMaster(attID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>!"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Employee/delete_attendanceMaster'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'hidden-id': attID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                load_attendanceTB()
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function saveAttendance() {
            var postData = $('#attendanceDetail_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Employee/save_attendanceDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        attendanceDetail.modal('hide');
                        load_attendanceTB()
                    }
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'error');
                }
            });
        }

        /*** Attendance review  functions****/
        function loadDataFromTemptable() {
            var postData = $('#attFetching_form').serializeArray();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: postData,
                url: '<?php echo site_url('Employee/load_empAttDataView'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#attendanceReview >tbody').html(data['tBody']);
                    $('#attReview-showingCount').text(data['rowCount']);
                    $('#attReview-totalRowCount').text(data['rowCount']);

                    makeDate_dropDown(data['date_arr']);
                    unAssignedData_manipulation(data['unAssignedMachineID'], data['unAssignedShifts']);


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    $('#attendanceReview >tbody').html('');
                    $('#attendanceReview').append('<tr><td colspan="21">No data available in table </td></tr>');

                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function makeDate_dropDown(date_arr) {
            var searchDate = $('#searchDate');
            var options = '<option value="" selected="selected">Search Date</option>';

            searchDate.empty();
            $.each(date_arr, function (val) {
                options += '<option value="' + val + '" >' + val + '</option>';
            });
            searchDate.append(options);
        }

        function unAssignedData_manipulation(unAssignedMachineID_arr, unAssignedShifts_arr) {
            var unAssignedShift_div = $('#unAssignedShift-div');
            var unAssignedMachine_div = $('#unAssignedMachine-div');

            unAssignedShift_div.hide();
            unAssignedMachine_div.hide();

            if (unAssignedMachineID_arr.length > 0) {
                unAssignedMachine_div.show();

                $('#unAssignedMachine tbody').remove();
                var unAssignedMachineTB = $('#unAssignedMachine');
                var machineDet = '';

                $.each(unAssignedMachineID_arr, function (i, row) {
                    machineDet += '<tr>';
                    machineDet += '<td>' + (i + 1) + '</td>';
                    machineDet += '<td>' + row['ECode'] + '</td>';
                    machineDet += '<td>' + row['Ename1'] + '</td>';
                    machineDet += '<td></td>';
                    machineDet += '</tr>';
                });

                unAssignedMachineTB.append(machineDet);
            }

            if (unAssignedShifts_arr.length > 0) {
                unAssignedShift_div.show();

                $('#unAssignedShift tbody').remove();
                var unAssignedShiftTB = $('#unAssignedShift');
                var shiftDet = '';

                $.each(unAssignedShifts_arr, function (i, row) {
                    shiftDet += '<tr >';
                    shiftDet += '<td>' + (i + 1) + '</td>';
                    shiftDet += '<td>' + row['ECode'] + '</td>';
                    shiftDet += '<td>' + row['Ename1'] + '</td>';
                    shiftDet += '<td></td>';
                    shiftDet += '</tr>';
                });

                unAssignedShiftTB.append(shiftDet);
            }
        }

        function save_attReview() {
            var postData = $('#attendanceReview_form').serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: postData,
                url: '<?php echo site_url('Employee/save_attendanceReviewData'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });


        }

        $('#attendanceReview').on('click', 'tbody tr', function (event) {
            $(this).addClass('highlight').siblings().removeClass('highlight');
        });

        /*   $('.table-row-select tbody').on('click', 'tr', function () {
         duplicates();
         var tableID = $(this).parent().parent().attr('id');
         if( tableID == 'attendanceReview' ){
         var removingClass = ( $(this).hasClass('evenTR') )? 'evenTR' : 'oddTR';
         $(this).attr('data-removeTR', removingClass);
         $(this).removeClass('oddTR evenTR');

         var lastSelectedTR = $('#attendanceReview .dataTable_selectedTr');

         var lastSelectedTR_class = lastSelectedTR.attr('data-removeTR');

         if( lastSelectedTR_class != undefined ){
         lastSelectedTR.addClass( lastSelectedTR_class );
         }

         lastSelectedTR.removeClass('dataTable_selectedTr');
         }
         else{
         $('.table-row-select tr').removeClass('dataTable_selectedTr');
         }

         $(this).toggleClass('dataTable_selectedTr');
         });*/

        function loadautoleave_confirm_details() {
            var leaveTypeID = $('#leaveTypeID').val();
            var halfDay = 0;
            var shortLV = 0;
            var isAllowminus = null;
            var isCalenderDays = 0;
            var entitleSpan = $('#leavebalance').html();
            var startDate = $('#attendanceDatehn').val();
            var endDate = $('#attendanceDatehn').val();
            var empID = $('#trempID').val();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'leaveTypeID': leaveTypeID,
                    'halfDay': halfDay,
                    'shortLV': shortLV,
                    'isAllowminus': isAllowminus,
                    'isCalenderDays': isCalenderDays,
                    'entitleSpan': entitleSpan,
                    'startDate': startDate,
                    'endDate': endDate
                },
                url: '<?php echo site_url('Employee/leaveEmployeeCalculation'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data) {
                        $('#leavebalancehn').val(data['leaveBlance']);
                        $('#appliedLeavehn').val(data['appliedLeave']);
                        $('#workingDayshn').val(data['workingDays']);
                        getlvpolicy_and_group(empID, leaveTypeID)
                    }


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function getlvpolicy_and_group(empID, leaveTypeID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'empID': empID, 'leaveTypeID': leaveTypeID},
                url: '<?php echo site_url('Employee/getlvpolicy_and_group'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data) {
                        $('#policyMasterIDhn').val(data['leaveGroupID']['leaveGroupID']);
                        $('#leaveGroupIDhn').val(data['policyMasterID']['policyMasterID']);
                    }


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function saveLeaves() {
            var dt = $('#attendanceDatehn').val();
            var leaveMasterID = null;
            var applicationType = '1';
            var empName = $('#trempID').val();
            var empID = null;
            var entryDate = '<?php echo date('Y-m-d') ?>';
            var leaveTypeID = $('#leaveTypeID').val();
            var coveringValidated = 0;
            var coveringAvailabilityValidated = 0;
            var coveringEmpID = null;
            var startDate = dt;
            var endDate = dt;
            var shift = 0;
            var comment = null;
            var attachmentDescription = null;
            var document_name = "Leave Management";
            var document_file = null;
            var isConfirmed = '1';
            var isCalenderDays = 0;
            var entitleSpan = $('#leavebalance').html();
            var appliedLeave = $('#appliedLeavehn').val();
            var leaveBlance = $('#leavebalancehn').val();
            var policyMasterID = $('#policyMasterIDhn').val();
            var leaveGroupID = $('#leaveGroupIDhn').val();
            var workingDays = $('#workingDayshn').val();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'leaveMasterID': leaveMasterID,
                    'applicationType': applicationType,
                    'empName': empName,
                    'empID': empID,
                    'entryDate': entryDate,
                    'leaveTypeID': leaveTypeID,
                    'coveringValidated': coveringValidated,
                    'coveringAvailabilityValidated': coveringAvailabilityValidated,
                    'coveringEmpID': coveringEmpID,
                    'startDate': startDate,
                    'endDate': endDate,
                    'shift': shift,
                    'comment': comment,
                    'attachmentDescription': attachmentDescription,
                    'document_name ': document_name,
                    'document_file': document_file,
                    'isConfirmed': isConfirmed,
                    'isCalenderDays': isCalenderDays,
                    'entitleSpan': entitleSpan,
                    'appliedLeave': appliedLeave,
                    'leaveBlance': leaveBlance,
                    'policyMasterID': policyMasterID,
                    'leaveGroupID': leaveGroupID,
                    'workingDays': workingDays
                },
                url: '<?php echo site_url('Employee/save_employeesLeave'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#modalleave').modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function send_late_attendance_email(ID)
        {
            swal({
                title: "Are you sure?", /*Are you sure?*/
                text: 'Send Email to Employees?',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'ID' : ID},
                    url: "<?php echo site_url('Employee/late_attendance_mail'); ?>",
                    beforeSend: function () {
                        // startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
        }
    </script>
<?php
