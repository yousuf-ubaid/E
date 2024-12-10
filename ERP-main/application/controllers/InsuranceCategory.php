<?php defined('BASEPATH') OR exit('No direct script access allowed');

class InsuranceCategory extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Insurance_category_modal');
    }

    function fetch_insurance_category()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyID = " . $companyid . "";
        $this->datatables->select("insurancecategoryID,description");
        $this->datatables->from('srp_erp_family_insurancecategory');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '<a onclick="edit_insurance_category($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>', 'insurancecategoryID');
        echo $this->datatables->generate();
    }

    function save_insurance_category()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Insurance_category_modal->save_insurance_category());
        }
    }

    function edit_insurance_category(){
        echo json_encode($this->Insurance_category_modal->edit_insurance_category());
    }

}