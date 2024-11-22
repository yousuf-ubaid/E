<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SM_School extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('SM_School_model');
        $this->load->helper('sm_school');
        $this->load->library('s3');
    }

    function fetch_student()
    {
        
        $this->datatables->select('stuID, student_code, name, dob, present_class,g.grade as class,parent.first_name')
            ->from('srp_erp_sm_studentmaster as stu')
            ->join('srp_erp_sm_grade as g','stu.gradeId = g.gradeId','left')
            ->join('srp_erp_sm_parentmaster as parent','stu.contact_person = parent.parentID','left')
            ->where('stu.companyID', $this->common_data['company_data']['company_id'])
            ->add_column('action','$1','loadstudentAction(stuID)');
        echo $this->datatables->generate();
    }
    function delete_student()
    {
        echo $this->School_model->delete_student();
    }
    function edit_student()
    {
        echo $this->School_model->edit_student();
    }
    function save_student()
    {
        if ($this->input->post('stuID') == NULL) {
            $this->form_validation->set_rules('Stu_Name', 'StuDob', 'age', 'trim|required');
            $this->form_validation->set_rules('Stu_gender', 'admissionDate', 'admitted_year', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {

            echo json_encode($msgtype = 'e', validation_errors());
        } else {
            echo $this->School_model->save_student();
        }

    }

    function fetch_parent()
    {
        $this->datatables->select('parentID, parent_code, contact_person, resident_labour_nic_civil	, email, telephone_mobile, area')
            ->from('srp_erp_sm_parentmaster')
            ->where('srp_erp_sm_parentmaster.companyID', $this->common_data['company_data']['company_id'])
            ->edit_column('action', '$1', 'loadparentAction(stuID)');
        echo $this->datatables->generate();
    }
    function delete_parent()
    {
        echo $this->School_model->delete_parent();
    }
    function edit_parent()
    {
        echo $this->School_model->edit_parent();
    }
    function save_parent()
    {
        if ($this->input->post('parentID') == NULL) {
            $this->form_validation->set_rules('Contact_Name', 'NIC', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {

            echo json_encode($msgtype = 'e', validation_errors());
        } else {
            echo $this->School_model->save_parent();
        }

    }
}