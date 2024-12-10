
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('subtaskTaskdashboard', 'Crm Task', True, false);
            } ?>
        </div>
        <div class="col-md-4">
           <button type="button" onclick="clander_navigation_back();" class="btn btn-link"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i><!--Add Item-->
            </button>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="subtaskTaskdashboard">
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th style="width: 2%;">#</th>
                        <th style="width: 15%;">Document Code</th>
                        <th style="width: 10%;">Task Type</th>
                        <th style="width: 10%;">Task Description</th>
                        <th style="width: 18%;">Assignee</th>
                        <th width="13%;">Est. Start Date</th>
                        <th width="13%;">Est. End Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th style="width: 5%;">Days</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    $coltot = [];
                    $netto = 0;
                    $exchangerate = 0;
                    $decimalPlace = 2;
                    foreach($details as $dtl){
                        ?>
                        <tr>
                         <td style="border-style: none;text-align: right;"><?php echo $x ?>.</td>
                         <td class="mailbox-star" width="15%"><a href="#" >
                                    <a class="link-person noselect" href="#"  onclick="fetchPage('system/crm/task_edit_view','<?php echo $dtl['taskID'] ?>','View Task','CRM','CRM')" >   <?php echo $dtl['documentSystemCode'] ?></a>
                                    <?php //echo var_dump($val); ?>

                        </td>



                         <td style="border-style: none;text-align: left;"><?php echo $dtl['categoryDescription']?></td>
                         <td style="border-style: none;text-align: left;"><?php echo $dtl['taskdescription']?></td>
                         <td style="border-style: none;text-align: left;">
                             <?php $companyID = $this->common_data['company_data']['company_id'];
                             $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where documentID = 2 AND companyID = ".$companyID." AND MasterAutoID = " . $dtl['taskID'] . "")->result_array();
                             if (!empty($assignees)) {
                                 foreach ($assignees as $val) {
                                     echo $val['Ename2'] . ",";} } else {

                                 echo  '-';
                             } ?>
                         </td>
                            <td style="border-style: none;text-align: left;"><?php echo $dtl['starDate']?></td>
                            <td style="border-style: none;text-align: left;"><?php echo $dtl['DueDate']?></td>
                            <td style="border-style: none;text-align: left;"><?php echo $dtl['PriorityTask']?></td>
                            <td style="border-style: none;text-align: left;"><?php echo $dtl['statusDescription']?></td>
                            <td style="border-style: none;text-align: left;">


                                <?php

                                if($dtl['isClosed'] !=1)
                                {
                                    echo $dtl['datedifferencetask'];
                                }else
                                {
                                echo ' <strong style="color: red;">Closed</strong>';
                                }
                              ?>


                            </td>
                        </tr>

                        <?php
                        $subtask = $this->db->query("SELECT *,DATE_FORMAT(startDate, '%Y-%m-%d') as datecreated,DATE_FORMAT(endDate, '%Y-%m-%d') as enddatecreated,DATEDIFF(DATE_FORMAT( endDate, '%Y-%m-%d' ),CURDATE()) as datedifferencesubtask FROM `srp_erp_crm_subtasks` where companyID = $companyID And taskID = '{$dtl['taskID']}'")->result_array();


                        ?>
                        <?php if(!empty($subtask)) {
                            $xs = 1;
                            $companyid = current_companyID();
                            ?>
                            <tr>
                                <td style="border-style: none;"></td>
                                <td style="border-style: none;"></td>
                                <td style="border-style: none;text-align: center;"><b><u>Sub Task</u></b></td>
                                <td colspan="5" style="border-style: none;"> </td>
                            </tr>
                            <tr style="background-color: LightCyan;">
                                <td style="text-align: right;border-style: none;"> </td>
                                <td style="text-align: right;border-style: none;"><b>SN</b></td>
                                <td style="border-style: none;"><b>Description</b></td>
                                <td style="border-style: none;"><b>Assignee</b></td>
                                <td style="border-style: none;"><b>Date From</b></td>
                                <td style="width: 12%;border-style: none;"><b>Date To</b></td>
                                <td style="width: 21%;border-style: none; text-align: center;"><b>Estimated Days | Hours</b></td>
                                <td style="width: 18%;border-style: none;"><b>Remaining Days | Hours</b></td>

                            </tr>
                            <?php foreach ($subtask as $val) {


                                $assigneesubtask = $this->db->query("select emp.Ename2 as employeename from srp_erp_crm_assignees assignees LEft join srp_employeesdetails emp on emp.EIdNo = assignees.empID where
companyID = $companyid ANd MasterAutoID = '{$val['subTaskID']}' ANd documentID = 10")->result_array();

                                $timespentsubtask = $this->db->query("select SUM(timeSpent) as subtasktimespent from srp_erp_crm_subtasksessions where companyID = $companyid And taskID = '{$val['taskID']}' And subTaskID = '{$val['subTaskID']}'")->row_array();
                                ?>
                                <tr style="background-color: LightCyan;">
                                    <td style="text-align: right;border-style: none;"> </td>
                                    <td style="text-align: right;border-style: none; "><a href="#"><?php echo $xs; ?>.</a></td>
                                    <td style="border-style: none;"><a href="#"><?php echo $val['taskDescription']; ?></a></td>
                                    <td style="border-style: none;"><a href="#">
                                            <?php

                                            if (!empty($assigneesubtask)) {
                                                foreach ($assigneesubtask as $valemp) {
                                                    echo $valemp['employeename'] . ",";
                                                }
                                            }
                                            ?></a></td>
                                    <td style="border-style: none;"><a href="#"><?php echo $val['datecreated']; ?></td>
                                    <td style="border-style: none;"><a href="#"><?php echo $val['enddatecreated']; ?></td>
                                    <td style="border-style: none;"><a href="#"><?php echo $val['estimatedDays']; ?> |
                                            <?php
                                            $hoursNew = floor($val['estimatedHours']/ 60);
                                            $minutesnew = ($val['estimatedHours'] % 60);
                                            echo sprintf('%02d hours %02d minutes', $hoursNew, $minutesnew); ?></a>
                                    </td>
                                    <td style="border-style: none;">

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
                            }

                        }?>

                        <?php
                        $x ++;
                    }
                    ?>

                    <tr>
                      <!--  <td><b>Total</b></td>-->
                        <?php
/*                        foreach($header as $key => $headval){
                            $tot=array_sum($coltot[$key]);
                            */?><!--
                            <td class="text-right reporttotal "><?php /*echo number_format($tot, $decimalPlace) ; */?></td>
                            <?php
/*                        }
                        */?>
                        <td class="text-right reporttotal "><?php /*echo number_format($netto, $decimalPlace) ; */?></td>-->
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                No Records found <!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
    function test()
    {
        fetchPage('system/crm/dashboard', '', 'Dashboard');
    }

</script>