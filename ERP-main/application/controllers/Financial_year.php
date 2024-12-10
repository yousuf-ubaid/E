<?php

class Financial_year extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Financial_year_model');
    }

    function load_Financial_year()
    {
        $this->datatables->select("companyFinanceYearID,beginingDate,endingDate,comments,isActive,isCurrent,isClosed");
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->from('srp_erp_companyfinanceyear');
        $this->datatables->add_column('financial_year', '<center> $1- $2 </center>', 'beginingDate,endingDate');
        /*        $this->datatables->add_column('current_status', '$1', 'confirm(isCurrent)');
        $this->datatables->add_column('closed_status', '$1', 'confirm(isClosed)');*/
        $this->datatables->add_column('comments', '$1', 'load_Financial_year_comments(comments,companyFinanceYearID)');

        $this->datatables->add_column('status', '$1', 'load_Financial_year_status(companyFinanceYearID,isActive)');
        $this->datatables->add_column('current', '$1', 'load_Financial_year_current(companyFinanceYearID,isCurrent)');
        $this->datatables->add_column('close', '$1', 'load_Financial_year_close(companyFinanceYearID,isClosed)');
        $this->datatables->add_column('action', '$1', 'load_fy_action(isClosed,companyFinanceYearID)');
        echo $this->datatables->generate();
    }
    public function update_comments()
    {
        $pk = explode('|', trim($this->input->post('pk') ?? ''));  // Primary key from the editable field
        $financialYearID = $pk[0];
        $comments = $this->input->post('value');      // Updated value (comments)

        $this->form_validation->set_rules("value", "Comments", 'trim|required');

        // Validate the input
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(['e', validation_errors(), $pk[1], $pk[0]]);
        }
        else
        {
            echo json_encode($this->Financial_year_model->update_comments());
        }
    }


    function save_financial_year()
    {
        $this->form_validation->set_rules('beginningdate', 'Beginning Date', 'trim|required');
        $this->form_validation->set_rules('endingdate', 'Ending Date', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            $companyID = current_companyID();
            $chkFinanceYear = $this->db->query("SELECT companyFinanceYearID,beginingDate,endingDate,companyID FROM srp_erp_companyfinanceyear where companyID = {$companyID} AND ('" . $this->input->post('beginningdate') . "' BETWEEN beginingDate AND endingDate OR '" . $this->input->post('endingdate') . "' BETWEEN beginingDate AND endingDate)")->row_array();

            if ($chkFinanceYear)
            {
                $this->session->set_flashdata('e', 'Financial Year already created !');
                echo json_encode(FALSE);
            }
            else
            {
                echo json_encode($this->Financial_year_model->save_financial_year());
            }
        }
    }

    function update_year_status()
    {
        $chkFinanceYearCurrent = $this->db->query("SELECT companyFinanceYearID,isActive,isCurrent,isClosed FROM srp_erp_companyfinanceyear where companyFinanceYearID = " . $this->input->post('companyFinanceYearID') . "")->row_array();

        if ($chkFinanceYearCurrent['isClosed'] == 1)
        {
            $this->session->set_flashdata('e', 'A closed financial year cannot be set as current year');
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Financial_year_model->update_year_status());
        }
    }

    function update_year_close()
    {
        echo json_encode($this->Financial_year_model->update_year_close());
    }

    function update_year_current()
    {
        $chkFinanceYearCurrent = $this->db->query("SELECT companyFinanceYearID,isActive,isCurrent,isClosed FROM srp_erp_companyfinanceyear where companyFinanceYearID = " . $this->input->post('companyFinanceYearID') . "")->row_array();

        if ($chkFinanceYearCurrent['isClosed'] == 1)
        {
            $this->session->set_flashdata('e', 'A closed financial year cannot be set as current year');
            echo json_encode(FALSE);
        }
        else
        {
            if ($chkFinanceYearCurrent['isActive'] == 0)
            {
                $this->session->set_flashdata('e', 'This Financial Year is not activated !');
                echo json_encode(FALSE);
            }
            else
            {
                echo json_encode($this->Financial_year_model->update_year_current());
            }
        }
    }

    function load_isactiveeditdetails()
    {
        $this->datatables->select("companyFinancePeriodID,companyFinanceYearID,dateFrom,dateTo,isActive,isCurrent,isClosed");
        $this->datatables->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $this->datatables->from('srp_erp_companyfinanceperiod', 'srp_employeesdetails');
        $this->datatables->add_column('status', '$1', 'load_Financial_year_isactive_status(companyFinancePeriodID,isActive)');
        $this->datatables->add_column('current', '$1', 'load_Financial_year_isactive_current(companyFinancePeriodID,isCurrent,companyFinanceYearID)');
        $this->datatables->add_column('closed', '$1', 'load_financialperiod_isclosed_closed(companyFinancePeriodID,isClosed)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_is_closed_is_current_class(isClosed,isCurrent)');
        $this->datatables->add_column('reopen', '$1', 'load_fp_action(isClosed,companyFinancePeriodID)');

        echo $this->datatables->generate();
    }

    function update_financial_year_isactive_status()
    {
        echo json_encode($this->Financial_year_model->update_financial_year_isactive_status());
    }

    function change_financial_period_current()
    {
        echo json_encode($this->Financial_year_model->change_financial_period_current());
    }

    function update_financialperiodclose()
    {
        echo json_encode($this->Financial_year_model->update_financialperiodclose());
    }

    function check_financial_period_iscurrent_activated()
    {
        echo json_encode($this->Financial_year_model->check_financial_period_iscurrent_activated());
    }

    function check_financial_year_iscurrent_activated()
    {
        echo json_encode($this->Financial_year_model->check_financial_year_iscurrent_activated());
    }
    function delete_financial_year()
    {
        $financeYearID = trim($this->input->post('financeYearID') ?? '');
        $recordExist = check_finance_year_id_exists($financeYearID);

        if ($recordExist == 1)
        {

            echo json_encode(array('e', 'Financial year has been used already. please close this and create a new one.'));
            exit();
        }
        else
        {

            $financeYearMasterTBL = $this->db->query("DELETE FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID = $financeYearID");
            $financePeriodTBL = $this->db->query("DELETE FROM srp_erp_companyfinanceperiod WHERE companyFinanceYearID = $financeYearID");
            echo json_encode(array('s', 'Finance Year deleted successfully'));
        }
    }

    function reopen_financial_year()
    {
        $financeYearID = trim($this->input->post('financeYearID') ?? '');
        $financePeriodTBL = $this->db->query("UPDATE srp_erp_companyfinanceyear SET isClosed = 0 WHERE companyFinanceYearID = $financeYearID");
        echo json_encode(array('s', 'Finance Year reopened successfully'));
    }

    function reopen_financial_period()
    {
        $financePeriodID = trim($this->input->post('financePeriodID') ?? '');
        $financePeriodTBL = $this->db->query("UPDATE srp_erp_companyfinanceperiod SET isClosed = 0 WHERE companyFinancePeriodID = $financePeriodID");
        echo json_encode(array('s', 'Finance Period reopened successfully'));
    }


    function create_13th_month_Financial_Period_toThisYear()
    {
        echo json_encode($this->Financial_year_model->create_13th_month_Financial_Period_toThisYear());
    }


    function save_department_financial_periods()
    {
        echo json_encode($this->Financial_year_model->save_department_financial_periods());
    }

    function load_department_isactiveeditdetails()
    {
        $fyDepartmentID = $this->input->post('fyDepartmentID');
        $finYearID = $this->input->post('finYearID');

        if (!empty($fyDepartmentID))
        {
            $this->datatables->select("fyDepartmentID,id as departmentFinancePeriodID,companyFinancePeriodID,companyFinanceYearID,dateFrom,dateTo,isActive,isCurrent,isClosed");
            $this->datatables->where('fyDepartmentID', $this->input->post('fyDepartmentID'));
            if (!empty($finYearID))
            {
                $this->datatables->where('companyFinanceYearID', $this->input->post('finYearID'));
            }
            $this->datatables->from('srp_erp_departmentfinanceperiod', 'srp_employeesdetails');
            $this->datatables->add_column('status', '$1', 'load_department_Financial_year_isactive_status(departmentFinancePeriodID,isActive)');
            $this->datatables->add_column('current', '$1', 'load_department_Financial_year_isactive_current(departmentFinancePeriodID,isCurrent,companyFinanceYearID)');
            $this->datatables->add_column('closed', '$1', 'load_department_financialperiod_isclosed_closed(departmentFinancePeriodID,isClosed)');
            $this->datatables->edit_column('DT_RowClass', '$1', 'set_is_closed_is_current_class(isClosed,isCurrent)');
            $this->datatables->add_column('reopen', '$1', 'load_department_fp_action(isClosed,departmentFinancePeriodID)');
        }
        else
        {
            $this->datatables->select("companyFinancePeriodID,companyFinanceYearID,dateFrom,dateTo,isActive,isCurrent,isClosed");
            $this->datatables->where('companyFinanceYearID', $this->input->post('finYearID'));
            $this->datatables->from('srp_erp_companyfinanceperiod', 'srp_employeesdetails');
            $this->datatables->add_column('status', '$1', 'load_department_Financial_year_isactive_status(departmentFinancePeriodID,isActive,"department_required")');
            $this->datatables->add_column('current', '$1', 'load_department_Financial_year_isactive_current(departmentFinancePeriodID,isCurrent,companyFinanceYearID,"department_required")');
            $this->datatables->add_column('closed', '$1', 'load_department_financialperiod_isclosed_closed(departmentFinancePeriodID,isClosed,"department_required")');
            $this->datatables->edit_column('DT_RowClass', '$1', 'set_is_closed_is_current_class(isClosed,isCurrent,"department_required")');
            $this->datatables->add_column('reopen', '$1', 'load_department_fp_action(isClosed,departmentFinancePeriodID,"department_required")');
        }
        echo $this->datatables->generate();
    }

    function update_department_financial_year_isactive_status()
    {
        echo json_encode($this->Financial_year_model->update_department_financial_year_isactive_status());
    }

    function check_department_financial_period_iscurrent_activated()
    {
        echo json_encode($this->Financial_year_model->check_department_financial_period_iscurrent_activated());
    }

    function change_department_financial_period_current()
    {
        echo json_encode($this->Financial_year_model->change_department_financial_period_current());
    }

    function update_department_financialperiodclose()
    {
        echo json_encode($this->Financial_year_model->update_department_financialperiodclose());
    }

    function reopen_department_financial_period()
    {
        $departmentFinancePeriodID = trim($this->input->post('departmentFinancePeriodID') ?? '');
        $financePeriodTBL = $this->db->query("UPDATE srp_erp_departmentfinanceperiod SET isClosed = 0 WHERE id = $departmentFinancePeriodID");
        if ($this->db->affected_rows() > 0)
        {
            echo json_encode(array('s', 'Finance Period reopened successfully for the selected Department'));
        }
        else
        {
            echo json_encode(array('s', 'Failed to reopen Finance Period for the selected Department'));
        }
    }
}
