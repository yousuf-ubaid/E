

<!--Translation added by Naseek-->

<?php
/*echo '<pre>';print_r($emp_arr); echo '</pre>'; die();*/


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$attDrop = attendanceType_drop();
$attTime = $masterData['AttTime'];
$isClosed = $masterData['isAttClosed'];
$disabled = ( $isClosed == 1 )? 'disabled' : '';
?>

<style type="text/css">
    .hideTr{ display: none }

    .oddTR td{ background: #f9f9f9 !important; }

    .evenTR td{ background: #ffffff !important; }

    #staff-attendance-table td {
        vertical-align: middle;
    }

    .fixHeader_Div {
         height: 500px;
         border: 1px solid #c0c0c0;
     }

    div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
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
        height: 10px;
    }

    .emp-attendance-img{
        height: 20px;
        width: 25px;
    }

    @media (min-width: 768px) {
        .modal-dialog {
            margin: 10px auto !important;
        }
    }

    #staff-attendance-table .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single{
        height: 22px;
        padding: 0px 5px
    }

    #staff-attendance-table .select2-container--default .select2-selection--single .select2-selection__arrow{ height: 18px !important;}

    .select2-container { width: 100px !important; }

    .trInputs{
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
    }
</style>


<div class="fixHeader_Div">
    <table class="<?php echo table_class(); ?>" id="staff-attendance-table">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('hrms_attendance_staff_code');?><!--Staff Code--></th>
                <th style="width: 26px; height: 21px"></th>
                <th style="width: auto"><?php echo $this->lang->line('common_name');?><!--Name--></th>
                <th style="max-width: 62px;">
                    <?php echo $this->lang->line('hrms_attendance_is_attended');?><!-- Is Attended-->
                    <input type="checkbox" id="selectAllChk" style="margin:2px" onchange="selectAll(this)" <?php echo $disabled; ?>/>
                </th>
                <th style="width: 100px; z-index: 10"> <?php echo $this->lang->line('hrms_attendance_attendance_time');?><!--Attendance Time--></th>
                <th style="width: 100px !important; z-index: 10"> <?php echo $this->lang->line('hrms_attendance_present_type');?><!--Present Type--></th>
                <th style="width:130px"> <?php echo $this->lang->line('hrms_attendance_remarks');?><!--Remarks--></th>
            </tr>
        </thead>

        <tbody>
        <?php


        foreach($emp_arr as $key=>$row){
        $filePath = imagePath() .$row->EmpImage;
        $emp_img = checkIsFileExists($filePath);
        $empName =  $row->empName; // $row->Ename1.' '.$row->Ename2.' '.$row->Ename3.' '.$row->Ename4;
        $attTime = ( !empty($row->AttTime) )? $row->AttTime : $attTime;
        $AttPresentTypeID = ( !empty($row->AttPresentTypeID) )? $row->AttPresentTypeID : 0;
        $remarks = ( !empty($row->AttPresentRemarks) )? $row->AttPresentRemarks : '';
        $isChecked = ( $row->isAttended == 1 )? 'checked' : '';


        $tr_data = $row->ECode.''.$empName;
         $remark =   $this->lang->line('hrms_attendance_remarks');
        echo '<tr data-value="'.$tr_data.'">
                <td>'.($key+1).'</td>
                <td>'.$row->ECode.'</td>
                <td> <img src="'.$emp_img.'" class="emp-attendance-img"/></td>
                <td>'.$empName.'</td>
                <td align="center">
                    <input type="hidden" name="att-emp[]" value="'.$row->EIdNo.'" />
                    <input type="checkbox" name="isAttended[]" class="is-emp-attended" value="'.$row->EIdNo.'" style="margin:2px"
                    onchange="selectEmp(this)" '.$isChecked.'  '.$disabled.'/>
                </td>
                <td>
                    <div class="input-group  bootstrap-timepicker timepicker">
                        <span class="input-group-addon" style="padding:0px 7px; font-size: 10px">
                            <i class="glyphicon glyphicon-time" style="font-size:10px"></i>
                        </span>
                        <input type="text" name="att-time[]" class="form-control timeTxt trInputs" value="'.$attTime.'" style="width:80px" '.$disabled.' />
                    </div>
                </td>
                <td style="width: 100px !important;">
                    '. form_dropdown('att-type[]', $attDrop, $AttPresentTypeID, 'class="form-control select2 attType" onchange="changeStatus(this)" '.$disabled.'') .'
                </td>
                <td style="width:130px"> <input type="text" name="remarks[]" class="form-control trInputs" value="'.$remarks.'" placeholder="'.$remark.'" '.$disabled.'/> </td>
             </tr>';/*Remarks*/

        }
        ?>
        </tbody>
    </table>
</div>


