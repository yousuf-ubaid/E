<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
                <div id="toolbar">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="toolbar-title">
                                <i class="fa fa-file-text" aria-hidden="true"></i> <?php echo $this->lang->line('crm_task_reports');?>
                            </div><!--Task Reports-->
                        </div>
                        <div class="col-sm-4">
                        <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('task')">
                                <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF
            </span> </a></span>

                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                      <?php  echo export_buttons('taskreport', 'Task Report', True, false)?>
                              </span>

                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12" id="taskreport">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="width: 12%;">Task Type</th><!--Task Title-->
                                            <th style="width: 16%;">Task Description </th><!--Task Title-->
                                            <th>Assignee</th><!--Date Created-->
                                            <th style="width: 13%;">Est. Start Date</th><!--Date Created-->
                                            <th>Est. End Date</th><!--Date Due-->
                                            <th>Priority</th><!--Date Due-->
                                            <th style="width: 13%;"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                                            <th style="width: 13%;">Days</th><!--Status-->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        if (!empty($task)) {

                                            foreach ($task as $row) { ?>
                                                <tr>
                                                    <td><?php echo $x; ?></td>
                                                    <td><?php echo $row['categoryDescription'] ?></td>
                                                    <td><?php echo $row['subject'] ?></td>
                                                    <td><?php $companyID = $this->common_data['company_data']['company_id'];
                                                        $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where documentID = 2 AND companyID = ".$companyID." AND MasterAutoID = " . $row['taskID'] . "")->result_array();
                                                        if (!empty($assignees)) {
                                                            foreach ($assignees as $val) {
                                                                echo $val['Ename2'] . ",";} } ?> </td>
                                                    <td><?php echo $row['starDate'] ?></td>

                                                    <td><?php echo $row['DueDate'] ?></td>
                                                    <td><?php echo $row['PriorityTask'] ?></td>
                                                    <td style="text-align: center"><?php echo $row['statusDescription'] ?></td>
                                                    <td style="text-align: center">   <?php

                                                        if($row['isClosed'] !=1)
                                                        {
                                                            echo $row['datedifferencetask'];
                                                        }else
                                                        {
                                                            echo '<strong style="color: red;">Closed</strong>';
                                                        }
                                                        ?></td>
                                                    <!--<td style="text-align: center"><?php /*echo $row['progress']." %" */?></td>-->
                                                </tr>

                                                <?php
                                                $subtask = $this->db->query("SELECT *,DATE_FORMAT(startDate, '%Y-%m-%d') as datecreated,DATE_FORMAT(endDate, '%Y-%m-%d') as enddatecreated,DATEDIFF(DATE_FORMAT( endDate, '%Y-%m-%d' ),CURDATE()) as datedifferencesubtask FROM `srp_erp_crm_subtasks` where companyID = $companyID And taskID = '{$row['taskID']}'")->result_array();


                                                ?>
                                <?php if(!empty($subtask)) {
                                                    $xs = 1;
                                                    $companyid = current_companyID();
                                                     ?>
                                                        <tr>
                                                            <td></td>
                                                            <td><b><u>Sub Task</u></b></td>
                                                            <td colspan="5"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td> </td>
                                                            <td style="text-align: right;"><b>SN</b></td>
                                                            <td><b>Description</b></td>
                                                            <td><b>Assignee</b></td>
                                                            <td><b>Date From</b></td>
                                                            <td style="width: 12%;"><b>Date To</b></td>
                                                            <td style="width: 24%; text-align: center;"><b>Estimated Days | Hours</b></td>
                                                            <td style="width: 24%"><b>Remaining Days | Hours</b></td>
                                                            <td>&nbsp;</td>

                                                        </tr>
                                                    <?php foreach ($subtask as $val) {


                                                        $assigneesubtask = $this->db->query("select emp.Ename2 as employeename from srp_erp_crm_assignees assignees LEft join srp_employeesdetails emp on emp.EIdNo = assignees.empID where
companyID = $companyid ANd MasterAutoID = '{$val['subTaskID']}' ANd documentID = 10")->result_array();

                                                        $timespentsubtask = $this->db->query("select SUM(timeSpent) as subtasktimespent from srp_erp_crm_subtasksessions where companyID = $companyid And taskID = '{$val['taskID']}' And subTaskID = '{$val['subTaskID']}'")->row_array();
                                                        ?>
                                                        <tr>
                                                            <td> </td>
                                                            <td style="text-align: right"><a href="#"><?php echo $xs; ?>.</a></td>
                                                            <td><a href="#"><?php echo $val['taskDescription']; ?></a></td>
                                                            <td><a href="#">
                                                                    <?php

                                                                    if (!empty($assigneesubtask)) {
                                                                        foreach ($assigneesubtask as $valemp) {
                                                                            echo $valemp['employeename'] . ",";
                                                                        }
                                                                    }
                                                                    ?></a></td>
                                                            <td><a href="#"><?php echo $val['datecreated']; ?></td>
                                                            <td><a href="#"><?php echo $val['enddatecreated']; ?></td>
                                                            <td><a href="#"><?php echo $val['estimatedDays']; ?> |
                                                                    <?php
                                                        $hoursNew = floor($val['estimatedHours']/ 60);
                                                         $minutesnew = ($val['estimatedHours'] % 60);
                                                        echo sprintf('%02d hours %02d minutes', $hoursNew, $minutesnew); ?></a>
                                                            </td>
                                                            <td>

                                                                <?php if($val['datedifferencesubtask']>0) {?>
                                                                    <a href="#"><?php echo $val['datedifferencesubtask']?></a >
                                                                <?php } else { ?>
                                                                    <strong style="color: red;"><?php echo $val['datedifferencesubtask']?></strong>
                                                                <?php } ?>

                                                                |
                                                                 <?php
                                                                $remainingtime = ($val['estimatedHours'] - $timespentsubtask['subtasktimespent']);
                                                                $remainingtimenegative = ($val['estimatedHours'] - $timespentsubtask['subtasktimespent']);

                                                                if($remainingtime > 0) {
                                                                    $hoursNewremaining = floor($remainingtime/ 60);
                                                                    $minutesnewremaining = ($remainingtime % 60); ?>
                                                                    <a href="#"> <?php echo sprintf('%02d hours %02d minutes', $hoursNewremaining, $minutesnewremaining); ?> </a >
                                                                <?php } else {
                                                                    $remainingtimenegative = $remainingtime * -1;
                                                                    $hoursNewremaining = floor($remainingtimenegative/ 60);
                                                                    $minutesnewremaining = ($remainingtimenegative % 60); ?>
                                                                        <strong style="color: red;"><?php echo sprintf('%02d hours %02d minutes', $hoursNewremaining, $minutesnewremaining);?></strong>

                                                                <?php }?></td>
                                                            <td>&nbsp;</td>
                                                        </tr>


                                                    <?php
                                                        $xs++;
                                                    }

                                                }?>
                                                <?php
                                                $x++;

                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="6" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?> </td><!--No Records Found -->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>

