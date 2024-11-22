<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_add_shift_master');
echo head_page($title, false);


$week_arr = fetch_weekDays();
$current_date = current_format_date();
$date_format_policy = date_format_policy();
//echo '<pre>';print_r($week_arr); echo '</pre>';
?>
<style>
    .timeIcon {
        font-size: 13px;
    }

    .groupBox {
        padding: 2px 6px;
    }

    .datepicker {
        z-index: 10000000 !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_shift()"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="shiftMaster" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 50%"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
            <th style="min-width: 7%"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="shift_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('hrms_attendance_new_shift'); ?><!--New Shift--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="newShift_form" method="get"'); ?>

            <div class="modal-body">
                <div class="form-group">
                    <div class="col-sm-12 ">
                        <button type="button" id="multiemp" class="btn btn-primary btn-sm saveBtn pull-right hidden"
                                onclick="openEmployeeModal()"><i class="fa fa-fw fa-user"></i>
                            <?php echo $this->lang->line('hrms_attendance_add_employee'); ?><!--Add Employee-->
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        <?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="shiftDescription" name="shiftDescription">
                    </div>
                </div>

                <?php
                // border-style: ridge
                 echo '</br>
                 <div class="row" style="margin-right:2px; margin-left:2px">
                     <label class="col-sm-2" style="height:50px; text-align:center">Day </label>
                     <div class="col-sm-8">
                         <div class="row">
                             <div class="col-sm-4" style="height:50px; text-align:center">
                                 <label class="">On Duty Time </label>
                             </div>

                             <div class="col-sm-4" style="height:50px; text-align:center">
                                 <label class="">Off Duty Time </label>
                             </div>
                             <div class="col-sm-2" style="height:50px; text-align:center">
                                 <label class="">Grace Period </label>
                             </div>
                             <div class="col-sm-1" style="height:50px; text-align:center">
                                 <label class="">Half Day </label>
                             </div>
                             <div class="col-sm-1" style="height:50px; text-align:center">
                                <label class="">Next Day </label>
                            </div>
                         </div>
                     </div>
                     <div class="col-sm-2" style="height:50px; text-align:center">
                         <label class="">Is Weekend </label>
                     </div>
                 </div>';
                foreach ($week_arr as $row) {
                    $isWeekendCheck = ($row['isWeekend']) ? 'checked' : '';
                    $onTime = ($row['isWeekend']) ? '' : '09:00 AM';
                    $offTime = ($row['isWeekend']) ? '' : '05:00 PM';

                    $lang_string = 'cal_' . strtolower(trim($row['DayDesc'] ?? ''));
                    $day = $this->lang->line($lang_string);
                    $on_time = '';//$this->lang->line('hrms_attendance_on_time');
                    $off_time = '';//$this->lang->line('hrms_attendance_off_time');
                    //$row['DayDesc']
                    echo '
                    <div class="form-group">
                        <label class="col-sm-2 control-label">' . $day . '</label>
                        <div class="col-sm-8">
                            <div class="row">
                            
                                <div class="col-sm-4">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-time timeIcon"></i></span>
                                        <input name="onTime[]" type="text" class="form-control input-small shiftTime shift_onTime" id="onDayID_' . $row['DayID'] . '"
                                        value="' . $onTime . '" data-isweekend="' . $row['isWeekend'] . '" >
                                        <span class="input-group-addon groupBox">' . $on_time . '</span>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-time timeIcon"></i></span>
                                        <input name="offTime[]" type="text" class="form-control input-small shiftTime shift_offTime" id="offDayID_' . $row['DayID'] . '"
                                        value="' . $offTime . '" data-isweekend="' . $row['isWeekend'] . '">
                                        <span class="input-group-addon groupBox">' . $off_time . '</span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="row">
                                        <div class="col-sm-12">
                                        <input  type="text" name="h_graceperiod[]" onkeyup="validate_hours(this,'.$row['DayID'].')" class="trInputs  timeBox txtH number h_graceperiod" style="width: 25px" value="00" id="h_graceperiod_'.$row['DayID'].'"> :<input  type="text" onkeyup="validate_mins(this,'.$row['DayID'].')" name="m_graceperiod[]" class="trInputs  timeBox txtH number m_graceperiod" style="width: 25px" id="m_graceperiod_'.$row['DayID'].'" value="00"> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label class="checkbox-inline"><input type="checkbox" onclick="checkHalfDay(this)" name="HalfDay[]" id="HalfDay_' . $row['DayID'] . '"></label>
                                    <input type="hidden" name="isHalfDay[]" class="isHalfDay" id="isHalfDay_' . $row['DayID'] . '">
                                </div>

                                <div class="col-sm-1">
                                <label class="checkbox-inline"><input type="checkbox" onclick="checkFallForNextDay(this)" name="FallForNextDay[]" id="FallForNextDay_' . $row['DayID'] . '"></label>
                                <input type="hidden" name="isFallForNextDay[]" class="isFallForNextDay"  id="isFallForNextDay_' . $row['DayID'] . '">
                                </div>
                        
                            </div>
                        </div>

                        <div class="col-sm-2">
                    
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" onclick="changeWeekStatus(this)" ' . $isWeekendCheck . ' id="checkboxID_' . $row['DayID'] . '" />
                            </span>
                            <input class="form-control input-small dayType" value="' . $this->lang->line('hrms_attendance_weekend') . '" readonly>
                            <input type="hidden" name="isWeekend[]" class="isWeekend" value="' . $row['isWeekend'] . '" data-value="' . $row['isWeekend'] . '">
                            <input type="hidden" name="masterDayID[]" value="' . $row['DayID'] . '">
                            <input type="hidden" name="dayDescription[]" value="' . $row['DayDesc'] . '">
                            <input type="hidden" name="shiftDetID[]" id="shiftDetID_' . $row['DayID'] . '">
                        </div>
                    

                        
                    
                        </div>
                    </div>';
                }
                ?>
                <hr>
                <legend></legend>
                <div class="form-group">
                        <label class="col-sm-2 control-label" for="isSpecialOT"><?php echo $this->lang->line('hrms_attendance_special_ot'); ?><!--Excel Upload--></label>
                        <div class="col-sm-2">
                            <input type="checkbox" onchange="apply_special_ot(this)" value="1" id="isSpecialOT"
                                    name="isSpecialOT">
                        </div>


                        <div class="hide" id="specialOtHours_div">
                            <label class="col-sm-2 control-label" for="specialOtHours"><?php echo $this->lang->line('hrms_attendance_special_ot_hours'); ?><!--Excel Upload--></label>
                            <div class="col-sm-2">
                                <input type="text"  id="specialOtHours" class="form-control"
                                    name="specialOtHours">
                            </div>
                        </div>
                </div>

           
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
            <input type="hidden" id="editID" name="editID">
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"
     style="z-index: 999999">
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


<script type="text/javascript">
    var shiftMaster_form = $('#newShift_form');
    var shift_modal = $('#shift_modal');
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({"bPaginate": false});
    var empTempory_arr = [];

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/shift_master', 'Test', 'HRMS');
        });

        load_shiftMaster();

        /*$('.shiftTime').timepicker({
         minuteStep: 1,
         template: 'dropdown',
         appendWidgetTo: 'body',
         showSeconds: false,
         showMeridian: true
         });*/

        $('.shiftTime').timepicker({
            minuteStep: 1,
            template: 'dropdown',
            appendWidgetTo: 'body',
            showSeconds: false,
            showMeridian: true
        });

        shiftMaster_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    shiftDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
                }
            })
            .on('success.form.bv', function (e) {
                $('.submitBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var postData = $form.serializeArray();
                var urlReq = $form.attr('action');


                $.ajax({
                    type: 'post',
                    url: urlReq,
                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            shift_modal.modal('hide');
                            var editID = $('#editID').val();
                            var masterID = ($.isNumeric(editID)) ? editID : data[2];
                            load_shiftMaster(masterID);
                        }

                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                });

            });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {

        });
    });

    function load_shiftMaster(selectedID=null) {
        var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        $('#shiftMaster').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_shiftMaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['shiftID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "shiftID"},
                {"mData": "Description"},
                {"mData": "action"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function new_shift() {
        $("#multiemp").addClass('hidden');
        shiftMaster_form.attr('action', '<?php echo site_url('Employee/saveShiftMaster'); ?>');
        shiftMaster_form[0].reset();
        shiftMaster_form.bootstrapValidator('resetForm', true);
        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_new_shift');?>');

        $('.shiftTime').each(function () {


            if ($(this).attr('data-isweekend') == 1) {
                $(this).val(null);

            } else {

                if ($(this).hasClass('shift_onTime')) {
                    $(this).val('09:00 AM');
                }
                else {
                    $(this).val('05:00 PM');
                }

            }
        });

        shift_modal.modal({backdrop: "static"});
    }

    function edit_shift(editID, des) {
        shiftMaster_form.attr('action', '<?php echo site_url('Employee/updateShiftMaster'); ?>');
        shiftMaster_form[0].reset();
        shiftMaster_form.bootstrapValidator('resetForm', true);
        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_edit_shift'); ?>');
        $('#editID').val(editID);
        $('#shieftIDhn').val(editID);
        $('#shiftDescription').val(des);
        $("#multiemp").removeClass('hidden');
      

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/fetch_shiftDetails'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'shiftID': editID},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    shift_modal.modal({backdrop: "static"});

                    var specialOT = 0;
                    var specialOThours = 0;

                    $.each(data[1], function (i, elm) {
                        $('#onDayID_' + elm['dayID']).val(elm['onDutyTime']);
                        $('#offDayID_' + elm['dayID']).val(elm['offDutyTime']);
                        $('#shiftDetID_' + elm['dayID']).val(elm['shiftDetailID']);
                        $('#checkboxID_' + elm['dayID']).prop('checked', ( elm['onDutyTime'] == null ));

                        if (elm['isHalfDay'] == 1) {
                            $('#HalfDay_' + elm['dayID']).prop('checked', true);
                        } else {
                            $('#HalfDay_' + elm['dayID']).prop('checked', false)
                        }

                        if (elm['isNextDay'] == 1) {
                            $('#FallForNextDay_' + elm['dayID']).prop('checked', true);
                        } else {
                            $('#FallForNextDay_' + elm['dayID']).prop('checked', false)
                        }

                        $('#isHalfDay_' + elm['dayID']).val(elm['isHalfDay']);
                        $('#isFallForNextDay_' + elm['dayID']).val(elm['isNextDay']);
                     
                        $('#h_graceperiod_' + elm['dayID']).val(elm['graceperiodhrs']);
                        $('#m_graceperiod_' + elm['dayID']).val(elm['gracemins']);

                        specialOT = elm['isSpecialOT'];
                        specialOThours = elm['specialOT'];

                    });

                    if(specialOT == 1){
                        $('#isSpecialOT').prop('checked',true).change();
                        $('#specialOtHours').val(specialOThours);
                    }else{
                        $('#isSpecialOT').prop('checked',false).change();
                    }

                } else {
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });


    }

    function delete_shift(delID, des) {
        swal(
            {
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
                    url: "<?php echo site_url('Employee/deleteShiftMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'deleteID': delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_shiftMaster()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function changeWeekStatus(obj) {
        var dayType = ( $(obj).is(':checked') ) ? 1 : 0;
        $(obj).parent().parent().find('.isWeekend').val(dayType);
    }

    function checkHalfDay(obj) {
        var dayType = ( $(obj).is(':checked') ) ? 1 : 0;
        $(obj).parent().parent().find('.isHalfDay').val(dayType);
    }

    function checkFallForNextDay(obj) {
        var dayType = ( $(obj).is(':checked') ) ? 1 : 0;
        $(obj).parent().parent().find('.isFallForNextDay').val(dayType);
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

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
                //aoData.push({'name': 'epfMasterID', 'value': epfMasterID});
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
                if (data[0] == 's') {
                    $('#employee_model').modal('hide');
                    clearAllRows();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });



    }
    function validate_hours(ele,day)
    { 
        var el = $(this);
       if(ele.value > 24){ 
        $('#h_graceperiod_'+day).val(0);
        myAlert('w','Hours cannot be greater than 24')
       }
      
       

    }   

    function validate_mins(ele,day)
    {
        if(ele.value > 59)
        {
            $('#m_graceperiod_'+day).val(0);
            myAlert('w','Minutes cannot be greater than 59')
            
        }

    }

    function apply_special_ot(ev){
        var isSpecial = $(ev).is(':checked');

        if(isSpecial){
            $('#specialOtHours_div').removeClass('hide');
        }else{
            $('#specialOtHours_div').addClass('hide');
        }
    }

</script>


<?php
