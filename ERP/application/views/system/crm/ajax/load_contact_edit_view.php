
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
        color: #828282;
        padding: 4px 10px 0 0;
        font-size: 13px;
        font-weight: bold;
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


    .btn-filemultiup {
        position: relative;
        overflow: hidden;
    }

    .multipleattachmentbtn {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>
<?php
if (!empty($header)) {
//print_r($header);
?>
<div class="row">
    <div class="col-md-1">
        <div>
            <?php


            if ($header['contactImage'] != '') { ?>

                <img style="width: 40px; height: 40px;"  src="<?php echo $contactimage ?>" alt="Contact">

                <?php
            } else { ?>

                <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo $noimage ?>">
            <?php } ?>
        </div>
    </div>
    <div class="col-md-8" style="padding: 0px;">
        <div id="obj-type">Contact</div>
        <h1 style="margin-top: 4px;font-size: 15px;"><?php echo $header['firstName'] . " " . $header['lastName'] . " - " . $header['documentSystemCodecontact']; ?> </h1>
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/crm/create_contact',<?php echo $header['contactID'] ?>,'Edit Contact','CRM');">
            <span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
        </button>
    </div>
</div>
<br>
<ul class="nav nav-tabs" id="main-tabs">
    <?php if(($page == 'ContactTask') || ($page == 'opportunitiecontact')){?>
    <li><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
    <?php }else {?>
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
    <?php }?>


    <li><a href="#emails" onclick="emailssentview()"  data-toggle="tab"><i class="fa fa-television"></i>Emails </a></li>
    <li><a href="#notes" onclick="contact_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a></li>
    <li><a href="#files" onclick="contact_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Files
        </a></li>
    <?php if($page == 'ContactTask'){?>
    <li class="active"><a href="#tasks" onclick="contact_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
    <?php }else {?>
        <li><a href="#tasks" onclick="contact_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
    <?php }?>
    <?php if($page == 'opportunitiecontact'){?>
    <li class="active"><a href="#opportunities" onclick="link_opportunities()" data-toggle="tab"><i class="fa fa-television"></i>Opportunities</a></li>
    <?php }else {?>
        <li><a href="#opportunities" onclick="link_opportunities()" data-toggle="tab"><i class="fa fa-television"></i>Opportunities</a></li>
    <?php }?>
</ul>
<input type="hidden" id="editcontactID" value="<?php echo $header['contactID'] ?>">
<div class="tab-content">
    <?php if(($page == 'ContactTask') || ($page == 'opportunitiecontact')){?>
    <div class="tab-pane" id="about">
    <?php }else {?>
        <div class="tab-pane active" id="about">
    <?php }?>
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
                <div class="fileinput-new">
                    <?php if ($header['contactImage'] != '') { ?>
                        <img src="<?php echo $contactimage ?>"
                             id="changeImg" style="width: 200px; height: 200px;border-radius: 100%;">
                        <?php
                    } else { ?>
                        <img src="<?php echo $noimage ?>" id="changeImg"
                             style="width: 200px; height: 200px;border-radius: 100%;">
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
                    <h2>PERMISSIONS</h2><!--PERMISSIONS-->
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Visibility</label><!--Visibility-->
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input id="isPermissionEveryone" type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          name="userPermission"
                                                                          value="1"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400">Everyone</label><!--Everyone-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionCreator" type="radio" data-caption="" class="columnSelected" value="2"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400">Only For Me</label><!--Only For Me-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionGroup" type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          onclick="leadPermission(3)"
                                                                          value="3"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400">Select a Group</label><!--Select a Group-->
                    </div>
                </div>
                <div class="row hide" id="show_groupPermission">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-4" style="margin-left: 2%;">
                        <?php echo form_dropdown('groupID', $groupmaster_arr, '', 'class="form-control" id="groupID"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionMultiple"
                                                                          type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          onclick="leadPermission(4)"
                                                                          value="4"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400">Select Multiple People</label><!--Select Multiple People-->
                    </div>
                </div>
                <div class="row hide" id="show_multiplePermission">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-4" style="margin-left: 2%;">
                        <?php echo form_dropdown('employees[]', $employees_arr, '', 'class="form-control select2" id="employeesID"  multiple="" style="z-index: 0;"'); ?>
                    </div>
                </div>
                </form>
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
    </div>
    <div class="tab-pane" id="emails">
        <br>

        <div class="row">
            <div class="col-sm-12">
               <!-- <button type="button" onclick="compose_email_contact()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Compose Email
                </button>-->
                <div class="emailssent">

                    </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="notes">
        <br>

        <div class="row" id="show_add_notes_button">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Contact Notes </h4></div>
            <div class="col-md-4">
                <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Add Note
                </button>
            </div>
        </div>
        <br>
        <?php echo form_open('', 'role="form" id="frm_contact_add_notes"'); ?>
        <input type="hidden" name="contactID" value="<?php echo $header['contactID']; ?>">
        <input type="hidden" name="notesID" id="edit_notesID">

        <div id="show_add_notes" class="hide">
            <div class="row">
                <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="note_description"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <button class="btn btn-primary" type="submit" id="addBtn_type">Add</button>
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
    <div class="tab-pane" id="files">
        <br>

        <div class="row" id="show_add_files_button">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Contact Files </h4></div>
            <div class="col-md-4">
                <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Add Files
                </button>
            </div>
        </div>
        <div class="row hide" id="add_attachemnt_show">
            <?php echo form_open_multipart('', 'id="contact_attachment_uplode_form" class="form-inline"'); ?>
            <div class="col-sm-10" style="margin-left: 3%">
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="contactattachmentDescription"
                               name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                        <input type="hidden" class="form-control" id="documentID" name="documentID" value="6">
                        <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                               value="Contact">
                        <input type="hidden" class="form-control" id="contact_documentAutoID" name="documentAutoID"
                               value="<?php echo $header['contactID']; ?>">
                    </div>
                </div>
                <div class="col-sm-8" style="margin-top: -8px;">
                    <div class="form-group">
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
                    <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                            class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                    </form>
                </div>
            </div>

        </div>
        <br>

        <div id="show_all_attachments"></div>
    </div>
        <?php if($page == 'ContactTask'){?>
        <div class="tab-pane active" id="tasks">
            <?php }else {?>
            <div class="tab-pane" id="tasks">
                <?php }?>

        <br>

        <div id="show_all_tasks"></div>
    </div>
            <?php if($page == 'opportunitiecontact'){?>
         <div class="tab-pane active" id="opportunities">
        <?php }else {?>
        <div class="tab-pane" id="opportunities">
                <?php }?>
        <br>

        <div class="row">
            <div class="col-sm-12">
                <div id="show_all_opportunities"></div>
            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
               id="emailcomposemodel">
        <div class="modal-dialog" style="width: 59%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="usergroup-title">Compose Email</h4>
                </div>
                <?php echo form_open('', 'role="form" id="emailcomposemasterform"'); ?>
                <div class="modal-body">
                    <input type="hidden" id="contactid" name="contactid" value="<?php echo $header['contactID']; ?>">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>To</label>
                            <input class="form-control" placeholder="To:" name="To" autocomplete="off" value="<?php echo $header['contactEmail'];?>" readonly>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>cc</label>
                            <input class="form-control" placeholder="cc:" name="cc" autocomplete="off">
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Bcc</label>
                            <input class="form-control" placeholder="Bcc:" name="Bcc" autocomplete="off">
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Subject</label>
                            <input class="form-control" placeholder="Subject:" name="Subject" autocomplete="off">
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Body</label>
                            <textarea id="compose-textarea" name="compose-textarea"  class="form-control customerTypeDescription" style="height: 300px"></textarea>
                        </div>

                    </div>
                    <br>
                    <div class="row" >
                        <div class="form-group col-sm-6 files" id="files3">
                        <span class="btn btn-default btn-filemultiup">
                               Attachment  <input type="file" name="files3" class="multipleattachmentbtn" multiple/>
                             </span>
                            <br/>
                            <ul class="fileList"></ul>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                                   aria-hidden="true"></span> Send
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php
    }
    ?>

    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            <?php if($page == 'ContactTask'){?>
            contact_tasks();
            <?php }?>
            <?php  if($page == 'opportunitiecontact'){?>
            link_opportunities();
            <?php } ?>
            loadcontactheader();

            $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
                var fileIdCounter = 0;

                this.closest(".files").change(function (evt) {
                    var output = [];

                    for (var i = 0; i < evt.target.files.length; i++) {
                        fileIdCounter++;
                        var file = evt.target.files[i];
                        var fileId = sectionIdentifier + fileIdCounter;

                        filesToUpload.push({
                            id: fileId,
                            file: file
                        });

                        var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

                        output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
                    };

                    $(this).children(".fileList")
                        .append(output.join(""));

                    //reset the input to null - nice little chrome bug!
                    evt.target.value = null;
                });

                $(this).on("click", ".removeFile", function (e) {
                    e.preventDefault();

                    var fileId = $(this).parent().children("a").data("fileid");

                    // loop through the files array and check if the name of that file matches FileName
                    // and get the index of the match
                    for (var i = 0; i < filesToUpload.length; ++i) {
                        if (filesToUpload[i].id === fileId)
                            filesToUpload.splice(i, 1);
                    }

                    $(this).parent().remove();
                });

                this.clear = function () {
                    for (var i = 0; i < filesToUpload.length; ++i) {
                        if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                            filesToUpload.splice(i, 1);
                    }

                    $(this).children(".fileList").empty();
                }

                return this;
            };
            var filesToUpload = [];
            var files3Uploader = $("#files3").fileUploader(filesToUpload, "files3");



            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-purple',
                radioClass: 'iradio_square_relative-purple',
                increaseArea: '20%'
            });
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

            $('#emailcomposemasterform').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    to: {validators: {notEmpty: {message: 'To is required'}}},/*To is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                tinymce.triggerSave();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');

                var data = new FormData($("#emailcomposemasterform")[0]);
                for (var i = 0, len = filesToUpload.length; i < len; i++) {
                    data.append("photo[]", filesToUpload[i].file);
                }
                /* $.each($(".email_attachment"), function(i, obj) {
                 $.each(obj.files,function(i,file){
                 var push =  data.append('photo[]', file);
                 array_push($a);
                 });
                 });*/
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CrmMailBox/compose_email_contact'); ?>",
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
                            $('.btn-primary').prop('disabled', false);
                            emailssentview();
                            files3Uploader.clear();
                            $('#emailcomposemodel').modal('hide');
                        } else {
                           $('.btn-primary').prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });


            tinymce.init({
                selector: ".customerTypeDescription",
                height: 200,
                browser_spellcheck: true,
                plugins: [
                    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
                ],
                toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
                toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
                toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

                menubar: false,
                toolbar_items_size: 'small',

                style_formats: [{
                    title: 'Bold text',
                    inline: 'b'
                }, {
                    title: 'Red text',
                    inline: 'span',
                    styles: {
                        color: '#ff0000'
                    }
                }, {
                    title: 'Red header',
                    block: 'h1',
                    styles: {
                        color: '#ff0000'
                    }
                }, {
                    title: 'Example 1',
                    inline: 'span',
                    classes: 'example1'
                }, {
                    title: 'Example 2',
                    inline: 'span',
                    classes: 'example2'
                }, {
                    title: 'Table styles'
                }, {
                    title: 'Table row 1',
                    selector: 'tr',
                    classes: 'tablerow1'
                }],

                templates: [{
                    title: 'Test template 1',
                    content: 'Test 1'
                }, {
                    title: 'Test template 2',
                    content: 'Test 2'
                }]
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
        function loadcontactheader()
        {
            var contactid = <?php echo $header['contactID'] ?>;
            if (contactid) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'contactID': contactid},
                    url: "<?php echo site_url('Crm/load_contact_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                    //$('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    //$('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    //$('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    //$('#isPermissionMultiple').iCheck('disable');
                                }
                            });
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }
        function emailssentview() {
            var contactid = <?php echo $header['contactID'] ?>;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'contactID': contactid},
                url: "<?php echo site_url('CrmMailbox/contactwise_emails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                  $('.emailssent').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function compose_email_contact()
        {
            $('#emailcomposemasterform')[0].reset();
            $('#emailcomposemasterform').bootstrapValidator('resetForm', true);
            $('#emailcomposemodel').modal('show');
        }

        function readFile(input) {
            $("#status").html('Processing...');
            counter = input.files.length;
            for(x = 0; x<counter; x++){
                if (input.files && input.files[x]) {

                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $("#photos").append('<div class="col-md-3 col-sm-3 col-xs-3"><img src="<?php echo base_url("images/crm/emailattachment.png")?>" class="img-thumbnail"></div>');
                    };

                    reader.readAsDataURL(input.files[x]);
                }
            }
            if(counter == x){$("#status").html('');}

        }



    </script>

