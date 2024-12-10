<?php

class Recurring_je extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Recurring_je_model');
        $this->load->helpers('recurring_je_helper');
    }

    function fetch_recurring_journal_entry()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_recurringjvmaster.RJVMasterAutoId as RJVMasterAutoId,RJVcode,confirmedYN,approvedYN,transactionCurrency,transactionCurrencyDecimalPlaces, RJVNarration,RJVStartDate, RJVEndDate,transactionAmount,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID,IFNULL(debamt.debitAmount,0) as total_value");
        $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY RJVMasterAutoId) debamt', '(debamt.RJVMasterAutoId = srp_erp_recurringjvmaster.RJVMasterAutoId)', 'left');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->from('srp_erp_recurringjvmaster');
        $this->datatables->add_column('detail', '<b> &nbsp;&nbsp;Narration : </b> $1', 'RJVNarration');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"RJV",RJVMasterAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"RJV",RJVMasterAutoId)');
        /* $this->datatables->add_column('action', '<span class="pull-right"><a onclick="fetchPage(\'system/finance/journal_entry_new\',$1,\'Add Journal Entry\',\'Journal Entry\'); "><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'JE\',$1)"> <span class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;<a href="$2/$1" target="_blank"><span class="glyphicon glyphicon-print"></a> </span>', 'JVMasterAutoId,site_url("Journal_entry/journal_entry_conformation")');*/
        $this->datatables->add_column('action', '$1', 'recurring_journal_entry_action(RJVMasterAutoId,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('RJVStartDate', '<span >$1 </span>', 'convert_date_format(RJVStartDate)');
        $this->datatables->edit_column('RJVEndDate', '<span >$1 </span>', 'convert_date_format(RJVEndDate)');
        echo $this->datatables->generate();
        //&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_supplier_invoice($1,\'Supplier Invoice\');"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
    }

    function fetch_recurring_journal_entry_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $convertFormat = convert_date_format_sql();
        $currentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_recurringjvmaster.RJVMasterAutoId as RJVMasterAutoId,RJVcode,RJVNarration,DATE_FORMAT(RJVStartDate,\'' . $convertFormat . '\') AS RJVStartDate,DATE_FORMAT(RJVEndDate,\'' . $convertFormat . '\') AS RJVEndDate,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID, approvalLevelID,srp_erp_recurringjvmaster.companyID,transactionCurrencyDecimalPlaces,transactionCurrency,IFNULL(debamt.debitAmount,0) as total_value,ROUND(IFNULL(debamt.debitAmount,0), 2) as total_value_search');
            $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY RJVMasterAutoId) debamt', '(debamt.RJVMasterAutoId = srp_erp_recurringjvmaster.RJVMasterAutoId)', 'left');
            $this->datatables->from('srp_erp_recurringjvmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_recurringjvmaster.RJVMasterAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_recurringjvmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_recurringjvmaster.currentLevelNo');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_recurringjvmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'RJV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'RJV');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('RJVcode', '$1', 'approval_change_modal(RJVcode,RJVMasterAutoId,documentApprovedID,approvalLevelID,approvedYN,RJV,0)');
            $this->datatables->add_column('detail', '<b>Narration : </b> $2 <b> &nbsp;&nbsp;Start Date : </b> $3 <b> &nbsp;&nbsp;End Date : </b> $1', 'RJVEndDate,RJVNarration,RJVStartDate');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "RJV", RJVMasterAutoId)');
            $this->datatables->add_column('edit', '$1', 'rjv_approval(RJVMasterAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_recurringjvmaster.RJVMasterAutoId as RJVMasterAutoId,RJVcode,RJVNarration,DATE_FORMAT(RJVStartDate,\'' . $convertFormat . '\') AS RJVStartDate,DATE_FORMAT(RJVEndDate,\'' . $convertFormat . '\') AS RJVEndDate,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID, approvalLevelID,srp_erp_recurringjvmaster.companyID,transactionCurrencyDecimalPlaces,transactionCurrency,IFNULL(debamt.debitAmount,0) as total_value,ROUND(IFNULL(debamt.debitAmount,0), 2) as total_value_search');
            $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY RJVMasterAutoId) debamt', '(debamt.RJVMasterAutoId = srp_erp_recurringjvmaster.RJVMasterAutoId)', 'left');
            $this->datatables->from('srp_erp_recurringjvmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_recurringjvmaster.RJVMasterAutoId');


            $this->datatables->where('srp_erp_recurringjvmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'RJV');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID',$currentuserid);
            $this->datatables->group_by('srp_erp_recurringjvmaster.RJVMasterAutoId');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');


            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('RJVcode', '$1', 'approval_change_modal(RJVcode,RJVMasterAutoId,documentApprovedID,approvalLevelID,approvedYN,RJV,0)');
            $this->datatables->add_column('detail', '<b>Narration : </b> $2 <b> &nbsp;&nbsp;Start Date : </b> $3 <b> &nbsp;&nbsp;End Date : </b> $1', 'RJVEndDate,RJVNarration,RJVStartDate');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "RJV", RJVMasterAutoId)');
            $this->datatables->add_column('edit', '$1', 'rjv_approval(RJVMasterAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_recurring_journal_entry_header()
    {
        $date_format_policy = date_format_policy();
        $Jdte = $this->input->post('RJVStartDate');
        $RJVStartDate = input_format_date($Jdte, $date_format_policy);

        $EJdte = $this->input->post('RJVEndDate');
        $RJVEndDate = input_format_date($EJdte, $date_format_policy);

        $this->form_validation->set_rules('RJVStartDate', 'Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('RJVEndDate', 'End Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('RJVNarration', 'Narration', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if ($RJVEndDate >= $RJVStartDate) {
                echo json_encode($this->Recurring_je_model->save_recurring_journal_entry_header());
            } else {
                $this->session->set_flashdata('e', 'Start date should be less than End date');
                echo json_encode(FALSE);
            }
        }
    }

    function save_gl_detail()
    {
        //$projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        /*$gl_types = $this->input->post('gl_type');*/
        $segment_gls = $this->input->post('segment_gl');
        $creditAmount = $this->input->post('creditAmount');
        $debitAmount = $this->input->post('debitAmount');
        $descriptions = $this->input->post('description');

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
            /*$this->form_validation->set_rules("gl_type[{$key}]", 'GL Type', 'required|trim');*/
            $gl = fetch_gl_account_desc($gl_codes[$key]);
            if ($gl['masterCategory'] == 'PL') {
                $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            }
            $this->form_validation->set_rules("creditAmount[{$key}]", 'Credit Amount', 'trim|required');
            $this->form_validation->set_rules("debitAmount[{$key}]", 'Debit Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Narration', 'trim|required');
            /* if($projectExist == 1){
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Recurring_je_model->save_gl_detail());
        }
    }

    function update_gl_detail()
    {
        //$projectExist = project_is_exist();
        $gl_codes = $this->input->post('edit_gl_code');
        /*$gl_types = $this->input->post('gl_type');*/
        $segment_gls = $this->input->post('edit_segment_gl');
        $creditAmount = $this->input->post('editcreditAmount');
        $debitAmount = $this->input->post('editdebitAmount');
        $descriptions = $this->input->post('editdescription');

        $this->form_validation->set_rules("edit_gl_code", 'GL Code', 'required|trim');
        $gl = fetch_gl_account_desc($gl_codes);
        if ($gl['masterCategory'] == 'PL') {
            $this->form_validation->set_rules("edit_segment_gl", 'Segment', 'trim|required');
        }
        $this->form_validation->set_rules("editcreditAmount", 'Credit Amount', 'trim|required');
        $this->form_validation->set_rules("editdebitAmount", 'Debit Amount', 'trim|required');
        $this->form_validation->set_rules("editdescription", 'Narration', 'trim|required');
       /*  if($projectExist == 1){
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        } */

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Recurring_je_model->update_gl_detail());
        }
    }

    function save_rjv_approval()
    {
        $system_code = trim($this->input->post('RJVMasterAutoId') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if($status==1){
            $approvedYN=checkApproved($system_code,'RJV',$level_id);
            if($approvedYN){
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('RJVMasterAutoId');
                $this->db->where('RJVMasterAutoId', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_recurringjvmaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('RJVMasterAutoId', 'RJV Master Auto Id', 'trim|required');
                    $this->form_validation->set_rules('Level', 'Level', 'trim|required');
                    if($this->input->post('status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Recurring_je_model->save_rjv_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('RJVMasterAutoId');
            $this->db->where('RJVMasterAutoId', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_recurringjvmaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'RJV',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('RJVMasterAutoId', 'RJV Master Auto Id', 'trim|required');
                    $this->form_validation->set_rules('Level', 'Level', 'trim|required');
                    if($this->input->post('status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Recurring_je_model->save_rjv_approval());
                    }
                }
            }
        }
    }

    function load_journal_entry_header()
    {
        echo json_encode($this->Recurring_je_model->load_journal_entry_header());
    }

    function fetch_journal_entry_detail()
    {
        echo json_encode($this->Recurring_je_model->fetch_journal_entry_detail());
    }

    function delete_Journal_entry_detail()
    {
        echo json_encode($this->Recurring_je_model->delete_Journal_entry_detail());
    }

    function delete_recurring_journal_entry()
    {
        echo json_encode($this->Recurring_je_model->delete_recurring_journal_entry());
    }

    function recurring_journal_entry_confirmation()
    {
        echo json_encode($this->Recurring_je_model->recurring_journal_entry_confirmation());
    }

    function recurring_journal_entry_conformation()
    {
        $RJVMasterAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('RJVMasterAutoId') ?? '');
        $data['extra'] = $this->Recurring_je_model->fetch_Journal_entry_template_data($RJVMasterAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
         $data['signature']=$this->Recurring_je_model->fetch_signaturelevel();
        } else {
           $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/recurringJV/recurring_journal_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function referback_journal_entry()
    {

        $RJVMasterAutoId = $this->input->post('RJVMasterAutoId');


            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($RJVMasterAutoId, 'RJV');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }




    }

    function load_jv_detail()
    {
        $RJVDetailAutoID = $this->input->post('RJVDetailAutoID');
        $detail = $this->db->query("select * from srp_erp_recurringjvdetail WHERE RJVDetailAutoID={$RJVDetailAutoID}")->row_array();
        echo exit(json_encode($detail));
    }

    function re_open_journal_entry()
    {
        echo json_encode($this->Recurring_je_model->re_open_journal_entry());
    }


}