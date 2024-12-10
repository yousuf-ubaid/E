<?php

class Financial_year_model extends ERP_Model
{

    function save_financial_year()
    {
        $this->db->trans_start();
        $x = 0;
        $data['beginingDate'] = trim($this->input->post('beginningdate') ?? '');
        $data['endingDate'] = trim($this->input->post('endingdate') ?? '');
        $data['comments'] = trim($this->input->post('comments') ?? '');
        $data['isActive'] = 1;
        $data['isClosed'] = 0;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_companyfinanceyear', $data);
        $last_id = $this->db->insert_id();
        $date_arr = array();
        $first_date = $this->input->post('beginningdate');
        $next_date = $this->input->post('endingdate');
        while ($first_date <= $next_date)
        {
            $last_date = date("Y-m-t", strtotime($first_date));
            array_push($date_arr, array('dateFrom' => $first_date, 'dateTo' => $last_date, 'companyFinanceYearID' => $last_id, 'companyID' => $this->common_data['company_data']['company_id'], 'companyCode' => $this->common_data['company_data']['company_code']));
            $first_date = date("Y-m-d", strtotime($first_date . '+ 1 month'));
            $x++;
        }
        $this->db->insert_batch('srp_erp_companyfinanceperiod', $date_arr);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->session->set_flashdata('e', 'Financial Year :  Created & ' . $x . ' Financial Period Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        }
        else
        {
            $this->session->set_flashdata('s', 'Financial Year : Created & ' . $x . ' Financial Period Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function update_comments()
    {
        $pk = explode('|', trim($this->input->post('pk') ?? ''));  // Primary key from the editable field
        $financialYearID = $pk[0];
        $comments = $this->input->post('value');
        // Proceed with the update if validation passes
        $this->db->trans_start();  // Start the transaction

        // Update the comments field in the financial year record
        $data['comments'] = $comments;

        $this->db->where('companyFinanceYearID', $financialYearID);
        $this->db->update('srp_erp_companyfinanceyear', $data);

        $this->db->trans_complete();  // Complete the transaction

        // Check the transaction status
        if ($this->db->trans_status() === FALSE)
        {
            // Transaction failed
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            // Transaction succeeded
            $this->db->trans_commit();
            return true;
        }
    }

    function update_year_status()
    {
        $checked = trim($this->input->post('chkedvalue') ?? '');
        $data['isActive'] = $this->input->post('chkedvalue');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $result = $this->db->update('srp_erp_companyfinanceyear', $data);
        if ($result)
        {
            if ($checked == 0)
            {
                $data['isActive'] = $checked;
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
                $this->db->update('srp_erp_companyfinanceperiod', $data);
            }
        }
        $this->session->set_flashdata('s', 'Financial Year : Updated Successfully');
        return true;
    }

    function update_year_current()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('beginingDate, endingDate, companyFinanceYearID');
        $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $finance_year_data = $this->db->get('srp_erp_companyfinanceyear')->row_array();
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_companyfinanceyear', array('isCurrent' => 0));
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_companyfinanceperiod', array('isCurrent' => 0));
        $data['isCurrent'] = 1;
        $data['isActive'] = 1;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $this->db->update('srp_erp_companyfinanceyear', $data);
        $this->db->where('company_id', $this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_company', array('companyFinanceYearID' => $finance_year_data['companyFinanceYearID'], 'companyFinanceYear' => $finance_year_data['beginingDate'] . ' - ' . $finance_year_data['endingDate'], 'FYBegin' => $finance_year_data['beginingDate'], 'FYEnd' => $finance_year_data['endingDate']));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->session->set_flashdata('e', 'Financial Year : ' . $finance_year_data['beginingDate'] . ' - ' . $finance_year_data['endingDate'] . ' Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        }
        else
        {
            $this->session->set_flashdata('s', 'Financial Year : ' . $finance_year_data['beginingDate'] . ' - ' . $finance_year_data['endingDate'] . ' Activated Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
        return true;
    }

    function update_year_close()
    {
        $data['isActive'] = 0;
        $data['isCurrent'] = 0;
        $data['isClosed'] = $this->input->post('chkedvalue');
        $data['closedByEmpID'] = $this->common_data['current_userID'];
        $data['closedByEmpName'] = $this->common_data['current_user'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $closedYear = $this->db->update('srp_erp_companyfinanceyear', $data);
        if ($closedYear)
        {
            $data['isActive'] = 0;
            $data['isCurrent'] = 0;
            $data['isClosed'] = $this->input->post('chkedvalue');
            $data['closedByEmpID'] = $this->common_data['current_userID'];
            $data['closedByEmpName'] = $this->common_data['current_user'];
            $data['closedDate'] = $this->common_data['current_date'];
            $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
            $this->db->update('srp_erp_companyfinanceperiod', $data);
            $this->session->set_flashdata('s', 'Financial Year : Closed Successfully');
            return true;
        }
    }

    function update_financial_year_isactive_status()
    {

        $checkMonth = $this->db->query("select companyFinancePeriodID,dateFrom,dateTo,companyFinanceYearID from srp_erp_companyfinanceperiod where companyFinancePeriodID =  " . $this->input->post('companyFinancePeriodID') . "")->row_array();

        $checkyearActive = $this->db->query("select companyFinanceYearID,isActive from srp_erp_companyfinanceyear where companyFinanceYearID =  " . $checkMonth['companyFinanceYearID'] . "")->row_array();

        if ($checkyearActive['isActive'] == 1)
        {
            $current_month = date("Y-m");
            $database_month = date("Y-m", strtotime($checkMonth['dateFrom']));

            if ($current_month >= $database_month)
            {
                $data['isActive'] = $this->input->post('chkedvalue');
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $this->db->where('companyFinancePeriodID', $this->input->post('companyFinancePeriodID'));
                $this->db->update('srp_erp_companyfinanceperiod', $data);
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
            else
            {
                $this->session->set_flashdata('e', 'Financial Period Cannot be greater than current month');
                return false;
            }
        }
        else
        {
            $this->session->set_flashdata('e', 'Financial Year should be active !');
            return false;
        }
    }

    function change_financial_period_current()
    {
        $this->db->trans_start();

        $this->db->select('dateFrom, dateTo');
        $this->db->where('companyFinancePeriodID', $this->input->post('companyFinancePeriodID'));
        $finance_period_data = $this->db->get('srp_erp_companyfinanceperiod')->row_array();

        $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_companyfinanceperiod', array('isCurrent' => 0));

        $data['isCurrent'] = 1;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('companyFinancePeriodID', $this->input->post('companyFinancePeriodID'));
        $this->db->update('srp_erp_companyfinanceperiod', $data);

        $this->db->where('company_id', $this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_company', array('FYPeriodDateFrom' => $finance_period_data['dateFrom'], 'FYPeriodDateTo' => $finance_period_data['dateTo'], 'companyFinancePeriodID' => $this->input->post('companyFinancePeriodID')));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->session->set_flashdata('e', 'Financial Period : ' . $finance_period_data['dateFrom'] . ' - ' . $finance_period_data['dateTo'] . ' Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        }
        else
        {
            $this->session->set_flashdata('s', 'Financial Period : ' . $finance_period_data['dateFrom'] . ' - ' . $finance_period_data['dateTo'] . ' Activated Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
        return true;
    }

    function update_financialperiodclose()
    {
        $data['isClosed'] = $this->input->post('chkedvalue');
        $data['closedByEmpID'] = $this->common_data['current_userID'];
        $data['closedByEmpName'] = $this->common_data['current_user'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($data['isClosed'] == 1)
        {
            $data['isActive'] = 0;
        }

        $this->db->where('companyFinancePeriodID', $this->input->post('companyFinancePeriodID'));
        $result = $this->db->update('srp_erp_companyfinanceperiod', $data);
        if ($result)
        {
            $this->session->set_flashdata('s', 'Records Closed Successfully');
        }
        return true;
    }

    function check_financial_period_iscurrent_activated()
    {
        $this->db->select('companyFinanceYearID,isCurrent');
        $this->db->from('srp_erp_companyfinanceyear');
        $this->db->where('companyFinanceYearID', trim($this->input->post('companyFinanceYearID') ?? ''));
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('companyFinancePeriodID,isActive');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinancePeriodID', trim($this->input->post('companyFinancePeriodID') ?? ''));
        $data['details'] = $this->db->get()->row_array();
        return $data;
    }

    function create_13th_month_Financial_Period_toThisYear()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $financeYearID = trim($this->input->post('financeYearID') ?? '');

        $this->db->select('companyFinancePeriodID');
        $this->db->where('companyFinanceYearID', $financeYearID);
        $this->db->where('is13thMonth', 1);
        $this->db->where('companyID', $companyID);
        $finance_13th_period_data = $this->db->get('srp_erp_companyfinanceperiod')->row_array();

        if (!empty($finance_13th_period_data['companyFinancePeriodID']))
        {
            return array('e', '13th month financial period is already created!');
        }
        else
        {
            //print($financeYearID);exit;
            $succeed = $this->db->query("INSERT INTO srp_erp_companyfinanceperiod (is13thMonth, companyFinanceYearID, dateFrom, dateTo, isActive, isCurrent, isClosed,  companyID, companyCode) 
                                SELECT 
                                    1 as is13thMonth,
                                    companyFinanceYearID,
                                    dateFrom,
                                    dateTo,
                                    isActive,
                                    isCurrent,
                                    isClosed, 
                                    companyID,
                                    companyCode 
                                FROM
                                    srp_erp_companyfinanceperiod 
                                WHERE
                                    dateTo = ( SELECT MAX( dateTo ) FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$financeYearID}) 
                                    AND companyID = {$companyID} 
                                    AND companyFinanceYearID = {$financeYearID}
                            ");

            if ($succeed)
            {
                return array('s', 'Successfully added 13th month financial period for this year!');
            }
            else
            {
                return array('e', 'Failed to add 13th month financial period for this year!');
            }
        }
    }


    function save_department_financial_periods()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $financeYearID = trim($this->input->post('financeYearID') ?? '');
        $fyDepartmentID = trim($this->input->post('fyDepartmentID') ?? '');

        $this->db->select('*');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinanceYearID', $financeYearID);
        $this->db->where('companyID', $companyID);
        $finance_periods_data = $this->db->get()->result_array();
        //echo '<pre>';print_r($finance_periods_data);exit;

        $this->db->select('*');
        $this->db->from('srp_erp_departmentfinanceperiod');
        $this->db->where('companyFinanceYearID', $financeYearID);
        $this->db->where('fyDepartmentID', $fyDepartmentID);
        $this->db->where('companyID', $companyID);
        $department_finance_periods_data = $this->db->get()->result_array();
        //echo '<pre>';print_r($department_finance_periods_data);exit;

        if (!empty($department_finance_periods_data))
        {
            foreach ($finance_periods_data as $key => $row)
            {
                $exists = false;
                foreach ($department_finance_periods_data as $dep_row)
                {
                    if ($row['companyFinancePeriodID'] == $dep_row['companyFinancePeriodID'])
                    {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists)
                {
                    $dep_companyFinancePeriodID = $row['companyFinancePeriodID'];
                    $companyFinanceYearID = $this->db->escape($row['companyFinanceYearID']);
                    $dateFrom = $this->db->escape($row['dateFrom']);
                    $dateTo = $this->db->escape($row['dateTo']);
                    $isActive = $this->db->escape($row['isActive']);
                    $isCurrent = $this->db->escape($row['isCurrent']);
                    $isClosed = $this->db->escape($row['isClosed']);
                    $companyID = $this->db->escape($row['companyID']);
                    $companyCode = $this->db->escape($row['companyCode']);

                    $this->db->query("INSERT INTO srp_erp_departmentfinanceperiod (fyDepartmentID, companyFinancePeriodID, companyFinanceYearID, dateFrom, dateTo, isActive, isCurrent, isClosed,  companyID, companyCode) 
                                  VALUES ( 
                                            {$fyDepartmentID},
                                            {$dep_companyFinancePeriodID},
                                            {$companyFinanceYearID},
                                            {$dateFrom},
                                            {$dateTo},
                                            {$isActive},
                                            {$isCurrent},
                                            {$isClosed},
                                            {$companyID},
                                            {$companyCode} 
                                        )
                    ");
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Failed to add missed financial periods for this department!');
            }
            else
            {
                return array('s', 'Successfully added missed financial periods for the selected department!');
            }
        }
        else
        {
            $this->db->query("INSERT INTO srp_erp_departmentfinanceperiod (fyDepartmentID, companyFinancePeriodID, companyFinanceYearID, dateFrom, dateTo, isActive, isCurrent, isClosed,  companyID, companyCode) 
                        SELECT 
                            {$fyDepartmentID} AS fyDepartmentID,
                            companyFinancePeriodID,
                            companyFinanceYearID,
                            dateFrom,
                            dateTo,
                            isActive,
                            isCurrent,
                            isClosed, 
                            companyID,
                            companyCode 
                        FROM
                            srp_erp_companyfinanceperiod 
                        WHERE
                            companyID = {$companyID} 
                            AND companyFinanceYearID = {$financeYearID} 
            ");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Failed to add financial periods for the selected department!');
            }
            else
            {
                return array('s', 'Successfully added all financial periods of this year for the selected department!');
            }
        }
    }

    function update_department_financial_year_isactive_status()
    {

        if (!empty($this->input->post('departmentFinancePeriodID')))
        {

            $checkMonth = $this->db->query("select id as departmentFinancePeriodID,dateFrom,dateTo,companyFinanceYearID from srp_erp_departmentfinanceperiod where id =  " . $this->input->post('departmentFinancePeriodID') . "")->row_array();

            $checkyearActive = $this->db->query("select companyFinanceYearID,isActive from srp_erp_companyfinanceyear where companyFinanceYearID =  " . $checkMonth['companyFinanceYearID'] . "")->row_array();

            if ($checkyearActive['isActive'] == 1)
            {
                $current_month = date("Y-m");
                $database_month = date("Y-m", strtotime($checkMonth['dateFrom']));

                if ($current_month >= $database_month)
                {
                    $data['isActive'] = $this->input->post('chkedvalue');
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];

                    $this->db->where('id', $this->input->post('departmentFinancePeriodID'));
                    $this->db->update('srp_erp_departmentfinanceperiod', $data);
                    $this->session->set_flashdata('s', 'Records Updated Successfully');
                    return true;
                }
                else
                {
                    $this->session->set_flashdata('e', 'Financial Period Cannot be greater than current month');
                    return false;
                }
            }
            else
            {
                $this->session->set_flashdata('e', 'Financial Year should be active !');
                return false;
            }
        }
        else
        {
            $this->session->set_flashdata('e', 'Please Select a Department');
            return false;
        }
    }


    function change_department_financial_period_current()
    {
        $this->db->trans_start();

        $departmentFinancePeriodID = $this->input->post('departmentFinancePeriodID');

        if (!empty($departmentFinancePeriodID))
        {
            $this->db->select('dateFrom, dateTo');
            $this->db->where('id', $this->input->post('departmentFinancePeriodID'));
            $finance_period_data = $this->db->get('srp_erp_departmentfinanceperiod')->row_array();

            $this->db->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->update('srp_erp_departmentfinanceperiod', array('isCurrent' => 0));

            $data['isCurrent'] = 1;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('id', $this->input->post('departmentFinancePeriodID'));
            $this->db->update('srp_erp_departmentfinanceperiod', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Financial Period : ' . $finance_period_data['dateFrom'] . ' - ' . $finance_period_data['dateTo'] . ' Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Financial Period : ' . $finance_period_data['dateFrom'] . ' - ' . $finance_period_data['dateTo'] . ' added Successfully as current Financial Period for the Selected Department');
                $this->db->trans_commit();
                return array('status' => true);
            }
        }
        else
        {
            $this->session->set_flashdata('e', 'Please Select a Department');
            return false;
        }
    }

    function check_department_financial_period_iscurrent_activated()
    {
        $this->db->select('companyFinanceYearID,isCurrent');
        $this->db->from('srp_erp_companyfinanceyear');
        $this->db->where('companyFinanceYearID', trim($this->input->post('companyFinanceYearID') ?? ''));
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('id as departmentFinancePeriodID,isActive');
        $this->db->from('srp_erp_departmentfinanceperiod');
        $this->db->where('id', trim($this->input->post('departmentFinancePeriodID') ?? ''));
        $data['details'] = $this->db->get()->row_array();
        return $data;
    }

    function update_department_financialperiodclose()
    {
        if (!empty($this->input->post('departmentFinancePeriodID')))
        {
            $data['isClosed'] = $this->input->post('chkedvalue');
            $data['closedByEmpID'] = $this->common_data['current_userID'];
            $data['closedByEmpName'] = $this->common_data['current_user'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if ($data['isClosed'] == 1)
            {
                $data['isActive'] = 0;
            }

            $this->db->where('id', $this->input->post('departmentFinancePeriodID'));
            $result = $this->db->update('srp_erp_departmentfinanceperiod', $data);
            if ($result)
            {
                $this->session->set_flashdata('s', 'Financial Period Closed Successfully for the Selected Department');
            }
            return true;
        }
        else
        {
            $this->session->set_flashdata('e', 'Please Select a Department');
            return false;
        }
    }
}
