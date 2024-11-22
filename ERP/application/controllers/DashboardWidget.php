<?php
/*
-- =============================================
-- File Name : DashboardWidget.php
-- Project Name : SME ERP
-- Module Name : Dashboard
-- Create date : 26 - December 2016
-- Description : This file contains all the generation of finance dashboard.

-- REVISION HISTORY
-- =============================================*/
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardWidget extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("DashboardWidget_model");
    }

    function fetch_userdashboardmaster()
    {
        $this->datatables->select('dashboardDescription,employeeID,srp_erp_userdashboardmaster.templateID,templateDescription', false)
            ->from('srp_erp_userdashboardmaster')
            ->join('srp_erp_dashboardtemplate', 'srp_erp_userdashboardmaster.templateID = srp_erp_dashboardtemplate.templateID', 'LEFT')
            ->where('employeeID', $this->session->userdata("empID"))
            ->edit_column('userDashboardID', '<div class="text-right"> $1 </div>', 'dashboard_format_number(userDashboardID)');
        echo $this->datatables->generate();
    }

    function loadTemplate()
    {
        $data = array();
        $data["templateID"] = $this->input->post('templateID');
        $path = 'system/widget/erp_dashboard_widget_template_'.$this->input->post('templateID');
        $this->load->view($path, $data);
    }

    function loadWidget()
    {
        $position = $this->input->post('position');
        echo json_encode($this->DashboardWidget_model->getWidget($position));
    }

    function loadTemplateWidget()
    {
        $userDashboardID = $this->input->post('userDashboardID');
        $data = array();
        $data["userDashboardID"] = $this->DashboardWidget_model->getWidgetEdit($userDashboardID);
        $data["userDashboard"] = $this->input->post('userDashboardID');
        //$data["userWidgetPositionEdit"] = $this->DashboardWidget_model->getWidgetPositionEdit($userDashboardID);
        $path = 'system/widget/erp_dashboard_widget_template_edit';
        $this->load->view($path, $data);
    }

    function loadWidgetEdit()
    {
        $userDashboardID = $this->input->post('userDashboardID');
        echo json_encode($this->DashboardWidget_model->getWidgetPositionEdit($userDashboardID));
    }

    function save_template_setup()
    {
        echo json_encode($this->DashboardWidget_model->save_template_setup());
    }

    function save_template_setup_add(){

          $this->form_validation->set_rules('templateIDAdd', 'Template', 'trim|required');
          $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->DashboardWidget_model->save_template_setup_add());
        }

    }

    function delete_dashboard(){
        echo json_encode($this->DashboardWidget_model->delete_dashboard());
    }

}
