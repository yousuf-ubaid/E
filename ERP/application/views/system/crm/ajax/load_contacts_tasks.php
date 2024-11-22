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
</style>
<?php
if (!empty($tasks)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Contact Tasks</div>
                    <div class="taskcount"><?php //echo sizeof($tasks) ?></div>
                </td>
            </tr>
            <?php
            $x = 1;
            foreach ($tasks as $val) {
                $textdecoration = '';
                if ($val['status'] == 7) {
                    $textdecoration = 'textClose';
                }
                ?>
                <tr>

                    <td class="mailbox-name" width="11%"><a href="#"><?php echo $val['documentSystemCode'] ?></a></td>
                    <td class="mailbox-star" width="10%"><span class="label" style="background-color:<?php echo $val['backGroundColor'] ?>; color: <?php echo $val['textColor'] ?>; font-size: 11px;"><?php echo $val['categoryDescription'] ?></span>
                    </td>
                    <td class="mailbox-name"><a href="#" class="<?php echo $textdecoration; ?>"
                                                onclick="fetchPage('system/crm/task_edit_view','<?php echo $val['taskID'] ?>','View Task',<?php echo $masterID ?>,'contactTask')"><?php echo $val['subject'] ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#"
                                                class="<?php echo $textdecoration; ?>"><?php echo $val['starDate'] . " - " . $val['DueDate'] ?></a>
                    <td class="mailbox-name"><a href="#">
                            <?php
                            if ($val['Priority'] == 3) { ?>
                                <button type="button"
                                        class="priority-btn high-ptry tipped-top active" title="High Priority">!!!
                                </button><span class="tddata"></span>
                                <?php
                            } else if ($val['Priority'] == 2) { ?>
                                <button type="button"
                                        class="priority-btn med-ptry tipped-top active" title="Medium Priority">!!
                                </button> <span class="tddata"></span>
                                <?php
                            } else if ($val['Priority'] == 1) { ?>
                                <button type="button"
                                        class="priority-btn low-ptry tipped-top" title="Low Priority">!
                                </button><span class="tddata"></span>

                                <?php
                            }
                            ?>
                        </a>
                    </td>
                    <td class="mailbox-name"><a href="#"
                                                class="<?php echo $textdecoration; ?>"><?php echo $val['progress'] . " %"; ?></a>
                    <td class="mailbox-name"><a href="#" class="<?php echo $textdecoration; ?>"><?php
                            $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where MasterAutoID = " . $val['taskID'] . "")->result_array();
                            if (!empty($assignees)) {
                                foreach ($assignees as $row) {
                                    echo $row['Ename2'] . ",";
                                }
                            }
                            ?></a>
                    </td>
                    <td class="mailbox-star" width="10%"><span class="label"
                                                               style="background-color:#9e9e9e; color:#ffffff; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
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
    <div class="search-no-results">THERE ARE NO TASKS TO DISPLAY.</div>
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