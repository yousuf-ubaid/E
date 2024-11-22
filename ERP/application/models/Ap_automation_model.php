<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ap_automation_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
        $this->load->model('Ap_automation_model'); 
        $this->load->helper('ap_automation');
        $this->load->helper('payable');
        
    }

    function set_vendor_bills_header(){

        $this->db->trans_start();

        $doc_id = $this->input->post('doc_id');
        $payment_date = $this->input->post('payment_date');
        $comments = $this->input->post('comments');
        $financeyear = $this->input->post('financeyear');
        $financeyear_period = $this->input->post('financeyear_period');
        $PVbankCode = $this->input->post('PVbankCode');
        $selection_type = $this->input->post('selection_type');
        $BillsDataRangeFrom = $this->input->post('BillsDataRangeFrom');
        $BillsDataRangeTo = $this->input->post('BillsDataRangeTo');
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $fund_availablity = $this->input->post('fund_availablity');
        $available_funds = $this->input->post('available_funds');
        $payment_mode = $this->input->post('paymentType');
        $segment = $this->input->post('segment');
        $data = array();
        $bank_str = '';


        //check for the doc id exists
        $ex_payment_rec = get_automation_payment_master($doc_id);
        $bank_detail = fetch_gl_account_desc($PVbankCode);


        if($bank_detail){
            $bank_str = trim($bank_detail['systemAccountCode'] ?? '') . ' | ' . trim($bank_detail['GLSecondaryCode'] ?? '') . ' | ' . trim($bank_detail['GLDescription'] ?? '') . ' | ' . trim($bank_detail['subCategory'] ?? '');
            $bank_currency = $bank_detail;
        }

        $data['doc_id'] = $doc_id;
        $data['date'] = $payment_date;
        $data['narration'] = $comments;
        $data['bank_gl'] = $PVbankCode;
        $data['bank_detail'] = $bank_str;
        $data['financial_year'] = $financeyear;
        $data['financial_period'] = $financeyear_period;
        $data['payment_mode'] = $payment_mode;
        $data['selection_type'] = $selection_type;
        $data['fund_available'] = $available_funds;
        $data['funding_availability'] = $fund_availablity;
        $data['selection_date_from'] = $BillsDataRangeFrom;
        $data['selection_date_to'] = $BillsDataRangeTo;
        $data['bank_currency'] = $transactionCurrencyID;
        $data['transaction_currency_id'] = $transactionCurrencyID;
        $data['segment'] = $segment;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];

        if($ex_payment_rec){

            $response = $this->db->where('doc_id',$doc_id)->update('srp_erp_ap_vendor_payments_master',$data);

            if ($this->db->trans_status() === FALSE) {
              
                $this->db->trans_rollback();
                return array('status' => false);

            } else {
              
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $ex_payment_rec['id']);

            }

        }else{
            $this->db->insert('srp_erp_ap_vendor_payments_master',$data);

            $last_id = $this->db->insert_id();

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Master create Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Master Created Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }

        }

    }

    public function sendProgress($percentage) {
        echo '+';
        flush();
        // ob_flush();
    }

    function get_supplier_invoices_for_master($master_id){

        $ex_payment_rec = get_automation_payment_master_by_id($master_id);
        $booking_date = date('Y-m-d');
        $usd_conversion = 300;
        $base_arr = array();

        if(empty($ex_payment_rec)){
            return false;
        }

        $selection_type = $ex_payment_rec['selection_type'];
        $doc_id = $ex_payment_rec['doc_id'];
        $fund_availablity = $ex_payment_rec['funding_availability'];
        $fund_available = $ex_payment_rec['fund_available'];
        $date_from = $ex_payment_rec['selection_date_from'];
        $date_to = $ex_payment_rec['selection_date_to'];
        $bank_currency = $ex_payment_rec['bank_currency'];
        $transactionCurrencyID = $ex_payment_rec['transaction_currency_id'];

        //clear expayment record
        //get suppliers list for criteria
        if($fund_availablity == 1){

            if($selection_type == 1 || $selection_type == 2 || $selection_type == 3){
          
                //rest update
                //if(count($vendor_list) > 0){
                $res = $this->reset_table_allocations_invoices('srp_erp_ap_vendor_invoice_allocation',$ex_payment_rec['id']);
                //}

                // All the bills
                $vendor_list = $this->get_vendor_list();
               
                if($vendor_list){
    
                    foreach($vendor_list as $vendor){
    
                        $vendor_auto_id = $vendor['supplierID'];
    
                        $invoices = $this->fetch_supplier_inv($vendor_auto_id,$transactionCurrencyID,$booking_date);     

                        $debitnotes = $this->fetch_supplier_debitnotes_main($vendor_auto_id,$transactionCurrencyID,$booking_date);    

                        $supplier_data = fetch_supplier_data($vendor_auto_id);
                    
                        $totalAmount_arr = $this->fetch_supplier_total_amount($invoices,$invoices[0]);

                        $totalAmount_debitnotes = $this->fetch_supplier_total_amount($debitnotes,$invoices[0]);

                        // foreach($toa)
                        $totalAmount_arr['totalAmountBank'] = $totalAmount_arr['totalAmountBank'] - $totalAmount_debitnotes['totalAmountBank'];
                        $totalAmount_arr['totalAmountLocal'] = $totalAmount_arr['totalAmountLocal'] - $totalAmount_debitnotes['totalAmountLocal'];
                        $totalAmount_arr['totalAmountDue'] = $totalAmount_arr['totalAmountDue'] - $totalAmount_debitnotes['totalAmountDue'];
                        $totalAmount_arr['totalAmountDueLocal'] = $totalAmount_arr['totalAmountDueLocal'] - $totalAmount_debitnotes['totalAmountDueLocal'];

                       
                        $allocation = $totalAmount_arr['totalAmountBank'];

                        foreach($debitnotes as $val){
                            $invoices[] = $val;
                        }
    
                        //Add vendor records for the doc
                        $res = $this->add_supplier_details_allocation($ex_payment_rec,$supplier_data,$invoices,$totalAmount_arr,$allocation);
                       
    
                        //update total amount
                        $res = $this->update_allocation_master($ex_payment_rec['id'],array('confirmedYN'=>0));
    
                        $base_arr[$vendor_auto_id]['supplier_detail'] = $supplier_data;
                        $base_arr[$vendor_auto_id]['invoices'] = $invoices;
                        $base_arr[$vendor_auto_id]['totalAmount'] = $totalAmount_arr;


                        //$this->sendProgress(1);
                       
                    }
    
                }
    
            }

        }elseif($fund_availablity == 2){
            
            //Limited funds
            $supplier_arr_due = array();
            $total_invoice_plus = 0;

            if($selection_type == 1){

                $vendor_list = $this->get_vendor_list();
                $base_supplier_sorted_arr = array();

                foreach($vendor_list as $vendor){
    
                    $vendor_auto_id = $vendor['supplierID'];

                    $invoices = $this->fetch_supplier_inv($vendor_auto_id,$bank_currency,$booking_date);
                    $supplier_data = fetch_supplier_data($vendor_auto_id);
                    $totalAmount_arr = $this->fetch_supplier_total_amount($invoices,$ex_payment_rec);

                    $supplier_arr_due[$supplier_data['supplierAutoID']] = $totalAmount_arr['totalAmountBank'];

                    $total_invoice_plus += $totalAmount_arr['totalAmountBank'];

                    $base_arr[$vendor_auto_id]['supplier_detail'] = $supplier_data;
                    $base_arr[$vendor_auto_id]['invoices'] = $invoices;
                    $base_arr[$vendor_auto_id]['totalAmount'] = $totalAmount_arr;
                    
                }

                

                arsort($supplier_arr_due);

                //add config records
                $res = $this->add_allocation_config_report($ex_payment_rec,$total_invoice_plus);

                $res = $this->reset_table_allocations_master_id('srp_erp_ap_vendor_payments',$master_id);

                $report_config = get_automation_report_config($doc_id);

                foreach($supplier_arr_due as $key => $supplier_arr_sorted){
            
                    $vendor_auto_id_sorted = $key;

                    $value_selected = $base_arr[$vendor_auto_id_sorted];
            
                    $base_supplier_sorted_arr[] = $value_selected;

                    $supplier_detail = $value_selected['supplier_detail'];
                    $invoices = $value_selected['invoices'];
                    $total_arr = $value_selected['totalAmount'];
                    $allocation = round($total_arr['totalAmountBank']*$report_config['unit_allocation'],$this->common_data['company_data']['company_default_decimal']);

                    $res = $this->add_supplier_details_allocation($ex_payment_rec,$supplier_detail,$invoices,$total_arr,$allocation);
                    
                }

              //  rest table allocation
   
              //  print_r($total_invoice_plus); exit;

              //  print_r(json_encode($base_supplier_sorted_arr)); exit;

            }elseif($selection_type == 2){

                $vendor_list = $this->get_vendor_list();
                $base_supplier_sorted_arr = array();

                foreach($vendor_list as $vendor){
    
                    $vendor_auto_id = $vendor['supplierID'];

                    $invoices = $this->fetch_supplier_inv($vendor_auto_id,1,$booking_date);
                    $supplier_data = fetch_supplier_data($vendor_auto_id);
                    $totalAmount_arr = $this->fetch_supplier_total_amount($invoices,$ex_payment_rec);

                    $supplier_arr_due[$supplier_data['supplierAutoID']] = $totalAmount_arr['totalAmountDue'];

                    $total_invoice_plus += $totalAmount_arr['totalAmountDue'];

                    $base_arr[$vendor_auto_id]['supplier_detail'] = $supplier_data;
                    $base_arr[$vendor_auto_id]['invoices'] = $invoices;
                    $base_arr[$vendor_auto_id]['totalAmount'] = $totalAmount_arr;
                    
                }

                arsort($supplier_arr_due);

                //add config records
                $res = $this->add_allocation_config_report($ex_payment_rec,$total_invoice_plus);

                $res = $this->reset_table_allocations_master_id('srp_erp_ap_vendor_payments',$master_id);

                $report_config = get_automation_report_config($doc_id);

                foreach($supplier_arr_due as $key => $supplier_arr_sorted){
            
                    $vendor_auto_id_sorted = $key;

                    $value_selected = $base_arr[$vendor_auto_id_sorted];
            
                    $base_supplier_sorted_arr[] = $value_selected;

                    $supplier_detail = $value_selected['supplier_detail'];
                    $invoices = $value_selected['invoices'];
                    $total_arr = $value_selected['totalAmount'];
                    $allocation = round($total_arr['totalAmountDue']*$report_config['unit_allocation'],$this->common_data['company_data']['company_default_decimal']);

                    $res = $this->add_supplier_details_allocation($ex_payment_rec,$supplier_detail,$invoices,$total_arr,$allocation);
                    
                }


            }elseif($selection_type == 3){

                $vendor_list = $this->get_vendor_list_by_date($date_from,$date_to);
                $base_supplier_sorted_arr = array();

                foreach($vendor_list as $vendor){
    
                    $vendor_auto_id = $vendor['supplierID'];

                    $invoices = $this->fetch_supplier_inv($vendor_auto_id,1,$booking_date,$date_from,$date_to);
                    $supplier_data = fetch_supplier_data($vendor_auto_id);
                    $totalAmount_arr = $this->fetch_supplier_total_amount($invoices,$ex_payment_rec);

                    $supplier_arr_due[$supplier_data['supplierAutoID']] = $totalAmount_arr['totalAmountDue'];

                    $total_invoice_plus += $totalAmount_arr['totalAmountDue'];

                    $base_arr[$vendor_auto_id]['supplier_detail'] = $supplier_data;
                    $base_arr[$vendor_auto_id]['invoices'] = $invoices;
                    $base_arr[$vendor_auto_id]['totalAmount'] = $totalAmount_arr;
                    
                }
               

                arsort($supplier_arr_due);

                //add config records
                $res = $this->add_allocation_config_report($ex_payment_rec,$total_invoice_plus);

                $res = $this->reset_table_allocations_master_id('srp_erp_ap_vendor_payments',$master_id);

                $report_config = get_automation_report_config($doc_id);

                foreach($supplier_arr_due as $key => $supplier_arr_sorted){
            
                    $vendor_auto_id_sorted = $key;

                    $value_selected = $base_arr[$vendor_auto_id_sorted];
            
                    $base_supplier_sorted_arr[] = $value_selected;

                    $supplier_detail = $value_selected['supplier_detail'];
                    $invoices = $value_selected['invoices'];
                    $total_arr = $value_selected['totalAmount'];
                    $allocation = round($total_arr['totalAmountDue'] * $report_config['unit_allocation'],$this->common_data['company_data']['company_default_decimal']);

                    $res = $this->add_supplier_details_allocation($ex_payment_rec,$supplier_detail,$invoices,$total_arr,$allocation);
                    
                }

            }

        }
        
       // print_r(json_encode($base_arr)); exit;
       // print_r($base_arr); exit;

    }

    function add_allocation_config_report($ex_payment_rec,$total_invoice_price){

        $data = array();
        $doc_id = $ex_payment_rec['doc_id'];

        $ex_record = get_automation_report_config($doc_id);

        $data['master_id'] = $ex_payment_rec['id'];
        $data['doc_id'] = $ex_payment_rec['doc_id'];
        $data['total_allocation'] = $ex_payment_rec['fund_available'];
        $data['available_allocation'] = 0;
        $data['total_balance_due'] = round($total_invoice_price,$this->common_data['company_data']['company_default_decimal']);

        if($total_invoice_price < $ex_payment_rec['fund_available']){
            $data['unit_allocation'] = 1;
        }else{
            $data['unit_allocation'] = round(($ex_payment_rec['fund_available']/$total_invoice_price),$this->common_data['company_data']['company_default_decimal']);
        }
       

        if(empty($ex_record)) {
            $this->db->insert('srp_erp_ap_automation_report_configs',$data);
        }else{
            $this->db->where('doc_id',$ex_payment_rec['doc_id'])->update('srp_erp_ap_automation_report_configs',$data);
        }

    }

    function add_supplier_details_allocation($master_detail,$supplier_data,$invoices,$totalAmount_arr,$allocation){

        $data = array();

        if($supplier_data){

            $supplier_currency_id = $supplier_data['supplierCurrencyID'];
            $supplier_currency = $supplier_data['supplierCurrency'];
            $bank_currency = $master_detail['bank_currency'];
            $bank_currency_detail = get_currency_master_by_id($bank_currency);

            $bank_currency_exchange = currency_conversionID($supplier_currency_id,$bank_currency);

            $data['master_id'] = $master_detail['id'];
            $data['doc_id'] = $master_detail['doc_id'];
            $data['vendor_code'] = $supplier_data['supplierSystemCode'];
            $data['vendor_name'] = $supplier_data['supplierName'];
            $data['balance_due'] = round($totalAmount_arr['totalAmountBank'], $this->common_data['company_data']['company_default_decimal']);
            $data['schedule_pmt'] = round($totalAmount_arr['totalAmountDue'], $this->common_data['company_data']['company_default_decimal']);
            $data['allocation'] = round($allocation, $this->common_data['company_data']['company_default_decimal']);
            $data['local_balance_due'] = round($totalAmount_arr['totalAmountLocal'], $this->common_data['company_data']['company_default_decimal']);
            $data['local_schedule_pmt'] = round($totalAmount_arr['totalAmountDueLocal'], $this->common_data['company_data']['company_default_decimal']);
            $data['date'] = date('Y-m-d H:i:s');
            $data['bank_currency'] = $bank_currency.'|'.$bank_currency_detail['CurrencyCode'];
            $data['payment_currency'] = $supplier_currency_id.'|'.$supplier_currency;
            $data['exchange_rate'] = $bank_currency_exchange['conversion'];

            $vendor = $this->db->insert('srp_erp_ap_vendor_payments',$data);
            $vendor_id = $this->db->insert_id();
        }

        if(isset($vendor_id)){
            $res = $this->add_invoice_allocations($master_detail,$invoices,$vendor_id,$allocation);
        }

        return TRUE;

    }

    function add_invoice_allocations($master_detail,$invoices,$vendor_id,$allocation){

        $data = array();
        $doc_id = $master_detail['doc_id'];
        //104979.107
        $config_record = get_automation_report_config($doc_id);
        
        $total_allocation = $allocation;
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];

        
        if(is_array($invoices)){
            foreach($invoices as $invoice){

                $currency_detail = get_currency_master_by_id($invoice['transactionCurrencyID']);
                $bank_currency_detail = get_currency_master_by_id($invoice['bank_currency']);
    
                $currency_exchange_rate = currency_conversionID($invoice['transactionCurrencyID'],$invoice['bank_currency']);
                $bank_exchange_rate = currency_conversionID($invoice['bank_currency'],$invoice['transactionCurrencyID']);
    
                // if($total_allocation >= $invoice['allocationTotal']){
                    $allocated_value = $invoice['allocationTotal'];
                    $total_allocation -= $allocated_value;
                // }else{
                //     $allocated_value = 0;
                // }
    
                //check invoice already added
                $ex_record = $this->db->where('InvoiceAutoID',$invoice['InvoiceAutoID'])->where('master_id',$master_detail['id'])->where('invoiceType','SupplierInvoice')->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();
                
                if($ex_record){
                    continue;
                }
    
                $data['master_id'] = $master_detail['id'];
                $data['invoiceType'] = isset($invoice['type']) ? $invoice['type'] : 'SupplierInvoice';
                $data['payment_id'] = $vendor_id;
                $data['doc_id'] = $doc_id;
                $data['date'] = date('Y-m-d H:i:s');
                $data['current_amount'] = abs(round($invoice['transactionAmount'], $com_currDPlace));
                $data['bank_amount_due'] = abs(round($invoice['allocationTotal'], $com_currDPlace));
                $data['allocation_amount'] = abs(round($allocated_value, $com_currDPlace)); //change
                $data['InvoiceAutoID'] = $invoice['InvoiceAutoID'];
                $data['bookingInvCode'] = $invoice['bookingInvCode'];
                $data['invoiceDate'] = $invoice['bookingDate'];
                $data['invoiceDueDate'] = isset($invoice['invoiceDueDate']) ? $invoice['invoiceDueDate'] : $invoice['bookingDate'];
                $data['transaction_currency'] = $currency_detail['currencyID'].'|'.$currency_detail['CurrencyCode'];
                $data['bank_currency'] = $bank_currency_detail['currencyID'].'|'.$bank_currency_detail['CurrencyCode'];
                $data['bank_exchange_rate'] = $bank_exchange_rate['conversion'];
                $data['currency_exchange_rate'] = $currency_exchange_rate['conversion'];
                $data['RefNo'] = $invoice['RefNo'];
                $data['status'] = ($allocated_value > 0) ? 1 : 0;
    
                $invoice_add = $this->db->insert('srp_erp_ap_vendor_invoice_allocation',$data);
    
                //update master record
                $res = $this->reverse_master_document_impact($invoice['InvoiceAutoID'],$data['allocation_amount'],$data['invoiceType']);
    
    
            }
    
            if(is_array($total_allocation) && $total_allocation > 0){
                $remaining_to_allocate = $config_record['available_allocation'] + $total_allocation;
                $res = $this->update_allocation_config_field($doc_id,'available_allocation',$remaining_to_allocate);
            }
        }

       

        return TRUE;

    }

    function add_invoice_allocations_debitnotes($master_detail,$invoices,$vendor_id,$allocation){

        $data = array();
        $doc_id = $master_detail['doc_id'];
        //104979.107
        $config_record = get_automation_report_config($doc_id);
        
        $total_allocation = $allocation;

        //Reset invoice allocations
    

        foreach($invoices as $invoice){

            $currency_detail = get_currency_master_by_id($invoice['transactionCurrencyID']);
            $bank_currency_detail = get_currency_master_by_id($invoice['bank_currency']);

            $currency_exchange_rate = currency_conversionID($invoice['transactionCurrencyID'],$invoice['bank_currency']);
            $bank_exchange_rate = currency_conversionID($invoice['bank_currency'],$invoice['transactionCurrencyID']);

            // if($total_allocation >= $invoice['allocationTotal']){
                $allocated_value = $invoice['allocationTotal'];
                $total_allocation -= $allocated_value;
            // }else{
            //     $allocated_value = 0;
            // }

            $ex_record = $this->db->where('InvoiceAutoID',$invoice['InvoiceAutoID'])->where('master_id',$master_detail['id'])->where('invoiceType','debitnote')->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();
            
            if($ex_record){
                $this->session->set_flashdata($msgtype = 'w', 'Debitnote already added.');
                continue;
            }


            $data['master_id'] = $master_detail['id'];
            $data['invoiceType'] = isset($invoice['type']) ? $invoice['type'] : 'SupplierInvoice';
            $data['payment_id'] = $vendor_id;
            $data['doc_id'] = $doc_id;
            $data['date'] = date('Y-m-d H:i:s');
            $data['current_amount'] = abs(round($invoice['transactionAmount'], $this->common_data['company_data']['company_default_decimal']));
            $data['bank_amount_due'] = abs(round($invoice['allocationTotal'], $this->common_data['company_data']['company_default_decimal']));
            $data['allocation_amount'] = abs(round($allocated_value, $this->common_data['company_data']['company_default_decimal'])); //change
            $data['InvoiceAutoID'] = $invoice['InvoiceAutoID'];
            $data['bookingInvCode'] = $invoice['bookingInvCode'];
            $data['invoiceDate'] = $invoice['bookingDate'];
            $data['invoiceDueDate'] = isset($invoice['invoiceDueDate']) ? $invoice['invoiceDueDate'] : $invoice['bookingDate'];
            $data['transaction_currency'] = $currency_detail['currencyID'].'|'.$currency_detail['CurrencyCode'];
            $data['bank_currency'] = $bank_currency_detail['currencyID'].'|'.$bank_currency_detail['CurrencyCode'];
            $data['bank_exchange_rate'] = $bank_exchange_rate['conversion'];
            $data['currency_exchange_rate'] = $currency_exchange_rate['conversion'];
            $data['RefNo'] = $invoice['RefNo'];
            $data['status'] = ($allocated_value > 0) ? 1 : 0;

            $invoice_add = $this->db->insert('srp_erp_ap_vendor_invoice_allocation',$data);

            //update master record
            $res = $this->reverse_master_document_impact($invoice['InvoiceAutoID'],$data['allocation_amount'],$data['invoiceType']);


        }

        if($total_allocation > 0){
            $remaining_to_allocate = $config_record['available_allocation'] + $total_allocation;
            $res = $this->update_allocation_config_field($doc_id,'available_allocation',$remaining_to_allocate);
        }

        return TRUE;

    }

    function reverse_master_document_impact($id,$allocated_amount,$type,$reverse = null){

        $data = array();
        if($type == 'SupplierInvoice'){

            $this->db->where('invoiceAutoID',$id);
            $master_rec = $this->db->from('srp_erp_paysupplierinvoicemaster')->get()->row_array();

            if($reverse){
                $data['paymentTotalAmount'] = ($master_rec['paymentTotalAmount'] - $allocated_amount);
            }else{
                $data['paymentTotalAmount'] = ($master_rec['paymentTotalAmount'] + $allocated_amount);
            }
           
            if($data['paymentTotalAmount'] < 0){
                $data['paymentTotalAmount'] = 0;
            }

            $this->db->where('invoiceAutoID',$id)->update('srp_erp_paysupplierinvoicemaster',$data);

            
        }elseif($type == 'debitnote'){


        }

        return true;

    }

    function update_allocation_config_field($doc_id,$field,$value){

        $data[$field] = $value;
        $res = $this->db->where('doc_id',$doc_id)->update('srp_erp_ap_automation_report_configs',$data);

    }

    function reset_table_allocations_doc_id($table_name, $doc_id){

        $res = $this->db->delete($table_name, array('doc_id'=>$doc_id));

    }

    function reset_table_allocations_invoices($table_name, $master_id){
  
        $all_payment_records = $this->db->where('master_id',$master_id)->from('srp_erp_ap_vendor_invoice_allocation')->get()->result_array();

        foreach($all_payment_records as $payments){

            $res = $this->reverse_master_document_impact($payments['InvoiceAutoID'],$payments['allocation_amount'],$payments['invoiceType'],'reverse');

        }

        $res = $this->db->delete('srp_erp_ap_vendor_invoice_allocation', array('master_id'=>$master_id));

        $this->db->where('master_id',$master_id)->delete('srp_erp_ap_vendor_payments');

    }

    function reset_table_allocations_master_id($table_name, $master_id){

        $res = $this->db->delete($table_name, array('master_id'=>$master_id));

    }

    function get_invoice_allocation_detail($allocation_id,$select=null){

        //current allocation total
        // $ex_record =  $this->db->select($select)->where('id',$allocation_id)->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();
     
        // if($ex_record){
        //     if($ex_record['invoiceType'] == 'SupplierInvoice'){
        //         $invoiceAutoID = $ex_record['InvoiceAutoID'];
        //         $allocation_amount = $ex_record['allocation_amount'];

        //         $master_rec = $this->db->where('InvoiceAutoID',$invoiceAutoID)->from('srp_erp_paysupplierinvoicemaster')->get()->row_array();

        //         $current_amount = $master_rec['transactionAmount'] - $master_rec['paymentTotalAmount'];

        //         if($current_amount < 0){

        //         }

        //     }
           
        // //    print_r($ex_record); exit;

        // }


        if($select){
            $data = $this->db->select($select)->where('id',$allocation_id)->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();
        }else{
            $data = $this->db->where('id',$allocation_id)->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();
        }
        
        return $data;

    }

    function update_invoice_allocation_detail(){

        $allocation_id = $this->input->post('allocation_id');
        $allocation_amount = $this->input->post('allocation_amount');

        $data = array();
        $invoice_allocation = $this->db->where('id',$allocation_id)->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();

        if(($invoice_allocation['bank_amount_due'] < $allocation_amount) && $allocation_amount != 0){
            $this->session->set_flashdata($msgtype = 'e', 'Allocated amount is invalid.');
            return json_encode(TRUE);
        }
        
        $data['allocation_amount'] = $allocation_amount;

        if($allocation_amount != 0){
            $data['status']  = 1;
        }else{
            $data['status']  = 0;
        }

        if($invoice_allocation && $invoice_allocation['invoiceType'] == 'debitnote'){

        } else {

            $data_supplier = array();
            $ex_amount_allocated = $invoice_allocation['allocation_amount'];
            $new_allocated_amount = $allocation_amount;
            $balance_amount = $new_allocated_amount -  $ex_amount_allocated;

            //update suppler invoice
            $this->db->where('invoiceAutoID',$invoice_allocation['InvoiceAutoID']);
            $master_rec = $this->db->from('srp_erp_paysupplierinvoicemaster')->get()->row_array();

            $data_supplier['paymentTotalAmount'] = ($master_rec['paymentTotalAmount'] + $balance_amount);
           

            if($data_supplier['paymentTotalAmount'] < 0){
                $data_supplier['paymentTotalAmount'] = 0;
            }

            $this->db->where('invoiceAutoID',$invoice_allocation['InvoiceAutoID'])->update('srp_erp_paysupplierinvoicemaster',$data_supplier);

        }

        $res = $this->db->where('id',$allocation_id)->update('srp_erp_ap_vendor_invoice_allocation',$data);
       

        if($invoice_allocation){
            $res = $this->update_total_allocation_supplier($invoice_allocation['payment_id']);
        }


        $this->session->set_flashdata($msgtype = 's', 'Updated Successfully');
        echo json_encode(TRUE);
        
    }

    function update_total_allocation_supplier($payment_id){

        $allocated_amount = 0;
        $data = array();

        $invoice_list = $this->db->where('payment_id',$payment_id)->where('status',1)->from('srp_erp_ap_vendor_invoice_allocation')->get()->result_array();

        foreach($invoice_list as $invoice){
            if($invoice['invoiceType'] == 'debitnote'){
                $allocated_amount -= $invoice['allocation_amount'];
            }else{
                $allocated_amount += $invoice['allocation_amount'];
            }
        }

        $data['allocation'] = $allocated_amount;

        $res = $this->db->where('id',$payment_id)->update('srp_erp_ap_vendor_payments',$data);

        return TRUE;

    }

    function get_added_total_allocation(){

        $master_id = $this->input->post('master_id');
        $ex_master_record = get_automation_payment_master_by_id($master_id);

        $allocations = $this->db->where('master_id',$master_id)
                                        ->select('SUM(schedule_pmt) as total_pmt,SUM(allocation) as total_allocation,SUM(balance_due) as balance_due,payment_currency')
                                        ->from('srp_erp_ap_vendor_payments')
                                        ->get()
                                        ->row_array();

        $allocations['remaining_value'] = $ex_master_record['fund_available'] - $allocations['total_allocation'];

        return $allocations;
        
    }

    function get_vendor_list($selection_type = 1){
         
        $company_id = $this->common_data['company_data']['company_id'];
        $booking_date = date('Y-m-d');
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $selection_type = $this->input->post('selection_type');
        $transactionCurremcyFilter = '';
        $dateFilter = '';

        $selection_type = $this->input->post('selection_type');
        $date_from = $this->input->post('BillsDataRangeFrom');
        $date_to = $this->input->post('BillsDataRangeTo');

        if($transactionCurrencyID){
            $transactionCurremcyFilter .= " AND master.transactionCurrencyID ='{$transactionCurrencyID}'";
        }

        if($date_from && $date_to && $selection_type == 3){
            $dateFilter .= " AND master.invoiceDate BETWEEN '{$date_from}' AND '{$date_to}' ";
        }

        $bypass_inv = "AND allocation.id IS NULL";

        if($selection_type == 2){

            $today_date = date('Y-m-d');

            $output = $this->db->query("
                SELECT DISTINCT supplierID
                FROM 
                srp_erp_paysupplierinvoicemaster as master
                LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON master.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'
                WHERE master.confirmedYN = 1 AND master.approvedYN = 1 AND master.paymentInvoiceYN = 0  AND master.companyID = '$company_id' {$transactionCurremcyFilter} AND master.invoiceDueDate < '{$today_date} {$bypass_inv}'
                ORDER BY master.InvoiceAutoID DESC;
            ")->result_array();
        }elseif($selection_type == 3){

            $output = $this->db->query("
                SELECT DISTINCT supplierID
                FROM 
                srp_erp_paysupplierinvoicemaster as master
                LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON master.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'
                WHERE master.confirmedYN = 1 AND master.approvedYN = 1 AND master.paymentInvoiceYN = 0  AND master.companyID = '$company_id' {$transactionCurremcyFilter} {$dateFilter} {$bypass_inv}
                ORDER BY master.InvoiceAutoID DESC;
            ")->result_array();

        }else{
            $output = $this->db->query("
                SELECT DISTINCT supplierID
                FROM 
                srp_erp_paysupplierinvoicemaster as master
                LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON master.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'
                WHERE master.confirmedYN = 1 AND master.approvedYN = 1 AND master.paymentInvoiceYN = 0  AND master.companyID = '$company_id' {$transactionCurremcyFilter}  {$bypass_inv}
                ORDER BY master.InvoiceAutoID DESC;
            ")->result_array();
        }
        
        if(count($output) == 0){
            $this->session->set_flashdata($msgtype = 'e', 'No Invoices to Process');
            return False;
        }


        return $output;

    }

    function get_vendor_list_by_date($date_from,$date_to){
         
        $company_id = $this->common_data['company_data']['company_id'];
        $booking_date = date('Y-m-d');

        $output = $this->db->query("
                SELECT 	DISTINCT srp_erp_paysupplierinvoicemaster.supplierID
                FROM 
                srp_erp_paysupplierinvoicemaster 
                LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'
                WHERE srp_erp_paysupplierinvoicemaster.confirmedYN = 1 AND srp_erp_paysupplierinvoicemaster.approvedYN = 1 AND srp_erp_paysupplierinvoicemaster.paymentInvoiceYN = 0  AND srp_erp_paysupplierinvoicemaster.companyID = '$company_id' AND srp_erp_paysupplierinvoicemaster.transactionCurrencyID = '1' AND srp_erp_paysupplierinvoicemaster.invoiceDate BETWEEN '$date_from' AND '$date_to'
                ORDER BY srp_erp_paysupplierinvoicemaster.InvoiceAutoID DESC;
        ")->result_array();

        return $output;
    }


    function fetch_supplier_debitnotes_main($supplierID, $bank_currency, $PVdate,$date_from = null,$date_to = null){
        $selection_type = $this->input->post('selection_type');

        if(empty($date_fro) && empty($date_to) && $selection_type == 3){
            $date_from = $this->input->post('BillsDataRangeFrom');
            $date_to = $this->input->post('BillsDataRangeTo');
        }   

        $debit_notes = $this->fetch_supplier_debitnotes($supplierID, $bank_currency, $PVdate, $date_from, $date_to);
        $base_arr = array();

        if(count($debit_notes) > 0){
            foreach($debit_notes as $key => $debitnote){
                $default_currency = currency_conversionID($bank_currency,$debitnote['transactionCurrencyID']);

                $conversion = $default_currency['conversion'];
                $decimal_place = $default_currency['DecimalPlaces'];
                $differemce = $debitnote['transactionAmount'] - ($debitnote['allocation_amount']+$debitnote['PVTransactionAmount']);

               
                $debit_notes[$key]['allocationTotal'] = ($debitnote['transactionAmount'] - ($debitnote['allocation_amount']+$debitnote['PVTransactionAmount'])) * $conversion;
                $debit_notes[$key]['decimalPlaces'] = $decimal_place;
                $debit_notes[$key]['bank_currency'] = $bank_currency;
                $debit_notes[$key]['transactionCurrency'] = $debitnote['transactionCurrency'];
                $debit_notes[$key]['transactionAmount'] = ($debitnote['transactionAmount'] - ($debitnote['allocation_amount']+$debitnote['PVTransactionAmount']));
                
                $debit_notes[$key]['due_status'] = FALSE;

                if($debit_notes[$key]['transactionAmount'] > 0){
                    $base_arr[] = $debit_notes[$key];
                }
    
            }
        }

        return $base_arr;

    }

    function fetch_supplier_inv($supplierID, $bank_currency, $PVdate,$date_from = null,$date_to = null)
    {   
        // echo '<pre>';
        $selection_type = $this->input->post('selection_type');

        if(empty($date_fro) && empty($date_to) && $selection_type == 3){
            $date_from = $this->input->post('BillsDataRangeFrom');
            $date_to = $this->input->post('BillsDataRangeTo');
        }   

        if($date_from && $date_to){
            $invoice_list = $this->fetch_supplier_inv_list_by_date($supplierID, $bank_currency, $PVdate, $date_from, $date_to);
        }else{
            $invoice_list = $this->fetch_supplier_inv_list($supplierID, $bank_currency, $PVdate);
        }

 
      

        if(count($invoice_list) == 0){
            $this->session->set_flashdata($msgtype = 'e', 'No Invoices to Process');
            return TRUE;
        }

        $base_arr = array('totalAmount' => 0,'invoices'=>'');
        $date = date('Y-m-d');
     
        foreach($invoice_list as $key => $invoice){

            $default_currency = currency_conversionID($bank_currency,$invoice['transactionCurrencyID']);

            $conversion = $default_currency['conversion'];
            $decimal_place = $default_currency['DecimalPlaces'];

            $allocation = $invoice['transactionAmount'] - ($invoice['paymentTotalAmount'] + $invoice['DebitNoteTotalAmount'] + $invoice['advanceMatchedTotal']);
            $invoice_list[$key]['transactionAmount'] = $invoice['transactionAmount'] - ($invoice['paymentTotalAmount'] + $invoice['DebitNoteTotalAmount'] + $invoice['advanceMatchedTotal']);

            $invoice_list[$key]['allocationTotal'] = $allocation * $conversion;
            $invoice_list[$key]['decimalPlaces'] = $decimal_place;
            $invoice_list[$key]['bank_currency'] = $bank_currency;

            if($date > $invoice['invoiceDueDate']){
                $invoice_list[$key]['due_status'] = TRUE;
            }else{
                $invoice_list[$key]['due_status'] = FALSE;
            }

        }

        return $invoice_list;

    }

    function fetch_supplier_total_amount($invoices,$ex_payment_rec){

        $totalAmountBank = 0;
        $totalAmountLocal = 0;
        $totalAmountDue = 0;
        $totalAmountDueLocal = 0;
        $date = date('Y-m-d');

        if(is_array($invoices)){
            foreach($invoices as $key => $inv){

                $conversion_rates = currency_conversionID($inv['transactionCurrencyID'],$ex_payment_rec['bank_currency']);
    
                // $invoiceDueDate = $inv['invoiceDueDate'];
    
                // if($date > $invoiceDueDate){
    
                //round(($supplier['allocation'] * $default_currency['conversion']),$this->common_data['company_data']['company_default_decimal']);
                $totalAmountDue += round(abs($inv['allocationTotal']),$this->common_data['company_data']['company_default_decimal']);
                $totalAmountDueLocal += round(abs($inv['transactionAmount']),$this->common_data['company_data']['company_default_decimal']);
                // }
        
                $totalAmountBank += round(abs($inv['allocationTotal']),$this->common_data['company_data']['company_default_decimal']);
                $totalAmountLocal += round(abs($inv['transactionAmount']),$this->common_data['company_data']['company_default_decimal']);
            }  
            
        }

        $base_arr = array('totalAmountLocal' => 0, 'totalAmountBank' => $totalAmountBank, 'totalAmountDue' => $totalAmountDue, 'totalAmountDueLocal'=> 0);
    
        return $base_arr;

    }

    function fetch_supplier_inv_list($supplierID, $currencyID, $PVdate,$selected_inv = null)
    {
        $PVdate = format_date($PVdate);

        $pv_bypass = '';//' AND allocation.id IS NULL';
        //$where_condition = ' AND srp_erp_paysupplierinvoicemaster.transactionAmount != (srp_erp_paysupplierinvoicemaster.DebitNoteTotalAmount + srp_erp_paysupplierinvoicemaster.paymentTotalAmount + srp_erp_paysupplierinvoicemaster.advanceMatchedTotal)';

        $where_condition = '';

        $selection_type  = $this->input->post('selection_type');
        
        $due_dates_filter = '';
        $selected_inv_filter = '';

        if($selection_type == 2){
            $due_dates_filter = " AND srp_erp_paysupplierinvoicemaster.invoiceDueDate < '{$PVdate}' ";
        }

        if($selected_inv){
            $selected_inv_filter = " AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID IN ($selected_inv)";
        }
     
        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_paysupplierinvoicemaster.bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,srp_erp_paysupplierinvoicemaster.RefNo, supplierInvoiceNo,srp_erp_paysupplierinvoicemaster.invoiceDueDate, srp_erp_paysupplierinvoicemaster.bookingDate, transactionCurrencyID,transactionCurrency,
                            ((((IFNULL(tax.taxPercentage, 0) / 100 ) * ( (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) ) + (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) AS transactionAmount
                             FROM srp_erp_paysupplierinvoicemaster 
                             LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID 
                             LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID 
                             WHERE srp_erp_paysupplierinvoicemaster.confirmedYN = 1 AND srp_erp_paysupplierinvoicemaster.approvedYN = 1 AND srp_erp_paysupplierinvoicemaster.transactionCurrencyID = '{$currencyID}' AND srp_erp_paysupplierinvoicemaster.paymentInvoiceYN = 0 AND srp_erp_paysupplierinvoicemaster.supplierID = '{$supplierID}' {$due_dates_filter} AND srp_erp_paysupplierinvoicemaster.bookingDate <= '{$PVdate}' {$pv_bypass}{$where_condition} {$selected_inv_filter} order by srp_erp_paysupplierinvoicemaster.invoiceDueDate ASC"
                            )->result_array();



        //AND `transactionCurrencyID` = '{$currencyID}'
        // LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'

        return $output;
    }

    function fetch_supplier_inv_list_by_date($supplierID, $currencyID, $PVdate,$date_from,$date_to)
    {
        $PVdate = format_date($PVdate);
        
        $pv_bypass = '';//' AND allocation.id IS NULL';
        // $where_condition = ' AND srp_erp_paysupplierinvoicemaster.transactionAmount != (srp_erp_paysupplierinvoicemaster.DebitNoteTotalAmount + srp_erp_paysupplierinvoicemaster.paymentTotalAmount + srp_erp_paysupplierinvoicemaster.advanceMatchedTotal)';

        $where_condition = '';

        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_paysupplierinvoicemaster.bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,srp_erp_paysupplierinvoicemaster.RefNo, supplierInvoiceNo,srp_erp_paysupplierinvoicemaster.invoiceDueDate, srp_erp_paysupplierinvoicemaster.bookingDate, transactionCurrencyID,
                            ((((IFNULL(tax.taxPercentage, 0) / 100 ) * ( (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) ) + (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) AS transactionAmount
                             FROM srp_erp_paysupplierinvoicemaster 
                             LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID 
                             LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID 
                             WHERE srp_erp_paysupplierinvoicemaster.confirmedYN = 1 {$pv_bypass} {$where_condition} AND srp_erp_paysupplierinvoicemaster.approvedYN = 1 AND srp_erp_paysupplierinvoicemaster.paymentInvoiceYN = 0 AND srp_erp_paysupplierinvoicemaster.supplierID = '{$supplierID}' AND srp_erp_paysupplierinvoicemaster.transactionCurrencyID = '{$currencyID}' AND srp_erp_paysupplierinvoicemaster.bookingDate BETWEEN '$date_from' AND '$date_to' order by srp_erp_paysupplierinvoicemaster.invoiceDueDate ASC"
                            )->result_array();

        //echo $this->db->last_query();
        // LEFT JOIN srp_erp_ap_vendor_invoice_allocation as allocation ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = allocation.invoiceAutoID AND allocation.invoiceType = 'SupplierInvoice'
        return $output;
    }

    function fetch_supplier_debitnotes($supplierID, $currencyID, $PVdate,$date_from,$date_to,$selected_inv=null){

        $PVdate = format_date($PVdate);

        $selected_inv_filter = '';
        $date_filter = '';
        if($selected_inv){
            $selected_inv_filter = " AND masterTbl.debitNoteMasterAutoID IN ($selected_inv)";
        }

        if($date_from && $date_to){
            $date_filter = " AND masterTbl.debitNoteDate BETWEEN '{$date_from}' AND '{$date_to}' ";
        }

        $output = $this->db->query("SELECT * FROM(
            SELECT
                masterTbl.debitNoteMasterAutoID AS debitNoteMasterAutoID,
                masterTbl.debitNoteCode AS debitNoteCode,
                IFNULL( detailTbl.transactionAmount, 0 ) AS transactionAmount,
                masterTbl.transactionCurrencyID AS transactionCurrencyID,
                masterTbl.transactionCurrency AS transactionCurrency,
                masterTbl.transactionExchangeRate AS transactionExchangeRate,
                masterTbl.debitNoteMasterAutoID AS InvoiceAutoID,
                masterTbl.debitNoteCode AS bookingInvCode,
                masterTbl.docRefNo AS RefNo,
                masterTbl.debitNoteDate AS debitNoteDate,
                masterTbl.debitNoteDate AS bookingDate,
                SUM(pvDetail.transactionAmount) AS PVTransactionAmount,
                'debitnote' AS type,
                SUM(allocation.allocation_amount) AS allocation_amount
            FROM
                srp_erp_debitnotemaster masterTbl
            LEFT JOIN (
                SELECT
                    SUM(transactionAmount) AS transactionAmount,
                    SUM( IFNULL(taxAmount, 0)) AS taxAmount,
                    debitNoteMasterAutoID
                FROM
                    srp_erp_debitnotedetail
                WHERE
                    (
                        ISNULL(InvoiceAutoID)
                        OR InvoiceAutoID = 0
                    )
                GROUP BY
                    debitNoteMasterAutoID
            ) detailTbl ON detailTbl.debitNoteMasterAutoID = masterTbl.debitNoteMasterAutoID
            LEFT JOIN srp_erp_paymentvoucherdetail AS pvDetail ON pvDetail.debitNoteAutoID = masterTbl.debitNoteMasterAutoID AND pvDetail.type='debitnote'
            LEFT JOIN srp_erp_ap_vendor_invoice_allocation AS allocation ON masterTbl.debitNoteMasterAutoID = allocation.invoiceAutoID AND allocation.invoiceType='debitnote'
            WHERE
                masterTbl.confirmedYN = 1
                AND masterTbl.approvedYN = 1
                AND masterTbl.transactionCurrencyID = '" . $currencyID . "'
                AND masterTbl.debitNoteDate <= '" . $PVdate . "'
                AND masterTbl.supplierID = '" . $supplierID . "'
                {$selected_inv_filter}
                {$date_filter}
                
            GROUP BY
                masterTbl.debitNoteMasterAutoID HAVING transactionAmount !=0

            UNION ALL

            SELECT
                masterTbl.stockReturnAutoID AS debitNoteMasterAutoID,
                masterTbl.stockReturnCode AS debitNoteCode,
                detailTbl.transactionAmount,
                masterTbl.transactionCurrencyID AS transactionCurrencyID,
                masterTbl.transactionCurrency AS transactionCurrency,
                masterTbl.transactionExchangeRate AS transactionExchangeRate,
                masterTbl.returnDate AS debitNoteDate,
                masterTbl.returnDate AS bookingDate,
                masterTbl.stockReturnAutoID AS InvoiceAutoID,
                masterTbl.stockReturnCode AS bookingInvCode,
                masterTbl.referenceNo AS RefNo,
                SUM(pvDetail.transactionAmount) AS PVTransactionAmount,
                'SR' AS type,
                masterTbl.stockReturnAutoID AS InvoiceAutoID
            FROM
                srp_erp_stockreturnmaster masterTbl
            LEFT JOIN (
                SELECT
                    SUM(totalValue + IFNULL(taxAmount, 0)) AS transactionAmount,
                    stockReturnAutoID
                FROM
                    srp_erp_stockreturndetails
                GROUP BY
                    stockReturnAutoID
            ) detailTbl ON detailTbl.stockReturnAutoID = masterTbl.stockReturnAutoID
            LEFT JOIN srp_erp_paymentvoucherdetail AS pvDetail ON pvDetail.debitNoteAutoID = masterTbl.stockReturnAutoID AND pvDetail.type='SR'
            WHERE
                masterTbl.confirmedYN = 1
            AND masterTbl.approvedYN = 1
            AND masterTbl.transactionCurrencyID = '" . $currencyID . "'
            AND masterTbl.returnDate <= '" . $PVdate . "'
            AND masterTbl.supplierID = '" . $supplierID . "'
            GROUP BY
                masterTbl.stockReturnAutoID) as result")->result_array();
        

        //AND masterTbl.supplierID = '" . $supplierID . "'
       // echo $this->db->last_query();
        return $output;

    }

    //Records update
    function update_allocation_master($id,$data){

        try{

            $res = $this->db->where('id',$id)->update('srp_erp_ap_vendor_payments_master',$data);

            return TRUE;

        }catch(Exception $e){

        }

    }

    // Payment voucher create
    function confirm_payment_voucher_create(){

        // echo '<pre>';

        $master_id = $this->input->post('master_id');
        $ex_master_record = get_automation_payment_master_by_id($master_id);
        $payment_voucher_count = 0;
        $invoices_arr = array();
        $sub_pv_arr = array();

        $this->load->model('Payment_voucher_model'); 


        try{
            
            $supplier_list = get_automation_payment_allocation_by_master_id($master_id,'all');

        
            foreach($supplier_list as $supplier){

                $payment_id = $supplier['id'];
    
                $allocated_inoices = get_invoice_payment_allocation_by_payment_id($payment_id);
                $allocated_inoices_debitnotes = get_invoice_payment_allocation_by_debitnotes_payment_id($payment_id);
          
                if($allocated_inoices){
                    $pv_header = $this->save_paymentvoucher_header($ex_master_record,$supplier,1);
                    
                    if(isset($pv_header['last_id'])){
                        $ex_master_record['payVoucherAutoId'] = $pv_header['last_id'];
                      
                        $sub_pv_arr[] =  $pv_header['last_id'];

                        $invoice_details = $this->save_inv_base_items($ex_master_record,$allocated_inoices,$supplier);

                        $invoice_details = $this->save_inv_base_items_debitnotes($ex_master_record,$allocated_inoices_debitnotes,$supplier);

            
                        //update allocated invoices
                        $pv_numbers_update = $this->update_pv_auto_id($ex_master_record,$supplier);

                        
                        $payment_voucher_count++;
        
                    }
                   
                }

                foreach($allocated_inoices as $value){
                    $invoices_arr[] = $value;
                }

                foreach($allocated_inoices_debitnotes as $value_debit){
                    $invoices_arr[] = $value_debit;
                }
    
    
            }


            //update pv master for sub invoices
            $sub_invoice_list = join(',',$sub_pv_arr);

            //hits master record for bankledger
            // echo '<pre>';
            // print_r($ex_master_record); exit;

            $pv_header_main = $this->save_paymentvoucher_header_main($ex_master_record,$sub_invoice_list);

            if($pv_header_main){
                $ex_master_record['payVoucherAutoId'] = $pv_header_main['last_id'];

                $allocation_total_amount = 0;

                $invoice_details = $this->save_inv_base_items_main($ex_master_record,$invoices_arr);

                $_POST['PayVoucherAutoId'] =  $ex_master_record['payVoucherAutoId'];
                $_POST['isWithoutGeneralLedger'] =  1;
                $this->Payment_voucher_model->payment_confirmation($ex_master_record['payVoucherAutoId']);

                $_POST['PayVoucherAutoId'] = '';

            }

    
            //Status change
            $payment_master_update = $this->update_master_feild('srp_erp_ap_vendor_payments_master',$ex_master_record['id'],array('status'=>1,'confirmedYN'=>1));
       

            $this->session->set_flashdata($msgtype = 's', "$payment_voucher_count Payment Vouchers Successfully Created");
            echo json_encode(array('status'=>TRUE));

        } catch(Exception $e){
            $this->session->set_flashdata($msgtype = 'e', "Something went wrong");
            echo json_encode(array('status'=>FALSE));
        }
       

    }

    //////////////////Payment voucher Generate //////////////////////////////////


    function save_paymentvoucher_header($payment_master,$supplier_payment,$confirmedYN=null)
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $payment_master['date'];
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $payment_master['date'];
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $chequeRegister = getPolicyValues('CRE', 'All');
        $accountPayeeOnly = 0;
        $voucherType = 'SupplierInvoice';
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails') ?? ''));
        $segment_str = $payment_master['segment']; 
        // $currency_str = 'OMR|Omani Rial';
        $PVbankCode = $payment_master['bank_gl'];
        $bank_detail = fetch_gl_account_desc($PVbankCode);
        $payment_type = $payment_master['payment_mode'];
        $transactionCurrencyID = $payment_master['transaction_currency_id'];

        $trans_currency_detail = get_currency_details_by_id($transactionCurrencyID);
        $currency_str = $trans_currency_detail['CurrencyCode'].'|'.$trans_currency_detail['CurrencyName'];

        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        
        $financePeriod = get_financial_period_date_wise($PaymentVoucherdate);
        $company_finance_year = company_finance_year($financePeriod['companyFinanceYearID']);

        $companyFinanceYear = $company_finance_year['startdate'].' - '.$company_finance_year['endingdate'];

       
        
        if ($financeyearperiodYN == 1) { 
            $FYBegin = input_format_date($financePeriod['dateFrom'], $date_format_policy);
            $FYEnd = input_format_date($financePeriod['dateTo'], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($PVdate);

            if (empty($financeYearDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($PVdate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $segment = explode('|', trim($segment_str));
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $currency_code = explode('|', trim($currency_str));
        

        $data['PVbankCode'] = trim($PVbankCode);
        
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = trim($financePeriod['companyFinanceYearID'] ?? '');
        $data['companyFinanceYear'] = trim($companyFinanceYear);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($financePeriod['companyFinancePeriodID'] ?? '');
        $data['PVdate'] = trim($PVdate);

        $narration = $payment_master['narration'];
        $data['PVNarration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['PVbank'] = $bank_detail['bankName'];
        $data['PVbankBranch'] = $bank_detail['bankBranch'];
        $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['PVbankType'] = $bank_detail['subCategory'];
        $data['paymentType'] = $payment_type;
        $data['supplierBankMasterID'] = $this->input->post('supplierBankMasterID');

        $data['confirmedYN'] = $confirmedYN;
        
        if($PVcheqDate == null)
        {
            $data['PVchequeDate'] = null;
        }

        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = null;
        } else {
            $data['PVchequeNo'] = $bank_detail['bankCheckNumber'] + 1;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = trim($PVchequeDate);
        }
        
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['pvType'] = trim($voucherType);
        $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
        $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $transactionCurrencyID;
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];

        $supplier_arr = fetch_supplier_data_by_supplier_systemcode($supplier_payment['vendor_code']);


        //$supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
        $data['partyType'] = 'SUP';
        $data['partyID'] = $supplier_arr['supplierAutoID'];
        $data['partyCode'] = $supplier_arr['supplierSystemCode'];
        $data['partyName'] = $supplier_arr['supplierName'];
        $data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['partyTelephone'] = $supplier_arr['supplierTelephone'];
        $data['partyFax'] = $supplier_arr['supplierFax'];
        $data['partyEmail'] = $supplier_arr['supplierEmail'];
        $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
        $data['partyExchangeRate'] = $data['transactionExchangeRate'];
        $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];

        $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        $data['partyExchangeRate'] = $partyCurrency['conversion'];
        $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];

       

        if (trim($this->input->post('PayVoucherAutoId') ?? '')) {
            $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
            $this->db->update('srp_erp_paymentvouchermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(!empty($data['chequeRegisterDetailID'])){
                    $this->update_cheque_detail($data['chequeRegisterDetailID'],$this->input->post('PayVoucherAutoId'));
                } else {
                    $this->delete_cheque_detail($this->input->post('PayVoucherAutoId'));
                }
                $this->session->set_flashdata('s', 'Payment Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('PayVoucherAutoId'),'data'=>$data);
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
          //  $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
            //$this->load->library('sequence');
            $data['isGroupBasedTax'] =  ((getPolicyValues('GBT', 'All')==1)?1:0);
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $type = substr($data['pvType'], 0, 3);
            $data['PVcode'] = 0;

            $this->db->insert('srp_erp_paymentvouchermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(!empty($data['chequeRegisterDetailID'])){
                  //  $this->update_cheque_detail($data['chequeRegisterDetailID'],$last_id);
                }
                $this->session->set_flashdata('s', 'Payment Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id,'data'=>$data);
            }
        }
    }

    function save_paymentvoucher_header_main($payment_master,$sub_invoice_list=null)
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $payment_master['date'];
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $payment_master['date'];
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $chequeRegister = getPolicyValues('CRE', 'All');
        $accountPayeeOnly = 0;
        $voucherType = 'DirectExpense';
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails') ?? ''));
        $segment_str = $payment_master['segment']; 

        $PVbankCode = $payment_master['bank_gl'];
        $bank_detail = fetch_gl_account_desc($PVbankCode);
        $payment_type = $payment_master['payment_mode'];
        $transactionCurrencyID = $payment_master['transaction_currency_id'];

        $trans_currency_detail = get_currency_details_by_id($transactionCurrencyID);
        $currency_str = $trans_currency_detail['CurrencyCode'].'|'.$trans_currency_detail['CurrencyName'];

        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        
        $financePeriod = get_financial_period_date_wise($PaymentVoucherdate);
        $company_finance_year = company_finance_year($financePeriod['companyFinanceYearID']);

        $companyFinanceYear = $company_finance_year['startdate'].' - '.$company_finance_year['endingdate'];

       
        if ($financeyearperiodYN == 1) { 
            $FYBegin = input_format_date($financePeriod['dateFrom'], $date_format_policy);
            $FYEnd = input_format_date($financePeriod['dateTo'], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($PVdate);

            if (empty($financeYearDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($PVdate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $segment = explode('|', trim($segment_str));
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $currency_code = explode('|', trim($currency_str));
        

        $data['PVbankCode'] = trim($PVbankCode);
        
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = trim($financePeriod['companyFinanceYearID'] ?? '');
        $data['companyFinanceYear'] = trim($companyFinanceYear);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($financePeriod['companyFinancePeriodID'] ?? '');
        $data['PVdate'] = trim($PVdate);

        $narration = $payment_master['narration'];
        $data['PVNarration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['PVbank'] = $bank_detail['bankName'];
        $data['PVbankBranch'] = $bank_detail['bankBranch'];
        $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['PVbankType'] = $bank_detail['subCategory'];
        $data['paymentType'] = $payment_type;
        $data['supplierBankMasterID'] = $this->input->post('supplierBankMasterID');
        $data['subInvoiceList'] = $sub_invoice_list;
        $data['bypassLedger'] = 1;
        
        if($PVcheqDate == null)
        {
            $data['PVchequeDate'] = null;
        }

        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = null;
        } else {
            $data['PVchequeNo'] = $bank_detail['bankCheckNumber'] + 1;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = trim($PVchequeDate);
        }
        
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['pvType'] = trim($voucherType);
        $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
        $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $transactionCurrencyID;
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        $data['partyExchangeRate'] = 1;
        $data['partyCurrencyDecimalPlaces'] = 2;
      //  $supplier_arr = fetch_supplier_data_by_supplier_systemcode($supplier_payment['vendor_code']);


        //$supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
        // $data['partyType'] = 'SUP';
        // $data['partyID'] = $supplier_arr['supplierAutoID'];
        // $data['partyCode'] = $supplier_arr['supplierSystemCode'];
        // $data['partyName'] = $supplier_arr['supplierName'];
        // $data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        // $data['partyTelephone'] = $supplier_arr['supplierTelephone'];
        // $data['partyFax'] = $supplier_arr['supplierFax'];
        // $data['partyEmail'] = $supplier_arr['supplierEmail'];
        // $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
        // $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        // $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        // $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
        // $data['partyExchangeRate'] = $data['transactionExchangeRate'];
        // $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];

        // $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        // $data['partyExchangeRate'] = $partyCurrency['conversion'];
        // $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];

       

        if (trim($this->input->post('PayVoucherAutoId') ?? '')) {
            $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
            $this->db->update('srp_erp_paymentvouchermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(!empty($data['chequeRegisterDetailID'])){
                    $this->update_cheque_detail($data['chequeRegisterDetailID'],$this->input->post('PayVoucherAutoId'));
                } else {
                    $this->delete_cheque_detail($this->input->post('PayVoucherAutoId'));
                }
                $this->session->set_flashdata('s', 'Payment Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('PayVoucherAutoId'),'data'=>$data);
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
          //  $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
            //$this->load->library('sequence');
            $data['isGroupBasedTax'] =  ((getPolicyValues('GBT', 'All')==1)?1:0);
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $type = substr($data['pvType'], 0, 3);
            $data['PVcode'] = 0;

            $this->db->insert('srp_erp_paymentvouchermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(!empty($data['chequeRegisterDetailID'])){
                  //  $this->update_cheque_detail($data['chequeRegisterDetailID'],$last_id);
                }
                $this->session->set_flashdata('s', 'Payment Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id,'data'=>$data);
            }
        }
    }

    function delete_cheque_detail($documentMasterAutoID){
        $dataD = array(
            'status' => 0,
            'documentMasterAutoID' => null,
            'documentID' => null
        );
        $this->db->where('documentMasterAutoID', $documentMasterAutoID);
        $this->db->where('documentID', 'PV');
        $this->db->update('srp_erp_chequeregisterdetails', $dataD);
    }

    function save_inv_base_items($master_record,$allocated_invoices,$supplier)
    {
        $default_currency = currency_conversionID(1,1);
        $supplier_settlement = round(($supplier['allocation'] * $default_currency['conversion']),$this->common_data['company_data']['company_default_decimal']);

        foreach($allocated_invoices as $invoice){

            $totally_allocated = 0;
            $allocation_amount = round($invoice['allocation_amount'],$this->common_data['company_data']['company_default_decimal']);
            $bank_amount_due = round($invoice['bank_amount_due'],$this->common_data['company_data']['company_default_decimal']);
            $invoice_arr = array();
            $amount_arr = array();
            
            if($allocation_amount == $bank_amount_due){
                $totally_allocated = 1;
            }else{
                $totally_allocated = 0;
            }  

            $this->db->trans_start();

            $invoice_arr[] = $invoice['InvoiceAutoID'];
            $amount_arr[] = $invoice['allocation_amount'];

            $InvoiceAutoID = $invoice_arr;
            $settlementAmount =  $supplier_settlement;
            $payVoucherAutoId = $master_record['payVoucherAutoId'];
            $amount = $amount_arr;

            //$this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,	(
                (
                (
                ( IFNULL( tax.taxPercentage, 0 ) / 100 ) * (
                ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) - (
                ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
                ) 
                ) 
                ) + ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
                ) - (
                ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
                ) 
                ) AS transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid', 'srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID', 'left');
            $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID', 'left');
            $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $InvoiceAutoID);
            $master_recode = $this->db->get()->result_array();
            
            $am_arr = []; $inv_arr = []; $re_arr = [];
            foreach($InvoiceAutoID as $key=>$row){
                $am_arr[$row] = $amount[$key];
            }

            for ($i = 0; $i < count($master_recode); $i++) {
                $invAutoID=$master_recode[$i]['InvoiceAutoID'];
                $due_amount = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['paymentTotalAmount'] + $master_recode[$i]['DebitNoteTotalAmount'] + $master_recode[$i]['advanceMatchedTotal']));
        
                $data[$i]['payVoucherAutoId'] = $payVoucherAutoId;
                $data[$i]['InvoiceAutoID'] = $master_recode[$i]['InvoiceAutoID'];
                $data[$i]['type'] = 'Invoice';
                $data[$i]['bookingInvCode'] = $master_recode[$i]['bookingInvCode'];
                $data[$i]['referenceNo'] = $master_recode[$i]['RefNo'];
                $data[$i]['bookingDate'] = $master_recode[$i]['bookingDate'];
                $data[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
                $data[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
                $data[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
                $data[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
                $data[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
                $data[$i]['description'] = $master_recode[$i]['comments'];
                $data[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
                $data[$i]['due_amount'] = $due_amount + $master_recode[$i]['paymentTotalAmount'];
                $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - (float)$am_arr[$invAutoID]);
                $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = (float)$am_arr[$invAutoID];
                $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];

                if(isset($master_recode[$i]['companyLocalExchangeRate']) && $master_recode[$i]['companyLocalExchangeRate'] != 0){
                    $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                }else{
                    $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / 1);
                }
                
                $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];

                if(isset($master_recode[$i]['companyReportingExchangeRate']) && $master_recode[$i]['companyReportingExchangeRate'] != 0){
                    $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                }else{
                    $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / 1);
                }
            
                $data[$i]['partyCurrencyID'] = $master_recode[$i]['supplierCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode[$i]['supplierCurrency'];
                $data[$i]['partyExchangeRate'] = $master_recode[$i]['supplierCurrencyExchangeRate'];
                if(isset($master_recode[$i]['supplierCurrencyExchangeRate']) && $master_recode[$i]['supplierCurrencyExchangeRate'] != 0){
                    $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['supplierCurrencyExchangeRate']);
                }else{
                    $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / 1);
                }
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];

                $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID[$i];
                $grv_m[$i]['paymentTotalAmount'] = ($master_recode[$i]['paymentTotalAmount'] + $am_arr[$invAutoID]);
                $grv_m[$i]['paymentInvoiceYN'] = 0;
                if ($data[$i]['balance_amount'] <= 0) {
                    $grv_m[$i]['paymentInvoiceYN'] = 1;
                }
            }

            $data_up_settlement['settlementTotal'] = $settlementAmount;
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->update('srp_erp_paymentvouchermaster', $data_up_settlement);

            if (!empty($data)) {
              //  $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
                $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                  
                } else {
                    $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                    $this->db->trans_commit();
                    
                }
            } else {
               
            }

        }
        
    }

    function save_inv_base_items_debitnotes($master_record,$allocated_invoices,$supplier){

        $default_currency = currency_conversionID(1,1);
        $supplier_settlement = round(($supplier['allocation'] * $default_currency['conversion']),$this->common_data['company_data']['company_default_decimal']);

        foreach($allocated_invoices as $invoice){

            $totally_allocated = 0;
            $allocation_amount = round($invoice['allocation_amount'],$this->common_data['company_data']['company_default_decimal']);
            $bank_amount_due = round($invoice['bank_amount_due'],$this->common_data['company_data']['company_default_decimal']);
            $invoice_arr = array();
            $amount_arr = array();
            
            if($allocation_amount == $bank_amount_due){
                $totally_allocated = 1;
            }else{
                $totally_allocated = 0;
            }  

            $data = array();
            $debitNoteMasterID = $invoice['InvoiceAutoID'];
            $payVoucherAutoId = $master_record['payVoucherAutoId'];
            $amount = $amount_arr;

            $master_recode = $this->get_debitNote_master($debitNoteMasterID);
            $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($debitNoteMasterID); // use this value to get due amount
            $due_amount = $invoice['current_amount'] - $alreadyPaidAmount;
            $balance_amount = $due_amount - $allocation_amount;

            $data['debitNoteAutoID'] = $debitNoteMasterID;
            $data['InvoiceAutoID'] = null;
            $data['type'] = 'debitnote';
            $data['payVoucherAutoId'] = $payVoucherAutoId;
            $data['bookingInvCode'] = $master_recode['debitNoteCode'];
            $data['referenceNo'] = $master_recode['docRefNo'];
            $data['bookingDate'] = $master_recode['debitNoteDate'];
            $data['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
            $data['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
            $data['GLCode'] = $master_recode['supplierliabilityGLAccount'];
            $data['GLDescription'] = $master_recode['supplierliabilityDescription'];
            $data['GLType'] = $master_recode['supplierliabilityType'];
            $data['description'] = $master_recode['comments'];

            $data['Invoice_amount'] = $invoice['current_amount'];
            $data['due_amount'] = $due_amount;
            $data['balance_amount'] = $balance_amount;

            $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
            $data['transactionCurrency'] = $master_recode['transactionCurrency'];
            $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
            $data['transactionAmount'] = (float)$allocation_amount;

            $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
            $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
            $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
            $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
            $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
            $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
            $data['partyCurrencyID'] = $master_recode['supplierCurrencyID'];
            $data['partyCurrency'] = $master_recode['supplierCurrency'];
            $data['partyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
            $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
           

        }

    }

    function get_debitNote_master($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_debitnotemaster');
        $this->db->where('debitNoteMasterAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }

    function get_debitNote_paymentVoucher_transactionAmount($debitNoteAutoID, $type = 'debitnote')
    {
        $sumTransactionAmount = $this->db->query("SELECT SUM(transactionAmount)AS totalTransactionAmount FROM srp_erp_paymentvoucherdetail WHERE debitNoteAutoID = '" . $debitNoteAutoID . "' AND type='" . $type . "'")->row('totalTransactionAmount');
        return $sumTransactionAmount;

    }


    function save_inv_base_items_main($master_record,$allocated_invoices)
    {
        $default_currency = currency_conversionID(1,1);
        $data = array();

        $this->db->where('payVoucherAutoId',$master_record['payVoucherAutoId']);
        $master = $this->db->from('srp_erp_paymentvouchermaster')->get()->row_array();

        $allocation_amount = 0;
        $bank_amount_due = 0;

        foreach($allocated_invoices as $invoice){
            if($invoice['invoiceType'] != 'debitnote'){
                $allocation_amount += $invoice['allocation_amount'];
            }else{
                $allocation_amount -= $invoice['allocation_amount'];
            }
            
        }

        $data['payVoucherAutoId'] = $master_record['payVoucherAutoId'];
        $data['type'] = 'GL';
        $data['GLDescription'] = $master_record['doc_id']. ' Total Payment';
        $data['Invoice_amount'] = $allocation_amount;
        $data['referenceNo'] = $master_record['doc_id'];
        $data['bookingDate'] = $master_record['date'];
        $data['description'] = '';
        $data['transactionAmount'] = $allocation_amount;

        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];

        if(isset($master['companyLocalExchangeRate']) && $master['companyLocalExchangeRate'] != 0){
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        }else{
            $data['companyLocalAmount'] = ($data['transactionAmount'] / 1);
        }
        $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];

        if(isset($master['companyReportingExchangeRate']) && $master['companyReportingExchangeRate'] != 0){
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        }else{
            $data['companyReportingAmount'] = ($data['transactionAmount'] / 1);
        }
        $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

        if(isset($master['supplierCurrencyExchangeRate']) && $master['supplierCurrencyExchangeRate'] != 0){
            $data['partyAmount'] = ($data['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
        }else{
            $data['partyAmount'] = ($data['transactionAmount']/ 1);
        }
        $data['partyCurrencyID'] = '';
        $data['partyCurrency'] = '';
        $data['partyExchangeRate'] = 1;

        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        if (!empty($data)) {
            //  $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
              $this->db->insert('srp_erp_paymentvoucherdetail', $data);
              $this->db->trans_complete();
              if ($this->db->trans_status() === FALSE) {
                  $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                  $this->db->trans_rollback();
                
              } else {
                  $this->session->set_flashdata('s', 'Supplier Invoice :  Item Details Saved Successfully . ');
                  $this->db->trans_commit();
                  
              }
        }
        
    }

    function update_pv_auto_id($master_record,$supplier){

        if($supplier){

            $id = $supplier['id'];

            $data = array();

            $data['paymentVoucherAutoID'] = $master_record['payVoucherAutoId'];
            $data['status'] = 1;
            $this->db->where('id',$id)->update('srp_erp_ap_vendor_payments',$data);

        }

    }

    function update_master_feild($table,$id,$data){

        $this->db->where('id',$id)->update($table,$data);

    }

    function remove_added_invoice(){
        $id = $this->input->post('id');

        $this->db->where('id',$id);
        $invoice_detail = $this->db->from('srp_erp_ap_vendor_invoice_allocation')->get()->row_array();

        $payment_id = $invoice_detail['payment_id'];
        
        $this->db->where('id',$payment_id);
        $payment_record = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();

        $data_payment = array();
        $data_master = array();

        //remove the record
        $this->db->where('id',$id)->delete('srp_erp_ap_vendor_invoice_allocation');


        $payment_added = $this->db->query(
            "
            SELECT IFNULL(debitnote.allocation,0) as debitnoteTotalAmount, IFNULL(supplier.allocation,0) as supplierTotalAmount
            FROM srp_erp_ap_vendor_payments as payment
            LEFT JOIN (
                SELECT SUM(allocation_amount) as allocation,payment_id
                FROM srp_erp_ap_vendor_invoice_allocation
                WHERE invoiceType = 'debitnote' AND payment_id = {$payment_id}
                GROUP BY payment_id
            ) as debitnote ON payment.id = debitnote.payment_id
            LEFT JOIN (
                SELECT SUM(allocation_amount) as allocation,payment_id
                FROM srp_erp_ap_vendor_invoice_allocation
                WHERE invoiceType = 'SupplierInvoice' AND payment_id = {$payment_id}
                GROUP BY payment_id
            ) as supplier ON payment.id = supplier.payment_id
            WHERE payment.id = {$payment_id}
            "
        )->row_array();
        
        $allocation = $payment_added['supplierTotalAmount'] - $payment_added['debitnoteTotalAmount'];
        //update payment records
        $data_payment['balance_due'] = $allocation;
        $data_payment['schedule_pmt'] = $allocation;
        $data_payment['allocation'] = $allocation;

        if($data_payment['allocation'] < 0){
            $this->session->set_flashdata('w', 'Grnad total become less than 0');
        }

        $this->db->where('id',$payment_id)->update('srp_erp_ap_vendor_payments',$data_payment);
        

        if($invoice_detail['invoiceType'] == 'debitnote'){
          
            //update master record
            
            //update payment records
            // $data_payment['balance_due'] = $payment_record['balance_due'] + $invoice_detail['allocation_amount'];
            // $data_payment['schedule_pmt'] = $payment_record['balance_due'] + $invoice_detail['allocation_amount'];
            // $data_payment['allocation'] = $payment_record['balance_due'] + $invoice_detail['allocation_amount'];

            // $this->db->where('id',$payment_id)->update('srp_erp_ap_vendor_payments',$data_payment);

           

        }else{

            //update master record
            $master_rec = $this->db->where('InvoiceAutoID',$invoice_detail['InvoiceAutoID'])->from('srp_erp_paysupplierinvoicemaster')->get()->row_array();
            $data_master['paymentTotalAmount'] = $master_rec['paymentTotalAmount'] - $invoice_detail['allocation_amount'];

            if($data_master['paymentTotalAmount'] < 0){
                $data_master['paymentTotalAmount'] = 0;
            }

            $this->db->where('InvoiceAutoID',$invoice_detail['InvoiceAutoID'])->update('srp_erp_paysupplierinvoicemaster',$data_master);


        }

      

        $this->session->set_flashdata('s', 'Invoice removed Successfully');
        return true;

    }

    function remove_complete_vendor_payment(){

       // echo '<pre>';
        $id = $this->input->post('id');

        $this->db->where('id',$id);
        $payment_record = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();

        $this->db->where('payment_id',$payment_record['id']);
        $invoice_details = $this->db->from('srp_erp_ap_vendor_invoice_allocation')->get()->result_array();

        foreach($invoice_details as $invoice){

            $data_master = array();

            if($invoice['invoiceType'] == 'debitnote'){


            }else{
                $master_rec = $this->db->where('InvoiceAutoID',$invoice['InvoiceAutoID'])->from('srp_erp_paysupplierinvoicemaster')->get()->row_array();
                $data_master['paymentTotalAmount'] = $master_rec['paymentTotalAmount'] - $invoice['allocation_amount'];
    
                if($data_master['paymentTotalAmount'] < 0){
                    $data_master['paymentTotalAmount'] = 0;
                }
    
                $this->db->where('InvoiceAutoID',$invoice['InvoiceAutoID'])->update('srp_erp_paysupplierinvoicemaster',$data_master);
            }

        }
        
        //remove allocations
        $this->db->where('payment_id',$id)->delete('srp_erp_ap_vendor_invoice_allocation');

        //remove payment
        $this->db->where('id',$id)->delete('srp_erp_ap_vendor_payments');

        $this->session->set_flashdata('s', 'Supplier removed Successfully');
        return true;


    }

    function get_more_suppliers_add(){

        $master_id = $this->input->post('master_id');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->where('srp_erp_ap_vendor_payments.id IS NULL',null);
        $this->db->where('srp_erp_suppliermaster.companyID',$companyID);
        $this->db->from('srp_erp_suppliermaster');
        $this->db->join('srp_erp_ap_vendor_payments',"srp_erp_suppliermaster.supplierSystemCode = srp_erp_ap_vendor_payments.vendor_code AND srp_erp_ap_vendor_payments.master_id = {$master_id}",'left');
        $suppliers = $this->db->get()->result_array();

        return $suppliers;

    }

    function add_supplier_to_payment(){
        
        $master_id = $this->input->post('master_id');
        $supplierID = $this->input->post('supplierID');

        $this->db->where('supplierAutoID',$supplierID);
        $supplier_detail = $this->db->from('srp_erp_suppliermaster')->get()->row_array();

        
        $this->db->where('id',$master_id);
        $master = $this->db->from('srp_erp_ap_vendor_payments_master')->get()->row_array();

        if($supplier_detail){
            
            $supplier_code = $supplier_detail['supplierSystemCode'];

            $this->db->where('vendor_code',$supplier_code);
            $this->db->where('master_id',$master_id);
            $add_vendor_ex = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();

            if(empty($add_vendor_ex)){
                $data = array();
                $data['vendor_code'] = $supplier_code;
                $data['vendor_name'] = $supplier_detail['supplierName'];
                $data['master_id'] = $master_id;
                $data['date'] = current_date();
                $data['doc_id'] = $master['doc_id'];

                $transaction_currency = get_currency_master_by_id($master['transaction_currency_id']);
                $bank_currency = get_currency_master_by_id($master['bank_currency']);


                $data['bank_currency'] = $bank_currency['currencyID'].'|'.$bank_currency['CurrencyCode'];
                $data['payment_currency'] = $transaction_currency['currencyID'].'|'.$transaction_currency['CurrencyCode'];
                $exchange_rate = currency_conversionID($master['bank_currency'],$master['transaction_currency_id']);
                $data['exchange_rate'] = $exchange_rate['conversion'];
                
                $this->db->insert('srp_erp_ap_vendor_payments',$data);

                $this->session->set_flashdata('s', 'Supplier Added Successfully');
                return true;

            }

            $this->session->set_flashdata('e', 'Supplier Already exists');
            return true;

        }

    }

    function get_more_invoice_for_supplier(){
        // echo '<pre>';
        $payment_id = $this->input->post('payment_id');

        $this->db->where('srp_erp_ap_vendor_payments.id',$payment_id);
        $payment_detail = $this->db->from('srp_erp_ap_vendor_payments')
                            ->join('srp_erp_ap_vendor_payments_master','srp_erp_ap_vendor_payments.master_id = srp_erp_ap_vendor_payments_master.id','left')
                            ->join('srp_erp_suppliermaster','srp_erp_ap_vendor_payments.vendor_code = srp_erp_suppliermaster.supplierSystemCode','left')->get()->row_array();

        if($payment_detail){
            $vendor_code = $payment_detail['vendor_code'];
            $supplierAutoID = $payment_detail['supplierAutoID'];
            $currencyID = $payment_detail['transaction_currency_id'];
            $PVdate = $payment_detail['date'];

            $_POST['selection_type'] = 1;
            $get_invoices = $this->fetch_supplier_inv($supplierAutoID,$currencyID, $PVdate);

            $get_debitnotes = $this->fetch_supplier_debitnotes_main($supplierAutoID,$currencyID, $PVdate,null,null);  

            foreach($get_debitnotes as $debit){
                $get_invoices[] = $debit;
            }

            return $get_invoices;


        }
       
    }

    function set_vendor_additionl_bill(){

        $master_id = $this->input->post('master_id');
        $payment_id = $this->input->post('selected_invoice');
        $invoice = $this->input->post('invoice');
        $invoice_debitnote = $this->input->post('invoice_debitnote');

        if(count($invoice) > 0){

            $invoices_list = join(',',$invoice);

            $this->db->where('id',$master_id);
            $master = $this->db->from('srp_erp_ap_vendor_payments_master')->get()->row_array();
    
            $this->db->where('srp_erp_ap_vendor_payments.id',$payment_id);
            $payment_detail = $this->db->from('srp_erp_ap_vendor_payments')
                                ->join('srp_erp_ap_vendor_payments_master','srp_erp_ap_vendor_payments.master_id = srp_erp_ap_vendor_payments_master.id','left')
                                ->join('srp_erp_suppliermaster','srp_erp_ap_vendor_payments.vendor_code = srp_erp_suppliermaster.supplierSystemCode','left')->get()->row_array();
    
            $detail_invoice_list = $this->fetch_supplier_inv_list($payment_detail['supplierAutoID'],$payment_detail['transaction_currency_id'],$payment_detail['date'],$invoices_list);
    
            $allocation = 0;
            foreach($detail_invoice_list as $key => $invoices){
                $invoices['transactionAmount'] = $invoices['transactionAmount'] - ($invoices['paymentTotalAmount'] + $invoices['DebitNoteTotalAmount'] + $invoices['advanceMatchedTotal']);
                
                $allocation += $invoices['transactionAmount'];
                $detail_invoice_list[$key]['allocationTotal'] = $invoices['transactionAmount'];
                $detail_invoice_list[$key]['bank_currency'] = $master['bank_currency'];
                $detail_invoice_list[$key]['transactionAmount'] = $invoices['transactionAmount'];
            }
    
            $res = $this->add_invoice_allocations($master,$detail_invoice_list,$payment_id,$allocation);
    
            $this->db->where('srp_erp_ap_vendor_payments.id',$payment_id);
            $payment_detail = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();
    
            $data_payment = array();
            $data_payment['balance_due'] = $payment_detail['balance_due'] + $allocation;
            $data_payment['schedule_pmt'] = $payment_detail['schedule_pmt'] + $allocation;
            $data_payment['allocation'] = $payment_detail['allocation'] + $allocation;
    
            $this->db->where('id',$payment_id)->update('srp_erp_ap_vendor_payments',$data_payment);
        }

        if(count($invoice_debitnote) > 0){
            $invoices_list = join(',',$invoice_debitnote);

            $this->db->where('id',$master_id);
            $master = $this->db->from('srp_erp_ap_vendor_payments_master')->get()->row_array();
    
            $this->db->where('srp_erp_ap_vendor_payments.id',$payment_id);
            $payment_detail = $this->db->from('srp_erp_ap_vendor_payments')
                                ->join('srp_erp_ap_vendor_payments_master','srp_erp_ap_vendor_payments.master_id = srp_erp_ap_vendor_payments_master.id','left')
                                ->join('srp_erp_suppliermaster','srp_erp_ap_vendor_payments.vendor_code = srp_erp_suppliermaster.supplierSystemCode','left')->get()->row_array();
            
            $detail_invoice_list = $this->fetch_supplier_debitnotes($payment_detail['supplierAutoID'],$payment_detail['transaction_currency_id'],$payment_detail['date'],null,null,$invoices_list);

      
            $allocation = 0;
            foreach($detail_invoice_list as $key => $invoices){
                $allocation += ($invoices['transactionAmount'] - ($invoices['allocation_amount']+$invoices['PVTransactionAmount']));
                $detail_invoice_list[$key]['allocationTotal'] =  ($invoices['transactionAmount'] - ($invoices['allocation_amount']+$invoices['PVTransactionAmount']));
                $detail_invoice_list[$key]['bank_currency'] = $master['bank_currency'];
                $detail_invoice_list[$key]['transactionAmount'] = ($invoices['transactionAmount'] - ($invoices['allocation_amount']+$invoices['PVTransactionAmount']));
            }

            $res = $this->add_invoice_allocations_debitnotes($master,$detail_invoice_list,$payment_id,$allocation);
    
            $this->db->where('srp_erp_ap_vendor_payments.id',$payment_id);
            $payment_detail = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();
    
            $data_payment = array();
            $data_payment['balance_due'] = $payment_detail['balance_due'] - $allocation;
            $data_payment['schedule_pmt'] = $payment_detail['schedule_pmt'] - $allocation;
            $data_payment['allocation'] = $payment_detail['allocation'] - $allocation;
    
            $this->db->where('id',$payment_id)->update('srp_erp_ap_vendor_payments',$data_payment);
       

        }
       

        $this->session->set_flashdata('s', 'Supplier Added Successfully');
        return true;

    }

    function delete_all_invoice(){
        $delete_arr = $this->input->post('delete_arr');

        foreach($delete_arr as $id){

            $_POST['id'] = $id;

            $this->remove_added_invoice();

        }

        if($delete_arr){
            $this->session->set_flashdata('s', 'Invoice Deleted Successfully');
            return true;
        }
       
    }

    function delete_all_master(){

        $master_id = $this->input->post('master_id');

        $this->db->where('master_id',$master_id);
        $master_arr = $this->db->from('srp_erp_ap_vendor_payments')->get()->result_array();

        foreach($master_arr as $payment){

            $_POST['id'] = $payment['id'];

            $res = $this->remove_complete_vendor_payment();

        }

        $this->db->where('id',$master_id);
        $master = $this->db->delete('srp_erp_ap_vendor_payments_master');


        $this->session->set_flashdata('s', 'Deleted Successfully');
        return true;

    }

}