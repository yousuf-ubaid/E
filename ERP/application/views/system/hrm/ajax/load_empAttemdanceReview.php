<style type="text/css">

    .fixHeader_Div {
        height: 340px;
        border: 1px solid #c0c0c0;
    }

    /*
        div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            //border-radius: 3px;
        }

        div.fixHeader_Div::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb  {
            margin-left: 30px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
            width: 3px;
            position: absolute;
            top: 0px;
            opacity: 0.4;
            border-radius: 7px;
            z-index: 99;
            right: 1px;
            height: 40px;
        }
    */

    #attendanceReview td {
        vertical-align: middle;
    }

    #attendanceReview tr:hover > td {
        background: #86a5c3 !important; /*#2c4762*/
        color: #ffffff;
    }

    #attendanceReview tr:hover > td.fixed-td {
        background: #86a5c3 !important; /*#2c4762*/
        color: #ffffff;
    }

    #attendanceReview .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        height: 22px;
        padding: 0px 5px;
    }

    #attendanceReview .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 18px !important;
    }

    .attType {
        height: 22px;
        padding: 2px;
        font-size: 12px;
    }

    .fixed-td {
        z-index: 10;
    }

    /*.oddTR>.fixed-td{ background: #3cd6e6 !important; z-index: 10; color: #f3f3f3 }
    .evenTR>.fixed-td{ background: #97eaf4 !important; z-index: 10 }*/

</style>

<script type="text/javascript">
    var clockIn_arr = [];
    var clockOut_arr = [];
</script>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-4 col-xs-5" style="">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>&nbsp;&nbsp;
                    More than one occurrences
                </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-3 hidden-xs">&nbsp;</div>
    <div class="col-sm-2 col-xs-3">
        <?php
        $search_dateRange = array('' => 'Search Date');
        $frmDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        if (!empty($frmDate) && !empty($toDate)) {
            $begin = new DateTime($frmDate);
            $end = new DateTime($toDate);
            $end = $end->add(new DateInterval('P1D'));

            $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $end);

            foreach ($dateRange as $date) {
                $val = $date->format("Y-m-d");
                $search_dateRange[$val] = $val;
            }
        }
        echo form_dropdown('searchDate', $search_dateRange, null, 'class="form-control pull-right" id="searchDate" style="max-width:120px" onchange="searchWithDate(this)"');
        ?>
    </div>
    <div class="col-sm-3 col-xs-4">
        <input type="text" class="form-control" id="attReview-searchItem" value="" placeholder="Search Name | Code">
    </div>
</div>

