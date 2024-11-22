<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
// echo head_page($title  , false);
// $title = $this->lang->line('fleet_fuel_usage');
$date_format_policy = date_format_policy();
$current_date = current_format_date(); ?>


<style>
    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    #recordInfoTable tr td:first-child {
        color: #095db3;
    }

    #recordInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs>li>a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs>li>a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:hover,
    .nav-tabs>li.active>a:focus {
        color: #5e7bf1;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #095db3;
    }

    .bigdrop {
        width: 30% !important;
    }
</style>
<?php
if (!empty($master)) {
?>
    <?php if ($master['isActive'] == 1) {
    ?>

        <div class="row">
            <div class="col-md-9">
                &nbsp;
            </div>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right"

                    onclick="fetchPage('system/Fleet_Management/load_asset_edit_view','<?php echo $master['vehicleMasterID'] ?>','Edit Vehicle - <?php echo $master['bodyType_description'] ?> |  <?php echo $master['brand_description']; ?>',)">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    <?php echo $this->lang->line('common_edit') ?>
                </button>
            </div>
        </div>
    <?php
    } else {
    } ?>

    <br>
    <ul class="nav nav-tabs" id="main-tabs">

        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('fleet_About') ?></a></li>
        <li><a href="#files" onclick="vehicle_attachments()" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_attachments') ?>
            </a></li>


        <?php if ($asset_type_id == 1) { // Check if asset_type_id is 1 
        ?>
            <li><a href="#extra-tab1" data-toggle="tab">Components</a></li>
            <li><a href="#extra-tab2" data-toggle="tab">Spare Parts</a></li>
        <?php } ?>

        <li>
            <a href="#assignTemplate" onclick="assign_templates()" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('fleet_assign_Templates') ?>
            </a>
        </li>

    </ul>
    <input type="hidden" id="editvehicleMasterID" value="<?php echo $master['vehicleMasterID'] ?>">
    <div class="tab-content">


        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php

                            if ($asset_type_id == 1) {
                                echo 'Asset Details';
                            } elseif ($asset_type_id == 2) {

                                echo 'Component Details';
                            }
                            ?></h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="table table-striped" id="profileInfoTable"

                        style="background-color: #ffffff; width: 100%">
                        <tbody>
                            <tr>
                                <td>
                                    <strong id="brand_deception"> <?php echo $this->lang->line('_fleet_main_category'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo $master['brand_description']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong id="model_deception"> <?php echo $this->lang->line('_fleet_sub_category'); ?>: </strong>
                                </td>
                                <td>
                                    <?php echo $master['model_description']; ?>
                                </td>
                            </tr>



                            <tr>
                                <td>
                                    <strong id="manufacturedYear"><?php echo $this->lang->line('fleet_vehicle_year'); ?>: </strong>
                                </td>
                                <td>
                                    <?php echo $master['manufacturedYear'] ?>
                                </td>
                            </tr>



                            <tr>
                                <td>
                                    <strong id="VehicleNo"><?php echo $this->lang->line('fleet_serial_no'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo $master['assetSerialNo']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong id="registerDate"><?php echo $this->lang->line('fleet_vehicle_registerDate'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo date('dS F Y (l)', strtotime($master['registerDate'])) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong id="insuranceDate"><?php echo $this->lang->line('fleet_vehicle_insuranceDate'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo date('dS F Y (l)', strtotime($master['insuranceDate'])) ?>
                                </td>
                            </tr>


                            <!-- <tr>

                            <td>
                                <strong id="vehicle_type"><?php echo $this->lang->line('fleet_status'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['vehicle_type'] ?>
                            </td>
                        </tr> -->

                            <tr>
                                <td>
                                    <strong id="_HouseNo"><?php echo $this->lang->line('fleet_description'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo $master['vehDescription'] ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong id="location"><?php echo $this->lang->line('common_location'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php echo $master['locationName'] ?>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <strong
                                        id="_Status"><?php echo $this->lang->line('fleet_Status'); ?>
                                        : </strong>
                                </td>
                                <td>
                                    <?php if ($master['isActive'] == 1) {
                                    ?>
                                        <span class="label"
                                            style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('fleet_VehicleActive'); ?><!--Active--></span>
                                    <?php
                                    } else {
                                    ?>
                                        <span class="label"
                                            style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('fleet_VehicleInactive'); ?><!--inactive--></span>
                                    <?php

                                    } ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="fileinput-new thumbnail">

                        <?php if ($master['vehicleImage'] != '') {
                            $vehicalimage = $this->s3->createPresignedRequest('uploads/Fleet/VehicleImg/' . $master['vehicleImage'], '1 hour');
                        ?>

                            <img src="<?php echo $vehicalimage ?>"
                                id="changeImg" style="width: 200px; height: 145px;">
                        <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                style="width: 200px; height: 145px;">
                        <?php } ?>

                        <input type="file" name="contactImage" id="itemImage" style="display: none;"
                            onchange="loadImage(this)" />

                    </div>
                    <h4 style="text-align: center;margin: 0;color: #095db3;font-weight: bold">
                        <?php echo empty($master['bodyType_deception']) ? '' : $master['brand_deception']; ?>
                    </h4>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">

                        <h2><?php echo $this->lang->line('fleet_record_details') ?><!--RECORD DETAILS--></h2>
                    </header>
                </div>
            </div>
            <table class="table table-striped" id="recordInfoTable"

                style="background-color: #ffffff;width: 100%">
                <tbody>
                    <tr>
                        <td>
                            <strong id="_CD"><?php echo $this->lang->line('fleet_CreatedDate'); ?>:</strong>
                        </td>
                        <td>
                            <?php
                            echo $master['createDateTime'];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong id="_CD"><?php echo $this->lang->line('fleet_LastUpdated'); ?>:</strong>
                        </td>
                        <td>
                            <?php echo $master['modifiedDateTime'] ?>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>



        <div class="tab-pane" id="files">
            <br>

            <div class="row" id="show_add_files_button">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Attachments
                    </button>
                </div>
            </div>
            <div class="row hide" id="add_attachment_show">
                <?php echo form_open_multipart('', 'id="attachment_Upload_form" class="form-inline"'); ?>
                <div class="col-sm-12" style="">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo $this->lang->line('fleet_Description'); ?><!--Description--><?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="contactattachmentDescription"

                                name="attachmentDescription" placeholder="Description..." style="width: 115%;">
                            <input type="hidden" class="form-control" id="documentID" name="documentID" value="7">
                            <input type="hidden" class="form-control" id="contact_documentAutoID" name="documentAutoID"
                                value="<?php echo $master['vehicleMasterID']; ?>">

                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo $this->lang->line('fleet_ExpiryDate'); ?><!--Expiry Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="docExpiryDate" style="width: 120%;"
                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                    value="" id="docExpiryDate" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8" style="margin-top: -8px;">
                        <div class="form-group">
                            <label class=" control-label" style="visibility: hidden;">UPLOAD</label>
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput"><i
                                        class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                        class="fileinput-filename"></span></div>
                                <span class="input-group-addon btn btn-default btn-file"><span
                                        class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                            aria-hidden="true"></span></span><span
                                        class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                            aria-hidden="true"></span></span><input
                                        type="file" name="document_file" id="document_file"></span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                        aria-hidden="true"></span></a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" style="margin-top: 3%"
                            onclick="attchment_Upload()"><span
                                class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                    </div>
                </div>

            </div>
            <br>

            <div id="show_all_attachments"></div>
        </div>

        <?php if ($asset_type_id == 1) { // Check if asset_type_id is 1 
        ?>
            <div class="tab-pane" id="extra-tab1">
                <div class="row">
                    <div class="col-md-3">
                        &nbsp;
                    </div>
                    <div class="col-md-9 text-right">
                        <button type="button" id="addRowBtn" class="btn btn-primary btn-sm pull-right" onclick="test_modal()"
                            style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_Add'); ?><!--New Asset-->
                        </button>
                    </div>
                </div>

                <hr>


                <!-- Content for Extra Tab 1 -->

                <div class="table-responsive">
                    <table id="comfetchTable" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th> Item Code</th>
                                <th>Description</th>
                                <th>Serial No</th>
                                <th>Manufacture</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="comfetchbody">
                            <input type="hidden" id="type" name="type" value="1">


                        </tbody>
                    </table>
                </div>
                <hr>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-right" style="margin-top: 20px;"> <!-- Adjust the value as needed -->

                        </div>
                    </div>
                </div>



            </div>







            <div class="tab-pane" id="extra-tab2">
                <div class="row">
                    <div class="col-md-3">
                        &nbsp;
                    </div>
                    <div class="col-md-9 text-right">
                        <button type="button" id="addRowBtn" class="btn btn-primary btn-sm pull-right" onclick="test_sparemodal()"
                            style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_Add'); ?><!--New Asset-->
                        </button>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table id="spareTable" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th> Item Code</th>
                                <th>Description</th>
                                <th>Part No</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="sparebody">
                            <input type="hidden" id="sparetype" name="sparetype" value="2">


                        </tbody>
                    </table>
                </div>
                <hr>



            </div>
        <?php } ?>

        <div class="tab-pane" id="assignTemplate">
            <div id="show_all_templates"></div>   
        </div>


    </div>
<?php
}
?>

<!-- Bootstrap Modal -->
<div class="modal fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" width="100px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Components</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table id="componentTable" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Serial No</th>
                                <th>Manufacture</th>
                                <th>Action</th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="appendRow()">
                                        <i class="fa fa-plus"></i></button>


                                </th>
                            </tr>
                        </thead>
                        <tbody id="componentbody">
                            <input type="hidden" id="type" name="type" value="1">


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveTable()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Modal -->
<div class="modal fade" id="spare_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" width="100px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Spare parts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table id="spareTable" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Part No</th>
                                <th>Action</th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="appendSpareRow()">
                                        <i class="fa fa-plus"></i></button>


                                </th>
                            </tr>
                        </thead>
                        <tbody id="sparefetchbody">
                            <input type="hidden" id="type" name="type" value="2">


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveSpareRows()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var fleetData = [];
    var selectedVehicleIds = [];
    var sparedata = [];
    var p_id;

    setTimeout(function() {
        p_id = localStorage.getItem("page_id");
        }, 3000);




    $(document).ready(function() {

        $('.select2').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {});

        Inputmask().mask(document.querySelectorAll("input"));

        // $('#exampleModal5').on('shown.bs.modal', function () {  
        //     setTimeout(function() {             
        //         $('.itemCode').select2({                 
        //             dropdownParent: $('#exampleModal5')             
        //         });         
        //     }, 500);

        //     });
    });

    function test_modal() {
        $('#exampleModal5').modal();
    }

    function test_sparemodal() {
        $('#spare_modal').modal();
    }
    // Fetch fleet data when the page loads
    $.ajax({
        url: "<?php echo site_url('Fleet/get_fleet_data'); ?>",
        method: 'GET',
        dataType: 'json',
        success: function(data) {

            fleetData = data;
        },
        error: function(error) {
            }
    });

    // Fetch fleet data when the page loads
    $.ajax({
        url: "<?php echo site_url('Fleet/get_inventory_data'); ?>",
        method: 'GET',
        dataType: 'json',
        success: function(data) {

            sparedata = data;
        },
        error: function(error) {
            }
    });




    function fetchComponentData(comasset) {

        $.ajax({
            url: "<?php echo site_url('Fleet/display_components'); ?>",

            method: 'GET',
            data: {
                comasset: p_id
            },
            dataType: 'json',
            success: function(data) {
                populateTable(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching component data: ', textStatus, errorThrown);
            }
        });
    }

    function populateTable(data) {
        var tbody = $('#comfetchbody');
        tbody.empty(); // Clear existing table rows

        if (data.length > 0) {
            $.each(data, function(index, component) {
                var row = $('<tr>');

                // Add columns for each property of the component
                row.append('<td>' + component.vehicleCode + '</td>');
                row.append('<td>' + component.componentName + '</td>');
                row.append('<td>' + component.assetSerialNo + '</td>');
                row.append('<td>' + component.manufacturer + '</td>');

                // Add delete button column
                var deleteButton = $('<button class="action-btn"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button>');
                deleteButton.on('click', function() {
                    // Call deleteComponent function when delete button is clicked
                    deleteComponent(component.id);
                });

                row.append($('<td>').append(deleteButton));
                tbody.append(row);
            });
        } else {
            tbody.append('<tr><td colspan="5">No records found</td></tr>');
        }
    }

    // Fetch and display data on page load
    fetchComponentData();
    // ================================================================
    function appendRow() {

        // Create a new table row element
        var newRow = $('<tr></tr>');

        // Create the dropdown element
        var dropdown = $('<select class="form-control select2 itemCode" id="itemCode" name="itemCode"></select>');
        dropdown.append('<option value="">Select Item Code</option>'); // Default option

        // Populate the dropdown with fleetData
        fleetData.forEach(function(item) {
            var option = $('<option value="' + item.vehicleMasterID + '">' + item.vehicleCode + ' | ' + item.vehDescription + ' | ' + item.assetSerialNo + '</option>');
            dropdown.append(option);
        });

        // Create the description, serial number, and manufacture inputs
        var descriptionInput = $('<input type="text" class="form-control" name="description" readonly>');
        var serialNoInput = $('<input type="text" class="form-control" name="serialNo" readonly>');
        var manufactureInput = $('<input type="text" class="form-control" name="manufacture" readonly>');
        var hidden = $('<input type="hidden" id="comasset" name="comasset">');

        // Handle dropdown change event
        dropdown.on('change', function() {
            var selectedValue = $(this).val();
            var selectedItem = fleetData.find(item => item.vehicleMasterID == selectedValue);
            if (selectedItem) {
                // Update the description, serial number, and manufacture inputs
                descriptionInput.val(selectedItem.vehDescription);
                serialNoInput.val(selectedItem.assetSerialNo);
                manufactureInput.val(selectedItem.manufacturedYear);
                hidden.val(selectedItem.vehicleMasterID);
            } else {
                descriptionInput.val('');
                serialNoInput.val('');
                manufactureInput.val('');
                hidden.val('');
            }
        });

        // Add table data (td) elements to the row
        newRow.append($('<td></td>').append(dropdown));
        newRow.append($('<td></td>').append(descriptionInput));
        newRow.append($('<td></td>').append(serialNoInput));
        newRow.append($('<td></td>').append(manufactureInput));
        newRow.append('<td><button type="button" onclick="removeRow(this)"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button></td>');
        newRow.append($('<td></td>').append(hidden));

        $('#componentbody').append(newRow);



        dropdown.select2({
            dropdownParent: $('#exampleModal5'),
            templateResult: formatOption,
            templateSelection: formatSelection,
            dropdownCssClass: 'bigdrop'
        });


        function formatOption(option) {
            if (!option.id) return option.text;
            var text = option.text.split('|');
            return $('<span>' + text[0] + ' | ' + text[1] + ' | ' + text[2] + '</span>'); // Show vehicleCode, vehDescription, assetSerialNo
        }

        function formatSelection(option) {
            if (!option.id) return option.text;
            var text = option.text.split('|');
            return text[0];
        }
    }


    function removeRow(button) {
        // Remove the row that contains the clicked button
        $(button).closest('tr').remove();
    }



    function saveTable() {
        var tableData = [];
        var hasInvalidRow = false; // Flag to check if there's any invalid row
        // var atype = $('#type').val();

        // Collect data from the `componentbody` table only (ensure it's unique)
        $('#componentbody tr').each(function() {
            var row = $(this);
            var itemCode = row.find('select[name="itemCode"]').val();
            var description = row.find('input[name="description"]').val();
            var serialNo = row.find('input[name="serialNo"]').val();
            var manufacture = row.find('input[name="manufacture"]').val();
            var comasset = row.find('input[name="comasset"]').val();

            // Validate each row (ensure itemCode is filled)
            if (itemCode) {
                tableData.push({
                    itemCode: itemCode,
                    description: description,
                    serialNo: serialNo,
                    manufacture: manufacture,
                    atype: 1,
                    comasset: p_id // Assuming `p_id` is a global variable or passed as context
                });
            } else {
                hasInvalidRow = true; // If itemCode is empty, mark the row as invalid
            }
        });

        if (tableData.length === 0) {
            alert('No records to save. Please add some rows.');
            return;
        }

        if (hasInvalidRow) {
            alert('Please fill in all required fields in the table.');
            return;
        }

        // Send data to the server
        $.ajax({
            url: "<?php echo site_url('Fleet/save_fleet_data'); ?>",
            method: 'POST',
            data: {
                tableData: tableData
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    myAlert('s', 'Record Added Successfully');
                    // Clear table rows after successful save
                    $('#componentbody').empty();
                    $('#exampleModal5').modal('hide');
                    fetchComponentData(); // Re-fetch component data
                } else if (response.status === 'duplicate') {
                    var duplicateItems = response.duplicates.join(', ');
                    myAlert('w', 'Some records were duplicates and were not saved. Duplicate items: ' + duplicateItems);
                    $('#componentbody').empty();
                    $('#exampleModal5').modal('hide');
                    fetchComponentData();
                } else {
                    myAlert('e', 'Record Added Unsuccessfully');
                    $('#exampleModal5').modal('hide');
                    fetchComponentData();
                }
                $('#componentbody').empty();
                $('#exampleModal5').modal('hide');
            },
            error: function(error) {
                myAlert('e', 'An error occurred while saving the data.');
            }
        });
    }


    function fetchSpareData(asset) {

        $.ajax({
            url: "<?php echo site_url('Fleet/display_spare'); ?>",

            method: 'GET',
            data: {
                asset: p_id
            },
            dataType: 'json',
            success: function(data) {
                spareTable(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching spare data: ', textStatus, errorThrown);
            }
        });
    }

    function spareTable(data) {
        var tbody = $('#sparebody');
        tbody.empty(); // Clear existing table rows

        if (data.length > 0) {
            $.each(data, function(index, inventory) {
                var row = $('<tr>');

                // Add columns for each property of the inventory
                row.append('<td>' + inventory.itemCode + '</td>');
                row.append('<td>' + inventory.inventory_name + '</td>');
                row.append('<td>' + inventory.part_number + '</td>');


                // Add delete button column
                var deleteinvButton = $('<button class="action-btn"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button>');
                deleteinvButton.on('click', function() {
                    // Call deleteComponent function when delete button is clicked
                    deleteInventory(inventory.id);
                });

                row.append($('<td>').append(deleteinvButton));
                tbody.append(row);
            });
        } else {
            tbody.append('<tr><td colspan="5">No records found</td></tr>');
        }
    }

    // Fetch and display data on page load
    fetchSpareData();

    function appendSpareRow() {
        var uniqueId = Date.now();

        // Create a new table row element
        var newRow = $('<tr>');

        // Create the dropdown element
        var dropdown = $('<select class="form-control select2 itemCode" id="itemCode_' + uniqueId + '" name="itemCode">');
        dropdown.append('<option value="">Select Item Code</option>'); // Default option
        sparedata.forEach(function(item) {
            var option = $('<option value="' + item.itemAutoID + '">' + item.itemSystemCode + ' | ' + item.itemDescription + ' | ' + item.partNo + '</option>');
            dropdown.append(option);
        });

        // Create the description input
        var descriptionInput = $('<input type="text" class="form-control" name="description" readonly>');

        // Create the part number input
        var partNoInput = $('<input type="text" class="form-control" name="partNo" readonly>');

        var hiddenInput = $('<input type="hidden" id="asset_' + uniqueId + '" name="asset">');

        dropdown.on('change', function() {
            var selectedOptionText = $(this).find('option:selected').text();
            var result = selectedOptionText.split('|');

            if (result.length === 3) {
                descriptionInput.val(result[1].trim());
                partNoInput.val(result[2].trim());
                hiddenInput.val(result[0].trim());
            } else {
                descriptionInput.val('');
                partNoInput.val('');
                hiddenInput.val('');
            }
        });

        // Add table data (td) elements to the row
        newRow.append($('<td>').append(dropdown)); // Dropdown for item code
        newRow.append($('<td>').append(descriptionInput)); // Item description field
        newRow.append($('<td>').append(partNoInput)); // Part number field

        // Add a remove button
        newRow.append('<td><button type="button" onclick="removeRowSpare(this)"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button></td>');

        newRow.append($('<td>').append(hiddenInput));

        // Append the new row to the table body
        $('#sparefetchbody').append(newRow);

        // Initialize Select2 for the new dropdown

        $('#itemCode_' + uniqueId).select2({
            templateResult: formatOption,
            templateSelection: formatSelection,
            dropdownParent: $('#spare_modal'),
            dropdownCssClass: 'bigdrop'
        });



        function formatOption(option) {
            if (!option.id) return option.text;
            var text = option.text.split('|');
            return $('<span>' + text[0] + ' | ' + text[1] + ' | ' + text[2] + '</span>'); // Show full details in dropdown
        }


        function formatSelection(option) {
            if (!option.id) return option.text;
            var text = option.text.split('|');
            return text[0];
        }
    }


    function removeRowSpare(button) {
        // Remove the row that contains the clicked button
        $(button).closest('tr').remove();
    }

    function saveSpareRows() {
        var rowsData = [];

        $('#sparefetchbody tr').each(function() {
            var row = $(this);
            var rowData = {
                // Assuming you have an assetID input somewhere in the form
                itemCode: row.find('select[name="itemCode"]').val(),
                spareDescription: row.find('input[name="description"]').val(),
                partNo: row.find('input[name="partNo"]').val(),
                type: 2, // Assuming type 2 for inventory
                asset: p_id
                // master_id : p_id
                // Add other necessary fields here
            };

            rowsData.push(rowData);
        });


        $.ajax({
            url: "<?php echo site_url('Fleet/save_spare_data'); ?>",
            method: 'POST',
            data: {
                spareData: rowsData
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    fetchSpareData();
                    myAlert('s', res.message); // Display success message
                } else if (res.status === 'duplicate') {
                    myAlert('w', res.message); // Display duplicate warning message
                }
                $('#sparefetchbody').empty();
                $('#spare_modal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                fetchSpareData();
                // Handle error response
            }
        });

    }
    fetchSpareData();




    function attchment_Upload() {
        var formData = new FormData($("#attachment_Upload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Fleet/ngo_Assetattachement_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachment_show').addClass('hide');
                    $('#remove_id').click();
                    $('#contactattachmentDescription').val('');
                    vehicle_attachments();
                }
            },
            error: function(data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function vehicle_attachments() {
        var vehicleMasterID = $('#editvehicleMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                vehicleMasterID: vehicleMasterID
            },
            url: "<?php echo site_url('Fleet/load_asset_all_attachments'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $('#show_all_attachments').html(data);
                stopLoad();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function assign_templates() {
        var vehicleMasterID = $('#editvehicleMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                vehicleMasterID: vehicleMasterID
            },
            url: "<?php echo site_url('Fleet/load_asset_templates'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $('#show_all_templates').html(data);
                stopLoad();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_file() {
        $('#add_attachment_show').removeClass('hide');
        $('#docExpiryDate').val('');
    }

    function delete_member_attachment(id, fileName) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'attachmentID': id,
                        'myFileName': fileName
                    },
                    url: "<?php echo site_url('Fleet/delete_asset_attachment'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            vehicle_attachments();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function() {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    $('#changeImg').click(function() {
        $('#itemImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUpload();
        }
    }

    function profileImageUpload() {
        var imgageVal = new FormData();
        imgageVal.append('vehicleID', $('#editvehicleMasterID').val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Fleet/asset_image_upload'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
                //get_vehicle_DetailsView(id);

            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_vehicle_image(id, fileName) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'attachmentID': id,
                        'myFileName': fileName
                    },
                    url: "<?php echo site_url('Fleet/delete_vehicle_image'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            get_vehicle_DetailsView(id);
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function() {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function deleteComponent(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function() {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'id': id
                        },
                        url: "<?php echo site_url('Fleet/delete_components'); ?>",
                        beforeSend: function() {
                            startLoad();
                        },
                        success: function(data) {
                            stopLoad();
                            if (data[0] === 's') {
                                refreshNotifications(true);
                                swal("Deleted!", data[1], "success");
                                fetchComponentData();
                            } else {
                                swal("Error!", data[1], "error");

                            }
                        },
                        error: function() {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function deleteInventory(sid) {
        if (sid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function() {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'id': sid
                        },
                        url: "<?php echo site_url('Fleet/delete_inventory'); ?>",
                        beforeSend: function() {
                            startLoad();
                        },
                        success: function(data) {
                            stopLoad();
                            if (data[0] === 's') {
                                fetchSpareData();
                                refreshNotifications(true);
                                swal("Deleted!", data[1], "success");

                            } else {
                                swal("Error!", data[1], "error");

                            }
                        },
                        error: function() {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    // function component_modal() {
    //     $('#component_modal').modal('show');
    // }

    // function spare_modal() {
    //     $('#spare_modal').modal('show');
    // }
</script>