<?php
class MFQ_Report_model extends ERP_Model
{
    function get_all_job_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $where = "srp_erp_mfq_job.linkedJobID IS NOT NULL";

        // Amount Query based on entry configuration
        $this->load->model('MFQ_Dashboard_modal');
        $qry = $this->MFQ_Dashboard_modal->job_entry_query();
        $query = '';
        if(!empty($qry)) {
            $query = "(SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
        }

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        if (!empty($datefrom) && !empty($dateto)) {
            $where .= " AND srp_erp_mfq_job.documentDate BETWEEN  '" . $datefromconvert . "' AND '" . $datetoconvert . "'";
        }
        $DepartmentID = $this->input->post('filter_DepartmentID');
        if(!empty($DepartmentID)) {
            $where .= " AND srp_erp_mfq_job.mfqSegmentID IN (" . join(',', $DepartmentID) . ")";
        }
        $customerID = $this->input->post('filter_customerID');
        if(!empty($customerID)) {
            $where .= " AND srp_erp_mfq_job.mfqCustomerAutoID IN (" . join(',', $customerID) . ")";
        }
        $search = $this->input->post('search');
        if ($search) {
            $where .= " AND (mainJob.documentCode LIKE '%" . $search . "%' OR srp_erp_mfq_segment.segmentCode LIKE '%" . $search . "%' OR CustomerName LIKE '%" . $search . "%' OR secondaryItemCode LIKE '%" . $search . "%' OR itemDescription LIKE '%" . $search . "%' OR ciCode LIKE '%" . $search . "%' OR estimateCode LIKE '%" . $search . "%')";
        }

        $subJobStatus = $this->input->post('filter_subJobStatus');
        if($subJobStatus == 1) {
            $where .= " AND srp_erp_mfq_job.approvedYN != 1";
        } else if($subJobStatus == 2) {
            $where .= " AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL";
        } else if($subJobStatus == 3) {
            $where .= " AND dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL";
        } else if($subJobStatus == 4) {
            $where .= " AND srp_erp_mfq_job.approvedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate < '" . current_date(false) . "'";
        } else if($subJobStatus == 5) {
            $where .= " AND srp_erp_mfq_job.approvedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate >= '" . current_date(false) . "'";
        }

