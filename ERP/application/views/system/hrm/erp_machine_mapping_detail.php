<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine_detail');
echo head_page($title, false);


$policyArr = leavePolicy_drop();
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_leave()" ><i class="fa fa-plus"></i>  <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="machineMappingDetail" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 50%"><?php echo $this->lang->line('hrms_attendance_column_name');?><!--Column Name--></th>
                <th style="min-width: 50%"><?php echo $this->lang->line('hrms_attendance_column_mapping');?><!--Column Mapping--></th>
                <th style="min-width: 50%"><?php echo $this->lang->line('hrms_attendance_sort_order');?><!--Sort Order--></th>
                <th style="min-width: 5%"></th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>

    <div class="modal fade" id="leaveType_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_machine_detail');?><!--Machine Detail--></h4>
                </div>
              <?php echo form_open('','role="form" class="form-horizontal" id="newLeave_form" method="get"'); ?>
                <input type="hidden" id="machineID" name="machineID" value="<?php echo $this->input->post('page_id') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_column');?><!--Column--></label>
                        <div class="col-sm-6">
                          <?php /*echo form_dropdown('machineTypeID', machine_type_drop(), '', 'class="form-control form1 select2" id="machineTypeID" '); */?>
                            <input type="machineTypeID" name="columnName" id="columnName" class="form-control">

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save()" class="btn btn-primary btn-sm modalBtn" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
              <?php echo form_close();?>
            </div>
        </div>
    </div>

    <script>
        machineMasterID=<?php echo json_encode(trim($this->input->post('page_id'))); ?>;
$('#machineID').val(<?php echo json_encode(trim($this->input->post('page_id'))); ?>);
        var modalBtn = $('.modalBtn');

        $(document).ready(function() {
            load_machine_mapping_detail();
            $('.headerclose').click(function(){
                fetchPage('system/hrm/erp_machine_mapping','Test','HRMS');
            });

        });

        function edit_machinMapping(machineMasterID){
            fetchPage('system/hrm/erp_machine_mapping_detail',machineMasterID,'HRMS');
        }

        function edit_updatemachinetype(value,masterID,detailID){
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/update_machineMappingcolumn_detail'); ?>',
                data: {value:value,masterID:masterID,detailID:detailID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping_detail();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }

        function edit_updateSortOrder(value,masterID,detailID){
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/update_machineMapping_detail'); ?>',
                data: {value:value,masterID:masterID,detailID:detailID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping_detail();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }

        function load_machine_mapping_detail(){
            $('#machineMappingDetail').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_machinedetail'); ?>",
                "aaSorting": [[0, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);




                        x++;
                    }
                    $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                    $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                    $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
                },
                "aoColumns": [
                    {"mData": "sortOrder"},
                    {"mData": "description"},
                    {"mData": "columnMapping"},
                    {"mData": "sortOrderdesc"},
                    {"mData": "action"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "machineMasterID", "value": machineMasterID });
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }


        function new_leave(){
           $('#machineTypeID').val('');

            $('#leaveType_modal').modal({backdrop: "static"});
        }



        function save(){

            var postData = $('#newLeave_form').serializeArray();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_machineMapping_detail'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping_detail();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

        }

        function edit_LeaveType( editID, des, isPaidLeave ){

            $('#newLeave_form input, #newLeave_form select').not('.isPaidLeaveTxt, .isPaidLeave').prop('value', '');
            $('#myModalLabel').text('Edit Leave Type');
            $('#editID').val(editID);
            $('#leaveDescription').val(des);
            /* $('#policy').val(policy);*/
            modalBtn.hide();
            modalBtn.removeAttr('disabled');
            $('#updateBtn').show();

            $('.isPaidLeave').prop('checked', false);

            if( isPaidLeave == 1 ){
                $('#isPaid').prop('checked', true);
            }
            else{
                $('#isNotPaid').prop('checked', true);
            }

            $('#leaveType_modal').modal({backdrop: "static"});
        }

        function update(){
            var postData = $('#newLeave_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/update_leaveTypes'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping_detail(  $('#editID').val() );
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }

        function delete_machinMappingdetail(machineDetailID){
            swal(
                {
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        url :"<?php echo site_url('Employee/delete_machine_detail'); ?>",
                        type : 'post',
                        dataType : 'json',
                        data : {machineDetailID:machineDetailID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if( data[0] == 's'){
                                load_machine_mapping_detail()
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
