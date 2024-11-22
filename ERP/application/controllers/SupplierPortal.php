<?php

class SupplierPortal extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Session_model'); 
    }

    function index(){

        $data = array();

        if(!isset($_GET['link'])){
           return $this->load->view('error_500',$data); //_style2
        }

        $link = $_GET['link'];

        $link_arr = explode('_',$link);
        
        if(!isset($link_arr[2])){
            return $this->load->view('error_500',$data); //_style2
        }

        if($link_arr){

            $data['compID'] = $company_id = $link_arr[2];
            $data['supID'] = $supplier_id = $link_arr[1];

            $tmpCompanyInfo = $this->db->select('*')
                ->from('srp_erp_company')
                ->where('company_id', $company_id)
                ->get()->row_array();

            $this->company_info = $tmpCompanyInfo;

            if (!empty($this->company_info)) {
                $this->company_id = $this->company_info['company_id'];
                $this->setDb();
            }
            
        }

        $this->load->view('system/srm/supplier_inquery/inquery', $data); //_style2

    }

    protected function setDb()
    {
        if (!empty($this->company_info)) {
            $config['hostname'] = trim($this->encryption->decrypt($this->company_info["host"]));
            $config['username'] = trim($this->encryption->decrypt($this->company_info["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($this->company_info["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($this->company_info["db_name"]));
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
            $this->load->database($config, FALSE, TRUE);
        }
    }


    function retrive_order_inquery(){

        $companyid = 505; //trim($this->input->post('companyID') ?? '');
        $text = trim($this->input->post('searchInquiry') ?? '');
        $confirmedYN = trim($this->input->post('confirmedYN') ?? '');

        // $filterconfirmedYN = '';
        // if ($confirmedYN == 0) {
        //     $filterconfirmedYN = " AND srp_erp_srm_orderinquirymaster.confirmedYN = 0";
        // } else if ($confirmedYN == 1) {
        //     $filterconfirmedYN = " AND srp_erp_srm_orderinquirymaster.confirmedYN = 1";
        // }

        // $search_string = '';
        // if (isset($text) && !empty($text)) {
        //     $search_string = " AND documentCode Like '%" . $text . "%'";
        // }

        $where = "srp_erp_srm_orderinquirymaster.companyID = " . $companyid;
            $this->db->select('srp_erp_srm_orderinquirymaster.confirmedYN,srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.inquiryType,srp_erp_srm_orderinquirymaster.documentCode as orderCode, customerName,customerOrderCode,srp_erp_srm_orderinquirymaster.confirmedYN as inquiryConfirm,CurrencyCode');
            $this->db->from('srp_erp_srm_orderinquirymaster');
            $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_orderinquirymaster.customerID', 'LEFT');
            $this->db->join('srp_erp_srm_customerordermaster', 'srp_erp_srm_customerordermaster.customerOrderID = srp_erp_srm_orderinquirymaster.customerOrderID', 'LEFT');
            $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
           // $this->db->where($where);
            $this->db->order_by('inquiryID', 'DESC');

        $data['output'] = $this->db->get()->result_array();

        print_r($data); exit;


    }
   


}