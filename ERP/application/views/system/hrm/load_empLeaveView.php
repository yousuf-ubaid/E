<?php
/*echo '<pre>';
print_r( $this->common_data);
echo '</pre>';*/
$leaveTypes = leaveTypes_drop(1);
?>

<style type="text/css">
    .frm_input{
        height: 28px;
        font-size: 12px;
    }
</style>


<div class="pull-right" style="margin-bottom: 2%">
    <!--<button type="button" class="btn btn-sm btn-primary disableBtn" onclick="add_Leaves()"> <i class="fa fa-plus"></i>&nbsp; Add </button>-->
</div>

<div class="box-body " style="padding: 0px;">
    <table class="table table-bordered" id="add_declarationTB">
        <thead>
        <tr>
            <th> Description </th>
            <th> Days / Policy </th>
            <!--<th width="70px"> Action </th>-->
        </tr>
        </thead>

        <tbody>
        <?php
        if( !empty($leaves) > 0) {
            foreach ($leaves as $lev) {
            $id=$lev['leaveEntitledID']; $leaveTypeID=$lev['leaveTypeID']; $days=$lev['days'];
            $policy=$lev['policyDescription']; $description=$lev['description'];
            $onClickEdit = "editEmpLeaveEntitle($id, $leaveTypeID, '$policy', $days )";
            $onClickDelete = "deleteEmpLeaveEntitle($id, '$description')";
            echo
             '<tr>
                <td>'.$description. '</td>
                <td>'.$days.' / '.$policy.'</td>
                /*<td align="right">
                    <a onclick="'.$onClickEdit.'"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; | &nbsp;
                    <a onclick="'.$onClickDelete.'"><span class="glyphicon glyphicon-trash" style="color:#d15b47"></span></a>
                </td>*/
            </tr>';
            }
        }
        else{
            echo '<tr><td colspan="2">No records found</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>



<div class="modal fade" id="empLeave_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <?php echo form_open('','role="form" class="form-horizontal" id="empLeave_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Employee Leave</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="<?php echo table_class(); ?>" id="add_leaveTB" style="width: 100%;margin-top: 2%">
                        <thead>
                        <tr>
                            <th style="width: 40%">Type</th>
                            <th style="width: 30%">Policy</th>
                            <th style="width: 20%">Days</th>
                            <th style="width: 10%">
                                <button class="btn btn-primary btn-xs" type="button" onclick="addNewRow_leaveTB()"> + </button>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select name="leaveType[]" class="form-control frm_input leaveType" id="" onchange="/*getPolicy(this)*/">
                                        <option > </option>
                                        <?php
                                        foreach($leaveTypes as $leave){
                                            echo '<option value="'.$leave['leaveTypeID'].'" data-value="'.$leave['policyDescription'].'">'.$leave['description'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="text" name="policy[]" class="form-control frm_input policy"  disabled /></td>
                                <td><input type="number" name="leave_days[]" class="form-control frm_input leave_days" /></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="empLeave_btn()">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
            <input type="hidden" name="empID" value="<?php echo $empID ?>">
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="empLeaveEdit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <?php echo form_open('','role="form" class="form-horizontal" id="empLeaveUpdate"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Employee Leave Edit</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="<?php echo table_class(); ?>" id="edit_leaveTB" style="width: 100%;margin-top: 2%">
                        <thead>
                        <tr>
                            <th style="width: 40%">Type</th>
                            <th style="width: 30%">Policy</th>
                            <th style="width: 20%">Days</th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select name="leaveType_e" class="form-control frm_input" id="leaveType_e" onchange="/*getPolicy(this)*/">
                                        <option > </option>
                                        <?php
                                        foreach($leaveTypes as $leave){
                                            echo '<option value="'.$leave['leaveTypeID'].'" data-value="'.$leave['policyDescription'].'">'.$leave['description'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="text" name="policy_e" class="form-control frm_input" id="policy_e" disabled /></td>
                                <td><input type="number" name="leave_days_e" class="form-control frm_input" id="leave_days_e" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="empLeaveUpdate_btn()">Save Changes</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
            <input type="hidden" name="empID" value="<?php echo $empID ?>">
            <input type="hidden" name="editID" id="editID" value="">
            <?php echo form_close();?>
        </div>
    </div>
</div>

<script type="text/javascript">
    number_validation();

    function add_Leaves(){
        $('#empLeave_modal').modal({backdrop: "static"});
    }

    $(document).on('change', '.leaveType', function(){
        var policy = $.trim($(this).find(':selected').data('value'));
        var row = $(this).closest('tr');
        policy = ( policy == '' )? '' : policy;
        row.find('.policy').attr('value', policy);
    });

    $('#leaveType_e').change(function(){
        var policy = $.trim($(this).find(':selected').data('value'));
        policy = ( policy == '' )? '' : policy;
        $('#policy_e').val(policy);

    });

    function addNewRow_leaveTB(){
        var row = $('#add_leaveTB tr').last().clone();
        var removeIcon = '<span class="glyphicon glyphicon-trash removeButton" style="color:#d15b47;margin:7px 15px;"></span>';
        var td1 = '<input type="text" name="policy[]" class="form-control frm_input policy"  disabled />';
        var td2 = '<input type="number" name="leave_days[]" class="form-control frm_input leave_days"  />';

        $('td:eq(1)', row).html(td1);
        $('td:eq(2)', row).html(td2);
        $('td:eq(3)', row).html(removeIcon);

        $('#add_leaveTB').append(row);
    }

    function empLeave_btn(){
        var postData = $('#empLeave_form').serializeArray();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_empLeaveEntitle'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    var leaveTabLink = $('#leaveTabLink');
                    $('#empLeave_modal').modal('hide');
                    leaveTabLink.attr('data-value', '0');
                    setTimeout(function () { leaveTabLink.click(); },300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function editEmpLeaveEntitle(id, typeID, policy, days){
        $('#leaveType_e').val(typeID);
        $('#policy_e').val(policy);
        $('#leave_days_e').val(days);
        $('#editID').val(id);

        $('#empLeaveEdit_modal').modal({backdrop:'static'});
    }

    function empLeaveUpdate_btn(){
        var postData = $('#empLeaveUpdate').serializeArray();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/update_empLeaveEntitle'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    var leaveTabLink = $('#leaveTabLink');
                    $('#empLeaveEdit_modal').modal('hide');
                    leaveTabLink.attr('data-value', '0');
                    setTimeout(function () { leaveTabLink.click(); },300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function deleteEmpLeaveEntitle(delID, des){
        swal(
            {
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_empLeaveEntitle'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'deleteID':delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's'){
                            var leaveTabLink = $('#leaveTabLink');
                            leaveTabLink.attr('data-value', '0');
                            setTimeout(function () { leaveTabLink.click(); },300);
                        }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }



</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-25
 * Time: 4:07 PM
 */