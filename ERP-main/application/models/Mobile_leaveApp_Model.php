<?php

/**
 * Created by PhpStorm.
 * Date: 20/04/2017
 * Time: 10:26
 *
 * Code cleared on 2019-08-01
 *
 * change log
 *      -   removed unwanted comments
 *      -   change token key name HTTP_TOKEN_KEY -> HTTP_SME_API_KEY
 *
 */
class Mobile_leaveApp_Model extends ERP_Model
{

    public function __construct()
    {
        $this->load->library('JWT');
        $this->load->helper('employee');
        $this->load->library('S3');
        $this->load->model('Employee_model');
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        if (isset($_SERVER['HTTP_SME_API_KEY']) && $_SERVER['HTTP_SME_API_KEY'] != 'null' && $_SERVER['HTTP_SME_API_KEY'] != 'undefined') {
            $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
            $output['token'] = $this->jwt->decode($tokenKey, "token");

            $config['hostname'] = $output['token']->db_host;
            $config['username'] = $output['token']->db_username;
            $config['password'] = $output['token']->db_password;
            $config['database'] = $output['token']->db_name;
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = TRUE;
            $this->load->database($config, FALSE, TRUE);

        }
    }

    function employeeLeave_details_with_more_data($masterID)
    {
        $item = $this->db->query("SELECT IFNULL(approvalComments,'') as approvalComments,leaveAvailable,leaveMasterID, empID, srp_erp_leavemaster.leaveTypeID,
                                 IF(policyMasterID = 2, DATE_FORMAT(startDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(startDate, '%Y-%m-%d')) AS startDate, currentLevelNo,
                                 IF(policyMasterID = 2, DATE_FORMAT(endDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(endDate, '%Y-%m-%d')) AS endDate, days, hours, ishalfDay,shift,
                                 documentCode, srp_erp_leavemaster.serialNo, entryDate, comments, isCalenderDays, nonWorkingDays, workingDays, srp_erp_leavemaster.leaveGroupID, isAttendance, policyMasterID,
                                 confirmedYN, confirmedByEmpID, confirmedByName, confirmedDate, approvedYN, approvedDate, approvedbyEmpID, approvedbyEmpName, coveringEmpID,
                                 srp_erp_leavemaster.companyID, srp_erp_leavemaster.companyCode, description, applicationType, requestForCancelYN, cancelledYN, isShortLeave,
                                 srp_employeesdetails.Ename2
                                 FROM srp_erp_leavemaster
                                 LEFT JOIN srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_leavemaster.coveringEmpID
                                 LEFT JOIN srp_erp_leavetype on srp_erp_leavetype.leaveTypeID=srp_erp_leavemaster.leaveTypeID
                                 WHERE srp_erp_leavemaster.leaveMasterID={$masterID}")->row_array();

        $leave_details = array();
        $leave_details['approvalComments'] = $item['approvalComments'];
        $leave_details['leaveAvailable'] = (float)$item['leaveAvailable'];
        $leave_details['leaveMasterID'] = (float)$item['leaveMasterID'];
        $leave_details['empID'] = (float)$item['empID'];
        $leave_details['leaveTypeID'] = (float)$item['leaveTypeID'];
        $leave_details['startDate'] = $item['startDate'];
        $leave_details['currentLevelNo'] = (float)$item['currentLevelNo'];
        $leave_details['endDate'] = $item['endDate'];
        $leave_details['days'] = (float)$item['days'];
        $leave_details['hours'] = (float)$item['hours'];
        $leave_details['ishalfDay'] = (float)$item['ishalfDay'];
        //$leave_details['isShortLeave'] = (float)$item['isShortLeave'];
        $leave_details['shift'] = (float)$item['shift'];
        $leave_details['documentCode'] = $item['documentCode'];
        $leave_details['serialNo'] = (float)$item['serialNo'];
        $leave_details['entryDate'] = $item['entryDate'];
        $leave_details['comments'] = $item['comments'];
        //$leave_details['isCalenderDays'] = (float)$item['isCalenderDays'];
        $leave_details['nonWorkingDays'] = (float)$item['nonWorkingDays'];
        $leave_details['workingDays'] = (float)$item['workingDays'];
        //$leave_details['leaveGroupID'] = (float)$item['leaveGroupID'];
        $leave_details['isAttendance'] = (float)$item['isAttendance'];
        $leave_details['policyMasterID'] = (float)$item['policyMasterID'];
        $leave_details['confirmedYN'] = (float)$item['confirmedYN'];
        $leave_details['confirmedByEmpID'] = (float)$item['confirmedByEmpID'];
        $leave_details['confirmedByName'] = $item['confirmedByName'];
        if ($item['confirmedDate'] != null) {
            $confirmedDate = date_create($item['confirmedDate']);
            $leave_details['confirmedDate'] = date_format($confirmedDate, "Y-m-d");
        } else {
            $leave_details['confirmedDate'] = null;
        }
        $leave_details['approvedYN'] = (float)$item['approvedYN'];
        $leave_details['approvedDate'] = $item['approvedDate'];
        $leave_details['approvedbyEmpID'] = (float)$item['approvedbyEmpID'];
        $leave_details['approvedbyEmpName'] = $item['approvedbyEmpName'];
        $leave_details['coveringEmpID'] = (float)$item['coveringEmpID'];
        $leave_details['companyID'] = (float)$item['companyID'];
        $leave_details['companyCode'] = $item['companyCode'];
        //$leave_details['description'] = $item['description'];
        $leave_details['applicationType'] = (float)$item['applicationType'];
        $leave_details['requestForCancelYN'] = (float)$item['requestForCancelYN'];
        $leave_details['cancelledYN'] = (float)$item['cancelledYN'];
        $leave_details['coveringEmpName'] = $item['Ename2'];
        return $leave_details;
    }

    function employeeLeave_details($masterID) //for leave approval popup
    {
        return $this->db->query("SELECT leaveAvailable,leaveMasterID, empID, srp_erp_leavemaster.leaveTypeID, IF(policyMasterID = 2, DATE_FORMAT(startDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(startDate, '%Y-%m-%d')) AS startDate, IF(policyMasterID = 2, DATE_FORMAT(endDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(endDate, '%Y-%m-%d')) AS endDate, days, hours, ishalfDay, documentCode, srp_erp_leavemaster.serialNo, entryDate, comments, isCalenderDays, nonWorkingDays, workingDays, srp_erp_leavemaster.leaveGroupID, isAttendance, policyMasterID, confirmedYN, confirmedByEmpID, confirmedByName, confirmedDate, approvedYN, approvedDate, approvedbyEmpID, approvedbyEmpName, srp_erp_leavemaster.companyID, srp_erp_leavemaster.companyCode, description,Ename1 AS recEmp,srp_erp_leavemaster.shift,concat(t2.ECode,' | ',t2.Ename2) as covering_emp
                                  FROM srp_erp_leavemaster 
                                  LEFT JOIN srp_erp_leavetype ON srp_erp_leavetype.leaveTypeID=srp_erp_leavemaster.leaveTypeID
                                  LEFT JOIN srp_employeesdetails t2 ON  t2.EIdNo = srp_erp_leavemaster.coveringEmpID
                                  WHERE srp_erp_leavemaster.leaveMasterID={$masterID}")->row_array();
       

    }

    function getemployeedetails($empID, $companyID)
    {

        $qry = "SELECT srp_employeesdetails.EIdNo, srp_employeesdetails.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
                IFNULL(srp_employeesdetails.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
                DepartmentDes as department, concat(manager.ECode,' | ',manager.Ename2) as manager
                FROM srp_employeesdetails
                INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
                INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
                LEFT JOIN srp_erp_segment  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                LEFT JOIN  (
                     SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                     JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                     WHERE EmpID=$empID AND departTB.Erp_companyID=$companyID AND empDep.Erp_companyID=$companyID AND empDep.isActive=1
                ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
                LEFT JOIN `srp_erp_employeemanagers` on EIdNo=empID AND active=1
                LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
                WHERE srp_employeesdetails.Erp_companyID=$companyID  AND srp_employeesdetails.EIdNo =$empID ";

        $data = $this->db->query($qry)->row_array();
        $employee_details = array();
        $employee_details['EIdNo'] = (float)$data['EIdNo'];
        $employee_details['ECode'] = $data['ECode'];
        $employee_details['EmpSecondaryCode'] = (float)$data['EmpSecondaryCode'];
        $employee_details['DesDescription'] = $data['DesDescription'];
        $employee_details['employee'] = $data['employee'];
        $employee_details['leaveGroupID'] = (float)$data['leaveGroupID'];
        $employee_details['DateAssumed'] = $data['DateAssumed'];
        $employee_details['department'] = $data['department'];
        $employee_details['manager'] = $data['manager'];
        return $employee_details;
    }

    public function employee_leave_details($masterID, $companyID)
    {

        /*   $leave= $this->db->query("select policyMasterID from `srp_erp_leavemaster` WHERE leaveMasterID={$masterID}")->row_array();
           $policyMasterID= $leave['policyMasterID'];*/
        $leaveDet = $this->employeeLeave_details_with_more_data($masterID);
        if ($leaveDet['empID'] == null) {
            return null;
        }
        $empDet = $this->getemployeedetails($leaveDet['empID'], $companyID);
        //var_dump($empDet);exit;
        $entitleDet = $this->employeeLeaveSummery($leaveDet['empID'], $leaveDet['leaveTypeID'], $leaveDet['policyMasterID']);

        $leaveType = $this->getLeaveTypeDetails($leaveDet['leaveTypeID']);

        $attachments = $this->get_attachments($masterID, $companyID);

        $approval_details = $this->fetch_all_approval_users_modal($companyID, "LA", $masterID);
//var_dump($approval_details);exit;
        return array(
            'leaveDet' => $leaveDet,
            'empDet' => $empDet,
            'entitleDet' => $entitleDet,
            'attachments' => $attachments,
            'approval_details' => $approval_details,
            'leaveType' => $leaveType
        );

    }

    function getLeaveTypeDetails($leaveTypeID)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_leavetype` WHERE leaveTypeID=$leaveTypeID");
        $row = $query->row_array();
        $leaveType['leaveTypeID'] = (int)$row['leaveTypeID'];
        $leaveType['description'] = $row['description'];
        $leaveType['isPaidLeave'] = (int)$row['isPaidLeave'];
        $leaveType['isAnnualLeave'] = (int)$row['isAnnualLeave'];
        $leaveType['isSickLeave'] = (int)$row['isSickLeave'];
        $leaveType['isShortLeave'] = (int)$row['isShortLeave'];
        $leaveType['attachmentRequired'] = (int)$row['attachmentRequired'];
        return $leaveType;
    }

    function get_attachments($masterID, $companyID)
    {
        $this->db->where('documentSystemCode', $masterID);
        $this->db->where('documentID', 'LA');
        $this->db->where('companyID', $companyID);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        //var_dump($this->db->last_query());exit;
        $result = '';
        $x = 1;
        $attachment_links = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $attachment = array();

                $attachment['id'] = (int)$val['attachmentID'];
                $attachment['link'] = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');
                $attachment['type'] = $val['fileType'];
                $attachment['fileName'] = $val['myFileName'];
                $attachment['description'] = $val['attachmentDescription'];
//                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
//                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
//                } else {
//                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
//                }
                array_push($attachment_links, $attachment);
                $x++;
            }
        } else {
            //$result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        return $attachment_links;
    }

    function employeeData($empID) //for leave approval popup
    {

        $this->db->select('EIdNo, ECode, IFNULL(Ename1,"") Ename1, IFNULL(Ename2,"") Ename2, IFNULL(Ename3,"") Ename3, IFNULL(Ename4,"") Ename4, DesDescription, EmpImage');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID', 'left');
        $this->db->join('srp_erp_currencymaster AS cur', 'cur.currencyID = srp_employeesdetails.payCurrencyID', 'left');
        $this->db->where("EIdNo", $empID);
        $query = $this->db->get();
        return $query->row();
    }

    function get_leaveApprovals($eId, $compId)
    {
        $setupData = getLeaveApprovalSetup('N', $compId);

        if ($setupData["approvalSetup"] != null) {

            $approvalLevel = $setupData['approvalLevel'];
            $approvalSetup = $setupData['approvalSetup'];
            $approvalEmp_arr = $setupData['approvalEmp'];

            $x = 0;
            $str = 'CASE';
            while ($x < $approvalLevel) {
                $level = $x + 1;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {

                    $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                    $hrManagerID = array_column($hrManagerID, 'empID');

                    if (!empty($hrManagerID)) {
                        $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ';
                        foreach ($hrManagerID as $key => $hrManagerRow) {
                            $str .= ($key > 0) ? ' OR' : '';
                            $str .= ' ( \'' . $eId . '\' = ' . $hrManagerRow . ')';
                        }
                        $str .= ' , 1, 0 ) ';
                    }

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ' . $managerType . ' = ' . $eId . ', 1, 0 ) ';
                }
                $x++;
            }
            $str .= 'END AS isInApproval';


            $this->db->select("leaveMasterID, documentCode, ECode, empName as Ename1, empID, currentLevelNo, repManager, leaveTypeID,coveringEmp,startDate,endDate,ishalfDay,comments,
            confirmedYN,approvedYN,cancelledYN,requestForCancelYN");
            $this->db->from("( SELECT *, {$str} FROM (
                                            SELECT leaveMasterID, documentCode, ECode, Ename2 AS empName, lMaster.empID, currentLevelNo,
                                            repManager, leaveTypeID, coveringEmpID AS coveringEmp,lMaster.startDate,lMaster.endDate,lMaster.ishalfDay,
                                            lMaster.comments,lMaster.confirmedYN,lMaster.approvedYN,lMaster.cancelledYN,lMaster.requestForCancelYN
                                            FROM srp_erp_leavemaster AS lMaster
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
                                            LEFT JOIN (
                                                SELECT empID, managerID AS repManager
                                                FROM srp_erp_employeemanagers WHERE active = 1 AND companyID={$compId}
                                            ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID
                                            WHERE lMaster.companyID = '{$compId}' AND lMaster.confirmedYN = 1 AND
                                            lMaster.approvedYN = '0'
                                        ) AS leaveData
                                        LEFT JOIN (
                                            SELECT managerID AS topManager, empID AS topEmpID
                                            FROM srp_erp_employeemanagers WHERE companyID={$compId} AND active = 1
                                        ) AS topManagerTB ON leaveData.repManager = topManagerTB.topEmpID
                                       ) AS t1");
            $this->db->where('t1.isInApproval', 1);

            $res = $this->db->get()->result_array();
            return $res;

        } else {
            return [];
        }
    }

    function getLeavedetails($userID, $companyID)
    {
        $where = "srp_erp_leavemaster.companyID = {$companyID} AND empID={$userID} ";
        $this->db->select('leaveMasterID, documentCode, ECode,Ename2 AS empName,empID, confirmedYN, approvedYN, description,startDate,endDate', true)
            ->from('srp_erp_leavemaster')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID')
            ->join('srp_erp_leavetype', 'srp_erp_leavetype.leaveTypeID = srp_erp_leavemaster.leaveTypeID')
            ->where($where)
            ->order_by("leaveMasterID", "desc");

        return $this->db->get()->result_array();

    }

    /**
     * get leave request create
     * @param userID 
     * @param date_range_start 
     * @param date_range_end 
     * @param status 
     * @controller Api_spur_ilooops/get_employee_leave_list_get
     * @created at 2023-01-11
     * @updated at 2024-01-11
     */

    function get_employee_leave_list($userID, $companyID, $date_range_start, $date_range_end, $status)
    {

        if($date_range_start && $date_range_end){
            $where = "srp_erp_leavemaster.companyID = {$companyID} AND empID={$userID} AND startDate>=CAST('$date_range_start' AS DATE) AND startDate<=CAST('$date_range_end' AS DATE)";
        }else{
            $where = "srp_erp_leavemaster.companyID = {$companyID} AND empID={$userID}";
        }

        if ($status != 'all') {
            switch ($status) {
                case 'draft':
                    $where .= " AND confirmedYN = 0 ";
                    break;

                case 'confirmed':
                    $where .= " AND confirmedYN = 1 AND approvedYN = 0 ";
                    break;

                case 'approved':
                    $where .= " AND approvedYN = 1 AND ( requestForCancelYN = 0 OR requestForCancelYN IS NULL )";
                    break;

                case 'cancellation_request':
                    $where .= " AND requestForCancelYN = 1 AND cancelledYN = 0";
                    break;

                case 'cancelled':
                    $where .= " AND cancelledYN = 1 ";
                    break;
            }
        }


        $this->db->select('ishalfDay,leaveMasterID, shift, documentCode, ECode,Ename2 AS empName,empID, confirmedYN, approvedYN, description,startDate,endDate,days,srp_erp_leavetype.isShortLeave,
        cancelledYN,srp_erp_leavemaster.leaveTypeID,srp_erp_leavemaster.currentLevelNo,srp_erp_leavemaster.coveringEmpID,srp_erp_leavemaster.comments,
        requestForCancelYN', true)
            ->from('srp_erp_leavemaster')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID')
            ->join('srp_erp_leavetype', 'srp_erp_leavetype.leaveTypeID = srp_erp_leavemaster.leaveTypeID')
            ->where($where)
            ->order_by("leaveMasterID", "desc");

        $leave_list = array();
        foreach ($this->db->get()->result_array() as $row) {

            $leave = array();
            $leave['leaveMasterID'] = (int)$row['leaveMasterID'];
            $leave['documentCode'] = $row['documentCode'];
            $leave['ECode'] = $row['ECode'];
            $leave['empName'] = $row['empName'];
            $leave['empID'] = (int)$row['empID'];
            $leave['confirmedYN'] = (int)$row['confirmedYN'];
            $leave['approvedYN'] = (int)$row['approvedYN'];
            $leave['description'] = $row['description'];
            $leave['startDate'] = $row['startDate'];
            $leave['endDate'] = $row['endDate'];
            $leave['days'] = (float)$row['days'];
            $leave['documentType'] = 'LA';
            $leave['leaveTypeID'] = (int)$row['leaveTypeID'];
            $leave['currentLevelNo'] = (int)$row['currentLevelNo'];
            $leave['coveringEmpID'] = (int)$row['coveringEmpID'];
            $leave['comments'] = $row['comments'];
            $leave['isShortLeave'] = (int)$row['isShortLeave'];
            $leave['shift'] = (int)$row['shift'];
            $leave['cancelledYN'] = (int)$row['cancelledYN'];
            $leave['requestForCancelYN'] = (int)$row['requestForCancelYN'];
            $leave['ishalfDay'] = (int)$row['ishalfDay'];
            $leave['leave_status'] = $this->get_leave_status_from_record($row);
            array_push($leave_list, $leave);
        }
        return $leave_list;
    }


     /**
     * get leave status according to record status
     * @param record srp_erp_leavemaster record
     * @created at 2023-01-11
     * @updated at 2024-01-11
     */
    private function get_leave_status_from_record($row){

        if($row['cancelledYN'] == 1){
            return array('id' => 1,'status' => 'leave_canceled');
        }elseif($row['cancelledYN'] == 0 && $row['requestForCancelYN'] == 1){
            return array('id' => 2,'status' => 'leave_canceled_requested');
        }elseif($row['confirmedYN'] == 1 && $row['approvedYN'] != 1){
            return array('id' => 4,'status' => 'confirmed');
        }elseif($row['confirmedYN'] != 1){
            return array('id' => 5,'status' => 'not_confirmed');
        }elseif($row['confirmedYN'] == 1 && $row['approvedYN'] == 1){
            return array('id' => 6,'status' => 'approved');
        }else{
            return array('id' => 7,'status' => 'not_defined');
        }

    }


    function check_leavegroup($userID)
    {
        $this->db->select('leaveGroupID');
        $this->db->from('srp_employeesdetails');
        $this->db->where('EIdNo', $userID);
        $leavegroupID = $this->db->get()->row_array();

        if ($leavegroupID["leaveGroupID"] != null) {

            $q = "SELECT count(*) as `count`,policyMasterID as isMonthly  from `srp_erp_leavegroupdetails` WHERE leaveGroupID ='" . $leavegroupID["leaveGroupID"] . "'";
            $results = $this->db->query($q)->row_array();
            return $results;
        } else {
            return array('count' => '0');
        }
    }

    function fetch_holyWeekenddays($comid)
    {
        $qu = "SELECT  fulldate as fulldate FROM `srp_erp_calender` where holiday_flag='1' or weekend_flag='1' and companyID={$comid}";
        return $this->db->query($qu)->result_array();
    }

    function get_emp_leavetypes($empID, $companyID)
    {

        $output = $this->db->query("SELECT policyMasterID, lType.leaveTypeID, lType.description, leaveGroupDetailID, isSickLeave,
                                        groupDet.leaveGroupID, noOfDays, isAllowminus, isCalenderDays, noOfHours, noOfHourscompleted
                                        FROM srp_employeesdetails AS empTB
                                        LEFT JOIN srp_erp_leavegroupdetails AS groupDet ON empTB.leaveGroupID=groupDet.leaveGroupID
                                        LEFT JOIN  srp_erp_leavetype AS lType ON groupDet.leaveTypeID=lType.leaveTypeID
                                        WHERE EIdNo='{$empID}' AND empTB.Erp_companyID='{$companyID}'
                                        AND lType.companyID='{$companyID}' AND typeConfirmed=1 ORDER BY sortOrder")->result_array();

        $leaveTypes = array();

        if (!empty($output)) {

            $isBasedOnSortOrder = $this->getPolicyValues('SL', 'All', $companyID);
//            $isBasedOnSortOrder = getPolicyValues('SL', 'All', $companyID);
            $isSickLeavePulled = 0;
            foreach ($output as $value) {
                $policyMasterID = $value['policyMasterID'];
                $leaveTypeID = $value['leaveTypeID'];
                $isSickLeave = $value['isSickLeave'];
                $leaveGroupID = $value['leaveGroupID'];
                $isValid = 'Y';

                /************************************************************************************************
                 * If document confirmed then no need to validate sick leave short order
                 ************************************************************************************************/
                /*** Validate is sick leave based on sort order***/
                if ($isBasedOnSortOrder == 1 && $isSickLeave == 1) {
                    if ($isSickLeave == 1 && $isSickLeavePulled == 0) {
                        $leaveData = $this->employeeLeaveSummery($empID, $leaveTypeID, $policyMasterID);
                        if ($leaveData['balance'] == 0) {
                            $isValid = 'N';
                        } else {
                            $isSickLeavePulled = 1;
                        }
                    } else {
                        $isValid = 'N';
                    }
                }

                if ($isValid == 'Y') {
                    array_push($leaveTypes, $value);
                }
            }
        }
        return $leaveTypes;
    }

    function leaveApplicationEmployee($empID, $com)
    {
        if (isset($empID)) {
            $filter = " AND srp_employeesdetails.EIdNo =$empID";
        } else {
            $filter = " AND srp_employeesdetails.isDischarged != 1";

        }

        $qry = "SELECT srp_employeesdetails.EIdNo, srp_employeesdetails.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
              IFNULL(srp_employeesdetails.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
              IFNULL(DepartmentDes, '') as department, concat(manager.ECode,' | ',manager.Ename2) as manager
              FROM srp_employeesdetails
              INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
              INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
              LEFT JOIN `srp_erp_segment`  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
              LEFT JOIN `srp_erp_employeemanagers` on EIdNo=empID AND active=1
              LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
              LEFT JOIN  (
                     SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                     JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                     WHERE departTB.Erp_companyID=$com AND empDep.Erp_companyID=$com AND empDep.isActive=1 GROUP BY EmpID
              ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
              WHERE srp_employeesdetails.Erp_companyID=$com  $filter";
        return $this->db->query($qry)->result_array();
    }

    function getPolicyValues($code, $documentCode, $companyID)
    {
        $CI =& get_instance();
        $policyValues = null;
        $policyArr = $this->Session_model->fetch_company_policy($companyID);

        if (array_key_exists($code, $policyArr)) {
            if (array_key_exists($documentCode, $policyArr[$code])) {
                $policyValues = $policyArr[$code][$documentCode][0]["policyvalue"];
            }
        }
        return $policyValues;
    }

    function loadLeaveTypeDropDown_old($empID, $companyID, $confirmedYN)
    {
        $financialYear = $this->fetch_current_financial_year_details($companyID);
        if ($empID != '') {
            $output = $this->db->query("SELECT groupDet.policyMasterID,srp_erp_leavepolicymaster.policyDescription, lType.leaveTypeID, lType.description, leaveGroupDetailID, isSickLeave,
                                        groupDet.leaveGroupID, noOfDays, isAllowminus, isCalenderDays, noOfHours, noOfHourscompleted, isShortLeave,attachmentRequired
                                        FROM srp_employeesdetails AS empTB
                                        LEFT JOIN srp_erp_leavegroupdetails AS groupDet ON empTB.leaveGroupID=groupDet.leaveGroupID
                                        LEFT JOIN  srp_erp_leavetype AS lType ON groupDet.leaveTypeID=lType.leaveTypeID
                                        LEFT JOIN srp_erp_leavepolicymaster ON srp_erp_leavepolicymaster.policyMasterID = groupDet.policyMasterID
                                        WHERE EIdNo='{$empID}' AND empTB.Erp_companyID='{$companyID}'
                                        AND lType.companyID='{$companyID}' AND typeConfirmed=1 ORDER BY sortOrder")->result_array();
        }

        $leave_types = array();
        if (!empty($output)) {
            $isBasedOnSortOrder = $this->getPolicyValues('SL', 'All', $companyID);
            $isSickLeavePulled = 0;
            foreach ($output as $value) {
                $policyMasterID = (int)$value['policyMasterID'];
                $leaveTypeID = (int)$value['leaveTypeID'];
                $isSickLeave = (int)$value['isSickLeave'];
                $isShortLeave = (int)$value['isShortLeave'];
                $leaveGroupID = (int)$value['leaveGroupID'];
                $policyDescription = $value['policyDescription'];
                $attachmentRequired = $value['attachmentRequired'];
                $isValid = 'Y';

                $balanceDet = $this->db->query("SELECT
	EIdNo,
	round(
		(
			IFNULL(
				(
				SELECT
					SUM( daysEntitled ) 
				FROM
					srp_erp_leaveaccrualdetail AS detailTB
					JOIN (
					SELECT
						leaveaccrualMasterID,
						confirmedYN,
						CONCAT( `year`, '-', LPAD( `month`, 2, '00' ), '-01' ) AS accrualDate 
					FROM
						srp_erp_leaveaccrualmaster 
					WHERE
						confirmedYN = 1 
						AND companyID = '{$companyID}' 
					) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
					JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
					AND leavGroupDet.leaveTypeID = {$leaveTypeID}
				WHERE
				IF
					(
						isCarryForward = 0 
						AND ( leavGroupDet.policyMasterID = 1 OR leavGroupDet.policyMasterID = 3 ),
					IF
						( leavGroupDet.policyMasterID = 1, YEAR ( accrualDate ) = YEAR({$financialYear['beginingDate']}), accrualDate BETWEEN '{$financialYear['dateFrom']}' AND '{$financialYear['dateTo']}' ),
						accrualDate <= '{$financialYear['endingDate']}' 
					) 
					AND detailTB.leaveType = {$leaveTypeID}
					AND leavGroupDet.policyMasterID IN ( 1, 3 ) 
					AND ( detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL ) 
					AND detailTB.empID = {$empID} 
				),
				0 
				) - IFNULL(
				(
				SELECT
					SUM( days ) 
				FROM
					srp_erp_leavemaster
					JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
					AND leavGroupDet.leaveTypeID = {$leaveTypeID}
				WHERE
					srp_erp_leavemaster.leaveTypeID = {$leaveTypeID}
					AND ( cancelledYN = 0 OR cancelledYN IS NULL ) 
					AND leavGroupDet.policyMasterID IN ( 1, 3 ) 
					AND srp_erp_leavemaster.empID = {$empID} 
					AND approvedYN = 1 
				AND
				IF
					(
						isCarryForward = 0 
						AND ( leavGroupDet.policyMasterID = 1 OR leavGroupDet.policyMasterID = 3 ),
					IF
						( leavGroupDet.policyMasterID = 1, endDate BETWEEN '{$financialYear['beginingDate']}' AND '{$financialYear['endingDate']}', endDate BETWEEN '{$financialYear['dateFrom']}' AND '{$financialYear['dateTo']}' ),
						endDate <= '{$financialYear['endingDate']}' 
					) 
				),
				0 
			) 
		),
		2 
	) AS `leavebalance`
FROM
	srp_employeesdetails 
WHERE
	EIdNo = '{$empID}' 
	AND Erp_companyID = '{$companyID}'")->row_array();

                $leave_balance = $balanceDet['leavebalance'];

                /************************************************************************************************
                 * If document confirmed then no need to validate sick leave short order
                 ************************************************************************************************/
                if ($confirmedYN != 1) {
                    /*** Validate is sick leave based on sort order***/
                    if ($isBasedOnSortOrder == 1 && $isSickLeave == 1) {
                        if ($isSickLeave == 1 && $isSickLeavePulled == 0) {
                            $leaveData = $this->Employee_model->employeeLeaveSummery($empID, $leaveTypeID, $policyMasterID);
                            if ($leaveData['balance'] == 0) {
                                $isValid = 'N';
                            } else {
                                $isSickLeavePulled = 1;
                            }
                        } else {
                            $isValid = 'N';
                        }
                    }
                }

                if ($isValid == 'Y') {

                    if ($policyMasterID != 2) {//Ignoring policy id 2 as a requirement.
                        $leave_type = array(
                            'leaveTypeID' => $leaveTypeID,
                            'description' => $value['description'],
                            'policyMasterID' => $policyMasterID,
                            'leaveGroupID' => $leaveGroupID,
                            'isAllowMinus' => (int)$value['isAllowminus'],
                            'isCalenderDays' => (int)$value['isCalenderDays'],
                            'isShortLeave' => $isShortLeave,
                            'policyDescription' => $policyDescription,
                            'attachmentRequired' => (int)$attachmentRequired,
                            'leaveBalance' => (float)$leave_balance
                        );
                        array_push($leave_types, $leave_type);
                    }

                }
            }

        }

        return $leave_types;
    }

    function loadLeaveTypeDropDown($empID, $companyID)
    {
        $leavGroup = $this->db->query("SELECT leaveGroupID FROM `srp_employeesdetails` WHERE EIdNo = {$empID}")->row_array();

        $asOfDate = date('Y-m-d');
        $currentYear = date('Y');
        $monthlyFirstDate = date('Y-m-01', strtotime($asOfDate));
        $monthlyEndDate = date('Y-m-t', strtotime($asOfDate));
        $yearFirstDate = date('Y-01-01', strtotime($asOfDate));
        $yearEndDate = date('Y-12-31', strtotime($asOfDate));

        if ($leavGroup['leaveGroupID'] == '') {
            $leaveType = array();
        } else {
            $leaveType = $this->db->query("SELECT srp_erp_leavegroupdetails.leaveTypeID,srp_erp_leavegroupdetails.policyMasterID, srp_erp_leavetype.description, isAllowMinus, isCalenderDays, isShortLeave, attachmentRequired, policyDescription 
                                                    FROM `srp_erp_leavegroupdetails` 
                                                    LEFT JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID
                                                    LEFT JOIN srp_erp_leavepolicymaster ON srp_erp_leavepolicymaster.policyMasterID = srp_erp_leavegroupdetails.policyMasterID 
                                                    WHERE leaveGroupID = {$leavGroup['leaveGroupID']}  ORDER BY srp_erp_leavetype.description asc")->result_array();
        }

        $carryForwardLogic = "IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3), 
                                  IF( leavGroupDet.policyMasterID=1,  YEAR(accrualDate) = {$currentYear},
                                  accrualDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), accrualDate <= '{$yearEndDate}') ";

        $carryForwardLogic2 = "AND IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3),
                                   IF( leavGroupDet.policyMasterID=1,  endDate BETWEEN '{$yearFirstDate}' AND '{$yearEndDate}',
                                   endDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), endDate <= '{$yearEndDate}') ";

        $select = 'EIdNo';
        $data_array = array();
        $return_arr = array();
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

                $data_arr = array(
                    'leaveTypeID' => (int)$val['leaveTypeID'],
                    'description' => $val['description'],
                    'policyMasterID' => (int)$val['policyMasterID'],
                    'leaveGroupID' => (int)$leavGroup['leaveGroupID'],
                    'isAllowMinus' => (int)$val['isAllowMinus'],
                    'isCalenderDays' => (int)$val['isCalenderDays'],
                    'isShortLeave' => (int)$val['isShortLeave'],
                    'policyDescription' => $val['policyDescription'],
                    'attachmentRequired' => (int)$val['attachmentRequired']
                );
                array_push($data_array, $data_arr);

            }

            $detail = $this->db->query("SELECT $select FROM srp_employeesdetails WHERE EIdNo = {$empID} AND Erp_companyID='{$companyID}'")->row_array();
           foreach ($data_array as $val){
               $ind = $val['leaveTypeID'] . 'balance';
               $val['leaveBalance'] = (float)$detail[$ind];
               array_push($return_arr, $val);
           }
        }
//        echo '<pre>'; print_r($return_arr);
        return $return_arr;
    }

    function covering_employees($empID, $companyID, $coveringEmp = 0, $confirmedYN = 0)
    {
        $covering_employees = array();
        if ($confirmedYN == 1) {
            $empData = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails
                       WHERE Erp_companyID={$companyID} AND EIdNo={$coveringEmp}")->row_array();
            // $html .= '<option value="' . $empData['EIdNo'] . '" selected>' . $empData['ECode'] . ' - ' . $empData['Ename2'] . '</option>';
        }

        if (!empty($empID) && $confirmedYN != 1) {

            $empList = $this->db->query("SELECT * FROM (
                                        SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails AS empTB
                                        JOIN srp_erp_employeemanagers AS mangerTB ON mangerTB.empID=empTB.EIdNo
                                        WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged=0
                                        AND isSystemAdmin=0 AND mangerTB.active=1 AND companyID={$companyID}
                                        AND EIdNo != {$empID}
                                        AND managerID = (
                                            SELECT managerID FROM srp_erp_employeemanagers WHERE empID={$empID} AND active=1
                                        )
                                        UNION
                                        SELECT EIdNo, ECode, Ename2 
                                        FROM srp_erp_employeemanagers manTB
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = manTB.managerID
                                        WHERE empID={$empID} AND active=1
                                        UNION 
                                        SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails AS empTB
                                        WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged=0
                                        AND isSystemAdmin=0 
                                        AND EIdNo IN (
                                            SELECT empID FROM srp_erp_employeemanagers WHERE managerID={$empID} AND active=1
                                        )
                                     ) AS t1 ORDER BY Ename2")->result_array();

            if (!empty($empList)) {
                foreach ($empList as $val) {
                    $selected = ($coveringEmp == $val['EIdNo']) ? 'selected' : '';
                    $covering_employee = array(
                        "EIdNo" => (int)$val['EIdNo'],
                        "ECode" => $val['ECode'],
                        "Ename2" => $val['Ename2']
                    );
                    array_push($covering_employees, $covering_employee);
                }
            }
        }
        return $covering_employees;
    }

    function leaveEmployeeCalculation($policyMasterID, $companyID, $leaveTypeID, $halfDay, $shortLV, $startDate, $endDate, $isAllowminus, $isCalenderDays, $entitleSpan)
    {

        if ($policyMasterID != 2) {

           
            $date1 = new DateTime("$startDate");
         
            $date2 = new DateTime("$endDate");
            $diff = $date2->diff($date1)->format("%a");
          
            $dateDiff = $diff + 1;
            $dateDiff2 = $diff + 1;
           
            $calenderDays['workingDays'] = 0;
            $datetime1 = date('Y-m-d', strtotime($startDate));
            $datetime2 = date('Y-m-d', strtotime($endDate));
            if ($datetime1 > $datetime2) {
                
                return array('error' => 1, 'message' => 'Please check start and end date.');
                exit;
            }
          
            if ($isCalenderDays != 1) {
               
                $sd = explode('-', $startDate);
                $sYear = $sd[0];
                $sMonth = $sd[1];

                $ed = explode('-', $endDate);
                $eYear = $ed[0];
                $eMonth = $ed[1];
              

                $calendervalidate = $this->db->query("SELECT sum(IF(monthnumber = {$sMonth} && year={$sYear}, 1, 0)) as startDate ,  sum(IF(monthnumber = {$eMonth} && year={$eYear}, 1, 0)) as endDate FROM `srp_erp_calender` WHERE monthnumber AND year AND companyID={$companyID}")->row_array();

              

                if ($calendervalidate['startDate'] == 0 || $calendervalidate['endDate'] == 0) {
                    return array('error' => 1, 'message' => 'Calender not configured for selected date.');
                    exit;
                }

                $calenderDays = $this->db->query("SELECT SUM(IF(fulldate != '', 1, 0)) AS nonworkingDays, SUM(IF(fulldate != '', 1, 0)) - SUM(IF(weekend_flag = 1 || holiday_flag = 1, 1, 0)) AS workingDays FROM `srp_erp_calender` WHERE fulldate BETWEEN '{$startDate}' AND '{$endDate}' AND companyID = {$companyID}")->row_array();
                if ($calenderDays['workingDays'] != null) {
                    /*    $calenderDays['workingDays']=  ($calenderDays['workingDays'] == null ? 0:$calenderDays['workingDays']);*/
                    /* if ($calenderDays['workingDays'] == null) {
                         echo json_encode(array('error' => 1, 'message' => 'Calender is not set for this company'));
                         exit;
                     }
                     }*/
                    $dateDiff = $calenderDays['workingDays'];
                }
            } else {
                $calenderDays['workingDays'] = $dateDiff2;
               
                //print_R($calenderDays['workingDays']);
                //exit();
            }
            if ($halfDay == 1) {//|| $shortLV==1
                $dateDiff = $dateDiff2 = $calenderDays['workingDays'] = 0.5; /*half day*/
            }
            $leaveBlance = $entitleSpan - $dateDiff;
            if ($isAllowminus != 1) {
                if ($leaveBlance < 0) {
                    return array('error' => 3, 'message' => 'The maximum leave accumulation is  ' . "$entitleSpan" . ' days');
                    exit;
                }
            }
            $avaialable_leave_count = $leaveBlance + (float)$calenderDays['workingDays'];
            return array('error' => 0, 'appliedLeave' => $dateDiff2, 'leaveBlance' => $leaveBlance, 'calenderYN' => (float)$isCalenderDays, 'workingDays' => (float)$calenderDays['workingDays'], 'available' => $avaialable_leave_count);
            exit;
        } else {
            $datetime1 = date('Y-m-d H:i:s', strtotime($startDate));
            $datetime2 = date('Y-m-d H:i:s', strtotime($endDate));
            if ($datetime1 < $datetime2) {
                $dteStart = new DateTime($startDate);
                $dteEnd = new DateTime($endDate);

                $dteDiff = $dteStart->diff($dteEnd);
                $hour = $dteDiff->format("%H");
                $minutes = $dteDiff->format('%I');
                $day = $dteDiff->format('%d');
                $totalMinutes = ($day * 1440) + ($hour * 60) + $minutes;
            } else {
                return array('error' => 1, 'message' => 'Please check the start date and end date');
                exit;
            }
            $balance = $entitleSpan - $totalMinutes;

            if ($isAllowminus != 1) {
                if ($balance < 0) {

                    $hours = floor($entitleSpan / 60);
                    $min = $entitleSpan - ($hours * 60);

                    $entitle = $hours . "h:" . $min . "m";
                    return array('error' => 3, 'message' => 'The maximum leave accumulation is  ' . "$entitle" . ' ');
                    exit;
                }
            }

            return array('error' => 0, 'appliedLeave' => $totalMinutes, 'leaveBlance' => $balance, 'workingDays' => 0, 'available' => 0);

        }


    }

    function employeeLeaveSummery($empID, $leaveType, $policyMasterID)
    {

        if ($policyMasterID == 2) {
            $qry3 = "SELECT
                        t4.attachmentRequired,
                        t3.policyMasterID,
                        t5.description,
                        '' AS entitled,
                        IFNULL(
                            (
                                SELECT
                                    SUM(hoursEntitled)
                                FROM
                                    srp_erp_leaveaccrualdetail
                                LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID
                                WHERE
                                    empID =  $empID 
                                AND leaveType =  $leaveType 
                                AND confirmedYN = 1
                            ),
                            0
                        ) AS leaveAvailable,
                        IFNULL(
                            (
                                SELECT
                                    SUM(hours)
                                FROM
                                    srp_erp_leavemaster
                                WHERE
                                    empID =  $empID 
                                AND leaveTypeID =  $leaveType 
                                AND approvedYN = 1
                            ),
                            0
                        ) AS leaveTaken,
                        IFNULL(
                            (
                                SELECT
                                    SUM(hoursEntitled)
                                FROM
                                    srp_erp_leaveaccrualdetail
                                LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID
                                WHERE
                                    empID =  $empID 
                                AND leaveType =  $leaveType 
                                AND confirmedYN = 1
                            ),
                            0
                        ) - IFNULL(
                            (
                                SELECT
                                    SUM(hours)
                                FROM
                                    srp_erp_leavemaster
                                WHERE
                                    empID =  $empID 
                                AND leaveTypeID =  $leaveType 
                                AND approvedYN = 1
                            ),
                            0
                        ) AS balance,
                        policyDescription,
                        IFNULL(
                            (
                                SELECT
                                    SUM(hoursEntitled)
                                FROM
                                    srp_erp_leaveaccrualdetail
                                LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID
                                WHERE
                                    empID =  $empID 
                                AND leaveType =  $leaveType 
                                AND confirmedYN = 1
                            ),
                            0
                        ) AS accrued,
                        isPaidLeave
                    FROM
                        `srp_employeesdetails` t1
                    LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID
                    LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t2.leaveGroupID = t3.leaveGroupID
                    LEFT JOIN srp_erp_leavetype AS t4 ON t4.leaveTypeID = t3.leaveTypeID
                    JOIN srp_erp_leavepolicymaster t5 ON t5.policyMasterID = t3.policyMasterID
                    WHERE
                        t3.leaveTypeID =  $leaveType 
                    AND EIdNo =  $empID ";
        } else {
            $isCarryForwardStr = $isCarryForwardStr2 = '';
            if ($policyMasterID == 1) {
                $isCarryForward = $this->db->query("SELECT isCarryForward FROM srp_erp_leavegroupdetails  t1
                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                WHERE leaveTypeID={$leaveType}  AND EIdNo={$empID}")->row('isCarryForward');

                if ($isCarryForward == 0) {
                    $isCarryForwardStr = " AND `year`='" . date('Y') . "'";
                    $isCarryForwardStr2 = " AND year(startDate) = '" . date('Y') . "'";
                }
            }

            if ($policyMasterID == 3) {
                $isCarryForward = $this->db->query("SELECT isCarryForward FROM srp_erp_leavegroupdetails  t1
                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                WHERE leaveTypeID={$leaveType}  AND EIdNo={$empID}")->row('isCarryForward');

                if ($isCarryForward == 0) {
                    $isCarryForwardStr = " AND `year`='" . date('Y') . "'  AND `month`='" . date('m') . "'";
                    $isCarryForwardStr2 = " AND year(startDate) = '" . date('Y') . "' AND MONTH(startDate) = '" . date('m') . "'";
                }
            }


            $qry3 = "SELECT *, (entitled - leaveTaken) AS balance FROM ( 
                         SELECT t3.policyMasterID,
                         IFNULL( (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail 
                           LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                           WHERE empID = $empID AND leaveType = $leaveType AND confirmedYN = 1 $isCarryForwardStr), 0
                         ) AS entitled, 
                         IFNULL( (SELECT SUM(days) FROM srp_erp_leavemaster WHERE empID = $empID AND leaveTypeID = $leaveType 
                           AND approvedYN = 1 $isCarryForwardStr2), 0
                         ) AS leaveTaken, policyDescription, isPaidLeave, t5.description 
                         FROM srp_employeesdetails t1 
                         LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID 
                         LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t1.leaveGroupID = t3.leaveGroupID 
                         LEFT JOIN srp_erp_leavepolicymaster t4 ON t4.policyMasterID = t3.policyMasterID 
                         LEFT JOIN srp_erp_leavetype AS t5 ON t5.leaveTypeID = t3.leaveTypeID WHERE t3.leaveTypeID = $leaveType AND EIdNo = $empID 
                     ) dataTB";
        }

       //var_dump($qry3);
       // exit();
        $data = $this->db->query($qry3)->row_array();
        $entitle_details = array();
        $entitle_details['policyMasterID'] = (int)$data['policyMasterID'];
        $entitle_details['entitled'] = (int)$data['entitled'];
        $entitle_details['leaveTaken'] = (int)$data['leaveTaken'];
        $entitle_details['policyDescription'] = $data['policyDescription'];
        $entitle_details['isPaidLeave'] = (int)$data['isPaidLeave'];
        $entitle_details['description'] = $data['description'];
        $entitle_details['balance'] = (float)$data['balance'];
        return $entitle_details;
    }

    function checkIscalander($compid, $eid, $lid)
    {
        $sql = "SELECT
            isAllowminus,
            isCalenderDays
        FROM
            srp_erp_leavegroupdetails t1
        left JOIN srp_employeesdetails t2 ON t1.leaveGroupID = t2.leaveGroupID
        WHERE
            leaveTypeID = $lid 
        AND t2.Erp_companyID=$compid and t2.EIdNo=$eid;";
        return $this->db->query($sql)->row_array();
    }


    function save_employeesLeave($empID, $companyID, $companyCode, $name, $available, $enddate, $leavetypeid, $startDate, $halfday, $confirmed, $coveringEmp, $days, $policyid, $comments, $attachmentDescription, $token, $shift, $workingDays, $nonWorkingDays, $leaveAvailable)
    {

        $this->db->select('leaveGroupID');
        $this->db->from('srp_employeesdetails');
        $this->db->where('EIdNo', $empID);
        $this->db->where('Erp_companyID', $companyID);
        $EmpLeavegroupID = $this->db->get()->row_array();

        $request_body = file_get_contents('php://input');
        $request = json_decode($request_body);

        $entryDate = Date('Y-m-d');
        $endDate = $enddate;
        $leaveTypeID = $leavetypeid;
        $halfDay = $halfday;
        $isconfirmed = $confirmed;
        $isCalenderDays = '';
        $coveringEmpID = $coveringEmp;
        $coveringEmpID = (!empty($coveringEmpID)) ? $coveringEmpID : 0;

        //$workingDays = $days;
        $policyMasterID = $policyid;
        $leaveGroupID = $EmpLeavegroupID['leaveGroupID'];
        $createdPCID = $token->current_pc;
        $createdUserID = $empID;
        $createdUserName = $name;
        $createdUserGroup = '';
        $createdDateTime = Date('Y-m-d');

        if ($isconfirmed == '1') {
            $confirmedEmpID = $empID;
            $cdate = date('Y-m-d');
        } else {
            $confirmedEmpID = "";
            $cdate = "";
        }
        $hour = 0;

        //Get last leave no
        $lastCodeArray = $this->db->query("SELECT serialNo FROM srp_erp_leavemaster WHERE companyID={$companyID}
                                            ORDER BY leaveMasterID DESC LIMIT 1")->row_array();
        $lastCodeNo = $lastCodeArray['serialNo'];
        $lastCodeNo = ($lastCodeNo == null) ? 1 : $lastCodeArray['serialNo'] + 1;

        $this->load->library('sequence_mobile');
        $dCode = $this->sequence_mobile->sequence_generator('LA', $lastCodeNo, $companyID, $companyCode, $empID, $name);

        if ($policyMasterID == 2) {
        }

        $data = array(

            'empID' => $empID,
            'leaveTypeID' => $leaveTypeID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'days' => $days,
            'ishalfDay' => $halfDay,
            'shift' => $shift,
            'isCalenderDays' => $isCalenderDays,
            'workingDays' => $workingDays,
            'nonWorkingDays' => $nonWorkingDays,
            'leaveGroupID' => $leaveGroupID,
            'policyMasterID' => $policyMasterID,
            //'leaveAvailable' => $available - $days,
            'leaveAvailable' => $leaveAvailable,
            'documentCode' => $dCode,
            'serialNo' => $lastCodeNo,
            'hours' => $hour,
            'entryDate' => date('Y-m-d'),
            'coveringEmpID' => $coveringEmpID,
            'comments' => $comments,
            'companyID' => $companyID,
            'companyCode' => $companyCode,
            'createdPCID' => $createdPCID,
            'createdUserID' => $createdUserID,
            'createdUserGroup' => $createdUserGroup,
            'createdDateTime' => $createdDateTime,
            'confirmedYN' => $isconfirmed,
            'confirmedByEmpID' => $confirmedEmpID,
            'confirmedByName' => $name,
            'confirmedDate' => $cdate
        );
        $this->db->trans_start();
        $this->db->insert('srp_erp_leavemaster', $data);
        //var_dump($this->db->last_query());exit;
        $leaveMasterID = $this->db->insert_id();
        //echo  $leaveMasterID;
        //exit();

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            // return array('e', 'Failed Insert Data');
        } else {
            $this->db->trans_commit();

            //image upload
            if (isset($_FILES['attachmentDescription'])) {
                  
                //echo 123;
                //exit();

                $files           = $_FILES;
                $files_count = sizeof($_FILES['attachmentDescription']['name']);
                //$file = $_FILES['attachmentDescription'];
              
               //print_R($file['name']);
                //exit();
                for ($i = 0; $files_count > $i; $i++) {
                   
                    $_FILES['file']['name'] = $files['attachmentDescription']['name'][$i];
                    $_FILES['file']['error'] = $files['attachmentDescription']['error'][$i];
 
                    //$attachmentDesc = trim($attachmentDescription[$i]);
                    $attachmentDesc=$_FILES['file']['name'];
                  
                
           

                    if ($attachmentDesc == '') {
                        return ['e', 'Please enter attachment description'];
                    }

                    $num = $this->db->select('companyID')->where('documentID', 'LA')->get('srp_erp_documentattachments')->result_array();
                    $file_name = 'LA' . '_' . (count($num) + 1);

                    if ($_FILES['file']['error'] == 1) {
                        die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
                    }

                    
                    $ext = pathinfo($attachmentDesc,PATHINFO_EXTENSION);
                  
                    $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
                    $allowed_types = explode('|', $allowed_types);
                    if (!in_array($ext, $allowed_types)) {
                        die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
                    }

                    $size =  $files['attachmentDescription']['size'][$i];
                    $size = number_format($size / 1048576, 2);

                    if ($size > 5) {
                        die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
                    }

                    $file_name = "$file_name.$ext";
                    $s3Upload = $this->s3->upload($files['attachmentDescription']['tmp_name'][$i], $file_name);

                    if (!$s3Upload) {
                        die(json_encode(['e', 'Error in document upload location configuration']));
                    }

                    $detail['documentID'] = 'LA';
                    $detail['documentSystemCode'] = $leaveMasterID;
                    $detail['attachmentDescription'] = $attachmentDesc;
                    $detail['myFileName'] = $file_name;
                    $detail['fileType'] = $ext;
                    $detail['fileSize'] = $size;
                    $detail['timestamp'] = date('Y-m-d H:i:s');
                    $detail['companyID'] = $token->Erp_companyID;
                    $detail['companyCode'] = $token->company_code;
                    $detail['modifiedPCID'] = $token->current_pc;
                    $detail['modifiedUserID'] = $token->id;
                    $detail['modifiedUserName'] = $token->username;
                    $detail['modifiedDateTime'] = date('Y-m-d H:i:s');
                    $detail['createdPCID'] = $token->current_pc;
                    $detail['createdUserID'] = $token->id;
                    $detail['createdUserName'] = $token->username;
                    $detail['createdUserGroup'] = $token->usergroupID;
                    $detail['createdDateTime'] = date('Y-m-d H:i:s');
                    $this->db->insert('srp_erp_documentattachments', $detail);
                    //var_dump($this->db->last_query());

                    //exit();

                }

            }

            if ($isconfirmed == 1) {
                return $this->leave_ApprovalCreate($leaveMasterID, '0', $companyID, $companyCode, $empID, $name);
//                $this->db->select('*');
//                $this->db->from('srp_erp_employeemanagers');
//                $this->db->join('srp_employeesdetails', 'managerID=EIdNo', 'left');
//                $this->db->where('empID', $empID);
//                $this->db->where('active', 1);
//                $result = $this->db->get()->result_array();
//                foreach ($result as $val) {
//                    send_push_notification($val['managerID'], 'Leave Approval', $companyCode, 1, $name);
//                }

//                return array('1');
            } else {
                return array('status' => 1, 'email' => null);
            }
        }

    }

    function getLeaveApprovalSetup($companyID, $isSetting = 'N')
    {

        $appSystemValues = $this->db->query("SELECT * FROM srp_erp_leavesetupsystemapprovaltypes")->result_array();

        if ($isSetting == 'Y') {
            $arr = [0 => ''];
            foreach ($appSystemValues as $key => $val) {
                $arr[$val['id']] = $val['description'];
            }
            $appSystemValues = $arr;
        }

        $approvalLevel = $this->db->query("SELECT approvalLevel FROM srp_erp_documentcodemaster WHERE documentID = 'LA' AND
                                         companyID={$companyID} ")->row('approvalLevel');

        $approvalSetup = $this->db->query("SELECT approvalLevel, approvalType, empID, systemTB.*
                                         FROM srp_erp_leaveapprovalsetup AS setupTB
                                         JOIN srp_erp_leavesetupsystemapprovaltypes AS systemTB ON systemTB.id = setupTB.approvalType
                                         WHERE companyID={$companyID} ORDER BY approvalLevel")->result_array();

        $approvalEmp = $this->db->query("SELECT approvalLevel, empTB.empID
                                       FROM srp_erp_leaveapprovalsetup AS setupTB
                                       JOIN srp_erp_leaveapprovalsetuphremployees AS empTB ON empTB.approvalSetupID = setupTB.approvalSetupID
                                       WHERE setupTB.companyID={$companyID} AND empTB.companyID={$companyID}")->result_array();

        if (!empty($approvalEmp)) {
            $approvalEmp = array_group_by($approvalEmp, 'approvalLevel');
        }

        return [
            'appSystemValues' => $appSystemValues,
            'approvalLevel' => $approvalLevel,
            'approvalSetup' => $approvalSetup,
            'approvalEmp' => $approvalEmp
        ];
    }

    function leave_ApprovalCreate($leaveMasterID, $level, $companyID, $comcode, $userID, $name)
    {
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode,coveringEmpID FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];
        $setupData = $this->getLeaveApprovalSetup($companyID);
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $isManagerAvailableForNxtApproval = 0;
        $nextLevel = null;
        $nextApprovalEmpID = null;
        $data_app = [];


        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($level <= $approvalLevel) {

            $managers = $this->db->query("SELECT * ,{$coveringEmpID} AS coveringEmp  FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $level;

            /**** Validate is there a manager available for next approval level ****/
            $i = 0;

            while ($x <= $approvalLevel) {

                $isCurrentLevelApproval_exist = 0;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if ($approvalType == 3) {
                    $isCurrentLevelApproval_exist = 1;

                    if ($isManagerAvailableForNxtApproval == 0) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $isManagerAvailableForNxtApproval = 1;
                    }
                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $isCurrentLevelApproval_exist = 1;

                        if ($isManagerAvailableForNxtApproval == 0) {
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                        }
                    }

                }

                if ($isCurrentLevelApproval_exist == 1) {
                    $data_app[$i]['companyID'] = $companyID;
                    $data_app[$i]['companyCode'] = $comcode;
                    $data_app[$i]['departmentID'] = 'LA';
                    $data_app[$i]['documentID'] = 'LA';
                    $data_app[$i]['documentSystemCode'] = $leaveMasterID;
                    $data_app[$i]['documentCode'] = $leave['documentCode'];
                    $data_app[$i]['table_name'] = 'srp_erp_leavemaster';
                    $data_app[$i]['table_unique_field_name'] = 'leaveMasterID';
                    $data_app[$i]['documentDate'] = date('Y-m-d');
                    $data_app[$i]['approvalLevelID'] = $x;
                    $data_app[$i]['roleID'] = null;
                    $data_app[$i]['approvalGroupID'] = null;
                    $data_app[$i]['roleLevelOrder'] = null;
                    $data_app[$i]['docConfirmedDate'] = date('Y-m-d H:i:s');
                    $data_app[$i]['docConfirmedByEmpID'] = $userID;
                    $data_app[$i]['approvedEmpID'] = null;
                    $data_app[$i]['approvedYN'] = 0;
                    $data_app[$i]['approvedDate'] = null;
                    $i++;
                }

                $x++;
            }

        }

        if (!empty($data_app)) {

            $this->db->insert_batch('srp_erp_documentapproved', $data_app);

            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => '',
                'modifiedUserID' => $userID,
                'modifiedUserName' => $name,
                'modifiedDateTime' => date('Y-m-d H:i:s')
            ];
            $this->db->where('leaveMasterID', $leaveMasterID);
            $update = $this->db->update('srp_erp_leavemaster', $upData);

            if ($update) {
                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                /*** Firebase Mobile Notification*/
                $token_android = firebaseToken($nextApprovalEmpID, 'android', $companyID);
                $token_ios = firebaseToken($nextApprovalEmpID, 'apple', $companyID);

                if($leave['startDate'] == $leave['endDate']) {
                    $firebaseBody = $leave['Ename2'] . " has applied for a leave on " . date('d M Y', strtotime($leave['startDate']));
                } else {
                    $firebaseBody = $leave['Ename2'] . " has applied for a leave from " . date('d M Y', strtotime($leave['startDate'])) . " to " . date('d M Y', strtotime($leave['endDate']));
                }

                $this->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->firebase_notification->sendFirebasePushNotification("New Leave Approval", $firebaseBody, $token_android, 1, $leave['documentCode'], "LA", $leaveMasterID, "android");
                }
                if(!empty($token_ios)) {
                    $this->firebase_notification->sendFirebasePushNotification("New Leave Approval", $firebaseBody, $token_ios, 1, $leave['documentCode'], "LA", $leaveMasterID, "apple");
                }

                $mail_Data = array();
                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave application ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr> ';

                    if ($coveringEmpID != $nxtEmpData['EIdNo']) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Approval',
                        'param' => $param
                    ];
                    $mail_Data[] = $mailData;
                }
                return array("status" => 's', "email" => $mail_Data);

            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array("status" => 'e', "email" => null);
            }

        } else {

            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 0,
                'approvedDate' => date('Y-m-d'),
                'approvedbyEmpID' => $userID,
                'approvedbyEmpName' => '',
                'approvalComments' => '',
            );

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->update('srp_erp_leavemaster', $data);

            /**** Confirm leave accrual pending*/
            $accrualData = [
                'confirmedYN' => 1,
                'confirmedby' => $userID,
                'confirmedDate' => date('Y-m-d')
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('confirmedYN', 0);
            $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                /*** Firebase Mobile Notification*/
                $token_android = firebaseToken($empID, 'android', $companyID);
                $token_ios = firebaseToken($empID, 'apple', $companyID);

                if($leave['startDate'] == $leave['endDate']) {
                    $firebaseBody = "Your leave on " . date('d M Y', strtotime($leave['startDate'])) . ' has been approved';
                } else {
                    $firebaseBody = "Your leave from " . date('d M Y', strtotime($leave['startDate'])) . " to " . date('d M Y', strtotime($leave['endDate'])) . ' has been approved';
                }

                $this->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->firebase_notification->sendFirebasePushNotification("Leave Approved", $firebaseBody, $token_android, 3, $leave['documentCode'], "LA", $leaveMasterID, "android");
                }
                if(!empty($token_ios)) {
                    $this->firebase_notification->sendFirebasePushNotification("Leave Approved", $firebaseBody, $token_ios, 3, $leave['documentCode'], "LA", $leaveMasterID, "apple");
                }

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Approved',
                     'param' => $param,
                ];

                $success_msg = $this->lang->line('Leave_approved_successfully');/*'Approved successfully'*/
                return array("status" => 's', "email" => $mailData);
            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
//                return array('e', $common_failed);
                return array("status" => 'e', "email" => null);
            }
        }
    }

    function saveleaveApproval($UserID, $userName, $companyID, $status, $leaveMasterID, $comment, $companyCode, $level)
    {
        if ($status == 1) {

            $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
            $empID = $leave['empID'];

            $setupData = getLeaveApprovalSetup($companyID);
            $approvalLevel = $setupData['approvalLevel'];
            $approvalEmp_arr = $setupData['approvalEmp'];
            $isManagerAvailableForNxtApproval = 0;
            $nextApprovalEmpID = null;
            $nextLevel = ($level + 1);


            if ($nextLevel <= $approvalLevel) {

                $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

                $approvalSetup = $setupData['approvalSetup'];
                $x = $nextLevel;

                /**** Validate is there a manager available for next approval level ****/
                while ($x <= $approvalLevel) {
                    $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                    $arr = array_map(function ($k) use ($approvalSetup) {

                        return $approvalSetup[$k];
                    }, $keys);

                    $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                    if ($approvalType == 3) {
                        $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $nextLevel = $x;
                        $nextApprovalEmpID = $hrManagerID;
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    } else {
                        $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                        if (!empty($managers[$managerType])) {
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                            $x = $approvalLevel;
                        }
                    }
                    $x++;
                }
            }


            if ($isManagerAvailableForNxtApproval == 1) {
                $upData = [
                    'currentLevelNo' => $nextLevel,
                    'modifiedPCID' => '',
                    'modifiedUserID' => $UserID,
                    'modifiedUserName' => $userName,
                    'modifiedDateTime' => current_date()
                ];

                $this->db->trans_start();

                $this->db->where('leaveMasterID', $leaveMasterID);
                $this->db->where('companyID', $companyID);
                $this->db->update('srp_erp_leavemaster', $upData);

                $approvalData = [
                    'approvedYN' => $status,
                    'approvedEmpID' => $UserID,
                    'approvedComments' => $comment,
                    'approvedDate' => current_date(),
                    'approvedPC' => ''
                ];

                $this->db->where('companyID', $companyID);
                $this->db->where('departmentID', 'LA');
                $this->db->where('documentSystemCode', $leaveMasterID);
                $this->db->where('approvalLevelID', $level);
                $this->db->update('srp_erp_documentapproved', $approvalData);

                $this->db->trans_complete();

                if ($this->db->trans_status() == true) {
                    $this->db->trans_commit();

                    $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                    $balanceLeave = $leaveBalanceData['balance'];
                    $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                    if (is_array($nextApprovalEmpID)) {
                        /**** If the approval type HR there may be more than one employee for next approval process ****/
                        $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                    }

                    $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                    AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                    foreach ($nxtEmpData_arr as $nxtEmpData) {

                        $param["empName"] = $nxtEmpData["Ename2"];
                        $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                        $mailData = [
                            'approvalEmpID' => $nxtEmpData["EIdNo"],
                            'documentCode' => $leave['documentCode'],
                            'toEmail' => $nxtEmpData["EEmail"],
                            'subject' => 'Leave Approval',
                            'param' => $param
                        ];
                    }
                } else {
                    $this->db->trans_rollback();
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    return array('e', $common_failed);
                }

            } else {

                $data = array(
                    'currentLevelNo' => $approvalLevel,
                    'approvedYN' => 1,
                    'approvedDate' => current_date(),
                    'approvedbyEmpID' => $UserID,
                    'approvedbyEmpName' => $userName,
                    'approvalComments' => $comment,
                );

                $this->db->trans_start();

                if ($leave["isSickLeave"] == 1) {
                    $this->sickLeaveNoPay_calculation($leave, $companyID);
                }

                $this->db->where('leaveMasterID', $leaveMasterID);
                $this->db->where('companyID', $companyID);
                $this->db->update('srp_erp_leavemaster', $data);

                $approvalData = [
                    'approvedYN' => $status,
                    'approvedEmpID' => $UserID,
                    'approvedComments' => $comment,
                    'approvedDate' => current_date(),
                    'approvedPC' => ''
                ];

                $this->db->where('companyID', $companyID);
                $this->db->where('departmentID', 'LA');
                $this->db->where('documentSystemCode', $leaveMasterID);
                $this->db->where('approvalLevelID', $level);
                $this->db->update('srp_erp_documentapproved', $approvalData);

                /**** Confirm leave accrual pending*/
                $accrualData = [
                    'confirmedYN' => 1,
                    'confirmedby' => $UserID,
                    'confirmedDate' => current_date()
                ];

                $this->db->where('companyID', $companyID);
                $this->db->where('leaveMasterID', $leaveMasterID);
                $this->db->where('confirmedYN', 0);
                $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

                $this->db->trans_complete();

                if ($this->db->trans_status() == true) {

                    $leaveBalanceData = $this->employeeLeaveSummery($UserID, $leave['leaveTypeID'], $leave['policyMasterID']);
                    $balanceLeave = $leaveBalanceData['balance'];

                    $param["empName"] = $leave["Ename2"];
                    $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                    $mailData = [
                        'approvalEmpID' => $leave['empID'],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $leave["EEmail"],
                        'subject' => 'Employee Leave Approved',
                        'param' => $param,
                    ];


                } else {
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    return array('e', $common_failed);
                }
            }

            return true;

        } else {

            $this->db->select('documentCode');
            $this->db->where('leaveMasterID', trim($leaveMasterID));
            $this->db->from('srp_erp_leavemaster');
            $documentCode = $this->db->get()->row_array();

            $upData = [
                'currentLevelNo' => 0,
                'confirmedYN' => 2,
                'confirmedByEmpID' => null,
                'confirmedByName' => null,
                'confirmedDate' => current_date(),
                'modifiedPCID' => '',
                'modifiedUserID' => $UserID,
                'modifiedUserName' => $userName,
                'modifiedDateTime' => current_date()
            ];

            $this->db->where('leaveMasterID', trim($leaveMasterID));
            $this->db->where('companyID', $companyID);
            $update = $this->db->update('srp_erp_leavemaster', $upData);
            if ($update) {
                $data = array(
                    'documentID' => "LA",
                    'systemID' => $leaveMasterID,
                    'documentCode' => $documentCode['documentCode'],
                    'comment' => $comment,
                    'rejectedLevel' => 1,
                    'rejectByEmpID' => $UserID,
                    'rejectByEmpName' => $userName,
                    'table_name' => "srp_erp_leavemaster",
                    'table_unique_field' => "leaveMasterID",
                    'companyID' => $companyID,
                    'companyCode' => $companyCode,
                    'createdUserGroup' => '.',
                    'createdPCID' => '_',
                    'createdUserID' => $UserID,
                    'createdUserName' => $userName,
                    'createdDateTime' => current_date(),
                );
                $this->db->insert('srp_erp_approvalreject', $data);

                return true;
            } else {
                return array('0');
            }
        }
    }

    function leaveConfirm($name, $companyID, $eid, $companyCode,$leaveMasterID,$empID)
    {
        
       
        $datas = array(
            'confirmedYN' => 1,
            'confirmedByName' => $name
        );
        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $datas);


        $lastCodeArray = $this->db->query("SELECT serialNo FROM srp_erp_leavemaster WHERE companyID={$companyID}
                                            ORDER BY leaveMasterID DESC LIMIT 1")->row_array();
        $lastCodeNo = $lastCodeArray['serialNo'];
        $lastCodeNo = ($lastCodeNo == null) ? 1 : $lastCodeArray['serialNo'] + 1;

        $this->load->library('sequence_mobile');

        $this->db->select('*');
        $this->db->from('srp_erp_employeemanagers');
        $this->db->join('srp_employeesdetails', 'managerID=EIdNo', 'left');
        $this->db->where('empID', $eid);
        $this->db->where('active', 1);
        $result = $this->db->get()->result_array();
        foreach ($result as $val) {
            send_push_notification($val['managerID'], 'Leave Approval', $companyCode, 1, $name);
        }

        return $this->leave_ApprovalCreate($leaveMasterID, '1', $companyID, $companyCode, $empID, $name);
    }

    function delete_empLeave($masterID, $companyID)
    {

        $det = $this->employeeLeave_details($masterID);

        if ($det['approvedYN'] == 1) {
            return "This leave application is Approved";
        } else {
            $this->db->trans_start();
            $this->db->where('leaveMasterID', $masterID)->delete('srp_erp_leavemaster');

            /*** Delete accrual leave ***/
            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $masterID);
            $this->db->delete('srp_erp_leaveaccrualmaster');

            $this->db->where('leaveMasterID', $masterID);
            $this->db->delete('srp_erp_leaveaccrualdetail');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return "Failed delete";
            } else {
                $this->db->trans_commit();
                return "Successfully Deleted";
            }
        }
    }

    function sickLeaveNoPay_calculation($leave = [], $companyID)
    {

//        $isNonSalaryProcess = getPolicyValues('NSP', 'All', $companyID);
        $isNonSalaryProcess = $this->getPolicyValues('SL', 'All', $companyID);
        $leaveTypeID = $leave["leaveTypeID"];
        $empID = $leave["empID"];

        $result = $this->db->query("SELECT salaryCategoryID, formulaString, isNonPayroll FROM srp_erp_sickleavesetup
                                    WHERE companyID='{$companyID}' AND leaveTypeID={$leaveTypeID}")->result_array();

        if (!empty($result)) {
            $detail = [];
            $isSet = 0;
            foreach ($result as $key => $row) {

                $isNonPayroll = $row['isNonPayroll'];
                $table = ($isNonPayroll != 'Y') ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                $formula = trim($row['formulaString'] ?? '');
                $formulaBuilder = formulaBuilder_to_sql_simple_convertion($formula, $companyID);
                $formulaDecodeFormula = $formulaBuilder['formulaDecode'];
                $select_str = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];

                $f_Data = $this->db->query("SELECT (round(({$formulaDecodeFormula }), dPlace) )AS transactionAmount, dPlace
                                             FROM (
                                                SELECT employeeNo, " . $select_str . ", transactionCurrencyDecimalPlaces AS dPlace
                                                FROM {$table} AS salDec
                                                JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID
                                                WHERE salDec.companyID = {$companyID} AND employeeNo={$empID} AND salDec.salaryCategoryID
                                                IN (" . $whereInClause . ") AND salCat.companyID ={$companyID}
                                                GROUP BY employeeNo, salDec.salaryCategoryID
                                             ) calculationTB
                                             JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo
                                             WHERE EIdNo={$empID} AND Erp_companyID = {$companyID}
                                             GROUP BY employeeNo")->row_array();

                $_amount = (!empty($f_Data)) ? $f_Data['transactionAmount'] : 0;
                $dPlace = (!empty($f_Data)) ? $f_Data['dPlace'] : 0;
                $_amount = round(($_amount * $leave['workingDays']), $dPlace);
                if ($row['isNonPayroll'] == 'N') {
                    if ($_amount != 0) {
                        $detail['noPayAmount'] = $_amount;
                        $detail['salaryCategoryID'] = $row['salaryCategoryID'];
                        $isSet++;
                    }
                } else {
                    if ($_amount != 0) {
                        $detail['noPaynonPayrollAmount'] = $_amount;
                        $detail['nonPayrollSalaryCategoryID'] = $row['salaryCategoryID'];
                        $isSet++;
                    }
                }
            }
            if ($isSet == 1) {
                $detail['leaveMasterID'] = $leave['leaveMasterID'];
                $detail['empID'] = $empID;
                $detail['attendanceDate'] = date('Y-m-d', strtotime($leave['endDate']));
                $detail['companyID'] = $companyID;
                $detail['companyCode'] = '';

                $this->db->insert('srp_erp_pay_empattendancereview', $detail);
            }
        }
    }

    function leaveDelete($companyId)
    {
        $request_body = file_get_contents('php://input');
        $request = json_decode($request_body);

        $leaveMasterID = $request->id;
        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyId);
        $this->db->delete('srp_erp_leavemaster');
        return array('1');
    }

    function update_employeesLeave($leaveMasterID, $empID, $leaveType, $startDate, $endDate, $isConfirmed, $entitleSpan, $halfDay, $shift, $comment
        , $isCalenderDays, $appliedLeave, $workingDays, $policyMasterID, $applicationType, $coveringEmpID, $output, $attachmentDescription, $companyID, $empname)
    {
        $this->db->select('leaveGroupID');
        $this->db->from('srp_employeesdetails');
        $this->db->where('EIdNo', $empID);
        $this->db->where('Erp_companyID', $companyID);
        $employeesdetails = $this->db->get()->row_array();
        $leaveGroupID = $employeesdetails['leaveGroupID'];

        $token = $output['token'];
        $leaveMasterID = $leaveMasterID;
        $leaveType = $leaveType;


        $coveringEmpID = (!empty($coveringEmpID)) ? $coveringEmpID : 0;
        $hour = 0;
        $leaveAvailable = $entitleSpan;

        $det = $this->employeeLeave_details($leaveMasterID);

        if ($det['confirmedYN'] == 1) {
            return (array('e', '[ ' . $det['documentCode'] . ' ] is already confirmed'));
        } else {

            if ($isCalenderDays == 1) {
                $days = $appliedLeave;
                $workingDays = $days;
                $nonWorkingDays = $days;

            } else {
                $days = $workingDays;
                $nonWorkingDays = $appliedLeave;
            }

            if ($policyMasterID == 2) {
                /*if its hourly set value for hour and clear*/
                $hour = $days;
                $days = 0;
                $nonWorkingDays = 0;

                $dteStart = new DateTime($startDate);
                $dteEnd = new DateTime($endDate);
                $startDate = $dteStart->format('Y-m-d H:i:s');
                $endDate = $dteEnd->format('Y-m-d H:i:s');
            }

//var_dump($output['token']->name);exit;

            $modifiedPCID = $output['token']->current_pc;
            $modifiedUserID = $output['token']->id;
            $modifiedUserName = $empname;
            $modifiedDateTime = date('Y-m-d H:i:s');

            $data = array(
                'empID' => $empID,
                'leaveTypeID' => $leaveType,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'days' => $days,
                'ishalfDay' => $halfDay,
                'shift' => $shift,
                'isCalenderDays' => $isCalenderDays,
                'workingDays' => $workingDays,
                'nonWorkingDays' => $nonWorkingDays,
                'leaveGroupID' => $leaveGroupID,
                'policyMasterID' => $policyMasterID,
                'applicationType' => $applicationType,
                'hours' => $hour,
                'leaveAvailable' => $leaveAvailable,
                'coveringEmpID' => $coveringEmpID,
                'comments' => $comment,
                'modifiedPCID' => $modifiedPCID,
                'modifiedUserID' => $modifiedUserID,
                'modifiedUserName' => $modifiedUserName,
                'modifiedDateTime' => $modifiedDateTime,
            );

            if ($isConfirmed == 1) {
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = $output['token']->id;
                $data['confirmedByName'] = $empname;
                $data['confirmedDate'] = date('Y-m-d H:i:s');
            } else {
                $data['confirmedYN'] = 0;
            }

            $this->db->trans_start();

            /*attachment */

            $files_count = sizeof($_FILES);
            $i = 0;
            foreach ($_FILES as $file) {
//var_dump($file);exit;
                $attachmentDesc = trim($attachmentDescription[$i]);
                if ($attachmentDesc == '') {
                    return ['e', 'Please enter attachment description'];
                }

                $num = $this->db->select('companyID')->where('documentID', 'LA')->get('srp_erp_documentattachments')->result_array();
                $file_name = 'LA' . '_' . (count($num) + 1);


                if ($file['error'] == 1) {
                    die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
                }
                $ext = pathinfo($file['name'][$i], PATHINFO_EXTENSION);
                $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
                $allowed_types = explode('|', $allowed_types);
                if (!in_array($ext, $allowed_types)) {
                    die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
                }

                $size = $file['size'][$i];
                $size = number_format($size / 1048576, 2);

                if ($size > 5) {
                    die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
                }

                $file_name = "$file_name.$ext";
                $s3Upload = $this->s3->upload($file['tmp_name'][$i], $file_name);

                if (!$s3Upload) {
                    die(json_encode(['e', 'Error in document upload location configuration']));
                }

                $detail['documentID'] = 'LA';
                $detail['documentSystemCode'] = $leaveMasterID;
                $detail['attachmentDescription'] = $attachmentDesc;
                $detail['myFileName'] = $file_name;
                $detail['fileType'] = $ext;
                $detail['fileSize'] = $size;
                $detail['timestamp'] = date('Y-m-d H:i:s');
                $detail['companyID'] = $token->Erp_companyID;
                $detail['companyCode'] = $token->company_code;
                $detail['modifiedPCID'] = $token->current_pc;
                $detail['modifiedUserID'] = $token->id;
                $detail['modifiedUserName'] = $token->username;
                $detail['modifiedDateTime'] = date('Y-m-d H:i:s');
                $detail['createdPCID'] = $token->current_pc;
                $detail['createdUserID'] = $token->id;
                $detail['createdUserName'] = $empname;
                $detail['createdUserGroup'] = $token->usergroupID;
                $detail['createdDateTime'] = date('Y-m-d H:i:s');
                $this->db->insert('srp_erp_documentattachments', $detail);
                //var_dump($this->db->last_query());
                $i++;
            }


            if ($this->input->post('isConfirmed') == 1) {
                $leaveTypeID = $this->input->post('leaveTypeID');
                $isRequiredYes = $this->db->query("select * from srp_erp_leavetype WHERE  leaveTypeID=$leaveTypeID AND attachmentRequired=1 ")->row_array();
                if (!empty($isRequiredYes)) {
                    $leaveMasterID = $this->input->post('leaveMasterID');
                    $attachmentExist = $this->db->query("SELECT * FROM srp_erp_documentattachments WHERE documentID='LA' AND documentSystemCode='$leaveMasterID'")->row_array();
                    if (empty($attachmentExist)) {
                        echo exit(json_encode(array('e', 'Please attach relevant document to confirm')));

                    }
                }
            }

            /*leave Update*/
            $this->db->where('leaveMasterID', $leaveMasterID)->update('srp_erp_leavemaster', $data);


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Failed Update Data');
            } else {
                $this->db->trans_commit();

                if ($isConfirmed == 1) {

                    return $this->leave_ApprovalCreate($leaveMasterID, $level = 1, $companyID, $output['token']->company_code, $empID, $token->username);
                    /*$leaveBalanceData = $this->employeeLeaveSummery($empID, $leaveType, $policyMasterID);
                    $balanceLeave = $leaveBalanceData['balance'];
                    $balanceLeave = ($balanceLeave > 0)?  ($balanceLeave - $days) : 0;

                    $this->db->select('*');
                    $this->db->from('srp_erp_employeemanagers');
                    $this->db->join('srp_employeesdetails', 'managerID=EIdNo', 'left');
                    $this->db->where('empID', $empID);
                    $this->db->where('active', 1);
                    $result = $this->db->get()->result_array();
                    foreach ($result as $val) {

                        $param["empName"] = $val["Ename2"];
                        $param["body"] = 'Leave application ' . $det['documentCode'] . ' is pending for your approval.<br/>
                                          <table border="0px">
                                                <tr><td><strong>Leave type </td><td> : '.$leaveBalanceData['description'].'</td></tr>
                                                <tr><td><strong>Leave balance </td><td> : '.$balanceLeave.'</td></tr>
                                          </table>';

                        $mailData = [
                            'approvalEmpID' => $val['managerID'],
                            'documentCode' => $det['documentCode'],
                            'toEmail' => $val["EEmail"],
                            'subject' => 'Leave Approval',
                            'param' => $param,
                        ];

                        send_approvalEmail($mailData);
                    }

                    return ['s', 'Leave Approval created successfully.'];*/

                } else {
                    return array("status" => 's', "email" => null);
//                    return ['s', 'Leave Update Process Success.'];
                }
            }
        }

    }

    public function date_format_policy($companyID)
    {
        $date_format_policy_details = $this->session_model->fetch_company_policy($companyID);
        $date_format_policy = $date_format_policy_details['DF']['All'][0]["policyvalue"];
        return $date_format_policy;
    }

    public function convert_date_format_sql($companyID)
    {
        $date_format_policy = $this->date_format_policy($companyID);
        $text = str_replace('yyyy', '%Y', $date_format_policy);
        $text = str_replace('mm', '%m', $text);
        $text = str_replace('dd', '%d', $text);
        return $text;
    }

    function fetch_all_approval_users_modal($companyID, $documentID, $systemID)
    {
        switch ($documentID) {

            case 'LA':
                $convertFormat = convert_date_format_sql();
                $this->db->select('requestForCancelYN, coveringEmpID');
                $this->db->from('srp_erp_leavemaster');
                $this->db->where('leaveMasterID', $systemID);
                $this->db->where('companyID', $companyID);
                $masterData = $this->db->get()->row_array();

                $coveringEmpID = $masterData['coveringEmpID'];
                $requestForCancelYN = $masterData['requestForCancelYN'];

                $this->db->select("approvalLevelID,approvedYN,DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                if ($requestForCancelYN == 1) {
                    $this->db->where('isCancel', 1);
                }
                $approved = $this->db->get()->result_array();


                $setupData = $this->getLeaveApprovalSetup($companyID, 'Y');
                $approvalSetup = $setupData['approvalSetup'];
                $approvalEmp_arr = $setupData['approvalEmp'];
                $managers = $this->db->query("SELECT * FROM (
                                                 SELECT repManager, repManagerName, currentLevelNo
                                                 FROM srp_erp_leavemaster AS empTB
                                                 LEFT JOIN (
                                                     SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                                                     JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND Erp_companyID={$companyID}
                                                     WHERE active = 1 AND companyID={$companyID}
                                                 ) AS repoManagerTB ON empTB.empID = repoManagerTB.empID
                                                 WHERE companyID = '{$companyID}' AND leaveMasterID={$systemID}
                                             ) AS empData
                                             LEFT JOIN (
                                                  SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                                                  FROM srp_erp_employeemanagers AS t1
                                                  JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND Erp_companyID={$companyID}
                                                  WHERE companyID={$companyID} AND active = 1
                                             ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

                $approvalData = [];
                $k = 0;
                foreach ($approved as $key => $row) {
                    $thisLevel = $row['approvalLevelID'];

                    $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $thisLevel);
                    $arr = array_map(function ($k) use ($approvalSetup) {
                        return $approvalSetup[$k];
                    }, $keys);

                    $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                    if ($approvalType == 3) {
                        $hrManagerID = (array_key_exists($thisLevel, $approvalEmp_arr)) ? $approvalEmp_arr[$thisLevel] : [];
                        $hrManagerID = array_column($hrManagerID, 'empID');

                        if (!empty($hrManagerID)) {
                            foreach ($hrManagerID as $hrManagerRow) {
                                $hrEmpData = fetch_employeeNo($hrManagerRow);
                                $approved[$key]['Ename2'] = $hrEmpData['Ename2'];
                                $approvalData[] = $approved[$key];
                            }
                        } else {
                            $approvalData[] = $approved[$key];
                        }
                    } else if ($approvalType == 4) {
                        /*echo $approvalType.' <br/> cover :';
                        echo $coveringEmpID.' <br/>';*/
                        if (!empty($coveringEmpID)) {
                            $coveringEmpData = fetch_employeeNo($coveringEmpID);
                            $approved[$key]['Ename2'] = $coveringEmpData['Ename2'];
                            $approvalData[] = $approved[$key];
                        } else {
                            $approvalData[] = $approved[$key];
                        }
                    } else {
                        $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                        if (!empty($managers[$managerType])) {
                            $approved[$key]['Ename2'] = $managers[$managerType . 'Name'];
                        }
                        $approvalData[] = $approved[$key];

                    }
                }


                $approval_details = array();
                foreach ($approvalData as $item) {
                    $approval_item = array();
                    $approval_item['approvalLevelID'] = (int)$item['approvalLevelID'];
                    $approval_item['approvedYN'] = (int)$item['approvedYN'];
                    $approval_item['approvedDate'] = $item['approvedDate'];
                    $approval_item['approvedComments'] = $item['approvedComments'];
                    $approval_item['documentCode'] = $item['documentCode'];
                    $approval_item['docConfirmedByEmpID'] = (int)$item['docConfirmedByEmpID'];
                    $approval_item['documentDate'] = $item['documentDate'];
                    $approval_item['Ename2'] = $item['Ename2'];
                    $approval_item['docConfirmedDate'] = $item['docConfirmedDate'];
                    $approval_item['approveDate'] = $item['approveDate'];
                    array_push($approval_details, $approval_item);
                }

                $data_arr['approvals'] = $approval_details;
//                if(sizeof($data_arr['approvals'])>0){
//                    $data_arr['document_code'] = $data_arr['approvals'][0]['documentCode'];
//                    $data_arr['document_date'] = $data_arr['approvals'][0]['documentDate'];
//                    $data_arr['confirmed_date'] = $data_arr['approvals'][0]['docConfirmedDate'];
//                    $empData = fetch_employeeNo($data_arr['approvals'][0]['docConfirmedByEmpID']);
//                    $data_arr['conformed_by'] = $empData['Ename2'];
//                }else{
//                    $data_arr['document_code'] = null;
//                    $data_arr['document_date'] =null;
//                    $data_arr['confirmed_date'] =null;
//                    $data_arr['conformed_by'] = null;
//                }

                $data_arr['requestForCancelYN'] = $requestForCancelYN;

                return $data_arr;
                break;
            case 'PRQ':
                $convertFormat = convert_date_format_sql();
//                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                /*    $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                      DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS
                                      docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\")
                                      AS approveDate");
                    $this->db->from('srp_erp_documentapproved');
                    $this->db->join('srp_erp_approvalusers ap', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID');
                    $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = ap.employeeID');
                    $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                    $this->db->where('ap.documentID', $documentID);
                    $this->db->where('documentSystemCode', $systemID);
                    $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                    $this->db->where('ap.companyID', $companyID);*/

                $data_arr['approved'] = $this->db->query("SELECT
IF(ap.employeeID=-1,reporting.EIdNo,app_emp.EIdNo) as EIdNo,
IF(ap.employeeID=-1,reporting.ECode, app_emp.ECode) as ECode,
IF(ap.employeeID=-1,reporting.Ename2,`app_emp`.`Ename2`) as Ename2,


    `approvalLevelID`,
    srp_erp_documentapproved.approvedYN,
    DATE_FORMAT(srp_erp_documentapproved.approvedDate,\"" . $convertFormat . "\") AS approvedDate,
    `approvedComments`,
    srp_erp_documentapproved.documentCode,
    `docConfirmedByEmpID`,
    DATE_FORMAT(srp_erp_documentapproved.documentDate,\"" . $convertFormat . "\") AS documentDate,
    DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
    DATE_FORMAT(srp_erp_documentapproved.approvedDate,\"" . $convertFormat . "\") AS approveDate
FROM
    `srp_erp_documentapproved`
    JOIN `srp_erp_approvalusers` `ap` ON `ap`.`levelNo` = `srp_erp_documentapproved`.`approvalLevelID`
    LEFT JOIN `srp_employeesdetails` `app_emp` ON `app_emp`.`EIdNo` = `ap`.`employeeID`
		LEFT JOIN srp_erp_purchaserequestmaster prmaster on prmaster.purchaseRequestID = srp_erp_documentapproved.documentSystemCode

		LEFT JOIN (SELECT employee.EIdNo,empID FROM srp_erp_employeemanagers managerTb JOIN srp_employeesdetails employee ON managerTb.managerID=employee.EIdNo
where managerTb.active=1 ) employeemanager on employeemanager.empID = prmaster.requestedEmpID
LEFT JOIN srp_employeesdetails reporting on reporting.EIdNo = employeemanager.EIdNo

WHERE
    `srp_erp_documentapproved`.`documentID` = '{$documentID}'
    AND `ap`.`documentID` = '{$documentID}'
    AND `documentSystemCode` = '{$systemID}'
    AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
    AND `ap`.`companyID` = '{$companyID}'")->result_array();


                if (!empty($data_arr['approved'])) {
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
                break;

            case 'SAR':
                $convertFormat = convert_date_format_sql();
//                $convertFormat = convert_date_format_sql();
                $data_arr = array();

                $data_arr['approved'] = $this->db->query("SELECT IF(ap.employeeID=-1,reporting.EIdNo,app_emp.EIdNo) as EIdNo,
                        IF(ap.employeeID=-1,reporting.ECode, app_emp.ECode) as ECode, IF(ap.employeeID=-1,reporting.Ename2,app_emp.Ename2) as Ename2,
                        approvalLevelID, doc_app.approvedYN,DATE_FORMAT(doc_app.approvedDate, '{$convertFormat}') AS approvedDate, approvedComments, doc_app.documentCode, docConfirmedByEmpID, 
                        DATE_FORMAT(doc_app.documentDate, '{$convertFormat}') AS documentDate, DATE_FORMAT(docConfirmedDate, '{$convertFormat}') AS docConfirmedDate,
                        DATE_FORMAT(doc_app.approvedDate, '{$convertFormat}') AS approveDate
                        FROM srp_erp_documentapproved AS doc_app
                        JOIN srp_erp_approvalusers AS ap ON ap.levelNo = doc_app.approvalLevelID
                        LEFT JOIN srp_employeesdetails AS app_emp ON app_emp.EIdNo = ap.employeeID
                        LEFT JOIN srp_erp_pay_salaryadvancerequest prmaster ON prmaster.masterID = doc_app.documentSystemCode
                        LEFT JOIN (
                            SELECT employee.EIdNo,empID FROM srp_erp_employeemanagers managerTb 
                            JOIN srp_employeesdetails employee ON managerTb.managerID=employee.EIdNo
                            WHERE managerTb.active = 1 
                        ) AS employeemanager ON employeemanager.empID = prmaster.empID
                        LEFT JOIN srp_employeesdetails reporting on reporting.EIdNo = employeemanager.EIdNo
                        WHERE doc_app.documentID = '{$documentID}' AND ap.documentID = '{$documentID}' AND documentSystemCode = '{$systemID}' 
                        AND doc_app.companyID = '{$companyID}' AND ap.companyID = '{$companyID}'")->result_array();


                if (!empty($data_arr['approved'])) {
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);

                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
                break;
            case 'EC':
                $convertFormat = convert_date_format_sql();
                $this->db->select('*,DATE_FORMAT(srp_erp_expenseclaimmaster.approvedDate,\'' . $convertFormat . '\') AS approvedDate,DATE_FORMAT(srp_erp_expenseclaimmaster.confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,DATE_FORMAT(srp_erp_expenseclaimmaster.expenseClaimDate,\'' . $convertFormat . '\') AS expenseClaimDate,srp_employeesdetails.Ename2');
                $this->db->where('expenseClaimMasterAutoID', $systemID);
                $this->db->where('srp_erp_employeemanagers.active', 1);
                $this->db->join('srp_erp_employeemanagers', 'srp_erp_expenseclaimmaster.claimedByEmpID = srp_erp_employeemanagers.empID');
                $this->db->join('srp_employeesdetails', 'srp_erp_employeemanagers.managerID = srp_employeesdetails.EIdNo');
                $this->db->from('srp_erp_expenseclaimmaster');
                $res = $this->db->get()->row_array();
//                $data['approvals'] = $res;

                $approval_EC = array();
                $approval_EC['approvalLevelID'] = (int)$res['level'];
                $approval_EC['approvedYN'] = (int)$res['approvedYN'];
                $approval_EC['approvedDate'] = $res['approvedDate'];
                $approval_EC['approvedComments'] = $res['approvalComments'];

                $approval_EC['documentCode'] = $res['expenseClaimCode'];
                $approval_EC['docConfirmedByEmpID'] = (int)$res['confirmedByEmpID'];
                $approval_EC['documentDate'] = $res['expenseClaimDate'];
                $approval_EC['Ename2'] = $res['Ename2'];
                $approval_EC['docConfirmedDate'] = $res['confirmedDate'];
                $approval_EC['approveDate'] = $res['approvedDate'];

                $data['approvals'][] = $approval_EC;

                return $data;
                break;
            default:
                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS
                                  docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->join('srp_erp_approvalusers ap', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID');
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = ap.employeeID');
                $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                $this->db->where('ap.documentID', $documentID);
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                $this->db->where('ap.companyID', $companyID);
                $data_arr['approved'] = $this->db->get()->result_array();
                if (!empty($data_arr['approved'])) {
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
        }
    }

    function all_document_code_drop($companyID, $status = TRUE)
    {

        $this->db->select("srp_erp_documentcodemaster.documentID,srp_erp_documentcodemaster.document");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->join('srp_erp_documentcodes',
            'srp_erp_documentcodes.documentID = srp_erp_documentcodemaster.documentID');
        $this->db->where('companyID', $companyID);
        $this->db->where('isApprovalDocument', 1);
        $data = $this->db->get()->result_array();
        if (isset($data)) {
            $document_type_list = array();
            foreach ($data as $row) {
                $document_type = array();
                $document_type['id'] = $row['documentID'];
                $document_type['description'] = $row['document'];
                array_push($document_type_list, $document_type);
            }
        }

        return $document_type_list;
    }

    function save_leaveApproval($companyID, $current_userID, $status, $level, $comments, $leaveMasterID)
    {

        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        if ($status == 2) {
            /**** Document refer back process ****/

            $upData = [
                'currentLevelNo' => 0,
                'confirmedYN' => 2,
                'confirmedByEmpID' => null,
                'confirmedByName' => null,
                'confirmedDate' => null,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);


            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->delete('srp_erp_documentapproved');


            $rejectData = [
                'documentID' => 'LA',
                'systemID' => $leaveMasterID,
                'documentCode' => $leave['documentCode'],
                'comment' => $comments,
                'rejectedLevel' => $level,
                'rejectByEmpID' => current_userID(),
                'table_name' => 'srp_erp_leavemaster',
                'table_unique_field' => 'leaveMasterID',
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            ];

            $this->db->insert('srp_erp_approvalreject', $rejectData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is refer backed';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Refer backed',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                return array('s', 'Leave application refer backed successfully');

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }


        $setupData = getLeaveApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $isManagerAvailableForNxtApproval = 0;
        $nextApprovalEmpID = null;
        $nextLevel = ($level + 1);

        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($nextLevel <= $approvalLevel) {

            $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $nextLevel;

            /**** Validate is there a manager available for next approval level ****/
            while ($x <= $approvalLevel) {
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {
                    //$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '';
                    $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                    $nextLevel = $x;
                    $nextApprovalEmpID = $hrManagerID;
                    $isManagerAvailableForNxtApproval = 1;
                    $x = $approvalLevel;

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = $managers[$managerType];
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    }

                }

                $x++;
            }

        }


        if ($isManagerAvailableForNxtApproval == 1) {
            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                    AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave application ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                 <table border="0px">
                                    <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                    <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                    <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr> ';

                    if ($coveringEmpID != $nxtEmpData["EIdNo"]) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Approval',
                        'param' => $param
                    ];


                    send_approvalEmail($mailData);
                }

                $success_msg = strtolower($this->lang->line('hrms_payroll_approved_successfully'));/*'Approved successfully'*/
                return array('s', 'Level ' . $level . ' is ' . $success_msg);

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        } else {

            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 1,
                'approvedDate' => current_date(),
                'approvedbyEmpID' => $current_userID,
                'approvedbyEmpName' => $this->common_data['current_user'],
                'approvalComments' => $comments,
            );

            $this->db->trans_start();


            if ($leave["isSickLeave"] == 1) {
                $this->sickLeaveNoPay_calculation($leave);
            }

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $data);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);


            /**** Confirm leave accrual pending*/
            $accrualData = [
                'confirmedYN' => 1,
                'confirmedby' => current_userID(),
                'confirmedDate' => current_date()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('confirmedYN', 0);
            $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Approved',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                $success_msg = $this->lang->line('hrms_payroll_approved_successfully');/*'Approved successfully'*/
                return array('s', $success_msg);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }
    }

    function leave_cancellation_approval()
    {
        $companyID = current_companyID();
        $current_userID = current_userID();

        $status = $this->input->post('status');
        $level = $this->input->post('level');
        $comments = $this->input->post('comments');
        $leaveMasterID = $this->input->post('hiddenLeaveID');

        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        if ($status == 2) {
            /**** Document refer back process ****/
            //die(json_encode(['e', 'Error']));
            $upData = [
                'requestForCancelYN' => 2,
                'cancelRequestedDate' => null,
                'cancelRequestComment' => null,
                'cancelRequestByEmpID' => null,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);


            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('isCancel', 1);
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->delete('srp_erp_documentapproved');


            $rejectData = [
                'documentID' => 'LA',
                'systemID' => $leaveMasterID,
                'documentCode' => $leave['documentCode'],
                'comment' => $comments,
                'isFromCancel' => 1,
                'rejectedLevel' => $level,
                'rejectByEmpID' => current_userID(),
                'table_name' => 'srp_erp_leavemaster',
                'table_unique_field' => 'leaveMasterID',
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            ];

            $this->db->insert('srp_erp_approvalreject', $rejectData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave cancellation ' . $leave['documentCode'] . ' is refer backed';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Refer backed',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                return array('s', 'Leave cancellation refer backed successfully');

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }


        $setupData = getLeaveApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $isManagerAvailableForNxtApproval = 0;
        $nextApprovalEmpID = null;
        $nextLevel = ($level + 1);

        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($nextLevel <= $approvalLevel) {

            $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $nextLevel;

            /**** Validate is there a manager available for next approval level ****/
            while ($x <= $approvalLevel) {
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {
                    //$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '';
                    $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                    $nextLevel = $x;
                    $nextApprovalEmpID = $hrManagerID;
                    $isManagerAvailableForNxtApproval = 1;
                    $x = $approvalLevel;

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = $managers[$managerType];
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    }

                }

                $x++;
            }

        }


        if ($isManagerAvailableForNxtApproval == 1) {
            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                    AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave cancellation ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>';

                    if ($coveringEmpID != $nxtEmpData["EIdNo"]) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Cancellation Approval',
                        'param' => $param
                    ];


                    send_approvalEmail($mailData);
                }

                $success_msg = strtolower($this->lang->line('hrms_payroll_approved_successfully'));/*'Approved successfully'*/
                return array('s', 'Level ' . $level . ' is ' . $success_msg);

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        } else {

            $data = array(
                'cancelledYN' => 1,
                'currentLevelNo' => $approvalLevel,
                'cancelledDate' => current_date(),
                'cancelledByEmpID' => $current_userID,
                'cancelledComment' => $comments,
            );

            $this->db->trans_start();


            if ($leave["isSickLeave"] == 1) {
                //$this->sickLeaveNoPay_calculation($leave);
            }


            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $data);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('isCancel', 1);
            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);


            /**** delete leave accruals that are created from calender holiday declaration*/
            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->delete('srp_erp_leaveaccrualmaster');

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->delete('srp_erp_leaveaccrualdetail');


            //if($leave['isCalenderDays'] == 0){
            /***** create leave accrual for leave cancellation  *****/
            $this->create_leave_accrual($leave);
            //}

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is cancelled.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Cancelled',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                $success_msg = $this->lang->line('hrms_payroll_approved_successfully');/*'Approved successfully'*/
                return array('s', $success_msg);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }
    }

    function fetch_current_financial_year_details($companyID)
    {
        $this->db->select('beginingDate, endingDate');
        $this->db->where('companyID', $companyID);
        $this->db->where('isCurrent', 1);
        $financialYear = $this->db->get('srp_erp_companyfinanceyear')->row_array();

        $this->db->select('dateFrom, dateTo');
        $this->db->where('companyID', $companyID);
        $this->db->where('isCurrent', 1);
        $financialPeriod = $this->db->get('srp_erp_companyfinanceperiod')->row_array();

        $financialYearDetails = array(
            'beginingDate' => $financialYear['beginingDate'],
            'endingDate' => $financialYear['endingDate'],
            'dateFrom' => $financialPeriod['dateFrom'],
            'dateTo' => $financialPeriod['dateTo']
        );
        return $financialYearDetails;
    }

    function checkApproved($documentSystemCode, $documentID, $approvalLevelID, $companyID)
    {
        $this->db->SELECT("documentApprovedID");
        $this->db->FROM('srp_erp_documentapproved');
        $this->db->where('companyID', $companyID);
        $this->db->where('documentSystemCode', $documentSystemCode);
        $this->db->where('documentID', $documentID);
        $this->db->where('approvalLevelID', $approvalLevelID);
        $this->db->where('approvedYN', 1);
        $data = $this->db->get()->row_array();
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     /**
     * Change user password
     * @param password new password
     * @param old_password
     * @param username email of the user
     * @created Hasitha
     * @created at 2022-08-10
     * 
     * refering to Api_spur/changepassword_post 
     */
    function changePassword($password,$old_password,$username){

        $newPassword = md5($password);
        $oldPassword = md5($old_password);
        $data = array();

        $emp_user = $this->getEmployeeMaster($username);
        
        if($emp_user){
            $emp_user_password = $emp_user->Password;
            $empID = $emp_user->empID;
            
            if($oldPassword != $emp_user_password){
                $data['status'] = 'error';
                $data['message'] = 'Given password not mached to existing one';
                return $data;
            }

            try {
                // update user main db 
                $res = $this->setEmployeeMainUserPassword($newPassword,$username);

                // update company db
                $res = $this->setEmployeeCompanyPassword($newPassword,$empID);

                $data['status'] = 'success';
                $data['message'] = 'Operation Successful.';

            } catch(Exception $e){
                $data['status'] = 'error';
                $data['message'] = 'Something went wrong.';
            }
         
        }
       
      return $data;

    }

    function getEmployeeMaster($username){

        $CI =& get_instance();
        
        $db2 = $CI->load->database('db2', TRUE);

        $user = $db2->from('user')->where('username', $username)->get()->row();
       
        return $user;

    }

    function setEmployeeMainUserPassword($password,$username){

        $CI =& get_instance();
        
        $db2 = $CI->load->database('db2', TRUE);

        if($username){

            $data['Password'] = $password;

            $db2->where('Username', $username);
            $db2->update('user', $data);

        }
       
    }

    function setEmployeeCompanyPassword($password,$empID){

        $emp_record = $this->db->from('srp_employeesdetails')
                            ->where('EIdNo',$empID)
                            ->get()->row();
        
        if($emp_record){

            $data['Password'] = $password;

            $this->db->where('EIdNo', $empID);
            $this->db->update('srp_employeesdetails', $data);

        }

    }

}