<div class="table-responsive" style="padding: 0px !important;">
    <div class="fixHeader_Div" style="max-width: 100%;">
        <table id="attendanceReview" class="<?php echo table_class(); ?>" style="width: 1750px !important;">
            <thead>
            <!--<tr>
                <th colspan="13">&nbsp;</th>
                <th colspan="5" style="background-color: #abb0bb">Over Time</th>
            </tr>-->
            <tr>
                <th style="width: 15px; background: #4a88bf">#</th>
                <th style="width: 100px; background: #4a88bf">EMP Code</th>
                <th style="min-width: 120px; background: #4a88bf">Emp Name</th>
                <th style="width: 85px"> Date</th>
                <th style="width: 85px"> Department</th>
                <th style="width: 120px">On Duty Time</th>
                <th style="width: 120px">Off Duty Time</th>
                <th style="z-index: 10; width: 115px">Clock In</th>
                <th style="z-index: 10; width: 115px">Clock Out</th>
                <th style="width: 105px">Absent</th>
                <th style="width: 80px">Late</th>
                <th style="width: 80px">Early</th>
                <th style="width: 100px">OT Time</th>
                <th style="width: 100px">Work Time</th>
                <th style="width: 100px">Week End</th>
                <th style="width: 100px">Holiday</th>
                <th style="width: 100px;background-color: #abb0bb">ATT_Time</th>
                <th style="width: 80px;background-color: #abb0bb">NDays OT</th>
                <th style="width: 95px;background-color: #abb0bb">Weekend OT</th>
                <th style="width: 80px;background-color: #abb0bb">Holiday OT</th>
                <th style="width: 25px; background: #4a88bf; z-index: 10; border-left: 1px solid #d01d33">&nbsp;</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $appData = '';
            $disabled = '';
            $attDrop = attendanceType_drop();
            $attDate = $this->input->post('attDate');
            $empArray = array();

            if (!empty($attData['tempAttData'])) {
                foreach ($attData['tempAttData'] as $key => $row) {
                    $i = $key + 1;
                    $empID = $row['EIdNo'];
                    $attendanceDate = $row['actualDate']; //$row['attDate'];
                    $tr_data = $row['ECode'] . ' ' . $row['Ename1'] . ' ' . $attendanceDate . ' ' . $row['floorDescription'];
                    $clockIn = $row['attTime'];
                    $clockOut = $row['offTime'];
                    $totWorkingHours = '';
                    $actualWorkingHours_obj = null;
                    $totWorkingHours_obj = null;
                    $actualWorkingHours = '';
                    $lateHours = '';
                    $earlyHours = '';
                    $overTimeHours = '';
                    $weekendOTHours = '';
                    $isAllSet = 0; //  shift && check in and check out are not null
                    $onDuty = ($row['onDutyTime'] == null) ? '-not set-' : $row['onDutyTime'];
                    $offDuty = ($row['offDutyTime'] == null) ? '-not set-' : $row['offDutyTime'];
                    $AttPresentTypeID = '';

                    array_push($empArray, $empID);

                    /************ Calculate the actual working hours *************/
                    if ($row['onDutyTime'] != null && $row['offDutyTime'] != null) {
                        $datetime1 = new DateTime($onDuty);
                        $datetime2 = new DateTime($offDuty);
                        $actualWorkingHours_obj = $datetime1->diff($datetime2);
                        $minutes = ($actualWorkingHours_obj->format('%i') != 0) ? $actualWorkingHours_obj->format('%i') . ' m' : '';
                        $actualWorkingHours = $actualWorkingHours_obj->format('%h') . " h " . $minutes;
                    } else {
                        $isAllSet += 1;
                    }


                    /***** Pushing null value to a javascript array to set the time to blank ****/
                    if ($clockIn == null) {
                        echo '<script>clockIn_arr.push(' . $i . ')</script>';
                    }

                    if ($clockOut == null) {
                        echo '<script>clockOut_arr.push(' . $i . ')</script>';
                    }

                    /****** Employee total working hours for this day ******/
                    if ($clockIn != null && $clockOut != null) {
                        $datetime1 = new DateTime($clockIn);
                        $datetime2 = new DateTime($clockOut);
                        $totWorkingHours_obj = $datetime1->diff($datetime2);
                        $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";
                    } else {
                        $isAllSet += 1;
                    }


                    if ($isAllSet == 0) {

                        /**** Calculation for late hours ****/
                        $datetime1 = new DateTime($clockIn);
                        $datetime2 = new DateTime($onDuty);
                        if ($datetime1->format('h:i:s') > $datetime2->format('h:i:s')) {
                            $interval = $datetime1->diff($datetime2);
                            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') . 'h &nbsp;&nbsp;' : '';
                            $lateHours = $hours . '' . $interval->format('%i') . " m";
                        }


                        /**** Calculation for early hours ****/
                        $datetime1 = date('Y-m-d H:i:s', strtotime($clockOut));
                        $datetime2 = date('Y-m-d H:i:s', strtotime($offDuty));
                        if ($datetime1 < $datetime2) {
                            $datetime1 = new DateTime($clockOut);
                            $datetime2 = new DateTime($offDuty);
                            $interval = $datetime2->diff($datetime1);
                            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') . 'h &nbsp;&nbsp;' : '';
                            $earlyHours = $hours . '' . $interval->format('%i') . " m";
                        }


                        /**** Calculation for over time hours ****/
                        if ($actualWorkingHours_obj->format('%h %i') < $totWorkingHours_obj->format('%h %i')) {
                            $totW = new DateTime($totWorkingHours_obj->format('2015-01-01 %h:%i:%s'));
                            $actW = new DateTime($actualWorkingHours_obj->format('2015-01-01 %h:%i:%s'));
                            $overTime_obj = $actW->diff($totW);
                            $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') . 'h &nbsp;&nbsp;' : '';
                            $overTimeHours = $hours . '' . $overTime_obj->format('%i') . " m";

                        }
                    }

                    if ($earlyHours != '') {
                        $AttPresentTypeID = 5;
                    }
                    /**** Presented Earlier *****/
                    if ($lateHours != '') {
                        $AttPresentTypeID = 2;
                    }
                    /**** Presented Later*****/
                    if ($earlyHours != '' && $lateHours != '' & $isAllSet == 0) {
                        $AttPresentTypeID = 1;
                    }
                    /**** Presented On time *****/
                    if ($clockIn == null && $clockOut == null) {
                        $AttPresentTypeID = 4;
                    }
                    /**** Absents *****/

                    if ($row['isWeekend'] == 1) {
                        $weekendOTHours = $totWorkingHours;
                    }


                    /*$isEvenRow = ($key % 2);
                    $class = ( $isEvenRow == 0 )? 'oddTR' : 'evenTR';*/
                    $class = ' emp_' . $empID;
                    ?>
                    <tr data-value="<?php echo $tr_data; ?>'" data-date="<?php echo $attendanceDate; ?>"
                        class="<?php echo $class; ?>">
                        <td class="fixed-td"><?php echo $i; ?></td>
                        <td class="fixed-td"><?php echo $row['ECode']; ?></td>
                        <td class="fixed-td"><?php echo $row['Ename1']; ?></td>
                        <td style="text-align: center"><?php echo $attendanceDate; ?></td>
                        <td style="text-align: center"><?php echo $row['floorDescription']; ?></td>
                        <td style="text-align: center"><?php echo $onDuty; ?></td>
                        <td style="text-align: center"><?php echo $offDuty; ?></td>
                        <td>
                            <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                            <span class="input-group-addon" style="padding:0px 7px; font-size: 10px">
                            <i class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                                <input type="text" name="clock-in[]" class="form-control timeTxt trInputs"
                                       value="<?php echo $clockIn; ?>" style="width:80px"
                                       id="cl-in-<?php echo $i; ?>"/>
                            </div>
                        </td>
                        <td>
                            <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                             <span class="input-group-addon" style="padding:0px 7px; font-size: 10px">
                             <i class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                                <input type="text" name="clock-out[]" class="form-control timeTxt trInputs"
                                       value="<?php echo $clockOut; ?>"
                                       style="width:80px" id="cl-out-<?php echo $i; ?>"/>
                            </div>
                        </td>
                        <td>
                            <?php
                            echo form_dropdown('att-type[]', $attDrop, $AttPresentTypeID, 'class="form-control select2 attType" onchange="attStatus(this)" ' . $disabled . '');
                            ?>
                        </td>
                        <td align="right"><?php echo $lateHours; ?></td>
                        <td align="right"><?php echo $earlyHours; ?></td>
                        <td align="right"><?php echo $overTimeHours; ?></td>
                        <td align="right"><?php echo $totWorkingHours; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?php echo $weekendOTHours; ?></td>
                        <td></td>
                        <td class="fixed-td"><span class="glyphicon glyphicon-trash" style="color:#c3331b;"
                                                   onclick="removeTr(this)"></span></td>
                    </tr>
                    <?php
                }
            } else {
                $appData = '<tr><td colspan="21">No data available in table</td></tr>';
            }

            echo $appData;
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php
/*echo '<pre>'; print_r($empArray); echo '</pre>';*/
?>
<div class="row">
    <div class="" style="margin-top: 1% !important;">
        <div class="col-sm-12" style="margin-top: 1px">
            <label>
                Showing <span id="attReview-showingCount"> <?php echo count($attData['tempAttData']); ?> </span> of
                <span id="attReview-totalRowCount"> <?php echo count($attData['tempAttData']); ?> </span> entries
            </label>
        </div>
    </div>
