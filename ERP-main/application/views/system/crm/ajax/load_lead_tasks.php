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

            <?php

            $count = 5;
            $category = array();
            $date_format = date_format_policy();
            foreach ($tasks as $val) {
                $category[$val["Documenttype"]][] = $val;
            }
            if (!empty($category)) {
                foreach ($category as $key => $value) {
                    $textdecoration = '';

                    echo "<tr><td  colspan='12' class='headrowtitle' style='border-top: 1px solid #ffffff;'><strong><u>" . $key . "</u></strong></td></tr>";
                    foreach ($value as $key2 => $value1) {
                        if ($value1['status'] == 7) {
                            $textdecoration = 'textClose';
                        }
                        echo "<td class='mailbox-star' width='11%'><a href='#'>".$value1['documentSystemCode']."</a></td>";
                        echo "<td class=\"mailbox-star\" width=\"10%\"><span class=\"label\" style=\"background-color:".$value1['backGroundColor']."; color:". $value1['textColor']."; font-size: 11px;\">".$value1['categoryDescription']."</span></td>";
                        echo "<td class='mailbox-name'><a href='#' class='.$textdecoration.' onclick=\"fetchPage('system/crm/task_edit_view','".$value1['taskID']."','View Task','".$masterID."','Lead')\">".$value1['subject']."</a></td>";
                        echo "<td class='mailbox-name'><a href='#'
                                                class='.$textdecoration.'>".$value1['starDate'] ."-".  $value1['DueDate']."</a> </td>";

                        echo "<td><a href='#'>";

                            if ($value1['Priority'] == 3)
                            {
                            echo "<button type='button'
                                        class='priority-btn high-ptry tipped-top active' title='High Priority'>!!!
                                </button><span class='tddata'></span>";
                            }else if ($value1['Priority'] == 2)
                            {
                                echo "<button type=\"button\"
                                        class=\"priority-btn med-ptry tipped-top active\" title=\"Medium Priority\">!!
                                </button> <span class=\"tddata\"></span>";
                            }else if($value1['Priority'] == 1)
                            {
                                echo " <button type=\"button\"
                                        class=\"priority-btn low-ptry tipped-top\" title=\"Low Priority\">!
                                </button><span class=\"tddata\"></span>";
                            }

                            echo "</td>";
                        echo "<td class=\"mailbox-name\"><a href=\"#\"
                                                class=".$textdecoration.">".$value1['progress']." %</a></td>";
                        echo "<td class=\"mailbox-name\">";
                        $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where MasterAutoID = " . $value1['taskID'] . "")->result_array();
                        if (!empty($assignees)) {
                            foreach ($assignees as $row) {
                                echo $row['Ename2'] . ",";
                            }
                        }
                        echo "</td>";
                        echo "<td class=\"mailbox-star\" width=\"10%\"><span class=\"label\"
                                                               style=\"background-color:#9e9e9e; color:#ffffff; font-size: 11px;\">".$value1['statusDescription']."</span>
                    </td>";


                            echo "</tr>";



                    }
                    echo "<tr>";
                }

                echo "</tr>";
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