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
$FamMasterID = $famLogMas['FamMasterID'];
$famLogConfigDes = $famLogMas['FamilyName'].' '.'&nbsp;';
$famLogConfigDes .= '<a target="_blank" href="'. site_url('CommunityNgo/load_community_family_confirmation/') . '/' . $FamMasterID .'"  ><span title="Print" style="color:#70adff;height:8px;width:8px;" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

$LeaderID = $famLogMas['LeaderID'];

echo head_page( $famLogConfigDes , false);

$famLogConfigDes2 = $famLogMas['FamilySystemCode'];
$RegionID = $famLogMas['RegionID'];
$LeaderID = $famLogMas['LeaderID'];
$TP_Mobile = $famLogMas['TP_Mobile'];
$FamUsername = $famLogMas['FamUsername'];
$CName_with_initials = $famLogMas['CName_with_initials'];
$FamilyAddedDt = $famLogMas['FamilyAddedDt'];
$stateID = $famLogMas['stateID'];
$stDescription = $famLogMas['stDescription'];

$this->load->helper('community_ngo_helper');

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
                        <div class="step" style="font-size:9px;"><span style="font-weight: bold;"><?php echo $this->lang->line('CommunityNgo_famAddedDate') .': '; ?></span><span><?php echo $FamilyAddedDt; ?></span></div>
                    </div>
                    <br>

                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $famLogConfigDes2 .': '. $this->lang->line('communityngo_famLoginConf');?>
                        </div><!--Committees Area Wise-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>
                    <?php if(!empty($RegionID) && !empty($LeaderID)){ ?>
                        <div class="post-area">
                            <article class="page-content">

                                <div class="system-settings">
                                    <form id="form_pipeline">
                                        <table id="fetchCmteCondent" class="table ">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line('communityngo_famLogUsername');?> </th><!--Username-->
                                                <th><?php echo $this->lang->line('communityngo_famLogPassword');?></th><!--Password-->
                                                <th><?php echo $this->lang->line('communityngo_CreatedDate');?> </th><!--Created Date-->
                                                <th><?php echo $this->lang->line('communityngo_famLoginActive');?> </th><!--Login Active-->
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>


                                            <input name="LeaderID" type="hidden" value="<?php echo $LeaderID; ?>">
                                            <input name="FamMasterID" id="FamMasterID" type="hidden" value="<?php echo $FamMasterID; ?>">
                                            <?php if(!empty($FamMasterID) && empty($FamUsername)){ ?>
                                            <tr>
                                                <td></td>
                                                <td colspan="">
                                                    <input class="text" id="FamUsername" name="FamUsername" type="text" value="<?php echo $TP_Mobile; ?>">
                                                   </td>
                                                <td style="text-align: center">
                                                    <input class="text" id="FamPassword" name="FamPassword"
                                                           placeholder="<?php echo $this->lang->line('communityngo_famLogPassword');?>" type="text" value="">

                                                </td>
                                                <td style="text-align: center">
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input onchange="$('#FamLogCreatedDate').val(this.value);" type="text" name="FamLogCreatedDate"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $current_date; ?>" id="FamLogCreatedDate" class="form-control" required>
                                                    </div>
                                                </td>
                                                <td style="text-align: center"><input name="isLoginActive" type="hidden" value="0"> <input class="" id="isLoginActive" name="isLoginActive" type="checkbox" value="1" checked>
                                                </td>
                                                <td colspan=""><a onclick="submitFamLogDel();" id="AddNewPipeline" class="btn btn-primary btn-xs"><?php echo $this->lang->line('communityngo_famLogSubmit');?></a></td><!--Submit Log-->
                                            </tr>
                                            <?php } else{ ?>
                                                <tr><td colspan="6"></td></tr>
                                            <?php }  ?>
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
                                    <?php if(!empty($RegionID) && empty($LeaderID)){ ?>
                                        <span style="color: red;font-size: 13px;">Can not add the members until assign the head of the committee for <?php echo $famLogConfigDes2; ?> </span>
                                    <?php } else{ ?>
                                        <span style="color: red;font-size: 13px;">Can not add the members until assign the area and head of the committee for <?php echo $famLogConfigDes2; ?> </span>
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
            cmt_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (cmt_id) {
                FamMasterID = cmt_id;

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

        fetch_famLogDel();
        function fetch_famLogDel() {
            var Otable = $('#fetchCmteCondent').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_famLog_del'); ?>",
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
                    {"mData": "FamMasterID"},
                    {"mData": "FamUsername"},
                    {"mData": "FamPassword"},
                    {"mData": "FamLogCreatedDate"},
                    {"mData": "isLoginActive"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});

                    aoData.push({ "name": "FamMasterID","value": $("#FamMasterID").val()});

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
        function submitFamLogDel() {

            var data = $('#form_pipeline').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_famLogDel'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    $('#FamUsername').val('').change();
                    $('#FamPassword').val('').change();
                    $('#FamLogCreatedDate').val('').change();
                    $('#isLoginActive').attr('checked', true);

                    fetch_famLogDel();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_famLogDel(FamMasterID) {

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
                        data: {'FamMasterID': FamMasterID},
                        url: "<?php echo site_url('CommunityNgo/delete_famLogDel'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_famLogDel();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    </script>


<?php
