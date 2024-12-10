<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<?php
$date_format_policy = date_format_policy();
if (!empty($housingEnrl)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Housing Details', True, True);
            } ?>
        </div>
    </div>
    <div class="col-md-12 " id="communityRprt">
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <thead>
            <tr class="task-cat-upcoming" style="">
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 4%">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 12%"><?php echo $this->lang->line('CommunityNgo_famName'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 11%">Head Of The House</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 12%"><?php echo $this->lang->line('communityngo_contactAddress'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 12%"><?php echo $this->lang->line('communityngo_famOwnType'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 12%"><?php echo $this->lang->line('communityngo_famHouseType'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 10%"><?php echo $this->lang->line('communityngo_famHouseSizeInPrch'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 27%" colspan="6"><?php echo $this->lang->line('communityngo_famHouseFacilities'); ?></td>
            </tr>
            <tr>
                <td class="headrowtitle" colspan="7"></td>
                <td class="headrowtitle" style="font-size: 9px;">Electricity</td>
                <td class="headrowtitle" style="font-size: 9px;">Water Supply</td>
                <td class="headrowtitle" style="font-size: 9px;">Toilet</td>
                <td class="headrowtitle" style="font-size: 9px;">Bathroom</td>
                <td class="headrowtitle" style="font-size: 9px;">Telephone</td>
                <td class="headrowtitle" style="font-size: 9px;">Kitchen</td>
            </tr>
            </thead>
           <tbody>
            <?php
            $x = 1;
            $totEnrolled = 0;
            $totExitInFam = 0;
            foreach ($housingEnrl as $val) {

                if($val['hEnrollingID']){

                    $famOfExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_house_enrolling LEFT JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID LEFT JOIN srp_erp_ngo_com_communitymaster ON Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_ngo_com_house_ownership_master ON srp_erp_ngo_com_house_ownership_master.ownershipAutoID = srp_erp_ngo_com_house_enrolling.ownershipAutoID LEFT JOIN srp_erp_ngo_com_house_type_master ON srp_erp_ngo_com_house_enrolling.hTypeAutoID=srp_erp_ngo_com_house_type_master.hTypeAutoID WHERE srp_erp_ngo_com_house_enrolling.companyID={$val['companyID']} AND FamHouseSt=1 AND srp_erp_ngo_com_house_enrolling.Link_hEnrollingID={$val['hEnrollingID']} ");
                    $familyEnExt = $famOfExist->result();

                    ?>
                    <tr>
                        <td class="mailbox-star"><?php echo $x; ?></td>
                        <td class="mailbox-star"><?php echo $val['FamilySystemCode'].' |'. $val['FamilyName']; ?></td>
                        <td class="mailbox-star"><?php echo $val['CName_with_initials']; ?></td>
                        <td class="mailbox-star"><?php echo $val['C_Address']; ?></td>
                        <td class="mailbox-star"><?php echo $val['ownershipDescription']; ?></td>
                        <td class="mailbox-star"><?php echo $val['hTypeDescription']; ?></td>
                        <td class="mailbox-star"><?php echo $val['hESizeInPerches']; ?></td>
                        <td class="mailbox-star"><?php if($val['isHmElectric']==1){
                        if ($type == 'html') { ?>
                            <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                            <?php
                        } else{
                            ?>
                            <span class="label label-success"> Yes </span>
                        <?php } } else{ if ($type == 'html') { ?>

                                <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                                <?php
                            } else{
                                ?>
                                <span class="label label-danger"> No </span>

                        <?php } } ?>
                        </td>
                        <td class="mailbox-star"><?php if($val['isHmWaterSup']==1){
                        if ($type == 'html') { ?>
                        <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                        <?php
                    } else{
                        ?>
                        <span class="label label-success"> Yes </span>
                    <?php } } else{ if ($type == 'html') { ?>

                    <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                    <?php
                } else{
                    ?>
                    <span class="label label-danger"> No </span>

                <?php } } ?>
                        </td>
                        <td class="mailbox-star"><?php   if($val['isHmToilet']==1){
                                if ($type == 'html') { ?>
                                <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                                <?php
                            } else{
                                ?>
                                <span class="label label-success"> Yes </span>
                            <?php } } else{ if ($type == 'html') { ?>

                                <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                                <?php
                            } else{
                                ?>
                                <span class="label label-danger"> No </span>

                            <?php } } ?>
                        </td>
                        <td class="mailbox-star"><?php if($val['isHmBathroom']==1){
                        if ($type == 'html') { ?>
                        <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                        <?php
                    } else{
                        ?>
                        <span class="label label-success"> Yes </span>
                    <?php } } else{ if ($type == 'html') { ?>

                    <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                    <?php
                } else{
                    ?>
                    <span class="label label-danger"> No </span>

                <?php } } ?>
                        </td>
                        <td class="mailbox-star"><?php if($val['isHmTelephone']==1){
                        if ($type == 'html') { ?>
                        <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                        <?php
                    } else{
                        ?>
                        <span class="label label-success"> Yes </span>
                    <?php } } else{ if ($type == 'html') { ?>

                    <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                    <?php
                } else{
                    ?>
                    <span class="label label-danger"> No </span>

                <?php } } ?>
                        </td>
                        <td class="mailbox-star"><?php if($val['isHmKitchen']==1){
                        if ($type == 'html') { ?>
                        <img style="width: 13px;" src="<?php echo base_url("images/community/right.jpg") ?>">
                        <?php
                    } else{
                        ?>
                        <span class="label label-success"> Yes </span>
                    <?php } } else{ if ($type == 'html') { ?>

                    <img style="width: 13px;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                    <?php
                } else{
                    ?>
                    <span class="label label-danger"> No </span>

                <?php } } ?>
                        </td>
                    </tr>
                    <?php
                    if(!empty($familyEnExt)){
                        ?>
                        <tr style="background-color:#e5e5e5;">
                            <td colspan="13" style="font-weight: inherit;">With <?php echo $val['FamilySystemCode'].' |'. $val['FamilyName']; ?> Other Included Families</td>
                        </tr>
                        <?php

                        foreach ($familyEnExt as $familyEnl) {
                        ?>
                        <tr style="background-color: #c2d7ef;">
                            <td></td>
                            <td colspan="12"><?php echo $familyEnl->FamilySystemCode.' |'. $familyEnl->FamilyName; ?></td>
                        </tr>
                        <?php

                            $totExitInFam += 1;
                     }
                    }
                    ?>
                    <?php
                    $x++;
                    $totEnrolled += 1;
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>

                <td class="text-left" colspan="13">
                    Total House Enrolled : <?php echo $totEnrolled; ?>
                    <span style="color: #8b8b8b;">Total Enrolled Families : <?php echo ($totEnrolled + $totExitInFam); ?></span>


             </td>
            </tr>
            </tfoot>
        </table>
    </div>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
    <script type="text/javascript">
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    </script>


<?php