        $this->db->select("	ciCode,
                            DATE_FORMAT(srp_erp_mfq_customerinquiry.documentDate, '$convertFormat') AS documentDate,
                            srp_erp_mfq_job.mfqSegmentID,
                            CustomerName,
                            srp_erp_mfq_segment.segmentCode,
                            statusID as inquiryStatus,
                            DATE_FORMAT(srp_erp_mfq_customerinquiry.deliveryDate, '$convertFormat') AS actualSubmissionDate,
                            DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate, '$convertFormat') AS plannedDeliveryDate,
                            DATE_FORMAT(mainJob.awardedDate, '$convertFormat') AS awardedDate,
                            poNumber,
                            estimateCode,
                            quotationStatus AS quoteStatus,
                            mainCategory,
                            mainJob.documentCode AS mainJobCode,
                            mainjobstatus,
                            srp_erp_mfq_job.documentCode AS subJobCode,
                            CASE
                                WHEN srp_erp_mfq_job.approvedYN != 1 THEN 'Open' 
                                WHEN srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL THEN 'Invoiced' 
                                WHEN dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL THEN 'Delivered' 
                                WHEN srp_erp_mfq_job.approvedYN = 1 AND dnQty.deliveryNoteID IS NULL AND srp_erp_mfq_job.expectedDeliveryDate < '" . current_date(false) . "' THEN 'Overdue' 
                                WHEN srp_erp_mfq_job.approvedYN = 1 AND dnQty.deliveryNoteID IS NULL AND srp_erp_mfq_job.expectedDeliveryDate >= '" . current_date(false) . "' THEN 'Closed' 
                            END AS jobStatus,
                            ((IFNULL(machineCost, 0) + IFNULL(overheadCost, 0) + IFNULL(labourCost, 0) + IFNULL(materialCost, 0)) * srp_erp_mfq_job.qty) AS BOMCost,
                            IFNULL(wipAmount, 0) AS amount,
                            ((((discountedPrice / srp_erp_mfq_estimatedetail.companyLocalExchangeRate) * ((100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) / expectedQty) * srp_erp_mfq_job.qty AS estimateValue,
                            deliveryNoteCode,
                            DATE_FORMAT(dnQty.deliveryDate, '$convertFormat') AS deliveryDate,
                            invoiceRevenue,
                            realizedCost, srp_erp_mfq_customerinvoicemaster.invoiceCode AS mfqInvoiceNo, customerinvoiceCode
                            ");
        $this->db->from("srp_erp_mfq_job");
        $this->db->join("srp_erp_mfq_job mainJob", "mainJob.workProcessID = srp_erp_mfq_job.linkedJobID", "LEFT");
        $this->db->join("srp_erp_mfq_estimatemaster", "srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID", "LEFT");
        $this->db->join("srp_erp_mfq_estimatedetail", "srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID", "LEFT");
        $this->db->join("srp_erp_mfq_customerinquiry", "srp_erp_mfq_customerinquiry.ciMasterID = srp_erp_mfq_estimatemaster.ciMasterID", "LEFT");
        $this->db->join("srp_erp_mfq_customermaster", "srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID", "LEFT");
        $this->db->join("srp_erp_mfq_itemmaster", "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID", "LEFT");
        $this->db->join("srp_erp_mfq_segment", "srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_job.mfqSegmentID", "LEFT");
        $this->datatables->join('(SELECT linkedJobID, MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS mainjobstatus 
                                    FROM srp_erp_mfq_job
                                        LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
                                        LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                                    GROUP BY linkedJobID)MainJobStatus', 'MainJobStatus.linkedJobID = srp_erp_mfq_job.linkedJobID', 'left');
        $this->datatables->join('(SELECT SUM(srp_erp_mfq_customerinvoicedetails.companyLocalAmount) AS invoiceRevenue, jobID
                                    FROM srp_erp_mfq_customerinvoicedetails
                                        JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.invoiceAutoID = srp_erp_mfq_customerinvoicedetails.invoiceAutoID
                                        JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID = srp_erp_mfq_customerinvoicedetails.deliveryNoteDetID
                                    WHERE srp_erp_mfq_customerinvoicemaster.confirmedYN = 1 GROUP BY jobID)invoicedAmount', 'invoicedAmount.jobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->datatables->join('(SELECT SUM(srp_erp_customerinvoicedetails.companyLocalWacAmount * srp_erp_customerinvoicedetails.requestedQty) AS realizedCost, jobID, invoiceCode as customerinvoiceCode
                                    FROM srp_erp_customerinvoicedetails
                                        JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                                        JOIN srp_erp_mfq_customerinvoicedetails ON srp_erp_mfq_customerinvoicedetails.invoiceDetailsAutoID = srp_erp_customerinvoicedetails.mfqinvoiceDetailsAutoID
                                        JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID = srp_erp_mfq_customerinvoicedetails.deliveryNoteDetID 
                                    WHERE srp_erp_customerinvoicemaster.approvedYN = 1 GROUP BY jobID)realizedAmount', 'realizedAmount.jobID = srp_erp_mfq_job.workProcessID', 'left');
        $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS machineCost, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID)machine", "machine.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
        $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS overheadCost, bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID)overhead", "overhead.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
        $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS labourCost, bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID)labourtask", "labourtask.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
        $this->db->join("(SELECT SUM(materialCost/companyLocalExchangeRate) AS materialCost, bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID)material", "material.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
        $this->db->join("(SELECT SUM(deliveredQty) AS deliveredQty, srp_erp_mfq_deliverynotedetail.jobID, deliveryNoteID,deliveryNoteCode, MAX(deliveryDate) as deliveryDate FROM srp_erp_mfq_deliverynotedetail JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID)dnQty", "dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty", "LEFT");
        $this->db->join("srp_erp_mfq_customerinvoicemaster", "srp_erp_mfq_customerinvoicemaster.deliveryNoteID= dnQty.deliveryNoteID", "LEFT");
        if(!empty($query)) {
            $this->db->join($query,'wipCalculate.workProcessID = srp_erp_mfq_job.workProcessID','left');
        }
        $this->db->where("srp_erp_mfq_job.companyID", $companyid);
        $this->db->where($where);
        $data = $this->db->get()->result_array();
        return $data;
    }

