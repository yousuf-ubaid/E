<?php
class OverTime extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Over_time_template_model');
        $this->load->helper('employee');
    }

    function fetch_general_ot_master_table()
    {
        $convertFormat = convert_date_format_sql();
        $companyID=$this->common_data['company_data']['company_id'];
        $this->datatables->select('generalOTMasterID,otCode,description,srp_erp_generalotmaster.currencyID,confirmedYN,approvedYN,srp_erp_currencymaster.CurrencyCode as currency,documentDate')
            ->join('srp_erp_currencymaster ', 'srp_erp_generalotmaster.currencyID = srp_erp_currencymaster.currencyID')
            ->where('srp_erp_generalotmaster.companyID', $companyID)
            ->from('srp_erp_generalotmaster');
        //$this->datatables->add_column('edit', '<a onclick="fetchPage(\'system/OverTime/erp_genaral_ot_detail\',$1,\'Over Time\',\'ATS\');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_ot_template($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" ></span></a>', 'generalOTMasterID');
        $this->datatables->add_column('edit', '$1', 'load_attendance_summary_actions(generalOTMasterID,confirmedYN,approvedYN)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ATS",generalOTMasterID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ATS",generalOTMasterID)');
        $this->datatables->edit_column('documentDate', '<span >$1 </span>', 'convert_date_format(documentDate)');
        echo $this->datatables->generate();
    }

    function fetch_over_time_template(){
        $data['detail'] = $this->Over_time_template_model->get_ot_template_details();
        $html = $this->load->view('system/OverTime/ajax/ajax_erp_load_template_category_table', $data, true);
        echo $html;
    }

    function load_over_time_template_categories(){
        $data['companyID'] = current_companyID();
        $html = $this->load->view('system/OverTime/ajax/ajax_erp_load_template_category', $data, true);
        echo $html;
    }

    function save_over_time_template(){
        echo json_encode($this->Over_time_template_model->save_over_time_template());
    }

    function delete_ot_template(){
        $status=$this->db->delete('srp_erp_generalottemplatedetails', array('templatedetailID' => trim($this->input->post('templatedetailID') ?? '')));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function fetch_over_time_templates(){
        $generalOTMasterID=$this->input->post('generalOTMasterID');
        $data['detail'] = $this->Over_time_template_model->get_ot_templates();
        $data['empDetails'] = $this->Over_time_template_model->get_ot_templates_emp_details($generalOTMasterID);
        $data['MasterID'] = $generalOTMasterID;
        $html = $this->load->view('system/OverTime/ajax/ajax_erp_load_ot_templates', $data, true);
        echo $html;
    }

    function save_ot_master(){
        $this->form_validation->set_rules('documentDate', 'Date', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Over_time_template_model->save_ot_master());
        }
    }

    function getEmployeesDataTableShift()
    {

        $segmentID = trim($this->input->post('segmentID') ?? '');
        $segemnt_filter = '';
        //$whereIN = '';
        if (!empty($segmentID)) {
            $segmentID = array($this->input->post('segmentID'));
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segemnt_filter = " and segmentID IN " . $whereIN;
        }

        $companyID = current_companyID();
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $this->db->select('documentDate,currencyID');
        $this->db->where('generalOTMasterID', trim($generalOTMasterID));
        $this->db->from('srp_erp_generalotmaster');
        $date = $this->db->get()->row_array();
        $documentDate=$date['documentDate'];
        $currency=$date['currencyID'];
        $con = "IFNULL(Ename2, '')";
        $entryDateLast = date('Y-m-t', strtotime($documentDate));

        $where ="srp_employeesdetails.Erp_companyID = ".$companyID .$segemnt_filter." ";
       // $str_lastOCGrade = '(SELECT ocGrade FROM srp_erp_sso_epfreportdetails WHERE empID = EIdNo AND companyID=' . $companyID . ' ORDER BY id DESC LIMIT 1)';
        $this->datatables->select('EIdNo, ECode, CONCAT(' . $con . ') AS empName, DesDescription');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join("(SELECT EIdNo AS empID, dischargedDate, IF( isDischarged != 1, 0, 
                                  IF('{$entryDateLast}' <= LAST_DAY(lastWorkingDate) AND finalSettlementDoneYN=0, 0, 1)	)AS isDischargedStatus
                                  FROM srp_employeesdetails WHERE Erp_companyID={$companyID}) AS dischargedStatusTB",
            'dischargedStatusTB.empID = srp_employeesdetails.EIdNo');
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        //$this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.payCurrencyID', $currency);
        $this->datatables->where($where);
        $this->datatables->where('dischargedStatusTB.isDischargedStatus', 0);
        $this->datatables->where('srp_employeesdetails.eidNo NOT IN( SELECT detailtb.empID FROM srp_erp_generalotdetail detailtb
 INNER JOIN srp_erp_generalotmaster mastertb ON detailtb.generalOTMasterID = mastertb.generalOTMasterID
WHERE
    detailtb.`companyID` = '.$companyID.' AND  month(mastertb.documentDate)=month("'.$documentDate.'") AND  YEAR(mastertb.documentDate)=YEAR("'.$documentDate.'"))');
        /*$this->datatables->where('srp_employeesdetails.eidNo IN( SELECT employeeNo FROM srp_erp_pay_salarydeclartion
WHERE companyID='.$companyID.'
GROUP BY employeeNo )');*/
        $this->datatables->where('srp_employeesdetails.isPayrollEmployee', 1);
        $this->datatables->where('srp_employeesdetails.isSystemAdmin !=', 1);

        echo $this->datatables->generate();
    }

    function add_employees_to_ot()
    {
        echo json_encode($this->Over_time_template_model->add_employees_to_ot());
    }

    function save_general_ot_template_frm(){
        echo json_encode($this->Over_time_template_model->save_general_ot_template_frm());
    }

    function general_ot_template_sort_frm(){
        echo json_encode($this->Over_time_template_model->save_general_ot_template_sort_frm());
    }

    function comfirm_general_ot_template(){
        echo json_encode($this->Over_time_template_model->comfirm_general_ot_template());
    }

    function referback_general_ot(){
        echo json_encode($this->Over_time_template_model->referback_general_ot());
    }

    function fetch_over_time_template_view(){
        $generalOTMasterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('generalOTMasterID') ?? '');
        $data['master'] = $this->Over_time_template_model->fetch_template_master($generalOTMasterID);
        $data['detail'] = $this->Over_time_template_model->get_ot_templates();
        $data['empDetails'] = $this->Over_time_template_model->get_ot_templates_emp_details($generalOTMasterID);
        $data['MasterID'] = $generalOTMasterID;
        $html = $this->load->view('system/OverTime/ajax/ajax_erp_load_ot_template_view', $data, true);
        echo $html;
    }

    function load_general_ot_print(){
        $generalOTMasterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('generalOTMasterID') ?? '');
        $data['master'] = $this->Over_time_template_model->fetch_template_master($generalOTMasterID);
        $data['detail'] = $this->Over_time_template_model->get_ot_templates();
        $data['empDetails'] = $this->Over_time_template_model->get_ot_templates_emp_details($generalOTMasterID);
        $data['MasterID'] = $generalOTMasterID;
        $html = $this->load->view('system/OverTime/ajax/ajax_erp_load_ot_template_print', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', $data['master']['approvedYN']);
    }


    function fetch_general_ot_approval()
    {
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
         $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_generalotmaster.generalOTMasterID as generalOTMasterID,srp_erp_generalotmaster.companyCode,srp_erp_generalotmaster.description,otCode,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_generalotmaster.documentDate,\'' . $convertFormat . '\') AS documentDate,currencyID', false);
            $this->datatables->from('srp_erp_generalotmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_generalotmaster.generalOTMasterID AND srp_erp_documentapproved.approvalLevelID = srp_erp_generalotmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_generalotmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'ATS');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'ATS');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_generalotmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
            //$this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"ATS",generalOTMasterID)');
            $this->datatables->add_column('edit', '$1', 'got_action_approval(generalOTMasterID,approvalLevelID,approvedYN,documentApprovedID,ATS)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_generalotmaster.generalOTMasterID as generalOTMasterID,srp_erp_generalotmaster.companyCode,srp_erp_generalotmaster.description,otCode,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_generalotmaster.documentDate,\'' . $convertFormat . '\') AS documentDate,currencyID', false);
            $this->datatables->from('srp_erp_generalotmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_generalotmaster.generalOTMasterID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'ATS');
            $this->datatables->where('srp_erp_generalotmaster.companyID', $companyID);
             $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_generalotmaster.generalOTMasterID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            //$this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"ATS",generalOTMasterID)');
            $this->datatables->add_column('edit', '$1', 'got_action_approval(generalOTMasterID,approvalLevelID,approvedYN,documentApprovedID,ATS)');
            echo $this->datatables->generate();
        }

    }

    function save_general_ot_approval()
    {
        $system_code = trim($this->input->post('generalOTMasterID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('got_status') ?? '');
        if($status==1){
            $approvedYN=checkApproved($system_code,'ATS',$level_id);
            if($approvedYN){
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('generalOTMasterID');
                $this->db->where('generalOTMasterID', trim($system_code));
                $this->db->where('approvedYN', 2);
                $this->db->where('confirmedYN !=', 1);
                $this->db->from('srp_erp_generalotmaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('got_status', 'Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('generalOTMasterID', 'General OT Master ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Over_time_template_model->save_general_ot_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('generalOTMasterID');
            $this->db->where('generalOTMasterID', trim($system_code));
            $this->db->where('approvedYN', 2);
            $this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_generalotmaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'ATS',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('got_status', 'Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('generalOTMasterID', 'General OT MasterID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Over_time_template_model->save_general_ot_approval());
                    }
                }
            }
        }
    }

    function delete_general_ot_template(){
        echo json_encode($this->Over_time_template_model->delete_general_ot_template());
    }

    function delete_general_ot_template_employees(){
        echo json_encode($this->Over_time_template_model->delete_general_ot_template_employees());
    }






}
