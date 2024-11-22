<style>
    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .flex {
        display:
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$CommitteeAreawiseID = $committeeMas['CommitteeAreawiseID'];
$subCommitteeDes = $committeeMas['CommitteeAreawiseDes'] .'  '.'&nbsp;';
$subCommitteeDes .= '<button class="btn btn-default btn-sm" style="" onclick="edit_subCommittee('. $CommitteeAreawiseID.')"><span title="Edit" style="color:#70adff;height:10px;width:10px;" rel="tooltip" class="glyphicon glyphicon-pencil "></span></button>' .'  '.'&nbsp;';
$subCommitteeDes .= '<button class="btn btn-default btn-sm" style="float: right;" onclick="generate_subCommitteePdf()"><span title="Print" style="color:#70adff;height:10px;width:10px;" rel="tooltip" class="glyphicon glyphicon-print"></span></button>';

$CommitteeID = $committeeMas['CommitteeID'];

echo head_page( $subCommitteeDes , false);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$subCommitteeDes2 = $committeeMas['CommitteeAreawiseDes'];
$SubAreaId = $committeeMas['SubAreaId'];
$CommitteeHeadID = $committeeMas['CommitteeHeadID'];
$CName_with_initials = $committeeMas['CName_with_initials'];
$startDatet = $committeeMas['startDatet'];
$endDatet = $committeeMas['endDatet'];
$stateID = $committeeMas['stateID'];
$stDescription = $committeeMas['stDescription'];

$this->load->helper('community_ngo_helper');
$com_positn = fetch_committee_postitn();

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$companyID = current_companyID();
?>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/committees.css'); ?>">
<div class="row">
    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div class="arrow-steps clearfix">
                        <div class="step" style="font-size:9px;"><span style="font-weight: bold;"><?php echo $this->lang->line('communityngo_region') .': '; ?></span><span><?php echo $stDescription; ?></span></div>
                        <div class="step" style="font-size:9px;"><span style="font-weight: bold;"><?php echo 'Head : '; ?></span><span><?php echo $CName_with_initials; ?></span></div>
                        <div class="step" style="font-size:9px;"><span style="font-weight: bold;"><?php echo $this->lang->line('CommunityNgo_famAddedDate') .': '; ?></span><span><?php echo $startDatet; ?></span><span style="font-weight: bold;"><?php if ($endDatet){echo 'Expiry : '; ?></span><span><?php echo $endDatet; } ?></span></div>
                    </div>
                    <br>

                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $subCommitteeDes2 .': '. $this->lang->line('communityngo_CommitteeAreaWise');?>
                        </div><!--Committees Area Wise-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>
                    <?php if(!empty($SubAreaId) && !empty($CommitteeHeadID)){ ?>
                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <form method="post" name="form_subComite" id="form_subComite" class="">

                                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

                                    <table id="fetchCmteCondent" class="table ">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('communityngo_CommitteeMem');?> </th><!--Committee Mem-->
                                            <th><?php echo $this->lang->line('communityngo_CommitPosition');?></th><!--Position-->
                                            <th><?php echo $this->lang->line('communityngo_CommitJoinDate');?> </th><!--Joined Date-->
                                            <th><?php echo $this->lang->line('communityngo_com_member_header_Status');?>  </th><!--Status-->
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                        <tfoot>
                                        <input name="CommitteeID" type="hidden" value="<?php echo $CommitteeID; ?>">
                                        <input name="CommitteeAreawiseID" id="CommitteeAreawiseID" type="hidden" value="<?php echo $CommitteeAreawiseID; ?>">
                                        <tr>
                                            <td></td>
                                            <td colspan="">
                                                <select id="Com_MasterID" class="form-control select2" name="Com_MasterID">
                                                    <option data-currency=""
                                                            value="">Select Member</option>
                                                    <?php

                                                    $query= $this->db->query("SELECT Com_MasterID,CName_with_initials FROM srp_erp_ngo_com_communitymaster WHERE companyID='".$companyID."' AND RegionID ='".$stateID."'");
                                                    $com_masters = $query->result();
                                                    if (!empty($com_masters)) {
                                                        foreach ($com_masters as $val) {
                                                            ?>
                                                            <option value="<?php echo $val->Com_MasterID; ?>"><?php echo $val->CName_with_initials; ?></option>
                                                            <?php

                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td style="text-align: center">
                                                <select id="CommitteePositionID" class="form-control select2" name="CommitteePositionID">
                                                    <option data-currency=""
                                                            value="">Select Position</option>
                                                    <?php
                                                    if (!empty($com_positn)) {
                                                        foreach ($com_positn as $val) {
                                                            ?>
                                                            <option value="<?php echo $val['CommitteePositionID'] ?>"><?php echo $val['CommitteePositionDes'] ?></option>
                                                            <?php

                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td style="text-align: center">
                                                <div class="input-group datepic">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input onchange="$('#joinedDate').val(this.value);" type="text" name="joinedDate"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                           value="<?php echo $current_date; ?>" id="joinedDate" class="form-control" required>
                                                </div>
                                            </td>
                                            <td style="text-align: center"><input name="isMemActive" type="hidden" value="0"> <input class="" id="isMemActive" name="isMemActive" type="checkbox" value="1" checked>
                                            </td>
                                            <td colspan=""><a onclick="submitSubCmntMem();" id="AddNewPipeline" class="btn btn-primary btn-xs CA_Alter_btn"><?php echo $this->lang->line('communityngo_CommitAddMems');?></a></td><!--Add Member-->
                                        </tr>

                                        </tfoot>

                                    </table>
                                </form>
                            </div>
                        </article>
                    </div>
                   <?php } else{?>
                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">
                                <?php if(!empty($SubAreaId) && empty($CommitteeHeadID)){ ?>
                                <span style="color: red;font-size: 13px;">Can not add the members until assign the head of the committee for <?php echo $subCommitteeDes2; ?> </span>
                                <?php } else{ ?>
                                <span style="color: red;font-size: 13px;">Can not add the members until assign the area and head of the committee for <?php echo $subCommitteeDes2; ?> </span>
                                <?php } ?>
                            </div>
                        </article>
                    </div>
                   <?php } ?>
                </div>
            </div>
        </section>
    </div>

    <script>

        $(document).ready(function () {

            //control_staff_access(0, 'system/communityNgo/ngo_mo_committeesMaster', 0);

            cmt_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (cmt_id) {
                CommitteeAreawiseID = cmt_id;

            }

            $('.headerclose').click(function () {
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });

        fetch_comtMembers();
        function fetch_comtMembers() {
            var Otable = $('#fetchCmteCondent').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_SubCmnt_members'); ?>",
                "aaSorting": [[1, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                },

                "columnDefs": [

                    {"width": "10%", "targets": 5}
                ],
                "aoColumns": [
                    {"mData": "CommitteeMemID"},
                    {"mData": "CName_with_initials"},
                    {"mData": "CommitteePositionDes"},
                    {"mData": "joinedDate"},
                    {"mData": "isMemActive"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});

                    aoData.push({ "name": "CommitteeAreawiseID","value": $("#CommitteeAreawiseID").val()});

                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }
        function submitSubCmntMem() {

            var data = $('#form_subComite').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_SubCmntMem'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    $('#Com_MasterID').val('').change();
                    $('#CommitteePositionID').val('').change();
                    $('#joinedDate').val('').change();
                    $('#isMemActive').attr('checked', true);

                    fetch_comtMembers();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_SubCmntMem(CommitteeMemID) {

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'CommitteeMemID': CommitteeMemID},
                        url: "<?php echo site_url('CommunityNgo/delete_SubCmntMem'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_comtMembers();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function generate_subCommitteePdf() {

            var form = document.getElementById('form_subComite');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#form_subComite').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/get_subCommitteeState_pdf'); ?>';
            form.submit();

        }

    </script>

