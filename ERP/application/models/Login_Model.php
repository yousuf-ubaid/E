<?php

class Login_Model extends ERP_Model
{

    function __construct()
    {

    }

    function set_default_db(){
        $env_db_conf = unserialize(env_DB);

        $config['hostname'] = $env_db_conf['host'];
        $config['username'] = $env_db_conf['user'];
        $config['password'] = $env_db_conf['password'];
        $config['database'] = $env_db_conf['database'];
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
    }

    function get_users($username, $pwd)
    {
        $this->db->select('count(UserName) as uname');
        $this->db->from('user');
        $this->db->where('UserName', $username);
        $this->db->where('Password', $pwd);
        return $this->db->get()->row_array();
    }

    public function get_session_data($empid,$company_id,$isGroupUser = 0){
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->where("company_id", $company_id);
        $resultDb2 = $CI->db->get("srp_erp_company")->row_array();
        $config['hostname'] = trim($CI->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($CI->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($CI->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($CI->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $CI->load->database($config, FALSE, TRUE);

        $CI->db->select('company_id,company_name,company_logo,UserName,Ename1,Ename2,Ename3,Ename4,serialNo,EIdNo,company_code ,branchID,SchMasterId,EmpImage,Gender, ECode,EmpShortCode,srp_employeesdetails.languageID as languageID,srp_employeesdetails.locationID as locationIDemp,srp_employeesdetails.userType as userType');
        $CI->db->where("EIdNo", $empid);
        $CI->db->where("srp_erp_company.confirmedYN", 1);
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_employeesdetails.Erp_companyID', 'left');

        $user_master_data = $CI->db->get()->row_array();

        if ($user_master_data) {
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
                'emplangid' => $user_master_data['languageID'],
                'emplanglocationid' => $user_master_data['locationIDemp'],
                'isGroupUser' => $isGroupUser,
                'userType' => $user_master_data['userType'],
                'status' => TRUE
            );

            //revert db configuration.
            $CI->load->database('default', FALSE, TRUE);

            return $session_data;
        }
    }

    function get_userID($username, $pwd)
    {
        $this->load->library('JWT');
        $config = array();
        if (!isset($_SERVER['HTTP_SME_API_KEY'])) {
            $env_db_conf = unserialize(env_DB);
            $config['hostname'] = $env_db_conf['host'];
            $config['username'] = $env_db_conf['user'];
            $config['password'] = $env_db_conf['password'];
            $config['database'] = $env_db_conf['database'];
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
        } else {
            $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
            $output['id_token'] = $this->JWT->decode($tokenKey, "id_token");
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
        }

        $this->load->database($config, FALSE, TRUE);

        $this->db->select('empID as EIdNo, user.username, companyID as Erp_companyID, company.company_code, company.db_username, 
        company.db_password, company.db_password, company.host, company.db_name, company.defaultTimezoneID');
        $this->db->from('user');
        $this->db->join('srp_erp_company as company', 'company.company_id = user.companyID');
        $this->db->where('UserName', $username);
        $this->db->where('Password', $pwd);
        return $this->db->get()->row_array();
    }

    function get_emploayeeName($Eid, $CompanyID)
    {
        $e_name = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo={$Eid} and Erp_companyID= {$CompanyID}")->row('Ename2');
        return $e_name;
    }

    public function get_session_data_for_max_portal_open_link($empid,$company_id,$isGroupUser = 0){
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->where("company_id", $company_id);
        $resultDb2 = $CI->db->get("srp_erp_company")->row_array();
        $config['hostname'] = trim($CI->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($CI->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($CI->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($CI->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $CI->load->database($config, FALSE, TRUE);

        $CI->db->select('company_id,company_name,company_address1,company_address2,company_city,company_country,company_phone,company_email,company_logo,UserName,Ename1,Ename2,Ename3,Ename4,serialNo,EIdNo,company_code ,branchID,SchMasterId,EmpImage,Gender, ECode,EmpShortCode,srp_employeesdetails.languageID as languageID,srp_employeesdetails.locationID as locationIDemp,srp_employeesdetails.userType as userType');
        $CI->db->where("EIdNo", $empid);
        $CI->db->where("srp_erp_company.confirmedYN", 1);
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_employeesdetails.Erp_companyID', 'left');

        $user_master_data = $CI->db->get()->row_array();

        if ($user_master_data) {
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
                'emplangid' => $user_master_data['languageID'],
                'emplanglocationid' => $user_master_data['locationIDemp'],
                'isGroupUser' => $isGroupUser,
                'userType' => $user_master_data['userType'],
                'status' => TRUE,
                'company_address1' => $user_master_data['company_address1'],
                'company_address2' => $user_master_data['company_address2'],
                'company_city' => $user_master_data['company_city'],
                'company_country' => $user_master_data['company_country'],
                'company_phone' => $user_master_data['company_phone'],
                'company_email' => $user_master_data['company_email']
            );

            //revert db configuration.
            $CI->load->database('default', FALSE, TRUE);

            return $session_data;
        }
    }
}