<script type="text/javascript">
    var isComplete = $('#isComplete');
    var empSearchBox = $('#searchItem');
    var totalRowCount = '<?php echo count($emp_arr) ?>';
    var empAttendance_arr = [];

    $('.select2').select2();
    $('.timeTxt').timepicker();


    $(document).ready(function() {
        $('#saveAatDetail_btn').prop('disabled', <?php echo $isClosed; ?>);
        isComplete.prop('disabled', <?php echo $isClosed; ?>);
        isComplete.prop('checked', <?php echo $isClosed; ?>);

        $('#staff-attendance-table').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 999999
        });

        $('#totalRowCount').text( totalRowCount );
        $('#showingCount').text( '<?php echo count($emp_arr) ?>' );
        empSearchBox.val('');
    });

    empSearchBox.keyup(function(){
        var searchKey = $.trim($(this).val()).toLowerCase();
        var tableTR = $('#staff-attendance-table tbody>tr');
        var checkedRowCount = 0;
        var row = 0;
        $("#overlay").show();
        tableTR.removeClass('hideTr evenTR oddTR');

        tableTR.each(function(){
            var dataValue = ''+$(this).attr('data-value')+'';
            dataValue = dataValue.toLocaleLowerCase();

            if(dataValue.indexOf(''+searchKey+'') == -1){
                $(this).addClass('hideTr');
            }
            else{
                row++;

                if( $(this).find('td:eq(4) .is-emp-attended').is(':checked') ){
                    checkedRowCount++;
                }
            }

            $('#showingCount').text(row);
        });

        applyRowNumbers();
        changeStatusAllSelectCheckbox(checkedRowCount);

    });

    function applyRowNumbers(){
        var m = 1;

        $('#staff-attendance-table tbody>tr').each(function(i){
            if( !$(this).hasClass('hideTr') ){

                var isZero = ( m % 2 );
                if( isZero == 0 ){
                    $(this).addClass('evenTR');
                }else{
                    $(this).addClass('oddTR');
                }

                $(this).find('td:eq(0)').html( m );
                m += 1;

            }
        });

        $("#overlay").hide();
    }

    function selectAll(obj){
        var isChecked = $(obj).is(':checked');

        $('.is-emp-attended').each(function(){
            if( !$(this).closest('tr').hasClass('hideTr') ){
                $(this).prop('checked', isChecked);

                var thisVal = $(this).val();
                var thisPresentType = $(this).closest('tr').find('td:eq(6) .attType');

                if ( $(this).is(':checked') ) {
                    thisPresentType.val(1);
                    thisPresentType.change();
                    var inArray = $.inArray(thisVal, empAttendance_arr);
                    if (inArray == -1) {
                        empAttendance_arr.push(thisVal);
                    }
                }
                else {
                    thisPresentType.val(4);
                    thisPresentType.change();
                    empAttendance_arr = $.grep(empAttendance_arr, function(data, index) {
                        return data.id != thisVal
                    });
                }
            }
        });
    }

    function selectEmp(obj){

        var thisVal = $(obj).val();
        var thisPresentType = $(obj).closest('tr').find('td:eq(6) .attType');

        if ( $(obj).is(':checked') ) {
            thisPresentType.val(1);
            thisPresentType.change();
            var inArray = $.inArray(thisVal, empAttendance_arr);
            if (inArray == -1) {
                empAttendance_arr.push(thisVal);
            }
        }
        else {
            thisPresentType.val(4);
            thisPresentType.change();
            empAttendance_arr = $.grep(empAttendance_arr, function(data, index) {
                return data.id != thisVal
            });
        }

        changeStatusAllSelectCheckbox();
    }

    function changeStatusAllSelectCheckbox(selectedDisplayCount=null){
        var selectAllChk = $('#selectAllChk');
        var displayCount = parseInt( $.trim($('#showingCount').text()) );

        if( selectedDisplayCount == null ){

            var tableTR = $('#staff-attendance-table tbody>tr');

            tableTR.each(function(){
                if( !$(this).hasClass('hideTr') ) {
                    if ($(this).find('td:eq(4) .is-emp-attended').is(':checked')) {
                        selectedDisplayCount++;
                    }
                }
            });

        }

        selectAllChk.attr('onchange', '');
        if( displayCount != 0 ){
            selectAllChk.prop('checked', (displayCount == selectedDisplayCount));
        }
        else{
            selectAllChk.prop('checked', false);
        }
        selectAllChk.attr('onchange', 'selectAll(this)');

    }

    function changeStatus(obj){
        var closestCheckBox = $(obj).closest('tr').find('td:eq(4) .is-emp-attended');
        var thisPresentType = $(obj).val();

        if( thisPresentType == 4 ){
            $(closestCheckBox).prop('checked', false);
        }
        else{
            $(closestCheckBox).prop('checked', true);
        }
    }
</script>

<?php
