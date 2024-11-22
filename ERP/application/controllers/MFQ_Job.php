<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MFQ_Job extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Job_model');
        $this->load->model('MFQ_Job_Card_model');
        $this->load->model('Inventory_modal');
    }

    function fetch_job()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('filter_dateTo');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('filter_dateFrom');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $filter_customerID = $this->input->post('filter_customerID');
        $filter_DepartmentID = $this->input->post('filter_DepartmentID');
        $filter_mainJobStatus = $this->input->post('filter_mainJobStatus');
        $filter_subJobStatus = $this->input->post('filter_subJobStatus');
        $companyID = current_companyID();
       
        $where_subjob = '';
        $join_subjob = '';

        $where = "srp_erp_mfq_job.companyID = " . current_companyID() . " AND (srp_erp_mfq_job.linkedJobID = 0 OR srp_erp_mfq_job.linkedJobID = '' OR srp_erp_mfq_job.linkedJobID IS NULL)";
     
        if(!empty($datefrom)) {
            $where .= " AND srp_erp_mfq_job.documentDate <= '" . $datefromconvert . "'";
        }
        if(!empty($dateto)) {
            $where .= " AND srp_erp_mfq_job.documentDate >= '" . $datetoconvert . "'";
        }
        if(!empty($filter_customerID)) {
            $where .= " AND srp_erp_mfq_job.mfqCustomerAutoID IN (" . $filter_customerID . ")";
        }
        if(!empty($filter_DepartmentID)) {
            $where .= " AND srp_erp_mfq_job.mfqSegmentID IN (" . $filter_DepartmentID . ")";
        }
        if(!empty($filter_mainJobStatus)) {
            $where .= " AND jobStatus = " . $filter_mainJobStatus;
        }
        
        if(!empty($filter_subJobStatus))
        { 
            if($filter_subJobStatus == 1) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN != 1";
            } else if($filter_subJobStatus == 2) {
                $where_subjob .= " AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL";
            } else if($filter_subJobStatus == 3) {
                $where_subjob .= " AND dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL";
            } else if($filter_subJobStatus == 4) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate < '" . current_date(false) . "'";
            } else if($filter_subJobStatus == 5) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate >= '" . current_date(false) . "'";
            }
            $join_subjob .= '';
    
        }

        // templateDescription,
        $this->datatables->select("IFNULL(jobStatus, 1) AS jobStatus, documentCode,	estimate.poNumber as poNumber,workProcessID,
                                    DATE_FORMAT(srp_erp_mfq_job.documentDate,'" . $convertFormat . "') AS documentDate,job2.percentage as percentage,
                                    (DATE_FORMAT(expectedDeliveryDate,'" . $convertFormat . "')) AS expectedDeliveryDate,srp_erp_mfq_job.description,
                                    IFNULL(DATE_FORMAT(estimate.deliveryDate,'" . $convertFormat . "') ,' - ') AS deliveryDate,
                                    IFNULL(DATE_FORMAT(MainJobStatus.deliveryDate,'" . $convertFormat . "') ,' - ') AS actualDeliveryDate,
                                 
                                    CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,srp_erp_mfq_job.approvedYN,
                                    srp_erp_mfq_job.confirmedYN,isFromEstimate, cust.CustomerName as CustomerName,srp_erp_mfq_job.estimateMasterID as estimateMasterID,
                                    srp_erp_mfq_job.linkedJobID as linkedJobID, srp_erp_mfq_job.isDeleted AS isDeleted", false);
        $this->datatables->from('srp_erp_mfq_job');
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID', 'left');
        $this->datatables->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'left');
        // $this->datatables->join('srp_erp_mfq_templatemaster', 'srp_erp_mfq_templatemaster.templateMasterID = srp_erp_mfq_job.workFlowTemplateID', 'left');
        $this->datatables->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'left');
        // $this->datatables->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID', 'left');
        // $this->datatables->join('srp_erp_mfq_deliverynote', 'srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID', 'left');
        $this->datatables->join('
                                (SELECT (SUM(a.percentage)/COUNT( * )) as percentage,linkedJobID 
                                    FROM srp_erp_mfq_job 
                                    LEFT JOIN (
                                        SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)/COUNT(*)) * 100 as percentage 
                                        FROM srp_erp_mfq_workflowstatus GROUP BY jobID
                                    ) a ON a.jobID = srp_erp_mfq_job.workProcessID  GROUP BY linkedJobID
                                )  job2', 'job2.linkedJobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->datatables->join('
                                (SELECT srp_erp_mfq_deliverynote.deliveryDate, linkedJobID, MIN(CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
                                FROM srp_erp_mfq_job
                                LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
                                LEFT JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                                LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                                GROUP BY linkedJobID)MainJobStatus', 'MainJobStatus ON MainJobStatus.linkedJobID = srp_erp_mfq_job.workProcessID', 'left');
        
               

    
        $this->datatables->where($where);

        $this->datatables->add_column('edit', '$1', 'editJob(workProcessID,confirmedYN,approvedYN,isFromEstimate,estimateMasterID,linkedJobID,isDeleted)');
        $this->datatables->add_column('dates', '<span title="Document Date"> <b> Doc D : </b> $1<br></span><span title="Expected Document Date"> <b> EDD : </b> $3</span>', 'documentDate, deliveryDate, expectedDeliveryDate');
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(percentage)');
        $this->datatables->edit_column('poNumber', '<span class="text-center" style="vertical-align: middle"><strong>PO No :</strong> $1</span>', 'poNumber');
        $this->datatables->add_column('jobStatus', '$1', 'load_main_job_status(jobStatus)');
        $this->datatables->add_column('status', '$1', ' ');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
        //        $this->datatables->generate();
        //        var_dump($this->db->last_query());exit;
    }

    function save_job_header()
    {
        $companyID = current_companyID();
        $templateID = trim($this->input->post('workFlowTemplateID') ?? '');
        $isExist = [];

        $manufacture = getPolicyValues('MANFL', 'All');

        
        $this->form_validation->set_rules('workFlowTemplateID', 'Template', 'trim|required');
        if($this->input->post('fromType') == 'MFQ'){
            $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required');
            $this->form_validation->set_rules('endDate', 'End Date', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            if ($this->input->post('isProcessBased') != 1) {
                $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
            }
        }
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
        if ($this->input->post('type') == 1) {
            $this->form_validation->set_rules('mfqItemID', 'Item', 'trim|required');
        } else {
            $this->form_validation->set_rules('estMfqItemID', 'Item', 'trim|required');
        }
        $this->form_validation->set_rules('mfqWarehouseAutoID', 'Warehouse', 'trim|required');
        // $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');
        $this->form_validation->set_rules('itemUoM', 'UOM', 'trim|required');
        // $this->form_validation->set_rules('search[]', 'Item', 'trim|required');

        $isConfigurationNotExists = $this->db->query("SELECT 
                                                      description 
                                                      FROM
                                                      `srp_erp_mfq_templatedetail`
                                                      where 
                                                      srp_erp_mfq_templatedetail.companyID = $companyID 
                                                      AND templateMasterID = $templateID 
                                                      AND workFlowTemplateID NOT IN (SELECT  mfqProcessID FROM srp_erp_mfq_billofmaterial where companyID = $companyID)")->result_array();

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if(!empty($isConfigurationNotExists) && ($this->input->post('isProcessBased') == 1)){ 
                foreach($isConfigurationNotExists as $val){ 
                 array_push($isExist,$val['description']);
                }
                echo json_encode(array('e','Following process are not assigned with a BOM',$isExist));//bom not configured
              }else { 
                echo json_encode($this->MFQ_Job_model->save_job_header());
              }
           
        }
    }

    function save_job_detail()
    {
        $this->form_validation->set_rules('mfqItemID', 'Item', 'trim|required');
        $this->form_validation->set_rules('qty', 'Template', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->save_job_detail());
        }
    }

    function load_job_header()
    {
        $data = $this->MFQ_Job_model->load_job_header();
        $workflow = $this->MFQ_Job_model->get_jobs();
        if ($workflow) {
            $data['completeStatus'] = 1;
        } else {
            $data['completeStatus'] = 0;
        }
        echo json_encode($data);
    }

    function load_unit_of_measure()
    {
        echo json_encode($this->MFQ_Job_model->load_unit_of_measure());
    }

    function load_mfq_estimate()
    {
        echo json_encode($this->MFQ_Job_model->load_mfq_estimate());
    }

    function get_workflow_status()
    {
        echo json_encode($this->MFQ_Job_model->get_workflow_status());
    }

    function get_jobs()
    {
        echo json_encode($this->MFQ_Job_model->get_jobs());
    }

    function close_job()
    {

        $this->form_validation->set_rules('closedDate', 'Closed Date', 'trim|required');
        $this->form_validation->set_rules('closedComment', 'Comment', 'trim|required');
        $this->form_validation->set_rules('location', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('uomID', 'Unit of Measure', 'trim|required');
        $this->form_validation->set_rules('qty', 'Quantity', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->close_job());
        }
    }

    function close_job_process_based()
    {
        $this->form_validation->set_rules('closedDate', 'Closed Date', 'trim|required');
        /* $this->form_validation->set_rules('closedComment', 'Comment', 'trim|required'); */
        $this->form_validation->set_rules('outputWarehouseAutoID', 'Output Warehouse', 'trim|required');
        $this->form_validation->set_rules('primaryQty', 'Output Qty', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->close_job());
        }
    }

    function fetch_double_entry_job()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID') ?? '');
        $jobCardID = $this->db->select_max("jobcardID")->where('workProcessID',$masterID)->get('srp_erp_mfq_jobcardmaster')->row_array();
        $data['extra'] = $this->MFQ_Job_model->fetch_double_entry_job_test($masterID,$jobCardID['jobcardID']);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function getSemifinishGoods()
    {
        echo json_encode($this->MFQ_Job_model->getSemifinishGoods());
    }

    function fetch_job_approval()
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
            $searches = " ((srp_erp_mfq_job.documentCode Like '%$sSearch%') OR (cust.CustomerName Like '%$sSearch%') OR (itemSystemCode Like '%$sSearch%') OR (DATE_FORMAT(srp_erp_mfq_job.documentDate,'$convertFormat') Like '%$sSearch%') OR (srp_erp_mfq_job.description Like '%$sSearch%')) ";
        }
        $this->datatables->select('srp_erp_mfq_job.documentCode,srp_erp_mfq_job.workProcessID as workProcessID,DATE_FORMAT(srp_erp_mfq_job.documentDate,\'' . $convertFormat . '\') AS documentDate,srp_erp_mfq_job.description as description,CONCAT(itemSystemCode,\' - \',itemDescription) as itemDescription,srp_erp_documentapproved.approvalLevelID as approvalLevelID,documentApprovedID,srp_erp_documentapproved.approvedYN as approvedYN,cust.CustomerName as CustomerName,jcMax.jobcardID as jobcardID,DATE_FORMAT(srp_erp_mfq_job.postingFinanceDate,\'' . $convertFormat . '\') AS postingFinanceDate,appMax.approvalLevel as approvalLevel', false);
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID', 'left');
        $this->datatables->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'left');
        $this->datatables->from('srp_erp_mfq_job');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_mfq_job.workProcessID AND srp_erp_documentapproved.approvalLevelID = srp_erp_mfq_job.currentLevelNo');
        $this->datatables->join('(SELECT MAX(levelNo) as approvalLevel,documentID,companyID
           FROM srp_erp_approvalusers GROUP BY documentID,companyID) appMax', 'appMax.documentID = srp_erp_mfq_job.documentID AND srp_erp_mfq_job.companyID = appMax.companyID','left');
        $this->datatables->join('(SELECT MAX(jobcardID) as jobcardID,workProcessID
           FROM srp_erp_mfq_jobcardmaster GROUP BY workProcessID) jcMax', 'jcMax.workProcessID = srp_erp_mfq_job.workProcessID');
        //$this->datatables->join('srp_erp_mfq_jobcardmaster', 'jcMax.jobcardID = srp_erp_mfq_jobcardmaster.jobcardID');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_mfq_job.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'JOB');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'JOB');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        if(!empty($searches)) {
            $this->datatables->where($searches);
        }
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_mfq_job.companyID', $companyID);
        // $this->datatables->where('srp_erp_mfq_job.linkedJobID IS NOT NULL');
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->add_column('detail', '<b>Client : </b> $1 <b> <br>Job Date : </b> $2  <b><br>Item : </b> $3 <b><br>Description : </b> $4',
            'CustomerName,documentDate,itemDescription,description');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"JOB",workProcessID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action(workProcessID,approvalLevelID,approvedYN,documentApprovedID,"JOB",jobcardID,approvalLevel,postingFinanceDate)');
        echo $this->datatables->generate();
    }

    function save_job_approval()
    {
        $system_code = trim($this->input->post('workProcessID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'JOB', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('workProcessID');
                $this->db->where('workProcessID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_job');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('workProcessID', 'Job ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_model->save_job_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('workProcessID');
            $this->db->where('workProcessID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_job');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'JOB', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Job Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('workProcessID', 'Job ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_model->save_job_approval());
                    }
                }
            }
        }
    }

    function referback_job()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($workProcessID, 'JOB');
        if ($status == 1) {
            $this->db->select('workProcessFlowID');
            $this->db->from('srp_erp_mfq_workflowstatus');
            $this->db->where('jobID', $workProcessID);
            $this->db->order_by('workProcessFlowID', 'DESC');
            $this->db->limit(1);
            $detail = $this->db->get()->row_array();

            $this->db->set('status', 0);
            $this->db->where('workProcessFlowID', $detail['workProcessFlowID']);
            $this->db->where('jobID', $workProcessID);
            $this->db->update('srp_erp_mfq_workflowstatus');

            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }


    function fetch_job_print()
    {
        $data = array();
        $jobcard = $this->db->query("SELECT * FROM srp_erp_mfq_jobcardmaster INNER JOIN (SELECT MAX( jobCardID ) AS jobCardID FROM srp_erp_mfq_jobcardmaster WHERE workProcessID = " . $this->input->post('workProcessID') . ") job ON job.jobCardID = srp_erp_mfq_jobcardmaster.jobCardID")->row_array();

        $job = $this->db->query("SELECT * FROM srp_erp_mfq_templatedetail WHERE templateDetailID = " . $jobcard["templateDetailID"])->row_array();

        $_POST["jobCardID"] = $jobcard["jobCardID"];
        $_POST["workFlowID"] = $jobcard["workFlowID"];
        $_POST["templateDetailID"] = $jobcard["templateDetailID"];
        $_POST["type"] = 2;
        $_POST["templateMasterID"] = $job["templateMasterID"];
        $_POST["linkworkFlow"] = $job["linkWorkFlow"];

        $data["workProcessID"] = $this->input->post('workProcessID');
        $data["type"] = $this->input->post('type');
        $data["material"] = $this->MFQ_Job_Card_model->fetch_jobcard_material_consumption();
        $data["overhead"] = $this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost();
        $data["labourTask"] = $this->MFQ_Job_Card_model->fetch_jobcard_labour_task();
        $data["machine"] = $this->MFQ_Job_Card_model->fetch_jobcard_machine_cost();
        $data["jobheader"] = $this->MFQ_Job_model->load_job_header();
        $data["jobCardID"] = $this->input->post('jobCardID');
        $data["workFlowID"] = $this->input->post('workFlowID');
        $data["templateDetailID"] = $this->input->post('templateDetailID');
        $data["linkworkFlow"] = $this->input->post('linkworkFlow');
        $data["templateMasterID"] = $this->input->post('templateMasterID');

        if ($this->input->post('linkworkFlow')) {
            $data["prevJobCard"] = get_prev_job_card($this->input->post('workProcessID'), $this->input->post('workFlowID'), $this->input->post('linkworkFlow'), $this->input->post('templateDetailID'), $this->input->post('templateMasterID'));
        }
        $data["jobcardheader"] = get_job_cardID($this->input->post('workProcessID'), $this->input->post('workFlowID'), $this->input->post('templateDetailID'));
        $html = $this->load->view('system/mfq/ajax/job_card_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', 1, 'L');
        }
    }

    function fetch_job_approval_print()
    {
        $data = array();

        $data["workProcessID"] = $this->input->post('workProcessID');
        $data["material"] = $this->MFQ_Job_Card_model->fetch_jobcard_material_consumption();
        $data["packaging"] = $this->MFQ_Job_Card_model->fetch_jobcard_material_consumption(1);
        $data["overhead"] = $this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost();
        $data["thirdparty"] = $this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost(2);
        $data["labourTask"] = $this->MFQ_Job_Card_model->fetch_jobcard_labour_task();
        $data["machine"] = $this->MFQ_Job_Card_model->fetch_jobcard_machine_cost();
        $data["jobheader"] = $this->MFQ_Job_model->load_job_header();
        $data['estimateTotal'] = $this->MFQ_Job_model->get_bill_of_material_detail();

        // print_r($data['estimateTotal']); exit;

        $html = $this->load->view('system/mfq/ajax/job_card_print_approval', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', 1, 'L');
        }
    }

    function fetch_checklist(){

        $data = array();
        $data["workProcessID"] = $this->input->post('workProcessID');
        $data["checklist"] = $this->MFQ_Job_Card_model->fetch_checklist();

        $html = $this->load->view('system/mfq/ajax/job_item_checklist', $data, true);
        echo $html;
    }

    function get_mfq_job_drilldown()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $where = '';
        if($status == 1) {
            $where .= " AND srp_erp_mfq_job.confirmedYN != 1";
        } else if($status == 2) {
            $where .= " AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL";
        } else if($status == 3) {
            $where .= " AND dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL";
        } else if($status == 4) {
            $where .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate < '" . current_date(false) . "'";
        } else if($status == 5) {
            $where .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate >= '" . current_date(false) . "'";
        }
        $data["workProcessID"] = $workProcessID;
        $data['mfqJobDetail'] = $this->db->query("SELECT documentCode,varianceYN,workProcessID, DATE_FORMAT(srp_erp_mfq_job.documentDate, '" . $convertFormat . "') AS documentDate, IFNULL(DATE_FORMAT(srp_erp_mfq_job.closedDate, '" . $convertFormat . "'), ' - ') AS closedDate, srp_erp_mfq_job.description, templateDescription, ws.percentage as percentage, CONCAT(itemSystemCode, ' - ', itemDescription) as itemDescription, srp_erp_mfq_job.approvedYN, srp_erp_mfq_job.confirmedYN,isFromEstimate,linkedJobID,srp_erp_mfq_job.estimateMasterID,cust.CustomerName, dnQty.deliveryNoteID, srp_erp_mfq_customerinvoicemaster.invoiceAutoID, IFNULL(DATE_FORMAT(expectedDeliveryDate,'" . $convertFormat . "') ,' - ') AS expectedDeliveryDate,
            IFNULL(DATE_FORMAT(estimate.deliveryDate,'" . $convertFormat . "') ,' - ') AS deliveryDate,IFNULL(DATE_FORMAT(dnQty.deliveryDate,'" . $convertFormat . "') ,' - ') AS actualDeliveryDate, qty
            FROM `srp_erp_mfq_job` 
            LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID` 
            LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID` 
            LEFT JOIN (SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus GROUP BY jobID) ws ON `ws`.`jobID` = `srp_erp_mfq_job`.`workProcessID` 
            LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID
            LEFT JOIN (
                        SELECT SUM(deliveredQty) AS deliveredQty, srp_erp_mfq_deliverynotedetail.jobID, deliveryNoteID, MAX(deliveryDate ) as deliveryDate
                            FROM srp_erp_mfq_deliverynotedetail
                            JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
                            WHERE deletedYn != 1 
                            GROUP BY srp_erp_mfq_deliverynotedetail.jobID
            )dnQty ON dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty
            LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID= dnQty.deliveryNoteID
            LEFT JOIN srp_erp_mfq_estimatemaster estimate ON estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID
            WHERE `srp_erp_mfq_job`.`linkedJobID` = {$workProcessID} AND `srp_erp_mfq_job`.`companyID` = {$companyID} {$where} ORDER BY `workProcessID`")->result_array();
        //var_dump($this->db->last_query());exit;
        $this->load->view('system/mfq/ajax/job_drilldown_view', $data);
    }

    function get_mfq_job_drilldown2()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $data["workProcessID"] = $workProcessID;
        $data['mfqJobDetail'] = $this->db->query("SELECT documentCode, workProcessID, DATE_FORMAT(documentDate, '%d-%m-%Y') AS documentDate, description, templateDescription, ws.percentage as percentage, CONCAT(itemSystemCode, ' - ', itemDescription) as itemDescription, approvedYN, confirmedYN,isFromEstimate,linkedJobID,estimateMasterID,cust.CustomerName FROM `srp_erp_mfq_job` LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID` LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID` LEFT JOIN (SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus GROUP BY jobID) ws ON `ws`.`jobID` = `srp_erp_mfq_job`.`workProcessID` LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID WHERE `srp_erp_mfq_job`.`linkedJobID` = {$workProcessID} AND `srp_erp_mfq_job`.`companyID` = {$companyID} ORDER BY `workProcessID`")->result_array();

        $this->load->view('system/mfq/ajax/job_drilldown2_view', $data);

    }

    function save_sub_job()
    {
        echo json_encode($this->MFQ_Job_model->save_sub_job());
    }

    function load_route_card()
    {
        echo json_encode($this->MFQ_Job_model->load_route_card());
    }

    function update_checklist_response(){
        echo json_encode($this->MFQ_Job_model->update_checklist_response());
    }

    function save_route_card()
    {
        $this->form_validation->set_rules('workProcessFlowID', 'Work Process Flow', 'trim|required');
        $this->form_validation->set_rules('process[]', 'Process', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->save_route_card());
        }
    }

    function delete_routecard()
    {
        echo json_encode($this->MFQ_Job_model->delete_routecard());
    }

    function load_material_consumption_qty()
    {
        echo json_encode($this->MFQ_Job_model->load_material_consumption_qty());
    }

    function load_usage_history()
    {
        echo json_encode($this->MFQ_Job_model->load_usage_history());
    }

    function save_usage_qty()
    {
        //$this->form_validation->set_rules('qtyUsage[]', 'Qty', 'trim|required');
        //if ($this->form_validation->run() == FALSE) {
        //echo json_encode(array('e', validation_errors()));
        //} else {
        echo json_encode($this->MFQ_Job_model->save_usage_qty());
        //}
    }

    function get_material_request()
    {
        $data["master"] = $this->Inventory_modal->load_material_request_header();
        $data['detail'] = $this->Inventory_modal->fetch_material_request_detail();
        $data['location'] = load_location_drop();
        $html = $this->load->view('system/mfq/ajax/material_request_view', $data, true);
        echo $html;
    }

    function save_material_request(){
        $this->form_validation->set_rules('wareHouseAutoID', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('qtyRequested[]', 'Qty Requested', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->save_material_request());
        }
    }

    function fetch_usage_history(){
        echo json_encode($this->MFQ_Job_model->fetch_usage_history());
    }

    function delete_job(){
        echo json_encode($this->MFQ_Job_model->delete_job());
    }

    function delete_sub_job(){
        echo json_encode($this->MFQ_Job_model->delete_sub_job());
    }

    function get_job_pulled_documents()
    {
        $data['details'] = $this->MFQ_Job_model->get_job_pulled_documents();
        $data['type'] = 'html';
        $html = $this->load->view('system/mfq/ajax/job_pulled_document_view', $data, true);
        echo $html;
    }

    function fetch_mfq_job_inquiry_history()
    {
        $detID = $this->input->post('detID');
        $field = $this->input->post('field');
        $docID = $this->input->post('docID');
        $masterID = $this->input->post('masterID');
        $type = $this->input->post('type');
        $companyid = current_companyID();
        
      
        // if($type==0){

        // }
        

        $select = '	mfqChangeHistoryID, srp_erp_mfq_changehistory.createdUserName AS changedby,DATE_FORMAT( previousValue, \'%d-%m-%Y\') as previousvalue,DATE_FORMAT( value, \'%d-%m-%Y\') as changedvalue, DATE_FORMAT(srp_erp_mfq_changehistory.createdDateTime, \'%d-%m-%Y\' ) AS changeddate ,';
        

        $this->datatables->select($select, false);
        $this->datatables->from('srp_erp_mfq_changehistory');
     
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_mfq_changehistory.value','left');
        $this->datatables->join('srp_employeesdetails previous', 'previous.EIdNo = srp_erp_mfq_changehistory.previousValue','left');
        
        $this->datatables->where('documentID', 'JOB');
        $this->datatables->where('srp_erp_mfq_changehistory.companyID', $companyid);
        $this->datatables->where('documentMasterID',$masterID);
        $this->datatables->where('fieldName', $field);
        $this->datatables->where('documentDetailID', $detID);

        echo $this->datatables->generate();
    }

    function fetch_job_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('JOB');
        $this->load->database();

        $header = ['#', 'JOB NUMBER', 'DOCUMENT DATE', 'EXPECTED DELIVERY DATE', 'CUSTOMER', 'PO NUMBER', 'DESCRIPTION', 'JOB STATUS', 'STATUS', 'PERCENTAGE'];

        $details = $this->MFQ_Job_model->fetch_job_details();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['JOB'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'JOB.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function save_document_setup()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->save_document_setup());
        }
    }

    function fetch_document_setup()
    {
        $this->datatables->select("docSetupID, description, isMandatory, isActive", false)
            ->from('srp_erp_mfq_documentsetup');
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="edit_document_setup($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_document_setup($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a>', 'docSetupID');
        $this->datatables->add_column('isMandatory', '$1', 'status_yes_no(isMandatory)');
        $this->datatables->add_column('isActive', '$1', 'status_yes_no(isActive)');
        echo $this->datatables->generate();
    }

    function load_document_setup()
    {
        echo json_encode($this->MFQ_Job_model->load_document_setup());
    }

    function delete_document_setup()
    {
        echo json_encode($this->MFQ_Job_model->delete_document_setup());
    }

    function do_upload_aws_S3_job()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
        $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $this->load->library('s3');
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if(empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }

            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */
            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['documentSubID'] = trim($this->input->post('att_docType') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
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

            $companyID = current_companyID();
            $this->db->select('srp_erp_customerinvoicemaster.invoiceAutoID AS erpInvoiceAutoID, srp_erp_mfq_customerinvoicemaster.invoiceAutoID AS mfqInvoiceAutoID');
            $this->db->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID', 'LEFT');
            $this->db->join('srp_erp_mfq_customerinvoicemaster', 'srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID', 'LEFT');
            $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.mfqInvoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID', 'LEFT');
            $this->db->where('workProcessID', trim($this->input->post('documentSystemCode') ?? ''));
            $this->db->where('srp_erp_mfq_customerinvoicemaster.confirmedYN', 1);
            $this->db->where('srp_erp_mfq_job.companyID', $companyID);
            $invoiceIDs = $this->db->get('srp_erp_mfq_job')->row_array();

            if($invoiceIDs) {
                if($invoiceIDs['erpInvoiceAutoID']) {
                    $data['documentID'] = 'CINV';
                    $data['documentSystemCode'] = $invoiceIDs['erpInvoiceAutoID'];
                    $data['documentSubID'] = $invoiceIDs['mfqInvoiceAutoID'];
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
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
                }
                if($invoiceIDs['mfqInvoiceAutoID']) {
                    $data['documentID'] = 'MCINV_JOB';
                    $data['documentSystemCode'] = trim($invoiceIDs['mfqInvoiceAutoID'] ?? '');
                    $data['documentSubID'] = trim($this->input->post('documentSystemCode') ?? '');
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
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
                }
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload));
            }
        }
    }

    function fetch_attachments()
    {
        $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
        $this->db->where('srp_erp_documentattachments.documentID', $this->input->post('documentID'));
        $this->db->where('srp_erp_documentattachments.companyID', $this->common_data['company_data']['company_id']);
        $this->db->join('srp_erp_mfq_documentsetup', 'srp_erp_mfq_documentsetup.docSetupID = srp_erp_documentattachments.documentSubID', 'LEFT');
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        $confirmedYN = $this->input->post('confirmedYN');
        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
                //$link = generate_encrypt_link_only($burl); // old attachment
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                    $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['description'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
                } else {
                    $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['description'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                }
                $x++;

            }
        } else {
            $result = '<tr class="danger"><td colspan="6" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);
    }

    function itemLedger_update_DN()
    {
        $deliveryNoteID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('deliveryNoteID') ?? '');
        echo json_encode($this->MFQ_Job_model->itemLedger_update_DN($deliveryNoteID));
    }

    function usage_qty_update()
    {
        $jobID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('jobID') ?? '');
        echo json_encode($this->MFQ_Job_model->usage_qty_update($jobID));
    }

    function job_close_ledger_entries()
    {
        $jobID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('jobID') ?? '');
        echo json_encode($this->MFQ_Job_model->job_close_ledger_entries($jobID));
    }

    function get_item_wise_template()
    {
        echo json_encode($this->MFQ_Job_model->get_item_wise_template());
    }

    function fetch_job_process_based()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('filter_dateTo');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('filter_dateFrom');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $filter_customerID = $this->input->post('filter_customerID');
        $filter_DepartmentID = $this->input->post('filter_DepartmentID');
        $filter_mainJobStatus = $this->input->post('filter_mainJobStatus');
        $filter_subJobStatus = $this->input->post('filter_subJobStatus');
        $companyID = current_companyID();
       
        $where_subjob = '';
        $join_subjob = '';

        $where = "srp_erp_mfq_job.companyID = " . current_companyID() . " AND (srp_erp_mfq_job.linkedJobID = 0 OR srp_erp_mfq_job.linkedJobID = '' OR srp_erp_mfq_job.linkedJobID IS NULL)";
     
        if(!empty($datefrom)) {
            $where .= " AND srp_erp_mfq_job.documentDate <= '" . $datefromconvert . "'";
        }
        if(!empty($dateto)) {
            $where .= " AND srp_erp_mfq_job.documentDate >= '" . $datetoconvert . "'";
        }
        if(!empty($filter_customerID)) {
            $where .= " AND srp_erp_mfq_job.mfqCustomerAutoID IN (" . $filter_customerID . ")";
        }
        if(!empty($filter_DepartmentID)) {
            $where .= " AND srp_erp_mfq_job.mfqSegmentID IN (" . $filter_DepartmentID . ")";
        }
        if(!empty($filter_mainJobStatus)) {
            $where .= " AND jobStatus = " . $filter_mainJobStatus;
        }
        
        if(!empty($filter_subJobStatus))
        { 
            if($filter_subJobStatus == 1) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN != 1";
            } else if($filter_subJobStatus == 2) {
                $where_subjob .= " AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL";
            } else if($filter_subJobStatus == 3) {
                $where_subjob .= " AND dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL";
            } else if($filter_subJobStatus == 4) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate < '" . current_date(false) . "'";
            } else if($filter_subJobStatus == 5) {
                $where_subjob .= " AND srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate >= '" . current_date(false) . "'";
            }
            $join_subjob .= '';
        }

        $this->datatables->select("IFNULL(jobStatus, 1) AS jobStatus, documentCode, if(srp_erp_mfq_job.estimateMasterID, CONCAT('PO No :', estimate.poNumber), itemDescription) AS poNumber,workProcessID,DATE_FORMAT(srp_erp_mfq_job.documentDate,'" . $convertFormat . "') AS documentDate,(DATE_FORMAT(expectedDeliveryDate,'" . $convertFormat . "')) AS expectedDeliveryDate,IFNULL(DATE_FORMAT(estimate.deliveryDate,'" . $convertFormat . "') ,' - ') AS deliveryDate,IFNULL(DATE_FORMAT(srp_erp_mfq_deliverynote.deliveryDate,'" . $convertFormat . "') ,' - ') AS actualDeliveryDate,srp_erp_mfq_job.description,templateDescription,job2.percentage as percentage,CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,srp_erp_mfq_job.approvedYN as approvedYN,srp_erp_mfq_job.confirmedYN as confirmedYN,isFromEstimate,cust.CustomerName as CustomerName,srp_erp_mfq_job.estimateMasterID as estimateMasterID,srp_erp_mfq_job.linkedJobID as linkedJobID, srp_erp_mfq_job.isDeleted AS isDeleted,
        srp_erp_mfq_deliverynote.deliverNoteID AS deliverNoteID,
        srp_erp_mfq_customerinvoicemaster.invoiceAutoID AS invoiceAutoID,srp_erp_mfq_templatemaster.templateDescription AS TemplateDesc,
        DATE( expectedDeliveryDate ) AS DeliveryDate", false);
        $this->datatables->from('srp_erp_mfq_job');
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID', 'left');
        $this->datatables->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'left');
        $this->datatables->join('srp_erp_mfq_templatemaster', 'srp_erp_mfq_templatemaster.templateMasterID = srp_erp_mfq_job.workFlowTemplateID', 'left');
        $this->datatables->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'left');
        $this->datatables->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->datatables->join('srp_erp_mfq_deliverynote', 'srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID', 'left');
        $this->datatables->join('(SELECT
        IFNULL((completed.completedCount/COUNT(srp_erp_mfq_workflowstatus.workProcessFlowID)) * 100,0) as percentage,
        srp_erp_mfq_workflowstatus.jobID
    FROM
        `srp_erp_mfq_workflowstatus`
        LEFT JOIN (SELECT 
                            COUNT(workProcessFlowID) as completedCount,
                            jobID
                            FROM
                            srp_erp_mfq_workflowstatus
                            WHERE 
                            status = 1 
                            GROUP BY 
                            jobID
                            ) completed on completed.jobID = srp_erp_mfq_workflowstatus.jobID
        GROUP BY
        srp_erp_mfq_workflowstatus.jobID)  job2', 'job2.jobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->datatables->join('(SELECT	linkedJobID, MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
                        FROM srp_erp_mfq_job
                            LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
                            LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                        GROUP BY linkedJobID)MainJobStatus', 'MainJobStatus ON MainJobStatus.linkedJobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->datatables->join('srp_erp_mfq_customerinvoicemaster ', 'srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID', 'left');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '$1', 'editJob_process_based(workProcessID,confirmedYN,approvedYN,isFromEstimate,estimateMasterID,linkedJobID,isDeleted,documentCode)');
        $this->datatables->add_column('dates', '<span title="Document Date"> <b> Doc D : </b> $1<br></span><span title="Expected Document Date"> <b> EDD : </b> $3</span>', 'documentDate, deliveryDate, expectedDeliveryDate');
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(percentage)');
        // $this->datatables->edit_column('poNumber', '<span class="text-center" style="vertical-align: middle"><strong>PO No :</strong> $1</span>', 'poNumber');
        $this->datatables->add_column('jobStatus', '$1', 'load_main_job_status(jobStatus)');
        //$this->datatables->add_column('jobStatus', '$1', 'load_main_job_status(jobStatus)');
        $this->datatables->add_column('jobStatus', '$1', 'get_job_status(confirmedYN, deliverNoteID,invoiceAutoID,DeliveryDate)');
        $this->datatables->add_column('status', '$1', ' ');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_double_entry_job_process_based()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID') ?? '');
        $jobCardID = $this->db->select("jobcardID")->where('workProcessID',$masterID)->get('srp_erp_mfq_jobcardmaster')->result_array();
        $jobCardID_str = join(',', array_column($jobCardID, 'jobcardID'));
        $data['extra'] = $this->MFQ_Job_model->fetch_double_entry_job_process_based($masterID,$jobCardID_str);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_job_item_units()
    {
        echo json_encode($this->MFQ_Job_model->fetch_job_item_units());
    }

    function load_open_jobCard_dropdown()
    {
        echo json_encode($this->MFQ_Job_model->load_open_jobCard_dropdown());
    }

    function load_material_consumption_qty_process_based()
    {
        echo json_encode($this->MFQ_Job_model->load_material_consumption_qty_process_based());
    }

    function fetch_job_approval_process_based()
    {
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $searches = " ((srp_erp_mfq_job.documentCode Like '%$sSearch%') OR (cust.CustomerName Like '%$sSearch%') OR (itemSystemCode Like '%$sSearch%') OR (DATE_FORMAT(srp_erp_mfq_job.documentDate,'$convertFormat') Like '%$sSearch%') OR (srp_erp_mfq_job.description Like '%$sSearch%')) ";
        }
        $this->datatables->select('srp_erp_mfq_job.documentCode,srp_erp_mfq_job.workProcessID as workProcessID,DATE_FORMAT(srp_erp_mfq_job.documentDate,\'' . $convertFormat . '\') AS documentDate,srp_erp_mfq_job.description as description,CONCAT(itemSystemCode,\' - \',itemDescription) as itemDescription,srp_erp_documentapproved.approvalLevelID as approvalLevelID,documentApprovedID,srp_erp_documentapproved.approvedYN as approvedYN,cust.CustomerName as CustomerName,jcMax.jobcardID as jobcardID,DATE_FORMAT(srp_erp_mfq_job.postingFinanceDate,\'' . $convertFormat . '\') AS postingFinanceDate,appMax.approvalLevel as approvalLevel', false);
        $this->datatables->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID', 'left');
        $this->datatables->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'left');
        $this->datatables->from('srp_erp_mfq_job');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_mfq_job.workProcessID AND srp_erp_documentapproved.approvalLevelID = srp_erp_mfq_job.currentLevelNo');
        $this->datatables->join('(SELECT MAX(levelNo) as approvalLevel,documentID,companyID
           FROM srp_erp_approvalusers GROUP BY documentID,companyID) appMax', 'appMax.documentID = srp_erp_mfq_job.documentID AND srp_erp_mfq_job.companyID = appMax.companyID','left');
        $this->datatables->join('(SELECT MAX(jobcardID) as jobcardID,workProcessID
           FROM srp_erp_mfq_jobcardmaster GROUP BY workProcessID) jcMax', 'jcMax.workProcessID = srp_erp_mfq_job.workProcessID');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_mfq_job.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'JOB');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'JOB');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        if(!empty($searches)) {
            $this->datatables->where($searches);
        }
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_mfq_job.companyID', $companyID);
        $this->datatables->where('(srp_erp_mfq_job.linkedJobID IS NOT NULL  OR (srp_erp_mfq_job.linkedJobID IS NULL AND estimateMasterID IS NULL))');
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->add_column('detail', '<b>Client : </b> $1 <b> <br>Job Date : </b> $2  <b><br>Item : </b> $3 <b><br>Description : </b> $4',
            'CustomerName,documentDate,itemDescription,description');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"JOB",workProcessID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action(workProcessID,approvalLevelID,approvedYN,documentApprovedID,"JOB",jobcardID,approvalLevel,postingFinanceDate)');
        echo $this->datatables->generate();
    }

    function fetch_job_approval_print_process_based()
    {
        $data = array();

        $data["workProcessID"] = $this->input->post('workProcessID');
        $data["material"] = $this->MFQ_Job_Card_model->fetch_jobcard_material_consumption();
        $data["overhead"] = $this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost();
        $data["labourTask"] = $this->MFQ_Job_Card_model->fetch_jobcard_labour_task();
        $data["machine"] = $this->MFQ_Job_Card_model->fetch_jobcard_machine_cost();
        $data["jobheader"] = $this->MFQ_Job_model->load_job_header();
        $html = $this->load->view('system/mfq/ajax/job_card_print_approval', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', 1, 'L');
        }
    }

    function save_job_approval_process_based()
    {
        $system_code = trim($this->input->post('workProcessID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'JOB', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('workProcessID');
                $this->db->where('workProcessID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_job');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('workProcessID', 'Job ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_model->save_job_approval_process_based());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('workProcessID');
            $this->db->where('workProcessID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_job');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'JOB', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Job Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('workProcessID', 'Job ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_model->save_job_approval_process_based());
                    }
                }
            }
        }
    }

    function insert_sub_item_configuration(){
        $this->form_validation->set_rules('outputWarehouseAutoID', 'Output Warehouse', 'trim|required');
        if($this->input->post('subItemUOM') != 2) {
            $this->form_validation->set_rules('outputQty', 'Output Qty', 'trim|required');
        }
        if($this->input->post('subItemUOM') == 2) {
            $this->form_validation->set_rules('secondaryQty', 'secondary Qty', 'trim|required');
        }
        $this->form_validation->set_rules('workProcessID', 'Job ID', 'trim|required');
        //$this->form_validation->set_rules('secondaryQty', 'Secondary Qty', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
        /*     $error_message = validation_errors();
            echo warning_message($error_message); */
            echo json_encode(array('e', validation_errors()));
        } else {
            $output = $this->MFQ_Job_model->insert_sub_item_configuration();
            $data['attributes'] = fetch_company_assigned_attributes();
            $data['grvDetailsID'] = null;
            $data['documentID'] = trim($this->input->post('workProcessID') ?? '');
            $data['itemMasterSubTemp'] = $output;

            $viewDetail['view'] = $this->load->view('system/grv/sub-views/ajax-load-sub-item-master-tmp', $data,true);
            $viewDetail['msg'] = 's';
          
            echo json_encode($viewDetail); 
           // echo json_encode(array('s', 'sub item.'));

        }
    }

    function referback_job_processBased()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($workProcessID, 'JOB');


        $this->db->where('receivedDocumentAutoID', trim($workProcessID));
        $this->db->where('receivedDocumentID', 'JOB');
        $this->db->where('receivedDocumentDetailID IS NULL');
        $this->db->delete('srp_erp_itemmaster_subtemp');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }
    function get_workflowDefaultItem(){ 
        $companyID = current_companyID();
        $workFlowID = $this->input->post('workFlowID');
        $query = $this->db->query("SELECT
        srp_erp_mfq_workflowtemplateitems.mfqItemID,
        CONCAT(srp_erp_mfq_itemmaster.itemSystemCode,'-',srp_erp_mfq_itemmaster.itemName)  as item,
        srp_erp_mfq_itemmaster.defaultUnitOfMeasure,
            srp_erp_mfq_itemmaster.defaultUnitOfMeasureID
            FROM
                `srp_erp_mfq_workflowtemplateitems`
                LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_workflowtemplateitems.mfqItemID
                where 
                srp_erp_mfq_workflowtemplateitems.companyID = $companyID 
                AND workFlowTemplateID = $workFlowID ")->row_array();
            echo json_encode($query);
    }

    function save_usage_qty_job()
    {
        echo json_encode($this->MFQ_Job_model->save_usage_qty_job());
    }

    function load_statusbased_mfq_customer()
    {
        $customer_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $status_filter = '';
        $companyID = current_companyID();
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND (isActive = 0 or isActive is null ) ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
         
        $customer= $this->db->query("SELECT mfqCustomerAutoID,CustomerName,CustomerSystemCode,CustomerCountry,CompanyCode 
            FROM `srp_erp_mfq_customermaster` WHERE `companyID` = $companyID  $status_filter")->result_array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['mfqCustomerAutoID'] ?? '')] = (trim($row['CustomerSystemCode'] ?? '') ? trim($row['CustomerSystemCode'] ?? '') . ' | ' : '') . trim($row['CustomerName'] ?? '') . (trim($row['CustomerCountry'] ?? '') ? ' | ' . trim($row['CustomerCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('filter_customerID[]', $customer_arr, '', 'class="form-control" multiple="multiple" id="filter_customerID" onchange="oTable.draw()"'); 
    }

    function update_mfq_remarks()
    {
        echo json_encode($this->MFQ_Job_model->update_mfq_remarks());
    }

    function update_mfq_progress()
    {
        echo json_encode($this->MFQ_Job_model->update_mfq_progress());
    }

    function save_mfq_job_wise_stage()
    {

        $this->form_validation->set_rules('mfq_stage_id', 'Stage', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
        
            echo json_encode(array('e', validation_errors()));
            
        } else {

            echo json_encode($this->MFQ_Job_model->save_mfq_job_wise_stage());
        }
        
    }

    function get_workprocess_stage_update(){

       $workProcessID = $this->input->post("workProcessID");
       $documentID = $this->input->post("documentID");
       $templateDetailID = $this->input->post("templateDetailID");
       $data = array();

       $data['mfq_stage'] = get_mfq_stage($workProcessID,$templateDetailID);
       $data['documentID'] = $documentID;
       $data['workProcessID'] = $workProcessID;
       $data['templateDetailID'] = $templateDetailID;

       $this->load->view('system/mfq/ajax/mfq_job_stage_tbl', $data);

    }

    function update_stage_assignee(){
        echo json_encode($this->MFQ_Job_model->update_stage_assignee());
    }

    function update_stage_value(){
        echo json_encode($this->MFQ_Job_model->update_stage_value());
    }

    function create_variance_documents(){
        echo json_encode($this->MFQ_Job_model->create_variance_documents());
    }

    function load_check_list(){

        $workProcessID = $this->input->post("workProcessID");
        $stage_id = $this->input->post("stage_id");
        $data = array();

        
        
        
        // $this->load->view('system/mfq/ajax/mfq_job_stage_tbl', $data);

    }

    function save_rfi_header(){

        $this->form_validation->set_rules('workProcessID', 'Invalid Request', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->save_rfi_header());
        }
    }

    function fetch_rfi_details(){

        $workProcessID = $this->input->post("workProcessID");
        $companyID = current_companyID();

        $this->datatables->select("t1.*,t1.rfiID as rfiID,t1.status as status,t1.rfiType as rfiType")
            ->from('srp_erp_mfq_jobrfimaster t1')
            ->add_column('edit', '$1', 'rfi_edit_action(rfiID, status)')
            ->edit_column('rfiType', '$1', 'rfi_type_action(rfiType)')
            ->edit_column('status', '$1', 'rfi_type_status(status)')
            ->where('t1.companyID', $companyID)
            ->where('t1.workProcessID', $workProcessID);

        echo $this->datatables->generate();
    }

    function fetch_rfi_details_approval(){
        $companyID = current_companyID();

        $this->datatables->select("t1.*,t1.rfiID as rfiID,t1.status as status,t1.rfiType as rfiType")
            ->from('srp_erp_mfq_jobrfimaster t1')
            ->add_column('edit', '$1', 'rfi_edit_action(rfiID, status)')
            ->edit_column('rfiType', '$1', 'rfi_type_action(rfiType)')
            ->edit_column('status', '$1', 'rfi_type_status(status)')
            ->where('t1.companyID', $companyID)
            ->where('t1.status', 'Submit');
        
        echo $this->datatables->generate();
    }

    function get_detail_table(){

        $workProcessID = $this->input->post("workProcessID");
        $rfiID = $this->input->post("rfiID");
        $approval = $this->input->post("approval");
        $data = array();

        $data['master'] = $this->db->where('rfiID',$rfiID)->from('srp_erp_mfq_jobrfimaster')->get()->row_array();
        $data['details'] = $this->db->where('rfiID',$rfiID)->from('srp_erp_mfq_jobrfidetail')->get()->result_array();
        $data['approval'] = $approval;

        $view = $this->load->view('system/mfq/ajax/mfq_rfi_detail_tbl', $data);
        return $view;
    }

    function add_item_Detail(){
        
        $workProcessID = $this->input->post("workProcessID");
        $rfiID = $this->input->post("rfiID");
        $ItemAutoID = $this->input->post("ItemAutoID");

        $itemDetail = $this->db->where('ItemAutoID',$ItemAutoID)->from('srp_erp_itemmaster')->get()->row_array();

        $data = array();

        $data['workProcessID'] = $workProcessID;
        $data['rfiID'] = $rfiID;
        $data['itemAutoID'] = $ItemAutoID;
        $data['itemDescription'] = $itemDetail['itemSystemCode'].' - '.$itemDetail['itemDescription'];
        $data['itemStatus'] = 'Open';

        $this->db->insert('srp_erp_mfq_jobrfidetail',$data);

        return true;
    }

    function delete_rfi_detail(){

        $id = $this->input->post("id");

        $itemDetail = $this->db->where('id',$id)->delete('srp_erp_mfq_jobrfidetail');

        $this->session->set_flashdata('s', 'Successfully deleted RFI.');

        return true;

    }

    function load_stage_checklist(){

        $workProcessID = $this->input->post("workProcessID");
        $templateID = $this->input->post("templateID");
        $stage_id = $this->input->post("stage_id");
        $data = array();

        $data['data'] = $this->db->where('jobID',$workProcessID)->where('templateID',$templateID)->where('stage_id',$stage_id)->from('srp_erp_mfq_job_stage_checklist')->get()->result_array();

        $view = $this->load->view('system/mfq/ajax/mfq_stage_checklist', $data);
        
        return $view;
    }

    function change_job_checklist_value(){

        $id = $this->input->post("id");
        $val = $this->input->post("val");
        $data = array();

        $data['value'] = $val;

        $this->db->where('id',$id)->update('srp_erp_mfq_job_stage_checklist',$data);

        $this->session->set_flashdata('s', 'Successfully Updated.');

        return TRUE;

    }

    function add_remark_for_item(){

        $id = $this->input->post("id");
        $remark = $this->input->post("remark");
        $type = $this->input->post("type");

        if($type == 'QC'){
            $this->db->where('id',$id)->update('srp_erp_mfq_jobrfidetail',array('qc_comment'=>$remark,'inspectedBy'=>current_user()));
        } else {
            $this->db->where('id',$id)->update('srp_erp_mfq_jobrfidetail',array('remarks'=>$remark,'inspectedBy'=>current_user()));
        }
        
        $this->session->set_flashdata('s', 'Successfully updated RFI.');

        return true;
    }

    function change_value_detail(){
        echo json_encode($this->MFQ_Job_model->change_value_detail());
    }

    function delete_rfi_master(){
        echo json_encode($this->MFQ_Job_model->delete_rfi_master());
    }

    function get_added_third_party_suppliers(){
        echo json_encode($this->MFQ_Job_model->get_added_third_party_suppliers());
    }

    function po_genearete_overhead(){
        $this->form_validation->set_rules('overheadID[]', 'Thirdparty Service', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
        /*     $error_message = validation_errors();
            echo warning_message($error_message); */
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_model->po_genearete_overhead());
        }
        
    }

    function load_mfq_outputitem(){

        $data = array();

        $workProcessID = $this->input->post("workProcessID");

        $data['details'] = $this->db->where('workProcessID',$workProcessID)->from('srp_erp_mfq_joboutputitems')->get()->result_array();

        $view = $this->load->view('system/mfq/ajax/mfq_outputitems', $data);

        return $view;

    }

    function save_outputitems(){
        echo json_encode($this->MFQ_Job_model->save_outputitems());
    }

    function update_total_item_estimate(){
        echo json_encode($this->MFQ_Job_model->update_total_item_estimate());
    }

}