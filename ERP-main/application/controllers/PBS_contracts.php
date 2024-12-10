<?php
/** ====================================================================================================================
 * -- File Name : PBS contracts.php
 * -- Project Name : GS_SME 
 * -- Create date : 19 - Aug 2020
 * -- Description : This controller used to get PBS contracts
 */

class PBS_contracts extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index() {
        Header('Access-Control-Allow-Origin: true');
        Header('Access-Control-Allow-Methods: GET');

        $company_id = PBS_COMPANY_ID;
        $company_det = $this->db->get_where('srp_erp_company', ['company_id'=> $company_id])->row_array();
        setup_clientDB($company_det);


        $this->db->select('contractAutoID,contractCode,customerName,contractDate')
            ->where(['companyID'=> $company_id, 'contractType'=> 'Contract', 'approvedYN'=> 1])->order_by('contractAutoID');
        $contracts = $this->db->get('srp_erp_contractmaster')->result_array();

        $contracts_arr = [];
        foreach ($contracts as $row){
            $contracts_arr[ $row['contractAutoID'] ] = $row['contractCode'].' | '.$row['customerName'].' | '.$row['contractDate'];
        }

        echo json_encode([
            'success' => true,
            'message' => "Contracts Retrieved Successfully!",
            'data' => $contracts_arr
        ]);
    }
}
