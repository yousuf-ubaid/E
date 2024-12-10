<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('operation_ngo_helper');

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
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
        text-transform: uppercase;
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

    .sizepipeline {
        width: 405px;
        overflow: auto;
        padding-right: 10px;
    }

</style>
<?php
if (!empty($master)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Latest Project Proposals</div>
                    <div class="taskcount"><?php echo sizeof($master) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: left;">Proposal Description</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center; min-width: 15px;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) {
                $textdecoration = '';
                if ($val['status'] != 0) {
                    $textdecoration = '';
                }
                $qualifieddonors = $this->db->query("Select COUNT(ppd.proposalBeneficiaryID) AS Ben from srp_erp_ngo_projectproposalbeneficiaries ppd WHERE ppd.`proposalID` = {$val['proposalID']} AND  ppd.isQualified = 1 ORDER BY `proposalBeneficiaryID` DESC")->row_array();
                $donoramt = $this->db->query("SELECT ppm.proposalID,IFNULL(	ppben.netTotal, '0' ) as totalamt,IFNULL( donorcom.commitedamt, '0' ) as commitedamt FROM srp_erp_ngo_projectproposals ppm LEFT JOIN (SELECT ppben.*,SUM( IFNULL( ppben.totalEstimatedValue, 0 ) ) AS netTotal FROM srp_erp_ngo_projectproposalbeneficiaries ppben LEFT JOIN srp_erp_ngo_beneficiarymaster benmaster ON benmaster.benificiaryID = ppben.beneficiaryID WHERE proposalID = {$val['proposalID']}  AND ppben.isQualified =1) ppben ON ppben.proposalID = ppm.proposalID LEFT JOIN ( SELECT SUM( IFNULL( propdonors.commitedAmount, 0 ) ) AS commitedamt, proposalID FROM srp_erp_ngo_projectproposaldonors propdonors WHERE proposalID = {$val['proposalID']}  ) donorcom ON donorcom.proposalID = ppm.proposalID 
WHERE ppm.proposalID = {$val['proposalID']} GROUP BY ppm.proposalID ORDER BY ppm.proposalID DESC")->row_array();
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentSystemCode']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Proposal Name : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['proposalName']; ?></a><br><strong class="contacttitle">Document Date : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['DocumentDate']; ?></a><br><strong
                                        class="contacttitle">Qualified Beneficiaries : <?php echo $qualifieddonors['Ben'] ?>  / <?php echo $val['Beneficiarycount'] ?> </strong>  <a class="link-person noselect" href="#"> </a><br><strong class="contacttitle">Commited Amount (<?php echo $val['CurrencyCode']?>) : <?php echo number_format($donoramt['commitedamt'],2)?> / <?php echo number_format($donoramt['totalamt'],2)?></strong><a class="link-person noselect" href="#"> </a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <div class="arrow-steps clearfix sizepipeline">
                            <?php
                            if (!empty($val['status'])) {
                                $pipeline = $this->db->query("SELECT * FROM srp_erp_ngo_proposalstages WHERE statusLineID ={$val['status']}")->result_array();
                                $html = '';
                                if (!empty($pipeline)) {
                                    $count = count($pipeline);
                                    $percentage = 100 / $count;
                                    foreach ($pipeline as $pipe) {
                                        $active = 'not-current';
                                        if ($pipe['proposalStageID'] <= $val['proposalStageID']) {
                                            $active = "current";
                                        }
                                        echo '<div class="step ' . $active . '" style="margin-top: -4px !important;"><span class="' . $textdecoration . '" title="' . $pipe['stageName'] . '">' . substr($pipe['stageName'], 0, 9) . '</span></div>';

                                    }

                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="mailbox-attachment" width="10%">
                        <span class="pull-right">
                            <?php if ($val['confirmedYN'] != 1 && $val['approvedYN'] != 1) {
                            ?>
                            <?php if($val['zakatDefault'] == '1'){?>
                                <a href="#"
                                   onclick="fetchPage('system/communityNgo/ngo_mo_comProject_create_proposal','<?php echo $val['proposalID'] ?>','Edit Project Proposal')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>

                            <?php } else{ ?>
                                <a href="#"
                                   onclick="fetchPage('system/operationNgo/create_project_proposal','<?php echo $val['proposalID'] ?>','Edit Project Proposal')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>
                            <?php } ?>
                            &nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                               <a target="_blank"
                                  href="<?php echo site_url('OperationNgo/load_project_proposal_print_pdf/') . '/' . $val['proposalID'] ?>"><span
                                           title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                               &nbsp;&nbsp;|&nbsp;&nbsp;<a
                                    onclick="delete_project_proposal(<?php echo $val['proposalID'] ?>);"><span
                                        title="Delete"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a></span>
                        <?php
                        } else {
                            if ($val['createdUserID'] == trim(current_userID()) && $val['confirmedYN'] == 1 && $val['approvedYN'] != 1) {
                                ?>
                                <a onclick="referback_project_proposal(<?php echo $val['proposalID'] ?>);"><span
                                            title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"
                                            style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|
                                <?php
                            }
                            ?>
                            <a target="_blank"
                               href="<?php echo site_url('OperationNgo/load_project_proposal_print_pdf/') . '/' . $val['proposalID'] ?>"><span
                                        title="Print" rel="tooltip"
                                        class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                    onclick="load_project_proposal_email(<?php echo $val['proposalID'] ?>)" title=""
                                    rel="tooltip" data-original-title="Send Mail"><i class="fa fa-envelope"
                                                                                     aria-hidden="true"></i></a>&nbsp;
                            <?php if (/*$val['isEdit'] == 1 &&*/
                                $val['approvedYN'] == 1) { ?>
                                |


                                <?php if($val['zakatDefault'] == '1'){?>
                                    <a href="#"
                                       onclick="fetchPage('system/communityNgo/ngo_mo_comProject_create_proposal','<?php echo $val['proposalID'] ?>','Edit Project Proposal')"><span
                                            title="Edit" rel="tooltip"
                                            class="glyphicon glyphicon-pencil"></span></a>

                                <?php } else{ ?>
                                    <a href="#"
                                       onclick="fetchPage('system/operationNgo/create_project_proposal','<?php echo $val['proposalID'] ?>','Edit Project Proposal')"><span
                                            title="Edit" rel="tooltip"
                                            class="glyphicon glyphicon-pencil"></span></a>
                                <?php } ?>

                                <?php if ($val['closedYN'] == 0) { ?>
                                    |
                                    <a
                                            onclick="project_proposal_convertion(<?php echo $val['proposalID'] ?>,<?php echo $val['projectID'] ?>);"><span
                                                title="Close Project Proposal"
                                                rel="tooltip"
                                                class="glyphicon glyphicon-ok-sign"
                                        ></span></a></span>
                                <?php } else { ?>
                                    |
                                    <a
                                            onclick="view_converted_proposal(<?php echo $val['proposalID'] ?>,<?php echo $val['projectID'] ?>);"><span
                                                title="Re Open Closed Project Proposal"
                                                rel="tooltip"
                                                style="color:rgb(153, 0, 0);"
                                                class="glyphicon glyphicon-ok-sign"
                                        ></span></a></span>

                                    <?php
                                } ?>

                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
                $x++;
            } ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY</div>
    <?php
}

?>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
    });
</script>
