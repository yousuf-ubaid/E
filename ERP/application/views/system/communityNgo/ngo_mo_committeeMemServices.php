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

    .bottom20 {
        margin-bottom: 20px;
    }

    section.block-pipeline {
        margin-top: 10px;
        margin-bottom: 20px
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/commtNgo_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/committees.css'); ?>">
<?php

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$companyID = current_companyID();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');
$com_positn = fetch_committee_postitn();

$CommitteeAreawiseID = $committeeMas['CommitteeAreawiseID'];
$CommitteeID = $committeeMas['CommitteeID'];
$isMemActive =  $committeeMas['isMemActive'];
if($isMemActive == 1){
    $memState ='';
}
else{
    $memState ='Inactive Member';
}
$subCom2 =  $committeeMas['CommitteeAreawiseDes'].'  '.'&nbsp;';
$subCom2 .=  '<label style="color: red;">'.$memState.'</label>';

echo head_page($subCom2 , false);

$subCom =  $committeeMas['CommitteeAreawiseDes'];
$CommitteeMemID =  $committeeMas['CommitteeMemID'];
$ComtMemName =  $committeeMas['CName_with_initials'];
$CommitteePositionID =  $committeeMas['CommitteePositionID'];
$joinedDatet =  $committeeMas['joinedDatet'];
$expDatet =  $committeeMas['expiryDatet'];
$committeeMemRemark =  $committeeMas['committeeMemRemark'];

?>

<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> Member Services
                        </div><!--Member Services-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <div class="further-link">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    <a onclick="redirect_cmtMemPage(1,<?php echo $CommitteeAreawiseID; ?>,<?php echo $CommitteeID; ?>)"><strong> <?php echo $subCom .' : '.$ComtMemName; ?> </strong></a>
                                </div>


                                <div id="settingsContainer">

                                    <br>

                                    <form id="form_cmtMemServices">
                                        <input name="editCommitteeMemID" id="editCommitteeMemID" type="hidden" value="<?php echo $CommitteeMemID; ?>">
                                        <input name="editCommitteeID" id="editCommitteeID" type="hidden" value="<?php echo $CommitteeID; ?>">
                                        <input name="editCommitteeAreawiseID" id="editCommitteeAreawiseID" type="hidden" value="<?php echo $CommitteeAreawiseID; ?>">

                                        <table id="fetchCmteCondent" class="table ">
                                            <thead>
                                            <tr>
                                                <th>#</th>

                                                <th><?php echo $this->lang->line('communityngo_ComitMemService');?> </th><!--Member Service-->
                                                <th><?php echo $this->lang->line('common_date');?> </th><!--Date-->

                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                           <?php if($isMemActive == 1){ ?>
                                            <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <input type="hidden" value="<?php echo $CommitteeMemID ?>"
                                                           name="masterID">
                                                    <input class="text" id="CmtMemService" name="CmtMemService"
                                                           placeholder="Member Service" type="text" value=""></td><!--Member Service-->
                                                <td>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input onchange="$('#ServiceDate').val(this.value);" type="text" name="ServiceDate"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $current_date; ?>" id="ServiceDate" class="form-control" required>
                                                    </div>
                                                </td>


                                                <td colspan=""><a onclick="submitMemService();" id="AddNewPipeline"
                                                                  class="btn btn-primary btn-xs CA_Alter_btn"><?php echo $this->lang->line('common_save'); ?> </a></td><!--Save Service-->
                                            </tr>

                                            </tfoot>
                                         <?php } ?>
                                        </table>
                                    </form>


                                </div>


                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>

        $(document).ready(function () {

            //control_staff_access(0, 'system/communityNgo/ngo_mo_committeesMaster', 0);

            $('.headerclose').click(function () {

                redirect_cmtMemPage(1, ($('#editCommitteeAreawiseID').val()),($('#editCommitteeID').val()));
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });
        
        fetch_cmtMemService();

        function fetch_cmtMemService() {

            var Otable = $('#fetchCmteCondent').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_comiteMem_service'); ?>",
                "aaSorting": [[0, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    /*  if (oSettings.bSorted || oSettings.bFiltered) {
                     for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                     $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                     }
                     }*/
                },
                "columnDefs": [

                    {"width": "2%", "targets": 3}

                ],
                "aoColumns": [
                    {"mData": "sortOrder"},
                    {"mData": "CmtMemService"},
                    {"mData": "ServiceDate"},
                    {"mData": "edit"}

                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "masterID", "value": <?php echo $CommitteeMemID ?>});
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

        function memService_update(CmtMemServiceID) {

            sortOrderID = $('#order_' + CmtMemServiceID).val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    masterID:<?php echo $CommitteeMemID ?>,
                    CmtMemServiceID: CmtMemServiceID,
                    CmtMemService: $('#cmtmemservice_' + CmtMemServiceID).val(),
                    ServiceDate: $('#servicedate_' + CmtMemServiceID).val(),
                    sortOrder: sortOrderID,
                },
                url: "<?php echo site_url('CommunityNgo/save_comiteMemService'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#cmtmemservice_' + CmtMemServiceID).val('');
                        $('#servicedate_' + CmtMemServiceID).val('');
                    }

                    fetch_cmtMemService();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function submitMemService() {

            var data = $('#form_cmtMemServices').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_comiteMemService'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#CmtMemService').val('');
                        $('#ServiceDate').val('');
                    }

                    fetch_cmtMemService();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function edit_memService(CmtMemServiceID) {
            $('.updatediv').addClass('hide');
            $('.canceldiv').removeClass('hide');

            $('.showinput').removeClass('hide');
            $('.hideinput').addClass('hide');
            $('.xxx_' + CmtMemServiceID).removeClass('hide');
            $('.xx_' + CmtMemServiceID).addClass('hide');
            $('#editmemService_' + CmtMemServiceID).addClass('hide');
            $('#updateMemService_' + CmtMemServiceID).removeClass('hide');

        }

        function memService_cancel(CmtMemServiceID) {

            $('.updatediv').addClass('hide');
            $('.canceldiv').removeClass('hide');

            $('.showinput').removeClass('hide');
            $('.hideinput').addClass('hide');
        }

        function delete_memService(CmtMemServiceID) {
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
                        data: {'CmtMemServiceID': CmtMemServiceID},
                        url: "<?php echo site_url('CommunityNgo/delete_comiteMemService'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_cmtMemService();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    </script>

<?php
