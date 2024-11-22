<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('fleet_helper');

$date_format_policy = date_format_policy();
$getAll_vehicle = load_vehicles();
$getAll_vehicleColor = load_vehicle_color();
$getAll_vehiclebrand = load_vehicle_brand();
$getAll_vehicleModel = load_vehicle_model();
$getAll_fuelType = load_fuel_type();
$Brand_arr = load_vehicle_brand();
$Model_arr = array('' => 'Select Model');
$assets = load_all_assets();
$supplier_arr = all_supplier_drop();
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
    <br>
    <br>
<?php echo form_open('', 'role="form" id="AddVehicleForm"'); ?>
    <input type="hidden" name="vehicleMasterID" id="vehicleMasterID">
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>
                 <!--   --><?php echo $this->lang->line('fleet_asset_details'); ?><!--Asset Details--></h2>
            </header>

            <div class="row" style="margin-top: 10px;">

                <div class="form-group col-sm-2">
                    <label class="title">
                        <?php echo $this->lang->line('fleet_Brand'); ?><!--Brand--><label>
                </div>


                <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field">
                            <div class="input-group">
                                <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add-vehicalebrand"
                                                style="height: 27px; padding: 2px 10px;">
                                            <i class="fa fa-plus" style="font-size: 11px"></i>
                                        </button>
                                </span>
                                <?php echo form_dropdown('vehicalebrand', load_vehicle_brand(), '', 'class="form-control select2" id="vehicalebrand" onchange = fetch_vehicale_model(this.value)'); ?>
                                <?php /*echo form_dropdown('vehicalemodel', $Model_arr, '', 'class="form-control select2" id="vehicalemodel"'); */ ?>

                            </span>
                </div>
                <span class="input-req-inner"></span></span>
            </div>

            <div class="form-group col-sm-2">
                <label class="title">
                  <!--  --><?php echo $this->lang->line('fleet_capacity'); ?><!--Capacity--></label>
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                            <input type="text" name="EngineCapacity"
                                   placeholder="<?php echo $this->lang->line('fleet_vehicle_engine'); ?>"
                                   value="" id="EngineCapacity" class="form-control">
                              <span class="input-req-inner"></span></span>
            </div>


        </div>


        <div class="row" style="margin-top: 10px;">

            <div class="form-group col-sm-2">
                <label class="title">
                    <?php echo $this->lang->line('fleet_Model'); ?></label>
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field">
                            <div class="input-group">
                                <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add-vehicalemodel"
                                                style="height: 27px; padding: 2px 10px;">
                                            <i class="fa fa-plus" style="font-size: 11px"></i>
                                        </button>
                                </span>
                                <div id="div_loadmodel">
                                    <?php echo form_dropdown('vehicalemodel', $Model_arr, '', 'class="form-control select2" id="vehicalemodel"'); ?>
                                </div>
                            </span>
            </div>
            <span class="input-req-inner"></span></span>
        </div>
        <div class="form-group col-sm-2">
            <label class="title">
               <!-- --><?php echo $this->lang->line('fleet_expected_km_per_hrs'); ?><!--Expected KM/hrs--><label>
        </div>
        <div class="form-group col-sm-4">

            <input type="text" name="Speed"
                   placeholder="Expected KM/hrs"
                   value="" id="Speed" class="form-control">

        </div>

    </div>


    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class="title">
               <!-- --><?php echo $this->lang->line('fleet_body_type'); ?> <!--Body Type--></label>
        </div>

        <div class="form-group col-sm-4">
                                 <span class="input-req" title="Required Field">
                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('fuelBodyID', $getAll_vehicle, '',
                                                    'class="form-control select2" id="fuelBodyID" '); ?>
                                </span></span>
            <span class="input-req-inner"></span></span>
        </div>
        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_product_no'); ?><!--Product Number--></label>

        </div>
        <div class="form-group col-sm-4">
                             <span class="input-req" title="Required Field">
                                    <span class="input-req" title="Required Field"><input class="form-control"
                                                                                          id="VehicleNo"
                                                                                          name="VehicleNo"
                                                                                          placeholder="Product Number"
                                        >
                                        </span></span>

            <span class="input-req-inner"></span></span>
        </div>
    </div>


    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_color'); ?><!--color :--></label>
        </div>
        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('colourID', $getAll_vehicleColor, '',
                                                    'class="form-control select2" id="colourID" '); ?>
                                <span class="input-req-inner"></span></span>
        </div>

        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_registerDate'); ?><!--Date of Birth--></label>
        </div>


        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="registerDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           id="registerDate" class="form-control">
                        </div>
                      <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_fuel'); ?><!--Fuel Type :--></label>
        </div>

        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('fuelTypeID', $getAll_fuelType, '',
                                                    'class="form-control select2" id="fuelTypeID" '); ?>
                                <span class="input-req-inner"></span></span>
        </div>

        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_insuranceDate'); ?><!--Date of Birth--></label>
        </div>
        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic_insurance">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="insuranceDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           id="insuranceDate" class="form-control">
                        </div>
                      <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>
    </div>


    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_transmission'); ?><!-- Transmission Description--></label>
        </div>

        <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field">
                            <!--<select id="transmisson_deception" class="form-control select2"
                                    name="transmisson_deception"
                                    data-placeholder="<?php /*echo $this->lang->line('fleet_vehicle_transmission'); */?>">
                                <option value=""></option>
                                <option value="Automatic" selected>Automatic</option>
                                <option value="Manual">Manual</option>-->
                                    <?php echo form_dropdown('transmisson_type', array('1' => 'Automatic', '2' => 'Manual'), '', 'class="form-control select2" id="transmisson_type"'); ?>

                                     <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>


        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_licenceDate'); ?><!--Date of Birth--></label>
        </div>
        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic_license">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="lisenceDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           id="lisenceDate" class="form-control">
                        </div>
                      <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class="title">
                <?php echo $this->lang->line('fleet_vehicle_year'); ?><!--Manufacture Year--></label>
        </div>
        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                            <input type="text" name="yearManu"
                                   placeholder="<?php echo $this->lang->line('fleet_vehicle_year'); ?>"
                                   value="" id="yearManu" class="form-control">
                             <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>

        <div class="form-group col-sm-2">
            <label class=" title"
                   for="status"><?php echo $this->lang->line('common_type'); ?> </label>
        </div>

        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">

                                <?php echo form_dropdown('vehicle_type', array('1' => 'Own', '2' => 'Third Party'), '', 'class="form-control select2" id="vehicle_type"'); ?>
                                  <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>
    </div>


    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class=" title"
                   for="status"><?php echo $this->lang->line('fleet_initial_usage'); ?><!--Initial Usage--> </label>
        </div>

        <div class="form-group col-sm-4">
              <span class="input-req" title="Required Field">
            <input type="text" name="initialmileage" placeholder="Initial Usage" id="initialmileage" class="form-control">
        <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>


        <div class="form-group col-sm-2">
            <label class=" title" for="ivmsnumber">Chessi / Part No</label>
        </div>

        <div class="form-group col-sm-4">

                             <input type="text" name="chessino"
                                    placeholder="Chessi / Part No"
                                    value="" id="chessino" class="form-control">

        </div>
    </div>


    <div class="row" style="margin-top: 10px;">

        <div class="form-group col-sm-2">
            <label class=" title" for="ivmsnumber"><?php echo $this->lang->line('fleet_fixed_asset'); ?><!--Fixed Asset--></label>
        </div>
        <div class="form-group col-sm-4">
            <?php echo form_dropdown('fixedasset', $assets, '', 'class="form-control select2" id="fixedasset"'); ?>

        </div>

       <div class="hide" id="linksupplier">
        <div class="form-group col-sm-2">
            <label class=" title"
                   for="supplier "><?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> </label>
        </div>
        <div class="form-group col-sm-4">
         <span class="input-req" title="Required Field">
            <?php echo form_dropdown('supplier', $supplier_arr, '', 'class="form-control select2" id="supplier"'); ?>
        <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>
        </div>

    </div>


    <div class="row" style="margin-top: 10px;">

        <div class="form-group col-sm-2">
            <label class=" title" for="ivmsnumber">IVMS / IOT No</label>
        </div>

        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                             <input type="text" name="ivmsno"
                                    placeholder="IVMS / IOT No"
                                    value="" id="ivmsno" class="form-control">
                                  <span class="input-req-inner" style="z-index: 100"></span></span>
        </div>

        <div class="form-group col-sm-2">
            <label class=" title"
                   for="status"><?php echo $this->lang->line('common_description'); ?></label>
        </div>

        <div class="form-group col-sm-4">

                            <textarea class="form-control"
                                      id="vehDescription"
                                      name="vehDescription"
                                      rows="2"></textarea>

        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-2">
            <label class=" title" for="status"><?php echo $this->lang->line('fleet_Status'); ?></label>
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


    <br>

        </div>
    </div>

    <div class="pull-right" id="statusbtns">
        <button type="submit" class="btn btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_save'); ?></button><!--Save-->
        <!--<button type="button" class="btn btn-primary" onclick="confirmedvehicale();" id="statussubbtn">Confirmed</button>--><!--Submit-->
    </div>
    </div>
    </div>
    </form>


    <div class="modal fade bs-example-modal-lg" id="brand_model_new" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"
                        id="exampleModalLabel">Add Brand</h4>
                    <!--Link Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3"
                                   class="col-sm-3 control-label">Brand </label>
                            <div class="col-sm-7">
                                <?php
                                echo form_dropdown('vehicale_brand', $Brand_arr, '', 'class="form-control select2" id="vehicale_brand" '); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">Close
                    </button>
                    <button type="button" class="btn btn-primary"
                            onclick="fetch_vehicale_detail()">Add Vehicle Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="vehicle_model_new" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"
                        id="exampleModalLabel">Add Model</h4>
                    <!--Link Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3"
                                   class="col-sm-3 control-label">Model</label>
                            <div class="col-sm-7">
                                <?php
                                echo form_dropdown('vehicle_model', $getAll_vehicleModel, '', 'class="form-control select2" id="vehicle_model"'); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">Close
                    </button>
                    <button type="button" class="btn btn-primary"
                            onclick="fetch_Model_detail()">Add Vehicle Model
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="vehical_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><?php echo $this->lang->line('fleet_add_new_model')?><!--Add New Model--></h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('fleet_Model')?></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="add-vehicalemodel_txt"
                                           name="vehicalemodel_txt">
                                    <input type="hidden" class="form-control" id="vehicalebrand_add"
                                           name="vehicalebrand_add">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" id="vehicale-add"><?php echo $this->lang->line('common_save')?></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="vehical_brand_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><?php echo $this->lang->line('fleet_add_new_model')?><!--Add New Brand--></h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('fleet_Brand')?></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="add-vehicalebrand_txt"
                                           name="vehicalebrand_txt">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" id="vehicale-add_brand"><?php echo $this->lang->line('common_save')?></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // $('#save_btn').html('Save');
        var vehicleMasterID;
        var vehicalebrandid;
        var vehiclemodelid;

        $(document).ready(function () {
            vehicalebrandid = null;
            vehiclemodelid = null;
            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '', 'Vehicle Master');
            });

            $('.select2').select2();

            $('.extraColumnsgreen input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                radioClass: 'iradio_square_relative-green',
                increaseArea: '20%'
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
            Inputmask().mask(document.querySelectorAll("input"));

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            vehicleMasterID = p_id;
            load_vehicle();


            $('#AddVehicleForm').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    fuelBodyID: {validators: {notEmpty: {message: 'Vehicle Body Type is Required.'}}},
                    VehicleNo: {validators: {notEmpty: {message: 'Vehicle No is Required.'}}},
                    initialmileage: {validators: {notEmpty: {message: 'Initial Mileage is Required.'}}},
                    // vehicalemodel: {validators: {notEmpty: {message: 'Vehicle Model is Required.'}}},
                    vehicalebrand: {validators: {notEmpty: {message: 'Vehicle Brand is Required.'}}},
                    colourID: {validators: {notEmpty: {message: 'Vehicle Color is Required.'}}},
                    fuelTypeID: {validators: {notEmpty: {message: 'Fuel Type is Required.'}}},
                    transmisson_deception: {validators: {notEmpty: {message: 'Transmission Type is Required.'}}},
                    yearManu: {validators: {notEmpty: {message: 'Manufacture Year is Required.'}}},
                    EngineCapacity: {validators: {notEmpty: {message: 'Engine Capacity is Required.'}}},
                    registerDate: {validators: {notEmpty: {message: 'Vehicle Registered Date is Required.'}}},
                    insuranceDate: {validators: {notEmpty: {message: 'Insurance Date is Required.'}}},
                    lisenceDate: {validators: {notEmpty: {message: 'Lisence Date is Required.'}}},
                    ivmsno: {validators: {notEmpty: {message: 'IVMS No is Required.'}}},
                    //fixedasset: {validators: {notEmpty: {message: 'Fixed Asset is Required.'}}},
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $('.saveBtn').prop('disabled', false);
                data.push({'name': 'vehicalebranddescription', 'value': $('#vehicalebrand option:selected').text()});
                data.push({'name': 'vehicalemodeldescription', 'value': $('#vehicalemodel option:selected').text()});
                data.push({'name': 'transmissontypedescription', 'value': $('#transmisson_type option:selected').text()});
                data.push({'name': 'vehicleMasterID', 'value': vehicleMasterID});
                $.ajax({
                    type: 'post',
                    url: "<?php echo site_url('Fleet/Save_vehicle') ?>",
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            vehicleMasterID = data[2];
                            $('#vehicleMasterID').val(vehicleMasterID);
                            fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '', 'Fleet');
                        }

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
                $('#AddVehicleForm').bootstrapValidator('revalidateField', 'registerDate');

            });

            $('.datepic_insurance').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#AddVehicleForm').bootstrapValidator('revalidateField', 'insuranceDate');
            });


            $('.datepic_license').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

                $('#AddVehicleForm').bootstrapValidator('revalidateField', 'lisenceDate');


            });

        });

        function load_vehicle() {
            if (vehicleMasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'vehicleMasterID': vehicleMasterID},
                    url: "<?php echo site_url('Fleet/load_vehicle'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            vehicleMasterID = data['vehicleMasterID'];
                            $('#vehicleMasterID').val(data['vehicleMasterID']).change();
                            setTimeout(function () {
                                $('#vehicalemodel').val(data['model_id']).change();
                            }, 500);
                            $('#EngineCapacity').val(data['engineCapacity']);
                            $('#vehicalebrand').val(data['brand_id']).change();
                            $('#Speed').val(data['expKMperLiter']);
                            $('#fuelBodyID').val(data['bodyType']).change();
                            $('#VehicleNo').val(data['VehicleNo']);
                            $('#colourID').val(data['colour']).change();
                            $('#registerDate').val(data['registerDate']);
                            $('#fuelTypeID').val(data['fuelTypeID']).change();
                            $('#insuranceDate').val(data['insuranceDate']);
                            $('#lisenceDate').val(data['lisenceDate']);
                            $('#yearManu').val(data['manufacturedYear']);
                            $('#supplier').val(data['thirdPartySupplierID']).change();
                            $('#vehicle_type').val(data['vehicle_type']).change();
                            if (data['vehicle_type'] == 1) {
                               $('#linksupplier').addClass('hide');
                            } else {
                                $('#linksupplier').removeClass('hide');
                            }
                            $('#vehDescription').val(data['vehDescription']);
                            $('#fixedasset').val(data['faID']).change();
                            $('#ivmsno').val(data['ivmsNo']);
                            $('#transmisson_type').val(data['transmisson']).change();
                            $('#vehicale_brand').val(data['brand_id']).change();
                            $('#vehicle_model').val(data['model_id']).change();

                            $('#initialmileage').val(data['initialMilage']);
                            $('#chessino').val(data['chessiNo']);
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

        function link_vehicale_brand() {
            /*$('#employee_id').val('').change();*/
            $('#brand_model_new').modal('show');
        }

        function link_vehicleModel() {
            /*$('#employee_id').val('').change();*/
            $('#vehicle_model_new').modal('show');
        }

        function clear_vehicale() {
            $('#vehicale_brand').val('').change();
            $('#vehicalebrand').val('').trigger('input');
            $('#vehicalebrand').prop('readonly', false);
        }

        function clear_vehicleModel() {
            $('#vehicle_model').val('').change();
            $('#vehicleModel').val('').trigger('input');
            $('#vehicleModel').prop('readonly', false);
        }

        function fetch_vehicale_detail() {
            var vehicalemodel_id = $('#vehicale_brand').val();
            if (vehicalemodel_id == '') {
                //swal("", "Select An Employee", "error");
                myAlert('e', 'Select A Brand');
            } else {
                vehicalebrandid = vehicalemodel_id;
                var vehicalemodel = $("#vehicale_brand option:selected").text();

                $('#vehicalebrand').val($.trim(vehicalemodel)).trigger('input');
                $('#vehicalebrand').prop('readonly', true);
                $('#brand_model_new').modal('hide');
            }
        }

        function fetch_Model_detail() {
            var vehicalemodel_id = $('#vehicle_model').val();
            if (vehicalemodel_id == '') {
                //swal("", "Select An Employee", "error");
                myAlert('e', 'Select A Model');
            } else {
                vehiclemodelid = vehicalemodel_id;
                var vehicalemodel = $("#vehicle_model option:selected").text();

                $('#vehicleModel').val($.trim(vehicalemodel)).trigger('input');
                $('#vehicleModel').prop('readonly', true);
                $('#vehicle_model_new').modal('hide');
            }
        }

        function fetch_vehicale_model(VehicleBrand) {


            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'VehicleBrand': VehicleBrand},
                url: "<?php echo site_url('Fleet/fetch_vehical_model_all'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loadmodel').html(data);
                    $('.select2').select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        $('#add-vehicalemodel').click(function () {
            var vehicalebrand = $('#vehicalebrand').val();
            $('#vehicalemodel_txt').val('');
            $('#vehicalebrand_add').val(vehicalebrand);
            $('#vehical_model').modal({backdrop: 'static'});
        });

        $('#add-vehicalebrand').click(function () {
            $('#vehicalebrand_txt').val('');
            $('#vehical_brand_model').modal({backdrop: 'static'});
        });

        $('#vehicale-add').click(function (e) {
            e.preventDefault();
            var brand = $.trim($('#vehicalebrand_add').val());
            var Model = $.trim($('#add-vehicalemodel_txt').val());
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'brand': brand, 'Model': Model},
                url: '<?php echo site_url("Fleet/add_new_model"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    var vegicalemodel = $('#vehicalemodel');
                    if (data[0] == 's') {
                        vegicalemodel.append('<option value="' + data[2] + '">' + Model + '</option>');
                        vegicalemodel.val(data[2]);
                        $('#vehical_model').modal('hide');
                    }


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#vehicale-add_brand').click(function (e) {
            e.preventDefault();
            var brand = $.trim($('#add-vehicalebrand_txt').val());
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'brand': brand},
                url: '<?php echo site_url("Fleet/add_new_model_brand"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    var vegicalemodel = $('#vehicalebrand');
                    if (data[0] == 's') {
                        vegicalemodel.append('<option value="' + data[2] + '">' + brand + '</option>');
                        vegicalemodel.val(data[2]);
                        $('#vehical_brand_model').modal('hide');
                    }


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
        $("#vehicle_type").change(function () {
            if (this.value == 1) {
                $('#linksupplier').addClass('hide');
            } else {
                $('#linksupplier').removeClass('hide');
            }
        });

        function confirmedvehicale() {
            if (vehicleMasterID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want confirm this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'vehicleMasterID': vehicleMasterID},
                            url: "<?php echo site_url('Fleet/vehicale_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {

                                stopLoad();
                                if (data['error'] == 1) {
                                    myAlert('e', data['message']);
                                } else if (data['error'] == 2) {
                                    myAlert('w', data['message']);
                                }
                                else {
                                    myAlert('s', data['message']);
                                    fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '', 'Fleet');
                                    refreshNotifications(true);
                                }

                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            };
        }


    </script>

