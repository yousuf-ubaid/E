<?php

class Dashboard_model extends CI_Model
{

    Private $main;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;

    public function __construct()
    {
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $companyID = $this->input->post('companyid');
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("*");
        $this->main->from("srp_erp_company");
        $this->main->where("company_id", $companyID);
        $r = $this->main->get()->row_array();
        if (!empty($r)) {
            $this->db_host = $r['host'];
            $this->db_name = $r['db_name'];
            $this->db_password = $r['db_password'];
            $this->db_username = $r['db_username'];
        }
    }

    function get_db_array()
    {
        $config['hostname'] = trim($this->encryption->decrypt($this->db_host));
        $config['username'] = trim($this->encryption->decrypt($this->db_username));
        $config['password'] = trim($this->encryption->decrypt($this->db_password));
        $config['database'] = trim($this->encryption->decrypt($this->db_name));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = TRUE;
        return $config;
    }

    function fetch_currency_arr()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->main->select("currencyID,CurrencyCode,CurrencyName,DecimalPlaces");
        $this->main->from('srp_erp_currencymaster');
        $currency = $this->main->get()->result_array();
        $currency_arr = array('' => 'Select Currency');
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']) . ' | ' . trim($row['DecimalPlaces']);
            }
        }
        return $currency_arr;
    }

    function fetch_school_arr()
    {
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("srp_schoolmaster.SchMasterID,SchNameEn,BranchDes,branchID");
        $this->main->from('srp_schbranches');
        $this->main->join('srp_schoolmaster', 'srp_schoolmaster.SchMasterID = srp_schbranches.SchMasterID');
        $school = $this->main->get()->result_array();
        $school_arr = array('' => 'Select School');
        $school_arr['0|0'] = 'Only ERP System';
        if (isset($school)) {
            foreach ($school as $row) {
                $school_arr[trim($row['SchMasterID']) . '|' . trim($row['branchID'])] = trim($row['SchNameEn']) . ' | ' . trim($row['BranchDes']);
            }
        }
        return $school_arr;
    }

    function load_company_header()
    {
        $companyID = $this->input->post('companyid');
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('*');
        $this->db->where('company_id', $companyID);
        $data = $this->db->get('srp_erp_company')->row_array();
        if($data){
            $data['diskUsage'] = $this->get_client_disk_usage($companyID);
        }

        return $data;
    }

    function get_client_disk_usage($companyID){
        $this->db->select('SUM(fileSize) AS totSize')->where('companyID', $companyID);
        $totSize = $this->db->get('srp_erp_documentattachments')->row('totSize');

        return readableBytes($totSize);
    }

    function load_company_host_detail()
    {
        $company_id = $this->input->post('companyid');
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select('*');
        $this->main->where('company_id', $company_id);
        $result = $this->main->get('srp_erp_company')->row_array();

        if(empty($result)){
            return null;
        }

        $config['company_name_str'] = null;
        if($result["company_code"]){
            $config['company_name_str'] = $result["company_name"].' [ '.$result["company_code"].' ]';
        }
        $config['contractID'] = trim($result["contractID"]);
        $config['isPartnerCompany'] = trim($result["isPartnerCompany"]);
        $config['host'] = trim($this->encryption->decrypt($result["host"]));
        $config['db_username'] = trim($this->encryption->decrypt($result["db_username"]));
        $config['db_password'] = trim($this->encryption->decrypt($result["db_password"]));
        $config['db_name'] = trim($this->encryption->decrypt($result["db_name"]));

        $this->main->where('company_id', $company_id)->select('product_id');
        $config['product_id'] = $this->main->get('product_company')->row('product_id');

        return $config;
    }

    function load_company_subscription_detail()
    {
        $this->main = $this->load->database('db2', TRUE);
        $company_id = $this->input->post('company_id');
        $result = $this->db->query("SELECT *, sub_tb.nextRenewalDate AS sub_nextRenewalDate, IFNULL(subscriptionCurrency, 0) AS subscriptionCurrency_str
                            FROM srp_erp_company AS com
                            JOIN (
                               SELECT nextRenewalDate, companyID FROM companysubscriptionhistory 
                               WHERE companyID={$company_id} ORDER BY subscriptionID ASC LIMIT 1
                            ) AS sub_tb ON sub_tb.companyID = com.company_id
                            WHERE company_id={$company_id} ")->row_array();

        return $result;
    }

    function save_company_host()
    {
        $companyID = trim($this->input->post('companyid'));
        $userType = current_userType();
        $userID = current_userID();
        $date_time = date('Y-m-d H:i:s');

        $this->main = $this->load->database('db2', TRUE);
        $this->main->trans_start();
        $pbs_contract = $this->input->post('pbs_contract');
        $isVerified = $this->input->post('isVerified');

        if($userType == 1){
            $data['contractID'] = $pbs_contract;
        }

        if( $this->input->post('newDBVerify') == 0 ){
            $this->check_db_exists();
        }
        else{
            $this->create_new_client_DB();
        }

        $clientDB_host = $this->config->item('clientDB_host');
        $clientDB = $this->input->post('db_name');
        $clientDB_user = $this->config->item('clientDB_user');
        $clientDB_pass = $this->config->item('clientDB_password');

        $data['isPartnerCompany'] = $this->input->post('company_type');
        $data['host'] = $this->encryption->encrypt($clientDB_host);
        $data['db_name'] = $this->encryption->encrypt($clientDB);
        $data['db_username'] = $this->encryption->encrypt($clientDB_user);
        $data['db_password'] = $this->encryption->encrypt($clientDB_pass);
        $data['attachmentHost'] = $this->input->post('attachmentHost');
        $data['attachmentFolderName'] = $this->input->post('attachmentFolderName');
        $data['adminType'] = $userType;

        $old_records = $this->db->get_where('srp_erp_company', ['company_id'=>$companyID])->row_array();
        $audit_log = [];
        foreach($data as $column=>$val){
            $old_val = (!empty($old_records))? $old_records[$column]: '';

            if(in_array($column, ['host', 'db_name', 'db_username', 'db_password'])){
                $val = $this->encryption->decrypt($val);
                $old_val = ($old_val != '' ) ? $this->encryption->decrypt($old_val): '';
            }
            $disOldVal = $old_val;
            $disNewVal = $val;

            if($column == 'isPartnerCompany'){
                $disOldVal = $this->db->select('description')->where(['id'=>$old_val])->get('system_company_type')->row('description');
                $disNewVal = $this->db->select('description')->where(['id'=>$val])->get('system_company_type')->row('description');                
            }

            if($column == 'contractID'){
                if($old_val != $val && $isVerified == 0){
                    //Get all companies with same contract ID
                    $this->db->select("CONCAT(company_name ,' [ ', company_code, ' ]') AS com")->where('contractID', $val);
                    $company_with_this_contract = $this->db->where("contractID != {$companyID}")->get('srp_erp_company')->result_array();
                    if($company_with_this_contract){
                        $company_with_this_contract = array_column($company_with_this_contract, 'com');
                        $msg = 'Following companies already created with selected contract.<br/><b> &nbsp; &nbsp; - ';
                        $msg .= implode('<br/> &nbsp; &nbsp; - ', $company_with_this_contract);
                        $msg .= '</b><br/>Are you sure, You want to proceed?';
                        return array('w', $msg);
                    }

                }
                $disOldVal = $this->db->query("SELECT display_new_val FROM srp_erp_audit_log
                                            WHERE id = (
                                                SELECT MAX(id) FROM srp_erp_audit_log WHERE companyID = '{$companyID}' 
                                                AND tableName = 'srp_erp_company' AND columnName = 'contractID'
                                            )")->row('display_new_val');
                $disNewVal = $this->input->post('pbs_contract_display');
            }

            if($old_val != $val){
                $log['tableName'] = 'srp_erp_company';
                $log['columnName'] = $column;
                $log['old_val'] = $old_val;
                $log['display_old_val'] = $disOldVal;
                $log['new_val'] = $val;
                $log['display_new_val'] = $disNewVal;
                $log['rowID'] = &$companyID;
                $log['companyID'] = &$companyID;
                $log['userID'] = $userID;
                $log['timestamp'] = $date_time;

                $audit_log[] = $log;
            }
        }

        $config['hostname'] = $clientDB_host;
        $config['username'] = $clientDB_user;
        $config['password'] = $clientDB_pass;
        $config['database'] = $clientDB;
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = FALSE;
        $config['pconnect'] = FALSE;
        $db_obj = $this->load->database($config, TRUE); // ommit the error

        if (empty($db_obj->conn_id)) {
            return ['e', 'Unable to connect with database with given db details'];
        }

        $msg = 'updated';
        if ($companyID) {
            $msg = 'saved';
            $this->main->where('company_id', $companyID)->update('srp_erp_company', $data);             
        }
        else {
            $this->main->insert('srp_erp_company', $data);
            $companyID = $this->main->insert_id();            
        }

        if(!empty($audit_log)){
            $this->main->insert_batch('srp_erp_audit_log', $audit_log);
        }

        $this->main->trans_complete();
        if ($this->main->trans_status() === FALSE) {
            $this->main->trans_rollback();
            $msg = ($msg == 'updated')? 'update': 'save';            
            return ['e', "Error in host detail {$msg} process"];
        } 
        else {            
            $this->main->trans_commit();
            return ['s', "Host detail {$msg} successfully.", 'last_id' => $companyID];
        }

    }

    function check_db_exists(){
        $db_name = $this->input->post('db_name');        
        $isExists = $this->db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$db_name}'")->row_array();

        if(empty($isExists)){
            die( json_encode(['newDB', "<b>{$db_name}</b> DB is not exists,<br/> Do you want to create it?"]) );
        }
        
    }

    function create_new_client_DB(){

        set_time_limit(300);

        $db_name = $this->input->post('db_name');
        $new_db = 'QN_empty';
        
        $isExists = $this->db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$db_name}'")->row_array();

        if($isExists){
            die( json_encode(['e', "Something went wrong.<br/>{$db_name} DB is already exists."]) );
        }

        $originalDB = $this->load->database('empty_db', TRUE);

        $this->load->dbforge();
        if ($this->dbforge->create_database($db_name)) {
            $db_config = [
                'hostname' => $this->config->item('clientDB_host'),
                'username' => $this->config->item('clientDB_user'),
                'password' => $this->config->item('clientDB_password'),
                'database' => $db_name,
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => (ENVIRONMENT !== 'production'),
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8mb4',
                'dbcollat' => 'utf8mb4_unicode_ci',
                'swap_pre' => '',
                'encrypt'  => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
            ];

            $newDB = $this->load->database($db_config, TRUE);
            $this->clone_tables($originalDB, $newDB, $new_db);
            $this->clone_functions($originalDB, $newDB);
            $this->clone_procedures($originalDB, $newDB);
            $this->clone_views($originalDB, $newDB);
        }
    }

    private function clone_tables($originalDB, $newDB, $original_db)
    {
        $newDB->query("SET FOREIGN_KEY_CHECKS=0;");

        $tablesQuery = $originalDB->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = $tablesQuery->result_array();

        foreach ($tables as $table) {
            $table_name = '`' . $table['Tables_in_' . $original_db] . '`';

            $query = $originalDB->query("SHOW CREATE TABLE $table_name");
            $createTableStmt = $query->row_array()['Create Table'];

            $newDB->query($createTableStmt);

            $offset = 0;
            $batchSize = 500; // Adjust as needed for optimal performance

            do {
                // Fetch a batch of rows
                $dataQuery = $originalDB->query("SELECT * FROM $table_name LIMIT $offset, $batchSize");
                $rows = $dataQuery->result_array();

                if (!empty($rows)) {
                    $newDB->insert_batch(str_replace('`', '', $table_name), $rows);
                    $offset += $batchSize;
                }
            } while (!empty($rows));
        }

        $newDB->query("SET FOREIGN_KEY_CHECKS=1;");

        foreach ($tables as $table) {
            $table_name = '`' . $table['Tables_in_' . $original_db] . '`';
            $foreign_keys = $newDB->query("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$original_db}' AND TABLE_NAME = '{$table_name}' AND REFERENCED_TABLE_NAME IS NOT NULL")->result();
            foreach ($foreign_keys as $fk) {
                $newDB->query("ALTER TABLE `{$table_name}` ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}` FOREIGN KEY (`{$fk->COLUMN_NAME}`) REFERENCES `{$fk->REFERENCED_TABLE_NAME}`(`{$fk->REFERENCED_COLUMN_NAME}`) ON DELETE {$fk->DELETE_RULE} ON UPDATE {$fk->UPDATE_RULE};");
            }
        }
    }

    private function clone_views($originalDB, $newDB)
    {
        $viewsQuery = $originalDB->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        $views = $viewsQuery->result_array();

        foreach ($views as $view) {
            $view_name = $view['Tables_in_' . $originalDB->database];

            $viewCreateQuery = $originalDB->query("SHOW CREATE VIEW `$view_name`");
            $createViewStmt = $viewCreateQuery->row_array()['Create View'];

            $newDB->query($createViewStmt);
        }
    }

    private function clone_procedures($originalDB, $newDB)
    {
        $proceduresQuery = $originalDB->query("SHOW PROCEDURE STATUS WHERE Db = '{$originalDB->database}'");
        $procedures = $proceduresQuery->result_array();

        foreach ($procedures as $procedure) {
            $procedureName = $procedure['Name'];

            $procedureCreateQuery = $originalDB->query("SHOW CREATE PROCEDURE `$procedureName`");
            $createProcedureStmt = $procedureCreateQuery->row_array()['Create Procedure'];

            $newDB->query($createProcedureStmt);
        }
    }

    private function clone_functions($originalDB, $newDB)
    {
        $functionsQuery = $originalDB->query("SHOW FUNCTION STATUS WHERE Db = '{$originalDB->database}'");
        $functions = $functionsQuery->result_array();

        foreach ($functions as $function) {
            $functionName = $function['Name'];

            $functionCreateQuery = $originalDB->query("SHOW CREATE FUNCTION `$functionName`");
            $createFunctionStmt = $functionCreateQuery->row_array()['Create Function'];

            $newDB->query($createFunctionStmt);
        }
    }

    function config_db($db_name){
        $config = [
            'hostname'=> $this->main->hostname, 'username'=> $this->main->username,
            'password'=> $this->main->password, 'database'=> $db_name,
            'dbdriver'=> 'mysqli', 'db_debug'=> TRUE
        ];
        
        return $this->load->database($config, TRUE);
    }

    function save_company_subscription()
    {

        $this->db->trans_start();

        $company_id = $this->input->post('company_id');

        $company_data = $this->db->get_where('srp_erp_company', ['company_id' => $company_id])->row_array();

        $isAlreadyConfirmed = $company_data['isInitialSubscriptionConfirmed'];
        if ($isAlreadyConfirmed == 1) {
            return ['e', 'Subscription details already confirmed you can not update details'];
        }

        $registeredDate = $this->input->post('registeredDate');
        $sub_startDate = $this->input->post('subscriptionStartDate');
        $sub_amount = $this->input->post('subscriptionAmount');
        $paymentEnabled = (int)$this->input->post('paymentEnabled');
        $imp_amount = $this->input->post('implementationAmount');
        $date_time = date('Y-m-d H:i:s');
        $nextRenewalDate = date('Y-m-d', strtotime($sub_startDate . ' + 365 days'));
        $dueDate = date('Y-m-d', strtotime($sub_startDate . ' + 14 days'));
        $isConfirmedYN = ($this->input->post('isConfirmedYN') == 1) ? 1 : 0;
        $currencyID = $this->input->post('currencyID');
        $pc = current_pc();
        $user_id = current_userID();
        $user_name = current_userName();

        $curr_data = $this->db->get_where('srp_erp_currencymaster', ['currencyID' => $currencyID])->row_array();
        $dPlace = $curr_data['DecimalPlaces'];
        $sub_amount = round((float)$sub_amount, $dPlace);
        $imp_amount = round((float)$imp_amount, $dPlace);

        if ($isConfirmedYN == 1 && $sub_amount <= 0) {
            return ['e', 'Subscription amount not valid'];
        }

        $data = [
            'registeredDate' => $registeredDate, 'subscriptionStartDate' => $sub_startDate, 'subscriptionCurrency' => $currencyID,
            'subscriptionAmount' => $sub_amount, 'implementationAmount' => $imp_amount, 'isInitialSubscriptionConfirmed' => $isConfirmedYN,
            'paymentEnabled' => $paymentEnabled,
        ];


        if (empty($company_data['subscriptionNo']) || $company_data['subscriptionNo'] == '') {
            $this->load->helper('host');
            $sub_no = str_pad($company_id, 5, '0', STR_PAD_LEFT);
            $data['subscriptionNo'] = SYS_NAME . $sub_no;
        }

        $audit_log = [];  $old_records = $company_data;
        foreach($data as $column=>$new_val){
            $old_val = (!empty($old_records))? $old_records[$column]: '';


            if($old_val != $new_val){
                switch ($column){
                    case 'subscriptionCurrency':
                        $old_display_val = $this->db->select('CurrencyName')->from('srp_erp_currencymaster')->where(['currencyID'=>$old_val])->get()->row('CurrencyName');
                        $new_display_val = $this->db->select('CurrencyName')->from('srp_erp_currencymaster')->where(['currencyID'=>$new_val])->get()->row('CurrencyName');
                    break;

                    case 'paymentEnabled':
                    case 'isInitialSubscriptionConfirmed':
                        $old_display_val = ($old_val == 0)? 'No': 'Yes';
                        $new_display_val = ($new_val == 0)? 'No': 'Yes';
                    break;

                    default:
                        $old_display_val = $old_val;
                        $new_display_val = $new_val;
                }

                $audit_log[] = [
                    'tableName' => 'srp_erp_company', 'columnName'=> $column, 'old_val'=> $old_val, 'display_old_val'=> $old_display_val,
                    'new_val'=> $new_val, 'display_new_val'=> $new_display_val, 'rowID'=> $company_id, 'companyID'=> $company_id,
                    'userID'=> $user_id, 'timestamp'=> $date_time,
                ];
            }
        }

        if(!empty($audit_log)){
            $this->main->insert_batch('srp_erp_audit_log', $audit_log);
        }

        $data2 = [
            'modifiedPCID' => $pc, 'modifiedUserID' => $user_id, 'modifiedDateTime' => $date_time,
            'modifiedUserName' => $user_name, 'timestamp' => $date_time,
        ];

        $data = array_merge($data, $data2);

        $this->db->where('company_id', $company_id)->update('srp_erp_company', $data);

        $subscription_old_data = $this->db->query("SELECT * FROM companysubscriptionhistory WHERE companyID={$company_id} 
                                            ORDER BY subscriptionID ASC LIMIT 1")->row_array();

        $sub_history_data = [
            'subscriptionStartDate' => $sub_startDate, 'dueDate' => $dueDate,
            'nextRenewalDate' => $nextRenewalDate, 'subscriptionAmount' => $sub_amount,
        ];

        $audit_log = []; $subscriptionID = null;
        foreach($sub_history_data as $column=>$new_val){
            $old_val = (!empty($subscription_old_data))? $subscription_old_data[$column]: '';
            $old_display_val = $old_val;
            $new_display_val = $new_val;

            if($old_val != $new_val){
                $audit_log[] = [
                    'tableName' => 'companysubscriptionhistory', 'columnName'=> $column, 'old_val'=> $old_val,
                    'display_old_val'=> $old_display_val, 'new_val'=> $new_val, 'display_new_val'=> $new_display_val,
                    'rowID'=> &$subscriptionID, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
                ];
            }
        }

        if ($subscription_old_data) {
            $sub_history_data['modifiedPCID'] = $pc;
            $sub_history_data['modifiedUserID'] = $user_id;
            $sub_history_data['modifiedUserName'] = $user_name;
            $sub_history_data['modifiedDateTime'] = $date_time;
            $sub_history_data['timestamp'] = $date_time;

            $subscriptionID = $subscription_old_data['subscriptionID'];

            $this->db->where(['subscriptionID' => $subscriptionID])->update('companysubscriptionhistory', $sub_history_data);
        }
        else {
            $sub_history_data['companyID'] = $company_id;
            $sub_history_data['createdPCID'] = $pc;
            $sub_history_data['createdUserID'] = $user_id;
            $sub_history_data['createdUserName'] = $user_name;
            $sub_history_data['createdDateTime'] = $date_time;
            $sub_history_data['timestamp'] = $date_time;

            $this->db->insert('companysubscriptionhistory', $sub_history_data);
            $subscriptionID = $this->db->insert_id();
        }

        if(!empty($audit_log)){
            $this->main->insert_batch('srp_erp_audit_log', $audit_log);
        }

        $built_view = '';
        if ($isConfirmedYN == 1) {
            $invNo = $this->generate_subscription_inv_no();

            $inv_data['mas_data'] = [
                'invNo' => $invNo, 'invDate' => $date_time, 'company_name' => $company_data['company_name'], 'CurrencyCode' => $curr_data['CurrencyCode'],
                'companyPrintAddress' => $company_data['companyPrintAddress'], 'invDecPlace' => $dPlace, 'sub_id' => $subscriptionID, 'dueDate' => $dueDate
            ];
            $inv_data['det_data'][] = ['itemID' => 1, 'itemDescription' => 'Subscription Amount', 'description' => 'Subscription Amount', 'amount' => $sub_amount];
            $data['inv_data'] = $inv_data;
            $data['view_type'] = 'E';
            $data['company_id'] = $company_id;

            $built_view = $this->load->view('subscription-invoice-view.php', $data, true);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in update process'];
        } else {
            $this->db->trans_commit();
            $is_implementation_billing = ($imp_amount > 0) ? 1 : 0;
            return ['s', 'Subscription detail updated Successfully.', 'isConfirmedYN' => $isConfirmedYN,
                'built_view' => $built_view, 'is_implementation_billing' => $is_implementation_billing, 'paymentEnabled'=>$paymentEnabled];
        }
    }

    function load_invoice_view($inv_id)
    {
        $mas_data = $this->db->query("SELECT inv_mas.invNo, inv_mas.invTotal, inv_mas.invDecPlace, cur_mas.CurrencyCode, inv_mas.invDate, 
                                      com.company_name, companyPrintAddress, inv_mas.isAmountPaid, inv_mas.paymentType, inv_mas.companyID, 
                                      com.company_code, sub_his.dueDate, inv_mas.subscriptionID AS sub_id, inv_mas.payRecDate
                                      FROM subscription_invoice_master AS inv_mas 
                                      JOIN srp_erp_company AS com ON com.company_id = inv_mas.companyID
                                      LEFT JOIN companysubscriptionhistory AS sub_his ON sub_his.subscriptionID = inv_mas.subscriptionID
                                      JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=inv_mas.invCur
                                      WHERE inv_mas.invID = {$inv_id}")->row_array();

        if(empty($mas_data)){
            return ['status'=> 'error', 'msg'=> 'Invoice master not found'];
        }

        $det_data = $this->db->query("SELECT inv_det.amountBeforeDis, inv_det.amount, item_type.description, inv_det.itemDescription,                                       
                                      inv_det.itemID, discountPer, discountAmount
                                      FROM subscription_invoice_details AS inv_det 
                                      LEFT JOIN system_invoice_item_type AS item_type ON item_type.type_id=inv_det.itemID
                                      WHERE inv_det.invID = {$inv_id}")->result_array();

        return ['status'=> 'success', 'mas_data' => $mas_data, 'det_data' => $det_data];
    }

    function save_company_master()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        //$this->db->trans_start();
        $school_id = explode('|', trim($this->input->post('school_id')));
        $default_currency = explode('|', trim($this->input->post('default_currency')));
        $reporting_currency = explode('|', trim($this->input->post('reporting_currency')));
        
        $data['company_link_id'] = $school_id[0];
        $data['branch_link_id'] = $school_id[1];
        $data['company_code'] = trim($this->input->post('companycode'));
        $data['company_name'] = trim($this->input->post('companyname'));
        $data['company_start_date'] = trim($this->input->post('companystartdate'));
        $data['company_url'] = trim($this->input->post('companyurl'));
        $data['legalName'] = trim($this->input->post('legalname'));
        $data['textIdentificationNo'] = trim($this->input->post('txtidntificationno'));
        $data['textYear'] = trim($this->input->post('textyear'));
        $data['industryID'] = empty(trim($this->input->post('industryID'))) ? NULL : trim($this->input->post('industryID'));
        $data['industry'] = trim($this->input->post('industry_dec'));
        $data['company_email'] = trim($this->input->post('companyemail'));
        $data['company_phone'] = trim($this->input->post('companyphone'));
        $data['company_address1'] = trim($this->input->post('companyaddress1'));
        $data['company_address2'] = trim($this->input->post('companyaddress2'));
        $data['company_city'] = trim($this->input->post('companycity'));
        $data['company_province'] = trim($this->input->post('companyprovince'));
        $data['company_postalcode'] = trim($this->input->post('companypostalcode'));
        $com_country = explode('|', trim($this->input->post('companycountry')));
        $data['countryID'] = $com_country[0];
        $data['company_country'] = $com_country[1];
        $data['default_segment'] = trim($this->input->post('default_segment') ?? '');
        $data['company_default_currencyID'] = trim($this->input->post('company_default_currencyID'));
        $data['company_default_currency'] = trim($default_currency[0]);
        $data['company_default_decimal'] = trim($default_currency[2]);
        $data['company_reporting_currencyID'] = trim($this->input->post('company_reporting_currencyID'));
        $data['company_reporting_currency'] = trim($reporting_currency[0]);
        $data['company_reporting_decimal'] = trim($reporting_currency[2]);
        $data['defaultTimezoneID'] = $this->input->post('timezone');
        //$data['noOfUsers'] = $this->input->post('noOfUsers');

        $companyID = trim($this->input->post('companyid'));

        $this->db->select('*');
        $this->db->where('company_id', $companyID);
        $old_records = $exist_company = $this->db->get('srp_erp_company')->row_array();

        $audit_log = []; $userID = current_userID(); $date_time = date('Y-m-d H:i:s');
        foreach($data as $column=>$new_val){ 
            $old_val = (!empty($old_records))? $old_records[$column]: '';
            $old_display_val = $old_val;
            $new_display_val = $new_val;

            if($old_val != $new_val){
                if(in_array($column, ['defaultTimezoneID', 'company_link_id', 'branch_link_id', 'industryID', 'countryID'])){

                    switch ($column){
                        case 'defaultTimezoneID':
                            $old_display_val = $this->db->select('description')->from('srp_erp_timezonedetail')->where(['detailID'=>$old_val])->get()->row('description');
                            $new_display_val = $this->db->select('description')->from('srp_erp_timezonedetail')->where(['detailID'=>$new_val])->get()->row('description');
                        break;

                        case 'company_link_id':
                            $old_display_val = $this->db->select('SchNameEn')->from('srp_schoolmaster')->where(['SchMasterID'=>$old_val])->get()->row('SchNameEn');
                            $new_display_val = $this->db->select('SchNameEn')->from('srp_schoolmaster')->where(['SchMasterID'=>$new_val])->get()->row('SchNameEn');
                        break;

                        case 'branch_link_id':
                            $old_display_val = $this->db->select('BranchDes')->from('srp_schbranches')->where(['branchID'=>$old_val])->get()->row('BranchDes');
                            $new_display_val = $this->db->select('BranchDes')->from('srp_schbranches')->where(['branchID'=>$new_val])->get()->row('BranchDes');
                        break;

                        case 'industryID':
                            $old_display_val = $this->db->select('industryTypeDescription')->from('srp_erp_industrytypes')->where(['industrytypeID'=>$old_val])->get()->row('industryTypeDescription');
                            $new_display_val = $this->db->select('industryTypeDescription')->from('srp_erp_industrytypes')->where(['industrytypeID'=>$new_val])->get()->row('industryTypeDescription');
                        break;

                        case 'countryID':
                            $old_display_val = $this->db->select('CountryDes')->from('srp_erp_countrymaster')->where(['countryID'=>$old_val])->get()->row('CountryDes');
                            $new_display_val = $this->db->select('CountryDes')->from('srp_erp_countrymaster')->where(['countryID'=>$new_val])->get()->row('CountryDes');
                        break;
                    }

                }

                $audit_log[] = [
                    'tableName' => 'srp_erp_company', 'columnName'=> $column, 'old_val'=> $old_val, 'display_old_val'=> $old_display_val,
                    'new_val'=> $new_val, 'display_new_val'=> $new_display_val, 'rowID'=> $companyID, 'companyID'=> $companyID,
                    'userID'=> $userID, 'timestamp'=> $date_time,
                ];
            }
        }

        if(!empty($audit_log)){
            $this->main->insert_batch('srp_erp_audit_log', $audit_log);
        }


        $data['modifiedPCID'] = current_pc();
        $data['modifiedUserID'] = current_userID();
        $data['modifiedUserName'] = current_userName();
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');

        if ($exist_company) {
            
            $this->main = $this->load->database('db2', TRUE);
            $this->main->where('company_id', $companyID);
            $this->main->update('srp_erp_company', $data);


            $this->db->where('company_id', $companyID);
            $this->db->update('srp_erp_company', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Company : ' . $data['company_name'] . ' Update Failed ' . $this->db->_error_message()];
            }
            else {
                $this->db->trans_commit();
                return ['s', 'Company : ' . $data['company_name'] . ' Update Successfully.', 'last_id' => $companyID];
            }
        }
        else {
            $data['company_logo'] = 'no-logo.png';
            $data['createdUserGroup'] = 'Admin';
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = null;
            $data['createdUserName'] = null;
            $data['createdDateTime'] = $data['modifiedDateTime'];
            $data["company_id"] = (int)$companyID;
            $data['pos_isFinanceEnables'] = 1;
            $this->db->insert('srp_erp_company', $data);

            $company_id = $this->db->insert_id();

            $this->db->query("INSERT INTO `srp_erp_customertypemaster` ( `customerDescription`,`displayDescription`, `isDefault`, `company_id`, `createdBy`, `createdDatetime`, `createdPc`, `timestamp`) 
                              VALUES ('Dine-in','Dine-in', '1', $company_id, '', NULL, '', '0000-00-00 00:00:00'), ('Take-away','Take-away', '0', $company_id, '', NULL, '', '0000-00-00 00:00:00'),
                              ('Delivery Orders','Delivery Orders', '0', $company_id, '', NULL, '', '0000-00-00 00:00:00');");

            unset($data["company_id"]);
            unset($data["pos_isFinanceEnables"]);

            $this->main = $this->load->database('db2', TRUE);
            $this->main->where('company_id', $companyID);
            $this->main->update('srp_erp_company', $data);

            //add to navigation templates
            $newTemplates = $this->db->query("SELECT srp_erp_templatemaster.TempMasterID,srp_erp_formcategory.FormCatID,srp_erp_formcategory.navigationMenuID
                                              FROM srp_erp_formcategory
                                              INNER JOIN srp_erp_templatemaster ON srp_erp_formcategory.FormCatID = srp_erp_templatemaster.FormCatID 
                                              WHERE isDefault = 1")->result_array();
            if ($newTemplates) {
                $templates = array();
                foreach ($newTemplates as $val) {
                    $templates[] = array('companyID' => $company_id, 'TempMasterID' => $val['TempMasterID'], 'FormCatID' => $val['FormCatID'], 'navigationMenuID' => $val['navigationMenuID']);
                }
                $this->db->insert_batch('srp_erp_templates', $templates);
            }

            $user_group_arr = array(
                array('description' => 'CEO', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'CFO', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'Finance Manager', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'Purchasing Manager', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'HR Manager', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'Operation Manager', 'isActive' => 1, 'companyID' => $company_id),
                array('description' => 'Sales Manager', 'isActive' => 1, 'companyID' => $company_id)
            );
            $insert = $this->db->insert_batch('srp_erp_usergroups', $user_group_arr);
            $this->db->insert('srp_erp_usergroups', array('description' => 'Administrator', 'isActive' => 1, 'companyID' => $company_id));
            $user_group_id = $this->db->insert_id();
            /*shahmy*/
            $usergroupArr = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$company_id} AND description IN 
                                            ('CEO' , 'CFO', 'Finance Manager', 'Purchasing Manager', 'HR Manager','Operation Manager', 'Sales Manager','Administrator') 
                                             ")->result_array();
            $defaultWidgets = $this->db->query("select widgetID from srp_erp_widgetmaster")->result_array();
            if ($insert) {
                if (!empty($usergroupArr)) {
                    $x = 0;
                    foreach ($usergroupArr as $row) {
                        foreach ($defaultWidgets as $val) {
                            if ($row['description'] == 'HR Manager' && in_array($val['widgetID'], array(15, 16, 17))) {
                                $widgetdata[$x]['companyID'] = $company_id;
                                $widgetdata[$x]['userGroupID'] = $row['userGroupID'];
                                $widgetdata[$x]['widgetID'] = $val['widgetID'];
                            } else if ($row['description'] == 'Operation Manager' && in_array($val['widgetID'], array(13))) {
                                $widgetdata[$x]['companyID'] = $company_id;
                                $widgetdata[$x]['userGroupID'] = $row['userGroupID'];
                                $widgetdata[$x]['widgetID'] = $val['widgetID'];
                            } else if ($row['description'] == 'Sales Manager' && in_array($val['widgetID'], array(13, 14, 6))) {
                                $widgetdata[$x]['companyID'] = $company_id;
                                $widgetdata[$x]['userGroupID'] = $row['userGroupID'];
                                $widgetdata[$x]['widgetID'] = $val['widgetID'];
                            } else if ($row['description'] == 'Purchasing Manager') {
                                /*none*/
                            } else {
                                $widgetdata[$x]['companyID'] = $company_id;
                                $widgetdata[$x]['userGroupID'] = $row['userGroupID'];
                                $widgetdata[$x]['widgetID'] = $val['widgetID'];
                            }
                            $x++;
                        }
                    }
                }
            }

            if ($widgetdata) {
                $insertDefaultWidget = $this->db->insert_batch('srp_erp_usergroupwidget', $widgetdata);
            }
            /*End Shahmy*/
            $title_arr = array();
            $title_arr[0]['TitleDescription'] = 'Mr';
            $title_arr[0]['Erp_companyID'] = $company_id;
            $title_arr[1]['TitleDescription'] = 'Mrs';
            $title_arr[1]['Erp_companyID'] = $company_id;
            $title_arr[2]['TitleDescription'] = 'Miss';
            $title_arr[2]['Erp_companyID'] = $company_id;
            $this->db->insert_batch('srp_titlemaster', $title_arr);
            $cat_arr = array();
            array_push($cat_arr, array('partyType' => 1, 'categoryDescription' => 'General', 'companyCode' => $data['company_code'], 'companyID' => $company_id));
            array_push($cat_arr, array('partyType' => 2, 'categoryDescription' => 'General', 'companyCode' => $data['company_code'], 'companyID' => $company_id));
            $this->db->insert_batch('srp_erp_partycategories', $cat_arr);

            $religion_arr = array();
            $religion_arr[0]['Religion'] = 'Christianity';
            $religion_arr[0]['ReligionAr'] = 'ؤاقهسفهشى';
            $religion_arr[0]['Erp_companyID'] = $company_id;
            $religion_arr[1]['Religion'] = 'Islam';
            $religion_arr[1]['ReligionAr'] = 'ةعسخمهة';
            $religion_arr[1]['Erp_companyID'] = $company_id;
            $religion_arr[2]['Religion'] = 'Hinduism';
            $religion_arr[2]['ReligionAr'] = 'اهىيع';
            $religion_arr[2]['Erp_companyID'] = $company_id;
            $religion_arr[3]['Religion'] = 'Buddhism';
            $religion_arr[3]['ReligionAr'] = 'يبيبسبس';
            $religion_arr[3]['Erp_companyID'] = $company_id;
            $religion_arr[4]['Religion'] = 'Others';
            $religion_arr[4]['ReligionAr'] = '';
            $religion_arr[4]['Erp_companyID'] = $company_id;

            $this->db->insert_batch('srp_erp_companypolicymaster_value', [
                ['companypolicymasterID' => '5', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id],
                ['companypolicymasterID' => '6', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id]
            ]);

            $nationality_arr = array();
            $country_arr = array();
            $this->db->select('*');
            $this->db->from('srp_erp_countrymaster');
            $nationality = $this->db->get()->result_array();
            foreach ($nationality as $key => $value) {
                $nationality_arr[$key]['Nationality'] = $value['Nationality'];
                $nationality_arr[$key]['Erp_companyID'] = $company_id;
                $country_arr[$key]['countryShortCode'] = $value['countryShortCode'];
                $country_arr[$key]['CountryDes'] = $value['CountryDes'];
                $country_arr[$key]['CountryDes'] = $value['CountryDes'];
                $country_arr[$key]['countryMasterID'] = $value['countryID'];
                $country_arr[$key]['Erp_companyID'] = $company_id;
            }
            if ($nationality_arr) {
                $this->db->insert_batch('srp_nationality', $nationality_arr);
            }

            if ($religion_arr) {
                $this->db->insert_batch('srp_religion', $religion_arr);
            }

            if ($country_arr) {
                $this->db->insert_batch('srp_countrymaster', $country_arr);
            }

            $currency_arr = array();
            if ($data['company_default_currencyID'] == $data['company_reporting_currencyID']) {
                $currency_arr['currencyID'] = $data['company_default_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_default_currency'];
                $currency_arr['CurrencyName'] = $default_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_default_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                    'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                    'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                    'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                ]);
            }
            else {
                $currency_arr['currencyID'] = $data['company_default_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_default_currency'];
                $currency_arr['CurrencyName'] = $default_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_default_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                        'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                        'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                        'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                    ]
                );

                $currency_arr['currencyID'] = $data['company_reporting_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_reporting_currency'];
                $currency_arr['CurrencyName'] = $reporting_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_reporting_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                    'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                    'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                    'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                ]);
            }

            $ememployeetype = array();
            $ememployeetype_arr = $this->db->get('srp_erp_systememployeetype')->result_array();
            for ($i = 0; $i < count($ememployeetype_arr); $i++) {
                $ememployeetype[$i]['Description'] = $ememployeetype_arr[$i]['employeeType'];
                $ememployeetype[$i]['typeID'] = $ememployeetype_arr[$i]['employeeTypeID'];
                $ememployeetype[$i]['Erp_CompanyID'] = $company_id;
            }
            if ($ememployeetype) {
                $this->db->insert_batch('srp_empcontracttypes', $ememployeetype);
            }
            $this->db->query("INSERT INTO `srp_erp_companypolicy` (`companypolicymasterID`,`companyID`,`documentID`,`isYN`,`value`) 
                              SELECT companypolicymasterID,'{$company_id}', documentID,1, defaultValue FROM srp_erp_companypolicymaster");


            $this->load->library('sequence', $this->get_db_array());
            $this->db->select('*');
            $this->db->from('srp_erp_accountcategorytypes');
            $account_types = $this->db->get()->result_array();
            for ($i = 0; $i < count($account_types); $i++) {
                $master_arr['systemAccountCode'] = $this->sequence->sequence_generator($account_types[$i]['subType'], 0, $company_id, $data['company_code']);
                $master_arr['GLSecondaryCode'] = $master_arr['systemAccountCode'];
                $master_arr['GLDescription'] = $account_types[$i]['CategoryTypeDescription'] . ' - Accounts ';
                $master_arr['masterAccountYN'] = 1;
                $master_arr['controllAccountYN'] = 0;
                $master_arr['masterAutoID'] = 0;
                $master_arr['masterCategory'] = $account_types[$i]['Type'];
                $master_arr['accountCategoryTypeID'] = $account_types[$i]['accountCategoryTypeID'];
                $master_arr['CategoryTypeDescription'] = $account_types[$i]['CategoryTypeDescription'];
                $master_arr['subCategory'] = $account_types[$i]['subType'];
                $master_arr['isActive'] = 1;
                $master_arr['isAuto'] = 1;
                $master_arr['approvedYN'] = 1;
                $master_arr['approvedDate'] = $data['createdDateTime'];
                $master_arr['approvedbyEmpID'] = $data['createdUserName'];
                $master_arr['approvedbyEmpName'] = $data['createdUserName'];
                $master_arr['approvedComment'] = 'By System';
                $master_arr['companyID'] = $company_id;
                $master_arr['companyCode'] = $data['company_code'];
                $master_arr['createdPCID'] = $data['modifiedPCID'];
                $master_arr['createdUserGroup'] = $user_group_id;
                $master_arr['createdUserName'] = $data['createdUserName'];
                $master_arr['createdUserID'] = $data['createdUserName'];
                $master_arr['createdDateTime'] = $data['createdDateTime'];
                $master_arr['modifiedPCID'] = $data['modifiedPCID'];
                $master_arr['modifiedUserID'] = $data['createdUserName'];
                $master_arr['modifiedUserName'] = $data['createdUserName'];
                $master_arr['modifiedDateTime'] = $data['createdDateTime'];
                $this->db->insert('srp_erp_chartofaccounts', $master_arr);
                $master_id = $this->db->insert_id();
                $control_account = array();
                if ($master_arr['CategoryTypeDescription'] == 'Account Receivable') {
                    $GL_data['GLSecondaryCode'] = 'AR0001';
                    $GL_data['GLDescription'] = 'Accounts Receivable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];;
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ARA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Account Payable') {
                    $GL_data['GLSecondaryCode'] = 'AP0001';
                    $GL_data['GLDescription'] = 'Accounts Payable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'APA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Current Asset') {
                    $GL_data['GLSecondaryCode'] = 'IN0001';
                    $GL_data['GLDescription'] = 'Inventory Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'INVA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    $GL_data['GLSecondaryCode'] = 'ADSP0001';
                    $GL_data['GLDescription'] = 'Asset Disposal Proceeds Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ADSP';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    //WIP
                    $GL_data['GLSecondaryCode'] = 'WIP';
                    $GL_data['GLDescription'] = 'Work in Progress';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'WIP';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    //RRVR
                    $GL_data['GLSecondaryCode'] = 'RRVR';
                    $GL_data['GLDescription'] = 'Receipt Reversal Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'RRVR';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    //GIT
                    $GL_data['GLSecondaryCode'] = 'GIT';
                    $GL_data['GLDescription'] = 'Goods In Transit';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'GIT';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'IOU';
                    $GL_data['GLDescription'] = 'IOU Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'IOU';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'IEXC';
                    $GL_data['GLDescription'] = 'IEXC';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'IEXC';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'UBI';
                    $GL_data['GLDescription'] = 'Un-Billed Invoices';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'UBI';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Current Liability') {
                    $GL_data['GLSecondaryCode'] = 'PCA0001';
                    $GL_data['GLDescription'] = 'Payroll Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'PCA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    $GL_data['GLSecondaryCode'] = 'TAX0001';
                    $GL_data['GLDescription'] = 'TAX Payable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'TAX';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    $GL_data['GLSecondaryCode'] = 'UGRV0001';
                    $GL_data['GLDescription'] = 'Unbill GRV Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'UGRV';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    //PRVR
                    $GL_data['GLSecondaryCode'] = 'PRVR';
                    $GL_data['GLDescription'] = 'Payment Reversal Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'PRVR';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Cost of Goods Sold') {
                    $GL_data['GLSecondaryCode'] = 'COGS0001';
                    $GL_data['GLDescription'] = 'Cost of goods sold Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'COGS';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Expense') {
                    $GL_data['GLSecondaryCode'] = 'LEC';
                    $GL_data['GLDescription'] = 'Leave Encashment Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'LEC';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Expense') {
                    $GL_data['GLSecondaryCode'] = 'ERGL0001';
                    $GL_data['GLDescription'] = 'Exchange Rate Gain or Loss';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ERGL';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Fixed Asset') {
                    $GL_data['GLSecondaryCode'] = 'AST0001';
                    $GL_data['GLDescription'] = 'Asset Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ACA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
            }

            if ($data['company_link_id'] != 0 AND $data['branch_link_id'] != 0) {
                $emp_arr = array();
                // $this->db->insert('srp_erp_usergroups', array('description'=>'School Team','isActive'=>1,'companyID'=>$company_id));
                // $school_group_id = $this->db->insert_id();
                $this->db->select('EIdNo');
                $this->db->where('SchMasterId', $data['company_link_id']);
                $this->db->where('branchID', $data['branch_link_id']);
                $this->db->from('srp_employeesdetails');
                $emp_data = $this->db->get()->result_array();
                for ($i = 0; $i < count($emp_data); $i++) {
                    $this->db->where('EIdNo', $emp_data[$i]['EIdNo']);
                    $this->db->update('srp_employeesdetails', array('Erp_companyID' => $company_id));
                }
            }

            $this->db->select('*');
            $this->db->from('srp_erp_documentcodes');
            $document_codes = $this->db->get()->result_array();
            for ($i = 0; $i < count($document_codes); $i++) {
                $document_code[$i]['documentID'] = $document_codes[$i]['documentID'];
                $document_code[$i]['document'] = $document_codes[$i]['document'];
                $document_code[$i]['prefix'] = $document_codes[$i]['documentID'];
                $document_code[$i]['startSerialNo'] = 1;
                $document_code[$i]['serialNo'] = 0;
                $document_code[$i]['formatLength'] = 6;
                $document_code[$i]['approvalLevel'] = 3;
                $document_code[$i]['format_1'] = 'prefix';
                $document_code[$i]['format_2'] = '/';
                $document_code[$i]['companyID'] = $company_id;
                $document_code[$i]['companyCode'] = $data['company_code'];
                $document_code[$i]['createdPCID'] = $data['modifiedPCID'];
                $document_code[$i]['createdUserGroup'] = $user_group_id;
                $document_code[$i]['createdUserName'] = $data['createdUserName'];
                $document_code[$i]['createdUserID'] = $data['createdUserName'];
                $document_code[$i]['createdDateTime'] = $data['createdDateTime'];
                $document_code[$i]['modifiedPCID'] = $data['modifiedPCID'];
                $document_code[$i]['modifiedUserID'] = $data['createdUserName'];
                $document_code[$i]['modifiedUserName'] = $data['createdUserName'];
                $document_code[$i]['modifiedDateTime'] = $data['createdDateTime'];
            }
            $document_code = array_values($document_code);
            if ($document_code) {
                $this->db->insert_batch('srp_erp_documentcodemaster', $document_code);
            }

            $item_category[0]['description'] = 'Inventory';
            $item_category[0]['categoryTypeID'] = '1';
            $item_category[0]['codePrefix'] = 'INV';
            $item_category[0]['StartSerial'] = '1';
            $item_category[0]['codeLength'] = '6';
            $item_category[0]['companyID'] = $company_id;
            $item_category[0]['companyCode'] = $data['company_code'];
            $item_category[0]['createdPCID'] = $data['modifiedPCID'];
            $item_category[0]['createdUserGroup'] = $user_group_id;
            $item_category[0]['createdUserName'] = $data['createdUserName'];
            $item_category[0]['createdUserID'] = $data['createdUserName'];
            $item_category[0]['createdDateTime'] = $data['createdDateTime'];
            $item_category[0]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[0]['modifiedUserID'] = $data['createdUserName'];
            $item_category[0]['modifiedUserName'] = $data['createdUserName'];
            $item_category[0]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[1]['description'] = 'Service';
            $item_category[1]['categoryTypeID'] = '2';
            $item_category[1]['codePrefix'] = 'SRV';
            $item_category[1]['StartSerial'] = '1';
            $item_category[1]['codeLength'] = '6';
            $item_category[1]['companyID'] = $company_id;
            $item_category[1]['companyCode'] = $data['company_code'];
            $item_category[1]['createdPCID'] = $data['modifiedPCID'];
            $item_category[1]['createdUserGroup'] = $user_group_id;
            $item_category[1]['createdUserName'] = $data['createdUserName'];
            $item_category[1]['createdUserID'] = $data['createdUserName'];
            $item_category[1]['createdDateTime'] = $data['createdDateTime'];
            $item_category[1]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[1]['modifiedUserID'] = $data['createdUserName'];
            $item_category[1]['modifiedUserName'] = $data['createdUserName'];
            $item_category[1]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[2]['description'] = 'Fixed Assets';
            $item_category[2]['categoryTypeID'] = '3';
            $item_category[2]['codePrefix'] = 'FA';
            $item_category[2]['StartSerial'] = '1';
            $item_category[2]['codeLength'] = '6';
            $item_category[2]['companyID'] = $company_id;
            $item_category[2]['companyCode'] = $data['company_code'];
            $item_category[2]['createdPCID'] = $data['modifiedPCID'];
            $item_category[2]['createdUserGroup'] = $user_group_id;
            $item_category[2]['createdUserName'] = $data['createdUserName'];
            $item_category[2]['createdUserID'] = $data['createdUserName'];
            $item_category[2]['createdDateTime'] = $data['createdDateTime'];
            $item_category[2]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[2]['modifiedUserID'] = $data['createdUserName'];
            $item_category[2]['modifiedUserName'] = $data['createdUserName'];
            $item_category[2]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[3]['description'] = 'Non Inventory';
            $item_category[3]['categoryTypeID'] = '2';
            $item_category[3]['codePrefix'] = 'NINV';
            $item_category[3]['StartSerial'] = '1';
            $item_category[3]['codeLength'] = '6';
            $item_category[3]['companyID'] = $company_id;
            $item_category[3]['companyCode'] = $data['company_code'];
            $item_category[3]['createdPCID'] = $data['modifiedPCID'];
            $item_category[3]['createdUserGroup'] = $user_group_id;
            $item_category[3]['createdUserName'] = $data['createdUserName'];
            $item_category[3]['createdUserID'] = $data['createdUserName'];
            $item_category[3]['createdDateTime'] = $data['createdDateTime'];
            $item_category[3]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[3]['modifiedUserID'] = $data['createdUserName'];
            $item_category[3]['modifiedUserName'] = $data['createdUserName'];
            $item_category[3]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category = array_values($item_category);
            if ($item_category) {
                $this->db->insert_batch('srp_erp_itemcategory', $item_category);
            }

            $segment['segmentCode'] = 'GEN';
            $segment['description'] = 'General';
            $segment['companyID'] = $company_id;
            $segment['companyCode'] = $data['company_code'];
            $this->db->insert('srp_erp_segment', $segment);
            $seg_id = $this->db->insert_id();
            $this->db->where('company_id', $company_id);
            $this->db->update('srp_erp_company', array('default_segment' => $seg_id . '|GEN'));

            $data_u['UnitShortCode'] = 'Each';
            $data_u['UnitDes'] = 'Each';
            $data_u['companyID'] = $company_id;
            $data_u['createdPCID'] = $data['modifiedPCID'];
            $data_u['createdUserGroup'] = $user_group_id;
            $data_u['createdUserName'] = $data['createdUserName'];
            $data_u['createdUserID'] = $data['createdUserName'];
            $data_u['createdDateTime'] = $data['createdDateTime'];
            $data_u['modifiedPCID'] = $data['modifiedPCID'];
            $data_u['modifiedUserID'] = $data['createdUserName'];
            $data_u['modifiedUserName'] = $data['createdUserName'];
            $data_u['modifiedDateTime'] = $data['createdDateTime'];
            $this->db->insert('srp_erp_unit_of_measure', $data_u);
            $unite_id = $this->db->insert_id();
            $this->db->insert('srp_erp_unitsconversion', array('masterUnitID' => $unite_id, 'subUnitID' => $unite_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'companyID' => $company_id));

            $warehouse['wareHouseCode'] = 'GEN';
            $warehouse['wareHouseDescription'] = 'General';
            $warehouse['wareHouseLocation'] = $data['company_city'];
            $warehouse['warehouseAddress'] = $data['company_address1'] . ' ' . $data['company_address2'];
            $warehouse['warehouseTel'] = $data['company_phone'];
            $warehouse['isPosLocation'] = 0;
            $warehouse['isActive'] = 1;
            $warehouse['companyID'] = $company_id;
            $warehouse['companyCode'] = $data['company_code'];
            $warehouse['createdPCID'] = $data['modifiedPCID'];
            $warehouse['createdUserGroup'] = $user_group_id;
            $warehouse['createdUserName'] = $data['createdUserName'];
            $warehouse['createdUserID'] = $data['createdUserName'];
            $warehouse['createdDateTime'] = $data['createdDateTime'];
            $warehouse['modifiedPCID'] = $data['modifiedPCID'];
            $warehouse['modifiedUserID'] = $data['createdUserName'];
            $warehouse['modifiedUserName'] = $data['createdUserName'];
            $warehouse['modifiedDateTime'] = $data['createdDateTime'];
            $this->db->insert('srp_erp_warehousemaster', $warehouse);


            $arrayGroup = array(array("description" => "General", "companyID" => $company_id, "companyCode" => $data['company_code']));
            $this->db->insert_batch('srp_erp_pay_overtimegroupmaster', $arrayGroup);

            /*add leave type*/
            $leave_type_arr = array(
                array('description' => 'Annual Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code']),
                array('description' => 'Sick Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code']),
                array('description' => 'Emergency Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code'])
            );
            $insert = $this->db->insert_batch('srp_erp_leavetype', $leave_type_arr);

            /*add leave group*/
            $leave_group_arr = array(
                array('description' => 'Permanent Employees', 'companyID' => $company_id),
                array('description' => 'Temporary Employees', 'companyID' => $company_id)
            );
            $insert = $this->db->insert_batch('srp_erp_leavegroup', $leave_group_arr);

            /*add staff salaries account to chartofaccount*/
            $chartofaccount = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE companyID=$company_id AND masterAccountYN = 1 AND accountCategoryTypeID = 13")->row_array();
            $master_arr['systemAccountCode'] = $this->sequence->sequence_generator('PLE', 0, $company_id, $data['company_code']);
            $master_arr['GLSecondaryCode'] = 'SAL-0001';
            $master_arr['GLDescription'] = 'Staff Salaries Account';
            $master_arr['masterAccountYN'] = 0;
            $master_arr['controllAccountYN'] = 0;
            $master_arr['masterAutoID'] = $chartofaccount["GLAutoID"];
            $master_arr['masterCategory'] = $chartofaccount['masterCategory'];
            $master_arr['accountCategoryTypeID'] = $chartofaccount['accountCategoryTypeID'];
            $master_arr['CategoryTypeDescription'] = $chartofaccount['CategoryTypeDescription'];
            $master_arr['subCategory'] = $chartofaccount['subCategory'];
            $master_arr['isActive'] = 1;
            $master_arr['isAuto'] = 1;
            $master_arr['approvedYN'] = 1;
            $master_arr['approvedDate'] = $data['createdDateTime'];
            $master_arr['approvedbyEmpID'] = $data['createdUserName'];
            $master_arr['approvedbyEmpName'] = $data['createdUserName'];
            $master_arr['approvedComment'] = 'By System';
            $master_arr['companyID'] = $company_id;
            $master_arr['companyCode'] = $data['company_code'];
            $master_arr['createdPCID'] = $data['modifiedPCID'];
            $master_arr['createdUserGroup'] = $user_group_id;
            $master_arr['createdUserName'] = $data['createdUserName'];
            $master_arr['createdUserID'] = $data['createdUserName'];
            $master_arr['createdDateTime'] = $data['createdDateTime'];
            $master_arr['modifiedPCID'] = $data['modifiedPCID'];
            $master_arr['modifiedUserID'] = $data['createdUserName'];
            $master_arr['modifiedUserName'] = $data['createdUserName'];
            $master_arr['modifiedDateTime'] = $data['createdDateTime'];
            $insert = $this->db->insert('srp_erp_chartofaccounts', $master_arr);
            $GLAutoID = $this->db->insert_id();
            /*add salary category*/
            $salary_category_arr = array(
                array('salaryDescription' => 'Basic Salary', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code']),
                array('salaryDescription' => 'Transport Allowance', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code']),
                array('salaryDescription' => 'Housing Allowance', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code'])
            );

            $this->db->insert_batch('srp_erp_pay_salarycategories', $salary_category_arr);

            return ['s', 'Company : ' . $data['company_name'] . ' Saved Successfully.', 'last_id' => $company_id];

        }
    }

    function fetch_admin_users()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('Gender,ECode,UserName,Ename1,Ename2,EIdNo,description as userGroupID');
        $this->db->order_by("EIdNo", "desc");
        $this->db->where('Erp_companyID', trim($this->input->post('companyid')));
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_employeenavigation', 'srp_erp_employeenavigation.empID = srp_employeesdetails.EIdNo', 'LEFT');
        $this->db->join('srp_erp_usergroups', 'srp_erp_usergroups.userGroupID = srp_erp_employeenavigation.userGroupID', 'LEFT');
        $this->db->group_by("EIdNo");
        return $this->db->get()->result_array();
    }

    function fetch_user_group()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('*');
        $this->db->where('isActive', 1);
        $this->db->where('companyID', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_usergroups');
        return $this->db->get()->result_array();
    }

    function save_user()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $this->load->library('sequence', $this->get_db_array());
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $companyID = trim($this->input->post('companyid'));

        $user_data['Ename1'] = trim($this->input->post('Ename1'));
        $user_data['Ename2'] = trim($this->input->post('Ename2'));
        $user_data['Gender'] = trim($this->input->post('Gender'));
        $user_data['EDOJ'] = trim($this->input->post('EDOJ'));
        $user_data['payCurrencyID'] = trim($this->input->post('payCurrencyID'));
        $user_data['EEmail'] = trim($this->input->post('EEmail'));
        $user_data['UserName'] = trim($this->input->post('EEmail'));
        $user_data['Password'] = md5(trim($this->input->post('Password')));
        $user_data['EmpImage'] = '/gs_sme/images/users/default.gif';
        $user_data['SchMasterId'] = $company_data['company_link_id'];
        $user_data['branchID'] = $company_data['branch_link_id'];
        $user_data['Erp_companyID'] = $companyID;
        $user_data['isSystemAdmin'] = 1;
        $user_data['isPayrollEmployee'] = 0;
        $user_data['ECode'] = $this->sequence->sequence_generator('EMP', 0, $company_data['company_id'], $company_data['company_code']);
        $this->db->insert('srp_employeesdetails', $user_data);
        $user_id = $this->db->insert_id();

        $user_data2['UserName'] = trim($this->input->post('EEmail'));
        $user_data2['Password'] = md5(trim($this->input->post('Password')));
        $user_data2['companyID'] = trim($this->input->post('companyid'));
        $user_data2['isSystemAdmin'] = 1;
        $user_data2['email'] = trim($this->input->post('EEmail'));
        $user_data2['empID'] = $user_id;
        $this->main = $this->load->database('db2', TRUE);
        $this->main->insert('user', $user_data2);

        $audit_log = []; $userID = current_userID(); $date_time = date('Y-m-d H:i:s');
        foreach ($user_data2 as $column => $val) {
            $audit_log[] = [
                'tableName' => 'user', 'columnName'=> $column, 'old_val'=> '', 'display_old_val'=> '',
                'new_val'=> $val, 'display_new_val'=> $val, 'rowID'=> $user_id, 'companyID'=> $companyID,
                'userID'=> $userID, 'timestamp'=> $date_time,
            ];
        }

        $this->main->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->insert('srp_erp_employeenavigation', array('userGroupID' => trim($this->input->post('user_group_id')), 'empID' => $user_id, 'companyID' => $company_data['company_id']));
        $user_group_id = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'User : ' . $user_data['UserName'] . '  Saved Failed '];
        } else {
            $this->db->trans_commit();
            return array('s', 'User : ' . $user_data['UserName'] . ' Saved Successfully.', 'last_id' => $user_id);
        }
    }

    function make_admin()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->SELECT("userGroupID");
        $this->db->where('description', 'Administrator');
        $this->db->where('companyID', $this->input->post('companyid'));
        $this->db->FROM('srp_erp_usergroups');
        $userGroupID = $this->db->get()->row('userGroupID');

        $this->db->insert('srp_erp_employeenavigation', array('userGroupID' => $userGroupID, 'empID' => trim($this->input->post('emp_id')), 'companyID' => trim($this->input->post('companyid'))));

        $user_id = trim($this->input->post('emp_id'));
        $approvals = array();
        $this->db->select('documentID,document');
        $this->db->where('companyID', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_documentcodemaster');
        $code = $this->db->get()->result_array();
        for ($i = 0; $i < count($code); $i++) {
            $approvals[$i]['levelNo'] = 1;
            $approvals[$i]['documentID'] = $code[$i]['documentID'];
            $approvals[$i]['document'] = $code[$i]['document'];
            $approvals[$i]['employeeID'] = $user_id;
            $approvals[$i]['employeeName'] = 'Admin';
            $approvals[$i]['companyID'] = $this->input->post('companyid');
            $approvals[$i]['companyCode'] = '';
            $approvals[$i]['Status'] = 1;
            $approvals[$i]['createdUserGroup'] = 'Admin';
            $approvals[$i]['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $approvals[$i]['createdUserID'] = null;//$this->session->userdata("username");
            $approvals[$i]['createdUserName'] = null;//$this->session->userdata("username");
            $approvals[$i]['modifiedDateTime'] = date('Y-m-d h:i:s');
            $approvals[$i]['createdDateTime'] = $approvals[$i]['modifiedDateTime'];
            $approvals[$i]['modifiedPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $approvals[$i]['modifiedUserID'] = null;//$this->session->userdata("username");
            $approvals[$i]['modifiedUserName'] = null;//$this->session->userdata("username");
        }
        if (!empty($approvals)) {
            $approvals = array_values($approvals);
            $this->db->insert_batch('srp_erp_approvalusers', $approvals);
        }
        return array('status' => true, 'last_id' => null);
    }

    function save_segment()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $data['description'] = trim($this->input->post('description'));
        $data['segmentCode'] = trim($this->input->post('segmentcode'));
        $data['companyCode'] = $company_data['company_code'];
        $data['companyID'] = $company_data['company_id'];

        if (trim($this->input->post('segmentID'))) {
            $this->db->where('segmentID', trim($this->input->post('segmentID')));
            $this->db->update('srp_erp_segment', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Segment Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Segment Update Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('segmentID'));
            }
        } else {
            $checkExist = $this->db->query("select * from srp_erp_segment where companyID =  " . $data['companyID'] . " AND segmentCode = '" . $this->input->post('segmentcode') . "'")->row_array();
            if (!empty($checkExist)) {
                $this->session->set_flashdata('e', 'Segment Code already exist');
                return array('status' => false);
            } else {
                $this->load->library('sequence', $this->get_db_array());
                $this->db->insert('srp_erp_segment', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Segment Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Segment Save Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function save_financial_year()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $x = 0;
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $this->db->select('beginingDate,endingDate,companyFinanceYearID,isCurrent');
        $this->db->where('companyID', $company_data['company_id']);
        $this->db->where('isCurrent', 1);
        $this->db->from('srp_erp_companyfinanceyear');
        $iscurrentChk = $this->db->get()->row_array();

        $company_id = $company_data['company_id'];
        $start_date = trim($this->input->post('beginningdate'));
        $end_date = trim($this->input->post('endingdate'));

        $chkFinanceYear = $this->db->query("SELECT companyFinanceYearID,beginingDate,endingDate,companyID FROM srp_erp_companyfinanceyear WHERE companyID = {$company_id} 
                                         AND ('{$start_date}' BETWEEN beginingDate AND endingDate OR '{$end_date}' BETWEEN beginingDate AND endingDate)")->row_array();

        if (!empty($chkFinanceYear)) {
            return ['e', 'Financial Year already created !'];
        }

        $data['beginingDate'] = $start_date;
        $data['endingDate'] = $end_date;
        $data['comments'] = trim($this->input->post('comments'));
        $data['isActive'] = 1;
        $data['isClosed'] = 0;
        if (is_array($iscurrentChk) && $iscurrentChk['isCurrent'] == 1) {
            $data['isCurrent'] = 0;
        } else {
            $data['isCurrent'] = 1;
        }
        $data['companyCode'] = $company_data['company_code'];
        $data['companyID'] = $company_id;
        $data['createdUserGroup'] = null;
        $data['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $data['createdUserID'] = null;//$this->session->userdata("username");
        $data['createdUserName'] = null;//$this->session->userdata("username");
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');
        $data['createdDateTime'] = $data['modifiedDateTime'];
        $data['modifiedPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $data['modifiedUserID'] = null;//$this->session->userdata("username");
        $data['modifiedUserName'] = null;//$this->session->userdata("username");

        $this->db->insert('srp_erp_companyfinanceyear', $data);
        $last_id = $this->db->insert_id();
        $date_arr = array();
        $first_date = $this->input->post('beginningdate');
        $next_date = $this->input->post('endingdate');
        while ($first_date <= $next_date) {
            $last_date = date("Y-m-t", strtotime($first_date));
            array_push($date_arr, array('dateFrom' => $first_date, 'dateTo' => $last_date, 'companyFinanceYearID' => $last_id, 'companyID' => $data['companyID'], 'companyCode' => $data['companyCode'], 'isActive' => 0));
            $first_date = date("Y-m-d", strtotime($first_date . '+ 1 month'));
            $x++;
        }
        $date_arr = array_values($date_arr);
        $this->db->insert_batch('srp_erp_companyfinanceperiod', $date_arr);
        if (is_array($iscurrentChk) && $iscurrentChk['isCurrent'] == 1) {
            $data = array(
                'companyFinanceYear' => trim($iscurrentChk['beginingDate'] . " - " . trim($iscurrentChk['endingDate'])),
                'companyFinanceYearID' => $iscurrentChk['companyFinanceYearID'],
            );

            $this->db->where("company_id", trim($this->input->post('companyid')));
            $this->db->update("srp_erp_company", $data);
        } else {
            $data = array(
                'companyFinanceYear' => trim($this->input->post('beginningdate') . " - " . trim($this->input->post('endingdate'))),
                'companyFinanceYearID' => $last_id,
            );

            $this->db->where("company_id", trim($this->input->post('companyid')));
            $this->db->update("srp_erp_company", $data);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in Financial year create process<p>' . $this->db->_error_message()];
        } else {
            $this->db->trans_commit();
            return ['s', 'Financial year created successfully with Finance Period.', 'last_id' => $last_id];
        }
    }

    function save_warehousemaster()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $data['wareHouseCode'] = trim($this->input->post('warehousecode'));
        $data['wareHouseDescription'] = trim($this->input->post('warehousedescription'));
        $data['wareHouseLocation'] = trim($this->input->post('warehouselocation'));
        $data['warehouseAddress'] = trim($this->input->post('warehouseAddress'));
        $data['warehouseTel'] = trim($this->input->post('warehouseTel'));
        $data['companyCode'] = $company_data['company_code'];
        $data['companyID'] = $company_data['company_id'];
        $data['createdUserGroup'] = '1';
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdUserName'] = current_userName();
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');
        $data['createdDateTime'] = $data['modifiedDateTime'];


        if (trim($this->input->post('wareHouseAutoID') ?? '')) {
            $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID')));
            $this->db->update('srp_erp_warehousemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Warehouse Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Warehouse Update Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('segmentID'));
            }
        } else {
            $checkExist = $this->db->query("select * from srp_erp_warehousemaster where companyID =  " . $data['companyID'] . " AND wareHouseCode = '" . $this->input->post('warehousecode') . "'")->row_array();
            if (!empty($checkExist)) {
                $this->session->set_flashdata('e', 'Warehouse Code already exist');
                return array('status' => false);
            } else {
                $this->db->insert('srp_erp_warehousemaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Warehouse Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Warehouse Save Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function fetch_assigned_currency()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('srp_erp_companycurrencyassign.CurrencyCode,srp_erp_companycurrencyassign.DecimalPlaces ,srp_erp_currencymaster.CurrencyName,currencyassignAutoID');
        $this->db->from('srp_erp_companycurrencyassign');
        $this->db->where('companyID', trim($this->input->post('companyid')));
        $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyassign.currencyID');
        return $this->db->get()->result_array();
    }

    function detail_assignedcurrency_company()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $companyID = $this->input->post('companyid');
        $mastercurrencyassignAutoID = $this->input->post('mastercurrencyassignAutoID');
        $output = $this->db->query("SELECT mastercurrencyassignAutoID,subcurrencyassignAutoID,currencyConversionAutoID,m.CurrencyName as baseCurrency,s.CurrencyName as subCurrency,conversion FROM srp_erp_companycurrencyconversion LEFT JOIN srp_erp_currencymaster  m on m.CurrencyID=masterCurrencyID LEFT JOIN srp_erp_currencymaster  s on s.CurrencyID=subCurrencyID WHERE companyID = {$companyID} AND mastercurrencyassignAutoID = {$mastercurrencyassignAutoID}")->result_array();
        return $output;
    }

    function update_currencyexchange()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $currencyConversionAutoID = $this->input->post('currencyConversionAutoID');
        $mastercurrencyassignAutoID = $this->input->post('mastercurrencyassignAutoID');
        $subcurrencyassignAutoID = $this->input->post('subcurrencyassignAutoID');
        $conversion = $this->input->post('conversion');

        $masterconversion = round($conversion, 8);
        $subConversion = 1 / $conversion;
        $subConversion = round($subConversion, 8);
        $subData = array('conversion' => $subConversion);
        $masterData = array('conversion' => $masterconversion);

        $update = $this->db->update('srp_erp_companycurrencyconversion', $masterData, array('mastercurrencyassignAutoID' => $mastercurrencyassignAutoID, 'subcurrencyassignAutoID' => $subcurrencyassignAutoID));
        if ($update) {
            $update = $this->db->update('srp_erp_companycurrencyconversion', $subData, array('mastercurrencyassignAutoID' => $subcurrencyassignAutoID, 'subcurrencyassignAutoID' => $mastercurrencyassignAutoID));
            $this->session->set_flashdata('s', 'Company Conversion updated Successfully.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Updated failed.');
            return true;
        }
    }

    function dropdown_currencyAssignedExchangeDropdown()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $companyID = trim($this->input->post('companyid'));
        $data = $this->db->query("SELECT srp_erp_currencymaster.currencyID,concat(srp_erp_currencymaster.CurrencyCode,' | ',srp_erp_currencymaster.CurrencyName) as currencyName FROM srp_erp_companycurrencyassign LEFT JOIN srp_erp_currencymaster on srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID WHERE companyID = {$companyID}")->result_array();
        $data_arr = array('' => 'Select Currency');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['currencyID'])] = trim($row['currencyName']);
            }
        }
        return $data_arr;
    }

    function fetch_template_data($companyid)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $data = array();
        $this->db->select('*');
        $this->db->where('company_id', $companyid);
        $this->db->from('srp_erp_company');
        $data['company'] = $this->db->get()->row_array();
        $this->db->select('UserName,ECode,EDOJ,Gender,Ename1,Ename2');
        $this->db->where('Erp_companyID', $companyid);
        $this->db->from('srp_employeesdetails');
        $data['employee'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('companyID', $companyid);
        $this->db->from('srp_erp_segment');
        $data['segment'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('companyID', $companyid);
        $this->db->from('srp_erp_warehousemaster');
        $data['warehouse'] = $this->db->get()->result_array();
        return $data;
    }

    function company_confirmation()
    {
        //*************Updating MainDB Start*************
        $companyID = $this->input->post('companyid');
        $date_time = date('Y-m-d H:i:s');
        $user_id = current_userID();

        $data_maindb = array(
            'confirmedYN' => 1,
            //'confirmedDate'      => $this->common_data['current_date'],
            //'confirmedByEmpID'   => $this->common_data['current_userID'],
            //'confirmedByName'    => $this->common_data['current_user']
        );

        $old_val = $this->db->get_where('srp_erp_company', ['company_id'=> $companyID])->row('confirmedYN');

        $this->db->where('company_id', $companyID)->update('srp_erp_company', $data_maindb);

        $audit_log = [
            'tableName' => 'subscription_invoice_master', 'columnName'=> 'confirmedYN', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> 1, 'display_new_val'=> 1,
            'rowID'=> $companyID, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->insert('srp_erp_audit_log', $audit_log);


        //*************Updating MainDB End*************

        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $data = array(
            'confirmedYN' => 1,
            //'confirmedDate'      => $this->common_data['current_date'],
            //'confirmedByEmpID'   => $this->common_data['current_userID'],
            //'confirmedByName'    => $this->common_data['current_user']
        );
        $this->db->where('company_id', $this->input->post('companyid'));
        $this->db->update('srp_erp_company', $data);

        $this->db->query("UPDATE srp_erp_company
JOIN srp_erp_countrymaster ON srp_erp_company.company_country=srp_erp_countrymaster.CountryDes
set srp_erp_company.countryID=srp_erp_countrymaster.countryID");

        $this->db->query("UPDATE srp_nationality
JOIN srp_erp_countrymaster on srp_nationality.nationality=srp_erp_countrymaster.nationality
set srp_nationality.countryID=srp_erp_countrymaster.countryID");

       $this->db->query("
INSERT INTO srp_erp_pay_templatefields (
    fieldName,
    caption,
    fieldType,
    salaryCatID,
    companyID,
    companyCode
) (
    SELECT
        salary.salaryDescription AS fieldName,
        salary.salaryDescription AS caption,
        salary.salarycategoryType AS fieldType,
        salary.salaryCategoryID AS salaryCatID,
        salary.companyID AS companyID,
        salary.companycode AS companyCode
    FROM
        srp_erp_pay_salarycategories salary
    WHERE
        salary.salaryCategoryID NOT IN (
            SELECT
                ifnull(salarycatID, 0)
            FROM
                srp_erp_pay_templatefields
            GROUP BY
                salaryCatID
        )
)");


        $this->session->set_flashdata('s', 'Company Conform updated Successfully.');
        return true;
    }

    function load_country_drop()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->main->SELECT("countryID,CountryDes,countryShortCode");
        $this->main->FROM('srp_erp_countrymaster');
        return $this->main->get()->result_array();
    }

    function fetch_assigned_modulus()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $companyid = $this->input->post('companyid');
        $data = $this->db->query("SELECT nm.navigationMenuID, `description`,
                                  CASE 
                                    WHEN ma.navigationMenuID IS NOT NULL THEN 1 
                                    ELSE 0 
                                  END AS `status` 
                                  FROM srp_erp_navigationmenus nm 
                                  LEFT JOIN (
                                      SELECT navigationMenuID FROM srp_erp_moduleassign WHERE companyid='{$companyid}'
                                  ) ma ON (nm.navigationMenuID = ma.navigationMenuID) 
                                  WHERE nm.masterID IS NULL")->result_array();
        return $data;
    }

    function remove_modul()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->delete('srp_erp_moduleassign', array('navigationMenuID' => $this->input->post('navigationMenuID'), 'companyID' => $this->input->post('companyid')));
        return true;
    }

    function save_nav_menu()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $nav = array();
        $companyID = trim($this->input->post('companyid'));
        $navigationMenuID = $this->input->post('navigationMenuID');

        $count = count($navigationMenuID);
        $navigation_ID = null;
        for ($i = 0; $i < $count; $i++) {
            $nav[$i]['navigationMenuID'] = $navigationMenuID[$i];
            $nav[$i]['companyID'] = $companyID;
            if (($count - 1) > $i) {
                $navigation_ID .= $navigationMenuID[$i] . ',';
            } else {
                $navigation_ID .= $navigationMenuID[$i];
            }
        }

        if (!empty($nav)) {
            $this->db->delete('srp_erp_moduleassign', array('companyID' => $companyID));
            $nav = array_values($nav);
            $this->db->insert_batch('srp_erp_moduleassign', $nav);
        }

        /*get all default user group*/

        $usergroupArr = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$companyID} AND description IN ('CEO' , 'CFO', 'Finance Manager', 'Purchasing Manager', 'HR Manager','Operation Manager', 'Sales Manager','Administrator') ")->result_array();

        if (!empty($usergroupArr)) {
            foreach ($usergroupArr as $row) {
                /*delete previous links*/
                $this->db->delete('srp_erp_navigationusergroupsetup', array('userGroupID' => $row['userGroupID']));

                $navigationMenuID = $this->input->post('navigationMenuID');

                switch ($row['description']) {
                    case 'CEO':
                    case 'CFO':
                    case 'Administrator':
                        $navigation_ID = implode(', ', $navigationMenuID);
                        break;
                    case 'Finance Manager':
                        //remove HRMS
                        $hrms = array_search(38, $navigationMenuID);
                        unset($navigationMenuID[$hrms]);
                        $navigation_ID = implode(', ', $navigationMenuID);
                        break;
                    case 'HR Manager':
                        $array = array(38, 29, 329); //HRMS , My Profile ,Dashboard
                        $result = array_intersect($navigationMenuID, $array);
                        $navigation_ID = implode(', ', $result);
                        break;
                    case 'Sales Manager':
                        $array = array(361, 34, 29);  // - Sales & Marketing , Account Receivable , Dashboard
                        $result = array_intersect($navigationMenuID, $array);
                        $navigation_ID = implode(', ', $result);
                        break;

                }

                if (empty($navigation_ID)) {
                    continue; //continue if value empty;
                }
                $data_nav = $this->db->query("SELECT srp_erp_navigationmenus.navigationMenuID,srp_erp_navigationmenus.levelNo,srp_erp_navigationmenus.sortOrder, 
                        IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID 
                        FROM srp_erp_navigationmenus 
                        LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID 
                        AND userGroupID = {$row['userGroupID']} 
                        WHERE srp_erp_navigationmenus.navigationMenuID NOT IN (
                            SELECT srp_erp_navigationmenus.navigationMenuID FROM srp_erp_navigationmenus 
                            LEFT JOIN `srp_erp_moduleassign` ON srp_erp_navigationmenus.navigationMenuID = srp_erp_moduleassign.navigationMenuID 
                            AND companyID = '{$companyID}' AND srp_erp_navigationmenus.navigationMenuID IN ({$navigation_ID})  
                            WHERE masterID IS NULL AND srp_erp_moduleassign.moduleID IS NULL
                        ) ORDER BY levelNo , sortOrder")->result_array();


                $navigationID = null;
                $nav_count = count($data_nav);
                for ($i = 0; $i < $nav_count; $i++) {
                    if (($nav_count - 1) > $i) {
                        $navigationID .= $data_nav[$i]['navigationMenuID'] . ',';
                    } else {
                        $navigationID .= $data_nav[$i]['navigationMenuID'];
                    }
                }

                $this->db->query("INSERT srp_erp_navigationusergroupsetup (userGroupID ,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist) SELECT {$row['userGroupID']},navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist FROM srp_erp_navigationmenus WHERE navigationMenuID IN ({$navigationID})");
            }
        }

        /* assigne approval user according to seleted module*/

        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $this->db->select('EIdNo,Ename1,Ename2');
        $this->db->where('Erp_companyID', trim($this->input->post('companyid')));
        $this->db->where('isSystemAdmin', 1);
        $this->db->from('srp_employeesdetails');
        $users = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->join('srp_erp_documentcodes', 'srp_erp_documentcodes.documentID=srp_erp_approvalusers.documentID', 'inner');
        $this->db->where('companyID', trim($this->input->post('companyid')));
        $this->db->where(' (srp_erp_documentcodes.moduleID = 0 OR srp_erp_documentcodes.moduleID IS NOT NULL) ');
        $this->db->group_by('moduleID');
        $this->db->from('srp_erp_approvalusers');
        $exist = $this->db->get()->result_array();

        $this->db->select('documentID,document,moduleID');
        $this->db->where_in('moduleID', $navigationMenuID);
        $this->db->from('srp_erp_documentcodes');
        $code = $this->db->get()->result_array();

        if (empty($exist)) {
            if (!empty($users)) {
                $this->db->select('documentID,document');
                $this->db->where_in('moduleID', $navigationMenuID);
                $this->db->from('srp_erp_documentcodes');
                $code = $this->db->get()->result_array();
                $approvals = array();
                foreach ($users as $user) {
                    for ($i = 0; $i < count($code); $i++) {
                        $approvals[$i]['levelNo'] = 1;
                        $approvals[$i]['documentID'] = $code[$i]['documentID'];
                        $approvals[$i]['document'] = $code[$i]['document'];
                        $approvals[$i]['employeeID'] = $user["EIdNo"];
                        $approvals[$i]['employeeName'] = $user['Ename1'] . ' ' . $user['Ename2'];
                        $approvals[$i]['companyID'] = $company_data['company_id'];
                        $approvals[$i]['companyCode'] = $company_data['company_code'];
                        $approvals[$i]['Status'] = 1;
                        $approvals[$i]['createdUserGroup'] = NULL;
                        $approvals[$i]['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $approvals[$i]['createdUserID'] = null;
                        $approvals[$i]['createdUserName'] = null;
                        $approvals[$i]['createdDateTime'] = date('Y-m-d H:i:s');
                    }
                }
                if (!empty($approvals)) {
                    $approvals = array_values($approvals);
                    $this->db->insert_batch('srp_erp_approvalusers', $approvals);
                }
            }
        }
        else {
            $moduleIDs = array_column($exist, 'moduleID');
            $arrayDiff = array_diff($moduleIDs, $navigationMenuID);

            if ($arrayDiff) {
                $arrayDiff = join(',', $arrayDiff);
                $this->db->query("DELETE srp_erp_approvalusers FROM srp_erp_approvalusers 
                                INNER JOIN srp_erp_documentcodes ON srp_erp_documentcodes.documentID=srp_erp_approvalusers.documentID 
                                WHERE moduleID IN({$arrayDiff}) AND companyID = " . trim($this->input->post('companyid')));
            }

            $arrayDiff2 = array_diff($navigationMenuID, $moduleIDs);
            if ($arrayDiff2) {
                $this->db->select('documentID,document');
                $this->db->where_in('moduleID', $arrayDiff2);
                $this->db->from('srp_erp_documentcodes');
                $code = $this->db->get()->result_array();

                $approvals2 = array();
                if (!empty($users)) {
                    foreach ($users as $user) {
                        for ($i = 0; $i < count($code); $i++) {
                            $approvals2[$i]['levelNo'] = 1;
                            $approvals2[$i]['documentID'] = $code[$i]['documentID'];
                            $approvals2[$i]['document'] = $code[$i]['document'];
                            $approvals2[$i]['employeeID'] = $user["EIdNo"];
                            $approvals2[$i]['employeeName'] = $user['Ename1'] . ' ' . $user['Ename2'];
                            $approvals2[$i]['companyID'] = $company_data['company_id'];
                            $approvals2[$i]['companyCode'] = $company_data['company_code'];
                            $approvals2[$i]['Status'] = 1;
                            $approvals2[$i]['createdUserGroup'] = NULL;
                            $approvals2[$i]['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                            $approvals2[$i]['createdUserID'] = null;
                            $approvals2[$i]['createdUserName'] = null;
                            $approvals2[$i]['createdDateTime'] = date('Y-m-d H:i:s');
                        }
                    }
                }
                if (!empty($approvals2)) {
                    $approvals2 = array_values($approvals2);
                    $this->db->insert_batch('srp_erp_approvalusers', $approvals2);
                }
            }
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Successfully Navigations updated to this group');
        }

        return true;
    }

    /** created by Shafri */
    function loadAllCompanies()
    {
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("*");
        $this->main->from("srp_erp_company");
        $r = $this->main->get()->result_array();
        return $r;
    }

    function load_srp_erp_companyadminmaster($companyID)
    {
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("*");
        $this->main->from("srp_erp_companyadminmaster");
        $this->main->where("companyID", $companyID);
        $this->main->order_by("adminMasterID", "desc");
        $r = $this->main->get()->result_array();
        return $r;
    }

    function get_srp_erp_company_specific($companyID)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where("company_id", $companyID);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function save_companyAdmin($data)
    {
        $this->main = $this->load->database('db2', TRUE);
        $result = $this->main->insert('srp_erp_companyadminmaster', $data);
        return $result;
    }

    function get_srp_erp_companyadminmaster_specific($id)
    {
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("*");
        $this->main->from("srp_erp_companyadminmaster");
        $this->main->where("adminMasterID", $id);
        $r = $this->main->get()->row_array();
        return $r;
    }

    function update_pin($id, $pin)
    {
        $this->main = $this->load->database('db2', TRUE);
        $data = array(
            'pinNumber' => $pin,
        );
        $this->main->where('adminMasterID', $id);
        $result = $this->main->update('srp_erp_companyadminmaster', $data);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    function set_company_credential($companyID = null)
    {
        $this->main = $this->load->database('db2', TRUE);
        $this->main->select("*");
        $this->main->from("srp_erp_company");
        $this->main->where("company_id", $companyID);
        $r = $this->main->get()->row_array();
        if ($r) {
            $this->db_host = $r['host'];
            $this->db_name = $r['db_name'];
            $this->db_password = $r['db_password'];
            $this->db_username = $r['db_username'];
        }
    }

    function save_addNewcurrencyExchange()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', trim($this->input->post('companyid')));
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $mastercurrencyassignAutoID = $this->input->post('mastercurrencyassignAutoID');
        $currency = $this->input->post('currency');
        $conversion = $this->input->post('conversion');
        $companyID = $company_data['company_id'];
        $companyCode = $company_data['company_code'];
        $masterconversion = round($conversion, 8);
        $subConversion = 1 / $conversion;
        $subConversion = round($subConversion, 8);

        $exist = $this->db->query("SELECT * FROM srp_erp_companycurrencyconversion WHERE companyID = {$companyID} AND mastercurrencyassignAutoID = {$mastercurrencyassignAutoID} AND subCurrencyID={$currency}")->result_array();
        if (!empty($exist)) {
            $this->session->set_flashdata('e', 'The selected currency already exist. ');
            return false;
        }

        $master = $this->db->query("select * from srp_erp_companycurrencyassign where companyID={$companyID} AND currencyassignAutoID={$mastercurrencyassignAutoID}")->row_array();
        $sub = $this->db->query("select * from srp_erp_companycurrencyassign where companyID={$companyID} AND currencyID={$currency}")->row_array();

        $data = array();
        $data[0]['companyID'] = $companyID;
        $data[0]['companyCode'] = $companyCode;
        $data[0]['mastercurrencyassignAutoID'] = $master['currencyassignAutoID'];
        $data[0]['masterCurrencyID'] = $master['currencyID'];
        $data[0]['masterCurrencyCode'] = $master['CurrencyCode'];
        $data[0]['subcurrencyassignAutoID'] = $sub['currencyassignAutoID'];
        $data[0]['subCurrencyID'] = $sub['currencyID'];
        $data[0]['subCurrencyCode'] = $sub['CurrencyCode'];
        $data[0]['conversion'] = $masterconversion;

        if ($master['currencyID'] != $sub['currencyID']) {
            $data[1]['companyID'] = $companyID;
            $data[1]['companyCode'] = $companyCode;
            $data[1]['mastercurrencyassignAutoID'] = $sub['currencyassignAutoID'];
            $data[1]['masterCurrencyID'] = $sub['currencyID'];
            $data[1]['masterCurrencyCode'] = $sub['CurrencyCode'];
            $data[1]['subcurrencyassignAutoID'] = $master['currencyassignAutoID'];
            $data[1]['subCurrencyID'] = $master['currencyID'];
            $data[1]['subCurrencyCode'] = $master['CurrencyCode'];
            $data[1]['conversion'] = $subConversion;
        }
        $this->db->insert_batch('srp_erp_companycurrencyconversion', $data);
        $this->session->set_flashdata('s', 'Recods Inserted Successfully.');
        return true;
    }

    function showAllmodules()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        return $this->db->query("select * from `srp_erp_navigationmenus` WHERE isAddon = 1 OR isAddon=2")->result_array();
    }

    function showAllInvoicesByCompanyID()
    {
        $companyID = $this->input->post('companyid');
        $this->main->select("*, companysubscriptionhistory.timestamp as sub_update_datetime");
        $this->main->from("companysubscriptionhistory");
        $this->main->join('srp_erp_company', 'srp_erp_company.company_id = companysubscriptionhistory.companyID');
        $this->main->where("companyID", $companyID);
        $r = $this->main->get()->result_array();
        return $r;
    }

    function getModuleDetail()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $navigationMenuID = $this->input->post('navID');
        $detail = $this->db->query("select addonDetails from `srp_erp_navigationmenus` where navigationMenuID={$navigationMenuID}")->row_array();
        return array($detail['addonDetails']);
    }

    function update_moduleDescirption()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $navigationMenuID = $this->input->post('navigationMenuID');
        $description = $this->input->post('description');
        $this->db->update('srp_erp_navigationmenus', array('addonDetails' => $description), array('navigationMenuID' => $navigationMenuID));
        if ($this->db->affected_rows() > 0) {
            return array('error' => 0, 'message' => 'Updated');

        } else {
            return array('error' => 1, 'message' => 'Failed');
        }
    }

    function load_navigation_usergroup_setup()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $data = $this->db->query("SELECT srp_erp_navigationmenus.* FROM srp_erp_navigationmenus  ORDER BY levelNo , sortOrder")->result_array();
        return $data;
    }

    function save_navigation()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $level = $this->input->post('level');
        $icon = $this->input->post('icon');
        $pagetitle = $this->input->post('pagetitle');
        $description = $this->input->post('description');
        $comid = $this->input->post('companyid');
        $subexist = $this->input->post('subexist');
        $type = $this->input->post('type');
        if ($level == 0) {
            $sortOrder = $this->db->query("SELECT  MAX(sortOrder) as sortOrder from srp_erp_navigationmenus WHERE masterID IS NULL ")->row_array();
            $data = array(
                'description' => $description,
                //'masterID' => null,
                'url' => $this->input->post('url'),
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder'] + 1,
                'isSubExist' => $subexist,
                'addonDescription' => $description,
            );
            $result = $this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID = $this->db->insert_id();
            if ($result) {
                if ($subexist == 0) {
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat = $this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID = $this->db->insert_id();
                    if ($resultFrmCat) {
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster = $this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID = $this->db->insert_id();
                        if ($resultTemMaster) {
                            $dataTempMaster = array(
                                'companyID' => $comid,
                                'TempMasterID' => $lastTempMasterID,
                                'FormCatID' => $lastFormCatID,
                                'navigationMenuID' => $lastNavigationMenuID,
                            );
                            $resultTemps = $this->db->insert('srp_erp_templates', $dataTempMaster);
                            if ($resultTemps) {
                                return array('s', ' Saved Successfully ');
                            }
                        }
                    }
                } else {
                    return array('s', ' Saved Successfully ');
                }
            } else {
                return array('s', ' Error in saving record ');
            }
        } else if ($level == 1) {
            $modules = $this->input->post('modules');
            $url = $this->input->post('url');
            $sortOrder = $this->db->query("SELECT  IFNULL(MAX(sortOrder),0) as sortOrder from srp_erp_navigationmenus WHERE masterID='$modules' ")->row_array();
            $data = array(
                'description' => $description,
                'masterID' => $modules,
                'url' => $url,
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder'] + 1,
                'isSubExist' => $subexist,
                'addonDescription' => $description,
            );
            $result = $this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID = $this->db->insert_id();
            if ($result) {
                if ($subexist == 0) {
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat = $this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID = $this->db->insert_id();
                    if ($resultFrmCat) {
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster = $this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID = $this->db->insert_id();
                        if ($resultTemMaster) {
                            $dataTempMaster = array(
                                'companyID' => $comid,
                                'TempMasterID' => $lastTempMasterID,
                                'FormCatID' => $lastFormCatID,
                                'navigationMenuID' => $lastNavigationMenuID,
                            );
                            $resultTemps = $this->db->insert('srp_erp_templates', $dataTempMaster);
                            if ($resultTemps) {
                                return array('s', ' Saved Successfully ');
                            }
                        }
                    }
                } else {
                    return array('s', ' Saved Successfully ');
                }
            } else {
                return array('s', ' Error in saving record ');
            }
        } else if ($level == 2) {
            $modules = $this->input->post('modules');
            $masters = $this->input->post('masters');
            $url = $this->input->post('url');
            $sortOrder = $this->db->query("SELECT  IFNULL(MAX(sortOrder),0) as sortOrder from srp_erp_navigationmenus WHERE masterID='$masters' ")->row_array();
            $data = array(
                'description' => $description,
                'masterID' => $masters,
                'url' => $url,
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder'] + 1,
                'isSubExist' => 0,
                'addonDescription' => $description,
            );
            $result = $this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID = $this->db->insert_id();
            if ($result) {
                if ($subexist == 0) {
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat = $this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID = $this->db->insert_id();
                    if ($resultFrmCat) {
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster = $this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID = $this->db->insert_id();
                        if ($resultTemMaster) {
                            $dataTempMaster = array(
                                'companyID' => $comid,
                                'TempMasterID' => $lastTempMasterID,
                                'FormCatID' => $lastFormCatID,
                                'navigationMenuID' => $lastNavigationMenuID,
                            );
                            $resultTemps = $this->db->insert('srp_erp_templates', $dataTempMaster);
                            if ($resultTemps) {
                                return array('s', ' Saved Successfully ');
                            }
                        }
                    }
                } else {
                    return array('s', ' Saved Successfully ');
                }
            } else {
                return array('s', ' Error in saving record ');
            }
        }
    }

    function all_currency_drop($status = true)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select("currencyID,CurrencyCode,CurrencyName");
        $this->db->from('srp_erp_currencymaster');
        $currency = $this->db->get()->result_array();
        $currency_arr = array('' => 'Select Currency');
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']);
            }
        }
        return $currency_arr;
    }

    function load_navigation_module($id = false, $state = true) /*$id parameter is used to display only ID as value in select option*/
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->select('navigationMenuID,description,masterID');
        $this->db->from('srp_erp_navigationmenus');
        $this->db->where('masterID', null);
        $data = $this->db->get()->result_array();
        if ($state == true) {
            $data_arr = array('' => 'Select Module');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['navigationMenuID'])] = trim($row['description']);

            }
        }
        return $data_arr;
    }

    function load_master()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $masterID = $this->input->post('modules');
        $result = $this->db->query("SELECT navigationMenuID,description,masterID from srp_erp_navigationmenus WHERE masterID='$masterID' and isSubExist=1 ")->result_array();
        return $result;
    }

    function generate_subscription_inv_no($is_both = 0)
    {
        $type = current_userType();
        
        $serialNo = $this->db->query("SELECT MAX(serialNo) AS serialNo 
                                      FROM subscription_invoice_master AS inv_mas
                                      JOIN srp_erp_company AS com ON com.company_id = inv_mas.companyID
                                      WHERE com.adminType = {$type}")->row('serialNo');
        $serialNo += 1;
        $invNo = str_pad($serialNo, 5, '0', STR_PAD_LEFT);
        $invNo = "INV{$invNo}";

        return ($is_both == 1) ? ['inv_no' => $invNo, 'serialNo' => $serialNo] : $invNo;
    }

    function QHSE_api_requests($companyID, $data, $req_url, $is_put=false){
        $db2 = $this->load->database('db2', TRUE);
        $authorization = $db2->query("SELECT `key` FROM `keys` WHERE company_id = {$companyID} AND key_type = 'QHSE'")->row('key');

        if(empty($authorization)){
            return ['status'=> 'e', 'message'=> 'QHSE authorization key not configured.'];
        }

        $site_url = $this->config->item('QHSE_login_url');
        if(empty($site_url)){
            return ['status'=> 'e', 'message'=> 'QHSE site url not configured.' ];
        }

        $site_url .= $req_url;

        $headers = [
            'Authorization: QHSE '.$authorization,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt( $ch, CURLOPT_URL, $site_url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, ($is_put)? 'PUT': 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
        $response = curl_exec ( $ch );
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $msg = "Error: " . curl_error($ch);
            return [ 'status'=> 'e', 'message'=> $msg, 'http_code'=> $http_code ];
        }
        curl_close ( $ch );

        if(!in_array($http_code, ['200', '401', '404', '422', '500'])){
            return [ 'status'=> 'e', 'message'=> 'some thing went wrong,<br/>Please contact for system support', 'http_code'=> $http_code ];
        }

        $response = json_decode($response);

        $status = ($response->success != false)? 's': 'e';
        $rt_data = [
            'status'=> $status, 'message'=> $response->message, 'http_code'=> $http_code
        ];

        if($status == 's'){
            if(property_exists($response, 'data')){
                $rt_data['data'] = $response->data;
            }
        }

        if($http_code == '422'){ //Unprocessable Entity (Form validation failed)
            $msg = '';
            foreach ($rt_data['message'] as $row){
                $msg .= '<br/>'.implode('<br/>', $row);
            }
            $rt_data['message'] = $msg;
        }

        return $rt_data;
    }
}
