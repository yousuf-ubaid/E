<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('community_ngo_helper');

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
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">PROPOSAL DESCRIPTION</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Confirm</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) {

                $qualifiedFamilies = $this->db->query("Select COUNT(ppd.proposalBeneficiaryID) AS zakFamilies,SUM(ppd.totalEstimatedValue) AS totZakatAmount from srp_erp_ngo_projectproposalbeneficiaries ppd WHERE ppd.`proposalID` = {$val['proposalID']} ORDER BY `proposalBeneficiaryID` DESC")->row_array();

                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentSystemCode']; ?></a></td>
                    <td class="mailbox-name"><a href="#">
                            <div class="contact-box">
                                <div class="link-box">
                                    <strong class="contacttitle">Proposal Name : </strong><a class="link-person noselect" href="#"><?php echo $val['proposalName']; ?></a>
                                    <br><strong class="contacttitle">Document Date : </strong><a class="link-person noselect" href="#"><?php echo $val['DocumentDate']; ?></a>
                                    <br><strong class="contacttitle">Qualified Families : <?php echo $qualifiedFamilies['zakFamilies'] ?> </strong>  <a class="link-person noselect" href="#"> </a><br><strong class="contacttitle">Commited Amount (LKR) : <?php echo number_format($qualifiedFamilies['totZakatAmount'],2)?></strong><a class="link-person noselect" href="#"> </a>
                                </div>
                            </div>
                    </a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['statusName']; ?></a></td>
                    <td class="mailbox-name"><?php echo confirmation_status($val['confirmedYN']); ?></td>
                    <td class="mailbox-attachment">
                        <span class="pull-right">
                            <?php if ($val['confirmedYN'] != 1/* && $val['approvedYN'] != 1*/) {
                            ?>
                            <a href="#"
                               onclick="fetchPage('system/communityNgo/ngo_mo_comProject_create_proposal','<?php echo $val['proposalID'] ?>','Edit Project Proposal')"><span
                                    title="Edit" rel="tooltip"
                                    class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
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
                            if ($val['createdUserID'] == trim(current_userID()) && $val['confirmedYN'] == 1/* && $val['approvedYN'] != 1*/) {
                                ?>
                                <a onclick="referback_project_proposal(<?php echo $val['proposalID'] ?>);"><span
                                        title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"
                                        style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <?php
                            }
                            ?>
                            <a target="_blank"
                               href="<?php echo site_url('OperationNgo/load_project_proposal_print_pdf/') . '/' . $val['proposalID'] ?>"><span
                                    title="Print" rel="tooltip"
                                    class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                onclick="load_project_proposal_email(<?php echo $val['proposalID'] ?>)" title=""
                                rel="tooltip" data-original-title="Send Mail"><i class="fa fa-envelope"
                                                                                 aria-hidden="true"></i></a>

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
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY</div>
    <?php
}

?>
<?php
