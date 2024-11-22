<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flush_data extends CI_Controller
{
    Private $main;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;

    public $date_time;
    
    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
        if (!$CI->session->has_userdata('sme_company_status')) {
            header('Location: ' . site_url('login/logout'));
        }

        $this->date_time = date('Y-m-d H:i:s');        
        $this->load->helper('configuration');
        $this->load->library('s3');
 

        $this->encryption->initialize(['driver' => 'mcrypt']);
        $companyID = $this->input->post('company_id');
        if(isset($companyID)) {
            $this->main = $this->load->database('db2', TRUE);

            $this->main->select("*")->from("srp_erp_company")->where("company_id", $companyID);
            $r = $this->main->get()->row_array();
            if (!empty($r)) {
                $this->db_host = $r['host'];
                $this->db_name = $r['db_name'];
                $this->db_password = $r['db_password'];
                $this->db_username = $r['db_username'];
            }
        }
    }

    function get_db_array($isDecrypted=false)
    {
        $this->db_host = trim($this->db_host);
        $this->db_username = trim($this->db_username);
        $this->db_password = trim($this->db_password);
        $this->db_name = trim($this->db_name);


        $config['hostname'] = ($isDecrypted)? $this->db_host : decryptData($this->db_host);
        $config['username'] = ($isDecrypted)? $this->db_username : decryptData($this->db_username);
        $config['password'] = ($isDecrypted)? $this->db_password : decryptData($this->db_password);
        $config['database'] = ($isDecrypted)? $this->db_name : decryptData($this->db_name);
        $config['dbdriver'] = 'mysqli';    
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        return $config;
    }

    function index(){
        $companyID = $this->uri->segment(2); 
        
        $this->db->select('*')->where(['company_id'=> $companyID]);
        $company_det = $this->db->get('srp_erp_company')->row_array();
        
        $data['title'] = 'Flush Data';
        $data['main_content'] = 'flush-data-views/flush-data-main-view';
        $data['companyID'] = $companyID;             
        $data['company_det'] = $company_det;             
        $data['extra'] = ['js_page' => 'flush-data-views/flush-data-js'];
        
        $this->load->view('include/template', $data);
    }

    function fetch_flush_headers(){
        $company_id = $this->input->post('company_id');
        $str = '<span class="pull-right">';
        $str .= '<span class="label label-success dataTableBtn" onclick="initialize_flush(this)" ';
        $str .= ' title="Flush"><i class="fa fa-minus"></i></span> &nbsp; ';
        $str .= '<span class="label label-primary dataTableBtn" onclick="view_det(this)" ';
        $str .= ' title="View Details"><i class="fa fa-eye"></i></span> &nbsp; ';        
        $str .= '<span class="label label-primary dataTableBtn" onclick="edit_flushHeader(this)"';
        $str .= ' title="Edit"><i class="fa fa-pencil"></i></span> &nbsp; ';    
        $str .= '<span class="label label-danger dataTableBtn" onclick="delete_master(this)"';
        $str .= ' title="Delete"><i class="fa fa-trash"></i></span>';
        $str .= '</span>';
        
        $this->datatables->select("id,description,createdDate,createdBy,flushStatus")
            ->from('flush_data_master')                             
            ->where('companyID', $company_id);
        $this->datatables->add_column('action', $str, 'id')
        ->add_column('flushStatus_lable', '$1', 'flushStatus_lable(flushStatus)');;
        echo $this->datatables->generate();
    }
    
    function save_flush_header(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('company_id', 'Company', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $flush_id = $this->input->post('flush_id');
        $description = trim($this->input->post('description'));
        $companyID = trim($this->input->post('company_id'));

        $centralDB = $this->main;

        $auto_id = null; $userID = current_userID(); $pc = current_pc();
        $old_val = '';
        if(!empty($flush_id)){
            $old_val = $centralDB->get_where('flush_data_master', ['id'=>$flush_id])->row('description');
            $auto_id = $flush_id;         
        }

        $audit_log = [
            'tableName' => 'flush_data_master', 'columnName'=> 'description', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> $description, 'display_new_val'=> $description,
            'rowID'=> &$auto_id, 'companyID'=> $companyID, 'userID'=> $userID, 'timestamp'=> $this->date_time,
        ];
 

        $centralDB->trans_start();

        if(empty($flush_id)){
            $data = [
                'description'=> $description, 'companyID'=> $companyID, 'createdPc'=> $pc, 
                'createdBy'=> $userID, 'createdDate'=> $this->date_time,
            ];

            $centralDB->insert('flush_data_master', $data);
            $auto_id = $centralDB->insert_id();
            $un_flush_modules = $this->config->item('un_flush_modules');

            $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

            $this->db->select('n.navigationMenuID AS moduleID, n.description AS moduleDes')
            ->join('srp_erp_navigationmenus AS n', 'n.navigationMenuID = m.navigationMenuID')
            ->where('m.companyID', $companyID);
            
            if($un_flush_modules){
                $this->db->where_not_in('n.navigationMenuID', $un_flush_modules);
            }
            $modules = $this->db->get('srp_erp_moduleassign AS m')->result_array();

            if(!empty($modules)){
                //Get commen tables like approvall, general ledger
                $centralDB->select('moduleID, trName AS moduleDes')->where('moduleID < 1');
                $common_tbls = $centralDB->get('module_related_tbl')->result_array();

                foreach ($common_tbls as $row) {
                    $modules[] = $row;
                }                 

                foreach ($modules as $key => $row) {
                    $modules[$key]['masterID'] = $auto_id;
                    $modules[$key]['sortOrder'] = ($key+1);
                    $modules[$key]['createdBy'] = $userID;
                    $modules[$key]['createdDate'] = $this->date_time;
                    $modules[$key]['createdPc'] = $pc;
                }                            

                $centralDB->insert_batch('flush_data_details', $modules);
            }

        }
        else{
            $data = [
                'description'=> $description, 'modifiedPc'=> $pc, 'modifiedBy'=> $userID,
                'modifiedDate'=> $this->date_time
            ];

            $centralDB->where('id', $flush_id)->update('flush_data_master', $data);
        }

        $centralDB->insert('srp_erp_audit_log', $audit_log);

        $centralDB->trans_complete();
        if($centralDB->trans_status() == true){
            echo json_encode(['s', 'Flush header create successfully.']);
        }else{
            echo json_encode(['e', 'Error in Flush header create process.']);
        }
    }

    function fetch_flush_modules(){
        $masterID  = $this->input->post('masterID');
        $str = '<span class="pull-right">';
        $str .= '<span class="label label-primary dataTableBtn" onclick="view_error_log(this)">';
        $str .= '<i class="fa fa-eye"></i></span> &nbsp; '; 
        $str .= '</span>';
        
        $this->datatables->select("id,masterID,moduleID,moduleDes,flushStatus,sortOrder")
            ->from('flush_data_details')                  
            ->where('masterID', $masterID );
        $this->datatables->add_column('action', $str, 'id')
            ->add_column('flushStatus_lable', '$1', 'flushStatus_lable(flushStatus)');
        echo $this->datatables->generate();
    }

    function initialize_flush(){
        $this->form_validation->set_rules('id', 'Flush id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $flush_id = $this->input->post('id');

        $this->db->select('moduleID, moduleDes')->where('flushStatus <> 1')
         ->where('masterID', $flush_id)->order_by('sortOrder');
        $module = $this->db->get('flush_data_details')->result_array();
        $module_count = count($module);

        if($module_count == 0){
            echo json_encode(['e', 'No modules found for flush.']);
        }
        else{
            echo json_encode(['s', '', 'module'=> $module, 'module_count'=> $module_count]);
        }
    }

    function flush(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('moduleID', 'Module ID', 'trim|required');    
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        sleep(1);
        
        $id = $this->input->post('masterID');
        $companyID = $this->input->post('company_id');
        $moduleID = $this->input->post('moduleID');        

        $centralDB = $this->main;        
        
        $centralDB->select('moduleID, moduleDes, flushStatus')->where([
            'masterID'=> $id, 'moduleID'=> $moduleID
        ]);
        $module_det = $centralDB->get('flush_data_details')->row_array();

        //Get module related datas
        $centralDB->select('*')->where('moduleID', $moduleID);
        $related_data = $centralDB->get('module_related_tbl')->result_array();                

        if($related_data){
            $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        }

        $this->db->trans_start();        
        
        $err_log = [];
        foreach ($related_data as $key => $det) {
            $trName = $det['trName'];
            $docCode = $det['docCode'];
            $tbl = $det['tbl'];         
            
            $mas_tbl = $mas_pk = $fk = '';
            switch ($tbl) {
                /* 
                below tables does not have companyID columns, to delete the records we need to join with
                their master tables.
                make sure when define these tables in the 'module_related_tbl' first define detail table 
                and then define their master table.

                Other wise we can not match the records (master table records get deleted at first)
                */
                case 'srp_erp_stockreturndetails':
                    $mas_tbl = 'srp_erp_stockreturnmaster';
                    $mas_pk = $fk = 'stockReturnAutoID';
                break;

                case 'srp_erp_stockadjustmentdetails':
                    $mas_tbl = 'srp_erp_stockadjustmentmaster';
                    $mas_pk = $fk = 'stockAdjustmentAutoID';
                break;

                case 'srp_erp_stockcountingdetails':
                    $mas_tbl = 'srp_erp_stockcountingmaster';
                    $mas_pk = $fk = 'stockCountingAutoID';
                break;
                
                case 'srp_erp_stocktransferdetails':
                    $mas_tbl = 'srp_erp_stocktransfermaster';
                    $mas_pk = $fk = 'stockTransferAutoID';
                break;
                
                case 'srp_erp_stocktransferdetails_bulk':
                    $mas_tbl = 'srp_erp_stocktransfermaster_bulk';
                    $mas_pk = $fk = 'stockTransferAutoID';
                break;
                
                case 'srp_erp_budgettransferdetail':
                    $mas_tbl = 'srp_erp_budgettransfer';
                    $mas_pk = $fk = 'budgetTransferAutoID';
                break;
            }

            $qry_status = null;
            
            if($moduleID == -5){ // WAC and stock update
                $qry_status = $this->db->where('companyID', $companyID)->update('srp_erp_itemmaster', [
                          'currentStock'=> 0, 'companyLocalWacAmount'=> 0, 'companyReportingWacAmount'=> 0,
                        ]);
            }
            else if( in_array($moduleID, [-1, -6]) ){ // Document Approve, Serial No update
                $centralDB->select('r.docCode')->where('d.masterID',$id)->where('r.docCode IS NOT NULL')
                ->join('flush_data_details AS d', 'd.moduleID = r.moduleID')->group_by('r.docCode');
                $sr_codes = $centralDB->get('module_related_tbl r')->result_array();

                if($sr_codes){
                    $sr_code_arr = [];
                    foreach ($sr_codes as $value) {
                        $multiple_codes = explode('/', $value['docCode']);                        
                        if( count($multiple_codes) > 1 ){
                            foreach ($multiple_codes as $row) {
                                $sr_code_arr[] = trim($row);
                            }
                        }
                        else{
                            $sr_code_arr[] = trim($value['docCode']);
                        }                        
                    }
                    

                    if($moduleID == -1){ // Document Approve
                        $this->db->where('companyID', $companyID)->where_in('documentID', $sr_code_arr);
                        $qry_status = $this->db->delete('srp_erp_documentapproved');
                    }
                    if($moduleID == -6){ //Serial No update
                        $this->db->where('companyID', $companyID)->where_in('documentID', $sr_code_arr);
                        $qry_status = $this->db->update('srp_erp_documentcodemaster', ['serialNo'=> 0]);
                        if($qry_status == false){
                            $err = $this->db->error();                
                            $err_log[] = [
                                'flush_master_id'=> $id, 'module_id'=> $moduleID,
                                'error_code'=> $err['code'], 'error_msg'=> $err['message'], 
                                'processed_qry'=> $this->db->last_query(), 'created_at'=> $this->date_time
                            ];
                            $qry_status = null;
                        }

                        $this->db->where('companyID', $companyID)->where_in('documentID', $sr_code_arr);
                        $qry_status = $this->db->update('srp_erp_financeyeardocumentcodemaster', ['serialNo'=> 0]);
                    }                    
                }                
            }
            elseif($mas_tbl){
                $qry_status = $this->db->query("DELETE {$tbl}
                                    FROM {$tbl}
                                    JOIN {$mas_tbl} ON {$mas_tbl}.{$mas_pk}={$tbl}.{$fk}
                                    WHERE {$mas_tbl}.companyID = {$companyID}");
            }
            else{
                $qry_status = $this->db->delete($tbl, ['companyID'=> $companyID]);
            }

            if($qry_status == false){
                $err = $this->db->error();                
                $err_log[] = [
                    'flush_master_id'=> $id, 'module_id'=> $moduleID,
                    'error_code'=> $err['code'], 'error_msg'=> $err['message'], 
                    'processed_qry'=> $this->db->last_query(), 'created_at'=> $this->date_time
                ];
            } 
        }

        $status = 1;
        if(!empty($err_log)){
            $status = 2;
            $centralDB->insert_batch('flush_data_log', $err_log);
        }
        
        $centralDB->where(['masterID'=> $id, 'moduleID'=> $moduleID])
        ->update('flush_data_details', [ 'flushStatus'=> $status ]);
 
        $this->finalize_flush($id, $moduleID);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            echo json_encode(['s', 'success']);
        }
        else {            
            echo json_encode(['e', 'Failed', 'err_log'=> $err_log]);
        }
    }

    function finalize_flush($master, $module){
        $centralDB = $this->main;
 
        $nxt_module = $centralDB->query("SELECT id, sortOrder FROM flush_data_details
                        WHERE flushStatus <> 1 AND sortOrder > (
                            SELECT sortOrder FROM flush_data_details 
                            WHERE masterID = {$master} AND moduleID = {$module}
                        )")->row('id');

        if($nxt_module){
            return true;
        }

        $centralDB->select('COUNT(id) AS failCount')->where('flushStatus <> 1')->where('masterID', $master);
        $failCount = $centralDB->get('flush_data_details')->row('failCount');

        $status = ($failCount > 0)? 2: 1;
        $centralDB->where('id', $master)->update('flush_data_master', ['flushStatus'=> $status]);
    }

    function fetch_error_log(){
        $masterID  = $this->input->post('masterID');
        $moduleID  = $this->input->post('moduleID');
        $str = '<span class="pull-right">';
        $str .= '<span class="label label-primary dataTableBtn" onclick="view_error_log(this)">';
        $str .= '<i class="fa fa-eye"></i></span> &nbsp; '; 
        $str .= '</span>';
        
        $this->datatables->select("id,created_at,error_msg,processed_qry")
            ->from('flush_data_log')
            ->where(['flush_master_id'=> $masterID, 'module_id'=> $moduleID ]);
        $this->datatables->add_column('action', $str, 'id')
            ->add_column('id_str', '$1', 'date_to_id_conv(created_at)');
        echo $this->datatables->generate();
    }

    function delete_flush_header(){
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $id = $this->input->post('id');

        $this->db->select('id')->where('masterID', $id)->where('flushStatus <> 0');
        $flushStatus = $this->db->get('flush_data_details')->row('id');

        if($flushStatus){
            die( json_encode(['e', 'This header is partially processed.<br/>You can not delete this header.']) );
        }
        
        $this->db->trans_start();
        
        $this->db->delete('flush_data_details', ['masterID'=> $id]);
        $this->db->delete('flush_data_master', ['id'=> $id]);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            echo json_encode(['s', 'Successfully deleted']);
        }
        else {
            echo json_encode(['e', 'Failed to delete the flush details']);
        }            
    }    
}