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
    $printHelpAndNestArray = fetch_com_project_shortCode($header['benificiaryID']);
    $testArray = array_column($printHelpAndNestArray, 'projectShortCode');
    ?>
    <div class="row">
        <div class="col-md-5">
            <div
                style="font-size: 16px; font-weight: 600;"><?php echo $header['fullName']; ?></div>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <?php
        if ($header['confirmedYN'] == 0) { ?>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="fetchPage('system/communityNgo/ngo_mo_communityBeneficiary',<?php echo $header['benificiaryID'] ?>,'Edit Community Beneficiary','NGO');">
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
        <li><a href="#tab_familyDetail" onclick="beneficiary_family_details()" data-toggle="tab"><i
                    class="fa fa-television"></i>Family Detail </a></li>
        <li><a href="#notes" onclick="beneficiary_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a>
        </li>
        <!-- --><?php /*if (in_array('HN', $testArray)) { */?>
        <li><a href="#HNimageTab" onclick="fetch_comBeneficiary_imageView()" data-toggle="tab"><i class="fa fa-television"></i>Image
                Upload </a>
        </li>
        <?php /*} */?>
        <li><a href="#files" onclick="fetch_comBeneficiary_documentView()" data-toggle="tab"><i
                    class="fa fa-television"></i>Documents
            </a></li>
    </ul>
    <input type="hidden" id="editbenificiaryID" value="<?php echo $header['benificiaryID'] ?>">
    <input type="hidden" id="editprojectID" value="<?php echo $header['projectID'] ?>">
    <div class="tab-content">
        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>BENEFICIARY NAME AND DETAIL</h2>
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
                            <td class="ralign"><span class="title">Beneficiary Type</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['benTypeDescription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Registered Date</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['registeredDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Full Name</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['fullName']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Name with Initials</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['nameWithInitials']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Date of Birth</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['dateOfBirth']; ?></span>
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
                    <h4>Beneficiary Image</h4>

                    <div class="fileinput-new thumbnail">
                        <?php if ($header['benificiaryImage'] != '') { ?>
                            <img
                                src="<?php echo base_url('uploads/NGO/beneficiaryImage/' . $header['benificiaryImage']); ?>"
                                id="changeImg" style="width: 200px; height: 145px;">
                            <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 200px; height: 145px;">
                        <?php } ?>
                        <input type="file" name="contactImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
                    </div>
                </div>
            </div>
            <br>
            <?php if (in_array('HN', $testArray)) { ?>
                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2>BENEFICIARY HELP AND NEST PROJECT DETAIL</h2>
                        </header>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-sm-12">
                            <table class="property-table">
                                <tbody>
                                <tr>
                                    <td class="ralign"><span class="title">NIC No</span></td>
                                    <td><span class="tddata"><?php echo $header['NIC']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="ralign"><span class="title">Family Details</span></td>
                                    <td><span class="tddata"><?php echo $header['familyMembersDetail']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="ralign"><span class="title">Own Land Available</span></td>
                                    <td><span class="tddata">
                                        <?php
                                        $land = '';
                                        if ($header['ownLandAvailable'] == 1) {
                                            $land = 'Yes. ';
                                        } else if ($header['ownLandAvailable'] == 2) {
                                            $land = 'No. ';
                                        }
                                        echo $land . $header['ownLandAvailableComments']; ?>
                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ralign"><span class="title">Total Sq Ft</span></td>
                                    <td><span class="tddata"><?php echo $header['totalSqFt']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="ralign"><span class="title">Total Cost</span></td>
                                    <td><span class="tddata"><?php echo $header['totalCost']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="ralign"><span class="title">Reason in Brief</span></td>
                                    <td><span class="tddata"><?php echo $header['reasoninBrief']; ?></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
            <?php } ?>
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
            <?php if (in_array('HN', $testArray)) {

            } else { ?>
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
            <?php } ?>
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
                    <td class="ralign"><span class="title">Beneficiary Created By</span></td>
                    <td><span class="tddata"><?php echo $header['contactCreadtedUser'] ?></span></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="tab_familyDetail">
            <br>

            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> Family Details </h4></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_familyDetail"></div>
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
            <?php echo form_open('', 'role="form" id="frm_beneficiary_add_notes"'); ?>
            <input type="hidden" name="benificiaryID" value="<?php echo $header['benificiaryID']; ?>">

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
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_notes"></div>
        </div>
        <!-- --><?php /*if (in_array('HN', $testArray)) { */?>
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
                    <div id="beneficiary_document_view"></div>
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

        $('#frm_beneficiary_add_notes').bootstrapValidator({
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
                url: "<?php echo site_url('CommunityNgo/add_comBeneficiary_notes'); ?>",
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
        var benificiaryID = $('#editbenificiaryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {benificiaryID: benificiaryID},
            url: "<?php echo site_url('CommunityNgo/load_comBeneficiary_allNotes'); ?>",
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
        $('#frm_beneficiary_add_notes')[0].reset();
        $('#frm_beneficiary_add_notes').bootstrapValidator('resetForm', true);
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
            url: "<?php echo site_url('CommunityNgo/comMemBen_attachment_upload'); ?>",
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
        var benificiaryID = $('#editbenificiaryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {benificiaryID: benificiaryID},
            url: "<?php echo site_url('CommunityNgo/load_comMemBen_all_attachment'); ?>",
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

    function delete_comMemBen_attachment(id, fileName) {
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
                    url: "<?php echo site_url('CommunityNgo/delete_comMemBen_attachment'); ?>",
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

    $('#helpNest_changeImg').click(function () {
        $('#helpNest_itemImage').click();
    });

    $('#helpNest_changeImg_two').click(function () {
        $('#helpNest_itemImage_two').click();
    });

    function loadImage_helpNest(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#helpNest_changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadBeneficiary_helpNest();
        }
    }

    function loadImage_helpNest_two(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#helpNest_changeImg_two').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadBeneficiary_helpNest_two();
        }
    }

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadBeneficiary();
        }
    }

    function profileImageUploadBeneficiary_helpNest() {
        var imgageVal = new FormData();
        imgageVal.append('benificiaryID', $('#editbenificiaryID').val());

        var files = $("#helpNest_itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#contact_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('CommunityNgo/comBeneficiary_imgUpload_helpNest'); ?>",
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

    function profileImageUploadBeneficiary_helpNest_two() {
        var imgageVal = new FormData();
        imgageVal.append('benificiaryID', $('#editbenificiaryID').val());

        var files = $("#helpNest_itemImage_two")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#contact_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('CommunityNgo/comBeneficiary_imgUpload_helpNest_two'); ?>",
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

    function profileImageUploadBeneficiary() {
        var imgageVal = new FormData();
        imgageVal.append('benificiaryID', $('#editbenificiaryID').val());

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
            url: "<?php echo site_url('CommunityNgo/comBeneficiary_imgUpload'); ?>",
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

    function delete_comBen_note(notesID) {
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
                    url: "<?php echo site_url('CommunityNgo/delete_comBen_masterNote_allDocument'); ?>",
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

    function beneficiary_family_details() {
        var beneficiaryID = $('#editbenificiaryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {beneficiaryID: beneficiaryID},
            url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_familyDel_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#beneficiary_familyDetail').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_comBeneficiary_documentView() {
        var benificiaryID = $('#editbenificiaryID').val();
        var projectID = $('#editprojectID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'benificiaryID': benificiaryID, projectID: projectID},
            url: '<?php echo site_url("CommunityNgo/load_comBeneficiary_documents_view_forEdit"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#beneficiary_document_view').html(data);
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

    function familyimage_uplode() {
        var benificiaryID = $('#editbenificiaryID').val();
        var formData = new FormData($("#family_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('CommunityNgo/comBeneficiary_familyImg_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    beneficiary_family_details(benificiaryID);
                    $('#modaluploadimages').modal('hide');
                }
                $('#family_image_uplode_form')[0].reset();
            },
            error: function (data) {
                stopLoad();
                myAlert('e', 'Please contact support Team');
            }
        });
        return false;
    }

    function fetch_comBeneficiary_imageView() {
        var benificiaryID = $('#editbenificiaryID').val();
        var projectID = $('#editprojectID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'benificiaryID': benificiaryID, projectID: projectID},
            url: '<?php echo site_url("CommunityNgo/load_comBeneficiary_multipleImage_view"); ?>',
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
