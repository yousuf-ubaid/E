<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('fleet_helper');
$date_format_policy = date_format_policy();
$employee_arr = all_employee_drop();

?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">

    <style>
        .title {
            float: left;
            width: 170px;
            text-align: right;
            font-size: 13px;
            color: #7b7676;
            padding: 4px 10px 0 0;
        }
    </style>

    <br><br>
    <div class="tab-content">

        <?php echo form_open('', 'role="form" id="AddDriverForm"'); ?>
        <input type="hidden" name="driverMasID" id="driverMasID">

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('fleet_driver_details'); ?><!--Vehicle Details--></h2>
                </header>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_driverName'); ?><!--Vehicle Body--></label>
                    </div>
                  <!--  <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field"><input class="form-control"
                                                                                          id="driverName"
                                                                                          name="driverName"
                                                                                          placeholder="<?php echo $this->lang->line('fleet_driverName'); ?>"
                                                                                          required>
                                        <span class="input-req-inner"></span></span>
                    </div>
-->
                    <!--<div class="form-group col-sm-4 ">
                        <div class="input-group">
                            <input type="text" class="form-control" id="driverName" name="driverName" required>
                            <input type="hidden" class="form-control" id="Ename2" name="Ename2">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearDriver()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Supplier" rel="tooltip"
                                onclick="link_emp_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                    </div>
                    </div>-->

                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="employeeName" name="employeeName" required>
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Employee" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                        </div>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_otherName'); ?><!--Vehicle Body--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input class="form-control"
                               id="OtherName"
                               name="OtherName"
                               placeholder="<?php echo $this->lang->line('fleet_otherName'); ?>">
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_driver_age'); ?><!--Vehicle Body--></label>
                    </div>
                    <div class="form-group col-sm-4">
                                  <input class="form-control"
                                         id="driverAge"
                                         name="driverAge"
                                         placeholder="<?php echo $this->lang->line('fleet_driver_age'); ?>">

                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_BloodGroup'); ?><!--Vehicle Body--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <select id="bloodGroup" class="form-control select2"
                                data-placeholder="<?php echo $this->lang->line('fleet_BloodGroup'); ?>"
                                name="bloodGroup">
                            <option value="">Select Blood Group</option>
                            <?php

                            $bg_drop = load_bloodGroup();
                            if (!empty($bg_drop)) {
                                foreach ($bg_drop as $val) {
                                    ?>
                                    <option
                                            value="<?php echo $val['BloodTypeID'] ?>"><?php echo $val['BloodDescription'] ?></option>
                                    <?php

                                }
                            }
                            ?>
                        </select>
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_driverPhone'); ?><!--Name_with_initial--></label>

                    </div>
                    <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field"><input class="form-control"
                                                                                          id="drivPhoneNo"
                                                                                          name="drivPhoneNo"
                                                                                          placeholder="<?php echo $this->lang->line('fleet_driverPhone'); ?>"
                                                                                          required>
                                        <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_driverAddress'); ?><!--Marital Status :--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field"> <input type="text" name="drivAddress"
                               placeholder="<?php echo $this->lang->line('fleet_driverAddress'); ?>"
                               value="" id="drivAddress" class="form-control">
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_licenceNo'); ?><!--Vehicle Body--></label>
                    </div>
                    <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field"><input class="form-control"
                                                                                          id="licenceNo"
                                                                                          name="licenceNo"
                                                                                          placeholder="<?php echo $this->lang->line('fleet_licenceNo'); ?>"
                                                                                          required>
                                        <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('fleet_licenceExpiryDate'); ?><!--Date of Birth--></label>
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="liceExpireDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="liceExpireDate" class="form-control" required>
                                </div>
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                    <label class="title">
                        <?php echo $this->lang->line('fleet_driverDescription'); ?><!--Marital Status :--></label>
                    </div>

                    <div class="form-group col-sm-4">
                            <textarea class="form-control"
                                      id="driveDescript"
                                      name="driveDescript"
                                      rows="2"></textarea>

                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class=" title" for="status"><?php echo $this->lang->line('fleet_driverStatus'); ?></label>
                    </div>

                    <div class="form-group col-sm-4">
                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox"><?php echo $this->lang->line('common_active'); ?>&nbsp;&nbsp;</label>
                                    <input id="active" type="radio" data-caption="" class="columnSelected"
                                           name="active" value="1">
                                </div>
                            </label>
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox"><?php echo $this->lang->line('common_in_active'); ?>&nbsp;&nbsp;</label>
                                    <input id="inactive" type="radio" data-caption="" class="columnSelected"
                                           name="active" value="0">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>




        <div class="row" style="margin-top: 11px;">
            <div class="form-group col-sm-12">

                <button type="submit" class="btn btn-primary btn-sm pull-right" id="saveBtn">
                    <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <!--   <button id="save_btn" class="btn btn-primary pull-right" type="submit">
                        <?php echo $this->lang->line('common_save'); ?><!--Save</button> -->
            </div>
        </div>

        </form>

    </div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" id="employee_model" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalLabel">
                        <?php echo $this->lang->line('fleet_link_employee'); ?><!--Link Employee--></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('common_employee_name'); ?><!--Employee--></label>
                            <div class="col-sm-7">
                                <?php
                                $emp_arr = employee_drop();
                                echo form_dropdown('EIdNo', $emp_arr, '', 'class="form-control select2" id="EIdNo" required'); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="fetch_emp_detail()">
                        <?php echo $this->lang->line('common_add_employee'); ?><!--Add employee--></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"
                        id="exampleModalLabel"> <?php echo $this->lang->line('fleet_link_driver'); ?></h4>
                    <!--Link Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3"
                                   class="col-sm-3 control-label"> <?php echo $this->lang->line('fleet_Driver'); ?> </label>
                            <div class="col-sm-7">
                                <?php
                                echo form_dropdown('employee_id', $employee_arr, '', 'class="form-control select2" id="employee_id" required'); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                    <button type="button" class="btn btn-primary"
                            onclick="fetch_employee_detail()"><?php echo $this->lang->line('fleet_add_driver'); ?> </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        var AddDriverForm = $('#AddDriverForm');
        var EIdNo;
        $(document).ready(function () {
            $('.extraColumnsgreen input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                radioClass: 'iradio_square_relative-green',
                increaseArea: '20%'
            });

            Inputmask().mask(document.querySelectorAll("input"));
            EIdNo = null;
            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_DriverMaster', '','Dricer Master');
            });

            $('.select2').select2();

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            driverMasID = p_id;
            load_driver();

            AddDriverForm.bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    driverName: {validators: {notEmpty: {message: 'Required'}}},
                    drivPhoneNo: {validators: {notEmpty: {message: 'Required.'}}},
                    drivAddress: {validators: {notEmpty: {message: 'Required.'}}},
                    licenceNo: {validators: {notEmpty: {message: 'Required.'}}},
                    liceExpireDate: {validators: {notEmpty: {message: 'Required.'}}}
                },
            }).on('success.form.bv', function (e) {

                $('.saveBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var postData = $form.serializeArray();
                postData.push({'name': 'employeeID', 'value': EIdNo});
                postData.push({'name': 'employee_det', 'value': $('#employee_id option:selected').text()});
                $.ajax({
                    type: 'post',
                    url: "<?php echo site_url('Fleet/Save_New_Driver') ?>",
                    //    url: "<?php echo site_url('com_ngo_dataTableUpdate/Save_New_School') ?>",

                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            driverMasID = data[2];
                            $('#driverMasID').val(driverMasID);
                        }
                        fetchPage('system/Fleet_Management/fleet_saf_DriverMaster', '','Dricer Master');
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                //$('#AddDriverForm').bootstrapValidator('revalidateField', 'licenceIssue');
                $('#AddDriverForm').bootstrapValidator('revalidateField', 'liceExpireDate');
            });
        });

        function fetch_employee_detail() {
            var employee_id = $('#employee_id').val();
            if (employee_id == '') {
                //swal("", "Select An Employee", "error");
                myAlert('e', 'Select A Driver');
            } else {
                EIdNo = employee_id;
                var empName = $("#employee_id option:selected").text();
                /*  var empNameSplit = empName.split('|');*/
                $('#employeeName').val($.trim(empName)).trigger('input');
                $('#employeeName').prop('readonly', true);
                $('#emp_model').modal('hide');
            }
        }

        function load_driver() {
            if (driverMasID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'driverMasID': driverMasID},
                    url: "<?php echo site_url('Fleet/load_driver'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            driverMasID = data['driverMasID'];


                            $('#driverMasID').val(data['driverMasID']).change();

                            if (data["empID"] > 0) {

                                    $('#employeeName').prop('readonly', true);
                                    $('#employeeName').val(data['driverName']);
                                } else {
                                    $('#employeeName').val(data['driverName']);
                                }
                            EIdNo = data['empID'];
                            $('#employee_id').val(data['empID']).change();
                            $('#OtherName').val(data['OtherName']);
                            $('#driverAge').val(data['driverAge']);
                            $('#bloodGroup').val(data['bloodGroup']).change();
                            $('#drivPhoneNo').val(data['drivPhoneNo']);
                            $('#drivAddress').val(data['drivAddress']);
                            $('#licenceNo').val(data['licenceNo']);
                            $('#liceExpireDate').val(data['liceExpireDate']);
                            $('#driveDescript').val(data['driveDescript']);
                           setTimeout(function () {
                            if (data['isActive'] == 1) {
                                $('#active').iCheck('check');
                            }else if(data['isActive'] == 0){
                                $('#inactive').iCheck('check');
                            } }, 500);
                            $('#save_btn').html('<?php echo $this->lang->line('common_update');?>');
                            //  document.getElementById('nxtBtn').style.display = 'block';

                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }


        function clearEmployee() {
            $('#employee_id').val('').change();
            $('#employeeName').val('').trigger('input');
            $('#employeeName').prop('readonly', false);
            EIdNo = null;
        }
        function link_employee_model() {
            /*$('#employee_id').val('').change();*/
            $('#emp_model').modal('show');
        }


        function fetch_emp_detail() {
            var EIdNo = $('#EIdNo').val();
            if (EIdNo) {
                window.EIdNo = EIdNo;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'EIdNo': EIdNo},
                    url: "<?php echo site_url('Fleet/fetch_employee_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $('#driverName').val(data['Ename2']).trigger('input');
                            $('#Ename2').val(EIdNo);

                            $('#driverName').prop('readonly', true);
                            $('#employee_model').modal('hide');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            } else {

            }
        }

    </script>

