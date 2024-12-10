<?php

$appData = '';
$disabled = 'disabled';
$attDrop = attendanceType_drop();
$empArray = array();
$clockIn_arr = array();
$clockOut_arr = array();
$emparr = array();
$is_disable_clock_in_out = getPolicyValues('DAT', 'All'); //Disable attendance Clock in / Clock out Time
/****** Employee total working hours for this day ******/

if (!empty($tempAttData)) {
    foreach ($tempAttData as $key => $row) {
        $row['clockinFloorDescription'] = '';
        $row['clockoutFloorDescription'] = '';
        $totWorkingHours = 0;
        $attendhours = '';
        $isAllSet = 0;

        
        $attendanceDate = $row['attendanceDate'];
        $attendanceNextDay = $row['attendanceDate'];
        
        if($row['isShiftNextDay']){
            $attendanceNextDay = date('Y-m-d',strtotime('+1 days',strtotime($attendanceDate)));
        }


        if ($row['checkIn'] != null && $row['checkOut'] != null && $row['offDuty'] != null) {
            $datetime1 = new DateTime($attendanceNextDay.' '.$row['offDuty']);
            $datetime2 = new DateTime($attendanceDate.' '.$row['onDuty']);

            $totWorkingHours_obj = $datetime1->diff($datetime2);
            $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";

        }
        if ($row['checkIn'] != null && $row['checkOut'] != null) {
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
        /*      $tr_data = $empID;*/
        $tr_data = $row['ECode'] . ' ' . $row['Ename2'] . ' ' . $row['attendanceDate'] . ' ' . $row['clockinFloorDescription'];
        $tr_data2 = $row['empID'] . '_' . $row['attendanceDate'] . '_' . $row['floorID'];

        $datacode = 'Current Employee : ' . $row['ECode'] . ' | ' . $row['Ename2'];


        $isEvenRow = ($key % 2);
        $class = ($isEvenRow == 0) ? 'oddTR' : 'evenTR';
        $class .= ' emp_' . $empID;

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
        array_push($emparr, $tr_data2);
        $disabled2 = '';
        if ($row['isCheckin'] == 1) {
           // $disabled2 = 'disabled';
        }
        $bg = '';
        if ($row['isWeekEndDay'] == 1) {
            $bg = 'background-color:rgba(218, 207, 247, 0.42)';
        }

        if ($row['isHoliday'] == 1) {
            $bg = 'background-color:rgb(249, 177, 168)';
        }

        if($row['isMultipleOcc'] == 1){
            $bg = 'background-color:#ffff0082';
        }

        $row_id = $row['ID'];
        ?>
        <tr data-code="<?php echo $datacode; ?>" style="white-space: nowrap;<?php echo $bg ?>"
            data-duplicate="<?php echo $tr_data2; ?>" data-id="<?php echo $i; ?>"
            data-masterid="<?=$row_id?>" data-value="<?=$tr_data?>"
            data-date="<?php echo $row['attendanceDate']; ?>"
            class="<?php echo $class; ?> <?php echo $tr_data2; ?>">
            <td style="opacity: 100" class="fixed-td"><?php echo $i; ?></td>
            <td style="opacity: 100" class="fixed-td"><?php echo $row['ECode']; ?></td>
            <td style="opacity: 100" class="fixed-td">
                <?php echo $row['Ename2']; ?>
                <input type="hidden" name="empID[]" value="<?php echo $empID; ?>"/>
                <input type="hidden" name="machineID[]" value="<?php echo $row['empMachineID']; ?>"/>
                <input type="hidden" name="floorID[]" value="<?php echo $row['floorID']; ?>"/>
            </td>
            <td style="text-align: center">
                <?php
                if($row['isMultipleOcc'] == 1){
                    $occ_str = $row_id.', \''.$row['ECode'].' - '.$row['Ename2'].'\', \'Check in or Check out on '.$row['attendanceDate'].'\'';

                    echo '<span class="occurrence-popover" onclick="multiple_occ_in_popup('.$occ_str.')">'.$row['attendanceDate'].'</span>';
                }
                else{
                    echo $row['attendanceDate'];
                }
                ?>
                <input type="hidden" name="attDate[]" value="<?php echo $row['attendanceDate']; ?>"/>
            </td>

            <td style="text-align: center"><?php echo $row['contractReference']; ?></td>
            <td style="text-align: center"><?php echo $row['jobReference']; ?></td>
            <!-- <td style="text-align: center"> //$row['clockinFloorDescription'];</td> -->
            <!-- <td style="text-align: center">//$row['clockoutFloorDescription'];</td> -->
            <td style="text-align: center">
                <?php echo $onDuty ?>
                <input type="hidden" name="onDuty[]" value="<?php echo $row['onDuty']; ?>"/>
            </td>
            <td style="text-align: center">
            <div class="" style="width: 55px">
                <div class="input-group">
                    <span class="input-group-btn">
                        <input type="text" name="grace_hours" class="trInputs timeBox txtH number grace_hours" style="width: 25px" value="<?=$row['graceperiodhrs']?>" disabled>
                    </span>
                <span style="font-size: 14px; font-weight: bolder"> : </span>
                    <span class="input-group-btn">
                        <input  type="text" name="grace_mins" class="trInputs  timeBox txtM number grace_mins" style="width: 25px" value="<?=$row['gracemins']?>" disabled>
                    </span>
                </div>
            </div>
            </td>

            <td style="text-align: center">
                <?php echo $offDuty ?>
                <input type="hidden" name="offDuty[]" value="<?php echo $row['offDuty']; ?>"/>
            </td>
            <td>
                <input type="date" class="trInputs" onchange="update_date(this)" id="checkInDate-<?php echo $i; ?>"  <?php echo $disabled2 ?> name="checkInDate" value="<?php echo ($row['checkInDate']) ? $row['checkInDate'] : $row['attendanceDate']; ?>"/>
            </td>
            <td>
                <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"><i
                            class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                    <input <?php echo $disabled2 ?> type="text" name="checkIn" class="form-control timeTxt trInputs "
                                                    value="<?php echo $row['checkIn']; ?>" style="width:80px"
                                                    id="checkIn-<?php echo $i; ?>"/>
                </div>
            </td>
            <td>
                <input type="date" class="trInputs" onchange="update_date(this)" id="checkOutDate-<?php echo $i; ?>"  <?php echo $disabled2 ?> name="checkOutDate" value="<?php echo ($row['checkOutDate']) ? $row['checkOutDate'] : $row['attendanceDate']; ?>"/>
            </td>
            <td>
                <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"> <i
                            class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                    <input <?php echo $disabled2 ?> type="text" name="checkOut" class="form-control timeTxt trInputs"
                                                    value="<?php echo $row['checkOut']; ?>"
                                                    style="width:80px" id="checkOut-<?php echo $i; ?>"/>
                </div>
            </td>
            <!--<td style="text-align: center">
                <input type="number" step="any" onchange="updateOthers(this)" name="normalTime"
                        <?php /*echo $disabled */?> class="trInputs inputdisabled "
                       value="<?php /*echo $row['normalTime'] */?>">
            </td>-->
            <td style="text-align: center">
                <input type="number" step="any" onchange="updateOthers(this)" name="realTime" <?php echo $disabled ?>
                        class="trInputs realTime inputdisabled" value="<?php echo $row['realTime'] ?>">
            </td>
            <td>
                <?php
                echo form_dropdown('presentTypeID', $attDrop, $row['presentTypeID'], ' style="width:80px;background-color:white" class="form-control select2 attType trInputs inputdisabled" onchange="updateOthers(this)"  ' . $disabled . '');
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

            $shiftHour=date("H",strtotime($row['offDuty']))-date("H",strtotime($row['onDuty']));
            
            if($row['isShiftNextDay'] == 1){
                $next_att_date = date("Y-m-d",strtotime('+1 days',strtotime($row['attendanceDate'])));
                $dateWithOnduty = new DateTime($row['attendanceDate'].' '.$row['onDuty']);
                $dateWithOffduty = new DateTime($next_att_date.' '.$row['offDuty']);
                $actualWorkingHours_obj = $dateWithOnduty->diff($dateWithOffduty);
                $shiftHour = $actualWorkingHours_obj->format('%h');//date("H",strtotime($row['onDuty'])) - date("H",strtotime($row['offDuty']));
            }

            ?>
            <td align="right"><?php makeTimeTextBox('lateHours', $lateHoursarr)/*echo $lateHours*/ ; ?></td>
            <td align="right"><?php makeTimeTextBox('earlyHours', $earlyHoursarr)/*echo $lateHours*/ ; ?></td>
            <td align="right"><?php makeTimeTextBox('OTHours', $OTHoursarr)/*echo $lateHours*/ ; ?></td>
            <td align="right"><span class="shiftHours"><?php echo $shiftHour; ?></span></td>
            <td align="right"><span class="totWorkingHours"><?php echo $totWorkingHours; ?></span></td>
            <td align="center"><span class="attendhours"><?php echo $attendhours ?></span></td>
            <td align="center"><input type="text"  disabled name="normalDay[]"
                                      value="<?php echo $row['normalDay'] ?>"
                                      class="normalDay trInputs number inputdisabled"></td>
            <td align="center"><input type="text"  disabled name="weekend[]" value="<?php echo $row['weekend'] ?>"
                                      class="weekend trInputs number inputdisabled ">
            </td>
            <td align="center"><input type="text"  disabled name="holiday[]" value="<?php echo $row['holiday'] ?>"
                                      class="holiday trInputs number inputdisabled"></td>
            <!--    <td align="center"><input type="text" name="nDaysOT[]" class="trInputs number" value=""></td>-->
            <td align="center"><?php makeTimeTextBox('NDaysOT', $NDaysOTsarr); ?></td>
            <td align="center"><?php makeTimeTextBox('weekendOTHours', $weekendOTHoursarr); ?></td>
            <!-- $weekendOTHours -->
            <td align="center"><?php makeTimeTextBox('holidayOTHours', $holidayOTHoursarr); ?></td>
            <td class="fixed-td"><span class="glyphicon glyphicon-trash" style="color:#c3331b;" onclick="removeTr(this)"></span></td>
        </tr>
        <?php
    }
} else {
    echo '<tr><td colspan="21">No data available in table</td></tr>';
}

$duplicates = get_duplicates($emparr);
function get_duplicates($array)
{
    return array_unique(array_diff_assoc($array, array_unique($array)));
}

?>

    <script type="text/javascript">
        $('.occurrence-popover').popover({
            content: details_in_popup,
            html: true,
            trigger: 'focus',
            delay: 500
        });

        function details_in_popup(){
            let id = $(this).attr("data-id");
            let rep = '<div id="temp-occ-'+id+'"><i class="fa fa-spinner fa-spin"></i> loading..</div>';
            $.ajax({
                url: "<?php echo site_url('Employee/load_attendanceOccurrences'); ?>",
                data: {'id': id },
                success: function(data){
                    $('#temp-occ-'+id).html(data);
                }
            });
            return rep;
        }

        function multiple_occ_in_popup(id, title, title2){
            $('#mul-occ-title').html(title);
            $('#mul-occ-title2').html(title2);
            $('#multiple-occ-modal').modal('show');
            $.ajax({
                url: "<?php echo site_url('Employee/load_attendanceOccurrences'); ?>",
                data: {'id': id },
                beforeSend: function () {
                    $('#multiple-occ-content').html('<i class="fa fa-spinner fa-spin"></i> loading..</div>');
                },
                success: function(data){
                    $('#multiple-occ-content').html(data);
                }
            });

        }


        var is_disable_clock_in_out = '<?php echo $is_disable_clock_in_out; ?>';

        if( is_disable_clock_in_out == 1){
            $('.timeTxt').prop('disabled', true);
        }

        var attendanceReview = $('#attendanceReview');
        $(document).ready(function () {
            $("tr").click(function () {
                $(this).closest("tr").siblings().removeClass("highlight");
                $(this).toggleClass("highlight");
            })
            $(".wrapper").click(function (e) {
                if (e.target.id == "attendanceReview" || $(e.target).parents("#attendanceReview").size()) {

                    $('#attendanceReview').on('click', 'tbody tr', function (event) {


                        $('.emptitle').html($(this).attr('data-code'));


                    });
                } else {
                    $('.emptitle').html('');
                }
            });

            $('.select21').select2();

            var clockIn_arr = [];
            clockIn_arr = <?php echo json_encode($clockIn_arr); ?>;
            var clockOut_arr = [];
            clockOut_arr = <?php echo json_encode($clockOut_arr); ?>;

            duplicates();


            var x = 0;
            while (x < clockIn_arr.length) {
                $('#checkIn-' + clockIn_arr[x]).val('');
                x++;
            }

            var y = 0;
            while (y < clockOut_arr.length) {
                $('#checkOut-' + clockOut_arr[y]).val('');
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

        function duplicates() {
            var duplicates = [];
            duplicates = <?php echo json_encode($duplicates); ?>;


            $.each(duplicates, function (index, value) {

                $("." + value).css("background-color", "rgba(255, 255, 0, 0.51)");
            });

        }

        function updatebothfields(other, col) {
            trID = $(other).closest('tr').attr('data-id');
            masterID = $(other).closest('tr').attr('data-masterid');
            name = col;
            hours = $(other).closest('tr').find('#h_' + col).val();
            minutes = $(other).closest('tr').find('#m_' + col).val();
            value = hours + '_' + minutes;
            updatefields(trID, masterID, value, name);
        }


        function updateOthers(other) {

            trID = $(other).closest('tr').attr('data-id');
            masterID = $(other).closest('tr').attr('data-masterid');
            name = $(other).attr('name');
            value = other.value;


            updatefields(trID, masterID, value, name);
        }

        function updatefields(trID, masterID, value, name) {
           
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
                        $("tr[data-id='" + trID + "']").find(".realTime").val(data['data']['realTime']);
                        $("tr[data-id='" + trID + "']").find(".attType").val(data['data']['presentTypeID']);
                        $("tr[data-id='" + trID + "']").find(".weekend").val(data['data']['weekend']);
                        $("tr[data-id='" + trID + "']").find(".holiday").val(data['data']['holiday']);
                        $("tr[data-id='" + trID + "']").find(".normalDay").val(data['data']['normalDay']);
                        $("tr[data-id='" + trID + "']").find(".attendhours").html(data['data']['attendhours']);
                        $("tr[data-id='" + trID + "']").find(".h_NDaysOT").val(data['data']['h_NDaysOT']);
                        $("tr[data-id='" + trID + "']").find(".h_OTHours").val(data['data']['h_OTHours']);
                        $("tr[data-id='" + trID + "']").find(".h_earlyHours").val(data['data']['h_earlyHours']);
                        $("tr[data-id='" + trID + "']").find(".h_holidayOTHours").val(data['data']['h_holidayOTHours']);
                        $("tr[data-id='" + trID + "']").find(".h_lateHours").val(data['data']['h_lateHours']);
                        $("tr[data-id='" + trID + "']").find(".h_weekendOTHours").val(data['data']['h_weekendOTHours']);
                        $("tr[data-id='" + trID + "']").find(".m_NDaysOT").val(data['data']['m_NDaysOT']);
                        $("tr[data-id='" + trID + "']").find(".m_OTHours").val(data['data']['m_OTHours']);
                        $("tr[data-id='" + trID + "']").find(".m_earlyHours").val(data['data']['m_earlyHours']);
                        $("tr[data-id='" + trID + "']").find(".m_lateHours").val(data['data']['m_lateHours']);
                        $("tr[data-id='" + trID + "']").find(".m_holidayOTHours").val(data['data']['m_holidayOTHours']);
                        $("tr[data-id='" + trID + "']").find(".m_weekendOTHours").val(data['data']['m_weekendOTHours']);
                        $("tr[data-id='" + trID + "']").find(".totWorkingHours").html(data['data']['totWorkingHours']);

                    }

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function u2pdatefields(trID, masterID, value, name) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {trID: trID, masterID: masterID, value: value, name: name},
                url: "<?php echo site_url('Employee/update_attendance'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }


        /*     function attReview_applyRowNumbers() {
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
         }*/

        function searchWithDate() {
            $('#attReview-searchItem').keyup();
        }

        function attStatus(obj) {

        }

        function removeTr(obj) {
            masterID = $(obj).closest('tr').attr('data-masterid');
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this row!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
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
                                $('#attReview-searchItem').keyup();
                                $('#attReview-totalRowCount').text($('#attendanceReview tbody>tr').length);
                                myAlert('s', data['message']);
                            } else {
                                myAlert('e', data['message']);
                            }

                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });


                }
            );
        }

        function minutesValidate(obj) {
            var thisVal = $.trim(obj.value);
            var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

            if (convertedVal > 59) {
                $(obj).val('');
            }
        }

        function minutesValidateChange(obj) {
            var thisVal = $.trim(obj.value);
            var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

            if (convertedVal > 59) {
                $(obj).val('');
            }

            var str = '';
            switch (convertedVal.toString().length) {
                case  0:
                    str = '00';
                    break;

                case 1:
                    str = '0';
                    break;

                default:
                    str = '';
            }

            $(obj).val(str + '' + convertedVal);
        }

        function hoursValidate(obj) {

        }

        $('.number').keypress(function (event) {

            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });

        function update_date(other){

            trID = $(other).closest('tr').attr('data-id');
            masterID = $(other).closest('tr').attr('data-masterid');
            name = $(other).attr('name');
            value = other.value;


            updatefields(trID, masterID, value, name);

        }

    </script>


<?php
