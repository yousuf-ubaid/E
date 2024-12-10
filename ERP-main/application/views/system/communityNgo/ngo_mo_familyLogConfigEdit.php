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

$FamMasterID = $famLogMas['FamMasterID'];
$LeaderID = $famLogMas['LeaderID'];
$familyDel=  $famLogMas['FamilySystemCode'].' :'.$famLogMas['FamilyName'];

echo head_page( $familyDel, false);

$FamUsername =  $famLogMas['FamUsername'];
$FamPassWord =  $famLogMas['FamPassword'];
$FamLogCreatedDt =  $famLogMas['FamLogCreatedDt'];
$isLoginActive =  $famLogMas['isLoginActive'];

?>

<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> Edit Family Login Config
                        </div>
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <div class="further-link">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    <a onclick="redirect_famLogConfPage(1,<?php echo $FamMasterID; ?>,<?php echo $LeaderID; ?>)"><strong> <?php echo $familyDel; ?> </strong></a>
                                </div>

                                <br>
                                <div id="settingsContainer">
                                    <form id="form_editFamLog">
                                        <input name="editFamMasterID" id="editFamMasterID" type="hidden" value="<?php echo $FamMasterID; ?>">
                                        <input name="editLeaderID" id="editLeaderID" type="hidden" value="<?php echo $LeaderID; ?>">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-4" style="">
                                                    <label><?php echo $this->lang->line('communityngo_famLogUsername');?><!--Username--> <?php required_mark(); ?></label>
                                                    <br>
                                                    <input name="editFamUserName" id="editFamUserName" type="text" style="width: 100%;" value="<?php echo $FamUsername; ?>">

                                                </div>
                                                <div class="form-group col-sm-4" style="">
                                                    <label><?php echo $this->lang->line('communityngo_famLogPassword');?><!--Password--> <?php required_mark(); ?></label>
                                                    <br>
                                                    <input name="editFamPassWord" id="editFamPassWord" type="text" style="width: 100%;" value="<?php echo $FamPassWord; ?>">

                                                </div>
                                                <div class="form-group col-sm-4">

                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-4" style="">
                                                    <label for=""><?php echo $this->lang->line('communityngo_CommitJoinDate');?> <!--Joined Date--> <?php required_mark(); ?></label>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input onchange="$('#editCreatedDate').val(this.value);" type="text" name="editCreatedDate"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $FamLogCreatedDt; ?>" id="editCreatedDate" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group col-sm-4">
                                                    <label for=""><?php echo $this->lang->line('communityngo_com_member_header_Status');?> <!--Status--></label>
                                                    <br>
                                                    <input name="editLogActive" id="editLogActive" type="hidden" value="<?php echo $isLoginActive;?>">
                                                   <?php if($isLoginActive == '1'){?>
                                                    <input class="" id="editLogAct" name="editLogAct" type="checkbox" onchange="isActive_edit(this);" value="<?php echo $isLoginActive;?>" checked>
                                                    <?php } else{?>

                                                       <input class="" id="editLogAct" name="editLogAct" type="checkbox" onchange="isActive_edit(this);" value="<?php echo $isLoginActive;?>" >

                                                   <?php } ?>
                                                </div>
                                                <div class="form-group col-sm-4">

                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-sm-4">
                                                </div>
                                                <div class="form-group col-sm-4" style="">
                                                    <a onclick="submitEditFamLog();" style="float: right;" id="Save" class="btn btn-primary btn-xxs"><?php echo $this->lang->line('common_save');?></a><!--Save-->

                                                </div>
                                                <div class="form-group col-sm-4">
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

                redirect_famLogConfPage(1, ($('#editFamMasterID').val()),($('#editLeaderID').val()));
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });

        function isActive_edit(y){
            var yid= y.id;
            var vid= yid.replace(/[^\d.]/g, '');

            var f=document.getElementById('editLogAct').checked;

            if(f==true) {
                document.getElementById('editLogActive').value = 1;
            }if(f==false){
                document.getElementById('editLogActive').value = 0;
            }
        }

        function submitEditFamLog() {

            var data = $('#form_editFamLog').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_famLogEdit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#editFamUserName').val('').change();
                        $('#editFamPassWord').val('').change();
                        $('#editCreatedDate').val('').change();
                        $('#editLogActive').attr('checked', true);
                        redirect_famLogConfPage(1, ($('#editFamMasterID').val()),($('#editLeaderID').val()));
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
