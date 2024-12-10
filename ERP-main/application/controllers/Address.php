<?php
class Address extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Address_model');
    }

    function load_address(){
        $this->datatables->select('addressID,addressType,addressDescription,contactPerson,contactPersonTelephone,contactPersonFaxNo,contactPersonEmail')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->from('srp_erp_address')
        ->edit_column('action', '<span class="pull-right" ><a onclick="openaddressmodel($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="deleteaddress($1)"><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"  rel="tooltip"></span></a></span>', 'addressID');
        echo $this->datatables->generate();
    }

    function save_address()
    {
        $this->form_validation->set_rules('addresstypeid', 'Address Type ID', 'trim|required');
        $this->form_validation->set_rules('addressdescription', 'Address Description', 'trim|required');
        $this->form_validation->set_rules('contactpersonid', 'Contact Person ID', 'trim|required');
        //$this->form_validation->set_rules('contactpersontelephone', 'Contact Person Telephone', 'trim|required');
        //$this->form_validation->set_rules('contactpersonfaxno', 'contact person Fax No', 'trim|required');
        //$this->form_validation->set_rules('contactpersonemail', 'Contact Person Email', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Address_model->save_address());
        }
    }

    function edit_address()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Address_model->edit_address());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function delete_address()
    {
        echo json_encode($this->Address_model->delete_address());
    }
}
