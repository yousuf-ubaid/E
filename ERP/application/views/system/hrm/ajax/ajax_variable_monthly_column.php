<?php
    $disabled_str = ($disabled == 1) ? 'disabled' : '' ;
   
    
?>

<div class="table-responsive" style="padding: 0px !important;">
    <div>
        <a href="" class="btn btn-excel btn-md pull-right" id="btn-excel" download="Variable_Payement_Report.xls"
            onclick="var file = tableToExcel('print_variable_payment', 'Variable Payement Report'); $(this).attr('href', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </a>
    </div>
    <div id="print_variable_payment">

    <table id="unAssignedMachine_ex" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 15px">#</th>
                <th class="hide" style="width: 100px">EMP ID</th>
                <th style="width: 100px">EMP Code</th>
                <th style="width: 100px">EMP Name</th>
                <th style="width: 100px">Attandance Date</th>
                <th style="width: 200px">Check In</th>
                <th style="width: 200px">Check Out</th>
                <th style="width: 100px">Working Hours</th>
                <?php foreach($columns as $column) { 
                    echo "<th style='width: 100px'>".$column['monthlyDeclaration']."</th>";
                } ?>
            </tr>
        </thead>

        <tbody>
            <?php if(count($data) > 0) { foreach($data as $attandance) { ?>

                <?php //print_r($attandance); exit; ?>
                <tr>
                    <td></td>
                    <td class="hide" ><?php echo $attandance['empID'] ?></td>
                    <td class="text-bold"><?php echo $attandance['ECode'] ?></td>
                    <td class="text-bold"><?php echo $attandance['Ename1'] ?></td>
                    <td class="text-bold"><?php echo $attandance['attendanceDate'] ?></td>
                    <td style="width: 200px">
                        <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                            <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"><i class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                            <input type="text" name="checkIn" class="form-control timeTxt trInputs remove-required-class" value="<?php echo $attandance['checkIn'] ?>" style="width:80px" id="checkIn-1" disabled>
                        </div>
                    </td>
                    <td style="width: 200px">
                        <div class="input-group  bootstrap-timepicker timepicker timeDiv">
                            <span class="input-group-addon" style="padding:0px 7px; font-size: 10px"><i class="glyphicon glyphicon-time" style="font-size:10px"></i></span>
                            <input type="text" name="checkIn" class="form-control timeTxt trInputs remove-required-class" value=" <?php echo $attandance['checkOut'] ?>" style="width:80px" id="checkIn-1" disabled>
                        </div>
                   </td>
                    <td><?php echo isset($attandance['working_hours']) ? $attandance['working_hours'] : 0  ?></td>
                    <?php foreach($columns as $column) { 
                        $monthlyDeclaration = str_replace(" ","_",$column['monthlyDeclaration']);
                        $id = $attandance['ID'];

                        if($column['calType'] == 2){
                            //$attandance[$monthlyDeclaration]
                            $checked = ($attandance[$monthlyDeclaration] > 0) ? 'checked': '';
                            echo '<td><input type="checkbox" name="days[]"  value="1" '.$checked.'  onchange="change_monthlyValue('.$attandance['ID'].','.$column['monthlyDeclarationID'].',this)" '.$disabled_str.' /></td>';
                           // echo '<td><input type="text" name="days[]" value="'.$attandance[$monthlyDeclaration].'" onchange="change_monthlyValue('.$attandance['ID'].','.$column['monthlyDeclarationID'].',this)" '.$disabled_str.'/></td>';
                        }else{
                            $month_arr = explode(':',$attandance[$monthlyDeclaration]);
                            $mn_0 = isset($month_arr[0]) ? $month_arr[0] : 0;
                            $mn_1 = isset($month_arr[1]) ? $month_arr[1] : 0;
                            $hours_class = 'grace_hours_'.$column['monthlyDeclarationID'].'_'.$id;
                            $minutes_class = 'grace_mins_'.$column['monthlyDeclarationID'].'_'.$id;
                            echo ' <td style="text-align: center">
                                <div class="" style="width: 55px">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <input type="text" name="'. $id .'" class="trInputs timeBox txtH number '.$hours_class.'" style="width: 25px" value="'.trim($mn_0).'" onchange="change_monthlyValue('.$attandance['ID'].','.$column['monthlyDeclarationID'].',this,\'hours\')" '.$disabled_str.'>
                                        </span>
                                    <span style="font-size: 14px; font-weight: bolder"> : </span>
                                        <span class="input-group-btn">
                                            <input  type="text" name="'. $id .'" class="trInputs  timeBox txtM number '.$minutes_class.'" style="width: 25px" value="'.trim($mn_1).'" onchange="change_monthlyValue('.$attandance['ID'].','.$column['monthlyDeclarationID'].',this,\'minutes\')" '.$disabled_str.'>
                                        </span>
                                    </div>
                                </div>
                            </td>';
                        }
                       
                    } ?>
                </tr>
            <?php } } else{
                echo '<tr><td colspan="6" class="text-bold">No Employee Result Found</td></tr>';
            } ?>

           
       
        </tbody>
    </table>

    </div>
</div>

<script>


    //  $('#unAssignedMachine_ex').DataTable();
    

    function change_monthlyValue(ID,mothlyDeclarationID,ev,type = 'day'){
        
        var entered_val = $(ev).is(':checked');
        
        entered_val = (entered_val == true) ? 1 : 0;

        var hours = $(ev).closest('tr').find('.grace_hours_'+mothlyDeclarationID+'_'+ID).val();
        var minutes = $(ev).closest('tr').find('.grace_mins_'+mothlyDeclarationID+'_'+ID).val();

        if(type != 'day'){
            var entered_val = hours+':'+minutes;
        }
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empViewID': ID,'mothlyDeclarationID':mothlyDeclarationID,'value':entered_val,'type':type},
            url: "<?php echo site_url('Employee/update_pay_variable_values'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                   
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

    function excel_download(){

        var from_date = $('#fromDate').val();
        var to_date = $('#toDate').val();

        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {'from_date': from_date,'to_date':to_date,'excel':1},
            url: '<?php echo site_url('Employee/get_attandance_variable_table'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attandance_va_view').empty();
                $('#attandance_va_view').html(data);
                $('#modal_variable_attandance').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });


    }

</script>