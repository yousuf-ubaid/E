<?php

class Group_financial_year extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_financial_year_model');
    }

    function load_Financial_year()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select("groupFinanceYearID,beginingDate,endingDate,comments,isActive,isCurrent,isClosed");
        $this->datatables->where('groupID', $companyID);
        $this->datatables->from('srp_erp_groupfinanceyear');
        $this->datatables->add_column('financial_year', '<center> $1- $2 </center>', 'beginingDate,endingDate');
        $this->datatables->add_column('status', '$1', 'load_Financial_year_status(groupFinanceYearID,isActive)');
        $this->datatables->add_column('current', '$1', 'load_Financial_year_current(groupFinanceYearID,isCurrent)');
        $this->datatables->add_column('close', '$1', 'load_Financial_year_close(groupFinanceYearID,isClosed)');
        //$this->datatables->add_column('action', '<span class="pull-right"><a onclick="openisactiveeditmodel($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>', 'companyFinanceYearID');
        echo $this->datatables->generate();
    }

    function save_financial_year()
    {
        $this->form_validation->set_rules('beginningdate', 'Beginning Date', 'trim|required');
        $this->form_validation->set_rules('endingdate', 'Ending Date', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $companyID=$this->common_data['company_data']['company_id'];
            //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

            $chkFinanceYear = $this->db->query("SELECT groupFinanceYearID,beginingDate,endingDate,groupID FROM srp_erp_groupfinanceyear where groupID = {$companyID} AND ('" . $this->input->post('beginningdate') . "' BETWEEN beginingDate AND endingDate OR '" . $this->input->post('endingdate') . "' BETWEEN beginingDate AND endingDate)")->row_array();

            if ($chkFinanceYear) {
                $this->session->set_flashdata('e', 'Financial Year already created !');
                echo json_encode(FALSE);
            } else {
                echo json_encode($this->Group_financial_year_model->save_financial_year());
            }
        }
    }

    function update_year_status()
    {
        $chkFinanceYearCurrent = $this->db->query("SELECT groupFinanceYearID,isActive,isCurrent,isClosed FROM srp_erp_groupfinanceyear where groupFinanceYearID = " . $this->input->post('groupFinanceYearID'))->row_array();

        if ($chkFinanceYearCurrent['isClosed'] == 1) {
            $this->session->set_flashdata('e', 'A closed financial year cannot be set as current year');
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Group_financial_year_model->update_year_status());
        }

    }

    function update_year_close()
    {
        echo json_encode($this->Group_financial_year_model->update_year_close());
    }

    function update_year_current()
    {
        $chkFinanceYearCurrent = $this->db->query("SELECT groupFinanceYearID,isActive,isCurrent,isClosed FROM srp_erp_groupfinanceyear where groupFinanceYearID = " . $this->input->post('groupFinanceYearID'))->row_array();

        if ($chkFinanceYearCurrent['isClosed'] == 1) {
            $this->session->set_flashdata('e', 'A closed financial year cannot be set as current year');
            echo json_encode(FALSE);
        } else {
            if ($chkFinanceYearCurrent['isActive'] == 0) {
                $this->session->set_flashdata('e', 'This Financial Year is not activated !');
                echo json_encode(FALSE);
            } else {
                echo json_encode($this->Group_financial_year_model->update_year_current());
            }
        }
    }


}
