<?php

class Access_menu extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Access_menu_model');
    }

    /*load navigation usergroup setup */



    function load_master(){
        echo json_encode($this->Access_menu_model->load_master());
    }

   /* function save_navigation(){
        $type=$this->input->post('type');
        $level=$this->input->post('level');
        $subexist=$this->input->post('subexist');
        $this->form_validation->set_rules('icon', 'Icon', 'trim|required');
        $this->form_validation->set_rules('pagetitle', 'Page Title', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');

        if($type==2 && $level==1 || $level==2){
            $this->form_validation->set_rules('modules', 'Modules', 'trim|required');
        }
        if($subexist==0){
            $this->form_validation->set_rules('url', 'URL', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Access_menu_model->save_navigation());
        }
    }*/



}
