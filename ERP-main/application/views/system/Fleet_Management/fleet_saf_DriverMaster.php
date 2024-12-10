<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$title = $this->lang->line('fleet_Driver_Master');
echo head_page($title, false);


?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-3">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active');?> </td><!--Active-->
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_in_active');?></td><!-- In-Active-->
                </tr>
            </table>
        </div>
        <div class="col-md-9 text-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="fetchPage('system/Fleet_Management/load_Driver_edit_view', '', '<?php echo $this->lang->line('fleet_driverADD'); ?>')"><i
                        class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add'); ?><!-- Add New School-->
            </button>
        </div>
    </div>

    <hr>
    <div class="table-responsive">
        <table id="driver_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 14%"><?php echo $this->lang->line('fleet_driverID');?></th><!--Driver ID-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_driver_name');?></th><!--Reference-->
                <th style="min-width: 14%"><?php echo $this->lang->line('common_contact_number');?></th><!--Number-->
                <th style="min-width: 20%"><?php echo $this->lang->line('common_address');?></th><!--Address-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_licenceNo');?></th><!--Licence Number-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="Driver-modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title Add-Driver-title" id="myModalLabel"><?php echo $this->lang->line('fleet_Driver'); ?></h4>
                </div>
                <?php echo form_open('', 'role="form" class="form-horizontal" id="AddDriver_Form"'); ?>
                <input type="hidden" name="driverMasID" id="driverMasID"/>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class=" control-label"
                                   for="driverName"> <?php echo $this->lang->line('fleet_driverName'); ?> <?php required_mark(); ?></label>

                            <input type="text" name="driverName" id="driverName" class="form-control">
                        </div>
                        <div class="col-sm-12">
                            <label class=" control-label"
                                   for="drivPhoneNo"> <?php echo $this->lang->line('fleet_driverPhone'); ?></label>

                            <input type="text" name="drivPhoneNo" id="drivPhoneNo" class="form-control">
                        </div>
                        <div class="col-sm-12">
                            <label class=" control-label"
                                   for="drivAddress"> <?php echo $this->lang->line('fleet_driverAddress'); ?></label>

                            <input type="text" name="drivAddress" id="drivAddress" class="form-control">
                        </div>
                        <div class="col-sm-12">
                            <label class=" control-label" for="isActive"><?php echo $this->lang->line('fleet_driverStatus'); ?></label>

                            <div class="form-control">
                                <label class="radio-inline">
                                    <input type="radio" name="isActive" value="1" id="Active" class="isActive"
                                           checked="checked"><?php echo $this->lang->line('fleet_driverActive'); ?><!--Male-->
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isActive" value="0" id="InActive" class="isActive"><?php echo $this->lang->line('fleet_driverInactive'); ?><!--Makthabs-->
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    var DriverAddForm = $('#AddDriver_Form');
    var oTable;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_saf_DriverMaster', '', '<?php $this->lang->line('fleet_Driver'); ?>');

        });


        DriverAddForm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                driverName: {validators: {notEmpty: {message: 'Driver Name is required.'}}},
                drivPhoneNo: {validators: {notEmpty: {message: 'Driver Phone Number is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var postData = $form.serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo site_url('Fleet/Save_New_Driver') ?>",
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    if (data['isActive'] == 1) {
                        $('#Active').iCheck('check');
                    } else if (data['isActive'] == 2) {
                        $('#InActive').iCheck('check');
                    }

                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    else if (data['error'] == 0) {
                        oTable.draw();
                        $('#Driver-modal').modal('hide');
                        myAlert('s', data['message']);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        });

        driverTable();
    });

    function driverTable() {

            oTable = $('#driver_table').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Fleet/fetch_drivers'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "driverMasID"},
                    {"mData": "driverCode"},
                    {"mData": "driverName"},
                    {"mData": "drivPhoneNo"},
                    {"mData": "drivAddress"},
                    {"mData": "licenceNo"},
                    {"mData": "isActive"},
                    {"mData": "action"},
                ],
                "columnDefs": [{"searchable": false, "targets": [0,6,7]}],
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

    function new_Driver() {
        $('.Add-Driver-title').text('Driver');
        DriverAddForm[0].reset();
        DriverAddForm.bootstrapValidator('resetForm', true);
        $("#driverMasID").val('');
        $('#Driver-modal').modal({backdrop: "static"});
    }

    function edit_driver(id,element) {
        var table = $('#driver_table').DataTable();
        var thisRow = $(element);
        var details = table.row(thisRow.parents('tr')).data();
        $('#Driver-modal').modal({backdrop: "static"});
        $('#driverMasID').val($.trim(id));
        $('#driverName').val($.trim(details.driverName));
        $('#drivPhoneNo').val($.trim(details.drivPhoneNo));
        $('#drivAddress').val($.trim(details.drivAddress));
        $('#isActive').val($.trim(details.isActive));
    }

    function delete_driver(id, description) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Fleet/delete_driverMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'driverMasID': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            driverTable();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

</script>

