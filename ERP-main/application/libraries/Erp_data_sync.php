<?php

class Erp_data_sync
{
    protected $ci;
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->ci->load->database();
        $this->ci->load->model('Erp_data_sync_model');
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

    function send_data_to_ecommerce($item_list){

        if($item_list){
            $company_id = null;
            foreach($item_list as $item){
                $company_id = $item['companyId'];
                $itemAutoId =  $item['itemAutoId'];

                $compnay_details = $this->ci->Erp_data_sync_model->getMainCompanyDetails($company_id);
                $getDb = $this->setDb($compnay_details['host'], $compnay_details['db_username'], $compnay_details['db_password'], $compnay_details['db_name']);
                
                $item_details = $this->ci->Erp_data_sync_model->getItemDetails($itemAutoId);
                
                print_r(json_encode($item_details)); exit;
            }

       }else{
            return false;
       }

    }

    protected function setDb($db_host,$db_username,$db_password,$db_name)
    {
        $this->ci->encryption->initialize(array('driver' => 'mcrypt'));

        if (!empty($db_host)) {
            $config['hostname'] = trim($this->ci->encryption->decrypt($db_host));
            $config['username'] = trim($this->ci->encryption->decrypt($db_username));
            $config['password'] = trim($this->ci->encryption->decrypt($db_password));
            $config['database'] = trim($this->ci->encryption->decrypt($db_name));
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
            return $this->ci->load->database($config, FALSE, TRUE);
        }
      
    }

}