<?php echo head_page($_POST['page_name'], false);
$this->load->helper('journeyplan_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$driver = fetch_drivers();
$vehicle = fetch_vehiclenumber();
$jm_arr = all_employee_drop();
$currency_arr = all_currency_new_drop();
$tour_arr = all_tour_types();
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
<?php echo form_open('', 'role="form" id="journeyplanheader_form" autocomplete = "off"'); ?>
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

                <div class="form-group col-sm-2">
                    <label class="title">Type of Tour</label>
                </div>
                <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                             <div class="input-group">
                                <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add-tourType"
                                                style="height: 27px; padding: 2px 10px;">
                                            <i class="fa fa-plus" style="font-size: 11px"></i>
                                        </button>
                                </span>
                                <div id="div_loadmodel">
                                    <?php echo form_dropdown('tourType', $tour_arr, '', 'class="form-control select2" id="tourType" required'); ?>
                                </div>
                             </div>
                            <span class="input-req-inner"></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Tour Date</label>
                </div>
                <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                            <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="departuredate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="departuredate" class="form-control">
                        </div>
                       <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Pick Up Time</label>
                </div>
                <div class="form-group col-sm-4">
                    <div class="input-group pickuptimePicker">
                        <input type="text" class="form-control " name="pickupTime" id="pickupTime" /><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Guest Name</label>
                </div>
                <div class="form-group col-sm-4">
                           <input type="text" class="form-control" id="guestName" name="guestName" placeholder="Guest Name">
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">No Of Passengers</label>
                </div>
                <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                           <input type="text" class="form-control" id="noofpassengers" name="noofpassengers"
                                  placeholder="No Of Passengers">
                               <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Driver Name</label>
                </div>
                <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('drivername', $driver, '', 'class="form-control select2" id="drivername" onchange="fetch_driverdetails(this.value)"  required'); ?>
                            <span class="input-req-inner"></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Driver Phone Number</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" class="form-control" id="phonenumber" name="phonenumber" placeholder="Driver Phone Number">
                    <span class="input-req-inner"></span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Guide Name</label>
                </div>
                <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
              <input type="text" class="form-control" id="guideName" name="guideName" placeholder="Guide Name">
                            <span class="input-req-inner"></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Guide Number</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" class="form-control" id="guideNumber" name="guideNumber" placeholder="Guide Phone Number">
                    <span class="input-req-inner"></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Villa Host</label>
                </div>
                <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="villaHost" name="villaHost" placeholder="Villa Host OR Agent Name">
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Room No</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" class="form-control" id="roomNo" name="roomNo" placeholder="Guide Phone Number">
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Reason For Night Driving</label>
                </div>
                <div class="form-group col-sm-4">
                        <textarea class="form-control" rows="3" id="reasonnightdriving"
                                  name="reasonnightdriving"></textarea>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Comments for Drivers</label>
                </div>
                <div class="form-group col-sm-4">
                        <textarea class="form-control" rows="3" id="commentfordrivers"
                                  name="commentfordrivers"></textarea>
                </div>
            </div>


            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Offline tracking Number</label>
                </div>
                <div class="form-group col-sm-4">

                    <input type="text" class="form-control" id="offlinetrackingnumber" name="offlinetrackingnumber" placeholder="Offline tracking Number">
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Currency</label>
                </div>
                <div class="form-group col-sm-4">
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID"  required'); ?>
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
                            <div class="input-group">
                                <input type="text" class="form-control" id="vehiclenumber" name="vehiclenumber" required>
                                <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearVehicle()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Vehicle" rel="tooltip"
                                onclick="link_Vehicle_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                                </span>
                            </div>
                            <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                        </div>



                        <div class="col-sm-9">
                            <input type="hidden" name="ivmsnoup" id="ivmsnoup">
                            <table class="table table-striped" id="profileInfoTable"
                                   style="background-color: #ffffff;">
                                <tbody>
                                <tr>
                                    <td>
                                        <strong> IVMS Number
                                            : </strong>
                                    </td>
                                    <td id="ivmsnumber" style="width: 70%;"><input id="ivmsNumber_add" name="ivmsNumber_add"></td>
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
                                <input type="text" class="form-control" id="journeymanager" name="journeymanager" required>
                                <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Journey Manager" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                            </div>
                        </div>
                        <div class="form-group col-sm-4"><label>Tel Office</label>
                            <input type="text" class="form-control" id="jmphonenumber" name="jmphonenumber"
                                   placeholder="JM Telephone Number Office">
                            <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                        </div>
                        <div class="form-group col-sm-4"><label>Tel Mobile</label>
                            <input type="text" class="form-control" id="jmphonenumbermob" name="jmphonenumbermob"
                                   placeholder="JM Telephone Number Mobile">
                            <span class="input-req-inner" style="width: 31px; height: 66px;"></span>
                        </div>
                        <br>
                        <div class="form-group col-sm-4"><label>Vehicle Daily Checked</label>
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="vehicaledailychk" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="vehicaledailychk" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-4"><label>Counselling For Drivers</label>
                            <div class="skin-section extraColumns"><input id="counsellingdr" type="checkbox"
                                                                          data-caption="" class="columnSelected"
                                                                          name="counsellingdr" value="1"><label
                                    for="checkbox">&nbsp;</label></div>
                        </div>

                    </fieldset>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-12">
                    <button class="btn btn-primary pull-right" type="submit" id="save_btn">Save</button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <div class="row addTableView">
        <div class="col-md-12 animated zoomIn"style="width: 108%;">
            <header class="head-title">
                <h2>ROUTE details</h2>
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
                <h2>Passenger details</h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="col-sm-11">
                    <div id="passanger_Detial_item"></div>
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
                <h2>Tour Price details</h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="col-sm-11">
                    <div id="tour_price_details"></div>
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
                <h2>Additional Charges</h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="col-sm-11">
                    <div id="additional_charge_details"></div>
                </div>
                <div class="col-sm-1">
                    &nbsp;
                </div>
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary " onclick="save_draft()" id="saveasdraft">Save as Draft</button>
                <button class="btn btn-success submitWizard" id="confirmbtn" onclick="confirmation()" id="confirmbtn">Confirm</button>
            </div>
        </div>

    </div>

    <div class="modal fade bs-example-modal-lg" id="vehicle_model" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"
                        id="exampleModalLabel">Link Vehicle</h4>
                    <!--Link Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3"
                                   class="col-sm-3 control-label">Driver </label>
                            <div class="col-sm-7">
                                <?php
                                echo form_dropdown('vehicleID', $vehicle, '', 'class="form-control select2" id="vehicleID" onchange=""  required');
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">Close </button>
                    <button type="button" class="btn btn-primary"
                            onclick="fetch_vehicle_detail()">Add Vehicle </button>
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
                                echo form_dropdown('employee_id', $jm_arr, '', 'class="form-control select2" id="employee_id"  required'); ?>
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

    <div class="modal fade" id="tourType_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Add New Tour Type</h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Tour Type</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="tourType_description"
                                           name="tourType_description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="add_new_tour_type()">Save</button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <script>
        var jpnumber;
        var EIdNo;
        var VehNo;
        var currency_decimal;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/journeyplan/journey_plan_map_tour', '', 'Journey Plan')
            });

            EIdNo = null;
            VehNo = null;
            number_validation();

            $('.select2').select2();
            $("[rel=tooltip]").tooltip();
            $(".paymentmoad").hide();
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

            $('.pickuptimePicker').datetimepicker({
                useCurrent: false,
                format : 'HH:mm',
                widgetPositioning: {
                    vertical: 'bottom'
                }
            }).on('dp.change', function (ev) {

            });

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                jpnumber = p_id;
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
        });

        $('#add-tourType').click(function () {
            $('#tourType_description').val('');
            $('#tourType_model').modal({backdrop: 'static'});
        });

        function add_new_tour_type(){
            var tourType_description = $.trim($('#tourType_description').val());
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'tourType_description': tourType_description},
                url: '<?php echo site_url("Journeyplan/add_new_tour_type"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    var tourType = $('#tourType');
                    if (data[0] == 's') {
                        tourType.append('<option value="' + data[2] + '">' + tourType_description + '</option>');
                        tourType.val(data[2]);
                        $('#tourType_model').modal('hide');
                    }


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
        jpnumber = null;

        $('#journeyplanheader_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                noofpassengers: {validators: {notEmpty: {message: 'Number Of Passengers is required.'}}},
                departuredate: {validators: {notEmpty: {message: 'Departure Date is required.'}}},
                vehiclenumber: {validators: {notEmpty: {message: 'Vehicle number is required.'}}},
                drivername: {validators: {notEmpty: {message: 'Driver Name is required.'}}},
                tourType: {validators: {notEmpty: {message: 'Type of tour is required.'}}},
                guideName: {validators: {notEmpty: {message: 'Guide Name is required.'}}},
                journeymanager: {validators: {notEmpty: {message: 'Journey Manager is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'employeeID', 'value': EIdNo});
            data.push({'name': 'vehicleID', 'value': VehNo});
            data.push({'name': 'vehicle_Num', 'value': $('#vehicleID option:selected').text()});
            data.push({'name': 'employee_det', 'value': $('#employee_id option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Journeyplan/save_journeyplan_header_map_tour'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        jpnumber = data[2];
                        $('#jpmasterid').val(jpnumber);
                        get_jp_passenger_details(jpnumber);
                        get_jp_tour_price(jpnumber);
                        passanger_Detial_item(jpnumber);
                        get_jp_additional_charges(jpnumber);
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
            $('#employeeName').val('').trigger('input');
            $('#employeeName').prop('readonly', false);
            EIdNo = null;
        }

        function clearVehicle() {
            $('#vehicleID').val('').change();
            fetch_vehicaledetails("");
            $('#vehiclenumber').val('').trigger('input');
            $('#vehiclenumber').prop('readonly', false);
            VehNo = null;
        }
        function link_Vehicle_model() {
            $('#vehicleID').val('').change();
            $('#vehicle_model').modal('show');
        }

        function fetch_vehicle_detail() {
            var vehicle_ID = $('#vehicleID').val();
            if (vehicle_ID == '') {
                myAlert('e', 'Select A Vehicle');
            } else {
                VehNo = vehicle_ID;
                fetch_vehicaledetails(VehNo);
                var vehicleNumber = $("#vehicleID option:selected").text();
                $('#vehiclenumber').val($.trim(vehicleNumber)).trigger('input');
                $('#vehiclenumber').prop('readonly', true);
                $('#vehicle_model').modal('hide');
            }
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
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'employeeid': employee_id},
                    url: "<?php echo site_url('Journeyplan/fetch_employee_details'); ?>",
                    success: function (data) {
                        $("#jmphonenumber").val(data['EcTel']);
                        $("#jmphonenumbermob").val(data['EcMobile']);
                    }
                });
            }
        }

        function fetch_vehicaledetails(vehicalemasterid) {
            $('#ivmsnumber').html('<input id="ivmsNumber_add" name="ivmsNumber_add">');
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

                            $("#changeImg").attr("src",data['vehicaleimagenew']);
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
                            $('#jpmasterid').val(data['journeyPlanMasterID']);
                            $('#noofpassengers').val(data['noOfPassengers']);
                            $('#jpnumberedit').val(data['documentCode']);
                            $('#departuredate').val(data['departureDateconverted']);
                            $('#drivername').val(data['driverID']).change();
                            $('#reasonnightdriving').val(data['reasonForNightDriving']);
                            $('#commentfordrivers').val(data['commentsForDriver']);
                            $('#offlinetrackingnumber').val(data['offlineTrackingRefNo']);

                            $('#tourType').val(data['journeyTypeID']).change();
                            $('#guideName').val(data['guideName']);
                            $('#guideNumber').val(data['guidePhoneNumber']);
                            $('#guestName').val(data['guestName']);
                            $('#villaHost').val(data['agentName']);
                            $('#roomNo').val(data['roomNo']);
                            $('#pickupTime').val(data['depatureTime']);
                            $('#transactionCurrencyID').val(data['transactionCurrrencyID']).change();

                            if(data['confirmedYN'] == 1)
                            {
                                $('#confirmbtn').addClass('hide');
                                $('#confirmbtn').addClass('hide');
                            }

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

                            VehNo = data['vehicleID'];
                            $('#vehicleID').val(data['vehicleID']).change();

                            if(data['vehicleID'] == '' || data['vehicleID'] == 0){
                                $('#vehiclenumber').val(data['vehicleNumber']);
                                $('#ivmsnumber').html('<input id="ivmsNumber_add" name="ivmsNumber_add">');
                                $('#ivmsNumber_add').val(data['ivmsNumber']);
                            } else {
                                $('#vehiclenumber').val(data['vehicleNumber']);
                                $('#vehiclenumber').prop('readonly', true);
                                $('#ivmsnumber').html('......');
                                fetch_vehicaledetails(data['vehicleID']);

                            }

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
                            passanger_Detial_item(jpnumber);
                            get_jp_tour_price(jpnumber);
                            get_jp_additional_charges(jpnumber);
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
                url: "<?php echo site_url('Journeyplan/load_detail_view'); ?>",
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
        function get_jp_tour_price(jpnumber) { //jp details
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'jpnumber': jpnumber},
                url: "<?php echo site_url('Journeyplan/load_jp_tour_price'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#tour_price_details').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function get_jp_additional_charges(jpnumber) { //jp details
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'jpnumber': jpnumber},
                url: "<?php echo site_url('Journeyplan/load_jp_additional_charges'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#additional_charge_details').html(data);
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
                url: "<?php echo site_url('Journeyplan/fetch_passanger_details'); ?>",
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
                        fetchPage('system/journeyplan/journey_plan_map_tour',jpnumber,'Journey Plan');
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
                                    fetchPage('system/journeyplan/journey_plan_map_tour',jpnumber,'Journey Plan');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }
    </script>
<?php
