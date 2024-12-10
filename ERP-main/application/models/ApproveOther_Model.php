<?php

class ApproveOther_Model extends ERP_Model
{

    public function __construct()
    {
        $this->load->library('jwt');
        $this->load->helpers('exceedmatch');
        $this->encryption->initialize(array('driver' => 'mcrypt'));

        if (isset($_SERVER['HTTP_TOKEN_KEY']) && $_SERVER['HTTP_TOKEN_KEY'] != 'null' && $_SERVER['HTTP_TOKEN_KEY'] != 'undefined') {
            $tokenKey = $_SERVER['HTTP_TOKEN_KEY'];
            $output['id_token'] = $this->jwt->decode($tokenKey, "id_token");
            $companyID = $output['id_token']->Erp_companyID;
            $ip = $output['id_token']->ip;
            $db = $output['id_token']->db;

            $config['hostname'] = $ip;
            $config['username'] = dbData_user;
            $config['password'] = dbData_pass;
            $config['database'] = $db;
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = FALSE;
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);

            $result = $this->db->query('SELECT host,db_username,db_password,db_name from srp_erp_company where company_id ="' . $companyID . '"')->row_array();
            //var_dump($result);


//        var_dump( trim($this->encryption->decrypt($result['host'])));
            if (!empty($result)) {
//
                $config['hostname'] = trim($this->encryption->decrypt($result['host']));
                $config['username'] = trim($this->encryption->decrypt($result['db_username']));
                $config['password'] = trim($this->encryption->decrypt($result['db_password']));
                $config['database'] = trim($this->encryption->decrypt($result['db_name']));
                $config['dbdriver'] = 'mysqli';
                $config['db_debug'] = TRUE;
                $this->load->database($config, FALSE, TRUE);
            }
        }
    }

    function save_expense_Claim_approval($system_code, $level_id, $status, $comments, $documentCode, $companyID, $UserID, $empName,$token)
    {
        $this->load->library('firebase_notification');
        if ($status == 1) {
            $data = array(
                'approvedYN' => 1,
                'approvedDate' => date('Y-m-d H:i:s'),
                'approvedByEmpID' => $UserID,
                'approvedByEmpName' => $token->username,
                'approvalComments' => $comments,
            );
            $this->db->where('expenseClaimMasterAutoID', $system_code);
            $this->db->update('srp_erp_expenseclaimmaster', $data);

            $this->db->select('expenseClaimCode, claimedByEmpID');
            $this->db->where('expenseClaimMasterAutoID', $system_code);
            $this->db->from('srp_erp_expenseclaimmaster');
            $documentCode = $this->db->get()->row_array();

            /*** Firebase Mobile Notification*/
            $token_android = firebaseToken($documentCode['claimedByEmpID'], 'android', $companyID);
            $token_ios = firebaseToken($documentCode['claimedByEmpID'], 'apple', $companyID);

            $this->load->library('firebase_notification');
            if(!empty($token_android)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approved", "Your expense claim has been approved", $token_android, 4, $documentCode['expenseClaimCode'], "EC", $system_code, "android");
            }
            if(!empty($token_ios)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approved", "Your expense claim has been approved", $token_ios, 4, $documentCode['expenseClaimCode'], "EC", $system_code, "apple");
            }

            return array('status' => '1', 'data' => 'Approved Successfully', 'email' => null);
//            return "Approved Successfully";
        } else {
            $this->db->select('expenseClaimCode, claimedByEmpID');
            $this->db->where('expenseClaimMasterAutoID', $system_code);
            $this->db->from('srp_erp_expenseclaimmaster');
            $documentCode = $this->db->get()->row_array();

            if(!empty($documentCode)) {
                $datas = array(
                    'confirmedYN' => 2,
                    /*'confirmedDate' => null,
                    'confirmedByEmpID' => null,
                    'confirmedByName' => null,*/
                );
                $this->db->where('expenseClaimMasterAutoID', $system_code);
                $update = $this->db->update('srp_erp_expenseclaimmaster', $datas);

                if ($update) {
                    $data = array(
                        'documentID' => "EC",
                        'systemID' => $system_code,
                        'documentCode' => $documentCode['expenseClaimCode'],
                        'comment' => $comments,
                        'rejectedLevel' => 1,
                        'rejectByEmpID' => $UserID,
                        'rejectByEmpName' => $token->username,
                        'table_name' => "srp_erp_expenseclaimmaster",
                        'table_unique_field' => "expenseClaimMasterAutoID",
                        'companyID' => $token->Erp_companyID,
                        'companyCode' => $token->company_code,
                        'createdUserGroup' => $token->usergroupID,
                        'createdPCID' => $token->current_pc,
                        'createdUserID' => $UserID,
                        'createdUserName' => $token->username,
                        'createdDateTime' => date('Y-m-d H:i:s'),
                    );
                    $this->db->insert('srp_erp_approvalreject', $data);
//                return "Rejected Successfully";

                    /*** Firebase Mobile Notification*/
                    $token_android = firebaseToken($documentCode["claimedByEmpID"], 'android', $companyID);
                    $token_ios = firebaseToken($documentCode["claimedByEmpID"], 'apple', $companyID);

                    $this->load->library('firebase_notification');
                    if(!empty($token_android)) {
                        $this->firebase_notification->sendFirebasePushNotification("Expense Claim Referred Back", "Your expense claim has referred back", $token_android, 4, $documentCode['expenseClaimCode'], "EC", $system_code, "android");
                    }
                    if(!empty($token_ios)) {
                        $this->firebase_notification->sendFirebasePushNotification("Expense Claim Referred Back", "Your expense claim has referred back", $token_ios, 4, $documentCode['expenseClaimCode'], "EC", $system_code, "apple");
                    }

                    return array('status' => '1', 'data' => 'Rejected Successfully', 'email' => null);
                }
            }

        }
    }

    function update_GL_approvals($UserID,$empName,$companyID,$postdata,$token)
    {
        $this->load->model('Auth_mobileUsers_Model');
        $this->db->trans_start();
        $this->load->library('approvals');
        $this->load->library('Approvals_mobile');

        $companyID = current_companyID();

        $request = (object)$postdata;
        $documentCode = $request->docId;
//        $level_id = $request->level;
//        $status = $request->status;
//        $comments = $request->comment;
//        $system_code = $request->masterid;
        $table=$request->table;
        $field = $request->field;
        $appdate = date('Y-m-d H:i:s');

        $system_code = trim($this->input->post('masterid') ?? '');
        $level_id = trim($this->input->post('level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comment') ?? '');

        switch ($documentCode) {
            case "EC" :
                $approvals_status = $this->save_expense_Claim_approval($system_code, $level_id, $status, $comments, $documentCode, $companyID, $UserID, $empName,$token);
                break;

            case "LA" :
                $approvals_status = $this->save_leaveApproval($system_code, $level_id, $status, $comments, $companyID, $UserID,$token);
                break;

            case "LAC" :
                $approvals_status = $this->leave_cancellation_approval();
                break;

            case "BSI" :
//                var_dump('BST'); exit();
                $approvals_status = $this->save_supplier_invoice_approval(2);
                break;

            case "PV" :
                $approvals_status = $this->save_pv_approval(2);
                break;

            case "JV" :
                $approvals_status = $this->save_jv_approval(2);
                break;

            case "RV" :
                $currentdate = current_date(false);
                $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
                $mastertbl = $this->db->query("SELECT RVdate, RVchequeDate FROM `srp_erp_customerreceiptmaster` where companyID = $companyID And receiptVoucherAutoId = $system_code ")->row_array();
                $mastertbldetail = $this->db->query("SELECT receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail` WHERE companyID = $companyID AND type = 'Item' AND receiptVoucherAutoId = $system_code")->row_array();
                if ($PostDatedChequeManagement == 1 && ($mastertbl['RVchequeDate'] != '' || !empty($mastertbl['RVchequeDate'])) && (empty($mastertbldetail['receiptVoucherAutoId']) || $mastertbldetail['receiptVoucherAutoId']==' ') && $status == 1) {
                    if ($mastertbl['RVchequeDate'] > $mastertbl['RVdate'] && $currentdate < $mastertbl['RVchequeDate']) {
                        return array('status' => '0', 'data' => 'This is a post dated cheque document. you cannot approve this document before the cheque date.', 'email' => null);
                        exit();
                    }
                }
                $approvals_status = $this->save_rv_approval(2);
                break;

            case 'CINV' :
                $approvals_status = $this->save_invoice_approval(2);
                break;

            case 'PO' :
                $approvals_status = $this->save_purchase_order_approval(2);
                break;

            case 'PRQ' :
                $approvals_status = $this->save_purchase_request_approval(2);
                break;

            case 'GRV' :
                $approvals_status = $this->save_grv_approval(2);
                break;

            case "SLR" :
                $approvals_status = $this->save_sales_return_approval(2);
                break;

            case "SC" :
                $approvals_status = $this->save_sc_approval(2);
                break;

            case "DO" :
                $approvals_status = $this->approve_delivery_order(2);
                break;

            case "SO" : case "CNT" : case "QUT" :
                $approvals_status = $this->save_quotation_contract_approval(2);
                break;

            case "DN" :
                $approvals_status = $this->save_dn_approval(2);
                break;

            case "CN" :
                $approvals_status = $this->save_cn_approval(2);
                break;

            case "MI" :
                $approvals_status = $this->save_material_issue_approval(2);
                break;

            case "MR" :
                $approvals_status = $this->save_material_request_approval(2);
                break;

            case "MRN" :
                $approvals_status = $this->save_material_receipt_approval(2);
                break;

            case "SA" :
                $approvals_status = $this->save_stock_adjustment_approval(2);
                break;

            case "ST" :
                $approvals_status = $this->save_stock_transfer_approval(2);
                break;

            case "SR" :
                $approvals_status = $this->save_stock_return_approval(2);
                break;

            case "RJV" :
                $approvals_status = $this->save_rjv_approval(2);
                break;

            case "BRC" :
                $approvals_status = $this->save_bank_rec_approval(2);
                break;

            case "BT" :
                $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
                $mastertbl = $this->db->query("SELECT DATE_FORMAT(transferedDate,'%Y-%m-%d') as transferedDate, chequeDate FROM `srp_erp_banktransfer` WHERE companyID = $companyID And bankTransferAutoID = $system_code ")->row_array();
                $currentdate = current_date(false);
                if($PostDatedChequeManagement == 1 && ($mastertbl['chequeDate'] != '' || !empty($mastertbl['chequeDate'])) && $status == 1)
                {
                    if ($mastertbl['chequeDate'] > $mastertbl['transferedDate']) {
                        if ($currentdate < $mastertbl['chequeDate']) {
                            $approvals_status = array('status' => '0', 'data' => 'This is a post dated cheque document. you cannot approve this document before the cheque date.', 'email' => null);
                        }
                    }
                }

                $approvals_status = $this->confirm_bank_approval(2);
                break;

            default :
                $approvals_status = array('status' => '0', 'data' => 'Mobile Approval Setup Not Done', 'email' => null);
        }

        /*if ($approvals_status == 1) {
            $this->load->helper('receivable');
            $this->load->helper('payable_helper');
            $this->load->library('wac');
            $documentCode = '';
            switch ($documentCode) {
                case    'SPN'  :
                   $this->approve_Salaryprocess($UserID,$companyID,$system_code,$documentCode,$empName,'Y');
                    break;
                case    'SP'   :
                   $this->approve_Salaryprocess($UserID,$companyID,$system_code,$documentCode,$empName,'N');
                    break;
                default :
            }
        }*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return "Database error.";
        } else {
            $this->db->trans_commit();
            return $approvals_status;
        }
    }

    function save_leaveApproval($leaveMasterID, $level, $status, $comments, $companyID, $current_userID,$token)
    {
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        if ($status == 0) {
            /**** Document refer back process ****/
            $upData = [
                'currentLevelNo' => 0,
                'confirmedYN' => 2,
                'confirmedByEmpID' => null,
                'confirmedByName' => null,
                'confirmedDate' => null,
                'modifiedPCID' => $token->current_pc,
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $token->username,
                'modifiedDateTime' => date('Y-m-d H:i:s')
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
                'rejectByEmpID' => $current_userID,
                'table_name' => 'srp_erp_leavemaster',
                'table_unique_field' => 'leaveMasterID',
                'companyID' => $companyID,
                'companyCode' => $token->company_code,
                'createdPCID' => $token->current_pc,
                'createdUserID' => $current_userID,
                'createdUserName' => $token->username,
                'createdDateTime' =>  date('Y-m-d H:i:s')
            ];
            $this->db->insert('srp_erp_approvalreject', $rejectData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is refer backed';

                $mailData[] = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Refer backed',
                    'param' => $param,
                ];
//                send_approvalEmail($mailData);
//                return 'Leave application refer backed successfully';


                /*** Firebase Mobile Notification*/
                $token_android = firebaseToken($leave["empID"], 'android', $companyID);
                $token_ios = firebaseToken($leave["empID"], 'apple', $companyID);


                if($leave['startDate'] == $leave['endDate']) {
                    $firebaseBody = "Your leave on " . date('d M Y', strtotime($leave['startDate'])) . ' has been referred back';
                } else {
                    $firebaseBody = "Your leave from " . date('d M Y', strtotime($leave['startDate'])) . " to " . date('d M Y', strtotime($leave['endDate'])) . ' has been referred back';
                }

                $this->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->firebase_notification->sendFirebasePushNotification("Leave Referred back", $firebaseBody, $token_android, 3, $leave['documentCode'], "LA", $leaveMasterID, "android");
                }
                if(!empty($token_ios)) {
                    $this->firebase_notification->sendFirebasePushNotification("Leave Referred back", $firebaseBody, $token_ios, 3, $leave['documentCode'], "LA", $leaveMasterID, "apple");
                }

                return array('status' => '1', 'data' => 'Leave application refer backed successfully', 'email' => $mailData);
            } else {
                $this->db->trans_rollback();
//                return 'failed';
                return array('status' => '0', 'data' => 'failed');
            }
        }

        $setupData = getLeaveApprovalSetup('', $companyID);
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
                'modifiedPCID' => $token->current_pc,
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $token->username,
                'modifiedDateTime' => date('Y-m-d H:i:s')
            ];

            $this->db->trans_start();
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => $current_userID,
                'approvedComments' => $comments,
                'approvedDate' => date('Y-m-d'),
                'approvedPC' => $token->current_pc
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID'], $companyID);
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

                $mail_data = array();
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

                    $mail_data[] = $mailData;
//                    send_approvalEmail($mailData);
                }
                return array('status' => '1', 'data' => 'Level ' . $level . ' is Approved successfully', 'email' => $mail_data);
//                return 'Level ' . $level . ' is Approved successfully';
            } else {
                $this->db->trans_rollback();
//                return 'failed';
                return array('status' => '0', 'data' => 'failed');
            }
        } else {
            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 1,
                'approvedDate' => date('Y-m-d H:i:s'),
                'approvedbyEmpID' => $current_userID,
                'approvedbyEmpName' => $token->username,
                'approvalComments' => $comments
            );

            $this->db->trans_start();

            if ($leave["isSickLeave"] == 1) {
                $this->sickLeaveNoPay_calculation($leave, $companyID, $token);
            }

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $data);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => $current_userID,
                'approvedComments' => $comments,
                'approvedDate' => date('Y-m-d H:i:s'),
                'approvedPC' => $token->current_pc
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            /**** Confirm leave accrual pending*/
            $accrualData = [
                'confirmedYN' => 1,
                'confirmedby' => $current_userID,
                'confirmedDate' => date('Y-m-d H:i:s')
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('confirmedYN', 0);
            $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID'], $companyID);
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

                $mailData[] = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Approved',
                    'param' => $param,
                ];
//                send_approvalEmail($mailData);

                /*** Firebase Mobile Notification*/
                $token_android = firebaseToken($leave['empID'], 'android', $companyID);
                $token_ios = firebaseToken($leave['empID'], 'apple', $companyID);

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

                return array('status' => '1', 'data' => 'Approved successfully', 'email' => $mailData);
