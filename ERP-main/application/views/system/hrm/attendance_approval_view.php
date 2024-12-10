<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$location_arr = floors_drop();
$records = array_group_by($records, 'empID');

?>
<style type="text/css">
    .fixHeader_Div {
        height: 500px;
    }

    #attendanceReview td {
        vertical-align: middle;
    }

    #attendanceReview th {
        z-index: 10;
    }

    #attendanceReview tr:hover > td {
        background: rgba(14, 191, 70, 0.31) !important;
    }

    #attendanceReview tr:hover > td .trInputs {
        color: #000;
    }

    #attendanceReview tr:hover > td.fixed-td {
        background: rgba(14, 191, 70, 0.31) !important;
    }


    #attendanceReview tr:hover {
        background-color: #FFFFAA;
    }

    #attendanceReview tr.selected td {
        background: none repeat scroll 0 0 #FFCF8B;
        color: #000000;
    }

    .highlight {
        background-color: rgba(167, 251, 132, 0.35) !important;
        opacity: 200;
    }

    .tb thead tr {
        background: rgb(178, 203, 230);
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
        background-color: #303a4a;
        color: #fff;
    }

    .attendanceReview .table > tbody > tr > td {
        padding: 4px;
    }

    .input_disabled {
        background-color: white;
    }

    #attendanceReview .input-group-addon {
        border: 0px solid #ccc;
    }

    .odd_column{
        background-color: #F7F8FA;
    }

    .punch_area{
        background: #0a3544;
        color: #fff2e1;
    }

    .punch_area:hover{
        cursor: pointer;
    }

    #attendanceReview>tbody>tr>td{
        padding: 2px;
    }

    .highlight td{
        background-color: rgba(167, 251, 132, 0.35) !important;
        opacity: 200;
    }

    .trInputs{
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
        border: 0px solid #ccc;
    }
</style>
<div class="">
    <label class="control-label">Period : </label> <?=date('Y - F', strtotime($master_data['attendancePeriod']))?>
</div>

