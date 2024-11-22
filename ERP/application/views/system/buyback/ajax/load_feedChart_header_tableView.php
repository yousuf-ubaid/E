<?php
$comapanyid = current_companyID();
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
$deleteOption = $this->db->query("SELECT MAX(feedScheduleID) as feedScheduleID FROM srp_erp_buyback_feedschedulemaster WHERE companyID = {$comapanyid}")->row_array();

if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);">#</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);">Days</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);">Feed Type</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center">Feed</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            $total = 0;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star"><?php echo $val['changedDate'] ?></td>
                    <td class="mailbox-star"><?php echo $val['feedType']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['feedAmount'] . ' %'; ?></td>
                    <td class="mailbox-attachment taskaction_td"><span class="pull-right">
                            <a onclick="edit_feedChart_header(<?php echo $val['feedScheduleID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                            <?php if ($deleteOption['feedScheduleID'] == $val['feedScheduleID']) {?>
                            &nbsp;&nbsp;<a
                                onclick="delete_feedChart_header(<?php echo $val['feedScheduleID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                    <?php } ?>
                    </td>
                </tr>
                <?php
                $x++;
                $total += $val['feedAmount'];
            }
            ?>
            </tbody>
            <tfoot >
            <tr>
                <td style="min-width: 85%  !important" class="text-right sub_total" colspan="3">
                    Total  </td>
                <td style="min-width: 15% !important"
                    class="text-right total"><?php echo $total . ' %'; ?></td>
                <td style="min-width: 85%  !important" class="text-right sub_total" >&nbsp;</td>
            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO FEED CHART HEADER DETAIL TO DISPLAY.</div>
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