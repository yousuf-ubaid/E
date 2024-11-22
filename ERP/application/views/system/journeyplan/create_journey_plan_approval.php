<?php echo head_page($_POST['page_name'], false);
$this->load->helper('journeyplan_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$driver = fetch_drivers();
$vehicle = fetch_vehiclenumber();
$jm_arr = all_employee_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .titlebalance {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 12px;
        color: #151212;
        font-weight: bold;
        padding: 4px 10px 0 0;
    }

    .totalbal {
        float: left;
        width: 170px;
        text-align: left;
        font-size: 12px;
        color: #f76f01;
        font-weight: bold;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }
</style>
<?php echo form_open('', 'role="form" id="journeyplanheader_form"'); ?>
<input type="hidden" name="jpmasterid" id="jpmasterid">
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>JOURNEY PLAN HEADER</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">JP Number</label>
            </div>
            <div class="form-group col-sm-4">
                       <span class="input-req" title="Required Field">
                            <input type="text" class="form-control" id="jpnumberedit" name="jpnumber"
                                   placeholder="Journey Paln Number" readonly>
                       <span class="input-req-inner" style="z-index: 100"></span></span>
            </div>
            <!-- <div class="form-group col-sm-2">
                 <label class="title">No Of Passengers</label>
             </div>
             <div class="form-group col-sm-4">
                       <span class="input-req" title="Required Field">
                       <input type="text" class="form-control" id="noofpassengers" name="noofpassengers"
                              placeholder="No Of Passengers">
                           <span class="input-req-inner" style="z-index: 100"></span></span>
             </div>-->
            <div class="form-group col-sm-2">
                <label class="title">Departure Date</label>
            </div>
            <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                            <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="departuredate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="departuredate" class="form-control" readonly>
                        </div>
                       <span class="input-req-inner" style="z-index: 100"></span></span>
            </div>

        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Driver Name</label>
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('drivername', $driver, '', 'class="form-control select2" id="drivername" onchange="fetch_driverdetails(this.value)"  required disabled'); ?>
                            <span class="input-req-inner"></span>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Telephone Number</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="phonenumber" name="phonenumber" placeholder="Phone Number" readonly>
                <span class="input-req-inner"></span>
            </div>


        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Reason For Night Driving</label>
            </div>
            <div class="form-group col-sm-4">

                        <textarea class="form-control" rows="3" id="reasonnightdriving"
                                  name="reasonnightdriving" readonly></textarea>

            </div>

            <div class="form-group col-sm-2">
                <label class="title">Comments for Drivers</label>
            </div>
            <div class="form-group col-sm-4">

                        <textarea class="form-control" rows="3" id="commentfordrivers"
                                  name="commentfordrivers" readonly></textarea>

            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Offline tracking Number</label>
            </div>
            <div class="form-group col-sm-4">

                <input type="text" class="form-control" id="offlinetrackingnumber" name="offlinetrackingnumber" placeholder="Offline tracking Number" readonly>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12" >
                <fieldset>
                    <legend style="font-size: 16px;">Vehicle Details</legend>


                        <div class="col-md-3">
                            <center>
                                <div class="fileinput-new thumbnail" style="width: 180px; height: 130px;">
                                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                         class="img-responsive" style="width: 180px; height: 120px;">
                                    <input type="file" name="itemImage" id="itemImage" style="display: none;">
                                    <!--<input type="file" name="itemImage" id="itemImage" style="display: none;"
                                           onchange="loadCropImage(this)"/>-->
                                </div>
                            </center>
                        </div>
                    <div class="form-group col-sm-4"><label>Vehicle Number</label>
                        <?php echo form_dropdown('vehiclenumber', $vehicle, '', 'class="form-control select2" id="vehiclenumber" onchange="fetch_vehicaledetails(this.value)"  required disabled'); ?>
                        <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                    </div>

                        <div class="col-sm-9">
                            <input type="hidden" name="ivmsnoup" id="ivmsnoup" disabled>
                            <table class="table table-striped" id="profileInfoTable"
                                   style="background-color: #ffffff;">
                                <tbody>
                                <tr>
                                    <td>
                                        <strong> IVMS Number
                                            : </strong>
                                    </td>
                                    <td id="ivmsnumber" style="width: 70%;">.......</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> Vehicle Description
                                            : </strong>
                                    </td>
                                    <td id="vehicaledescription" style="width: 70%;">.......</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> Insurance Date
                                            : </strong>
                                    </td>
                                    <td id="insurancedate" style="width: 70%;">.......</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> Licenced Date
                                            : </strong>
                                    </td>
                                    <td id="licenceddate" style="width: 70%;">.......</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> Status
                                            : </strong>
                                    </td>
                                    <td id="status" style="width: 70%;">.......</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                </fieldset>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12" >
                <fieldset>
                    <legend style="font-size: 16px;">Journey Manager Details</legend>
                    <div class="form-group col-sm-4">
                        <label>Journey Manager</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="journeymanager" name="journeymanager" required readonly>
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;" disabled><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Journey Manager" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;" disabled><i class="fa fa-plus"></i></button>
                    </span>
                        </div>
                    </div>
                    <div class="form-group col-sm-4"><label>Tel Office</label>
                        <input type="text" class="form-control" id="jmphonenumber" name="jmphonenumber"
                               placeholder="JM Telephone Number Office" readonly>
                        <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                    </div>
                    <div class="form-group col-sm-4"><label>Tel Mobile</label>
                        <input type="text" class="form-control" id="jmphonenumbermob" name="jmphonenumbermob"
                               placeholder="JM Telephone Number Mobile" readonly>
                        <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                    </div>

                    <div class="form-group col-sm-4"><label>Vehicle Daily Checked</label>
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input id="vehicaledailychk" type="checkbox"
                                                                          data-caption="" class="columnSelected"
                                                                          name="vehicaledailychk" value="1" disabled><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                    <div class="form-group col-sm-4"><label>Counselling For Drivers</label>
                        <div class="skin-section extraColumns"><input id="counsellingdr" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="counsellingdr" value="1" disabled><label
                                    for="checkbox">&nbsp;</label></div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
</form>

<div class="row addTableView">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>ROUTE DETIALS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-11">
                <div id="jp_details"></div>
            </div>
            <div class="col-sm-1">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<br>
<div class="row addTableView">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>Passenger Detials</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-11">
                <div id="passanger_Detial_item"></div>
            </div>
            <div class="col-sm-1">
                &nbsp;
            </div>
        </div>
        <hr>
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
                    id="exampleModalLabel">Link Journey Manager</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Journey Manager</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $jm_arr, '', 'class="form-control select2" id="employee_id" onchange="fetch_jm_tp_employee(this.value)" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()">Save
                </button>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var jpnumber;
    var documetapprovedid;
    var EIdNo;
    var currency_decimal;
    $(document).ready(function () {

        number_validation();

        $('.select2').select2();
        $("[rel=tooltip]").tooltip();
        $(".paymentmoad").hide();
        $('.headerclose').click(function () {
            fetchPage('system/journeyplan/journey_plan', '', 'Journey Plan')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#grv_forjourneyplanheader_formm').bootstrapValidator('revalidateField', 'departuredate');
        });
        $('.datepicdetails').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datetimepicker3').datetimepicker({
            useCurrent: false,
            format: 'LT'
            });

    });


    p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    policy_id = <?php echo json_encode(trim($this->input->post('policy_id'))); ?>;
    if (p_id) {
        jpnumber = p_id;
        documetapprovedid = policy_id;
        load_jp_header();
    } else {
        loadjpnumber();
        $('.btn-wizard').addClass('disabled');
        $('.addTableView').addClass('hide');

    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
    });



    $('.next').click(function () {
        var nextId = $(this).parents('.tab-pane').next().attr("id");
        $('[href=#' + nextId + ']').tab('show');
    });

    $('.prev').click(function () {
        var prevId = $(this).parents('.tab-pane').prev().attr("id");
        $('[href=#' + prevId + ']').tab('show');
    });
    jpnumber = null;
    EIdNo = null;
    $('#journeyplanheader_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            noofpassengers: {validators: {notEmpty: {message: 'Number Of Passengers is required.'}}},
            departuredate: {validators: {notEmpty: {message: 'Departure Date is required.'}}},
            vehiclenumber: {validators: {notEmpty: {message: 'Vehicle number is required.'}}},
            drivername: {validators: {notEmpty: {message: 'Driver Name is required.'}}},
            journeymanager: {validators: {notEmpty: {message: 'Journey Manager is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'employeeID', 'value': EIdNo});
        data.push({'name': 'employee_det', 'value': $('#employee_id option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Journeyplan/save_journeyplan_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    jpnumber = data[2];
                    $('#jpmasterid').val(jpnumber);
                    $('.btn-primary').prop('disabled', false);
                    $('#save_btn').html('Update');
                    $('.addTableView').removeClass('hide');
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

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function link_employee_model() {
        $('#employee_id').val('').change();
        $('#emp_model').modal('show');
    }

    function clearEmployee() {
        $('#employee_id').val('').change();
        $('#journeymanager').val('').trigger('input');
        $('#journeymanager').prop('readonly', false);
        EIdNo = null;
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id == '') {
            myAlert('e', 'Select A Journey Manager');
        } else {
            EIdNo = employee_id;
            var empName = $("#employee_id option:selected").text();
            $('#journeymanager').val($.trim(empName)).trigger('input');
            $('#journeymanager').prop('readonly', true);
            $('#emp_model').modal('hide');
        }
    }

    function fetch_vehicaledetails(vehicalemasterid) {
        $('#ivmsnumber').html('.......');
        $('#vehicaledescription').html('.......');
        $('#insurancedate').html('.......');
        $('#licenceddate').html('.......');
        $('#status').html('.......');
        $("#changeImg").attr("src", "<?php echo base_url('images/item/no-image.png'); ?>");
        if(vehicalemasterid)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Journeyplan/fetch_vehicale_details'); ?>",
                success: function (data) {
                    if (data) {
                        $("#ivmsnumber").html(data['ivmsNo']);
                        $("#ivmsnoup").val(data['ivmsNo']);
                        $("#vehicaledescription").html(data['vehDescription']);
                        $('#insurancedate').html(data['insuranceDate']);
                        $('#licenceddate').html(data['licenseDate']);
                        $('#status').html(data['vehicalestatus']);
                    }
                    if (data['vehicleImage'] == ' ') {
                        $("#changeImg").attr("src", "<?php echo base_url('uploads/Fleet/no_image.jpg'); ?>");
                    } else {
                        $("#changeImg").attr("src", "<?php echo base_url('uploads/Fleet/VehicleImg'); ?>" + '/' + data['vehicleImage']);
                    }
                }
            });
        }

    }

    function fetch_driverdetails(drivermasterid) {
        $('#phonenumber').val(' ');
        if(drivermasterid)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'drivermasterid': drivermasterid},
                url: "<?php echo site_url('Journeyplan/fetch_driver_details'); ?>",
                success: function (data) {
                    if (data['drivPhoneNo']) {
                        $("#phonenumber").val(data['drivPhoneNo']);

                    }

                }
            });
        }

    }
    function fetch_jm_tp_employee() {
       // $("#jmphonenumber").val(' ');
       var empid = $('#employee_id').val();
        if(empid)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'employeeid': empid},
                url: "<?php echo site_url('Journeyplan/fetch_employee_details'); ?>",
                success: function (data) {
                    $("#jmphonenumber").val(data['EcTel']);
                    $("#jmphonenumbermob").val(data['EcMobile']);
                }
            });
        }

    }
    function loadjpnumber() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Journeyplan/fetch_jp_number'); ?>",
            success: function (data) {
                $("#jpnumberedit").val(data);
            }
        });
    }
    function load_jp_header()
    {
        if (jpnumber) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'jpnumber': jpnumber},
                url: "<?php echo site_url('Journeyplan/load_jp_header'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {

                    if (!jQuery.isEmptyObject(data)) {
                        jpnumber = data['journeyPlanMasterID'];

                        $('#documentApprovedID').val(documetapprovedid);
                        $('#jpmasterid').val(data['journeyPlanMasterID']);
                        $('#noofpassengers').val(data['noOfPassengers']);
                        $('#jpnumberedit').val(data['documentCode']);
                        $('#departuredate').val(data['departureDateconverted']);
                        $('#drivername').val(data['driverID']).change();
                        $('#reasonnightdriving').val(data['reasonForNightDriving']);
                        $('#vehiclenumber').val(data['vehicleID']).change();
                        $('#commentfordrivers').val(data['commentsForDriver']);
                        $('#offlinetrackingnumber').val(data['offlineTrackingRefNo']);
                        $('#Commentsjp').val(data['closedComment']);
                        $('#jpstatus').val(data['status']);

                        if (data["journeyManagerEmpID"] > 0) {
                           $('#journeymanager').prop('readonly', true);
                            $('#journeymanager').val(data['journeyManagerName']);
                        } else {
                            $('#journeymanager').val(data['journeyManagerName']);

                        }
                        $('#jmphonenumber').val(data['journeyManagerOfficeNo']);
                        $('#jmphonenumbermob').val(data['journeyManagerMobileNo']);

                        $('#employee_id').val(data['journeyManagerEmpID']).change();
                         EIdNo = data['journeyManagerEmpID'];
                        if (data['counsellingForDriver'] == 1) {
                            $('#counsellingdr').iCheck('check');
                        }else {
                            $('#counsellingdr').iCheck('uncheck');
                        }
                        if (data['vehicleDailyCheck'] == 1) {
                            $('#vehicaledailychk').iCheck('check');
                        }else {
                            $('#vehicaledailychk').iCheck('uncheck');
                        }
                        get_jp_passenger_details(jpnumber);
                        passanger_Detial_item(jpnumber)
                        $('#save_btn').html('Update');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }
    function get_jp_passenger_details(jpnumber) { //jp details
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'jpnumber': jpnumber},
            url: "<?php echo site_url('Journeyplan/load_detail_view_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#jp_details').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function passanger_Detial_item(jpnumber)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'jpnumber': jpnumber},
            url: "<?php echo site_url('Journeyplan/fetch_passanger_details_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#passanger_Detial_item').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function jp_detail_model()
    {
        $('#jp_detail_add_form')[0].reset();
        $('#jpmasterid').val(jpnumber);
        $('#jp_detail_add_table tbody tr').not(':first').remove();
        $("#jp_detail_add_modal").modal({backdrop: "static"});
    }
    function add_more_vouchers() {
        $('select.select2').select2('destroy');
        var appendData = $('#jp_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#jp_detail_add_table').append(appendData);
        var lenght = $('#jp_detail_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepicdetails').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#jp_detail_add_table').bootstrapValidator('revalidateField', 'departdatecls');
        });
        $('.datetimepicker3').datetimepicker({
            useCurrent: false,
            format: 'LT',

        }).on('dp.change', function (ev) {
            $('#jp_detail_add_table').bootstrapValidator('revalidateField', 'departtimecls');
        });
    }
    function save_details() {
        var $form = $('#jp_detail_add_form');
        var data = $form.serializeArray();
        data.push({'name': 'jpnumber', 'value': jpnumber});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Journeyplan/save_jp_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                    $('#jp_detail_add_form')[0].reset();
                    get_jp_passenger_details(jpnumber);//jp details
                    $('#iou_voucher_detail_add_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function save_draft() {
        if (jpnumber) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/journeyplan/journey_plan',jpnumber,'Journey Plan');
                });
        }
    }

    function confirmation() {
        if (jpnumber) {
            swal({
                    title: "Are you sure?",
                    text: "You want confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'jpnumber': jpnumber},
                        url: "<?php echo site_url('Journeyplan/jp_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }
                            else if (data['error'] == 0) {
                                myAlert('s', data['message']);
                                fetchPage('system/journeyplan/journey_plan',jpnumber,'Journey Plan');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function submit_jp_status() {
        var  masterid = $('#jpmasterid').val();
        var  status = $('#jpstatus').val();
        var  comment = $('#Commentsjp').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterid':masterid,'status':status,'comment':comment},
            url: "<?php echo site_url('Journeyplan/save_jp_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetchPage('system/journeyplan/journey_plan','','Journey Plan');
                }
            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
</script>
