<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payable extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Payable_modal');
        $this->load->helpers('payable');
        $this->load->helpers('exceedmatch');
    }

    function fetch_debit_note()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( debitNoteDate >= '" . $datefromconvert . " 00:00:00' AND debitNoteDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( debitNoteCode Like '%$search%' ESCAPE '!') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (debitNoteDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%') OR (docRefNo Like '%$sSearch%')) ";
        }
        $where = "srp_erp_debitnotemaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches. "";
        $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,srp_erp_debitnotemaster.documentID AS documentID,debitNoteCode,
                DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,comments,srp_erp_suppliermaster.supplierName as suppliername,confirmedYN,approvedYN,
                srp_erp_debitnotemaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,
                det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_debitnotemaster.confirmedByEmpID as confirmedByEmp, srp_erp_debitnotemaster.docRefNo AS docRefNo');
        $this->datatables->join('(SELECT (SUM( transactionAmount )) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_debitnotemaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('dn_detail', '<b>Supplier Name : </b> $2 <br> <b>Debit Note Date : </b> $3  <br><b>Comments : </b> $1 <br><b> Ref No : </b> $5', 'comments,suppliername,debitNoteDate,transactionCurrency,docRefNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_Debit_note_action(debitNoteMasterAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_supplier_invoices()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }

            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');

        $searches='';

        $where = "srp_erp_paysupplierinvoicemaster.companyID=" . $companyid . $supplier_filter . $date . $status_filter . $searches .  "";
        $this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,confirmedYN,approvedYN,srp_erp_paysupplierinvoicemaster.createdUserID as createdUser,
                                    CASE WHEN invoiceType = 'GRV Base' THEN 'GRV Base' WHEN invoiceType = 'StandardPO' THEN 'PO Invoice' WHEN invoiceType = 'StandardItem' THEN 'Direct Item' WHEN invoiceType = 'Standard' THEN 'Direct Item' 	WHEN invoiceType = 'StandardExpense' THEN 'Direct Expense' ELSE invoiceType END AS invoiceType, srp_erp_suppliermaster.supplierName as suppliermastername,transactionCurrencyDecimalPlaces,((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 ) AS total_value,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value_search,isDeleted,srp_erp_paysupplierinvoicemaster.confirmedByEmpID as confirmedByEmp,srp_erp_paysupplierinvoicemaster.isSytemGenerated as isSytemGenerated,IFNULL(srp_erp_paysupplierinvoicemaster.supplierInvoiceNo,'-') AS supplierInvoiceNo,IFNULL( DATE_FORMAT(invoiceDate,'$convertFormat'),'-') AS supplierInvoiceDate, srp_erp_paysupplierinvoicemaster.RefNo AS RefNo, srp_erp_paysupplierinvoicemaster.totalRetension as totalRetension");
        //$this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,InvoiceAutoID,confirmedYN,approvedYN,createdUserID,invoiceType,supplierName,transactionCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,SUM(taxAmount) as taxAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID', 'left');
        $this->datatables->from('srp_erp_paysupplierinvoicemaster');
        $this->datatables->add_column('detail', '$1', 'fetch_bsi_details(comments,suppliermastername,bookingDate,transactionCurrency,invoiceType,invoiceDueDate,supplierInvoiceNo,supplierInvoiceDate,RefNo,InvoiceAutoID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$1 : </b> $2 </div>', 'transactionCurrency,supplier_invoice_total_value(InvoiceAutoID,transactionCurrencyDecimalPlaces)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_supplier_invoice_action(InvoiceAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp,isSytemGenerated,totalRetension)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_supplier_invoices_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }

            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');

        $searches='';
        /*if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND ((bookingInvCode Like '%$search%' ESCAPE '!') OR (invoiceType Like '%$sSearch%' ESCAPE '!') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (bookingDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%'))";

        } SME-3150 */



        $where = "srp_erp_paysupplierinvoicemaster.companyID=" . $companyid . $supplier_filter . $date . $status_filter . $searches .  "";
        $this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,confirmedYN,approvedYN,srp_erp_paysupplierinvoicemaster.createdUserID as createdUser,
                                    CASE WHEN invoiceType = 'GRV Base' THEN 'GRV Base' WHEN invoiceType = 'StandardPO' THEN 'PO Invoice' WHEN invoiceType = 'StandardItem' THEN 'Direct Item' WHEN invoiceType = 'Standard' THEN 'Direct Item' 	WHEN invoiceType = 'StandardExpense' THEN 'Direct Expense' ELSE invoiceType END AS invoiceType, srp_erp_suppliermaster.supplierName as suppliermastername,transactionCurrencyDecimalPlaces,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value_search,isDeleted,srp_erp_paysupplierinvoicemaster.confirmedByEmpID as confirmedByEmp,srp_erp_paysupplierinvoicemaster.isSytemGenerated as isSytemGenerated,IFNULL(srp_erp_paysupplierinvoicemaster.supplierInvoiceNo,'-') AS supplierInvoiceNo,IFNULL( DATE_FORMAT(invoiceDate,'$convertFormat'),'-') AS supplierInvoiceDate, srp_erp_paysupplierinvoicemaster.RefNo AS RefNo ");
        //$this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,InvoiceAutoID,confirmedYN,approvedYN,createdUserID,invoiceType,supplierName,transactionCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID', 'left');
        $this->datatables->from('srp_erp_paysupplierinvoicemaster');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Document Date : </b> $3 &nbsp;&nbsp; | &nbsp;&nbsp;<b>Invoice Due Date : </b> $6 <br>
<b> Supplier Invoice No : </b> $7 &nbsp;&nbsp; | &nbsp;&nbsp;<b>Supplier Invoice Date : $8</b><br>
<b> Type : </b> $5 &nbsp;&nbsp; |&nbsp;&nbsp; <b> Ref No : </b>$9 <br> <b>Narration : </b> $1 ', 'comments,suppliermastername,bookingDate,transactionCurrency,invoiceType,invoiceDueDate,supplierInvoiceNo,supplierInvoiceDate,RefNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$1 : </b> $2 </div>', 'transactionCurrency,supplier_invoice_total_value(InvoiceAutoID,transactionCurrencyDecimalPlaces)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_supplier_invoice_action_buyback(InvoiceAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp,isSytemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function load_supplier_invoice_conformation()
    {
        $InvoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('InvoiceAutoID') ?? '');
        $data['extra'] = $this->Payable_modal->fetch_supplier_invoice_template_data($InvoiceAutoID);
        $printSize = $this->uri->segment(4);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $data['html']=$this->input->post('html');
        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'BSI');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        $printlink = print_template_pdf('BSI','system/accounts_payable/erp_supplier_invoice_print');
        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $InvoiceAutoID);

        $html = $this->load->view($printlink, $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');

            if($printSize == 0 && $printSize!=null){
                $defaultpapersize='A5-L';
            }else{
                $defaultpapersize='A4';
            }



            $pdf = $this->pdf->printed($html, $defaultpapersize, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function getInvoiceAutoID(){
        $invoiceCodeFrom = trim($this->input->post('invoiceCodeFrom') ?? '');
        $invoiceCodeTo = trim($this->input->post('invoiceCodeTo') ?? '');

        $data['InvoiceAutoIDFrom'] = $this->Payable_modal->getInvoiceAutoID($invoiceCodeFrom); //Get InvoiceAutoID date from
        $data['InvoiceAutoIDTo'] = $this->Payable_modal->getInvoiceAutoID($invoiceCodeTo); //Get InvoiceAutoID date to

        echo json_encode($data);
    }

    function load_supplier_invoice_range_conformation()
    {
        $invoiceCodeFrom = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceCodeFrom') ?? '');
        $invoiceCodeTo = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('invoiceCodeTo') ?? '');        
        
        $RangeInvoiceAutoIDs = $this->Payable_modal->getRangeInvoiceAutoID($invoiceCodeFrom, $invoiceCodeTo); //Fetch Range InvoiceAutoID set

        $invoiceID_arr = implode(',', array_column($RangeInvoiceAutoIDs, 'InvoiceAutoID'));

        //$InvoiceAutoID = 2834;
        $base_html= '';
        //var_dump($RangeInvoiceAutoIDs);
        
        foreach($RangeInvoiceAutoIDs as $invoiceID){       
            
            $data['extra'] = $this->Payable_modal->fetch_selected_supplier_invoice_template_data($invoiceID['InvoiceAutoID']);            
                   
            $printSize = $this->uri->segment(4);
            $data['approval'] = $this->input->post('approval');
            if (!$this->input->post('html')) {
                $data['signature'] = $this->Payable_modal->fetch_signaturelevel();
            } else {
                $data['signature'] = '';
            }
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $data['html']=$this->input->post('html');
            $printHeaderFooterYN=1;
            $data['printHeaderFooterYN']= $printHeaderFooterYN;
            $this->db->select('printHeaderFooterYN,printFooterYN');
            $this->db->where('companyID', current_companyID());
            $this->db->where('documentID', 'BSI');
            $this->db->from('srp_erp_documentcodemaster');
            $result = $this->db->get()->row_array();
            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN']= $printHeaderFooterYN;
            $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $invoiceID['InvoiceAutoID'], 'BSI', 'InvoiceAutoID');
            $printlink = print_template_pdf('BSI','system/accounts_payable/erp_selected_supplier_invoice_print');
            $data['isRcmDocument'] =  isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $invoiceID['InvoiceAutoID']);

            $html = $this->load->view($printlink, $data, true);
            $brk = "<div class='wrapper-page' style='page-break-before:always'>&nbsp;</div>";
            $base_html .= $html . $brk;

        }

        if ($this->input->post('html')) {
            echo $base_html;
        } else {                
            $this->load->library('pdf');

            if($printSize == 0 && $printSize!=null){
                $defaultpapersize='A5-L';
            }else{
                $defaultpapersize='A4';
            }
            $pdf = $this->pdf->printed($base_html, $defaultpapersize, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
           
    }

    function load_dn_conformation()
    {
        $debitNoteMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('debitNoteMasterAutoID') ?? '');
        $data['isTaxGroupPolicyEnable'] = existTaxPolicyDocumentWise('srp_erp_debitnotemaster',$debitNoteMasterAutoID,'DN','debitNoteMasterAutoID');;
        $data['extra'] = $this->Payable_modal->fetch_debit_note_template_data($debitNoteMasterAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel_debit_note();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/accounts_payable/erp_debit_note_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $printSize = $this->uri->segment(4);
            $printSizeText='A4';
            if($printSize == 0 && ($printSize!='')){
                $printSizeText='A5-L';
            }
            $printlink = print_template_pdf('DN','system/accounts_payable/erp_debit_note_print');
            $html = $this->load->view($printlink, $data, true);
            $printFooter = 1;
            if($printlink == "system/accounts_payable/erp_debit_note_print_buyback") {
                $printFooter = 0;
            }
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printFooter);
        }
    }

    function save_supplier_invoice_header()
    {
        $date_format_policy = date_format_policy();
        $bokngDt = $this->input->post('bookingDate');
        $bookingDate = input_format_date($bokngDt, $date_format_policy);

        $invduedt = $this->input->post('supplierInvoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($invduedt, $date_format_policy);

        $invdt = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invdt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('supplierID', 'Supplier Id', 'trim|required');
        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('bookingDate', 'Invoice Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('supplierInvoiceDueDate', 'Invoice Due Date', 'trim|required|validate_date');
        $invoicedReceivedDatePlolicy = getPolicyValues('IRDt', 'All');
        if($invoicedReceivedDatePlolicy == 1){
            $this->form_validation->set_rules('invoiceReceivedDate', 'Invoice Received Date', 'trim|required|validate_date');
        }
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($bookingDate >= $financePeriod['dateFrom'] && $bookingDate <= $financePeriod['dateTo']) {
                    if (($invoiceDate) > ($supplierInvoiceDueDate)) {
                        $this->session->set_flashdata('e', ' Invoice Due Date cannot be lesser than invoice Date!');
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_supplier_invoice_header());
                    }
                } else {
                    $this->session->set_flashdata('e', 'Invoice Date not between Financial Period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Payable_modal->save_supplier_invoice_header());
            }
        }
    }

    function laad_supplier_invoice_header()
    {
        echo json_encode($this->Payable_modal->laad_supplier_invoice_header());
    }

    function fetch_supplier_invoice()
    {
        $data = $this->Payable_modal->fetch_supplier_invoice();
        $html = $this->load->view('system/accounts_payable/erp_debit_note_detail', $data, true);
        echo $html;
    }

    function save_debit_base_items()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('gl_code[]', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
        $this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID[]', 'InvoiceAutoID', 'trim|required');
        /* if ($projectExist == 1) {
            $this->form_validation->set_rules("project[]", 'Project', 'trim|required');
        } */
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_debit_base_items());
        }
    }

    function save_bsi_tax_detail()
    {
        $isGroupBased = trim($this->input->post('isGroupBasedTax') ?? '');
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        if($isGroupBased !=1){
            $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        }
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_bsi_tax_detail());
        }
    }

    function fetch_bsi_detail()
    {
        echo json_encode($this->Payable_modal->fetch_bsi_detail());
    }

    function fetch_supplier_inv_currency()
    {
        echo json_encode($this->Payable_modal->fetch_supplier_inv_currency());
    }

    function supplier_invoice_confirmation()
    {
        echo json_encode($this->Payable_modal->supplier_invoice_confirmation());
    }

    function fetch_supplier_invoice_detail()
    {
        $data['master'] = $this->Payable_modal->laad_supplier_invoice_header();
        if ($this->input->post('invoiceType') == 'GRV Base') {
            $data['supplier_grv'] = $this->Payable_modal->fetch_supplier_invoice_grv($data['master']['segmentID'], $data['master']['bookingDate']);
        }else{
            $data['supplier_po'] = $this->Payable_modal->fetch_supplier_po($data['master']);
        }
        $data['segment_arr'] = $this->Payable_modal->fetch_segment();
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        $data['supplierID'] = trim($this->input->post('supplierID') ?? '');
        $data['detail'] = $this->Payable_modal->fetch_supplier_invoice_detail();
        $companyID = current_companyID();
        update_group_based_tax('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $data['InvoiceAutoID'],null,null,'BSI');

        $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster',$data['InvoiceAutoID'],'BSI','InvoiceAutoID')!=''?existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster',$data['InvoiceAutoID'],'BSI','InvoiceAutoID'):0);

        if($data['isGroupBasedTaxEnable'] == 0){
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster
                                  SET rcmApplicableYN = 0
                                  WHERE
	                             InvoiceAutoID={$data['InvoiceAutoID']}
                                  AND companyID ={$companyID}");
            $data['isRcmApplicable'] = 0;
        }else {
            $data['isRcmApplicable'] = isRcmApplicable('srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        }
        $this->load->view('system/accounts_payable/fetch_supplier_invoice_detail', $data);
    }

    function fetch_detail_header_lock()
    {

        echo json_encode($this->Payable_modal->fetch_supplier_invoice_detail());
    }

    function save_bsi_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        $this->form_validation->set_rules("gl_code", 'GL Code', 'required|trim');
        $this->form_validation->set_rules("segment_gl", 'Segment', 'required|trim');
        if($advanceCostCapturing == 1){
            $this->form_validation->set_rules("activityCode", 'Activity Code', 'required|trim');
        }
        $this->form_validation->set_rules("amount", 'Amount', 'trim|required');
        $this->form_validation->set_rules("description", 'Description', 'trim|required');
        $cat_mandetory = Project_Subcategory_is_exist();

        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }

        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_detail());
        }
    }

    function save_bsi_detail_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        $gl_codes = $this->input->post('gl_code');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            if($advanceCostCapturing == 1){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
            }
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
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
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_detail_multiple());
        }
    }

    function delete_bsi_detail()
    {
        echo json_encode($this->Payable_modal->delete_bsi_detail());
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Payable_modal->delete_tax_detail());
    }

    function referback_supplierinvoice()
    {

        $InvoiceAutoID = $this->input->post('InvoiceAutoID');

        $this->db->select('approvedYN,bookingInvCode');
        $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $approved_inventory_payable_supplierinvoice = $this->db->get()->row_array();
        if (!empty($approved_inventory_payable_supplierinvoice)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_payable_supplierinvoice['bookingInvCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($InvoiceAutoID, 'BSI');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function save_grv_base_items()
    {
        echo json_encode($this->Payable_modal->save_grv_base_items());
    }

    function delete_supplier_invoice()
    {
        echo json_encode($this->Payable_modal->delete_supplier_invoice());
    }

    function fetch_supplier_invoice_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuserid = current_userID();

        $company_doc_approval_type = getApprovalTypesONDocumentCode('BSI',$companyID);
        
        $approvalBasedWhere='';

        // if($company_doc_approval_type['approvalType']==1){

        // }else if($company_doc_approval_type['approvalType']==2){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount';
        // }else if($company_doc_approval_type['approvalType']==3){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        // }else if($company_doc_approval_type['approvalType']==4){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        // }

        if($company_doc_approval_type['approvalType']==1){

        }else if($company_doc_approval_type['approvalType']==2){
           // $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount';
           $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1))";
        }else if($company_doc_approval_type['approvalType']==3){
            $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        }else if($company_doc_approval_type['approvalType']==4){
            //$approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID";
        }
        
        $where = "srp_erp_paysupplierinvoicemaster.companyID = " . $companyID . $approvalBasedWhere."";
        if($approvedYN == 0)
        {
            // ((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0)))+IFNULL(det.taxAmount,0) as total_value
            // ROUND(((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))), 2) +IFNULL(det.taxAmount,0)  as total_value_search
            $this->datatables->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,bookingInvCode,comments,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,transactionCurrencyDecimalPlaces,transactionCurrency, ((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 ) as total_value, ((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 ) as total_value_search, srp_erp_paysupplierinvoicemaster.RefNo AS RefNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,SUM(taxAmount) as taxAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_paysupplierinvoicemaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paysupplierinvoicemaster.InvoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_paysupplierinvoicemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paysupplierinvoicemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BSI');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'BSI');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            //$this->datatables->where('srp_erp_paysupplierinvoicemaster.companyID', $companyID);
            $this->datatables->where( $where);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('details', '$1 <br><b> Ref No : </b>$2', 'comments,RefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('bookingInvCode', '$1', 'approval_change_modal(bookingInvCode,InvoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,BSI,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BSI", InvoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'supplier_invoice_action_approval(InvoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            // (IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0))+IFNULL(det.taxAmount,0)  as  as total_value
            //  as total_value_search
            $this->datatables->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,bookingInvCode,comments,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,transactionCurrencyDecimalPlaces,transactionCurrency, ((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 )  as total_value,((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 ) as total_value_search, srp_erp_paysupplierinvoicemaster.RefNo AS RefNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,SUM(taxAmount) as taxAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
            $this->datatables->from('srp_erp_paysupplierinvoicemaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paysupplierinvoicemaster.InvoiceAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BSI');
            //$this->datatables->where('srp_erp_paysupplierinvoicemaster.companyID', $companyID);
            $this->datatables->where( $where);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_paysupplierinvoicemaster.InvoiceAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvedEmpID');
            $this->datatables->add_column('details', '$1 <br><b> Ref No : </b>$2', 'comments,RefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('bookingInvCode', '$1', 'approval_change_modal(bookingInvCode,InvoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,BSI,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BSI", InvoiceAutoID)');
            $this->datatables->add_column('edit', '$1', 'supplier_invoice_action_approval(InvoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_supplier_invoice_approval()
    {
        $system_code = trim($this->input->post('InvoiceAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'BSI', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('InvoiceAutoID');
                $this->db->where('InvoiceAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('InvoiceAutoID', 'Invoice Auto ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_supplier_invoice_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'BSI', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('InvoiceAutoID', 'Invoice Auto ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_supplier_invoice_approval());
                    }
                }
            }
        }
    }


    function save_debitnote_header()
    {
        $date_format_policy = date_format_policy();
        $dDt = $this->input->post('dnDate');
        $dnDate = input_format_date($dDt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Supplier Currency', 'trim|required');
        //$this->form_validation->set_rules('exchangerate', 'Exchange Rate', 'trim|required');
        $this->form_validation->set_rules('dnDate', 'Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }
        /*$this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');*/
        //$this->form_validation->set_rules('comments', 'Comments', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($dnDate >= $financePeriod['dateFrom'] && $dnDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Payable_modal->save_debitnote_header());
                } else {
                    $this->session->set_flashdata('e', 'Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Payable_modal->save_debitnote_header());
            }
        }
    }

    function load_debit_note_header()
    {
        echo json_encode($this->Payable_modal->load_debit_note_header());
    }

    function delete_dn()
    {
        echo json_encode($this->Payable_modal->delete_dn());
    }

    function fetch_dn_detail_table()
    {
        echo json_encode($this->Payable_modal->fetch_dn_detail_table());
    }

    function save_dn_detail()
    {
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_dn_detail());
        }
    }

    function fetch_dn_detail()
    {
        echo json_encode($this->Payable_modal->fetch_dn_detail());
    }

    function delete_dn_detail()
    {
        echo json_encode($this->Payable_modal->delete_dn_detail());
    }

    function dn_confirmation()
    {
        echo json_encode($this->Payable_modal->dn_confirmation());
    }

    function referback_dn()
    {
        $debitNoteMasterAutoID = $this->input->post('debitNoteMasterAutoID');

        $this->db->select('approvedYN,debitNoteCode');
        $this->db->where('debitNoteMasterAutoID', trim($debitNoteMasterAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_debitnotemaster');
        $approved_inventory_debit_note = $this->db->get()->row_array();
        if (!empty($approved_inventory_debit_note)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_debit_note['debitNoteCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($debitNoteMasterAutoID, 'DN');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function fetch_debit_note_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,debitNoteCode,comments,supplierID,supplierCode,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount, srp_erp_debitnotemaster.docRefNo AS docRefNo');
            $this->datatables->join('(SELECT SUM( transactionAmount ) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
            $this->datatables->from('srp_erp_debitnotemaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_debitnotemaster.debitNoteMasterAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_debitnotemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_debitnotemaster.currentLevelNo');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_debitnotemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'DN');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'DN');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('details', '$1 <br> <b> Ref No : </b> $2', 'comments,docRefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('debitNoteCode', '$1', 'approval_change_modal(debitNoteCode,debitNoteMasterAutoID,documentApprovedID,approvalLevelID,approvedYN,DN,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DN",debitNoteMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'dn_action_approval(debitNoteMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,debitNoteCode,comments,supplierID,supplierCode,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_debitnotemaster.docRefNo AS docRefNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
            $this->datatables->from('srp_erp_debitnotemaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_debitnotemaster.debitNoteMasterAutoID');
            $this->datatables->where('srp_erp_debitnotemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'DN');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_debitnotemaster.debitNoteMasterAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvedEmpID');
            $this->datatables->add_column('details', '$1 <br> <b> Ref No : </b> $2', 'comments,docRefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('debitNoteCode', '$1', 'approval_change_modal(debitNoteCode,debitNoteMasterAutoID,documentApprovedID,approvalLevelID,approvedYN,DN,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DN",debitNoteMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'dn_action_approval(debitNoteMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

            echo $this->datatables->generate();
        }

    }

    function save_dn_approval()
    {
        $system_code = trim($this->input->post('debitNoteMasterAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'DN', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('debitNoteMasterAutoID');
                $this->db->where('debitNoteMasterAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_debitnotemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('debitNoteMasterAutoID', 'Debit Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_dn_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('debitNoteMasterAutoID');
            $this->db->where('debitNoteMasterAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_debitnotemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'DN', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('debitNoteMasterAutoID', 'Debit Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_dn_approval());
                    }
                }
            }
        }
    }

    function delete_supplierInvoices_attachement()
    {
        echo json_encode($this->Payable_modal->delete_supplierInvoices_attachement());
    }

    function delete_debitNote_attachement()
    {
        echo json_encode($this->Payable_modal->delete_debitNote_attachement());
    }

    function delete_paymentVoucher_attachement()
    {
        echo json_encode($this->Payable_modal->delete_paymentVoucher_attachement());
    }

    function fetch_customer_currency_by_id()
    {
        echo json_encode($this->Payable_modal->fetch_customer_currency_by_id());
    }

    function save_debitNote_detail_GLCode_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $gl_codes = $this->input->post('gl_code_array');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code_array[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            if($this->input->post('activityCode')){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim'); 
            }
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
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
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payable_modal->save_debitNote_detail_GLCode_multiple());
        }
    }

    function re_open_supplier_invoice()
    {
        echo json_encode($this->Payable_modal->re_open_supplier_invoice());
    }

    function re_open_dn()
    {
        echo json_encode($this->Payable_modal->re_open_dn());
    }

    function save_bsi_item_detail_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item 1', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }

            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }

            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_item_detail_multiple());
        }
    }

    function save_bsi_item_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $this->form_validation->set_rules("batch_number[]", 'Batch Number', 'trim|required');
        }

        $cat_mandetory = Project_Subcategory_is_exist();
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_item_detail());
        }
    }

    function fetch_po_detail_table()
    {
        echo json_encode($this->Payable_modal->fetch_po_detail_table());
    }

    function save_po_base_items()
    {
        echo json_encode($this->Payable_modal->save_po_base_items());
    }

    function Update_PO_detail()
    {
        $this->form_validation->set_rules('requestedQty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('unittransactionAmount', 'Unit Cost', 'trim|required');
        $this->form_validation->set_rules('transactionAmount', 'Net Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('wareHouseAutoID', 'Warehouse', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->Update_PO_detail());
        }
    }

    function save_general_discount(){

        $this->form_validation->set_rules('discountPercentage', 'Discount Percentage', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(['e',validation_errors()]);
        } else {
            echo json_encode($this->Payable_modal->save_general_discount());
        }
    }

    function edit_discount(){
        echo json_encode($this->Payable_modal->laad_supplier_invoice_header());
    }

    function delete_discount(){
        echo json_encode($this->Payable_modal->delete_discount());
    }
    function fetch_customer_details_by_id()
    {
        echo json_encode($this->Payable_modal->fetch_customer_details_by_id());
    }
    function fetch_customer_details_currency()
    {
        echo json_encode($this->Payable_modal->fetch_customer_details_currency());
    }

    function fetch_supplier_invoice_detail_suom()
    {
        $data['master'] = $this->Payable_modal->laad_supplier_invoice_header();
        if ($this->input->post('invoiceType') == 'GRV Base') {
            $data['supplier_grv'] = $this->Payable_modal->fetch_supplier_invoice_grv($data['master']['segmentID'], $data['master']['bookingDate']);
        }else{
            $data['supplier_po'] = $this->Payable_modal->fetch_supplier_po($data['master']);
        }
        $data['segment_arr'] = $this->Payable_modal->fetch_segment();
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        $data['supplierID'] = trim($this->input->post('supplierID') ?? '');
        $data['detail'] = $this->Payable_modal->fetch_supplier_invoice_detail();

        $this->load->view('system/accounts_payable/fetch_supplier_invoice_detail_suom', $data);
    }

    function fetch_supplier_invoices_suom()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }

            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');

        $searches='';
        /*if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND ((bookingInvCode Like '%$search%' ESCAPE '!') OR (invoiceType Like '%$sSearch%' ESCAPE '!') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (bookingDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%'))";
        }*/


        $where = "srp_erp_paysupplierinvoicemaster.companyID=" . $companyid . $supplier_filter . $date . $status_filter . $searches .  "";
        $this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,confirmedYN,approvedYN,srp_erp_paysupplierinvoicemaster.createdUserID as createdUser,invoiceType,srp_erp_suppliermaster.supplierName as suppliermastername,transactionCurrencyDecimalPlaces,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value_search,isDeleted,srp_erp_paysupplierinvoicemaster.confirmedByEmpID as confirmedByEmp");
        //$this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,InvoiceAutoID,confirmedYN,approvedYN,createdUserID,invoiceType,supplierName,transactionCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID', 'left');
        $this->datatables->from('srp_erp_paysupplierinvoicemaster');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Document Date : </b> $3 &nbsp;&nbsp; | &nbsp;&nbsp;<b>Invoice Due Date : </b> $6 <br><b> Type : </b> $5 <br> <b>Narration : </b> $1 ', 'comments,suppliermastername,bookingDate,transactionCurrency,invoiceType,invoiceDueDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$1 : </b> $2 </div>', 'transactionCurrency,supplier_invoice_total_value(InvoiceAutoID,transactionCurrencyDecimalPlaces)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_supplier_invoice_action_suom(InvoiceAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function save_bsi_item_detail_multiple_suom()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item 1', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            //$this->form_validation->set_rules("SUOMIDhn[{$key}]", 'Secondary UOM', 'trim|required');
            if(!empty($this->input->post("SUOMIDhn[$key]"))){
                $this->form_validation->set_rules("SUOMQty[{$key}]", 'Secondary QTY', 'trim|required|greater_than[0]');
            }
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_item_detail_multiple());
        }
    }

    function save_bsi_item_detail_suom()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        $this->form_validation->set_rules('SUOMIDhn', 'Secondary UOM', 'trim|required');
        if(!empty($this->input->post('SUOMIDhn'))){
            $this->form_validation->set_rules('SUOMQty', 'Secondary QTY', 'trim|required');
        }
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_item_detail());
        }
    }

    function open_payment_voucher_modal(){
        echo json_encode($this->Payable_modal->open_payment_voucher_modal());
    }

    function save_paymentvoucher_from_BSI_header()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('PVdate');
        $PVdate = input_format_date($Pdte, $date_format_policy);

        $PVchqDte = $this->input->post('PVchequeDate');
        $voucherType = $this->input->post('vouchertype');
        $PVbankCode = $this->input->post('PVbankCode');
        $PVchequeDate = input_format_date($PVchqDte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $this->form_validation->set_rules('vouchertype', 'Voucher Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('PVdate', 'Payment Voucher Date', 'trim|required|validate_date');
        if ($voucherType == 'Direct') {
            /*$this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');
            $this->form_validation->set_rules('narration', 'Narration', 'trim|required');*/
        }

        //$this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('PVbankCode', 'Bank Code', 'trim|required');
        if ($financeyearperiodYN == 1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }

        if ($voucherType == 'Supplier') {
            $this->form_validation->set_rules('partyID', 'Supplier', 'trim|required');
        } elseif ($voucherType == 'Direct' || $voucherType == 'PurchaseRequest') {
            $this->form_validation->set_rules('partyName', 'Payee Name', 'trim|required');
        } elseif ($voucherType == 'Employee') {
            $this->form_validation->set_rules('partyID', 'Employee Name', 'trim|required');
        } elseif ($voucherType == 'SC') {
            $this->form_validation->set_rules('partyID', 'Sales Person', 'trim|required');
        }
        $bank_detail = fetch_gl_account_desc($this->input->post('PVbankCode'));

        if ($bank_detail['isCash'] == 0) {
            //$this->form_validation->set_rules('PVchequeNo', 'Cheque Number', 'trim|required');
            /* if ($voucherType == 'Supplier') {
                 $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
             }*/
            $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
            if ($this->input->post('paymentType') == 2 && $voucherType == 'Supplier') {
                $this->form_validation->set_rules('supplierBankMasterID', 'Supplier Bank', 'trim|required');
            } else if(($this->input->post('paymentType') == 1) && (($voucherType == 'Supplier') || ($voucherType == 'Direct') || ($voucherType == 'Employee') || ($voucherType == 'PurchaseRequest'))) {
                $this->form_validation->set_rules('PVchequeDate', 'Cheque Date', 'trim|required');
                $chequeRegister = getPolicyValues('CRE', 'All');

                if ($chequeRegister == 1)
                {
                    $this->form_validation->set_rules('chequeRegisterDetailID', 'Cheque Number', 'trim|required');
                }else
                {
                    $this->form_validation->set_rules('PVchequeNo', 'Cheque Number', 'trim|required');
                }
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($PVdate >= $financePeriod['dateFrom'] && $PVdate <= $financePeriod['dateTo']) {

                    if ($PVchequeDate < $PVdate && $bank_detail['isCash'] == 0 && $this->input->post('paymentType') == 1) {
                        $this->session->set_flashdata('e', 'Cheque Date Cannot be less than Payment Voucher Date  !');
                        echo json_encode(FALSE);

                    } else {
                        echo json_encode($this->Payable_modal->save_paymentvoucher_from_BSI_header());
                    }

                } else {
                    $this->session->set_flashdata('e', 'Payment Voucher Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            } else {
                echo json_encode($this->Payable_modal->save_paymentvoucher_from_BSI_header());
            }
        }
    }

    function fetch_debit_note_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( debitNoteDate >= '" . $datefromconvert . " 00:00:00' AND debitNoteDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( debitNoteCode Like '%$search%' ESCAPE '!') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (debitNoteDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%') OR (docRefNo Like '%$sSearch%')) ";
        }
        $where = "srp_erp_debitnotemaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches. "";
        $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,srp_erp_debitnotemaster.documentID AS documentID,debitNoteCode,
                DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,comments,srp_erp_suppliermaster.supplierName as suppliername,confirmedYN,approvedYN,
                srp_erp_debitnotemaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,
                det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_debitnotemaster.confirmedByEmpID as confirmedByEmp, srp_erp_debitnotemaster.docRefNo AS docRefNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_debitnotemaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('dn_detail', '<b>Supplier Name : </b> $2 <br> <b>Debit Note Date : </b> $3  <br><b>Comments : </b> $1 <br><b> Ref No : </b> $5', 'comments,suppliername,debitNoteDate,transactionCurrency,docRefNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_Debit_note_action_buyback(debitNoteMasterAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    function load_line_tax_amount()
    {
        $taxCalculationformulaID = $this->input->post('taxtype');
        $applicableAmnt = $this->input->post('applicableAmnt');
        $disount = $this->input->post('discount');
        $invoiceAutoID = $this->input->post('InvoiceAutoID');
        $InvoiceDetailAutoID = $this->input->post('InvoiceDetailAutoID');
        $isTaxAdd = $this->input->post('isTaxAdd');
       
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $this->input->post('InvoiceAutoID'), 'BSI', 'InvoiceAutoID');
        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID',$invoiceAutoID);
        $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,($disount!=''?$disount:0),'BSI',$invoiceAutoID,$this->input->post('InvoiceDetailAutoID'),$isRcmDocument);
        
        //Add tax ledger record if needed
        if($isTaxAdd){
            if ($group_based_tax == 1) {
                tax_calculation_vat(null, null, $taxCalculationformulaID, 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), $applicableAmnt, 'BSI', $InvoiceDetailAutoID,$disount, 1,$isRcmDocument);
            }
        }
        
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $amnt = 0;
        } else {
            $amnt = $return['amount'];
        }
       echo json_encode($amnt);

    }
    function fetch_line_tax_and_vat(){
        $itemAutoID = $this->input->post('itemAutoID');
        $data['tax_drop'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 2);
        $selected_itemTax =   array_column($data['tax_drop'], 'assignedItemTaxFormula');
        $data['selected_itemTax'] =   isset($selected_itemTax[0]);
        echo json_encode($data);
    }
    function fetch_lineWiseTax(){
        $InvoiceDetailAutoID = ($this->input->post('InvoiceDetailAutoID')!=''?$this->input->post('InvoiceDetailAutoID'):null);
        $InvoiceAutoID = ($this->input->post('InvoiceAutoID'));
        $purchaseOrderAutoID =  ($this->input->post('purchaseOrderID'));
        $purchaseOrderDetailID =  ($this->input->post('purchaseOrderDetailID'));
        $companyID = current_companyID();
        $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$purchaseOrderAutoID);
        $qty = trim($this->input->post('qty') ?? '');
        $purchaseOrderMaster  = $this->db->query("SELECT
                                                      (($qty * unitAmount)+($qty* IFNULL(discountAmount,0)	))  as totalAmount,
                                                      ($qty * IFNULL(discountAmount,0)) as discountAmount
                                                      FROM
	                                                  `srp_erp_purchaseorderdetails`
	                                                  where 
	                                                  companyID = $companyID 
	                                                  AND purchaseOrderDetailsID = $purchaseOrderDetailID")->row_array();
        $return = fetch_line_wise_itemTaxcalculation(trim($this->input->post('taxCalculationFormulaID') ?? ''),$purchaseOrderMaster['totalAmount'],$purchaseOrderMaster['discountAmount'],'BSI',$InvoiceAutoID,$InvoiceDetailAutoID,$isRcmDocument);
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $tax = 0;
        } else {
            $tax = $return['amount'];
        }
        echo json_encode($tax);
    }

    function  load_line_tax_amount_dn(){
        $taxCalculationformulaID = $this->input->post('taxtype');
        $applicableAmnt = $this->input->post('applicableAmnt');
        $disount = $this->input->post('disount');
        $debitNoteMasterAutoID = $this->input->post('debitNoteMasterAutoID');

        $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,($disount!=''?$disount:0),'DN',$debitNoteMasterAutoID,$this->input->post('debitNoteDetailsID'));
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $amnt = 0;
        } else {
            $amnt = $return['amount'];
        }
        echo json_encode($amnt);
    }

    function fetch_supplier_po_list(){
        echo json_encode($this->Payable_modal->fetch_supplier_po_list());
    }

    function fetch_po_details(){
        echo json_encode($this->Payable_modal->fetch_po_details());
    }

    function create_retension_invoice(){
        echo json_encode($this->Payable_modal->create_retension_invoice());
    }
    
}
