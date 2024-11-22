<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Approvals_mobile
{

    private $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->ci->load->library('email_manual');
        $this->ci->load->database();
    }


    /**
     * @param $document - document shortcode eg. GRV,PO AND BSI
     * @param $documentID - document Auto ID
     * @param $documentCode - system generated code
     * @param $documentName - Document full name eg. Good Received Note
     * @param string $table_name
     * @param string $table_unique_field_name
     * @param int $autoApprove - not in use
     * @param $documentDate - document date
     * @return int
     */
    public function CreateApproval($document, $documentID, $documentCode, $documentName, $table_name = '', $table_unique_field_name = '', $autoApprove = 0, $documentDate=null, $companyData=array() )
    {
        $companyID = current_companyID();
        $company_code = current_companyCode();
        $createdUserGroup = current_user_group();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_user();
        $createdDate = current_date();
        $createdDateTime = current_date();

        $maxlevel = $this->maxlevel($document, $companyID);
        $documentDate = ($documentDate==null)? $createdDate:$documentDate;
        /*$this->ci->db->select('levelNo, employeeID');
        $this->ci->db->where('Status', 1);
        $this->ci->db->where('companyID', $this->ci->common_data['company_data']['company_id']);
        $this->ci->db->where('documentID', $document);
        $this->ci->db->from('srp_erp_approvalusers');
        $this->ci->db->order_by('levelNo');
        $approvalusers = $this->ci->db->get()->result_array();*/

        if (!empty($maxlevel["levelNo"])) {
            $data_app = array();
            for ($i = 1; $i <= $maxlevel["levelNo"]; $i++) {
                $data_app[$i]['companyID'] = $companyID;
                $data_app[$i]['companyCode'] = $company_code;
                $data_app[$i]['departmentID'] = $document;
                $data_app[$i]['documentID'] = $document;
                $data_app[$i]['documentSystemCode'] = $documentID;
                $data_app[$i]['documentCode'] = $documentCode;
                $data_app[$i]['table_name'] = $table_name;
                $data_app[$i]['table_unique_field_name'] = $table_unique_field_name;
                $data_app[$i]['documentDate'] = $documentDate;
                $data_app[$i]['approvalLevelID'] = $i;
                $data_app[$i]['roleID'] = null;
                $data_app[$i]['approvalGroupID'] = $createdUserGroup;
                $data_app[$i]['roleLevelOrder'] = null;
                $data_app[$i]['docConfirmedDate'] = $createdDate;
                $data_app[$i]['docConfirmedByEmpID'] = $createdUserID;
                $data_app[$i]['approvedEmpID'] = null;
                $data_app[$i]['approvedYN'] = 0;
                $data_app[$i]['approvedDate'] = null;
            }

            $this->ci->db->insert_batch('srp_erp_documentapproved', $data_app);

            if (trim($table_name) != '' && trim($table_unique_field_name) != '') {
                $data = array(
                    'confirmedYN' => '1',
                    'confirmedDate' => $createdDate,
                    'confirmedByEmpID' => $createdUserID,
                );

                if(!in_array($document, ['VD'])){
                    $data['confirmedByName'] = $createdUserName;
                }

                $this->ci->db->where($table_unique_field_name, $documentID);
                $this->ci->db->update($table_name, $data);

                /* write my alert table*/
//                $policy = getPolicyValues('SEN', 'All');
//                if ($policy == 1 || $policy == null) {
                    //$this->emailAlert($document, 1, $documentID, $documentCode);
//                }
                /**/
                $this->ci->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
            }

            $this->ci->session->set_flashdata('s', 'Approval Created : ' . $documentName . ' : ' . $documentCode . ' Successfully.');
            return 1;
        }
        else {
            if ($autoApprove == 1) {
                if (trim($table_name) != '' && trim($table_unique_field_name) != '') {
                    $data = array(
                        'confirmedYN' => '1',
                        'confirmedDate' => $createdDate,
                        'confirmedByEmpID' => $createdUserID,
                        'approvedYN' => '1',
                        'approvedDate' => $createdDate,
                        'approvedbyEmpID' => $createdUserID,
                    );

                    if(!in_array($document, ['VD'])){
                        $data['confirmedByName'] = $createdUserName;
                        $data['approvedbyEmpName'] = $createdUserName;
                    }

                    $this->ci->db->where($table_unique_field_name, $documentID);
                    $this->ci->db->update($table_name, $data);


                    $this->ci->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
                    return 1;
                } else {
                    $this->ci->session->set_flashdata('w', 'There are no users exist to perform ' . $documentName . ' : ' . $documentCode . ' approval for this company.');
                    return 3;
                }
            } else {
                $this->ci->session->set_flashdata('w', 'There are no users exist to perform ' . $documentName . ' : ' . $documentCode . ' approval for this company.');
                return 3;
            }

        }
    }

    //approve_document($system_code, $level_id, $status, $comments, 'ST');
    //$this->approvals->approve_document($system_code,$level_id,$status,$comments,'GRV');
    function approve_document($system_code, $level_id, $status, $comments, $documentCode)
    {
        $this->ci->db->select('documentCode,approvedYN');
        $this->ci->db->from('srp_erp_documentapproved');
        $this->ci->db->where('documentID', $documentCode);
        $this->ci->db->where('documentSystemCode', $system_code);
        $this->ci->db->where('approvedYN', 2);
        $this->ci->db->where('companyID', current_companyID());
        $approval_data = $this->ci->db->get()->row_array();

        if (!empty($approval_data)) {
            $this->session->set_flashdata('w', $documentCode . 'Approval : ' . $approval_data['documentCode'] . ' This ' . $documentCode . ' has been rejected already! You cannot do approval for this..');
//            return 3;
            return array('status' => 'e', 'email' => null);
        } else {
            if ($level_id > 1) {
                $previousLevel = $level_id - 1;
                $isLast_where = array('documentID' => $documentCode, 'documentSystemCode' => $system_code, 'approvalLevelID' => $previousLevel);
                $this->ci->db->select('approvedYN');
                $this->ci->db->from('srp_erp_documentapproved');
                $this->ci->db->where($isLast_where);
                $isLastLevelApproved = $this->ci->db->get()->row_array();
                if ($isLastLevelApproved['approvedYN'] == 1) {
                    if ($status == 1) {
                        return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                    } elseif ($status == 2) {
                        return $this->reject($system_code, $level_id, $comments, $documentCode);
                    }

                } else {
                    $this->ci->session->set_flashdata('w', $documentCode . ' `s Previous level Approval not Finished.');
//                    return 5;
                    return array('status' => '5', 'email' => null);
                }
            } else {
                if ($status == 1) {
                    return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                } elseif ($status == 2) {
                    return $this->reject($system_code, $level_id, $comments, $documentCode);
                }
            }
        }
    }

    function approve($system_code, $level_id, $status, $comments, $documentCode)
    {
        
        $maxlevel = $this->maxlevel($documentCode);
        $maxlevelNo = $maxlevel['levelNo'];

        $this->ci->db->trans_start();

        $data = array(
            'approvedYN' => $status,
            'approvedEmpID' => current_userID(),
            'approvedComments' => $comments,
            'approvedDate' => current_date(true),
            'approvedPC' => current_pc()
        );


        $this->ci->db->where('documentSystemCode', $system_code);
        $this->ci->db->where('documentID', $documentCode);
        $this->ci->db->where('approvalLevelID', $level_id);
        $this->ci->db->where('companyID', current_companyID());
        $this->ci->db->update('srp_erp_documentapproved', $data);
        $data = $this->details($system_code, $documentCode);
        /* write my alert table*/

        $policy = getPolicyValues('SEN', 'All');

        $email = null;
        if ($policy == 1 || $policy == null) {
            $email = $this->emailAlert_other_approvers($documentCode, $level_id, $system_code, $data['documentCode']);
            $emails = $this->emailAlert($documentCode, $level_id + 1, $system_code, $data['documentCode']);
            if(!empty($emails)) {
                foreach ($emails  as $mail){
                    array_push($email, $mail);
                }
            }
            if ($maxlevelNo == $level_id) {
                $mailData = $this->emailfinalAlert($data['table_name'], $data['table_unique_field_name'], $system_code, $data['documentCode'], $documentCode);
                if(!empty($mailData)){
                    array_push($email, $mailData);
                }
            }
        }
        /**/
        if ($data['approvedYN'] == 1) {
            if (!empty($data['table_unique_field_name']) && !empty($data['table_name'])) {
                $dataUpdate = array(
                    'approvedYN' => '1',
                    'approvedDate' => current_date(true),
                    'approvedbyEmpID' => current_userID(),
                );

                if(!in_array($documentCode, ['VD'])){
                    $dataUpdate['approvedbyEmpName'] = current_user();
                }

                $this->ci->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
                $this->ci->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

                $this->ci->db->trans_complete();
                if ($this->ci->db->trans_status() === FALSE) {
                    $this->ci->db->trans_rollback();
                    $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval.');
                    return array('status' => 'e', 'email' => $email);
//                    return 'e';
                } else {
                    $this->ci->db->trans_commit();
                    $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                    return array('status' => 1, 'email' => $email);
//                    return 1;
                }

            } else {
                $this->ci->db->trans_complete();
                if ($this->ci->db->trans_status() === FALSE) {
                    $this->ci->db->trans_rollback();
                    $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval.');
                    return array('status' => 'e', 'email' => null);
//                    return 'e';
                } else {
                    $this->ci->db->trans_commit();
                    $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                    return array('status' => 3, 'email' => $email);
//                    return 3;
                }
            }
        } else {
            /*update current level in master record*/
            $dataUpdate = array(
                'currentLevelNo' => $level_id + 1,
            );
            $this->ci->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
            $this->ci->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

            $this->ci->db->trans_complete();
            if ($this->ci->db->trans_status() === FALSE) {
                $this->ci->db->trans_rollback();
                return array('status' => 'e', 'email' => $email);
//                return 'e';
            } else {
                $this->ci->db->trans_commit();
                $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                return array('status' => 2, 'email' => $email);
//                return 2;
            }
        }

    }

    function details($system_code, $documentCode)
    {
        $this->ci->db->select('documentID, documentCode, table_name, table_unique_field_name, approvedYN');
        $this->ci->db->from('srp_erp_documentapproved');
        $this->ci->db->where('documentSystemCode', $system_code);
        $this->ci->db->where('documentID', $documentCode);
        $this->ci->db->where('companyID', current_companyID());
        $this->ci->db->order_by('approvalLevelID', 'DESC');
        $this->ci->db->limit(1);
        return $this->ci->db->get()->row_array();
    }

    function maxlevel($document, $companyID = null)
    {
        if(empty($companyID)) {
            $companyID = current_companyID();
        }
        $this->ci->db->select_max('levelNo');
        $this->ci->db->where('Status', 1);
        $this->ci->db->where('companyID', $companyID);
        $this->ci->db->where('documentID', $document);
        $this->ci->db->from('srp_erp_approvalusers');
        return $this->ci->db->get()->row_array();
    }

    function reject($system_code, $level_id, $comments, $documentCode)
    {
        $this->ci->db->trans_start();
        $data = $this->details($system_code, $documentCode);
        $rejectData = array(
            'documentID' => $data['documentID'],
            'systemID' => $system_code,
            'documentCode' => $data['documentCode'],
            'comment' => $comments,
            'rejectedLevel' => $level_id,
            'rejectByEmpID' => current_userID(),
            'table_name' => $data['table_name'],
            'table_unique_field' => $data['table_unique_field_name'],
            'companyID' => current_companyID(),
            'companyCode' => current_companyCode(),
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_user(),
            'createdDateTime' => current_date()
        );

        $this->ci->db->insert('srp_erp_approvalreject', $rejectData);

        $this->ci->db->trans_commit();
        if ($this->ci->db->trans_status() === FALSE) {
            $this->ci->db->trans_rollback();
            $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval Reject Process.');
//            return 'e';
            return array('status' => 'e', 'email' => null);
        } else {

            $delete_data = $this->approve_delete($system_code, $documentCode, false);
            $mailarray = array();
            if ($delete_data == 1) {
                $mailData = $this->emailRejectAlert($data['table_name'], $data['table_unique_field_name'], $system_code, $data['documentCode'], $documentCode);
                array_push($mailarray, $mailData);
                $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Approvals  Reject Process Successfully done.');
//                return 3;
                return array('status' => 3, 'email' => $mailarray);
            } else {
                $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Approvals  Reject Process Failed.');
//                return $delete_data;
                return array('status' => $delete_data, 'email' => null);
            }
        }

    }

    function approve_delete($system_code, $documentCode, $status = true)
    {
        $this->ci->db->trans_start();

        $data = $this->details($system_code, $documentCode);

        if ($status) {
            $confirmedYN = 3;
        } else {
            $confirmedYN = 2;
        }

        if (!empty($data)) {
            $where = array('documentID' => $documentCode, 'documentSystemCode' => $system_code);
            $this->ci->db->where($where)->delete('srp_erp_documentapproved');

            if (!empty($data['table_unique_field_name']) && !empty($data['table_name'])) {
                $dataUpdate = array(
                    'confirmedYN' => $confirmedYN,
                    'confirmedByEmpID' => '',
                    'confirmedDate' => '',
                    'currentLevelNo' => 1
                );

                if(!in_array($documentCode, ['VD'])){
                    $dataUpdate['confirmedByName'] = '';
                }

                $this->ci->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
                $this->ci->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

                if($documentCode == 'FS'){ /*If final settlement*/
                    $empID = $this->ci->db->get_where('srp_erp_pay_finalsettlementmaster', ['masterID'=>$system_code])->row('empID');
                    $upData = [ 'finalSettlementDoneYN'=>0, 'ModifiedPC' => current_pc(), 'ModifiedUserName' => current_employee(), 'Timestamp' => current_date() ];
                    $this->ci->db->where(['EIdNo'=>$empID])->update('srp_employeesdetails', $upData);
                }

                $this->ci->db->trans_commit();
                if ($this->ci->db->trans_status() === FALSE) {
                    $this->ci->db->trans_rollback();
                    $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
                    return 'e';
                } else {
                    $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Referred Back Successfully.');
                    return 1;
                }
            } else {
                $this->ci->db->trans_commit();
                if ($this->ci->db->trans_status() === FALSE) {
                    $this->ci->db->trans_rollback();
                    $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
                    return 'e';
                } else {
                    $this->ci->session->set_flashdata('s', $data['documentCode'] . ' Approvals Deleted Successfully.');
                    return 3;
                }
            }
        } else {
            $this->ci->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
            return 'e1';
        }

    }

    public function emailAlert($documentID, $levelNo, $documentSystemCode, $documentCode)
    {
        $email = array();
        $companyID = current_companyID();
        /*get approval user email address*/
       if($documentID == 'PRQ')
       {
            $qry = "SELECT  IF ( ap.employeeID =- 1, reporting.EIdNo, app_emp.EIdNo ) AS EIdNo, IF ( ap.employeeID =- 1, reporting.Ename2, `app_emp`.`Ename2` ) AS Ename2, IF ( ap.employeeID =- 1, reporting.EEmail, `app_emp`.`EEmail` ) AS EEmail, ap.companyID, \"Purchase Request\" as document FROM `srp_erp_documentapproved` JOIN `srp_erp_approvalusers` `ap` ON `ap`.`levelNo` = `srp_erp_documentapproved`.`approvalLevelID` LEFT JOIN `srp_employeesdetails` `app_emp` ON `app_emp`.`EIdNo` = `ap`.`employeeID` LEFT JOIN srp_erp_purchaserequestmaster prmaster ON prmaster.purchaseRequestID = srp_erp_documentapproved.documentSystemCode LEFT JOIN ( SELECT employee.EIdNo, empID FROM srp_erp_employeemanagers managerTb JOIN srp_employeesdetails employee ON managerTb.managerID = employee.EIdNo WHERE managerTb.active = 1 ) employeemanager ON employeemanager.empID = prmaster.requestedEmpID LEFT JOIN srp_employeesdetails reporting ON reporting.EIdNo = employeemanager.EIdNo WHERE `srp_erp_documentapproved`.`documentID` = '{$documentID}' AND `ap`.`documentID` = '{$documentID}' AND ap.levelNo = {$levelNo} AND `documentSystemCode` = '{$documentSystemCode}' AND `srp_erp_documentapproved`.`companyID` = '{$companyID}' AND `ap`.`companyID` = '{$companyID}' AND Status = '1'";

       }
       elseif ($documentID == 'SAR'){
           $qry = "SELECT IF ( ap.employeeID =- 1, reporting.EIdNo, app_emp.EIdNo ) AS EIdNo, IF ( ap.employeeID =- 1, reporting.Ename2, app_emp.Ename2 ) AS Ename2,
                   IF ( ap.employeeID =- 1, reporting.EEmail, app_emp.EEmail ) AS EEmail, ap.companyID, 'Salary Advance Request' AS document
                   FROM srp_erp_documentapproved AS docApp
                   JOIN srp_erp_approvalusers AS ap ON ap.levelNo = docApp.approvalLevelID
                   LEFT JOIN srp_employeesdetails AS app_emp ON app_emp.EIdNo = ap.employeeID
                   LEFT JOIN srp_erp_pay_salaryadvancerequest AS advReq ON advReq.masterID = docApp.documentSystemCode
                   LEFT JOIN (
                        SELECT employee.EIdNo, empID FROM srp_erp_employeemanagers managerTb
                        JOIN srp_employeesdetails employee ON managerTb.managerID = employee.EIdNo
                        WHERE managerTb.active = 1
                   ) employeemanager ON employeemanager.empID = advReq.empID
                   LEFT JOIN srp_employeesdetails reporting ON reporting.EIdNo = employeemanager.EIdNo
                   WHERE docApp.documentID = '{$documentID}' AND ap.documentID = '{$documentID}' AND ap.levelNo = {$levelNo}
                   AND documentSystemCode = '{$documentSystemCode}' AND docApp.companyID = '{$companyID}' AND ap.companyID = '{$companyID}' AND ap.`Status` = '1'";
       }
       else
       {
           $qry = "SELECT srp_erp_approvalusers.documentID, EIdNo, Ename2, EEmail, srp_erp_approvalusers.companyID, srp_erp_documentcodemaster.document FROM srp_erp_approvalusers INNER JOIN srp_employeesdetails ON EIdNo = employeeID AND Erp_companyID = {$companyID} LEFT JOIN srp_erp_documentcodemaster ON srp_erp_approvalusers.documentID = srp_erp_documentcodemaster.documentID AND srp_erp_documentcodemaster.companyID = {$companyID} WHERE srp_erp_approvalusers.documentID = '{$documentID}' AND levelNo = {$levelNo} AND srp_erp_approvalusers.companyID = {$companyID} AND Status = '1'";
       }


        $result = $this->ci->db->query($qry)->result_array();
        $params = array();
        if (!empty($result)) {
            $x = 0;
            foreach ($result as $value) {
                $x++;
                $params[$x]["companyID"] = $companyID;
                $params[$x]["documentID"] = $documentID;
                $params[$x]["documentSystemCode"] = $documentSystemCode;
                $params[$x]["documentCode"] = $documentCode;
                $params[$x]["emailSubject"] = $value['document'] . ' Approval' . " - Level" . $levelNo;
                $params[$x]["empEmail"] = $value['EEmail'];
                $params[$x]["empID"] = $value['EIdNo'];
                $params[$x]["empName"] = $value['Ename2'];
                $params[$x]["emailBody"] = "{$value['document']} - {$documentCode} is pending for your approval.";

                /*$data = array(
                    'NotificationDate' => $this->ci->common_data['current_date'],
                    'SentFromID' => $this->ci->common_data['current_userID'],
                    'ReceivedByID' => $value['EIdNo'],
                    'NotificationSubject' => $value['document'] . ' Approval'." - Level".$levelNo,
                    'NotificationDescription' => "{$value['document']} - {$documentCode} is pending for your approval.",
                    'CreatedDate' => $this->ci->common_data['current_date'],
                    'CreatedPC' => $this->ci->common_data['current_pc'],
                );
                $this->ci->db->insert('srp_notifications', $data);*/

                $param["empName"] = $value['Ename2'];
                $param["body"] = $params[$x]["emailBody"];
                $mailData = [
                    'approvalEmpID' => $params[$x]["empID"],
                    'documentCode' => $documentCode,
                    'toEmail' => $params[$x]["empEmail"],
                    'subject' => $params[$x]["emailSubject"],
                    'param' => $param
                ];

                array_push($email, $mailData);
                /** firebase notification for approval */
                $token_android = firebaseToken($value['EIdNo'], 'android', $companyID);
                $token_ios = firebaseToken($value['EIdNo'], 'apple', $companyID);

                $firebaseHeader = "Pending for your approval.";
                $firebaseBody = "{$value['document']} - {$documentCode} is pending for your approval.";

                $this->ci->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_android, 8, $documentCode, $documentID, $result['EIdNo'], "android");
                }
                if(!empty($token_ios)) {
                    $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_ios, 8, $documentCode, $documentID, $result['EIdNo'], "apple");
                }
                /** End of firebase notification for approval */


