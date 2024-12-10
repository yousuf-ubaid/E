<?php 

class Ap_automation extends ERP_Controller
{
 
    function __construct()
    {
        parent::__construct();
        $this->load->model('Ap_automation_model'); 
        $this->load->helper('ap_automation');
        $this->load->helper('payable');

    }

    function get_vendor_bills(){

        $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
        $this->form_validation->set_rules('fund_availablity', 'Fund Availability', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        
        $selection_type = $this->input->post('selection_type');
        $payment_type = $this->input->post('paymentType');
        $fund_availablity = $this->input->post('fund_availablity');

        if($selection_type == 3){
            $this->form_validation->set_rules('BillsDataRangeFrom', 'Date Range From', 'trim|required');
            $this->form_validation->set_rules('BillsDataRangeTo', 'Date Range To', 'trim|required');
        }

        // if($payment_type == 1){
        //     $this->form_validation->set_rules('cheque_number', 'Cheque Number', 'trim|required');
        // }

        if($fund_availablity == 2){
            $this->form_validation->set_rules('available_funds', 'Available Funds', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
            
        } else {

            // Add header for automation request
            $automationHeader = $this->Ap_automation_model->set_vendor_bills_header();

            //Get vendor bills
            if($automationHeader){
                $status = $automationHeader['status'];
                $last_id = $automationHeader['last_id'];

                if($status == TRUE ){
                    $vendor_bills = $this->Ap_automation_model->get_supplier_invoices_for_master($last_id);

                    $this->session->set_flashdata('s', 'Payment Allocations Created Successfully.');
                    echo $last_id;
                    exit;

                }
            }
        
            $this->session->set_flashdata('e', 'Payment Allocations Failed.');
            return FALSE;

        }

    }

    function fetch_vendor_allocation(){

        $doc_id = $this->input->post('doc_id');
        $master_id = $this->input->post('master_id');

        $this->datatables->select('srp_erp_ap_vendor_payments.*,srp_erp_ap_vendor_payments.vendor_code as vendor_code,srp_erp_ap_vendor_payments.vendor_name as vendor_name,srp_erp_ap_vendor_payments.id as id,srp_erp_ap_vendor_payments.paymentVoucherAutoID as paymentVoucherAutoID,
            srp_erp_ap_vendor_payments.status as status,srp_erp_ap_vendor_payments.balance_due as balance_due,srp_erp_ap_vendor_payments.bank_currency as bank_currency,
            srp_erp_ap_vendor_payments.schedule_pmt as schedule_pmt,srp_erp_ap_vendor_payments.allocation as allocation,srp_erp_ap_vendor_payments_master.confirmedYN as confirmedYN')
                ->where('master_id',$master_id)
                ->from('srp_erp_ap_vendor_payments')
                ->join('srp_erp_ap_vendor_payments_master','srp_erp_ap_vendor_payments.master_id = srp_erp_ap_vendor_payments_master.id');
        $this->datatables->add_column('view', '$1','fetch_view_record(id)');
        $this->datatables->add_column('modify', '$1','fetch_modify_record(id,confirmedYN)');
        $this->datatables->add_column('voucher','$1','payment_voucher_view(paymentVoucherAutoID,status)');
        $this->datatables->edit_column('balance_due','$1','fetch_amount_with_currency(balance_due,bank_currency)');
        $this->datatables->edit_column('schedule_pmt','$1','fetch_amount_with_currency(schedule_pmt,bank_currency)');
        $this->datatables->edit_column('allocation','$1','fetch_amount_with_currency(allocation,bank_currency)');
        //<a target="_blank" onclick="documentPageView_modal('PV','5235','UOM')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>
        echo $this->datatables->generate();


    }

    function fetch_payment_master(){
        $this->datatables->select('srp_erp_ap_vendor_payments_master.*,srp_erp_ap_vendor_payments_master.id as id,srp_erp_ap_vendor_payments_master.confirmedYN as confirmedYN')
                ->where('companyID' , current_companyID())
                ->from('srp_erp_ap_vendor_payments_master');
        $this->datatables->add_column('confirm','$1', 'fetch_confirmed(confirmedYN)');
        $this->datatables->add_column('edit', 
        '<a onclick="edit_master_record($1)" class="btn transparent-btn" title="Edit">
            <span class="glyphicon glyphicon-cog"></span>
            </a> 
            <a class="btn transparent-btn" target="_blank" href="' . site_url('Payment_voucher/load_sub_invoice_allocation/$1') . '" title="Allocation">
                <span class="glyphicon glyphicon-print" style="color: #607d8b"></span>
            </a> 
            <a onclick="delete_master_record($1)">
                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>
        </a>'
        ,'id');

        echo $this->datatables->generate();
    }

    function fetch_vendor_invoice_wise(){

        $payment_id = $this->input->post('payment_id');

        $this->datatables->select('srp_erp_ap_vendor_invoice_allocation.*,srp_erp_ap_vendor_invoice_allocation.id as id,srp_erp_ap_vendor_invoice_allocation.InvoiceAutoID as InvoiceAutoID,srp_erp_ap_vendor_invoice_allocation.bookingInvCode as bookingInvCode,srp_erp_ap_vendor_invoice_allocation.bank_amount_due as bank_amount_due,srp_erp_ap_vendor_invoice_allocation.current_amount as current_amount,
        srp_erp_ap_vendor_invoice_allocation.transaction_currency as transaction_currency,srp_erp_ap_vendor_invoice_allocation.bank_currency as bank_currency,srp_erp_ap_vendor_invoice_allocation.allocation_amount as allocation_amount,
        srp_erp_ap_vendor_invoice_allocation.status as status,srp_erp_ap_vendor_invoice_allocation.invoiceType as invoiceType,srp_erp_paysupplierinvoicemaster.supplierInvoiceNo,srp_erp_paysupplierinvoicemaster.RefNo,srp_erp_debitnotemaster.debitNoteDate as invoiceDate')
        ->where('payment_id',$payment_id)
        ->join('srp_erp_paysupplierinvoicemaster','srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_ap_vendor_invoice_allocation.InvoiceAutoID AND srp_erp_ap_vendor_invoice_allocation.invoiceType = "SupplierInvoice"','left')
        ->join('srp_erp_debitnotemaster','srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_ap_vendor_invoice_allocation.InvoiceAutoID AND srp_erp_ap_vendor_invoice_allocation.invoiceType = "debitnote"','left')
        ->from('srp_erp_ap_vendor_invoice_allocation');
        $this->datatables->add_column('action', '<a onclick="modify_invoice_allocation($1)" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i></a> &nbsp <a class="btn btn-danger btn-sm" onclick="delete_pulled_invoice($1)"><i class="fa fa-trash"></i></a>','id');
        $this->datatables->add_column('modify', '<a onclick="modify_allocations($1)" class="btn btn-primary"><i class="fa fa-cog"></i> Modify</a>','id');
        $this->datatables->add_column('delete', '<input type="checkbox" name="delete_arr[]" value="$1">','id');
        $this->datatables->edit_column('current_amount','$1','fetch_amount_with_currency(current_amount,transaction_currency)');
        $this->datatables->edit_column('bank_amount_due','$1','fetch_amount_with_currency(bank_amount_due,bank_currency)');
        $this->datatables->edit_column('allocation_amount','$1','fetch_amount_with_currency(allocation_amount,bank_currency)');
        $this->datatables->edit_column('status','$1','fetch_invoice_allocation_status(status)');
        // $this->datatables->edit_column('bank_amount_due','format_number_abs(bank_amount_due)');
        // $this->datatables->edit_column('current_amount','format_number_abs(current_amount)');
        $this->datatables->edit_column('bookingInvCode','$1','fetch_invoice_view(bookingInvCode,InvoiceAutoID,invoiceType)');

        echo $this->datatables->generate();
        //documentPageView_modal(\'BSI\',$2)
        //echo $this->db->last_query();
       
    }

    function get_invoice_allocation(){

        $allocation_id = $this->input->post('allocation_id');

        $detail =  $this->Ap_automation_model->get_invoice_allocation_detail($allocation_id);

        echo json_encode($detail);
       
    }

    function set_invoice_allocation(){
        echo  $this->Ap_automation_model->update_invoice_allocation_detail();
    }

    function fetch_total_allocations(){

        $data = array();

        $data['total_arr'] =  $this->Ap_automation_model->get_added_total_allocation();

        $html = $this->load->view('system/ap_automation/partials/total_allocations',$data);

        return $html;

    }

    function confirm_payment_master(){

        $res = $this->Ap_automation_model->confirm_payment_voucher_create();

        return true;

    }

    
    function remove_added_invoice(){
        echo  $this->Ap_automation_model->remove_added_invoice();
    }

    function remove_complete_vendor_payment(){
        echo  $this->Ap_automation_model->remove_complete_vendor_payment();
    }

    function get_more_suppliers_add(){
        echo json_encode($this->Ap_automation_model->get_more_suppliers_add());
    }

    function add_supplier_to_payment(){
        echo json_encode($this->Ap_automation_model->add_supplier_to_payment());
    }

    function get_more_invoice_for_supplier(){
        echo json_encode($this->Ap_automation_model->get_more_invoice_for_supplier());
    }

    function set_vendor_additionl_bill(){
        echo json_encode($this->Ap_automation_model->set_vendor_additionl_bill());
    }

    function delete_all_invoice(){
        $this->form_validation->set_rules('delete_arr[]', 'Invoice', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
            
        } else {
            echo json_encode($this->Ap_automation_model->delete_all_invoice());

        }
    }

    function delete_all_master(){
        echo json_encode($this->Ap_automation_model->delete_all_master());
    }
}