   function get_unbilled_job_report()
   {
    $convertFormat = convert_date_format_sql();
    $companyid = current_companyID();
    $where = "linkedJobID IS NOT NULL";
    $jobStatus = array();
    // Amount Query based on entry configuration
    $this->load->model('MFQ_Dashboard_modal');
    $qry = $this->MFQ_Dashboard_modal->job_entry_query();
    $query = '';
    if(!empty($qry)) {
        $query = "(SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
    }
    
    $date_format_policy = date_format_policy();
    $datefrom = $this->input->post('datefrom');
    $datefromconvert = input_format_date($datefrom, $date_format_policy);
    $dateto = $this->input->post('dateto');
    $datetoconvert = input_format_date($dateto, $date_format_policy);
    if (!empty($datefrom) && !empty($dateto)) {
        $where .= " AND srp_erp_mfq_job.documentDate BETWEEN  '" . $datefromconvert . "' AND '" . $datetoconvert . "'";
    }
    $DepartmentID = $this->input->post('filter_DepartmentID');
    if(!empty($DepartmentID)) {
        $where .= " AND srp_erp_mfq_job.mfqSegmentID IN (" . join(',', $DepartmentID) . ")";
    }
    $customerID = $this->input->post('filter_customerID');
    if(!empty($customerID)) {
        $where .= " AND srp_erp_mfq_job.mfqCustomerAutoID IN (" . join(',', $customerID) . ")";
    }
    $search = $this->input->post('search');
    if ($search) {
        $where .= " AND (documentCode LIKE '%" . $search . "%' OR srp_erp_mfq_segment.segmentCode LIKE '%" . $search . "%' OR srp_erp_mfq_customermaster.CustomerName LIKE '%" . $search . "%' OR secondaryItemCode LIKE '%" . $search . "%' OR itemDescription LIKE '%" . $search . "%')";
    }

    $subJobStatus = $this->input->post('filter_subJobStatus');
    if(isset($subJobStatus)) {
        if (in_array(1, $subJobStatus)) {
            $jobStatus[] = " (srp_erp_mfq_job.approvedYN != 1)";
        } 
        if (in_array(2, $subJobStatus)) {
            $jobStatus[] = " ((srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL) OR (dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL))";
        } 
        if (in_array(3, $subJobStatus)) {
            $jobStatus[] = " ((srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL) OR (dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL))";
        } 
        if (in_array(4, $subJobStatus)) {
            $jobStatus[] = " (srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND srp_erp_mfq_job.expectedDeliveryDate < '" . current_date(false) . "')";
        } 
        if (in_array(5, $subJobStatus)) {
            $jobStatus[] = " (srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND srp_erp_mfq_job.expectedDeliveryDate >= '" . current_date(false) . "')";
        }
    
        if(count($jobStatus) > 0) {
            $status = join(' OR ', $jobStatus);
            $where .= " AND ($status)";
        }
    }

    $this->db->select(" srp_erp_mfq_job.workProcessID, 
                        IFNULL(wipAmount, 0) AS amount, 
                        DATE_FORMAT(srp_erp_mfq_job.createdDateTime, '$convertFormat') AS createdDate,
                        DATE_FORMAT(srp_erp_mfq_job.documentDate, '$convertFormat') AS documentDate,
                        documentCode, 
                        description, 
                        ciCode,
                        ((((discountedPrice / srp_erp_mfq_estimatedetail.companyLocalExchangeRate) * ((100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) / expectedQty) * srp_erp_mfq_job.qty AS estimateValue,
                        ((IFNULL(machineCost, 0) + IFNULL(overheadCost, 0) + IFNULL(labourCost, 0) + IFNULL(materialCost, 0)) * srp_erp_mfq_job.qty) AS BOMCost,
                        srp_erp_mfq_job.estimateMasterID, estimateCode,
                        IFNULL(DATE_FORMAT(dnQty.deliveryDate, '$convertFormat'), ' - ') as deliveryDate, 
                        IFNULL(dnQty.deliveryNoteCode, ' - ') as deliveryNoteCode,
                        srp_erp_mfq_job.mfqSegmentID, 
                        srp_erp_mfq_segment.segmentCode, 
                        srp_erp_mfq_segment.description as segmentDescription,
                        srp_erp_mfq_job.mfqCustomerAutoID, 
                        srp_erp_mfq_customermaster.CustomerName as CustomerName, 
                        srp_erp_mfq_job.mfqItemID, mainCategory, 
                        secondaryItemCode, itemDescription, 
                        qty,
                        IFNULL(srp_erp_mfq_customerinvoicemaster.invoiceCode, ' - ') AS mfqInvoiceCode,
                        IFNULL(DATE_FORMAT(srp_erp_mfq_customerinvoicemaster.invoiceDate, '$convertFormat'), ' - ') AS mfqInvoiceDate,
                        srp_erp_customerinvoicemaster.approvedYN as cusApprovedYN,
                        IFNULL(srp_erp_customerinvoicemaster.invoiceCode, ' - ') AS cusInvoiceCode,
                        IFNULL(DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate, '$convertFormat'), ' - ') AS cusInvoiceDate,
                        CASE
                            WHEN srp_erp_mfq_job.confirmedYN != 1 THEN 'Open' 
                            WHEN srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NOT NULL THEN 'Invoiced' 
                            WHEN dnQty.deliveryNoteID IS NOT NULL AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID IS NULL THEN 'Delivered' 
                            WHEN srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate < '" . current_date(false) . "' THEN 'Overdue' 
                            WHEN srp_erp_mfq_job.confirmedYN = 1 AND dnQty.deliveryNoteID IS NULL AND expectedDeliveryDate >= '" . current_date(false) . "' THEN 'Closed' 
                        END AS jobStatus");
    $this->db->from("srp_erp_mfq_job");
    $this->db->join("srp_erp_mfq_estimatemaster", "srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID", "LEFT");
    $this->db->join("srp_erp_mfq_estimatedetail", "srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID", "LEFT");
    $this->db->join("srp_erp_mfq_customerinquiry", "srp_erp_mfq_customerinquiry.ciMasterID = srp_erp_mfq_estimatemaster.ciMasterID", "LEFT");
    $this->db->join("srp_erp_mfq_customermaster", "srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID", "LEFT");
    $this->db->join("srp_erp_mfq_itemmaster", "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID", "LEFT");
    $this->db->join("srp_erp_mfq_segment", "srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_job.mfqSegmentID", "LEFT");
    $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS machineCost, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID)machine", "machine.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
    $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS overheadCost, bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID)overhead", "overhead.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
    $this->db->join("(SELECT SUM(totalValue/companyLocalExchangeRate) AS labourCost, bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID)labourtask", "labourtask.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
    $this->db->join("(SELECT SUM(materialCost/companyLocalExchangeRate) AS materialCost, bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID)material", "material.bomMasterID = srp_erp_mfq_job.bomMasterID", "LEFT");
    $this->db->join("(SELECT SUM(deliveredQty) AS deliveredQty, deliveryNoteCode, srp_erp_mfq_deliverynotedetail.jobID, deliveryNoteID, MAX(deliveryDate) as deliveryDate FROM srp_erp_mfq_deliverynotedetail JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID)dnQty", "dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty", "LEFT");
    $this->db->join("srp_erp_mfq_customerinvoicemaster", "srp_erp_mfq_customerinvoicemaster.deliveryNoteID= dnQty.deliveryNoteID", "LEFT");        
    $this->db->join("srp_erp_customerinvoicemaster", "srp_erp_mfq_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicemaster.mfqInvoiceAutoID AND srp_erp_customerinvoicemaster.isDeleted != 1", "LEFT");        
    if(!empty($query)) {
        $this->db->join($query,'wipCalculate.workProcessID = srp_erp_mfq_job.workProcessID','left');
    }
    $this->db->where("srp_erp_mfq_job.companyID", $companyid);
    $this->db->where($where);
    $this->db->having("(cusApprovedYN IS NULL OR cusApprovedYN = 0)");
    $data = $this->db->get()->result_array();
    return $data;
   }
}