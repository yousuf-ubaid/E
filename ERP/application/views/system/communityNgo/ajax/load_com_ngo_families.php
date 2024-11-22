<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <style>
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .label {
            display: inline;
            padding: .2em .8em .3em;
        }

        .actionicon {
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #89e68d;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 2px 5px 2px 5px;
            line-height: 14px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }

        .headrowtitle {
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }

        .task-cat-upcoming {
            border-bottom: solid 1px #f76f01;
        }

        .task-cat-upcoming-label {
            display: inline;
            float: left;
            color: #f76f01;
            font-weight: bold;
            margin-top: 5px;
            font-size: 15px;
        }

        .taskcount {
            display: inline-block;
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
            color: #888;
        }

        .numberColoring {
            font-size: 13px;
            font-weight: 600;
            color: saddlebrown;
        }
    </style>
<?php
if (!empty($familyMas)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="11">
                    <div class="task-cat-upcoming-label"><?php echo $this->lang->line('CommunityNgo_latest_fam');?><!--Latest Family--></div>
                    <div class="taskcount"><?php echo sizeof($familyMas) ?></div>
                </td>
            </tr>
            <tr>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">#</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_ledger_no');?><!--Ledger No--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_ref_no');?><!--Reference No--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_famName');?><!--Family Name--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_leader');?><!--Leader--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_fam_ancestry');?><!--Ancestory--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('CommunityNgo_famAddedDate');?><!--Added Date--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;" title="Total Members"><?php echo $this->lang->line('CommunityNgo_famTotMem');?><!--Total Members--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;" title="Is Enroll To House Count"><?php echo $this->lang->line('communityngo_famHusEnrl');?><!-- House Enrolled --></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;"></td>

            </tr>
            <?php

            $x = 1;
            $totHs =0;
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
                if(empty($rowFM4)){
                    $totMms = '0';
                }
                else{
                    $totMms = $totMm;
                }
                $in_femMem = "'".implode("', '", $femMem2)."'";

                $qmEMOtrin = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $val['companyID'] . "' AND FamMasterID !='" . $val['FamMasterID'] . "' AND Com_MasterID IN($in_femMem)");
                $datMemOtrin= $qmEMOtrin->row();

                $qmHousing = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_com_house_enrolling WHERE companyID='" . $val['companyID'] . "' AND FamMasterID ='" . $val['FamMasterID'] . "'");
                $datHousing= $qmHousing->row();
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#" style="color: #40adff;" onclick="fetchPage('system/communityNgo/ngo_mo_familyMaster_view','<?php echo $val['FamMasterID'] ?>','View Family -<?php echo $val['FamilySystemCode'].' | '. $val['FamilyName'] ?>','NGO')"><?php echo $val['FamilySystemCode']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['LedgerNo']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['FamilyName']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['CName_with_initials']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $FamAnces; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['FamilyAddedDate']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><span data-toggle="tooltip" title="Total Members Of The Family" style="background-color: lightgrey; color: black;font-size: 11px;" class="badge"><b><?php echo $totMms; ?></b></span></td>
                    <td class="mailbox-name"><a href="#">
                            <?php if(!empty($datHousing)){
                                $totHs += 1;
                                ?>

                                <a href="#" style="font-size:14px;"><span title="Enrolled" style="color: green;" rel="tooltip" class="fa fa-home" data-original-title="Enrolled"></span></a> &nbsp;

                                <?php
                            }else{

                                ?>

                                <a href="#" style="font-size:14px;"><span title="Not Enrolled" style="color: red;" rel="tooltip" class="fa fa-home" data-original-title="Not Enrolled"></span></a> &nbsp;

                                <?php
                            }
                            ?>

                    </td>
                    <td class="mailbox-name">
                        <?php if($val['confirmedYN']==0){
                            ?>
                            <span class="label label-danger">&nbsp;</span>
                            <?php
                        }else{
                            ?>
                            <span class="label label-success">&nbsp;</span>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="mailbox-attachment"><span class="pull-right">
                              <?php
                              if((!empty($datMemOtrin)) && $datMemIn->Com_MasterID = $datMemOtrin->Com_MasterID){ ?>

                              <a href="#" onclick="fetchPage('system/communityNgo/ngo_mo_familyLink_view','<?php echo $val['FamMasterID'] ?>','Family Relationship','NGO')"><span title="" rel="tooltip" style="color:green;" class="glyphicon glyphicon-link fa-lg" data-original-title="Family Relationship"></span></a> | &nbsp;

                              <?php }
                              else{
                              }
                              ?>
                            <a href="#" onclick="fetchPage('system/communityNgo/ngo_mo_familyMaster_view','<?php echo $val['FamMasterID'] ?>','View Family -<?php echo $val['FamilySystemCode'].' | '. $val['FamilyName'] ?>','NGO')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a> &nbsp;

                        <?php
                        if ($val['createdUserID'] == trim(current_userID()) && $val['confirmedYN'] == 1) {
                            ?>
                            |  <a onclick="referback_family_creation(<?php echo $val['FamMasterID'] ?>);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a> |&nbsp;&nbsp;
                            <?php
                        }
                        if($val['confirmedYN']==0) {
                                 ?>
                           | <a class="CA_Alter_btn" href="#" onclick="fetchPage('system/communityNgo/ngo_mo_familyCreate.php','<?php echo $val['FamMasterID'] ?>','Edit Family')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> |&nbsp;
                               <a class="CA_Print_Excel_btn" target="_blank" href="<?php echo site_url('CommunityNgo/load_community_family_confirmation/') . '/' . $val['FamMasterID'] ?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                               &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_family_master(<?php echo $val['FamMasterID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                    <?php
                    }else{
                        ?>&nbsp;&nbsp;&nbsp;
                        <a target="_blank" href="<?php echo site_url('CommunityNgo/load_community_family_confirmation/') . '/' . $val['FamMasterID'] ?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                        <?php
                    }
                    ?>

                    </td>
                </tr>
                <?php

                $x++;
            }
            ?>

            </tbody>
            <tfoot style="border-color: saddlebrown;">
            <tr style="background: white;color: #8b8b8b;font-size: 11px;"><td class="task-cat-upcoming" colspan="11">Total House Enrolled Families : <?php echo $totHs; ?></td></tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php

}
else { ?>
    <br>

    <div class="search-no-results"><?php echo $this->lang->line('community_there_are_no_rec_to_display');?><!--THERE ARE NO RECORDS TO DISPLAY-->.</div>
    <?php
}

?>

    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {

            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });

        });
    </script>


<?php
