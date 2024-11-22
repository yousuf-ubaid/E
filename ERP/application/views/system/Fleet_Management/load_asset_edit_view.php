<?php
$title = $this->lang->line('');
echo head_page($title, false);
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$this->load->helper('fleet_helper');

$date_format_policy = date_format_policy();
$getAll_vehicle = load_vehicles();
$getAll_vehicleColor = load_vehicle_color();
$getAll_vehiclebrand = load_vehicle_brand();
$getAll_vehicleModel = load_vehicle_model();
$getAll_fuelType = load_fuel_type();
$getAll_sub = load_asset_sub();
$getAll_main = load_asset_main();
$fetch_all_location = fetch_all_location();
$fetch_asset_status = fetch_asset_status();
$Brand_arr = load_vehicle_brand();
$Main_arr = array('' => 'Select Main Category');

$Model_arr = array('' => 'Select Sub Category');
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

<?php echo form_open('', 'role="form" id="AddVehicleForm"'); ?>

<input type="hidden" name="vehicleMasterID" id="vehicleMasterID">
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>
                Asset Details</h2>
        </header>

        <div class="row" style="margin-top: 10px;">
            <!-- <input type="hidden" name="asset_type" id="asset_type" value ="1"> -->

            <div class="form-group col-sm-2">
                <label class="title">
                    <?php echo $this->lang->line('fleet_serial_no'); ?><!--Serial Number--></label>

            </div>
            <div class="form-group col-sm-4">

                <span class="input-req" title="Required Field"><input class="form-control"
                        id="SerialNo"
                        name="SerialNo"
                        placeholder="Serial Number">
                </span>
                <span class="input-req-inner"></span>
            </div>
            <div class="form-group col-sm-2">
                <label class=" title"
                    for="status"><?php echo $this->lang->line('common_type'); ?> </label>
            </div>

            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('asset_type', array('' => 'Select Type', '1' => 'Asset', '2' => 'Component'), '', 'class="form-control select2 "  id="asset_type" '); ?>
                </span>
                <span class="input-req-inner"></span>
            </div>

        </div>

        <div class="row" style="margin-top: 10px;">

            <div class="form-group col-sm-2">
                <label class="title">
                    <?php echo $this->lang->line('_fleet_main_category'); ?><!--Main Category--><label>
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
                        <?php echo form_dropdown('vehicalebrand', $Main_arr, '', 'class="form-control select2"  id="vehicalebrand"  '); ?> <!-- onchange="fetch_sub_all(this.value)" -->
                        <?php /*echo form_dropdown('vehicalemodel', $Model_arr, '', 'class="form-control select2" id="vehicalemodel"'); */ ?>
                    </div>
                </span>

                <span class="input-req-inner"></span>
            </div>


            <div class="form-group col-sm-2">
                <label class="title">
                    <?php echo $this->lang->line('_fleet_sub_category'); ?></label>
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


                        <!-- <div id="div_loadmodel"> -->
                        <?php echo form_dropdown('vehicalemodel', $Model_arr, '', 'class="form-control select2" id="vehicalemodel" '); ?><!-- onclick="fetch_Model_detail()" -->
                        <!-- </div> -->
                    </div>
                </span>
                <span class="input-req-inner"></span>
            </div>

        </div>



        <div class="row" style="margin-top: 10px;">


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
                    <span class="input-req-inner" style="z-index: 100"></span>
                </span>

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
                    <span class="input-req-inner" style="z-index: 100"></span>
                </span>
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
                    <span class="input-req-inner" style="z-index: 100"></span>
                </span>
            </div>
        </div>





        <div class="row" style="margin-top: 10px;">

            <div class="form-group col-sm-2">
                <label class=" title" for="ivmsnumber"><?php echo $this->lang->line('fleet_fixed_astmap'); ?><!--Fixed Asset--></label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">

                    <?php echo form_dropdown('fixedasset', $assets, '', 'class="form-control select2" id="fixedasset"'); ?>
                    <span class="input-req-inner" style="z-index: 100"></span>
                </span>
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
            <div class="form-group col-sm-2">
                <label class=" title">
                    <?php echo $this->lang->line('asset_location'); ?><!--Asset Location--></label>
            </div>
            <div class="form-group col-sm-4">

                <?php echo form_dropdown('location', $fetch_all_location, '', 'class="form-control select2" id ="location" onchange="usersTable()" '); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">

            <div class="form-group col-sm-2">
                <label class=" title">
                    <?php echo $this->lang->line('asset_status'); ?><!--Asset Status--></label>
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('assetstatus', $fetch_asset_status, '', 'class="form-control select2" id ="assetstatus" onchange="usersTable()" '); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class=" title"><?php echo $this->lang->line('common_description'); ?></label>
            </div>

            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <textarea class="form-control"
                        id="vehDescription"
                        name="vehDescription"
                        rows="2"></textarea>
                </span>
                <span class="input-req-inner"></span>

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
                    id="exampleModalLabel">Add Main Category</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label
                            class="col-sm-3 control-label">Main Category </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('vehicale_brand', $Main_arr, '', 'class="form-control select2" id="vehicale_brand" '); ?>
                        </div>
                        <input type="hidden" id="selected_asset_type_id" name="selected_asset_type_id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary"
                    onclick="fetch_vehicale_detail()">Add Main Category
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
                    id="exampleModalLabel">Add Main Category</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label
                            class="col-sm-3 control-label">Sub Category</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('vehicle_model', $Model_arr, '', 'class="form-control select2" id="vehicle_model"'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary"
                    onclick="fetch_Model_detail()">Add Sub Catgeory
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
                <h3 class="modal-title"><?php echo $this->lang->line('fleet_add_new_sub') ?><!--Add New Model--></h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class=" title"
                                for="status"><?php echo $this->lang->line('common_type'); ?> </label>
                        </div>

                        <div class="form-group col-sm-6">


                            <?php echo form_dropdown('sub_asset_type_id', array('1' => 'Asset', '2' => 'Component'), '', 'class="form-control select2" id="sub_asset_type_id"'); ?>

                        </div>


                        <div class="form-group col-sm-4">
                            <label class="title">
                                <?php echo $this->lang->line('_fleet_main_category'); ?><!--Main Category--><label>
                        </div>


                        <div class="form-group col-sm-6">
                            <!-- <span class="input-req" title="Required Field">
                                <div class="input-group"> -->
                            <!-- <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button" id="add-vehicalebrand"
                                                                    style="height: 27px; padding: 2px 10px;">
                                                                <i class="fa fa-plus" style="font-size: 11px"></i>
                                                            </button>
                                                    </span> -->
                            <?php echo form_dropdown('modvehicalebrand', load_asset_main(), '', 'class="form-control select2" id="modvehicalebrand"'); ?>
                            <!-- </span>
                        </div> -->
                        </div>
                    </div>
                    <!-- <div class="row"> -->


                    <div class="form-group col-sm-4">
                        <label class="title"><?php echo $this->lang->line('_fleet_sub_category') ?></label>
                    </div>
                    <div class="form-group col-sm-6">

                        <input type="text" class="form-control" id="add-vehicalemodel_txt"
                            name="vehicalemodel_txt">
                        <input type="hidden" class="form-control" id="vehicalebrand_add"
                            name="vehicalebrand_add">
                        <!-- <input type="hidden" id="sub_asset_type_id" name="sub_asset_type_id"> -->
                    </div>

                    <!-- </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="vehicale-add"><?php echo $this->lang->line('common_save') ?></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?></button>
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
                <h3 class="modal-title"><?php echo $this->lang->line('fleet_add_new_main') ?><!--Add New Brand--></h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class=" title"><?php echo $this->lang->line('common_type'); ?> </label>
                        </div>

                        <div class="form-group col-sm-6">


                            <?php echo form_dropdown('main_asset_type_id', array('1' => 'Asset', '2' => 'Component'), '', 'class="form-control select2" id="main_asset_type_id"'); ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4 ">
                            <label class=" title"><?php echo $this->lang->line('_fleet_main_category') ?></label>
                            <!-- <input type="hidden" id="main_asset_type_id" name="main_asset_type_id"> -->
                        </div>
                        <div class="form-group col-sm-6">
                            <input type="text" class="form-control" id="add-vehicalebrand_txt"
                                name="vehicalebrand_txt">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="vehicale-add_brand"><?php echo $this->lang->line('common_save') ?></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?></button>
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
    $(document).ready(function() {
        vehicalebrandid = null;
        vehiclemodelid = null;
        $('.headerclose').click(function() {
            fetchPage('system/Fleet_Management/maintenance_asset', '', 'Asset Master');
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
        load_assets();

        $('#AddVehicleForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                SerialNo: {
                    validators: {
                        notEmpty: {
                            message: 'Serial No is Required.'
                        }
                    }
                },
                asset_type: {
                    validators: {
                        notEmpty: {
                            message: 'Type is Required.'
                        }
                    }
                },
                vehicalebrand: {
                    validators: {
                        notEmpty: {
                            message: 'Main Category is Required.'
                        }
                    }
                },
                vehicalemodel: {
                    validators: {
                        notEmpty: {
                            message: 'Sub Category is Required.'
                        }
                    }
                },
                registerDate: {
                    validators: {
                        notEmpty: {
                            message: 'Registered Date is Required.'
                        }
                    }
                },
                insuranceDate: {
                    validators: {
                        notEmpty: {
                            message: 'Insurance Date is Required.'
                        }
                    }
                },
                yearManu: {
                    validators: {
                        notEmpty: {
                            message: 'Manufacture Year is Required.'
                        }
                    }
                },
                vehicle_type: {
                    validators: {
                        notEmpty: {
                            message: 'Type is Required.'
                        }
                    }
                },
                fixedasset: {
                    validators: {
                        notEmpty: {
                            message: 'Fixed Asset is Required.'
                        }
                    }
                },
                vehDescription: {
                    validators: {
                        notEmpty: {
                            message: 'Description is Required.'
                        }
                    }
                },
                // transmisson_deception: {validators: {notEmpty: {message: 'Transmission Type is Required.'}}},
                // // lisenceDate: {validators: {notEmpty: {message: 'Lisence Date is Required.'}}},
                // ivmsno: {validators: {notEmpty: {message: 'IVMS No is Required.'}}},
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $('.saveBtn').prop('disabled', false);
            data.push({
                'name': 'vehicalebranddescription',
                'value': $('#vehicalebrand option:selected').text()
            });
            data.push({
                'name': 'vehicalemodeldescription',
                'value': $('#vehicalemodel option:selected').text()
            });
            // data.push({'name': 'transmissontypedescription', 'value': $('#transmisson_type option:selected').text()});
            data.push({
                'name': 'vehicleMasterID',
                'value': vehicleMasterID
            });
            $.ajax({
                type: 'post',
                url: "<?php echo site_url('Fleet/Save_Asset') ?>",
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        vehicleMasterID = data[2];
                        $('#vehicleMasterID').val(vehicleMasterID);
                        fetchPage('system/Fleet_Management/maintenance_asset', '', 'Asset Master');
                    }

                },
                error: function() {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {
            $('#AddVehicleForm').bootstrapValidator('revalidateField', 'registerDate');

        });

        $('.datepic_insurance').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {
            $('#AddVehicleForm').bootstrapValidator('revalidateField', 'insuranceDate');
        });


        $('.datepic_license').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {

            $('#AddVehicleForm').bootstrapValidator('revalidateField', 'lisenceDate');


        });

        $('#sub_asset_type_id').on('change', function() {
            var subAssetTypeId = $(this).val();
            fetchMainCategory(subAssetTypeId);
        });

        $('#asset_type').on('change', function() {
            var AssetTypeId = $(this).val();
            fetchMainCategory2(AssetTypeId);
        });
        $('#vehicalebrand').on('change', function() {
            var MainCatId = $(this).val();
            fetchSubCategory2(MainCatId);
        });

    });

    function fetchMainCategory(subAssetTypeId) {
        $.ajax({
            url: "<?php echo site_url('Fleet/fetch_main_category') ?>",
            type: 'POST',
            data: {
                sub_asset_type_id: subAssetTypeId
            },
            success: function(response) {
                $('#modvehicalebrand').html(response);
            },
            error: function() {
                alert('Error fetching main category data.');
            }
        });
    }

    function fetchMainCategory2(AssetTypeId) {
        $.ajax({
            url: "<?php echo site_url('Fleet/fetch_main_category2') ?>",
            type: 'POST',
            data: {
                asset_type: AssetTypeId
            },
            success: function(response) {
                $('#vehicalebrand').html(response);
            },
            error: function() {
                alert('Error fetching main category data.');
            }
        });
    }

    function fetchSubCategory2(MainCatId) {
        $.ajax({
            url: "<?php echo site_url('Fleet/fetch_sub_all') ?>",
            type: 'POST',
            data: {
                'VehicleBrand': MainCatId
            },
            success: function(response) {
                $('#vehicalemodel').html(response);
            },
            error: function() {
                alert('Error fetching sub category data.');
            }
        });
    }



    function load_assets() {
        if (vehicleMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'vehicleMasterID': vehicleMasterID
                },
                url: "<?php echo site_url('Fleet/load_assets'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    if (!jQuery.isEmptyObject(data)) {
                        vehicleMasterID = data['vehicleMasterID'];

                        $('#vehicleMasterID').val(data['vehicleMasterID']).change();
                        // First, set the vehicalebrand

                        // Set vehicalebrand first with a timeout
                        setTimeout(function() {
                            $('#vehicalebrand').val(data['brand_id']).change();

                            // Set vehicalemodel after the brand has been set
                            setTimeout(function() {
                                $('#vehicalemodel').val(data['model_id']).change();
                            }, 500); // Delay for brand loading
                        }, 500); // Delay for model loading after brand

                        $('#asset_type').val(data['asset_type_id']).change();
                        // $('#vehicalebrand').val(data['brand_id']).change();

                        $('#SerialNo').val(data['assetSerialNo']);

                        $('#registerDate').val(data['registerDate']);

                        $('#insuranceDate').val(data['insuranceDate']);

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


                        // $('#vehicale_brand').val(data['brand_id']).change();
                        // $('#vehicle_model').val(data['model_id']).change();
                        $('#location').val(data['locationID']).change();
                        $('#assetstatus').val(data['assetStatus']).change();

                        setTimeout(function() {
                            if (data['isActive'] == 1) {
                                $('#active').iCheck('check');
                            } else if (data['isActive'] == 0) {
                                $('#inactive').iCheck('check');
                            }
                        }, 500);

                        $('#save_btn').html('<?php echo $this->lang->line('common_update'); ?>');
                        //  document.getElementById('nxtBtn').style.display = 'block';

                    }
                    stopLoad();
                    refreshNotifications(true);
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
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

    function fetch_sub_all(VehicleBrand) {


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'VehicleBrand': VehicleBrand
            },
            url: "<?php echo site_url('Fleet/fetch_sub_all'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $('#div_loadmodel').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#add-vehicalemodel').click(function() {
        $('#vehicalemodel_txt').val('');
        var vehicalebrand = $('#vehicalebrand').val();
        var asset_type_id = $('#asset_type').val();
        $('#sub_asset_type_id').val(asset_type_id).trigger('change');
        setTimeout(function() {
            $('#modvehicalebrand').val(vehicalebrand).trigger('change');
        }, 500);
        $('#vehicalebrand_add').val(vehicalebrand);
        $('#sub_asset_type_id').prop('disabled', true);
        $('#modvehicalebrand').prop('disabled', true);
        // var asset_type_id  = $('#asset_type_id').val();
        // $('#main_asset_type_id').val(asset_type_id);

        $('#vehical_model').modal({
            backdrop: 'static'
        });
    });

    $('#add-vehicalebrand').click(function() {
        $('#vehicalebrand_txt').val('');
        var asset_type_id = $('#asset_type').val();
        $('#main_asset_type_id').val(asset_type_id).trigger('change');
        $('#main_asset_type_id').prop('disabled', true);

        $('#vehical_brand_model').modal({
            backdrop: 'static'
        });
    });

    $('#vehicale-add').click(function(e) {
        e.preventDefault();
        var brand = $.trim($('#modvehicalebrand').val());
        var Model = $.trim($('#add-vehicalemodel_txt').val());
        var subtype = $.trim($('#sub_asset_type_id').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'brand': brand,
                'Model': Model,
                'type': subtype
            },
            url: '<?php echo site_url("Fleet/asset_add_new_model"); ?>',
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var vegicalemodel = $('#vehicalemodel');
                if (data[0] == 's') {
                    vegicalemodel.append('<option value="' + data[2] + '">' + Model + '</option>');
                    vegicalemodel.val(data[2]);
                    $('#vehical_model').modal('hide');
                }


            },
            error: function() {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    $('#vehicale-add_brand').click(function(e) {
        e.preventDefault();
        var brand = $.trim($('#add-vehicalebrand_txt').val());
        var type = $.trim($('#main_asset_type_id').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'brand': brand,
                'type': type
            },
            url: '<?php echo site_url("Fleet/asset_add_new_model_brand"); ?>',
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var vegicalemodel = $('#vehicalebrand');
                if (data[0] == 's') {
                    vegicalemodel.append('<option value="' + data[2] + '">' + brand + '</option>');
                    vegicalemodel.val(data[2]);
                    $('#vehical_brand_model').modal('hide');
                }


            },
            error: function() {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });
    $("#vehicle_type").change(function() {
        if (this.value == 1) {
            $('#linksupplier').addClass('hide');
        } else {
            $('#linksupplier').removeClass('hide');
        }
    });

    function confirmedvehicale() {
        if (vehicleMasterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    /*You want confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function() {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'vehicleMasterID': vehicleMasterID
                        },
                        url: "<?php echo site_url('Fleet/vehicale_confirmation'); ?>",
                        beforeSend: function() {
                            startLoad();
                        },
                        success: function(data) {

                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            } else {
                                myAlert('s', data['message']);
                                fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '', 'Fleet');
                                refreshNotifications(true);
                            }

                        },
                        error: function() {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        };
    }
</script>