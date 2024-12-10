<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$groupArr =array_group_by($empDetails, 'empID');
    ?>
<form id="general_ot_template_frm">
    <input type="hidden"  value="<?php echo $MasterID ?>" name="MasterID"/>
<table class="table table-bordered table-striped table-condensed" style="overflow: scroll;">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('hrms_leave_management_emp_number');?><!--Emp Number--></th>
        <th><?php echo $this->lang->line('hrms_leave_management_emp_name');?><!--Emp Name--></th>
        <?php
        if (!empty($detail)){
        foreach ($detail as $val){
        ?>
        <th><?php if ($val['defaultcategoryID']==0) {
                echo $val['categoryDescription'];
            } else {
                echo $val['defultDescription'];
            } ?></th>
            <th style="font-size: 13px !important;"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>

    <?php
    }
    }
    ?>
        <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
    </tr>
    </thead>
    <?php
    if (!empty($empDetails)){
        echo'<tbody>';
        foreach ($groupArr as $val2){
            $generalOTMasterID = $val2[0]['generalOTMasterID'];
            $empID = $val2[0]['empID'];
            echo '<tr>';
            echo '<td>'.$val2[0]['ECode'].'</td><td>'.$val2[0]['empname'].'</td>';
            foreach ($detail as $val) {
                $tempID = $val['templatedetailID'];
                $inputType = $val['inputType'];
                $hours = search_otElement($val2, $tempID);
                $amount = search_otAmount($val2, $tempID);
                if($val['inputType']==1 ){
                    $hrs=floor($hours/60);
                    $min=$hours%60;
                    echo '<td>
<input type="hidden" id="" style="width: 30px;" class="hourorDays" value="" name="hourorDays[]"/>
                              <input type="text" id="" class="hours" placeholder="HH" onchange="save_general_ot_template_change_frm_single(this)" style="width: 24px;text-align: right;" value="'.$hrs.'" name="hours[]"/>
                               <input type="text" id="" class="minuites" placeholder="MM" onchange="save_general_ot_template_change_frm_single(this)" style="width: 24px;text-align: right;" value="'.$min.'" name="minuites[]"/>
                <input type="hidden"  value="'.$tempID.'" class="templateDetailID" name="templateDetailID[]"/>
                <input type="hidden"  value="'.$empID.'" class="empID" name="empID[]"/>
                <input type="hidden"  value="'.$inputType.'" class="inputType" name="inputType[]"/>
                <input type="hidden"  value="'.$generalOTMasterID.'" class="generalOTMasterID" name="generalOTMasterID[]"/>
                </td>';
                }else{
                    echo '<td> <input type="hidden" id="" class="hours" style="width: 24px;" value="" name="hours[]"/>
                               <input type="hidden" id="" class="minuites" style="width: 24px;" value="" name="minuites[]"/>
<input type="number" id="hourorDays" style="width: 40px;text-align: right;" onchange="save_general_ot_template_change_frm_single(this)" class="hourorDays" value="'.$hours.'" name="hourorDays[]"/>
                <input type="hidden"  value="'.$tempID.'" class="templateDetailID" name="templateDetailID[]"/>
                <input type="hidden"  value="'.$empID.'" class="empID" name="empID[]"/>
                <input type="hidden"  value="'.$inputType.'" class="inputType" name="inputType[]"/>
                <input type="hidden"  value="'.$generalOTMasterID.'" class="generalOTMasterID" name="generalOTMasterID[]"/>
                </td>';
                }
                echo '<td id="amount_'.$empID.'-'.$val['templatedetailID'].'">'.$amount.'</td>';

                //echo '<td> <input type="number" id="" value="'.$hours.'" name="hourorDays[]"/> </td>';
            }
            echo '<td><a onclick="delete_general_ot_template_employees('.$generalOTMasterID.','.$empID.');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td>';
            echo '</tr>';
        }
        echo'</tbody>';
    }
    ?>
</table>
</form>
<hr>
<div class="row">
    <button type="button" style="margin-right: 15px"  class="btn btn-success btn-sm pull-right"
        onclick="comfirm_general_ot_template()"><i class="fa fa-floppy-o"></i><?php echo $this->lang->line('common_save_and_confirm') ?><!-- Save & Confirm-->
    </button>
    <button type="button" style="margin-right: 15px"  class="btn btn-primary btn-sm pull-right"
            onclick="save_general_ot_template_frm()"> <?php echo $this->lang->line('common_save_as_draft') ?><!--Save & Draft-->
    </button>

</div>

<script>
    function saveTemplateData(type,empid,templateid,data){
        if(type=='h'){
            var value=$
        }

    }

    /*function save_general_ot_template_frm(){
        var postData = $('#general_ot_template_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php //echo site_url('OverTime/save_general_ot_template_frm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    over_time_templates();
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }*/

    function save_general_ot_template_frm(){
        myAlert('s', 'Successfully Saved');
        fetchPage('system/OverTime/erp_genaral_ot_template', 'Test', 'Attendance Summary');
    }

    function comfirm_general_ot_template(){
        var postData = $('#general_ot_template_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('OverTime/comfirm_general_ot_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetchPage('system/OverTime/erp_genaral_ot_template', 'Test', 'Attendance Summary');
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_general_ot_template_change_frm(){
        var postData = $('#general_ot_template_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('OverTime/save_general_ot_template_frm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                    over_time_templates();
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_general_ot_template_employees(generalOTMasterID,empID){
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'generalOTMasterID': generalOTMasterID,'empID':empID},
                    url: "<?php echo site_url('OverTime/delete_general_ot_template_employees'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            over_time_templates();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_general_ot_template_change_frm_single(det){
        var hourorDays = $(det).closest('td').find('.hourorDays').val();
        var hours = $(det).closest('td').find('.hours').val();
        var minuites = $(det).closest('td').find('.minuites').val();
        var templateDetailID = $(det).closest('td').find('.templateDetailID').val();
        var empID = $(det).closest('td').find('.empID').val();
        var inputType = $(det).closest('td').find('.inputType').val();
        var generalOTMasterID = $(det).closest('td').find('.generalOTMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'hourorDays[]': hourorDays,'hours[]':hours,'minuites[]':minuites,'templateDetailID[]':templateDetailID,'empID[]':empID,'inputType[]':inputType,'generalOTMasterID[]':generalOTMasterID},
            url: "<?php echo site_url('OverTime/save_general_ot_template_frm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#amount_'+empID+'-'+templateDetailID).html(data[2]['transactionAmount']);
             /*   over_time_templates();*/
                stopLoad();

            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>