//                return 'Approved successfully';
            } else {
                return array('status' => '0', 'data' => 'failed');
            }
        }
    }

    function employeeLeaveSummery($empID = null, $leaveType = null, $policyMasterID = null, $companyID = null)
    {
        $companyID = current_companyID();
        if ($policyMasterID == 2) {
            /*Hourly leave type not developed fully*/
            return [
                'policyMasterID' => 2, 'entitled' => 0, 'leaveTaken' => 0, 'policyDescription' => 'Hourly',
                'isPaidLeave' => 1, 'description' => '', 'balance' => 0
            ];
            $qry3 = "SELECT t3.policyMasterID, IFNULL((SELECT SUM(hoursEntitled) 
                     FROM srp_erp_leaveaccrualdetail 
                     LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID
                     WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) AS entitled, IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster 
                     WHERE empID = {$empID} AND leaveTypeID = {$leaveType} AND approvedYN = 1), 0) AS leaveTaken, IFNULL((SELECT SUM(hoursEntitled) 
                     FROM srp_erp_leaveaccrualdetail 
                     LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                     WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) - IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster 
                     WHERE empID = {$empID} AND leaveTypeID = {$leaveType} AND approvedYN = 1), 0) AS balance, policyDescription,
                      IFNULL((SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail 
                      LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                      WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) AS accrued, isPaidLeave, t5.description 
                      FROM `srp_employeesdetails` t1 LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID 
                      LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t2.leaveGroupID = t3.leaveGroupID 
                      LEFT JOIN srp_erp_leavetype AS t4 ON t4.leaveTypeID = t3.leaveTypeID JOIN srp_erp_leavepolicymaster t5 ON t5.policyMasterID = t3.policyMasterID 
                      WHERE t3.leaveTypeID = {$leaveType} AND EIdNo = {$empID}";
        } else {
            if ($companyID == null) {
                $companyID = $companyID;
            }

            $currentYear = date('Y');
            $monthlyFirstDate = date('Y-m-01');
            $monthlyEndDate = date('Y-m-t');
            $yearFirstDate = date('Y-01-01');
            $yearEndDate = date('Y-12-31');


            $carryForwardLogic = "IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3), 
                                  IF( leavGroupDet.policyMasterID=1,  YEAR(accrualDate) = {$currentYear},
                                  accrualDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), accrualDate <= '{$yearEndDate}') ";

            $carryForwardLogic2 = "AND IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3),
                                   IF( leavGroupDet.policyMasterID=1,  endDate BETWEEN '{$yearFirstDate}' AND '{$yearEndDate}',
                                   endDate BETWEEN '{$monthlyFirstDate}' AND '{$monthlyEndDate}'), endDate <= '{$yearEndDate}') ";

            $qry3 = "SELECT *, (entitled - leaveTaken) AS balance FROM ( 
                         SELECT t3.policyMasterID,
                         IFNULL( (
                               SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                               JOIN (
                                    SELECT leaveaccrualMasterID, confirmedYN,
                                    CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                    FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                               ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
                               JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                               AND leavGroupDet.leaveTypeID = '{$leaveType}'
                               WHERE {$carryForwardLogic} AND detailTB.leaveType = '{$leaveType}' AND leavGroupDet.policyMasterID IN (1,3)
                               AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) AND detailTB.empID = {$empID} 
                           ), 0
                         ) AS entitled, 
                         IFNULL( (
                                SELECT SUM(days) FROM srp_erp_leavemaster 
                                JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                AND leavGroupDet.leaveTypeID = '{$leaveType}'
                                WHERE srp_erp_leavemaster.leaveTypeID = '{$leaveType}' AND
                                (cancelledYN = 0 OR cancelledYN IS NULL) AND leavGroupDet.policyMasterID IN (1,3) AND
                                srp_erp_leavemaster.empID = {$empID} AND approvedYN = 1 {$carryForwardLogic2}
                           ), 0
                         ) AS leaveTaken, policyDescription, isPaidLeave, t5.description 
                         FROM srp_employeesdetails t1 
                         LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID 
                         LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t1.leaveGroupID = t3.leaveGroupID 
                         LEFT JOIN srp_erp_leavepolicymaster t4 ON t4.policyMasterID = t3.policyMasterID 
                         LEFT JOIN srp_erp_leavetype AS t5 ON t5.leaveTypeID = t3.leaveTypeID WHERE t3.leaveTypeID = {$leaveType} AND EIdNo = {$empID} 
                     ) dataTB";

        }

        $leaveDet = $this->db->query($qry3)->row_array();
        return $leaveDet;
    }

    function sickLeaveNoPay_calculation($leave = [], $companyID, $token)
    {
        $leaveTypeID = $leave["leaveTypeID"];
        $empID = $leave["empID"];

        $result = $this->db->query("SELECT salaryCategoryID, formulaString, isNonPayroll FROM srp_erp_sickleavesetup
                                    WHERE companyID='{$companyID}' AND leaveTypeID={$leaveTypeID}")->result_array();

        if (!empty($result)) {
            $detail = [];
            foreach ($result as $key => $row) {

                $isNonPayroll = $row['isNonPayroll'];
                $table = ($isNonPayroll != 'Y') ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                $formula = trim($row['formulaString'] ?? '');
                $formulaBuilder = formulaBuilder_to_sql_simple_conversion($formula, $companyID);
                $formulaDecodeFormula = $formulaBuilder['formulaDecode'];
                $select_str = $formulaBuilder['select_str'];
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
                    $detail['noPayAmount'] = $_amount;
                    $detail['salaryCategoryID'] = $row['salaryCategoryID'];
                } else {
                    $detail['noPaynonPayrollAmount'] = $_amount;
                    $detail['nonPayrollSalaryCategoryID'] = $row['salaryCategoryID'];
                }
            }

            if ($detail['noPayAmount'] != 0 || ($detail['noPaynonPayrollAmount'] != 0)) {
                $detail['leaveMasterID'] = $leave['leaveMasterID'];
                $detail['empID'] = $empID;
                $detail['attendanceDate'] = date('Y-m-d', strtotime($leave['endDate']));
                $detail['companyID'] = $companyID;
                $detail['companyCode'] = $token->company_code;

                $this->db->insert('srp_erp_pay_empattendancereview', $detail);
            }
        }
    }

    function approve_Salaryprocess($UserID,$companyID,$system_code,$documentCode,$empName,$isNonPayroll,$appdate=null)
    {

        $payMasted_det = $this->getPayrollDetails($system_code, $isNonPayroll,$companyID);
        $narration = str_replace("'", '&#39', $payMasted_det['narration']);
        if($isNonPayroll != 'Y' ){
            $masterTableName = 'srp_erp_payrollmaster';
            $detailTableName = 'srp_erp_payrolldetail';
        }
        else {
            $masterTableName = 'srp_erp_non_payrollmaster';
            $detailTableName = 'srp_erp_non_payrolldetail';
        }
        $time = $appdate;
        /************** Debit entries *************/
        $amountStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.transactionAmount * -1), t1.transactionAmount )), t1.transactionCurrencyDecimalPlaces )';
        $amountLocalStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.companyLocalAmount * -1), t1.companyLocalAmount )), t1.companyLocalCurrencyDecimalPlaces )';
        $amountRepoStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.companyReportingAmount * -1), t1.companyReportingAmount )), t1.companyReportingCurrencyDecimalPlaces )';

        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                              createdDateTime, createdUserName)

                              SELECT '{$documentCode}', t1.payrollMasterID, t2.documentCode, LAST_DAY(CONCAT(payrollYear,'-',payrollMonth,'-01')), payrollYear, payrollMonth,
                              '{$narration}', GLCode, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, IF( {$amountStr} > 0, 'dr', 'cr' ),
                              {$amountStr}, t1.transactionCurrencyID, t1.transactionCurrency, t1.transactionCurrencyDecimalPlaces, t1.transactionER,
                              {$amountLocalStr}, t1.companyLocalCurrencyID, t1.companyLocalCurrency, t1.companyLocalCurrencyDecimalPlaces, t1.companyLocalER,
                              {$amountRepoStr}, t1.companyReportingCurrencyID, t1.companyReportingCurrency, t1.companyReportingCurrencyDecimalPlaces, t1.companyReportingER,
                              t2.confirmedByEmpID, t2.confirmedByName, t2.confirmedDate, t2.approvedDate, t2.approvedbyEmpID, t2.approvedbyEmpName, t1.segmentID,
                              t1.segmentCode, t2.companyID, t2.companyCode, '{''}', '{''}', {$UserID}, '{$time}', '{$empName}'
                              FROM {$detailTableName} t1
                              JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                              JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.GLCode AND t3.companyID = {$companyID}
                              WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$system_code} AND GLCode != 0 AND GLCode IS NOT NULL
                              GROUP BY GLCode, t1.transactionCurrency, t1.segmentID");

        /************* Credit entries ***************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                              createdDateTime, createdUserName)

                              SELECT '{$documentCode}', t1.payrollMasterID, t2.documentCode, LAST_DAY(CONCAT(payrollYear,'-',payrollMonth,'-01')), payrollYear, payrollMonth,
                              '{$narration}', liabilityGL, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, 'cr',
                              round( sum(t1.transactionAmount), t1.transactionCurrencyDecimalPlaces ), t1.transactionCurrencyID, t1.transactionCurrency,
                              t1.transactionCurrencyDecimalPlaces, t1.transactionER, round( sum(t1.companyLocalAmount), t1.companyLocalCurrencyDecimalPlaces ),
                              t1.companyLocalCurrencyID, t1.companyLocalCurrency, t1.companyLocalCurrencyDecimalPlaces, t1.companyLocalER,
                              round( sum(t1.companyReportingAmount), t1.companyReportingCurrencyDecimalPlaces ), t1.companyReportingCurrencyID,
                              t1.companyReportingCurrency, t1.companyReportingCurrencyDecimalPlaces, t1.companyReportingER, t2.confirmedByEmpID, t2.confirmedByName,
                              t2.confirmedDate, t2.approvedDate, t2.approvedbyEmpID, t2.approvedbyEmpName, t1.segmentID, t1.segmentCode, t2.companyID, t2.companyCode,
                              '{''}', '{''}', {$UserID}, '{$time}', '{$empName}'
                              FROM {$detailTableName} t1
                              JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                              JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.liabilityGL AND t3.companyID = {$companyID}
                              WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$system_code} AND liabilityGL != 0 AND liabilityGL IS NOT NULL
                              GROUP BY liabilityGL, t1.transactionCurrency, t1.segmentID");


        /************  Company Payroll Control Account ID [$payrollCA_data] *************/
        $payrollCA_data = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                                WHERE GLAutoID = (
                                                  SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'PCA' AND companyID = {$companyID}
                                                ) AND companyID={$companyID} ")->row_array();


        $GLAutoID = $payrollCA_data['GLAutoID'];
        $systemAccountCode = $payrollCA_data['systemAccountCode'];
        $GLSecondaryCode = $payrollCA_data['GLSecondaryCode'];
        $GLDescription = $payrollCA_data['GLDescription'];
        $subCategory = $payrollCA_data['subCategory'];

        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)

                              SELECT '{$documentCode}', documentMasterAutoID, masterTB.documentCode, documentDate, documentYear, documentMonth, '{$narration}', {$GLAutoID},
                              '{$systemAccountCode}', '{$GLSecondaryCode}', '{$GLDescription}', '{$subCategory}', IF( (SUM(trAmount) > 1), 'dr', 'cr' ),
                              round( SUM(trAmount), dPlace), transactionCurrencyID,
                              transactionCurrency, dPlace, 1, round( SUM(localAmount), localDPlace), companyLocalCurrencyID, companyLocalCurrency, localDPlace, localER,
                              round( SUM(repotingAmount), reportingDPlace), companyReportingCurrencyID, companyReportingCurrency, reportingDPlace, repotingER,
                              confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, companyID, companyCode, createdUserGroup,
                              createdPCID, createdUserID, createdDateTime, createdUserName
                              FROM
                              (
                                    SELECT documentMasterAutoID, documentCode, documentDate, documentYear, documentMonth, (transactionAmount*-1) AS trAmount,
                                    transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces AS dPlace,
                                    (companyLocalAmount *- 1) AS localAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate AS localER,
                                    companyLocalCurrencyDecimalPlaces AS localDPlace, (companyReportingAmount *- 1) AS repotingAmount, companyReportingCurrencyID,
                                    companyReportingCurrency, companyReportingExchangeRate AS repotingER, companyReportingCurrencyDecimalPlaces AS reportingDPlace
                                    FROM srp_erp_generalledger
                                    WHERE documentMasterAutoID={$system_code} AND documentCode ='{$documentCode}' AND companyID={$companyID}
                              ) AS calTable
                              JOIN {$masterTableName} AS masterTB ON masterTB.payrollMasterID=calTable.documentMasterAutoID
                              AND calTable.documentMasterAutoID={$system_code} AND calTable.documentCode ='{$documentCode}' AND masterTB.companyID={$companyID}
                              GROUP BY calTable.transactionCurrencyID ");

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return TRUE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function get_GLAutoID($companyID,$type='ERGL')
    {
        $this->db->select('GLAutoID');
        $this->db->from('srp_erp_companycontrolaccounts');
        $this->db->where('controlAccountType', $type);
        $this->db->where('companyID', $companyID);
        $res =  $this->db->get()->row_array();
        return $res["GLAutoID"];

    }

    function get_defaultSegment($companyID)
    {
        $this->db->select('default_segment');
        $this->db->from('srp_erp_company');
        $this->db->where('company_id', $companyID);
        $res =  $this->db->get()->row_array();
        return $res["default_segment"];

    }

    function calculateNewWAC_salesReturn($oldStock, $WACAmount, $qty, $cost, $decimal = 2)
    {
        $newStock = $oldStock + $qty;
        $newWACAmount = round(((($oldStock * $WACAmount) + ($cost * $qty)) / $newStock), $decimal);
        return $newWACAmount;
    }

    function getPayrollDetails($payrollID, $isNonPayroll=null, $companyID)
    {
        $tableName = ($isNonPayroll != 'Y')? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        //$convertFormat=convert_date_format_sql();
        $query = $this->db->select('payrollYear, payrollMonth, documentCode, narration,processDate AS processDate, confirmedYN,
            confirmedByName, approvedYN, approvedbyEmpName, isBankTransferProcessed, templateID,
            LAST_DAY(CONCAT(payrollYear,"-",payrollMonth,"-01")) AS payrollLastDate')
            ->from($tableName)->where('payrollMasterID', $payrollID)->where('companyID', $companyID)->get();
        return $query->row_array();
    }

    function fetch_gl_account_desc($id, $companyID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);
        $CI->db->where('companyID', $companyID);

        return $CI->db->get()->row_array();
    }

    function fetch_item_data($itemAutoID, $companyID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->WHERE('itemAutoID', $itemAutoID);
        $CI->db->where('companyID', $companyID);

        return $CI->db->get()->row_array();
    }

    function save_supplier_invoice_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->load->library('Approvals_mobile');
        $this->load->library('Sequence_mobile');
        if($autoappLevel==1) {
            $system_id = trim($this->input->post('InvoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else if ($autoappLevel == 2) {
            $system_id = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['InvoiceAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_id, $level_id, $status, $comments, 'BSI');
        }

        if ($approvals_status['status'] == 1) {
            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $item_detail = $this->db->get()->result_array();

            $this->db->select('sum(transactionAmount) as transactionAmount');
            $this->db->where('InvoiceAutoID', $system_id);
            $totalsum = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

            $disciunt=($master['generalDiscountPercentage']/100)*$totalsum['transactionAmount'];

            //echo 'totsum = '.$totalsum['transactionAmount'] .'<br>'.'discamount = '.$disciunt;
            if($master['documentOrigin']!='CINV'){
                for ($a = 0; $a < count($item_detail); $a++) {
                    if ($item_detail[$a]['type'] == 'Item' || $item_detail[$a]['type'] == 'PO') {
                        $item = fetch_item_data($item_detail[$a]['itemAutoID']);
                        $this->db->select('GLAutoID');
                        $this->db->where('controlAccountType', 'ACA');
                        $this->db->where('companyID', current_companyID());
                        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                        if($disciunt>0){
                            $amountaftrdisc=($item_detail[$a]['transactionAmount']/$totalsum['transactionAmount'])*$disciunt;
                        }else{
                            $amountaftrdisc=0;
                        }
                        $company_loc = (($item_detail[$a]['transactionAmount']-$amountaftrdisc) / $master['companyLocalExchangeRate']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] == 'Service') {
                            $itemAutoID = $item_detail[$a]['itemAutoID'];
                           
                            $itemledgerCurrentStock = fetch_itemledger_currentstock($item_detail[$a]['itemAutoID']);
                            $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'], 'companyLocalExchangeRate');
                            $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'],'companyReportingExchangeRate');
                          
                          
                           
                           
                            $qty = $item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID'];
                            $wareHouseAutoID = $item_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];
                           
                            $item_arr[$a]['currentStock'] = ($itemledgerCurrentStock + $qty);
                            $item_arr[$a]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + $company_loc) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + (($item_detail[$a]['transactionAmount']-$amountaftrdisc) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']),wacDecimalPlaces);
                          
                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($item_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }

                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['InvoiceAutoID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['bookingInvCode'];
                            $itemledger_arr[$a]['documentDate'] = $master['bookingDate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['RefNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $item_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $item_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $item_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $item_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $item_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $item_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['SUOMID'] = $item_detail[$a]['SUOMID'];
                            $itemledger_arr[$a]['SUOMQty'] = $item_detail[$a]['SUOMQty'];
                            $itemledger_arr[$a]['defaultUOMID'] = $item_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $item_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOM'] = $item_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $item_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $item_detail[$a]['requestedQty'];
                            $itemledger_arr[$a]['convertionRate'] = $item_detail[$a]['conversionRateUOMID'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $amountaftrdisc=$item_detail[$a]['transactionAmount']-($item_detail[$a]['transactionAmount']/$totalsum['transactionAmount'])*$disciunt;
                            if($disciunt>0){
                                $itemledger_arr[$a]['transactionAmount'] = $amountaftrdisc;
                            }else{
                                $itemledger_arr[$a]['transactionAmount'] = $item_detail[$a]['transactionAmount'];
                            }
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $item_arr[$a]['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['supplierCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['supplierCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                            $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                            $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                            $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                            $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                            $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];

                        } elseif ($item['mainCategory'] == 'Fixed Assets') {
                            $assat_data = array();
                            if($disciunt>0){
                                $assetdiscamnt=($item_detail[$a]['transactionAmount']/$totalsum['transactionAmount'])*$disciunt;
                            }else{
                                $assetdiscamnt=0;
                            }
                            $assat_amount = (($item_detail[$a]['transactionAmount']-$assetdiscamnt) / ($item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID']));
                            for ($b = 0; $b < ($item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID']); $b++) {
                                $assat_data[$b]['documentID'] = 'FA';
                                $assat_data[$b]['docOriginSystemCode'] = $master['InvoiceAutoID'];
                                $assat_data[$b]['docOriginDetailID'] = $item_detail[$a]['InvoiceDetailAutoID'];
                                $assat_data[$b]['docOrigin'] = 'BSI';
                                $assat_data[$b]['dateAQ'] = $master['bookingDate'];
                                $assat_data[$b]['grvAutoID'] = $master['InvoiceAutoID'];
                                $assat_data[$b]['isFromGRV'] = 1;
                                $assat_data[$b]['assetDescription'] = $item['itemDescription'];
                                $assat_data[$b]['comments'] = trim($comments);
                                $assat_data[$b]['faCatID'] = $item['subcategoryID'];
                                $assat_data[$b]['faSubCatID'] = $item['subSubCategoryID'];
                                $assat_data[$b]['assetType'] = 1;
                                $assat_data[$b]['transactionAmount'] = $assat_amount;
                                $assat_data[$b]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                $assat_data[$b]['transactionCurrency'] = $master['transactionCurrency'];
                                $assat_data[$b]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                                $assat_data[$b]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                $assat_data[$b]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                $assat_data[$b]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                $assat_data[$b]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $assat_data[$b]['companyLocalAmount'] = round($assat_amount, $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $assat_data[$b]['companyReportingAmount'] = round($assat_amount, $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                $assat_data[$b]['supplierID'] = $master['supplierID'];
                                $assat_data[$b]['segmentID'] = $master['segmentID'];
                                $assat_data[$b]['segmentCode'] = $master['segmentCode'];
                                $assat_data[$b]['companyID'] = $master['companyID'];
                                $assat_data[$b]['companyCode'] = $master['companyCode'];
                                $assat_data[$b]['createdUserGroup'] = $master['createdUserGroup'];
                                $assat_data[$b]['createdPCID'] = $master['createdPCID'];
                                $assat_data[$b]['createdUserID'] = $master['createdUserID'];
                                $assat_data[$b]['createdDateTime'] = $master['createdDateTime'];
                                $assat_data[$b]['createdUserName'] = $master['createdUserName'];
                                $assat_data[$b]['modifiedPCID'] = $master['modifiedPCID'];
                                $assat_data[$b]['modifiedUserID'] = $master['modifiedUserID'];
                                $assat_data[$b]['modifiedDateTime'] = $master['modifiedDateTime'];
                                $assat_data[$b]['modifiedUserName'] = $master['modifiedUserName'];
                                $assat_data[$b]['costGLAutoID'] = $item['faCostGLAutoID'];
                                $assat_data[$b]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                                $assat_data[$b]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                                $assat_data[$b]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                                $assat_data[$b]['isPostToGL'] = 1;
                                $assat_data[$b]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                                $assat_data[$b]['postGLCode'] = $ACA['systemAccountCode'];
                                $assat_data[$b]['postGLCodeDes'] = $ACA['GLDescription'];
                                $assat_data[$b]['faCode'] = $this->Sequence_mobile->sequence_generator("FA");
                            }
                            if (!empty($assat_data)) {
                                $assat_data = array_values($assat_data);
                                $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                            }
                        }
                    }
                }

                if (!empty($itemledger_arr)) {
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }
            }

            $this->load->model('Double_entry_model');
            if($master['documentOrigin']=='CINV'){
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_insurance_data($system_id, 'BSI');
            }else{
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($system_id, 'BSI');
            }

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['InvoiceAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['bookingInvCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['invoiceType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['bookingDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $maxLevel = $this->approvals_mobile->maxlevel('BSI');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $system_id;
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'PV'));

                }
            }

            $itemAutoIDarry = array();
            $wareHouseAutoIDDarry = array();
            foreach($item_detail as $value){
                if($value['itemAutoID']){
                    array_push($itemAutoIDarry,$value['itemAutoID']);
                }
                if($value['wareHouseAutoID']){
                    array_push($wareHouseAutoIDDarry,$value['wareHouseAutoID']);
                }
            }

            $company_id=current_companyID();
            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
JOIN (
    SELECT
        purchaseOrderID AS pid,
        (
            CASE
            WHEN balance = 0 THEN
                '2'
            WHEN balance = requestedtqy THEN
                '0'
            ELSE
                '1'
            END
        ) AS sts
    FROM
        (
            SELECT
    t2.purchaseOrderID,
  sum(requestedtqy) as requestedtqy ,
    sum(balance) AS balance
FROM
    (
SELECT
            po.purchaseOrderDetailsID,
            purchaseOrderID,
            po.itemAutoID,
            ifnull((po.requestedQty),0) AS requestedtqy,
            (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty,
        IF (
            (
                (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
            ) < 0,
            0,
            (
                (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
            )
        ) AS balance
        FROM
            srp_erp_purchaseorderdetails po
        LEFT JOIN (
            SELECT
                purchaseOrderMastertID,
                ifnull(sum(requestedQty),0) AS receivedQty,
                itemAutoID,
                purchaseOrderDetailsID
            FROM
                srp_erp_paysupplierinvoicedetail
        left join srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                where sinm.invoiceType='Standard' and sinm.approvedYN=1
            GROUP BY
              srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
        ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

				LEFT JOIN (
            SELECT
                purchaseOrderMastertID,
                ifnull(sum(receivedQty),0) AS receivedQty,
                itemAutoID,
                purchaseOrderDetailsID
            FROM
                srp_erp_grvdetails
        left join srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                where grvm.grvType='PO Base' and grvm.approvedYN=1
            GROUP BY
              srp_erp_grvdetails.purchaseOrderDetailsID
        ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID

    ) t2 group by t2.purchaseOrderID
        ) z
) tt ON prd.purchaseOrderID = tt.pid
SET prd.isReceived = tt.sts
where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");

            if($itemAutoIDarry && $wareHouseAutoIDDarry){
                $companyID=current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $wareHouseAutoIDDarry) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID=0;
                if(!empty($exceededitems_master)){
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['bookingDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['InvoiceAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['bookingInvCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = current_user_group();
                    $exceededmatch['createdPCID'] = current_pc();
                    $exceededmatch['createdUserID'] = current_userID();
                    $exceededmatch['createdUserName'] = current_user();
                    $exceededmatch['createdDateTime'] = current_date();
                    $exceededmatch['documentSystemCode'] = $this->Sequence_mobile->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID=$this->db->insert_id();
                }

                foreach($item_detail as $itemid){
                    if($itemid['type']=='Item'){
                        $receivedQty=$itemid['requestedQty'];
                        $receivedQtyConverted=$itemid['requestedQty']/$itemid['conversionRateUOMID'];
                        $companyID=current_companyID();
                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $itemid['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                        $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                        $sumqty=array_column($exceededitems,'balanceQty');
                        $sumqty=array_sum($sumqty);
                        if(!empty($exceededitems)){
                            foreach($exceededitems as $exceededItemAutoID){
                                if($receivedQtyConverted>0){
                                    $balanceQty=$exceededItemAutoID['balanceQty'];
                                    $updatedQty=$exceededItemAutoID['updatedQty'];
                                    $balanceQtyConverted=$exceededItemAutoID['balanceQty']/$exceededItemAutoID['conversionRateUOM'];
                                    $updatedQtyConverted=$exceededItemAutoID['updatedQty']/$exceededItemAutoID['conversionRateUOM'];
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;
                                        //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                        $exeed['updatedQty'] = ($updatedQtyConverted*$exceededItemAutoID['conversionRateUOM'])+($balanceQtyConverted*$exceededItemAutoID['conversionRateUOM']);
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetail['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                        $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetail['totalValue'] = $balanceQtyConverted*$exceededmatchdetail['itemCost'];
                                        $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                        $exceededmatchdetail['createdPCID'] = current_pc();
                                        $exceededmatchdetail['createdUserID'] = current_userID();
                                        $exceededmatchdetail['createdUserName'] = current_user();
                                        $exceededmatchdetail['createdDateTime'] = current_date();

                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                    } else {
                                        $exeed['balanceQty'] = $balanceQtyConverted-$receivedQtyConverted;
                                        $exeed['updatedQty'] = $updatedQtyConverted+$receivedQtyConverted;
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetails['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                        $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetails['totalValue'] = $receivedQtyConverted*$exceededmatchdetails['itemCost'];
                                        $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                        $exceededmatchdetails['createdPCID'] = current_pc();
                                        $exceededmatchdetails['createdUserID'] = current_userID();
                                        $exceededmatchdetails['createdUserName'] = current_user();
                                        $exceededmatchdetails['createdDateTime'] = current_date();
                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                        $receivedQty = $receivedQty - $exeed['updatedQty'];
                                        $receivedQtyConverted =$receivedQtyConverted- ($updatedQtyConverted+$receivedQtyConverted);
                                    }
                                }
                            }
                        }
                    }

                }
                if(!empty($exceededitems_master)){
                    exceed_double_entry($exceededMatchID);
                }
            }


            $this->db->select('sum(srp_erp_paysupplierinvoicedetail.transactionAmount) AS transactionAmount ,srp_erp_paysupplierinvoicedetail.companyLocalExchangeRate ,srp_erp_paysupplierinvoicedetail.companyReportingExchangeRate, srp_erp_paysupplierinvoicedetail.supplierCurrencyExchangeRate');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceAutoID', $system_id);
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $transactionAmount = $this->db->get()->row_array();

            $company_loc = ($transactionAmount['transactionAmount'] / $transactionAmount['companyLocalExchangeRate']);
            $company_rpt = ($transactionAmount['transactionAmount'] / $transactionAmount['companyReportingExchangeRate']);
            $supplier_cr = ($transactionAmount['transactionAmount'] / $transactionAmount['supplierCurrencyExchangeRate']);

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = current_userID();
            // $data['approvedbyEmpName']      = current_user();
            // $data['approvedDate']           = current_date();
            // $data['companyLocalAmount']     = $company_loc;
            // $data['companyReportingAmount'] = $company_rpt;
            // $data['supplierCurrencyAmount'] = $supplier_cr;
            // $data['transactionAmount']      = $transactionAmount['transactionAmount'];

            // $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            // $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            return array('status' => '1', 'data' => 'Supplier Invoices Approved Successfully', 'email' => $approvals_status['email']);
        }else if ($approvals_status['status'] == 2) {
            return array('status' => '1', 'data' => 'Supplier Invoices Partially Approved', 'email' => $approvals_status['email']);
        }
        else if ($approvals_status['status'] == 3) {
            return array('status' => '1', 'data' => 'Supplier Invoices Rejected Successfully', 'email' => $approvals_status['email']);
        }
    }

    function save_pv_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        $this->load->library('Sequence_mobile');
        $msgReturn = '';
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('payVoucherAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif ($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['payVoucherAutoId'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'PV');
        }

        if ($approvals_status['status'] == 1) {
            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvouchermaster');
            $master = $this->db->get()->row_array();
            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvoucherdetail');
            $payment_detail = $this->db->get()->result_array();
            for ($a = 0; $a < count($payment_detail); $a++) {
                if ($payment_detail[$a]['type'] == 'Item' || $payment_detail[$a]['type'] == 'PRQ') {
                    $item = fetch_item_data($payment_detail[$a]['itemAutoID']);

                    $this->db->select('GLAutoID');
                    $this->db->where('controlAccountType', 'ACA');
                    $this->db->where('companyID', current_companyID());
                    $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                    $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                    $company_loc = ($payment_detail[$a]['transactionAmount'] / $master['companyLocalExchangeRate']);
                    if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory'  or $item['mainCategory'] == 'Service') {
                        $itemAutoID = $payment_detail[$a]['itemAutoID'];
                        $qty = ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']);
                        $wareHouseAutoID = $payment_detail[$a]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                        $item_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                        $itemledgerCurrentStock = fetch_itemledger_currentstock($payment_detail[$a]['itemAutoID']);
                        $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($payment_detail[$a]['itemAutoID'], 'companyLocalExchangeRate');
                        $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($payment_detail[$a]['itemAutoID'],'companyReportingExchangeRate');
                      
                      
                        
                        $item_arr[$a]['currentStock'] = ($itemledgerCurrentStock + $qty);
                        $item_arr[$a]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + $company_loc) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                        $item_arr[$a]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + ($payment_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                        if (!empty($item_arr)) {
                            $this->db->where('itemAutoID', trim($payment_detail[$a]['itemAutoID']));
                            $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                        }
                        $itemledger_arr[$a]['documentID'] = $master['documentID'];
                        $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                        $itemledger_arr[$a]['documentAutoID'] = $master['payVoucherAutoId'];
                        $itemledger_arr[$a]['documentSystemCode'] = $master['PVcode'];
                        $itemledger_arr[$a]['documentDate'] = $master['PVdate'];
                        $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                        $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                        $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                        $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                        $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $itemledger_arr[$a]['wareHouseAutoID'] = $payment_detail[$a]['wareHouseAutoID'];
                        $itemledger_arr[$a]['wareHouseCode'] = $payment_detail[$a]['wareHouseCode'];
                        $itemledger_arr[$a]['wareHouseLocation'] = $payment_detail[$a]['wareHouseLocation'];
                        $itemledger_arr[$a]['wareHouseDescription'] = $payment_detail[$a]['wareHouseDescription'];
                        $itemledger_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                        $itemledger_arr[$a]['itemSystemCode'] = $payment_detail[$a]['itemSystemCode'];
                        $itemledger_arr[$a]['itemDescription'] = $payment_detail[$a]['itemDescription'];
                        $itemledger_arr[$a]['SUOMID'] = $payment_detail[$a]['SUOMID'];
                        $itemledger_arr[$a]['SUOMQty'] = $payment_detail[$a]['SUOMQty'];
                        $itemledger_arr[$a]['defaultUOMID'] = $payment_detail[$a]['defaultUOMID'];
                        $itemledger_arr[$a]['defaultUOM'] = $payment_detail[$a]['defaultUOM'];
                        $itemledger_arr[$a]['transactionUOM'] = $payment_detail[$a]['unitOfMeasure'];
                        $itemledger_arr[$a]['transactionUOMID'] = $payment_detail[$a]['unitOfMeasureID'];
                        $itemledger_arr[$a]['transactionQTY'] = $payment_detail[$a]['requestedQty'];
                        $itemledger_arr[$a]['convertionRate'] = $payment_detail[$a]['conversionRateUOM'];
                        $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                        $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                        $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                        $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                        $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                        $itemledger_arr[$a]['PLType'] = $item['costType'];
                        $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                        $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                        $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                        $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                        $itemledger_arr[$a]['BLType'] = $item['assteType'];
                        $itemledger_arr[$a]['transactionAmount'] = $payment_detail[$a]['transactionAmount'];
                        $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyLocalWacAmount'] = $item_arr[$a]['companyLocalWacAmount'];
                        $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                        $itemledger_arr[$a]['partyCurrencyID'] = $master['partyCurrencyID'];
                        $itemledger_arr[$a]['partyCurrency'] = $master['partyCurrency'];
                        $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['partyExchangeRate'];
                        $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                        $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                        $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                        $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                        $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                        $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                        $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                        $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                        $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                        $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                        $itemledger_arr[$a]['companyID'] = $master['companyID'];
                        $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                        $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                        $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                        $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                        $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                        $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                        $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                        $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                        $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                        $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];

                    } elseif ($item['mainCategory'] == 'Fixed Assets') {
                        $assat_data = array();
                        $assat_amount = ($payment_detail[$a]['transactionAmount'] / ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']));
                        for ($b = 0; $b < ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']); $b++) {
                            $assat_data[$b]['documentID'] = 'FA';
                            $assat_data[$b]['docOriginSystemCode'] = $master['payVoucherAutoId'];
                            $assat_data[$b]['docOriginDetailID'] = $payment_detail[$a]['payVoucherDetailAutoID'];
                            $assat_data[$b]['docOrigin'] = 'PV';
                            $assat_data[$b]['dateAQ'] = $master['PVdate'];
                            $assat_data[$b]['grvAutoID'] = $master['payVoucherAutoId'];
                            $assat_data[$b]['isFromGRV'] = 1;
                            $assat_data[$b]['assetDescription'] = $item['itemDescription'];
                            $assat_data[$b]['comments'] = trim($comments);
                            $assat_data[$b]['faCatID'] = $item['subcategoryID'];
                            $assat_data[$b]['faSubCatID'] = $item['subSubCategoryID'];
                            $assat_data[$b]['assetType'] = 1;
                            $assat_data[$b]['transactionAmount'] = $assat_amount;
                            $assat_data[$b]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $assat_data[$b]['transactionCurrency'] = $master['transactionCurrency'];
                            $assat_data[$b]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                            $assat_data[$b]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $assat_data[$b]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $assat_data[$b]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $assat_data[$b]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $assat_data[$b]['companyLocalAmount'] = round($assat_amount/$master['companyLocalExchangeRate'], $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $assat_data[$b]['companyReportingAmount'] = round($assat_amount/$master['companyReportingExchangeRate'], $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $assat_data[$b]['supplierID'] = $master['partyID'];
                            $assat_data[$b]['segmentID'] = $master['segmentID'];
                            $assat_data[$b]['segmentCode'] = $master['segmentCode'];
                            $assat_data[$b]['companyID'] = $master['companyID'];
                            $assat_data[$b]['companyCode'] = $master['companyCode'];
                            $assat_data[$b]['createdUserGroup'] = $master['createdUserGroup'];
                            $assat_data[$b]['createdPCID'] = $master['createdPCID'];
                            $assat_data[$b]['createdUserID'] = $master['createdUserID'];
                            $assat_data[$b]['createdDateTime'] = $master['createdDateTime'];
                            $assat_data[$b]['createdUserName'] = $master['createdUserName'];
                            $assat_data[$b]['modifiedPCID'] = $master['modifiedPCID'];
                            $assat_data[$b]['modifiedUserID'] = $master['modifiedUserID'];
                            $assat_data[$b]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $assat_data[$b]['modifiedUserName'] = $master['modifiedUserName'];
                            $assat_data[$b]['costGLAutoID'] = $item['faCostGLAutoID'];
                            $assat_data[$b]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                            $assat_data[$b]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                            $assat_data[$b]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                            $assat_data[$b]['isPostToGL'] = 1;
                            $assat_data[$b]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                            $assat_data[$b]['postGLCode'] = $ACA['systemAccountCode'];
                            $assat_data[$b]['postGLCodeDes'] = $ACA['GLDescription'];
                            $assat_data[$b]['faCode'] = $this->sequence_mobile->sequence_generator("FA");
                        }
                        if (!empty($assat_data)) {
                            $assat_data = array_values($assat_data);
                            $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                        }
                    }
                } elseif ($payment_detail[$a]['type'] == 'Advance') {
                    $advance_data = array();
                }
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }
            $this->load->model('Double_entry_model');
            $generalledger_arr = array();
            $double_entry = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($system_code, 'PV');

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['pvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['PVNarration'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            $this->load->helper('payable_helper');
            $amount = payment_voucher_total_value($double_entry['master_data']['payVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
            $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
            $bankledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
            $bankledger_arr['transactionType'] = 2;
            $bankledger_arr['bankName'] = $double_entry['master_data']['PVbank'];
            $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
            $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
            $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
            $bankledger_arr['documentType'] = 'PV';
            $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
            $bankledger_arr['modeofPayment'] = $double_entry['master_data']['modeOfPayment'];
            $bankledger_arr['chequeNo'] = $double_entry['master_data']['PVchequeNo'];
            $bankledger_arr['chequeDate'] = $double_entry['master_data']['PVchequeDate'];
            $bankledger_arr['memo'] = $double_entry['master_data']['PVNarration'];
            $bankledger_arr['partyType'] = $double_entry['master_data']['partyType'];
            $bankledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
            $bankledger_arr['partyCode'] = $double_entry['master_data']['partyCode'];
            $bankledger_arr['partyName'] = $double_entry['master_data']['partyName'];
            $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
            $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $bankledger_arr['transactionAmount'] = $amount;
            $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
            $bankledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
            $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
            $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
            $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
            $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
            $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
            $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
            $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
            $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
            $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
            $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
            $bankledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
            $bankledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
            $bankledger_arr['createdPCID'] = current_pc();
            $bankledger_arr['createdUserID'] = current_userID();
            $bankledger_arr['createdDateTime'] = current_date();
            $bankledger_arr['createdUserName'] = current_user();
            $bankledger_arr['modifiedPCID'] = current_pc();
            $bankledger_arr['modifiedUserID'] = current_userID();
            $bankledger_arr['modifiedDateTime'] = current_date();
            $bankledger_arr['modifiedUserName'] = current_user();

            $this->db->insert('srp_erp_bankledger', $bankledger_arr);

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'PV');
                $this->db->where('documentMasterAutoID', $system_code);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
//                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $companyID = current_companyID();
                    $ERGL_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'ERGL'")->row_array();
                    $ERGL = fetch_gl_account_desc($ERGL_ID['GLAutoID']);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentType'] = $double_entry['master_data']['pvType'];
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['PVNarration'];
                    $generalledger_arr['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = $double_entry['master_data']['partyType'];
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['partyCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['partyName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID['GLAutoID'];
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                    $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = current_user_group();
                    $generalledger_arr['createdPCID'] = current_pc();
                    $generalledger_arr['createdUserID'] = current_userID();
                    $generalledger_arr['createdDateTime'] = current_date();
                    $generalledger_arr['createdUserName'] = current_user();
                    $generalledger_arr['modifiedPCID'] = current_pc();
                    $generalledger_arr['modifiedUserID'] = current_userID();
                    $generalledger_arr['modifiedDateTime'] = current_date();
                    $generalledger_arr['modifiedUserName'] = current_user();
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }

            $msgReturn = 'Payment Voucher Approval Successfully';

            $maxLevel = $this->approvals_mobile->maxlevel('PV');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $this->input->post('payVoucherAutoId');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'PV'));

                }
            }

            $itemAutoIDarry = array();
            $wareHouseAutoIDDarry = array();
            foreach ($payment_detail as $value) {
                if ($value['itemAutoID']) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                if ($value['wareHouseAutoID']) {
                    array_push($wareHouseAutoIDDarry, $value['wareHouseAutoID']);
                }

            }
            if ($itemAutoIDarry && $wareHouseAutoIDDarry) {
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $wareHouseAutoIDDarry) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['PVdate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['payVoucherAutoId'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['PVcode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = current_user_group();
                    $exceededmatch['createdPCID'] = current_pc();
                    $exceededmatch['createdUserID'] = current_userID();
                    $exceededmatch['createdUserName'] = current_user();
                    $exceededmatch['createdDateTime'] = current_date();
                    $exceededmatch['documentSystemCode'] = $this->sequence_mobile->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($payment_detail as $itemid) {
                    if ($itemid['type'] == 'Item') {
                        $receivedQty = $itemid['requestedQty'];
                        $receivedQtyConverted = ($itemid['requestedQty'] / $itemid['conversionRateUOM']);
                        $companyID = current_companyID();
                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $itemid['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                        $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                        $sumqty = array_column($exceededitems, 'balanceQty');
                        $sumqty = array_sum($sumqty);
                        if (!empty($exceededitems)) {
                            foreach ($exceededitems as $exceededItemAutoID) {
                                if ($receivedQtyConverted > 0) {
                                    $balanceQty = $exceededItemAutoID['balanceQty'];
                                    $updatedQty = $exceededItemAutoID['updatedQty'];
                                    $balanceQtyConverted = ($exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM']);
                                    $updatedQtyConverted = ($exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM']);
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;

                                        $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetail['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                        $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                        $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                        $exceededmatchdetail['createdPCID'] = current_pc();
                                        $exceededmatchdetail['createdUserID'] = current_userID();
                                        $exceededmatchdetail['createdUserName'] = current_user();
                                        $exceededmatchdetail['createdDateTime'] = current_date();

                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                    } else {
                                        $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                        $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetails['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                        $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                        $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                        $exceededmatchdetails['createdPCID'] = current_pc();
                                        $exceededmatchdetails['createdUserID'] = current_userID();
                                        $exceededmatchdetails['createdUserName'] = current_user();
                                        $exceededmatchdetails['createdDateTime'] = current_date();
                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                        $receivedQty = $receivedQty - $exeed['updatedQty'];
                                        $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                    }
                                }
                            }
                        }
                    }

                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);

                }
            }

            return array('status' => '1', 'data' => $msgReturn, 'email' => $approvals_status['email']);
        } else if ($approvals_status['status'] == 2) {
            return array('status' => '1', 'data' => 'Payment Voucher Partially Approved', 'email' => $approvals_status['email']);
        } else if ($approvals_status['status'] == 3) {
        return array('status' => '1', 'data' => 'Payment Voucher Rejected Successfully', 'email' => $approvals_status['email']);
        }


    }

    function save_jv_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');

        if($autoappLevel == 1) {
            $system_code = trim($this->input->post('JVMasterAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['JVMasterAutoId']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();

        $JVDetails = $this->db->query('SELECT
	    srp_erp_jvdetail.*,srp_erp_chartofaccounts.bankCurrencyID,srp_erp_chartofaccounts.bankCurrencyCode,srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,srp_erp_chartofaccounts.isBank,srp_erp_chartofaccounts.bankName
        FROM
            srp_erp_jvdetail
        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_jvdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
        WHERE
            JVMasterAutoId = '.$system_code.'
        AND srp_erp_jvdetail.companyID= '.$companyID.'  ')->result_array();
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'JV');
        }

        if ($approvals_status['status'] == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_journal_entry_data($system_code, 'JV');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['JVMasterAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['JVcode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['JVdate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['JVType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['JVdate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['JVdate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['description'];
                $generalledger_arr[$i]['chequeNumber'] = null;
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = $double_entry['gl_detail'][$i]['projectID'];
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            foreach($JVDetails as $val){
                if($val['isBank']==1){
                    if($val['gl_type']=='Cr'){
                        $transactionType=2;
                        $transactionAmount=$val['creditAmount'];
                    }else{
                        $transactionType=1;
                        $transactionAmount=$val['debitAmount'];
                    }
                    $bankledger['documentDate']=$double_entry['master_data']['JVdate'];
                    $bankledger['transactionType']=$transactionType;
                    $bankledger['transactionCurrencyID']=$double_entry['master_data']['transactionCurrencyID'];
                    $bankledger['transactionCurrency']=$double_entry['master_data']['transactionCurrency'];
                    $bankledger['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                    $bankledger['transactionAmount']=$transactionAmount;
                    $bankledger['transactionCurrencyDecimalPlaces']=$double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $bankledger['bankCurrencyID']=$val['bankCurrencyID'];
                    $bankledger['bankCurrency']=$val['bankCurrencyCode'];
                    $bankledger['bankCurrencyExchangeRate']=$val['bankCurrencyExchangeRate'];
                    $bankledger['bankCurrencyAmount']=$val['bankCurrencyAmount'];
                    $bankledger['bankCurrencyDecimalPlaces']=$val['bankCurrencyDecimalPlaces'];
                    $bankledger['memo']=$val['description'];
                    $bankledger['bankName']=$val['bankName'];
                    $bankledger['bankGLAutoID']=$val['GLAutoID'];
                    $bankledger['bankSystemAccountCode']=$val['systemGLCode'];
                    $bankledger['bankGLSecondaryCode']=$val['GLCode'];
                    $bankledger['documentMasterAutoID']=$val['JVMasterAutoId'];
                    $bankledger['documentType']='JV';
                    $bankledger['documentSystemCode']=$double_entry['master_data']['JVcode'];
                    $bankledger['createdPCID']=current_pc();
                    $bankledger['companyID']=$val['companyID'];
                    $bankledger['companyCode']=$val['companyCode'];
                    $bankledger['segmentID']=$val['segmentID'];
                    $bankledger['segmentCode']=$val['segmentCode'];
                    $bankledger['createdUserID']=current_userID();
                    $bankledger['createdDateTime']=current_date();
                    $bankledger['createdUserName']=current_user();
                    $this->db->insert('srp_erp_bankledger', $bankledger);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => '0', 'data' => 'Failed to Journal Voucher Approve', 'email' => null);
            } else {
                $this->db->trans_commit();
                return array('status' => '1', 'data' => 'Journal Voucher Approved Successfully', 'email' => $approvals_status['email']);
            }
        } else if($approvals_status['status'] == 2) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => '0', 'data' => 'Failed to Approve Journal Voucher', 'email' => null);
            } else {
                $this->db->trans_commit();
                return array('status' => '1', 'data' => 'Journal Voucher Approved Successfully', 'email' => $approvals_status['email']);
            }
        }
        else if($approvals_status['status'] == 3) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => '0', 'data' => 'Failed to Reject Journal Voucher', 'email' => null);
            } else {
                $this->db->trans_commit();
                return array('status' => '1', 'data' => 'Journal Voucher Rejected Successfully', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_rv_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        $this->load->library('Sequence_mobile');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_id = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['receiptVoucherAutoId'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, srp_erp_warehouseitems.currentStock as availableStock,
                SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,
                (srp_erp_warehouseitems.currentStock- SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM)) as stock ,
                srp_erp_warehouseitems.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID 
                FROM srp_erp_customerreceiptdetail 
                INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID
                 JOIN srp_erp_itemmaster ON srp_erp_customerreceiptdetail.itemAutoID = srp_erp_itemmaster.itemAutoID 
                AND srp_erp_customerreceiptdetail.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID 
                where receiptVoucherAutoId = '{$system_id}' AND itemCategory != 'Service' AND  itemCategory != 'Non Inventory'  
                GROUP BY itemAutoID
                Having stock < 0";

        $items_arr = $this->db->query($sql)->result_array();
        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals_mobile->approve_document($system_id, $level_id, $status, $comments, 'RV');
            }
            if ($approvals_status['status'] == 1) {
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptmaster');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptdetail');
                $receipt_detail = $this->db->get()->result_array();
                for ($a = 0; $a < count($receipt_detail); $a++) {
                    if ($receipt_detail[$a]['type'] == 'Item') {
                        $item = fetch_item_data($receipt_detail[$a]['itemAutoID']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory'  or $item['mainCategory'] == 'Service') {
                            $itemAutoID = $receipt_detail[$a]['itemAutoID'];
                            $qty = $receipt_detail[$a]['requestedQty'] / $receipt_detail[$a]['conversionRateUOM'];
                            $wareHouseAutoID = $receipt_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                            $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($receipt_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($receipt_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }
                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['receiptVoucherAutoId'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['RVcode'];
                            $itemledger_arr[$a]['documentDate'] = $master['RVdate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['referanceNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $receipt_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $receipt_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $receipt_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $receipt_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $receipt_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $receipt_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['SUOMID'] = $receipt_detail[$a]['SUOMID'];
                            $itemledger_arr[$a]['SUOMQty'] = $receipt_detail[$a]['SUOMQty'];
                            $itemledger_arr[$a]['defaultUOMID'] = $receipt_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $receipt_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOMID'] = $receipt_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionUOM'] = $receipt_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionQTY'] = ($receipt_detail[$a]['requestedQty'] * -1);
                            $itemledger_arr[$a]['convertionRate'] = $receipt_detail[$a]['conversionRateUOM'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['revanueGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['revanueSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['revanueGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['revanueDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['revanueType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                            $itemledger_arr[$a]['transactionAmount'] = round((($receipt_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['salesPrice'] = (($receipt_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])) * -1);
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                            $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                            $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                            $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                            $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                            $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        }
                    }
                }

                /*if (!empty($item_arr)) {
                    $item_arr = array_values($item_arr);
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }*/

                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($system_id, 'RV');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['RVType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['RVNarration'];
                    $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                    $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                    $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                    $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                    $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                    $generalledger_arr[$i]['createdPCID'] = current_pc();
                    $generalledger_arr[$i]['createdUserID'] = current_userID();
                    $generalledger_arr[$i]['createdDateTime'] = current_date();
                    $generalledger_arr[$i]['createdUserName'] = current_user();
                    $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                    $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                    $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                    $generalledger_arr[$i]['modifiedUserName'] = current_user();
                }
                $this->load->helper('receivable_helper');
                $amount = receipt_voucher_total_value($double_entry['master_data']['receiptVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
                $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                $bankledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                $bankledger_arr['transactionType'] = 1;
                $bankledger_arr['bankName'] = $double_entry['master_data']['RVbank'];
                $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
                $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
                $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
                $bankledger_arr['documentType'] = 'RV';
                $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                $bankledger_arr['modeofpayment'] = $double_entry['master_data']['modeOfPayment'];
                $bankledger_arr['chequeNo'] = $double_entry['master_data']['RVchequeNo'];
                $bankledger_arr['chequeDate'] = $double_entry['master_data']['RVchequeDate'];
                $bankledger_arr['memo'] = $double_entry['master_data']['RVNarration'];
                $bankledger_arr['partyType'] = 'CUS';
                $bankledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                $bankledger_arr['partyCode'] = $double_entry['master_data']['customerSystemCode'];
                $bankledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $bankledger_arr['transactionAmount'] = $amount;
                $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                $bankledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
                $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
                $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
                $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
                $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                $bankledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                $bankledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                $bankledger_arr['createdPCID'] = current_pc();
                $bankledger_arr['createdUserID'] = current_userID();
                $bankledger_arr['createdDateTime'] = current_date();
                $bankledger_arr['createdUserName'] = current_user();
                $bankledger_arr['modifiedPCID'] = current_pc();
                $bankledger_arr['modifiedUserID'] = current_userID();
                $bankledger_arr['modifiedDateTime'] = current_date();
                $bankledger_arr['modifiedUserName'] = current_user();

                $this->db->insert('srp_erp_bankledger', $bankledger_arr);
                if (!empty($generalledger_arr)) {
                    $generalledger_arr = array_values($generalledger_arr);
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                    $this->db->where('documentCode', 'RV');
                    $this->db->where('documentMasterAutoID', $system_id);
                    $totals = $this->db->get('srp_erp_generalledger')->row_array();
                    if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                        $generalledger_arr = array();
//                        $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                        $companyID = current_companyID();
                        $ERGL_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'ERGL'")->row_array();

                        $ERGL = fetch_gl_account_desc($ERGL_ID['GLAutoID']);
                        $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                        $generalledger_arr['documentCode'] = $double_entry['code'];
                        $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                        $generalledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentType'] = $double_entry['master_data']['RVType'];
                        $generalledger_arr['documentYear'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                        $generalledger_arr['documentNarration'] = $double_entry['master_data']['RVNarration'];
                        $generalledger_arr['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
                        $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr['partyContractID'] = '';
                        $generalledger_arr['partyType'] = 'CUS';
                        $generalledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                        $generalledger_arr['partySystemCode'] = $double_entry['master_data']['customerSystemCode'];
                        $generalledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                        $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                        $generalledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                        $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                        $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                        $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                        $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                        $generalledger_arr['amount_type'] = null;
                        $generalledger_arr['documentDetailAutoID'] = 0;
                        $generalledger_arr['GLAutoID'] = $ERGL_ID['GLAutoID'];
                        $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                        $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                        $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                        $generalledger_arr['GLType'] = $ERGL['subCategory'];
                        $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                        $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                        $generalledger_arr['subLedgerType'] = 0;
                        $generalledger_arr['subLedgerDesc'] = null;
                        $generalledger_arr['isAddon'] = 0;
                        $generalledger_arr['createdUserGroup'] = current_user_group();
                        $generalledger_arr['createdPCID'] = current_pc();
                        $generalledger_arr['createdUserID'] = current_userID();
                        $generalledger_arr['createdDateTime'] = current_date();
                        $generalledger_arr['createdUserName'] = current_user();
                        $generalledger_arr['modifiedPCID'] = current_pc();
                        $generalledger_arr['modifiedUserID'] = current_userID();
                        $generalledger_arr['modifiedDateTime'] = current_date();
                        $generalledger_arr['modifiedUserName'] = current_user();
                        $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                    }
                }
                $this->db->select_sum('transactionAmount');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $total = $this->db->get('srp_erp_customerreceiptdetail')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = current_userID();
                $data['approvedbyEmpName'] = current_user();
                $data['approvedDate'] = current_date();
                $data['transactionAmount'] = $total;
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->update('srp_erp_customerreceiptmaster', $data);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => '0', 'data' => 'Failed to Approval Receipt Voucher', 'email' => $approvals_status['email']);
                } else {
                    $this->db->trans_commit();
                    return array('status' => '1', 'data' => 'Receipt Voucher Approved Successfully', 'email' => $approvals_status['email']);
                }
            } else if ($approvals_status['status'] == 2){
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => '0', 'data' => 'Failed to Approve Receipt Voucher', 'email' => $approvals_status['email']);
                } else {
                    $this->db->trans_commit();
                    return array('status' => '1', 'data' => 'Receipt Voucher partially Approved', 'email' => $approvals_status['email']);
                }
            
            }
            else if ($approvals_status['status'] == 3){
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => '0', 'data' => 'Failed to Reject Receipt Voucher', 'email' => $approvals_status['email']);
                } else {
                    $this->db->trans_commit();
                    return array('status' => '1', 'data' => 'Receipt Voucher Rejected Successfully', 'email' => $approvals_status['email']);
                }
            }
        } else {
            return array('status' => '0', 'data' => 'Item quantities are insufficient.', 'email' => null);
        }
    }

    function save_invoice_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0,$isRetentionYN=0)
    {
        $this->load->library('Approvals_mobile');
        if($autoappLevel==1){
            $system_id = trim($this->input->post('invoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }elseif($autoappLevel==2){
            $system_id = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        }else{
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['invoiceAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, 
                SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) AS qty, ware_house.currentStock, ware_house.currentStock as availableStock,
                ( ware_house.currentStock - SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) ) AS stock,
                ware_house.itemAutoID, cus_inv.wareHouseAutoID
                FROM srp_erp_customerinvoicedetails AS cus_inv
                INNER JOIN srp_erp_warehouseitems AS ware_house ON ware_house.itemAutoID = cus_inv.itemAutoID
                JOIN srp_erp_itemmaster ON cus_inv.itemAutoID = srp_erp_itemmaster.itemAutoID
                AND cus_inv.wareHouseAutoID = ware_house.wareHouseAutoID
                WHERE invoiceAutoID = '{$system_id}'
                AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                GROUP BY itemAutoID
                HAVING stock < 0";


        $items_arr = $this->db->query($sql)->result_array();
        if($status!=1){
            $items_arr='';
        }
        if (!$items_arr) {
            if($autoappLevel==0){
                $approvals_status=1;
            }else{
                $approvals_status = $this->approvals_mobile->approve_document($system_id, $level_id, $status, $comments, 'CINV');
            }
            if ($approvals_status['status'] == 1 && $isRetentionYN==0) {
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicedetails');
                $invoice_detail = $this->db->get()->result_array();

                if($master['retentionPercentage']>0){
                    $this->create_retention_invoice($system_id);
                }

                if($master["invoiceType"] != "Manufacturing") {
                    if($master["invoiceType"] != "Insurance") {
                        for ($a = 0; $a < count($invoice_detail); $a++) {
                            if ($invoice_detail[$a]['type'] == 'Item') {
                                $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory'  or $item['mainCategory'] == 'Service') {
                                    $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                    $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                    $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                                    $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                                    if (!empty($item_arr)) {
                                        $this->db->where('itemAutoID', trim($invoice_detail[$a]['itemAutoID']));
                                        $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                                    }
                                    $itemledger_arr[$a]['documentID'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $master['invoiceAutoID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $master['invoiceCode'];
                                    $itemledger_arr[$a]['documentDate'] = $master['invoiceDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $invoice_detail[$a]['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $invoice_detail[$a]['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $invoice_detail[$a]['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $invoice_detail[$a]['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $invoice_detail[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $invoice_detail[$a]['itemDescription'];
                                    $itemledger_arr[$a]['SUOMID'] = $invoice_detail[$a]['SUOMID'];
                                    $itemledger_arr[$a]['SUOMQty'] = $invoice_detail[$a]['SUOMQty'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $invoice_detail[$a]['defaultUOMID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $invoice_detail[$a]['defaultUOM'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $invoice_detail[$a]['unitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionUOM'] = $invoice_detail[$a]['unitOfMeasure'];
                                    $itemledger_arr[$a]['transactionQTY'] = ($invoice_detail[$a]['requestedQty'] * -1);
                                    $itemledger_arr[$a]['convertionRate'] = $invoice_detail[$a]['conversionRateUOM'];
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $item['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $item['assteType'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                    $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                                    $itemledger_arr[$a]['transactionAmount'] = round((($invoice_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['salesPrice'] = (($invoice_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])) * -1);
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                    $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                                    $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                                    $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                                    $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                                    $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                                    $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $master['companyID'];
                                    $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                                    $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                                }
                            }
                        }
                        if (!empty($itemledger_arr)) {
                            $itemledger_arr = array_values($itemledger_arr);
                            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                        }
                    }
                    $this->load->model('Double_entry_model');
                    if($master["invoiceType"] != "Insurance") {
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');
                    }else{
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_insurance($system_id, 'CINV');
                    }

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                        $generalledger_arr[$i]['createdPCID'] = current_pc();
                        $generalledger_arr[$i]['createdUserID'] = current_userID();
                        $generalledger_arr[$i]['createdDateTime'] = current_date();
                        $generalledger_arr[$i]['createdUserName'] = current_user();
                        $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                        $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                        $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                        $generalledger_arr[$i]['modifiedUserName'] = current_user();
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }

                }else{
                    for ($a = 0; $a < count($invoice_detail); $a++) {
                        if ($invoice_detail[$a]['type'] == 'Item') {
                            $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                            if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                                $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];

                                $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                                $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);

                            }
                        }
                    }

                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_mfq_customer_invoice_data($system_id, 'CINV');
                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                        $generalledger_arr[$i]['createdPCID'] = current_pc();
                        $generalledger_arr[$i]['createdUserID'] = current_userID();
                        $generalledger_arr[$i]['createdDateTime'] = current_date();
                        $generalledger_arr[$i]['createdUserName'] = current_user();
                        $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                        $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                        $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                        $generalledger_arr[$i]['modifiedUserName'] = current_user();
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }
                }

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = current_userID();
                $data['approvedbyEmpName'] = current_user();
                $data['approvedDate'] = current_date();

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster', $data);
                //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');

                if($master["invoiceType"] == "Insurance") {
                    $sumsup = "SELECT (sum(transactionAmount)-sum(marginAmount)) as transactionAmount,
srp_erp_customerinvoicedetails.supplierAutoID as supplierAutoID,
srp_erp_customerinvoicedetails.segmentID as segmentID,
srp_erp_customerinvoicedetails.segmentCode as segmentCode,
srp_erp_suppliermaster.supplierName as supplierName,
srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,
srp_erp_suppliermaster.supplierAddress1 as supplierAddress,
srp_erp_suppliermaster.supplierTelephone as supplierTelephone,
srp_erp_suppliermaster.supplierFax as supplierFax,
srp_erp_suppliermaster.liabilityAutoID as liabilityAutoID,
srp_erp_suppliermaster.liabilitySystemGLCode as liabilitySystemGLCode,
srp_erp_suppliermaster.liabilityGLAccount as liabilityGLAccount,
srp_erp_suppliermaster.liabilityDescription as liabilityDescription,
srp_erp_suppliermaster.liabilityType as liabilityType,
srp_erp_suppliermaster.supplierCurrencyID as supplierCurrencyID,
srp_erp_suppliermaster.supplierCurrency as supplierCurrency,
srp_erp_suppliermaster.supplierCurrencyDecimalPlaces as supplierCurrencyDecimalPlaces
FROM
	`srp_erp_customerinvoicedetails`
Left JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID
WHERE
	`invoiceAutoID` = $system_id
	GROUP BY
		supplierAutoID";
                    $sumsupdetail = $this->db->query($sumsup)->result_array();
                    $this->load->library('Sequence_mobile');
                    $invdate=explode("-",$master['invoiceDate']);

                    foreach($sumsupdetail as $val){
                        $datasup['documentID'] = 'BSI';
                        $datasup['invoiceType'] = 'Standard';
                        $datasup['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $datasup['companyFinanceYear'] = $master['companyFinanceYear'];
                        $datasup['warehouseAutoID'] = $master['wareHouseAutoID'];
                        $datasup['isSytemGenerated'] = 1;
                        $datasup['documentOrigin'] = 'CINV';
                        $datasup['documentOriginAutoID'] = $system_id;
                        $datasup['FYBegin'] = $master['FYBegin'];
                        $datasup['FYEnd'] = $master['FYEnd'];
                        $datasup['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $datasup['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $datasup['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
                        $datasup['bookingInvCode'] = $this->sequence_mobile->sequence_generator_fin('BSI',$master['companyFinanceYearID'],$invdate[0],$invdate[1]);
                        $datasup['bookingDate'] = $master['invoiceDate'];
                        $datasup['invoiceDate'] = $master['invoiceDate'];
                        $datasup['invoiceDueDate'] = $master['invoiceDueDate'];
                        $datasup['comments'] = 'From custome invoice '.$master['invoiceCode'];
                        $datasup['RefNo'] = $master['invoiceCode'];
                        $datasup['supplierID'] = $val['supplierAutoID'];
                        $datasup['supplierCode'] = $val['supplierSystemCode'];
                        $datasup['supplierName'] = $val['supplierName'];
                        $datasup['supplierAddress'] = $val['supplierAddress'];
                        $datasup['supplierTelephone'] = $val['supplierTelephone'];
                        $datasup['supplierFax'] = $val['supplierFax'];
                        $datasup['supplierliabilityAutoID'] = $val['liabilityAutoID'];
                        $datasup['supplierliabilitySystemGLCode'] = $val['liabilitySystemGLCode'];
                        $datasup['supplierliabilityGLAccount'] = $val['liabilityGLAccount'];
                        $datasup['supplierliabilityDescription'] = $val['liabilityDescription'];
                        $datasup['supplierliabilityType'] = $val['liabilityType'];
                        $datasup['supplierInvoiceDate'] = $master['invoiceDate'];
                        $datasup['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $datasup['transactionCurrency'] = $master['transactionCurrency'];
                        $datasup['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $datasup['transactionAmount'] = $val['transactionAmount'];
                        $datasup['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $datasup['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $datasup['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $datasup['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $datasup['companyLocalAmount'] = $val['transactionAmount']/$master['companyLocalExchangeRate'];
                        $datasup['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $datasup['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $datasup['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $datasup['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $datasup['companyReportingAmount'] = $val['transactionAmount']/$master['companyReportingExchangeRate'];
                        $datasup['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $datasup['supplierCurrencyID'] = $val['supplierCurrencyID'];
                        $datasup['supplierCurrency'] = $val['supplierCurrency'];
                        $datasup['segmentID'] = $val['segmentID'];
                        $datasup['segmentCode'] = $val['segmentCode'];
                        $datasup['companyID'] = current_companyID();
                        $datasup['companyCode'] = current_companyCode();
                        $supplier_currency = currency_conversionID($master['transactionCurrencyID'], $val['supplierCurrencyID']);
                        $datasup['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
                        $datasup['supplierCurrencyAmount'] = $val['transactionAmount']/$supplier_currency['conversion'];
                        $datasup['supplierCurrencyDecimalPlaces'] = $val['supplierCurrencyDecimalPlaces'];
                        $datasup['confirmedYN'] = 1;
                        $datasup['confirmedByEmpID'] = current_userID();
                        $datasup['confirmedByName'] = current_user();
                        $datasup['confirmedDate'] = current_date();
                        $datasup['createdUserGroup'] = current_user_group();
                        $datasup['createdPCID'] = current_pc();
                        $datasup['createdUserID'] = current_userID();
                        $datasup['createdDateTime'] = current_date();
                        $datasup['createdUserName'] = current_user();

                        $supresult=$this->db->insert('srp_erp_paysupplierinvoicemaster', $datasup);
                        $last_idsup = $this->db->insert_id();
                        if($supresult){
                            $supid=$val['supplierAutoID'];
                            $supd = "SELECT * FROM `srp_erp_customerinvoicedetails` WHERE `invoiceAutoID` = $system_id AND `supplierAutoID` = $supid";
                            $supdetail = $this->db->query($supd)->result_array();

                            foreach($supdetail as $detl){
                                $datasupd['InvoiceAutoID'] = $last_idsup;
                                $datasupd['segmentID'] = $detl['segmentID'];
                                $datasupd['segmentCode'] = $detl['segmentCode'];
                                $datasupd['description'] = $detl['description'];
                                $datasupd['GLCode'] = "-";
                                $datasupd['transactionAmount'] = round($detl['transactionAmount']-$detl['marginAmount'],$master['transactionCurrencyDecimalPlaces']);
                                $datasupd['transactionExchangeRate'] = $master['transactionExchangeRate'];
                                $datasupd['companyLocalAmount'] = round($datasupd['transactionAmount']/$master['companyLocalExchangeRate'], $master['companyLocalCurrencyDecimalPlaces']);
                                $datasupd['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $datasupd['companyReportingAmount'] = round($datasupd['transactionAmount']/$master['companyReportingExchangeRate'], $master['companyReportingCurrencyDecimalPlaces']);
                                $datasupd['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $datasupd['supplierAmount'] = round($datasupd['transactionAmount']/$datasup['supplierCurrencyExchangeRate'], $datasup['supplierCurrencyDecimalPlaces']);
                                $datasupd['supplierCurrencyExchangeRate'] = $datasup['supplierCurrencyExchangeRate'];
                                $datasupd['companyCode'] = current_companyCode();
                                $datasupd['companyID'] = current_companyID();
                                $datasupd['createdUserGroup'] = current_user_group();
                                $datasupd['createdPCID'] = current_pc();
                                $datasupd['createdUserID'] = current_userID();
                                $datasupd['createdUserName'] = current_user();
                                $datasupd['createdDateTime'] = current_date();
                                $this->db->insert('srp_erp_paysupplierinvoicedetail', $datasupd);
                            }
                            $approvals_status_sup = $this->approvals_mobile->auto_approve($last_idsup, 'srp_erp_paysupplierinvoicemaster','InvoiceAutoID', 'BSI',$master['invoiceDate'],$master['invoiceDate']);
                            if($approvals_status_sup==1){
                                $this->save_supplier_invoice_approval(0, $last_idsup, 1, 'Auto Approved');
                            }
                        }
                    }
                }
            }else{
                if($isRetentionYN==1)
                {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_opr($system_id, 'CINV');

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                        $generalledger_arr[$i]['createdPCID'] = current_pc();
                        $generalledger_arr[$i]['createdUserID'] = current_userID();
                        $generalledger_arr[$i]['createdDateTime'] = current_date();
                        $generalledger_arr[$i]['createdUserName'] = current_user();
                        $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                        $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                        $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                        $generalledger_arr[$i]['modifiedUserName'] = current_user();
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                if($approvals_status['status'] == 1) {
                    return array('status' => '0', 'data' => 'Invoice Approval Failed', 'email' => $approvals_status['email']);
                } else if ($approvals_status['status'] == 3) {
                    return array('status' => '0', 'data' => 'Failed to Reject Invoice', 'email' => $approvals_status['email']);
                }
//                return array('e', 'Invoice Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                if($master['invoiceType']='Project')
                {
                    $this->load->model('Invoice_model');
                    $this->Invoice_model->updateRVMconfirmstatus($system_id);
                }
                if($approvals_status['status'] == 1) {
                    return array('status' => '0', 'data' => 'Invoice Approval Successfully', 'email' => $approvals_status['email']);
                } else if ($approvals_status['status'] == 2) {
                    return array('status' => '0', 'data' => 'Invoice partially Approved', 'email' => $approvals_status['email']);
                }
                else if ($approvals_status['status'] == 3) {
                    return array('status' => '0', 'data' => 'Invoice Rejected Successfully', 'email' => $approvals_status['email']);
                }
//                return array('s', 'Invoice Approval Successfull.', 1);
            }
        } else {
            return array('status' => '0', 'data' => 'Item quantities are insufficient', 'email' => null);
//            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function create_retention_invoice($invoiceAutoID)
    {
        $master = $this->db->query("SELECT * FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = $invoiceAutoID")->row_array();
        $data['documentID'] = 'CINV';
        $data['invoiceType'] = 'Operation';
        $data['isSytemGenerated'] = 1;
        $data['invoiceDate'] = $master['invoiceDate'];
        $data['invoiceDueDate'] = $master['invoiceDueDate'];
        $data['invoiceCode'] = $master['invoiceCode'].'/R';
        $data['referenceNo'] = $master['referenceNo'];
        $data['invoiceNarration'] = $master['invoiceNarration'];
        $data['bankGLAutoID'] = $master['bankGLAutoID'];
        $data['bankSystemAccountCode'] = $master['bankSystemAccountCode'];
        $data['bankGLSecondaryCode'] = $master['bankGLSecondaryCode'];
        $data['bankCurrencyID'] = $master['bankCurrencyID'];
        $data['bankCurrency'] = $master['bankCurrency'];
        $data['invoicebank'] = $master['invoicebank'];
        $data['invoicebankBranch'] = $master['invoicebankBranch'];
        $data['invoicebankSwiftCode'] = $master['invoicebankSwiftCode'];
        $data['invoicebankAccount'] = $master['invoicebankAccount'];
        $data['invoicebankType'] = $master['invoicebankType'];
        $data['companyFinanceYearID'] = $master['companyFinanceYearID'];
        $data['companyFinanceYear'] = $master['companyFinanceYear'];
        $data['FYBegin'] = $master['FYBegin'];
        $data['FYEnd'] = $master['FYEnd'];
        $data['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
        $data['customerID'] = $master['customerID'];
        $data['customerSystemCode'] = $master['customerSystemCode'];
        $data['customerName'] = $master['customerName'];
        $data['customerAddress'] = $master['customerAddress'];
        $data['customerTelephone'] = $master['customerTelephone'];
        $data['customerFax'] = $master['customerFax'];
        $data['customerEmail'] = $master['customerEmail'];
        $data['customerReceivableAutoID'] = $master['customerReceivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $master['customerReceivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $master['customerReceivableGLAccount'];
        $data['customerReceivableDescription'] = $master['customerReceivableDescription'];
        $data['customerReceivableType'] = $master['customerReceivableType'];
        $data['isPrintDN'] = $master['isPrintDN'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data['transactionAmount'] = $master['retensionTransactionAmount'];
        $data['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = $master['retensionLocalAmount'];
        $data['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
        $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = $master['retensionReportingAmount'];
        $data['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
        $data['customerCurrencyID'] = $master['customerCurrencyID'];
        $data['customerCurrency'] = $master['customerCurrency'];
        $data['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data['customerCurrencyAmount'] = 0;
        $data['customerCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        $data['confirmedYN'] = 1;

        $data['confirmedByEmpID'] = current_userID();
        $data['confirmedByName'] = current_user();
        $data['confirmedDate'] = current_date();
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['companyID'] = $master['companyID'];
        $data['companyCode'] = $master['companyCode'];
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = current_date();
        $data['createdUserName'] = current_user();
        $data['isOpYN'] = 1;
        $data['retensionInvoiceID'] = $invoiceAutoID;

        $insert = $this->db->insert('srp_erp_customerinvoicemaster', $data);
        $last_idR = $this->db->insert_id();
        if($insert){
            $dataD['invoiceAutoID'] = $last_idR;
            $dataD['type'] = 'OP';
            $dataD['contractQty'] = 1;
            $dataD['contractAmount'] = $data['transactionAmount'];
            $dataD['description'] = 'Retention Balance';
            $dataD['transactionAmount'] = $data['transactionAmount'];
            $dataD['companyLocalAmount'] = $data['companyLocalAmount'];
            $dataD['companyReportingAmount'] = $data['companyReportingAmount'];
            $dataD['customerAmount'] = $data['transactionAmount'];
            $dataD['segmentID'] = $data['segmentID'];
            $dataD['segmentCode'] = $data['segmentCode'];
            $dataD['companyID'] = $master['companyID'];
            $dataD['companyCode'] = $master['companyCode'];
            $dataD['createdPCID'] = current_pc();
            $dataD['createdUserID'] = current_userID();
            $dataD['createdDateTime'] = current_date();
            $dataD['createdUserName'] = current_user();

            $insertD = $this->db->insert('srp_erp_customerinvoicedetails', $dataD);
            if($insertD){
                $this->load->library('Approvals_mobile');
                $approvals_status_cinv = $this->approvals_mobile->auto_approve($last_idR, 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$data['invoiceCode'],$master['invoiceDate']);
                if($approvals_status_cinv==1){
                    $this->save_invoice_approval(0,$last_idR,1,'Auto Approved',1);
                }
            }
        }
    }

    function save_purchase_order_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        $companyID = current_companyID();
        $msg = '';
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('purchaseOrderID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('po_status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['purchaseOrderID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $amountBasedApproval = getPolicyValues('ABA', 'All');
        if($amountBasedApproval == 1) {
            $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency, ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaseordermaster
                        LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                    WHERE
                        srp_erp_purchaseordermaster.purchaseOrderID = {$system_code} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();

//            $defaultCurrencyID = current_company_default_currencyID();
//            $conversion = currency_conversionID($documentTotal['transactionCurrencyID'], $defaultCurrencyID,  $documentTotal['total_value']);

            $poLocalAmount = $documentTotal['total_value'] / $documentTotal['companyLocalExchangeRate'];
            $amountApprovable = amount_based_approval('PO', $poLocalAmount, $level_id);
            if($amountApprovable['type'] == 'e') {
                return array('status' => '0', 'data' => 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO Value', 'email' => null);
            }
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'PO');
        }
        if ($approvals_status['status'] == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = current_userID();
            $data['approvedbyEmpName'] = current_user();
            $data['approvedDate'] = current_date();
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            $this->db->where('purchaseOrderID', trim($system_code));
            $this->db->update('srp_erp_purchaseordermaster', $data);

            $msg = array('status' => '0', 'data' => 'Purchase Order Approved Successfully', 'email' => $approvals_status['email']);
        } else if ($approvals_status['status'] == 3) {
            $msg = array('status' => '0', 'data' => 'Purchase Order Approval Rejected Successfully', 'email' => $approvals_status['email']);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $msg;
        } else {
            $this->db->trans_commit();
            return $msg;
        }
    }

    function save_purchase_request_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        $msg = '';
        if($autoappLevel==1){
            $system_code = trim($this->input->post('purchaseRequestID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('po_status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['purchaseRequestID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'PRQ');
        }

        if ($approvals_status['status'] == 1) {
            $msg = array('status' => '1', 'data' => 'Purchase Request Approved Successfully', 'email' => $approvals_status['email']);
        } else if($approvals_status['status'] == 3) {
            $msg = array('status' => '1', 'data' => 'Purchase Request Rejected Successfully', 'email' => $approvals_status['email']);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $msg;
        } else {
            $this->db->trans_commit();
            return $msg;
        }
    }

    function save_grv_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('grvAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['grvAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $company_id = current_companyID();
        $transaction_tot = 0;
        $company_rpt_tot = 0;
        $supplier_cr_tot = 0;
        $company_loc_tot = 0;

        $maxLevel = $this->approvals_mobile->maxlevel('GRV');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
    
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'GRV');
        }
        if ($approvals_status['status'] == 1) {
            $this->db->select('*');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $company_id);
            $this->db->from('srp_erp_grvmaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $company_id);
            $this->db->from('srp_erp_grvdetails');
            $grvdetails = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $po_arr = array();
            $wareHouseAutoID = $master['wareHouseAutoID'];
            $company_loc = 0;
            $company_rpt = 0;
            $supplier_cr = 0;
            $grvdetail_tot = 0;

            for ($i = 0; $i < count($grvdetails); $i++) {
                $company_loc = ($grvdetails[$i]['fullTotalAmount'] / $master['companyLocalExchangeRate']);
                $company_rpt = ($grvdetails[$i]['fullTotalAmount'] / $master['companyReportingExchangeRate']);
                $supplier_cr = ($grvdetails[$i]['fullTotalAmount'] / $master['supplierCurrencyExchangeRate']);

                $transaction_tot += $grvdetails[$i]['fullTotalAmount'];
                $company_loc_tot += $company_loc;
                $company_rpt_tot += $company_rpt;
                $supplier_cr_tot += $supplier_cr;
                $grvdetail_tot += $grvdetails[$i]['receivedTotalAmount'];

                $po_arr[$i] = $grvdetails[$i]['purchaseOrderMastertID'];
                $item = fetch_item_data($grvdetails[$i]['itemAutoID']);
                $this->db->select('GLAutoID');
                $this->db->where('controlAccountType', 'ACA');
                $this->db->where('companyID', $company_id);
                $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();

                $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                if ($grvdetails[$i]['itemCategory'] == 'Inventory' or $grvdetails[$i]['itemCategory'] == 'Non Inventory'  or $grvdetails[$i]['itemCategory'] == 'Service') {
                    $itemAutoID = $grvdetails[$i]['itemAutoID'];
                    $grv_qty = $grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$grv_qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    
                    $itemledgerCurrentStock = fetch_itemledger_currentstock($grvdetails[$i]['itemAutoID']);
                    $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($grvdetails[$i]['itemAutoID'], 'companyLocalExchangeRate');
                    $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($grvdetails[$i]['itemAutoID'],'companyReportingExchangeRate');
                   
                    $item_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($itemledgerCurrentStock + $grv_qty);
                    $item_arr[$i]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + $company_loc) / $item_arr[$i]['currentStock']),wacDecimalPlaces);
                    $item_arr[$i]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + $company_rpt) / $item_arr[$i]['currentStock']),wacDecimalPlaces);
                    $itemledger_arr[$i]['documentID'] = $master['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $master['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $master['grvAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $master['grvPrimaryCode'];
                    $itemledger_arr[$i]['documentDate'] = $master['grvDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $master['grvDocRefNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $master['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $master['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $master['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $grvdetails[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $grvdetails[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOMID'] = $grvdetails[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['defaultUOM'] = $grvdetails[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOMID'] = $grvdetails[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['transactionUOM'] = $grvdetails[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = $grvdetails[$i]['receivedQty'];
                    $itemledger_arr[$i]['convertionRate'] = $grvdetails[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $grvdetails[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $grvdetails[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $grvdetails[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $grvdetails[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $grvdetails[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $grvdetails[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $grvdetails[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $grvdetails[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $grvdetails[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $grvdetails[$i]['BLType'];
                    $itemledger_arr[$i]['transactionAmount'] = round($grvdetails[$i]['fullTotalAmount'], $master['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $master['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalAmount'] = round($company_loc, $master['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item_arr[$i]['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingAmount'] = round($company_rpt, $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item_arr[$i]['companyReportingWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['partyCurrencyID'] = $master['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $master['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = round($supplier_cr, $master['supplierCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['confirmedYN'] = $master['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $master['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $master['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $master['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $master['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                    $itemledger_arr[$i]['segmentID'] = $master['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $master['segmentCode'];
                    $itemledger_arr[$i]['companyID'] = $master['companyID'];
                    $itemledger_arr[$i]['companyCode'] = $master['companyCode'];
                    $itemledger_arr[$i]['createdUserGroup'] = $master['createdUserGroup'];
                    $itemledger_arr[$i]['createdPCID'] = $master['createdPCID'];
                    $itemledger_arr[$i]['createdUserID'] = $master['createdUserID'];
                    $itemledger_arr[$i]['createdDateTime'] = $master['createdDateTime'];
                    $itemledger_arr[$i]['createdUserName'] = $master['createdUserName'];
                    $itemledger_arr[$i]['modifiedPCID'] = $master['modifiedPCID'];
                    $itemledger_arr[$i]['modifiedUserID'] = $master['modifiedUserID'];
                    $itemledger_arr[$i]['modifiedDateTime'] = $master['modifiedDateTime'];
                    $itemledger_arr[$i]['modifiedUserName'] = $master['modifiedUserName'];
                } elseif ($grvdetails[$i]['itemCategory'] == 'Fixed Assets') {
                    $this->load->library('Sequence_mobile');
                    $assat_data = array();
                    $assat_amount = ($grvdetails[$i]['fullTotalAmount'] / ($grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM']));
                    for ($a = 0; $a < ($grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM']); $a++) {
                        $assat_data[$a]['documentID'] = 'FA';
                        $assat_data[$a]['assetDescription'] = $item['itemDescription'];
                        // $assat_data[$a]['MANUFACTURE']                         = trim($this->input->post('MANUFACTURE') ?? '');
                        $assat_data[$a]['docOriginSystemCode'] = $system_code;
                        $assat_data[$a]['docOriginDetailID'] = $grvdetails[$i]['grvDetailsID'];
                        $assat_data[$a]['docOrigin'] = 'GRV';
                        $assat_data[$a]['dateAQ'] = $master['grvDate'];
                        $assat_data[$a]['grvAutoID'] = $system_code;
                        $assat_data[$a]['isFromGRV'] = 1;
                        $assat_data[$a]['comments'] = trim($comments);
                        $assat_data[$a]['faCatID'] = $item['subcategoryID'];
                        $assat_data[$a]['faSubCatID'] = $item['subSubCategoryID'];
                        $assat_data[$a]['faSubCatID2'] = null;
                        $assat_data[$a]['assetType'] = 1;
                        $assat_data[$a]['transactionAmount'] = $assat_amount;
                        $assat_data[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $assat_data[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $assat_data[$a]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                        $assat_data[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $assat_data[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $assat_data[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $assat_data[$a]['companyLocalAmount'] = round($assat_amount/$master['companyLocalExchangeRate'], $assat_data[$a]['transactionCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $assat_data[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $assat_data[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $assat_data[$a]['companyReportingAmount'] = round($assat_amount/$master['companyReportingExchangeRate'], $assat_data[$a]['companyLocalCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $assat_data[$a]['supplierID'] = $master['supplierID'];
                        $assat_data[$a]['segmentID'] = $master['segmentID'];
                        $assat_data[$a]['segmentCode'] = $master['segmentCode'];
                        $assat_data[$a]['companyID'] = $master['companyID'];
                        $assat_data[$a]['companyCode'] = $master['companyCode'];
                        $assat_data[$a]['createdUserGroup'] = $master['createdUserGroup'];
                        $assat_data[$a]['createdPCID'] = $master['createdPCID'];
                        $assat_data[$a]['createdUserID'] = $master['createdUserID'];
                        $assat_data[$a]['createdDateTime'] = $master['createdDateTime'];
                        $assat_data[$a]['createdUserName'] = $master['createdUserName'];
                        $assat_data[$a]['modifiedPCID'] = $master['modifiedPCID'];
                        $assat_data[$a]['modifiedUserID'] = $master['modifiedUserID'];
                        $assat_data[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                        $assat_data[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        $assat_data[$a]['costGLAutoID'] = $item['faCostGLAutoID'];
                        $assat_data[$a]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                        // $assat_data[$a]['ACCDEPGLCODE']                        = $item['modifiedUserName'];
                        // $assat_data[$a]['ACCDEPGLCODEdes']                     = $item['modifiedUserName'];
                        $assat_data[$a]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                        // $assat_data[$a]['DEPGLCODE']                           = $item['modifiedUserName'];
                        // $assat_data[$a]['DEPGLCODEdes']                        = $item['modifiedUserName'];
                        $assat_data[$a]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                        // $assat_data[$a]['DISPOGLCODE']                         = $item['modifiedUserName'];
                        // $assat_data[$a]['DISPOGLCODEdes']                      = $item['modifiedUserName'];
                        $assat_data[$a]['isPostToGL'] = 1;
                        $assat_data[$a]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                        $assat_data[$a]['postGLCode'] = $ACA['systemAccountCode'];
                        $assat_data[$a]['postGLCodeDes'] = $ACA['GLDescription'];
                        $assat_data[$a]['faCode'] = $this->sequence_mobile->sequence_generator("FA");
                    }

                    if (!empty($assat_data)) {
                        $assat_data = array_values($assat_data);
                        $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                    }
                }
            }

            if (!empty($item_arr)) {
                $item_arr = array_values($item_arr);
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_grv_data($system_code, 'GRV');

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['grvAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['grvPrimaryCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['grvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['grvDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['grvNarration'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $generalledger_arr = array_values($generalledger_arr);
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $this->db->select('supplierID,bookingCurrencyAmount,bookingCurrency,bookingCurrencyID,supplierCurrencyID ,bookingCurrencyExchangeRate, supplierCurrency ,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,companyLocalCurrency,companyLocalExchangeRate,companyReportingCurrency,companyReportingExchangeRate,total_amount,companyLocalCurrencyID,companyReportingCurrencyID');
            $this->db->from('srp_erp_grv_addon');
            $this->db->where('grvAutoID', $system_code);
            //$this->db->where('isChargeToExpense', 0);
            $this->db->where('companyID', current_companyID());
            $grv_addon_arr = $this->db->get()->result_array();
            $match_supplierinvoice_arr = array();
            for ($x = 0; $x < count($grv_addon_arr); $x++) {
                $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
                $match_supplierinvoice_arr[$x]['supplierID'] = $grv_addon_arr[$x]['supplierID'];
                $match_supplierinvoice_arr[$x]['bookingAmount'] = $grv_addon_arr[$x]['bookingCurrencyAmount'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $grv_addon_arr[$x]['bookingCurrencyID'];
                $match_supplierinvoice_arr[$x]['bookingCurrency'] = $grv_addon_arr[$x]['bookingCurrency'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $grv_addon_arr[$x]['bookingCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $grv_addon_arr[$x]['companyLocalCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $grv_addon_arr[$x]['companyLocalCurrency'];
                $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $grv_addon_arr[$x]['companyLocalExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $grv_addon_arr[$x]['companyReportingCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $grv_addon_arr[$x]['companyReportingCurrency'];
                $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $grv_addon_arr[$x]['companyReportingExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $grv_addon_arr[$x]['supplierCurrencyID'];
                $match_supplierinvoice_arr[$x]['supplierCurrency'] = $grv_addon_arr[$x]['supplierCurrency'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $grv_addon_arr[$x]['supplierCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $grv_addon_arr[$x]['supplierCurrencyDecimalPlaces'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
                $match_supplierinvoice_arr[$x]['isAddon'] = 1;
                $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
                $match_supplierinvoice_arr[$x]['companyID'] = current_companyID();

                $transaction_tot += $grv_addon_arr[$x]['total_amount'];
                $company_loc_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']);
                $company_rpt_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']);
                $supplier_cr_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']);
            }
            $x++;
            $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
            $match_supplierinvoice_arr[$x]['supplierID'] = $master['supplierID'];
            $match_supplierinvoice_arr[$x]['bookingAmount'] = $grvdetail_tot;
            $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $master['transactionCurrencyID'];
            $match_supplierinvoice_arr[$x]['bookingCurrency'] = $master['transactionCurrency'];
            $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $master['transactionExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyLocalExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyReportingExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $master['supplierCurrencyID'];
            $match_supplierinvoice_arr[$x]['supplierCurrency'] = $master['supplierCurrency'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
            $match_supplierinvoice_arr[$x]['isAddon'] = 0;
            $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
            $match_supplierinvoice_arr[$x]['companyID'] = current_companyID();

            if (!empty($match_supplierinvoice_arr)) {
                $this->db->insert_batch('srp_erp_match_supplierinvoice', $match_supplierinvoice_arr);
            }

            /*$data['approvedYN']             = 1;
            $data['approvedbyEmpID']        = current_userID();
            $data['approvedbyEmpName']      = current_user();
            $data['approvedDate']           = current_date();*/
            $data['companyLocalAmount'] = round($company_loc_tot, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyReportingAmount'] = round($company_rpt_tot, $master['companyReportingCurrencyDecimalPlaces']);
            $data['supplierCurrencyAmount'] = round($supplier_cr_tot, $master['supplierCurrencyDecimalPlaces']);
            $data['transactionAmount'] = round($transaction_tot, $master['transactionCurrencyDecimalPlaces']);

            $this->db->where('grvAutoID', $system_code);
            $this->db->update('srp_erp_grvmaster', $data);

            //$this->db->query("UPDATE srp_erp_purchaseordermaster prd JOIN ( SELECT purchaseOrderID AS pid,( CASE WHEN balance = 0 THEN '2' WHEN balance = requestedtqy THEN  '0' ELSE '1' END) AS sts FROM(SELECT purchaseOrderID,sum(po.requestedQty) AS requestedtqy,sum(gd.receivedQty) AS receivedqty,(sum(po.requestedQty) - sum(gd.receivedQty)) AS balance FROM srp_erp_purchaseorderdetails po INNER JOIN srp_erp_grvdetails gd ON po.purchaseOrderID = gd.purchaseOrderMastertID AND gd.companyID = {$company_id} WHERE po.companyID = {$company_id} GROUP BY purchaseOrderID) z) tt ON prd.purchaseOrderID = tt.pid SET prd.isReceived = tt.sts WHERE prd.companyID = {$company_id} and prd.isReceived!=2;");

            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
JOIN (
    SELECT
        purchaseOrderID AS pid,
        (
            CASE
            WHEN balance = 0 THEN
                '2'
            WHEN balance = requestedtqy THEN
                '0'
            ELSE
                '1'
            END
        ) AS sts
    FROM
        (
            SELECT
    t2.purchaseOrderID,
  sum(requestedtqy) as requestedtqy ,
    sum(balance) AS balance
FROM
    (
SELECT
            po.purchaseOrderDetailsID,
            purchaseOrderID,
            po.itemAutoID,
            ifnull((po.requestedQty),0) AS requestedtqy,
            (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty,
        IF (
            (
                (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
            ) < 0,
            0,
            (
                (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
            )
        ) AS balance
        FROM
            srp_erp_purchaseorderdetails po
        LEFT JOIN (
            SELECT
                purchaseOrderMastertID,
                ifnull(sum(requestedQty),0) AS receivedQty,
                itemAutoID,
                purchaseOrderDetailsID
            FROM
                srp_erp_paysupplierinvoicedetail
        left join srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                where sinm.invoiceType='Standard' and sinm.approvedYN=1
            GROUP BY
              srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
        ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

				LEFT JOIN (
            SELECT
                purchaseOrderMastertID,
                ifnull(sum(receivedQty),0) AS receivedQty,
                itemAutoID,
                purchaseOrderDetailsID
            FROM
                srp_erp_grvdetails
        left join srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                where grvm.grvType='PO Base' and grvm.approvedYN=1
            GROUP BY
              srp_erp_grvdetails.purchaseOrderDetailsID
        ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID

    ) t2 group by t2.purchaseOrderID
        ) z
) tt ON prd.purchaseOrderID = tt.pid
SET prd.isReceived = tt.sts
where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");

            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('grvAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID));

                }
            }
            $itemAutoIDarry = array();
            foreach ($grvdetails as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
            }
            $companyID = current_companyID();
            $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
            $exceededMatchID = 0;
            if (!empty($exceededitems_master)) {
                $this->load->library('Sequence_mobile');
                $exceededmatch['documentID'] = "EIM";
                $exceededmatch['documentDate'] = $master ['grvDate'];
                $exceededmatch['orginDocumentID'] = $master ['documentID'];
                $exceededmatch['orginDocumentMasterID'] = $master ['grvAutoID'];
                $exceededmatch['orginDocumentSystemCode'] = $master ['grvPrimaryCode'];
                $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                $exceededmatch['companyID'] = current_companyID();
                $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
                $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                $exceededmatch['FYBegin'] = $master ['FYBegin'];
                $exceededmatch['FYEnd'] = $master ['FYEnd'];
                $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];


                $exceededmatch['createdUserGroup'] = current_user_group();
                $exceededmatch['createdPCID'] = current_pc();
                $exceededmatch['createdUserID'] = current_userID();
                $exceededmatch['createdUserName'] = current_user();
                $exceededmatch['createdDateTime'] = current_date();
                $exceededmatch['documentSystemCode'] = $this->sequence_mobile->sequence_generator($exceededmatch['documentID']);
                $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                $exceededMatchID = $this->db->insert_id();
            }

            foreach ($grvdetails as $itemid) {
                $receivedQty = $itemid['receivedQty'];
                $receivedQtyConverted = $itemid['receivedQty'] / $itemid['conversionRateUOM'];
                $companyID = current_companyID();
                $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                $sumqty = array_column($exceededitems, 'balanceQty');
                $sumqty = array_sum($sumqty);
                if (!empty($exceededitems)) {
                    foreach ($exceededitems as $exceededItemAutoID) {
                        if ($receivedQtyConverted > 0) {
                            $balanceQty = $exceededItemAutoID['balanceQty'];
                            $updatedQty = $exceededItemAutoID['updatedQty'];
                            $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                            $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];

                            if ($receivedQtyConverted > $balanceQtyConverted) {
                                $qty = $receivedQty - $balanceQty;
                                $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                $receivedQty = $qty;
                                $receivedQtyConverted = $qtyconverted;
                                $exeed['balanceQty'] = 0;
                                //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                $this->db->update('srp_erp_itemexceeded', $exeed);

                                $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
                                $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                $exceededmatchdetail['createdPCID'] = current_pc();
                                $exceededmatchdetail['createdUserID'] = current_userID();
                                $exceededmatchdetail['createdUserName'] = current_user();
                                $exceededmatchdetail['createdDateTime'] = current_date();

                                $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                            } else {
                                $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                $this->db->update('srp_erp_itemexceeded', $exeed);

                                $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                $exceededmatchdetails['warehouseAutoID'] = $master['wareHouseAutoID'];
                                $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                $exceededmatchdetails['createdPCID'] = current_pc();
                                $exceededmatchdetails['createdUserID'] = current_userID();
                                $exceededmatchdetails['createdUserName'] = current_user();
                                $exceededmatchdetails['createdDateTime'] = current_date();
                                $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                $receivedQty = $receivedQty - $exeed['updatedQty'];
                                $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                            }
                        }
                    }
                }
            }
            if (!empty($exceededitems_master)) {
                exceed_double_entry($exceededMatchID);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Failed to Approve Good Receipt Voucher', 'email' => $approvals_status['email']);
            } else  if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Failed to approve partially', 'email' => $approvals_status['email']);
            }
            else  if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Good Receipt Voucher', 'email' => $approvals_status['email']);
            }

        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Good Receipt Voucher Approved Successfully!', 'email' => $approvals_status['email']);
            }else  if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Good Receipt Voucher partially approved!', 'email' => $approvals_status['email']);
            } 
            else  if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Good Receipt Voucher Rejected Successfully!', 'email' => $approvals_status['email']);
            }

        }
    }

    function save_sales_return_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('salesReturnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_id = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['salesReturnAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_id, $level_id, $status, $comments, 'SLR');
        }

        if ($approvals_status['status'] == 1) {
            $this->db->select('*');
            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->from('srp_erp_salesreturnmaster');
            $master = $this->db->get()->row_array();

            /* $this->db->select('*');
             $this->db->where('salesReturnAutoID', $system_id);
             $this->db->from('srp_erp_salesreturndetails');*/
            $qry = "SELECT *,SUM(return_Qty) as return_Qty FROM srp_erp_salesreturndetails WHERE salesReturnAutoID = $system_id GROUP BY itemAutoID,unitOfMeasureID";
            $detailTbl = $this->db->query($qry)->result_array();


            $this->db->trans_start();
            /**setup data for item master & item ledger */
            $i = 0;
            foreach ($detailTbl as $invDetail) {

                $itemAutoID = $invDetail['itemAutoID'];
                $decimal = $master['companyLocalCurrencyDecimalPlaces'];
                $item = fetch_item_data($itemAutoID);

                $wareHouseAutoID = $master['wareHouseAutoID'];
                $qty = $invDetail['return_Qty'] / $invDetail['conversionRateUOM'];
                $newStock = $item['currentStock'] + $qty;

                $this->db->select('*');
                $this->db->from('srp_erp_warehouseitems');
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('itemAutoID', $invDetail['itemAutoID']);
                $warehouseItem = $this->db->get()->row_array();
                $newStock_warehouse = $warehouseItem['currentStock'] + $qty;

                /** update warehouse stock */
                //$this->db->query("UPDATE srp_erp_warehouseitems SET currentStock =  '{$newStock}'  WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID='{$itemAutoID}'");

                /** WAC Calculation  */
                $this->load->model('Inventory_modal');
                $companyLocalWacAmount = $this->Inventory_modal->calculateNewWAC_salesReturn($item['currentStock'], $item['companyLocalWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);
                $companyReportingWacAmount = $this->Inventory_modal->calculateNewWAC_salesReturn($item['currentStock'], $item['companyReportingWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);

                /** warehouse item update data */
                $warehouseItemData[$i]['warehouseItemsAutoID'] = $warehouseItem['warehouseItemsAutoID'];
                $warehouseItemData[$i]['currentStock'] = $newStock_warehouse;

                /** Item master update data */
                $itemMaster[$i]['itemAutoID'] = $itemAutoID;
                $itemMaster[$i]['currentStock'] = $newStock;
                $itemMaster[$i]['companyLocalWacAmount'] = $companyLocalWacAmount;
                $itemMaster[$i]['companyReportingWacAmount'] = $companyReportingWacAmount;

                /** setup Item Ledger Data  */
                $itemLedgerData[$i]['documentID'] = $master['documentID'];
                $itemLedgerData[$i]['documentCode'] = $master['documentID'];
                $itemLedgerData[$i]['documentAutoID'] = $master['salesReturnAutoID'];
                $itemLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $itemLedgerData[$i]['documentDate'] = $master['returnDate'];
                $itemLedgerData[$i]['referenceNumber'] = $master['referenceNo'];
                $itemLedgerData[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                $itemLedgerData[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                $itemLedgerData[$i]['FYBegin'] = $master['FYBegin'];
                $itemLedgerData[$i]['FYEnd'] = $master['FYEnd'];
                $itemLedgerData[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                $itemLedgerData[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                $itemLedgerData[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                $itemLedgerData[$i]['wareHouseCode'] = $master['wareHouseCode'];
                $itemLedgerData[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                $itemLedgerData[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                $itemLedgerData[$i]['itemAutoID'] = $itemAutoID;
                $itemLedgerData[$i]['itemSystemCode'] = $invDetail['itemSystemCode'];
                $itemLedgerData[$i]['itemDescription'] = $invDetail['itemDescription'];
                $itemLedgerData[$i]['defaultUOMID'] = $invDetail['defaultUOMID'];
                $itemLedgerData[$i]['defaultUOM'] = $invDetail['defaultUOM'];
                $itemLedgerData[$i]['transactionUOMID'] = $invDetail['unitOfMeasureID'];
                $itemLedgerData[$i]['transactionUOM'] = $invDetail['unitOfMeasure'];
                $itemLedgerData[$i]['transactionQTY'] = $invDetail['return_Qty'];
                $itemLedgerData[$i]['convertionRate'] = $invDetail['conversionRateUOM'];
                $itemLedgerData[$i]['currentStock'] = $newStock;
                $itemLedgerData[$i]['PLGLAutoID'] = $item['costGLAutoID'];
                $itemLedgerData[$i]['PLSystemGLCode'] = $item['costSystemGLCode'];
                $itemLedgerData[$i]['PLGLCode'] = $item['costGLCode'];
                $itemLedgerData[$i]['PLDescription'] = $item['costDescription'];
                $itemLedgerData[$i]['PLType'] = $item['costType'];
                $itemLedgerData[$i]['BLGLAutoID'] = $item['assteGLAutoID'];
                $itemLedgerData[$i]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                $itemLedgerData[$i]['BLGLCode'] = $item['assteGLCode'];
                $itemLedgerData[$i]['BLDescription'] = $item['assteDescription'];
                $itemLedgerData[$i]['BLType'] = $item['assteType'];
                $itemLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                $itemLedgerData[$i]['transactionAmount'] = round((($invDetail['currentWacAmount'] / $ex_rate_wac) * ($itemLedgerData[$i]['transactionQTY'] / $invDetail['conversionRateUOM'])), $itemLedgerData[$i]['transactionCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['salesPrice'] = $invDetail["salesPrice"];
                $itemLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $itemLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $itemLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                $itemLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $itemLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $itemLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyLocalAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyLocalExchangeRate']), $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $itemLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $itemLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyReportingAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyReportingExchangeRate']), $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                $itemLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $itemLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $itemLedgerData[$i]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $itemLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['partyCurrencyAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['partyCurrencyExchangeRate']), $itemLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['confirmedYN'] = $master['confirmedYN'];
                $itemLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $itemLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $itemLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $itemLedgerData[$i]['approvedYN'] = $master['approvedYN'];
                $itemLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $itemLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $itemLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $itemLedgerData[$i]['segmentID'] = $invDetail['segmentID'];
                $itemLedgerData[$i]['segmentCode'] = $invDetail['segmentCode'];
                $itemLedgerData[$i]['companyID'] = $master['companyID'];
                $itemLedgerData[$i]['companyCode'] = $master['companyCode'];
                $itemLedgerData[$i]['createdUserGroup'] = $master['createdUserGroup'];
                $itemLedgerData[$i]['createdPCID'] = $master['createdPCID'];
                $itemLedgerData[$i]['createdUserID'] = $master['createdUserID'];
                $itemLedgerData[$i]['createdDateTime'] = $master['createdDateTime'];
                $itemLedgerData[$i]['createdUserName'] = $master['createdUserName'];
                $i++;
            }


            /** updating Item master new stock */
            if (!empty($itemMaster)) {
                $this->db->update_batch('srp_erp_itemmaster', $itemMaster, 'itemAutoID');
            }

            /** updating warehouse Item new stock */
            if (!empty($warehouseItemData)) {
                $this->db->update_batch('srp_erp_warehouseitems', $warehouseItemData, 'warehouseItemsAutoID');
            }

            /** updating Item Ledger */
            if (!empty($itemLedgerData)) {
                $this->db->insert_batch('srp_erp_itemledger', $itemLedgerData);
            }


            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sales_return_data($system_id, 'SLR');


            /**setup data for general Ledger  */
            $i = 0;

            foreach ($double_entry['GLEntries'] as $doubleEntry) {
                $generalLedgerData[$i]['documentMasterAutoID'] = $master['salesReturnAutoID'];
                $generalLedgerData[$i]['documentCode'] = $master['documentID'];
                $generalLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $generalLedgerData[$i]['documentDate'] = $master['returnDate'];
                $generalLedgerData[$i]['documentType'] = '';
                $generalLedgerData[$i]['documentYear'] = date("Y", strtotime($master['returnDate']));;
                $generalLedgerData[$i]['documentMonth'] = date("m", strtotime($master['returnDate']));
                $generalLedgerData[$i]['documentNarration'] = $master['comment'];
                $generalLedgerData[$i]['chequeNumber'] = '';
                $generalLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $generalLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $generalLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $generalLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $generalLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $generalLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $generalLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $generalLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['partyContractID'] = '';
                $generalLedgerData[$i]['partyType'] = 'CUS';
                $generalLedgerData[$i]['partyAutoID'] = $master['customerID'];
                $generalLedgerData[$i]['partySystemCode'] = $master['customerSystemCode'];
                $generalLedgerData[$i]['partyName'] = $master['customerName'];
                $generalLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $generalLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $generalLedgerData[$i]['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $generalLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $generalLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $generalLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $generalLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $generalLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $generalLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $generalLedgerData[$i]['companyID'] = $master['companyID'];
                $generalLedgerData[$i]['companyCode'] = $master['companyCode'];
                $amount = $doubleEntry['debit'];
                if ($doubleEntry['amountType'] == 'cr') {
                    $amount = ($doubleEntry['credit'] * -1);
                }

                $transactionAmount = $doubleEntry['transactionAmount'];

                $generalLedgerData[$i]['transactionAmount'] = round($transactionAmount, $doubleEntry['transactionDecimal']);
                $generalLedgerData[$i]['companyLocalAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyLocalExchangeRate']), $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['companyReportingAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyReportingExchangeRate']), $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['partyCurrencyAmount'] = round(($transactionAmount / $generalLedgerData[$i]['partyExchangeRate']), $generalLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['amount_type'] = $doubleEntry['amountType'];
                $generalLedgerData[$i]['documentDetailAutoID'] = $doubleEntry['auto_id'];
                $generalLedgerData[$i]['GLAutoID'] = $doubleEntry['GLAutoID'];
                $generalLedgerData[$i]['systemGLCode'] = $doubleEntry['SystemGLCode'];
                $generalLedgerData[$i]['GLCode'] = $doubleEntry['GLSecondaryCode'];
                $generalLedgerData[$i]['GLDescription'] = $doubleEntry['GLDescription'];
                $generalLedgerData[$i]['GLType'] = $doubleEntry['GLType'];
                $generalLedgerData[$i]['segmentID'] = $doubleEntry['segmentID'];
                $generalLedgerData[$i]['segmentCode'] = $doubleEntry['segmentCode'];
                $generalLedgerData[$i]['subLedgerType'] = $doubleEntry['subLedgerType'];
                $generalLedgerData[$i]['subLedgerDesc'] = $doubleEntry['subLedgerDesc'];
                $generalLedgerData[$i]['isAddon'] = 0;
                $generalLedgerData[$i]['createdUserGroup'] = current_user_group();
                $generalLedgerData[$i]['createdPCID'] = current_pc();
                $generalLedgerData[$i]['createdUserID'] = current_userID();
                $generalLedgerData[$i]['createdDateTime'] = current_date();
                $generalLedgerData[$i]['createdUserName'] = current_user();
                $i++;
            }

            if (!empty($generalLedgerData)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalLedgerData);
            }

            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = current_userID();
            $data['approvedbyEmpName'] = current_user();
            $data['approvedDate'] = current_date();

            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->update('srp_erp_salesreturnmaster', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Sales Return Approve Failed', 'email' => $approvals_status['email']);
            }else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Failed to Sales Return partially', 'email' => $approvals_status['email']);
            } 
            else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Sales Return', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Sales Return Approved Successfully!', 'email' => $approvals_status['email']);
            }else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Sales Return partially approved Successfully!', 'email' => $approvals_status['email']);
            } 
            else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Sales Return Rejected Successfully!', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_sc_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('salesCommisionID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['salesCommisionID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'SC');
        }

        if ($approvals_status['status'] == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_SC($system_code, 'SC');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['salesCommisionID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['salesCommisionCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['asOfDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['Description'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['referenceNo'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount / $double_entry['gl_detail'][$i]['partyExchangeRate']), $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Failed to Approve Sales Commission.', 'email' => $approvals_status['email']);
            } else   if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Failed to partially approve Sales Commission.', 'email' => $approvals_status['email']);
            } 
            else   if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Sales Commission.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Sales Commission Approved Successfully!', 'email' => $approvals_status['email']);
            } else   if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Sales Commission partially approved Successfully!', 'email' => $approvals_status['email']);
            } 
            else   if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Sales Commission Rejected Successfully!', 'email' => $approvals_status['email']);
            }

        }
    }

    function approve_delivery_order($autoappLevel=1)
    {
        if($autoappLevel==1) {
            $orderID = trim($this->input->post('orderAutoID') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $orderID = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        }

        if($status == 1) { /*validate if approval */
            $company_id = current_companyID();
            $item_low_qty = $this->db->query("SELECT ware_house.itemAutoID, ware_house.currentStock, SUM( detTB.deliveredQty / detTB.conversionRateUOM ) AS qty,
                               round(( ware_house.currentStock - SUM( detTB.deliveredQty / detTB.conversionRateUOM ) ),4) AS stock, detTB.wareHouseAutoID, 
                              itm_mas.itemSystemCode, itm_mas.itemDescription, ware_house.currentStock AS availableStock
                              FROM srp_erp_deliveryorderdetails AS detTB 
                              JOIN (
                                  SELECT SUM(transactionQTY/convertionRate) AS currentStock, wareHouseAutoID, itemAutoID 
                                  FROM srp_erp_itemledger WHERE companyID = {$company_id} GROUP BY wareHouseAutoID, itemAutoID
                              ) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID   
                              JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID AND detTB.wareHouseAutoID = ware_house.wareHouseAutoID
                              WHERE DOAutoID = {$orderID} AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                              GROUP BY itemAutoID
                              HAVING stock < 0")->result_array();

            if (!empty($item_low_qty)) {
                return array('status' => '0', 'data' => 'Some Item quantities are not sufficient to confirm this transaction.', 'email' => null);
//                die(json_encode(['e', '', 'in-suf-items' => $item_low_qty, 'in-suf-qty' => 'Y']));
            }
        }

        $this->load->library('Approvals_mobile');
        $approvals_status = $this->approvals_mobile->approve_document($orderID, $level_id, $status, $comments, 'DO');

        if ($approvals_status['status'] == 1) {
            $master = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=> $orderID])->row_array();
            $this->load->model('Delivery_order_model');
            $this->Delivery_order_model->update_item_ledger($orderID, $master);
            $this->Delivery_order_model->double_entry_delivery_order($orderID);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Error in Delivery order approval process.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Delivery order.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();

            switch ($approvals_status['status']){
                case 1: return array('status' => '1', 'data' => 'Delivery order fully approved.', 'email' => $approvals_status['email']); break;
                case 2: return array('status' => '1', 'data' => 'Delivery order level - '.$level_id.' successfully approved.', 'email' => $approvals_status['email']); break;
                case 3: return array('status' => '1', 'data' => 'Delivery order successfully rejected.', 'email' => $approvals_status['email']); break;
                case 3: return array('status' => '1', 'data' => 'Delivery order successfully rejected.', 'email' => $approvals_status['email']); break;
                case 5: return array('status' => '0', 'data' => 'Previous Level Approval Not Finished.', 'email' => $approvals_status['email']); break;
                default : return array('status' => '0', 'data' => 'Error in Delivery order approvals process.', 'email' => $approvals_status['email']); break;

                /*case 1: return ['s', 'Delivery order fully approved.']; break;
                case 2: return ['s', 'Delivery order level - '.$level_id.' successfully approved']; break;
                case 3: return ['s', 'Delivery order successfully rejected.']; break;
                case 5: return ['w', 'Previous Level Approval Not Finished']; break;
                default : return ['e', 'Error in Delivery order approvals process'];*/
            }
        }
    }

    function save_quotation_contract_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('contractAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
            $code = trim($this->input->post('code') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        $this->db->select('documentID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $system_code);
        $code = $this->db->get()->row('documentID');

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, $code);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Error in approvals process.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject document.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Document Approved Successfully!', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Document Rejected Successfully!', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_dn_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->load->library('Approvals_mobile');
        if($autoappLevel==1) {
            $system_id = trim($this->input->post('debitNoteMasterAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_id = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['debitNoteMasterAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_id, $level_id, $status, $comments, 'DN');
        }
        if ($approvals_status['status'] == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_debit_note_data($system_id, 'DN');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                // To get actual amount from debit note detail table
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'DN');
                $this->db->where('documentMasterAutoID', $system_id);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
//                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $companyID = current_companyID();
                    $ERGL_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'ERGL'")->row_array();

                    $ERGL = fetch_gl_account_desc($ERGL_ID['GLAutoID']);
                    //print_r($ERGL);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentType'] = '';
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['comments'];
                    $generalledger_arr['chequeNumber'] = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'SUP';
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['supplierID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['supplierName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID['GLAutoID'];
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|', current_default_segment());
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = current_user_group();
                    $generalledger_arr['createdPCID'] = current_pc();
                    $generalledger_arr['createdUserID'] = current_userID();
                    $generalledger_arr['createdDateTime'] = current_date();
                    $generalledger_arr['createdUserName'] = current_user();
                    $generalledger_arr['modifiedPCID'] = current_pc();
                    $generalledger_arr['modifiedUserID'] = current_userID();
                    $generalledger_arr['modifiedDateTime'] = current_date();
                    $generalledger_arr['modifiedUserName'] = current_user();
                    //print_r($generalledger_arr);
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }
//            $this->session->set_flashdata('s', 'Debit Note Approval Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Error in Approving Document!', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Error in partially approved Document!', 'email' => $approvals_status['email']);
            }
             else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Error in Rejecting Document!', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Document Approved Successfully!', 'email' => $approvals_status['email']);
            }else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Document partiallt Approved Successfully!', 'email' => $approvals_status['email']);
            }
            else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Document Rejected Successfully!', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_cn_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0){
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('creditNoteMasterAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['creditNoteMasterAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code,$level_id,$status,$comments,'CN');
        }

        if ($approvals_status['status'] ==1) {
            $this->load->model('Double_entry_model');
            $double_entry  = $this->Double_entry_model->fetch_double_entry_credit_note_data($system_code,'CN');
            for ($i=0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID']                      = $double_entry['master_data']['creditNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode']                              = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode']                        = $double_entry['master_data']['creditNoteCode'];
                $generalledger_arr[$i]['documentDate']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentType']                              = '';
                $generalledger_arr[$i]['documentYear']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentMonth']                             = date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                $generalledger_arr[$i]['documentNarration']                         = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber']                              = '';
                $generalledger_arr[$i]['transactionCurrency']                       = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID']                     = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate']                   =$double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']          = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency']                      = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID']                    = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate']                  = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']         = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency']                  = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID']                = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate']              = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']     = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID']                           = '';
                $generalledger_arr[$i]['partyType']                                 = 'CUS';
                $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['customerID'];
                $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['customerCode'];
                $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['customerName'];
                $generalledger_arr[$i]['partyCurrencyID']                           = $double_entry['master_data']['customerCurrencyID'];
                $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['customerCurrency'];
                $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID']                          = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName']                           = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate']                             = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate']                              = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID']                           = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName']                         = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID']                                 = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode']                               = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type']=='cr') {
                    $amount =($double_entry['gl_detail'][$i]['gl_cr']*-1);
                }
                $generalledger_arr[$i]['transactionAmount']                         = round($amount,$generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount']                        = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyLocalExchangeRate']),$generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount']                    = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyReportingExchangeRate']),$generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type']                               = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID']                      = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID']                                  = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode']                              = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode']                                    = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription']                             = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType']                                    = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID']                                 = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode']                               = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType']                             = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc']                             = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon']                                   = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup']                          = current_user_group();
                $generalledger_arr[$i]['createdPCID']                               = current_pc();
                $generalledger_arr[$i]['createdUserID']                             = current_userID();
                $generalledger_arr[$i]['createdDateTime']                           = current_date();
                $generalledger_arr[$i]['createdUserName']                           = current_user();
                $generalledger_arr[$i]['modifiedPCID']                              = current_pc();
                $generalledger_arr[$i]['modifiedUserID']                            = current_userID();
                $generalledger_arr[$i]['modifiedDateTime']                          = current_date();
                $generalledger_arr[$i]['modifiedUserName']                          = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentMasterAutoID',$system_code);
                $this->db->where('documentCode','CN');
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] !=0 or $totals['companyLocal_total'] !=0 or $totals['companyReporting_total'] !=0 or $totals['party_total'] !=0) {
                    $generalledger_arr = array();
//                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $companyID = current_companyID();
                    $ERGL_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'ERGL'")->row_array();

                    $ERGL = fetch_gl_account_desc($ERGL_ID['GLAutoID']);
                    $generalledger_arr['documentMasterAutoID']= $double_entry['master_data']['creditNoteMasterAutoID'];
                    $generalledger_arr['documentCode']        = $double_entry['code'];
                    $generalledger_arr['documentSystemCode']  = $double_entry['master_data']['creditNoteCode'];
                    $generalledger_arr['documentDate']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentType']        = '';
                    $generalledger_arr['documentYear']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentMonth']=date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                    $generalledger_arr['documentNarration']   = $double_entry['master_data']['docRefNo'];
                    $generalledger_arr['chequeNumber']        = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate']=$double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency']=$double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'CUS';
                    $generalledger_arr['partyAutoID']               = $double_entry['master_data']['customerID'];
                    $generalledger_arr['partySystemCode']           = $double_entry['master_data']['customerCode'];
                    $generalledger_arr['partyName']                 = $double_entry['master_data']['customerName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                    $generalledger_arr['partyCurrency']             = $double_entry['master_data']['customerCurrency'];
                    $generalledger_arr['partyExchangeRate']  = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces']=$double_entry['master_data']['customerCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total']* -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total']* -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total']* -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total']* -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID['GLAutoID'];
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|', current_default_segment());
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = current_user_group();
                    $generalledger_arr['createdPCID'] = current_pc();
                    $generalledger_arr['createdUserID'] = current_userID();
                    $generalledger_arr['createdDateTime'] = current_date();
                    $generalledger_arr['createdUserName'] = current_user();
                    $generalledger_arr['modifiedPCID'] = current_pc();
                    $generalledger_arr['modifiedUserID'] = current_userID();
                    $generalledger_arr['modifiedDateTime'] = current_date();
                    $generalledger_arr['modifiedUserName'] = current_user();
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }

//            $this->session->set_flashdata('s', 'Credit Note Approval Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Error in Approving Credit Note!', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Error in partially Approving Credit Note!', 'email' => $approvals_status['email']);
            }
            else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Error in Rejecting Credit Note!', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Credit Note Approved Successfully!', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Credit Note partially approved Successfully!', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Credit Note Rejected Successfully!', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_material_issue_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('itemIssueAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['itemIssueAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $this->db->select('wareHouseAutoID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();

        $this->db->select('(srp_erp_warehouseitems.currentStock-srp_erp_itemissuedetails.qtyIssued) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0');
        $items_arr = $this->db->get()->result_array();
        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'MI');
            }
            if ($approvals_status['status'] == 1) {
                $this->db->select('*,COALESCE(SUM(srp_erp_itemissuedetails.qtyIssued),0) AS qtyUpdatedIssued,COALESCE(SUM(srp_erp_itemissuedetails.totalValue),0) AS UpdatedTotalValue');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
                $this->db->join('srp_erp_itemissuemaster', 'srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID');
                $this->db->group_by('srp_erp_itemissuedetails.itemAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {
                        $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                        $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']));
                        $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                        $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                        $qty = ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']);
                        $itemSystemCode = $details_arr[$i]['itemAutoID'];
                        $location = $details_arr[$i]['wareHouseLocation'];
                        $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                        $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['itemIssueAutoID'];
                        $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['itemIssueCode'];
                        $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['issueDate'];
                        $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['issueRefNo'];
                        $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                        $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                        $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                        $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                        $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                        $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                        $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                        $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                        $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                        $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                        $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                        $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                        $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                        $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                        $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                        $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                        $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                        $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                        $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                        $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                        $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                        $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                        $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalWacAmount'] = round($details_arr[$i]['currentlWacAmount'], $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                        $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                        $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                        $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyReportingWacAmount'] = round(($itemledger_arr[$i]['companyLocalWacAmount'] / $itemledger_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                        $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                        $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                        $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                        $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                        $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                        $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                        $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                        $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                        $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                        $itemledger_arr[$i]['companyID'] = current_companyID();
                        $itemledger_arr[$i]['companyCode'] = current_companyCode();
                        $itemledger_arr[$i]['createdUserGroup'] = current_user_group();
                        $itemledger_arr[$i]['createdPCID'] = current_pc();
                        $itemledger_arr[$i]['createdUserID'] = current_userID();
                        $itemledger_arr[$i]['createdDateTime'] = current_date();
                        $itemledger_arr[$i]['createdUserName'] = current_user();
                        $itemledger_arr[$i]['modifiedPCID'] = current_pc();
                        $itemledger_arr[$i]['modifiedUserID'] = current_userID();
                        $itemledger_arr[$i]['modifiedDateTime'] = current_date();
                        $itemledger_arr[$i]['modifiedUserName'] = current_user();
                    }
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }

                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_material_issue_data($system_code, 'MI');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['itemIssueAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['itemIssueCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['issueType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['issueDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = 'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                    $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                    $generalledger_arr[$i]['createdPCID'] = current_pc();
                    $generalledger_arr[$i]['createdUserID'] = current_userID();
                    $generalledger_arr[$i]['createdDateTime'] = current_date();
                    $generalledger_arr[$i]['createdUserName'] = current_user();
                    $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                    $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                    $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                    $generalledger_arr[$i]['modifiedUserName'] = current_user();
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                if ($approvals_status['status'] == 1) {
                    return array('status' => '0', 'data' => 'Material Issue Approval Failed!', 'email' => $approvals_status['email']);
                } else if ($approvals_status['status'] == 2) {
                    return array('status' => '0', 'data' => 'Material Issue partially approve Failed!', 'email' => $approvals_status['email']);
                } 
                else if ($approvals_status['status'] == 3) {
                    return array('status' => '0', 'data' => 'Material Issue Rejection Failed!', 'email' => $approvals_status['email']);
                }
            } else {
                $this->db->trans_commit();
                if ($approvals_status['status'] == 1) {
                    return array('status' => '1', 'data' => 'Material Issue Approved Successfully!', 'email' => $approvals_status['email']);
                } else if ($approvals_status['status'] == 2) {
                    return array('status' => '1', 'data' => 'Material Issue Approved Successfully!', 'email' => $approvals_status['email']);
                }
    
                else if ($approvals_status['status'] == 3) {
                    return array('status' => '1', 'data' => 'Material Issue Rejected Successfully!', 'email' => $approvals_status['email']);
                }
            }
        } else {
            return array('status' => '0', 'data' => 'Item quantities are insufficient!', 'email' => null);
        }
    }

    function save_material_request_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('mrAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['mrAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'MR');
        }

        if (!empty($approvals_status['status'])) {
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Material Request Approved Successfully.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3){
                return array('status' => '1', 'data' => 'Material Request Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        } else {
            if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Material Request Approval Failed.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Failed to Reject Material Request.', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_material_receipt_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        $msg = '';
        if($autoappLevel==1){
            $system_code = trim($this->input->post('mrnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['mrnAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'MRN');
        }

        if ($approvals_status['status'] == 1) {

            $this->db->select('*');
            $this->db->where('mrnAutoID', $system_code);
            $this->db->from('srp_erp_materialreceiptmaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*,COALESCE(SUM(srp_erp_materialreceiptdetails.qtyReceived),0) AS qtyUpdatedReceived,COALESCE(SUM(srp_erp_materialreceiptdetails.totalValue),0) AS UpdatedTotalValue');
            $this->db->from('srp_erp_materialreceiptdetails');
            $this->db->where('srp_erp_materialreceiptdetails.mrnAutoID', $system_code);
            $this->db->join('srp_erp_materialreceiptmaster', 'srp_erp_materialreceiptmaster.mrnAutoID = srp_erp_materialreceiptdetails.mrnAutoID');
            $this->db->group_by('srp_erp_materialreceiptdetails.itemAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);


                    $itemledgerCurrentStock = fetch_itemledger_currentstock($details_arr[$i]['itemAutoID']);
                    $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'],'companyLocalExchangeRate');
                    $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'],'companyReportingExchangeRate');



                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($itemledgerCurrentStock + ($details_arr[$i]['qtyUpdatedReceived'] / $details_arr[$i]['conversionRateUOM']));
                    $qty = ($details_arr[$i]['qtyUpdatedReceived'] / $details_arr[$i]['conversionRateUOM']);

                    $localUnitCost = $details_arr[$i]['unitCost'] / $master['companyLocalExchangeRate'];
                    $reportingUnitCost = $details_arr[$i]['unitCost'] / $master['companyReportingExchangeRate'];

                    $item_arr[$i]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + ($localUnitCost * $qty)) / $item_arr[$i]['currentStock']), wacDecimalPlaces);
                    $item_arr[$i]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + ($reportingUnitCost * $qty)) / $item_arr[$i]['currentStock']), wacDecimalPlaces);

                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['wareHouseLocation'];
                    $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['mrnAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['mrnCode'];
                    $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['receivedDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['RefNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                    $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedReceived']);
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']));
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']));
                    $itemledger_arr[$i]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + ($localUnitCost * $qty)) / $item_arr[$i]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']));
                    $itemledger_arr[$i]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + ($reportingUnitCost * $qty)) / $item_arr[$i]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$i]['companyID'] = current_companyID();
                    $itemledger_arr[$i]['companyCode'] = current_companyCode();
                    $itemledger_arr[$i]['createdUserGroup'] = current_user_group();
                    $itemledger_arr[$i]['createdPCID'] = current_pc();
                    $itemledger_arr[$i]['createdUserID'] = current_userID();
                    $itemledger_arr[$i]['createdDateTime'] = current_date();
                    $itemledger_arr[$i]['createdUserName'] = current_user();
                    $itemledger_arr[$i]['modifiedPCID'] = current_pc();
                    $itemledger_arr[$i]['modifiedUserID'] = current_userID();
                    $itemledger_arr[$i]['modifiedDateTime'] = current_date();
                    $itemledger_arr[$i]['modifiedUserName'] = current_user();
                }
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_material_receipt_data($system_code, 'MRN');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['mrnAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['mrnCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['receivedDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['receiptType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['receivedDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['receivedDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'EMP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['employeeID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['employeeCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['employeeName'];
                $generalledger_arr[$i]['partyCurrencyID'] = '';//$double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_cr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'dr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_dr']);
                }
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $generalledger_arr[$i]['transactionAmount'] = round($amount * -1, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($amount * -1 / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($amount * -1 / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                } else {
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                }
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
            $msg = 'Material Receipt Approval Successful';

            $itemAutoIDarry = array();
            foreach ($details_arr as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
            }
            $companyID = current_companyID();
            $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
            $exceededMatchID = 0;
            if (!empty($exceededitems_master)) {
                $this->load->library('Sequence_mobile');
                $exceededmatch['documentID'] = "EIM";
                $exceededmatch['documentDate'] = $master ['receivedDate'];
                $exceededmatch['orginDocumentID'] = $master ['documentID'];
                $exceededmatch['orginDocumentMasterID'] = $master ['mrnAutoID'];
                $exceededmatch['orginDocumentSystemCode'] = $master ['mrnCode'];
                $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                $exceededmatch['companyID'] = current_companyID();
                $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                $exceededmatch['FYBegin'] = $master ['FYBegin'];
                $exceededmatch['FYEnd'] = $master ['FYEnd'];
                $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                $exceededmatch['createdUserGroup'] = current_user_group();
                $exceededmatch['createdPCID'] = current_pc();
                $exceededmatch['createdUserID'] = current_userID();
                $exceededmatch['createdUserName'] = current_user();
                $exceededmatch['createdDateTime'] = current_date();
                $exceededmatch['documentSystemCode'] = $this->sequence_mobile->sequence_generator($exceededmatch['documentID']);
                $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                $exceededMatchID = $this->db->insert_id();
            }

            foreach ($details_arr as $itemid) {
                $receivedQty = $itemid['qtyReceived'];
                $receivedQtyConverted = $itemid['qtyReceived'] / $itemid['conversionRateUOM'];
                $companyID = current_companyID();
                $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                $sumqty = array_column($exceededitems, 'balanceQty');
                $sumqty = array_sum($sumqty);
                if (!empty($exceededitems)) {
                    foreach ($exceededitems as $exceededItemAutoID) {
                        if ($receivedQtyConverted > 0) {
                            $balanceQty = $exceededItemAutoID['balanceQty'];
                            $updatedQty = $exceededItemAutoID['updatedQty'];
                            $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                            $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                            if ($receivedQtyConverted > $balanceQtyConverted) {
                                $qty = $receivedQty - $balanceQty;
                                $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                $receivedQty = $qty;
                                $receivedQtyConverted = $qtyconverted;
                                $exeed['balanceQty'] = 0;
                                //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                $this->db->update('srp_erp_itemexceeded', $exeed);

                                $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
                                $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                $exceededmatchdetail['createdPCID'] = current_pc();
                                $exceededmatchdetail['createdUserID'] = current_userID();
                                $exceededmatchdetail['createdUserName'] = current_user();
                                $exceededmatchdetail['createdDateTime'] = current_date();

                                $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                            } else {
                                $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                $this->db->update('srp_erp_itemexceeded', $exeed);

                                $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                $exceededmatchdetails['warehouseAutoID'] = $master['wareHouseAutoID'];
                                $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                $exceededmatchdetails['createdPCID'] = current_pc();
                                $exceededmatchdetails['createdUserID'] = current_userID();
                                $exceededmatchdetails['createdUserName'] = current_user();
                                $exceededmatchdetails['createdDateTime'] = current_date();
                                $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                $receivedQty = $receivedQty - $exeed['updatedQty'];
                                $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                            }
                        }
                    }
                }
            }
            if (!empty($exceededitems_master)) {
                exceed_double_entry($exceededMatchID);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Material Receipt Note Approval Failed.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Material Receipt Note partially Approval Failed.', 'email' => $approvals_status['email']);
            }
            else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Material Receipt Note.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Material Receipt Approved Successful', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Material Receipt Rejected Successful', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_stock_adjustment_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockAdjustmentAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockAdjustmentAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            if ($status == 1) {
                /*   $stockValidation = $this->minus_qty_validation($system_code);
                   if (empty($stockValidation)) {*/
                $approvals_status = $this->Approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'SA');
                /* }*//* else {
                    return array('error' => 'e', 'message' => 'Balance Qty cannot be less than 0', 'stock' => $stockValidation);
                }*/
            } else {
                $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'SA');
            }
        }

        if ($approvals_status['status'] == 1) {
            $this->db->select('*,srp_erp_stockadjustmentdetails.segmentID as segID,srp_erp_stockadjustmentdetails.segmentCode as segCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentAutoID', $system_code);
            $this->db->join('srp_erp_stockadjustmentmaster',
                'srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                $this->db->select('currentStock');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $details_arr[$i]['itemAutoID']);
                $prevItemMasterTotal = $this->db->get()->row_array();

                $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                $qty = $details_arr[$i]['adjustmentStock'];
                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                if ($details_arr[$i]['adjustmentType'] == 0) {
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] + $qty);
                } else {
                    $item_arr[$i]['currentStock'] = $prevItemMasterTotal['currentStock'];
                }
                $item_arr[$i]['companyLocalWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $details_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $item_arr[$i]['companyReportingWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $details_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                $location = $details_arr[$i]['wareHouseLocation'];
                $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                if ($details_arr[$i]['adjustmentType'] == 0) {
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = currentStock + {$details_arr[$i]['adjustmentWareHouseStock']}  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");
                }
                $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockAdjustmentAutoID'];
                $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockAdjustmentCode'];
                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['stockAdjustmentDate'];
                $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['transactionQTY'] = $qty;
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['totalValue'],
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];

                $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                /*$itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];*/
                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segCode'];
                $itemledger_arr[$i]['companyID'] = current_companyID();
                $itemledger_arr[$i]['companyCode'] = current_companyCode();
                $itemledger_arr[$i]['createdUserGroup'] = current_user_group();
                $itemledger_arr[$i]['createdPCID'] = current_pc();
                $itemledger_arr[$i]['createdUserID'] = current_userID();
                $itemledger_arr[$i]['createdDateTime'] = current_date();
                $itemledger_arr[$i]['createdUserName'] = current_user();
                $itemledger_arr[$i]['modifiedPCID'] = current_pc();
                $itemledger_arr[$i]['modifiedUserID'] = current_userID();
                $itemledger_arr[$i]['modifiedDateTime'] = current_date();
                $itemledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }
            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sa_data($system_code, 'SA');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockAdjustmentAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockAdjustmentCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m",
                    strtotime($double_entry['master_data']['stockAdjustmentDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                // $generalledger_arr[$i]['partyType']                                 = 'SUP';
                // $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['supplierID'];
                // $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['supplierSystemCode'];
                // $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['supplierName'];
                // $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['supplierCurrency'];
                // $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                // $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']),
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']*$generalledger_arr[$i]['partyExchangeRate']),4);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : NULL;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $maxLevel = $this->approvals_mobile->maxlevel('SA');

            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? TRUE : FALSE;
            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('stockAdjustmentAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }
                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp',
                        array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'SA'));

                }
            }
            $itemAutoIDarry = array();
            $ajststkarry = 0;
            foreach ($details_arr as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
                $ajststkarry += $value['adjustmentStock'];
            }
            $companyID = current_companyID();
            $this->db->select('*');
            $this->db->from('srp_erp_stockadjustmentmaster');
            $this->db->where('stockAdjustmentAutoID', trim($system_code));
            $master = $this->db->get()->row_array();
            if ($master['adjustmentType'] == 0 && $ajststkarry > 0) {
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;

                if (!empty($exceededitems_master)) {
                    $this->load->library('Sequence_mobile');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['stockAdjustmentDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockAdjustmentAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockAdjustmentCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = current_user_group();
                    $exceededmatch['createdPCID'] = current_pc();
                    $exceededmatch['createdUserID'] = current_userID();
                    $exceededmatch['createdUserName'] = current_user();
                    $exceededmatch['createdDateTime'] = current_date();
                    $exceededmatch['documentSystemCode'] = $this->sequence_mobile->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['adjustmentStock'];
                    $receivedQtyConverted = $itemid['adjustmentStock'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQtyConverted > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];

                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                    $exceededmatchdetail['createdPCID'] = current_pc();
                                    $exceededmatchdetail['createdUserID'] = current_userID();
                                    $exceededmatchdetail['createdUserName'] = current_user();
                                    $exceededmatchdetail['createdDateTime'] = current_date();

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $master['wareHouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                    $exceededmatchdetails['createdPCID'] = current_pc();
                                    $exceededmatchdetails['createdUserID'] = current_userID();
                                    $exceededmatchdetails['createdUserName'] = current_user();
                                    $exceededmatchdetails['createdDateTime'] = current_date();
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Stock adjustment Approval Failed.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Failed to partially approve Stock adjustment.', 'email' => $approvals_status['email']);
            } 
            
            else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Stock adjustment.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Stock adjustment Approved Successfully.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Stock adjustment partially Approved Successfully.', 'email' => $approvals_status['email']);
            } 
            else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Stock adjustment Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_stock_transfer_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $maxLevel = $this->approvals_mobile->maxlevel('ST');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        $this->db->select('from_wareHouseAutoID');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $master = $this->db->get()->row_array();

        $this->db->select('(srp_erp_warehouseitems.currentStock-srp_erp_stocktransferdetails.transfer_QTY) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['from_wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0');
        $items_arr = $this->db->get()->result_array();
        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'ST');
            }

            if ($approvals_status['status'] == 1) {
                $this->db->select('*');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
                $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                $x = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = $item['currentStock'];
                    $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $qty = ($details_arr[$i]['transfer_QTY'] / $details_arr[$i]['conversionRateUOM']);
                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['from_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty}) WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $location = $details_arr[$i]['to_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$x]['wareHouseAutoID'] = $details_arr[$i]['from_wareHouseAutoID'];
                    $itemledger_arr[$x]['wareHouseCode'] = $details_arr[$i]['form_wareHouseCode'];
                    $itemledger_arr[$x]['wareHouseLocation'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['wareHouseDescription'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['transactionQTY'] = ($qty * -1);
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['SUOMID'] = $details_arr[$i]['SUOMID'];
                    $itemledger_arr[$x]['SUOMQty'] = $details_arr[$i]['SUOMQty'];
                    $itemledger_arr[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['transactionAmount'] = (round($details_arr[$i]['totalValue'], $itemledger_arr[$x]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyLocalExchangeRate']), $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyReportingExchangeRate']), $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$x]['companyID'] = current_companyID();
                    $itemledger_arr[$x]['companyCode'] = current_companyCode();
                    $itemledger_arr[$x]['createdUserGroup'] = current_user_group();
                    $itemledger_arr[$x]['createdPCID'] = current_pc();
                    $itemledger_arr[$x]['createdUserID'] = current_userID();
                    $itemledger_arr[$x]['createdDateTime'] = current_date();
                    $itemledger_arr[$x]['createdUserName'] = current_user();
                    $itemledger_arr[$x]['modifiedPCID'] = current_pc();
                    $itemledger_arr[$x]['modifiedUserID'] = current_userID();
                    $itemledger_arr[$x]['modifiedDateTime'] = current_date();
                    $itemledger_arr[$x]['modifiedUserName'] = current_user();


                    $itemledger_arr_to[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr_to[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr_to[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr_to[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr_to[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr_to[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr_to[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr_to[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr_to[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr_to[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr_to[$x]['wareHouseAutoID'] = $details_arr[$i]['to_wareHouseAutoID'];
                    $itemledger_arr_to[$x]['wareHouseCode'] = $details_arr[$i]['to_wareHouseCode'];
                    $itemledger_arr_to[$x]['wareHouseLocation'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr_to[$x]['wareHouseDescription'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['transactionQTY'] = $qty;
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['SUOMID'] = $details_arr[$i]['SUOMID'];
                    $itemledger_arr_to[$x]['SUOMQty'] = $details_arr[$i]['SUOMQty'];
                    $itemledger_arr_to[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr_to[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr_to[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr_to[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr_to[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr_to[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr_to[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr_to[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr_to[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr_to[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr_to[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr_to[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['transactionAmount'] = round($details_arr[$i]['totalValue'], $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyLocalAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyLocalExchangeRate']), $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr_to[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr_to[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyReportingAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyReportingExchangeRate']), $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr_to[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr_to[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr_to[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr_to[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr_to[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr_to[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr_to[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr_to[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr_to[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr_to[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr_to[$x]['companyID'] = current_companyID();
                    $itemledger_arr_to[$x]['companyCode'] = current_companyCode();
                    $itemledger_arr_to[$x]['createdUserGroup'] = current_user_group();
                    $itemledger_arr_to[$x]['createdPCID'] = current_pc();
                    $itemledger_arr_to[$x]['createdUserID'] = current_userID();
                    $itemledger_arr_to[$x]['createdDateTime'] = current_date();
                    $itemledger_arr_to[$x]['createdUserName'] = current_user();
                    $itemledger_arr_to[$x]['modifiedPCID'] = current_pc();
                    $itemledger_arr_to[$x]['modifiedUserID'] = current_userID();
                    $itemledger_arr_to[$x]['modifiedDateTime'] = current_date();
                    $itemledger_arr_to[$x]['modifiedUserName'] = current_user();
                    $x++;
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }
                if (!empty($itemledger_arr_to)) {
                    $itemledger_arr_to = array_values($itemledger_arr_to);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr_to);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_stock_transfer_data($system_code, 'ST');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockTransferAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockTransferCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['itemType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['tranferDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = '';//'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = '';//$double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = '';//$double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = '';//$double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                    $generalledger_arr[$i]['createdPCID'] = current_pc();
                    $generalledger_arr[$i]['createdUserID'] = current_userID();
                    $generalledger_arr[$i]['createdDateTime'] = current_date();
                    $generalledger_arr[$i]['createdUserName'] = current_user();
                    $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                    $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                    $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                    $generalledger_arr[$i]['modifiedUserName'] = current_user();
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                /** update sub item master sub : shafry */
                if ($isFinalLevel) {
                    $masterID = $this->input->post('stockTransferAutoID');

                    $masterData = $this->db->query("SELECT  * FROM srp_erp_stocktransfermaster WHERE stockTransferAutoID = '" . $masterID . "'")->row_array();

                    $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_sub WHERE soldDocumentID = 'ST' AND isSold='1' AND soldDocumentAutoID = '" . $masterID . "'")->result_array();

                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $item) {
                            $result[$i]['receivedDocumentID'] = 'ST';
                            $result[$i]['receivedDocumentAutoID'] = $item['soldDocumentAutoID'];
                            $result[$i]['receivedDocumentDetailID'] = $item['soldDocumentDetailID'];
                            $result[$i]['isSold'] = null;
                            $result[$i]['soldDocumentID'] = null;
                            $result[$i]['soldDocumentDetailID'] = null;
                            $result[$i]['soldDocumentAutoID'] = null;

                            $result[$i]['wareHouseAutoID'] = $masterData['to_wareHouseAutoID'];

                            unset($result[$i]['subItemAutoID']);
                            $i++;
                        }


                        $this->db->insert_batch('srp_erp_itemmaster_sub', $result);

                    }
                }
                $itemAutoIDarry = array();
                foreach ($details_arr as $value) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('Sequence_mobile');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['tranferDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockTransferAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockTransferCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = current_user_group();
                    $exceededmatch['createdPCID'] = current_pc();
                    $exceededmatch['createdUserID'] = current_userID();
                    $exceededmatch['createdUserName'] = current_user();
                    $exceededmatch['createdDateTime'] = current_date();
                    $exceededmatch['documentSystemCode'] = $this->sequence_mobile->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['transfer_QTY'];
                    $receivedQtyConverted = $itemid['transfer_QTY'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQty > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = current_user_group();
                                    $exceededmatchdetail['createdPCID'] = current_pc();
                                    $exceededmatchdetail['createdUserID'] = current_userID();
                                    $exceededmatchdetail['createdUserName'] = current_user();
                                    $exceededmatchdetail['createdDateTime'] = current_date();

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = current_user_group();
                                    $exceededmatchdetails['createdPCID'] = current_pc();
                                    $exceededmatchdetails['createdUserID'] = current_userID();
                                    $exceededmatchdetails['createdUserName'] = current_user();
                                    $exceededmatchdetails['createdDateTime'] = current_date();
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
                //$this->session->set_flashdata('s', 'Stock Transfer Approval Successfully.');
            } /*else {
            $this->session->set_flashdata('s', 'Stock Transfer Approval : Level ' . $level_id . ' Successfully.');
        }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                if ($approvals_status['status'] == 1) {
                    return array('status' => '0', 'data' => 'Stock Transfer Approval Failed.', 'email' => $approvals_status['email']);
                } else  if ($approvals_status['status'] == 2) {
                    return array('status' => '1', 'data' => 'Stock Transfer partially Approval failed.', 'email' => $approvals_status['email']); 
                }
                else  if ($approvals_status['status'] == 3) {
                    return array('status' => '0', 'data' => 'Failed to Reject Stock Transfer.', 'email' => $approvals_status['email']);
                }
            } else {
                $this->db->trans_commit();
                if ($approvals_status['status'] == 1) {
                    return array('status' => '1', 'data' => 'Stock Transfer Approved Successfully.', 'email' => $approvals_status['email']);
                } else  if ($approvals_status['status'] == 2) {
                    return array('status' => '1', 'data' => 'Stock Transfer partially Approved Successfully.', 'email' => $approvals_status['email']);
                }else  if ($approvals_status['status'] == 3) {
                    return array('status' => '1', 'data' => 'Stock Transfer Rejected Successfully.', 'email' => $approvals_status['email']);
                }
            }
        } else {
            return array('status' => '0', 'data' => 'Item quantities are insufficient.', 'email' => null);
//            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function save_stock_return_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals_mobile');
        $this->load->library('wac');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockReturnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockReturnAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'SR');
        }
        if ($approvals_status['status'] == 1) {
            $this->db->select('*');
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->where('srp_erp_stockreturndetails.stockReturnAutoID', $system_code);
            $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $qty = ($details_arr[$i]['return_Qty'] / $details_arr[$i]['conversionRateUOM']);
                    $wacAmount = $this->wac->wac_calculation_amounts($details_arr[$i]['itemAutoID'], $details_arr[$i]['unitOfMeasure'], ($details_arr[$i]['return_Qty'] * -1), $details_arr[$i]['transactionCurrency'], $details_arr[$i]['currentlWacAmount']); //get Local and reporitng Amount
                    /*$item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] - $qty);
                    $item_arr[$i]['companyLocalWacAmount'] = $wacAmount["companyLocalWacAmount"];
                    $item_arr[$i]['companyReportingWacAmount'] = $wacAmount["companyReportingWacAmount"];*/

                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockReturnAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockReturnCode'];
                    $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['returnDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['return_Qty'] * -1);
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = ($item['currentStock'] - $qty);
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['transactionAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['transactionExchangeRate']), $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];

                    $itemledger_arr[$i]['partyCurrencyID'] = $details_arr[$i]['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $details_arr[$i]['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $details_arr[$i]['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['supplierCurrencyExchangeRate']), $details_arr[$i]['supplierCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $details_arr[$i]['supplierCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyID'] = current_companyID();
                    $itemledger_arr[$i]['companyCode'] = current_companyCode();
                    $itemledger_arr[$i]['createdUserGroup'] = current_user_group();
                    $itemledger_arr[$i]['createdPCID'] = current_pc();
                    $itemledger_arr[$i]['createdUserID'] = current_userID();
                    $itemledger_arr[$i]['createdDateTime'] = current_date();
                    $itemledger_arr[$i]['createdUserName'] = current_user();
                    $itemledger_arr[$i]['modifiedPCID'] = current_pc();
                    $itemledger_arr[$i]['modifiedUserID'] = current_userID();
                    $itemledger_arr[$i]['modifiedDateTime'] = current_date();
                    $itemledger_arr[$i]['modifiedUserName'] = current_user();
                }
            }

            /*if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }*/

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            //$data['approvedYN']             = $status;
            //$data['approvedbyEmpID']        = current_userID();
            //$data['approvedbyEmpName']      = current_user();
            //$data['approvedDate']           = current_date();
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            //$this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
            //$this->db->update('srp_erp_stockreturnmaster', $data);
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_stock_return_data($system_code, 'SR');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockReturnAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockReturnCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentType'] = 'Return';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['returnDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierSystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
                $generalledger_arr[$i]['createdPCID'] = current_pc();
                $generalledger_arr[$i]['createdUserID'] = current_userID();
                $generalledger_arr[$i]['createdDateTime'] = current_date();
                $generalledger_arr[$i]['createdUserName'] = current_user();
                $generalledger_arr[$i]['modifiedPCID'] = current_pc();
                $generalledger_arr[$i]['modifiedUserID'] = current_userID();
                $generalledger_arr[$i]['modifiedDateTime'] = current_date();
                $generalledger_arr[$i]['modifiedUserName'] = current_user();
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
//            $this->session->set_flashdata('s', 'Purchase Return Approved Successfully.');
        } /*else {
            $this->session->set_flashdata('s', 'Purchase Return Approval : Level ' . $level_id . ' Successfully.');
        }*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Purchase Return Approval Failed.', 'email' => $approvals_status['email']);
            }else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Purchase Return partially approval failed.', 'email' => $approvals_status['email']);
            }  
            else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Purchase Return.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Purchase Return Approved Successfully.', 'email' => $approvals_status['email']);
            }else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Purchase Return partially approved Successfully.', 'email' => $approvals_status['email']);
            } 
            else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Purchase Return Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_rjv_approval($autoappLevel = 1)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');

        if($autoappLevel == 1){
            $system_code = trim($this->input->post('RJVMasterAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        }
        $this->db->select('documentID');
        $this->db->from('srp_erp_recurringjvmaster');
        $this->db->where('RJVMasterAutoId', $system_code);
        $code = $this->db->get()->row('documentID');

        $approvals_status = $this->approvals_mobile->approve_document($system_code,$level_id, $status, $comments, $code);
//        if ($approvals_status['status'] == 1) {
            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            // $this->db->update('srp_erp_creditnotemaster', $data);
//            $this->session->set_flashdata('s', 'Approval Successfully.');
//        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Failed to Approve Recurring JV.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Reject Recurring JV.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Recurring JV Approved Successfully.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Recurring JV Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        }
    }

    function save_bank_rec_approval($autoappLevel = 1)
    {
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');

        if($autoappLevel == 1){
            $system_code = trim($this->input->post('bankRecAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        }

        $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'BRC');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Failed to Reject Bank Reconciliation.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Failed to Approve Bank Reconciliation.', 'email' => $approvals_status['email']);
            }
        } else {
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Bank Reconciliation Approved Successfully.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Bank Reconciliation Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        }
    }

    function confirm_bank_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $companyID = current_companyID();
        $this->db->trans_start();
        $this->load->library('Approvals_mobile');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('bankTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } elseif($autoappLevel == 2) {
            $system_code = trim($this->input->post('masterid') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['bankTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {

            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'BT');
        }

        if ($approvals_status['status'] == 1) {
            $this->load->model('Bank_rec_model');
            $master = $this->Bank_rec_model->bank_transfer_master($system_code);
            $date_format_policy = date_format_policy();
            $transferedDate = $master['transferedDate'];
            $master['transferedDate'] = input_format_date($transferedDate, $date_format_policy);
            $data['exchange'] = 1 / $master['exchangeRate'];
            $data = array(array('companyID' => $master['companyID'],
                'companyCode' => $master['companyCode'],
                'documentDate' => $master['transferedDate'],
                'transactionType' => 2,
                'documentType' => 'BT',
                'chequeNo' => $master['chequeNo'],
                'chequeDate' => $master['chequeDat'],
                'transactionCurrencyID' => $master['fromBankCurrencyID'],
                'transactionCurrency' => $master['fromcurrency'],
                'transactionExchangeRate' => 1,
                'transactionAmount' => $master['transferedAmount'],
                'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'bankCurrencyID' => $master['fromBankCurrencyID'],
                'bankCurrency' => $master['fromcurrency'],
                'bankCurrencyExchangeRate' => 1,
                'bankCurrencyAmount' => $master['transferedAmount'],
                'bankCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'memo' => $master['narration'],
                'bankName' => $master['bankfrom'],
                'bankGLAutoID' => $master['fromBankGLAutoID'],
                'bankSystemAccountCode' => $master['fromSystemAccountCode'],
                'bankGLSecondaryCode' => $master['fromGLSecondaryCode'],
                'documentMasterAutoID' => $master['bankTransferAutoID'],
                'documentSystemCode' => $master['bankTransferCode'],
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date(),
                'createdUserName' => current_user()),

                array('companyID' => $master['companyID'],
                    'companyCode' => $master['companyCode'],
                    'documentDate' => $master['transferedDate'],
                    'transactionType' => 1,
                    'documentType' => 'BT',
                    'chequeNo' => $master['chequeNo'],
                    'chequeDate' => $master['chequeDat'],
                    'transactionCurrencyID' => $master['fromBankCurrencyID'],
                    'transactionCurrency' => $master['fromcurrency'],
                    'transactionExchangeRate' => 1,
                    'transactionAmount' => $master['transferedAmount'],
                    'transactionCurrencyDecimalPlaces' => $master['toDecimalPlaces'],
                    'bankCurrencyID' => $master['toBankCurrencyID'],
                    'bankCurrency' => $master['tocurrency'],
                    'bankCurrencyExchangeRate' => $data['exchange'],
                    'bankCurrencyAmount' => $master['transferedAmount'] * $master['exchangeRate'],
                    'bankCurrencyDecimalPlaces' => $master['toDecimalPlaces'],
                    'memo' => $master['narration'],
                    'bankName' => $master['bankto'],
                    'bankGLAutoID' => $master['toBankGLAutoID'],
                    'bankSystemAccountCode' => $master['toCurrencySystemAccountCode'],
                    'bankGLSecondaryCode' => $master['toCurrencyGLSecondaryCode'],
                    'documentMasterAutoID' => $master['bankTransferAutoID'],
                    'documentSystemCode' => $master['bankTransferCode'],
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date(),
                    'createdUserName' => current_user()));
            $transferedDate = format_date($master['transferedDate']);
            $orderdate = explode('-', $transferedDate);
            $month = $orderdate[1];
            $year = $orderdate[0];
            $localdecimal = fetch_currency_desimal($master['companyLocalCurrency']);
            $reportingdecimal = fetch_currency_desimal($master['companyReportingCurrency']);
            //echo '<pre>';print_r($master); echo '</pre>'; die();
            /*localexchange*/
            /*if ($master['fromCurrency'] == $master['companyLocalCurrencyID']) {
              $companyLocalExchangeRate = 1 / $master['exchangeRate'];
            } else {
                $default_currency = currency_conversionID($master['fromCurrency'], $master['companyLocalCurrencyID']);
             $companyLocalExchangeRate = $default_currency['conversion'];
            }*/
            if ($master['companyLocalCurrencyID'] == $master['tocurrencyID']) {
                $companyLocalExchangeRate = 1 / $master['exchangeRate'];
            } else {
                $companyLocalExchangeRate = $master['companyLocalExchangeRate'];
            }

            if ($master['companyReportingCurrencyID'] == $master['tocurrencyID']) {
                $companyReportingexchangeRate = 1 / $master['exchangeRate'];
            } else {
                $companyReportingexchangeRate = $master['companyReportingExchangeRate'];
            }

            /*reporting Exchange*/
            /*if ($master['fromCurrency'] == $master['companyReportingCurrencyID']) {
                $companyReportingexchangeRate = 1 / $master['exchangeRate'];
            } else {
                $report = currency_conversionID($master['fromcurrencyID'], $master['companyReportingCurrencyID']);
                $companyReportingexchangeRate = $report['conversion'];
            }*/

            $data2 = array(array('documentCode' => 'BT',
                'documentMasterAutoID' => $master['bankTransferAutoID'],
                'documentSystemCode' => $master['bankTransferCode'],
                'documentType' => 'BT',
                'documentDate' => $master['transferedDate'],
                'documentYear' => $year,
                'documentMonth' => $month,
                'documentNarration' => $master['narration'],
                'GLAutoID' => $master['fromBankGLAutoID'],
                'systemGLCode' => $master['fromSystemAccountCode'],
                'GLCode' => $master['fromGLSecondaryCode'],
                'GLDescription' => $master['fromGLDescription'],
                'GLType' => $master['fromSubCategory'],
                'amount_type' => 'cr',
                'transactionCurrencyID' => $master['fromBankCurrencyID'],
                'transactionCurrency' => $master['fromcurrency'],
                'transactionExchangeRate' => 1,
                'transactionAmount' => -1 * abs($master['transferedAmount']),
                'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'companyLocalCurrencyID' => $master['companyLocalCurrencyID'],
                'companyLocalCurrency' => $master['companyLocalCurrency'],
                'companyLocalExchangeRate' => $companyLocalExchangeRate,
                'companyLocalAmount' => -1 * abs($master['transferedAmount'] / $companyLocalExchangeRate),
                'companyLocalCurrencyDecimalPlaces' => $localdecimal,
                'companyReportingCurrencyID' => $master['companyReportingCurrencyID'],
                'companyReportingCurrency' => $master['companyReportingCurrency'],
                'companyReportingExchangeRate' => $companyReportingexchangeRate,
                'companyReportingAmount' => -1 * abs($master['transferedAmount'] / $companyReportingexchangeRate),
                'companyReportingCurrencyDecimalPlaces' => $reportingdecimal,
                'confirmedByEmpID' => $master['confirmedByEmpID'],
                'confirmedByName' => $master['confirmedByName'],
                'confirmedDate' => $master['confirmedDate'],
                'approvedDate' => current_date(),
                'approvedbyEmpID' => current_userID(),
                'approvedbyEmpName' => current_user(),
                'companyID' => $master['companyID'],
                'companyCode' => $master['companyCode'],
                'createdUserGroup' => current_user_group(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date(),
                'createdUserName' => current_user()),

                array('documentCode' => 'BT',
                    'documentMasterAutoID' => $master['bankTransferAutoID'],
                    'documentSystemCode' => $master['bankTransferCode'],
                    'documentType' => 'BT',
                    'documentDate' => $master['transferedDate'],
                    'documentYear' => $year,
                    'documentMonth' => $month,
                    'documentNarration' => $master['narration'],
                    'GLAutoID' => $master['toBankGLAutoID'],
                    'systemGLCode' => $master['toCurrencySystemAccountCode'],
                    'GLCode' => $master['toCurrencyGLSecondaryCode'],
                    'GLDescription' => $master['toGLDescription'],
                    'GLType' => $master['toSubCategory'],
                    'amount_type' => 'dr',
                    'transactionCurrencyID' => $master['fromBankCurrencyID'],
                    'transactionCurrency' => $master['fromcurrency'],
                    'transactionExchangeRate' => 1,
                    'transactionAmount' => $master['transferedAmount'],
                    'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                    'companyLocalCurrencyID' => $master['companyLocalCurrencyID'],
                    'companyLocalCurrency' => $master['companyLocalCurrency'],
                    'companyLocalExchangeRate' => $companyLocalExchangeRate,
                    'companyLocalAmount' => $master['transferedAmount'] / $companyLocalExchangeRate,
                    'companyLocalCurrencyDecimalPlaces' => $localdecimal,
                    'companyReportingCurrencyID' => $master['companyReportingCurrencyID'],
                    'companyReportingCurrency' => $master['companyReportingCurrency'],
                    'companyReportingExchangeRate' => $companyReportingexchangeRate,
                    'companyReportingAmount' => $master['transferedAmount'] / $companyReportingexchangeRate,
                    'companyReportingCurrencyDecimalPlaces' => $reportingdecimal,
                    'confirmedByEmpID' => $master['confirmedByEmpID'],
                    'confirmedByName' => $master['confirmedByName'],
                    'confirmedDate' => $master['confirmedDate'],
                    'approvedDate' => current_date(),
                    'approvedbyEmpID' => current_userID(),
                    'approvedbyEmpName' => current_user(),
                    'companyID' => $master['companyID'],
                    'companyCode' => $master['companyCode'],
                    'createdUserGroup' => current_user_group(),
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date(),
                    'createdUserName' => current_user()));

            $approvals_status1 = $this->approvals_mobile->approve_document($system_code, $level_id, $status, $comments, 'BT');

            $levelNo = $this->db->query("select max(levelNo) as levelNo from srp_erp_approvalusers WHERE Status=1 AND companyID={$companyID} AND documentID='BT'  ")->row_array();
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($approvals_status['status'] == 1) {
                return array('status' => '0', 'data' => 'Error in Approving Bank Transfer.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '0', 'data' => 'Error in Approving Bank Transfer.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 3) {
                return array('status' => '0', 'data' => 'Error in Rejecting Bank Transfer.', 'email' => $approvals_status['email']);
            }
        } else {

            if ($status == 1) {
                if($levelNo['levelNo']==$level_id){
                    $this->db->insert_batch('srp_erp_bankledger', $data);
                    $this->db->insert_batch('srp_erp_generalledger', $data2);
                }
            }
            $this->db->trans_commit();
            if ($approvals_status['status'] == 1) {
                return array('status' => '1', 'data' => 'Bank Transfer Approved Successfully.', 'email' => $approvals_status['email']);
            } else if ($approvals_status['status'] == 2) {
                return array('status' => '1', 'data' => 'Bank Transfer partially approved Successfully.', 'email' => $approvals_status['email']);
            }
            else if ($approvals_status['status'] == 3) {
                return array('status' => '1', 'data' => 'Bank Transfer Rejected Successfully.', 'email' => $approvals_status['email']);
            }
        }
    }

    function leave_cancellation_approval()
    {
        $companyID = current_companyID();
        $current_userID = current_userID();
        $email = array();

        $status = $this->input->post('status');
        $level = $this->input->post('level');
        $comments = $this->input->post('comment');
        $leaveMasterID = $this->input->post('masterid');

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

                array_push($email, $mailData);
                return array('status' => '1', 'data' => 'Leave cancellation refer backed successfully.', 'email' => $email);
            } else {
                $this->db->trans_rollback();
                return array('status' => '0', 'data' => 'Leave cancellation refer backed Failed.', 'email' => []);
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
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => current_user(),
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

                $email = array();
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

                    array_push($email, $mailData);
                }
                return array('status' => '1', 'data' => 'Leave cancellation Approved successfully.', 'email' => $email);

            } else {
                $this->db->trans_rollback();
                return array('status' => '0', 'data' => 'Failed to Approve Leave cancellation.', 'email' => []);
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
            $email = array();
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

                array_push($email, $mailData);
                return array('status' => '0', 'data' => 'Leave cancellation Approved successfully.', 'email' => $email);
            } else {
                return array('status' => '0', 'data' => 'Failed to Approve Leave cancellation.', 'email' => $email);
            }
        }
    }

    function create_leave_accrual($leave)
    {
        $accDet = [];
        $leaveMasterID = $leave['leaveMasterID'];
        $daysEntitle = $leave['days'];
        $period = $leave['startDate'];
        $d = explode('-', $period);
        $description = 'Leave Accrual for leave cancellation ';
        $comment = $description . ' - ' . $leave['documentCode'];
        $leaveGroupID = $leave['leaveGroupID'];
        $policyMasterID = $leave['policyMasterID'];
        $this->load->library('Sequence_mobile');
        $code = $this->sequence_mobile->sequence_generator('LAM');


        $accMaster = [
            'companyID' => current_companyID(),
            'leaveaccrualMasterCode' => $code,
            'documentID' => 'LAM',
            'cancelledLeaveMasterID' => $leaveMasterID,
            'description' => $comment,
            'year' => $d[0],
            'month' => $d[1],
            'leaveGroupID' => $leaveGroupID,
            'policyMasterID' => $policyMasterID,
            'createdUserGroup' => current_user_group(),
            'createDate' => current_date(),
            'createdpc' => current_pc(),
            'confirmedYN' => 1,
            'confirmedby' => current_userID(),
            'confirmedDate' => current_date(),
        ];


        $this->db->insert('srp_erp_leaveaccrualmaster', $accMaster);


        $accDet['leaveaccrualMasterID'] = $this->db->insert_id();
        $accDet['cancelledLeaveMasterID'] = $leaveMasterID;
        $accDet['empID'] = $leave['empID'];
        $accDet['comment'] = '';
        $accDet['leaveGroupID'] = $leaveGroupID;
        $accDet['leaveType'] = $leave['leaveTypeID'];
        $accDet['daysEntitled'] = $daysEntitle;
        $accDet['comment'] = $comment;
        $accDet['description'] = $description;
        $accDet['leaveMasterID'] = $leaveMasterID;
        $accDet['createDate'] = current_date();
        $accDet['createdUserGroup'] = current_user_group();
        $accDet['createdPCid'] = current_pc();

        $this->db->insert('srp_erp_leaveaccrualdetail', $accDet);

        return 1;
    }
}
