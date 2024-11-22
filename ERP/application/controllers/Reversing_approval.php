<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reversing_approval extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Reversing_modal');
        $this->load->helper('reversing');
    }
    
    function fetch_reversing_approval()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $documentID = $this->input->post('documentID');
        $ApprovalforItemmasterPol = getPolicyValues('AIM', 'All');
            $bankrecglautoid = '';
       
     
      if($ApprovalforItemmasterPol == 1)
       { 
         $documentID_filter = '\'PO\', \'GRV\', \'SR\', \'ST\', \'BSI\', \'PV\', \'MPV\', \'MI\', \'DN\', \'CINV\', \'RV\', \'MRV\', \'CN\', \'QUT\', \'CNT\', \'SO\', \'SP\', \'SLR\', \'FA\', \'ATS\', \'SPN\', \'SD\', \'JV\',\'DC\',\'BT\',\'BBBC\',\'BBDPN\',\'BBGRN\',\'BBDR\',\'BBPV\',\'BBRV\',\'BBSV\',\'PRQ\',\'MR\',\'DO\',\'HCINV\',\'BRC\',\'INV\',\'SUP\',\'MRN\' ';
       }else 
       { 
         $documentID_filter = '\'PO\', \'GRV\', \'SR\', \'ST\', \'BSI\', \'PV\', \'MPV\', \'MI\', \'DN\', \'CINV\', \'RV\', \'MRV\', \'CN\', \'QUT\', \'CNT\', \'SO\', \'SP\', \'SLR\', \'FA\', \'ATS\', \'SPN\', \'SD\', \'JV\',\'DC\',\'BT\',\'BBBC\',\'BBDPN\',\'BBGRN\',\'BBDR\',\'BBPV\',\'BBRV\',\'BBSV\',\'PRQ\',\'MR\',\'DO\',\'HCINV\',\'BRC\',\'SUP\',\'MRN\' ';
       }




       /* if($documentID == 'BRC')
        {
            $bankrecglautoid = $this->db->query("SELECT bankGLAutoID FROM `srp_erp_bankrecmaster` where companyID = 13 AND bankRecAutoID = 97")->row('bankGLAutoID');
        }*/

        //$withExpenseClaim = false;
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefrom . " 00:00:00' AND documentDate <= '" . $dateto . " 23:59:00')";
        }
        
        
        if (!empty($documentID)) {
           // $documentID = explode(',', $documentID);
           // $withExpenseClaim = (in_array('EC', $documentID));
           // $documentID_filter = "'" . join("' , '", $documentID) . "'";
            //$documentID = explode(',', $documentID);

                $documentID = explode(',', $documentID);
                $documentID_filter = "'". implode("', '", $documentID)."'";
               
           
        }

        $sSearch=$this->input->post('sSearch');
        $searches='';

        $where = "companyID = " . $companyID . $date . $searches . " AND approvedYN =1 AND isReverseApplicableYN =1" ;
        $date_time_frm = '%Y-%m-%d %h:%i:%s';
        $date_filter =  (!empty($datefrom) && !empty($dateto)) ? "AND DATE(expenseClaimDate) BETWEEN '{$datefrom}' AND '{$dateto}'": '';


       /* $this->db->query("INSERT INTO reversal_temp_tb (documentApprovedID,documentCode,approvedComments,documentDate,documentID,empName,documentSystemCode,approvedDate)
                        SELECT documentApprovedID,documentCode,approvedComments,DATE_FORMAT(documentDate, '{$date_time_frm}') AS documentDate,
                        documentID,empName,documentSystemCode,DATE_FORMAT(approvedDate, '{$date_time_frm}') AS approvedDate
                        FROM srp_erp_documentapproved AS t1
                        JOIN (
                             SELECT approvedTB.documentApprovedID AS appID, srp_employeesdetails.Ename2 AS empName
                             FROM srp_erp_documentapproved AS approvedTB
                             JOIN srp_employeesdetails ON approvedTB.approvedEmpID = srp_employeesdetails.EIdNo
                             JOIN (
                                 SELECT MAX(approvalLevelID) AS MaxLevel, srp_erp_documentapproved.documentSystemCode,srp_erp_documentapproved.documentID
                                 FROM srp_erp_documentapproved WHERE companyID = {$companyID}  AND approvedYN = 1 GROUP BY documentSystemCode,documentID
                             ) AS maxLevelTB ON approvedTB.documentSystemCode=maxLevelTB.documentSystemCode AND approvedTB.approvalLevelID=maxLevelTB.MaxLevel 
                             AND approvedTB.documentID=maxLevelTB.documentID
                             WHERE approvedTB.documentID IN({$documentID_filter}) AND {$where}
                             GROUP BY approvedTB.documentID, approvedTB.documentSystemCode                                  
                        ) AS dataTable ON t1.documentApprovedID=dataTable.appID
                        GROUP BY t1.documentApprovedID
                        UNION ALL 
                        SELECT 0, expenseClaimCode, approvalComments, DATE_FORMAT(expenseClaimDate,'{$date_time_frm}') AS documentDate,
                       'EC', appTB.Ename2, expenseClaimMasterAutoID, DATE_FORMAT(approvedDate,'{$date_time_frm}') AS approvedDate
                       FROM srp_erp_expenseclaimmaster AS ex
                       JOIN srp_employeesdetails AS appTB ON ex.approvedByEmpID = appTB.EIdNo
                       WHERE companyID = {$companyID} AND approvedYN = 1 {$date_filter}");*/

        $this->datatables->select("documentApprovedID,documentCode,approvedComments,DATE_FORMAT(documentDate, '%Y-%m-%d %h:%i:%s') as documentDate,documentID,empName,documentSystemCode,DATE_FORMAT(approvedDate, '%Y-%m-%d %h:%i:%s') as approvedDate");
        $this->datatables->from("srp_erp_documentapproved AS t1");
        $this->datatables->join("(SELECT approvedTB.documentApprovedID as appID, srp_employeesdetails.Ename2 AS empName
                                 FROM srp_erp_documentapproved AS approvedTB
                                 JOIN srp_employeesdetails ON approvedTB.approvedEmpID = srp_employeesdetails.EIdNo
                                 JOIN (
                                     SELECT MAX(approvalLevelID) as MaxLevel, srp_erp_documentapproved.documentSystemCode,srp_erp_documentapproved.documentID
                                     FROM srp_erp_documentapproved WHERE companyID = {$companyID}  AND approvedYN = 1 group by documentSystemCode,documentID
                                 ) AS maxLevelTB ON approvedTB.documentSystemCode=maxLevelTB.documentSystemCode AND approvedTB.approvalLevelID=maxLevelTB.MaxLevel AND approvedTB.documentID=maxLevelTB.documentID
                                 WHERE approvedTB.documentID IN({$documentID_filter})
                                 AND {$where}
                                 GROUP BY approvedTB.documentID, approvedTB.documentSystemCode                                  
                                 ) AS dataTable ", "t1.documentApprovedID=dataTable.appID");
        /*if($search) {
            $this->datatables->like('t1.documentCode', $search);
        }*/
        /*$this->datatables->select("documentApprovedID,documentCode,approvedComments, documentDate,documentID,empName,documentSystemCode,approvedDate");
        $this->datatables->from("reversal_temp_tb");*/
        $this->datatables->add_column('employee', '$1', 'Ename2');
        $this->datatables->add_column('action', '$1', 'reversing_approval(documentID,documentApprovedID,documentSystemCode)');
        $this->datatables->group_by('t1.documentApprovedID');
        echo $this->datatables->generate();
    }

    function fetch_reversing_split_approval()
    {
        //only for POS invoices
        $companyID = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $documentID = $this->input->post('documentID');
        $ApprovalforItemmasterPol = getPolicyValues('AIM', 'All');
        $bankrecglautoid = '';
       

        $documentID_filter = '\'POS\' ';

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefrom . " 00:00:00' AND documentDate <= '" . $dateto . " 23:59:00')";
        }
        
        
        if (!empty($documentID)) {
      
            $documentID = explode(',', $documentID);
            $documentID_filter = "'". implode("', '", $documentID)."'";
            
        
        }

        $sSearch=$this->input->post('sSearch');
        $searches='';

        $where = "companyID = " . $companyID . $date . $searches . " AND approvedYN =1 AND isReverseApplicableYN =1" ;
        $date_time_frm = '%Y-%m-%d %h:%i:%s';
        $date_filter =  (!empty($datefrom) && !empty($dateto)) ? "AND DATE(expenseClaimDate) BETWEEN '{$datefrom}' AND '{$dateto}'": '';


        // $this->datatables->select("documentApprovedID,documentCode,approvedComments,DATE_FORMAT(documentDate, '%Y-%m-%d %h:%i:%s') as documentDate,documentID,empName,documentSystemCode,DATE_FORMAT(approvedDate, '%Y-%m-%d %h:%i:%s') as approvedDate");
        $this->datatables->select("pi.*,pi.documentSystemCode as documentSystemCode,ed.id as split_id");
        $this->datatables->from("srp_erp_pos_invoice as pi")->join('srp_erp_reversal_documentsplit as ed','pi.documentSystemCode = ed.document_id','left');
        
        // $this->datatables->join("(SELECT approvedTB.documentApprovedID as appID, srp_employeesdetails.Ename2 AS empName
        //                          FROM srp_erp_documentapproved AS approvedTB
        //                          JOIN srp_employeesdetails ON approvedTB.approvedEmpID = srp_employeesdetails.EIdNo
        //                          JOIN (
        //                              SELECT MAX(approvalLevelID) as MaxLevel, srp_erp_documentapproved.documentSystemCode,srp_erp_documentapproved.documentID
        //                              FROM srp_erp_documentapproved WHERE companyID = {$companyID}  AND approvedYN = 1 group by documentSystemCode,documentID
        //                          ) AS maxLevelTB ON approvedTB.documentSystemCode=maxLevelTB.documentSystemCode AND approvedTB.approvalLevelID=maxLevelTB.MaxLevel AND approvedTB.documentID=maxLevelTB.documentID
        //                          WHERE approvedTB.documentID IN({$documentID_filter})
        //                          AND {$where}
        //                          GROUP BY approvedTB.documentID, approvedTB.documentSystemCode                                  
        //                          ) AS dataTable ", "t1.documentApprovedID=dataTable.appID");
        /*if($search) {
            $this->datatables->like('t1.documentCode', $search);
        }*/
        /*$this->datatables->select("documentApprovedID,documentCode,approvedComments, documentDate,documentID,empName,documentSystemCode,approvedDate");
        $this->datatables->from("reversal_temp_tb");*/
        // $this->datatables->add_column('employee', '$1', 'Ename2');
        $this->datatables->add_column('action', '$1', 'reversing_split_approval(documentSystemCode,split_id)');
        // $this->datatables->group_by('t1.documentApprovedID');
        echo $this->datatables->generate();
    }

    function reversing_approval_document()
    {
        $this->form_validation->set_rules('auto_id', 'auto_id', 'trim|required');
        $this->form_validation->set_rules('comments', 'comments', 'trim|required');
        $this->form_validation->set_rules('document_id', 'document_id', 'trim|required');
        $this->form_validation->set_rules('document_code', 'document_code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {

            $document_code = $this->input->post('document_code');
            $HR_documentCodes = array('SP', 'SD', 'SPN');
            if (in_array($document_code, $HR_documentCodes)) {
                $data = $this->Reversing_modal->reversing_approval_HRDocument();
            } else {
                $data = $this->Reversing_modal->reversing_approval_document();
            }

            echo json_encode($data);
        }
    }

    function load_reversing_split_amount(){
        $this->form_validation->set_rules('documentSystemCode', 'Document System Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {   
            $document_code = $this->input->post('documentSystemCode');

            $ex_document = reversing_fetch_split_record($document_code);
            $data = $this->Reversing_modal->get_adjusted_values($document_code);

            if($ex_document){
                $data['ex_document'] = $ex_document;
                $html = $this->load->view('system/approval_document/ajax/reverse_split_edit',$data);

            }else{

                $html = $this->load->view('system/approval_document/ajax/reverse_split_view',$data);

            }
        
          

            return $html;
        }

    }

    function set_gl_split_amount(){
        echo json_encode($this->Reversing_modal->set_gl_split_amount());
    }
}
