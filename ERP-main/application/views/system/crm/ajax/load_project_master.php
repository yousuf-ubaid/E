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
    .actioniconclose {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #fc0000;
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
    .headrowtitle{
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
                    <div class="task-cat-upcoming-label">Projects</div><!--Latest Tasks-->
                    <div class="taskcount"><?php echo sizeof($headercount); ?></div>
                </td>
            </tr>
            <tr>
               <!-- <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>-->
               <!-- <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Assignee</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name');?></td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('crm_pipeline');?></td><!--pipeline-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created</td><!--Name-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--status-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            $mn = 1;
            foreach ($header as $val) {
                $textdecoration = '';
                if ($val['closeStatus'] != 0) {
                    $textdecoration = '';
                    //$textdecoration = 'textClose';
                }
                ?>
                <tr>
                    <!--<td class="mailbox-name"><a href="#" class="numberColoring"><?php /*echo $mn; */?></a></td>-->
                    <td class="mailbox-name" style="width: 22%;">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle"><strong>

                                        <a
                                        class="link-person noselect" href="#"
                                        onclick="fetchPage('system/crm/project_edit_view','<?php echo $val['projectID'] ?>','View Project','CRM')">

                                        <?php echo $val['documentSystemCode'] ?></strong><br>

                                </strong></a>
                                <?php if(!empty($val['organizationName'])){ echo $val['organizationName']."<br>"; } echo $val['projectName'] ?>
                                <br><?php echo number_format($val['transactionAmount'], 2) . " " . $val['CurrencyCode'] ?><br> Closing Date <?php echo $val['projectEndDate'] ?> <br> <?php echo '<div style="margin-top: 3%;color: #6b0a0a;font-weight: 700; font-size:10px;">'.$val['opportunityCode'].'</div>' ?>
                                <br><a href="#"><strong><?php echo $val['responsiblePerson'] ?></strong></div>
                            
                            </a>
                        </div>
                    </td>


                    <td class="mailbox-name" style="width: 67%;">
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
                                        //echo '<div class="step ' . $active . '" style="margin-top: 3px !important;"><span class="' . $textdecoration . '" title="' . $pipe['stageName'] . '">' . substr($pipe['stageName'], 0, 5) . '</span></div>';
                                        echo '<div class="step ' . $active . '" style="margin-top: 3px !important;"><span class="' . $textdecoration . '" title="' . $pipe['stageName'] . '">' . $pipe['stageName'] . '</span></div>';
                                        $x++;
                                    }

                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="mailbox-name" style="width: 12%;">
                        <strong class="contacttitle">
                            <a class="link-person noselect" href="#"> User: <?php echo $val['createduserproject'] ?>
                                <br>Date <?php echo $val['createddateproject'] ?>
                        </strong></a>
                    </td>
                    <td class="mailbox-name"><span class="label"
                                                   style="background-color:<?php echo $val['statusBackGroundColor'] ?>; color:<?php echo $val['statusTextColor'] ?>; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                        <br>
                    </td>
                    <td class="mailbox-attachment"><span class="pull-right">
                              <?php if($val['isClosed'] == 1){ ?>
                                  <div class="actionicon" style="background-color: #6b77d6;"><span class="glyphicon glyphicon-ok"
                                                                                                   style="color:rgb(255, 255, 255);"
                                                                                                   title="Closed"></span>
                                                            </div>

                            <?php }else if ($val['isClosed'] == 2){?>
                                  <div class="actionicon"><span class="glyphicon glyphicon-ok"
                                                                style="color:rgb(255, 255, 255);"
                                                                title="completed"></span>
                                                            </div>
                          <?php }else if ($val['isClosed'] == 3){?>
                                  <div class="actioniconclose"><span class="glyphicon glyphicon-ok"
                                                                style="color:rgb(255, 255, 255);"
                                                                title="Cancelled"></span>
                                                            </div>

                         <?php } else {?>
                                  <a href="#"
                                     onclick="fetchPage('system/crm/create_project','<?php echo $val['projectID'] ?>','Edit Project','CRM')"><span
                                              title="Edit" rel="tooltip"
                                              class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;

                          <?php }?>

                        </span>
                    </td>
<!--                    <?php
/*                    if ($issuperadmin['isSuperAdmin'] == 1 && ($val['closeStatus'] == 1 || $val['closeStatus'] == 2)) {
                        */?>
                        <td class="mailbox-attachment">
                            <div class="actionicon" style="background-color: red">
                                <a href="#" onclick="reopenOpportunities(<?php /*echo $val['projectID'] */?>)" <i
                                    class="fa fa-repeat" aria-hidden="true" style="color: white"></i>
                            </div>
                        </td>
                    --><?php /*} */?>

                </tr>
                <?php
                $mn++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_projects_to_display');?>.</div><!--THERE ARE NO PROJECTS TO DISPLAY-->
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