//                send_approvalEmail($mailData);
            }
        }
        if (!empty($params)) {
            //$this->ci->email_manual->set_email_detail($params);
        }
        return $email;
//        return true;
    }

    public function emailAlert_other_approvers($documentID, $levelNo, $documentSystemCode, $documentCode)
    {
        $email = array();
        $companyID = current_companyID();
        $approvedEmp = current_userID();
        /*get approval user email address*/
       if($documentID == 'PRQ')
       {
            $qry = "SELECT  IF ( ap.employeeID =- 1, reporting.EIdNo, app_emp.EIdNo ) AS EIdNo, IF ( ap.employeeID =- 1, reporting.Ename2, `app_emp`.`Ename2` ) AS Ename2, IF ( ap.employeeID =- 1, reporting.EEmail, `app_emp`.`EEmail` ) AS EEmail, ap.companyID, \"Purchase Request\" as document FROM `srp_erp_documentapproved` JOIN `srp_erp_approvalusers` `ap` ON `ap`.`levelNo` = `srp_erp_documentapproved`.`approvalLevelID` LEFT JOIN `srp_employeesdetails` `app_emp` ON `app_emp`.`EIdNo` = `ap`.`employeeID` LEFT JOIN srp_erp_purchaserequestmaster prmaster ON prmaster.purchaseRequestID = srp_erp_documentapproved.documentSystemCode LEFT JOIN ( SELECT employee.EIdNo, empID FROM srp_erp_employeemanagers managerTb JOIN srp_employeesdetails employee ON managerTb.managerID = employee.EIdNo WHERE managerTb.active = 1 ) employeemanager ON employeemanager.empID = prmaster.requestedEmpID LEFT JOIN srp_employeesdetails reporting ON reporting.EIdNo = employeemanager.EIdNo WHERE `srp_erp_documentapproved`.`documentID` = '{$documentID}' AND `ap`.`documentID` = '{$documentID}' AND ap.levelNo = {$levelNo} AND `documentSystemCode` = '{$documentSystemCode}' AND `srp_erp_documentapproved`.`companyID` = '{$companyID}' AND `ap`.`companyID` = '{$companyID}' AND Status = '1' 	AND (CASE WHEN employeeID = '-1' THEN reporting.EIdNo <> {$approvedEmp} ELSE app_emp.EIdNo <> {$approvedEmp} END)";

       }
       elseif ($documentID == 'SAR'){
           $qry = "SELECT IF ( ap.employeeID =- 1, reporting.EIdNo, app_emp.EIdNo ) AS EIdNo, IF ( ap.employeeID =- 1, reporting.Ename2, app_emp.Ename2 ) AS Ename2,
                   IF ( ap.employeeID =- 1, reporting.EEmail, app_emp.EEmail ) AS EEmail, ap.companyID, 'Salary Advance Request' AS document
                   FROM srp_erp_documentapproved AS docApp
                   JOIN srp_erp_approvalusers AS ap ON ap.levelNo = docApp.approvalLevelID
                   LEFT JOIN srp_employeesdetails AS app_emp ON app_emp.EIdNo = ap.employeeID
                   LEFT JOIN srp_erp_pay_salaryadvancerequest AS advReq ON advReq.masterID = docApp.documentSystemCode
                   LEFT JOIN (
                        SELECT employee.EIdNo, empID FROM srp_erp_employeemanagers managerTb
                        JOIN srp_employeesdetails employee ON managerTb.managerID = employee.EIdNo
                        WHERE managerTb.active = 1
                   ) employeemanager ON employeemanager.empID = advReq.empID
                   LEFT JOIN srp_employeesdetails reporting ON reporting.EIdNo = employeemanager.EIdNo
                   WHERE docApp.documentID = '{$documentID}' AND ap.documentID = '{$documentID}' AND ap.levelNo = {$levelNo}
                   AND documentSystemCode = '{$documentSystemCode}' AND docApp.companyID = '{$companyID}' AND ap.companyID = '{$companyID}' AND ap.`Status` = '1'
                   AND (CASE WHEN employeeID = '-1' THEN reporting.EIdNo <> {$approvedEmp} ELSE app_emp.EIdNo <> {$approvedEmp}  END)";
       }
       else
       {
           $qry = "SELECT srp_erp_approvalusers.documentID, EIdNo, Ename2, EEmail, srp_erp_approvalusers.companyID, srp_erp_documentcodemaster.document FROM srp_erp_approvalusers INNER JOIN srp_employeesdetails ON EIdNo = employeeID AND Erp_companyID = {$companyID} LEFT JOIN srp_erp_documentcodemaster ON srp_erp_approvalusers.documentID = srp_erp_documentcodemaster.documentID AND srp_erp_documentcodemaster.companyID = {$companyID} WHERE srp_erp_approvalusers.documentID = '{$documentID}' AND levelNo = {$levelNo} AND srp_erp_approvalusers.companyID = {$companyID} AND Status = '1' AND employeeID <> {$approvedEmp}";
       }

        $result = $this->ci->db->query($qry)->result_array();
        $params = array();
        if (!empty($result)) {
            $x = 0;
            foreach ($result as $value) {
                $x++;
                $params[$x]["companyID"] = $companyID;
                $params[$x]["documentID"] = $documentID;
                $params[$x]["documentSystemCode"] = $documentSystemCode;
                $params[$x]["documentCode"] = $documentCode;
                $params[$x]["emailSubject"] = $value['document'] . ' Approval' . " - Level" . $levelNo;
                $params[$x]["empEmail"] = $value['EEmail'];
                $params[$x]["empID"] = $value['EIdNo'];
                $params[$x]["empName"] = $value['Ename2'];
                $params[$x]["emailBody"] = $value['document'] . ' - ' . $documentCode. ' level ' . $levelNo . ' is successfully approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Approved By </td><td> : ' . current_user() . '</td></tr>
                                      <tr><td><strong>Approved Date </td><td> : ' . current_date() . '</td></tr>
                                  </table>';
                $param["empName"] = $value['Ename2'];
                $param["body"] = $params[$x]["emailBody"];
                $mailData = [
                    'approvalEmpID' => $params[$x]["empID"],
                    'documentCode' => $documentCode,
                    'toEmail' => $params[$x]["empEmail"],
                    'subject' => $params[$x]["emailSubject"],
                    'param' => $param
                ];

                array_push($email, $mailData);
                /** firebase notification for approval */
                $token_android = firebaseToken($value['EIdNo'], 'android', $companyID);
                $token_ios = firebaseToken($value['EIdNo'], 'apple', $companyID);

                $firebaseHeader = $value['document'] . ' approved.';
                $firebaseBody = $value['document'] . ' - ' . $documentCode. ' level ' . $levelNo . ' is successfully approved.';

                $this->ci->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_android, 7, $documentCode, $documentID, $value['EIdNo'], "android");
                }
                if(!empty($token_ios)) {
                    $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_ios, 7, $documentCode, $documentID, $value['EIdNo'], "apple");
                }
                /** End of firebase notification for approval */

