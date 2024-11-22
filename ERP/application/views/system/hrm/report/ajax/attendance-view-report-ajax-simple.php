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
<!-- <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
           
                //echo export_buttons('attendancereport', 'Attendance Report', True, false);
           ?>
        </div>clockinFloorDescription
    </div> -->
<div class="row">
     <div class="col-sm-4">
        <!--<table class="table table-bordered table-striped table-condensed table-row-select" style="">
            <tbody>
                <tr>
                    <td> -->
                        <!-- <label for="filter_1"></label><i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> <?php //echo  $this->lang->line('common_approved')?> --><!--Approved-->
                    <!-- </td>
                </tr>
            </tbody>
        </table>-->
    </div> 
    <div class="col-sm-6">&nbsp;</div>
    <div class="col-sm-2 pull-right">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>
                <td>
                    <span class="label" style="padding: 0px 5px ;font-size: 100%;background-color: #dacff7">&nbsp;</span>&nbsp;<?php echo  $this->lang->line('hrms_attendance_shift_weekend')?>&nbsp;&nbsp; <!--Shift Weekend-->
                </td>
            </tr>
        </table>
    </div>
</div>
<br>

<span class="empDetail"></span>
<div style="max-height: 400px">
    <div class="col-md-12 " id="attendancereport">
    <table id="attendanceReview" class="table first attendanceReview">
        <thead>
        <tr style="white-space: nowrap">
            <th style=""><?php echo $this->lang->line('common_status'); ?><!-- Status --></th>
            <th style=""><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--Emp Name--></th>
            <th style=""><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
           
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_in'); ?><!--Clock In--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_clock_out'); ?><!--Clock Out--></th>
            <th style=" "><?php echo $this->lang->line('hrms_attendance_real_time'); ?><!--Real Time--></th>
            <th style=""><?php echo $this->lang->line('hrms_attendance_present'); ?><!--Present--></th>
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

        if (!empty($att_rec)) {
            foreach ($att_rec as $key => $row) {
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
                    <td style="" rel="<?php echo $isfilter; ?>" class="fixed-td <?php echo $isfilter; ?>"><center>

                        <?php if ($row['approvedYN'] == 1) {
                            ?>
                            <span class="label label-success"><?php echo  $this->lang->line('common_approved')?><!-- Approved --></span>
                            <!-- <i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> -->
                        <?php } else if ($row['confirmedYN'] == 1) {
                        ?>
                            <span class="label label-warning"><?php echo  $this->lang->line('common_confirmed')?><!-- Confirmed --></span>
                        <?php } else {    
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
                    <td class="fixed-td">
                        <?php echo $row['Ename2']; ?>
                    </td>
                    <td style="text-align: center">
                        <?php echo $row['attendanceDate']; ?>
                    </td>
                    <td>
                        <?php echo $row['checkIn'] ?>
                    </td>
                    <td>
                        <?php echo $row['checkOut']; ?>
                    </td>

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
                 
                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>
</div>
</div>
<span class="empDetail"></span>

<script>
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
</script>

<?php
