<?php

class Api_job_portal_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_country_data(){

        $this->db->select('*');
        $country_list = $this->db->get('srp_erp_countrymaster')->result_array();

        if($country_list){
            return array("status"=>true,"results"=>$country_list);
        }else{
            return array("status"=>false,"results"=>'');
        }
    }

    function get_designation_category(){

        $this->db->select('*');
        $designation_category_list = $this->db->get('srp_erp_designation_category')->result_array();

        if($designation_category_list){
            return array("status"=>true,"results"=>$designation_category_list);
        }else{
            return array("status"=>false,"results"=>'');
        }
    }

    function get_designation($categoryID, $companyID){

        if($categoryID != '' && $companyID != '' ){

            $this->db->select('*');
            $this->db->where('Erp_companyID', $companyID);
            $this->db->where('categoryID', $categoryID);
            $srp_designation = $this->db->get('srp_designation')->result_array();

            if($srp_designation){
                return array("status"=>true,"results"=>$srp_designation);
            }else{
                return array("status"=>false,"results"=>'');
            }
            
        }else{
            return array("status"=>false,"results"=>"");
        }

    }

}