//                send_approvalEmail($mailData);
            }
        }
        if (!empty($params)) {
            //$this->ci->email_manual->set_email_detail($params);
        }
        return $email;
//        return true;
    }

    function emailfinalAlert($table_name, $table_unique_field_name, $system_code, $documentCode, $documentID)
    {
        $companyID = current_companyID();
        if ($table_name == 'srp_erp_ngo_donorcollectionmaster') {
            $documentID = 'documentCode';
        } else {
            $documentID = 'documentID';
        }
        $qry = "SELECT EIdNo,Ename2,EEmail,document FROM {$table_name} as master INNER JOIN `srp_employeesdetails` ON EIdNo = createdUserID AND Erp_companyID = {$companyID} LEFT JOIN `srp_erp_documentcodemaster` ON master.$documentID = srp_erp_documentcodemaster.documentID AND srp_erp_documentcodemaster.companyID= {$companyID}  WHERE master.companyID =  {$companyID}  AND {$table_unique_field_name} = {$system_code}";
        $result = $this->ci->db->query($qry)->row_array();
        $params = array();
        if (!empty($result)) {
            $x = 0;
            $params[$x]["companyID"] = $companyID;
            $params[$x]["documentID"] = $documentID;
            $params[$x]["documentSystemCode"] = $system_code;
            $params[$x]["documentCode"] = $documentCode;
            $params[$x]["emailSubject"] = $documentCode . ' is Approved';
            $params[$x]["empEmail"] = $result['EEmail'];
            $params[$x]["empID"] = $result['EIdNo'];
            $params[$x]["empName"] = $result['Ename2'];
            $params[$x]["emailBody"] = "{$result['document']} - {$documentCode} is fully approved.";
        }
        if (!empty($params)) {
            //$this->ci->email_manual->set_email_detail($params);
        }

        /*$data = array(
            'NotificationDate' => $this->ci->common_data['current_date'],
            'SentFromID' => $this->ci->common_data['current_userID'],
            'ReceivedByID' => $result['EIdNo'],
            'NotificationSubject' => $documentCode . ' is Approved',
            'NotificationDescription' => "{$result['document']} - {$documentCode} is fully approved.",
            'CreatedDate' => $this->ci->common_data['current_date'],
            'CreatedPC' => $this->ci->common_data['current_pc'],
        );
        $this->ci->db->insert('srp_notifications', $data);*/


        $param["empName"] = $result['Ename2'];
        $param["body"] = "{$result['document']} - {$documentCode} is fully approved.";
        $mailData = [
            'approvalEmpID' => $result['EIdNo'],
            'documentCode' => $documentCode,
            'toEmail' => $result['EEmail'],
            'subject' => $documentCode . ' is Approved',
            'param' => $param
        ];

        /** firebase notification for approval */
        $token_android = firebaseToken($result['EIdNo'], 'android', $companyID);
        $token_ios = firebaseToken($result['EIdNo'], 'apple', $companyID);

        $firebaseHeader = "{$result['document']} fully approved.";
        $firebaseBody = "{$result['document']} - {$documentCode} is fully approved.";

        $this->ci->load->library('firebase_notification');
        if(!empty($token_android)) {
            $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_android, 7, $documentCode, $documentID, $result['EIdNo'], "android");
        }
        if(!empty($token_ios)) {
            $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_ios, 7, $documentCode, $documentID, $result['EIdNo'], "apple");
        }
        /** End of firebase notification for approval */

