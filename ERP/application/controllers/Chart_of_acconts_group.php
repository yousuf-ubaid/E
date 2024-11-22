<?php

class Chart_of_acconts_group extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Chart_of_acconts_group_model');
        $this->load->helpers('group_management');
    }

    function load_master_ofAccount()
    {
        $accountType = $this->input->post('accountTYpe');
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid=$companyID;
        $header = false;
        if (!empty($accountType)) {
            $commaList = implode(', ', $accountType);
            $header = $this->db->query("select Type,accountCategoryTypeID,CategoryTypeDescription from srp_erp_accountcategorytypes WHERE accountCategoryTypeID IN({$commaList})  order by sortOrder asc")->result_array();
        }
        $details = $this->db->query("SELECT srp_erp_groupchartofaccounts.* FROM srp_erp_groupchartofaccounts  WHERE srp_erp_groupchartofaccounts.groupID = {$Grpid} GROUP BY srp_erp_groupchartofaccounts.GLAutoID ORDER BY srp_erp_groupchartofaccounts.GLSecondaryCode ASC ")->result_array();

        $data['header'] = $header;
        $data['details'] = $details;

        $html = $this->load->view('system/group_chart_of_accounts/ajax/ajax-load-srp-erp-group-chart-of-account', $data, true);
        echo $html;


    }

    function fetch_chart_of_acconts()
    {
        //$this->load->library('Datatables-2');
        $this->datatables->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory,masterAccount,approvedYN,CategoryTypeDescription', false);
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('masterAccountYN', 1);
        if ($this->input->post('master_account')) {
            $this->datatables->where('masterAccount', $this->input->post('master_account'));
        }
        if ($this->input->post('category')) {
            $this->datatables->where('masterCategory', $this->input->post('category'));
        }
        if ($this->input->post('control_account')) {
            $this->datatables->where('subCategory', $this->input->post('control_account'));
        }
        $this->datatables->from('srp_erp_chartofaccounts');
        $this->datatables->add_column('subCategorys', '$1', 'fetch_coa_type(subCategory)');
        $this->datatables->add_column('confirmed', '$1', 'confirm(approvedYN)');
        $this->datatables->add_column('balance', '$1', '<spsn class="pull-right">00</span>');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="edit_chart_of_accont($1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_chart_of_accont($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'GLAutoID');
        echo $this->datatables->generate();
    }

    function save_chart_of_accont()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid=$companyID;
        if($this->input->post('controlAccountUpdate')==0) {  /*if its 0 edit | not control account */

        if (!$this->input->post('GLAutoID')) {
            $this->form_validation->set_rules('masterAccountYN', 'Master Account', 'trim|required');
            $this->form_validation->set_rules('isBank', 'Is Bank', 'trim|required');
        }
        if ($this->input->post('isBank') && $this->input->post('isCash') !=1) {
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
            $this->form_validation->set_rules('GLSecondaryCode', 'Account Code', 'trim|required');
            $this->form_validation->set_rules('GLDescription', 'Account Description', 'trim|required');
            $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');

            $this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required');
            $this->form_validation->set_rules('bank_branch', 'Bank Branch', 'trim|required');
            $this->form_validation->set_rules('bank_swift_code', 'Bank Swift Code', 'trim|required');
            $this->form_validation->set_rules('bankCurrencyCode', 'Bank Currency', 'trim|required');
            $this->form_validation->set_rules('bankAccountNumber', 'Bank Account Number', 'trim|required');
            $this->form_validation->set_rules('bankCheckNumber', 'Check Number', 'trim|required');
        }else{
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
            $this->form_validation->set_rules('GLSecondaryCode', 'Account Code', 'trim|required');
            $this->form_validation->set_rules('GLDescription', 'Account Description', 'trim|required');
            $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');
        }

        if ($this->input->post('masterAccountYN')) {
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        } else {
            $this->form_validation->set_rules('masterAccount', 'Master Account', 'trim|required');

        }

        $this->form_validation->set_rules('masterAccountYN', 'Master Account YN', 'trim|required');

        $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        }
        /*update validate control account*/
        $this->form_validation->set_rules('GLSecondaryCode', 'Secondary Code', 'trim|required');

        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($this->input->post('controlAccountUpdate')==0) {
                if ($this->input->post('GLAutoID')) {
                    $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
                    $accountCategoryTypeID = trim($this->input->post('accountCategoryTypeID') ?? '');
                    $masterAccountYN = trim($this->input->post('masterAccountYN') ?? '');
                    $validate = $this->db->query("select masterAccountYN,accountCategoryTypeID from srp_erp_groupchartofaccounts WHERE GLAutoID={$GLAutoID}")->row_array();
                    $recExistMaster = $this->db->query("SELECT * FROM srp_erp_groupchartofaccounts WHERE masterAutoID={$GLAutoID}")->row_array();
                    if (!empty($recExistMaster) && $masterAccountYN == 0) {
                        $this->session->set_flashdata('e', 'Ledger : You cannot change master account to sub account');

                        echo json_encode(array('status' => false));
                        exit;
                    }


                 $subAccountExist= $this->db->query("SELECT * FROM `srp_erp_groupchartofaccounts` WHERE masterAutoID = (SELECT GLAutoID FROM `srp_erp_groupchartofaccounts` WHERE `GLAutoID` = '{$GLAutoID}' AND `masterAccountYN` = '1') limit 1")->row_array();
        
                 if(!empty($subAccountExist)){
                      $this->session->set_flashdata('w', 'Ledger : Sub Account exist for this master account ');
                      
                    }
                } else {
                    $GLSecondaryCode = $this->input->post('GLSecondaryCode');
                    $companyID = current_companyID();
                    if ($GLSecondaryCode != '') {
                        $exit = $this->db->query("SELECT * FROM `srp_erp_groupchartofaccounts` WHERE groupID = {$Grpid}  AND GLSecondaryCode ='{$GLSecondaryCode}' ")->row_array();
                        if (!empty($exit)) {
                            $this->session->set_flashdata('e', 'GL secondary code is already exist');
                            echo json_encode(FALSE);
                            exit;
                        }

                    }
                }
            }

            echo json_encode($this->Chart_of_acconts_group_model->save_chart_of_accont());
        }
    }

    function fetch_chart_of_acconts_approval()
    {
        $this->datatables->select('GLAutoID,approvalLevelID,srp_erp_chartofaccounts.approvedYN as approvedYN,documentApprovedID,systemAccountCode,GLDescription');
        $this->datatables->from('srp_erp_chartofaccounts');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_chartofaccounts.GLAutoID');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'GL');
        $this->datatables->where('approvedEmpID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->where('srp_erp_chartofaccounts.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('systemAccountCode', '$1', 'approval_change_modal(systemAccountCode,GLAutoID,documentApprovedID,approvalLevelID,approvedYN,GL)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'confirm(approvedYN)');
        $this->datatables->add_column('edit', '$1', 'RV_action_approval(GLAutoID,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();
    }

    function load_chart_of_accont_header()
    {
        echo json_encode($this->Chart_of_acconts_group_model->load_chart_of_accont_header());
    }

    function fetch_master_account()
    {
        echo json_encode($this->Chart_of_acconts_group_model->fetch_master_account());
    }

    function delete_chart_of_accont()
    {
        echo json_encode($this->Chart_of_acconts_model->delete_chart_of_accont());
    }

    function fetch_cheque_number()
    {
        echo json_encode($this->Chart_of_acconts_model->fetch_cheque_number());
    }

    function fetch_chart_of_account_drilldown()
    {
        $GLAutoID = $this->input->post('GLAutoID');

        $outRecord = '';

        $depMaster = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE `masterAutoID` = '{$GLAutoID}'")->result_array();

        $outRecord .= '<table class="table" style="background-color: #f0f3f5;">';

        if (!empty($depMaster)) {
            foreach ($depMaster as $val) {
                $outRecord .= '<tr>';
                $outRecord .= '<td width="35%" style="text-indent: 7%;">' . $val['GLDescription'] . '</td>';
                $outRecord .= '<td width="11.5%">' . $val['systemAccountCode'] . '</td>';
                $outRecord .= '<td width="10.5%">' . $val['GLSecondaryCode'] . '</td>';
                $outRecord .= '<td width="10.5%">' . $val['CategoryTypeDescription'] . '</td>';
                $outRecord .= '<td width="10.5%">' . '<spsn class="pull-right">00</span>' . '</td>';
                $outRecord .= '<td width="6%">' . confirm($val['approvedYN']) . '</td>';
                $outRecord .= '<td width="10%">' . '<spsn class="pull-right"><a onclick="edit_chart_of_accont(' . $val['GLAutoID'] . ')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_chart_of_accont(' . $val['GLAutoID'] . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>' . '</td>';
                $outRecord .= '</tr>';
            }
        }
        $outRecord .= '</table>';
        echo $outRecord;
    }

    function load_company()
    {
        $data['GLAutoID'] = $this->input->post('GLAutoID');
        $html = $this->load->view('system/group_chart_of_accounts/ajax/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_chart_of_accounts()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['GLAutoID'] = $this->input->post('GLAutoID');
        $html = $this->load->view('system/group_chart_of_accounts/ajax/erp_load_company_chart_of_accounts', $data, true);
        echo $html;
    }

    function fetch_chart_Details(){
        $GLAutoID=$this->input->post('GLAutoID');
       /* $companyid = $this->common_data['company_data']['company_id'];
        $this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();
        $grpid=$grp['companyGroupID'];*/

        $this->datatables->select('groupChartofAccountDetailID,groupChartofAccountMasterID,chartofAccountID,srp_erp_groupchartofaccountdetails.companyID,companyGroupID,srp_erp_chartofaccounts.systemAccountCode as systemAccountCode,srp_erp_chartofaccounts.GLDescription as GLDescription,srp_erp_company.company_name as company_name');
        $this->datatables->from('srp_erp_groupchartofaccountdetails');
        $this->datatables->join('srp_erp_chartofaccounts', 'srp_erp_groupchartofaccountdetails.chartofAccountID = srp_erp_chartofaccounts.GLAutoID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupchartofaccountdetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID', $GLAutoID);
        //$this->datatables->where('srp_erp_groupchartofaccountdetails.companyGroupID', $grpid);
        $this->datatables->add_column('edit', '<a onclick="delete_chart_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupChartofAccountDetailID');
        echo $this->datatables->generate();
    }

    function save_chart_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('chartofAccountID[]', 'Accounts', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Chart_of_acconts_group_model->save_chart_link());
        }
    }

    function delete_chart_link(){
        echo json_encode($this->Chart_of_acconts_group_model->delete_chart_link());
    }

    function load_all_companies_chartofaccounts(){
        $company=array();
        $groupChartofAccountMasterID=$this->input->post('groupChartofAccountMasterID');
        $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra']=$this->Chart_of_acconts_group_model->fetch_chartofaccount_details();
        $comp = customer_company_link($groupChartofAccountMasterID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['masterAccountYN']=$masterAccountYN;
        $data['groupChartofAccountMasterID']=$groupChartofAccountMasterID;
        $html = $this->load->view('system/group_chart_of_accounts/ajax/erp_load_company_chart_of_accounts', $data, true);
        echo $html;
    }

    function load_all_companies_duplicate(){
        $company=array();
        $groupChartofAccountMasterID=$this->input->post('groupChartofAccountMasterID');
        $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra']=$this->Chart_of_acconts_group_model->fetch_chartofaccount_details();
        $comp = customer_company_link($groupChartofAccountMasterID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['masterAccountYN']=$masterAccountYN;
        $data['groupChartofAccountMasterID']=$groupChartofAccountMasterID;
        $html = $this->load->view('system/group_chart_of_accounts/ajax/erp_load_company_chart_of_accounts_duplicate', $data, true);
        echo $html;
    }

    function save_chart_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Chart_of_acconts_group_model->save_chart_duplicate());
        }
    }
    function updategroppolicy()
    {
        echo json_encode($this->Chart_of_acconts_group_model->updategroppolicy());
    }
}
