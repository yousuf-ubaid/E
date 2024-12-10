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
    .profile-photo-create-edit img {
        width: 40px;
        height: 40px;
    }
    .profile-photo-create-edit {
        width: 48px;
        padding: 3px;
        background: #fff;
        border-radius: 3px;
        border: solid 1px #ddd;
        position: relative;
        behavior: url(css/PIE.htc);
        z-index: 2;
    }
    #obj-type {
        font-size: 12px;
        line-height: 13px;
        color: #777;
        text-transform: uppercase;
        cursor: default;
    }
    .head .title {
        overflow: hidden;
        margin: 0;
        color: #000;
    }
    .title {
        padding-right: 10px;
    }
</style>
<?php
if (!empty($header)) {
//print_r($header);
?>
<input type="hidden" id="editcontactID" value="<?php echo $header['contactID'] ?>">

        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>NAME AND OCCUPATION</h2>
                </header>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9">
                <table class="property-table">
                    <tbody>
                    <tr>
                        <td class="ralign"><span class="title">Full Name</span></td>
                        <td><span
                                class="tddata"><?php echo $header['firstName'] . " " . $header['lastName']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Occupation</span></td>

                        <td><span class="tddata"><?php echo $header['occupation']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Department</span></td>

                        <td><span class="tddata"><?php echo $header['department']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Organization</span></td>

                        <td><span class="tddata"><?php
                                if ($header['organization'] == '') { ?>
                                    <div class="link-box"><strong class="contacttitle"><a
                                                class="link-person noselect" href="#"
                                                onclick="fetchPage('system/crm/organization_edit_view','<?php echo $header['organizationID'] ?>','View Organization','<?php echo $header['contactID'] ?>','Contact')"><?php echo $header['linkedorganization'] ?></a></strong>
                                    </div>
                                    <?php
                                } else {
                                    echo $header['organization'];
                                }
                                ?>
                                </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-3">
                <div class="fileinput-new thumbnail">
                    <?php if ($header['contactImage'] != '') { ?>
                        <img src="<?php echo base_url('uploads/crm/profileimage/' . $header['contactImage']); ?>"
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

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>CONTACT DETAILS</h2>
                </header>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9">
                <table class="property-table">
                    <tbody>
                    <tr>
                        <td class="ralign"><span class="title">Email</span></td>

                        <td><span class="tddata"><?php echo $header['contactEmail'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Phone (Mobile)</span></td>
                        <td><span class="tddata"><?php echo $header['contactPhoneMobile'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Phone (Home)</span></td>
                        <td><span class="tddata"><?php echo $header['contactPhoneHome'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Fax</span></td>
                        <td><span class="tddata"><?php echo $header['contactFax'] ?></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>ADDRESS</h2>
                </header>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9">
                <table class="property-table">
                    <tbody>
                    <tr>
                        <td class="ralign"><span class="title">Address</span></td>
                        <td><span class="tddata"><?php echo $header['address'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">City</span></td>
                        <td><span class="tddata"><?php echo $header['city'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">State</span></td>
                        <td><span class="tddata"><?php echo $header['state'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Postal Code</span></td>

                        <td><span class="tddata"><?php echo $header['postalCode'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ralign"><span class="title">Country</span></td>
                        <td><span class="tddata"><?php echo $header['CountryDes'] ?></span></td>
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
                <td class="ralign"><span class="title">Contact Created By</span></td>
                <td><span class="tddata"><?php echo $header['contactCreadtedUser'] ?></span></td>
            </tr>
            </tbody>
        </table>


    <?php
    }
    ?>
    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {

            $('#frm_contact_add_notes').bootstrapValidator({
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
                    url: "<?php echo site_url('Crm/add_contact_notes'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            close_add_note();
                            contact_notes();
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

        });

        function contact_notes() {
            var contactID = $('#editcontactID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {contactID: contactID},
                url: "<?php echo site_url('crm/load_contact_all_notes'); ?>",
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
            $("#note_description").wysihtml5();
            $('#addBtn_type').html('Add');
            $('#show_all_notes').addClass('hide');
            $('#show_add_notes_button').addClass('hide');
            $('#show_add_notes').removeClass('hide');
            $('#frm_contact_add_notes')[0].reset();
            $('#frm_contact_add_notes').bootstrapValidator('resetForm', true);
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
                url: "<?php echo site_url('crm/attachement_upload'); ?>",
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
            var contactID = $('#editcontactID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {contactID: contactID},
                url: "<?php echo site_url('crm/load_contact_all_attachments'); ?>",
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

        function delete_crm_attachment(id, fileName) {
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
                        url: "<?php echo site_url('crm/delete_crm_attachment'); ?>",
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

        function contact_tasks() {
            var contactID = $('#editcontactID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {contactID: contactID},
                url: "<?php echo site_url('crm/load_contact_all_tasks'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_all_tasks').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function link_opportunities() {
            var contactID = $('#editcontactID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {contactID: contactID},
                url: "<?php echo site_url('crm/load_contact_all_opportunities'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_all_opportunities').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
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
                profileImageUploadContact();
            }
        }

        function profileImageUploadContact() {
            var imgageVal = new FormData();
            imgageVal.append('contactID', $('#editcontactID').val());

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
                url: "<?php echo site_url('crm/contact_image_upload'); ?>",
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
                        url: "<?php echo site_url('crm/delete_master_notes_allDocuments'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Note Deleted Successfully');
                                contact_notes();
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

        function edit_note(notesID) {
            $('#show_all_notes').addClass('hide');
            $('#show_add_notes_button').addClass('hide');
            $('#show_add_notes').removeClass('hide');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'notesID': notesID},
                url: "<?php echo site_url('Crm/edit_master_notes_allDocuments'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#addBtn_type').html('Update');
                        $('#edit_notesID').val(data['notesID']);
                        $("#note_description").wysihtml5();
                        $('#note_description').val(data['description']);
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

    </script>