</div>

<?php if (!empty($attData['unAssignedMachineID']) || !empty($attData['unAssignedMachineID'])) { ?>
    <hr>
<?php } ?>

<div class="row" id="unAssignedDiv"> <!-- Un assigned div-->
    <?php if (!empty($attData['unAssignedShifts'])) { ?>
        <div class="col-sm-6" id="unAssignedShift-div"> <!-- Shift not assigned -->
            <h4>Employees not assigned to shift</h4>
            <div class="table-responsive" style="padding: 0px !important;">
                <table id="unAssignedShift" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="width: 15px">#</th>
                        <th style="width: 100px">EMP Code</th>
                        <th>Emp Name</th>
                        <th style="width: 80px"></th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php
                    if (count($attData['unAssignedShifts']) < 1) {
                        echo '<tr> <td colspan="4">&nbsp;</td> </tr>';
                    } else {
                        $appData = '';
                        foreach ($attData['unAssignedShifts'] as $key => $row) {
                            $i = $key + 1;
                            $appData .= '<tr>';
                            $appData .= '<td class="fixed-td">' . $i . '</td>';
                            $appData .= '<td class="fixed-td">' . $row['ECode'] . '</td>';
                            $appData .= '<td class="fixed-td">' . $row['Ename1'] . '</td>';
                            $appData .= '<td></td>';
                            $appData .= '</tr>';
                        }
                        echo $appData;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div> <!-- Shift not assigned -->
    <?php } ?>

    <?php if (!empty($attData['unAssignedMachineID'])) { ?>
        <div class="col-sm-6" id="unAssignedMachine-div"> <!-- Machine not assigned -->
            <h4>Employees un assigned to machine</h4>
            <div class="table-responsive" style="padding: 0px !important;">
                <table id="unAssignedMachine" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="width: 15px">#</th>
                        <th style="width: 100px">EMP Code</th>
                        <th>Emp Name</th>
                        <th style="width: 80px"></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    if (count($attData['unAssignedMachineID']) < 1) {
                        echo '<tr> <td colspan="4">&nbsp;</td> </tr>';
                    } else {
                        $appData = '';
                        foreach ($attData['unAssignedMachineID'] as $key => $row) {
                            $i = $key + 1;
                            $appData .= '<tr>';
                            $appData .= '<td class="fixed-td">' . $i . '</td>';
                            $appData .= '<td class="fixed-td">' . $row['ECode'] . '</td>';
                            $appData .= '<td class="fixed-td">' . $row['Ename1'] . '</td>';
                            $appData .= '<td></td>';
                            $appData .= '</tr>';
                        }
                        echo $appData;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div> <!-- Machine not assigned -->
    <?php } ?>
</div>


<script type="text/javascript">
    var attendanceReview = $('#attendanceReview');

    $(document).ready(function () {
        $('.timeTxt').timepicker();
        $('.select21').select2();

        /*$('#attendanceReview').tableHeadFixer({
         head: true,
         foot: true,
         left: 3,
         right: 1,
         'z-index': 10
         });*/

        var x = 0;
        while (x < clockIn_arr.length) {
            $('#cl-in-' + clockIn_arr[x]).val('');
            x++;
        }

        var y = 0;
        while (y < clockOut_arr.length) {
            $('#cl-out-' + clockOut_arr[y]).val('');
            y++;
        }
    });

    $('#attReview-searchItem').keyup(function () {

        var dateSearch = $.trim($('#searchDate').val());
        var searchKey = $.trim($(this).val()).toLowerCase();
        var tableTR = $('#attendanceReview tbody>tr');
        tableTR.removeClass('hideTr evenTR oddTR');


        tableTR.each(function () {
            var dataValue = '' + $(this).attr('data-value') + '';
            var dateValue = '' + $(this).attr('data-date') + '';
            dataValue = dataValue.toLocaleLowerCase();

            if (searchKey != '') {
                if (dataValue.indexOf('' + searchKey + '') == -1) {
                    $(this).addClass('hideTr');
                }
                else {
                    if (dateSearch != '') {
                        if (dateValue.indexOf('' + dateSearch + '') == -1) {
                            $(this).addClass('hideTr');
                        }
                    }
                }
            }
            else {
                if (dateSearch != '') {
                    if (dateValue.indexOf('' + dateSearch + '') == -1) {
                        $(this).addClass('hideTr');
                    }
                }
            }
        });

        attReview_applyRowNumbers();

    });


    function attReview_applyRowNumbers() {
        var m = 1;
        $('#attendanceReview tbody>tr').each(function (i) {
            if (!$(this).hasClass('hideTr')) {
                var isEvenRow = ( m % 2 );
                if (isEvenRow == 0) {
                    $(this).addClass('evenTR');
                } else {
                    $(this).addClass('oddTR');
                }

                $(this).find('td:eq(0)').html(m);
                m += 1;
            }
        });

        $('#attReview-showingCount').text((m - 1));
    }

    function searchWithDate() {
        $('#attReview-searchItem').keyup();
    }

    function attStatus(obj) {

    }

    function removeTr(obj) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this row!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $(obj).closest('tr').remove();
                $('#attReview-searchItem').keyup();
                $('#attReview-totalRowCount').text($('#attendanceReview tbody>tr').length);
            }
        );
    }
</script>


<?php
