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
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">
                <td class="task-cat-upcoming" colspan="12">
                    <div class="task-cat-upcoming-label">Tasks</div><!--Latest Tasks-->
                    <div class="taskcount"></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CODE</td>
              <!--  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>-->
              <!--  <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CODE</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_category');?><!--Category--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">DETAIL</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_priority');?><!--Priority--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_progress');?><!--Progress--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_responsible');?><!--Responsible--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;width:15%;">Created By</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;color:#000 !important; ;"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;color:#000 !important; "><?php echo $this->lang->line('common_action');?><!--Action--></td>
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
                    <td class="mailbox-star"> </td>
                    <td class="mailbox-star" width="11%"><a href="#" class="<?php echo $textdecoration; ?>">
                                    <a class="link-person noselect" href="#" class="<?php echo $textdecoration; ?>" onclick="fetchPage('system/task_managment/task_edit_view','<?php echo $val['taskID'] ?>','View Task','CRM','CRM')" >   <?php echo $val['documentSystemCode'] ?></a>
                                    <?php //echo var_dump($val); ?>

                    </td>
                  <!--  <td class="mailbox-name"><a href="#" class="numberColoring"><a href="#" class="<?php /*echo $textdecoration; */?>"><?php /*echo $val['documentSystemCode'] */?></a></td>-->

                    <td class="mailbox-star" width="10%"><span class="label" style="background-color:<?php echo $val['backGroundColor'] ?>; color: <?php echo $val['textColor'] ?>; font-size: 11px;"><?php echo $val['categoryDescription'] ?></span></td>



                    <td class="mailbox-name taskaction_td">

                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Subject :<a
                                            class="link-person noselect" href="#" class="<?php echo $textdecoration; ?>" onclick="fetchPage('system/task_managment/task_edit_view','<?php echo $val['taskID'] ?>','View Task','CRM','CRM')"><?php echo $val['subject']; ?></a></strong><br><strong class="contacttitle">Start Date : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['starDate']; ?></a><br><strong
                                        class="contacttitle">Due Date : </strong><a
                                            class="link-person noselect" href="#"><?php echo $val['DueDate']; ?></a></div></div></td>

<!--
                        <a href="#" class="<?php /*echo $textdecoration; */?>" onclick="fetchPage('system/crm/task_edit_view','<?php /*echo $val['taskID'] */?>','View Task','CRM','CRM')"><?php /*echo $val['subject'] */?></a><br><?php /*echo $val['starDate'] . " | " . $val['DueDate'] */?></td>-->



                    <?php if ($type == 1) {
                        ?> <!--Cheack if it is project or a opportunitie  1 for project 2 for opportunities-->
                        <!-- <td class="mailbox-name taskprojecteditview"><a href="#" class="<?php echo $textdecoration; ?>" onclick="fetchPage('system/crm/task_edit_view','<?php echo $val['taskID'] ?>','View Task','<?php echo $val['projectID'] ?>','ProjectsTask')"><?php echo $val['subject'] ?></a><br><?php echo $val['starDate'] . " | " . $val['DueDate'] ?></td> -->
                    <?php } elseif ($type == 2) {?>
                        <!-- <td class="mailbox-name taskprojecteditview"><a href="#" class="<?php echo $textdecoration; ?>" onclick="fetchPage('system/crm/task_edit_view','<?php echo $val['taskID'] ?>','View Task','<?php echo $opportunityID ?>','opportunitie')"><?php echo $val['subject'] ?></a><br><?php echo $val['starDate'] . " | " . $val['DueDate'] ?></td> -->
                    <?php }?>



                    <td class="mailbox-name"><a href="#">
                            <?php
                            if ($val['Priority'] == 3) { ?>
                                <button type="button"
                                        class="priority-btn high-ptry tipped-top active" title="High Priority">!!!
                                </button><span class="tddata"></span>
                                <?php
                            } elseif ($val['Priority'] == 2) { ?>
                                <button type="button"
                                        class="priority-btn med-ptry tipped-top active" title="Medium Priority">!!
                                </button> <span class="tddata"></span>
                                <?php
                            } elseif ($val['Priority'] == 1) { ?>
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
                            // $companyID = $this->common_data['company_data']['company_id'];
                            $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_task_assignees JOIN srp_employeesdetails ON srp_erp_task_assignees.empID = srp_employeesdetails.EIdNo where documentID = 2 AND  MasterAutoID = " . $val['taskID'] . "")->result_array();
                    if (!empty($assignees)) {
                        foreach ($assignees as $row) {
                            echo $row['Ename2'] . ",";
                        }
                    }
                    ?></a>
                    </td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle" style="color:#aaa;">User: </strong><a class="link-person noselect" href="#"><?php echo ucwords(trim_value($val['createdUserName'], 16)); ?></a><br>
                                <strong class="contacttitle"style="color:#aaa;"> Date: </strong><a class="link-person noselect" href="#"><?php echo $val['createdDateTime']; ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%"><span class="label" style="background-color:<?php echo $val['statusBackgroundColor']?>; color:#000; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                    </td>
                    <td class="mailbox-attachment taskaction_td"><span class="pull-right">
                            <?php
                            if ($val['isClosed'] == 1) { ?>
                                <div class="actionicon"><span class="glyphicon glyphicon-ok"
                                                              style="color:rgb(255, 255, 255);" title="completed"></span>
                                </div>
                                <?php
                            } else {
                                if ($val['visibility'] == 2) { ?>
                                <i class="fa fa-lock" aria-hidden="true" style="font-size: 15px;"
                                   title="Private Task"></i>&nbsp;&nbsp;
                                    <?php
                                }
                                ?>
                                <?php
                                // $companyid = current_companyID();
                                $taskid = $val['taskID'];
                                $currentuser = current_userID();
                                $results = $this->db->query("SELECT empID FROM `srp_erp_task_assignees` where  documentID = 2 AND MasterAutoID = '{$taskid}' AND empID = '{$currentuser}'")->row_array();
                                $results1 = $this->db->query("SELECT permissionID FROM `srp_erp_task_documentpermission` where documentID = 2 AND documentAutoID = '{$taskid}'")->row_array();

                                
                                if ($results1['permissionID'] == 4) {
                                    $permission = 4;
                                } else {
                                    $permission = 0;
                                }
                               
                               
                                if (!empty($results)) {
                                    $assignuser = 1;
                                } else {
                                    $assignuser = 0;
                                }
                                ?>



                            <a href="#"
                               onclick="edit_task('<?php echo $val['taskID'] ?>','<?php echo $val['createdUserIDtask']?>','<?php echo $assignuser ?>','<?php echo $permission ?>');"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_task(<?php echo $val['taskID'] ?>);"><span title="Delete" rel="tooltip"
                                                                                           class="glyphicon glyphicon-trash glyphicon-trash-btn"
                                                                                           ></span></a></span><!--Edit Task-->
                                <?php
                            }
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
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_no_task_to_diplay');?>.</div><!--THERE ARE NO TASKS TO DISPLAY-->
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>