//        send_approvalEmail($mailData);
        return $mailData;
//        return true;
    }


    function emailRejectAlert($table_name, $table_unique_field_name, $system_code, $documentCode, $documentID)
    {
        $companyID = current_companyID();
        if ($table_name == 'srp_erp_ngo_donorcollectionmaster') {
            $documentID = 'documentCode';
        } else {
            $documentID = 'documentID';
        }
        $qry = "SELECT EIdNo,Ename2,EEmail,document FROM {$table_name} as master INNER JOIN `srp_employeesdetails` ON EIdNo = createdUserID AND Erp_companyID = {$companyID} LEFT JOIN `srp_erp_documentcodemaster` ON master.$documentID = srp_erp_documentcodemaster.documentID AND srp_erp_documentcodemaster.companyID= {$companyID}  WHERE master.companyID =  {$companyID}  AND {$table_unique_field_name} = {$system_code}";
        $result = $this->ci->db->query($qry)->row_array();
        $params = array();
        if (!empty($result)) {
            $x = 0;
            $params[$x]["companyID"] = $companyID;
            $params[$x]["documentID"] = $documentID;
            $params[$x]["documentSystemCode"] = $system_code;
            $params[$x]["documentCode"] = $documentCode;
            $params[$x]["emailSubject"] = $documentCode . ' is Rejected';
            $params[$x]["empEmail"] = $result['EEmail'];
            $params[$x]["empID"] = $result['EIdNo'];
            $params[$x]["empName"] = $result['Ename2'];
            $params[$x]["emailBody"] = "{$result['document']} - {$documentCode} is Rejected.";
        }
        if (!empty($params)) {
            //$this->ci->email_manual->set_email_detail($params);
        }

        /*$data = array(
            'NotificationDate' => $this->ci->common_data['current_date'],
            'SentFromID' => $this->ci->common_data['current_userID'],
            'ReceivedByID' => $result['EIdNo'],
            'NotificationSubject' => $documentCode . ' is Rejected',
            'NotificationDescription' => "{$result['document']} - {$documentCode} is Rejected.",
            'CreatedDate' => $this->ci->common_data['current_date'],
            'CreatedPC' => $this->ci->common_data['current_pc'],
        );
        $this->ci->db->insert('srp_notifications', $data);*/


        $param["empName"] = $result['Ename2'];
        $param["body"] = "{$result['document']} - {$documentCode} is Rejected.";
        $mailData = [
            'approvalEmpID' => $result['EIdNo'],
            'documentCode' => $documentCode,
            'toEmail' => $result['EEmail'],
            'subject' => $documentCode . ' is Rejected',
            'param' => $param
        ];

        /** firebase notification for approval */
        $token_android = firebaseToken($result['EIdNo'], 'android', $companyID);
        $token_ios = firebaseToken($result['EIdNo'], 'apple', $companyID);

        $firebaseHeader = "{$result['document']} is Rejected.";
        $firebaseBody = "{$result['document']} - {$documentCode} is Rejected.";

        $this->ci->load->library('firebase_notification');
        if(!empty($token_android)) {
            $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_android, 9, $documentCode, $documentID, $result['EIdNo'], "android");
        }
        if(!empty($token_ios)) {
            $this->ci->firebase_notification->sendFirebasePushNotification($firebaseHeader, $firebaseBody, $token_ios, 9, $documentCode, $documentID, $result['EIdNo'], "apple");
        }
        /** End of firebase notification for approval */

