<?php

class Budget_transfer extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('budget_helper');
        $this->load->model('Budget_transfer_model');
    }

    function save_budget_transfer_header()
    {
        $this->form_validation->set_rules('financeYearID', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('comments', 'Description', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata(array('e', validation_errors()));
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $trfrDate = $this->input->post('documentDate');
            $documentDate = input_format_date($trfrDate, $date_format_policy);
            $this->load->library('sequence');
            $data['documentID'] = 'BDT';
            $data['companyID'] = current_companyID();
            $data['financeYearID'] = $this->input->post('financeYearID');
            $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
            $data['transactionExchangeRate'] = 1;
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $rep_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $rep_currency['conversion'];
            $data['comments'] = $this->input->post('comments');
            $data['documentDate'] = $documentDate;
            $data['documentSystemCode'] = $this->sequence->sequence_generator($data['documentID']);
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPcID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $insert = $this->db->insert('srp_erp_budgettransfer', $data);
            if($insert) {
                echo json_encode(array('s', 'Records Inserted Successfully'));
            }else{
                echo json_encode(array('e', 'Failed. Please Contact IT Team'));
            }

        }
    }

    function fetch_budget_entry(){
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("budgetTransferAutoID,documentSystemCode,documentDate AS createdDate ,srp_erp_budgettransfer.comments as comments,confirmedYN,approvedYN,CONCAT(srp_erp_companyfinanceyear.beginingDate,' - ',srp_erp_companyfinanceyear.endingDate) AS financeYear,srp_erp_currencymaster.CurrencyCode as CurrencyCode");
        $this->datatables->from('srp_erp_budgettransfer');
        $this->datatables->join('srp_erp_companyfinanceyear ', 'srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID');
        $this->datatables->join('srp_erp_currencymaster ', 'srp_erp_currencymaster.currencyID = srp_erp_budgettransfer.transactionCurrencyID');
        $this->datatables->where('srp_erp_budgettransfer.companyID', current_companyID());
        $this->datatables->add_column('edit', ' $1 ', 'load_budget_transfer_action(budgetTransferAutoID,confirmedYN,approvedYN)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BDT",budgetTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BDT",budgetTransferAutoID)');
        $this->datatables->edit_column('createdDate', '<span >$1 </span>', 'convert_date_format(createdDate)');
        echo $this->datatables->generate();
    }

    function fetch_budget_transfer_detail(){
        $convertFormat = convert_date_format_sql();
        $budgetTransferAutoID=$this->input->post('budgetTransferAutoID');
        $company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
        $this->datatables->select("budgetTransferDetailAutoID,budgetTransferAutoID,CONCAT(fsg.segmentCode,' - ',fsg.description) AS fsegment,CONCAT(tsg.segmentCode,' - ',tsg.description) AS tsegment,CONCAT(fgl.systemAccountCode,' - ',fgl.GLDescription) AS fGLC,CONCAT(tgl.systemAccountCode,' - ',tgl.GLDescription) AS tGLC,transferAmount");
        $this->datatables->from('srp_erp_budgettransferdetail');
        $this->datatables->join('srp_erp_segment fsg ', 'fsg.segmentID = srp_erp_budgettransferdetail.fromSegmentID');
        $this->datatables->join('srp_erp_segment tsg ', 'tsg.segmentID = srp_erp_budgettransferdetail.toSegmentID');

        $this->datatables->join('srp_erp_chartofaccounts fgl ', 'fgl.GLAutoID = srp_erp_budgettransferdetail.FromGLAutoID');
        $this->datatables->join('srp_erp_chartofaccounts tgl ', 'tgl.GLAutoID = srp_erp_budgettransferdetail.toGLAutoID');

        $this->datatables->where('srp_erp_budgettransferdetail.budgetTransferAutoID', $budgetTransferAutoID);
        $this->datatables->edit_column('total_value', '<div class="pull-right"> $1 </div>', 'format_number(transferAmount,'.$company_reporting_DecimalPlaces.')');
        $this->datatables->add_column('edit', ' $1 ', 'load_budget_transfer_detail_action(budgetTransferDetailAutoID)');
        echo $this->datatables->generate();
    }

    function get_budget_amount(){
    $FromGLAutoID=$this->input->post('FromGLAutoID');
    $fromSegmentID=$this->input->post('fromSegmentID');
    $budgetTransferAutoID=$this->input->post('budgetTransferAutoID');
    $companyID=current_companyID();
        if(!empty($FromGLAutoID) && !empty($fromSegmentID)){

            $this->db->select('financeYearID');
            $this->db->where('budgetTransferAutoID', $budgetTransferAutoID);
            $BTD_master = $this->db->get('srp_erp_budgettransfer')->row_array();
            $financeYearID=$BTD_master['financeYearID'];
            //$stock = $this->db->query('SELECT SUM(companyReportingAmount) as budgetAmt FROM `srp_erp_budgetdetail` where segmentID="' . $fromSegmentID . '" AND GLAutoID="' . $FromGLAutoID . '" AND masterGLAutoID="' . $financeYearID . '" ')->row_array();
            $notappr = $this->db->query('SELECT
	srp_erp_budgettransfer.documentSystemCode
FROM
	srp_erp_budgettransferdetail
LEFT JOIN srp_erp_budgettransfer ON srp_erp_budgettransfer.budgetTransferAutoID = srp_erp_budgettransferdetail.budgetTransferAutoID
WHERE
srp_erp_budgettransfer.budgetTransferAutoID!="' . $budgetTransferAutoID . '"
AND approvedYN !=1
	AND FromGLAutoID = "' . $FromGLAutoID . '"
AND fromSegmentID = "' . $fromSegmentID . '"')->row_array();
            if(!empty($notappr)){
                $gldesc=fetch_gl_account_desc($FromGLAutoID);
                $segdesc=$this->Budget_transfer_model->get_segment_details($fromSegmentID);
                $doc=$notappr['documentSystemCode'];
                echo json_encode(array('w', 'GL Code - '.$gldesc['systemAccountCode'].' AND Segment - '.$segdesc['description'].' has been pulled in following un approved document '.$doc ));
                exit;
            }

            $stock = $this->db->query('SELECT
	SUM(IFNULL(srp_erp_budgetdetail.companyReportingAmount, 0)) AS amount,srp_erp_budgetmaster.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces
FROM
	`srp_erp_budgetdetail`
LEFT JOIN srp_erp_budgetmaster ON srp_erp_budgetmaster.budgetAutoID =  srp_erp_budgetdetail.budgetAutoID
WHERE
srp_erp_budgetdetail.companyID= "' . $companyID . '"
AND GLAutoID = "' . $FromGLAutoID . '"
AND srp_erp_budgetdetail.segmentID = "' . $fromSegmentID . '"
AND companyFinanceYearID = "' . $financeYearID . '"
AND approvedYN = 1')->row_array();

            $alreadyexist = $this->db->query('SELECT
	SUM(transferAmount) as transferAmount
FROM
	srp_erp_budgettransferdetail
WHERE
 FromGLAutoID = "' . $FromGLAutoID . '"
AND fromSegmentID = "' . $fromSegmentID . '"
AND budgetTransferAutoID = "' . $budgetTransferAutoID . '"
')->row_array();

            $financeDetails = $this->db->query('SELECT beginingDate,endingDate FROM srp_erp_companyfinanceyear WHERE   companyFinanceYearID="' . $financeYearID . '"  ')->row_array();
            $beginingDate=$financeDetails['beginingDate'];
            $endingDate=$financeDetails['endingDate'];
            //get consumption amount
            $cousumtnamnt = $this->db->query('SELECT SUM(companyReportingAmount) AS rptamnt FROM srp_erp_generalledger WHERE   companyID="' . $companyID . '" AND GLAutoID="' . $FromGLAutoID . '" AND  segmentID="' . $fromSegmentID . '" AND documentDate BETWEEN "' . $beginingDate . '" AND "' . $endingDate . '" ')->row_array();
            $consuptndRptAmnt=0;
            if(!empty($cousumtnamnt)){
                $consuptndRptAmnt=$cousumtnamnt['rptamnt'];
            }
            $stockamount=0;
            if(!empty($alreadyexist)){
                $transframnt=$alreadyexist['transferAmount'];
                $stkamnt=$stock['amount']*-1;
                $stockamount=$stkamnt-$transframnt;
            }else{
                $stockamount=$stock['amount']*-1;
            }
            //
            echo json_encode(array('s','success', round($stockamount,$stock['companyReportingCurrencyDecimalPlaces']),round($consuptndRptAmnt,$stock['companyReportingCurrencyDecimalPlaces'])));
        }else{
            echo json_encode(array('s','success',0,0));
        }
    }

    function save_budget_transfer_detail(){
        $this->form_validation->set_rules('FromGLAutoID', 'From GL Code', 'trim|required');
        $this->form_validation->set_rules('fromSegmentID', 'From Segment', 'trim|required');
        $this->form_validation->set_rules('budgetAmount', 'Budget Amount', 'trim|required');
        $this->form_validation->set_rules('toGLAutoID', 'To GL Code', 'trim|required');
        $this->form_validation->set_rules('toSegmentID', 'To Segment', 'trim|required');
        $this->form_validation->set_rules('adjustmentAmount', 'Transfer Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $adjustmentAmount=$this->input->post('adjustmentAmount');
            $budgetAmount=$this->input->post('budgetAmount');
            if($adjustmentAmount>$budgetAmount){
                echo json_encode(array('w', 'Transfer Amount canot be greater than Budget amount'));
            }else{
                echo json_encode($this->Budget_transfer_model->save_budget_transfer_detail());
            }
        }
    }

    function confirm_budget_transfer(){
        echo json_encode($this->Budget_transfer_model->confirm_budget_transfer());
    }

    function referback_budjet_transfer(){
        $budgetTransferAutoID = $this->input->post('budgetTransferAutoID');

        $this->db->select('approvedYN,documentSystemCode');
        $this->db->where('budgetTransferAutoID', trim($budgetTransferAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_budgettransfer');
        $approved_bdt = $this->db->get()->row_array();
        if (!empty($approved_bdt)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_bdt['documentSystemCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($budgetTransferAutoID, 'BDT');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }

        }
    }

    function load_budget_transfer_view(){
        $budgetTransferAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('budgetTransferAutoID') ?? '');
        $data['extra'] = $this->Budget_transfer_model->fetch_template_data($budgetTransferAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/finance/erp_budget_transfer_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function delete_transfer_detail(){
        $this->db->where('budgetTransferDetailAutoID', $this->input->post('budgetTransferDetailAutoID'));
        $result=$this->db->delete('srp_erp_budgettransferdetail');
        if($result){
            echo json_encode(array('s','Deleted Successfully'));
        }else{
            echo json_encode(array('e','Deletion failed'));
        }
    }

    function fetch_budget_transfer_approval(){
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuesrid = current_userID();
        $convertFormat = convert_date_format_sql();
        if($approvedYN == 0)
        {
            $this->datatables->select("budgetTransferAutoID,srp_erp_budgettransfer.documentSystemCode as documentSystemCode,DATE_FORMAT(srp_erp_budgettransfer.documentDate,'$convertFormat') AS createdDate,srp_erp_budgettransfer.comments as comments,srp_erp_budgettransfer.confirmedYN as confirmedYN,srp_erp_budgettransfer.approvedYN as approvedYN,CONCAT(srp_erp_companyfinanceyear.beginingDate,' - ',srp_erp_companyfinanceyear.endingDate) AS financeYear,srp_erp_budgettransfer.createdUserID as createdUserID,srp_erp_documentapproved.approvalLevelID as approvalLevelID");
            $this->datatables->from('srp_erp_budgettransfer');
            $this->datatables->join('srp_erp_companyfinanceyear ', 'srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_budgettransfer.budgetTransferAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgettransfer.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_budgettransfer.currentLevelNo');
            $this->datatables->where('srp_erp_budgettransfer.companyID', current_companyID());
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BDT');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'BDT');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BDT", budgetTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'load_BDT_approval_action(budgetTransferAutoID,confirmedYN,approvedYN,createdUserID,approvalLevelID)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select("budgetTransferAutoID,srp_erp_budgettransfer.documentSystemCode as documentSystemCode,DATE_FORMAT(srp_erp_budgettransfer.documentDate,'$convertFormat') AS createdDate,srp_erp_budgettransfer.comments as comments,srp_erp_budgettransfer.confirmedYN as confirmedYN,srp_erp_budgettransfer.approvedYN as approvedYN,CONCAT(srp_erp_companyfinanceyear.beginingDate,' - ',srp_erp_companyfinanceyear.endingDate) AS financeYear,srp_erp_budgettransfer.createdUserID as createdUserID,srp_erp_documentapproved.approvalLevelID as approvalLevelID");
            $this->datatables->from('srp_erp_budgettransfer');
            $this->datatables->join('srp_erp_companyfinanceyear ', 'srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_budgettransfer.budgetTransferAutoID');

            $this->datatables->where('srp_erp_budgettransfer.companyID', current_companyID());
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BDT');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuesrid);

            $this->datatables->group_by('srp_erp_budgettransfer.budgetTransferAutoID', $currentuesrid);
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BDT", budgetTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'load_BDT_approval_action(budgetTransferAutoID,confirmedYN,approvedYN,createdUserID,approvalLevelID)');
            echo $this->datatables->generate();
        }


    }

    function save_budget_transfer_approval()
    {
        $system_code = trim($this->input->post('budgetTransferAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'BDT', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('budgetTransferAutoID');
                $this->db->where('budgetTransferAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_budgettransfer');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Budget_transfer_model->save_budget_transfer_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('budgetTransferAutoID');
            $this->db->where('budgetTransferAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_budgettransfer');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'BDT', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Budget_transfer_model->save_budget_transfer_approval());
                    }
                }
            }
        }
    }

    function get_budget_master(){
        $convertFormat = convert_date_format_sql();
        $budgetTransferAutoID=$this->input->post('budgetTransferAutoID');
        $this->db->select('documentSystemCode,srp_erp_budgettransfer.comments as comments,srp_erp_companyfinanceyear.beginingDate as beginingDate,srp_erp_companyfinanceyear.endingDate as endingDate,DATE_FORMAT(srp_erp_budgettransfer.documentDate,\''.$convertFormat.'\') AS createdDate');
        $this->db->where('budgetTransferAutoID', trim($budgetTransferAutoID));
        $this->db->join('srp_erp_companyfinanceyear ', 'srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID');
        $this->db->from('srp_erp_budgettransfer');
        $master = $this->db->get()->row_array();
        echo json_encode($master);
    }

    function edit_transfer_comment(){
        $comment=$this->input->post('comment');
        $comments = str_replace('<br />', PHP_EOL, $comment);
        $budgetTransferAutoID=$this->input->post('budgetTransferAutoID');


        $data = array(
            'comments' => $comments,
        );
        $this->db->where('budgetTransferAutoID', $budgetTransferAutoID);
        $update=$this->db->update('srp_erp_budgettransfer', $data);

        if($update) {
            echo json_encode(array('s', 'Records Updated Successfully'));
        }else{
            echo json_encode(array('e', 'Failed. Please Contact IT Team'));
        }
    }



}
