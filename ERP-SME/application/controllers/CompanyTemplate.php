<?php

class CompanyTemplate extends CI_Controller
{

    private $main;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;

    function __construct()
    {
        parent::__construct();
        $this->load->model('CompanyTemplate_model');
        //$this->load->helper('CompanyPolicy_helper');
        $companyID = $this->input->post('companyID');
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
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $config['hostname'] = trim($this->encryption->decrypt($this->db_host));
        $config['username'] = trim($this->encryption->decrypt($this->db_username));
        $config['password'] = trim($this->encryption->decrypt($this->db_password));
        $config['database'] = trim($this->encryption->decrypt($this->db_name));
        $config['dbdriver'] = 'mysqli';
        return $config;
    }

    function fetch_template_configuration()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $companyID= $this->input->post('companyID');
        $data['detail'] = $this->db->query("SELECT
	srp_erp_templatemaster.*, det.TempMasterID AS detailTempMasterID
FROM
	`srp_erp_templatemaster`
INNER JOIN (
	SELECT
		TempMasterID
	FROM
		srp_erp_templates
	WHERE
		companyID = '$companyID'
) det ON srp_erp_templatemaster.TempMasterID = det.TempMasterID
ORDER BY
	srp_erp_templatemaster.FormCatID")->result_array();
        $data['companyID'] = $companyID;
        echo $this->load->view('erp_template_configuration_table', $data, true);

    }

    function saveTemplate(){
        echo json_encode($this->CompanyTemplate_model->saveTemplate());
    }


    function update_business_name(){
        /* 
            This function implemented for Dev purpose only [ SME-2675 ] 
            This function is developed to update the main DB srp_erp_company.company_business_name
            column from their client DB records
         */
        
        $db2 = $this->load->database('db2', TRUE);
        
        $company_data = $db2->select('company_id, host, db_name, db_username, db_password,company_code,company_name') 
                        ->where("company_business_name = '' OR company_business_name IS NULL")
                        ->where('host is NOT NULL', NULL, FALSE)->where('db_username is NOT NULL', NULL, FALSE)
                        ->where('db_password is NOT NULL', NULL, FALSE)->where('db_name is NOT NULL', NULL, FALSE)                        
                        /* ->where_in('company_id', [
                            13, 644, 643 //, -368
                        ]) //-368, -363,  */
                        ->get('srp_erp_company')->result_array();
                        
                        
        if(empty($company_data)){
            die('No records found for proceed.');
        }
        echo '<pre>'; print_r($company_data);exit;

        $update_data = $pro = $unPr = []; 
        foreach($company_data as $val){
            
            $config['hostname'] = trim($this->encryption->decrypt($val["host"]));
            $config['username'] = trim($this->encryption->decrypt($val["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($val["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($val["db_name"]));             
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = FALSE;
            $config['pconnect'] = FALSE;
            
            $db_obj = $this->load->database($config, TRUE);  
                                
            $company_id = $val['company_id'];

            if ($db_obj->conn_id) {                
                $pro[] = $val['company_name'].' [ '.$val['company_code'].' ]';                

                $update_data[] = $db_obj->select('company_id, company_name AS company_business_name')
                    ->where(['company_id'=> $company_id])
                    ->get('srp_erp_company')->row_array();
                                        
            }
            else{                
                $unPr[] = $val;
            }
        }

        echo 'Total records : '.count($company_data);

        if(!empty($update_data)){
            $db2->update_batch('srp_erp_company', $update_data, 'company_id');
            echo '<br/><br/>No of updates : '.count($update_data);
        }

        if($unPr){
            echo '<style>
                    #table{
                        margin-top : 20px;
                        border-collapse: separate !important;
                        clear: both;
                        margin-top: 6px !important;
                        margin-bottom: 6px !important;
                        max-width: none !important;
                        border: 1px solid #f4f4f4;
                    }

                    #table tbody td {
                        border-left-width: 0;
                        border-bottom-width: 0;                        
                    }

                    #table >tbody>tr>td{
                        border: 1px solid #f4f4f4;
                    }

                    #table > tbody>tr:nth-of-type(odd) {
                        background-color: #f9f9f9;
                    }
                </style>';
            echo '<br/><br/><br/><br/>Failed DB connection`s Company list';
            echo '<table id="table">
                    <thead><tr><th>#</th><th>Company ID</th>
                <th>Name</th><tr/></thead><tbody>';

            foreach ($unPr as $key => $val) {
                echo '<tr>
                    <td width="25px">'.($key+1).'</td>
                    <td>'.$val['company_id'].'</td>
                    <td>'.$val['company_name'].' [ '.$val['company_code'].' ]</td>
                </tr>';                
            }
            echo '</tbody> </table>';
        }

        echo '<pre>'; print_r($update_data);exit;
    }
}