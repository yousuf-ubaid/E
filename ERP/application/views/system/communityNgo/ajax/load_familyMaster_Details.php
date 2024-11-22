<style>

    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
       /* font-weight: bold;*/
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
        color: #50749f;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #638bbe;
    }

    .textColor {
        color: #638bbe;
    }
</style>
<?php
if (!empty($master)) {

    if ($master['confirmedYN'] == 0){
        ?>
        <div class="row">
        <div class="col-md-5">
            &nbsp;
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right CA_Alter_btn"
        onclick="fetchPage('system/communityNgo/ngo_mo_familyCreate','<?php echo $master['FamMasterID'] ?>','Edit Family','Family Master')">
        <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
        Edit
    </button>
    </div>
    </div>
    <?php
      }
      ?>
    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-users"></i> Master</a></li>
        <li><a href="#files" onclick="famMaster_attachments()" data-toggle="tab"><i class="fa fa-paperclip"></i> Attachments
            </a></li>
    </ul>
    <input type="hidden" id="editCom_MasterID" value="<?php echo $master['Com_MasterID'] ?>">
    <div class="tab-content">
        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>HEAD OF THE FAMILY AND FAMILY MEMBERS DETAIL</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="table table-striped" id="profileInfoTable"
                           style="background-color: #ffffff;width: 100%">
                        <tbody>

                        <tr>
                            <td>
                                <strong class="textColor">Head Of The Family: </strong>
                            </td>
                            <td style="font-weight: bold;">
                                <?php echo $master['CName_with_initials']; ?>
                            </td>
                            <td>
                                <strong class="textColor">Full Name:</strong>
                            </td>
                            <td>
                                <?php echo $master['CFullName']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Area :</strong>
                            </td>
                            <td style="font-weight: bold;">
                                <?php echo $master['arDescription'] ?>
                            </td>
                            <td>
                                <strong class="textColor">Gender:</strong>
                            </td>
                            <td>
                                <?php echo $master['name'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Date of Birth :</strong>
                            </td>
                            <td>
                                <?php echo $master['CDOB'] ?>
                            </td>
                            <td>
                                <strong class="textColor">N.I.C :</strong>
                            </td>
                            <td>
                                <?php echo $master['CNIC_No']; ?>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Phone (Primary) :</strong>
                            </td>
                            <td>
                                <?php echo $master['TP_Mobile']; ?>
                            </td>
                            <td>
                                <strong class="textColor">Phone (Secondary) :</strong>
                            </td>
                            <td>
                                <?php echo $master['TP_home']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Contact Address :</strong>
                            </td>
                            <td>
                                <?php echo $master['C_Address'] ?>
                            </td>
                            <td>
                                <strong class="textColor">House No :</strong>
                            </td>
                            <td>
                                <?php echo $master['HouseNo'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong class="textColor">Email :</strong></td>
                            <td>
                                <?php echo $master['EmailID'] ?>
                            </td>
                            <td>
                                <strong class="textColor">Permanent Address :</strong>
                            </td>
                            <td>
                                <?php echo $master['P_Address'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Marital Status :</strong>
                            </td>
                            <td>
                                <?php
                                echo $master['maritalstatus'];
                                ?>
                            </td>
                            <td>
                                <strong class="textColor">GS Division :</strong>
                            </td>
                            <td>
                                <?php echo $master['diviDescription']. ' - ' .$master['GS_No'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="textColor">Reference No :</strong>
                            </td>
                            <td>
                                <?php echo $master['LedgerNo'] ?>
                            </td>
                            <td>
                                <strong class="textColor">Ledger No :</strong>
                            </td>
                            <td>
                                <?php echo $master['FamilySystemCode'] ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="fileinput-new thumbnail">
                        <?php if ($master['CImage'] != '') { ?>
                            <img src="<?php echo base_url('uploads/NGO/communitymemberImage/' . $master['CImage']); ?>"
                                 id="changeImg" style="width: 200px; height: 145px;">
                            <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 200px; height: 145px;">
                        <?php } ?>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <?php
            if (!empty($loadFamMem)) { ?>
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr class="task-cat-upcoming" style="color: black;font-weight: bold;">
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">#</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">MEMBER/S</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">GENDER</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">DATE OF BIRTH</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">RELATIONSHIP</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">MARITAL STATUS</td>
                            <td class="headrowtitle" style="border-bottom: solid 1px #50749f;">ADDED DATE</td>
                        </tr>
                        <?php
                        $x = 1;
                        $totalMem = 1;
                        foreach ($loadFamMem as $val) {

                            if($val['isMove'] ==1 ){ $moveStatus='<span onclick="get_memMoved_history('.$val['Com_MasterID'].','.$val['FamMasterID'].', \'' . $val['CName_with_initials']. '\');" style="width:8px;height:8px;font-size: 0.73em;float: right;background-color: #00a5e6; display:inline-block;color: #00a5e6;" title="Moved To Another Family">m</span>'; } else{ $moveStatus=''; }
                            if($val['isActive'] ==1 ){ $activeState=''; } else{
                                if($val['DeactivatedFor']==2){ $INactReson='Migrate';} else{$INactReson='Death';}
                                $activeState='<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color:red; display:inline-block;color: red;" title="The Member Is Inactive :'.$INactReson.'">a</span>';}
                            ?>
                            <tr>
                                <td class="mailbox-star" width=""><?php echo $x; ?></td>
                                <td class="mailbox-star" width=""><?php echo $val['CName_with_initials'] ."&nbsp;". $moveStatus ."&nbsp;&nbsp;".$activeState ?></td>
                                <td class="mailbox-star" width=""><?php echo $val['name'] ?></td>
                                <td class="mailbox-star" width=""><?php echo $val['CDOB'] ?> &nbsp;(<?php echo $val['Age'] ?>)</td>
                                <td class="mailbox-star" width=""><?php echo $val['relationship'] ?></td>
                                <td class="mailbox-star" width="">
                                    <?php
                                    echo $val['maritalstatus'];
                                    ?>
                                <td class="mailbox-star"><?php echo $val['FamMemAddedDate'] ?></td>
                            </tr>
                            <?php

                            $totMem = $totalMem++;
                            $x++;
                        }
                        ?>
                        </tbody>
                        <tfoot >
                        <tr>
                            <td style="" class="" colspan="7">Total Members : <?php echo $totMem; ?></td>
                        </tr>
                        </tfoot>
                    </table><!-- /.table -->
                </div>
                <?php
            } else { ?>
                <br>
                <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
                <?php
            }
            ?>
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
                        <strong class="textColor">Created Date :</strong>
                    </td>
                    <td>
                        <?php echo $master['createdDateTime'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <strong class="textColor">Family Created By :</strong>
                    </td>
                    <td>
                        <?php echo $master['createdUserName'] ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <strong class="textColor">Last Updated :</strong>
                    </td>
                    <td>
                        <?php echo $master['modifiedDateTime'] ?>
                    </td>
                    <td></td>
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
            <div class="row hide" id="add_attachemnt_show">
                <?php echo form_open_multipart('', 'id="attchment_Upload_form" class="form-inline"'); ?>
                <div class="col-sm-10" style="margin-left: 3%">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="contactattachmentDescription"
                                   name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                            <input type="hidden" class="form-control" id="documentID" name="documentID" value="7">
                            <input type="hidden" class="form-control" id="contact_documentAutoID" name="documentAutoID"
                                   value="<?php echo $master['Com_MasterID']; ?>">
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
                        <button type="button" class="btn btn-default" onclick="attchment_Upload()"><span
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


<div class="modal fade" id="mem_movedHistory_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="cmntModTitle">Member - <label style="font-size: 15px;font-weight: normal;" id="memDetail"></label></h4>
            </div>
            <div class="row modal-body">
                <label> &nbsp;&nbsp;&nbsp; <label class="glyphicon glyphicon-link"></label> Family Links</label>
                <div class="col-md-12" id="mem_movedId">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function attchment_Upload() {
        var formData = new FormData($("#attchment_Upload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('CommunityNgo/familyMaster_attachement_upload'); ?>",
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
                    famMaster_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function famMaster_attachments() {
        var Com_MasterID = $('#editCom_MasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {Com_MasterID: Com_MasterID},
            url: "<?php echo site_url('CommunityNgo/load_family_attachments'); ?>",
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
                    url: "<?php echo site_url('CommunityNgo/delete_family_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            famMaster_attachments();
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



    function get_memMoved_history(Com_MasterID,FamMasterID,CName_with_initials) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'Com_MasterID':Com_MasterID,'FamMasterID':FamMasterID},
            url: "<?php echo site_url('CommunityNgo/load_memberMovedHis'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#mem_movedHistory_modal').modal({backdrop: "static"});
                $('#memDetail').html(CName_with_initials);
                $('#mem_movedId').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

</script>

<?php
