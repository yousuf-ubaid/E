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
                        onclick="fetchPage('system/Fleet_Management/load_Driver_edit_view','<?php echo $master['driverMasID'] ?>','Edit Driver - <?php echo $master['driverCode'] ?> |  <?php echo $master['driverName']; ?>',)">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    Edit
                </button>
            </div>
        </div>
        <?php
    } else {
    } ?>

    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <li><a href="#files" onclick="driver_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Attachments
            </a></li>
    </ul>
    <input type="hidden" id="editdriverMasID" value="<?php echo $master['driverMasID'] ?>">
    <div class="tab-content">


        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('fleet_driver_details'); ?></h2>
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
                                <strong id="brand_deception"> <?php echo $this->lang->line('fleet_driverID'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['driverCode']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="model_deception"> <?php echo $this->lang->line('fleet_driverName'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['driverName']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong
                                    id="bodyType_deception"><?php echo $this->lang->line('fleet_otherName'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['OtherName']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong
                                    id="colour_deception"><?php echo $this->lang->line('fleet_driver_age'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['driverAge']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="fuel_type_deception"><?php echo $this->lang->line('fleet_BloodGroup'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['BloodDescription']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="transmisson_deception"><?php echo $this->lang->line('fleet_driverPhone'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['drivPhoneNo']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="manufacturedYear"><?php echo $this->lang->line('fleet_driverAddress'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['drivAddress'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="engineCapacity"><?php echo $this->lang->line('fleet_licenceNo'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo $master['licenceNo']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="expKMperLiter"><?php echo $this->lang->line('fleet_licenceExpiryDate'); ?>: </strong>
                            </td>
                            <td>
                                <?php echo date('dS F Y (l)', strtotime($master['liceExpireDate'])) ?>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <strong id="_HouseNo"><?php echo $this->lang->line('fleet_driverDescription'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php echo $master['driveDescript'] ?>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <strong
                                    id="_Status"><?php echo $this->lang->line('fleet_driverStatus'); ?>
                                    : </strong>
                            </td>
                            <td>
                                <?php if ($master['isActive'] == 1) {
                                    ?>
                                    <span class="label"
                                          style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('fleet_driverActive'); ?><!--Active--></span>
                                    <?php
                                } else {
                                    ?>
                                    <span class="label"
                                          style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('fleet_driverInactive'); ?><!--inactive--></span>
                                    <?php

                                } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>RECORD DETAILS</h2>
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
                        echo $master['createdDateTime'];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong id="_CD"><?php echo $this->lang->line('fleet_CreatedBy'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo $master['createdUserName'] ?>
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
                                   value="<?php echo $master['driverMasID']; ?>">
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
            <div class="row" id="show_all_attachments">
            </div>

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
                url: "<?php echo site_url('Fleet/ngo_driverattachement_upload'); ?>",
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
                        driver_attachments();
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function driver_attachments() {
            var driverMasID = $('#editdriverMasID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {driverMasID: driverMasID},
                url: "<?php echo site_url('Fleet/load_all_driver_attachments'); ?>",
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

        function delete_driver_attachment(id, fileName) {
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
                        url: "<?php echo site_url('Fleet/delete_driver_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                driver_attachments();
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

