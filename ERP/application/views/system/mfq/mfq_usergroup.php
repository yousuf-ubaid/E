<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_user_groups');
echo head_page($title, false);
$segment = fetch_mfq_segment(true);

?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" style="margin-right: 17px;" class="btn btn-primary pull-right"
                onclick="openusergroupmodel()"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_add_user_group') ?><!--Add User Group-->
        </button>
    </div>
</div>
<hr style="margin-top: 7px;margin-bottom: 7px;">
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="mfq_usergrouptbl" class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th class="text-uppercase" style="width: 12%"><?php echo $this->lang->line('common_description') ?><!--DESCRIPTION--></th>
                    <th class="text-uppercase" style="width: 10%"><?php echo $this->lang->line('manufacturing_group_type') ?><!--GROUP TYPE--></th>
                    <th class="text-uppercase" style="width: 10%"><?php echo $this->lang->line('common_segment') ?><!--SEGMENT--></th>
                    <th class="text-uppercase" style="width: 10%"><?php echo $this->lang->line('common_status') ?><!--STATUS--></th>
                    <th class="text-uppercase" style="width: 10%"><?php echo $this->lang->line('common_type') ?><!--TYPE--></th>

                    <th style="width: 5%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="usergroupmodel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="usergroup_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="userGroupID" name="userGroupID">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_description') ?><!--Description--></label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="description" name="description" required>
                    <!--<input type="text" name="partNumber" id="partNumber" class="form-control" placeholder="Part No" >-->
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('manufacturing_group_type') ?><!--Group Type--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('usergrouptype', array('' => 'Select Group Type', '1' => 'RFQ', '2' => 'Estimate','3' => 'Job Card'), '', 'class="form-control select2" id="usergrouptype"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_segment') ?><!--Segment--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('segmentmfq',$segment, '', 'class="form-control select2" id="segmentmfq"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('manufacturing_is_active') ?><!--Is Active--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="IsActive" type="checkbox"
                                                                          class="IsActive" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('manufacturing_is_default') ?><!--Is Default--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="Isdefault" type="checkbox"
                                                                          class="Isdefault" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?><!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
     id="mfq_user_groupdetail_model">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_employee')?><!--Employee--> </h4>
            </div>
            <div class="modal-body">

                <div id="sysnc">
                    <div class="table-responsive">
                        <input type="hidden" name="groupform" id="group_employee">
                        <table id="employee_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th class="text-uppercase" style="width: 5%">#</th>
                                <th class="text-uppercase" style="width: 12%"><?php echo $this->lang->line('manufacturing_employee_name')?><!--EMPLOYEE NAME--></abbr></th>
                                <th class="text-uppercase" style="width: 12%"><?php echo $this->lang->line('common_email')?><!--EMAIL--></th>
                                <th class="text-uppercase" style="width: 5%">&nbsp;
                                    <button type="button" data-text="Add" onclick="addemployee()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_employee')?><!--Add Employee-->
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>

            <h4 class="modal-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->lang->line('manufacturing_added_employee')?><!--Added Employees--> </h4>
            <hr>
            <div id="sysnc">
                <div class="table-responsive">
                    <input type="hidden" name="groupform" id="group_employee">
                    <table id="savedemployee" class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_employee_name')?><!--EMPLOYEE NAME--></abbr></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_email')?><!--EMAIL--></th>
                            <th style="min-width: 5%">&nbsp;

                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    var oTable2;
    var oTable3;
    var selectedItemsSync = [];
    userGroupID = null;

    p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    if (p_id) {
        userGroupID = p_id;

    } else {
        $('.btn-wizard').addClass('disabled');
    }


    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_usergroup', 'Test', 'UserGroup');
        });
        template();

        $('#usergroup_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            var isDefault;
            if ($("#IsActive").is(':checked')) {
                IsActive = 1;
            } else {
                IsActive = 0;
            }
            if ($("#Isdefault").is(':checked')) {
                Isdefault = 1;
            } else {
                Isdefault = 0;
            }
            data.push({name: "IsActive", value: IsActive});
            data.push({name: "Isdefault", value: Isdefault});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_UserGroup/save_mfq_user'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        template();
                        $('#usergroupmodel').modal('hide');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function openusergroupmodel() {
        $('#usergroup-title').text('<?php echo $this->lang->line('manufacturing_add_user_group') ?>');
        $('.extraColumns input').iCheck('uncheck');
        $('#usergroup_master_form')[0].reset();
        $('#usergroup_master_form').bootstrapValidator('resetForm', true);
        $('#userGroupID').val('');
        $("#usergrouptype").val(null).trigger("change");
        $("#segmentmfq").val(null).trigger("change");
        $('#usergroupmodel').modal('show');

    }

    function opendatatableemployee() {
        $('.extraColumns input').iCheck('uncheck');
        selectedItemsSync = [];
        $('#usergroup_master_form')[0].reset();
        $('#usergroup_master_form').bootstrapValidator('resetForm', true);
        $('#userGroupID').val('');
        $('#usergroupmodel').modal('show');

    }

    function template() {
        oTable = $('#mfq_usergrouptbl').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_UserGroup/fetch_usergroup'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
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
                {"mData": "userGroupID"},
                {"mData": "description"},
                {"mData": "grouptyperej"},
                {"mData": "segmentCode"},
                {"mData": "status"},
                {"mData": "type"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,4,5,6], "searchable": false}],
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

    function edit_usergroup(userGroupID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {userGroupID: userGroupID},
            url: "<?php echo site_url('MFQ_UserGroup/edit_mfq_user'); ?>",
            beforeSend: function () {
                startLoad();
                $('#usergroup-title').text('<?php echo $this->lang->line('manufacturing_edit_user_group') ?>');
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#usergroup_master_form').bootstrapValidator('resetForm', true);
                    $('#description').val(data['description']);
                    $('#userGroupID').val(data['userGroupID']);
                    if (data['isActive'] == 1) {
                        $('#IsActive').iCheck('check');
                    }
                    else if (data['isActive'] == 0) {
                        $('#IsActive').iCheck('uncheck');
                    }

                    if (data['isDefault'] == 1) {
                        $('#Isdefault').iCheck('check');
                    } else if (data['isDefault'] == 0){
                        $('#Isdefault').iCheck('uncheck');
                    }
                    $('#usergrouptype').val(data['groupType']).change();
                    $('#segmentmfq').val(data['segmentID']).change();
                    $('#usergroupmodel').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-purple',
        radioClass: 'iradio_square_relative-purple',
        increaseArea: '20%'
    });

    function add_userGroupDetail(userGroupID) {
        selectedItemsSync = [];
        template_userGroupDetail(userGroupID);
        template_userGroupDetailsavedemp(userGroupID);
        $('#group_employee').val(userGroupID);
        $('#mfq_user_groupdetail_model').modal('show');
    }

    function template_userGroupDetail(userGroupID) {
        oTable2 = $('#employee_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('MFQ_UserGroup/fetch_employees'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
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
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },

            "aoColumns": [
                {"mData": "primaryKey"},
                {"mData": "employeename"},
                {"mData": "email"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "userGroupID", "value": userGroupID});
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

    function template_userGroupDetailsavedemp(userGroupID) {//saved employees showing data table
        oTable3 = $('#savedemployee').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('MFQ_UserGroup/fetch_savedusergroup'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
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
                {"mData": "primaryKey"},
                {"mData": "employeename"},
                {"mData": "email"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "userGroupID", "value": userGroupID});
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

    function addemployee() {
        var userGroupID = $('#group_employee').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_UserGroup/link_employee"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync, 'userGroupID': userGroupID},
            async: false,
            success: function (data) {
                //   myAlert(data[0], data[1]);
                if (data['status']) {
                    refreshNotifications(true);
                    oTable2.draw();
                    oTable.draw();
                    oTable3.draw();
                    $('.extraColumns input').iCheck('uncheck');
                    $("#mfq_user_groupdetail_model").modal('show');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function delete_userGroupDetail(employeeNavigationID) { /*delete employee*/
        swal({
                title: "Are You Sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_UserGroup/delete_employee'); ?>",
                    type: 'post',
                    data: {'employeeNavigationID': employeeNavigationID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable3.draw();
                            oTable2.draw();
                            $('.extraColumns input').iCheck('uncheck');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function delete_userGroupDetaildatatable(userGroupID) {//delete user group
        swal({
                title: "Are you sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'userGroupID': userGroupID},
                    url: "<?php echo site_url('MFQ_UserGroup/delete_details_group_table'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                            refreshNotifications(true);
                            stopLoad();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>