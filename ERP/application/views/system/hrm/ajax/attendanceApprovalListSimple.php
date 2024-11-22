<style type="text/css">
    #empSearchLabel {
        float: right !important;
        font-weight: 600
    }

    @media (max-width: 767px) {
        #empSearchLabel {
            float: left !important;
        }

        #new-attBtn {
            margin-bottom: 30px;
        }
    }

    .trInputs {
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
        border: 0px solid #ccc;
    }

    .hideTr {
        display: none
    }

    .oddTR td {
        background: #f9f9f9 !important;
    }

    .evenTR td {
        background: #ffffff !important;
    }

    .fixHeader_Div {
        height: 340px;
        border: 1px solid #c0c0c0;
    }

    #attendanceReview td {
        vertical-align: middle;
    }

    #attendanceReview th {
        z-index: 10;
    }

    #attendanceReview tr:hover > td {
        background: #96e277 !important; /*#2c4762*/

    }

    #attendanceReview tr:hover > td.fixed-td {
        background: #96e277 !important; /*#2c4762*/

    }

    .timeBox {
        text-align: right;
        padding: 2px;
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
    #attendanceReview tr:hover {
        background-color: #FFFFAA;
    }

    #attendanceReview tr.selected td {
        background: none repeat scroll 0 0 #FFCF8B;
        color: #000000;
    }

    .highlight {
        background-color: rgba(167, 251, 132, 0.35) !important;
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

    #attendanceReview > tbody > tr > td {
        padding: 4px;
    }

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
    .inputdisabled {
        background-color: white;
    }

    #attendanceReview tbody tr > td:nth-child(2),
    #attendanceReview tbody tr > td:nth-child(13),
    #attendanceReview tbody tr > td:nth-child(14),
    #attendanceReview tbody tr > td:nth-child(15),
    #attendanceReview tbody tr > td:nth-child(22),
    #attendanceReview tbody tr > td:nth-child(23),
    #attendanceReview tbody tr > td:nth-child(24),
    #attendanceReview tbody tr > td:nth-child(18),
    #attendanceReview tbody tr > td:nth-child(19),
    #attendanceReview tbody tr > td:nth-child(20) {
        background-color: #F7F8FA;
    }

</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

?>


<!--<i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> Approved &nbsp;&nbsp;
<i class="fa fa-times"
   style="color: #990000"
   aria-hidden="true"></i> Not Approved
<span class="hideremove">
<input type="checkbox" id="checkAll"/> Select All</label>
</span>-->
<div class="row">
    <div class="col-sm-3">
        <table class="table table-bordered table-striped table-condensed table-row-select" style="">
            <tbody>
            <tr>
                <td>
                    <i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i> <?php echo $this->lang->line('common_approved');?><!--Approved--> &nbsp;
                </td>
                <td><span class="label"
                          style="padding: 0px 5px ;font-size: 100%;background-color: #dacff7">&nbsp;</span>&nbsp;&nbsp;
                    <?php echo $this->lang->line('hrms_payroll_shift_weekend');?> <!--Shift Weekend-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-9">
        <div class="pull-right"> <form method="post" target="_blank" action="<?php echo site_url('Employee/attendance_export_excel'); ?>">
                <input type="hidden" name="attendanceDate" value="<?php echo $attendanceDate ?>">
                <input type="hidden" name="approvedYN" value="<?php echo $approvedYN ?>">
                <input type="hidden" name="datefrom" value="<?php echo $datefrom ?>">
                <input type="hidden" name="dateto" value="<?php echo $dateto ?>">
                <input type="hidden" name="floorID" value="<?php echo $floorID ?>">
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                <button  type="submit"
                         class="btn btn-success btn-sm pull-right">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
            </form>
        </div>
    </div>

    <div class="col-sm-6" style="padding-top: 10px;">
        <label>Click the checkbox to approve the attendance</label>
    </div>
</div>
<br>

<div class="well emp_title_container" style="padding: 10px 10px;font-weight: bold; margin-bottom: 0px; display: none;">
    <span class="emp_title"></span>
