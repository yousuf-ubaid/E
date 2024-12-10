<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('operation_ngo_helper');

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
if (!empty($master)) {?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Latest Projects</div>
                    <div class="taskcount"><?php echo sizeof($master)?> </div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; ">Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Project Description</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Estimated End Date</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Project Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) { ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentSystemCode']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Project Name : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['projectname'] ?></a><br><strong
                                        class="contacttitle">Total Project Cost (<?php echo $val['CurrencyCode']; ?>) : </strong><a class="link-person noselect" href="#"><?php echo number_format($val['totalProjectValue'],2)  ?></a><br><strong class="contacttitle">Total Claimed Amount (<?php echo $val['CurrencyCode']; ?>) : </strong><a class="link-person noselect" href="#"><?php echo number_format($val['claimedamt'],2) ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name"><a href="#"  style="padding-right:30% "><?php echo $val['estimateenddate']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo project_status($val['percentage']) ?></a></td>
                    <td class="mailbox-name"><a href="#">

                        </a><a href="#"
                                                                                        onclick="fetchPage('system/operationNgo/create_convertedproposaltoproject','<?php echo $val['proposalID'] ?>','Edit Project')"><span
                                    title="Edit" rel="tooltip"
                                    class="glyphicon glyphicon-pencil"></span></a>&nbsp
                    </td>
                </tr>
                <?php
                $x++;

            } ?>

            </tbody>
        </table>
    </div>
<?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO PROJECT TO DISPLAY</div>
    <?php
} ?>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
    });
</script>
