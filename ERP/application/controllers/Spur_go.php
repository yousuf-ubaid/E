<?php

class Spur_go extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Spur_go_model');

    }
    public function index()
    {
        $data['title'] = 'Sign Up Form Spur GO';
        $data['currency_arr'] = $this->Spur_go_model->fetch_currency_arr();
        $data['countrys'] = $this->Spur_go_model->load_country_drop();
         //$this->load->view('spur_go_registration',$data);
         $this->load->view('spur_go_registration',$data);
    }
    function Savespurgo_Details()
    {
        $Date = new DateTime('now');
        $Date_end = new DateTime('now');
        //$start_date = $Date->modify('-5 year')->format('Y-01-01');
        //$end_date = $Date_end->modify('10 year')->format('Y-01-01');
        $namesec = $this->input->post('namesec');
        $usernamesec = $this->input->post('usernamesec');
        $passwordsec = $this->input->post('passwordsec');
        $istermsYN = $this->input->post('istermsYN');

        $this->form_validation->set_rules('companycode', 'Comapny Code', 'trim|required');
        $this->form_validation->set_rules('companyname', 'Comapny Name', 'trim|required');
        $this->form_validation->set_rules('companyaddress1', 'Comapny Address 1', 'trim|required');
        $this->form_validation->set_rules('financeyearmonth', 'Finance year begining month ', 'trim|required');
       // $this->form_validation->set_rules('companyaddress2', 'Comapny Address 2', 'trim|required');
        $this->form_validation->set_rules('company_default_currencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('companycity', 'Comapny City', 'trim|required');
        $this->form_validation->set_rules('companypostalcode', 'Comapny Postal Code', 'trim|required');
        $this->form_validation->set_rules('companycountry', 'Comapny Country', 'trim|required');
        $this->form_validation->set_rules('companyemail', 'Comapny Email', 'valid_email');
        $this->form_validation->set_rules('timezone', 'Time Zone', 'trim|required');
        $this->form_validation->set_rules('nameprimary', 'Line 1 Name', 'trim|required');
        $this->form_validation->set_rules('usernameprimary', 'Line 1 User Name', 'trim|required|valid_email');
        $this->form_validation->set_rules('passwordprimary', 'Line 1 Password', 'trim|required');
        $this->form_validation->set_rules('istermsYN', 'Terms of Service', 'trim|required');

        if(($namesec)||($usernamesec)||($passwordsec))
        {
            $this->form_validation->set_rules('namesec', ' Line 2 Name', 'trim|required');
            $this->form_validation->set_rules('usernamesec', 'Line 2 User Name', 'trim|required|valid_email');
            $this->form_validation->set_rules('passwordsec', 'Line 2 Password', 'trim|required');
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Spur_go_model->save_spurgo_companydetails());
        }

    }


}
