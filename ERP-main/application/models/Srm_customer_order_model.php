<?php

class Srm_customer_order_model extends ERP_Model
{


    function get_customer_details(){
        $companyID= current_companyID();
        $customerID = $this->input->post('customerID');
        $this->db->SELECT('CustomerAddress1,customerTelephone');
        $this->db->FROM('srp_erp_srm_customermaster');
        $this->db->where('CustomerAutoID',$customerID);
        $this->db->where('companyID',$companyID);
        return $this->db->get()->result_array();
    }

    function save_cusOrder_master()
    {
        $this->load->library('sequence');

        $cus_order_code = $this->sequence->sequence_generator('SRM-ORD');
        $data["customerOrderCode"] =$cus_order_code;
        $data["documentID"] ='SRM-ORD';
        $data["contactPersonName"] =$this->input->post('con_name');
        $data["contactPersonNumber"] =$this->input->post('con_number');
        $data["customerID"] =$this->input->post('typeID');
        $data["CustomerAddress"] =$this->input->post('cus_address');
        $data["documentDate"] =$this->input->post('doc_date');
        $data["narration"] =$this->input->post('narration');
        $data["referenceNumber"] =$this->input->post('ref_number');
        $data["paymentTerms"] =$this->input->post('payment_term');


        $this->db->insert('srp_erp_srm_customerordermaster', $data);

        if($this->db->affected_rows()> 0 ){
            return ['s', 'Saved Successfully'];
        }
        else{
            return ['e', 'An Error has occured'];
        }
    }


}