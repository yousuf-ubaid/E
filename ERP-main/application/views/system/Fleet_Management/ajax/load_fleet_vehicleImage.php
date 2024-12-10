<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

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

        .nav-tabs > li > a {
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

        .nav-tabs > li > a:hover {
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

        .nav-tabs > li.active > a,
        .nav-tabs > li.active > a:hover,
        .nav-tabs > li.active > a:focus {
            color: #5e7bf1;
            cursor: default;
            background-color: #fff;
            font-weight: bold;
            border-bottom: 3px solid #095db3;
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
                        onclick="fetchPage('system/Fleet_Management/load_Vehicle_edit_view','<?php echo $master['vehicleMasterID'] ?>','Edit Vehicle - <?php echo $master['bodyType_description'] ?> |  <?php echo $master['brand_description']; ?>',)">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    <?php echo $this->lang->line('common_edit')?>
                </button>
            </div>
        </div>
        <?php
    } else {
    } ?>

    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('fleet_About')?></a></li>
        <li><a href="#files" onclick="vehicle_attachments()" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_attachments')?>
            </a></li>
    </ul>
    <input type="hidden" id="editvehicleMasterID" value="<?php echo $master['vehicleMasterID'] ?>">
    <div class="tab-content">


        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('fleet_Vehicle_Master'); ?></h2>
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
                                <strong id="brand_deception"> <?php echo $this->lang->line('fleet_vehicle_Brand'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['brand_description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="model_deception"> <?php echo $this->lang->line('fleet_vehicle_model'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['model_description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong
                                    id="bodyType_deception"><?php echo $this->lang->line('fleet_vehicle_type'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['bodyType_description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong
                                    id="colour_deception"><?php echo $this->lang->line('fleet_vehicle_color'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['colour_description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="fuel_type_deception"><?php echo $this->lang->line('fleet_vehicle_fuel'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['fuel_type_description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="transmisson_deception"><?php echo $this->lang->line('fleet_vehicle_transmission'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['transmisson_description']; ?>
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
                                <strong id="engineCapacity"><?php echo $this->lang->line('fleet_vehicle_engine'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['engineCapacity']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="expKMperLiter"><?php echo $this->lang->line('fleet_vehicle_speed'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['expKMperLiter']; ?>
                            </td>
                        </tr>
                        <!--
                        <tr>
                            <td>
                                <strong id="_MaritialStatus"><?php echo $this->lang->line('communityngo_status'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php
                                if ($master['CurrentStatus'] == 0) {
                                    $description = 'Single';
                                } else if ($master['CurrentStatus'] == 1) {
                                    $description = 'Married';
                                } else if ($master['CurrentStatus'] == 2) {
                                    $description = 'Widow';
                                } else if ($master['CurrentStatus'] == 3) {
                                    $description = 'Widower (WR)';
                                } else if ($master['CurrentStatus'] == 4) {
                                    $description = 'Divorced';
                                }
                                echo $description;

                                ?>
                            </td>
                        </tr>
                        -->
                        <tr>
                            <td>
                                <strong id="VehicleNo"><?php echo $this->lang->line('fleet_vehicle_No'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['VehicleNo']; ?>
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
                        <tr>
                            <td>
                                <strong id="lisenceDate"><?php echo $this->lang->line('fleet_vehicle_licenceDate'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo date('dS F Y (l)', strtotime($master['licenseDate'])) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong id="vehicle_type"><?php echo $this->lang->line('fleet_vehicle_status'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['vehicle_type'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="_HouseNo"><?php echo $this->lang->line('fleet_vehicleDescription'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['vehDescription'] ?>
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
                                $vehicalimage = $this->s3->createPresignedRequest('uploads/Fleet/VehicleImg/'.$master['vehicleImage'], '1 hour');
                            ?>

                            <img src="<?php echo $vehicalimage ?>"
                                 id="changeImg" style="width: 200px; height: 145px;">
                            <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 200px; height: 145px;">
                        <?php } ?>

                        <input type="file" name="contactImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
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
                        <h2><?php echo $this->lang->line('fleet_record_details')?><!--RECORD DETAILS--></h2>
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
    </div>

    <?php
}
?>
    <script type="text/javascript">
        $(document).ready(function () {

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            Inputmask().mask(document.querySelectorAll("input"));
        });


        function attchment_Upload() {
            var formData = new FormData($("#attachment_Upload_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Fleet/ngo_Vehicleattachement_upload'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        $('#add_attachment_show').addClass('hide');
                        $('#remove_id').click();
                        $('#contactattachmentDescription').val('');
                        vehicle_attachments();
                    }
                },
                error: function (data) {
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
                data: {vehicleMasterID: vehicleMasterID},
                url: "<?php echo site_url('Fleet/load_vehicle_all_attachments'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_all_attachments').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
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
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': id, 'myFileName': fileName},
                        url: "<?php echo site_url('Fleet/delete_vehicle_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                vehicle_attachments();
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        $('#changeImg').click(function () {
            $('#itemImage').click();
        });

        function loadImage(obj) {
            if (obj.files && obj.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
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
                url: "<?php echo site_url('Fleet/vehicle_image_upload'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {

                    }
                    //get_vehicle_DetailsView(id);

                }, error: function () {
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
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': id, 'myFileName': fileName},
                        url: "<?php echo site_url('Fleet/delete_vehicle_image'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                get_vehicle_DetailsView(id);
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }


    </script>




