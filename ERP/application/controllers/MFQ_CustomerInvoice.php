<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MFQ_CustomerInvoice extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_CustomerInvoice_model');
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('mfq', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
    }

    function fetch_customer_invoice()
    {
        $customercode = trim($this->input->post('customerCode') ?? '');
        $segmentID = trim($this->input->post('mfqsegment') ?? '');
        $segmentallSelected = ($this->input->post('segmentallSelected'));
        $companyID= current_companyID();
        $jobID = $this->input->post('jobID');
        $customer_codefilter = '';
        $segment_filter = '';

        if($customercode)
        {
            $customer_codefilter .= " AND srp_erp_mfq_customerinvoicemaster.mfqCustomerAutoID IN($customercode)";
        }

        if($segmentID && $segmentallSelected != 1)
        {

            $segment_filter .= " AND srp_erp_mfq_customerinvoicemaster.mfqSegmentID IN($segmentID)";
        }
        $where = " srp_erp_mfq_customerinvoicemaster.companyID = $companyID $customer_codefilter $segment_filter";
        if(!empty($jobID)) {
            $where .= ' AND srp_erp_mfq_deliverynotedetail.jobID  IN ('.$jobID.')';
        }
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("invoiceCode,DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,invoiceNarration,srp_erp_mfq_customermaster.CustomerName as customerName,srp_erp_mfq_customerinvoicemaster.approvedYN,srp_erp_mfq_customerinvoicemaster.confirmedYN as confirmedYN,det.transactionAmount as transactionAmount,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_mfq_customerinvoicemaster.invoiceAutoID as invoiceAutoID,srp_erp_currencymaster.CurrencyCode as CurrencyCode,IFNULL(mfqsegment.segmentCode,'-') as segmentcode", false)
            ->from('srp_erp_mfq_customerinvoicemaster')
            ->join('srp_erp_mfq_deliverynote deliverynote', 'deliverynote.deliverNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID', 'left')
            ->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.deliveryNoteID = deliverynote.deliverNoteID', 'left')
            ->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = srp_erp_mfq_customerinvoicemaster.mfqSegmentID', 'left')
            ->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_customerinvoicemaster.mfqCustomerAutoID', 'left')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_mfq_customerinvoicemaster.transactionCurrencyID', 'left')
            ->join('(SELECT SUM(transactionAmount) as transactionAmount,invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID)', 'left')
            ->where($where)
            ->group_by('srp_erp_mfq_customerinvoicemaster.invoiceAutoID');
        $this->datatables->add_column('edit', '$1', 'editCustomerInvoice(invoiceAutoID,confirmedYN,approvedYN)');
        $this->datatables->add_column('invoice_detail', '<b>'.$this->lang->line('manufacturing_customer_name').' : </b> $2 <br> <b>'.$this->lang->line('manufacturing_document_date').' : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp '.$this->lang->line('manufacturing_due_date').' : </b> $4 <br>  <b>'.$this->lang->line('common_narration').' : </b> $1 ', 'trim_desc(invoiceNarration),customerName,invoiceDate,invoiceDueDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),CurrencyCode');
        $this->datatables->add_column('job_codes', '$1', 'customer_invoice_job_codes(invoiceAutoID)');
        $this->datatables->add_column('confirmed', '$1', 'confirmation_status(confirmedYN)');
        echo $this->datatables->generate();
    }

    function save_customer_invoice()
    {
        $this->form_validation->set_rules('invoiceNarration', 'Comment', 'trim|required');
        $this->form_validation->set_rules('invoiceDueDate', 'Invoice due date', 'trim|required');
        $this->form_validation->set_rules('deliveryNoteID', 'Delivery Note', 'trim|required');
        $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('mfqsegmentID', 'Segment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
            $validateCustomerLink = $this->db->query("SELECT mfqCustomerAutoID FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = {$mfqCustomerAutoID} AND CustomerAutoID IS NULL AND companyID = " . current_companyID())->row('mfqCustomerAutoID');
            if($validateCustomerLink) {
                echo json_encode(array('w', 'Manufacturing Customer not linked with ERP Customer'));
            } else {
                echo json_encode($this->MFQ_CustomerInvoice_model->save_customer_invoice());
            }
        }
    }

    function fetch_delivery_note()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->fetch_delivery_note());
    }

    function load_mfq_customerInvoice()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->load_mfq_customerInvoice());
    }

    function load_mfq_customerinvoicedetail()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->load_mfq_customerinvoicedetail());
    }

    function fetch_customer_invoice_print()
    {
        $data = array();
        $data["header"] = $this->MFQ_CustomerInvoice_model->load_mfq_customerInvoice();
        $data["itemDetail"] = $this->MFQ_CustomerInvoice_model->load_mfq_customerinvoicedetail();
        $data['approval'] = $this->input->post('approval');
        $this->load->view('system/mfq/ajax/customer_invoice_print', $data);
    }

    function customer_invoice_confirmation()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->customer_invoice_confirmation());
    }

    function delete_customerInvoiceDetail()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->delete_customerInvoiceDetail());
    }

    function fetch_double_entry_mfq_customerInvoice()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID') ?? '');
        $data['extra'] = $this->MFQ_CustomerInvoice_model->fetch_double_entry_mfq_customerInvoice($masterID);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_chartofAccount()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->fetch_chartofAccount());
    }

    function fetch_deliveryNote_details_invoice()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->fetch_deliveryNote_details());
    }

    function fetch_attachment_for_invoice()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->fetch_attachment_for_invoice());
    }

    function save_attachment_for_invoice()
    {
        $this->form_validation->set_rules('attachmentID[]', 'Customer', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_CustomerInvoice_model->save_attachment_for_invoice());
        }
    }

    function fetch_customer_invoice_attachment_print()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->fetch_customer_invoice_attachment_print());
    }

    function delete_attachments_mfq()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->delete_attachments_mcinv());
    }
    function save_customer_invoice_jobref()
    {
        $this->db->trans_start();
        $customerInvoiceID = $this->input->post('customerInvoiceID');
        $value = $this->input->post('value');
        $data['jobreferenceNo'] = str_replace('<br />', '|', $value);
        $this->db->where('invoiceAutoID', $customerInvoiceID);
        $this->db->update('srp_erp_mfq_customerinvoicemaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'update Faild ' . $this->db->_error_message()));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Job No Successfully Updated '));
        }
    }

    function fetch_customer_invoice_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Customer Invoice');
        $this->load->database();

        $header = ['#', 'Invoice Code', 'Document Date', 'Due Date', 'Customer Name', 'Narration', 'Segment', 'Currency', 'Total Value', 'status'];

        $details = $this->MFQ_CustomerInvoice_model->fetch_customer_invoice_details();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Customer Invoice'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'Customer Invoice.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function upload_attachment_for_invoice()
    {
        echo json_encode($this->MFQ_CustomerInvoice_model->upload_attachment_for_invoice());        
    }

    function referback_customer_invoice(){
        echo json_encode($this->MFQ_CustomerInvoice_model->referback_customer_invoice());
    }

    function load_line_tax_amount(){
        echo json_encode($this->MFQ_CustomerInvoice_model->load_line_tax_amount());
    }
}