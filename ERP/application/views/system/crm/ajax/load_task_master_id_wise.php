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
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_category');?><!--Category--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_subject');?><!--Subject--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('crm_start_and_due_date');?><!--Start & Due Date--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_priority');?><!--Priority--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_progress');?><!--Progress--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_responsible');?><!--Responsible--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?><!--Status--></td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                $textdecoration = '';
                if ($val['isClosed'] == 1) {
                    $textdecoration = 'textClose';
                }
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-star" width="5%"><span data-id="57698933" class="noselect follow following" title="Following"></span></td>
                    <td class="mailbox-star" width="10%"><span class="label"
                                                               style="background-color:<?php echo $val['backGroundColor'] ?>; color: <?php echo $val['textColor'] ?>; font-size: 11px;"><?php echo $val['categoryDescription'] ?></span>
                    </td>
                    <td class="mailbox-name"><a href="#" class="<?php echo $textdecoration; ?>"
                                                onclick="fetch_task_edit('system/crm/task_edit_view','<?php echo $val['taskID'] ?>','View Task','CRM')"><?php echo $val['subject'] ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#"
                                                class="<?php echo $textdecoration; ?>"><?php echo $val['starDate'] . " | " . $val['DueDate'] ?></a>
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
                            $companyID = $this->common_data['company_data']['company_id'];
                            $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where documentID = 2 AND companyID = ".$companyID." AND MasterAutoID = " . $val['taskID'] . "")->result_array();
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
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_no_task_to_diplay');?>.</div><!--THERE ARE NO TASKS TO DISPLAY-->
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