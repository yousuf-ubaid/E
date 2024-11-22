<?php $CI = get_instance() ?>
<?php $primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage); ?>
<div><b><?php echo $this->lang->line('emp_leave_balance'); ?><!--Leave Balance--></b></div>
<div>
    <div id="menu">
        <ul>
            <?php
            $select = '';
            $selectWrap = '';

            $detail = array();
            if (!empty($leaveType)) {
                foreach ($leaveType as $val) {

                    $leaveTypeID = $val['leaveTypeID'];
                    $leave_des = 'leaveTypeID_'.$leaveTypeID;
                    if ($val['policyMasterID'] == 2) {

                        $select .= "sum(if(leaveType='{$leaveTypeID}',hoursEntitled,0)) - IFNULL( ( SELECT SUM(if(leaveTypeID='{$leaveTypeID}',hours,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1 ), 0 ) as '{$leave_des}',";

                        $selectWrap .= "  CONCAT(FLOOR({$leave_des} / 60), 'h ', ABS(MOD({$leave_des}, 60)), 'm') AS '{$leave_des}', ";
                    } else {

                        $str = "sum(if(leaveType='{$leaveTypeID}',daysEntitled,0)) - IFNULL( ( SELECT SUM(if(leaveTypeID='{$leaveTypeID}',days,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1 ), 0 ) as '{$leave_des}',";
                        $isCarryForwardStr = $isCarryForwardStr2 = '';
                        if ($val['policyMasterID'] == 1) {
                            $isCarryForward = $CI->db->query("SELECT isCarryForward FROM srp_erp_leavegroupdetails  t1
                                                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                                                WHERE leaveTypeID={$leaveTypeID} AND EIdNo={$empID}")->row('isCarryForward');

                            if($isCarryForward == 0){
                                $currentYear = date('Y');
                                $str =  " IFNULL( (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail t1
                                            LEFT JOIN `srp_erp_leaveaccrualmaster` t2 ON t1.leaveaccrualMasterID = t2.leaveaccrualMasterID 
                                            WHERE empID = {$empID}  AND leaveType = {$leaveTypeID}  AND confirmedYN = 1 AND `year`='{$currentYear}'),0 ) - 
                                          IFNULL( ( SELECT SUM(if(leaveTypeID='{$leaveTypeID}',days,0)) FROM srp_erp_leavemaster 
                                            WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1 
                                            AND year(startDate) = '{$currentYear}'), 0 
                                          ) as '{$leave_des}',";
                            }
                        }

                        if ($val['policyMasterID'] == 3) {
                            $isCarryForward = $CI->db->query("SELECT isCarryForward FROM srp_erp_leavegroupdetails  t1
                                                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                                                WHERE leaveTypeID={$leaveTypeID} AND EIdNo={$empID}")->row('isCarryForward');

                            if($isCarryForward == 0){
                                $currentYear = date('Y');
                                $currentMonth = date('m');
                                $str =  " IFNULL( (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail t1
                                            LEFT JOIN `srp_erp_leaveaccrualmaster` t2 ON t1.leaveaccrualMasterID = t2.leaveaccrualMasterID
                                            WHERE empID = {$empID}  AND leaveType = {$leaveTypeID}  AND confirmedYN = 1 AND `year`='{$currentYear}' AND `month`='{$currentMonth}'),0 ) -
                                          IFNULL( ( SELECT SUM(if(leaveTypeID='{$leaveTypeID}',days,0)) FROM srp_erp_leavemaster
                                            WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1
                                            AND year(startDate) = '{$currentYear}'), 0
                                          ) as '{$leave_des}',";
                            }
                        }

                        $select .= $str;

                        $selectWrap .= "{$leave_des},";
                    }

                }
                //die($selectWrap);
                $qry2 = "select $selectWrap  Ename2 from (SELECT $select CONCAT(ECode, ' - ', Ename2) AS Ename2, srp_erp_leaveaccrualdetail.description, daysEntitled, srp_erp_leavetype.description AS leavetype, srp_erp_leavetype.leaveTypeID, empID, srp_erp_leaveaccrualmaster.policyMasterID, leaveaccrualDetailID, srp_erp_leaveaccrualmaster.confirmedYN FROM srp_employeesdetails INNER JOIN srp_erp_leaveaccrualdetail on empID= EIdNo INNER JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID AND   srp_erp_leaveaccrualmaster.confirmedYN = 1   INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType WHERE  srp_employeesdetails.leaveGroupID = {$leavGroupID} AND EIdNo=$empID GROUP BY empID ORDER BY srp_erp_leavetype.description asc) t";
                $detail = $CI->db->query($qry2)->result_array();

            }

            if ($detail) {
                foreach ($detail as $val) {

                    if ($leaveType) {
                        foreach ($leaveType as $row) {
                            $leaveTypeID = $row['leaveTypeID'];
                            $leave_des = 'leaveTypeID_'.$leaveTypeID;

                            $balance = $row['description'] . ' Balance : ';
                            echo " <li><div style='min-width: 120px'>{$row['description']}</div></li><li><div style='min-width: 80px'><i class='fa fa-caret-right' aria-hidden='true'></i> &nbsp; <a onclick='leaveBalanceModal(\"<b>$balance</b>\",{$row['leaveTypeID']}, $empID)'>$val[$leave_des]</a></div></li>";
                        }
                    }
                }
            } else {
                $Noresultsfound = $this->lang->line('common_no_records_found');
                $Leavegroupnotassigned = $this->lang->line('emp_leave_group_not_assigned');
                echo ' <li style="color: darkred"> <i>' . $Noresultsfound . ' <!--No results found--> . ' . $Leavegroupnotassigned . ' .</i></li>';/*Leave group not assigned*/
            }
            ?>
        </ul>
    </div>
</div>
<?php




