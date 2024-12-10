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
$subCom =  $committeeMas['CommitteeAreawiseDes'];

echo head_page( $subCom , false);

$CommitteeMemID =  $committeeMas['CommitteeMemID'];
$ComtMemName =  $committeeMas['CName_with_initials'];
$CommitteePositionID =  $committeeMas['CommitteePositionID'];
$joinedDatet =  $committeeMas['joinedDatet'];
$expDatet =  $committeeMas['expiryDatet'];
$isMemActive =  $committeeMas['isMemActive'];
$committeeMemRemark =  $committeeMas['committeeMemRemark'];
?>

<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> Edit Member Details
                        </div>
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <div class="further-link">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    <a onclick="redirect_cmtMemPage(1,<?php echo $CommitteeAreawiseID; ?>,<?php echo $CommitteeID; ?>)"><strong> <?php echo $subCom .' :'. $ComtMemName; ?> </strong></a>
                                </div>

<br>
                                <div id="settingsContainer">
                                    <form id="form_editComtMem">
                                        <input name="editCommitteeMemID" id="editCommitteeMemID" type="hidden" value="<?php echo $CommitteeMemID; ?>">
                                        <input name="editCommitteeID" id="editCommitteeID" type="hidden" value="<?php echo $CommitteeID; ?>">
                                        <input name="editCommitteeAreawiseID" id="editCommitteeAreawiseID" type="hidden" value="<?php echo $CommitteeAreawiseID; ?>">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-4" style="">
                                                    <label><?php echo $this->lang->line('communityngo_CommitteeMem');?><!--Committee Mem--> <?php required_mark(); ?></label>
                                                    <select id="editCom_MasterID" class="form-control select2" name="editCom_MasterID">
                                                        <option data-currency=""
                                                                value="">Select Member</option>
                                                        <?php

                                                        $query= $this->db->query("SELECT srp_erp_ngo_com_committeemembers.Com_MasterID,CName_with_initials FROM srp_erp_ngo_com_committeemembers INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeemembers.Com_MasterID WHERE srp_erp_ngo_com_committeemembers.companyID='".$companyID."' AND srp_erp_ngo_com_committeemembers.CommitteeMemID ='".$CommitteeMemID."'");
                                                        $com_masters = $query->result();
                                                        if (!empty($com_masters)) {
                                                            foreach ($com_masters as $val) {
                                                                ?>
                                                                <option value="<?php echo $val->Com_MasterID; ?>" selected><?php echo $val->CName_with_initials; ?></option>
                                                                <?php

                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-4" style="">
                                                    <label><?php echo $this->lang->line('communityngo_CommitPosition');?><!--Position--> <?php required_mark(); ?></label>
                                                    <select id="editCommtPosID" class="form-control select2" name="editCommtPosID">
                                                        <option data-currency=""
                                                                value="">Select Position</option>
                                                        <?php
                                                        if (!empty($com_positn)) {
                                                            foreach ($com_positn as $val) {

                                                                if ($val['CommitteePositionID'] == $CommitteePositionID) {
                                                                    ?>
                                                                    <option value="<?php echo $val['CommitteePositionID'] ?>" selected="selected"><?php echo $val['CommitteePositionDes'] ?></option>

                                                                    <?php

                                                                }
                                                                else {
                                                                    ?>

                                                                    <option value="<?php echo $val['CommitteePositionID'] ?>"><?php echo $val['CommitteePositionDes'] ?></option>

                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-4" style="">
                                                    <label for=""><?php echo $this->lang->line('communityngo_CommitJoinDate');?> <!--Joined Date--> <?php required_mark(); ?></label>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input onchange="$('#editjoinedDate').val(this.value);" type="text" name="editjoinedDate"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $joinedDatet; ?>" id="editjoinedDate" class="form-control" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-4">
                                                    <label for=""><?php echo $this->lang->line('communityngo_ExpiryDate');?><!--Expiry Date--> <?php required_mark(); ?></label>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input onchange="$('#editExpDate').val(this.value);" type="text" name="editExpDate"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $expDatet; ?>" id="editExpDate" class="form-control" required>
                                                    </div>

                                                </div>
                                                <div class="form-group col-sm-2">
                                                    <label for=""><?php echo $this->lang->line('communityngo_com_member_header_Status');?> <!--Status--></label>
                                                    <br>
                                                    <input name="editMemActive" id="editMemActive" type="hidden" value="<?php echo $isMemActive;?>">
                                                    <?php if($isMemActive == '1'){?>
                                                        <input class="" id="editMemAct" name="editMemAct" type="checkbox" onchange="isMemAct_edit(this);" value="<?php echo $isMemActive;?>" checked>
                                                    <?php } else{?>

                                                        <input class="" id="editMemAct" name="editMemAct" type="checkbox" onchange="isMemAct_edit(this);" value="<?php echo $isMemActive;?>" >

                                                    <?php } ?>

                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for=""><?php echo $this->lang->line('communityngo_Remarks');?> <!--Remarks--></label>
                                                    <input type="text" step="any" class="form-control" id="memRemarks"
                                                           name="memRemarks" value="<?php echo $committeeMemRemark; ?>">

                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-8">
                                                    </div>
                                                <div class="form-group col-sm-4" style="float: right;">
                                                    <a onclick="submitEditMem();" style="float: right;" id="Save" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_save');?></a><!--Save-->

                                                </div>
                                            </div>
                                        </div>

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

        function isMemAct_edit(y){
            var yid= y.id;
            var vid= yid.replace(/[^\d.]/g, '');

            var f=document.getElementById('editMemAct').checked;

            if(f==true) {
                document.getElementById('editMemActive').value = 1;
            }if(f==false){
                document.getElementById('editMemActive').value = 0;
            }
        }

        function submitEditMem() {

            var data = $('#form_editComtMem').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_cmteMembrEdit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#editCom_MasterID').val('').change();
                        $('#editCommtPosID').val('').change();
                        $('#editjoinedDate').val('').change();
                        $('#editExpDate').val('').change();
                        $('#editMemActive').attr('checked', true);
                        redirect_cmtMemPage(1, ($('#editCommitteeAreawiseID').val()),($('#editCommitteeID').val()));
                    }

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>
<?php