//        send_approvalEmail($mailData);
        return $mailData;
        return true;
    }

    public function AutoApprovalProject($document, $documentID, $documentCode, $documentName, $table_name = '', $table_unique_field_name = '', $autoApprove = 0)//( For Project Auto Approval & Confirmation)
    {

        $maxlevel = $this->maxlevel($document);

        /*$this->ci->db->select('levelNo, employeeID');
        $this->ci->db->where('Status', 1);
        $this->ci->db->where('companyID', $this->ci->common_data['company_data']['company_id']);
        $this->ci->db->where('documentID', $document);
        $this->ci->db->from('srp_erp_approvalusers');
        $this->ci->db->order_by('levelNo');
        $approvalusers = $this->ci->db->get()->result_array();*/
        if (!empty($maxlevel["levelNo"]) && $autoApprove == 1) {
            $data_app = array();
            for ($i = 1; $i <= $maxlevel["levelNo"]; $i++) {
                $data_app[$i]['companyID'] = current_companyID();
                $data_app[$i]['companyCode'] = current_companyCode();
                $data_app[$i]['departmentID'] = $document;
                $data_app[$i]['documentID'] = $document;
                $data_app[$i]['documentSystemCode'] = $documentID;
                $data_app[$i]['documentCode'] = $documentCode;
                $data_app[$i]['table_name'] = $table_name;
                $data_app[$i]['table_unique_field_name'] = $table_unique_field_name;
                $data_app[$i]['documentDate'] = current_date(false);
                $data_app[$i]['approvalLevelID'] = $i;
                $data_app[$i]['roleID'] = null;
                $data_app[$i]['approvalGroupID'] = current_user_group();
                $data_app[$i]['roleLevelOrder'] = null;
                $data_app[$i]['docConfirmedDate'] = current_date(false);
                $data_app[$i]['docConfirmedByEmpID'] = current_userID();
                $data_app[$i]['approvedEmpID'] = current_userID();
                $data_app[$i]['approvedYN'] = 1;
                $data_app[$i]['approvedDate'] = current_date(false);
                $data_app[$i]['approvedPC'] = current_pc();
            }

            $this->ci->db->insert_batch('srp_erp_documentapproved', $data_app);

            if (trim($table_name) != '' && trim($table_unique_field_name) != '') {
                $data = array(
                    'confirmedYN' => '1',
                    'confirmedDate' => current_date(false),
                    'confirmedByEmpID' => current_userID(),
                    'confirmedByName' => current_user(),
                    'approvedYN' => '1',
                    'approvedDate' => current_date(false),
                    'approvedbyEmpID' => current_userID(),
                    'approvedbyEmpName' => current_user(),
                    'isConvertedToProject' => '1',
                    'convertedDate' => current_date(false),
                    'convertedByEmpID' => current_userID(),
                );

                $this->ci->db->where($table_unique_field_name, $documentID);
                $this->ci->db->update($table_name, $data);

                /* write my alert table*/
                /*$policy = getPolicyValues('SEN', 'All');
                if($policy==1 || $policy==null) {
                    $this->emailAlert($document, 1, $documentID, $documentCode);
                }*/
                /**/
                $this->ci->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
            }

            $this->ci->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
            return 1;
        }
    }

    function auto_approve($autoID, $tableName, $uniqueField, $documentID, $documentCode, $docDate, $companyData=null)
    {
        $data = array(
            'confirmedYN' => '1',
            'confirmedDate' => current_date(),
            'confirmedByEmpID' => current_userID(),
            'approvedYN' => '1',
            'approvedDate' => current_date(),
            'approvedbyEmpID' => current_userID(),
        );

        if(!in_array($documentID, ['VD'])){
            $data['confirmedByName'] = current_user();
            $data['approvedbyEmpName'] = current_user();
        }

        $this->ci->db->where($uniqueField, $autoID);
        $tableUpdate = $this->ci->db->update($tableName, $data);

        if($tableUpdate){
            $data_app['companyID'] = current_companyID();
            $data_app['companyCode'] = current_companyCode();
            $data_app['departmentID'] = $documentID;
            $data_app['documentID'] = $documentID;
            $data_app['documentSystemCode'] = $autoID ;
            $data_app['documentCode'] = $documentCode;
            $data_app['table_name'] = $tableName;
            $data_app['table_unique_field_name'] = $uniqueField;
            $data_app['documentDate'] = $docDate;
            $data_app['approvalLevelID'] = 1;
            $data_app['roleID'] = null;

            $data_app['approvalGroupID'] = current_user_group();
            $data_app['roleLevelOrder'] = null;
            $data_app['docConfirmedDate'] = current_date();
            $data_app['docConfirmedByEmpID'] = current_userID();
            $data_app['approvedEmpID'] = current_userID();
            $data_app['approvedYN'] = 1;
            $data_app['approvedDate'] = current_date();
            $data_app['approvedPC'] = current_pc();

            $approved = $this->ci->db->insert('srp_erp_documentapproved', $data_app);
            if($approved){
                return 1;
            }else{
                return 3;
            }
        }
    }


}