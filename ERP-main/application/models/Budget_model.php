<?php

class Budget_model extends ERP_Model
{

    function get_budget_master_header($budgetAutoID)
    {
        $q = "SELECT * FROM srp_erp_budgetmaster WHERE budgetAutoID={$budgetAutoID}";
        $results = $this->db->query($q)->row_array();
        return $results;
    }

    function fetch_finance_year_period_budget(){
        $convertFormat=convert_date_format_sql();
        $this->db->select('companyFinancePeriodID,companyFinanceYearID,DATE_FORMAT(dateFrom,\''.$convertFormat.'\') AS dateFrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo ');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinanceYearID',$this->input->post('companyFinanceYearID'));
        //$this->db->where('isActive',1);
        //$this->db->where('isCurrent',1);
        //$this->db->where('isClosed',0);
        return $this->db->get()->result_array();
    }

    function fetch_finance_year_period_budget_load_missing($companyFinanceYearID){
        $convertFormat=convert_date_format_sql();
        $this->db->select('companyFinancePeriodID,companyFinanceYearID,DATE_FORMAT(dateFrom,\''.$convertFormat.'\') AS dateFrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo ');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinanceYearID',$companyFinanceYearID);
        //$this->db->where('isActive',1);
        //$this->db->where('isCurrent',1);
        //$this->db->where('isClosed',0);
        return $this->db->get()->result_array();
    }

    function budget_confirmation() {
        $budgetAutoID = trim($this->input->post('budgetAutoID') ?? '');
        $this->db->select('*');
        $this->db->where('budgetAutoID', $budgetAutoID);
        $this->db->from('srp_erp_budgetdetail');
        $detail = $this->db->get()->row_array();

        $system_code=$this->input->post('budgetAutoID');
        $this->load->library('approvals');
        $this->db->select('budgetAutoID, documentSystemCode,documentDate');
        $this->db->where('budgetAutoID', $system_code);
        $this->db->from('srp_erp_budgetmaster');
        $bd_data = $this->db->get()->row_array();

        $validate_code = validate_code_duplication($bd_data['documentSystemCode'], 'documentSystemCode', $system_code,'budgetAutoID', 'srp_erp_budgetmaster');
        if(!empty($validate_code)) {
            return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
        }

        $autoApproval= get_document_auto_approval('BD');
        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($bd_data['budgetAutoID'], 'srp_erp_budgetmaster','budgetAutoID', 'BD',$bd_data['documentSystemCode'],$bd_data['documentDate']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('BD', $bd_data['budgetAutoID'], $bd_data['documentSystemCode'], 'Budget', 'srp_erp_budgetmaster', 'budgetAutoID',0,$bd_data['documentDate']);
        }else{
            return array('e','Approval levels are not set for this document.');
        }

        if($detail){
            $data = array(
                'confirmedYN'        => 1,
                'confirmedDate'      => $this->common_data['current_date'],
                'confirmedByEmpID'   => $this->common_data['current_userID'],
                'confirmedByName'    => $this->common_data['current_user']
            );
            $this->db->where('budgetAutoID', $budgetAutoID);
            $this->db->update('srp_erp_budgetmaster', $data);
            return array('s','Document Confirmed Successfully');
        }else{
            return array('e','No detail records found to confirm this document');
        }
    }

    function save_budget_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->load->library('approvals');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('budgetAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['budgetAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $maxLevel = $this->approvals->maxlevel('BD');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'BD');
        }
        if ($approvals_status == 1) {
            $this->session->set_flashdata('s', 'Approved successfully');
            return true;
        }

    }
}