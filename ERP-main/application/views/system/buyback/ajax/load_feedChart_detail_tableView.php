<?php

$comapnyid = current_companyID();
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
$tasks = load_all_task_types();
$deleteOption = $this->db->query("SELECT MAX(feedscheduledetailID) as feedscheduledetailID FROM srp_erp_buyback_feedscheduledetail WHERE companyID = {$comapnyid}")->row_array();

if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;" colspan="2">Age</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);">&nbsp;</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;" colspan="2">body weight</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center" colspan="2">FCR</td>
                <?php if($tasks)
                echo "<td class='headrowtitle' style='border: solid 1px rgba(158, 158, 158, 0.23);text-align: center' colspan='" . count($tasks) . "'> Tasks </td>"?>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">&nbsp;</td>
            </tr>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Age </td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Feed per</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center">Total</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Min</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Max</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Min</td>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Max</td>
                <?php foreach ($tasks AS $val)
                {
                    echo "<td class='headrowtitle' style='border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;'><span title='" . $val['description'] . "' rel='tooltip'>" . $val['shortDescription'] . "</span></td>";
                }?>
                <td class="headrowtitle" style="border: solid 1px rgba(158, 158, 158, 0.23);text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            $total = 0;
            foreach ($detail as $val) {
                $companyID = $this->common_data['company_data']['company_id'];
                ?>
                <tr>
                    <td class="mailbox-star text-right"><?php echo $val['age'] ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['perDayFeed']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['totalAmount']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['minBodyWeight']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['maxBodyWeight']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['minFCR']; ?></td>
                    <td class="mailbox-star text-right"><?php echo $val['maxFCR']; ?></td>
                    <?php
                        foreach ($tasks as $value)
                        {
                            $taskState = $this->db->query("SELECT taskTypeDetailsID, tasktypeID, feedscheduledetailID, isActive FROM srp_erp_buyback_tasktypes_details WHERE companyID = {$companyID} AND feedscheduledetailID = {$val['feedscheduledetailID']} AND tasktypeID = {$value['tasktypeID']} ORDER BY tasktypeID ASC")->row_array();
                    ?>   <td class='mailbox-star text-center'><input type='checkbox' <?php if(!empty($taskState) && $taskState['isActive'] == 1) echo "checked"?> disabled></td>
                      <?php } ?>



                    <td class="mailbox-attachment taskaction_td"><span class="pull-right">

                            <a onclick="edit_feedChart_detail(<?php echo $val['feedscheduledetailID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
                            <?php if($val['feedscheduledetailID'] == $deleteOption['feedscheduledetailID']) {?>
                            <a onclick="delete_feedChart_detail(<?php echo $val['feedscheduledetailID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO FEED CHART DETAIL TO DISPLAY.</div>
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