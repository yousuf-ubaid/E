
<?php
$is_disable_clock_in_out = getPolicyValues('DAT', 'All'); //Disable attendance Clock in / Clock out Time
/****** Employee total working hours for this day ******/

$clockIn_arr = $clockOut_arr = [];
$leave_arr = array_column($on_leave, 'leave_key');
$i = 1;

if (!empty($tempAttData)) {
    foreach ($tempAttData as $key => $row) {

        $empID = $row['empID'];
        $attendanceDate = $row['attendanceDate'];
        $leave_key = "{$empID}-{$attendanceDate}";

        $sr_type = 't3';
        $tr_data = $row['empName'] . ' ' . $attendanceDate . ' ' . $row['location'];
        $tr_data2 = $empID . '_' . $attendanceDate . '_' ;
        $data_code = 'Current Employee : ' . $row['empName'] ;

        $isEvenRow = ($key % 2);
        $class = ($isEvenRow == 0) ? 'oddTR' : 'evenTR';
        $class .= ' emp_' . $empID;

        $disabled2 = 'disabled';
        $bool_disabled = false;


        $work_status = ($row['statusCode'] == 'A')? 'Absent': 'Present';

        if(in_array($leave_key, $leave_arr)){
            $clockIn_arr[] = $i;
            $clockOut_arr[] = $i;
            $work_status = 'Leave';
            $sr_type = 't4';
        }
        elseif($row['statusCode'] == 'A'){
            $clockIn_arr[] = $i;
            $clockOut_arr[] = $i;
        }
        elseif($row['missedInPunch'] == 1){
            $sr_type = 't1';
            $clockIn_arr[] = $i;
        }
        elseif($row['missedOutPunch'] == 1){
            $sr_type = 't1';
            $clockOut_arr[] = $i;
        }
        elseif ($row['totalDuration'] > 720){
            $sr_type = 't2';
        }

        $cnfStatus = $row['cnfStatus'];
        $punchRecord = $row['punchRecord'];
        $punchRecord_arr = [];
        $round_time = 0;
        /*if($work_status == 'Present'){
            $punchRecord2 = str_replace('(in)', '', $punchRecord);
            $punchRecord2 = str_replace('(out)', '', $punchRecord2);
            $punchRecord_arr = explode(',', $punchRecord2);
            $punchRecord_arr = array_filter($punchRecord_arr, 'remove_blank_values');

            $round_time = calculate_tot_time($punchRecord_arr);
        }*/


        ?>
        <tr style="white-space: nowrap;" class="<?= $class.' '.$work_status; ?>"
            data-code="<?= $data_code; ?>"  data-duplicate="<?= $tr_data2; ?>" data-id="<?= $i; ?>"
            data-master-id="<?= $row['logID'] ?>" data-value="<?= $tr_data; ?>" data-status="<?= $cnfStatus; ?>"
            data-date="<?= $attendanceDate; ?>" data-sr-type="<?= $sr_type; ?>" data-location="<?= $row['floorID']; ?>" >
            <td style="opacity: 100" class="fixed-td"><?= $i; ?></td>
            <td style="opacity: 100" class="fixed-td">
                <?= $row['empName']; ?>
                <input type="hidden" name="empID[]" value="<?= $empID; ?>"/>
                <input type="hidden" name="machineID[]" value="<?= $row['empMachineID']; ?>"/>
                <input type="hidden" name="floorID[]" value="<?= $row['floorID']; ?>"/>
            </td>
            <td style="text-align: center">
                <?= $attendanceDate; ?> <input type="hidden" name="attDate[]" value="<?= $attendanceDate; ?>"/>
            </td>
            <td style="text-align: center"><?= $row['location']; ?></td>
            <td>
                <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px">
                        <i class="glyphicon glyphicon-time" style="font-size:10px"></i>
                    </span>
                    <input <?= $disabled2 ?> type="text" name="checkIn" class="form-control timeTxt trInputs "
                                value="<?=$row['inTime']; ?>" style="width:80px" id="checkIn-<?=$i;?>"/>
                </div>
            </td>
            <td>
                <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px">
                        <i class="glyphicon glyphicon-time" style="font-size:10px"></i>
                    </span>
                    <input <?=$disabled2?> type="text" name="checkOut" class="form-control timeTxt trInputs"
                         value="<?=$row['outTime'];?>" style="width:80px" id="checkOut-<?=$i;?>"/>
                </div>
            </td>
            <td align="right">
                <?php
                if($work_status == 'Present'){
                    $total_hours = $row['totalDuration'];
                    $total_hours = ['h' => gmdate("H", $total_hours * 60), 'm' => gmdate("i", $total_hours * 60)];
                    echo makeTimeTextBox_2($row['logID'], $total_hours, $bool_disabled);
                }
                ?>
            </td>
            <td style="text-align: center"><?= $work_status; ?></td>
            <td style="text-align: center">
                <?php
                if(strlen($punchRecord) > 54){
                    $sub_punch = substr($punchRecord, 0, 45);
                    echo '<div title="'.$punchRecord.'" class="punch_area"> '.$sub_punch.' more...</attr>';
                }else{
                    echo $punchRecord;
                }
                //echo '<pre>'; print_r($punchRecord_arr); echo '</pre>'
                ?>
            </td>
            <td class="fixed-td" style="text-align: center">
                <?php
                if($cnfStatus == 2){
                    echo '<label class="label label-success" > &nbsp; </label>';
                }
                else if($cnfStatus == 1){
                    echo '<label class="label label-confirmed"> &nbsp; </label>';
                }
                else{
                    echo '<input type="checkbox" onclick="is_allChecked(this)" id="att-check-'.$row['logID'].'" class="btn att-check" value="'.$row['logID'].'">';
                }
                ?>
            </td>
        </tr>
        <?php

        $i++;
    }
} else {
    echo '<tr><td colspan="10">No data available in table</td></tr>';
}

