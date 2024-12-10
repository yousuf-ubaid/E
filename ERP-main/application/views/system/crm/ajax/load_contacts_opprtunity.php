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
    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
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
</style>
<?php
if (!empty($opportunity)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Opportunity Name</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Pipeline</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($opportunity as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star"><?php echo $val['documentSystemCode']; ?></td>
                    <td class="mailbox-star"><?php echo $val['opportunityName']; ?></td>
                    <td class="mailbox-name"><span class="label"
                                                   style="background-color:<?php echo $val['statusBackgroundColor'] ?>; color:<?php echo $val['statusColor'] ?>; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                    </td>
                    <td class="mailbox-name">
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
                                        echo '<div class="step ' . $active . '" style="margin-top: 3px !important;"><span title="' . $pipe['stageName'] . '">' . substr($pipe['stageName'], 0, 5) . '</span></div>';
                                        $x++;
                                    }

                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <span class="pull-right">
                        <a class="link-person noselect" href="#"
                            onclick="fetchPage('system/crm/opportunities_edit_view','<?php echo $val['opportunityID'] ?>','View Opportunity','<?php echo $masterID; ?>','Contact')">
                            <i class="fa fa-eye" aria-hidden="true" style="color: #00a5e6;font-weight: 600;font-size: 14px;"></i>
                        </a>
                            </span>
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
    <div class="search-no-results">THERE ARE NO OPPORTUNITIES TO DISPLAY.</div>
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