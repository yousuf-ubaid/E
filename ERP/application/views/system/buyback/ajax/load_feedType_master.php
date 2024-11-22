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
</style>
<?php
if (!empty($feedTypes)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Description</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Short Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($feedTypes as $val) {
                $feedAssign = $this->db->query("SELECT dispatchAutoID FROM srp_erp_buyback_feedtypes LEFT JOIN srp_erp_buyback_dispatchnotedetails ON srp_erp_buyback_dispatchnotedetails.feedType = srp_erp_buyback_feedtypes.buybackFeedtypeID WHERE buybackFeedtypeID = {$val['buybackFeedtypeID']} ")->row_array();
                ?>
                <tr>
                    <td class="mailbox-star" width="10%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="30%"><?php echo $val['description'] ?></td>
                    <td class="mailbox-star" width="40%"><?php echo $val['shortCode'] ?></td>
                    <?php if(!empty($feedAssign['dispatchAutoID'])){?>
                    <td class="mailbox-attachment" width="20%">
                        <span class="pull-right"><a href="#" onclick="edit_feedType(<?php echo $val['buybackFeedtypeID'] ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span></a>
                        </span>
                    </td>
                    <?php } else { ?>
                    <td class="mailbox-attachment" width="10%">
                        <span class="pull-right"><a href="#" onclick="edit_feedType(<?php echo $val['buybackFeedtypeID'] ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_feedType(<?php echo $val['buybackFeedtypeID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                    </td>
                    <?php } ?>
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
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO FEED TYPES TO DISPLAY, PLEASE CREATE USING <b>FEED TYPE</b> TO DISPLAY.</div>
    <?php
}
?>