?>

<script type="text/javascript">
    let is_disable_clock_in_out = '<?= $is_disable_clock_in_out; ?>';

    if( is_disable_clock_in_out == 1){
        $('.timeTxt').prop('disabled', true);
    }

    let attendanceReview = $('#attendanceReview');

    $(document).ready(function () {
        $("tr").click(function () {
            $(this).closest("tr").siblings().removeClass("highlight");
            $(this).toggleClass("highlight");
        });

        var clockIn_arr = [];
        clockIn_arr = <?php echo json_encode($clockIn_arr); ?>;
        var clockOut_arr = [];
        clockOut_arr = <?php echo json_encode($clockOut_arr); ?>;

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

    $('#attReview-searchItem').keyup(function (event) {
        if (event.which == 13 ) {
            filter_table();
        }
    });

    function is_allChecked(obj){
        if( $(obj).prop('checked') ){
            if($('.att-check:checked').not('.hide-chk').length == $('.att-check').not('.hide-chk').length){
                $('#check-all').prop('checked', true);
            }
        }
        else{
            $('#check-all').prop('checked', false);
        }
    }

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

    function updateTotalDuration(obj, log_id) {
        let hours = $('#h_' + log_id).val();
        let minutes = $('#m_' + log_id).val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {trID: trID, 'masterID': log_id, 'hours': hours, 'minutes': minutes},
            url: "<?php echo site_url('Employee/update_totalDuration'); ?>",
            success: function (data) {
                if (data['error'] == 1) {
                    alert(data['message']);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function filter_table() {
        let dateSearch = $.trim($('#searchDate').val());
        let type_filter = $.trim($('#search_type').val());
        let location_filter = $.trim($('#search_floorID').val());
        let status_filter = $.trim($('#search_status').val());
        let searchKey = $.trim($('#attReview-searchItem').val()).toLowerCase();
        let tableTR = $('#attendanceReview tbody>tr');
        tableTR.removeClass('hideTr evenTR oddTR');
        $('.att-check').removeClass('hide-chk');

        tableTR.each(function () {
            let dataValue = '' + $(this).attr('data-value') + '';
            let dateValue = '' + $(this).attr('data-date') + '';
            let isHidden = false;

            dataValue = dataValue.toLocaleLowerCase();

            if (searchKey != '') {
                if (dataValue.indexOf('' + searchKey + '') == -1) {
                    $(this).addClass('hideTr');
                    $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                    isHidden = true;
                }
                else {
                    if (dateSearch != '') {
                        if (dateValue.indexOf('' + dateSearch + '') == -1) {
                            $(this).addClass('hideTr');
                            $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                            isHidden = true;
                        }
                    }
                }
            }
            else {
                if (dateSearch != '') {
                    if (dateValue.indexOf('' + dateSearch + '') == -1) {
                        $(this).addClass('hideTr');
                        $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                        isHidden = true;
                    }
                }
            }

            if(type_filter != '' && isHidden === false){
                if(type_filter == 'P'){
                    if(!$(this).hasClass('Present')){
                        $(this).addClass('hideTr');
                        $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                    }
                }
                else{
                    if( $(this).attr('data-sr-type') != type_filter){
                        $(this).addClass('hideTr');
                        $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                    }
                }
            }

            if(location_filter != '' && isHidden === false){
                if( $(this).attr('data-location') != location_filter){
                    $(this).addClass('hideTr');
                    $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                }
            }

            if(status_filter != '' && isHidden === false){
                if( $(this).attr('data-status') != status_filter){
                    $(this).addClass('hideTr');
                    $(this).find('td:eq(9) .att-check').addClass('hide-chk');
                }
            }
        });

        attReview_applyRowNumbers();


        if($('.att-check:checked').not('.hide-chk').length == $('.att-check').not('.hide-chk').length && $('.att-check').not('.hide-chk').length > 0){
            $('#check-all').prop('checked', true);
        }
        else{
            $('#check-all').prop('checked', false);
        }
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
        let h = parseInt($(obj).val());
        if(h > 18){
            $(obj).val('');
            alert('Total hour can not be greater than 18h');
        }
    }

    $('.number').keypress(function (event) {
        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });
</script>