<h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: 1px;"></h5>
<div class="table-responsive" style="padding: 0px !important;">
    <?php echo form_open('', 'role="form" class="" id="attendanceReview_form" autocomplete="off"'); ?>
    <div class="fixHeader_Div" style="max-width: 100%;">
        <table id="attendanceReview" class="table tb " style="max-width: 1750px !important; margin-top: -1px;">
            <thead class="">
            <tr style="white-space: nowrap">
                <th style="width: 15px;">#</th>
                <th style="min-width: 230px;"><?= $this->lang->line('hrms_attendance_employee_name'); ?></th>
                <th style="z-index: 10; min-width: 95px"><?=$this->lang->line('hrms_attendance_floor'); ?></th>
                <?php
                foreach ($dateRange as $date){
                    echo '<th style="z-index: 10; width: 55px">'.$date->format("d").'</th>';
                }
                ?>
                <th style="width: 60px;"><?= $this->lang->line('common_total'); ?></th>
                <th style="width: 105px">OT hours</th>
                <th style="width: 105px">Adjust OT</th>
                <th style="width: 25px; z-index: 10">
                    <input type="checkbox" onclick="check_all()" id="check-all" class="btn" >
                </th>
            </tr>
            </thead>

            <tbody>
            <?php
            $i = 1;
            foreach ($records as $empID=>$row){
                $location = $row[0]['locationID'];
                $location_des = (array_key_exists($location, $location_arr))?  $location_arr[$location]: '';
                $duration_arr = array_column($row, 'att_day');
                $empName = $row[0]['empName'];

                echo '<tr data-name="'.$empName.'">
                          <td>'.$i.'</td>
                          <td>'.$empName.'</td>
                          <td>'.$location_des.'</td>';

                $total_duration = 0;
                foreach ($dateRange as $date){
                    $key = $date->format("d");
                    $dur_key = array_search($key, $duration_arr);
                    $this_duration = 0;
                    if($dur_key !== false){
                        $this_duration = $row[$dur_key]['totalDuration'];
                    }

                    $total_duration += $this_duration;
                    $class =  ($key%2)? 'odd_column': '';
                    echo '<td style="z-index: 10; width: 55px" class="'.$class.'">'.gmdate("H", $this_duration * 60).':'.gmdate("i", $this_duration * 60).'</td>';
                }

                $tot_hours = floor($total_duration/60);
                $tot_minutes = $total_duration % 60;

                $_ot = $row[0]['otHours'];
                $ot_hours = floor($_ot/60);
                $ot_minutes = $_ot % 60;

                $_adjOT = $row[0]['adjustOtHours'];
                $adjOT_hours = floor($_adjOT/60);
                $adjOT_minutes = $_adjOT % 60;
                $_adjOT = ['h'=>$adjOT_hours, 'm'=>$adjOT_minutes];
                $bool_disabled = true;

                $detID = $row[0]['detID'];

                $approve_str = '<label class="label label-success" > &nbsp; </label>';
                if($row[0]['approvedYN'] == 0){
                    $approve_str = '<input type="checkbox" onclick="is_allChecked(this)" id="att-check-'.$detID.'" class="btn att-check" value="'.$detID.'">';
                    $bool_disabled = false;
                }


                echo '<td style="z-index: 10; width: 55px" align="right">'.$tot_hours.'h&nbsp;'.$tot_minutes.'m</td>
                      <td style="z-index: 10; width: 55px" align="right">'.$ot_hours.'h&nbsp;'.$ot_minutes.'m</td>
                      <td style="z-index: 10; width: 55px" align="right">'.makeTimeTextBox_2($detID, $_adjOT, $bool_disabled).'</td>
                      <td style="z-index: 10; width: 55px" align="center">'.$approve_str.'</td> 
                      </tr>';
                $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo form_close(); ?>
</div>
<h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: -7px;"></h5>

<script>
    $('#attendanceReview').tableHeadFixer({
        head: true
    });

    let selected_employee_det = $('.selected-employee-det');

    $("#attendanceReview tr").click(function () {
        $(this).closest("tr").siblings().removeClass("highlight");
        $(this).toggleClass("highlight");


        let curEmp = $(this).attr('data-name');
        if(curEmp != undefined){
            selected_employee_det.css('display', 'block');
            curEmp += ' <span class="pull-right">'+curEmp+'</span>';
            selected_employee_det.html(curEmp);
        }
        else{
            selected_employee_det.css('display', 'none');
        }
    });

    function updateTotalDuration(obj, detID) {
        let hours = $('#h_' + detID).val();
        let minutes = $('#m_' + detID).val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'detID': detID, 'hours': hours, 'minutes': minutes},
            url: "<?php echo site_url('Employee/adjust_otHours'); ?>",
            success: function (data) {
                if(data[0]=='e'){
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function approve_att() {
        let count = $('.att-check:checked').length;

        if(count < 1){
            myAlert('e', 'Please select at least one record to confirm');
            return false;
        }

        swal({
                title: "Are you sure?",
                text: 'You are going to approve '+count+' records.',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_approve');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                let detID_list = [];
                $('.att-check:checked').each(function(i,v){
                    detID_list.push( $(this).val() );
                });

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detID': detID_list},
                    url: "<?php echo site_url('Employee/approve_attendanceRec'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            let lbl = '<label class="label label-success" > &nbsp; </label>';
                            $('.att-check:checked').each(function(i,v){
                                $(this).parent().html(lbl);
                            });
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in approval process');
                    }
                });
            }
        );
    }

    function check_all(){
        if( $('#check-all').prop('checked') ){
            $('.att-check').not('.hide-chk').prop('checked', true);
        }
        else{
            $('.att-check').not('.hide-chk').prop('checked', false);
        }
    }

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
</script>