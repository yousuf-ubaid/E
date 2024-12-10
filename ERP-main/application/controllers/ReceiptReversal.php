<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReceiptReversal extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Receipt_reversale_model');
        $this->load->helpers('receipt_reversal_helper');
    }

    function fetch_receipt_reversal()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where2=" AND srp_erp_customerreceiptmaster.approvedYN=1 AND srp_erp_customerreceiptmaster.prvrID is NULL";
        //$where2=" AND NOT EXISTS (SELECT * FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)";
        $where = "srp_erp_customerreceiptmaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter .$where2 ;

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_customerreceiptmaster.receiptVoucherAutoId as receiptVoucherAutoId,srp_erp_customerreceiptmaster.companyCode,RVcode,RVchequeNo,DATE_FORMAT(RVchequeDate,'$convertFormat') AS RVchequeDate,approvedYN ,RVdate,srp_erp_customerreceiptmaster.transactionCurrency as transactionCurrency,srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,det.transactionAmount as amount,dets.taxPercentage as taxPercentage,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(Creditnots.transactionAmount,0)) as amount,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(Creditnots.transactionAmount,0)) as total_value_search, srp_erp_customerreceiptmaster.referanceNo AS referanceNo");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type!="creditnote" GROUP BY receiptVoucherAutoId) det', '(det.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT IFNULL(SUM(taxPercentage),0) AS taxPercentage,receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId) dets', '(dets.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type="GL" OR srp_erp_customerreceiptdetail.type="Item"  GROUP BY receiptVoucherAutoId) tyepdet', '(tyepdet.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        // $this->datatables->join('(SELECT documentMasterAutoID FROM srp_erp_bankledger WHERE documentType="RV" AND clearedYN = 0 GROUP BY documentMasterAutoID) bankleger', '(bankleger.documentMasterAutoID = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'inner');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type="creditnote"  GROUP BY receiptVoucherAutoId) Creditnots', '(Creditnots.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->from('srp_erp_customerreceiptmaster');
        //$this->datatables->add_column('totamount', '$1', 'get_total_amount(amount,taxPercentage,transactionCurrency,transactionCurrencyDecimalPlaces)');
        $this->datatables->edit_column('totamount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(amount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('edit', '$1', 'load_rrvr_action(receiptVoucherAutoId,0)');
        //$this->datatables->edit_column('RVchequeDate', '<span >$1 </span>', 'convert_date_format(RVchequeDate)');
        $this->datatables->edit_column('RVdate', '<span >$1 </span>', 'convert_date_format(RVdate)');
        $this->datatables->where('srp_erp_customerreceiptmaster.receiptVoucherAutoId NOT IN (SELECT srp_erp_receiptreversalmaster.receiptVoucherAutoId FROM srp_erp_receiptreversalmaster WHERE srp_erp_receiptreversalmaster.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)');
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }

    function fetch_reversed_payment(){
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where2=" AND srp_erp_customerreceiptmaster.approvedYN=1";
        //$where2=" AND NOT EXISTS (SELECT * FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)";
        $where = "srp_erp_customerreceiptmaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter .$where2 ;

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_customerreceiptmaster.receiptVoucherAutoId as receiptVoucherAutoId,srp_erp_customerreceiptmaster.companyCode,RVcode,RVchequeNo,DATE_FORMAT(RVchequeDate,'$convertFormat') AS RVchequeDate,srp_erp_customerreceiptmaster.approvedYN AS approvedYN,RVdate,srp_erp_customerreceiptmaster.transactionCurrency as transactionCurrency,srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,det.transactionAmount as amount,dets.taxPercentage as taxPercentage,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(Creditnots.transactionAmount,0)) as amount,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(Creditnots.transactionAmount,0)) as total_value_search,CONCAT(srp_erp_receiptreversalmaster.documentSystemCode ,' :- ',srp_erp_receiptreversalmaster.narration) as documentSystemCode,srp_erp_receiptreversalmaster.documentDate as documentDate,srp_erp_customerreceiptmaster.referanceNo AS referanceNo");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type!="creditnote" GROUP BY receiptVoucherAutoId) det', '(det.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT IFNULL(SUM(taxPercentage),0) AS taxPercentage,receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId) dets', '(dets.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type="GL" OR srp_erp_customerreceiptdetail.type="Item"  GROUP BY receiptVoucherAutoId) tyepdet', '(tyepdet.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT documentMasterAutoID FROM srp_erp_bankledger WHERE documentType="RV" AND clearedYN = 0 GROUP BY documentMasterAutoID) bankleger', '(bankleger.documentMasterAutoID = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'inner');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type="creditnote"  GROUP BY receiptVoucherAutoId) Creditnots', '(Creditnots.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->join('srp_erp_receiptreversalmaster', '(srp_erp_receiptreversalmaster.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)', 'left');
        $this->datatables->from('srp_erp_customerreceiptmaster');
        //$this->datatables->add_column('totamount', '$1', 'get_total_amount(amount,taxPercentage,transactionCurrency,transactionCurrencyDecimalPlaces)');
        $this->datatables->edit_column('totamount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(amount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('edit', '$1', 'load_rrvr_action(receiptVoucherAutoId,1)');
        $this->datatables->edit_column('RVdate', '<span >$1 </span>', 'convert_date_format(RVdate)');
        $this->datatables->edit_column('documentDate', '<span >$1 </span>', 'convert_date_format(documentDate)');
        $this->datatables->where('srp_erp_customerreceiptmaster.receiptVoucherAutoId IN (SELECT srp_erp_receiptreversalmaster.receiptVoucherAutoId FROM srp_erp_receiptreversalmaster WHERE srp_erp_receiptreversalmaster.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId)');
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }

    function load_html()
    {
        //onchange="fetch_supplier_currency(this.value)"
        $select_value = trim($this->input->post('select_value') ?? '');
        if (trim($this->input->post('value') ?? '') == 'Employee') {
            echo form_dropdown('partyID', all_employee_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } elseif (trim($this->input->post('value') ?? '') == 'Sales Rep') {
            echo form_dropdown('partyID', all_srp_erp_sales_person_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } else {
            echo form_dropdown('partyID', all_supplier_drop(), $select_value, 'class="form-control select2" id="partyID" required onchange="fetch_supplier_currency_by_id(this.value)"');
        }
    }

    function save_paymentreversal_header()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('documentDate');
        $documentDate = input_format_date($Pdte, $date_format_policy);

        $voucherType = $this->input->post('Type');

        $this->form_validation->set_rules('Type', 'Type', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('PVbankCode', 'Bank Code', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($voucherType == 'Supplier') {
            $this->form_validation->set_rules('partyID', 'Supplier', 'trim|required');
        } elseif ($voucherType == 'Direct') {
            $this->form_validation->set_rules('partyName', 'Payee Name', 'trim|required');
        } elseif ($voucherType == 'Employee') {
            $this->form_validation->set_rules('partyID', 'Employee Name', 'trim|required');
        } elseif ($voucherType == 'SC') {
            $this->form_validation->set_rules('partyID', 'Sales Person', 'trim|required');
        }
        $bank_detail = fetch_gl_account_desc($this->input->post('PVbankCode'));


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Payment_reversale_model->save_paymentreversal_header());

            } else {
                $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function load_payment_reversal_header()
    {
        echo json_encode($this->Payment_reversale_model->load_payment_reversal_header());
    }

    function fetch_PRVR_detail_table()
    {
        echo json_encode($this->Payment_reversale_model->fetch_PRVR_detail_table());
    }

    function fetch_Pv_detail_table()
    {
        echo json_encode($this->Payment_reversale_model->fetch_Pv_detail_table());
    }

    function save_Payment_Reversale_detail()
    {
        $this->form_validation->set_rules('checkboxprvr[]', 'Select Payment Voucher', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e','Select an payment voucher'));
        } else {
            echo json_encode($this->Payment_reversale_model->save_Payment_Reversale_detail());
        }
    }

    function delete_payment_reversale_detail()
    {
        echo json_encode($this->Payment_reversale_model->delete_payment_reversale_detail());
    }

    function load_payment_reversal_conformation()
    {
        $paymentReversalAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('paymentReversalAutoID') ?? '');
        $data['extra'] = $this->Payment_reversale_model->fetch_template_data($paymentReversalAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/PaymentReversal/erp_payment_reversale_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function delete_payment_reversal()
    {
        echo json_encode($this->Payment_reversale_model->delete_payment_reversal());
    }
    function reverse_receiptVoucher()
    {
        $this->form_validation->set_rules('receiptVoucherAutoId', 'Receipt voucher id', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('reversalDate', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Receipt_reversale_model->reverse_receiptVoucher());
        }

    }
    function payment_reversal_confirmation()
    {
        echo json_encode($this->Payment_reversale_model->payment_reversal_confirmation());
    }

    function referback_paymentReversal()
    {
        $paymentReversalAutoID = $this->input->post('paymentReversalAutoID');

        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($paymentReversalAutoID, 'PRVR');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

    }


    function load_rrvr_conformation()
    {
        $receiptVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('receiptVoucherAutoId') ?? '');
        $data['extra'] = $this->Receipt_reversale_model->fetch_payment_voucher_template_data($receiptVoucherAutoId);
        $data['approval'] = $this->input->post('approval');
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/ReceiptReversal/erp_receipt_voucher_rrvr_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

}