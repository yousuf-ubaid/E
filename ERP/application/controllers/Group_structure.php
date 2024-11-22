<?php

class Group_structure extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('Group_structure_model');
        $this->load->helper('employee');

    }


    function submit_groupStructure()
    {
        $this->form_validation->set_rules('percentage', 'Percentage Name', 'trim|required');
        $this->form_validation->set_rules('dateFrom', 'Date From', 'trim|required');

        $this->form_validation->set_rules('companyID', 'companyID', 'trim|required');
        $this->form_validation->set_rules('shareholderName', 'Share Holder Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_structure_model->submit_groupStructure());
        }

    }

    function get_groupStructure(){

        echo $this->Group_structure_model->get_groupStructure();
        exit;

    }

    function deleteshareholding(){
        $this->db->where('groupstructureID', $this->input->post('groupstructureID'));
        $results = $this->db->delete('srp_erp_groupstructure');

        if ($results) {
            echo json_encode(['s', 'Successfully Deleted']);
            exit;
        } else {
            echo json_encode(['e', 'Failed']);
            exit;
        }

    }

    function groupStructure_update_field(){
       $groupstructureID= $this->input->post('groupstructureID');
        $value=$this->input->post('value');
        $this->db->where('groupstructureID', $groupstructureID);
        $this->db->update('srp_erp_groupstructure', array('isActive'=>$value));
        echo json_encode(['s', 'Successfully Deleted']);
        exit;


    }

    function loadCompanyForm(){
        echo $this->Group_structure_model->loadCompanyForm();
        exit;
    }

    function submit_groupStructurepullcompany(){
        $this->form_validation->set_rules('companyGroupID', 'Company Group', 'trim|required');
        $this->form_validation->set_rules('companyID[]', 'Company', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_structure_model->submit_groupStructurepullcompany());
        }
    }

    function loadstructurePage(){
        echo $this->Group_structure_model->loadstructurePage();
        exit;
    }

    function GroupStructurereportingTo(){
        echo $this->Group_structure_model->GroupStructurereportingTo();
        exit;
    }

    function submit_create_group(){
        $this->form_validation->set_rules('groupCode', 'Group Code', 'trim|required');
        $this->form_validation->set_rules('description', 'Group Description', 'trim|required');
        $this->form_validation->set_rules('reportingTo', 'Reporing To', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_structure_model->submit_create_group());
        }
    }

    function groupStructureDetail_update_field(){
        $companyGroupDetailID= $this->input->post('companyGroupDetailID');
        $value=$this->input->post('value');
        $this->db->where('companyGroupDetailID', $companyGroupDetailID);
        $this->db->update('srp_erp_companygroupdetails', array('typeID'=>$value));
        echo json_encode(['s', 'Successfully Deleted']);
        exit;

    }

    function GroupStructuremasterTo(){
        echo $this->Group_structure_model->GroupStructuremasterTo();
        exit;
    }


}