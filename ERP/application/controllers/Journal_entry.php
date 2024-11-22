<?php

class Journal_entry extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Journal_entry_model');
        $this->load->helper('employee');
    }

    function fetch_journal_entry()
    {
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( JVdate >= '" . $datefromconvert . " 00:00:00' AND JVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        $sSearch=$this->input->post('sSearch');
        $companyid=$this->common_data['company_data']['company_id'];
        $searches='';
        /*if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND ((JVcode Like '%$search%' ESCAPE '!') OR ( JVType Like '%$sSearch%' ESCAPE '!')  OR (JVNarration Like '%$sSearch%') OR (JVdate Like '%$sSearch%')) ";
        }*/

        $companyType = $this->session->userdata("companyType"); /**SMSD */                            /**SMSD */
        if($companyType == 2){                                                             /**SMSD */
            $where = "groupId = " . $companyid . $searches. $date . $status_filter ."";     /**SMSD */
        }else{                                                                              /**SMSD */
            $where = "companyID = " . $companyid . $searches. $date . $status_filter ."";   /**SMSD */
        }                                                                                   /**SMSD */

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_jvmaster.JVMasterAutoId as JVMasterAutoId,JVcode,JVdate,confirmedYN,approvedYN,transactionCurrency,transactionCurrencyDecimalPlaces, JVNarration,JVdate ,JVType,transactionAmount,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID,IFNULL(debamt.debitAmount,0) as total_value,IFNULL(debamt.debitAmount,0) as total_value_search,isSystemGenerated as isSystemGenerated");
        $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId) debamt', '(debamt.JVMasterAutoId = srp_erp_jvmaster.JVMasterAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_jvmaster');
        $this->datatables->add_column('detail', '<b>Type : </b> $1 <b> &nbsp;&nbsp;Narration : </b> $2', 'JVType,JVNarration,JVdate');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'je_total_value(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"JV",JVMasterAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"JV",JVMasterAutoId)');
        /* $this->datatables->add_column('action', '<span class="pull-right"><a onclick="fetchPage(\'system/finance/journal_entry_new\',$1,\'Add Journal Entry\',\'Journal Entry\'); "><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'JE\',$1)"> <span class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;<a href="$2/$1" target="_blank"><span class="glyphicon glyphicon-print"></a> </span>', 'JVMasterAutoId,site_url("Journal_entry/journal_entry_conformation")');*/
        $this->datatables->add_column('action', '$1', 'load_journal_voucher_actions(JVMasterAutoId,confirmedYN,approvedYN,createdUserID,isDeleted,JVType,confirmedByEmpID,isSystemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('JVdate', '<span >$1 </span>', 'convert_date_format(JVdate)');
        echo $this->datatables->generate();
        //&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_supplier_invoice($1,\'Supplier Invoice\');"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
    }
    
 /**SMSD */
    public function fetch_gl_codes()
    {
        $companyID = $this->input->post('companyID');
        
        $data_arr = dropdown_all_revenue_gl_JV($companyID);

        echo json_encode($data_arr);
    }
/**SMSD */
    function fetch_segment_codes(){
        $companyID = $this->input->post('companyID');
        
        $data_arr = fetch_segment($companyID);

        echo json_encode($data_arr);
    }
/**SMSD */
    public function fetch_gl_codesedit()
    {
        $companyID = $this->input->post('companyID');
        
        $data_arr = dropdown_all_revenue_gl_JV($companyID);

        echo json_encode($data_arr);
    }
/**SMSD */
    function fetch_segment_codesedit(){
        $companyID = $this->input->post('companyID');
        
        $data_arr = fetch_segment($companyID);

        echo json_encode($data_arr);
    }
    
    function fetch_journal_entry_approval()
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
            $this->datatables->select('srp_erp_jvmaster.JVMasterAutoId as JVMasterAutoId,JVcode,JVType,JVNarration,DATE_FORMAT(JVdate,\'' . $convertFormat . '\') AS JVdate ,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID, approvalLevelID,srp_erp_jvmaster.companyID,IFNULL(debamt.debitAmount,0) as total_value,ROUND(IFNULL(debamt.debitAmount,0), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency');
            $this->datatables->from('srp_erp_jvmaster');
            $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId) debamt', '(debamt.JVMasterAutoId = srp_erp_jvmaster.JVMasterAutoId)', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_jvmaster.JVMasterAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_jvmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_jvmaster.currentLevelNo');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_jvmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'JV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'JV');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('JVcode', '$1', 'approval_change_modal(JVcode,JVMasterAutoId,documentApprovedID,approvalLevelID,approvedYN,JV,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('detail', '<b>Type : </b> $1 <b> &nbsp;&nbsp;Narration : </b> $2 <b> &nbsp;&nbsp;Date : </b> $3', 'JVType,JVNarration,JVdate');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "JV", JVMasterAutoId)');
            $this->datatables->add_column('edit', '$1', 'jv_approval(JVMasterAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_jvmaster.JVMasterAutoId as JVMasterAutoId,JVcode,JVType,JVNarration,DATE_FORMAT(JVdate,\'' . $convertFormat . '\') AS JVdate ,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID, approvalLevelID,srp_erp_jvmaster.companyID,IFNULL(debamt.debitAmount,0) as total_value,ROUND(IFNULL(debamt.debitAmount,0), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency');
            $this->datatables->from('srp_erp_jvmaster');
            $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId) debamt', '(debamt.JVMasterAutoId = srp_erp_jvmaster.JVMasterAutoId)', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_jvmaster.JVMasterAutoId');

            $this->datatables->where('srp_erp_jvmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'JV');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID',$currentuserid);
            $this->datatables->group_by('srp_erp_jvmaster.JVMasterAutoId');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');



            $this->datatables->add_column('JVcode', '$1', 'approval_change_modal(JVcode,JVMasterAutoId,documentApprovedID,approvalLevelID,approvedYN,JV,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('detail', '<b>Type : </b> $1 <b> &nbsp;&nbsp;Narration : </b> $2 <b> &nbsp;&nbsp;Date : </b> $3', 'JVType,JVNarration,JVdate');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "JV", JVMasterAutoId)');
            $this->datatables->add_column('edit', '$1', 'jv_approval(JVMasterAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_journal_entry_header()
    {
        $date_format_policy = date_format_policy();
        $Jdte = $this->input->post('JVdate');
        $JVdate = input_format_date($Jdte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('JVType', 'JV Type', 'trim|required');
        $this->form_validation->set_rules('JVdate', 'JV Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        //$this->form_validation->set_rules('JVNarration', 'Narration', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($JVdate >= $financePeriod['dateFrom'] && $JVdate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Journal_entry_model->save_journal_entry_header());
                } else {
                    $this->session->set_flashdata('e', 'Journal Entry Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Journal_entry_model->save_journal_entry_header());
            }
        }
    }

    function save_gl_detail()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        $companyType = $this->session->userdata("companyType");     /**SMSD */
        if($companyType == 2){                                      /**SMSD */
            $company_IDs = $this->input->post('companyID');         /**SMSD */
        }                                                           /**SMSD */
        $segment_gls = $this->input->post('segment_gl');
        $creditAmount = $this->input->post('creditAmount');
        $debitAmount = $this->input->post('debitAmount');
        $descriptions = $this->input->post('description');
        $cat_mandetory = Project_Subcategory_is_exist();

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
            if($companyType == 2){ /**SMSD */                                                                     /*new*/
                $this->form_validation->set_rules("companyID[{$key}]", 'Company', 'required|trim');     /*new*/
            }   /**SMSD */                                                                                        /*new*/

            if($companyType == 2){/**SMSD */
                foreach($company_IDs as $key => $companyID){/**SMSD */
                    $gl = fetch_gl_account_desc($gl_codes[$key],$company_IDs[$key]);/**SMSD */
                }
            }else{/**SMSD */
                $gl = fetch_gl_account_desc($gl_codes[$key]);
            }/**SMSD */

          //  $gl = fetch_gl_account_desc($gl_codes[$key]);
            if ($gl['masterCategory'] == 'PL') {
                $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            }


            $this->form_validation->set_rules("creditAmount[{$key}]", 'Credit Amount', 'trim|required');
            $this->form_validation->set_rules("debitAmount[{$key}]", 'Debit Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Narration', 'trim|required');
            if($projectExist == 1){
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }

            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Journal_entry_model->save_gl_detail());
        }
    }

    function update_gl_detail()
    {
        $projectExist = project_is_exist();

        $companyType = $this->session->userdata("companyType");         /**SMSD */
        if($companyType == 2){                                          /**SMSD */
            $company_IDs = trim($this->input->post('edit_companyID') ?? '');        /**SMSD */
        } /**SMSD */
        
        $gl_codes = trim($this->input->post('edit_gl_code') ?? '');
        /*$gl_types = $this->input->post('gl_type');*/
        $segment_gls = $this->input->post('edit_segment_gl');
        $creditAmount = $this->input->post('editcreditAmount');
        $debitAmount = $this->input->post('editdebitAmount');
        $descriptions = $this->input->post('editdescription');
        $cat_mandetory = Project_Subcategory_is_exist();

        if($companyType == 2){ /**SMSD */                                                                     
            $this->form_validation->set_rules("edit_companyID", 'Company', 'required|trim');        /**SMSD */
        }        /**SMSD */                                                                                  

        $this->form_validation->set_rules("edit_gl_code", 'GL Code', 'required|trim');

        if($companyType == 2){/**SMSD */
           // foreach($company_IDs as $key => $companyID){
                $gl = fetch_gl_account_desc($gl_codes,$company_IDs);/**SMSD */
            //}
        }else{/**SMSD */
            $gl = fetch_gl_account_desc($gl_codes);
        }/**SMSD */

       // $gl = fetch_gl_account_desc($gl_codes);
        if ($gl['masterCategory'] == 'PL') {
            $this->form_validation->set_rules("edit_segment_gl", 'Segment', 'trim|required');
        }
        $this->form_validation->set_rules("editcreditAmount", 'Credit Amount', 'trim|required');
        $this->form_validation->set_rules("editdebitAmount", 'Debit Amount', 'trim|required');
        $this->form_validation->set_rules("editdescription", 'Narration', 'trim|required');
        if($projectExist == 1){
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Journal_entry_model->update_gl_detail());
        }
    }

    function save_jv_approval()
    {
        $system_code = trim($this->input->post('JVMasterAutoId') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if($status==1){
            $approvedYN=checkApproved($system_code,'JV',$level_id);
            if($approvedYN){
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('JVMasterAutoId');
                $this->db->where('JVMasterAutoId', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_jvmaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('JVMasterAutoId', 'JV Master Auto Id', 'trim|required');
                    $this->form_validation->set_rules('Level', 'Level', 'trim|required');
                    if($this->input->post('status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Journal_entry_model->save_jv_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('JVMasterAutoId');
            $this->db->where('JVMasterAutoId', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_jvmaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'JV',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('JVMasterAutoId', 'JV Master Auto Id', 'trim|required');
                    $this->form_validation->set_rules('Level', 'Level', 'trim|required');
                    if($this->input->post('status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Journal_entry_model->save_jv_approval());
                    }
                }
            }
        }
    }

    function load_journal_entry_header()
    {
        echo json_encode($this->Journal_entry_model->load_journal_entry_header());
    }

    function fetch_journal_entry_detail()
    {
        echo json_encode($this->Journal_entry_model->fetch_journal_entry_detail());
    }

    
    function get_jv_provision_employees()
    {
        echo json_encode($this->Journal_entry_model->get_jv_provision_employees());
    }


    function delete_Journal_entry_detail()
    {
        echo json_encode($this->Journal_entry_model->delete_Journal_entry_detail());
    }

    function delete_Journal_entry()
    {
        echo json_encode($this->Journal_entry_model->delete_Journal_entry());
    }

    function journal_entry_confirmation()
    {
        echo json_encode($this->Journal_entry_model->journal_entry_confirmation());
    }

    function journal_entry_conformation()
    {
        $JVMasterAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('JVMasterAutoId') ?? '');
        $data['extra'] = $this->Journal_entry_model->fetch_Journal_entry_template_data($JVMasterAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature']=$this->Journal_entry_model->fetch_signaturelevel_journal_voucher();
        } else {
            $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/finance/journal_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function referback_journal_entry()
    {

        $JVMasterAutoId = $this->input->post('JVMasterAutoId');

        $this->db->select('approvedYN,JVcode');
        $this->db->where('JVMasterAutoId', trim($JVMasterAutoId));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_jvmaster');
        $approved_journal_voucher = $this->db->get()->row_array();
        if (!empty($approved_journal_voucher)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_journal_voucher['JVcode']));
        }else
        {
            $this->load->library('Approvals');
            $this->load->library('CostAllocation');
            $status = $this->approvals->approve_delete($JVMasterAutoId, 'JV');
            $costAllocation = $this->costallocation->deleteDocumentCostAllocation('JV', $JVMasterAutoId);
            if ($status == 1 && true === $costAllocation) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }



    }

    function load_jv_detail()
    {
        $JVDetailAutoID = $this->input->post('JVDetailAutoID');
        $detail = $this->db->query("select * from srp_erp_jvdetail WHERE JVDetailAutoID={$JVDetailAutoID}")->row_array();
        echo exit(json_encode($detail));

    }

    function re_open_journal_entry()
    {
        echo json_encode($this->Journal_entry_model->re_open_journal_entry());
    }

    function getrecurringDataTable()
    {
        $date_format_policy = date_format_policy();
        $companyFinanceYear = trim($this->input->post('companyFinanceYear') ?? '');
        $Jdate = trim($this->input->post('JVdate') ?? '');
        $currencyID = trim($this->input->post('currencyID') ?? '');

        $year = explode(' - ', trim($companyFinanceYear));
        $FYBegin = input_format_date($year[0],$date_format_policy);
        $FYEnd = input_format_date($year[1],$date_format_policy);
        $JVdate = input_format_date($Jdate,$date_format_policy);


        $companyID = current_companyID();

        $where ="srp_erp_recurringjvmaster.companyID = ".$companyID ." ";
        // $str_lastOCGrade = '(SELECT ocGrade FROM srp_erp_sso_epfreportdetails WHERE empID = EIdNo AND companyID=' . $companyID . ' ORDER BY id DESC LIMIT 1)';
        $this->datatables->select('srp_erp_recurringjvmaster.RJVMasterAutoId as RJVMasterAutoId,RJVcode,RJVStartDate,RJVEndDate,RJVNarration');
        $this->datatables->from('srp_erp_recurringjvmaster');
        //$this->datatables->join('srp_erp_recurringjvdetail', 'srp_erp_recurringjvmaster.RJVMasterAutoId = srp_erp_recurringjvdetail.RJVMasterAutoId');
        $this->datatables->add_column('addBtn', '$1', 'addRecurringBtn(RJVMasterAutoId)');
        $this->datatables->where('srp_erp_recurringjvmaster.transactionCurrencyID', $currencyID);
        $this->datatables->where('srp_erp_recurringjvmaster.approvedYN', 1);
        $this->datatables->where(' \''.$JVdate.'\' BETWEEN srp_erp_recurringjvmaster.RJVStartDate AND srp_erp_recurringjvmaster.RJVEndDate ');
        $this->datatables->where($where);
        $this->datatables->where('srp_erp_recurringjvmaster.RJVMasterAutoId NOT IN( SELECT IFNULL(detailtb.recurringjvMasterAutoId,0) FROM srp_erp_jvdetail detailtb
 INNER JOIN srp_erp_jvmaster mastertb ON detailtb.JVMasterAutoId = mastertb.JVMasterAutoId
WHERE
    mastertb.`companyID` = '.$companyID.' AND FYPeriodDateFrom ="'.$FYBegin.'" AND FYPeriodDateTo = "'.$FYEnd.'"  )');

        echo $this->datatables->generate();
    }

    function get_recurringjv_details(){
        echo json_encode($this->Journal_entry_model->get_recurringjv_details());
    }

    function add_recarring_details()
    {
        echo json_encode($this->Journal_entry_model->add_recarring_details());
    }

    function delete_Journal_entry_recurring_detail()
    {
        echo json_encode($this->Journal_entry_model->delete_Journal_entry_recurring_detail());
    }

    function fetch_attachmentsJV()
    {
        $this->db->select('recurringjvMasterAutoId');
        $this->db->where('JVMasterAutoId', $this->input->post('documentSystemCode'));
        $this->db->from('srp_erp_jvdetail');
        $recurringjvMasterAutoId= $this->db->get()->row_array();
        if($recurringjvMasterAutoId){
            $this->db->select('documentSystemCode');
            $this->db->where('documentSystemCode', $recurringjvMasterAutoId['recurringjvMasterAutoId']);
            $this->db->where('documentID', 'RJV');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_documentattachments');
            $RJVAttach= $this->db->get()->row_array();
            if($RJVAttach){
                $where ="companyID = ".$this->common_data['company_data']['company_id'] ." OR documentSystemCode = ".$recurringjvMasterAutoId['recurringjvMasterAutoId']." AND documentID = 'RJV' ";
            }else{
                $where ="companyID = ".$this->common_data['company_data']['company_id'] ."";
            }
            $this->db->select('*');
            $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
            $this->db->where('documentID', $this->input->post('documentID'));
            $this->db->where($where);
            $this->db->from('srp_erp_documentattachments');
            $data= $this->db->get()->result_array();
            echo json_encode($data);
        }else{
            $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
            $this->db->where('documentID', $this->input->post('documentID'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $data = $this->db->get('srp_erp_documentattachments')->result_array();
            echo json_encode($data);
        }
    }


    function do_upload_jv($description = true)
    {
        //$this->load->model('upload_modal');
        if ($description) {
            $this->form_validation->set_rules('attachmentDescriptionJV', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('documentSystemCodeJV', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('document_nameJV', 'document_name', 'trim|required');
            $this->form_validation->set_rules('documentIDJV', 'documentID', 'trim|required');
        }
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status'=>0,'type'=>'e','message'=>validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentIDJV') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentIDJV') . '_' . $this->input->post('documentSystemCodeJV') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_fileJV")) {
                echo json_encode(array('status'=>0,'type'=>'w','message'=>'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $data['documentID'] = trim($this->input->post('documentIDJV') ?? '');
                $data['documentSystemCode'] = trim($this->input->post('documentSystemCodeJV') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescriptionJV') ?? '');
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status'=>0,'type'=>'e','message'=>'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status'=>1,'type'=>'s','message'=>'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function get_currency_decimal_places(){
        $this->db->select('DecimalPlaces');
        $this->db->where('currencyID', $this->input->post('CurrencyID'));
        $this->db->from('srp_erp_currencymaster');
        $data= $this->db->get()->row_array();
        echo json_encode($data);
    }

    function fetch_journal_entry_buyback()
    {
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( JVdate >= '" . $datefromconvert . " 00:00:00' AND JVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $companyid=$this->common_data['company_data']['company_id'];
        $searches='';
        /*if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND ((JVcode Like '%$search%' ESCAPE '!') OR ( JVType Like '%$sSearch%' ESCAPE '!')  OR (JVNarration Like '%$sSearch%') OR (JVdate Like '%$sSearch%')) ";
        }*/
        $where = "companyID = " . $companyid . $searches. $date . $status_filter ."";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_jvmaster.JVMasterAutoId as JVMasterAutoId,JVcode,JVdate,confirmedYN,approvedYN,transactionCurrency,transactionCurrencyDecimalPlaces, JVNarration,DATE_FORMAT(JVdate,'$convertFormat') AS JVdate ,JVType,transactionAmount,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID,IFNULL(debamt.debitAmount,0) as total_value,IFNULL(debamt.debitAmount,0) as total_value_search,isSystemGenerated as isSystemGenerated");
        $this->datatables->join('(SELECT SUM(debitAmount) as debitAmount,JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId) debamt', '(debamt.JVMasterAutoId = srp_erp_jvmaster.JVMasterAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_jvmaster');
        $this->datatables->add_column('detail', '<b>Type : </b> $1 <b> &nbsp;&nbsp;Narration : </b> $2', 'JVType,JVNarration,JVdate');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'je_total_value(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"JV",JVMasterAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"JV",JVMasterAutoId)');
        $this->datatables->add_column('action', '$1', 'journal_entry_action_buyback(JVMasterAutoId,confirmedYN,approvedYN,createdUserID,isDeleted,JVType,confirmedByEmpID,isSystemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
        //&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_supplier_invoice($1,\'Supplier Invoice\');"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
    }
    function journal_entry_conformation_buyback()
    {
        $JVMasterAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('JVMasterAutoId') ?? '');
        $data['extra'] = $this->Journal_entry_model->fetch_Journal_entry_template_data($JVMasterAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature']=$this->Journal_entry_model->fetch_signaturelevel_journal_voucher();
        } else {
            $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/finance/journal_entry_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN']);
        }
    }

    function cloneJV(){
        $JVID=$this->input->post('id');
        
        $this->db->Select('*');
        $this->db->where('JVMasterAutoId',$JVID);
        $this->db->from('srp_erp_jvmaster');
        $query = $this->db->get();

        $result = $query->row_array(); 
        
        $data=[
            'documentID'=>$result ['documentID'],
            'JVType'=>$result ['JVType'],
            'JVcode'=>0,
            'JVdate'=>$result ['JVdate'],
            'JVNarration'=>$result ['JVNarration'],
            'isSystemGenerated'=>null,
            'clonedJVID'=>$JVID,
            'referenceNo'=>$result ['referenceNo'],
            'companyFinanceYearID'=>$result ['companyFinanceYearID'],
            'companyFinanceYear'=>$result ['companyFinanceYear'],
            'FYBegin'=>$result ['FYBegin'],
            'FYEnd'=>$result ['FYEnd'],
            'FYPeriodDateFrom'=>$result ['FYPeriodDateFrom'],
            'FYPeriodDateTo'=>$result ['FYPeriodDateTo'],
            'companyFinancePeriodID'=>$result ['companyFinancePeriodID'],
            'recurringjvMasterAutoId'=>$result ['recurringjvMasterAutoId'],
            'recurringMonth'=>$result ['recurringMonth'],
            'recurringYear'=>$result ['recurringYear'],
            'transactionCurrencyID'=>$result ['transactionCurrencyID'],
            'transactionCurrency'=>$result ['transactionCurrency'],
            'transactionExchangeRate'=>$result ['transactionExchangeRate'],
            'transactionAmount'=>$result ['transactionAmount'],
            'transactionCurrencyDecimalPlaces'=>$result ['transactionCurrencyDecimalPlaces'],
            'companyLocalCurrencyID'=>$result ['companyLocalCurrencyID'],
            'companyLocalCurrency'=>$result ['companyLocalCurrency'],
            'companyLocalExchangeRate'=>$result ['companyLocalExchangeRate'],
            'companyLocalAmount'=>$result ['companyLocalAmount'],
            'companyLocalAmount'=>$result ['companyLocalAmount'],
            'companyReportingCurrencyID'=>$result ['companyReportingCurrencyID'],
            'companyReportingCurrency'=>$result ['companyReportingCurrency'],
            'companyReportingExchangeRate'=>$result ['companyReportingExchangeRate'],
            'companyReportingAmount'=>$result ['companyReportingAmount'],
            'companyReportingCurrencyDecimalPlaces'=>$result ['companyReportingCurrencyDecimalPlaces'],
            'confirmedYN'=>0,
            'confirmedByEmpID'=>null,
            'confirmedByName'=>null,
            'confirmedDate'=>null,
            'approvedYN'=>0,
            'approvedDate'=>null,
            'currentLevelNo'=>$result ['currentLevelNo'],
            'approvedbyEmpID'=>null,
            'approvedbyEmpName'=>null,
            'isDeleted'=>$result ['isDeleted'],
            'deletedEmpID'=>$result ['deletedEmpID'],
            'deletedDate'=>$result ['deletedDate'],
            'companyID'=>$result ['companyID'],
            'groupId'=>$result ['groupId'],
            'companyCode'=>$result ['companyCode'],
            'createdUserGroup'=>$this->common_data['user_group'],
            'createdPCID'=>$this->common_data['current_pc'],
            'createdUserID'=>$this->common_data['current_userID'],
            'createdDateTime'=>$this->common_data['current_date'],
            'createdUserName'=>$this->common_data['current_user'],
            'modifiedPCID'=>$this->common_data['current_pc'],
            'modifiedUserID'=>$this->common_data['current_userID'],
            'modifiedDateTime'=>$this->common_data['current_date'],
            'modifiedUserName'=>$this->common_data['current_user'],
            'timestamp'=>date('Y-m-d H:i:s'),
        ];

       $this->db->insert('srp_erp_jvmaster',$data);
       $lastid = $this->db->insert_id();
      
       $this->db->Select('*');
       $this->db->where('JVMasterAutoId',$JVID);
       $this->db->from('srp_erp_jvdetail');
       $query = $this->db->get();

       $JVdetails = $query->result_array(); 
       $responses = [];
       
       foreach ($JVdetails as $JVdetail) {
            $details = [
                'JVMasterAutoId' => $lastid,
                'projectID' => $JVdetail['projectID'],
                'project_categoryID' => $JVdetail['project_categoryID'],
                'project_subCategoryID' => $JVdetail['project_subCategoryID'],
                'projectExchangeRate' => $JVdetail['projectExchangeRate'],
                'recurringjvMasterAutoId' => $JVdetail['recurringjvMasterAutoId'],
                'rjvSystemCode' => $JVdetail['rjvSystemCode'],
                'recurringjvDetailAutoID' => $JVdetail['recurringjvDetailAutoID'],
                'type' => $JVdetail['type'],
                'activityCodeID' => $JVdetail['activityCodeID'],
                'segmentID' => $JVdetail['segmentID'],
                'segmentCode' => $JVdetail['segmentCode'],
                'gl_type' => $JVdetail['gl_type'],
                'GLAutoID' => $JVdetail['GLAutoID'],
                'systemGLCode' => $JVdetail['systemGLCode'],
                'GLCode' => $JVdetail['GLCode'],
                'GLDescription' => $JVdetail['GLDescription'],
                'GLType' => $JVdetail['GLType'],
                'description' => $JVdetail['description'],
                'debitAmount' => $JVdetail['debitAmount'],
                'debitCompanyLocalAmount' => $JVdetail['debitCompanyLocalAmount'],
                'debitCompanyReportingAmount' => $JVdetail['debitCompanyReportingAmount'],
                'creditAmount' => $JVdetail['creditAmount'],
                'creditCompanyLocalAmount' => $JVdetail['creditCompanyLocalAmount'],
                'creditCompanyReportingAmount' => $JVdetail['creditCompanyReportingAmount'],
                'isBank' => $JVdetail['isBank'],
                'bankCurrencyID' => $JVdetail['bankCurrencyID'],
                'bankCurrency' => $JVdetail['bankCurrency'],
                'bankCurrencyExchangeRate' => $JVdetail['bankCurrencyExchangeRate'],
                'bankCurrencyAmount' => $JVdetail['bankCurrencyAmount'],
                'companyID' => $JVdetail['companyID'],
                'companyCode' => $JVdetail['companyCode'],
                'isReversal' => $JVdetail['isReversal'],
            ];
            $insert_result = $this->db->insert('srp_erp_jvdetail', $details);
            if ($insert_result) {
                $responses[] = ['status' => 'success', 'message' => 'Journal Voucher clone is successful'];
            } else {
                $responses[] = ['status' => 'failed', 'message' => 'Journal Voucher clone failed'];
            }
        }
        echo json_encode($responses);
    }
}