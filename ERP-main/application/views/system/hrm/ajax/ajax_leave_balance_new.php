<?php
$CI = get_instance();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

    <div><b><?php echo $this->lang->line('emp_leave_balance'); ?><!--Leave Balance--></b></div>

    <div id="menu">
        <ul>
            <?php
            $companyID = current_companyID();
            $detail = array();
            $asOfDate = date('Y-m-d');
            $currentYear = date('Y');
            $monthlyFirstDate = date('Y-m-01', strtotime($asOfDate));
            $monthlyEndDate = date('Y-m-t', strtotime($asOfDate));
            $yearFirstDate = date('Y-01-01', strtotime($asOfDate));
            $yearEndDate = date('Y-12-31', strtotime($asOfDate));


            $carryForwardLogic = "IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3), 
                                  IF( leavGroupDet.policyMasterID=1,  YEAR(accrualDate) = {$currentYear},
                                  accrualDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), accrualDate <= '{$yearEndDate}') ";

            $carryForwardLogic2 = "AND IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3),
                                   IF( leavGroupDet.policyMasterID=1,  endDate BETWEEN '{$yearFirstDate}' AND '{$yearEndDate}',
                                   endDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), endDate <= '{$yearEndDate}') ";
            $select = 'EIdNo';

            if (!empty($leaveType)) {
                foreach ($leaveType as $val) {

                    $typeID = $val['leaveTypeID'];
                    $balance = '`'.$typeID.'balance`';

                    $select .= ", round(
                        ( IFNULL(
                          (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                           JOIN (
                                SELECT leaveaccrualMasterID, confirmedYN,
                                CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                           ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
                           JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                           AND leavGroupDet.leaveTypeID = '{$typeID}'
                           WHERE {$carryForwardLogic} AND detailTB.leaveType = '{$typeID}' AND leavGroupDet.policyMasterID IN (1,3)
                           AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) AND detailTB.empID = EidNo
                           ), 0
                          ) -
                          IFNULL(
                            (SELECT SUM(days) FROM srp_erp_leavemaster 
                             JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                             AND leavGroupDet.leaveTypeID = '{$typeID}'
                             WHERE srp_erp_leavemaster.leaveTypeID = '{$typeID}' AND
                             (cancelledYN = 0 OR cancelledYN IS NULL) AND leavGroupDet.policyMasterID IN (1,3) AND
                             srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 {$carryForwardLogic2}
                            ), 0
                          )
                        ) , 2) AS $balance ";



                }
                $detail = $CI->db->query("SELECT $select FROM srp_employeesdetails WHERE EIdNo = {$empID} AND Erp_companyID='{$companyID}'")->result_array();
                //echo '<pre>'.$this->db->last_query().'</pre>';
            }

            if ($detail) {
                foreach ($detail as $val) {

                    if ($leaveType) {
                        foreach ($leaveType as $row) {
                            $leaveTypeID = $row['leaveTypeID'];
                            $leave_des = $leaveTypeID.'balance';

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


<?php
