<?php
$locations = load_pos_location_drop();
$warehousemulti = load_pos_location_drop_multi();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_outlet');
echo head_page($title, false);
/*echo '<pre>';print_r($locations);echo '<pre>';*/
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="col-md-3">
        <div class="form-group col-sm-3">
            <label class="col-md-5 control-label">Warehouse </label>
            <div class="col-md-12">
                <?php echo form_dropdown('wareHouseIDfltr[]', $warehousemulti, '', 'multiple class="form-control" onchange="load_emp_from_warehouse()" id="wareHouseIDfltr"'); ?>
            </div>
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label class="col-md-5 control-label">Employee </label>
        <div class="col-md-12">
            <div id="div_emp_filter">
                <select name="employee[]" class="form-control select2" id="employee"
                        multiple="">
                </select>
            </div>
        </div>
    </div>

    <div class="form-group col-sm-1">
        <label class="col-md-5 control-label" style="color: white;">Employee </label>
        <div class="col-md-12">
            <button type="button" onclick="load_counterDetails()" class="btn btn-success btn-sm pull-right"><i
                        class="fa fa-plus"></i> Generate
            </button>
        </div>
    </div>

    <div class="col-md-3 pull-right">
        <button type="button" onclick="open_addEmpModel()" class="btn btn-primary btn-sm pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('posr_master_add_employee'); ?><!--Add Employee-->
        </button>
    </div>
    </div>


    <div class="table-responsive">
        <table id="counterMaster_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%"><?php echo $this->lang->line('posr_master_emp_code'); ?><!--Emp Code--></th>
                <th style="width: 30%">
                    <?php echo $this->lang->line('posr_master_employee_name'); ?><!--Employee Name--></th>
                <th style="width: 30%">Outlet<!--Warehouse-->
                    <!-- Outlet --></th>
                <th style="width: 5%">Super Admin</th>
                <th style="width: 5%">Warehouse Admin</th>
                <th style="width: 10%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="wareHouseUser_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" id="counterCreate_modal_title"></h3>
                </div>
                <form role="form" id="counter_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">

                                    <?php echo $this->lang->line('common_warehouse');?><!--Warehouse--></label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="wareHouseID" name="wareHouseID">
                                        <option value="">
                                            <?php echo $this->lang->line('posr_master_select_a_warehouse'); ?><!--Select a Warehouse--></option>
                                        <?php
                                        foreach ($locations as $loc) {
                                            echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] .' - ' . $loc['wareHouseLocation'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('posr_master_employee'); ?><!--Employee--></label>
                                <div class="col-sm-6">
                                    <?php echo form_dropdown('employeeID', employees_pos_outlet_drop(), '', 'class="form-control" id="employeeID"'); ?>
                                    <!--<input type="text" class="form-control" id="employeeName" name="employeeName">
                                    <input type="hidden" id="employeeID" name="employeeID" value="">
                                    <input type="hidden" id="employeeCode" name="employeeCode" value="">-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="requestLink" name="requestLink">
                        <input type="hidden" id="updateID" name="updateID">

                        <button type="submit" class="btn btn-primary btn-sm updateBtn submitBtn">
                            <?php echo $this->lang->line('common_update'); ?><!--Update--></button>
                        <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade pddLess" data-backdrop="static" id="buttonAccessControlModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
            <div class="modal-content"> <!-- <div class="color-line"></div>-->
                <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                    </button>
                    <h4 class="modal-title">Enable/Disable Buttons for <span id="userName"></span>
                        <span class="crew_outlet_title"></span></h4>
                </div>
                <div class="modal-body" style="min-height: 200px; ">
                    <table class="<?php echo table_class_pos(1) ?>" id="buttonAccessControlTable" style="font-size:12px;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Button</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"
            src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
    <script type="text/javascript">
        var configPageVariables = {};
        var modal_title = $("#counterCreate_modal_title");
        var wareHouseUser_model = $("#wareHouseUser_model");
        var counter_form = $("#counter_form");
        var counterMaster_table = '';
        var employeeName = $("#employeeName");


        $(document).ready(function () {
            $("#wareHouseID,#employeeID").select2();
            $('.headerclose').click(function () {
                fetchPage('system/pos/ware_house_users', 'Test', 'POS');
            });

            employeeSearch_typeHead();
            counter_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    wareHouseID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
                    employeeID: {validators: {notEmpty: {message: 'Employee ID is required.'}}}
                },
            }).on('success.form.bv', function (e) {
                $('.submitBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var data = $form.serializeArray();
                var requestUrl = $('#requestLink').val();
                save_update(data, requestUrl);

            });

            $("#wareHouseIDfltr").multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: '200px',
                maxHeight: '30px',
                numberDisplayed: 1
            });

            $("#wareHouseIDfltr").multiselect2('selectAll', false);
            $("#wareHouseIDfltr").multiselect2('updateButtonText');


            $('#employee').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: '200px',
                maxHeight: '30px',
                numberDisplayed: 1
            });
            $("#employee").multiselect2('selectAll', false);
            $("#employee").multiselect2('updateButtonText');

            load_emp_from_warehouse();
        });

        function employeeSearch_typeHead() {
            var item = new Bloodhound({
                datumTokenizer: function (d) {
                    return Bloodhound.tokenizers.whitespace();
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: "<?php echo site_url();?>Pos/emp_search/?q=%QUERY"
            });

            item.initialize();
            employeeName.typeahead(null, {
                minLength: 3,
                highlight: true,
                displayKey: 'empName',
                source: item.ttAdapter(),
                templates: {
                    empty: [
                        '<div class="tt-suggestion"><div style="white-space: normal;">',
                        'unable to find any item that match the current query',
                        '</div></div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div><strong>{{ECode}}</strong> – {{empName}}</div>')
                }
            }).on('typeahead:selected', function (object, datum) {
                $('#employeeID').val(datum.EIdNo);
                $('#employeeCode').val(datum.ECode);
                counter_form.bootstrapValidator('revalidateField', 'employeeID');


            });
        }

        employeeName.keyup(function (e) {
            if (e.keyCode != 13) {
                $('#employeeID').val('');
                counter_form.bootstrapValidator('revalidateField', 'employeeID');
            }
        });

        function load_counterDetails() {
            var selectedWarehouse = $('#wareHouseIDfltr').val();
            var selectedEmp = $('#employee').val();

            if (jQuery.isEmptyObject(selectedWarehouse)) {
                myAlert('e','Select Warehouse');
                return false;
            }

            if (jQuery.isEmptyObject(selectedEmp)) {
                myAlert('e','Select Employee');
                return false;
            }

            counterMaster_table = $('#counterMaster_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/fetch_ware_house_user'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }

                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });

                    $('input').on('ifChecked', function (event) {
                        if ($(this).hasClass('suprAdmin')) {
                            update_superadmin_warehouse(this,1);
                        }
                    });

                    $('input').on('ifUnchecked', function (event) {
                        if ($(this).hasClass('suprAdmin')) {
                            update_superadmin_warehouse(this,0);
                        }
                    });

                    $('input').on('ifChecked', function (event) {
                        if ($(this).hasClass('warehouseadmn')) {
                            update_warehouse_admin(this,1);
                        }
                    });

                    $('input').on('ifUnchecked', function (event) {
                        if ($(this).hasClass('warehouseadmn')) {
                            update_warehouse_admin(this,0);
                        }
                    });
                },
                "aoColumns": [
                    {"mData": "userID"},
                    {"mData": "ECode"},
                    {"mData": "empName"},
                    {"mData": "wareHouseDescription"},
                    {"mData": "superAdmn"},
                    {"mData": "wareAdmn"},
                    {"mData": "action"}
                ],
                // "columnDefs": [{
                //     "targets": [5],
                //     "orderable": false
                // }],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "selectedWarehouse[]", "value": selectedWarehouse});
                    aoData.push({"name": "selectedEmp[]", "value": selectedEmp});
                    $.ajax
                    ({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }

        $('.submitBtn').click(function () {
            if ($(this).hasClass('updateBtn')) {
                $('#requestLink').val('<?php echo site_url('pos/update_ware_house_user'); ?>');
            } else {
                $('#requestLink').val('<?php echo site_url('pos/add_ware_house_user'); ?>');
            }
        });

        function save_update(data, requestUrl) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: requestUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        wareHouseUser_model.modal("hide");
                        setTimeout(function () {
                            counterMaster_table.ajax.reload();
                        }, 300);


                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function open_addEmpModel() {
            counter_form[0].reset();
            $('#wareHouseID').val('').change();
            $('#employeeID').val('').change();
            counter_form.bootstrapValidator('resetForm', true);
            modal_title.text('<?php echo $this->lang->line('posr_master_assign_employees_to_warehouse');?>');
            /*Assign Employees to warehouse*/
            wareHouseUser_model.modal({backdrop: "static"});
            $('.submitBtn').prop('disabled', false);
            btnHide('saveBtn', 'updateBtn');
        }

        function edit_wareHouseUsers(editID, userID, empName, wareHouseID) {

            counter_form[0].reset();
            counter_form.bootstrapValidator('resetForm', true);
            modal_title.text(' <?php echo $this->lang->line('posr_master_edit_warehouse_users');?>');
            /*Edit Warehouse Users*/
            wareHouseUser_model.modal({backdrop: "static"});
            $('#wareHouseID').val(wareHouseID).change();
            $('#employeeID').val(userID).change();
            //$("#wareHouseID,#employeeID").select2();
            $('#employeeName').val(empName);
            $('#updateID').val(editID);
            btnHide('updateBtn', 'saveBtn');
            //$('#wareHouseID').val(data['wareHouseID']).change();
            //$('#employeeID').val(data['employeeID']).change();

        }

        function btnHide(btn1, btn2) {
            $('.' + btn1).show();
            $('.' + btn2).hide();
        }

        function delete_wareHouseUsers(id, eCode, wLocation) {
            swal({
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
                        type: 'post',
                        dataType: 'json',
                        data: {'autoID': id},
                        url: "<?php echo site_url('Pos/delete_ware_house_user'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);

                            if (data[0] == 's') {
                                setTimeout(function () {
                                    counterMaster_table.ajax.reload();
                                }, 300);
                            }
                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            );
        }

        function update_superadmin_warehouse(ds,valu) {
            var autoID=ds.value;
            var valu=valu;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'autoID': autoID,'valu': valu},
                url: "<?php echo site_url('Pos/update_superadmin_warehouse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        setTimeout(function () {
                            counterMaster_table.ajax.reload();
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }

        function update_warehouse_admin(ds,valu) {
            var res = ds.value.split("_");
            var autoID=res[0];
            var WHID=res[1];
            var valu=valu;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'autoID': autoID,'valu': valu,'WHID': WHID},
                url: "<?php echo site_url('Pos/update_warehouse_admin'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        setTimeout(function () {
                            counterMaster_table.ajax.reload();
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }

        function load_emp_from_warehouse() {
            var warehouse=$('#wareHouseIDfltr').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {warehouse: warehouse},
                url: "<?php echo site_url('Pos/load_emp_from_warehouse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_emp_filter').html(data);
                    $('#employee').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: '200px',
                        maxHeight: '30px',
                        numberDisplayed: 1
                    });
                    $("#employee").multiselect2('selectAll', false);
                    $("#employee").multiselect2('updateButtonText');

                    load_counterDetails();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function edit_button_access(EIdNo,wareHouseID,crewFirstName) {
            configPageVariables.EIdNo = EIdNo;
            configPageVariables.wareHouseID = wareHouseID;
            $("#userName").text(crewFirstName);
            warehouseUserButtonAccess();
            $("#buttonAccessControlModal").modal('show');
        }

        function warehouseUserButtonAccess() {
            $('#buttonAccessControlTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Pos_config/warehouseuser_button_access_details'); ?>",
                "aaSorting": [[1, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $(".accessControlSwitch").bootstrapSwitch();
                },
                "aoColumns": [
                    {"mData": "userAccessID"},
                    {"mData": "buttonText"},
                    {"mData": "action"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name': 'EIdNo', 'value': configPageVariables.EIdNo});
                    aoData.push({'name': 'wareHouseID', 'value': configPageVariables.wareHouseID});
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

        function accessControlSwitchToggle() {
            var user_access_id = $(this).data('user_access_id');
            var warehouse_user_id = $(this).data('warehouse_user_id');
            var button_id = $(this).data('button_id');
            var status = this.checked?0:1;//this value is inverted because system records isDisabled status.
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_config/change_button_access_privilege"); ?>',
                dataType: 'json',
                data: {user_access_id:user_access_id,status:status,warehouse_user_id:warehouse_user_id,button_id:button_id},
                async: false,
                success: function (data) {
                    warehouseUserButtonAccess();
                }
            });

        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-05
 * Time: 11:20 AM
 */