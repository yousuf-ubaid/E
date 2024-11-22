<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<?php
if (!empty($familyMas)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td style="border-bottom: solid 1px #f76f01;" colspan="11">
                    <div style="  display: inline;  float: left;  color: #f76f01;font-weight: bold; margin-top: 5px; font-size: 15px;"><?php echo $this->lang->line('CommunityNgo_latest_fam');?><!--Latest Family--></div>
                    <div style="display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #eee;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 1px 3px 0 3px;
            line-height: 14px;
            margin-left: 8px;
            margin-top: 9px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;"><?php echo sizeof($familyMas) ?></div>
                </td>
            </tr>
            <tr>
                <td  style="border-top: 1px solid #ffffff;">#</td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_ledger_no');?><!--Ledger No--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_ref_no');?><!--Reference No--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_famName');?><!--Family Name--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_leader');?><!--Leader--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_fam_ancestry');?><!--Ancestory--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_famAddedDate');?><!--Added Date--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;" title="Total Members"><?php echo $this->lang->line('CommunityNgo_famTotMem');?><!--Total Members--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;" title="Is Enroll To House Count"> House Enrolled</td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('common_status');?><!--Status--></td>

            </tr>
            <?php

            $x = 1;
            $totHs = 0;
            foreach ($familyMas as $val) {

                if($val['FamAncestory']==0){ $FamAnces ="Local"; }else{ $FamAnces ="Outside"; }

                $qmEMin = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $val['companyID'] . "' AND FamMasterID='" . $val['FamMasterID'] . "'");
                $datMemIn= $qmEMin->row();

                $queryFM4 = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $val['companyID'] . "' AND FamMasterID='" . $val['FamMasterID'] . "'");
                $rowFM4 = $queryFM4->result();
                $femMem2 = array();
                $totalMm=1;
                foreach ($rowFM4 as $resFM4) {
                    $femMem2[] = $resFM4->Com_MasterID;

                    $totMm = $totalMm++;

                }
                $in_femMem = "'".implode("', '", $femMem2)."'";

                $qmEMOtrin = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $val['companyID'] . "' AND FamMasterID !='" . $val['FamMasterID'] . "' AND Com_MasterID IN($in_femMem)");
                $datMemOtrin= $qmEMOtrin->row();

                $qmHousing = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_com_house_enrolling WHERE companyID='" . $val['companyID'] . "' AND FamMasterID ='" . $val['FamMasterID'] . "'");
                $datHousing= $qmHousing->row();
                ?>
                <tr>
                    <td class="mailbox-name" style="font-weight: 600; color: saddlebrown;"><?php echo $x; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['FamilySystemCode']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['LedgerNo']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['FamilyName']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['CName_with_initials']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $FamAnces; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['FamilyAddedDate']; ?></td>
                    <td class="mailbox-name"><span data-toggle="tooltip" title="Total Members Of Family" style="background-color: lightgrey; color: black;font-size: 11px;" class="badge"><b><?php echo $totMm; ?></b></span></td>
                    <td class="mailbox-name"><a href="#">
                            <?php if(!empty($datHousing)){
                                $totHs += 1;
                                ?>
                                <span data-toggle="tooltip" title="Enrolled" style="background-color: #009688; color: #f1f0f0;font-size:10px;" class="badge">Yes</span>
                                <?php
                            }else{
                                ?>
                                <span data-toggle="tooltip" title="Not Enrolled" style="background-color: #8bc34a; color: #f1f0f0;font-size:10px;" class="badge">No</span>
                                <?php
                            }
                            ?>

                    </td>
                    <td class="mailbox-name">
                        <?php if($val['confirmedYN']==1){
                            ?>
                            <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 10px;display: inline;
            padding: .2em .8em .3em;"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></span>
                            <?php
                        }else{
                            ?>
                            <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 10px;display: inline;
            padding: .2em .8em .3em;"><?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--></span>
                            <?php

                        }?>


                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>
            </tbody>
            &nbsp;
            <tfoot>
            <tr style="background: white;"><td  class="mailbox-name" style="border-top: solid 1px saddlebrown;color: #8b8b8b;font-size: 10px;" colspan="11">Total House Enrolled Families : <?php echo $totHs; ?></td></tr>
            </tfoot>

        </table><!-- /.table -->
    </div>
    <?php

}
else { ?>
    <br>

    <div><?php echo $this->lang->line('community_there_are_no_rec_to_display');?><!--THERE ARE NO RECORDS TO DISPLAY-->.</div>
    <?php
}

?>





<?php
