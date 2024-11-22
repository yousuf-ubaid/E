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

</style>
<br>
<?php
if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">Family Member</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">Damage</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">Remarks</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">Estimated
                    Amount
                </td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">Paid
                    Amount
                </td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center">Action
                </td>
            </tr>
            <?php
            $x = 1;
            foreach ($detail as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['name']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['Description']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['remarks']; ?></a></td>
                    <td class="mailbox-name"><a href="#"
                                                class="pull-right"><?php echo number_format($val['estimatedAmount'], 2); ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#"
                                                class="pull-right"><?php echo number_format($val['paidAmount'], 2); ?></a>
                    </td>

                    <td class="mailbox-attachment">
                        <span class="pull-right">
                            <?php
                            $status = '<span class="pull-right"><a onclick="edit_human_injury_assessment('.$val['humanInjuryID'].')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;|&nbsp;<a
                                    onclick="delete_human_injury_assessment('.$val['humanInjuryID'].');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                            $status .= '</span>';
                            echo $status;
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
    <div class="search-no-results">THERE ARE NO HUMAN INJURY TO DISPLAY.</div>
    <?php
}
?>