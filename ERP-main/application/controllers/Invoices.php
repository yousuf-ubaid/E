<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Invoices extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('buyback_helper');
        $this->load->helpers('insurancetype_helper');
        $this->load->model('Invoice_model');


    }

    function fetch_invoices()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ((isPreliminaryPrinted = 1 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

                                  
        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches.""; 
        //$this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,tempInvoiceID,referenceNo');
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.isPreliminaryPrinted as isPreliminaryPrinted,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency as transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(acknowledgementDate,\'' . $convertFormat . '\') AS acknowledgementDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,CASE WHEN invoiceType = \'DeliveryOrder\' THEN \'Delivery Order\' WHEN invoiceType = \'DirectItem\' THEN \'Direct Item\' WHEN invoiceType = \'DirectIncome\' THEN \'Direct Income\' WHEN invoiceType = \'Quotation\' THEN \'Quotation Based\' WHEN invoiceType = \'Contract\' THEN \'Contract Based\'  WHEN invoiceType = \'Sales Order\' THEN \'Sales Order Based\' WHEN invoiceType = \'Direct\' THEN \'Direct Item\'  ELSE invoiceType END as invoiceType,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL( retensionTransactionAmount, 0 ) - IFNULL(rebateAmount, 0) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL(retensionTransactionAmount,0) - IFNULL(rebateAmount, 0), 2) as total_value_search,isDeleted,tempInvoiceID,referenceNo,srp_erp_customerinvoicemaster.isSytemGenerated as isSytemGenerated,srp_erp_customerinvoicemaster.totalRetension as totalRetension');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        // $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
		//$this->datatables->add_column('invoice_detail','$1','load_invoice_detail(customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo, acknowledgementDate, invoiceAutoID)');
        $this->datatables->add_column('invoice_detail','$1','load_invoice_detail(invoiceNarration,customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo, acknowledgementDate, invoiceAutoID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isSytemGenerated, isPreliminaryPrinted, isRecurring, totalRetension)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_invoices_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
		$this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_buyback(invoiceAutoID,confirmedYN,approvedYN,createdUserID,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_recurring(){
        $date_format_policy = date_format_policy();

        $next_invoice_date = $this->input->post('next_invoice_date');
        $recurring_next_invoice_date = input_format_date($next_invoice_date, $date_format_policy);
        $start_date = $this->input->post('start_date');
        $recurring_StartDate = input_format_date($start_date, $date_format_policy);
        $end_date = $this->input->post('end_date');
        $recurring_EndDate = input_format_date($end_date, $date_format_policy);

        $this->form_validation->set_rules('next_invoice_date', 'Next Invoice Date', 'trim|required');
        $this->form_validation->set_rules('start_date', 'start date', 'trim|required');
        $this->form_validation->set_rules('end_date', 'end date', 'trim|required');
        $this->form_validation->set_rules('frequency_days', 'frequency days', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }else{
            if (($recurring_StartDate) > ($recurring_EndDate)) {
                $this->session->set_flashdata('e', '  Start Date cannot be greater than End Date!');
                echo json_encode(FALSE);
            }else if(($recurring_StartDate) > ($recurring_next_invoice_date)){
                $this->session->set_flashdata('e', ' Start Date cannot be greater than next invoice Date!');
                echo json_encode(FALSE);
            }
            else{
                echo json_encode($this->Invoice_model->save_recurring());
            }
        }
    }

    function recurring_det(){
        $invoiceautoid = $this->input->post('invoiceAutoID');

        $convertFormat = convert_date_format_sql();
        // $this->db->select('invoiceAutoID, srp_erp_customerinvoicemaster.isRecurring as isRecurring, frequencyDays, srp_erp_documentemailhistory.toEmailAddress as toEmailAddress, srp_erp_documentemailhistory.type as type, DATE_FORMAT(policyStartDate,\'' . $convertFormat . '\') AS policyStartDate ,DATE_FORMAT(policyEndDate,\'' . $convertFormat . '\') AS policyEndDate,DATE_FORMAT(nexInvoiceDate,\'' . $convertFormat . '\') AS nexInvoiceDate');
        $this->db->select('invoiceAutoID, isRecurring, frequencyDays, DATE_FORMAT(policyStartDate,\'' . $convertFormat . '\') AS policyStartDate ,DATE_FORMAT(policyEndDate,\'' . $convertFormat . '\') AS policyEndDate,DATE_FORMAT(nexInvoiceDate,\'' . $convertFormat . '\') AS nexInvoiceDate');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        //$this->db->join('srp_erp_documentemailhistory', 'srp_erp_documentemailhistory.documentAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster ');
        $rec_det = $this->db->get()->row_array();

        $this->db->select('toEmailAddress, type');
        $this->db->where('documentAutoID', $invoiceautoid);
        $this->db->from('srp_erp_documentemailhistory ');
        $rec_email = $this->db->get()->result_array();

        $data['rec_det'] = $rec_det;
        $data['rec_email'] = $rec_email;
        
        echo json_encode($data);
    }

    function save_invoice_header()
    {
        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('customerInvoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $docDate = $this->input->post('invoiceDate');
        $documentDate = input_format_date($docDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('invoiceDueDate', 'Invoice Due Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        
        if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
            $this->form_validation->set_rules('acknowledgeDate', 'Acknowledgemenr Date', 'trim|required');
        }
        
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        if ($this->input->post('invoiceType') == 'Direct') {
            //$this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
            //$this->form_validation->set_rules('invoiceNarration', 'Narration', 'trim|required');
        }
        if($this->input->post('invoiceType') == 'Project')
        {

            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if (($invoiceDate) > ($invoiceDueDate)) {
                $this->session->set_flashdata('e', ' Invoice Due Date cannot be less than Invoice Date!');
                echo json_encode(FALSE);
            } else {
                if($financeyearperiodYN==1) {
                    $financearray = $this->input->post('financeyear_period');
                    $financePeriod = fetchFinancePeriod($financearray);
                    if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                        echo json_encode($this->Invoice_model->save_invoice_header());
                    } else {
                        $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                        echo json_encode(FALSE);
                    }
                }else{
                    echo json_encode($this->Invoice_model->save_invoice_header());
                }
            }
        }
    }

    function save_direct_invoice_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $gl_codes = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $segment_gl = $this->input->post('segment_gl');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
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

            $this->session->set_flashdata($msgtype = 'e', join('', $validateMsg));
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Invoice_model->save_direct_invoice_detail());
        }
    }

    function update_income_invoice_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        //$this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_income_invoice_detail());
        }
    }

    function save_con_base_items()
    {
        $ids = $this->input->post('DetailsID');
        foreach ($ids as $key => $id) {
            $num = ($key + 1);
            $this->form_validation->set_rules("DetailsID[{$key}]", "Line {$num} ID", 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", "Line {$num} Amount", 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", "Line {$num} WareHouse", 'trim|required');
            $this->form_validation->set_rules("qty[{$key}]", "Line {$num} QTY", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Invoice_model->save_con_base_items());
        }
    }

    function save_delivery_based_items(){
        $ids = $this->input->post('DetailsID');
        foreach ($ids as $key => $id) {
            $num = ($key + 1);
            $this->form_validation->set_rules("DetailsID[{$key}]", "Line {$num} ID", 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", "Line {$num} Amount", 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", "Line {$num} WareHouse", 'trim|required');
            $this->form_validation->set_rules("qty[{$key}]", "Line {$num} QTY", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        echo json_encode($this->Invoice_model->save_delivery_based_items());
    }

    function fetch_con_detail_table()
    {
        //fetch_con_detail_table
        //fetch_billing_detail
        echo json_encode($this->Invoice_model->fetch_con_detail_table());
    }

    function delete_item_direct()
    {
        echo json_encode($this->Invoice_model->delete_item_direct());
    }

    function referback_customer_invoice()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $this->db->select('approvedYN,invoiceCode');
        $this->db->where('invoiceAutoID', trim($invoiceAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_customerinvoicemaster');
        $approved_custmoer_invoice = $this->db->get()->row_array();
        if (!empty($approved_custmoer_invoice)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_custmoer_invoice['invoiceCode']));
        } else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($invoiceAutoID, 'CINV');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function fetch_invoice_direct_details()
    {
        echo json_encode($this->Invoice_model->fetch_invoice_direct_details());
    }

    function load_invoice_header()
    {
        echo json_encode($this->Invoice_model->load_invoice_header());
    }

    function fetch_customer_invoice_detail()
    {
        echo json_encode($this->Invoice_model->fetch_customer_invoice_detail());
    }

    function load_invoices_conformation()
    {

        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $printtype = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $this->db->select('tempInvoiceID,approvedYN,invoiceType,isDOItemWisePolicy,retentionPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $data['Approved'] = $master['approvedYN'];
        $data['emailView'] = 0;
        $data['invoiceType'] = $master['invoiceType'];
        $this->load->library('NumberToWords');
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',$invoiceAutoID,'CINV','invoiceAutoID');
        $data['isDOItemWisePolicy']=$master['isDOItemWisePolicy'];
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        $pulled_do_items = getPolicyValues('DOIW', 'All');

        $data['bankCurrency'] = $this->db->query("SELECT bankCurrency FROM srp_erp_customerinvoicemaster WHERE
                                                srp_erp_customerinvoicemaster.companyID = {$companyID}
                                                 AND srp_erp_customerinvoicemaster.invoiceAutoID = {$invoiceAutoID}")->row('bankCurrency');
        
        $VatTax = $this->db->query("SELECT
                                            COUNT(taxCategory) as taxcat
                                        FROM
                                            `srp_erp_taxledger`
                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                        WHERE
                                            documentID = 'CINV'
                                            ANd srp_erp_taxledger.companyID = {$companyID}
                                            AND documentMasterAutoID = {$invoiceAutoID}")->row('taxcat');

        $data['is_tax_invoice'] = 0;
        if($data['group_based_tax']==1 && $VatTax > 0 ){
            $data['is_tax_invoice'] = 1;
        }

        $data['date_of_supply'] = $this->db->query("SELECT
                                                               DATE_FORMAT(supplyDate, '$convertFormat') as supplierDate
                                                        FROM
                                                            srp_erp_customerinvoicemaster
                                                        WHERE
                                                            srp_erp_customerinvoicemaster.companyID = {$companyID}
                                                            AND srp_erp_customerinvoicemaster.invoiceAutoID = {$invoiceAutoID}
            ")->row('supplierDate');

        //Get print data
        $isPrintDN = $this->db->query("SELECT isPrintDN as isPrintDN FROM srp_erp_customerinvoicemaster WHERE
                                  srp_erp_customerinvoicemaster.companyID = {$companyID} AND srp_erp_customerinvoicemaster.invoiceAutoID = {$invoiceAutoID} ")->row('isPrintDN');
        //end print data
        
        $data['invoice_referenceno_so_qut'] = $this->db->query("SELECT contractmaster.referenceNo as referenceno FROM `srp_erp_customerinvoicedetails`
	                                                    LEFT JOIN srp_erp_contractmaster contractmaster on contractmaster.contractAutoID =  srp_erp_customerinvoicedetails.contractAutoID
                                                        WHERE srp_erp_customerinvoicedetails.companyID = $companyID AND type != 'DO' AND invoiceAutoID = '{$invoiceAutoID}' GROUP BY 
                                                        srp_erp_customerinvoicedetails.contractAutoID")->row('referenceno');

        $data['invoice_referenceno'] = $this->db->query("SELECT
                                                            srp_erp_customerinvoicedetails.invoiceAutoID,
                                                            IF(delordermaster.referenceNo = ' ', contract.referenceNo,IFNULL( delordermaster.referenceNo, contract.referenceNo ))  AS referenceno 
                                                        FROM
                                                            `srp_erp_customerinvoicedetails`
                                                            LEFT JOIN srp_erp_deliveryorderdetails deloreder ON deloreder.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID 
                                                            LEFT JOIN srp_erp_deliveryorder delordermaster on delordermaster.DOAutoID = deloreder.DOAutoID
                                                            LEFT JOIN srp_erp_contractmaster contract ON contract.contractAutoID = deloreder.contractAutoID 
                                                        WHERE
                                                            invoiceAutoID = '{$invoiceAutoID}' 
                                                            AND srp_erp_customerinvoicedetails.type = 'DO' 
                                                            GROUP BY 
                                                            referenceno
                                                        ")->result_array();
                                                    
        $data['isPrintDN'] = $isPrintDN;
        $data['retensionPercentage'] = $master['retentionPercentage'];

                                                    
        if(!empty($master['tempInvoiceID'])){
         
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp($invoiceAutoID);            
            
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }


            $html = $this->load->view('system/invoices/erp_invoice_print_temp', $data, true);
            
            if ($this->input->post('html')) {
                echo $html;
            } else {

                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], 1, 'CINV');
            }
        }else{            

            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');
           
            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceAutoID);
            $data['taxDetails'] = fetch_tax_details('CINV',$invoiceAutoID,0);
           
            $data['templateInvoiceDetails'] = $this->Invoice_model->get_invoice_template_details($printtype);  


            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;


            $this->db->select('printHeaderFooterYN,printFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;

            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $companyID = current_companyID();  
        

            if($printtype) {
                if($printtype == 1){
                    $printlink = 'system/invoices/erp_invoice_print_simplified_vat';
                }elseif($printtype == 2){                       
                    $printlink = 'system/invoices/erp_tax_invoice_print';   
                }elseif($printtype == 3){
                    $printlink = print_template_pdf('CINV','system/invoices/erp_delivery_note_print');
                }elseif($printtype == 4){
                    $printlink = print_template_pdf('CINV','system/invoices/erp_tax_invoice_print_with_tax_details_print');
                }elseif($printtype == 5){
                    $printlink = print_template_pdf('CINV','system/invoices/erp_suspended_tax_invoice');
                }elseif($printtype == 0){
                    $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print');
                }elseif($printtype == 25 || $printtype == 27){
                    $printlink = print_template_pdf('CINV','system/invoices/tax_invoice_nov');
                }else {
                    if($data['templateInvoiceDetails'][0]['path'] == "gurafath"){
                        $printlink = 'system/invoices/erp_tax_invoice_print_gurafath';   
                    }else{
                        $printlink = 'system/invoices/erp_invoice_print_custom'; // custom template with show and hide elements 
                    }                        
                }

            } else {
                if($isPrintDN == 2){ //Check type ex: Print Invoice Only , Print Tax Invoice Only etc..
                    $printlink = 'system/invoices/erp_tax_invoice_print';
                }elseif($isPrintDN == 3){ //Print Delivery note only
                    $printlink = print_template_pdf('CINV','system/invoices/erp_delivery_note_print');
                }elseif($isPrintDN == 4){ //Print tax invoice with tax details
                    $printlink = print_template_pdf('CINV','system/invoices/erp_tax_invoice_print_with_tax_details_print');
                }elseif($isPrintDN == 5){ //Print suspended tax details
                    $printlink = print_template_pdf('CINV','system/invoices/erp_suspended_tax_invoice');
                } else {
                    if($pulled_do_items){
                        $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_doitem_print');
                    }else{
                        $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print');
                    }
                   
                }

                
            }

    
            $papersize = print_template_paper_size('CINV','A4');
            $data['papersize']=$papersize;

            if ($this->input->post('html')) {
                if($printlink == 'system/invoices/erp_invoice_print_DS') {
                    $html = $this->load->view('system/invoices/erp_invoice_print_html_DS', $data, true);
                } else {
                    if($pulled_do_items){
                        $html = $this->load->view('system/invoices/erp_invoice_doitems_print_html', $data, true);
                    }else{
                        $html = $this->load->view('system/invoices/erp_invoice_print_html', $data, true);
                    }
                    
                }

                echo $html;
                
            } else {
                $this->load->view($printlink, $data, $printHeaderFooterYN);

            }
        }

    }

    function load_checklist_print()
    {        
        //$data["jobcardheader"] = get_job_cardID($this->input->post('workProcessID'), $this->input->post('workFlowID'), $this->input->post('templateDetailID'));
        $data["details"] = "";
        $html = $this->load->view('system/checklist/daily_drillers_checklist', $data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function fetch_invoice_direct_details_commission()
    {
        echo json_encode($this->Invoice_model->fetch_invoice_direct_details_commission());
    }

    function fetch_customer_invoice_detail_commission()
    {
        echo json_encode($this->Invoice_model->fetch_customer_invoice_detail_commission());
    }

    function load_invoices_conformation_cs()
    {

        $this->load->model('Taxcalculationgroup_model');

  /*      var_dump($data['taxView']);exit;*/
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        //var_dump($invoiceAutoID);exit();
        $this->db->select('tempInvoiceID,approvedYN,invoiceType');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['emailView'] = 0;
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $data['Approved'] = $master['approvedYN'];
        $data['invoiceType'] = $master['invoiceType'];
        $this->load->library('NumberToWords');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        if(!empty($master['tempInvoiceID'])){
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_temp', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }else{

            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_commission($invoiceAutoID);

            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;


            $this->db->select('printHeaderFooterYN,printFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print_cs');



            $papersize = print_template_paper_size('CINV','A4');
            $data['papersize']=$papersize;
            //$pdfp = $this->load->view($printlink, $data, true);
            if ($this->input->post('html')) {
                $html = $this->load->view('system/invoices/erp_invoice_print_html_cs', $data, true);
                echo $html;
            } else {
                //$html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
                //$this->load->library('pdf');
                //$pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                //$pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                $this->load->view($printlink, $data, $printHeaderFooterYN);

            }
        }

    }

    function load_invoices_conformation_buyback()
    {
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceAutoID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/invoices/erp_invoice_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function fetch_detail()
    {
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        update_group_based_tax('srp_erp_customerinvoicemaster','invoiceAutoID',$invoiceAutoID,'srp_erp_customerinvoicetaxdetails','invoiceAutoID', 'CINV');
        update_do_item_wise_policy_value( $invoiceAutoID);

        $data['master'] = $this->Invoice_model->load_invoice_header();
        $data['invoiceAutoID'] = $invoiceAutoID;
        $data['isDOItemWisePolicy'] = $data['master']['isDOItemWisePolicy'];
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['marginpercent'] = 0;
        if(!empty($data['master']['insuranceTypeID'])){
            $this->db->select('marginPercentage');
            $this->db->where('insuranceTypeID', trim($data['master']['insuranceSubTypeID']));
            $this->db->from('srp_erp_invoiceinsurancetypes');
            $margindetails = $this->db->get()->row_array();
            $data['marginpercent'] = $margindetails['marginPercentage'];
        }
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['supplier_arr'] = all_supplier_drop();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['openContractPolicy'] = getPolicyValues('OCE', 'All');
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        $data['customer_job'] = array();

        if($data['master']['invoiceType'] == 'Job'){
            $data['customer_job'] = $this->Invoice_model->fetch_contract_job($data['master']['contractAutoID']);
        }

       

        $data['tabID'] = $this->input->post('tab');
        $data['invoiceproject'] = $this->db->query("SELECT detailid.itemDescription,	IF(isVariation = 1,variationAmount,totalTransCurrency) as totalTransCurrency,invoicemaster.transactionCurrencyDecimalPlaces,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,detailID,
            unitRateTransactionCurrency,
            IFNULL(srp_erp_customerinvoicedetails.transactionAmount,0) as transactionAmount,
            IFNULL(srp_erp_customerinvoicedetails.boqClaimPercentage,0) as boqClaimPercentage,
            isVariation,
            header.retensionPercentage,
            header.headerID,
            header.projectID,
            srp_erp_customerinvoicedetails.invoiceAutoID,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,
            srp_erp_customerinvoicedetails.boqDetailID,
           IFNULL( boqPreviousClaimPercentage,0) as boqPreviousClaimPercentage,
           IFNULL( boqTotalClaimPercentage,0) as boqTotalClaimPercentage
             FROM `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_boq_details detailid on detailid.detailID =srp_erp_customerinvoicedetails.boqDetailID
            LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.InvoiceAutoID =srp_erp_customerinvoicedetails.InvoiceAutoID
            LEFT JOIN srp_erp_boq_header header on header.headerID = detailid.headerID
             where  Type = 'Project' AND srp_erp_customerinvoicedetails.invoiceAutoID = {$data['invoiceAutoID']}
            ORDER BY
            isVariation asc")->result_array();


        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID');
        $data['customer_do'] = $this->Invoice_model->delivery_detail($data['master']['customerID']);
        $this->load->view('system/invoices/invoices_detail.php', $data);
    }

    function fetch_filtered_contract(){

        $ref_id = trim($this->input->post('ref_id') ?? '');
        $segment_id = trim($this->input->post('segment') ?? '');

        $invoice_master = $this->Invoice_model->load_invoice_header();

        $data['master'] = $invoice_master;

        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master'],$ref_id,$segment_id);

        return $this->load->view('system/invoices/ajax/invoice_contract_tbl', $data);

    }

    function load_un_billed_delivery_orders(){
        $master = $this->Invoice_model->load_invoice_header();
        $customerID = $master['customerID'];
        $delivery_detail = $this->Invoice_model->delivery_detail($customerID);
        $pulled_item_wise = getPolicyValues('PDOI', 'All');

        if (empty($delivery_detail)) {
            die( json_encode(['e', 'No records found']));
        }

        $str = '';

        if($pulled_item_wise){
            for ($i = 0; $i < count($delivery_detail); $i++) {
                $auto_id = $delivery_detail[$i]['DOAutoID'];

                $delivery_order_detail = $this->Invoice_model->fetch_delivery_order_details($auto_id);

                $dPlace = $master['transactionCurrencyDecimalPlaces'];
                $total_amount = round($delivery_detail[$i]['transactionAmount'], $dPlace);
                $invoiced_amount = round($delivery_detail[$i]['invoiced_amount'], $dPlace);
                $balance_amount = round(($total_amount - $invoiced_amount),$dPlace);
                if($balance_amount>0){
                    $str .= "<tr id='{$auto_id}'>";
                    $str .= "<td>" . ($i) . "</td>";
                    $str .= "<td>" . $delivery_detail[$i]['DOCode'] . " </td>";
                    $str .= "<td style='text-align: center'>" . $delivery_detail[$i]['DODate'] . "</td>";
                    $str .= "<td class='text-right'>" . $delivery_detail[$i]['referenceNo'] . "</td>";
    
                    if ($total_amount > 0) {
                        $str .= "<td class='text-right'>" . number_format($total_amount, $dPlace) . "</td>";
                    } else {
                        $str .= "<td class='text-right'>" . number_format(0, $dPlace) . "</td>";
                    }
                    $str .= "<td class='text-right'>" . number_format($invoiced_amount, $dPlace) . "</td>";
                    $str .= "<td class='text-right'>" . number_format($balance_amount, $dPlace) . "
                                    <a class='hoverbtn invoiceaddbtn'  onclick='applybtn(". $auto_id . ",".round($balance_amount, $dPlace).")'>
                                                <i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a></td>";
                    $str .= '<td><input type="hidden" name="orders[]" id="delivery_order_' . $auto_id . '" value="' . $auto_id . '">';
                    $str .= '<input type="text" name="amount[]" id="amount_' . $auto_id . '" data-auto-id="' . $auto_id . '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="validate_max_receivable(this,';
                    $str .= round($balance_amount, $dPlace) . ',' . $dPlace . ')" onchange="amount_round(this, ' . $dPlace . ')" class="number invoicing_amount" ></td>';
                    $str .= '<td class="text-right" style="display:none;"> </td>';
                    $str .= "</tr>";
                    
                  
                    foreach($delivery_order_detail as $order){
                        $str .= "<tr class='text-bold' style='background:#fbf0f5;'>";
                        $str .= "<td>
                            <input type='hidden' name='autoID' id='autoID' class='autoID' value=".$order['DOAutoID']." />
                            <input type='hidden' name='DODetailsAutoID' id='DODetailsAutoID' class='DODetailsAutoID' value=".$order['DODetailsAutoID']." />
                        </td>";
                        $str .= "<td>{$order['itemSystemCode']}</td>";
                        $str .= "<td>{$order['itemDescription']}</td>";
                        $str .= "<td>{$order['itemCategory']}</td>";
                        $str .= "<td><span class='text-bold'>Requested : </span> {$order['requestedQty']}</td>";
                        $str .= "<td><span class='text-bold'>Unit amount : </span> ".number_format($order['unittransactionAmount'], $dPlace)."</td>";
                        $str .= "<td>".number_format($order['transactionAmount'], $dPlace)."</td>";
                        $str .= "<td><input type='checkbox' id='detail_check' name='detail_check' class='detail_check' onchange='change_check_add_detail(this)' /></td>";
                        $str .= "</tr>";
                    }
                    
                }
            }
        }else{
            for ($i = 0; $i < count($delivery_detail); $i++) {
                $auto_id = $delivery_detail[$i]['DOAutoID'];
                $dPlace = $master['transactionCurrencyDecimalPlaces'];
                $total_amount = round($delivery_detail[$i]['transactionAmount'], $dPlace);
                $invoiced_amount = round($delivery_detail[$i]['invoiced_amount'], $dPlace);
                $balance_amount = round(($total_amount - $invoiced_amount),$dPlace);
                if($balance_amount>0){
                    $str .= "<tr>";
                    $str .= "<td>" . ($i) . "</td>";
                    $str .= "<td>" . $delivery_detail[$i]['DOCode'] . " </td>";
                    $str .= "<td style='text-align: center'>" . $delivery_detail[$i]['DODate'] . "</td>";
                    $str .= "<td class='text-right'>" . $delivery_detail[$i]['referenceNo'] . "</td>";
    
                    if ($total_amount > 0) {
                        $str .= "<td class='text-right'>" . number_format($total_amount, $dPlace) . "</td>";
                    } else {
                        $str .= "<td class='text-right'>" . number_format(0, $dPlace) . "</td>";
                    }
                    $str .= "<td class='text-right'>" . number_format($invoiced_amount, $dPlace) . "</td>";
                    $str .= "<td class='text-right'>" . number_format($balance_amount, $dPlace) . "
                                    <a class='hoverbtn invoiceaddbtn'  onclick='applybtn(". $auto_id . ",".round($balance_amount, $dPlace).")'>
                                                <i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a></td>";
                    $str .= '<td><input type="hidden" name="orders[]" id="delivery_order_' . $auto_id . '" value="' . $auto_id . '">';
                    $str .= '<input type="text" name="amount[]" id="amount_' . $auto_id . '" data-auto-id="' . $auto_id . '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="validate_max_receivable(this,';
                    $str .= round($balance_amount, $dPlace) . ',' . $dPlace . ')" onchange="amount_round(this, ' . $dPlace . ')" class="number invoicing_amount" ></td>';
                    $str .= '<td class="text-right" style="display:none;"> </td>';
                    $str .= "</tr>";
    
                }
            }
        }
        

        echo json_encode(['s', 'view'=>$str]);
    }

    function fetch_detail_buyback()
    {
        $data['master'] = $this->Invoice_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $this->load->view('system/invoices/invoices_detail_buyback', $data);
    }

    function fetch_detail_header_lock()
    {
        echo json_encode($this->Invoice_model->fetch_detail());
    }

    function add_do_detail_item_invoice(){
        echo ($this->Invoice_model->add_do_detail_item_invoice());
    }

    function fetch_invoices_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $usergroup_assign = getPolicyValues('UGSE','All');
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $current_user_id = current_userID();

        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(acknowledgementDate,\'' . $convertFormat . '\') AS acknowledgementDate,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0)- IFNULL( rebateAmount, 0 ) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0)- IFNULL( rebateAmount, 0 ), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            // $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo');
            if($usergroup_assign == 1){
                $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo','left');
                $this->datatables->join('srp_erp_segment_usergroups', 'srp_erp_approvalusers.groupID = srp_erp_segment_usergroups.userGroupID AND srp_erp_customerinvoicemaster.segmentID = srp_erp_segment_usergroups.segmentID','inner');
            }else{
                $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            }

            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('details', '$1 <br><b>Ref No :</b>$2 $3', 'invoiceNarration, referenceNo, view_acknowledgementDate(acknowledgementDate)');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(acknowledgementDate,\'' . $convertFormat . '\') AS acknowledgementDate,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) - IFNULL( rebateAmount, 0 ) as total_value,ROUND((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0))- IFNULL( rebateAmount, 0 ), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID');
            if($usergroup_assign == 1){
                $this->datatables->join('srp_erp_segment_usergroups', 'srp_erp_approvalusers.groupID = srp_erp_segment_usergroups.userGroupID AND srp_erp_customerinvoicemaster.segmentID = srp_erp_segment_usergroups.segmentID','inner');
               // $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            }
          
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $current_user_id);
            $this->datatables->group_by('srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('details', '$1 <br><b>Ref No :</b>$2 $3', 'invoiceNarration, referenceNo, view_acknowledgementDate(acknowledgementDate)');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }

    }

    function save_invoice_item_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $isBuyBackCompany = isBuyBack_company();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $cat_mandetory = Project_Subcategory_is_exist();
        if(in_array('0', $quantityRequested)) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        foreach ($searches as $key => $search) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $serviceitm= $this->db->get()->row_array();
            if($serviceitm && $serviceitm['mainCategory']!='Service'){
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }

            if ($projectExist == 1 && !empty($projectID[$key])) {
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }
            }
            if ($isBuyBackCompany == 1) {
                $this->form_validation->set_rules("noOfItems[{$key}]", 'No Item', 'trim|required');
                $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Qty', 'trim|required');
                $this->form_validation->set_rules("noOfUnits[{$key}]", 'Units', 'trim|required');
                $this->form_validation->set_rules("deduction[{$key}]", 'Deduction', 'trim|required');
            } else {
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
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
            echo json_encode($this->Invoice_model->save_invoice_item_detail());
        }
    }


    function save_invoice_item_detail_commission()
    {
        $projectExist = project_is_exist();
        $projectID = $this->input->post('projectID');
        $isBuyBackCompany = isBuyBack_company();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $invoiceType = $this->input->post('invoiceType');

        $cat_mandetory = Project_Subcategory_is_exist();
        if(in_array('0', $quantityRequested)) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        foreach ($searches as $key => $search) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $serviceitm= $this->db->get()->row_array();
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            if($serviceitm['mainCategory']!='Service'){
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }
            }
            if ($isBuyBackCompany == 1) {
                $this->form_validation->set_rules("noOfItems[{$key}]", 'No Item', 'trim|required');
                $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Qty', 'trim|required');
                $this->form_validation->set_rules("noOfUnits[{$key}]", 'Units', 'trim|required');
                $this->form_validation->set_rules("deduction[{$key}]", 'Deduction', 'trim|required');
            } else {
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            }
            if($invoiceType == "Commission"){
                $this->form_validation->set_rules("salesPersonID[{$key}]", 'Sales Person', 'trim|required');
                $this->form_validation->set_rules("designationID[{$key}]", 'Designation', 'trim|required');

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
            echo json_encode($this->Invoice_model->save_invoice_item_detail_commission());
        }
    }

    function update_invoice_item_detail()
    {
        $projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $quantityRequested=$this->input->post('quantityRequested');
        $itemAutoID=$this->input->post('itemAutoID');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();
        $cat_mandetory = Project_Subcategory_is_exist();
        if($quantityRequested == 0) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        if($serviceitm['mainCategory']!='Service') {
            $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        }
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $this->form_validation->set_rules("batch_number[]", 'Batch Number', 'trim|required');
        }

        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($isBuyBackCompany == 1) {
            $this->form_validation->set_rules("noOfItems", 'No Item', 'trim|required');
            $this->form_validation->set_rules("grossQty", 'Gross Qty', 'trim|required');
            $this->form_validation->set_rules("noOfUnits", 'Units', 'trim|required');
            $this->form_validation->set_rules("deduction", 'Deduction', 'trim|required');
        } else {
            $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_invoice_item_detail());
        }
    }
    function update_invoice_item_detail_commission()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $isBuyBackCompany = isBuyBack_company();
        $quantityRequested=$this->input->post('quantityRequested');
        $itemAutoID=$this->input->post('itemAutoID');
        $invoiceType=$this->input->post('invoiceType');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();
        $cat_mandetory = Project_Subcategory_is_exist();
        if($quantityRequested == 0) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        if($serviceitm['mainCategory']!='Service') {
            $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        }
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if($invoiceType == "Commission"){
            $this->form_validation->set_rules('salesPersonID', 'Sales Person', 'trim|required');
            $this->form_validation->set_rules('designationID', 'Designation', 'trim|required');
        }
        if ($isBuyBackCompany == 1) {
            $this->form_validation->set_rules("noOfItems", 'No Item', 'trim|required');
            $this->form_validation->set_rules("grossQty", 'Gross Qty', 'trim|required');
            $this->form_validation->set_rules("noOfUnits", 'Units', 'trim|required');
            $this->form_validation->set_rules("deduction", 'Deduction', 'trim|required');
        } else {
            $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_invoice_item_detail_commission());
        }
    }
    function invoice_confirmation()
    {
        echo json_encode($this->Invoice_model->invoice_confirmation());
    }

    // function save_inv_base_items(){
    //     echo json_encode($this->Invoice_model->save_inv_base_items());
    // }

    function save_invoice_approval()
    {
        $system_code = trim($this->input->post('invoiceAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'CINV', $level_id);
            if ($approvedYN) {
                //$this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(array('w', 'Document already approved', 1));
            } else {
                $this->db->select('invoiceAutoID');
                $this->db->where('invoiceAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_customerinvoicemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    //$this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(array('w', 'Document already rejected', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Invoice_model->save_invoice_approval());
                    }
                }
            }
        }
        else if ($status == 2) {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_customerinvoicemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                //$this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'CINV', $level_id);
                if (!empty($rejectYN)) {
                    //$this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Invoice_model->save_invoice_approval());
                    }
                }
            }
        }
    }

    function delete_customerInvoice_attachement()
    {
        echo json_encode($this->Invoice_model->delete_customerInvoice_attachement());
    }

    function delete_invoice_master()
    {
        echo json_encode($this->Invoice_model->delete_invoice_master());
    }


    function load_subItemList()
    {
        
        $detailID = $this->input->post('detailID');
        $documentID = $this->input->post('documentID');
        $warehouseID = $this->input->post('warehouseID');
        $subItemapplicableon = $this->input->post('subItemapplicableon');
        $status = $this->input->post('status');
        $data['subItems'] = $this->Invoice_model->load_subItem_notSold($detailID, $documentID, $warehouseID);


        switch ($documentID) {
            case "CINV":
                $data['detail'] = $this->Invoice_model->get_invoiceDetail($detailID);
                break;

            case "RV":
                $data['detail'] = $this->Invoice_model->get_receiptVoucherDetail($detailID);
                break;

            case "SR":
                $data['detail'] = $this->Invoice_model->get_stockReturnDetail($detailID);
                break;

            case "MI":
                $data['detail'] = $this->Invoice_model->get_materialIssueDetail($detailID);
                break;

            case "ST":
                $data['detail'] = $this->Invoice_model->get_stockTransferDetail($detailID);
                break;

            case "SA":
                $data['detail'] = $this->Invoice_model->get_stockAdjustmentDetail($detailID);
                break;

            case "DO":
                $this->load->model('Delivery_order_model');
                $data['detail'] = $this->Delivery_order_model->fetch_delivery_order_detail($detailID);
            break;

            case "JOB":
                $this->load->model('MFQ_Job_model');
                $data['detail'] = $this->MFQ_Job_model->fetch_mfq_item_detail($detailID);
                break;

            default:
                echo $documentID . ' Code not configured <br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
                die();

        }
        $data['subItemapplicableon'] = $subItemapplicableon;
        $data['status'] = $status;
        $data['attributes'] = fetch_company_assigned_attributes();
        $data['documentID'] = $documentID;
        $this->load->view('system/item/itemmastersub/load-sub-item-list', $data);
    }

    function save_subItemList()
    {
        $subItemCode = $this->input->post('subItemCode[]');
        $qty = $this->input->post('qty');

        if ($qty == count($subItemCode)) {
            $output = $this->Invoice_model->save_subItemList();
            echo json_encode($output);

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Please select ' . $qty . ' item/s.'));
        }


    }

    function re_open_invoice()
    {
        echo json_encode($this->Invoice_model->re_open_invoice());
    }

    function customerinvoiceGLUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->customerinvoiceGLUpdate());
        }
    }

    function save_customer_invoice_details_service_date()
    {
        
        $invoiceType = $this->input->post('invoiceType');
        if($invoiceType=='DirectIncome'){
            $this->form_validation->set_rules('serviceToDate', 'Service ToDate', 'trim|required');
        }

        if($invoiceType=='DirectItem'){
            $this->form_validation->set_rules('serviceToDate', 'Service ToDate', 'trim');
        }
        
        $this->form_validation->set_rules('serviceFromDate', 'Service FromDate', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else { 
            echo json_encode($this->Invoice_model->save_customer_invoice_details_service_date());
        }
    }

    function fetch_customer_invoice_all_detail_edit()
    {
        echo json_encode($this->Invoice_model->fetch_customer_invoice_all_detail_edit());
    }

    function load_customer_invoice_deatails()
    {
        echo json_encode($this->Invoice_model->load_customer_invoice_deatails());
    }


    function updateCustomerInvoice_edit_all_Item()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        if(in_array('0', $quantityRequested)) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $serviceitm= $this->db->get()->row_array();

            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }

            if($serviceitm['mainCategory']!='Service') {
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
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
            echo json_encode($this->Invoice_model->updateCustomerInvoice_edit_all_Item());
        }
    }

    function invoiceloademail()
    {


        $invoiceautoid = $this->input->post('invoiceAutoID');
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster ');
        $customeremail = $this->db->get()->row_array();

        $data['attachmentdescription'] = $this->db->query("SELECT attachmentID,myFileName as filename,attachmentDescription as description FROM `srp_erp_documentattachments` where 
	                                                    documentID = 'CINV' AND documentSystemCode = $invoiceautoid")->result_array();

        $data['customerEmail'] = $customeremail['customerEmail'];
        $this->load->view('system/invoices/erp_invoice_email_view.php', $data);

        //echo json_encode($this->Invoice_model->invoiceloademail());

    }

    function customer_invoiceloademail(){
        $invoiceautoid = $this->input->post('invoiceAutoID');

        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster ');
        $customeremail = $this->db->get()->row_array();

        $data['customerEmail'] = $customeremail['customerEmail'];
        echo json_encode($data);
    }

    function send_invoice_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->send_invoice_email());
        }
    }

    function load_default_note(){
        echo json_encode($this->Invoice_model->load_default_note());
    }

    function open_all_notes(){
        echo json_encode($this->Invoice_model->open_all_notes());
    }

    function load_notes(){
        echo json_encode($this->Invoice_model->load_notes());
    }
    function saveinsurancetype(){

        $this->form_validation->set_rules('insurancetype', 'Insurance Type', 'trim|required');
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        //$this->form_validation->set_rules('marginPercentage', 'Margin Percentage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode( array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->saveinsurancetype());
        }

    }
    function fetchinsurancetype()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_invoiceinsurancetypes.insuranceTypeID as insuranceTypeID,srp_erp_invoiceinsurancetypes.insuranceType,CONCAT(srp_erp_chartofaccounts.GLDescription, " - ", srp_erp_chartofaccounts.GLSecondaryCode) as GLDescription,srp_erp_invoiceinsurancetypes.marginPercentage,srp_erp_invoiceinsurancetypes.masterTypeID,srp_erp_invoiceinsurancetypes.noofMonths,mastertyp.insuranceType as mastertype', false)
            ->from('srp_erp_invoiceinsurancetypes')
        ->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_invoiceinsurancetypes.GLAutoID', 'left')
        ->join('srp_erp_invoiceinsurancetypes mastertyp', 'mastertyp.insuranceTypeID = srp_erp_invoiceinsurancetypes.masterTypeID', 'left')
        ->where('srp_erp_invoiceinsurancetypes.companyID', $companyID);
        //$this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="sub_insurance_type($1)"><span title="Sub Type" rel="tooltip" class="glyphicon glyphicon-menu-hamburger" ></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="openinsuranceeditmodel($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_insurancetype($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a> ', 'insuranceTypeID');
        $this->datatables->add_column('edit', '$1', 'load_insurancetype_action(insuranceTypeID,masterTypeID)');
        echo $this->datatables->generate();
    }
    function getinsurancetype(){
        echo json_encode($this->Invoice_model->getinsurancetype());
    }
    function deleteinsurancetype(){
        echo json_encode($this->Invoice_model->deleteinsurancetype());
    }

    function save_invoice_header_insurance()
    {
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('customerInvoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $docDate = $this->input->post('invoiceDate');
        $documentDate = input_format_date($docDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('invoiceDueDate', 'Invoice Due Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('contactPersonNumber', 'Telephone Number', 'trim|required');

        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        if ($this->input->post('invoiceType') == 'Insurance') {
            $this->form_validation->set_rules('insurancetypeid', 'Insurance Type', 'trim|required');
            $this->form_validation->set_rules('insuranceSubTypeID', 'Sub Type', 'trim|required');
            $this->form_validation->set_rules('policyStartDate', 'Policy Start Date', 'trim|required');
            $this->form_validation->set_rules('policyEndDate', 'Policy End Date ', 'trim|required');
        }


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if (($invoiceDate) > ($invoiceDueDate)) {
                $this->session->set_flashdata('e', ' Invoice Due Date cannot be less than Invoice Date!');
                echo json_encode(FALSE);
            } else {
                if($financeyearperiodYN==1) {
                    $financearray = $this->input->post('financeyear_period');
                    $financePeriod = fetchFinancePeriod($financearray);
                    if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                        echo json_encode($this->Invoice_model->save_invoice_header_insurance());
                    } else {
                        $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                        echo json_encode(FALSE);
                    }
                }else{
                    echo json_encode($this->Invoice_model->save_invoice_header_insurance());
                }
            }
        }
    }
    function fetch_invoices_insurance()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1) )";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0), 2) as total_value_search,isDeleted,tempInvoiceID,referenceNo,srp_erp_customerinvoicemaster.isSytemGenerated as isSytemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Ref No : </b> $6 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_insurancetype(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isSytemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    function load_invoices_conformation_invoicetype()
    {
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('tempInvoiceID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $this->load->library('NumberToWords');
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',$invoiceAutoID,'CINV','invoiceAutoID');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        if(!empty($master['tempInvoiceID'])){
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp_insurance($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_temp_insurancetype', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }else{
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp_insurance($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
            $this->db->select('printHeaderFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print_insurance');
            $papersize = print_template_paper_size('CINV','A4');
            $pdfp = $this->load->view('system/invoices/erp_invoice_print_insurance', $data, true);
            if ($this->input->post('html')) {
                $html = $this->load->view('system/invoices/erp_invoice_print_html_insurance', $data, true);
                echo $html;
            } else {
                //$html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
                $this->load->library('pdf');
                //$pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                $pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN'],$printHeaderFooterYN, 'CINV');
            }
        }

    }
    function fetch_invoices_approval_insurance()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $curentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_insurance(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,ROUND((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $curentuserid);
            $this->datatables->group_by('srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_insurance(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }

    }


    function fetch_invoices_margirn()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 0) or (confirmedYN = 3 AND approvedYN != 0) )";
            }else if ($status == 5) {
                $status_filter = " AND ((isPreliminaryPrinted = 1 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.isPreliminaryPrinted as isPreliminaryPrinted,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0) -IFNULL(retensionTransactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0) -IFNULL(retensionTransactionAmount,0)) as total_value_search,isDeleted,tempInvoiceID,referenceNo,srp_erp_customerinvoicemaster.isSytemGenerated as isSytemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Ref No : </b> $6 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_margin(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isSytemGenerated, isPreliminaryPrinted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_detail_margin()
    {
        update_group_based_tax('srp_erp_customerinvoicemaster','invoiceAutoID',trim($this->input->post('invoiceAutoID') ?? ''),'srp_erp_customerinvoicetaxdetails','invoiceAutoID', 'CINV');
        $data['master'] = $this->Invoice_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID');
        $this->load->view('system/invoices/fetch_detail_margin.php', $data);
    }

    function save_direct_invoice_detail_margin()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $segment_gl = $this->input->post('segment_gl');

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("marginPercentage[{$key}]", 'Percentage', 'trim|required');
            $this->form_validation->set_rules("marginAmount[{$key}]", 'Margin Amount', 'trim|required');
            $this->form_validation->set_rules("transactionAmount[{$key}]", 'Total Amount', 'trim|required');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'description', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));

            $this->session->set_flashdata($msgtype = 'e', join('', $validateMsg));
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Invoice_model->save_direct_invoice_detail_margin());
        }
    }

    function update_income_invoice_detail_margin()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules("marginPercentage", 'Percentage', 'trim|required');
        $this->form_validation->set_rules("marginAmount", 'Margin Amount', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_income_invoice_detail_margin());
        }
    }


    function load_invoices_conformation_margin()
    {
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('tempInvoiceID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $this->load->library('NumberToWords');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        if(!empty($master['tempInvoiceID'])){
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_temp_margin', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }else{
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
            $this->db->select('printHeaderFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_margin', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }
    }


    function save_insurance_invoice_detail_margin()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $segment_gl = $this->input->post('segment_gl');

        foreach ($segment_gl as $key => $seg_code) {
            $this->form_validation->set_rules("supplierAutoID[{$key}]", 'Supplier', 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("marginPercentage[{$key}]", 'Percentage', 'trim|required');
            $this->form_validation->set_rules("marginAmount[{$key}]", 'Margin Amount', 'trim|required');
            $this->form_validation->set_rules("totalAmount[{$key}]", 'Total Amount', 'trim|required');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'description', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));

            $this->session->set_flashdata($msgtype = 'e', join('', $validateMsg));
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Invoice_model->save_insurance_invoice_detail_margin());
        }
    }

    function update_income_invoice_detail_insurance()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('supplierAutoID', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules("marginPercentage", 'Percentage', 'trim|required');
        $this->form_validation->set_rules("marginAmount", 'Margin Amount', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_income_invoice_detail_insurance());
        }
    }

    function delivery_order_invoice(){
        $this->form_validation->set_rules('invoiceAutoID', 'Invoice ID', 'trim|required');
        $retensionEnabled = getPolicyValues('RETO', 'All');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $invoice_id = $this->input->post('invoiceAutoID');
        $amount_arr = $this->input->post('amounts');
        $orders_arr = $this->input->post('deliveryOrders');
        $dateTime = current_date();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,
                           companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID,customerID,retentionPercentage');
        $master = $this->db->where('invoiceAutoID', $invoice_id)->get('srp_erp_customerinvoicemaster')->row_array();
        $customerID = $master['customerID'];
        $d_place = $master['transactionCurrencyDecimalPlaces'];

        $order_data = $this->Invoice_model->delivery_detail($customerID);
        $order_data = (!empty($order_data))? array_group_by($order_data, 'DOAutoID'): $order_data;

        /*Un billed Invoice GL details*/
        $companyID = current_companyID();
        $un_billed_gl = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                          WHERE GLAutoID = (
                                              SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                          ) AND companyID={$companyID} ")->row_array();

        $data = [];
        foreach ($amount_arr as $key=>$amount) {
            $delivery_id = $orders_arr[$key];

            if($amount == 0){
                die( json_encode(['e', 'You can not pay `0` amount.']) );
            }

            if(!array_key_exists($delivery_id, $order_data)){
                die( json_encode(['e', 'Order details not found for <b>line no : '.($key+1).'</b>']) );
            }

            $amount = round($amount, $d_place);
            $this_order_data = $order_data[$delivery_id][0];
            $total_order_amount = round($this_order_data['transactionAmount'], $d_place);
            $invoiced_amount = round($this_order_data['invoiced_amount'], $d_place);
            $balance = $total_order_amount - $invoiced_amount;

            if($balance < $amount){
                $balance = number_format($balance, $d_place);
                die( json_encode(['e', "Maximum receivable amount <b>{$balance} for line no : ".($key+1)."</b>"]) );
            }

            $data[$key]['DOMasterID'] = $delivery_id;
            $data[$key]['invoiceAutoID'] = $invoice_id;
            $data[$key]['revenueGLAutoID'] = $un_billed_gl['GLAutoID'];
            $data[$key]['revenueSystemGLCode'] = $un_billed_gl['systemAccountCode'];
            $data[$key]['revenueGLCode'] = $un_billed_gl['GLSecondaryCode'];
            $data[$key]['revenueGLDescription'] = $un_billed_gl['GLDescription'];
            $data[$key]['revenueGLType'] = $un_billed_gl['subCategory'];

            $data[$key]['transactionAmount'] = round($amount, $d_place);
            $data[$key]['due_amount'] = round($balance, $d_place);
            $data[$key]['balance_amount'] = round(($balance-$amount), $d_place);
            $companyLocalAmount = $amount / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $amount / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $amount / $master['customerCurrencyExchangeRate'];
            $data[$key]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

            $data[$key]['type'] = 'DO';

            if($retensionEnabled == 1){
                $data[$key]['retensionPercentage'] = $master['retentionPercentage'];
                $data[$key]['retensionValue'] = round((($data[$key]['transactionAmount'] * $master['retentionPercentage']) / 100),2);
            }

            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $companyID;
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $dateTime;
        }

        if(empty($data)){
            die( json_encode(['e', 'There is no data to process']) );
        }
        $this->db->trans_start();
        $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

        /** Added (SME-2969)*/
        $details = $this->db->query("SELECT * FROM srp_erp_customerinvoicedetails WHERE invoiceAutoID = $invoice_id")->result_array();

        foreach ($details as $det) {
            $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                            FROM srp_erp_taxledger 
                                            WHERE documentID = 'CINV' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['invoiceDetailsAutoID']}"
                                        )->row('taxledgerID');

            if($dataExist == 0) {
                $ledgerDet = $this->db->query("SELECT
                                    IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                    customerCountryID,
                                    vatEligible,
                                    customerID,
                                    srp_erp_taxledger.*, outputVatGLAccountAutoID,
                                    IF(taxCategory = 2 ,outputVatTransferGLAccountAutoID,taxGlAutoID) as outputVatTransferGLAccountAutoID, deliveredTransactionAmount 
                                FROM
                                    srp_erp_taxledger
                                    JOIN ( SELECT deliveredTransactionAmount, DOAutoID, customerID FROM srp_erp_deliveryorder ) mastertbl ON mastertbl.DOAutoID = srp_erp_taxledger.documentMasterAutoID 
                                    AND srp_erp_taxledger.documentID = 'DO'
                                    JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                    JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                    JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                    WHERE srp_erp_taxmaster.taxCategory = 2 AND DOAutoID = {$det['DOMasterID']}")->result_array();
    
                if(!empty($ledgerDet)) {
                    $taxAmount = 0;
                    foreach ($ledgerDet as $val) {
                        $dataleg['documentID'] = 'CINV';
                        $dataleg['documentMasterAutoID'] = $invoice_id;
                        $dataleg['documentDetailAutoID'] = $det['invoiceDetailsAutoID'];
                        $dataleg['taxDetailAutoID'] = null;
                        $dataleg['taxPercentage'] = $val['taxPercentage'];
                        $dataleg['ismanuallychanged'] = 0;
                        $dataleg['isClaimable'] = $val['isClaimable'];
                        $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                        $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                        $dataleg['taxMasterID'] = $val['taxMasterID'];
                        $dataleg['amount'] = ($val['amount'] / $val['deliveredTransactionAmount']) * $det['transactionAmount'];
                        $dataleg['formula'] = $val['formula'];
                        $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                        $dataleg['transferGLAutoID'] = $val['outputVatTransferGLAccountAutoID'];
                        $dataleg['countryID'] = $val['customerCountryID'];
                        $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                        $dataleg['partyID'] = $val['customerID'];
                        $dataleg['locationID'] = null;
                        $dataleg['locationType'] = null;
                        $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                        $dataleg['createdPCID'] = $this->common_data['current_pc'];
                        $dataleg['createdUserID'] = $this->common_data['current_userID'];
                        $dataleg['createdUserName'] = $this->common_data['current_user'];
                        $dataleg['createdDateTime'] = $this->common_data['current_date'];
    
                        $ledgerEntry = $this->db->insert('srp_erp_taxledger', $dataleg);
                        $taxAmount += ($val['amount'] / $val['deliveredTransactionAmount']) * $det['transactionAmount'];
                    }
                    $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                    $data_detailTBL['taxAmount'] = $taxAmount;
                    $this->db->where('invoiceDetailsAutoID', $det['invoiceDetailsAutoID']);
                    $this->db->update('srp_erp_customerinvoicedetails', $data_detailTBL);
                }
            }
        }
        /** End (SME-2969)*/

        /** Added (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoice_id}")->row_array();
        if(!empty($rebate)) {
            $this->Invoice_model->calculate_rebate_amount($invoice_id);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            die( json_encode(['e', 'Invoice details save failed ' . $this->db->_error_message()]) );
        } else {
            $this->db->trans_commit();
            die( json_encode(['s', 'Invoice details saved successfully.']) );
        }
    }

    function save_inv_discount_detail()
    {
        $this->form_validation->set_rules('discountExtraChargeID', 'Discount Type', 'trim|required');
        $this->form_validation->set_rules('discountPercentage', 'Discount Percentage', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        $this->form_validation->set_rules('discount_amount', 'Discount Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Invoice_model->save_inv_discount_detail());
        }
    }

    function save_inv_extra_detail()
    {
        $this->form_validation->set_rules('discountExtraChargeIDExtra', 'Extra Type', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Invoice_model->save_inv_extra_detail());
        }
    }

    function delete_discount_gen()
    {
        echo json_encode($this->Invoice_model->delete_discount_gen());
    }

    function delete_extra_gen()
    {
        echo json_encode($this->Invoice_model->delete_extra_gen());
    }
    function fetch_customer_details_by_id()
    {
        echo json_encode($this->Invoice_model->fetch_customer_details_by_id());
    }
    function fetch_customer_details_currency()
    {
        echo json_encode($this->Invoice_model->fetch_customer_details_currency());
    }

    function fetch_invoices_suom()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ((isPreliminaryPrinted = 1 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        //$this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,tempInvoiceID,referenceNo');
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.isPreliminaryPrinted as isPreliminaryPrinted,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value_search,isDeleted,tempInvoiceID,referenceNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br><b>Ref No : </b> $6 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_suom(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isPreliminaryPrinted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_detail_suom()
    {
        $data['master'] = $this->Invoice_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['marginpercent'] = 0;
        if(!empty($data['master']['insuranceTypeID'])){
            $this->db->select('marginPercentage');
            $this->db->where('insuranceTypeID', trim($data['master']['insuranceTypeID']));
            $this->db->from('srp_erp_invoiceinsurancetypes');
            $margindetails = $this->db->get()->row_array();
            $data['marginpercent'] = $margindetails['marginPercentage'];
        }
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['supplier_arr'] = all_supplier_drop();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $this->load->view('system/invoices/invoices_detail_suom.php', $data);
    }

    function save_invoice_item_detail_suom()
    {
        $projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $serviceitm= $this->db->get()->row_array();
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            if($serviceitm['mainCategory']!='Service'){
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
            if ($isBuyBackCompany == 1) {
                $this->form_validation->set_rules("noOfItems[{$key}]", 'No Item', 'trim|required');
                $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Qty', 'trim|required');
                $this->form_validation->set_rules("noOfUnits[{$key}]", 'Units', 'trim|required');
                $this->form_validation->set_rules("deduction[{$key}]", 'Deduction', 'trim|required');
            } else {
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            }
            //$this->form_validation->set_rules("SUOMIDhn[{$key}]", 'Secondary UOM', 'trim|required');
            if(!empty($this->input->post("SUOMIDhn[$key]"))){
                $this->form_validation->set_rules("SUOMQty[{$key}]", 'Secondary QTY', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Invoice_model->save_invoice_item_detail());
        }
    }


    function update_invoice_item_detail_suom()
    {
        $projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $itemAutoID=$this->input->post('itemAutoID');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();

        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        if($serviceitm['mainCategory']!='Service') {
            $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        }
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($isBuyBackCompany == 1) {
            $this->form_validation->set_rules("noOfItems", 'No Item', 'trim|required');
            $this->form_validation->set_rules("grossQty", 'Gross Qty', 'trim|required');
            $this->form_validation->set_rules("noOfUnits", 'Units', 'trim|required');
            $this->form_validation->set_rules("deduction", 'Deduction', 'trim|required');
        } else {
            $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        }
        $this->form_validation->set_rules("SUOMQty", 'Secondary QTY', 'trim|required');
        $this->form_validation->set_rules("SUOMIDhn", 'Secondary UOM', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->update_invoice_item_detail());
        }
    }


    function load_invoices_conformation_suom()
    {

        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('tempInvoiceID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $this->load->library('NumberToWords');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        if(!empty($master['tempInvoiceID'])){
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_temp', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }else{

            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
            $this->db->select('printHeaderFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print_suom');


            $papersize = print_template_paper_size('CINV','A4');
            $pdfp = $this->load->view('system/invoices/erp_invoice_print_suom', $data, true);
            if ($this->input->post('html')) {
                $html = $this->load->view('system/invoices/erp_invoice_print_suom', $data, true);
                echo $html;
            } else {
                //$html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
                $this->load->library('pdf');
                //$pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                $pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN'],$printHeaderFooterYN, 'CINV');
            }
        }

    }


    function fetch_invoices_approval_suom()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $current_user_id = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_suom(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');

            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $current_user_id);
            $this->datatables->group_by('srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_suom(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }

    }

    function savesubinsurancetype(){

        $this->form_validation->set_rules('insuranceType', 'Insurance Type', 'trim|required');
        $this->form_validation->set_rules('noofMonths', 'No Of Months', 'trim|required');
        $this->form_validation->set_rules('marginPercentage', 'Margin Percentage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode( array('e', validation_errors()));
        } else {
            echo json_encode($this->Invoice_model->savesubinsurancetype());
        }
    }

    function load_sub_type()
    {
        echo json_encode($this->Invoice_model->load_sub_type());
    }

    function get_sub_type_months()
    {
        echo json_encode($this->Invoice_model->get_sub_type_months());
    }

    function fetch_customer_Dropdown_all()
    {
        $data_arr = array();
        $contractAutoID = $this->input->post('DocID');
        $Documentid = $this->input->post('Documentid');
        $customeridcurrentdoc = all_customer_drop_isactive_inactive($contractAutoID,$Documentid);
       $customerid = $this->input->post('customer');
       if($customerid)
       {
           $customer = $customerid;
       }else
       {
           $customer = '';
       }

        $companyID = $this->common_data['company_data']['company_id'];
        $customerqry = "SELECT customerAutoID,customerName,customerSystemCode,customerCountry FROM srp_erp_customermaster WHERE companyID = {$companyID} AND isActive = 1 AND  deletedYN = 0";
        $customermMaster = $this->db->query($customerqry)->result_array();
        $data_arr = array('' => 'Select Customer');
        if (!empty($customermMaster)) {
            foreach ($customermMaster as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }
        if ($contractAutoID != ' ' && !empty($customeridcurrentdoc)) {
            if ($customeridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
           }
        }
        echo form_dropdown('customerID', $data_arr, $customer, 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value);"');
    }

    function fetch_customer_Dropdown_all_contract()
    {
        $data_arr = array();
        $customerid = $this->input->post('customer');
        $contractAutoID = $this->input->post('DocID');
        $Documentid = $this->input->post('Documentid');
        $customeridcurrentdoc = all_customer_drop_isactive_inactive($contractAutoID,$Documentid);
        if($customerid)
        {
            $customer = $customerid;
        }else
        {
            $customer = '';
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $customerqry = "SELECT customerAutoID,customerName,customerSystemCode,customerCountry FROM srp_erp_customermaster WHERE companyID = {$companyID} AND isActive = 1 AND deletedYN = 0";
        $customermMaster = $this->db->query($customerqry)->result_array();
        $data_arr = array('' => 'Select Customer');
        if (!empty($customermMaster)) {
            foreach ($customermMaster as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }
        if ($contractAutoID != ' ' && !empty($customeridcurrentdoc)) {
            if ($customeridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
           }
        }
      /*  fetch_customersegmentwiseproject()*/
        echo form_dropdown('customerID', $data_arr, $customer, 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value);Load_customer_details(this.value);"');
    }

    function fetch_customer_details()
    {
        echo json_encode($this->Invoice_model->fetch_customer_details());
    }

    function fetch_customer_Dropdown_all_sales_return()
    {
        $data_arr = array();
        $customerid = $this->input->post('customer');
        $salesReturnAutoID = $this->input->post('DocID');
        $Documentid = $this->input->post('Documentid');
        $customeridcurrentdoc = all_customer_drop_isactive_inactive($salesReturnAutoID,$Documentid);
        if($customerid)
        {
            $customer = $customerid;
        }else
        {
            $customer = '';
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $customerqry = "SELECT customerAutoID,customerName,customerSystemCode,customerCountry FROM srp_erp_customermaster WHERE companyID = {$companyID} AND isActive = 1 AND deletedYN = 0 ";
        $customermMaster = $this->db->query($customerqry)->result_array();
        $data_arr = array('' => 'Select Customer');
        if (!empty($customermMaster)) {
            foreach ($customermMaster as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }
        if ($salesReturnAutoID != ' ' && !empty($customeridcurrentdoc)) {
            if ($customeridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('customerID', $data_arr, $customer, 'class="form-control select2" id="customerID" onchange="fetch_supplier_currency_by_id(this.value);"');
    }
    function fetch_customer_Dropdown_all_insurance()
    {
        $data_arr = array();
        $customerid = $this->input->post('customer');
        if($customerid)
        {
            $customer = $customerid;
        }else
        {
            $customer = '';
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $customerqry = "SELECT customerAutoID,customerName,customerSystemCode,customerCountry FROM srp_erp_customermaster WHERE companyID = {$companyID} AND deletedYN = 0";
        $customermMaster = $this->db->query($customerqry)->result_array();
        $data_arr = array('' => 'Select Customer');
        if (!empty($customermMaster)) {
            foreach ($customermMaster as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('customerID', $data_arr, $customer, 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value);"');
    }

    function load_mail_history(){
        $this->datatables->select('autoID,srp_erp_documentemailhistory.documentID,documentAutoID,sentByEmpID,toEmailAddress,sentDateTime,srp_employeesdetails.Ename2 as ename,srp_erp_customerinvoicemaster.invoiceCode')
            ->where('srp_erp_documentemailhistory.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_documentemailhistory.documentID', 'CINV')
            ->where('srp_erp_documentemailhistory.documentAutoID', $this->input->post('invoiceAutoID'))
            ->join('srp_employeesdetails','srp_erp_documentemailhistory.sentByEmpID = srp_employeesdetails.EIdNo','left')
            ->join('srp_erp_customerinvoicemaster','srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_documentemailhistory.documentAutoID','left')
            ->from('srp_erp_documentemailhistory');
        echo $this->datatables->generate();
    }

    function load_insurancetypetable(){
        $companyID = $this->common_data['company_data']['company_id'];

        $details = $this->db->query("SELECT srp_erp_invoiceinsurancetypes.insuranceTypeID AS insuranceTypeID, srp_erp_invoiceinsurancetypes.insuranceType, CONCAT( srp_erp_chartofaccounts.GLDescription, ' - ', srp_erp_chartofaccounts.GLSecondaryCode ) AS GLDescription, srp_erp_invoiceinsurancetypes.marginPercentage, srp_erp_invoiceinsurancetypes.masterTypeID, srp_erp_invoiceinsurancetypes.noofMonths FROM `srp_erp_invoiceinsurancetypes` LEFT JOIN `srp_erp_chartofaccounts` ON `srp_erp_chartofaccounts`.`GLAutoID` = `srp_erp_invoiceinsurancetypes`.`GLAutoID` WHERE `srp_erp_invoiceinsurancetypes`.`companyID` = $companyID ")->result_array();


        $data['details'] = $details;

        $html = $this->load->view('system/invoices/insurance_type_table_body', $data, true);
        echo $html;
    }

    function open_receipt_voucher_modal(){
        echo json_encode($this->Invoice_model->open_receipt_voucher_modal());
    }

    function save_receiptvoucher_from_CINV_header()
    {
        $date_format_policy = date_format_policy();
        $RVdt = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $RVcqeDte = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcqeDte, $date_format_policy);

        $this->form_validation->set_rules('RVdate', 'Receipt Voucher Date', 'trim|required');

        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
        if ($bank_detail['isCash'] == 0) {
            $this->form_validation->set_rules('RVchequeDate', 'Cheque Date', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($RVdate >= $financePeriod['dateFrom'] && $RVdate <= $financePeriod['dateTo']) {

                    echo json_encode($this->Invoice_model->save_receiptvoucher_from_CINV_header());
                } else {
                    echo json_encode(array('e', 'Receipt Voucher Date not between Financial period !'));
                }
            }else{
                echo json_encode($this->Invoice_model->save_receiptvoucher_from_CINV_header());
            }
        }
    }
    function fetch_quotation_segment()
    {
        $contractID = $this->input->post('contractAutoID');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT
	segmentID,segmentCode
FROM
	`srp_erp_contractmaster`
	where 
	companyID =  $companyID
	AND contractAutoID  = $contractID 
	")->row_array();

        echo  json_encode($data);


    }
    function fetch_invoices_DS()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ((isPreliminaryPrinted = 1 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        //$this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,tempInvoiceID,referenceNo');
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.isPreliminaryPrinted as isPreliminaryPrinted,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency as transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL( retensionTransactionAmount, 0 ) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL(retensionTransactionAmount,0), 2) as total_value_search,isDeleted,tempInvoiceID,referenceNo,srp_erp_customerinvoicemaster.isSytemGenerated as isSytemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br><b>Ref No : </b> $6 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo, isPreliminaryPrinted');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_DS(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isSytemGenerated, isPreliminaryPrinted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    function load_invoices_conformation_ds()
    {
        $convertFormat = convert_date_format_sql();
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID') ?? '');
        $companyID = current_companyID();
        $this->db->select('tempInvoiceID,approvedYN');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['emailView'] = 0;
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $data['Approved'] = $master['approvedYN'];
        $this->load->library('NumberToWords');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('CINV', $invoiceAutoID);
        $data['invoice_referenceno'] = $this->db->query("SELECT
                                                            srp_erp_customerinvoicedetails.invoiceAutoID,

                                                            IF(delordermaster.referenceNo = ' ', contract.referenceNo,IFNULL( delordermaster.referenceNo, contract.referenceNo ))  AS referenceno 
                                                        FROM
                                                            `srp_erp_customerinvoicedetails`
                                                            LEFT JOIN srp_erp_deliveryorderdetails deloreder ON deloreder.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID 
                                                            LEFT JOIN srp_erp_deliveryorder delordermaster on delordermaster.DOAutoID = deloreder.DOAutoID
                                                            LEFT JOIN srp_erp_contractmaster contract ON contract.contractAutoID = deloreder.contractAutoID 
                                                        WHERE
                                                            invoiceAutoID = '{$invoiceAutoID}' 
                                                            AND srp_erp_customerinvoicedetails.type = 'DO' 
                                                            GROUP BY 
                                                            referenceno
                                                        ")->result_array();
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',$invoiceAutoID,'CINV','invoiceAutoID');
        $VatTax = $this->db->query("SELECT
                                            COUNT(taxCategory) as taxcat
                                        FROM
                                            `srp_erp_taxledger`
                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                        WHERE
                                            documentID = 'CINV'
                                            ANd srp_erp_taxledger.companyID = {$companyID}
                                            AND documentMasterAutoID = {$invoiceAutoID}")->row('taxcat');

        $data['is_tax_invoice'] = 0;
        if($data['group_based_tax']==1 && $VatTax > 0 ){
            $data['is_tax_invoice'] = 1;
        }

        $data['date_of_supply'] = $this->db->query("SELECT
                                                               DATE_FORMAT(IFNULL(MAX(srp_erp_deliveryorder.DODate) ,(srp_erp_customerinvoicemaster.invoiceDate) ),'$convertFormat') as supplierDate
                                                        FROM
                                                            `srp_erp_customerinvoicedetails`
                                                        LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID
                                                        LEFT JOIN srp_erp_customerinvoicemaster ON  srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                                                        WHERE
                                                            srp_erp_customerinvoicedetails.companyID = {$companyID}
                                                            AND srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceAutoID}
	    ")->row('supplierDate');

        $data['invoice_referenceno_so_qut'] = $this->db->query("SELECT contractmaster.referenceNo as referenceno FROM `srp_erp_customerinvoicedetails`
	                                                    LEFT JOIN srp_erp_contractmaster contractmaster on contractmaster.contractAutoID =  srp_erp_customerinvoicedetails.contractAutoID
                                                        WHERE srp_erp_customerinvoicedetails.companyID = $companyID AND type != 'DO' AND invoiceAutoID = '{$invoiceAutoID}' GROUP BY 
                                                        srp_erp_customerinvoicedetails.contractAutoID")->row('referenceno');
        if(!empty($master['tempInvoiceID'])){
            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data_temp($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }

            $html = $this->load->view('system/invoices/erp_invoice_print_temp', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }else{

            $data['html'] = $this->input->post('html');
            $data['approval'] = $this->input->post('approval');

            $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceAutoID);
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
            $this->db->select('printHeaderFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'CINV');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();

            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;

            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print');

            $papersize = print_template_paper_size('CINV','A4');
            $data['papersize']=$papersize;
            //$pdfp = $this->load->view($printlink, $data, true);
            if ($this->input->post('html')) {
                $html = $this->load->view('system/invoices/erp_invoice_print_html_DS', $data, true);
                echo $html;
            } else {
                //$html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
                //$this->load->library('pdf');
                //$pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                //$pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
                $this->load->view($printlink, $data);

            }
        }

    }
    function fetch_invoices_approval_ds()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $current_user_id = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_ds(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,ROUND((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)), 2) as total_value_search,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName,srp_erp_customerinvoicemaster.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_customerinvoicemaster');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');

            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CINV');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_customerinvoicemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $current_user_id);
            $this->datatables->group_by('srp_erp_customerinvoicemaster.invoiceAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,CINV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CINV",invoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval_ds(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,CINV)');
            echo $this->datatables->generate();
        }

    }

    function invoice_detail_modal_operation(){
        echo json_encode($this->Invoice_model->invoice_detail_modal_operation());
    }

    function saveopDetails(){
        echo json_encode($this->Invoice_model->saveopDetails());
    }

    function saveRetentionAmnt(){
        echo json_encode($this->Invoice_model->saveRetentionAmnt());
    }

    function delete_item_direct_op()
    {
        echo json_encode($this->Invoice_model->delete_item_direct_op());
    }

    function delete_retention_amout(){
        echo json_encode($this->Invoice_model->delete_retention_amout());
    }

    function fetch_converted_price_qty_invoice()
    {
        echo json_encode($this->Invoice_model->fetch_converted_price_qty_invoice());
    }
    function fetch_converted_price_qty_invoice_new()
    {
        echo json_encode($this->Invoice_model->fetch_converted_price_qty_invoice_new());
    }
    function fetch_project_invoice_segment_customer()
    {
        $data_arr = array();
        $customerID = $this->input->post('customerID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $project_selected = '';
        if($invoiceAutoID)
        {
            $project_selected = $this->db->query("SELECT
 projectID
FROM
	`srp_erp_customerinvoicemaster`
	where 
	invoiceAutoID = $invoiceAutoID ")->row('projectID');
        }
        $segmentID = explode('|',$this->input->post('segmentID'));

        $companyID = current_companyID();
        $project = '';
        if(!empty($customerID)&& !empty($segmentID))
        {
            $project = $this->db->query("SELECT srp_erp_projects.projectID,srp_erp_projects.projectName FROM srp_erp_projects
	    INNER JOIN ( SELECT projectID,segementID,customerCode FROM srp_erp_boq_header WHERE companyID = $companyID ) boqproject ON boqproject.ProjectID = srp_erp_projects.projectID 
        WHERE companyID =$companyID AND segmentID = $segmentID[0] AND customerCode = $customerID ")->result_array();

        }
        $project_arr = array('' => 'Select a project');
        if (!empty($project)) {

            foreach ($project as $row) {
                $project_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID', $project_arr, $project_selected, 'class="form-control select2" id="projectID"');
    }

    function fetch_customer_segment_wise_contract(){

        $customerID = $this->input->post('customerID');
        $segmentID = $this->input->post('segmentID');
        $invoiceType = $this->input->post('invoiceType');
        $segment_arr = explode('|',$segmentID);
        $base_arr = array();

        if($invoiceType == 'Job'){
            //selection for contract pull
            $invoiceType = 'Contract';
        }
        
        $args_arr = array();
        $args_arr['customerID'] = $customerID;
        $args_arr['invoiceType'] = $invoiceType;
        $args_arr['transactionCurrencyID'] = null;

        $contractList = $this->Invoice_model->fetch_customer_con($args_arr,'',$segmentID);

        if($contractList){
            foreach($contractList as $contract){
                $base_arr[$contract['contractAutoID']] = $contract['contractCode'].' | '.$contract['segmentCode'].' | '.$contract['referenceNo'];
            }
        }

        echo json_encode($base_arr,true);

    }

    function fetch_contract_details(){

        echo json_encode($this->Invoice_model->fetch_contract_details());

    }


    function update_invoiceamount()
    {

        $invoicedetailID = $this->input->post('detailID');
        $headerID = $this->input->post('boqheaderID');
        $type = $this->input->post('type');
        $prevpersentage = $this->input->post('prevpersentage');


        $boqheader = $this->db->query("select retensionPercentage from srp_erp_boq_header
                                 where headerID = '{$headerID}' ")->row_array();
        $invoicedetail =  $this->db->query("select boqPreviousClaimedAmount from srp_erp_customerinvoicedetails where invoiceDetailsAutoID = $invoicedetailID")->row_array();

        if($type == 1 )
        {
            $invoiceamount_totalclaimed = $this->input->post('invoiceamount_totalclaimed');
            $invoiceamount = $this->input->post('amount');
            $prevpersentage = $this->input->post('prevpersentage');
            $claimedpercentage_varaiance = ($this->input->post('claimedpercentage') - $this->input->post('prevpersentage')) ;
            $claimedpercentage = ($this->input->post('claimedpercentage')) ;

            if( $this->input->post('prevpersentage') > $this->input->post('claimedpercentage'))
            {
                echo json_encode(array('e','Current claimed percentage should be greater than previous caimed percentage'));
                exit();
            }



        }else
        {
            $totalunitamount = $this->input->post('totalunitamount');
            $invoiceamount = $this->input->post('amount');
            $claimedpercentage =  (($invoiceamount/$totalunitamount)*100)+$prevpersentage;
            $claimedpercentage_varaiance =  ($claimedpercentage-$prevpersentage);
            $invoiceamount_totalclaimed =  (($totalunitamount)*$claimedpercentage/100);
            if($invoicedetail['boqPreviousClaimedAmount']>$invoiceamount)
            {
                echo json_encode(array('e','Invoice amount should be greater than previous invoice amount ('.$invoicedetail['boqPreviousClaimedAmount'].')'));
                exit();
            }

        }



        $data = array(
            'transactionAmount' => $invoiceamount,
            'boqTotalClaimedAmount' => $invoiceamount_totalclaimed,
            'boqTotalClaimPercentage'=>$claimedpercentage,
            'boqClaimPercentage'=>$claimedpercentage_varaiance,
        );
        $this->db->where('invoiceDetailsAutoID', $invoicedetailID);
        $result = $this->db->update('srp_erp_customerinvoicedetails', $data);
        if ($result) {
            echo json_encode(array('s','Records Updated Successfully'));
        } else {
            echo json_encode(array('e','Records Updated faild'));
        }

    }
    function  chk_exist_advancematch()
    {
        $invoiceAutoID = $this->input->post('invoiceautoID');
        $matchdetailexist = $this->db->query("SELECT matchID,matchinvoiceAutoID FROM `srp_erp_rvadvancematch` where 
	                                                matchinvoiceAutoID = $invoiceAutoID")->row_array();
        if(empty($matchdetailexist['matchinvoiceAutoID'] ) || ($matchdetailexist['matchinvoiceAutoID'] ==''))
        {
            $customerinvoicedetail = $this->db->query("SELECT invoiceDate,srp_erp_customermaster.customerSystemCode,srp_erp_customermaster.customerAutoID,srp_erp_customermaster.customerName,srp_erp_customerinvoicemaster.transactionCurrencyID,
srp_erp_customerinvoicemaster.transactionCurrency,srp_erp_customerinvoicemaster.transactionExchangeRate,srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.companyLocalCurrencyID,
srp_erp_customerinvoicemaster.companyLocalCurrency,srp_erp_customerinvoicemaster.companyLocalExchangeRate,srp_erp_customerinvoicemaster.companyLocalCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.companyReportingCurrencyID,
srp_erp_customerinvoicemaster.companyReportingCurrency,srp_erp_customerinvoicemaster.companyReportingExchangeRate,srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces,srp_erp_customermaster.customerCurrencyID,
srp_erp_customermaster.customerCurrency,srp_erp_customerinvoicemaster.customerCurrencyExchangeRate,srp_erp_customermaster.customerCurrencyDecimalPlaces
FROM srp_erp_customerinvoicemaster LEFT JOIN srp_erp_customermaster on srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID where invoiceAutoID= $invoiceAutoID ")->row_array();

            $this->db->trans_start();
            $customer_arr = $this->fetch_customer_data($customerinvoicedetail['customerAutoID']);
            $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
            $data['documentID'] = 'RVM';
            $data['matchDate'] = $customerinvoicedetail['invoiceDate'];
            $data['Narration'] = 'Receipt Voucher Auto Generated';
            $data['matchinvoiceAutoID'] = $invoiceAutoID;

            $data['customerID'] = $customer_arr['customerAutoID'];
            $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
            $data['customerName'] = $customer_arr['customerName'];
            $data['customerCurrency'] = $customer_arr['customerCurrency'];
            $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];

            $data['transactionCurrencyID'] =$customerinvoicedetail['transactionCurrencyID'];
            $data['transactionCurrency'] = $customerinvoicedetail['transactionCurrency'];
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
            $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
            $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['matchSystemCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_rvadvancematch', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                echo json_encode(array('e','Records Updated faild'));
            } else {
                echo json_encode(array('s',$last_id));
            }
        }else
        {
            echo json_encode(array('s',$matchdetailexist['matchID']));
        }
    }
    function delete_project_detail_invoice()
    {
        $invoiceautoID = $this->input->post('invoiceAutoID');
        $comapnyID = current_companyID();
        $rvmatch = $this->db->query("SELECT matchID,matchinvoiceAutoID FROM `srp_erp_rvadvancematch` where matchinvoiceAutoID = $invoiceautoID AND companyID =$comapnyID ")->row_array();
        if(!empty($rvmatch['matchinvoiceAutoID'])||$rvmatch['matchinvoiceAutoID']!='')
        {
            $this->db->delete('srp_erp_rvadvancematchdetails', array('matchID' => $rvmatch['matchID']));

        }
        $result = $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => $invoiceautoID,'type' =>'Project'));
        if ($result) {
            echo json_encode(array('s','Record deleted successfully!'));
        } else {
            echo json_encode(array('e','Error while deleting, please contact your system team!'));
        }
    }
    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function getWareHouseItemQty(){
        $detID = $this->input->post('contractDetID');
        $wareHouseID = $this->input->post('wareHouseID');

        $stock = $this->db->query("SELECT (SUM(transactionQTY/convertionRate) * conDet.conversionRateUOM) as currentStock 
                            FROM srp_erp_contractdetails conDet
                            JOIN srp_erp_itemledger items ON items.itemAutoID = conDet.itemAutoID AND wareHouseAutoID={$wareHouseID} 
                            WHERE conDet.contractDetailsAutoID = '{$detID}' ")->row('currentStock');

        $stock = (empty($stock))? 0: $stock;

        echo json_encode(['s', 'stock'=> $stock]);
    }

    function getWareHouseItemQty_bulk()
    {
        echo json_encode($this->Invoice_model->getWareHouseItemQty_bulk());
    }

    function fetch_invoices_commission()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
       
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        //$this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,tempInvoiceID,referenceNo');
        $this->datatables->select('srp_erp_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_customerinvoicemaster.confirmedByEmpID as confirmedByEmp,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency as transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,CASE WHEN invoiceType = \'DeliveryOrder\' THEN \'Delivery Order\' WHEN invoiceType = \'DirectItem\' THEN \'Direct Item\' WHEN invoiceType = \'DirectIncome\' THEN \'Direct Income\' WHEN invoiceType = \'Quotation\' THEN \'Quotation Based\' WHEN invoiceType = \'Contract\' THEN \'Contract Based\'  WHEN invoiceType = \'Sales Order\' THEN \'Sales Order Based\' WHEN invoiceType = \'Direct\' THEN \'Direct Item\'  ELSE invoiceType END as invoiceType,(IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL( retensionTransactionAmount, 0 ) - IFNULL(rebateAmount, 0) as total_value,ROUND((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0))+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexcharg.transactionAmount,0) - IFNULL(retensionTransactionAmount,0) - IFNULL(rebateAmount, 0), 2) as total_value_search,isDeleted,tempInvoiceID,referenceNo,srp_erp_customerinvoicemaster.isSytemGenerated as isSytemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails  GROUP BY invoiceAutoID) gendiscount', '(gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID) genexchargistax', '(genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID) genexcharg', '(genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster');
        $this->datatables->add_column('invoice_detail','$1','load_invoice_detail_commission(invoiceNarration,customermastername,invoiceDate,invoiceDueDate,invoiceType,referenceNo)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_commission(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted,tempInvoiceID,confirmedByEmp,isSytemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_invoice_commission_detail()
    {
       
        
        $salesPersonID = $this->input->post('salesPersonID');
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        
        $quantityRequested = $this->input->post('quantityRequested');
        //var_dump($quantityRequested);
        $estimatedAmount = $this->input->post('estimatedAmount');
        //$cat_mandetory = Project_Subcategory_is_exist();
        if(in_array('0', $quantityRequested)) {
            echo json_encode(array('e', 'Qty Cannot be zero'));
            exit();
        }
        foreach ($searches as $key => $search) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $serviceitm= $this->db->get()->row_array();
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            if($serviceitm['mainCategory']!='Service'){
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }
            } */
            
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            
        }

        if ($this->form_validation->run() == FALSE) {
            //var_dump($msg);
            $msg = explode('</p>', validation_errors());
            //var_dump($msg);
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
           // echo json_encode($this->Invoice_model->save_invoice_item_detail());
        } 
        //echo json_encode(array('s', 'Invoice Detail : Saved Successfully.'));

    }
    function invoice_commission_download_csv()
    {
        //$companyID = current_companyID();
        /* $empArr = $this->db->query("SELECT * FROM srp_erp_customerinvoicedetails WHERE companyID={$companyID}
                                    AND isPayrollEmployee =1 AND isDischarged=0 AND empConfirmedYN=1 {$segmentFilter}")->result_array(); */
        //$empArr = $this->db->query("")->result_array();

        $csv_data = [
            [
                0 => 'Sales Person Code',
                1 => 'Item Code',
                2 => 'Qty',
                3 => 'Unit Price'
            ]
        ];
        /* 
        foreach ($empArr as $key => $row) {
            $csv_data[$key + 1][1] = $row['ECode'];
            $csv_data[$key + 1][2] = $row['Ename2'];
            $csv_data[$key + 1][3] = '0';
        } */

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=file.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }

    public function commission_excelUpload()
    {
        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $i = 0;
        $m = 0;
        if (empty($masterID)) {
            die(json_encode(['e', 'Id field is required']));
        }
        if (empty($wareHouseAutoID)) {
            die(json_encode(['e', 'Warehouse field is required']));
        }
        

        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) 
        {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
        
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
            $this->db->where('invoiceAutoID', $masterID);
            $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
                  
            $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('companyID', $companyID);
            $wareHouseDetails = $this->db->get('srp_erp_warehousemaster')->row_array();

            //Get all salesperson in the company
            $salesPersonArr = $this->db->query("SELECT EIdNo AS salesPersonID,EmpSecondaryCode AS SalesPersonCode,Ename1 AS SalesPersonName FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged = 0 ")->result_array();
            $salesPerson_list = array_column($salesPersonArr, 'SalesPersonCode');
            
            $itemArr = $this->db->query("SELECT * FROM srp_erp_itemmaster WHERE companyID={$companyID}")->result_array();
            $item_list = array_column($itemArr, 'seconeryItemCode');
            
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            $unMatchRecords = [];
            $unMatchItemRecords = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                
                if ($i > 0) {
                    $excelSalesPersonCode = trim($getData[0] ?? '');
                    $designation = $this->db->query("SELECT `empdesig`.`DesignationID` AS `DesignationID` FROM
                        `srp_employeedesignation` empdesig
                        JOIN `srp_designation` desig ON `desig`.`DesignationID` = `empdesig`.`DesignationID` AND `desig`.`isDeleted` = 0 
                        AND `desig`.`Erp_companyID` = {$companyID}
                        JOIN `srp_employeesdetails` emp ON `emp`.`EIdNo` = `empdesig`.`EmpID` AND emp.Erp_companyID = {$companyID}	AND empConfirmedYN = 1 
                        AND emp.isDischarged = 0 
                        WHERE empdesig.Erp_companyID = {$companyID} AND
                        emp.EmpSecondaryCode = '$excelSalesPersonCode' ORDER BY isMajor DESC ")->row('DesignationID');

                    $excelItemSecondaryCode = trim($getData[1] ?? '');
                    $excelQty = trim($getData[2] ?? '');
                    $amount = str_replace(',', '', $excelQty);
                    $excelUnitPrice = trim($getData[3] ?? '');
                   
                    if (!empty($excelSalesPersonCode) && !empty($excelItemSecondaryCode) && ($excelQty > 0) && !empty($excelUnitPrice)) {

                        $keys = array_keys($salesPerson_list, $excelSalesPersonCode);
                        $keysi = array_keys($item_list, $excelItemSecondaryCode);

                        $thisSalesPersonData = array_map(function ($k) use ($salesPersonArr) {
                            return $salesPersonArr[$k];
                        }, $keys);
                        $thisItemData = array_map(function ($k) use ($itemArr) {
                            return $itemArr[$k];
                        }, $keysi);
                        
                        if (!empty($thisSalesPersonData[0]) ) {
                            if (!empty($designation) ) {
                                    $dataExcel[$m]['salesPersonID'] = $thisSalesPersonData[0]['salesPersonID'];
                                    $dataExcel[$m]['designationID'] = $designation;
                                    
                                    if (!empty($thisItemData[0]) ) {
                                    $dataExcel[$m]['itemAutoID'] = $thisItemData[0]['itemAutoID'];
                                    $dataExcel[$m]['requestedQty'] = $excelQty;
                                    $dataExcel[$m]['unitPrice'] = $excelUnitPrice;
                                    $stock = $this->db->query('SELECT IFNULL(SUM(transactionQTY/convertionRate),0) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $dataExcel[$m]['itemAutoID'] . '" ')->row_array();
                                    if ($stock['currentStock']<= $excelQty ) {
                                        $unMatchQtyRecords[] = ' &nbsp;&nbsp;- ' . $excelItemSecondaryCode;
                                    } 

                                }else{
                                    $unMatchItemRecords[] = ' &nbsp;&nbsp;- ' . $excelItemSecondaryCode;
                                }

                            $m++;
                            } else {
                                $emptyDesignationRecords[] = ' &nbsp;&nbsp;- ' . $excelSalesPersonCode;
                            }
                        } else {
                            $unMatchRecords[] = ' &nbsp;&nbsp;- ' . $excelSalesPersonCode;
                        }
                    }
                }
                $i++;
            }
            fclose($file);
            if (!empty($unMatchRecords) ) {
                
                $msg = '<strong>Following Sales Person codes does not match with the database.</strong><br/>';
                $msg .= implode('<br/>', $unMatchRecords);
                die(json_encode(['m', $msg]));
            }
            if (!empty($emptyDesignationRecords)) {
                $msg = '<strong>Designation not assigned for following records.</strong><br/>';
                $msg .= implode('<br/>', $emptyDesignationRecords);
                die(json_encode(['m', $msg]));
            }
            if (!empty($unMatchItemRecords)) {
                $msg = '<strong>Following Item codes does not match with the database.</strong><br/>';
                $msg .= implode('<br/>', $unMatchItemRecords);
                die(json_encode(['m', $msg]));
            }
            if (!empty($unMatchQtyRecords)) {
                $msg = '<strong>Following Items quantity exceeds current stock.</strong><br/>';
                $msg .= implode('<br/>', $unMatchQtyRecords);
                die(json_encode(['m', $msg]));
            }

            if (!empty($dataExcel)) {
               //echo '<pre>'; print_r($dataExcel); echo '</pre>';
               $notMatchRecords = [];
               //$groupCommissionBy = array_group_by($dataExcel, 'itemAutoID');
              // echo '<pre>'; print_r($dataExcel); echo '</pre>';

                $data = [];
                $k = 0;
                $canPull = true;
                foreach ($dataExcel as $key => $row) {
                    $itemAutoID = $row['itemAutoID'];
                    //echo '<pre>'; print_r($itemAutoID); echo '</pre>';
                   
                    $item_data = fetch_item_data($itemAutoID);
                    // echo '<pre>'; print_r($item_data); echo '</pre>';

                    $data[$k]['unitOfMeasure'] = trim($item_data['defaultUnitOfMeasure'] ?? '');
                    $data[$k]['unitOfMeasureID'] = $item_data['defaultUnitOfMeasureID'];
                    $data[$k]['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                    $data[$k]['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                    $data[$k]['conversionRateUOM'] = conversionRateUOM_id($data[$k]['unitOfMeasureID'], $data[$k]['defaultUOMID']);
                    
                  
                    $data[$k]['itemAutoID'] = $row['itemAutoID'];
                    $data[$k]['itemSystemCode'] = $item_data['itemSystemCode'];
                    
                    $data[$k]['itemDescription'] = $item_data['itemDescription'];
                    
                    //fetch_sales_price($item_data['companyLocalSellingPrice'], this, $data[$k]['defaultUOMID'], $row['itemAutoID']);
                    $localCurrencyER = 1 / $item_data['companyLocalExchangeRate'];
                    //$salesprice = trim($item_data['companyLocalSellingPrice'] ?? '');
                    //$data[$k]['unittransactionAmount'] = round(($salesprice / $localCurrencyER), $master['transactionCurrencyDecimalPlaces']);
                    $data[$k]['unittransactionAmount'] = round(($row['unitPrice']), $master['transactionCurrencyDecimalPlaces']);
                    

                    $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();



                    $data[$k]['requestedQty'] = $row['requestedQty'];
                    //$data[$k]['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
                    




                    $transactionAmount = $data[$k]['unittransactionAmount'] * $data[$k]['requestedQty'];
                    $data[$k]['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $data[$k]['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $data[$k]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $data[$k]['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $data[$k]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $data[$k]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $data[$k]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                    $data[$k]['comment'] = '';
                    $data[$k]['remarks'] = '';
                    
                    if($item_data['mainCategory']=="Service") {
                        $data[$k]['wareHouseAutoID'] = 0;
                        $data[$k]['wareHouseCode'] = null;
                        $data[$k]['wareHouseLocation'] = null;
                        $data[$k]['wareHouseDescription'] = null;
                    }else{
                        $data[$k]['wareHouseAutoID'] = $wareHouseAutoID;
                        $data[$k]['wareHouseCode'] = trim($wareHouseDetails['wareHouseCode'] ?? '');
                        $data[$k]['wareHouseLocation'] = trim($wareHouseDetails['wareHouseLocation'] ?? '');
                        $data[$k]['wareHouseDescription'] = trim($wareHouseDetails['wareHouseDescription'] ?? '');
                    }


                    $data[$k]['type'] = 'Commission';

                    $data[$k]['invoiceAutoID'] = $masterID;
                    $data[$k]['segmentID'] = $master['segmentID'];
                    $data[$k]['segmentCode'] = $master['segmentCode'];
                    $data[$k]['expenseGLAutoID'] = $item_data['costGLAutoID'];
                    $data[$k]['expenseGLCode'] = $item_data['costGLCode'];
                    $data[$k]['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data[$k]['expenseGLDescription'] = $item_data['costDescription'];
                    $data[$k]['expenseGLType'] = $item_data['costType'];
                    $data[$k]['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
                    $data[$k]['revenueGLCode'] = $item_data['revanueGLCode'];
                    $data[$k]['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
                    $data[$k]['revenueGLDescription'] = $item_data['revanueDescription'];
                    $data[$k]['revenueGLType'] = $item_data['revanueType'];
                    $data[$k]['assetGLAutoID'] = $item_data['assteGLAutoID'];
                    $data[$k]['assetGLCode'] = $item_data['assteGLCode'];
                    $data[$k]['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data[$k]['assetGLDescription'] = $item_data['assteDescription'];
                    $data[$k]['assetGLType'] = $item_data['assteType'];
                    $data[$k]['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                    $data[$k]['itemCategory'] = $item_data['mainCategory'];
                            
                    $data[$k]['salesPersonID'] = $row['salesPersonID'];
                    $data[$k]['designationID'] = $row['designationID'];

                    $data[$k]['taxSupplierCurrencyExchangeRate'] = 1;
                    $data[$k]['taxSupplierCurrencyDecimalPlaces'] = 2;
                    $data[$k]['taxSupplierCurrencyAmount'] = 0;
                    $data[$k]['companyID'] = $this->common_data['company_data']['company_id'];
                    $data[$k]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data[$k]['createdUserGroup'] = $this->common_data['user_group'];
                    $data[$k]['createdPCID'] = $this->common_data['current_pc'];
                    $data[$k]['createdUserID'] = $this->common_data['current_userID'];
                    $data[$k]['createdUserName'] = $this->common_data['current_user'];
                    $data[$k]['createdDateTime'] = $this->common_data['current_date']; 
                    //$commissionData = $this->db->query(" ")->row_array();
                    $k++;
                    

                }
                //echo '<pre>'; print_r($data); echo '</pre>';
                if (!empty($data)) {

                    $this->db->trans_start();
                    $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        die(json_encode(['e', 'Error in process']));
                    } else {
                        $this->db->trans_commit();
                        die(json_encode(['s', 'Successfully uploaded']));
                    }

                }
            } else {
                die(json_encode(['e', 'File is empty']));
            }
        }else{
            echo json_encode(['e', 'Please Select CSV File .']);
        }


    }

    function fetch_detail_cs()
    {
        $data['master'] = $this->Invoice_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['marginpercent'] = 0;
        if(!empty($data['master']['insuranceTypeID'])){
            $this->db->select('marginPercentage');
            $this->db->where('insuranceTypeID', trim($data['master']['insuranceSubTypeID']));
            $this->db->from('srp_erp_invoiceinsurancetypes');
            $margindetails = $this->db->get()->row_array();
            $data['marginpercent'] = $margindetails['marginPercentage'];
        }
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['supplier_arr'] = all_supplier_drop();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['openContractPolicy'] = getPolicyValues('OCE', 'All');
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $data['invoiceproject'] = $this->db->query("SELECT detailid.itemDescription,	IF(isVariation = 1,variationAmount,totalTransCurrency) as totalTransCurrency,invoicemaster.transactionCurrencyDecimalPlaces,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,detailID,
            unitRateTransactionCurrency,
            IFNULL(srp_erp_customerinvoicedetails.transactionAmount,0) as transactionAmount,
            IFNULL(srp_erp_customerinvoicedetails.boqClaimPercentage,0) as boqClaimPercentage,
            isVariation,
            header.retensionPercentage,
            header.headerID,
            header.projectID,
            srp_erp_customerinvoicedetails.invoiceAutoID,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,
            srp_erp_customerinvoicedetails.boqDetailID,
           IFNULL( boqPreviousClaimPercentage,0) as boqPreviousClaimPercentage,
           IFNULL( boqTotalClaimPercentage,0) as boqTotalClaimPercentage
             FROM `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_boq_details detailid on detailid.detailID =srp_erp_customerinvoicedetails.boqDetailID
            LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.InvoiceAutoID =srp_erp_customerinvoicedetails.InvoiceAutoID
            LEFT JOIN srp_erp_boq_header header on header.headerID = detailid.headerID
             where  Type = 'Project' AND srp_erp_customerinvoicedetails.invoiceAutoID = {$data['invoiceAutoID']}
            ORDER BY
            isVariation asc")->result_array();
        $this->load->view('system/invoices/invoices_detail_cs.php', $data);
    }

    function save_invoice_approval_cs()
    {
        $system_code = trim($this->input->post('invoiceAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

            if ($status == 1) {
                $approvedYN = checkApproved($system_code, 'CINV', $level_id);
                if ($approvedYN) {
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->db->select('invoiceAutoID');
                    $this->db->where('invoiceAutoID', trim($system_code));
                    $this->db->where('confirmedYN', 2);
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $po_approved = $this->db->get()->row_array();
                    if (!empty($po_approved)) {
                        echo json_encode(array('w', 'Document already rejected', 1));
                    } else {
                        $this->form_validation->set_rules('status', 'Status', 'trim|required');
                        if ($this->input->post('status') == 2) {
                            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                        }
                        $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                        if ($this->form_validation->run() == FALSE) {
                            echo json_encode(array('e', validation_errors(), 1));
                        } else {
                            echo json_encode($this->Invoice_model->save_invoice_approval_cs());
                        }
                    }
                }
            }
            else if ($status == 2) {
                $this->db->select('invoiceAutoID');
                $this->db->where('invoiceAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_customerinvoicemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected', 1));
                } else {
                    $rejectYN = checkApproved($system_code, 'CINV', $level_id);
                    if (!empty($rejectYN)) {
                        echo json_encode(array('w', 'Document already approved', 1));
                    } else {
                        $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                        if ($this->input->post('status') == 2) {
                            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                        }
                        $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                        if ($this->form_validation->run() == FALSE) {
                            echo json_encode(array('e', validation_errors(), 1));
                        } else {
                            echo json_encode($this->Invoice_model->save_invoice_approval_cs());
                        }
                    }
                }
            }
    }

    function fetch_invoice_commission()
    {
        $convertFormat = convert_date_format_sql();
        $status_filter='';
        $status = $this->input->post('statusFilter');
        if ($status != 'all') {
            $status_filter = " AND ( srp_erp_invoice_commision.confirmedYN = 1 AND srp_erp_invoice_commision.approvedYN = 1)";
            switch ($status){
                case 1:  $status_filter = " AND ( srp_erp_invoice_commision.confirmedYN = 0 AND srp_erp_invoice_commision.approvedYN = 0)";  break;
                case 2:  $status_filter = " AND ( srp_erp_invoice_commision.confirmedYN = 1 AND srp_erp_invoice_commision.approvedYN = 0)";  break;
                case 4:  $status_filter = " AND ((srp_erp_invoice_commision.confirmedYN = 3 AND srp_erp_invoice_commision.approvedYN != 1) or (srp_erp_invoice_commision.confirmedYN = 2 AND srp_erp_invoice_commision.approvedYN != 1))";  break;
            }
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_invoice_commision.companyID = " . $companyid . $status_filter . "";
        
        //$this->datatables->select("srp_erp_invoice_commision.commissionAutoID as commissionAutoID,srp_erp_invoice_commision.confirmedByEmpID as confirmedByEmp ,IFNULL(det.transactionAmount ,0) as transactionAmount, ROUND(det.transactionAmount, 2) as transactionAmount_search,srp_erp_invoice_commision.confirmedYN as confirmedYN ,srp_erp_invoice_commision.approvedYN as approvedYN,srp_erp_invoice_commision.documentSystemCode as documentSystemCode ,srp_erp_invoice_commision.createdUserID as createdUserID, srp_erp_customerinvoicemaster.invoiceCode as invoiceCode");
        $this->datatables->select("srp_erp_invoice_commision.commissionAutoID as commissionAutoID,
        srp_erp_invoice_commision.confirmedByEmpID as confirmedByEmp ,srp_erp_invoice_commision.confirmedYN as confirmedYN ,
        srp_erp_invoice_commision.approvedYN as approvedYN,srp_erp_invoice_commision.documentSystemCode as documentSystemCode,
        srp_erp_invoice_commision.createdUserID as createdUserID, srp_erp_customerinvoicemaster.invoiceCode as invoiceCode,
        srp_erp_customerinvoicemaster.invoiceDate as invoiceDate,srp_erp_invoice_commision.isDeleted as isDeleted");

        $this->datatables->where($where);
        $this->datatables->from('srp_erp_invoice_commision');
        $this->datatables->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_invoice_commision.invoiceID', 'left');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>  </b> $1 </div>', 'transactionAmount');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"IC",commissionAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"IC",commissionAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_ic_action(commissionAutoID,confirmedYN,approvedYN,createdUserID,confirmedByEmp,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('invoiceDate', '<span >$1 </span>', 'convert_date_format(invoiceDate)');
        echo $this->datatables->generate();
    }

    /* function invoice_commission_confirmation()
    {
        $commissionAutoID = trim($this->input->post('commissionAutoID') ?? '');

        //echo json_encode($this->Invoice_model->invoice_confirmation());
        echo json_encode(array('s', 'Document confirmed successfully'));

    } */


    function load_invoice_commission_confirmation()
    {
        $data['type'] = $this->input->post('html');
        $commissionAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('commissionAutoID') ?? '');
        $data['confirmedYN'] = ($this->input->post('confirmedYN') ? $this->input->post('confirmedYN') : 1);
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $data['extra'] = $this->Invoice_model->fetch_ic_data($commissionAutoID);

        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/delivery_order/erp_ic_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function invoice_commission_confirmation()
    {
        echo json_encode($this->Invoice_model->invoice_commission_confirmation());
    }


    function referbackic()
    {
        $commissionAutoID = $this->input->post('commissionAutoID');
        $this->db->select('approvedYN,commissionAutoID');
        $this->db->where('commissionAutoID', trim($commissionAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_invoice_commision');
        $approved_invoice_commission_master = $this->db->get()->row_array();
        if (!empty($approved_invoice_commission_master)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_invoice_commission_master['documentSystemCode']));
        }
        else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($commissionAutoID, 'IC');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function fetch_invoice_commission_approval()
    {
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select("srp_erp_invoice_commision.commissionAutoID AS commissionAutoID, srp_erp_invoice_commision.documentSystemCode as documentSystemCode,
            srp_erp_invoice_commision.confirmedYN,
            srp_erp_documentapproved.approvedYN AS approvedYN,
            documentApprovedID,
            approvalLevelID, srp_erp_customerinvoicemaster.invoiceCode as invoiceCode");
            $this->datatables->from('srp_erp_invoice_commision');
            $this->datatables->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_invoice_commision.invoiceID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = `srp_erp_invoice_commision`.`commissionAutoID`  AND srp_erp_documentapproved.approvalLevelID = srp_erp_invoice_commision.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_invoice_commision.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'IC');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'IC');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_invoice_commision.companyID', $companyID);
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"IC",commissionAutoID)');
            $this->datatables->add_column('edit', '$1', 'ic_action_approval(commissionAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_invoice_commision.commissionAutoID AS commissionAutoID, srp_erp_invoice_commision.documentSystemCode as documentSystemCode,
            srp_erp_invoice_commision.confirmedYN,
            srp_erp_documentapproved.approvedYN AS approvedYN,
            documentApprovedID,
            approvalLevelID, srp_erp_customerinvoicemaster.invoiceCode as invoiceCode', false);
            $this->datatables->from('srp_erp_invoice_commision');
            $this->datatables->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_invoice_commision.invoiceID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_invoice_commision.commissionAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'IC');
            $this->datatables->where('srp_erp_invoice_commision.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_invoice_commision.commissionAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"IC",commissionAutoID)');
            $this->datatables->add_column('edit', '$1', 'ic_action_approval(commissionAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_ic_approval()
    {
        $auto_id = trim($this->input->post('commissionAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        $this->form_validation->set_rules('commissionAutoID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('Level', 'Level', 'trim|required');
        if ($status == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $approvedYN = checkApproved($auto_id, 'IC', $level_id);
        //$approvedYN = true;
        if ($approvedYN) {
            die( json_encode(['w', 'Document already approved', 1]) );
        }

        $document_status = $this->db->get_where('srp_erp_invoice_commision', ['commissionAutoID'=>$auto_id])->row('confirmedYN');
        if ($document_status == 2) {
            die( json_encode(['w', 'Document already rejected', 1]) );
        }

        $this->load->library('approvals');
        $approvals_status = $this->approvals->approve_document($auto_id, $level_id, $status, $comments, 'IC');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in Delivery order approval process.', 1];
        } else {
            $this->db->trans_commit();

            switch ($approvals_status){
                case 1: echo  json_encode( ['s', 'Delivery order fully approved.']); break;
                case 2: echo  json_encode( ['s', 'Delivery order level - '.$level_id.' successfully approved']); break;
                case 3: echo  json_encode( ['s', 'Delivery order successfully rejected.']); break;
                case 5: echo  json_encode( ['w', 'Previous Level Approval Not Finished']); break;
                default : echo  json_encode( ['e', 'Error in Delivery order approvals process']);
            }
        }
    }
    
    function update_acknowledgementDate_CINV()
    {
        $pK = explode('|',trim($this->input->post('pk') ?? ''));
        $date = date_create($pK[1]);
        $this->form_validation->set_rules("value", "Date", 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors(),(date_format($date,"Y-m-d")),$pK[0]]);
         } else {
            echo json_encode($this->Invoice_model->update_acknowledgementDate_CINV());
        }

    }

    function export_excel_invoice()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Invoices');
        $this->load->database();
        $data = $this->Invoice_model->fetch_invoice_excel();

        $header = ['#', 'Invoice Code', 'Document Date', 'Due Date', 'Acknowledgement Date', 'Customer Name', 'Type', 'Reference Number', 'Comment', 'Currency', 'Amount', 'Confirmed Status', 'Approved Status', 'Deleted Status', 'Preliminary Submitted'];
       
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells("A1:E1");
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A4:O4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Invoice List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:O4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:O4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $y=6;
        foreach ($data as $val) {
            $this->excel->getActiveSheet()->setCellValue('A' . $y, $val['Num']);
            $this->excel->getActiveSheet()->setCellValue('B' . $y, $val['invoiceCode']);
            $this->excel->getActiveSheet()->setCellValue('C' . $y, $val['documentDate']);
            $this->excel->getActiveSheet()->setCellValue('D' . $y, $val['dueDate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $y, $val['acknowledgementDate']);
            $this->excel->getActiveSheet()->setCellValue('F' . $y, $val['customerName']);
            $this->excel->getActiveSheet()->setCellValue('G' . $y, $val['type']);
            $this->excel->getActiveSheet()->setCellValue('H' . $y, $val['referenceNumber']);
            $this->excel->getActiveSheet()->setCellValue('I' . $y, $val['comment']);
            $this->excel->getActiveSheet()->setCellValue('J' . $y, $val['currency']);
            $this->excel->getActiveSheet()->setCellValue('K' . $y, $val['amount']);
            $format_decimal = ( $val['decimalPlace'] == 3)? '#,##0.000': '#,##0.00';
            $this->excel->getActiveSheet()->getStyle('K' . $y)->getNumberFormat()->setFormatCode($format_decimal);

            $this->excel->getActiveSheet()->setCellValue('L' . $y, $val['confirmed']);
            $this->excel->getActiveSheet()->setCellValue('M' . $y, $val['approved']);
            $this->excel->getActiveSheet()->setCellValue('N' . $y, $val['deleted']);
            $this->excel->getActiveSheet()->setCellValue('O' . $y, $val['preliminaryPrinted']);
            $y++;
        }
        $filename = 'Invoice Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function update_preliminaryPrint_status_update()
    {
        echo json_encode($this->Invoice_model->update_preliminaryPrint_status_update());
    }

    function save_invoice_header_commission()
    {
        //$acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('customerInvoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $docDate = $this->input->post('invoiceDate');
        $documentDate = input_format_date($docDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('invoiceDueDate', 'Invoice Due Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        /* if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
            $this->form_validation->set_rules('acknowledgeDate', 'Acknowledgemenr Date', 'trim|required');
        } */
        
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        if ($this->input->post('invoiceType') == 'Direct') {
            //$this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
            //$this->form_validation->set_rules('invoiceNarration', 'Narration', 'trim|required');
        }
        if($this->input->post('invoiceType') == 'Project')
        {

            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if (($invoiceDate) > ($invoiceDueDate)) {
                $this->session->set_flashdata('e', ' Invoice Due Date cannot be less than Invoice Date!');
                echo json_encode(FALSE);
            } else {
                if($financeyearperiodYN==1) {
                    $financearray = $this->input->post('financeyear_period');
                    $financePeriod = fetchFinancePeriod($financearray);
                    if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                        echo json_encode($this->Invoice_model->save_invoice_header_commission());
                    } else {
                        $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                        echo json_encode(FALSE);
                    }
                }else{
                    echo json_encode($this->Invoice_model->save_invoice_header_commission());
                }
            }
        }
    }

    function fetch_discount_setup_percentage()
    {
        echo json_encode($this->Invoice_model->fetch_discount_setup_percentage());
    }
    function fetch_lineWiseTax(){
        $return = fetch_line_wise_itemTaxcalculation(trim($this->input->post('taxCalculationFormulaID') ?? ''),trim($this->input->post('total') ?? ''),trim($this->input->post('discount') ?? ''),trim($this->input->post('documentID') ?? ''),trim($this->input->post('documentMasterID') ?? ''));
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $tax = 0;
        } else {
            $tax = $return['amount'];
        }
        echo json_encode($tax);
    }

    function fetch_line_tax_and_vat()
    {
        echo json_encode($this->Invoice_model->fetch_line_tax_and_vat());
    }

    function load_line_tax_amount()
    {
        echo json_encode($this->Invoice_model->load_line_tax_amount());
    }

    function update_supply_date()
    {
        $pK = explode('|',trim($this->input->post('pk') ?? ''));
        $date = date_create($pK[1]);
        $this->form_validation->set_rules("value", "Date", 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors(),(date_format($date,"Y-m-d")),$pK[0]]);
        } else {
            echo json_encode($this->Invoice_model->update_supply_date());
        }

    }

    function fetch_do_detail_table()
    {
        echo json_encode($this->Invoice_model->fetch_do_detail_table());
    }

    function getWareHouseItemQtyDO(){
        $detID = $this->input->post('DODetailsAutoID');
        $wareHouseID = $this->input->post('wareHouseID');

        $stock = $this->db->query("SELECT (SUM(transactionQTY/convertionRate) * doDet.conversionRateUOM) as currentStock 
                            FROM srp_erp_deliveryorderdetails doDet
                            JOIN srp_erp_itemledger items ON items.itemAutoID = doDet.itemAutoID AND items.wareHouseAutoID={$wareHouseID} 
                            WHERE doDet.DODetailsAutoID = '{$detID}' ")->row('currentStock');

        $stock = (empty($stock))? 0: $stock;

        echo json_encode(['s', 'stock'=> $stock]);
    }

    function save_do_base_items()
    {
        $ids = $this->input->post('DetailsID');
        foreach ($ids as $key => $id) {
            $num = ($key + 1);
            $this->form_validation->set_rules("DetailsID[{$key}]", "Line {$num} ID", 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", "Line {$num} Amount", 'trim|required');
            // $this->form_validation->set_rules("wareHouseAutoID[{$key}]", "Line {$num} WareHouse", 'trim|required');
            // $this->form_validation->set_rules("qty[{$key}]", "Line {$num} QTY", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Invoice_model->save_do_base_items());
        }
    }

    function load_customer_dropdown()
    {
        echo $this->Invoice_model->load_customer_dropdown();
    }
    function load_invoice_template_list()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $invoicedetail =  $this->db->query("SELECT invoiceTemplateMasterID,invoiceTemplateName FROM srp_erp_invoicetemplatemaster WHERE status = 1 AND companyID = {$companyID}")->result_array();
        echo json_encode($invoicedetail);
    }

    function get_job_item_details(){

        echo json_encode($this->Invoice_model->get_job_item_details());

    }

    function add_job_based_items(){
    
        // echo json_encode($this->Invoice_model->add_job_based_items());
        echo json_encode($this->Invoice_model->add_job_based_billing_items());
    }

    function get_retension_details(){
        echo json_encode($this->Invoice_model->get_retension_details());
    }

    function create_retension_invoice(){

        $documentID = $this->input->post('documentID');

        if($documentID == 'CINV'){
            echo json_encode($this->Invoice_model->create_retension_invoice_cinv());
        }else{
            echo json_encode($this->Invoice_model->create_retension_invoice_sup());
        }

        
    }
    function fetch_customer($status = TRUE, $IsActive = null, $resultDestination=null){
        $interCompayYN=$this->input->post('ischeck');
        $CI = &get_instance();
        $CI->db->select("customerAutoID,customerName,customerSystemCode,customerCountry");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('deletedYN', 0);
        $CI->db->where('interCompayYN', $interCompayYN);
        if ($IsActive == 1) {
            $CI->db->where('isActive', 1);
        }

        $customer = $CI->db->get()->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = [];
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }

            if($resultDestination=='vat_report'){//this part will display only on customer drop down in vat report.
                $customer_arr[0] = 'Other';
            }

        }
        
        echo json_encode($customer_arr);
    }

   
}