</div>
<div style="max-height: 400px">
    <table id="attendanceReview" class="table">
        <thead>
        <tr style="white-space: nowrap">
            <th style="z-index: 10;"><span class="hideremove"> <input type="checkbox" id="checkAll"/> </span></th>
            <th style="z-index: 10;"><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
            <!--        <th style="z-index: 10;">#</th>-->
            <th style="z-index: 10;"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP Code--></th>
            <th style="z-index: 10;"><?php echo $this->lang->line('hrms_payroll_emp_name');?><!--Emp Name--></th>
            <th style="z-index: 10;"> <?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="z-index: 10;"><?php echo $this->lang->line('hrms_payroll_clock_in');?><!--Clock In--></th>
            <th style="z-index: 10; "><?php echo $this->lang->line('hrms_payroll_clock_out');?><!--Clock Out--></th>
            <!--<th style=" "><?php /*echo $this->lang->line('hrms_payroll_normal_time');*/?></th>-->
            <th style=" "><?php echo $this->lang->line('hrms_payroll_real_time');?><!--Real Time--></th>
            <th style="z-index: 10;"><?php echo $this->lang->line('hrms_payroll_present');?><!--Present--></th> 

            <?php if ($hideedit) { ?>
                <th style=""></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php

        $appData = '';
        $disabled = '';
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
                    /*    $datetime1 = new DateTime($row['offDuty']);
                        if($row['onDuty'] >=$row['checkIn']){
                            $datetime2= new DateTime($row['onDuty']);
                        }else{
                            $datetime2 = new DateTime($row['checkIn']);
                        }*/

                    if ($row['offDuty'] <= $row['checkOut']) {
                        $datetime1 = new DateTime($row['offDuty']);
                    } else {
                        $datetime1 = new DateTime($row['checkOut']);
                    }
                    if ($row['onDuty'] >= $row['checkIn']) {
                        $datetime2 = new DateTime($row['onDuty']);
                    } else {
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
                $disabled = '';
                $booldisabled = false;
                if ($row['approvedYN'] == 1) {
                    $disabled = 'disabled';
                    $booldisabled = true;
                }

                $bg = '';
                if ($row['isWeekEndDay'] == 1) {
                    $bg = 'background-color:rgba(218, 207, 247, 0.42)';
                }

                ?>
                <tr data-code="<?php echo $attrib ?>" style="white-space: nowrap;<?php echo $bg; ?>"
                    data-id="<?php echo $i; ?>"
                    data-masterid="<?php echo $row['ID'] ?>"
                    data-value="<?php echo $tr_data; ?>'"
                    data-date="<?php echo $row['attendanceDate']; ?>"
                    class="<?php echo $class; ?>">
                    <td class="fixed-td">

                        <?php if ($row['approvedYN'] == 1) {  ?>
                            <i class="fa fa-check" style="color: #00a65a" aria-hidden="true"></i>
                         <?php
                        }
                        else {
                            if ($hideedit) {
                                ?>

                                <i class="fa fa-times" style="color: #990000" aria-hidden="true"></i>
                                <?php

                            } else { ?>
                                <label><input name="ID[]" class="ID" value="<?php echo $row['ID'] ?>" type="checkbox"/>
                                </label>
                                <input name="masterID[]" class="masterID" value="<?php echo $row['ID'] ?>"
                                       type="hidden"/>
                                <input name="hiddenID[]" class="hiddenID" value="0" type="hidden"/>
                                <input name="empID[]" class="empID" value="<?php echo $row['empID'] ?>" type="hidden"/>
                                <input name="empName[]" class="empName" value="<?php echo $row['Ename1'] ?>"
                                       type="hidden"/>
                                <input name="attendanceDate[]" class="attendanceDate"
                                       value="<?php echo $row['attendanceDate'] ?>" type="hidden"/>
                                <input name="leave[]" class="leave"
                                       value="0" type="hidden"/>
                                <input type="hidden" name="machineID[]" value="<?php echo $row['empMachineID']; ?>"/>
                                <input type="hidden" name="floorID[]" value="<?php echo $row['floorID']; ?>"/>
                                <input type="hidden" name="attendanceApprovedID[]" value="<?php echo $row['attendanceApprovedID']; ?>"/>
                                <input type="hidden" name="level[]" value="<?php echo $row['level']; ?>"/>
                            <?php }
                        } ?>
                    </td>
                    <td class="fixed-td">
                        <input type="text" id="approvedComment" <?php echo $disabled ?> name="approvedComment[]"
                               class="form-control trInputs"
                               style="width:100px" value="<?php echo $row['approvedComment'] ?>">
                    </td>

                    <td class="fixed-td"><?php echo $row['ECode']; ?></td>
                    <td class="fixed-td"><?php echo $row['Ename2']; ?></td>
                    <td style="text-align: center">
                        <?php echo $row['attendanceDate']; ?>
                    </td>

                    <td>
                        <?php echo $row['checkIn']; ?>
                        <!--  <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"><i
                            class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                            <input <?php /*echo $disabled */ ?> type="text" name="checkIn" class="form-control timeTxt trInputs"
                                   value="<?php /*echo $row['checkIn']; */ ?>" style="width:80px"
                                   id="checkIn-<?php /*echo $i; */ ?>"/>
                        </div>-->
                    </td>
                    <td>
                        <?php echo $row['checkOut']; ?>
                        <!--   <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                    <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"> <i
                            class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                            <input <?php /*echo $disabled */ ?> type="text" name="checkOut" class="form-control timeTxt trInputs"
                                   value=""
                                   style="width:80px" id="checkOut-<?php /*echo $i; */ ?>"/>
                        </div>-->

                    </td>

                    <!--<td style="text-align: center">
                        <input type="number" disabled step="any" onchange="updateOthers(this)" name="normalTime"
                               id="normalTime" <?php /*echo $disabled */?> class="form-control trInputs inputdisabled"
                               value="<?php /*echo $row['normalTime'] */?>" style="width:60px;background-color: white">
                    </td>-->
                    <td style="text-align: center">
                        <input disabled type="number" step="any" onchange="updateOthers(this)" name="realTime"
                               id="realTime" <?php echo $disabled ?> class="form-control trInputs inputdisabled"
                               value="<?php echo $row['realTime'] ?>" style="width:60px;background-color: white">
                    </td>

                    <td style="text-align: center">
                        <?php
                        $disabled2 = '';
                        if ($row['presentTypeID'] == 5) { ?>
                            <input type="hidden" id="presentTypeID" class="present" name="presentTypeID[]" value="<?php echo $row['presentTypeID'] ?>">
                            <?php
                            $disabled2 = 'disabled';
                        }
                        ?>

                        <input type="hidden" class="present" name="present" value="<?php echo $row['presentTypeID'] ?>">
                        <?php
                        echo form_dropdown('presentTypeID[]', $attDrop, $row['presentTypeID'], 'class="attType form-control trInputs" style="width:100px"  onchange="modalLeave(this)" ' . $disabled . '  ' . $disabled2 . '');
                        ?>
                    </td>
                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>
</div>
<div class="well emp_title_container" style="padding: 10px 10px;font-weight: bold; margin-bottom: 0px; display: none;">
    <span class="emp_title"></span>
</div>

<script>
    function getleavebalance(leaveType) {
        leaveBalance($('#trempID').val(), leaveType)
    }

    $(".wrapper").click(function (e) {
        $('.emp_title_container').show();
        if (e.target.id == "attendanceReview" || $(e.target).parents("#attendanceReview").size()) {
            $('#attendanceReview').on('click', 'tbody tr', function (event) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
                $('.emp_title').html($(this).attr('data-code'));

            });
        } else {
            $('.emp_title_container').hide();
            $('.emp_title').html('');
        }

        if( $.trim( $('.emp_title').html() ) == '' ){
            $('.emp_title_container').hide();
        }
    });

    $(document).ready(function () {


        $('.timeTxt').timepicker();

        $('.timeTxt').timepicker().on('changeTime.timepicker', function (e) {
            value = e.time.value;
            trID = $(this).closest('tr').attr('data-id');
            masterID = $(this).closest('tr').attr('data-masterid');
            name = $(this).attr('name');


            updatefields(trID, masterID, value, name);
        });
        $('.select21').select2();

        var clockIn_arr = [];
        clockIn_arr = <?php echo json_encode($clockIn_arr); ?>;
        var clockOut_arr = [];
        clockOut_arr = <?php echo json_encode($clockOut_arr); ?>;


        /* $('.timeTxt').each(function(){
         var thisOn =  $(this).attr('onchange');
         $(this).attr('data-onchange', thisOn);
         $(this).removeAttr('onchange');
         })*/

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

        /*$('.timeTxt').each(function(){
         var thisOn =  $(this).attr('data-onchange');
         $(this).attr('onchange', thisOn);
         })*/
    });

    function minutesValidate(obj) {
        var thisVal = $.trim(obj.value);
        var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

        if (convertedVal > 59) {
            $(obj).val('');
        }

        if( $(obj).hasClass('m_NDaysOT') || $(obj).hasClass('m_weekendOTHours') || $(obj).hasClass('m_holidayOTHours') ){
            var ot_hour_obj = $(obj).attr('name');
            ot_hour_obj = ot_hour_obj.split('_');
            ot_hour_obj = ot_hour_obj[1];
            var ot_h = $(obj).closest('tr').find('.h_'+ot_hour_obj).val();
            var ot_m = thisVal;

            var max_h = $(obj).closest('tr').find('.h_OTHours').val();
            var max_m = $(obj).closest('tr').find('.m_OTHours').val();


            var max_ot = parseInt(max_h+''+max_m);
            var this_ot = parseInt(ot_h+''+ot_m);

            if(max_ot < this_ot){
                myAlert('w', 'You can not adjust the OT time greater than calculate OT time : <b>'+max_h+':'+max_m);
                $(obj).val('');
            }
        }
    }

    function hoursValidate(obj) {
        var thisVal = $.trim(obj.value);
        var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

        if( $(obj).hasClass('h_NDaysOT') || $(obj).hasClass('h_weekendOTHours') || $(obj).hasClass('h_holidayOTHours') ){

            var ot_minutes_obj = $(obj).attr('name');
            ot_minutes_obj = ot_minutes_obj.split('_');
            ot_minutes_obj = ot_minutes_obj[1];
            var ot_h = thisVal;
            var ot_m = $(obj).closest('tr').find('.m_'+ot_minutes_obj).val();

            var max_h = $(obj).closest('tr').find('.h_OTHours').val();
            var max_m = $(obj).closest('tr').find('.m_OTHours').val();


            var max_ot = parseInt(max_h+''+max_m);
            var this_ot = parseInt(ot_h+''+ot_m);

            if(max_ot < this_ot){
                myAlert('w', 'You can not adjust the OT time greater than calculate OT time : <b>'+max_h+':'+max_m);
                $(obj).val('');
            }
        }
    }

    function updatebothfields(other, col) {
        trID = $(other).closest('tr').attr('data-id');
        masterID = $(other).closest('tr').attr('data-masterid');
        name = col;
        hours = $(other).closest('tr').find('.h_' + col).val();
        minutes = $(other).closest('tr').find('.m_' + col).val();
        value = hours + '_' + minutes;

        if(col=='weekendOTHours'){
            $(other).closest('tr').find('.h_NDaysOT').val(00);
            $(other).closest('tr').find('.m_NDaysOT').val(00);

            $(other).closest('tr').find('.h_holidayOTHours').val(00);
            $(other).closest('tr').find('.m_holidayOTHours').val(00);
        }else if(col=='NDaysOT'){
            $(other).closest('tr').find('.h_weekendOTHours').val(00);
            $(other).closest('tr').find('.m_weekendOTHours').val(00);

            $(other).closest('tr').find('.h_holidayOTHours').val(00);
            $(other).closest('tr').find('.m_holidayOTHours').val(00);
        }else if(col=='holidayOTHours'){
            $(other).closest('tr').find('.h_weekendOTHours').val(00);
            $(other).closest('tr').find('.m_weekendOTHours').val(00);

            $(other).closest('tr').find('.h_NDaysOT').val(00);
            $(other).closest('tr').find('.m_NDaysOT').val(00);
        }else{
            $(other).closest('tr').find('.h_weekendOTHours').val(00);
            $(other).closest('tr').find('.m_weekendOTHours').val(00);

            $(other).closest('tr').find('.h_NDaysOT').val(00);
            $(other).closest('tr').find('.m_NDaysOT').val(00);

            $(other).closest('tr').find('.h_holidayOTHours').val(00);
            $(other).closest('tr').find('.m_holidayOTHours').val(00);
        }
        $(other).closest('tr').find('.noPayAmount').html('');
        $(other).closest('tr').find('.noPaynonPayrollAmount').html('');
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
                /*   stopLoad();*/
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }

                if (data['error'] == 0) {
                    $("tr[data-id='" + trID + "']").find("#realTime").val(data['data']['realTime']);
                    $("tr[data-id='" + trID + "']").find(".attType").val(data['data']['presentTypeID']);
                    $("tr[data-id='" + trID + "']").find(".weekend").val(data['data']['weekend']);
                    $("tr[data-id='" + trID + "']").find(".normalDay").val(data['data']['normalDay']);
                    $("tr[data-id='" + trID + "']").find(".holiday").val(data['data']['holiday']);
                    $("tr[data-id='" + trID + "']").find(".attendhours").html(data['data']['attendhours']);
                    $("tr[data-id='" + trID + "']").find("#h_NDaysOT").val(data['data']['h_NDaysOT']);
                    $("tr[data-id='" + trID + "']").find("#h_OTHours").val(data['data']['h_OTHours']);
                    $("tr[data-id='" + trID + "']").find("#h_earlyHours").val(data['data']['h_earlyHours']);
                    $("tr[data-id='" + trID + "']").find("#h_holidayOTHours").val(data['data']['h_holidayOTHours']);
                    $("tr[data-id='" + trID + "']").find("#h_lateHours").val(data['data']['h_lateHours']);
                    $("tr[data-id='" + trID + "']").find("#h_weekendOTHours").val(data['data']['h_weekendOTHours']);
                    $("tr[data-id='" + trID + "']").find("#m_NDaysOT").val(data['data']['m_NDaysOT']);
                    $("tr[data-id='" + trID + "']").find("#m_OTHours").val(data['data']['m_OTHours']);
                    $("tr[data-id='" + trID + "']").find("#m_earlyHours").val(data['data']['m_earlyHours']);
                    $("tr[data-id='" + trID + "']").find("#m_holidayOTHours").val(data['data']['m_holidayOTHours']);
                    $("tr[data-id='" + trID + "']").find("#m_weekendOTHours").val(data['data']['m_weekendOTHours']);
                    $("tr[data-id='" + trID + "']").find("#m_lateHours").val(data['data']['m_lateHours']);
                    $("tr[data-id='" + trID + "']").find(".totWorkingHours").html(data['data']['totWorkingHours']);
                    $("tr[data-id='" + trID + "']").find(".paymentOT").html(data['data']['paymentOT']);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    $('.ID').click(function () {
        if ($(this).is(":checked")) {

            $(this).closest('tr').find('.hiddenID').val(1);
        }
        else {

            $(this).closest('tr').find('.hiddenID').val(0);
        }

    });

    $('#attendanceReview').tableHeadFixer({
        head: true,
        foot: true,
        left: 4,
        right: 0,
        'z-index': 10
    });

    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));

        if ($(this).prop("checked")) {
            $('.hiddenID').val(1);
        } else {
            $('.hiddenID').val(0);
        }
    });

    function modalLeave(id) {

        value = $("tr[data-id='" + trID + "']").find(".present").val();


        if (id.value == 5) {
            var empID = $(id).closest('tr').find('.empID').val();
            var attendanceDate = $(id).closest('tr').find('.attendanceDate').val();
            dataID = $(id).closest('tr').attr('data-id');
            $('#employeeName').text($(id).closest('tr').attr('data-code'));
            get_leaveType(empID, attendanceDate, dataID)

        }else if(id.value == 6){
            dataID = $(id).closest('tr').attr('data-id');
            $("tr[data-id='" + trID + "']").find(".leave").val(0);
            $(id).closest('tr').find('.h_NDaysOT').val('00');
            $(id).closest('tr').find('.m_NDaysOT').val('00');
            $(id).closest('tr').find('.h_holidayOTHours').val('00');
            $(id).closest('tr').find('.m_holidayOTHours').val('00');
            $(id).closest('tr').find('.h_weekendOTHours').val('00');
            $(id).closest('tr').find('.m_weekendOTHours').val('00');
            $(id).closest('tr').find(".paymentOT").html('');
        }

        else {
            dataID = $(id).closest('tr').attr('data-id');
            $("tr[data-id='" + trID + "']").find(".leave").val(0);
        }

        var empID = $(id).closest('tr').find('.empID').val();
        var attendanceDate = $(id).closest('tr').find('.attendanceDate').val();
        dataID = $(id).closest('tr').attr('data-id');
        getNopay_amount(empID, attendanceDate, id.value, dataID)


    }

    function getNopay_amount(empID, attendanceDate, presentType, trID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {empID: empID, attendanceDate: attendanceDate, presentType: presentType},
            url: "<?php echo site_url('Employee/getNopay_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                noPayAmount=  parseFloat(Math.round(data[1]['noPayAmount'] * 100) / 100).toFixed(2);
                noPaynonPayrollAmount=parseFloat(Math.round(data[1]['noPaynonPayrollAmount'] * 100) / 100).toFixed(2);
                $("tr[data-id='" + trID + "']").find(".noPayAmount").html(noPayAmount);
                $("tr[data-id='" + trID + "']").find(".noPaynonPayrollAmount").html(noPaynonPayrollAmount);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function leaveClose() {
        trID = $('#trID').val();
        trempID = $('#trempID').val();

        $('#modalleave').modal('hide');

        value = $("tr[data-id='" + trID + "']").find(".present").val();


        $("tr[data-id='" + trID + "']").find(".attType").val(value);

        $("tr[data-id='" + trID + "']").find(".leave").val(0);

    }

    function saveLeave() {

        trID = $('#trID').val();
        trempID = $('#trempID').val();
        leave = $('#leaveTypeID').val();


        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('hrms_payroll_you_want_to_create_leave_application_for_this');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_no');?>",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    $("tr[data-id='" + trID + "']").find(".leave").val(leave);
                    $('#modalleave').modal('hide');
                } else {
                    $("tr[data-id='" + trID + "']").find(".leave").val(0);
                    $("tr[data-id='" + trID + "']").find(".attType").val('');
                    $('#modalleave').modal('hide');
                }
            });


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
                    $('#attendanceDatehn').val(attendanceDate);

                    leaveBalance(empID, $('#leaveTypeID').val())


                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function leaveBalance(empID, leaveType) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {empID: empID, leaveType: leaveType},
            url: "<?php echo site_url('Employee/employeeLeaveSummery'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (data['policyMasterID'] == 2) {
                    $('#leavebalance').html(display(data['balance']));
                } else {
                    $('#leavebalance').html(data['balance']);
                }

                loadautoleave_confirm_details()


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');

            }
        });

    }

    function display(a) {
        if (a >= 0) {
            // Do Something
            var hours = Math.trunc(a / 60);
            var minutes = a % 60;

            return hours + "h :" + minutes + "m";
        }
        else {
            a = Math.abs(a);
            var hours = Math.trunc(a / 60);
            var minutes = a % 60;

            return "-" + hours + "h :" + minutes + "m";
        }
    }
</script>

