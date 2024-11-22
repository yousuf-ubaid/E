<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


?>
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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
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

    .actionicon_project {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #de7a7a;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 4px 6px 5px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #fdfdfd;
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
if (!empty($header)) {
    $issuperadmin = crm_isSuperAdmin();
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">
                <td class="task-cat-upcoming" colspan="12">
                    <div class="task-cat-upcoming-label">Opportunities</div><!--Latest Tasks-->
                    <div class="taskcount"><?php echo sizeof($header); ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Description</td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('crm_pipeline');?></td><!--pipeline-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created By</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--status-->

              <!--  <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php /*echo $this->lang->line('common_action');*/?></td><!--Action-->
            </tr>
            <?php
            $tyr = 1;
            foreach ($header as $val) {
                $textdecoration = '';
                if ($val['closeStatus'] != 0) {
                    $textdecoration = '';
                    //$textdecoration = 'textClose';
                }
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $tyr; ?></a></td>

                    <td class="mailbox-name" >
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle"><?php echo $val['documentSystemCode'] ?><br>
                                    <a
                                        class="link-person noselect <?php echo $textdecoration; ?>" href="#"
                                        onclick="opp_edit_view('system/crm/opportunities_edit_view','<?php echo $val['opportunityID'] ?>','View Opportunity','CRM')"><?php echo $val['opportunityName'] ?>
                                        <br> <?php echo number_format($val['transactionAmount'], 2) . " " . $val['CurrencyCode'] ?><br><?php echo $val['responsiblePerson'] ?>
                                </strong></a><br> <?php echo $this->lang->line('crm_closing_date');?> <?php echo $val['forcastCloseDate'] ?>
                                </strong></div>
                            </a><!--Closing Date-->
                        </div>
                    </td>

                    <td class="mailbox-name" style="width: 50%;">
                        <div class="arrow-steps clearfix">
                            <?php
                            if (!empty($val['pipelineID'])) {
                                $pipeline = $this->db->query("SELECT * FROM srp_erp_crm_pipelinedetails WHERE pipeLineID={$val['pipelineID']}")->result_array();
                                $html = '';
                                if (!empty($pipeline)) {
                                    $count = count($pipeline);
                                    $percentage = 100 / $count;
                                    $x = 1;
                                    foreach ($pipeline as $pipe) {
                                        $active = 'not-current';
                                        if ($pipe['pipeLineDetailID'] == $val['pipelineStageID']) {
                                            $active = "current";
                                        }
                                        echo '<div class="step ' . $active . '" style="margin-top: 3px !important;"><span class="' . $textdecoration . '" title="' . $pipe['stageName'] . '">' . substr($pipe['stageName'], 0, 5) . '</span></div>';
                                        $x++;
                                    }

                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <strong class="contacttitle">
                            <a class="link-person noselect" href="#">User: <?php echo $val['campaigncreateduser'] ?>
                                <br>Date: <?php echo $val['createdDatetimeopportunity'] ?>
                        </strong></a>
                    </td>
                    <td class="mailbox-name"><span class="label"
                                                   style="background-color:<?php echo $val['statusBackGroundColor'] ?>; color:<?php echo $val['statusTextColor'] ?>; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                        <br>
                        <?php
                        if ($val['closeStatus'] == 2) { ?>

                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;"><?php echo $this->lang->line('crm_closed_and_converted');?>
                            </div><!--Closed & Converted-->
                            <?php
                        }
                        ?>
                        <?php
                        if ($val['closeStatus'] == 1) { ?>

                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;">Closed
                            </div><!--Closed & Converted-->
                            <?php
                        }
                        ?>
                    </td>


                    <?php
                    if ($issuperadmin['isSuperAdmin'] == 1 && ($val['closeStatus'] == 1 || $val['closeStatus'] == 2)) {
                        ?>

                    <?php } ?>

                </tr>
                <?php
                $tyr++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_opportunities_to_dispalay');?>.</div><!--THERE ARE NO OPPORTUNITIES TO DISPLAY-->
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