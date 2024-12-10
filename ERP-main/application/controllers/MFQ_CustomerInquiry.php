<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MFQ_CustomerInquiry extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_CustomerInquiry_model');
        $this->load->model('MFQ_Estimate_model');
        $this->load->library('s3');
    }
    

    function fetch_customerInquiry()
    {
        $customer = $this->input->post("customerID");
        $status = $this->input->post("statusID");
        $DepartmentID = $this->input->post("DepartmentID");
        $rfqtype = $this->input->post("rfqtype");
        $proposalengID = $this->input->post("proposalengID");
        $jobstatus = $this->input->post("jobstatus");
        $companyID = $this->common_data['company_data']['company_id'];
        $where = ' ci.companyID = '.$companyID.'';
        if($customer)
        { 
            $where .= ' AND ci.mfqCustomerAutoID IN ('.$customer.')';
        }

         if ($status) {
            $where .= ' AND ci.statusID = '.$status.' ';
        }
        if($jobstatus)
        {  
             $where .= ' AND jobStatus = '.$jobstatus.' ';
        }
       
        if($rfqtype)
        {
            $where .= ' AND ci.type = '.$rfqtype.'';
        
        }
        if($DepartmentID)
        {
            $where .= ' AND ci.segmentID IN ('.$DepartmentID.')';
        }
        if($proposalengID)
        {
         
            $where .= ' AND ci.proposalEngineerID IN ('.$proposalengID.')';
        }
      
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('jobStatus, DATE_FORMAT(DNDate.deliveryDate,\'' . $convertFormat . '\') as actualDeliveryDate,DATE_FORMAT(mfqjob.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,DATE_FORMAT(jbMas.awardedDate,\'' . $convertFormat . '\') as awardedDate, jbMas.documentCode AS documentCode,jbMas.workProcessID AS workProcessID, ci.documentDate as documentDate,DATE_FORMAT(ci.dueDate,\'' . $convertFormat . '\') as dueDate,DATE_FORMAT(ci.deliveryDate,\'' . $convertFormat . '\') as deliveryDate,ci.description,ci.paymentTerm, cust.CustomerName as CustomerName,
                    ci.ciMasterID as ciMasterID,ci.ciCode as ciCode,ci.confirmedYN as confirmedYN,ci.statusID as statusID,statusColor,statusBackgroundColor,srp_erp_mfq_status.description as statusDescription,ci.dueDate as plannedDate,referenceNo,ci.approvedYN as approvedYN,est.confirmedYN as estConfirmedYN,	IFNULL( mfqsegment.segmentcode,\'-\')  as segment,	IFNULL( srp_erp_mfq_estimatemaster.confirmedYN,0) AS estimateconf,
                    empdetail.Ename2 as proposalengineer, quotationStatus, IFNULL(srp_erp_mfq_estimatemaster.poNumber, "") AS poNumber, ((discountedPrice * ((100 + IFNULL(totMargin, 0))/100)) * ((100 - IFNULL(totDiscount, 0))/100)) AS estimateValue, srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces, CONCAT(srp_erp_currencymaster.CurrencyCode, " : ") AS transactionCurrency,est.estimateCode as estimateCode,srp_erp_mfq_estimatemaster.estimateMasterID as estimateMasterID', false)
            ->from('srp_erp_mfq_customerinquiry ci')
            ->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = ci.mfqCustomerAutoID', 'left')->join('srp_erp_mfq_status', 'srp_erp_mfq_status.statusID = ci.statusID', 'left')
            
            ->join('srp_erp_mfq_segment mfqsegment','mfqsegment.mfqSegmentID = ci.segmentID','left')
            ->join('(
                SELECT 
                srp_erp_mfq_estimatemaster.*
                FROM srp_erp_mfq_estimatemaster
                INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID
                )srp_erp_mfq_estimatemaster','srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID','left')
            ->join('srp_erp_currencymaster','srp_erp_mfq_estimatemaster.transactionCurrencyID = srp_erp_currencymaster.currencyID','left')

            ->join('(
                SELECT 
                    srp_erp_mfq_estimatedetail.estimateMasterID,srp_erp_mfq_estimatemaster.approvedYN,srp_erp_mfq_estimatedetail.ciMasterID,confirmedYN,
                    SUM(discountedPrice) AS discountedPrice,srp_erp_mfq_estimatemaster.estimateCode 
                FROM srp_erp_mfq_estimatedetail 
                INNER JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID
                GROUP BY srp_erp_mfq_estimatedetail.estimateMasterID
                ) est', 'est.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID', 'left')

        /* ->join('srp_erp_mfq_job','srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID AND linkedJobID IS NOT NULL AND (srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL)','left') */
           ->join('(SELECT 
                    linkedJobID,
                    srp_erp_mfq_job.estimateMasterID
                    FROM 
                    srp_erp_mfq_job 
                    LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
                    Where 	
                    linkedJobID IS NOT NULL 
                    AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ))mfqjobtbl','srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID','Left')
           
           
            ->join('(SELECT 
                    workProcessID,
                    awardedDate,
                    documentDate,
                    documentCode
                    FROM 
                    srp_erp_mfq_job)jbMas','jbMas.workProcessID = mfqjobtbl.linkedJobID','Left')

            ->join('srp_erp_segment segment','segment.segmentID = mfqsegment.segmentID','left')
            ->join('srp_employeesdetails empdetail','empdetail.EIdNo = ci.proposalEngineerID','left')
            ->join('(SELECT MAX( deliveryDate ) AS deliveryDate, linkedJobID FROM srp_erp_mfq_deliverynote
                            JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID
                            JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID
                            WHERE deletedYn != 1 GROUP BY linkedJobID)DNDate','DNDate.linkedJobID = jbMas.workProcessID','left')

            ->join('(SELECT	linkedJobID, MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
	FROM srp_erp_mfq_job
		LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
		LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
	GROUP BY linkedJobID)MainJobStatus', 'MainJobStatus ON MainJobStatus.linkedJobID = jbMas.workProcessID', 'left')
//            ->join('srp_erp_mfq_deliverynotedetail','srp_erp_mfq_deliverynotedetail.jobID =  srp_erp_mfq_job.workProcessID','left')
//            ->join('srp_erp_mfq_deliverynote','srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID AND deletedYn != 1','left');
             ->join('(SELECT
                      estimateMasterID,
                      expectedDeliveryDate, 
                      linkedJobID 
                      FROM
                      srp_erp_mfq_job 
                      WHERE
                      linkedJobID IS NULL)mfqjob','srp_erp_mfq_estimatemaster.estimateMasterID = mfqjob.estimateMasterID','Left');
        $this->datatables->where($where);
        $this->datatables->group_by('ci.ciMasterID');
       
        
      
        $this->datatables->add_column('details', ' <b> CLIENT : </b> $1 <br> <b> PROPOSAL ENG : </b>$2 <br> <b> SEGMENT : </b>$3 <br> <b> REF NO : </b>$4 ', 'CustomerName,proposalengineer,segment,referenceNo');
        $this->datatables->add_column('poNumber', ' <b> PO NO : </b> $1 <br> <b> JOB NO : </b>$2<br> <b> JOB STATUS : </b>$3', 'poNumber,documentCode, load_main_job_status(jobStatus)');
        $this->datatables->add_column('estAmount', '<span class="pull-right"><b><a href="#" onclick="viewDocument_customerInquiry($4)">$3</a>  </b><br><b> $1 </b> $2 </span>', 'transactionCurrency, number_format(estimateValue, transactionCurrencyDecimalPlaces),estimateCode,estimateMasterID');
        $this->datatables->add_column('edit', '$1', 'editCustomerInquiry(ciMasterID,confirmedYN,confirmedYN)');
        $this->datatables->add_column('status', '$1', 'customerInquiryStatus(quotationStatus, ciMasterID, statusID)');
        $this->datatables->add_column('statusID', '$1', 'customer_inquiry_approval_status(statusID)');
        $this->datatables->edit_column('documentDate', '<span >$1 </span>', 'convert_date_format(documentDate)');
        //$this->datatables->add_column('actualsubdate', '$1', 'actualsubmissiondate(ciMasterID,deliveryDate,estimateconf)');
        $this->datatables->add_column('dates', ' <span title="Actual Submission Date"> <b> ASD : </b> $1 </span><br> <span title="Planned Submission Date"><b> PSD : </b>$2 </span><br> <span title="Planned Delivery Date"><b> PDD : </b>$3 </span><br> <span title="Actual Delivery Date"><b> ADD : </b>$4 </span>', 'actualsubmissiondate(ciMasterID,deliveryDate,estimateconf),dueDate, expectedDeliveryDate, actualDeliveryDate');
        echo $this->datatables->generate();
    }

    function save_CustomerInquiry()
    {

        $flowserve = getPolicyValues('MANFL', 'All');

        $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        
        if($flowserve != 'Micoda'){
            $this->form_validation->set_rules('dueDate', 'Planned Submission Date', 'trim|required');
        }
       
       
        //$this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('referenceNo', 'Client Reference No', 'trim|required');
        // $this->form_validation->set_rules('statusID', 'Status', 'trim|required');
        
        $this->form_validation->set_rules('micoda', 'Micoda Operation', 'trim|required');
        $this->form_validation->set_rules('rfq_status', 'RFQ Status', 'trim|required');
        $this->form_validation->set_rules('document_status', 'Document Status', 'trim|required');
        $this->form_validation->set_rules('order_status', 'Order Status ', 'trim|required');

        $this->form_validation->set_rules('cat', 'Category', 'trim|required');
        $this->form_validation->set_rules('submission_status', 'Submission Status ', 'trim|required');

       // $this->form_validation->set_rules('sourceID', 'source', 'trim|required');
        $this->form_validation->set_rules('manufacturingType', 'Manufacturing Type', 'trim|required');
        

        
       
        if($flowserve =='FlowServe'){
            $this->form_validation->set_rules('order_job', 'JOB', 'trim|required');
        }

        if($flowserve !='GCC'){
            $this->form_validation->set_rules('prpengineer', 'Proposal Engineer', 'trim|required');
            $this->form_validation->set_rules('deliveryDate', 'Actual Submission Date', 'trim|required');
            $this->form_validation->set_rules('DepartmentID', 'Segment', 'trim|required');
        }

        if($flowserve =='Micoda'){
            $this->form_validation->set_rules('type', 'Inquiry Type', 'trim|required');
            $this->form_validation->set_rules('SalesManagerID', 'Sales Manager', 'trim|required');
            $this->form_validation->set_rules('estimatedEmpID', 'Estimated Employee', 'trim|required');
        }
       // $this->form_validation->set_rules('engineeringemployee', 'Engineering Employee', 'trim|required');
       // $this->form_validation->set_rules('EngineeringDeadLine', 'Engineering End Date', 'trim|required');

        //$this->form_validation->set_rules('purchasingemployee', 'Purchasing Employee', 'trim|required');
       // $this->form_validation->set_rules('purchasingDeadLine', 'Purchasing End Date', 'trim|required');

       // $this->form_validation->set_rules('productionemployee', 'Purchasing Employee', 'trim|required');
        //$this->form_validation->set_rules('DeadLineproduction', 'Purchasing End Date', 'trim|required');

       // $this->form_validation->set_rules('qaqcemployee', 'QA/QC Employee', 'trim|required');
       // $this->form_validation->set_rules('DeadLineqaqc', 'QA/QC End Date', 'trim|required');
        //$this->form_validation->set_rules('paymentTerm', 'Payment Terms', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_CustomerInquiry_model->save_CustomerInquiry());
        }
    }


    function fetch_tender_logs()
    {
        $convertFormat = convert_date_format_sql();

        $companyID= current_companyID();

        $datefrom = $this->input->post("IncidateDateFrom");
        $dateto = $this->input->post("IncidateDateTo");
        $customerCode = $this->input->post("customerCode");
        $proposalengID = $this->input->post("proposalengID");
        $rfqType = $this->input->post("rfqType");
        $rfqstatus = $this->input->post("rfqstatus");
        $micoda = $this->input->post("micoda");
        $nstatus = $this->input->post("nstatus");
        $orderstatus = $this->input->post("orderstatus");

        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        
        $supplier_filter = '';
        if (!empty($customerCode)) {
            $supplier = array($this->input->post('customerCode'));
            $whereIN1 = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND i.mfqCustomerAutoID IN " . $whereIN1;
        }

        $eng_filter = '';
        if (!empty($proposalengID)) {
            $eng = array($this->input->post('proposalengID'));
            $whereIN2 = "( " . join("' , '", $eng) . " )";
            $eng_filter = " AND i.proposalEngineerID IN " . $whereIN2;
        }

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( i.documentDate >= '" . $datefromconvert . " 00:00:00' AND i.documentDate <= '" . $datetoconvert . " 23:59:00')";
        }

        $rfqType_filter = '';
        if (!empty($rfqType)) {
            $rfqType_arr = array($this->input->post('rfqType'));
            $whereIN3 = "( " . join("' , '", $rfqType_arr) . " )";
            $rfqType_filter = " AND i.type IN " . $whereIN3;
        }

        $rfqstatus_filter = '';
        if (!empty($rfqstatus)) {
            $status_arr = array($this->input->post('rfqstatus'));
            $whereIN4 = "( " . join("' , '", $status_arr) . " )";
            $rfqstatus_filter = " AND i.rfqStatus IN " . $whereIN4;
        }

        $micoda_filter = '';
        if (!empty($micoda)) {
            $micoda_arr = array($this->input->post('micoda'));
            $whereIN5 = "( " . join("' , '", $micoda_arr) . " )";
            $micoda_filter = " AND i.locationAssigned IN " . $whereIN5;
        }

        $nstatus_filter = '';
        if (!empty($nstatus)) {
            $nstatus_arr = array($this->input->post('nstatus'));
            $whereIN6 = "( " . join("' , '", $nstatus_arr) . " )";
            $nstatus_filter = " AND i.documentStatus IN " . $whereIN6;
        }

        $orderstatus_filter = '';
        if (!empty($orderstatus)) {
            $orderstatus_arr = array($this->input->post('orderstatus'));
            $whereIN7 = "( " . join("' , '", $orderstatus_arr) . " )";
            $orderstatus_filter = " AND i.orderStatus IN " . $whereIN7;
        }

        
        
        $where = "i.companyID = " . $companyID .$date.$supplier_filter.$rfqType_filter.$rfqstatus_filter.$eng_filter.$micoda_filter.$nstatus_filter.$orderstatus_filter. "";
        //print_r($where);exit;
        $this->datatables->select('((discountedPrice * ((100 + IFNULL(totMargin, 0))/100)) * ((100 - IFNULL(totDiscount, 0))/100)) AS estimateValue , srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces, CONCAT(srp_erp_currencymaster.CurrencyCode, " : ") AS transactionCurrency, i.ciMasterID,job.workProcessID as workProcessID,sou.description as source,cou.CountryDes as CountryDes,i.ciCode,cus.CustomerName,i.description,i.category as category,est.totDiscountPrice,est.totalSellingPrice,i.type as type,i.locationAssigned as locationAssigned,i.confirmedByName,i.inquirySource,e.Ename2,i.documentDate as documentDate,i.rfqStatus as rfqStatus,i.documentStatus as documentStatus,i.orderStatus as orderStatus,i.dueDate,i.deliveryDate,i.submissionStatus as submissionStatus,est.poNumber,est.documentDate as poDate,est.materialCertificationComment,est.deliveryDate as podeliveryDate,job.documentCode as jobNumber',false)
        ->from('srp_erp_mfq_customerinquiry as i')
        ->join('srp_erp_mfq_estimatemaster as est','est.ciMasterID = i.ciMasterID', 'left')
        ->join('srp_erp_mfq_customermaster cus','cus.mfqCustomerAutoID = i.mfqCustomerAutoID', 'left')

        ->join('srp_erp_mfq_status', 'srp_erp_mfq_status.statusID = est.submissionStatus', 'left')
        ->join('(SELECT estimateMasterID,workProcessID FROM srp_erp_mfq_job WHERE (isDeleted IS NULL OR isDeleted != 1) GROUP BY estimateMasterID) jobx', 'jobx.estimateMasterID = est.estimateMasterID', 'left')
        ->join("(SELECT dueDate,srp_erp_mfq_estimatedetail.ciMasterID,srp_erp_mfq_estimatedetail.estimateMasterID,srp_erp_mfq_estimatedetail.estimateDetailID, SUM(discountedPrice) AS discountedPrice FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_estimatedetail.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID  WHERE srp_erp_mfq_estimatedetail.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_mfq_estimatedetail.estimateMasterID) estd", 'estd.estimateMasterID = est.estimateMasterID', 'left')
        ->join('(SELECT MAX(versionLevel),versionOrginID,MAX(estimateMasterID) as estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID) maxl', 'maxl.estimateMasterID = est.estimateMasterID', 'INNER')
        ->join('(SELECT IF(SUM(approvedYN) > 0,1,0) as docApprovedYN,documentSystemCode from srp_erp_documentapproved WHERE documentID="EST" AND companyID='.current_companyID().' GROUP BY documentSystemCode) docApp', 'est.estimateMasterID = docApp.documentSystemCode', 'left')
        ->join('srp_erp_mfq_segment mfqsegment','mfqsegment.mfqSegmentID = est.mfqSegmentID','left')
        ->join('srp_erp_currencymaster','est.transactionCurrencyID = srp_erp_currencymaster.currencyID','left')
        ->join('srp_erp_segment segment','segment.segmentID = mfqsegment.segmentID','left')

        ->join('srp_employeesdetails e','e.EIdNo = i.proposalEngineerID', 'left')
        ->join('srp_erp_mfq_job as job','job.estimateMasterID = est.estimateMasterID', 'left')
        ->join('srp_erp_mfq_customer_inquiry_source as sou','sou.sourceID = i.inquirySource', 'left')
        ->join('srp_countrymaster as cou','cou.countryID = i.locationAssigned', 'left');
        $this->datatables->where($where);
        
        $this->datatables->edit_column('year_data', '$1','mfq_rfq_year(documentDate,1)');
        $this->datatables->edit_column('month_data', '$1','mfq_rfq_year(documentDate,2)');
        $this->datatables->add_column('rfq_type', '$1', 'mfq_rfq_type(type)');
        $this->datatables->add_column('delayed', '$1', 'mfq_rfq_delayed(podeliveryDate,documentDate)');
        $this->datatables->add_column('order_status', '$1', 'mfq_rfq_order_status(orderStatus)');
        $this->datatables->add_column('rfq_status', '$1', 'mfq_rfq_status(rfqStatus)');
        $this->datatables->add_column('micoda', '$1', 'mfq_rfq_micoda_operation(locationAssigned)');
        $this->datatables->add_column('docstatus', '$1', 'mfq_rfq_document_status(documentStatus)');
        $this->datatables->add_column('cat', '$1', 'mfq_rfq_category(category)');
        $this->datatables->add_column('estAmount', '<span class="pull-right"><b> $1 </b> $2 </span>', 'transactionCurrency, number_format(estimateValue, transactionCurrencyDecimalPlaces)');
        $this->datatables->add_column('submission_status', '$1', 'mfq_rfq_submission_status(submissionStatus)');
        $this->datatables->add_column('crew', '$1', 'mfq_rfq_job_crew(workProcessID)');
        $this->datatables->add_column('pending_col', '$1', '-');
        echo $this->datatables->generate();
    }

    function fetch_tender_log_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Tender Logs');
        $this->load->database();
        
       //test header
        $header = [  'SL.No', 'Tender No', 'Client', 'Description', 'Category', 'Price', 'RFQ Type', 'Micoda Operation', 'RFQ Originator', 'Source','Estimator','Month','Year','RFQ Status','Status','Order Status','Assigned Date','Submission Date','Actual Submission Date','Submission Status','Alloted Manhours','Actual Manhours','No. of Days Delayed','Total','Rev.','PO Received Date','PO Number','Project Number','Remarks'];
      
        $details = $this->MFQ_CustomerInquiry_model->fetch_tender_log_excel();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:AC4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:AC1");
        $this->excel->getActiveSheet()->mergeCells("A2:AC2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['TenderLog'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:AC4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:AC4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'TenderLog.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_project_progress_entry()
    {
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post("IncidateDateFrom");
        $dateto = $this->input->post("IncidateDateTo");
        $customerCode = $this->input->post("customerCode");
        $proposalengID = $this->input->post("proposalengID");
        $rfqType = $this->input->post("rfqType");
        

        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( job.documentDate >= '" . $datefromconvert . " 00:00:00' AND job.documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        
        $supplier_filter = '';
        if (!empty($customerCode)) {
            $supplier = array($this->input->post('customerCode'));
            $whereIN1 = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND job.mfqCustomerAutoID IN " . $whereIN1;
        }

        $eng_filter = '';
        if (!empty($proposalengID)) {
            $eng = array($this->input->post('proposalengID'));
            $whereIN2 = "( " . join("' , '", $eng) . " )";
            $eng_filter = " AND inq.proposalEngineerID IN " . $whereIN2;
        }
       

        $sSearch=$this->input->post('sSearch');
        $companyid=current_companyID();
        $searches='';
      
        $where = "job.companyID = " . $companyid .$date.$supplier_filter.$eng_filter. "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('IFNULL(jobStatus, 1) AS jobStatus,((discountedPrice * ((100 + IFNULL(totMargin, 0))/100)) * ((100 - IFNULL(totDiscount, 0))/100)) AS estimateValue , srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces, CONCAT(srp_erp_currencymaster.CurrencyCode, " : ") AS transactionCurrency, inq.ciMasterID as ciMasterID,inq.ciCode as ciCode,est.estimateCode,job.workProcessID as workProcessID,job.documentCode,cus.CustomerName,inq.category,est.poNumber,e.Ename2,est.totalSellingPrice,est.documentDate as poDate,job.closedDate,job.documentDate as jobCardDate,job.description as jobdescription,job.closedYN as closedYN,job.confirmedYN as confirmedYN,job.approvedYN as approvedYN,job.qty,dn.deliveryNoteCode');
        $this->datatables->from('srp_erp_mfq_job as job');
        $this->datatables->join('srp_erp_mfq_customermaster cus','cus.mfqCustomerAutoID = job.mfqCustomerAutoID', 'left');

        

        $this->datatables->join('srp_erp_mfq_estimatemaster as est','est.estimateMasterID = job.estimateMasterID', 'left')

        ->join('srp_erp_mfq_status', 'srp_erp_mfq_status.statusID = est.submissionStatus', 'left')
        ->join('(SELECT estimateMasterID,workProcessID FROM srp_erp_mfq_job WHERE (isDeleted IS NULL OR isDeleted != 1) GROUP BY estimateMasterID) jobx', 'jobx.estimateMasterID = est.estimateMasterID', 'left')
        ->join("(SELECT dueDate,srp_erp_mfq_estimatedetail.ciMasterID,srp_erp_mfq_estimatedetail.estimateMasterID,srp_erp_mfq_estimatedetail.estimateDetailID, SUM(discountedPrice) AS discountedPrice FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_estimatedetail.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID  WHERE srp_erp_mfq_estimatedetail.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_mfq_estimatedetail.estimateMasterID) estd", 'estd.estimateMasterID = est.estimateMasterID', 'left')
        ->join('(SELECT MAX(versionLevel),versionOrginID,MAX(estimateMasterID) as estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID) maxl', 'maxl.estimateMasterID = est.estimateMasterID', 'INNER')
        ->join('(SELECT IF(SUM(approvedYN) > 0,1,0) as docApprovedYN,documentSystemCode from srp_erp_documentapproved WHERE documentID="EST" AND companyID='.current_companyID().' GROUP BY documentSystemCode) docApp', 'est.estimateMasterID = docApp.documentSystemCode', 'left')
        ->join('srp_erp_mfq_segment mfqsegment','mfqsegment.mfqSegmentID = est.mfqSegmentID','left')
        ->join('srp_erp_currencymaster','est.transactionCurrencyID = srp_erp_currencymaster.currencyID','left')
        ->join('srp_erp_segment segment','segment.segmentID = mfqsegment.segmentID','left')
        ->join('(SELECT	linkedJobID, MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
	FROM srp_erp_mfq_job
		LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
		LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
	GROUP BY linkedJobID)MainJobStatus', 'MainJobStatus ON MainJobStatus.linkedJobID = job.workProcessID', 'left');

        $this->datatables->join('srp_erp_mfq_customerinquiry as inq','inq.ciMasterID = est.ciMasterID', 'left');
        $this->datatables->join('srp_employeesdetails e','e.EIdNo = inq.proposalEngineerID', 'left');
        $this->datatables->join('srp_erp_mfq_deliverynotedetail d','d.jobID = job.workProcessID', 'left');
        $this->datatables->join('srp_erp_mfq_deliverynote dn','dn.deliverNoteID = d.deliveryNoteID', 'left');
       

        $this->datatables->where($where);
        
        $this->datatables->add_column('pending_col', '$1', '-');
        $this->datatables->edit_column('year_data', '$1','mfq_rfq_year(jobCardDate,1)');
        $this->datatables->add_column('jobStatus', '$1', 'load_main_job_status(jobStatus)');
        $this->datatables->add_column('estAmount', '<span class="pull-right"><b> $1 </b> $2 </span>', 'transactionCurrency, number_format(estimateValue, transactionCurrencyDecimalPlaces)');
        $this->datatables->edit_column('job_process1', '$1','mfq_rfq_load_job_process(workProcessID,1,0)');
        $this->datatables->edit_column('job_process2', '$1','mfq_rfq_load_job_process(workProcessID,1,1)');
        $this->datatables->edit_column('job_process3', '$1','mfq_rfq_load_job_process(workProcessID,2,0)');
        $this->datatables->edit_column('job_process4', '$1','mfq_rfq_load_job_process(workProcessID,2,1)');
        $this->datatables->edit_column('job_process5', '$1','mfq_rfq_load_job_process(workProcessID,3,0)');
        $this->datatables->edit_column('job_process6', '$1','mfq_rfq_load_job_process(workProcessID,3,1)');
        $this->datatables->edit_column('job_process7', '$1','mfq_rfq_load_job_process(workProcessID,4,0)');
        $this->datatables->edit_column('job_process8', '$1','mfq_rfq_load_job_process(workProcessID,5,0)');
        $this->datatables->edit_column('job_process9', '$1','mfq_rfq_load_job_process(workProcessID,6,0)');
        $this->datatables->edit_column('job_process10', '$1','mfq_rfq_load_job_process(workProcessID,7,0)');
        $this->datatables->edit_column('job_process11', '$1','mfq_rfq_load_job_process(workProcessID,8,0)');
        $this->datatables->edit_column('job_process12', '$1','mfq_rfq_load_job_process(workProcessID,8,1)');
        $this->datatables->edit_column('job_process13', '$1','mfq_rfq_load_job_process(workProcessID,9,0)');
        $this->datatables->edit_column('job_process14', '$1','mfq_rfq_load_job_process(workProcessID,10,0)');

        $this->datatables->edit_column('month_data', '$1','mfq_rfq_year(jobCardDate,2)');
        $this->datatables->edit_column('job_status', '$1','mfq_rfq_job_status(closedYN,confirmedYN,approvedYN)');
        echo $this->datatables->generate();
       
    }


    function fetch_project_process_log_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Tender Logs');
        $this->load->database();
        
       //test header
        $header = [ '#','MIC NO','TENDOR NO','ESTIMATE NO','JOB NUM','CLIENT','CATEGORY','CLIENT PO REF. NO','PROJECT FOCAL','PO VALUE','PO / IJOF DELIVERY','COMMITTED COMPLETION DATE'
        ,'ACTUAL COMPLETION DATE','MONTH','YEAR','Description','Current Status','ENGG','REMARK','PR','REMARK2','PO','REMARK3','FAB','NDE','HYDRO','PAINT','FAT','REMARK4','MRB','P&L','Overall Progress Achieved %',
        'TOTAL','PROJECT WITH VARIATION','VARIATION AMOUNT','STATUS OF VARIATION PO','ESTIMATED P&L','RESULT P&L','DELIVERY NOTE','COLLECTION OF GOODS' ];
      
        $details = $this->MFQ_CustomerInquiry_model->fetch_project_process_log_excel();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:AO4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:AO1");
        $this->excel->getActiveSheet()->mergeCells("A2:AO2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['OVERALL PROJECT PROGRESS REPORT'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:AO4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:AO4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'OVERALL PROJECT PROGRESS REPORT.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function customer_inquiry_confirmation()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->customer_inquiry_confirmation());
    }

    function delete_customerInquiryDetail()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->delete_customerInquiryDetail());
    }

    function load_mfq_customerInquiry()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->load_mfq_customerInquiry());
    }

    function load_mfq_customerInquiryDetail()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetail());
    }

    function fetch_customer_inquiry_print()
    {
        $data = array();
        $ciMasterID = $this->input->post('ciMasterID');
        $data["header"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiry();
        $data["itemDetail"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetail();
        $data['logo']=htmlImage;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CI', $ciMasterID);
        $this->load->view('system/mfq/ajax/customer_inquiry_print', $data);
    }

    function fetch_customer_inquiry_prints()
    {
        $ciMasterID =$this->uri->segment(3);
        $data = array();
        $data["id"] = $ciMasterID;
        $data["header"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryprint();
        $data["itemDetail"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetailprint();
        $data['logo']=htmlImage;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CI', $ciMasterID);
        

        $html = $this->load->view('system/mfq/ajax/customer_inquiry_print_page', $data, true);
  
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');  
            $this->pdf->printed($html, 'A4', $dataToCheck);
        }
        
    }

    function fetch_pending_rfq()
    {
        $data = array();

        $data["pending_rfq"] = $this->MFQ_CustomerInquiry_model->fetch_pending_rfq();

        $this->load->view('system/mfq/ajax/fetch_pending_rfq', $data);
    }

    function fetch_total_project_mfq()
    {
        $data = array();

        $data["total"] = $this->MFQ_CustomerInquiry_model->fetch_total_project_mfq();

        $this->load->view('system/mfq/ajax/fetch_total_project', $data);
    }

    function generate_job_total_barchart()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->generate_job_total_barchart());
    }

    function generate_job_total_linechart()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->generate_job_total_linechart());
    }

    function fetch_total_pending_rfq()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->fetch_total_pending_rfq());
    }

    function fetch_total_pending_rfq_bar_chart()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->fetch_total_pending_rfq_bar_chart());
    }


    function load_attachments()
    {
        $data['attachment'] = $this->MFQ_CustomerInquiry_model->load_attachments();
        $data['documentID'] = $this->input->post('documentID');
        $this->load->view('system/mfq/ajax/general_attachment_view', $data);
    }

    function fetch_finish_goods()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->fetch_finish_goods());
    }

    function generateEstimate()
    {
        $master = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiry();
        $detail = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetailOnlyItem();
        $_POST["mfqCustomerAutoID"] = $master["mfqCustomerAutoID"];
        $_POST["documentDate"] = current_format_date();
        $_POST["deliveryDate"] = current_format_date();
        $_POST["description"] = $master["description"];
        $_POST["scopeOfWork"] = null;
        $_POST["technicalDetail"] = null;
        $_POST["currencyID"] = $this->common_data['company_data']['company_default_currencyID'];
        if (!$detail) {
            echo json_encode($this->MFQ_Estimate_model->save_Estimate());
        } else {
            $estimateMasterID = $this->MFQ_Estimate_model->save_Estimate();
            $_POST["estimateMasterID"] = $estimateMasterID[2];
            $_POST["ciMasterID"] = array_column($detail, 'ciMasterID');
            $_POST["mfqItemID"] = array_column($detail, 'mfqItemID');
            $_POST["ciDetailID"] = array_column($detail, 'ciDetailID');
            $_POST["bomMasterID"] = array_column($detail, 'bomMasterID');
            $_POST["expectedQty"] = array_column($detail, 'expectedQty');
            $_POST["estimatedCost"] = array_column($detail, 'estimatedCost');
            echo json_encode($this->MFQ_Estimate_model->save_EstimateDetail());
        }
    }


    function fetch_customer_inquiry_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('DATE_FORMAT(ci.documentDate,\'' . $convertFormat . '\') as documentDate,DATE_FORMAT(ci.dueDate,\'' . $convertFormat . '\') as dueDate,DATE_FORMAT(ci.deliveryDate,\'' . $convertFormat . '\') as deliveryDate,ci.description,ci.paymentTerm, cust.CustomerName as CustomerName,ci.ciMasterID as ciMasterID,ci.ciCode,ci.confirmedYN as confirmedYN,ci.statusID as statusID,ci.dueDate as plannedDate,referenceNo,srp_erp_documentapproved.approvalLevelID as approvalLevelID,srp_erp_documentapproved.approvedYN as approvedYN', false);
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = ci.mfqCustomerAutoID', 'left');
        $this->datatables->from('srp_erp_mfq_customerinquiry ci');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = ci.ciMasterID AND srp_erp_documentapproved.approvalLevelID = ci.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = ci.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'CI');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'CI');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('ci.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->add_column('detail', '<b>Client : </b> $1 <b> <br>Inquiry Date : </b> $2  <br>Client Ref No : </b> $3 <b>',
            'CustomerName,documentDate,referenceNo');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CI",ciMasterID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action(ciMasterID,approvalLevelID,approvedYN,documentApprovedID,"CI")');
        echo $this->datatables->generate();
    }

    function save_customer_inquiry_approval()
    {
        $system_code = trim($this->input->post('ciMasterID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'CI', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('ciMasterID');
                $this->db->where('ciMasterID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_customerinquiry');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('ciMasterID', 'Customer Inquiry ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_CustomerInquiry_model->save_customer_inquiry_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('ciMasterID');
            $this->db->where('ciMasterID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_customerinquiry');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'CI', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Customer Inquiry Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('ciMasterID', 'Customer Inquiry ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->MFQ_CustomerInquiry_model->save_customer_inquiry_approval());
                    }
                }
            }
        }
    }

    function referback_customer_inquiry()
    {
        $ciMasterID = trim($this->input->post('ciMasterID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($ciMasterID, 'CI');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }
    function fetch_mfq_customer_inquiry_history()
    {
        $historyid = $this->input->post('historyid');
        $ciMasterID = $this->input->post('ciMasterID');
        $companyid = current_companyID();
        if($historyid == 6)
        {
          $fieldname = 'statusID';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,CASE WHEN srp_erp_mfq_changehistory.`previousValue` = "1" THEN "Pending" WHEN srp_erp_mfq_changehistory.`previousValue` = "2" THEN "Approved" ENd previousvalue,CASE WHEN srp_erp_mfq_changehistory.`value` = "1" THEN "Pending" WHEN srp_erp_mfq_changehistory.`value` = "2" THEN "Approved" ENd changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';
        }
        if($historyid == 5)
        {
            $fieldname = 'dueDate';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,DATE_FORMAT( previousValue, \'%d-%m-%Y\') as previousvalue,DATE_FORMAT( value, \'%d-%m-%Y\') as changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';
        }
        if($historyid == 4)
        {
            $fieldname = 'deliveryDate';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,DATE_FORMAT( previousValue, \'%d-%m-%Y\') as previousvalue,DATE_FORMAT( value, \'%d-%m-%Y\') as changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';
        }
        if($historyid == 3)
        {
            $fieldname = 'manufacturingType';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,CASE WHEN srp_erp_mfq_changehistory.`previousValue` = "1" THEN "Third Party" WHEN srp_erp_mfq_changehistory.`previousValue` = "2" THEN "In House" ENd previousvalue,CASE WHEN srp_erp_mfq_changehistory.`value` = "1" THEN "Third Party" WHEN srp_erp_mfq_changehistory.`value` = "2" THEN "In House" ENd changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';
        }
        if($historyid == 2)
        {
            $fieldname = 'mfqCustomerAutoID';
            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,customerpreviousval.CustomerName as previousvalue,srp_erp_mfq_customermaster.CustomerName AS changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }
        if($historyid == 7)
        {
            $fieldname = 'referenceNo';
            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,srp_erp_mfq_changehistory.`previousValue` AS previousvalue, srp_erp_mfq_changehistory.`value` AS changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }
        if($historyid == 8)
        {
            $fieldname = 'description';
            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby, srp_erp_mfq_changehistory.`previousValue` AS previousvalue, srp_erp_mfq_changehistory.`value` AS changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }
        if($historyid == 9)
        {
            $fieldname = 'type';
            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,CASE WHEN srp_erp_mfq_changehistory.`previousValue` = "1" THEN "Tender" WHEN srp_erp_mfq_changehistory.`previousValue` = "2" THEN "RFQ" WHEN srp_erp_mfq_changehistory.`previousValue` = "3" THEN "SPC" ENd previousvalue,CASE WHEN srp_erp_mfq_changehistory.`value` = "1" THEN "Tender" WHEN srp_erp_mfq_changehistory.`value` = "2" THEN "RFQ" WHEN srp_erp_mfq_changehistory.`value` = "3" THEN "SPC" ENd changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }
        if($historyid == 30)
        {
            $fieldname = 'segmentID';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,segmentprev.segmentCode AS previousvalue,segmentcurre.segmentCode AS changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';

            $this->datatables->join('srp_erp_mfq_segment mfqseprevious', 'mfqseprevious.mfqSegmentID = srp_erp_mfq_changehistory.previousValue','left');
            $this->datatables->join('srp_erp_mfq_segment mfqsegcurrent', ' mfqsegcurrent.mfqSegmentID = srp_erp_mfq_changehistory.`value`','left');
            $this->datatables->join('srp_erp_segment segmentprev', ' segmentprev.segmentID = mfqseprevious.`segmentID`','left');
            $this->datatables->join('srp_erp_segment segmentcurre', ' segmentcurre.segmentID = mfqsegcurrent.`segmentID`','left');

        }
        if($historyid == 31)
        {
            $fieldname = 'contactPerson';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,previousValue AS previousvalue,value AS changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';

        }
        if($historyid == 32)
        {
            $fieldname = 'customerPhoneNo';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,previousValue AS previousvalue,value AS changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';

        }
        if($historyid == 33)
        {
            $fieldname = 'customerEmail';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,previousValue AS previousvalue,value AS changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';

        }
        if($historyid == 10 || $historyid == 12 || $historyid == 14 || $historyid == 16)
        {
            if($historyid == 10)
            {
                $fieldname = 'engineeringResponsibleEmpID';
            }else if ($historyid == 12)
            {
                $fieldname = 'purchasingResponsibleEmpID';
            }
            else if ($historyid == 14)
            {
                $fieldname = 'productionResponsibleEmpID';
            }
            else if ($historyid == 16)
            {
                $fieldname = 'QAQCResponsibleEmpID';
            }


            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,previous.Ename2 as previousvalue,srp_employeesdetails.Ename2 as changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }
        if($historyid == 11 || $historyid == 13 || $historyid == 15 || $historyid == 17 || $historyid == 21 || $historyid == 22 || $historyid == 23 || $historyid == 24)
        {
            if($historyid == 11)
            {
                $fieldname = 'engineeringEndDate';
            }else if($historyid == 13)
            {
                $fieldname = 'purchasingEndDate';
            }else if($historyid == 15)
            {
                $fieldname = 'productionEndDate';
            }
            else if($historyid == 17)
            {
                $fieldname = 'QAQCEndDate';
            }else if($historyid == 21)
            {
                $fieldname = 'engineeringSubmissionDate';
            }else if($historyid == 22)
            {
                $fieldname = 'purchasingSubmissionDate';
            }
            else if($historyid == 23)
            {
                $fieldname = 'productionSubmissionDate';
            }
            else if($historyid == 24)
            {
                $fieldname = 'QAQCSubmissionDate';
            }

            $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,DATE_FORMAT( previousValue, \'%d-%m-%Y\') as previousvalue,DATE_FORMAT( value, \'%d-%m-%Y\') as changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        }

        $this->datatables->select($select, false);
        $this->datatables->from('srp_erp_mfq_changehistory');
        if($historyid == 2)
        {

            $this->datatables->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_changehistory.value','left');
            $this->datatables->join('srp_erp_mfq_customermaster customerpreviousval', 'customerpreviousval.mfqCustomerAutoID = srp_erp_mfq_changehistory.previousValue','left');
        }
        if($historyid == 10 || $historyid == 12  || $historyid == 14 || $historyid == 16)
        {
            $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_mfq_changehistory.value','left');
            $this->datatables->join('srp_employeesdetails previous', 'previous.EIdNo = srp_erp_mfq_changehistory.previousValue','left');
        }
        $this->datatables->where('documentID', 'CI');
        $this->datatables->where('srp_erp_mfq_changehistory.companyID', $companyid);
        $this->datatables->where('documentMasterID',$ciMasterID);
        $this->datatables->where('fieldName', $fieldname);

        echo $this->datatables->generate();
    }
    function fetch_mfq_customer_inquiry_history_detail()
    {
        $historyid = $this->input->post('historyid');
        $ciMasterID = $this->input->post('ciMasterID');
        $documentDetailID = $this->input->post('detailid');
        $companyid = current_companyID();
        if($historyid == 20)
        {
            $fieldname = 'expectedDeliveryDate';
            $select = 'mfqChangeHistoryID,createdUserName as changedby,DATE_FORMAT( value, \'%d-%m-%Y\') as changedvalue,DATE_FORMAT( createdDateTime, \'%d-%m-%Y\' ) AS changeddate,';
        }
        $this->datatables->select($select, false);
        $this->datatables->from('srp_erp_mfq_changehistory');
        $this->datatables->where('documentID', 'CI');
        $this->datatables->where('companyID', $companyid);
        $this->datatables->where('documentMasterID',$ciMasterID);
        $this->datatables->where('documentDetailID',$documentDetailID);
        $this->datatables->where('fieldName', $fieldname);

        echo $this->datatables->generate();
    }
    function fetchcontactpersonemail()
    {
            echo json_encode($this->MFQ_CustomerInquiry_model->fetchcontactpersonemail());
    }

    function actualsubmissiondate()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->actualsubmissiondate());
    }
    function referback_customer_inquiry_cus()
    {
        $CIMasterID = $this->input->post('ciMasterID');
        $dataUpdate = array(
            'confirmedYN' => 0,
            'confirmedByEmpID' => '',
            'confirmedByName' => '',
            'confirmedDate' => '',
            'approvedYN' => 0,
            'approvedbyEmpID' => '',
            'approvedbyEmpName' => '',
            'approvedDate' => '',
        );

        $this->db->where('ciMasterID', $CIMasterID);
        $this->db->update('srp_erp_mfq_customerinquiry', $dataUpdate);

        echo json_encode(array('s', ' Referred Back Successfully.'));

    }
   /* public function automatedemailmanufacturingcustomerinquiry()
    {

        echo json_encode($this->MFQ_CustomerInquiry_model->automatedemailmanufacturingcustomerinquiry());
    }*/

    function decline_customer_inquiry_quote()
    {
            $this->form_validation->set_rules('ciMasterID', 'ciMasterID', 'trim|required');
            $this->form_validation->set_rules('comment', 'comment', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                echo json_encode($this->MFQ_CustomerInquiry_model->decline_customer_inquiry_quote());
            }
    }

    function fetch_customerInquiry_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Customer Inquiry');
        $this->load->database();

        $header = [  '#', 'INQUIRY CODE', 'INQUIRY DATE', 'CLIENT', 'PROPOSAL ENG', 'SEGMENT', 'REF NO', 'SUBMISSION DATES', '','','', 'AWARDED DATE', 'PO NUMBER', 'JOB', '', 'ESTIMATE VALUE','', '', 'INQUIRY STATUS', 'QUOTE STATUS'];
        $header1 = [ 'ACTUAL SUBMISSION DATE', 'PLANNED SUBMISSION DATE', 'PLANNED DELIVERY DATE', 'ACTUAL DELIVERY DATE'];
        $header2 = [ 'ESTIMATE CODE', 'CURRENCY', 'VALUE'];
        $header3 = [ 'JOB NUMBER', 'JOB STATUS'];
        $this->excel->getActiveSheet()->mergeCells("A4:A5");
        $this->excel->getActiveSheet()->mergeCells("B4:B5");
        $this->excel->getActiveSheet()->mergeCells("C4:C5");
        $this->excel->getActiveSheet()->mergeCells("D4:D5");
        $this->excel->getActiveSheet()->mergeCells("E4:E5");
        $this->excel->getActiveSheet()->mergeCells("F4:F5");
        $this->excel->getActiveSheet()->mergeCells("G4:G5");
        $this->excel->getActiveSheet()->mergeCells("H4:K4");
        $this->excel->getActiveSheet()->mergeCells("L4:L5");
        $this->excel->getActiveSheet()->mergeCells("M4:M5");
        $this->excel->getActiveSheet()->mergeCells("P4:R4");
        $this->excel->getActiveSheet()->mergeCells("S4:S5");
        $this->excel->getActiveSheet()->mergeCells("T4:T5");
        $this->excel->getActiveSheet()->mergeCells("N4:O4");

        $details = $this->MFQ_CustomerInquiry_model->fetch_customerInquiry_details();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:T5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Customer Inquiry'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:T5')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:T5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($header1, null, 'H5');
        $this->excel->getActiveSheet()->fromArray($header2, null, 'P5');
        $this->excel->getActiveSheet()->fromArray($header3, null, 'N5');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

        $filename = 'Customer Inquiry.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function upload_attachment_for_inquiry()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->upload_attachment_for_inquiry());        
    }

    function savestage()
    {
        echo json_encode($this->MFQ_CustomerInquiry_model->savestage());
    }

    function fetch_stage(){
        $this->datatables->select('stage_id, stage_name, weightage,
            (CASE 
                WHEN DefaultType = 1 THEN "PR"
                WHEN DefaultType = 2 THEN "PO"
                ELSE NULL
            END) as DefaultType')
            ->from('srp_erp_mfq_stage');

        $this->datatables->add_column('delete', '$1', 'deleteStage(stage_id)');

        echo $this->datatables->generate();
    }

    function deleteStages(){
        $stageid=$this->input->post('stageid');
        $this->db->where('stage_id', $stageid);
        $result = $this->db->delete('srp_erp_mfq_stage');
        if ($result) {
            echo 'Record deleted successfully!';
        } else {
            echo 'Error while deleting, please contact your system team!';
        }
    }

    function weightage(){
        $stageid = $this->input->post('stageid');
        $this->db->select('checklistDescription');
        $this->db->where('stageID', $stageid);
        $this->db->from('srp_erp_mfq_stage_checklist');
        $query = $this->db->get();
        $result = $query->result();       

        echo json_encode($result);
    }

    function saveweightage(){
        echo json_encode($this->MFQ_CustomerInquiry_model->saveweightage());       

    }

}
