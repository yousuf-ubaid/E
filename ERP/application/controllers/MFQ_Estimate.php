<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MFQ_Estimate extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Estimate_model');
        $this->load->model('MFQ_Job_model');
        $this->load->helper('email');
        $this->load->library('NumberToWords');

    }

    function fetch_estimate()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('((discountedPrice * ((100 + IFNULL(totMargin, 0))/100)) * ((100 - IFNULL(totDiscount, 0))/100)) AS estimateValue, srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces, CONCAT(srp_erp_currencymaster.CurrencyCode, " : ") AS transactionCurrency, est.documentDate as documentDate,est.description as description , cust.CustomerName as CustomerName,est.estimateMasterID as estimateMasterID,est.estimateCode as estimateCode,est.confirmedYN as confirmedYN,est.submissionStatus as submissionStatus,statusColor,statusBackgroundColor,srp_erp_mfq_status.description as statusDescription,estd.dueDate as dueDate,job.estimateMasterID as estimateMasterIDJob,est.approvedYN as approvedYN,job.workProcessID as workProcessID,docApp.docApprovedYN as docApprovedYN,est.isMailSent as isMailSent,IFNULL(segment.segmentCode,\'-\') as depcode, est.isDeleted AS isDeleted', false)
            ->from('srp_erp_mfq_estimatemaster est')->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left')->join('srp_erp_mfq_status', 'srp_erp_mfq_status.statusID = est.submissionStatus', 'left')
            ->join('(SELECT estimateMasterID,workProcessID FROM srp_erp_mfq_job WHERE (isDeleted IS NULL OR isDeleted != 1) GROUP BY estimateMasterID) job', 'job.estimateMasterID = est.estimateMasterID', 'left')
            ->join("(SELECT dueDate,srp_erp_mfq_estimatedetail.ciMasterID,srp_erp_mfq_estimatedetail.estimateMasterID,srp_erp_mfq_estimatedetail.estimateDetailID, SUM(discountedPrice) AS discountedPrice FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_estimatedetail.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID  WHERE srp_erp_mfq_estimatedetail.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_mfq_estimatedetail.estimateMasterID) estd", 'estd.estimateMasterID = est.estimateMasterID', 'left')->join('(SELECT MAX(versionLevel),versionOrginID,MAX(estimateMasterID) as estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID) maxl', 'maxl.estimateMasterID = est.estimateMasterID', 'INNER')->join('(SELECT IF(SUM(approvedYN) > 0,1,0) as docApprovedYN,documentSystemCode from srp_erp_documentapproved WHERE documentID="EST" AND companyID='.current_companyID().' GROUP BY documentSystemCode) docApp', 'est.estimateMasterID = docApp.documentSystemCode', 'left')
            ->join('srp_erp_mfq_segment mfqsegment','mfqsegment.mfqSegmentID = est.mfqSegmentID','left')
            ->join('srp_erp_currencymaster','est.transactionCurrencyID = srp_erp_currencymaster.currencyID','left')
            ->join('srp_erp_segment segment','segment.segmentID = mfqsegment.segmentID','left');
        $this->datatables->where('est.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'editEstimate(estimateMasterID,confirmedYN,estimateMasterIDJob,approvedYN,workProcessID,docApprovedYN,isDeleted)');
        $this->datatables->add_column('submissionStatus', '$1', 'estimate_approval_status(approvedYN,confirmedYN,submissionStatus,estimateMasterID,"EST")');
        $this->datatables->add_column('estimateStatus', '$1', ' get_customerinquiry_status(confirmedYN,dueDate,isMailSent)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->add_column('estAmount', '<span class="pull-right"><b> $1 </b> $2 </span>', 'transactionCurrency, number_format(estimateValue, transactionCurrencyDecimalPlaces)');
        $this->datatables->edit_column('documentDate', '<span >$1 </span>', 'convert_date_format(documentDate)');
        echo $this->datatables->generate();
    }

    function save_Estimate()
    {
        $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Estimate Date', 'trim|required');
        //$this->form_validation->set_rules('deliveryDate', 'Delivery Date', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('submissionStatus', 'Submission Status', 'trim|required');
        $this->form_validation->set_rules('pricingFormula', 'Pricing Formula', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Estimate_model->save_Estimate());
        }
    }

    function save_EstimateDetail()
    {
        $this->form_validation->set_rules('expectedQty[]', 'Qty', 'trim|required');
        $this->form_validation->set_rules('estimatedCost[]', 'Cost', 'trim|required');
        $this->form_validation->set_rules('mfqItemID[]', 'Item', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Estimate_model->save_EstimateDetail());
        }
    }

    function confirm_Estimate()
    {
        $result = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        if ($result) {
            echo json_encode($this->MFQ_Estimate_model->confirm_Estimate());
        } else {
            echo json_encode(array('w', 'Please add items before confirm'));
        }

    }

    function delete_estimateDetail()
    {
        echo json_encode($this->MFQ_Estimate_model->delete_estimateDetail());
    }

    function load_mfq_estimate()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_estimate());
    }

    function load_mfq_estimate_detail()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_estimate_detail());
    }

    function save_allottedManhours()
    {
        echo json_encode($this->MFQ_Estimate_model->save_allottedManhours());
    }

    function save_unitSellingPrice()
    {
        echo json_encode($this->MFQ_Estimate_model->save_unitSellingPrice());
    }

    function load_mfq_estimate_detail_subJobGenerate()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_estimate_detail_subJobGenerate());
    }

    function fetch_customer_inquiry()
    {
        echo json_encode($this->MFQ_Estimate_model->fetch_customer_inquiry());
    }

    function load_mfq_customerInquiryDetail()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_customerInquiryDetail());
    }

    function save_estimate_detail_margin()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_margin());
    }

    function save_estimate_detail_margin_total()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_margin_total());
    }

    function save_estimate_detail_discount_total()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_discount_total());
    }

    function save_estimate_detail_discount()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_discount());
    }
    
    function save_estimate_detail_actualMargin()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_actualMargin());
    }

    function save_estimate_detail_warranty_cost()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_warranty_cost());
    }

    function save_estimate_detail_commision()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_commision());
    }
    
    function save_estimate_detail_selling_price(){
        echo json_encode($this->MFQ_Estimate_model->save_estimate_detail_selling_price());
    }

    function fetch_estimate_print()
    {
        $_POST["estimateMasterID"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('estimateMasterID') ?? '');
        $_POST["hideMargin"] = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('hideMargin') ?? '');

        $data = array();
        $data["header"] = $this->MFQ_Estimate_model->load_mfq_estimate();
        $data['customercountry'] = $this->db->query("SELECT customerCountry FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = '{$data['header']['mfqCustomerAutoID']}'")->row_array();
        $data["itemDetail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["version"] = $this->MFQ_Estimate_model->load_mfq_estimate_version();

        $data['viewMargin']= 0;
        if($this->input->post('hideMargin')){
            $data['viewMargin']= $this->input->post('hideMargin');
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/mfq/ajax/estimate_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L');
        }
    }

    function fetch_estimate_proposal_print(){

        $_POST["estimateMasterID"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('estimateMasterID') ?? '');
        $_POST["hideMargin"] = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('hideMargin') ?? '');

        $data = array();

        // $estimateMasterID = $this->input->post('estimateMasterID');
        $proposalID = $this->input->post('proposalID');

        $this->db->where('proposalID',$proposalID);
        $proposalDetail = $this->db->from('srp_erp_mfq_estimateproposalreview')->get()->row_array();

        $estimateMasterID = $proposalDetail['estimateMasterID'];

        $data["header"] = $this->MFQ_Estimate_model->load_mfq_estimate();
        // $data['customercountry'] = $this->db->query("SELECT customerCountry FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = '{$data['header']['mfqCustomerAutoID']}'")->row_array();
        // $data["itemDetail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        // $data["version"] = $this->MFQ_Estimate_model->load_mfq_estimate_version();
        $data['estimateMasterID'] = $estimateMasterID;
        $data['proposalID'] = $proposalID;

        $data['viewMargin']= 0;
        if($this->input->post('hideMargin')){
            $data['viewMargin']= $this->input->post('hideMargin');
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }


        $html = $this->load->view('system/mfq/ajax/mfq_estimate_proposal', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L');
        }


    }

    function save_estimate_proposal_approval(){

        $system_code = trim($this->input->post('estimateMasterID') ?? '');
        $system_code = trim($this->input->post('proposalID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'ESTP', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('proposalID');
                $this->db->where('proposalID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_estimateproposalreview');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('proposalID', 'Proposal ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Estimate_model->save_estimate_proposal_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('proposalID');
            $this->db->where('proposalID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_estimateproposalreview');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'ESTP', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Estimate Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('proposalID', 'Proposal ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Estimate_model->save_estimate_proposal_approval());
                    }
                }
            }
        }


    }


    function save_estimate_version()
    {
        echo json_encode($this->MFQ_Estimate_model->save_estimate_version());
    }

    function load_mfq_estimate_version()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_estimate_version());
    }

    function load_mfq_estimate_proposal_review()
    {
        echo json_encode($this->MFQ_Estimate_model->load_mfq_estimate_proposal_review());
    }

    function update_mfq_estimate_po_number()
    {
        echo json_encode($this->MFQ_Estimate_model->update_mfq_estimate_po_number());
    }

    function update_mfq_estimate_quotedComment()
    {
        echo json_encode($this->MFQ_Estimate_model->update_mfq_estimate_quotedComment());
    }

    function update_inquiry_department_comment()
    {
        echo json_encode($this->MFQ_Estimate_model->update_inquiry_department_comment());
    }


    function load_emails()
    {
        echo json_encode($this->MFQ_Estimate_model->load_emails());
    }

    function send_emails()
    {
        $this->form_validation->set_rules('emailNW[]', 'email', 'trim|valid_email');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Estimate_model->send_emails());
        }
    }

    function fetch_estimate_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $searches = " ((est.description Like '%$sSearch%') OR (cust.CustomerName Like '%$sSearch%') OR (DATE_FORMAT(est.documentDate,'$convertFormat') Like '%$sSearch%') OR (est.estimateCode Like '%$sSearch%')) ";
        }
        $this->datatables->select('DATE_FORMAT(est.documentDate,\'' . $convertFormat . '\') as documentDate,est.description as description, cust.CustomerName as CustomerName,est.estimateMasterID as estimateMasterID,est.estimateCode,est.confirmedYN as confirmedYN,srp_erp_documentapproved.approvalLevelID as approvalLevelID,documentApprovedID,srp_erp_documentapproved.approvedYN as approvedYN', false);
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->datatables->from('srp_erp_mfq_estimatemaster est');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = est.estimateMasterID AND srp_erp_documentapproved.approvalLevelID = est.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = est.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'EST');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'EST');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        if(!empty($searches)) {
            $this->datatables->where($searches);
        }
        $this->datatables->where('est.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->add_column('detail', '<b>Client : </b> $1 <b> <br>Estimate Date : </b> $2  <b><br> Description : </b> $3',
            'CustomerName,documentDate,description');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"EST",estimateMasterID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action(estimateMasterID,approvalLevelID,approvedYN,documentApprovedID,"EST")');
        echo $this->datatables->generate();
    }

    function fetch_estimate_proposal_approval(){
          /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $searches = " ((est.description Like '%$sSearch%') OR (cust.CustomerName Like '%$sSearch%') OR (DATE_FORMAT(est.documentDate,'$convertFormat') Like '%$sSearch%') OR (est.estimateCode Like '%$sSearch%')) ";
        }
        $this->datatables->select('DATE_FORMAT(est.documentDate,\'' . $convertFormat . '\') as documentDate,est.description as description,estreview.proposalID as proposalID, cust.CustomerName as CustomerName,est.estimateMasterID as estimateMasterID,estreview.proposalCode as estimateCode,est.confirmedYN as confirmedYN,srp_erp_documentapproved.approvalLevelID as approvalLevelID,documentApprovedID,srp_erp_documentapproved.approvedYN as approvedYN', false);

        $this->datatables->from('srp_erp_mfq_estimateproposalreview estreview');
        $this->datatables->join('srp_erp_mfq_estimatemaster est', 'estreview.estimateMasterID = est.estimateMasterID');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = estreview.proposalID AND srp_erp_documentapproved.approvalLevelID = estreview.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = estreview.currentLevelNo');
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'ESTP');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'ESTP');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        if(!empty($searches)) {
            $this->datatables->where($searches);
        }
        $this->datatables->where('est.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->add_column('detail', '<b>Client : </b> $1 <b> <br>Estimate Date : </b> $2  <b><br> Description : </b> $3',
            'CustomerName,documentDate,description');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"ESTP",proposalID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action(proposalID,approvalLevelID,approvedYN,documentApprovedID,"ESTP")');
        echo $this->datatables->generate();
    }

    function save_estimate_approval()
    {
        $system_code = trim($this->input->post('estimateMasterID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'EST', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('estimateMasterID');
                $this->db->where('estimateMasterID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_estimatemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('estimateMasterID', 'Estimate ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Estimate_model->save_estimate_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('estimateMasterID');
            $this->db->where('estimateMasterID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_estimateMaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'EST', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Estimate Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('estimateMasterID', 'Estimate ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Estimate_model->save_estimate_approval());
                    }
                }
            }
        }
    }

    function referback_estimate()
    {
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($estimateMasterID, 'EST');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }


    function save_additional_order_detail()
    {
        $this->form_validation->set_rules('exclusions', 'Exclusions', 'trim|required');
        $this->form_validation->set_rules('engineeringDrawings', 'Submission of Engineering Drawings', 'trim|required');
        $this->form_validation->set_rules('engineeringDrawingsComment', 'Submission of Engineering Drawings comment', 'trim|required');
        $this->form_validation->set_rules('submissionOfITP', 'Submission of ITP', 'trim|required');
        $this->form_validation->set_rules('itpComment', 'Submission of ITP Comment', 'trim|required');
        $this->form_validation->set_rules('qcqtDocumentation', 'QC/QT documentation', 'trim|required');
        $this->form_validation->set_rules('scopeOfWork', 'Scope of work', 'trim|required');
        $this->form_validation->set_rules('materialCertificateID[]', 'Material certificate', 'trim|required');
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('mfqWarehouseAutoID', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('orderStatus', 'Order status', 'trim|required');
        $this->form_validation->set_rules('awardedDate', 'Awarded Date', 'trim|required');

        if($this->input->post("orderStatus") == 2){
            $this->form_validation->set_rules('poNumber', 'PO Number', 'trim|required');
        }
        //$this->form_validation->set_rules('poNumber', 'PO Number', 'trim|required');

        /*$this->form_validation->set_rules('deliveryTerms', 'Delivery terms', 'trim|required');
        $this->form_validation->set_rules('warranty', 'Description', 'trim|required');
        $this->form_validation->set_rules('validity', 'validity', 'trim|required');*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Estimate_model->save_additional_order_detail());
        }
    }

    function fetch_job_comm_invoice_view()
    {
        $this->load->model("MFQ_Job_model");
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        
        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.deliveryTerms as deliveryTerms,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();
        //$data["jobMaster"] = $this->MFQ_Job_model->load_job_header();

        $this->db->select('srp_erp_mfq_job.workProcessID as workProcessID,
        srp_erp_customermaster.customerName as customerName,
        srp_erp_customermaster.customerAddress1 as customerAddress1,
        srp_erp_customermaster.customerAddress2,
        srp_erp_customermaster.customerCountry as customerCountry,
        srp_erp_mfq_estimatemaster.ClientReferenceNo as ClientReferenceNo');
        $this->db->from('srp_erp_mfq_job');
        $this->db->join('srp_erp_customermaster', 'srp_erp_mfq_job.mfqCustomerAutoID = srp_erp_customermaster.customerAutoID', 'inner');
        $this->db->join('srp_erp_mfq_estimatemaster', 'srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID', 'inner');
        $this->db->where('srp_erp_mfq_job.workProcessID', $estimateMasterID);
        $data["mfq_header"] = $this->db->get()->row_array();

        $data["type"] = 'html';
        $data["estimateMasterID"] = $estimateMasterID;
        $html = $this->load->view('system/mfq/ajax/estimate_job_comm_invoice_print_preview', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            /*$this->load->library('pdf');
            $footer = 'Doc No:'. $data["jobMaster"]["documentCode"].' Rev No:'.$data["header"]["versionLevel"];
            $pdf = $this->pdf->printed($html, 'A4',1,$footer);*/
        }
    }

    function fetch_job_packing_list_invoice_view()
    {
        $this->load->model("MFQ_Job_model");
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        
        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.deliveryTerms as deliveryTerms,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();
        //$data["jobMaster"] = $this->MFQ_Job_model->load_job_header();

        $this->db->select('srp_erp_mfq_job.workProcessID as workProcessID,
        srp_erp_customermaster.customerName as customerName,
        srp_erp_customermaster.customerAddress1 as customerAddress1,
        srp_erp_customermaster.customerAddress2,
        srp_erp_customermaster.customerCountry as customerCountry,
        srp_erp_mfq_estimatemaster.ClientReferenceNo as ClientReferenceNo');
        $this->db->from('srp_erp_mfq_job');
        $this->db->join('srp_erp_customermaster', 'srp_erp_mfq_job.mfqCustomerAutoID = srp_erp_customermaster.customerAutoID', 'inner');
        $this->db->join('srp_erp_mfq_estimatemaster', 'srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID', 'inner');
        $this->db->where('srp_erp_mfq_job.workProcessID', $estimateMasterID);
        $data["mfq_header"] = $this->db->get()->row_array();

        $data["type"] = 'html';
        $data["estimateMasterID"] = $estimateMasterID;
        $html = $this->load->view('system/mfq/ajax/estimate_job_packing_list_invoice_print_preview', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            /*$this->load->library('pdf');
            $footer = 'Doc No:'. $data["jobMaster"]["documentCode"].' Rev No:'.$data["header"]["versionLevel"];
            $pdf = $this->pdf->printed($html, 'A4',1,$footer);*/
        }
    }

    function fetch_job_order_view()
    {
        $this->load->model("MFQ_Job_model");
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $workProcessID = trim($this->input->post('workProcessID') ?? '');

        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.deliveryTerms as deliveryTerms,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();
        $data["estimateDetail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["certifications"] = $this->MFQ_Estimate_model->load_mfq_estimate_certifications();
        $data["jobMaster"] = $this->MFQ_Job_model->load_job_header();
        $data["detail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["userInput"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order();
        $data["certificationComment"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order_mc_comment();
        $data["type"] = 'html';
        $data["estimateMasterID"] = $estimateMasterID;
        $data["workProcessID"] = $workProcessID;
        $html = $this->load->view('system/mfq/ajax/estimate_job_order_print_preview', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $footer = 'Doc No:'. $data["jobMaster"]["documentCode"].' Rev No:'.$data["header"]["versionLevel"];
            $pdf = $this->pdf->printed($html, 'A4',1,$footer);
        }
    }


    function fetch_job_order_view_for_save()
    {
        $this->load->model("MFQ_Job_model");
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $workProcessID = trim($this->input->post('workProcessID') ?? '');

        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.deliveryTerms as deliveryTerms,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();
        $data["estimateDetail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["certifications"] = $this->MFQ_Estimate_model->load_mfq_estimate_certifications();
        $data["jobMaster"] = $this->MFQ_Job_model->load_job_header();
        $data["detail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["userInput"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order();
        $data["certificationComment"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order_mc_comment();
        $html = $this->load->view('system/mfq/ajax/estimate_job_order_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $footer = 'Doc No:'. $data["jobMaster"]["documentCode"].' Rev No:'.$data["header"]["versionLevel"];
            $pdf = $this->pdf->printed($html, 'A4',1,$footer);
        }
    }

    function fetch_job_order_save()
    {
        echo json_encode($this->MFQ_Estimate_model->fetch_job_order_save());
    }


    function fetch_quotation_view()
    {
        $estimateMasterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('estimateMasterID') ?? '');
        $_POST['estimateMasterID'] = $estimateMasterID;
        $data["pdfType"] = 0;
        $this->load->library('NumberToWords');
        $data['header'] =  $this->MFQ_Estimate_model->load_mfq_estimate();
        $data['detail'] =  $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data['customercountry'] = $this->db->query("SELECT customerCountry FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = '{$data['header']['mfqCustomerAutoID']}'")->row_array();
        if ($this->input->post('html')) {
            $data['mode'] = "html";
        } else {
            $data['mode'] = "pdf";
            $data["pdfType"] = 1;
        }
        $html = $this->load->view('system/mfq/ajax/quotation_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->view('system/mfq/ajax/quotation_print', $data);
        }
    }

     function generate_mfq_job()
    {
        $this->form_validation->set_rules('estimateMasterID', 'EstimateMaster ID', 'trim|required');
        $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer ID', 'trim|required');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('warehouseID', 'Warehouse', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
           echo json_encode($this->MFQ_Estimate_model->save_mfq_job());
        }
       // echo json_encode($this->MFQ_Estimate_model->save_mfq_job());
    }

    function delete_estimate()
    {
        echo json_encode($this->MFQ_Estimate_model->delete_estimate());
    }
    function fetch_job_order_view_pdf()
    {
        $this->load->model("MFQ_Job_model");
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.deliveryTerms as deliveryTerms,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();
        $data["estimateDetail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["certifications"] = $this->MFQ_Estimate_model->load_mfq_estimate_certifications();
        $data["jobMaster"] = $this->MFQ_Job_model->load_job_header();
        $data["detail"] = $this->MFQ_Estimate_model->load_mfq_estimate_detail();
        $data["userInput"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order();
        $data["certificationComment"] = $this->MFQ_Estimate_model->load_mfq_estimate_job_order_mc_comment();
        $data["type"] = 'pdf';
        $data["estimateMasterID"] = $estimateMasterID;
        $data["workProcessID"] = $workProcessID;
        $data['output'] = 'view';
        $html = $this->load->view('system/mfq/ajax/estimate_job_order_print_preview_new', $data, true);
        echo $html;

        //$this->load->library('pdf');
//        $footer = 'Doc No:'. $data["jobMaster"]["documentCode"].' Rev No:'.$data["header"]["versionLevel"];
        //$pdf = $this->pdf->printed($html, 'A4',1);

    }

    /* Function added */
    function fetch_item_bom()
    {
        $bomMasterID = $this->input->post('bomMasterID');
        $this->load->model('MFQ_BillOfMaterial_model');
        $mfqItemMaster = $this->MFQ_BillOfMaterial_model->get_srp_erp_mfq_billofmaterial($bomMasterID);

        $html = $this->load->view('system/mfq/ajax/bom_print', $mfqItemMaster, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L');
        }
    }
    /* End  Function */

    function validate_item()
    {
        echo json_encode($this->MFQ_Estimate_model->validate_item());
    }

    function change_discount_view()
    {
        $update = $this->MFQ_Estimate_model->change_discount_view();
        if($update) {
            echo ($this->fetch_estimate_print());
        }
    }
    function fetch_billofmaterialexist()
    {
        $mfqItemID = $this->input->post('mfqItemID');
        $companyID = current_companyID();
        $billofmaterialisexist = $this->db->query("select bomMasterID from srp_erp_mfq_billofmaterial where companyID = $companyID  AND mfqItemID = $mfqItemID")->row('bomMasterID');
        if($billofmaterialisexist=='')
        {
            $currentuserID = current_userID();
            $current_pc = current_pc();
            $currentdate = current_date(true);
            $currentusername  = $this->common_data['current_user'];
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_billofmaterial', 'bomMasterID', 'companyID');
            $codes = $this->sequence->sequence_generator('BOM', $serialInfo['serialNo'] + 1);
            $this->db->query("INSERT INTO srp_erp_mfq_billofmaterial(serialNo,documentCode,documentDate,mfqItemID,uomID,Qty,companyID,confirmedYN,confirmedUserID,createdPCID,createdUserID,createdUserName)
            select 
            {$serialInfo['serialNo']} as serialNo,
            '$codes' as documentCode,
            CURRENT_DATE as documentDate,
            $mfqItemID as mfqItemID,
            defaultUnitOfMeasureID as uomID,
            1 as Qty,
            $companyID as companyID,
            1 as confirmedYN,
            $currentuserID as confirmedUserID,
            '$current_pc' as createdPCID,
            $currentuserID as createdUserID,
            '$currentusername' as createdUserName 
            from srp_erp_mfq_itemmaster 
            where companyID = $companyID AND mfqItemID = $mfqItemID");
            $lastcreatedbom = $this->db->query("select bomMasterID from srp_erp_mfq_billofmaterial where companyID = $companyID AND mfqItemID = $mfqItemID")->row('bomMasterID');

            $data['billofmaterial'] =  2;
            $data['pageID'] =  $lastcreatedbom;
        }else
        {
            $data['billofmaterial'] = 1;
        }
        echo json_encode($data);
    }
    function update_estimate_qty()
    {
        $this->form_validation->set_rules('value', 'Qty', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $value =  explode('|', $this->input->post('pk'));
            $ID =$value[0];
            $unitcost =$value[1];
            $margin =$value[2];
            $discountpercentage =$value[5];
            $actualmargin =$value[6];
            $qty =  $this->input->post('value');
            $totalvalue = ($qty*$unitcost);
            $sellingprice =  (($totalvalue)*$margin)/100;


            $this->db->trans_start();
            $data['expectedQty'] = $qty;
            $data['margin'] = $margin;
            $data['sellingPrice'] =($totalvalue)+$sellingprice;
            $discountamount =  (($data['sellingPrice']) * $discountpercentage)/100;
            $data['discountedPrice'] = ( $data['sellingPrice'] -$discountamount);

            $data['actualMargin'] = $data['discountedPrice'] - $totalvalue;
            //print_r($data['actualMargin']);exit;

            $this->db->where('estimateDetailID', $ID);
            $this->db->update('srp_erp_mfq_estimatedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
             echo json_encode( array('e','Qty Save Failed '));
            } else {
                $this->db->trans_commit();
                $data_qty = $qty;
                echo json_encode( array('s','Qty Updated Successfully.', $data_qty, $ID, $data['discountedPrice'], $totalvalue, $data['sellingPrice'], $data['actualMargin']));
            }
        }
    }

    function estimate_item_cost()
    {
        echo json_encode($this->MFQ_Estimate_model->estimate_item_cost());
    }

    function fetch_estimate_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Estimate');
        $this->load->database();

        $header = [ '#', 'Estimate Code', 'Estimate Date', 'Segment', 'Customer', 'Description', 'Currency', 'Amount', 'Approval Status', 'Estimate Status'];

        $details = $this->MFQ_Estimate_model->fetch_estimate_details();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Estimate'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'Estimate.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_assigned_revenue_gl()
    {
        echo json_encode($this->MFQ_Estimate_model->fetch_assigned_revenue_gl());
    }

    function update_mfq_linked_item()
    {
        $this->form_validation->set_rules('linkedItemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('mfqItemID', 'Revenue GL', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Estimate_model->update_mfq_linked_item());
        }
    }

    function upload_attachment_for_estimate()
    {
        echo json_encode($this->MFQ_Estimate_model->upload_attachment_for_estimate());
    }

    function validate_item_pulled()
    {
        echo json_encode($this->MFQ_Estimate_model->validate_item_pulled());
    }

    function load_estimate_detail_items(){
        echo json_encode($this->MFQ_Estimate_model->load_estimate_detail_items());
    }

    function confirm_proposal_review(){

        //set estimate approval record
        $response = $this->MFQ_Estimate_model->set_EstimateProposal();

        echo json_encode($this->MFQ_Estimate_model->confirm_EstimateProposal());
    }

  
}