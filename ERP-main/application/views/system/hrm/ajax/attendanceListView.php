<!--<input class="prod_capacity" checked rel="approvedYN1" type="checkbox" value="approvedYN1">
<label for="filter_1"></label><i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> Approved<br>
<input class="prod_capacity" checked rel="approvedYN2" type="checkbox" value="approvedYN2">
<label for="filter_2"></label><i class="fa fa-times" style="color: #990000" aria-hidden="true"></i> Not Approved-->

<!--
<span class="hideremove">
<input type="checkbox" id="checkAll"/> Select All</label>
</span>-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_over_time_lang', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine_attendance_management');
echo head_page($title, false);
?>
<style>
  /*  tr:hover, tr.selected {
        background-color: #FFCF8B
    }*/

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

  /*  tbody td {
       // background-color: #EEEEEE;
    }*/

/*  #attendance tr td:first-child,
  #attendance tr th:first-child {
      border-top-left-radius: 6px;
      border-bottom-left-radius: 6px;
  }

  #attendance tr td:last-child,
  #attendance tr th:last-child {
      border-top-right-radius: 6px;
      border-bottom-right-radius: 6px;
  }*/
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
<div class="row">
    <div class="col-sm-4">
        <table class="table table-bordered table-striped table-condensed table-row-select" style="">
            <tbody>
            <tr>
                <td>
                    <label style="    margin: 2px;">   <i class="fa fa-filter"></i> <?php echo $this->lang->line('common_filter')?><!--Filter--></label>

                </td>
                <td>
                    <input class="prod_capacity" checked rel="approvedYN1" type="checkbox" value="approvedYN1">
                    <label for="filter_1"></label><i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> <?php echo $this->lang->line('common_approved')?><!--Approved-->
                </td>
                <td>
                    <input class="prod_capacity" checked rel="approvedYN2" type="checkbox" value="approvedYN2">
                    <label for="filter_2"></label><i class="fa fa-times" style="color: #990000" aria-hidden="true"></i> <?php echo $this->lang->line('common_not_approved')?><!--Not Approved-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-2" style="">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>

                <td><span class="label"
                          style="padding: 0px 5px ;font-size: 100%;background-color: #dacff7">&nbsp;</span>&nbsp;&nbsp;
                    <!--Shift Weekend--><?php echo $this->lang->line('hrms_over_time_shift_weekend')?>
                </td>
            </tr>
        </table>
    </div>
