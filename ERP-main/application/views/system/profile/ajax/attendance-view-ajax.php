<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine_attendance_management');

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
                    <span class="label" style="padding: 0px 5px ;font-size: 100%;background-color: #dacff7">&nbsp;</span>&nbsp;<?php echo  $this->lang->line('hrms_attendance_shift_weekend')?>&nbsp; <!--Shift Weekend-->
                </td>
            </tr>
        </table>
    </div>
</div>
<br>
<span class="empDetail"></span>
<div style="max-height: 400px">
    <table id="attendanceReview" class="table first attendanceReview">
        <thead>
        <tr style="white-space: nowrap">
            <th style=""><?php echo $this->lang->line('common_status'); ?><!-- Status --></th>
            <th style=""><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--Emp Name--></th>
            <th style=""><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_floor'); ?><!--Dept--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_on_duty_time'); ?><!--On Duty Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_off_duty_time'); ?><!--Off Duty Time--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_in_date'); ?><!--Clock In Date--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_in'); ?><!--Clock In--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_out_date'); ?><!--Clock In--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_out'); ?><!--Clock Out--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_real_time'); ?><!--Real Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_present'); ?><!--Present--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_late'); ?><!--Late--></th>

            <th style=""><?php echo $this->lang->line('hrms_attendance_early'); ?><!--Early--></th>

            <th style=""><?php echo $this->lang->line('hrms_attendance_over_time'); ?><!--OT Time--></th>

            <th style=""><?php echo $this->lang->line('hrms_attendance_work_time'); ?><!--Work Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_att_time'); ?><!--ATT_Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_normal_day'); ?><!--NDay--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_weekend'); ?><!--Week End--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_holiday'); ?><!--Holiday--></th>

            <th style=""><?php echo $this->lang->line('hrms_attendance_ndays_ot'); ?><!--NDays OT--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_weekend_ot'); ?><!--Weekend OT--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_holiday_ot'); ?><!--Holiday OT--></th>
        </tr>

        </thead>
        <tbody id="StatusTable">
        <?php

        $appData = '';
        $disabled = 'disabled';
        $attDrop = attendanceType_drop();
        $empArray = array();
        $clockIn_arr = array();
        $clockOut_arr = array();
        $emparr = array();
        $disabled2  = '';
   
        /****** Employee total working hours for this day ******/

        if (!empty($att_rec)) {
            foreach ($att_rec as $key => $row) {
                $totWorkingHours = '';
                $attendhours = '';
                $isAllSet = 0;
                $disabled_ar = ($row['confirmedYN'] == 1) ? 'disabled':'';

                $attendanceDate = $row['attendanceDate'];
                $attendanceNextDay = $row['attendanceDate'];
                
                if($row['isShiftNextDay']){
                    $attendanceNextDay = date('Y-m-d',strtotime('+1 days',strtotime($attendanceDate)));
                }

                if ($row['checkIn'] != null && $row['checkOut'] != null && $row['offDuty'] != null) {
                    // $datetime1 = new DateTime($row['offDuty']);

                    // if($row['onDuty'] >=$row['checkIn']){
                    //     $datetime2= new DateTime($row['onDuty']);
                    // }else{
                    //     $datetime2 = new DateTime($row['checkIn']);
                    // }
                    // $totWorkingHours_obj = $datetime1->diff($datetime2);
                    // $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";

                    $datetime1 = new DateTime($attendanceNextDay.' '.$row['offDuty']);
                    $datetime2 = new DateTime($attendanceDate.' '.$row['onDuty']);
        
                    $totWorkingHours_obj = $datetime1->diff($datetime2);
                    $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";
                }
                if ($row['checkIn'] != null && $row['checkOut'] != null) {
                    // $datetime1 = new DateTime($row['checkIn']);
                    // $datetime2 = new DateTime($row['checkOut']);
                    // $attendhours_obj = $datetime1->diff($datetime2);
                    // $attendhours = $attendhours_obj->format('%h') . " h &nbsp;&nbsp;" . $attendhours_obj->format('%i') . " m";
                    $datetime1 = new DateTime($row['checkOutDate'].' '.$row['checkOut']);
                    $datetime2 = new DateTime($row['checkInDate'].' '.$row['checkIn']);
        
                    $attendhours_obj = $datetime1->diff($datetime2);
                    $attendhours = $attendhours_obj->format('%h') . " h &nbsp;&nbsp;" . $attendhours_obj->format('%i') . " m";
                } else {
                    $isAllSet += 1;
                }


                if ($isAllSet == 0) {

                    /**** Calculation for late hours ****/
                    // $clockIn_datetime = new DateTime($row['checkIn']);
                    // $onDuty_datetime = new DateTime($row['onDuty']);
                    // if ($clockIn_datetime->format('h:i:s') > $onDuty_datetime->format('h:i:s')) {
                    //     $interval = $clockIn_datetime->diff($onDuty_datetime);
                    //     $hours = ($interval->format('%h') != 0) ? $interval->format('%h') . 'h &nbsp;&nbsp;' : '';
                    //     $lateHours = $hours . '' . $interval->format('%i') . " m";
                    //     $lateHours_arr = array('h' => $hours, 'm' => $interval->format('%i'));
                    // }
                    $clockIn_datetime = new DateTime($row['checkInDate'].' '.$row['checkIn']);
                    $onDuty_datetime = new DateTime($attendanceDate.' '.$row['onDuty']);
                    if ($clockIn_datetime->format('h:i:s') > $onDuty_datetime->format('h:i:s')) {
                        $interval = $clockIn_datetime->diff($onDuty_datetime);
                        $hours = ($interval->format('%h') != 0) ? $interval->format('%h') . 'h &nbsp;&nbsp;' : '';
                        $lateHours = $hours . '' . $interval->format('%i') . " m";
                        $lateHours_arr = array('h' => $hours, 'm' => $interval->format('%i'));
                    }
                }
                $i = $key + 1;
                $empID = $row['empID'];
                $tr_data = $empID;
                $isEvenRow = ($key % 2);
                $class = $empID . '-' . $row['attendanceDate'];
                $attrib = 'Current Employee : ' . $row['ECode'] . ' | ' . $row['Ename2'];

                /***** Pushing null value to a javascript array to set the time to blank ****/

                if ($row['checkIn'] == null) {
                    array_push($clockIn_arr, $i);
                }
                if ($row['checkOut'] == null) {
                    array_push($clockOut_arr, $i);
                }
                $onDuty = ($row['onDuty'] == null) ? '-not set-' : $row['onDuty'];
                $offDuty = ($row['offDuty'] == null) ? '-not set-' : $row['offDuty'];
                array_push($emparr, $class);
                $checked = '';

                $disabled = 'disabled';
                if ($row['approvedYN'] == 1) {
                    $is_filter='approvedYN1';
                }else{
                    $is_filter='approvedYN2';
                }

                $bg='';
                if($row['isWeekEndDay']==1){
                    $bg = 'background-color:rgba(218, 207, 247, 0.42)';
                }



                ?>
                <tr data-detail="<?php echo $attrib ?>" style="white-space: nowrap;<?php echo $bg?>" data-id="<?php echo $i; ?>" data-masterid="<?php echo $row['ID'] ?>"
                    data-value="<?php echo $tr_data; ?>'"
                    data-date="<?php echo $row['attendanceDate']; ?>"
                    class="<?php echo $is_filter; ?>">
                    <td style="" rel="<?php echo $is_filter; ?>" class="fixed-td <?php echo $is_filter; ?>"><center>

                        <?php if ($row['approvedYN'] == 1) { ?>
                            <span class="label label-success"><?php echo  $this->lang->line('common_approved')?><!-- Approved --></span>
                            <!-- <i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> -->
                        <?php
                        } else if ($row['confirmedYN'] == 1) { ?>
                            <span class="label label-warning"><?php echo  $this->lang->line('common_confirmed')?><!-- Confirmed --></span>
                        <?php
                        } else {
                            if ($is_edit) {
                                ?>
                                <span class="label label-danger"><?php echo  $this->lang->line('common_open')?><!-- Open --></span>
                                <!-- <i class="fa fa-times" style="color: #990000" aria-hidden="true"></i> -->
                                <?php
                            } else {
                                ?>
                                <label><input name="ID[]" class="ID" value="<?php echo $row['ID'] ?>" type="checkbox"/> </label>
                                <input name="hiddenID[]" class="hiddenID" value="0" type="hidden"/>
                                <input name="empID[]" class="empID" value="<?php echo $row['empID'] ?>" type="hidden"/>
                                <input name="empName[]" class="empName" value="<?php echo $row['Ename2'] ?>" type="hidden"/>
                                <input name="attendanceDate[]" class="attendanceDate" value="<?php echo $row['attendanceDate'] ?>" type="hidden"/>
                                <input name="leave[]" class="leave" value="0" type="hidden"/>
                            <?php }
                        } ?>
                    </center></td>
                    <td class="fixed-td">
                        <?php echo $row['approvedComment'] ?>
                        <i class="fa fa-comments-o pull-right" title="Comments" onclick="open_leave_conversation(<?=$row['ID'].",'".$row['attendanceDate']."'"?>)"></i>
                    </td>
                    <td class="fixed-td"><?php echo $row['ECode']; ?></td>
                    <td class="fixed-td"><?php echo $row['Ename2']; ?></td>
                    <td style="text-align: center"><?php echo $row['attendanceDate']; ?></td>
                    <td style="text-align: center"><?php echo $row['floorDescription']; ?></td>
                    <td style="text-align: center"><?php echo $onDuty ?></td>
                    <td style="text-align: center"><?php echo $offDuty ?></td>
                    <td>
                        <input type="date" class="trInputs" onchange="update_date(this)" id="checkInDate-<?php echo $i; ?>"  <?php echo $disabled2 ?> name="checkInDate" value="<?php echo ($row['checkInDate']) ? $row['checkInDate'] : $row['attendanceDate']; ?>"/>
                    </td>
                    <td>
                        <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                        <!-- <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"><i
                                class="glyphicon glyphicon-time" style="font-size:10px"></i></span> -->
                        <input type="text" name="checkIn" class="form-control timeTxt trInputs "
                                                        value="<?php echo $row['checkIn']; ?>" style="width:80px"
                                                        id="checkIn-<?php echo $i; ?>" <?php echo $disabled_ar ?>/>
                        </div>  
                    </td>
                    <td>
                        <input type="date" class="trInputs" onchange="update_date(this)" id="checkOutDate-<?php echo $i; ?>"  <?php echo $disabled2 ?> name="checkOutDate" value="<?php echo ($row['checkOutDate']) ? $row['checkOutDate'] : $row['attendanceDate']; ?>"/>
                    </td>
                    <td>
                        <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                            <!-- <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"> <i
                                    class="glyphicon glyphicon-time" style="font-size:10px"></i></span> -->
                            <input type="text" name="checkOut" class="form-control timeTxt trInputs"
                                                            value="<?php echo $row['checkOut']; ?>"
                                                            style="width:80px" id="checkOut-<?php echo $i; ?>" <?php echo $disabled_ar ?>/>
                        </div>
                    </td>
                    <td style="text-align: center realTime"><?php echo $row['realTime'] ?></td>

                    <td>
                        <input type="hidden" class="present" name="present" value="<?php echo $row['presentTypeID']?>">
                        <?php
                            $disabled2 = ($row['presentTypeID'] == 5)? 'disabled': '';
                            echo form_dropdown('presentTypeID[]', $attDrop, $row['presentTypeID'], 'class="attType trInputs" style="width:80px" onchange="modalLeave(this)" ' . $disabled . '   ' . $disabled2 . '');
                        ?>
                    </td>

                    <?php
                    $lateHoursarr = array('h' => gmdate("H", $row['lateHours'] * 60), 'm' => gmdate("i", $row['lateHours'] * 60));
                    $earlyHoursarr = array('h' => gmdate("H", $row['earlyHours'] * 60), 'm' => gmdate("i", $row['earlyHours'] * 60));
                    $OTHoursarr = array('h' => gmdate("H", $row['OTHours'] * 60), 'm' => gmdate("i", $row['OTHours'] * 60));
                    $weekendOTHoursarr = array('h' => gmdate("H", $row['weekendOTHours'] * 60), 'm' => gmdate("i", $row['weekendOTHours'] * 60));
                    $holidayOTHoursarr = array('h' => gmdate("H", $row['holidayOTHours'] * 60), 'm' => gmdate("i", $row['holidayOTHours'] * 60));
                    $NDaysOTsarr = array('h' => gmdate("H", $row['NDaysOT'] * 60), 'm' => gmdate("i", $row['NDaysOT'] * 60));
                    $specialNDaysOTsarr = array('h' => gmdate("H", $row['specialOThours'] * 60), 'm' => gmdate("i", $row['specialOThours'] * 60));

                    $shiftHour = date("H",strtotime($row['offDuty'])) - date("H",strtotime($row['onDuty']));
        
                    if($row['isShiftNextDay'] == 1){
                        $next_att_date = date("Y-m-d",strtotime('+1 days',strtotime($row['attendanceDate'])));
                        $dateWithOnduty = new DateTime($row['attendanceDate'].' '.$row['onDuty']);
                        $dateWithOffduty = new DateTime($next_att_date.' '.$row['offDuty']);
                        $actualWorkingHours_obj = $dateWithOnduty->diff($dateWithOffduty);
                        $shiftHour = $actualWorkingHours_obj->format('%h');//date("H",strtotime($row['onDuty'])) - date("H",strtotime($row['offDuty']));
                    }

                    ?>
                    <td align="right" class="lateHours"><?php echo gmdate("H:i", $row['lateHours'] * 60); ?></td>
                    <td align="right" class="earlyHours"><?php echo gmdate("H:i", $row['earlyHours'] * 60); ?></td>
                    <td align="right" class="OTHours"><?php echo gmdate("H:i", $row['OTHours'] * 60); ?></td>
                    <td align="right" class=""><?php echo $totWorkingHours; ?></td>
                    <td align="center" class="attendhours"><?php echo $attendhours ?></td>
                    <td align="center" class="normalDay"><?php echo $row['normalDay']?></td>
                    <td align="center" class="weekend"><?php echo $row['weekend']?></td>
                    <td align="center" class="holiday"><?php echo $row['holiday']?></td>
                    <td align="center" class="NDaysOT"><?php echo gmdate("H:i", $row['NDaysOT'] * 60); ?></td>
                    <td align="center" class="weekendOTHours"><?php echo gmdate("H:i", $row['weekendOTHours'] * 60); ?></td>
                    <td align="center" class="holidayOTHours"><?php echo gmdate("H:i", $row['holidayOTHours'] * 60); ?></td>
                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>
</div>
<span class="empDetail"></span>

<script>

    $('.timeTxt').timepicker({

        defaultTime: false, showMeridian: true
    }).on('changeTime.timepicker', function (e) {

        value = e.time.value;
        trID = $(this).closest('tr').attr('data-id');
        masterID = $(this).closest('tr').attr('data-masterid');
        name = $(this).attr('name');

        updatefields(trID, masterID, value, name);
    });

    // $(".timeTxt").change(function () {
    //     var input = $(this);

    //     if (input.val() == '') {
    //         trID = input.closest('tr').attr('data-id');
    //         masterID = input.closest('tr').attr('data-masterid');
    //         name = input.attr('name');
    //         value = input.val();

    //         updatefields(trID, masterID, value, name);
    //     }

    // });

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
    
    async function load_swal_input(trID, masterID, value, name, date = null){
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

    function update_date(other){

        trID = $(other).closest('tr').attr('data-id');
        masterID = $(other).closest('tr').attr('data-masterid');
        name = $(other).attr('name');
        value = other.value;

        updatefields(trID, masterID, value, name);

    }
</script>
<?php
