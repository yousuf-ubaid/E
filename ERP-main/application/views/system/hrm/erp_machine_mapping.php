

<!--Translation added by Naseek-->

<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine');
echo head_page($title, false);

$connectionType = connection_drop();
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_leave()"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="leaveMaster" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 50%"><?php echo $this->lang->line('common_description');?><!--Description--></th>

                <th style="min-width: 5%"></th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>

    <div class="modal fade" id="leaveType_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_machine');?><!--Machine--></h4>
                </div>
              <?php echo form_open('', 'role="form" class="form-horizontal" id="newLeave_form" method="get"'); ?>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_connection_type');?><!--Connection Type--></label>
                        <div class="col-sm-6">
                            <select id="connectionTypeID" name="connectionTypeID" onchange="loadConnectionType(this)"
                                    class="form-control select2">
                              <?php if ($connectionType) {
                                foreach ($connectionType as $row) {
                                  ?>
                                    <option data-db="<?php echo $row['dbYN'] ?>"
                                            value="<?php echo $row['connectionTypeID'] ?>"><?php echo $row['connectionType'] ?></option>
                                  <?php
                                }
                              } ?>
                            </select>

                        </div>
                    </div>
                    <div class="form-group hideShow">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_db_host');?><!--DB Host--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="dbhost" name="dbhost">
                        </div>
                    </div>
                    <div class="form-group hideShow">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_db_name');?><!--DB Name--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="dbname" name="dbname">
                        </div>
                    </div>
                    <div class="form-group hideShow">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_db_user');?><!--DB User--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="dbpassword" name="dbpassword">
                        </div>
                    </div>
                    <div class="form-group hideShow">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_db_password');?><!--DB Password--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="dbuser" name="dbuser">
                        </div>
                    </div>
                    <div class="form-group hideShow">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_attendance_db_table_name');?><!--DB Table Name--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="dbtableName" name="dbtableName">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save()" class="btn btn-primary btn-sm modalBtn" id="saveBtn"><?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>

                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
                <input type="hidden" id="editID" name="editID">
              <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <script>
        $('.hideShow').hide();
        var modalBtn = $('.modalBtn');
        window.dbYN;

        $(document).ready(function () {
            load_machine_mapping();
            $('.headerclose').click(function () {
                fetchPage('system/hrm/erp_machine_mapping', 'Test', 'HRMS');
            });

        });

        function loadConnectionType(thes) {
             dbYN = $('#connectionTypeID option:selected').attr('data-db');

            if (dbYN == 1) {
                $('.hideShow').show();
            }
            else {
                $('.hideShow').hide();
            }

        }

        function edit_machinMapping(machineMasterID) {
            fetchPage('system/hrm/erp_machine_mapping_detail', machineMasterID, 'HRMS');
        }

        function load_machine_mapping(selectedRowID=null) {
            $('#leaveMaster').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_machineType'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    /*if (oSettings.bSorted || oSettings.bFiltered) {
                     for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                     $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                     if( parseInt(oSettings.aoData[i]._aData['ID']) == selectedRowID ){
                     var thisRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
                     $(thisRow).addClass('dataTable_selectedTr');
                     }
                     }
                     }*/


                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                        if (parseInt(oSettings.aoData[x]._aData['ID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }

                        x++;
                    }

                },
                "aoColumns": [
                    {"mData": "ID"},
                    {"mData": "description"},
                    {"mData": "action"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
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

        function new_leave() {
            modalBtn.hide();
            modalBtn.removeAttr('disabled');
            $('#saveBtn').show();

            $('.isPaidLeave').prop('checked', false);
            $('#isPaid').prop('checked', true);
            $('#newLeave_form input, #newLeave_form select').not('.isPaidLeaveTxt, .isPaidLeave').prop('value', '');

            $('#leaveType_modal').modal({backdrop: "static"});
        }

        $('#intType').change(function () {
            if ($(this).val() == '1') {
                $('.perctageTR').fadeIn();
            } else {
                $('.perctageTR').fadeOut();
            }

            $('#percentage').val('');
        });

        function save() {

            var postData = $('#newLeave_form').serializeArray();
            postData.push({name: 'dbYN', value: dbYN});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_machineMapping'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

        }

        function edit_LeaveType(editID, des, isPaidLeave) {

            $('#newLeave_form input, #newLeave_form select').not('.isPaidLeaveTxt, .isPaidLeave').prop('value', '');
            $('#myModalLabel').text('Edit Leave Type');
            $('#editID').val(editID);
            $('#leaveDescription').val(des);
            /* $('#policy').val(policy);*/
            modalBtn.hide();
            modalBtn.removeAttr('disabled');
            $('#updateBtn').show();

            $('.isPaidLeave').prop('checked', false);

            if (isPaidLeave == 1) {
                $('#isPaid').prop('checked', true);
            }
            else {
                $('#isNotPaid').prop('checked', true);
            }

            $('#leaveType_modal').modal({backdrop: "static"});
        }

        function update() {
            var postData = $('#newLeave_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/update_leaveTypes'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#leaveType_modal').modal('hide');
                        load_machine_mapping($('#editID').val());
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }

        function delete_machinMapping(machineMasterID) {
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
                        async: true,
                        url: "<?php echo site_url('Employee/delete_machine_master'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {machineMasterID: machineMasterID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                load_machine_mapping()
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });
    </script>

<?php
