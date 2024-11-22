<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
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
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }
</style>
<?php
if (!empty($header)) {
    ?>
    <div class="row">
        <div class="col-md-5">
            <div
                style="font-size: 16px; font-weight: 600;"><?php echo $header['PropertyName']; ?></div>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <?php
        if ($header['confirmedYN'] == 0) { ?>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="fetchPage('system/operationNgo/create_publicProperty_beneficiary',<?php echo $header['publicPropertyBeneID'] ?>,'Edit Public Property Beneficiary','NGO');">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    Edit
                </button>
            </div>
            <?php
        }
        ?>

    </div>
    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <li><a href="#tab_familyDetail" onclick="damage_ass_details()" data-toggle="tab"><i
                    class="fa fa-television"></i>Damage Assessment </a></li>
        <li><a href="#notes" onclick="beneficiary_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a>
        </li>
        <li><a href="#HNimageTab" onclick="fetch_beneficiary_imageView()" data-toggle="tab"><i class="fa fa-television"></i>Image
                Upload </a>
        </li>
        <?php /*} */?>
        <li><a href="#files" onclick="fetch_pubProperty_documentView()" data-toggle="tab"><i
                    class="fa fa-television"></i>Documents
            </a></li>
    </ul>
    <input type="hidden" id="editpublicPropertyBeneID" value="<?php echo $header['publicPropertyBeneID'] ?>">
    <input type="hidden" id="editprojectID" value="<?php echo $header['projectID'] ?>">
    <div class="tab-content">
        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>BENEFICIARY PROPERTY NAME AND DETAIL</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">System Reference No</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['systemCode']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Secondary Reference No</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['secondaryCode']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Property Type</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['propTypeDescription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Registered Date</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['registeredDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Property Name</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['PropertyName']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Property Short Code</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['PropertyShortCode']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Commencement Date</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['commencementDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Email</span></td>

                            <td><span class="tddata"><?php echo $header['contactEmail'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Phone (Primary)</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['phoneCountryCodePrimary'] . " - " . $header['phoneAreaCodePrimary'] . $header['contactPhonePrimary'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Phone (Secondary)</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['phoneCountryCodeSecondary'] . " - " . $header['phoneAreaCodeSecondary'] . $header['phoneSecondary'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Country</span></td>
                            <td><span class="tddata"><?php echo $header['CountryDes'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Province / State</span></td>
                            <td><span class="tddata"><?php echo $header['provinceName'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Area / District</span></td>
                            <td><span class="tddata"><?php echo $header['districtName'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Division</span></td>
                            <td><span class="tddata"><?php echo $header['divisionName'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Mahalla</span></td>
                            <td><span class="tddata"><?php echo $header['subDivisionName'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Postal Code</span></td>
                            <td><span class="tddata"><?php echo $header['postalCode'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Address</span></td>
                            <td><span class="tddata"><?php echo $header['address'] ?></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <h4>Public Property Image</h4>

                    <div class="fileinput-new thumbnail">
                        <?php if ($header['publicPropertyImage'] != '') { ?>
                            <img
                                src="<?php echo base_url('uploads/NGO/beneficiaryImage/' . $header['publicPropertyImage']); ?>"
                                id="changeImg" style="width: 200px; height: 145px;">
                            <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 200px; height: 145px;">
                        <?php } ?>
                        <input type="file" name="contactImage" id="itemImage" style="display: none;"
                               onchange="loadPropertyDamageImage(this)"/>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>BENEFICIARY BENEFITED PROJECTS</h2>
                    </header>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <?php
                        if (!empty($projects)) {
                            foreach ($projects as $pro) {
                                echo $pro['projectName'] . ", ";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <br>
                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2>BENEFICIARY FAMILY DESCRIPTION</h2>
                        </header>
                    </div>
                </div>
                <br>
                <table class="property-table">
                    <tbody>
                    <tr>
                        <td style="padding-left: 5%;"><span
                                class="tddata"><?php echo $header['familyDescription'] ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>RECORD DETAILS</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td class="ralign"><span class="title">Created Date</span></td>
                    <td><span class="tddata"><?php echo $header['createdDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Last Updated</span></td>
                    <td><span class="tddata"><?php echo $header['modifydate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Damage Assessment Created By</span></td>
                    <td><span class="tddata"><?php echo $header['contactCreadtedUser'] ?></span></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="tab_familyDetail">
            <br>

            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> DAMAGE ASSESSMENT </h4></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="damage_assDetail"></div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="notes">
            <br>

            <div class="row" id="show_add_notes_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Notes </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Note
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_propertydamage_add_notes"'); ?>
            <input type="hidden" name="publicPropertyBeneID" value="<?php echo $header['publicPropertyBeneID']; ?>">

            <div id="show_add_notes" class="hide">
                <div class="row">
                    <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="description"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <button class="btn btn-primary" type="submit">Add</button>
                        <button class="btn btn-danger" type="button" onclick="close_add_note()">Close</button>
                    </div>
                    <div class="form-group col-sm-6" style="margin-top: 10px;">

                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_notes"></div>
        </div>
        <div class="tab-pane" id="HNimageTab">
            <br>

            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> Image Upload </h4></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_image_view"></div>
                </div>
            </div>
        </div>
        <?php /*} */?>
        <div class="tab-pane" id="files">
            <br>

            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> Documents </h4></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="publicProperty_document_view"></div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $("#description").wysihtml5();

        $('#frm_propertydamage_add_notes').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //campaign_name: {validators: {notEmpty: {message: 'Campaign Name is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/add_publicProperty_notes'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_note();
                        beneficiary_notes();
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

    function beneficiary_notes() {
        var publicPropertyBeneID = $('#editpublicPropertyBeneID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {publicPropertyBeneID: publicPropertyBeneID},
            url: "<?php echo site_url('OperationNgo/load_publicProperty_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_notes').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function show_add_note() {
        $('#show_all_notes').addClass('hide');
        $('#show_add_notes_button').addClass('hide');
        $('#show_add_notes').removeClass('hide');
        $('#frm_propertydamage_add_notes')[0].reset();
        $('#frm_propertydamage_add_notes').bootstrapValidator('resetForm', true);
    }

    function close_add_note() {
        $('#show_add_notes').addClass('hide');
        $('#show_all_notes').removeClass('hide');
        $('#show_add_notes_button').removeClass('hide');
    }

    function document_uplode() {
        var formData = new FormData($("#contact_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('OperationNgo/ngo_attachement_upload'); ?>",
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
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#contactattachmentDescription').val('');
                    contact_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function contact_attachments() {
        var publicPropertyBeneID = $('#editpublicPropertyBeneID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {publicPropertyBeneID: publicPropertyBeneID},
            url: "<?php echo site_url('OperationNgo/load_donor_all_attachments'); ?>",
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
        $('#add_attachemnt_show').removeClass('hide');
    }

    function delete_donor_attachment(id, fileName) {
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
                    url: "<?php echo site_url('OperationNgo/delete_donor_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            contact_attachments();
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

    function loadPropertyDamageImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadPublicProperty();
        }
    }

    function profileImageUploadPublicProperty() {
        var imgageVal = new FormData();
        imgageVal.append('publicPropertyBeneID', $('#editpublicPropertyBeneID').val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#contact_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('OperationNgo/publicProperty_image_upload'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_note(notesID) {
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
                    data: {notesID: notesID},
                    url: "<?php echo site_url('OperationNgo/delete_master_notes_allDocuments'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Note Deleted Successfully');
                            beneficiary_notes();
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

    function damage_ass_details() {
        var publicPropertyBeneID = $('#editpublicPropertyBeneID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {publicPropertyBeneID: publicPropertyBeneID},
            url: "<?php echo site_url('OperationNgo/fetch_damage_ass_details_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#damage_assDetail').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

        fetch_publicPropertyAssessment(publicPropertyBeneID)
    }

    function fetch_pubProperty_documentView() {
        var publicPropertyBeneID = $('#editpublicPropertyBeneID').val();
        var projectID = $('#editprojectID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'publicPropertyBeneID': publicPropertyBeneID, projectID: projectID},
            url: '<?php echo site_url("OperationNgo/load_publicProperty_documents_view_forEdit"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#publicProperty_document_view').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function modaluploadimages(empfamilydetailsID) {
        $('#empfamilydetailzID').val(empfamilydetailsID);
        $('#modaluploadimages').modal('show');
    }

    function fetch_beneficiary_imageView() {
        var publicPropertyBeneID = $('#editpublicPropertyBeneID').val();
        var projectID = $('#editprojectID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'publicPropertyBeneID': publicPropertyBeneID, projectID: projectID},
            url: '<?php echo site_url("OperationNgo/load_publicProperty_multiple_img_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#beneficiary_image_view').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


</script>



<?php
