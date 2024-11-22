<?php

class Srm_customer_order extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('srm');
        $this->load->model('Srm_customer_order_model');

    }

    function index()
    {

//       $data["masterdetails"] = $this->Srm_customer_order_model->get_customer_orderMaster();
//       $this->load->view('system/srm/srm_customer_order', $data, true);

    }


    function get_customers_dropdown(){
        $data["customers"] = $this->Srm_customer_order_model->get_customer_details();
        $this->load->view('system/srm/customer-order/create_new_customer_order',$data);
    }

    function save_cusOrder_master(){
      echo json_encode($this->Srm_customer_order_model->save_cusOrder_master());
    }


}