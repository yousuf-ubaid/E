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

    .numberOrder {

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

    .numberColoring {
        font-size: 12px;
        font-weight: 500;
        color: saddlebrown;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #8bc34a;;
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

    .tableHeader {
        border: solid 1px #e6e6e6 !important;
    }

    .btn-group-xs > .btn, .btn-xs {
        padding: 0px 3px !important;
    }

</style>
<div class="row">
    <div class="col-sm-12">
        <strong class="task-cat-upcoming-label">
            PROJECT :
            <?php
            if (isset($project_steps_projectname[0])) {
                echo $project_steps_projectname[0]['projectName'] . ' | ' . 'Project Cost : ' . number_format($project_steps_projectname[0]['projectvalue'], 2) . '(' . $project_steps_projectname[0]['CurrencyCode'] . ')';
            } ?></strong>
    </div>
</div>
<br>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;" width="5%">#</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;" width="15%">DESCRIPTION</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;" width="20%">GL CODE</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;" width="10%">AMOUNT</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;" width="10%">Claimed Status</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;"width="5%">ACTION</td>

            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <tr>
                    <td colspan="8" class="mailbox-name"><span
                                style="font-weight: 600;font-size: 13px"><?php echo $val['description'] . " - " . $val['percentage'] . "%" . " (" . number_format($val['stageAmount'], 2) . ") "  . " | Balance Amount". " (" . number_format($val['stageAmount'] - $val['amount'], 2).")" ; ?>
                            &nbsp; <a
                                    onclick="add_claim_project_stage(<?php echo $val['projectStageID'] ?>,<?php echo $val['ngoProjectID']?>);"><span
                                        title="Project Stages Description"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-plus"
                                        style="color: #0088cc"></span></a> |&nbsp;<a
                                    onclick="update_stage_details(<?php echo $val['projectStageID'] ?>,<?php echo $val['ngoProjectID']?>);"><span
                                        title="Edit"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-pencil"
                                        style="color: #f18754"></span></a> | <a
                                    onclick="add_a_claim(<?php echo $val['projectStageID'] ?>,<?php echo $val['ngoProjectID']?>);"><span
                                        title="Add Claim"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-ok"
                                        style="color: #00a65a"></span></a> | <a
                                    onclick="load_invoices_claimed(<?php echo $val['projectStageID'] ?>,<?php echo $val['ngoProjectID']?>);"><span
                                        title="View Invoices"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-file"
                                        style="color: #333"></span></a> | <a
                                    onclick="delete_project_step(<?php echo $val['projectStageID'] ?>);"><span
                                        title="Delete"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a> </span></td>
                </tr>

                <?php
                $x = 1;
                foreach ($detail as $row) {
                    if ($row['projectStageID'] == $val['projectStageID']) { ?>
                        <tr>
                            <td class="mailbox-name"><a href="#">&nbsp;&nbsp;&nbsp;<?php echo $x; ?></a></td>
                            <td class="mailbox-name"><a href="#">&nbsp;&nbsp;&nbsp;<?php echo $row['description'];  ?></a></td>
                            <td class="mailbox-name"><a href="#">&nbsp;&nbsp;<?php echo $row['gldescription']; ?>&nbsp;&nbsp;&nbsp;</a></td>
                            <td class="mailbox-name"><a href="#" class="pull-right">&nbsp;&nbsp;&nbsp; <?php echo $project_steps_projectname[0]['CurrencyCode'] . ' ' . number_format($row['amount']) ; ?></a></td>
                            <td class="mailbox-name"><a href="#" class="pull-center"><?php echo claimed_status($row['isClaimedYN']) ; ?></a></td>
                            <td class="mailbox-attachment">
                        <span class="pull-right">
                            <?php if($row['isClaimedYN']==0)
                            {
                                $status = '<span class="pull-right"><a onclick="load_claim_project_staus('.$row['projectStageDetailID'].','.$row['projectStageID'].')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;|&nbsp;<a
                                    onclick="delete_claim_project('.$row['projectStageDetailID'].','.$row['projectStageID'].');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                                $status .= '</span>';
                                echo $status;
                            }

                            ?>
                            </td>


                        </tr>
                        <?php
                        $x++;

                    }
                    ?>

                <?php }
            }
            ?>
            </tbody>

        </table>
    </div>
    <hr>
    <br>
    <?php if (!empty($percentagetot)) { ?>
        <div class="row" style="width: 50%;margin-left: 20%;">
            <label style="padding-left: 32%">OVERALL PROJECT DEVELOPMENT</label>
            <?php echo project_status($percentagetot['percentage']) ?>
        </div>
    <?php } ?>
    <?php
} else { ?>
    <div class="search-no-results">THERE ARE NO PROJECT STEPS DEFINED.</div>
    <?php
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();

    });
</script>
