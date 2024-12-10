<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<div id="tbl_unbilled_grv">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_task_report_re');?> </div><!--Task Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($task)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>

                        <th>#</th>
                        <th>Task Type</th>
                        <th>Task Description</th><!--Task Title-->
                        <th>Assignee</th>
                        <th>Est. Start Date</th><!--Date Created-->
                        <th>Est. End Date</th><!--Date Due-->
                        <th style="min-width:5%">Priority</th><!--Date Due-->
                        <th><?php echo $this->lang->line('common_status');?></th><!--Status-->
                        <th>Days</th><!--Percent-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    foreach ($task as $row) { ?>
                        <tr>
                            <td><?php echo $x; ?></td>
                            <td><?php echo $row['categoryDescription'] ?></td>
                            <td><?php echo $row['subject'] ?></td>
                            <td><?php
                                $companyID = $this->common_data['company_data']['company_id'];
                                $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where documentID = 2 AND companyID = ".$companyID." AND MasterAutoID = " . $row['taskID'] . "")->result_array();
                                if (!empty($assignees)) {
                                    foreach ($assignees as $val) {
                                        echo $val['Ename2'] . ",";
                                    }
                                }
                                ?></td>
                            <td><?php echo $row['starDate'] ?></td>

                            <td><?php echo $row['DueDate'] ?></td>
                            <td><?php echo $row['PriorityTask'] ?></td>
                            <td><?php echo $row['statusDescription'] ?></td>
                            <td>
                                <?php

                                if($row['isClosed'] !=1)
                                {
                                    echo $row['datedifferencetask'];
                                }else
                                {
                                    echo '<strong style="color: red;">Closed</strong>';
                                }
                                ?>
                            </td>
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
                            <td style='font-size: 10px;font-weight: bold;font-family: tahoma;text-decoration: underline;'>Sub Task</td>
                            <td colspan="5"> </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;font-weight: bold;font-family: tahoma;"> </td>
                            <td style="text-align: right;font-weight: bold;font-family: tahoma;"><b>SN</b></td>
                            <td style='font-size: 10px;font-weight: bold;font-family: tahoma;'>Description</b></td>
                            <td style='font-size: 10px;font-weight: bold;font-family: tahoma;'><b>Assignee</b></td>
                            <td style='font-size: 10px;font-weight: bold;font-family: tahoma;'><b>Date From</b></td>
                            <td style='width: 12%;font-size: 10px;font-weight: bold;font-family: tahoma;'><b>Date To</b></td>
                            <td style="width: 21%;font-size: 10px;font-weight: bold;font-family: tahoma; text-align: center;"><b>Estimated Days | Hours</b></td>
                            <td style="width: 18%;font-size: 10px;font-weight: bold;font-family: tahoma;"><b>Remaining Days | Hours</b></td>

                        </tr>
                        <?php foreach ($subtask as $val) {


                            $assigneesubtask = $this->db->query("select emp.Ename2 as employeename from srp_erp_crm_assignees assignees LEft join srp_employeesdetails emp on emp.EIdNo = assignees.empID where
companyID = $companyid ANd MasterAutoID = '{$val['subTaskID']}' ANd documentID = 10")->result_array();

                            $timespentsubtask = $this->db->query("select SUM(timeSpent) as subtasktimespent from srp_erp_crm_subtasksessions where companyID = $companyid And taskID = '{$val['taskID']}' And subTaskID = '{$val['subTaskID']}'")->row_array();
                            ?>
                            <tr>
                                <td style="text-align: right"></td>
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
                            </tr>


                            <?php
                            $xs++;
                        } }?>



                        <?php
                        $x++;
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);
            }
            ?>
        </div>
    </div>
</div>