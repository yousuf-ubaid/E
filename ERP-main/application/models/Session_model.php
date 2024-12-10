<?php

class Session_model extends CI_Model
{

    function Session_model()
    {
        parent:: __construct();
    }

    function authenticateLogin($login_data, $token=null)
    {
        $this->db->select('EIdNo,NoOfLoginAttempt,isDischarged,isActive,Ename2,Erp_companyID');
        $this->db->where("UserName", $login_data['userN']);
        if($token == null){
            $this->db->where("Password", $login_data['passW']);
        }
        $result = $this->db->get("srp_employeesdetails")->row_array();

        if (!is_array($result)) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Account not found";
            return $data;
        }

        if ($result['isDischarged'] == 0 && $result['isActive'] == 1) {
            if ($result['NoOfLoginAttempt'] == 4) {
                $data['stats'] = False;
                $data['type'] = "error";
                $data['message'] = "Your account has been blocked please contact support team";
                return $data;
            }
            else {
                if (!empty($result)) {
                    $data['NoOfLoginAttempt'] = 0;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $this->db->update('srp_employeesdetails', $data);
                    return array('stats' => True, 'data' => md5($result['EIdNo']));
                }
                else {
                    $this->db->select('EIdNo,NoOfLoginAttempt');
                    $this->db->where("UserName", $login_data['userN']);
                    $getusrName = $this->db->get("srp_employeesdetails")->row_array();
                    if (!empty($getusrName)) {
                        $noOfAttemps = $getusrName['NoOfLoginAttempt'] + 1;
                        if ($getusrName['NoOfLoginAttempt'] == 4) {
                            $data['stats'] = False;
                            $data['type'] = "error";
                            $data['message'] = "Your account has been blocked please contact support team";
                            return $data;
                        }
                        else if ($getusrName['NoOfLoginAttempt'] == 2) {
                            $datas['NoOfLoginAttempt'] = $noOfAttemps;
                            $this->db->where('EIdNo', $getusrName['EIdNo']);
                            $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                            if ($updateAttempt) {
                                $data['stats'] = False;
                                $data['type'] = "error";
                                $data['message'] = "Invalid username or password. <br/><strong><i class='fa fa-exclamation-triangle'></i> You have one more attempt.<strong>";
                                return $data;
                            }
                        }
                        else {
                            $datas['NoOfLoginAttempt'] = $noOfAttemps;
                            $this->db->where('EIdNo', $getusrName['EIdNo']);
                            $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                            if ($updateAttempt) {
                                $data['stats'] = False;
                                $data['type'] = "error";
                                $data['message'] = "Invalid username or password. Please  try again.";
                                return $data;
                            }
                        }
                    }else {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = " Wrong user name or password. Please  try again.";
                        return $data;
                    }
                }
            }
        }else if ($result['isActive'] == 0) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Your account is not activated";
            return $data;
        }  else {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Access Denied";
            return $data;
        }

    }

    function createSession($employee_code,$isGroupUser=0)
    {
        if ($employee_code != 'logout') {
            $this->db->select('company_id,company_name,company_logo,UserName,Ename1,Ename2,Ename3,Ename4,serialNo,EIdNo,company_code ,branchID,SchMasterId,EmpImage,Gender, ECode,EmpShortCode,srp_employeesdetails.languageID as languageID,srp_employeesdetails.locationID as locationIDemp,srp_employeesdetails.userType as userType,company_secondary_logo');
            $this->db->where("md5(EIdNo)", $employee_code);
            $this->db->where("srp_erp_company.confirmedYN", 1);
            $this->db->from('srp_employeesdetails');
            $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_employeesdetails.Erp_companyID', 'left');
            $user_master_data = $this->db->get()->row_array();

            if ($user_master_data) {

                $db2 = $this->load->database('db2', TRUE);
                $companyID = $user_master_data['company_id'];
                $supportToken = $db2->query("SELECT supportToken FROM `srp_erp_company` WHERE company_id = $companyID")->row('supportToken');

                $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(
                    array('userID' => $user_master_data['EIdNo'], 'companyID' => $user_master_data['company_id'], 'isActive' => 1)
                )->get()->row('wareHouseID');

                $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
                if ($imagePath_arr['isLocalPath'] == 1) {
                    $imagePath = base_url() . 'images/users/';
                } else { // FOR SRP ERP USERS
                    $imagePath = $imagePath_arr['imagePath'];
                }

                $emp_image = $user_master_data['EmpImage'];

                $this->load->library('s3');
                if (!empty($emp_image)) {
                    $emp_image = $this->s3->createPresignedRequest($emp_image);
                } else {
                    $emp_image = ($user_master_data['Gender'] == 1) ? 'male' : 'female';
                    $emp_image = $this->s3->createPresignedRequest("images/users/{$emp_image}.png");
                }

                $session_data = array(
                    'empID' => $user_master_data['EIdNo'],
                    'empCode' => $user_master_data['ECode'],
                    'username' => $user_master_data['Ename2'],
                    'loginusername' => $user_master_data['UserName'],
                    'companyID' => $user_master_data['company_id'],
                    'EmpShortCode' => $user_master_data['EmpShortCode'],
                    'companyType' => 1,
                    'company_link_id' => $user_master_data['SchMasterId'],
                    'branchID' => $user_master_data['branchID'],
                    'usergroupID' => $user_master_data['branchID'],
                    'ware_houseID' => $wareHouseID,
                    'empImage' => $emp_image,
                    'imagePath' => $imagePath,
                    'company_code' => $user_master_data['company_code'],
                    'company_name' => $user_master_data['company_name'],
                    'company_logo' => $user_master_data['company_logo'],
                    'company_secondary_logo' => $user_master_data['company_secondary_logo'],
                    'emplangid' => $user_master_data['languageID'],
                    'emplanglocationid' => $user_master_data['locationIDemp'],
                    'isGroupUser' => $isGroupUser,
                    'userType' => $user_master_data['userType'],
                    'status' => TRUE,
                    'supportToken' => $supportToken

                );

                $this->session->set_userdata($session_data);
                $subscription_status = $this->check_subscription_status($user_master_data['company_id']);
                if($subscription_status[0] == 'e'){
                    $this->session->unset_userdata('status' );
                    return ['stats'=> FALSE, 'message' => $subscription_status[1]];
                }

                $data['stats'] = TRUE;
                return $data;
            }
            else {
                $data['stats'] = FALSE;
                $data['type'] = "info";
                $data['message'] = "Current User From the System";
                return $data;
            }
        }
    }

    function check_subscription_status($company_id, $checkOnNavigation=null){
        //return ['s'];
        $db2 = $this->load->database('db2', TRUE);

        $subscription_st = $db2->get_where('srp_erp_company', ['company_id' => $company_id])->row('isSubscriptionDisabled');
        if($subscription_st > 0){
            $st = ($subscription_st == 1)? 'not activated': 'on hold';
            return ['e', "Your subscription is {$st}.", 'error_type'=> $subscription_st];
        }

        $isExpired = $db2->query("SELECT inv_mas.invID FROM subscription_invoice_master AS inv_mas 
                                JOIN subscription_invoice_details AS inv_det ON inv_mas.invID = inv_det.invID
                                JOIN system_invoice_item_type itm_type ON itm_type.type_id = inv_det.itemID 
                                JOIN companysubscriptionhistory AS sub ON sub.subscriptionID = inv_mas.subscriptionID
                                JOIN srp_erp_company AS com_tb ON com_tb .company_id = inv_mas.companyID 
                                WHERE inv_mas.companyID = {$company_id} AND inv_det.itemID = 1 AND inv_mas.isAmountPaid = 0 
                                AND dueDate < CURRENT_DATE AND com_tb.paymentEnabled = 1")->row('invID');

        if(!empty($isExpired)){
            return ['e', 'Your subscription has expired.', 'error_type'=> 'expired'];
        }

        if($checkOnNavigation == 1){
            return ['s'];
        }
        /********************************************************************
         * Expiry message start to show two month before due date
         * DATE_ADD(dueDate, INTERVAL -2 MONTH) <= CURRENT_DAT
         ********************************************************************
         */
        $history_data = $db2->query("SELECT inv_mas.invID, sub.subscriptionStartDate AS expiry, dueDate
                                        FROM subscription_invoice_master AS inv_mas 
                                        JOIN subscription_invoice_details AS inv_det ON inv_mas.invID = inv_det.invID
                                        JOIN system_invoice_item_type itm_type ON itm_type.type_id = inv_det.itemID 
                                        JOIN companysubscriptionhistory AS sub ON sub.subscriptionID = inv_mas.subscriptionID
                                        JOIN srp_erp_company AS com_tb ON com_tb .company_id = inv_mas.companyID 
                                        WHERE inv_mas.companyID = {$company_id} AND com_tb.paymentEnabled = 1 AND inv_det.itemID = 1 
                                        AND inv_mas.isAmountPaid = 0 AND sub.subscriptionStartDate < CURRENT_DATE
                                        AND DATE_ADD(dueDate, INTERVAL -2 MONTH) <= CURRENT_DATE                                         
                                        ORDER BY dueDate ASC LIMIT 1")->row_array();

        if( !empty($history_data) ){

            $subscription_dates = ['expiry'=>$history_data['expiry'], 'due'=>$history_data['dueDate']];

            $this->session->set_userdata( ['subscription_expire_notification'=> 1] );
            $this->session->set_userdata( ['subscription_dates'=> $subscription_dates] );
        }

        return ['s'];
    }

    function fetch_company_detail($com, $bran)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_company');
        $this->db->where('company_id', $com);
        return $this->db->get()->row_array();
    }

    function fetch_companycontrolaccounts($companyID, $company_code)
    {
        $this->load->library('sequence');
        $this->db->SELECT("controlAccountsAutoID,controlAccountType,srp_erp_chartofaccounts.GLAutoID");
        $this->db->where('srp_erp_chartofaccounts.companyID', $companyID);
        $this->db->where('srp_erp_companycontrolaccounts.companyID', $companyID);
        $this->db->FROM('srp_erp_chartofaccounts');
        $this->db->join('srp_erp_companycontrolaccounts', 'srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID');
        $control_account = $this->db->get()->result_array();
        $data = [];
        foreach ($control_account as $row) {
            $data[$row['controlAccountType']] = $row['GLAutoID'];
        }
        return $data;
    }


    function fetch_company_policy($com)
    {
        //get company policy
        $Companypolicy = $this->db->query("SELECT
	srp_erp_companypolicymaster.companypolicymasterID,
	companyPolicyDescription,
	srp_erp_companypolicymaster.code,
	IFNULL(cp.documentID,'All') as documentID,

IF (
	cp.`value` IS NULL,
	srp_erp_companypolicymaster.defaultValue,
	cp.`value`
) AS policyvalue
FROM
srp_erp_companypolicymaster
LEFT JOIN
 (SELECT * FROM srp_erp_companypolicy WHERE srp_erp_companypolicy.companyID = " . $com . ") cp ON(cp.companypolicymasterID = srp_erp_companypolicymaster.companypolicymasterID);")->result_array();
        $data = array_group_by($Companypolicy, 'code', 'documentID');
        return $data;
    }

    function fetch_group_policy($com)
    {//get company policy

        $Companypolicy = $this->db->query("SELECT
	srp_erp_grouppolicymaster.groupPolicymasterID,
	groupPolicyDescription,
	srp_erp_grouppolicymaster.code,
	IFNULL(cp.documentID,'All') as documentID,

IF (
	cp.`value` IS NULL,
	srp_erp_grouppolicymaster.defaultValue,
	cp.`value`
) AS policyvalue
FROM
srp_erp_grouppolicymaster
LEFT JOIN
 (SELECT * FROM srp_erp_grouppolicy WHERE srp_erp_grouppolicy.groupID = " . $com . ") cp ON(cp.groupPolicymasterID = srp_erp_grouppolicymaster.groupPolicymasterID);")->result_array();
        return array_group_by($Companypolicy, 'code', 'documentID');
    }

    function fetch_group_detail($com, $bran)
    {
        $this->db->select('*,description as company_name,companyGroupID as company_id,"" as company_code,group_address1 as company_address1,group_address2 as company_address2,group_city as company_city,group_country as company_country,group_logo as company_logo,groupCode as company_code,group_logo as  company_secondary_logo');
        $this->db->from('srp_erp_companygroupmaster');
        $this->db->where('companyGroupID', $com);
        return $this->db->get()->row_array();
    }

    function authenticateLoginUserName($login_data)
    {
        $this->db->select('EIdNo,NoOfLoginAttempt,isDischarged,isActive');
        $this->db->where("UserName", $login_data['userN']);
        $result = $this->db->get("srp_employeesdetails")->row_array();

        if (!is_array($result)) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Account not found";
            return $data;
        }

        if ($result['isDischarged'] == 0 && $result['isActive'] == 1) {
            if ($result['NoOfLoginAttempt'] == 4) {
                $data['stats'] = False;
                $data['type'] = "error";
                $data['message'] = "Your account has been blocked please contact support team";
                return $data;
            }elseif($result['NoOfLoginAttempt'] == 2){
                if(!empty($result)){
                    $datas['NoOfLoginAttempt'] = $result['NoOfLoginAttempt']+1;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                    if ($updateAttempt) {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = "Invalid username or password. <br/><strong><i class='fa fa-exclamation-triangle'></i> You have one more attempt.<strong>";
                        return $data;
                    }
                }else{
                    $data['stats'] = False;
                    $data['type'] = "error";
                    $data['message'] = " Wrong user name or password. Please  try again.";
                    return $data;
                }
            } else {
                if(!empty($result)){
                    $datas['NoOfLoginAttempt'] = $result['NoOfLoginAttempt']+1;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                    if ($updateAttempt) {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = "Invalid username or password. Please  try again.";
                        return $data;
                    }
                }else{
                    $data['stats'] = False;
                    $data['type'] = "error";
                    $data['message'] = " Wrong user name or password. Please  try again.";
                    return $data;
                }
            }
        }else if ($result['isActive'] == 0) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Your account is not activated";
            return $data;
        } else{
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Access Denied";
            return $data;
        }
    }

}
