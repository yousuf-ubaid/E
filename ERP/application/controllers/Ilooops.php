<?php

class Ilooops extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function visitorTicket(){
        
        $comp = trim($_GET['setting'] ?? '');
        $reference = trim($_GET['id'] ?? '');

        $company_id = $this->encryption->decrypt($comp);

        //get db
       
        $data = array();
        $request_details = null;

        if($company_id){

            $this->db = $this->setDb($company_id);

            $this->db->select('*');
            $this->db->where("companyID",$company_id);
            $this->db->where("validReference",$reference);
            $request_details = $this->db->get("srp_erp_op_visitor_log_link")->row_array();
        }
       

        if(empty($request_details) || empty($company_id)){
            $this->load->view('system/operations/op_visitorlog_error',$data);
         
        }else{
            $data['reference'] = $reference;
            $data['logo'] = mPDFImage;
            $data['comp'] = $comp;
    
            $this->load->view('system/operations/op_visitorlog',$data);
        }

    }

    function onlineCheckList(){

        $comp = trim($_GET['setting'] ?? '');
        $pin = trim($_GET['pin'] ?? '');
        $reference = trim($_GET['id'] ?? '');

      
        $company_id = $this->encryption->decrypt($comp);
        $pin = $this->encryption->decrypt($pin);
        //get db
        
        $data = array();
        $request_details = null;

        if($company_id){

            $this->db = $this->setDb($company_id);

            $checklist_header = $this->db->where('link_reference',$reference)->from('srp_erp_op_checklist_header')->get()->row_array();

            $data['checklist'] = $checklist_header;
            $data['header_id'] = $checklist_header['id'];
            $data['job_id'] = $checklist_header['job_id'];
            $data['companyID'] = $company_id;
        }

        if(empty($checklist_header) || empty($company_id)){
            $this->load->view('system/operations/op_visitorlog_error',$data);
        }else{
            $this->load->view('system/operations/op_checklist',$data);
        }

    }

    function save_visitor_log_online(){

        $setting = $this->input->post('setting');
        $company_id = $this->encryption->decrypt($setting);

        $this->db = $this->setDb($company_id);

        $reference = $this->input->post('reference');
        $date = $this->input->post('date');
        $full_name = $this->input->post('full_name');
        $company = $this->input->post('company');
        $position = $this->input->post('position');
        $p_of_visit = $this->input->post('p_of_visit');
        $mobile_no = $this->input->post('mobile_no');
        $medication = $this->input->post('medication');
        $h2s_validity = $this->input->post('h2s_validity');
        $rig_safety = $this->input->post('rig_safety');
        $propper_ppe = $this->input->post('propper_ppe');
        $time_in = $this->input->post('time_in');
        $timeout = $this->input->post('timeout');

        $this->db->select('*');
        $this->db->where("companyID",$company_id);
        $this->db->where("validReference",$reference);
        $request_details = $this->db->get("srp_erp_op_visitor_log_link")->row_array();

        try {

            if($request_details){

                $data = array();
                $data['job_id'] = $request_details['job_id'];
                $data['full_name'] = $full_name;
                $data['full_company'] = $company;
                $data['position'] = $position;
                $data['purpose_visit'] = $p_of_visit;
                $data['mobile_no'] = $mobile_no;
                $data['medication'] = $medication;
                $data['h2s_validity'] = $h2s_validity;
                $data['safety_briefing'] = $rig_safety;
                $data['proper_ppe'] = $propper_ppe;
                $data['time_in'] = $time_in;
                $data['time_out'] = $timeout;
                $data['date'] = $date;
                $data['companyID'] = $company_id;
                $data['status'] = 1;
                
                // update the status
                $data_req = array();
                $data_req['status'] = 2;
    
                $this->db->update('srp_erp_op_visitor_log_link',$data_req);
    
                // insert the record
                $this->db->insert('srp_erp_op_visitors_log',$data);
    
            }
            
            echo json_encode(array('status'=>'success','message' => 'Data Inserted'));

        } catch (\Throwable $th) {
            echo json_encode(array('status'=>'failed','message' => 'Error'));
        }

    }

    function setDb($company_id){

        $this->db->select('*');
        $this->db->where("company_id",$company_id);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();

        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
        $databaseresult = $this->load->database($config, True);

        return $databaseresult;
    }

    function vendor_rfq_view()
    {
            $companyid = base64_decode($_GET['comp']);
            $inquiryID = base64_decode($_GET['qut']);
            $supplierId = base64_decode($_GET['sup']);

            $this->db->select('*');
            $this->db->where("company_id",$companyid);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
            $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
           $databaseresult = $this->load->database($config, True);

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_srm_vendor_submit_rfq');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('supplierID', $supplierId);
        $databaseresult->where('inquiryMasterID', $inquiryID);
        $databaseresult->where('isRfqSubmitted', 1);

        $is_submit =$databaseresult->get()->row_array();

        if($is_submit){
            $data['is_submit_rfq']=1;
        }else{
            $data['is_submit_rfq']=0;
        }

        $databaseresult->select('srp_erp_srm_orderinquirymaster.inquiryID,confirmedYN,rfqExpDate,confirmedByName,DATEDIFF(rfqExpDate,NOW()) as linkexpiary,DATE_FORMAT(confirmedDate,\'%d-%m-%Y\') as confirmedDate,DATE_FORMAT(createdDateTime,\'%d-%m-%Y\') as createdDateTime,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID');
        $databaseresult->from('srp_erp_srm_orderinquirymaster');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('inquiryID', $inquiryID);

        $data['master']  = $databaseresult->get()->row_array();

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_srm_suppliermaster');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('supplierAutoID', $supplierId);

        $data['supplier']  = $databaseresult->get()->row_array();

        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();


        $data['logo'] = mPDFImage;
        $data['quatationId']=$inquiryID;
        $data['companyID']=$companyid;
        $data['supplierID']=$supplierId;

        $data['type'] = $this->input->post('type');

        if($data['master']['linkexpiary']>=0)
        {
            $this->load->view('system/srm/supplier_portal/srm_rfq_submit_view',$data);

        }else
        {
            $this->load->view('system/srm/supplier_portal/srm_rfq_link_expire');
        }


    }
 



   
}