</div>
<span class="empDetail"></span>
<div style="max-height: 400px">

    <table  id="attendanceReview" class="table first attendanceReview">
        <thead>

        <tr style="white-space: nowrap">

            <th style=""></th>
            <th style=""><?php echo $this->lang->line('common_comment')?><!--Comment--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--Emp Name--></th>
            <th style=""><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
            <th style="">Location in</th>
            <th style="">Location Out</th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_on_duty_time'); ?><!--On Duty Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_off_duty_time'); ?><!--Off Duty Time--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_in'); ?><!--Clock In--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_out'); ?><!--Clock Out--></th>
            <!--<th style=" ">Normal Time</th>-->
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
            <th style=""><?php echo $this->lang->line('common_action'); ?><!--Holiday OT--></th>
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
        /****** Employee total working hours for this day ******/

        if (!empty($tempAttData)) {
            foreach ($tempAttData as $key => $row) {
                $totWorkingHours = '';
                $attendhours = '';
                $isAllSet = 0;
                if ($row['checkIn'] != null && $row['checkOut'] != null && $row['offDuty'] != null) {
                    $datetime1 = new DateTime($row['offDuty']);

                    if($row['onDuty'] >=$row['checkIn']){
                        $datetime2= new DateTime($row['onDuty']);
                    }else{
                        $datetime2 = new DateTime($row['checkIn']);
                    }
                    $totWorkingHours_obj = $datetime1->diff($datetime2);
                    $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";
                }
                if ($row['checkIn'] != null && $row['checkOut'] != null) {
                    $datetime1 = new DateTime($row['checkIn']);
                    $datetime2 = new DateTime($row['checkOut']);
                    $attendhours_obj = $datetime1->diff($datetime2);
                    $attendhours = $attendhours_obj->format('%h') . " h &nbsp;&nbsp;" . $attendhours_obj->format('%i') . " m";
                } else {
                    $isAllSet += 1;
                }


                if ($isAllSet == 0) {

                    /**** Calculation for late hours ****/
                    $clockIn_datetime = new DateTime($row['checkIn']);
                    $onDuty_datetime = new DateTime($row['onDuty']);
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
                //  $class = ($isEvenRow == 0) ? 'oddTR' : 'evenTR';
                // $class .= ' emp_' . $empID;

                /***** Pushing null value to a javascript array to set the time to blank ****/
                /*if($clockIn == null){ echo '<script>clockIn_arr.push('.$i.')</script>'; }

                if($clockOut == null){ echo '<script>clockOut_arr.push('.$i.')</script>'; }*/

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
                        $isfilter='approvedYN1';
                }else{
                        $isfilter='approvedYN2';
                }

                $bg='';
                if($row['isWeekEndDay']==1){
                    $bg = 'background-color:rgba(218, 207, 247, 0.42)';
                }



                ?>
                <tr data-detail="<?php echo $attrib ?>" style="white-space: nowrap;<?php echo $bg?>" data-id="<?php echo $i; ?>" data-masterid="<?php echo $row['ID'] ?>"
                    data-value="<?php echo $tr_data; ?>'"
                    data-date="<?php echo $row['attendanceDate']; ?>"
                    class="<?php echo $isfilter; ?>">
                    <td style="" rel="<?php echo $isfilter; ?>" class="fixed-td <?php echo $isfilter; ?>">

                        <?php if ($row['approvedYN'] == 1) {
                            ?>
                            <i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i>
                            <?php
                        } else {
                            if ($hideedit) {
                                ?>

                                <i class="fa fa-times" style="color: #990000" aria-hidden="true"></i>
                                <?php

                            } else {
                                ?>
                                <label><input name="ID[]" class="ID" value="<?php echo $row['ID'] ?>" type="checkbox"/>
                                </label>
                                <input name="hiddenID[]" class="hiddenID" value="0" type="hidden"/>
                                <input name="empID[]" class="empID" value="<?php echo $row['empID'] ?>" type="hidden"/>
                                <input name="empName[]" class="empName" value="<?php echo $row['Ename1'] ?>"
                                       type="hidden"/>
                                <input name="attendanceDate[]" class="attendanceDate"
                                       value="<?php echo $row['attendanceDate'] ?>" type="hidden"/>
                                <input name="leave[]" class="leave"
                                       value="0" type="hidden"/>

                            <?php }
                        } ?>
                    </td>
                    <td class="fixed-td">

                        <?php echo $row['approvedComment'] ?>
                    </td>
                    <!--    <td class="fixed-td">

         <?php /*echo $i; */ ?>
            </td>-->
                    <td class="fixed-td"><?php echo $row['ECode']; ?></td>
                    <td class="fixed-td">
                        <?php echo $row['Ename2']; ?>
                    </td>
                    <td style="text-align: center">
                        <?php echo $row['attendanceDate']; ?>

                    </td>
                    <td style="text-align: center"><?php echo $row['clockinFloorDescription']; ?></td>
                    <td style="text-align: center"><?php echo $row['clockoutFloorDescription']; ?></td>
                    <td style="text-align: center">
                        <?php echo $onDuty ?>

                    </td>
                    <td style="text-align: center">
                        <?php echo $offDuty ?>

                    </td>
                    <td>
                        <?php echo $row['checkIn'] ?>

                    </td>
                    <td>
                        <?php echo $row['checkOut']; ?>
                    </td>
                    <!--<td style="text-align: center"> <?php /*echo $row['normalTime'] */?></td>-->
                    <td style="text-align: center">
                        <?php echo $row['realTime'] ?>

                    </td>

                    <td>
                        <input type="hidden" class="present" name="present" value="<?php echo $row['presentTypeID']?>">
                        <?php
                        $disabled2='';
                        if($row['presentTypeID']==5){
                            $disabled2='disabled';
                        }
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
                    ?>
                    <td align="right"><?php echo gmdate("H:i", $row['lateHours'] * 60); ?></td>
                    <td align="right"><?php echo gmdate("H:i", $row['earlyHours'] * 60); ?></td>
                    <td align="right"><?php echo gmdate("H:i", $row['OTHours'] * 60); ?></td>
                    <td align="right"><?php echo $totWorkingHours; ?></td>
                    <td align="center"><?php echo $attendhours ?></td>
                    <td align="center"><?php echo $row['normalDay']?></td>
                    <td align="center"><?php echo $row['weekend']?></td>
                    <td align="center"><?php echo $row['holiday']?></td>
                    <td align="center"><?php echo gmdate("H:i", $row['NDaysOT'] * 60); ?></td>
                    <td align="center"><?php echo gmdate("H:i", $row['weekendOTHours'] * 60); ?></td>
                    <!-- $weekendOTHours -->
                    <td align="center"><?php echo gmdate("H:i", $row['holidayOTHours'] * 60); ?></td>
                    <td><a class="btn btn-primary btn-outline" onclick="change_job_select(<?php echo $row['attendanceDate'].','.$row['empID'].',1' ?>)"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></a></td>

                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>
</div>
<span class="empDetail"></span>

<script>
  /*  $("#attendance tr").click(function(){
        $(this).addClass("selected").siblings().removeClass("selected");
    });*/
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

        display =$(this).attr("rel");


        if ($( this ).is(':checked')){
            $("#StatusTable tr."+display).show();
        }
        else{
            $("#StatusTable tr."+display).hide();
        }


    });

    function  filterapprove() {

    }


    $('.ID').click(function () {
        if ($(this).is(":checked")) {

            $(this).closest('tr').find('.hiddenID').val(1);
        }
        else {

            $(this).closest('tr').find('.hiddenID').val(0);
        }

    });

    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

    function modalLeave(id) {
        if (id.value == 4) {
            var empID = $(id).closest('tr').find('.empID').val();
            var attendanceDate = $(id).closest('tr').find('.attendanceDate').val();
            dataID = $(id).closest('tr').attr('data-id');

            get_leaveType(empID, attendanceDate, dataID)
        }
        else {
            dataID = $(id).closest('tr').attr('data-id');
            $("tr[data-id='" + trID + "']").find(".leave").val(0);
        }

    }

    function leaveClose() {
        alert();
        trID = $('#trID').val();
        trempID = $('#trempID').val();

        $('#modalleave').modal('hide');


       value= $("tr[data-id='" + trID + "']").find(".present").val();
        $("tr[data-id='" + trID + "']").find(".leave").val(value);
    }

    function saveLeave() {
        trID = $('#trID').val();
        trempID = $('#trempID').val();
        leave = $('#leaveTypeID').val();
        $("tr[data-id='" + trID + "']").find(".leave").val(leave);
        $('#modalleave').modal('hide');


    }

    function get_leaveType(empID, attendanceDate, dataID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {empID: empID, attendanceDate: attendanceDate},
            url: "<?php echo site_url('Employee/attendancegetLeave'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                    $("tr[data-id='" + dataID + "']").find(".attType").val('');
                }
                if (data['error'] == 0) {

                    $('#modalleave').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#leaveType').html(data['message']);
                    $('#trID').val(dataID);
                    $('#trempID').val(empID);


                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function change_job_select(date,empID,view = ''){

        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {'date': date,'empID':empID,'view':view},
            url: '<?php echo site_url('Employee/get_manufature_job'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#job_change_section').empty();
                $('#job_change_section').html(data);
                $('#modal_load_job_change').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
</script>

