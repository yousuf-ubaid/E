<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Erp_data_sync_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function create_update_company_item_sync_record($lastId,$request_type){
        //main db function

        $company_id=current_companyID();
        
        if($lastId && $company_id){
            $this->db->set('companyId', ($company_id));
            $this->db->set('itemAutoId', ($lastId));
            $this->db->set('request_type', $request_type);
            $this->db->set('created_date', ($this->common_data['current_date']));
            $result = $this->db->insert('srp_erp_ecommerce_datasync');
        }

    }

    function get_items_to_sync(){
        $this->db->where('send_status', 0);
        $this->db->from('srp_erp_ecommerce_datasync');
        return $item_list = $this->db->get()->result_array();
    }

    function getMainCompanyDetails($company_id){
       
        $db2 = $this->load->database('db2', TRUE);
        $db2->where('company_id', $company_id);
        $db2->from('srp_erp_company');
        return $item_list = $db2->get()->row_array();

    }

    function getItemDetails($itemAutoId){
        
        $this->db->select("itemSystemCode,documentID,itemImage,itemName,itemDescription,mainCategoryID,mainCategory,
        subcategoryID,subSubCategoryID,itemUrl,barcode,financeCategory,defaultUnitOfMeasureID,secondaryUOMID,defaultUnitOfMeasure,currentStock,reorderPoint,
        maximunQty,minimumQty,revanueGLAutoID,revanueSystemGLCode,revanueGLCode,
        revanueDescription,revanueType,costGLAutoID,costSystemGLCode,costGLCode,costDescription,
        costType,assteGLAutoID,assteSystemGLCode,assteGLCode,assteDescription,assteType,stockAdjustmentGLAutoID,
        stockAdjustmentSystemGLCode,stockAdjustmentGLCode,stockAdjustmentDescription,stockAdjustmentType,faCostGLAutoID,faACCDEPGLAutoID,
        faDEPGLAutoID,faDISPOGLAutoID,salesTaxFormulaID,isMfqItem,taxVatSubCategoriesAutoID,allowedtoBuyYN,allowedtoSellYN,
        purchaseTaxFormulaID,isActive,isSubitemExist,subItemapplicableon,companyLocalCurrency,companyLocalExchangeRate,companyLocalPurchasingPrice,
        companyReportingPurchasingPrice,companyLocalSellingPrice,companyLocalWacAmount,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,
        companyReportingExchangeRate,companyReportingSellingPrice,companyReportingWacAmount,companyReportingCurrencyDecimalPlaces,finCompanyPercentage,
        pvtCompanyPercentage,companyCode,masterConfirmedDate,masterApprovedDate");
        $this->db->where('itemAutoId', $itemAutoId);
        $this->db->from('srp_erp_itemmaster');
        return $item_list = $this->db->get()->row_array();

    }

    function set_ecommerce_mapping(){

        $companyID = $this->common_data['company_data']['company_id'];
        $gl_code = $this->input->post('gl_code');
        $invoice_type = $this->input->post('invoice_type');
        $company_group = $this->input->post('group');
        $segment = $this->input->post('segment');
        $client_header = $this->input->post('client_header');
        $client_header_name = $this->input->post('client_header_name');
        $client_header_id = $this->input->post('client_header_id');
        $transaction_type = $this->input->post('transaction_type'); // cr-- credit // dr -- debit
        $description = $this->input->post('description');
        $mapped_column = $this->input->post('mapped_column');
        $mapped_column_name = $this->input->post('mapped_column_name');
        $erp_header_id = $this->input->post('erp_header_id');
        $posting_id = $this->input->post('posting_id');
        $action = $this->input->post('action');
        $mapping_id = $this->input->post('mapping_id');
        $mapping_type = $this->input->post('mapping_type');
        $control_acc = $this->input->post('control_acc');
        $doc_type_customer = $this->input->post('doc_type_customer');
        $doc_type_vendor = $this->input->post('doc_type_vendor');
        $doc_type_customer_edit = $this->input->post('doc_type_customer_edit');
        $doc_type_vendor_edit = $this->input->post('doc_type_vendor_edit');
    
    
        $data = array();

        if($companyID){

            $this->db->trans_start();

            $data['client_sales_header'] = $client_header_name;
            $data['client_sales_header_id'] = $client_header_id;
            $data['erp_gl_code'] = $gl_code;
            $data['erp_column_name'] = $mapped_column_name;
            $data['erp_column_id'] = $erp_header_id;
            $data['erp_mapped_by'] = $this->common_data['current_user'];
            $data['erp_mapped_date'] = $this->common_data['current_date'];
            $data['erp_group_id'] = $company_group;
            $data['erp_segment_id'] = $segment;
            $data['erp_cr_dr'] = $transaction_type;
            $data['erp_description'] = $description;
            if($posting_id){
                $data['posting_id'] = $posting_id;
            }
            $data['mapping_type'] = $mapping_type;
            $data['control_acc'] = $control_acc;

            if($mapping_type == 2){
                if($action == 'edit'){
                    $data['invoice_type'] = $doc_type_customer_edit;
                }else{
                    $data['invoice_type'] = $doc_type_customer;
                }
                
            }else if($mapping_type == 1){
                if($action == 'edit'){
                    $data['invoice_type'] = $doc_type_vendor_edit;
                }else{
                    $data['invoice_type'] = $doc_type_vendor;
                }
               
            }

            if($action == 'edit'){
                $this->db->where('id',$mapping_id);
                $this->db->update('srp_erp_ecommerce_sales_clientmapping', $data);
            }else{
                $this->db->insert('srp_erp_ecommerce_sales_clientmapping', $data);
            }
            
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Sales : ' . $data['client_sales_header'] . ' Mapping Failed' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Sales : ' . $data['client_sales_header'] . ' Mapping Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
           
        }else{
            $this->session->set_flashdata('e', 'Soemthing Went Wrong');
            return array('status' => false);
        }


    }

    function get_mapping_data_record($id){
        
        $this->db->where('id', $id);
        $this->db->from('srp_erp_ecommerce_sales_clientmapping');
        return $mapping_list = $this->db->get()->row_array();

    }

    function destroy_ecommerce_mapping(){

        $mapping_id = $this->input->post('mapping_id');

        $this->db->where('id', $mapping_id);
        $results = $this->db->delete('srp_erp_ecommerce_sales_clientmapping');

        if ($results) {
            $this->session->set_flashdata('s', 'Record Deleted Successfully');
            return array('status' => true);
        } else {
           
            $this->session->set_flashdata('e', 'Error while deleting, please contact the system team!');
            return array('status' => false);
        }


    }


    // Double entry view

    function get_segement_detials($id){

        $response = $this->db->from('srp_erp_segment')->where('segmentID',$id)->get()->row_array();

        return $response;

    }

    function get_supplier_details($secondaryCode){

        $response = $this->db->from('srp_erp_suppliermaster')->where('secondaryCode',$secondaryCode)->get()->row_array();

        return $response;

    }

    function get_customer_details($secondaryCode){

        $response = $this->db->from('srp_erp_customermaster')->where('secondaryCode',$secondaryCode)->get()->row_array();

        return $response;

    }

    function get_gl_code_details($id){

        $response = $this->db->from('srp_erp_chartofaccounts')->where('GLAutoID',$id)->get()->row_array();

        return $response;

    }

    function get_active_posting_id($service_type = null, $payment = null){

        if($service_type){
            $service_type_id = srp_posting_service_type_get_by_name($service_type);

            $response = $this->db->from('srp_erp_ecommerce_posting')->where('service_type',$service_type_id)->where('mode_collection',$payment)->where('status',1)->get()->row_array();
        }else{
            $response = $this->db->from('srp_erp_ecommerce_posting')->where('status',1)->get()->row_array();
        }

        return $response;

    }

    function get_active_posting_id_for_service_type($posting_id){

        
        $posting_details = $this->get_posting_data_from_posting_from_doc_id($posting_id);

        if($posting_details){
            $service_type = $posting_details['service_type'];
            $mode_collection = $posting_details['mode_collection'];

            $response = $this->db->from('srp_erp_ecommerce_posting')->where('status',1)->where('service_type',$service_type)->where('mode_collection',$mode_collection)->get()->row_array();
        }else{
            $response = $this->db->from('srp_erp_ecommerce_posting')->where('status',1)->get()->row_array();
        }   
    
        return $response;

    }


    function get_company_mappings($mapping_type = null, $posting_id = null, $service_type = null,$payment = '1',$invoice_type = null){

        if($posting_id){
            $posting_details = $this->get_active_posting_id_for_service_type($posting_id);
        }else{

            if($service_type){
                $posting_details = $this->get_active_posting_id($service_type,$payment);

                if(empty($posting_details)){
                    $posting_details = $this->get_active_posting_id($service_type,1);
                }

            }else{
                $posting_details = $this->get_active_posting_id();
            }
            
        }

        $posting_id = $posting_details['id'];

        if($mapping_type){
            
            if($invoice_type){
                $response = $this->db->from('srp_erp_ecommerce_sales_clientmapping')->where('posting_id',$posting_id)->where('mapping_type',$mapping_type)->where('invoice_type',$invoice_type)->get()->result_array();
            }else{
                $response = $this->db->from('srp_erp_ecommerce_sales_clientmapping')->where('posting_id',$posting_id)->where('mapping_type',$mapping_type)->get()->result_array();
            }
        
        }else{
            $response = $this->db->from('srp_erp_ecommerce_sales_clientmapping')->where('posting_id',$posting_id)->get()->result_array();
        }

        return $response;

    }

    function get_sales_client_record($mapping_id =  null){

        if(empty($mapping_id)){
            $mapping_id = $this->input->post('client_sales_id');
        }
      
        $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')->where('id',$mapping_id)->get()->row_array();

        return $response;

    }

    function get_sales_client_records_from_store_id($store_id =  null){

        $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')->where('store_id',$store_id)->where('erp_record_status',0)->get()->result_array();

        return $response;

    }


    function get_order_process_log($order_id,$sales_id){

        $company_id = $this->common_data['company_data']['company_id'];

        $response = $this->db->from('srp_erp_ecommerce_error_log')
                        ->where('sales_id',$sales_id)
                        ->where('order_ref',$order_id)
                        ->where('company_id',$company_id)
                        ->get()
                        ->result_array();

        return $response;

    }

    function get_clent_ecommerce_settings(){

        $company_id = $this->common_data['company_data']['company_id'];

        $response = $this->db->from('srp_erp_ecommerce_settings')->where('company_id',$company_id)->get()->row_array();

        return $response;

    }

    function set_clent_ecommerce_settings(){

        $updated_type = $this->input->post('updated_type');
        $company_driver_id = $this->input->post('company_driver_id');
        $company_country = $this->input->post('company_country');
        $company_currency = $this->input->post('company_currency');
        $company_category = $this->input->post('company_category');
        $company_bank_id = $this->input->post('company_bank_id');


        $company_id = $this->common_data['company_data']['company_id'];

        try{

            $response = $this->db->set($updated_type,$$updated_type)->where('company_id',$company_id)->update('srp_erp_ecommerce_settings');

            $this->session->set_flashdata('s', 'Successfully updated');
            return array('status' => true);

        }catch(\Exception $e){
            $this->session->set_flashdata('w', 'Something went wrong');
            return array('status' => false);
        }

    }

    function get_sales_client_credit_debit($mapping_id = null,$mapping_type = null,$posting_id = null, $invoice_type = null){
        
        if(empty($mapping_id)){
            $mapping_id = $this->input->post('client_sales_id');
            $posting_id = $this->input->post('posting_id');
        }
        
        $record = $this->get_sales_client_record($mapping_id);

        $service_type = $record['service_type'];
        $payment = ($record['payment'])? $record['payment'] : 'ALL';

        $payment_id = srp_posting_mode_collection_get_by_name($payment);
    

        if($posting_id){
            $mapping_columns = $this->get_company_mappings($mapping_type,$posting_id,$service_type,$payment_id,$invoice_type);
        }else{
            $mapping_columns = $this->get_company_mappings($mapping_type,null,$service_type,$payment_id,$invoice_type);
        }

        $total_debit_value = 0;
        $total_credit_value = 0;
        
        $base_record_arr = array('data'=>array(),'total_credit_value'=>'','total_debit_value'=>'');

        foreach($mapping_columns as $value){

            $base_arr = array('client_header'=>'','segement'=>'','gl_code'=>'','descripiton'=>'','credit'=>'','debit'=>'','entry'=>'','store_id'=>'','currency'=>'','control_acc'=>'');
            $client_sales_header_id = $value['client_sales_header_id'];
            $client_sales_header_name = $value['client_sales_header'];
            $sales_value = isset($record[$client_sales_header_name]) ? $record[$client_sales_header_name]: '';

            $base_arr['client_header'] = $client_sales_header_name;

            $segment_details = $this->get_segement_detials($value['erp_segment_id']);
            if($segment_details){
                $base_arr['segement'] = $segment_details['companyCode'].'|'.$segment_details['segmentCode']; 
            }

            $gl_details = $this->get_gl_code_details($value['erp_gl_code']);
            if($gl_details){
                $base_arr['gl_code'] = $gl_details['systemAccountCode'].'|'.$gl_details['GLDescription']; 
            }

            $base_arr['descripiton'] = $value['erp_description'];
            $base_arr['store_id'] = isset($record['store_id'])? $record['store_id'] : '';
            $base_arr['currency'] = isset($record['currency'])? $record['currency'] : '';
            $base_arr['segment_id'] = $value['erp_segment_id'];
            $base_arr['control_acc'] = $value['control_acc'];
            $base_arr['mapping_type'] = $value['mapping_type'];
            $base_arr['gl_account_code'] = $gl_details['systemAccountCode'];
            $base_arr['gl_auto_id'] = $gl_details['GLAutoID'];
            $base_arr['gl_account_description'] = $gl_details['GLDescription'];

            if($value['erp_cr_dr'] == 'cr'){
                $base_arr['credit'] = $sales_value;
                $total_credit_value += $sales_value;
            }else{
                $base_arr['debit'] = $sales_value;
                $total_debit_value += $sales_value;
            }
            $base_arr['entry'] = $value['erp_cr_dr'];

            $base_record_arr['data'][] = $base_arr;

        }

        $base_record_arr['total_credit_value'] = $total_credit_value;
        $base_record_arr['total_debit_value'] = $total_debit_value;

        return $base_record_arr;

    }

    function get_sales_client_credit_debit_summary($mapping_id = null,$mapping_type = null,$posting_id = null, $invoice_type = null){
        
        if(empty($mapping_id)){
            $mapping_id = $this->input->post('client_sales_id');
            $posting_id = $this->input->post('posting_id');
        }

        if($posting_id){
            $sales = $this->get_sales_client_credit_debit($mapping_id,$mapping_type,$posting_id,$invoice_type);
        }else{
            $sales = $this->get_sales_client_credit_debit($mapping_id,$mapping_type,null,$invoice_type);
        }
        
        try {

            if($sales){
    
                $records = $sales['data'];

                if(empty($records)){
                    return array('status'=>'error','message'=>'No mapping records');
                }

                $total_credit_value = $sales['total_credit_value'];
                $total_debit_value = $sales['total_debit_value'];
                $gl_code_arr = array();
                $gl_code_result_arr = array();
                $sales_final = array();
                $temp_arr = array();
    
                foreach($records as $value){
    
                    $gl_code_arr['credit'][$value['gl_code']] =  ((isset($gl_code_arr['credit'][$value['gl_code']]) ? $gl_code_arr['credit'][$value['gl_code']] : 0) + $value['credit']);
                    $gl_code_arr['debit'][$value['gl_code']] =  ((isset($gl_code_arr['debit'][$value['gl_code']]) ? $gl_code_arr['debit'][$value['gl_code']] : 0) + $value['debit']);
    
                }
    
                foreach($gl_code_arr['credit'] as $key => $value){
    
                    $debit_value = $gl_code_arr['debit'][$key];
                    $gl_code_result_arr[$key] = $value - $debit_value; // plus -- credit // minus -- debit
    
                }
    
    
                foreach($records as $value){
                    if(!in_array($value['gl_code'] , $temp_arr)){
                        $value['final_value'] = $gl_code_result_arr[$value['gl_code']];
                        $sales_final[] = $value;
                        $temp_arr[] = $value['gl_code'];
                    }
                  
                }
    
    
                $sales['data'] = $sales_final;
    
            }
            
            return $sales;

        } catch (Exception $e){
            return $sales;
        }

    }

    function get_sales_client_credit_debit_summary_all($mapping_id = null){
        
        if(empty($mapping_id)){
            $mapping_id = $this->input->post('client_sales_id');
        }

        $sales = $this->get_sales_client_credit_debit($mapping_id);

        if($sales){

            $records = $sales['data'];
            $total_credit_value = $sales['total_credit_value'];
            $total_debit_value = $sales['total_debit_value'];
            $gl_code_arr = array();
            $gl_code_result_arr = array();
            $sales_final = array();
            $temp_arr = array();

            foreach($records as $value){

                $gl_code_arr['credit'][$value['gl_code']] =  ((isset($gl_code_arr['credit'][$value['gl_code']]) ? $gl_code_arr['credit'][$value['gl_code']] : 0) + $value['credit']);
                $gl_code_arr['debit'][$value['gl_code']] =  ((isset($gl_code_arr['debit'][$value['gl_code']]) ? $gl_code_arr['debit'][$value['gl_code']] : 0) + $value['debit']);

            }

            foreach($records as $value){
                if(!in_array($value['gl_code'] , $temp_arr)){
                    $value['final_credit_value'] =  $gl_code_arr['credit'][$value['gl_code']];
                    $value['final_debit_value'] =  $gl_code_arr['debit'][$value['gl_code']];
                    $sales_final[] = $value;
                    $temp_arr[] = $value['gl_code'];
                }
              
            }
          
            // foreach($gl_code_arr['credit'] as $key => $value){

            //     $debit_value = $gl_code_arr['debit'][$key];
            //     $gl_code_result_arr[$key] = $value - $debit_value; // plus -- credit // minus -- debit

            // }


            


            $sales['data'] = $sales_final;

        }
        
        

        return $sales;

    }

    function set_general_ledger_records($sales_id){

        $mapping_records = $this->get_sales_client_credit_debit_summary($sales_id);
        $base_ledger_arr = array();

        if($mapping_records){

            $response = $this->save_supplier_invoice_set($sales_id);
          
        }else{

            $response = array();
        }

        return $response;

    }

    function get_client_data_already_process($sales_id,$doc_type = null){

        $client_sales_data = $this->get_sales_client_record($sales_id);

        if($client_sales_data){
            if($doc_type == '3PL_vendor'){
                $doc_id = $client_sales_data['3pl_vendor_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }elseif($doc_type == '3PL_customer'){
                $doc_id = $client_sales_data['3pl_customer_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }elseif($doc_type == 'supplier'){
                $doc_id = $client_sales_data['invoice_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }elseif($doc_type == 'customer'){
                $doc_id = $client_sales_data['customer_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }elseif($doc_type == 'direct_invoice'){
                $doc_id = $client_sales_data['direct_receipt_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }elseif($doc_type == 'jv'){
                $doc_id = $client_sales_data['jv_auto_id'];

                if($doc_id){
                    return $doc_id;
                }
            }
            // $order = $client_sales_data['order'];

            // $this->db->where('RefNo', $order);
            // $this->db->from('srp_erp_paysupplierinvoicemaster');
            // $item_list = $this->db->get()->result_array();

            
        }

        return false;

    }

    function save_supplier_invoice_set($sales_id){

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,1);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];


        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

        // send back nothing generate
        if(empty($selected_gl_record)){
            add_process_log_record(0,'BSI',$sales_id,2,'No data to generate Supplier Invoice',1);
            return array('status'=>'error', 'message' => 'No data to generate Supplier Invoicer');
        }


        try{

            $response_header = $this->save_supplier_invoice_header($sales_id,$client_sales_data,$client_mapping);

            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_supplier_expense_records($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);

                $response_confirmation = $this->supplier_invoice_confirmation($response['last_id'],$client_sales_data);

                $up_response = $this->update_client_data($sales_id,$response['last_id']);

                add_process_log_record($response['last_id'],'BSI',$sales_id,1,'Supplier Invoice Created',1);


            }else{
                add_process_log_record(isset($response['last_id']) ? $response['last_id'] : '','BSI',$sales_id,2,'Supplier Invoice Generate Failed',1);

                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Supplier invoice successfully created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
      

    }

    function update_client_data($sales_id,$invoice_id,$type =  null){

        if($type == 'customer'){
            $this->db->set('customer_auto_id', ($invoice_id));
        }elseif($type == '3pl_vendor'){
            $this->db->set('3pl_vendor_auto_id', ($invoice_id));
        }elseif($type == '3pl_customer'){
            $this->db->set('3pl_customer_auto_id', ($invoice_id));
        }elseif($type == '3pl_direct_receipt_id'){
            $this->db->set('direct_receipt_auto_id', ($invoice_id));
        }elseif($type == 'jv_receipt_id'){
            $this->db->set('jv_auto_id', ($invoice_id));
        }elseif($type == 'dn_auto_id'){
            $this->db->set('dn_auto_id', ($invoice_id));
        }else{
            $this->db->set('invoice_auto_id', ($invoice_id));
        }
        
        $this->db->set('erp_record_status', 1);
        $this->db->set('erp_record_process_date', date('Y-m-d H:i:s'));
        $this->db->where('id',$sales_id);
        $result = $this->db->update('srp_erp_ecommerce_sales_clientdata');

    }

    function save_supplier_invoice_header($order_auto_id,$client_sales_data,$client_mapping,$vendor_type = null){

        if(empty($client_sales_data)){
            return false;
        }

        $bokngDt = $client_sales_data['date_time'];
        $invduedt = $client_sales_data['completed_time'];
        $invdt = $client_sales_data['date_time'];
        $invoiceType = 'StandardExpense';
        $financearray_rec = get_financial_period_date_wise($invduedt);
        $financearray = $financearray_rec['companyFinancePeriodID'];


        if($vendor_type == '3pl'){
            $supplier_details = $this->get_supplier_details(trim($client_sales_data['3pl_company_id'] ?? ''));
        }else{
            $supplier_details = $this->get_supplier_details(trim($client_sales_data['store_id'] ?? ''));
        }
       
        $segment_id = 86;
        $referenceno = $client_sales_data['order'];


        if(empty($supplier_details)){
            return false;
        }

        $date_format_policy = date_format_policy();
        $bookingDate = input_format_date($bokngDt, $date_format_policy);

        $supplierInvoiceDueDate = input_format_date($invduedt, $date_format_policy);
        $invoiceDate = input_format_date($invdt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        if($financeyearperiodYN==1) {
           
            $financePeriod = fetchFinancePeriod($financearray);

            if ($bookingDate >= $financePeriod['dateFrom'] && $bookingDate <= $financePeriod['dateTo']) {
                if (($invoiceDate) > ($supplierInvoiceDueDate)) {
                   return json_encode(array('e'=>"error","message"=>"Invoice Due Date cannot be lesser than invoice Date!"));
                } else {
                    return json_encode($this->save_supplier_invoice_header_process($bokngDt,$invduedt,$supplier_details,$invoiceType,$segment_id,$referenceno));
                }
            } else {
                return json_encode(array('e'=>"error","message"=>"Invoice Date not between Financial Period !"));
            }
        }else{
            return json_encode($this->save_supplier_invoice_header_process($bokngDt,$invduedt,$supplier_details,$invoiceType,$segment_id,$referenceno));
        }

       
    }


    function save_supplier_invoice_header_process($bookDate,$supplirInvDuDate,$supplier_details,$invoiceType,$segment_id,$referenceno)
    {
        $this->db->trans_start();

        //$bookDate = $this->input->post('bookingDate');
        // $supplirInvDuDate = $this->input->post('supplierInvoiceDueDate');
        $supplierinvoice = $this->input->post('supplier_invoice_no');
        $rcmApplicable = trim($this->input->post('rcmApplicable') ?? '');
        $rcmYN = trim($this->input->post('rcmYN') ?? '');
        $invoiceautoid = $this->input->post('InvoiceAutoID');
        $supplirinvoiceDate = $supplirInvDuDate;
        $currency_code = 'OMR';
        $company_finacial_year = $this->common_data['company_data']['companyFinanceYear'];
        $company_finacial_year_id = $this->common_data['company_data']['companyFinanceYearID'];
        $company_finacial_period_id = $this->common_data['company_data']['companyFinancePeriodID'];
        $currency_detail = getCurrencyDetail_byCurrencyCode($currency_code);

        // print_r(json_encode($currency_detail)); exit;
   
        $supplierid = $supplier_details['supplierAutoID'];
      
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();

        $bookingDate = input_format_date($bookDate, $date_format_policy);
        
        $supplierInvoiceDueDate = input_format_date($supplirInvDuDate, $date_format_policy);
       
        $supplierinvoiceDate_new = input_format_date($supplirinvoiceDate, $date_format_policy);
       
        $currency_code = explode('|', trim($currency_code));

        // $location = explode('|', trim($this->input->post('location') ?? ''));
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));

        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($company_finacial_year));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($bookingDate);
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
            $financePeriodDetails = get_financial_period_date_wise($bookingDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $supplier_arr = $supplier_details;
       

        // $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID') ?? ''));
        $data['invoiceType'] = trim($invoiceType);
        $data['bookingDate'] = trim($bookingDate);
        $data['invoiceDueDate'] = trim($supplierInvoiceDueDate);
        $data['invoiceDate'] = trim($supplierinvoiceDate_new);
        $data['companyFinanceYearID'] = trim($company_finacial_year_id);
        $data['companyFinanceYear'] = trim($company_finacial_year);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($company_finacial_period_id);
        $data['documentID'] = 'BSI';
        $data['supplierID'] = trim($supplierid);
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['supplierInvoiceNo'] = trim($supplierinvoice);
        $data['supplierInvoiceDate'] = trim($supplierInvoiceDueDate);
        $data['transactionCurrency'] = 'OMR';
        $data['segmentID'] = trim($segment_id);

        $data['RefNo'] = trim($referenceno);
        $comments = '';
        $data['comments'] = str_replace('<br />', PHP_EOL, $comments);
        //$data['comments'] = trim($this->input->post('comments') ?? '');
        $data['transactionCurrencyID'] = trim($currency_detail['currencyID'] ?? '');
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

        $data['rcmApplicableYN'] =$rcmYN;

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['bookingInvCode'] = 0;

        if ((trim($this->input->post('invoiceType') ?? '') == 'StandardItem') || (trim($this->input->post('invoiceType') ?? '') == 'Standard')) {
            $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All') == 1) ? 1 : 0);
        }
        
        $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);

        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Supplier Invoice   Saved Failed ' . $this->db->_error_message());
            // $this->db->trans_rollback();
            return array('status' => false, 'message'=>$this->db->_error_message());
        } else {
            // $this->session->set_flashdata('s', 'Supplier Invoice Saved Successfully.');
            // $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function save_supplier_expense_records($invoiceAutoID,$client_sales_data,$client_mapping,$selected_gl_record)
    {
        $this->db->trans_start();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $invoiceAutoID);
        $projectExist = project_is_exist();
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $invoiceAutoID, 'BSI', 'InvoiceAutoID');
        
        $descriptions = $client_sales_data['service_type'];
        $discountPercentage = $client_sales_data['discount'];

        $projectID = $this->input->post('projectID');
       
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $tax_type = $this->input->post('tax_type');
        
        
        foreach($selected_gl_record as $gl_record){
         
        $segment_details = get_segemnt_by_id($gl_record['segment_id']);
        $gl_codes = $gl_record['gl_account_code'];


        $amount =  abs($gl_record['final_value']);

        if($gl_record['entry'] == 'cr'){
            $amount = -1 * abs($gl_record['final_value']);
        }
    
        $segment_gls = $segment_details['segmentID'].' | '.$segment_details['segmentCode'] ;

        $g_code_details = fetch_gl_account_from_systemAccountCode($gl_codes,$this->common_data['company_data']['company_id']);
        $gl_code_des =  trim($g_code_details['systemAccountCode'] ?? '') . ' | ' . trim($g_code_details['GLSecondaryCode'] ?? '') . ' | ' . trim($g_code_details['GLDescription'] ?? '') . ' | ' . trim($g_code_details['subCategory'] ?? '');
 
        // foreach ($gl_codes as $key => $gl_code) {
            if($gl_codes){

                $segment = explode('|', $segment_gls);
                $gl_code = explode('|', $gl_code_des);

                $data['InvoiceAutoID'] = trim($invoiceAutoID);
                $data['GLAutoID'] = $g_code_details['GLAutoID'];
                $data['projectID'] = $projectID;
            

                $data['systemGLCode'] = trim($gl_code[0] ?? '');
                $data['GLCode'] = trim($gl_code[1] ?? '');
                $data['GLDescription'] = trim($gl_code[2] ?? '');
                $data['GLType'] = trim($gl_code[3] ?? '');
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');
                $data['description'] = $descriptions;
                $data['discountPercentage'] = trim($discountPercentage);
                $data['discountAmount'] = trim(($amount * $discountPercentage) / 100);
                // $data['transactionAmount'] = round($amount - $data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);
                $data['transactionAmount'] = $amount;
                $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
                

                $companyLocalAmount = 0;
                if ($master['companyLocalExchangeRate']) {
                    $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
                }

                $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $companyReportingAmount = 0;

                if ($master['companyReportingExchangeRate']) {
                    $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
                }

                $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $supplierAmount = 0;
                if ($master['supplierCurrencyExchangeRate']) {
                    $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
                }


                $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
                $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

               

                $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
                $last_id = $this->db->insert_id();

            }

        }

        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Supplier Invoice Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Supplier Invoice Detail : Saved Successfully.');
        }
    }

    function supplier_invoice_confirmation($invoiceAutoID,$client_sales_data)
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');

        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($invoiceAutoID));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $results = $this->db->get()->row_array();

    

        if (empty($results)) {
            return array('e'=>'error','message'=>'There are no records to confirm this document!');
        } else {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();

            if (!empty($Confirmed)) {
    
                return array('e'=>'error','message'=>'Document already confirmed ');
            } else {
                $this->db->trans_start();
                $system_id = trim($invoiceAutoID);

                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                $lenth = strlen($master_dt['bookingInvCode']);
    
                if ($lenth == 1) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();

                        if ((empty($location)) || ($location == '')) {
                            $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                            return false;
                        } else {
                            if ($emplocationid != '') {
                                $location = $this->sequence->sequence_generator_location('BSI', $master_dt['companyFinanceYearID'], $emplocationid, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            }
                        }
                    } else {
                        $location = $this->sequence->sequence_generator_fin('BSI', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($location, 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }

                    $invcod = array(
                        /* 'bookingInvCode' => $this->sequence->sequence_generator_fin('BSI',$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']),*/
                        'bookingInvCode' => $location,
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                } else {

                    $validate_code = validate_code_duplication($master_dt['bookingInvCode'], 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth,bookingDate');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $autoApproval = get_document_auto_approval('BSI');

           
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($master_data['InvoiceAutoID'], 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 'BSI', $master_data['bookingInvCode'], $master_data['bookingDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 0, $master_data['bookingDate']);
                } else {
                    $this->session->set_flashdata('e', 'Approval levels are not set for this document');
                    return array('status' => false);
                }

             

                if ($approvals_status == 1) {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $t_arr = array();
                    $tra_tax_total = 0;
                    $loca_tax_total = 0;
                    $rpt_tax_total = 0;
                    $sup_tax_total = 0;
                    $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];
                    $loca_total_amount += $data_arr['companyLocalAmount'];
                    $rpt_total_amount += $data_arr['companyReportingAmount'];
                    $supplier_total_amount += $data_arr['supplierAmount'];

              

                    $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();

                    
                    for ($x = 0; $x < count($tax_arr); $x++) {
                        $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                        $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                        $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                        $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                        $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                        $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                        $tra_tax_total = $t_arr[$x]['transactionAmount'];
                        $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                        $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                        $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                    }
                
                    /*updating transaction amount */
                    $companyID = current_companyID();
                    $r1 = "SELECT
                        srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
                            `srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                            `srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                            `srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                            `srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                            `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
                            `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
                            `srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
                            (srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0) as discountAmnt,
                            (
                                (
                                    (
                                        IFNULL(addondet.taxPercentage, 0) / 100
                                    ) * (IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0)))
                                ) + IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0))
                            ) AS total_value

                        FROM
                            `srp_erp_paysupplierinvoicemaster`
                        LEFT JOIN (
                            SELECT
                                SUM(transactionAmount) AS transactionAmount,
                                InvoiceAutoID
                            FROM
                                srp_erp_paysupplierinvoicedetail
                            GROUP BY
                                InvoiceAutoID
                        ) det ON (
                            `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                        )
                        LEFT JOIN (
                            SELECT
                                SUM(taxPercentage) AS taxPercentage,
                                InvoiceAutoID
                            FROM
                                srp_erp_paysupplierinvoicetaxdetails
                            GROUP BY
                                InvoiceAutoID
                        ) addondet ON (
                            `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                        )
                        WHERE
                            `companyID` = $companyID
                            AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                    $totalValue = $this->db->query($r1)->row_array();

                   
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );

                        if($totalValue['companyLocalExchangeRate'] > 0) {
                            $data['companyLocalAmount'] = round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces']);
                            $data['companyReportingAmount'] = (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces']));
                            $data['supplierCurrencyAmount'] = (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces']));
                        }else{
                            $data['companyLocalAmount'] = 0;
                            $data['companyReportingAmount'] = 0;
                            $data['supplierCurrencyAmount'] = 0;
                        }
                        
                    $data['transactionAmount'] = (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces']));
                    $data['generalDiscountAmount'] = ($totalValue['discountAmnt']);
                   
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                    if (!empty($t_arr)) {
                        $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                    }
                } else if ($approvals_status == 3) {
                    $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
                    return array('status' => true);
                } else {
                    $this->session->set_flashdata('e', 'Confirmation failed.');
                    return array('status' => false);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Confirmed failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $autoApproval = get_document_auto_approval('BSI');
                    if ($autoApproval == 0) {
                        $result = $this->save_supplier_invoice_approval(0, $master_data['InvoiceAutoID'], 1, 'Auto Approved');
                        if ($result) {
                            $this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');
                            $this->db->trans_commit();
                            return array('status' => true);
                        }
                    } else {
                        $this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true);
                    }
                }
            }
        }
    }


    function save_supplier_invoice_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $companyID = current_companyID();
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('InvoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['InvoiceAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'BSI');
        }

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*,0 as taxLedgerAmount');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $item_detail = $this->db->get()->result_array();

            $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster',$system_id,'BSI','InvoiceAutoID');
            if($isGroupByTax == 1){
                $item_detail = $this->db->query("SELECT
                srp_erp_paysupplierinvoicedetail.*,
                IFNULL( taxAmount, 0 ) AS taxAmount,
                IFNULL( bsiitemtaxamount.taxLedgerAmount, 0 ) AS taxLedgerAmount 
            FROM
                `srp_erp_paysupplierinvoicedetail`
                LEFT JOIN (
                SELECT
                    sum( amount ) AS taxLedgerAmount,
                    srp_erp_paysupplierinvoicedetail.itemAutoID 
                FROM
                    srp_erp_taxledger
                    LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                    LEFT JOIN srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
                WHERE
                    srp_erp_taxledger.documentID = 'BSI' 
                    AND (srp_erp_taxledger.isClaimable = 0) 
                    AND documentMasterAutoID = $system_id
                GROUP BY
                    srp_erp_paysupplierinvoicedetail.itemAutoID 
                ) bsiitemtaxamount ON bsiitemtaxamount.ItemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID 
            WHERE
                `InvoiceAutoID` = $system_id 
                AND `companyID` = $companyID")->result_array();
                        }


                $this->db->select('sum(transactionAmount) as transactionAmount');
            $this->db->where('InvoiceAutoID', $system_id);
            $totalsum = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

            $disciunt = ($master['generalDiscountPercentage'] / 100) * $totalsum['transactionAmount'];

            //echo 'totsum = '.$totalsum['transactionAmount'] .'<br>'.'discamount = '.$disciunt;
            if ($master['documentOrigin'] != 'CINV') {
                for ($a = 0; $a < count($item_detail); $a++) {
     
                    if ($item_detail[$a]['type'] == 'Item' || $item_detail[$a]['type'] == 'PO') {
                        $item = fetch_item_data($item_detail[$a]['itemAutoID']);
                        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
                        $this->db->select('GLAutoID');
                        $this->db->where('controlAccountType', 'ACA');
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                        if ($disciunt > 0) {
                            $amountaftrdisc = ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                        } else {
                            $amountaftrdisc = 0;
                        }
                        $company_loc = (($item_detail[$a]['transactionAmount'] - $amountaftrdisc) / $master['companyLocalExchangeRate']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                            $itemAutoID = $item_detail[$a]['itemAutoID'];
                            $qty = $item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID'];
                            $wareHouseAutoID = $item_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];

                            $itemledgerCurrentStock = fetch_itemledger_currentstock($item_detail[$a]['itemAutoID']);
                            $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'], 'companyLocalExchangeRate');
                            $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'], 'companyReportingExchangeRate');


                            // $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                            //  $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $company_loc) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            //  $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + (($item_detail[$a]['transactionAmount']-$amountaftrdisc) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);

                            $item_arr[$a]['currentStock'] = ($itemledgerCurrentStock + $qty);

                            $item_arr[$a]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + ($company_loc + $item_detail[$a]['taxLedgerAmount'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + ((($item_detail[$a]['transactionAmount'] - $amountaftrdisc)+$item_detail[$a]['taxLedgerAmount']) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);


                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($item_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }

                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['InvoiceAutoID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['bookingInvCode'];
                            $itemledger_arr[$a]['documentDate'] = $master['bookingDate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['RefNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $item_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $item_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $item_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $item_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $item_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $item_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['SUOMID'] = $item_detail[$a]['SUOMID'];
                            $itemledger_arr[$a]['SUOMQty'] = $item_detail[$a]['SUOMQty'];
                            $itemledger_arr[$a]['defaultUOMID'] = $item_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $item_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOM'] = $item_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $item_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $item_detail[$a]['requestedQty'];
                            $itemledger_arr[$a]['convertionRate'] = $item_detail[$a]['conversionRateUOMID'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $amountaftrdisc = $item_detail[$a]['transactionAmount'] - ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                            if ($disciunt > 0) {
                                $itemledger_arr[$a]['transactionAmount'] = $amountaftrdisc+ $item_detail[$a]['taxLedgerAmount'];
                            } else {
                                $itemledger_arr[$a]['transactionAmount'] = $item_detail[$a]['transactionAmount']+ $item_detail[$a]['taxLedgerAmount'];
                            }
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = ($item_arr[$a]['companyLocalWacAmount']);
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['supplierCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['supplierCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                            $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                            $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                            $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                            $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                            $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];

                        } elseif ($item['mainCategory'] == 'Fixed Assets') {
                            $this->load->library('sequence');
                            $assat_data = array();
                            if ($disciunt > 0) {
                                $assetdiscamnt = ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                            } else {
                                $assetdiscamnt = 0;
                            }
                            $assat_amount = (($item_detail[$a]['transactionAmount'] - $assetdiscamnt) / $item_detail[$a]['conversionRateUOMID']);
                            for ($b = 0; $b < ($item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID']); $b++) {
                                $assat_data[$b]['documentID'] = 'FA';
                                $assat_data[$b]['docOriginSystemCode'] = $master['InvoiceAutoID'];
                                $assat_data[$b]['docOriginDetailID'] = $item_detail[$a]['InvoiceDetailAutoID'];
                                $assat_data[$b]['docOrigin'] = 'BSI';
                                $assat_data[$b]['dateAQ'] = $master['bookingDate'];
                                $assat_data[$b]['grvAutoID'] = $master['InvoiceAutoID'];
                                $assat_data[$b]['isFromGRV'] = 1;
                                $assat_data[$b]['assetDescription'] = $item['itemDescription'];
                                $assat_data[$b]['comments'] = trim($this->input->post('comments') ?? '');
                                $assat_data[$b]['faCatID'] = $item['subcategoryID'];
                                $assat_data[$b]['faSubCatID'] = $item['subSubCategoryID'];
                                $assat_data[$b]['assetType'] = 1;
                                $assat_data[$b]['transactionAmount'] = $assat_amount;
                                $assat_data[$b]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                $assat_data[$b]['transactionCurrency'] = $master['transactionCurrency'];
                                $assat_data[$b]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                                $assat_data[$b]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                $assat_data[$b]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                $assat_data[$b]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                $assat_data[$b]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $assat_data[$b]['companyLocalAmount'] = round($assat_amount, $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $assat_data[$b]['companyReportingAmount'] = round($assat_amount, $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                $assat_data[$b]['supplierID'] = $master['supplierID'];
                                $assat_data[$b]['segmentID'] = $master['segmentID'];
                                $assat_data[$b]['segmentCode'] = $master['segmentCode'];
                                $assat_data[$b]['companyID'] = $master['companyID'];
                                $assat_data[$b]['companyCode'] = $master['companyCode'];
                                $assat_data[$b]['createdUserGroup'] = $master['createdUserGroup'];
                                $assat_data[$b]['createdPCID'] = $master['createdPCID'];
                                $assat_data[$b]['createdUserID'] = $master['createdUserID'];
                                $assat_data[$b]['createdDateTime'] = $master['createdDateTime'];
                                $assat_data[$b]['createdUserName'] = $master['createdUserName'];
                                $assat_data[$b]['modifiedPCID'] = $master['modifiedPCID'];
                                $assat_data[$b]['modifiedUserID'] = $master['modifiedUserID'];
                                $assat_data[$b]['modifiedDateTime'] = $master['modifiedDateTime'];
                                $assat_data[$b]['modifiedUserName'] = $master['modifiedUserName'];
                                $assat_data[$b]['costGLAutoID'] = $item['faCostGLAutoID'];
                                $assat_data[$b]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                                $assat_data[$b]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                                $assat_data[$b]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                                $assat_data[$b]['isPostToGL'] = 1;
                                $assat_data[$b]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                                $assat_data[$b]['postGLCode'] = $ACA['systemAccountCode'];
                                $assat_data[$b]['postGLCodeDes'] = $ACA['GLDescription'];
                                $assat_data[$b]['faCode'] = $this->sequence->sequence_generator("FA");
                            }
                            if (!empty($assat_data)) {
                                $assat_data = array_values($assat_data);
                                $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                            }
                        }
                    }
                }

                if (!empty($itemledger_arr)) {
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }
            }
                        
                        
            $this->load->model('Double_entry_model');
            if ($master['documentOrigin'] == 'CINV') {
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_insurance_data($system_id, 'BSI');
            } else {
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($system_id, 'BSI');
            }


            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['InvoiceAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['bookingInvCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['invoiceType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['bookingDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                  //  $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    if($double_entry['gl_detail'][$i]['gl_cr'] < 0){
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr']);
                    }else{
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                }

                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                
                if( $generalledger_arr[$i]['companyLocalExchangeRate'] == 0){
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                }else{
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                }

                if($generalledger_arr[$i]['companyReportingExchangeRate'] == 0){
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                }else{
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);

                }

                if($generalledger_arr[$i]['partyExchangeRate'] == 0){
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                }else{
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                }
               
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }


            $maxLevel = $this->approvals->maxlevel('BSI');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $this->input->post('InvoiceAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'PV'));

                }
            }

            $itemAutoIDarry = array();
            $wareHouseAutoIDDarry = array();
            foreach ($item_detail as $value) {
                if ($value['itemAutoID']) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                if ($value['wareHouseAutoID']) {
                    array_push($wareHouseAutoIDDarry, $value['wareHouseAutoID']);
                }
            }

            $company_id = current_companyID();
            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
                        JOIN (
                            SELECT
                                purchaseOrderID AS pid,
                                (
                                    CASE
                                    WHEN balance = 0 THEN
                                        '2'
                                    WHEN balance = requestedtqy THEN
                                        '0'
                                    ELSE
                                        '1'
                                    END
                                ) AS sts
                            FROM
                                (
                                    SELECT
                            t2.purchaseOrderID,
                        sum(requestedtqy) as requestedtqy ,
                            sum(balance) AS balance
                        FROM
                            (
                        SELECT
                                    po.purchaseOrderDetailsID,
                                    purchaseOrderID,
                                    po.itemAutoID,
                                    ifnull((po.requestedQty),0) AS requestedtqy,
                                    (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty,
                                IF (
                                    (
                                        (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                    ) < 0,
                                    0,
                                    (
                                        (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                    )
                                ) AS balance
                                FROM
                                    srp_erp_purchaseorderdetails po
                                LEFT JOIN (
                                    SELECT
                                        purchaseOrderMastertID,
                                        ifnull(sum(requestedQty),0) AS receivedQty,
                                        itemAutoID,
                                        purchaseOrderDetailsID
                                    FROM
                                        srp_erp_paysupplierinvoicedetail
                                left join srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                                        where sinm.approvedYN=1
                                    GROUP BY
                                    srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

                                        LEFT JOIN (
                                    SELECT
                                        purchaseOrderMastertID,
                                        ifnull(sum(receivedQty),0) AS receivedQty,
                                        itemAutoID,
                                        purchaseOrderDetailsID
                                    FROM
                                        srp_erp_grvdetails
                                left join srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                                        where grvm.grvType='PO Base' and grvm.approvedYN=1
                                    GROUP BY
                                    srp_erp_grvdetails.purchaseOrderDetailsID
                                ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID

                            ) t2 group by t2.purchaseOrderID
                                ) z
                        ) tt ON prd.purchaseOrderID = tt.pid
                        SET prd.isReceived = tt.sts
                        where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");


            if ($itemAutoIDarry && $wareHouseAutoIDDarry) {
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $wareHouseAutoIDDarry) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['bookingDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['InvoiceAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['bookingInvCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($item_detail as $itemid) {
                    if ($itemid['type'] == 'Item') {
                        $receivedQty = $itemid['requestedQty'];
                        $receivedQtyConverted = $itemid['requestedQty'] / $itemid['conversionRateUOMID'];
                        $companyID = current_companyID();
                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $itemid['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                        $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                        $sumqty = array_column($exceededitems, 'balanceQty');
                        $sumqty = array_sum($sumqty);
                        if (!empty($exceededitems)) {
                            foreach ($exceededitems as $exceededItemAutoID) {
                                if ($receivedQtyConverted > 0) {
                                    $balanceQty = $exceededItemAutoID['balanceQty'];
                                    $updatedQty = $exceededItemAutoID['updatedQty'];
                                    $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                    $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;
                                        //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                        $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetail['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                        $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                        $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                        $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                        $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                        $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                        $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                    } else {
                                        $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                        $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetails['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                        $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                        $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                        $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                        $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                        $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                        $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                        $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                        $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                        $receivedQty = $receivedQty - $exeed['updatedQty'];
                                        $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                    }
                                }
                            }
                        }
                    }

                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
            }


            $this->db->select('sum(srp_erp_paysupplierinvoicedetail.transactionAmount + IFNULL(srp_erp_paysupplierinvoicedetail.taxAmount, 0)) AS transactionAmount ,srp_erp_paysupplierinvoicedetail.companyLocalExchangeRate ,srp_erp_paysupplierinvoicedetail.companyReportingExchangeRate, srp_erp_paysupplierinvoicedetail.supplierCurrencyExchangeRate');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceAutoID', $system_id);
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $transactionAmount = $this->db->get()->row_array();

            if($transactionAmount['companyLocalExchangeRate'] == 0){
                $company_loc = ($transactionAmount['transactionAmount']);
            }else{
                $company_loc = ($transactionAmount['transactionAmount'] / $transactionAmount['companyLocalExchangeRate']);
            }

            if($transactionAmount['companyLocalExchangeRate'] == 0){
                $company_rpt = ($transactionAmount['transactionAmount']);
            }else{
                $company_rpt = ($transactionAmount['transactionAmount'] / $transactionAmount['companyReportingExchangeRate']);
            }

            if($transactionAmount['companyLocalExchangeRate'] == 0){
                $supplier_cr = ($transactionAmount['transactionAmount']);
            }else{
                $supplier_cr = ($transactionAmount['transactionAmount'] / $transactionAmount['supplierCurrencyExchangeRate']);
            }
          
          
    

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];
            // $data['companyLocalAmount']     = $company_loc;
            // $data['companyReportingAmount'] = $company_rpt;
            // $data['supplierCurrencyAmount'] = $supplier_cr;
            // $data['transactionAmount']      = $transactionAmount['transactionAmount'];

            // $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            // $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            $this->session->set_flashdata('s', 'Supplier Invoices Approved Successfully.');
        }

        // else{
        //     $this->session->set_flashdata('s', 'Supplier Invoices Approval : Level '.$level_id.' Successfully.');
        // }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_data_mapping_posting(){

        $group = $this->input->post('group');
        $invoice_type = $this->input->post('invoice_type');
        $posting_type = $this->input->post('posting_type');
        $posting_status = $this->input->post('posting_status');
        $posting_method = $this->input->post('posting_method');
        $service_type = $this->input->post('service_type');
        $mode_collection = $this->input->post('mode_collection');
        $company_id = current_companyID();

        $ex_posting_active = $this->db->from('srp_erp_ecommerce_posting')
                    ->where('status',1)
                    ->where('service_type',$service_type)
                    ->where('mode_collection',$mode_collection)
                    ->where('company_id',$company_id)
                    ->get()
                    ->row_array();

        if($ex_posting_active && $posting_status == 1){
            $this->session->set_flashdata('e', '1 Posting active already');
            return array('status' => false);
        }else{

            $data['group'] = $group;
            $data['invoice_type'] = $invoice_type;
            $data['posting_type'] = $posting_type;
            $data['posting_method'] = $posting_method;
            $data['name'] = $group.' Service';
            $data['description'] = ($posting_type == 1) ? "$group. Service - One document posting " : "$group. Service - Daily one entry ";
            $data['company_id'] = $company_id;
            $data['status'] = $posting_status;
            $data['service_type'] = $service_type;
            $data['mode_collection'] = $mode_collection;
            $data['created_date'] = current_date();
            $data['created_by_id'] = current_userID();

            $response = $this->db->insert('srp_erp_ecommerce_posting',$data);

            $this->session->set_flashdata('s', 'Successfully created posting '.$this->db->insert_id());
            return array('status' => true, 'id'=> $this->db->insert_id());

        }

        // $this->session->set_flashdata('s', 'Successfully updated the GL Code');
        // return array('status' => true);

    }

    function change_posting_method(){

        $mapping_id = $this->input->post('mapping_id');
        $posting_method = $this->input->post('posting_method');

        $data = array();

        try {

            if($posting_method && $mapping_id){
                $data['posting_method'] = $posting_method;
    
                $this->db->where('id',$mapping_id);
                $this->db->update('srp_erp_ecommerce_posting',$data);

                $this->session->set_flashdata('s', 'Posting has been updated.');
                return array('status' => true);
            }

        }catch(Exception $e){
                $this->session->set_flashdata('e', 'Posting update is failed.');
                return array('status' => false);
        }
        
        return TRUE;
    }

    function change_posting_status(){
        
        $mapping_id = $this->input->post('mapping_id');
        $data = array();

        $ex_mapping_record = $this->db->where('id',$mapping_id)->from('srp_erp_ecommerce_posting')->get()->row_array();

        if($ex_mapping_record){
            $service_type = $ex_mapping_record['service_type'];
            $mode_collection = $ex_mapping_record['mode_collection'];
            $status = $ex_mapping_record['status'];

            $ex_active_posting = $this->db->where('status',1)
                                    ->from('srp_erp_ecommerce_posting')
                                    ->where('service_type',$service_type)
                                    ->where('mode_collection',$mode_collection)
                                    ->get()
                                    ->row_array();
        }
        

        if($ex_active_posting && $ex_active_posting['id'] != $mapping_id && $status == 2){
            $this->session->set_flashdata('e', 'There is a posting active already, Make it inactive to proceed.');
            return array('status' => false);
        }else{

            if($ex_mapping_record){
                $data['status'] = ($ex_mapping_record['status'] == 2) ? 1 : 2 ;

                $this->db->where('id',$mapping_id)->update('srp_erp_ecommerce_posting',$data);

                $this->session->set_flashdata('s', 'Successfully updated the record.');
                return array('status' => true);

            }else {
                $this->session->set_flashdata('e', 'No Mapping record is available to proceed.');
                return array('status' => false);
            }

        }

    }

    function delete_posting_status(){

        $mapping_id = $this->input->post('mapping_id');
        $data = array();

        $ex_mapping_record = $this->db->where('id',$mapping_id)->from('srp_erp_ecommerce_posting')->get()->row_array();

        if($ex_mapping_record && $ex_mapping_record['status'] == 1){
            $this->session->set_flashdata('e', 'You can not delete the active posting.');
            return array('status' => false);
        }else{

            $this->db->delete('srp_erp_ecommerce_posting', array('id' => $mapping_id)); 

            $this->session->set_flashdata('s', 'Successfully deleted the posting.');
            return array('status' => true);

        }

    }

    function delete_manual_posting_record(){
        $id = $this->input->post('id');
        $data = array();

        $ex_mapping_record = $this->db->where('id',$id)->from('srp_erp_ecommerce_system_posting')->get()->row_array();

        if($ex_mapping_record){

            $res = $this->db->delete('srp_erp_ecommerce_system_posting', array('id' => $id)); 

            $this->session->set_flashdata('s', 'Successfully deleted the posting setting.');
            return array('status' => true);

        }
    }

    //////////////////////////////////Debit Note/////////////////////////////////////////////////

    function save_debit_note_invoice_header($order_auto_id,$client_sales_data,$client_mapping,$vendor_type = null){

        if(empty($client_sales_data)){
            return false;
        }

        $bokngDt = $client_sales_data['date_time'];
        $invduedt = $client_sales_data['completed_time'];
        $invdt = $client_sales_data['date_time'];
        $invoiceType = 'StandardExpense';
        $financearray_rec = get_financial_period_date_wise($invduedt);
        $financearray = $financearray_rec['companyFinancePeriodID'];

        $supplier_details = $this->get_supplier_details(trim($client_sales_data['store_id'] ?? ''));
       
        $segment_id = 86;
        $referenceno = $client_sales_data['order'];


        if(empty($supplier_details)){
            return false;
        }

        $date_format_policy = date_format_policy();
        $bookingDate = input_format_date($bokngDt, $date_format_policy);

        $supplierInvoiceDueDate = input_format_date($invduedt, $date_format_policy);
        $invoiceDate = input_format_date($invdt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');


        if($financeyearperiodYN==1) {
           
            $financePeriod = fetchFinancePeriod($financearray);

            if ($bookingDate >= $financePeriod['dateFrom'] && $bookingDate <= $financePeriod['dateTo']) {
                if (($invoiceDate) > ($supplierInvoiceDueDate)) {
                   return json_encode(array('e'=>"error","message"=>"Invoice Due Date cannot be lesser than invoice Date!"));
                } else {
                    return json_encode($this->save_debitnote_header($bokngDt,$invduedt,$supplier_details,$invoiceType,$segment_id,$referenceno,$client_sales_data));
                }
            } else {
                return json_encode(array('e'=>"error","message"=>"Invoice Date not between Financial Period !"));
            }
        }else{
            return json_encode($this->save_debitnote_header($bokngDt,$invduedt,$supplier_details,$invoiceType,$segment_id,$referenceno,$client_sales_data));
        }

       
    }

    /* Save debit note header - data for master */

    function save_debitnote_header($bookDate,$supplirInvDuDate,$supplier_details,$invoiceType,$segment_id,$referenceno,$client_sales_data)
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $debitnoteDate = $bookDate;
        $dnDate = input_format_date($debitnoteDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $currency_code_str = 'OMR | Omani Rial';
        $supllier_detail_str = $supplier_details['supplierSystemCode'].' | '.$supplier_details['supplierName'].' | '.$supplier_details['supplierCountry'];
        $currency_code = explode(' | ', trim($currency_code_str));
        $supplierdetails = explode(' | ', trim($supllier_detail_str));
        $financearray_rec = get_financial_period_date_wise($bookDate);
        
        $period = $financearray_rec['companyFinancePeriodID'];
        $financeYear = $financearray_rec['companyFinanceYearID'];
        $financeYearDetails = get_financial_year($bookDate);
        $financeyr_arr = $financeYearDetails['beginingDate'].' - '.$financeYearDetails['endingDate'];
        // $currencyID = get_currency_id($currency_code[0]);
        //$companyFinanceYear = $financearray_rec['dateFrom'].' - '.$financearray_rec['dateTo']
       

        
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($financeyr_arr));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        } else {
          
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
            $financePeriodDetails = get_financial_period_date_wise($dnDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
       

        $supplier_arr = $supplier_details;
        $data['documentID'] = 'DN';
        $data['debitNoteDate'] = trim($dnDate);
        $data['companyFinanceYearID'] = trim($financeYear);
        $data['companyFinanceYear'] = trim($financeyr_arr);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($period);
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['supplierID'] = trim($supplier_arr['supplierAutoID'] ?? '');
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['docRefNo'] = trim($referenceno);
        $data['isGroupBasedTax'] =getPolicyValues('GBT', 'All');
        $comments = $client_sales_data['order'];

        $data['comments'] = str_replace('<br />', PHP_EOL, $comments);
        $data['transactionCurrencyID'] = trim($supplier_arr['supplierCurrencyID'] ?? '');
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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($this->input->post('debitNoteMasterAutoID') ?? '')) {
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
            $this->db->update('srp_erp_debitnotemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('debitNoteMasterAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['debitNoteCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_debitnotemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_debitNote_detail_GLCode_multiple($invoiceAutoID,$client_sales_data,$client_mapping,$selected_gl_record)
    {

        $this->db->trans_start();
        $projectExist = project_is_exist();
        $this->db->select('*');
        $this->db->where('debitNoteMasterAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();

        

        $gl_codes = $selected_gl_record;
        $gl_code_des = $this->input->post('gl_code_des');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $tax_type = $this->input->post('tax_type');
        $group_based_tax = 1;


        foreach ($gl_codes as $key => $gl_code) {
            
            $segment = explode('|', $gl_code['segement']);
            // $gl_code = explode('|', $gl_code_des[$key]);
            $g_code_details = fetch_gl_account_from_systemAccountCode($gl_code['gl_account_code'],$this->common_data['company_data']['company_id']);
            $description = $gl_code['descripiton'];

            $data['debitNoteMasterAutoID'] = trim($invoiceAutoID);
            $data['GLAutoID'] = $gl_code['gl_auto_id'];
           
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }
            

            $data['systemGLCode'] = trim($gl_code['gl_account_code'] ?? '');
            $data['GLCode'] = trim($g_code_details['GLSecondaryCode'] ?? '');
            $data['GLDescription'] = trim($gl_code['gl_account_description'] ?? '');
            $data['GLType'] = trim($g_code_details['subCategory'] ?? '');

           
            $data['segmentID'] = trim($gl_code['segment_id'] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['description'] = $description;
        
            $amount = $gl_code['final_value'];

            $data['transactionAmount'] = round($amount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['isFromInvoice'] = 0;       
            
            $this->db->insert('srp_erp_debitnotedetail', $data);
            $last_id = $this->db->insert_id();

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Debit Note Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Debit Note Detail : Saved Successfully.');
        }

    }

    function dn_confirmation($invoiceAutoID,$client_sales_data)
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $this->db->select('debitNoteMasterAutoID');
        $this->db->where('debitNoteMasterAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_debitnotedetail');
        $results = $this->db->get()->row_array();

        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('debitNoteMasterAutoID');
            $this->db->where('debitNoteMasterAutoID', $invoiceAutoID);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_debitnotemaster');
            $Confirmed = $this->db->get()->row_array();

            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $system_id = $invoiceAutoID;
                $this->db->select('documentID, debitNoteCode,DATE_FORMAT(debitNoteDate, "%Y") as invYear,DATE_FORMAT(debitNoteDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('debitNoteMasterAutoID', $system_id);
                $this->db->from('srp_erp_debitnotemaster');
                $master_dt = $this->db->get()->row_array();
              
                $this->load->library('sequence');
                
                if ($master_dt['debitNoteCode'] == "0") {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();

                        if ((empty($location)) || ($location == '')) {
                            return array('w', 'Location is not assigned for current employee');
                        } else {
                            if ($emplocationid != '') {
                                $pvCd = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $emplocationid, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('w', 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $pvCd = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                   
                    $validate_code = validate_code_duplication($pvCd, 'debitNoteCode', $system_id, 'debitNoteMasterAutoID', 'srp_erp_debitnotemaster');
                    if (!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $pvCd = array(
                        'debitNoteCode' => $pvCd
                    );
                    
                    $this->db->where('debitNoteMasterAutoID', $system_id);
                    $this->db->update('srp_erp_debitnotemaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['debitNoteCode'], 'debitNoteCode', $system_id, 'debitNoteMasterAutoID', 'srp_erp_debitnotemaster');
                    if (!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }
                $this->load->library('Approvals');
                $this->db->select('debitNoteMasterAutoID, debitNoteCode,debitNoteDate');
                $this->db->where('debitNoteMasterAutoID', $system_id);
                $this->db->from('srp_erp_debitnotemaster');
                $grv_data = $this->db->get()->row_array();

            
                $autoApproval = 0; //get_document_auto_approval('DN'); //Always auto approved for system generated docs
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($grv_data['debitNoteMasterAutoID'], 'srp_erp_debitnotemaster', 'debitNoteMasterAutoID', 'DN', $grv_data['debitNoteCode'], $grv_data['debitNoteDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('DN', $grv_data['debitNoteMasterAutoID'], $grv_data['debitNoteCode'], 'Debit note', 'srp_erp_debitnotemaster', 'debitNoteMasterAutoID', 0, $grv_data['debitNoteDate']);
                } else {
                    return array('e', 'Approval levels are not set for this document');
                }                
                
                if ($approvals_status == 1) {
                    $autoApproval = 0;//get_document_auto_approval('DN');
                    if ($autoApproval == 0) {
                        $result = $this->save_dn_approval(0, $system_id, 1, 'Auto Approved');
                        if ($result) {
                            return array('s', 'Document confirmed Successfully');
                        }
                    } else {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('debitNoteMasterAutoID', $system_id);
                        $result = $this->db->update('srp_erp_debitnotemaster', $data);
                        if ($result) {
                            return array('s', 'Document confirmed Successfully');
                        }
                    }
                } else if ($approvals_status == 3) {
                    return array('w', 'There are no users exist to perform approval for this document.');
                } else {
                    return array('e', 'Document confirmation failed');
                }
            }
        }
    }

    function save_dn_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('debitNoteMasterAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['debitNoteMasterAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'DN');
        }
        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_debit_note_data($system_id, 'DN');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                // To get actual amount from debit note detail table
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'DN');
                $this->db->where('documentMasterAutoID', $system_id);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    //echo 'xx<hr/>';
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    //print_r($ERGL);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentType'] = '';
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['comments'];
                    $generalledger_arr['chequeNumber'] = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'SUP';
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['supplierID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['supplierName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID;
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|', $this->common_data['company_data']['default_segment']);
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                    //print_r($generalledger_arr);
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }
            $this->session->set_flashdata('s', 'Debit Note Approval Successfully.');
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }



     //////////////////////////////// Customer Invoice ////////////////////////////////
    function set_general_ledger_customer($sales_id){
        
        $mapping_records = $this->get_sales_client_credit_debit_summary($sales_id);
        $base_ledger_arr = array();

        if($mapping_records){

            $response = $this->save_customer_invoice_set($sales_id);
          
        }else{

            $response = array();
        }

        return $response;
    }

    function save_customer_invoice_set($sales_id){

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,2);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];

        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

         // send back nothing generate
        if(empty($selected_gl_record)){
            add_process_log_record(0,'CINV',$sales_id,2,'No data to generate Customer Invoice',2);
            return array('status'=>'error', 'message' => 'No data to generate Customer Invoice');
        }
        
        try{

            $response_header = $this->save_customer_invoice_header($sales_id,$client_sales_data,$client_mapping);
            // $response_header = array('last_id'=>'2390');
            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_direct_invoice_detail($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);

                $response_confirmation = $this->invoice_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'customer');

                add_process_log_record($response['last_id'],'CINV',$sales_id,1,'Customer Invoice Created',2);

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Customer invoice successfully created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
      

    }


    function save_customer_invoice_header($order_auto_id,$client_sales_data,$client_mapping,$doc_type = null)
    {
        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $date_format_policy = date_format_policy();
        $invDueDate = $client_sales_data['completed_time'];
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $client_sales_data['date_time'];
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $docDate = $client_sales_data['date_time'];
        $documentDate = input_format_date($docDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $referenceno = $client_sales_data['order'];

        if($doc_type == '3PL_customer'){
            $invoiceType = 'DirectIncome';
            $customer_details = $this->get_customer_details(trim($client_sales_data['3pl_company_id'] ?? ''));
        }else{
            $invoiceType = 'DirectIncome';
            $customer_details = $this->get_customer_details(trim($client_sales_data['store_id'] ?? ''));
        }
       
        $financearray_rec = get_financial_period_date_wise($invoiceDate);

        if(empty($financearray_rec)){
            $this->session->set_flashdata('e', 'Customer Invoice Fincance period not exists !');
            echo json_encode(FALSE);
            exit;
        }

        $financearray = $financearray_rec['companyFinancePeriodID'];
        $segment_id = 86;
       
 
        if (($invoiceDate) > ($invoiceDueDate)) {
            $this->session->set_flashdata('e', ' Invoice Due Date cannot be less than Invoice Date!');
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
              
                $financePeriod = fetchFinancePeriod($financearray);

                if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                    return json_encode($this->save_invoice_header($invoiceDate,$invoiceDueDate,$customer_details,$invoiceType,$segment_id,$referenceno));
                } else {
                    $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                    return json_encode(FALSE);
                }

            }else{
              
                return json_encode($this->save_invoice_header($invoiceDate,$invoiceDueDate,$customer_details,$invoiceType,$segment_id,$referenceno));
            }
        }

        
    }


    function save_invoice_header($invDate,$invDueDate,$supplier_details,$invoicetype,$segment_id,$referenceno)
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $customerInvoiceDate = $ackDate = $sullyD = input_format_date($invDate, $date_format_policy);
        $acknowledgeDate = input_format_date($ackDate, $date_format_policy);
        $supplyDate = input_format_date($sullyD, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $customerID = $supplier_details['customerAutoID'];
        $projectID =  $this->input->post('projectID');
        $financearray_rec = get_financial_period_date_wise($invDate);
        $financearray = $financearray_rec['companyFinancePeriodID'];
        $financePeriod = fetchFinancePeriod($financearray);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        
        $financeyear = $financearray_rec['companyFinanceYearID'];
        $company_finance_year = company_finance_year($financeyear);

        $companyFinanceYear = $company_finance_year['startdate'].' - '.$company_finance_year['endingdate'];
        $segmentId = '86|GEN';
        $currency_code = 'OMR|Omani Rial';
        $transaction_currency_id = 1;

        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
    
        
        if(($invoicetype =='Project')&&(!empty($invoiceAutoID))&&($projectID!=$projectID_detail['projectID']&&$projectID_detail['projectID']!=''))
        {

            if((!empty($detailexist)&&($detailexist!='')))
            {
                $this->session->set_flashdata('e', 'Please delete all the records and change the project');
                return array('status' => false);
                exit;
            }
        }

        $rebate = getPolicyValues('CRP', 'All');

      
        
        if($rebate == 1) {
            $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$customerID}")->row_array();
            if(!empty($rebate)) {
                $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
            }
        } else {
            $data['rebateGLAutoID'] = null;
            $data['rebatePercentage'] = null;
        }

      

        if($financeyearperiodYN==1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

            $FYBegin = input_format_date($financePeriod['dateFrom'], $date_format_policy);
            $FYEnd = input_format_date($financePeriod['dateTo'], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($invoiceDate);
            if(empty($financeYearDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                $FYBegin=$financeYearDetails['beginingDate'];
                $FYEnd=$financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails=get_financial_period_date_wise($invoiceDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $segment = explode('|', trim($segmentId));
        $customer_arr = $this->fetch_customer_data(trim($customerID));

        $RVbankCode = $ecommerce_settings['company_bank_id'];

     
        $customerName = $customer_arr['customerName'];
        $customerTelephone = $customer_arr['customerTelephone'];

        //$location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $currency_code = explode('|', trim($currency_code));
        if ($RVbankCode) {
            $bank_detail = fetch_gl_account_desc(trim($RVbankCode));
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['invoicebank'] = $bank_detail['bankName'];
            $data['invoicebankBranch'] = $bank_detail['bankBranch'];
            $data['invoicebankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['invoicebankAccount'] = $bank_detail['bankAccountNumber'];
            $data['invoicebankType'] = $bank_detail['subCategory'];
        }
        $data['documentID'] = 'CINV';
        $data['companyFinanceYearID'] = trim($financeyear);
        $data['companyFinanceYear'] = trim($companyFinanceYear);
        $data['contactPersonName'] = trim($customerName);
        $data['contactPersonNumber'] = trim($customerTelephone);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = $financearray;
        $data['projectID'] = $projectID;
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['invoiceDate'] = trim($invoiceDate);
        $data['customerInvoiceDate'] = trim($customerInvoiceDate);
        $data['invoiceDueDate'] = trim($invoiceDueDate);

        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
            $data['acknowledgementDate'] = trim($acknowledgeDate);
        } else {
            $data['acknowledgementDate'] = trim($invoiceDate);
        }

        $isGroupBasedTax = getPolicyValues('GBT', 'All');
        if(!empty($isGroupBasedTax) && $isGroupBasedTax == 1) {
            $data['supplyDate'] = trim($supplyDate);
        } else {
            $data['supplyDate'] = trim($invoiceDate);
        }

        $invoiceNarration = $referenceno;
        $data['invoiceNarration'] = str_replace('<br />', PHP_EOL, $invoiceNarration);
        //$data['invoiceNarration'] = trim_desc($this->input->post('invoiceNarration'));

        $crTypes = explode('<table', $this->input->post('invoiceNote'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['invoiceNote'] = $notes;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }
        // $data['wareHouseCode'] = trim($location[0] ?? '');
        // $data['wareHouseLocation'] = trim($location[1] ?? '');
        // $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['invoiceType'] = trim($invoicetype);
        if($this->input->post('invoiceType')=='Operation'){
            $data['isOpYN'] =1;
        }

        $data['seNumber'] = trim($this->input->post('se_number') ?? '');
        $data['referenceNo'] = trim($referenceno);
        $data['isPrintDN'] = 1;
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = trim($transaction_currency_id);
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];



        if (trim($this->input->post('invoiceAutoID') ?? '')) {
            
        } else {
            $data['isDOItemWisePolicy']=0;
            if($invoicetype =='DeliveryOrder') {
                $DOItemWiseYN = getPolicyValues('DOIW', 'All');
                if($DOItemWiseYN && $DOItemWiseYN == 1) {
                    $data['isDOItemWisePolicy'] = 1;
                }
            }
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            $this->load->library('sequence');
           
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['invoiceCode'] = 0;
            $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');

            $this->db->insert('srp_erp_customerinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
             
                $this->session->set_flashdata('s', 'Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_direct_invoice_detail($invoiceAutoID,$client_sales_data,$client_mapping,$selected_gl_record)
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($invoiceAutoID),'CINV','invoiceAutoID');
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $projectExist = project_is_exist();

        foreach($selected_gl_record as $gl_record){

            $gl_text_type = $gl_record['gl_account_description'];
            $segment_gls = $gl_record['segement'];
            $gl_code_des = $gl_record['gl_code'];
            $segement_id = $gl_record['segment_id'];
            $gl_code = $gl_record['gl_account_code'];
            $projectID = $this->input->post('projectID');
            $amount = $gl_record['final_value'];
            $description = $gl_record['descripiton'];
            $discountPercentage = $this->input->post('discountPercentage');
            $project_categoryID = $this->input->post('project_categoryID');
            $project_subCategoryID = $this->input->post('project_subCategoryID');
    
            // if($gl_record['entry'] == 'cr'){
            //     $amount = -1 * abs($gl_record['final_value']);
            // }
        
            //foreach ($segment_gls as $key => $segment_gl) {

            if($gl_code) {

                $g_code_details = fetch_gl_account_from_systemAccountCode($gl_code,$this->common_data['company_data']['company_id']);
                $gl_code_des =  trim($g_code_details['systemAccountCode'] ?? '') . ' | ' . trim($g_code_details['GLSecondaryCode'] ?? '') . ' | ' . trim($g_code_details['GLDescription'] ?? '') . ' | ' . trim($g_code_details['subCategory'] ?? '');
               

                $segment = explode('|', $segment_gls);
                $gl_code_de = explode('|', $gl_code_des);
                $data['invoiceAutoID'] = trim($invoiceAutoID);

                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID[$key]);
                    $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                    $data['project_categoryID'] = $project_categoryID[$key];
                    $data['project_subCategoryID'] = $project_subCategoryID[$key];
                }
           
             

                $data['revenueGLAutoID'] = $g_code_details['GLAutoID'];
                $data['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
                $data['revenueGLCode'] = trim($gl_code_de[1] ?? '');
                $data['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
                $data['revenueGLType'] = trim($gl_code_de[3] ?? '');
                $data['segmentID'] = trim($segement_id);
                $data['segmentCode'] = trim($segment[1] ?? '');
                $data['discountPercentage'] = trim($discountPercentage);
                $data['discountAmount'] = trim(($amount * $discountPercentage)/100);
                $data['transactionAmount'] = round($amount, 3);

                if(empty($master['companyLocalCurrencyDecimalPlaces'])){
                    $master['companyLocalCurrencyDecimalPlaces'] = 3;
                    $master['companyReportingCurrencyDecimalPlaces'] = 3;
                }

                $companyLocalAmount = $data['transactionAmount'];
                if( $master['companyLocalExchangeRate']){
                    $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
                }
                $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);

                $companyReportingAmount = $data['transactionAmount'];
                if($master['companyReportingExchangeRate']){
                    $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
                }
                $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                $customerAmount = $data['transactionAmount'];
                if($master['customerCurrencyExchangeRate']){
                    $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                }
               
                $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                $data['description'] = trim($description);
                $data['type'] = 'GL';    


             


                if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    /*$this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
                    $this->db->update('srp_erp_customerinvoicedetails', $data);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                    }*/
                } else {
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
    
                }
    
                $this->db->insert('srp_erp_customerinvoicedetails', $data);
                $last_id = $this->db->insert_id();


                if($isGroupByTax == 1){ 
                    if(!empty($gl_text_type[$key])){
                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$gl_text_type[$key]);
                        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
                
                        $this->db->trans_start();
                        $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
                        $this->db->where('invoiceAutoID', $invoiceAutoID);
                        $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
    
                        $dataTax['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                        $dataTax['taxFormulaMasterID'] = $gl_text_type[$key];
                        $dataTax['taxDescription'] = $master['Description'];
                        $dataTax['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
                        $dataTax['transactionCurrency'] = $inv_master['transactionCurrency'];
                        $dataTax['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
                        $dataTax['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
                        $dataTax['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
                        $dataTax['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
                        $dataTax['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
                        $dataTax['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
                        $dataTax['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
                        $dataTax['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

                    }             
                }
            }
    
        }
       
        // $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

        /** Added (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);

        } else {
            $this->session->set_flashdata('s', 'Invoice Detail Saved Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function invoice_confirmation($invoiceAutoID = null,$client_sales_data = null)
    {
        if(empty($invoiceAutoID)) {
            $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        }

        $this->db->trans_start();
        $total_amount = 0;
        $tax_total = 0;
        $t_arr = array();
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');

        $this->db->select('invoiceDetailsAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicedetails');
        $results = $this->db->get()->result_array();

        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_customerinvoicemaster');
            $Confirmed = $this->db->get()->row_array();


            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            }
            else {
                $customerInvoiceDetail = $this->db->query("SELECT
                GROUP_CONCAT( itemAutoID ) AS itemAutoID 
                FROM
                srp_erp_customerinvoicedetails 
                WHERE
                companyID = $companyID
                AND invoiceAutoID = $invoiceAutoID")->row('itemAutoID');
                if(!empty($customerInvoiceDetail)){ 
                        $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$customerInvoiceDetail");
                        if(!empty($wacTransactionAmountValidation)){ 
                            return array('e','Below items are with negative wac amount',$wacTransactionAmountValidation);
                            exit();
                        }
                } 

                $this->load->library('Approvals');
                $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceType');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth=strlen($master_dt['invoiceCode']);

                if($lenth == 1){
                    if($locationwisecodegenerate == 1)
                    {
                        $this->db->select('locationID');
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location ==' ')) {
                            return array('w' ,'Location is not assigned for current employee');
                        }else
                        {
                            if($locationemployee!='')
                            {
                                $codegerator = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'], $locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                            }else
                            {
                                return array('w' ,'Location is not assigned for current employee');
                            }
                        }
                    }
                    else
                    {
                        $codegerator = $this->sequence->sequence_generator_fin($master_dt['documentID'],$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegerator, 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $invcod = array(
                        'invoiceCode' => $codegerator,
                    );
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->update('srp_erp_customerinvoicemaster', $invcod);
                } else {
                    $validate_code = validate_code_duplication($master_dt['invoiceCode'], 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->db->select('invoiceAutoID, invoiceCode, documentID,transactionCurrency, transactionExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,customerCurrencyExchangeRate,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceDate ');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $autoApproval= 0; //get_document_auto_approval('CINV');
                
                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($master_data['invoiceAutoID'], 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$master_data['invoiceCode'],$master_data['invoiceDate']);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval($master_data['documentID'], $master_data['invoiceAutoID'], $master_data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID',0,$master_data['invoiceDate']);
                }else{
                    return array('e', 'Approval levels are not set for this document');
                    exit;
                }

                if ($approvals_status == 1) {
                    /** item Master Sub check */
                    $validate = $this->validate_itemMasterSub($invoiceAutoID);
                   
                    /** end of item master sub */
                    if ($validate) {
                        $this->db->select_sum('transactionAmount');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $transaction_total_amount = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                        $this->db->select_sum('totalAfterTax');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $item_tax = $this->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
                        $total_amount = ($transaction_total_amount - $item_tax);
                        $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate ,companyLocalExchangeRate ,taxPercentage');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $tax_arr = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
                        for ($x = 0; $x < count($tax_arr); $x++) {
                            $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $total_amount);
                            $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                            $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                            $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                            $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                            $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                            $tax_total = $t_arr[$x]['transactionAmount'];
                        }
                        /*updating transaction amount using the query used in the master data table */
                        $companyID=current_companyID();
                        $invautoid=$invoiceAutoID;
                        $r1 = "SELECT
                                        `srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
                                        `srp_erp_customerinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`customerCurrencyExchangeRate` AS `customerCurrencyExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`customerCurrencyDecimalPlaces` AS `customerCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,

                                        (
                                            IFNULL(addondet.taxPercentage, 0) / 100
                                        ) * (
                                            IFNULL(det.transactionAmount, 0) - IFNULL(det.detailtaxamount, 0) - (
                                                (
                                                    IFNULL(
                                                        gendiscount.discountPercentage,
                                                        0
                                                    ) / 100
                                                ) * IFNULL(det.transactionAmount, 0)
                                            ) + IFNULL(
                                                genexchargistax.transactionAmount,
                                                0
                                            )
                                        ) + IFNULL(det.transactionAmount, 0) - (
                                            (
                                                IFNULL(
                                                    gendiscount.discountPercentage,
                                                    0
                                                ) / 100
                                            ) * IFNULL(det.transactionAmount, 0)
                                        ) + IFNULL(
                                            genexcharg.transactionAmount,
                                            0
                                        ) AS total_value

                                    FROM
                                        `srp_erp_customerinvoicemaster`
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            sum(totalafterTax) AS detailtaxamount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicedetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) det ON (
                                        `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(taxPercentage) AS taxPercentage,
                                            InvoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicetaxdetails
                                        GROUP BY
                                            InvoiceAutoID
                                    ) addondet ON (
                                        `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(discountPercentage) AS discountPercentage,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicediscountdetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) gendiscount ON (
                                        `gendiscount`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoiceextrachargedetails
                                        WHERE
                                            isTaxApplicable = 1
                                        GROUP BY
                                            invoiceAutoID
                                    ) genexchargistax ON (
                                        `genexchargistax`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoiceextrachargedetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) genexcharg ON (
                                        `genexcharg`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    WHERE
                                        `companyID` = $companyID
                                    and srp_erp_customerinvoicemaster.invoiceAutoID= $invautoid ";
                        $totalValue = $this->db->query($r1)->row_array();

                        $retentionPercentage = 0;
                        $retensionTransactionAmount = '';
                        $retensionLocalAmount = '';
                        $retensionReportingAmount = '';
                        if($master_dt['invoiceType'] == 'Project')
                        {
                            $retentiondetail = $this->db->query("SELECT retensionPercentage, (SUM(srp_erp_customerinvoicedetails.transactionAmount) *(retensionPercentage/100))as Invoice_amount FROM
                                                                        `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_customerinvoicemaster invoicemater on srp_erp_customerinvoicedetails.invoiceAutoID = invoicemater.invoiceAutoID
                                                                        LEFT JOIN srp_erp_boq_header boqheader on boqheader.projectID = invoicemater.projectID where  invoicemater.invoiceAutoID = $invautoid  AND type ='Project'")->row_array();

                            $retentionPercentage =  $retentiondetail['retensionPercentage'];
                            $retensionTransactionAmount = (round($retentiondetail['Invoice_amount'],$totalValue['transactionCurrencyDecimalPlaces']));
                            $retensionLocalAmount =(round($retentiondetail['Invoice_amount'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces']));
                            $retensionReportingAmount = (round($retentiondetail['Invoice_amount'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces']));

                        }else if($master_dt['invoiceType'] == 'Operation')
                        {
                            $this->db->select('retentionPercentage,retensionTransactionAmount,retensionLocalAmount,retensionReportingAmount');
                            $this->db->where('invoiceAutoID', $invoiceAutoID);
                            $this->db->from('srp_erp_customerinvoicemaster');
                            $retntn_data = $this->db->get()->row_array();

                            if(!empty($retntn_data)){
                                $retentionPercentage =  $retntn_data['retentionPercentage'];
                                $retensionTransactionAmount = $retntn_data['retensionTransactionAmount'];
                                $retensionLocalAmount =$retntn_data['retensionLocalAmount'];
                                $retensionReportingAmount = $retntn_data['retensionReportingAmount'];
                            }
                        }

                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => (round($totalValue['total_value'],$totalValue['transactionCurrencyDecimalPlaces'])),
                            'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces'])),
                            'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'] ,$totalValue['companyReportingCurrencyDecimalPlaces'])),
                            'customerCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['customerCurrencyExchangeRate'],$totalValue['customerCurrencyDecimalPlaces'])),
                            'retentionPercentage' =>$retentionPercentage,
                            'retensionTransactionAmount' =>  $retensionTransactionAmount,
                            'retensionLocalAmount' =>  $retensionLocalAmount,
                            'retensionReportingAmount' => $retensionReportingAmount,
                        );

                        $this->db->where('invoiceAutoID', $invoiceAutoID);
                        $this->db->update('srp_erp_customerinvoicemaster', $data);


                        if (!empty($t_arr)) {
                            $this->db->update_batch('srp_erp_customerinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                        }
                        
                        if($wacRecalculationEnableYN == 0 && $master_dt['invoiceType'] != 'Manufacturing'){ 
                            reupdate_companylocalwac('srp_erp_customerinvoicedetails',$invoiceAutoID,'invoiceAutoID','companyLocalWacAmount');
                        }

                    } else {
                        return array('e', 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                        /*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*//*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*/
                        exit;
                    }
                } elseif($approvals_status == 3){
                    return array('w', 'There are no users exist to perform approval for this document.');
                    exit;
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                //$this->session->set_flashdata('e', 'Supplier Invoice Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                /* return array('error' => 0, 'message' => 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());*/
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                //return array('status' => false);
            } else {
                $autoApproval= 0; // get_document_auto_approval('CINV');

                if($autoApproval==0) {
                   
                    $result = $this->save_invoice_approval(0, $master_data['invoiceAutoID'], 1, 'Auto Approved');
                    if($result){
                        $this->db->trans_commit();
                        return array('s', 'Document confirmed successfully');
                    }
                }else{
                    $this->db->trans_commit();
                    return array('s', 'Document confirmed successfully');
                }
            }
        }
    }


    function save_invoice_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0,$isRetentionYN=0)
    {
        

        $this->load->library('Approvals');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        if($autoappLevel==1){
            $system_id = trim($this->input->post('invoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['invoiceAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();
      

        $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, 
                SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) AS qty,IFNULL(ware_house.currentStock,0) as currentStock ,
                IFNULL( ware_house.currentStock,0) as availableStock,
                ( IFNULL( ware_house.currentStock, 0 ) - ((IFNULL(pq.stock,0))+(IFNULL( SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ),0)))) AS stock,
                ware_house.itemAutoID, cus_inv.wareHouseAutoID
                FROM srp_erp_customerinvoicedetails AS cus_inv
                LEFT JOIN ( 
                    SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID ={$companyID} GROUP BY wareHouseAutoID, itemAutoID 
                ) AS ware_house ON ware_house.itemAutoID = cus_inv.itemAutoID AND ware_house.wareHouseAutoID = cus_inv.wareHouseAutoID
                JOIN srp_erp_itemmaster ON cus_inv.itemAutoID = srp_erp_itemmaster.itemAutoID
                LEFT JOIN (
                    SELECT
                        SUM( stock ) AS stock, t1.ItemAutoID, wareHouseAutoID 
                    FROM
                        (
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockadjustmentmaster
                            LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                        WHERE
                            companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockcountingmaster
                            LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                        WHERE
                            companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock, itemAutoID,  srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_itemissuemaster
                            LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                        WHERE
                            srp_erp_itemissuemaster.companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerreceiptmaster
                            LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                        WHERE
                            srp_erp_customerreceiptmaster.companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerinvoicemaster
                            LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                        WHERE
                            srp_erp_customerinvoicemaster.companyID = {$companyID} AND srp_erp_customerinvoicedetails.invoiceAutoID != '{$system_id}' 
                            AND approvedYN != 1  AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( deliveredQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_deliveryorder
                            LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                        WHERE
                            srp_erp_deliveryorder.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( transfer_QTY / conversionRateUOM ) AS stock,
                            itemAutoID,
                            srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                        FROM
                            srp_erp_stocktransfermaster
                            LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                        WHERE
                            srp_erp_stocktransfermaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' 
                        ) t1 
                    GROUP BY
                        t1.wareHouseAutoID,
                        t1.ItemAutoID 
                    ) AS pq ON pq.ItemAutoID = cus_inv.itemAutoID 
                    AND pq.wareHouseAutoID = cus_inv.wareHouseAutoID 
                WHERE invoiceAutoID = '{$system_id}'  AND type != 'DO'
                AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                GROUP BY itemAutoID
                HAVING stock < 0";
        $items_arr = $this->db->query($sql)->result_array();

        
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $system_id);
        $this->db->from('srp_erp_customerinvoicemaster');
        $master = $this->db->get()->row_array();

        if($wacRecalculationEnableYN == 0 && $master['invoiceType'] != 'Manufacturing'){ 
            reupdate_companylocalwac('srp_erp_customerinvoicedetails',$system_id,'invoiceAutoID','companyLocalWacAmount');
        }

        if($status!=1){
            $items_arr='';
        }
        if (!$items_arr) {
            if($autoappLevel==0){
                $approvals_status=1;
            }else{
                $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CINV');
            }
           
          
            if ($approvals_status == 1 && $isRetentionYN==0) {
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicedetails');
                $invoice_detail = $this->db->get()->result_array();

                

                if($master['retentionPercentage']>0){
                    $this->create_retention_invoice($system_id);
                }

                if($master["invoiceType"] != "Manufacturing") {
                    if($master["invoiceType"] != "Insurance") {
                        for ($a = 0; $a < count($invoice_detail); $a++) {
                            if ($invoice_detail[$a]['type'] == 'Item') {
                                $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                                    $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                    $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                    $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                                    $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    if (!empty($item_arr)) {
                                        $this->db->where('itemAutoID', trim($invoice_detail[$a]['itemAutoID']));
                                        $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                                    }
                                    $itemledger_arr[$a]['documentID'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $master['invoiceAutoID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $master['invoiceCode'];
                                    $itemledger_arr[$a]['documentDate'] = $master['invoiceDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $invoice_detail[$a]['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $invoice_detail[$a]['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $invoice_detail[$a]['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $invoice_detail[$a]['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $invoice_detail[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $invoice_detail[$a]['itemDescription'];
                                    $itemledger_arr[$a]['SUOMID'] = $invoice_detail[$a]['SUOMID'];
                                    $itemledger_arr[$a]['SUOMQty'] = $invoice_detail[$a]['SUOMQty'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $invoice_detail[$a]['defaultUOMID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $invoice_detail[$a]['defaultUOM'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $invoice_detail[$a]['unitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionUOM'] = $invoice_detail[$a]['unitOfMeasure'];
                                    $itemledger_arr[$a]['transactionQTY'] = ($invoice_detail[$a]['requestedQty'] * -1);
                                    $itemledger_arr[$a]['convertionRate'] = $invoice_detail[$a]['conversionRateUOM'];
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $item['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $item['assteType'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                    $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                                    $itemledger_arr[$a]['transactionAmount'] = round((($invoice_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['salesPrice'] = (($invoice_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])) * -1);
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                    $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                                    $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                                    $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                                    $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                                    $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                                    $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $master['companyID'];
                                    $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                                    $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                                }
                            }
                        }
                        if (!empty($itemledger_arr)) {
                            $itemledger_arr = array_values($itemledger_arr);
                            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                        }
                    }
                    $this->load->model('Double_entry_model');
                    if($master["invoiceType"] != "Insurance") {
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');
                    }else{
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_insurance($system_id, 'CINV');
                    }

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        // $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyLocalExchangeRate']) ? $generalledger_arr[$i]['companyLocalExchangeRate'] : 1), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyReportingExchangeRate']) ? $generalledger_arr[$i]['companyReportingExchangeRate'] : 1 ), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] /isset($generalledger_arr[$i]['partyExchangeRate']) ? $generalledger_arr[$i]['partyExchangeRate'] : 1 ), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, 3);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount']  / 1), 3);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1), 3);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1 ), 3);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }

                }else{
                    for ($a = 0; $a < count($invoice_detail); $a++) {
                        if ($invoice_detail[$a]['type'] == 'Item') {
                            $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                            if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                                $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];

                                $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);

                            }
                        }
                    }

                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_mfq_customer_invoice_data($system_id, 'CINV');
                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        //$generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        //$generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        //$generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        //$generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, 3);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($amount / 1), 3);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($amount / 1), 3);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount / 1 ), 3);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }

                }

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster', $data);
                //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');

               

                if($master["invoiceType"] == "Insurance") {
                    $sumsup = "SELECT (sum(transactionAmount)-sum(marginAmount)) as transactionAmount,
                                srp_erp_customerinvoicedetails.supplierAutoID as supplierAutoID,
                                srp_erp_customerinvoicedetails.segmentID as segmentID,
                                srp_erp_customerinvoicedetails.segmentCode as segmentCode,
                                srp_erp_suppliermaster.supplierName as supplierName,
                                srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,
                                srp_erp_suppliermaster.supplierAddress1 as supplierAddress,
                                srp_erp_suppliermaster.supplierTelephone as supplierTelephone,
                                srp_erp_suppliermaster.supplierFax as supplierFax,
                                srp_erp_suppliermaster.liabilityAutoID as liabilityAutoID,
                                srp_erp_suppliermaster.liabilitySystemGLCode as liabilitySystemGLCode,
                                srp_erp_suppliermaster.liabilityGLAccount as liabilityGLAccount,
                                srp_erp_suppliermaster.liabilityDescription as liabilityDescription,
                                srp_erp_suppliermaster.liabilityType as liabilityType,
                                srp_erp_suppliermaster.supplierCurrencyID as supplierCurrencyID,
                                srp_erp_suppliermaster.supplierCurrency as supplierCurrency,
                                srp_erp_suppliermaster.supplierCurrencyDecimalPlaces as supplierCurrencyDecimalPlaces
                            FROM
                                `srp_erp_customerinvoicedetails`
                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID
                            WHERE
                                `invoiceAutoID` = $system_id
                            GROUP BY
                              supplierAutoID";
                    $sumsupdetail = $this->db->query($sumsup)->result_array();
                    $this->load->library('sequence');
                    $invdate=explode("-",$master['invoiceDate']);

                    foreach($sumsupdetail as $val){
                        $datasup['documentID'] = 'BSI';
                        $datasup['invoiceType'] = 'Standard';
                        $datasup['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $datasup['companyFinanceYear'] = $master['companyFinanceYear'];
                        $datasup['warehouseAutoID'] = $master['wareHouseAutoID'];
                        $datasup['isSytemGenerated'] = 1;
                        $datasup['documentOrigin'] = 'CINV';
                        $datasup['documentOriginAutoID'] = $system_id;
                        $datasup['FYBegin'] = $master['FYBegin'];
                        $datasup['FYEnd'] = $master['FYEnd'];
                        $datasup['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $datasup['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $datasup['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
                        $datasup['bookingInvCode'] = $this->sequence->sequence_generator_fin('BSI',$master['companyFinanceYearID'],$invdate[0],$invdate[1]);
                        $datasup['bookingDate'] = $master['invoiceDate'];
                        $datasup['invoiceDate'] = $master['invoiceDate'];
                        $datasup['invoiceDueDate'] = $master['invoiceDueDate'];
                        $datasup['comments'] = 'From custome invoice '.$master['invoiceCode'];
                        $datasup['RefNo'] = $master['invoiceCode'];
                        $datasup['supplierID'] = $val['supplierAutoID'];
                        $datasup['supplierCode'] = $val['supplierSystemCode'];
                        $datasup['supplierName'] = $val['supplierName'];
                        $datasup['supplierAddress'] = $val['supplierAddress'];
                        $datasup['supplierTelephone'] = $val['supplierTelephone'];
                        $datasup['supplierFax'] = $val['supplierFax'];
                        $datasup['supplierliabilityAutoID'] = $val['liabilityAutoID'];
                        $datasup['supplierliabilitySystemGLCode'] = $val['liabilitySystemGLCode'];
                        $datasup['supplierliabilityGLAccount'] = $val['liabilityGLAccount'];
                        $datasup['supplierliabilityDescription'] = $val['liabilityDescription'];
                        $datasup['supplierliabilityType'] = $val['liabilityType'];
                        $datasup['supplierInvoiceDate'] = $master['invoiceDate'];
                        $datasup['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $datasup['transactionCurrency'] = $master['transactionCurrency'];
                        $datasup['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $datasup['transactionAmount'] = $val['transactionAmount'];
                        $datasup['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $datasup['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $datasup['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $datasup['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $datasup['companyLocalAmount'] = $val['transactionAmount']/$master['companyLocalExchangeRate'];
                        $datasup['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $datasup['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $datasup['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $datasup['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $datasup['companyReportingAmount'] = $val['transactionAmount']/$master['companyReportingExchangeRate'];
                        $datasup['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $datasup['supplierCurrencyID'] = $val['supplierCurrencyID'];
                        $datasup['supplierCurrency'] = $val['supplierCurrency'];
                        $datasup['segmentID'] = $val['segmentID'];
                        $datasup['segmentCode'] = $val['segmentCode'];
                        $datasup['companyID'] = current_companyID();
                        $datasup['companyCode'] = current_companyCode();
                        $supplier_currency = currency_conversionID($master['transactionCurrencyID'], $val['supplierCurrencyID']);
                        $datasup['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
                        $datasup['supplierCurrencyAmount'] = $val['transactionAmount']/$supplier_currency['conversion'];
                        $datasup['supplierCurrencyDecimalPlaces'] = $val['supplierCurrencyDecimalPlaces'];
                        $datasup['confirmedYN'] = 1;
                        $datasup['confirmedByEmpID'] = current_userID();
                        $datasup['confirmedByName'] = current_user();
                        $datasup['confirmedDate'] = $this->common_data['current_date'];
                        $datasup['createdUserGroup'] = $this->common_data['user_group'];
                        $datasup['createdPCID'] = $this->common_data['current_pc'];
                        $datasup['createdUserID'] = $this->common_data['current_userID'];
                        $datasup['createdDateTime'] = $this->common_data['current_date'];
                        $datasup['createdUserName'] = $this->common_data['current_user'];

                        $supresult=$this->db->insert('srp_erp_paysupplierinvoicemaster', $datasup);
                        $last_idsup = $this->db->insert_id();
                        if($supresult){
                            $supid=$val['supplierAutoID'];
                            $supd = "SELECT * FROM `srp_erp_customerinvoicedetails` WHERE `invoiceAutoID` = $system_id AND `supplierAutoID` = $supid";
                            $supdetail = $this->db->query($supd)->result_array();

                            foreach($supdetail as $detl){
                                $datasupd['InvoiceAutoID'] = $last_idsup;
                                $datasupd['segmentID'] = $detl['segmentID'];
                                $datasupd['segmentCode'] = $detl['segmentCode'];
                                $datasupd['description'] = $detl['description'];
                                $datasupd['GLCode'] = "-";
                                $datasupd['transactionAmount'] = round($detl['transactionAmount']-$detl['marginAmount'],$master['transactionCurrencyDecimalPlaces']);
                                $datasupd['transactionExchangeRate'] = $master['transactionExchangeRate'];
                                $datasupd['companyLocalAmount'] = round($datasupd['transactionAmount']/$master['companyLocalExchangeRate'], $master['companyLocalCurrencyDecimalPlaces']);
                                $datasupd['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $datasupd['companyReportingAmount'] = round($datasupd['transactionAmount']/$master['companyReportingExchangeRate'], $master['companyReportingCurrencyDecimalPlaces']);
                                $datasupd['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $datasupd['supplierAmount'] = round($datasupd['transactionAmount']/$datasup['supplierCurrencyExchangeRate'], $datasup['supplierCurrencyDecimalPlaces']);
                                $datasupd['supplierCurrencyExchangeRate'] = $datasup['supplierCurrencyExchangeRate'];
                                $datasupd['companyCode'] = $this->common_data['company_data']['company_code'];
                                $datasupd['companyID'] = $this->common_data['company_data']['company_id'];
                                $datasupd['createdUserGroup'] = $this->common_data['user_group'];
                                $datasupd['createdPCID'] = $this->common_data['current_pc'];
                                $datasupd['createdUserID'] = $this->common_data['current_userID'];
                                $datasupd['createdUserName'] = $this->common_data['current_user'];
                                $datasupd['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_paysupplierinvoicedetail', $datasupd);
                            }
                            $this->load->library('Approvals');
                            $approvals_status_sup = $this->approvals->auto_approve($last_idsup, 'srp_erp_paysupplierinvoicemaster','InvoiceAutoID', 'BSI',$master['invoiceDate'],$master['invoiceDate']);
                            if($approvals_status_sup==1){
                                $this->load->model('Payable_modal');
                                $this->Payable_modal->save_supplier_invoice_approval(0, $last_idsup, 1, 'Auto Approved');
                            }
                        }
                    }
                }
            }else{

                if($isRetentionYN==1)
                {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_opr($system_id, 'CINV');

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];

                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                          //  $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                            if($double_entry['gl_detail'][$i]['gl_cr'] < 0){
                                $amount = ($double_entry['gl_detail'][$i]['gl_cr']);
                            } else {
                                $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                            }
                        }

                       

                       // $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyLocalExchangeRate']) ? $generalledger_arr[$i]['companyLocalExchangeRate'] : 1), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyReportingExchangeRate']) ? $generalledger_arr[$i]['companyReportingExchangeRate'] : 1), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        // $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['partyExchangeRate']) ? $generalledger_arr[$i]['partyExchangeRate'] : 1), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        
                        $generalledger_arr[$i]['transactionAmount'] = round($amount,3);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($amount/1), 3);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($amount/1), 3);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount /1), 3);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
    
                        
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }

                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                if($master['invoiceType']='Project')
                {
                    $this->updateRVMconfirmstatus($system_id);
                }
                return array('s', 'Invoice Approval Successfull.', 1);
            }
        } else {
            return array('e', 'Some Item quantities are not sufficient to approve this transaction.', $items_arr);
        }
    }

    function updateRVMconfirmstatus($invoiceAutoID)
    {
        $rvmdetail = $this->db->query("SELECT matchID FROM `srp_erp_rvadvancematch` WHERE matchinvoiceAutoID = $invoiceAutoID")->row_array();
        $invoicedetail=  $this->db->query("SELECT invoiceCode FROM `srp_erp_customerinvoicemaster` WHERE invoiceAutoID = $invoiceAutoID ")->row_array();
        if(!empty($rvmdetail['matchID']))
        {
            $data = array(
                'confirmedYN' => 1,
                'Narration' => 'Receipt Voucher Auto Generated ('.$invoicedetail['invoiceCode'].')',
                'confirmedDate' => current_date(),
                'confirmedByEmpID' => current_userID(),
                'confirmedByName' => current_user()
            );

            $this->db->where('matchID', $rvmdetail['matchID']);
            $confirmation = $this->db->update('srp_erp_rvadvancematch', $data);

        }
    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = cinvDetail.invoiceDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();

        $query2 = "SELECT
                        SUM(cinvDetail.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";


        $r2 = $this->db->query($query2)->row_array();


        if (empty($r1) && empty($r2)) {
            $validate = true;
        } else if (empty($r1) || $r1['countAll'] == 0) {
            $validate = true;
        } else {
            if ($r1['countAll'] == $r2['totalQty']) {
                $validate = true;
            } else {
                $validate = false;
            }
        }
        return $validate;

    }


    /////////////////////////////// Customer Invoice End //////////////////////////////


    //////////////////////////////// 3PL Vendor Invoice ////////////////////////////////

    function set_general_ledger_records_3PL($sales_id){

        $mapping_records = $this->get_sales_client_credit_debit_summary($sales_id);
        $base_ledger_arr = array();
        $client_sales_data = $this->get_sales_client_record($sales_id);
        $settings = get_clent_ecommerce_settings();


        //check 3pl company id
        $pl_company_id = $client_sales_data['3pl_company_id'];
        $supplier_details = $this->get_supplier_details(trim($pl_company_id));

        if(empty($pl_company_id)){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL Vendor Invoice Created Failed No 3pl Company',3);
            return array('status'=>'eror', 'message' => '3PL Company ID not present.');
            return false;
        }elseif($pl_company_id == $settings['company_driver_id']){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL vendor is the same company',3);
            return array('status'=>'eror', 'message' => '3PL vendor is the same company.');
            return false;
        }elseif(empty($supplier_details)){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL Vendor Invoice Created Failed Vendor not exists',3);
            return array('status'=>'eror', 'message' => '3PL Vendor Invoice Created Failed Vendor not exists.');
            return false;
        }

        if($mapping_records){

            $response = $this->save_3PL_supplier_invoice_set($sales_id);
          
        }else{

            $response = array();
        }

        return $response;

    }

    function save_3PL_supplier_invoice_set($sales_id) {

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,3);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];


        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

        try{

            $response_header = $this->save_supplier_invoice_header($sales_id,$client_sales_data,$client_mapping,'3pl');

            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_supplier_expense_records($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);
        
                $response_confirmation = $this->supplier_invoice_confirmation($response['last_id'],$client_sales_data);

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'3pl_vendor');

                add_process_log_record($response['last_id'],'BSI',$sales_id,1,'3PL Vendor Invoice Created',3);


            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => '3PL Supplier invoice successfully created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
    }


    //////////////////////////////// 3PL Customer Invoice ////////////////////////////////
    function set_general_ledger_customer_3PL($sales_id){
        
        $mapping_records = $this->get_sales_client_credit_debit_summary($sales_id);
        $base_ledger_arr = array();

        if($mapping_records){

            $response = $this->save_customer_invoice_3pl_set($sales_id);
          
        }else{

            $response = array();
        }

        return $response;
    }

    function save_customer_invoice_3pl_set($sales_id){

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,4);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];
        $pl_company_id = $client_sales_data['3pl_company_id'];
        $customer_details = $this->get_customer_details(trim($client_sales_data['3pl_company_id'] ?? ''));
        $settings = get_clent_ecommerce_settings();

        if(empty($pl_company_id)){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL Customer Invoice Created Failed No 3pl Company ID',4);
            return array('status'=>'error', 'message' => '3PL Company ID not present.');
        }elseif($pl_company_id == $settings['company_driver_id']){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL customer is the same company',4);
            return array('status'=>'eror', 'message' => '3PL customer is the same company.');
            return false;
        }elseif(empty($customer_details)){
            add_process_log_record(0,'BSI',$sales_id,2,'3PL Customer Invoice Created Failed Customer not exists',4);
            return array('status'=>'error', 'message' => '3PL Customer Invoice Created Failed Customer not exists.');
        }

        
        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }
      
        try{

            $response_header = $this->save_customer_invoice_header($sales_id,$client_sales_data,$client_mapping,'3PL_customer');

            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_direct_invoice_detail($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);

                $response_confirmation = $this->invoice_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'3pl_customer');

                add_process_log_record($response['last_id'],'CINV',$sales_id,1,'3PL Customer Invoice Created',4);

                return array('status'=>'success', 'message' => '3PL Customer invoice successfully created.');

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
           

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
      

    }

    //////////////////////////////// Direct Invoice ////////////////////////////////
    function set_receiptvoucher_header($sales_id)
    {

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,5);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];
        $value_exts = 0;

        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value_exts = 1;
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

        // send back nothing generate
        if($value_exts == 0){
            add_process_log_record(0,'JV',$sales_id,2,'No records to generate Direct Receipt Voucher',5);
            return array('status'=>'error', 'message' => 'No records to generate Direct Receipt Voucher');
        }
        
        try{

            $response_header = $this->save_receiptvoucher_header($sales_id,$client_sales_data,$client_mapping);

            $response = $response_header;

            if($response && isset($response['last_id'])){

               $response_expense_record = $this->save_direct_rv_detail($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);

                $response_confirmation = $this->receipt_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'3pl_direct_receipt_id');

                add_process_log_record($response['last_id'],'RV',$sales_id,1,'Direct Receipt Created',5);

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Direct Income Receipt created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }

     
    }

    function save_receiptvoucher_header($sales_id,$client_sales_data,$client_mapping)
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $RVdates =  $client_sales_data['completed_time'];
        $RVdate = input_format_date($RVdates, $date_format_policy);
        
        $RVcheqDate = $client_sales_data['date_time'];
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);

        $invoiceType = 'DirectIncome';
        $referenceno = $client_sales_data['order'];
        $customer_details = $this->get_customer_details(trim($client_sales_data['store_id'] ?? ''));
        $financearray_rec = get_financial_period_date_wise($RVchequeDate);
        $financearray = $financearray_rec['companyFinancePeriodID'];

        $financeyear = $financearray_rec['companyFinanceYearID'];
        $company_finance_year = company_finance_year($financeyear);

        $financeYear = $company_finance_year['startdate'].' - '.$company_finance_year['endingdate'];
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);

        $segment_det = '86|GEN';
       // $financeYear = '01-04-2022 - 31-03-2023';
        $currency_code = 'OMR | Omani Rial';
        //'OMR|Omani Rial';
        $bank = 'Commercial Bank | World Trade Centre | CCEYLKLX | 8010009026 | BSA | Bank';
        $RvBankCode = $ecommerce_settings['company_bank_id'];
        $bank_detail = fetch_gl_account_desc($RvBankCode);
        
        if($bank_detail){
            $bank = trim($bank_detail['bankName'] ?? '') . ' | ' . trim($bank_detail['bankBranch'] ?? '') . ' | ' . trim($bank_detail['bankSwiftCode'] ?? '') . ' | ' . trim($bank_detail['bankAccountNumber'] ?? '') . ' | ' . trim($bank_detail['subCategory'] ?? ''). ' | Bank' ;
        }
  
        $currency_id = 1;
       
        

        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($financeYear));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($RVdate);
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
            $financePeriodDetails = get_financial_period_date_wise($RVdate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $segment = explode('|', trim($segment_det));
        $bank = explode('|', trim($bank));
        $currency_code = explode('|', trim($currency_code));
        $bank_detail = fetch_gl_account_desc(trim($RvBankCode));

        

        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = 121;
        $data['companyFinanceYear'] = trim($financeYear);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($financearray);
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['RVdate'] = trim($RVdate);
        $narration= $referenceno;
        $data['RVNarration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['RVbank'] = $bank_detail['bankName'];
        $data['RVbankBranch'] = $bank_detail['bankBranch'];
        $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['RVbankType'] = $bank_detail['subCategory'];
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['RVchequeNo'] = rand(11111,99999);
        if ($bank_detail['isCash'] == 0) {
            $paymentMode = 1;
            $data['paymentType'] = $paymentMode;
            if($paymentMode == 1) {
                $data['RVchequeDate'] = trim($RVchequeDate);
                $data['bankTransferDetails'] = null;
            } else {
                $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
                $data['RVchequeDate'] = null;
            }
        } else {
            $data['RVchequeDate'] = null;
        }
        $data['RvType'] = $invoiceType;
        $data['referanceNo'] = $referenceno;
        $data['RVbankCode'] = trim($RvBankCode);

        if ($data['RvType'] == 'Direct' || $data['RvType'] == 'DirectItem' || $data['RvType'] == 'DirectIncome') {
            $data['customerName'] = trim($customer_details['customerName'] ?? '');
            $data['customerID'] = '';
            $data['customerAddress'] = '';
            $data['customerTelephone'] = '';
            $data['customerFax'] = '';
            $data['customerEmail'] = '';
            $data['customerCurrency'] = trim($currency_code[0] ?? '');
            $data['customerCurrencyID'] = $currency_id;//trim($this->input->post('transactionCurrencyID') ?? '');
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['customerCurrencyID']);
        } else {
            $customer_arr = $this->fetch_customer_data(trim($customer_details['customerAutoID'] ?? ''));
            $data['customerID'] = $customer_arr['customerAutoID'];
            $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
            $data['customerName'] = $customer_arr['customerName'];
            $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
            $data['customerTelephone'] = $customer_arr['customerTelephone'];
            $data['customerFax'] = $customer_arr['customerFax'];
            $data['customerEmail'] = $customer_arr['customerEmail'];
            $data['customerreceivableAutoID'] = $customer_arr['receivableAutoID'];
            $data['customerreceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
            $data['customerreceivableGLAccount'] = $customer_arr['receivableGLAccount'];
            $data['customerreceivableDescription'] = $customer_arr['receivableDescription'];
            $data['customerreceivableType'] = $customer_arr['receivableType'];
            $data['customerCurrency'] = trim($currency_code[0] ?? '');//$customer_arr['customerCurrency'];
            $data['customerCurrencyID'] = $currency_id;//$customer_arr['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        }
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = trim($currency_id);
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];

        if (trim($this->input->post('receiptVoucherAutoId') ?? '')) {
            $masterID = $this->input->post('receiptVoucherAutoId');
            $taxAdded = $this->db->query("SELECT receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE receiptVoucherAutoId = $masterID
                                            UNION
                                        SELECT receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails WHERE receiptVoucherAutoId = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }

            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
            $this->db->update('srp_erp_customerreceiptmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Receipt Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('receiptVoucherAutoId'));
            }
        } else {
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }
            //$this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['RVcode'] = 0;
            $this->db->insert('srp_erp_customerreceiptmaster', $data);

            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Receipt Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_direct_rv_detail($receiptVoucherAutoId,$client_sales_data,$client_mapping,$selected_gl_record)
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($receiptVoucherAutoId),'RV','receiptVoucherAutoId');
        $this->db->trans_start();
        $this->db->select('transactionCurrency, customerExchangeRate, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

 

        foreach($selected_gl_record as $gl_record) {

            $projectExist = project_is_exist();
            $gl_text_type = $gl_record['gl_account_description'];
            $segment_gls = '86|GEN';
            $gl_code_des = $gl_record['gl_code'];
            $gl_code = $gl_record['gl_account_code'];
            $projectID = $this->input->post('projectID');
            $amount = $gl_record['final_value'];
            $description = $gl_record['descripiton'];
            $discountPercentage = $this->input->post('discountPercentage');
            $project_categoryID = $this->input->post('project_categoryID');
            $project_subCategoryID = $this->input->post('project_subCategoryID');
    
       
            $segment_gl = $gl_record['segement'];
            $gl_code_des = $this->input->post('gl_code_des');
           
            $gl_auto_ids = $gl_record['gl_code'];
            $amount = $gl_record['final_value'];
            $description = $gl_record['descripiton'];
            $gl_text_type = $gl_record['gl_account_description'];

            if($gl_record) {

                $segment = explode('|', trim($segment_gls));

                $g_code_details = fetch_gl_account_from_systemAccountCode($gl_code,$this->common_data['company_data']['company_id']);
                $gl_code_des =  trim($g_code_details['systemAccountCode'] ?? '') . ' | ' . trim($g_code_details['GLSecondaryCode'] ?? '') . ' | ' . trim($g_code_details['GLDescription'] ?? '') . ' | ' . trim($g_code_details['subCategory'] ?? '');
                $gl_code_de = explode('|', $gl_code_des);

                $data['receiptVoucherAutoId'] = trim($receiptVoucherAutoId);
                $data['GLAutoID'] = trim($g_code_details['GLAutoID'] ?? '');
                $data['systemGLCode'] = trim($gl_code_de[0] ?? '');
                $data['GLCode'] = trim($gl_code_de[1] ?? '');
                $data['GLDescription'] = trim($gl_code_de[2] ?? '');
                $data['GLType'] = trim($gl_code_de[3] ?? '');
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');

               
                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID);
                    $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                    $data['project_categoryID'] =$project_categoryID;
                    $data['project_subCategoryID'] =$project_subCategoryID;
                }
                $data['projectID'] = $projectID;
                $data['discountPercentage'] = trim($discountPercentage);
                $data['discountAmount'] = trim(($amount*$discountPercentage)/100);
                $data['transactionAmount'] = trim($amount-$data['discountAmount']);
                $data['companyLocalAmount'] = ($data['transactionAmount'] / isset($master['companyLocalExchangeRate']) ? $master['companyLocalExchangeRate']:1 );
                $data['companyReportingAmount'] = ($data['transactionAmount'] / isset($master['companyReportingExchangeRate']) ? $master['companyReportingExchangeRate'] : 1);
            
                $data['customerAmount'] = 0;
                if ($master['customerExchangeRate']) {
                    $data['customerAmount'] = ($data['transactionAmount'] / isset($master['customerExchangeRate']) ? $master['customerExchangeRate']: 1);
                }


                $data['description'] = trim($description);
                $data['type'] = 'GL';
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                //if (trim($this->input->post('receiptVoucherDetailAutoID') ?? '')) {
                /*$this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
                $this->db->update('srp_erp_customerreceiptdetail', $data[$key]);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Receipt Voucher Detail : ' . $data[$key]['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Receipt Voucher Detail : ' . $data[$key]['GLDescription'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('receiptVoucherDetailAutoID'));
                }*/
                //} else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                //}

                $this->db->insert('srp_erp_customerreceiptdetail', $data);
                $last_id = $this->db->insert_id();

                if($isGroupByTax == 1){ 
                    if(!empty($gl_text_type[$key])){
                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$gl_text_type[$key]);
                        $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                        $dataTax['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
                        $dataTax['taxFormulaMasterID'] = $gl_text_type[$key];
                        $dataTax['taxDescription'] = $tax_master['Description'];
                        $dataTax['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $dataTax['transactionCurrency'] = $master['transactionCurrency'];
                        $dataTax['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $dataTax['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $dataTax['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $dataTax['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $dataTax['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $dataTax['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $dataTax['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $dataTax['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    }             
                }
            }
        }

        // $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Receipt Voucher Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function receipt_confirmation($receiptVoucherAutoId,$client_sales_data)
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $RVcode = 0;
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $receiptVoucherAutoId = $receiptVoucherAutoId;

        $mastertbl = $this->db->query("SELECT RVdate, RVchequeDate FROM `srp_erp_customerreceiptmaster` where companyID = $companyID And receiptVoucherAutoId = $receiptVoucherAutoId ")->row_array();
        $mastertbldetail = $this->db->query("SELECT receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail` WHERE companyID = $companyID AND type = 'Item' AND receiptVoucherAutoId = $receiptVoucherAutoId")->row_array();
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $currentdate = current_date(false);
        $this->load->library('Approvals');
        $this->db->select('receiptVoucherAutoId');
        $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
        $this->db->from('srp_erp_customerreceiptdetail');
        $results = $this->db->get()->row_array();

        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {

               $receiptvoucherDetails = $this->db->query("select
                                        GROUP_CONCAT(itemAutoID) as itemAutoID
                                        from 
                                        srp_erp_customerreceiptdetail
                                        where 
                                        companyID = $companyID 
                                        AND receiptVoucherAutoId = $receiptVoucherAutoId")->row("itemAutoID");
                                        
        
        if(!empty($receiptvoucherDetails) && $wacRecalculationEnableYN == 0){ 
            $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$receiptvoucherDetails");
            if(!empty($wacTransactionAmountValidation)){ 
              
                return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                exit();
            }
         
        }
            $rvid = $receiptVoucherAutoId;
            $taxamnt = 0;
            $GL = $this->db->query("SELECT SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid AND srp_erp_customerreceiptdetail.type='GL' GROUP BY receiptVoucherAutoId")->row_array();

            if (empty($GL)) {
                $GL = 0;
            } else {
                $GL = $GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid AND srp_erp_customerreceiptdetail.type='Item' GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Item)) {
                $Item = 0;
            } else {
                $Item = $Item['transactionAmount'];
            }
            $creditnote = $this->db->query("SELECT SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid AND (srp_erp_customerreceiptdetail.type='creditnote' OR srp_erp_customerreceiptdetail.type='SLR') GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($creditnote)) {
                $creditnote = 0;
            } else {
                $creditnote = $creditnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid AND srp_erp_customerreceiptdetail.type='Advance' GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Advance)) {
                $Advance = 0;
            } else {
                $Advance = $Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid AND srp_erp_customerreceiptdetail.type='Invoice' GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Invoice)) {
                $Invoice = 0;
            } else {
                $Invoice = $Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT	SUM(srp_erp_customerreceipttaxdetails.taxPercentage) as taxPercentage FROM srp_erp_customerreceipttaxdetails WHERE srp_erp_customerreceipttaxdetails.receiptVoucherAutoId = $rvid GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($tax)) {
                $tax = 0;
            } else {
                $tax = $tax['taxPercentage'];
                $taxamnt = (($Item + $GL) / 100) * $tax;
            }
            $totalamnt = ($Item + $GL + $Invoice + $Advance + $taxamnt) - $creditnote;

            if ($totalamnt < 0) {
                return array('error' => 2, 'message' => 'Grand total should be greater than 0');
            } else {
                $this->db->select('documentID, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
                $this->db->from('srp_erp_customerreceiptmaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                if ($master_dt['RVcode'] == "0") {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                        } else {
                            if ($emplocationid != '') {
                                $RVcode = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $this->common_data['emplanglocationid'], $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $RVcode = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }
                    $validate_code = validate_code_duplication($RVcode, 'RVcode', $receiptVoucherAutoId,'receiptVoucherAutoId', 'srp_erp_customerreceiptmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                    $rvcd = array(
                        'RVcode' => $RVcode
                    );
                    $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
                    $this->db->update('srp_erp_customerreceiptmaster', $rvcd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['RVcode'], 'RVcode', $receiptVoucherAutoId,'receiptVoucherAutoId', 'srp_erp_customerreceiptmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $tamount = array(
                    'transactionAmount' => $totalamnt
                );
                $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
                $this->db->update('srp_erp_customerreceiptmaster', $tamount);

                $this->db->select('documentID,receiptVoucherAutoId, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID,RVdate');
                $this->db->where('receiptVoucherAutoId', trim($receiptVoucherAutoId));
                $this->db->from('srp_erp_customerreceiptmaster');
                $app_data = $this->db->get()->row_array();

                $sql = "SELECT 
                        SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,
                        IFNULL(warehouse.currentStock , 0) as currentStock,
                        TRIM(	TRAILING '.' FROM	(	TRIM(TRAILING 0 FROM	((ROUND((( warehouse.currentStock - (( IFNULL( pq.stock, 0 ) ) +( IFNULL( SUM( srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM ), 0 ))))), 2 )))))) AS stock,
                        warehouse.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID 
                        FROM srp_erp_customerreceiptdetail 
                        LEFT JOIN (SELECT  SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID,itemAutoID FROM srp_erp_itemledger  WHERE  companyID = {$companyID}
                                GROUP BY  wareHouseAutoID,  itemAutoID )warehouse ON warehouse.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = warehouse.wareHouseAutoID 
                        LEFT JOIN (
                            SELECT
                                SUM( stock ) AS stock,
                                t1.ItemAutoID,
                                wareHouseAutoID 
                            FROM
                                (
                                SELECT
                                    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_stockadjustmentmaster
                                    LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                                WHERE
                                    companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,	srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_stockcountingmaster
                                    LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                                WHERE
                                    companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_itemissuemaster
                                    LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                                WHERE
                                    srp_erp_itemissuemaster.companyID = {$companyID}   AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerreceiptmaster
                                    LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                                WHERE
                                    srp_erp_customerreceiptmaster.companyID = {$companyID} AND srp_erp_customerreceiptdetail.receiptVoucherAutoId != '{$receiptVoucherAutoId}'	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerinvoicemaster
                                    LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                                WHERE
                                    srp_erp_customerinvoicemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( deliveredQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_deliveryorder
                                    LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                                WHERE
                                    srp_erp_deliveryorder.companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( transfer_QTY / conversionRateUOM ) AS stock,itemAutoID,	srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                                FROM
                                    srp_erp_stocktransfermaster
                                    LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                                WHERE
                                    srp_erp_stocktransfermaster.companyID = {$companyID} AND approvedYN != 1 	AND itemCategory = 'Inventory' 
                                ) t1 
                            GROUP BY
                                t1.wareHouseAutoID,
                                t1.ItemAutoID 
                            ) AS pq ON pq.ItemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND pq.wareHouseAutoID = srp_erp_customerreceiptdetail.wareHouseAutoID 
                        where receiptVoucherAutoId = '{$receiptVoucherAutoId}' AND itemCategory != 'Service' AND  itemCategory != 'Non Inventory' GROUP BY itemAutoID
                        Having stock < 0";
                
                      
                $item_low_qty = $this->db->query($sql)->result_array();

                if (!empty($item_low_qty)) {
                    //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction', 'itemAutoID' => $item_low_qty);
                }

                $autoApproval = 0;//get_document_auto_approval('RV');
                if ($autoApproval == 0) {
                    if ($PostDatedChequeManagement == 1 && ($mastertbl['RVchequeDate'] != '' || !empty($mastertbl['RVchequeDate'])) && (empty($mastertbldetail['payVoucherAutoId']) || $mastertbldetail['payVoucherAutoId']==' ')) {
                        if ($mastertbl['RVchequeDate'] > $mastertbl['RVdate']) {
                            if ($currentdate >= $mastertbl['RVchequeDate']) {
                                $approvals_status = $this->approvals->auto_approve($app_data['receiptVoucherAutoId'], 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 'RV', $app_data['RVcode'], $app_data['RVdate']);
                            } else {
                                return array('error' => 1, 'message' => 'This is a post dated cheque document. you cannot approve this document before the cheque date.');
                            }
                        } else {
                            $approvals_status = $this->approvals->auto_approve($app_data['receiptVoucherAutoId'], 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 'RV', $app_data['RVcode'], $app_data['RVdate']);
                        }
                    } else {
                        $approvals_status = $this->approvals->auto_approve($app_data['receiptVoucherAutoId'], 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 'RV', $app_data['RVcode'], $app_data['RVdate']);
                    }
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('RV', $app_data['receiptVoucherAutoId'], $app_data['RVcode'], 'Receipt Voucher', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 0, $app_data['RVdate']);
                } else {
                    return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                }

                if ($approvals_status == 1) {
                    /** item Master Sub check */
                    $documentID = trim($receiptVoucherAutoId);
                    $validate = $this->validate_itemMasterSub($documentID);
                    /** end of item master sub */
                    if ($validate) {
                        $autoApproval = 0;//get_document_auto_approval('RV');
                        if ($autoApproval == 0) {
                            $result = $this->save_rv_approval(0, $app_data['receiptVoucherAutoId'], 1, 'Auto Approved');
                            if ($result) {
                                return array('error' => 0, 'message' => 'Document Confirmed Successfully!', 'code' => $RVcode);
                            }
                        } else {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                            $this->db->update('srp_erp_customerreceiptmaster', $data);
                            //return array('status' => true, 'data' => 'Document Confirmed Successfully!');

                            if($wacRecalculationEnableYN == 0){ 
                                  reupdate_companylocalwac('srp_erp_customerreceiptdetail',trim($this->input->post('receiptVoucherAutoId') ?? ''),'receiptVoucherAutoId','companyLocalWacAmount');
                            }

                            return array('error' => 0, 'message' => 'Document Confirmed Successfully!', 'code' => $RVcode);
                        }
                    } else {
                        return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                    }
                } else if ($approvals_status == 3) {
                    return array('error' => 1, 'message' => 'There are no users exist to perform approval for this document');
                } else {
                    return array('error' => 1, 'message' => 'Confirm this transaction');
                    //return array('status' => false, 'data' => 'Confirm this transaction');
                }
            }
        }
    }

    function save_rv_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['receiptVoucherAutoId'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $companyID = current_companyID();
      
        $sql = "SELECT  srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription,
                SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,
                IFNULL(warehouse.currentStock , 0) as availableStock, IFNULL(warehouse.currentStock , 0) as currentStock,
                TRIM(TRAILING '.' FROM	(TRIM(TRAILING 0 FROM((	ROUND(((warehouse.currentStock - ((	IFNULL( pq.stock, 0 )) +(IFNULL( SUM( srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM ), 0 ))))),2)))))) AS stock,
                warehouse.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID 
                FROM srp_erp_customerreceiptdetail 
                JOIN srp_erp_itemmaster ON srp_erp_customerreceiptdetail.itemAutoID = srp_erp_itemmaster.itemAutoID
                LEFT JOIN (SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE  companyID = {$companyID}
                    GROUP BY wareHouseAutoID, itemAutoID )warehouse ON warehouse.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = warehouse.wareHouseAutoID
                    LEFT JOIN (
                            SELECT
                                SUM( stock ) AS stock,
                                t1.ItemAutoID,
                                wareHouseAutoID 
                            FROM
                                (
                                SELECT
                                    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_stockadjustmentmaster
                                    LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                                WHERE
                                    companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,	srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_stockcountingmaster
                                    LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                                WHERE
                                    companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_itemissuemaster
                                    LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                                WHERE
                                    srp_erp_itemissuemaster.companyID = {$companyID}   AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerreceiptmaster
                                    LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                                WHERE
                                    srp_erp_customerreceiptmaster.companyID = {$companyID} AND srp_erp_customerreceiptdetail.receiptVoucherAutoId != '{$system_id}'	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerinvoicemaster
                                    LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                                WHERE
                                    srp_erp_customerinvoicemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( deliveredQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_deliveryorder
                                    LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                                WHERE
                                    srp_erp_deliveryorder.companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( transfer_QTY / conversionRateUOM ) AS stock,itemAutoID,	srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                                FROM
                                    srp_erp_stocktransfermaster
                                    LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                                WHERE
                                    srp_erp_stocktransfermaster.companyID = {$companyID} AND approvedYN != 1 	AND itemCategory = 'Inventory' 
                                ) t1 
                            GROUP BY
                                t1.wareHouseAutoID,
                                t1.ItemAutoID 
                            ) AS pq ON pq.ItemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND pq.wareHouseAutoID = srp_erp_customerreceiptdetail.wareHouseAutoID 
                where receiptVoucherAutoId = '{$system_id}' AND itemCategory != 'Service' AND itemCategory != 'Non Inventory' GROUP BY itemAutoID  Having stock < 0";
        $items_arr = $this->db->query($sql)->result_array();
        if($wacRecalculationEnableYN == 0){ 
            reupdate_companylocalwac('srp_erp_customerreceiptdetail',$system_id,'receiptVoucherAutoId','companyLocalWacAmount');
        }

        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'RV');
            }
            if ($approvals_status == 1) {
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptmaster');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptdetail');
                $receipt_detail = $this->db->get()->result_array();
                for ($a = 0; $a < count($receipt_detail); $a++) {
                    if ($receipt_detail[$a]['type'] == 'Item') {
                        $item = fetch_item_data($receipt_detail[$a]['itemAutoID']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                            $itemAutoID = $receipt_detail[$a]['itemAutoID'];
                            $qty = $receipt_detail[$a]['requestedQty'] / $receipt_detail[$a]['conversionRateUOM'];
                            $wareHouseAutoID = $receipt_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                            $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($receipt_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($receipt_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }
                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['receiptVoucherAutoId'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['RVcode'];
                            $itemledger_arr[$a]['documentDate'] = $master['RVdate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['referanceNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $receipt_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $receipt_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $receipt_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $receipt_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $receipt_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $receipt_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['SUOMID'] = $receipt_detail[$a]['SUOMID'];
                            $itemledger_arr[$a]['SUOMQty'] = $receipt_detail[$a]['SUOMQty'];
                            $itemledger_arr[$a]['defaultUOMID'] = $receipt_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $receipt_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOMID'] = $receipt_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionUOM'] = $receipt_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionQTY'] = ($receipt_detail[$a]['requestedQty'] * -1);
                            $itemledger_arr[$a]['convertionRate'] = $receipt_detail[$a]['conversionRateUOM'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['revanueGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['revanueSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['revanueGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['revanueDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['revanueType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                            $itemledger_arr[$a]['transactionAmount'] = round((($receipt_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['salesPrice'] = (($receipt_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])) * -1);
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                            $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                            $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                            $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                            $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                            $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        }
                    }
                }

                /*if (!empty($item_arr)) {
                    $item_arr = array_values($item_arr);
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }*/

                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($system_id, 'RV');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['RVType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['RVNarration'];
                    $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                    $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    // $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    // $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyLocalExchangeRate']) ? $generalledger_arr[$i]['companyLocalExchangeRate'] : 1), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    // $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['companyReportingExchangeRate']) ? $generalledger_arr[$i]['companyReportingExchangeRate'] : 1 ), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    // $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / isset($generalledger_arr[$i]['partyExchangeRate']) ? $generalledger_arr[$i]['partyExchangeRate'] : 1), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, 3);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1),3);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1 ), 3);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1),3);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                    $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                    $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }
                $amount = receipt_voucher_total_value($double_entry['master_data']['receiptVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
                $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                $bankledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                $bankledger_arr['transactionType'] = 1;
                $bankledger_arr['bankName'] = $double_entry['master_data']['RVbank'];
                $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
                $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
                $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
                $bankledger_arr['documentType'] = 'RV';
                $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                $bankledger_arr['modeofpayment'] = $double_entry['master_data']['modeOfPayment'];
                $bankledger_arr['chequeNo'] = $double_entry['master_data']['RVchequeNo'];
                $bankledger_arr['chequeDate'] = $double_entry['master_data']['RVchequeDate'];
                $bankledger_arr['memo'] = $double_entry['master_data']['RVNarration'];
                $bankledger_arr['partyType'] = 'CUS';
                $bankledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                $bankledger_arr['partyCode'] = $double_entry['master_data']['customerSystemCode'];
                $bankledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $bankledger_arr['transactionAmount'] = round($amount, 3);
                $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                $bankledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $bankledger_arr['partyCurrencyAmount'] = round($amount, 3);//($bankledger_arr['transactionAmount'] / isset($bankledger_arr['partyCurrencyExchangeRate']) ? $bankledger_arr['partyCurrencyExchangeRate'] : 1);
                $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
                $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
                $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
                $bankledger_arr['bankCurrencyAmount'] = round($amount, 3);//($bankledger_arr['transactionAmount'] / isset($bankledger_arr['bankCurrencyExchangeRate']) ? $bankledger_arr['bankCurrencyExchangeRate'] : 1);
                $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
                $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                $bankledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                $bankledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                $bankledger_arr['createdPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['createdUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['createdDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['createdUserName'] = $this->common_data['current_user'];
                $bankledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['modifiedUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_bankledger', $bankledger_arr);

                if (!empty($generalledger_arr)) {
                    $generalledger_arr = array_values($generalledger_arr);
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                    $this->db->where('documentCode', 'RV');
                    $this->db->where('documentMasterAutoID', $system_id);
                    $totals = $this->db->get('srp_erp_generalledger')->row_array();
                    if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                        $generalledger_arr = array();
                        $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                        $ERGL = fetch_gl_account_desc($ERGL_ID);
                        $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                        $generalledger_arr['documentCode'] = $double_entry['code'];
                        $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                        $generalledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentType'] = $double_entry['master_data']['RVType'];
                        $generalledger_arr['documentYear'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                        $generalledger_arr['documentNarration'] = $double_entry['master_data']['RVNarration'];
                        $generalledger_arr['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
                        $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr['partyContractID'] = '';
                        $generalledger_arr['partyType'] = 'CUS';
                        $generalledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                        $generalledger_arr['partySystemCode'] = $double_entry['master_data']['customerSystemCode'];
                        $generalledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                        $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                        $generalledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                        $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                        $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                        $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                        $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), 3);
                        $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), 3);
                        $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), 3);
                        $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                        $generalledger_arr['amount_type'] = null;
                        $generalledger_arr['documentDetailAutoID'] = 0;
                        $generalledger_arr['GLAutoID'] = $ERGL_ID;
                        $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                        $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                        $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                        $generalledger_arr['GLType'] = $ERGL['subCategory'];
                        $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                        $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                        $generalledger_arr['subLedgerType'] = 0;
                        $generalledger_arr['subLedgerDesc'] = null;
                        $generalledger_arr['isAddon'] = 0;
                        $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                        $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                    }
                }
                $this->db->select_sum('transactionAmount');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $total = $this->db->get('srp_erp_customerreceiptdetail')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];
                $data['transactionAmount'] = $total;
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->update('srp_erp_customerreceiptmaster', $data);
                //$this->session->set_flashdata('s', 'Receipt Voucher Approval Successfully.');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Receipt Voucher Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Receipt Voucher Approval Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////

    function set_journal_entry_header($sales_id){

        $this->db->trans_start();

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,6);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];
        $value_exts = 0;

        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value_exts = 1;
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

       // send back nothing generate
        if($value_exts == 0){
           add_process_log_record(1,'JV',$sales_id,2,'No records to generate Journel Voucher',6);
    
           return array('status'=>'error', 'message' => 'No records to generate Journel Voucher');
        }
        

        try{

            $response_header = $this->save_journal_entry_header($sales_id,$client_sales_data,$client_mapping);

            $response = $response_header;
           
            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_gl_detail($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);
               
                $response_confirmation = $this->journal_entry_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'jv_receipt_id');

                add_process_log_record($response['last_id'],'JV',$sales_id,1,'Journel Voucher Created',6);

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Journel Voucher Receipt created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }

       
    }

    function save_journal_entry_header($sales_id,$client_sales_data,$client_mapping)
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $Jdates = $client_sales_data['date_time'];
        $JVdate = input_format_date($Jdates, $date_format_policy);

        $financearray_rec = get_financial_period_date_wise($Jdates);
        $financearray = $financearray_rec['companyFinancePeriodID'];
        $financeYear = $financearray_rec['companyFinanceYearID'];
        $company_finance_year = company_finance_year($financeYear);
        $segment_det = '86|GEN';

        $financeyear_period = $financearray_rec['dateFrom'].' - '.$financearray_rec['dateTo'];
        // $companyFinanceYear = '01-04-2022 - 31-03-2023';
        $companyFinanceYear = $company_finance_year['startdate'].' - '.$company_finance_year['endingdate'];
        $currency_code = 'OMR | Omani Rial';
        $transaction_currency_id = 1;
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        //'OMR|Omani Rial';
        $bank = 'Commercial Bank | World Trade Centre | CCEYLKLX | 8010009026 | BSA | Bank';
        $RvBankCode = $ecommerce_settings['company_bank_id'];
        $bank_detail = fetch_gl_account_desc($RvBankCode);
        
        if($bank_detail){
            $bank = trim($bank_detail['bankName'] ?? '') . ' | ' . trim($bank_detail['bankBranch'] ?? '') . ' | ' . trim($bank_detail['bankSwiftCode'] ?? '') . ' | ' . trim($bank_detail['bankAccountNumber'] ?? '') . ' | ' . trim($bank_detail['subCategory'] ?? ''). ' | Bank' ;
        }
      

        $JVType = 'Standard';
        $referenceno = $client_sales_data['order'];
        $customer_details = $this->get_customer_details(trim($client_sales_data['store_id'] ?? ''));


        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if($financeyearperiodYN==1) {
            $companyFinancePeriod = trim($companyFinanceYear);
            $period = explode(' - ', trim($companyFinancePeriod));
            $PeriodBegin = input_format_date($period[0], $date_format_policy);
            $PeriodEnd = input_format_date($period[1], $date_format_policy);

            $year = explode(' - ', trim($financeyear_period));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);

        }else{
            
            $financeYearDetails=get_financial_year($JVdate);
            if(empty($financeYearDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                $FYBegin=$financeYearDetails['beginingDate'];
                $FYEnd=$financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails=get_financial_period_date_wise($JVdate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                $PeriodBegin = $financePeriodDetails['dateFrom'];
                $PeriodEnd = $financePeriodDetails['dateTo'];
            }
        }

        $currency_code = explode('|', trim($currency_code));


        $data['documentID'] = 'JV';
        $data['JVType'] = trim($JVType);
        $data['JVdate'] = trim($JVdate);
        $JVNarration = ($referenceno);
        $data['JVNarration'] = str_replace('<br />', PHP_EOL, $JVNarration);
        //$data['JVNarration'] = trim_desc($this->input->post('JVNarration'));
        $data['referenceNo'] = trim($referenceno);
        $data['companyFinanceYearID'] = trim($financeYear);
        $data['companyFinanceYear'] = trim($companyFinanceYear);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($financeyear_period);
        $data['FYPeriodDateFrom'] = trim($PeriodBegin);
        $data['FYPeriodDateTo'] = trim($PeriodEnd);
        $data['transactionCurrencyID'] = $transaction_currency_id;
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

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('JVMasterAutoId') ?? '')) {
            $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
            $this->db->update('srp_erp_jvmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('JVMasterAutoId'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['JVcode'] = 0;

            $this->db->insert('srp_erp_jvmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_gl_detail($JVMasterAutoId,$client_sales_data,$client_mapping,$selected_gl_record)
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $master = $this->db->get('srp_erp_jvmaster')->row_array();


        foreach($selected_gl_record as $gl_record) {

        $projectExist = project_is_exist();
        $gl_text_type = $gl_record['gl_account_description'];
        $segment_gls = '86|GEN';
        $gl_code_des = $gl_record['gl_code'];
        $gl_code = $gl_record['gl_account_code'];
        $projectID = $this->input->post('projectID');
        $amount = $gl_record['final_value'];
        $description = $gl_record['descripiton'];
        $discountPercentage = $this->input->post('discountPercentage');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');

        
        $g_code_details = fetch_gl_account_from_systemAccountCode($gl_code,$this->common_data['company_data']['company_id']);
        $gl_code_des =  trim($g_code_details['systemAccountCode'] ?? '') . ' | ' . trim($g_code_details['GLSecondaryCode'] ?? '') . ' | ' . trim($g_code_details['GLDescription'] ?? '') . ' | ' . trim($g_code_details['subCategory'] ?? '');
        $gl_code_de = explode('|', $gl_code_des);

        $gl_codes = $g_code_details['GLAutoID'];
        /*$gl_types = $this->input->post('gl_type');*/
        $creditAmount = 0;
        $debitAmount = 0;
        $gl_type = '';

        if($gl_record['entry'] == 'cr'){
            $creditAmount = $amount;
            $gl_type = 'Cr';
        }elseif($gl_record['entry'] == 'dr'){
            $debitAmount = $amount;
            $gl_type = 'Dr';
        }
       
        $projectID = $this->input->post('projectID');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        
        if($gl_code) {
        // foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls);
            $gldata = fetch_gl_account_desc($gl_codes);

            // if ($gldata['masterCategory'] == 'PL') {
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');
            // } else {
            //     /*   $data[$key]['segmentID'] = trim($segment[0] ?? '');
            //        $data[$key]['segmentCode'] = trim($segment[1] ?? '');*/
            //     $data['segmentID'] = null;
            //     $data['segmentCode'] = null;
            // }

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $gl_des = explode('|', $gl_code_des);
            $data['JVMasterAutoId'] = trim($JVMasterAutoId);
            $data['GLAutoID'] = $gl_codes;
            $data['systemGLCode'] = trim($gl_des[0] ?? '');
            $data['GLCode'] = trim($gl_des[1] ?? '');
            $data['GLDescription'] = trim($gl_des[2] ?? '');
            $data['GLType'] = trim($gl_des[3] ?? '');
            $data['projectID'] = $projectID;
            $data['gl_type'] = $gl_type;
            
            $master['transactionCurrencyDecimalPlaces'] = ($master['transactionCurrencyDecimalPlaces']) ? $master['transactionCurrencyDecimalPlaces'] : 3;


            if ($data['gl_type'] == 'Cr') {
                $data['creditAmount'] = round($creditAmount, $master['transactionCurrencyDecimalPlaces']);
                $creditCompanyLocalAmount = $data['creditAmount'] / isset($master['companyLocalExchangeRate']) ? $master['companyLocalExchangeRate'] : 1;
                $data['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $creditCompanyReportingAmount = $data['creditAmount'] / isset($master['companyReportingExchangeRate']) ? $master['companyReportingExchangeRate'] : 1;
                $data['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data['debitAmount'] = 0;
                $data['debitCompanyLocalAmount'] = 0;
                $data['debitCompanyReportingAmount'] = 0;

               

                if($gldata['isBank']==1){
                    $data['isBank'] = 1;
                    $data['bankCurrencyID'] = $gldata['bankCurrencyID'];
                    $data['bankCurrency'] = $gldata['bankCurrencyCode'];
                    $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                    $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data['bankCurrencyAmount'] = $data[$key]['creditAmount'] / $bank_currency['conversion'];
                }else{
                    $data['isBank'] = 0;
                    $data['bankCurrencyID'] = null;
                    $data['bankCurrency'] = null;
                    $data['bankCurrencyExchangeRate'] = null;
                    $data['bankCurrencyAmount'] = null;
                }


            } else {


                $data['debitAmount'] = round($debitAmount, $master['transactionCurrencyDecimalPlaces']);
                $debitCompanyLocalAmount = $data['debitAmount'] / isset($master['companyLocalExchangeRate']) ? $master['companyLocalExchangeRate'] : 1;
                $data['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $debitCompanyReportingAmount = $data['debitAmount'] / isset($master['companyReportingExchangeRate']) ? $master['companyReportingExchangeRate'] : 1;
                $data['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data['creditAmount'] = 0;
                $data['creditCompanyLocalAmount'] = 0;
                $data['creditCompanyReportingAmount'] = 0;

                if($gldata['isBank']==1){
                    $data['isBank'] = 1;
                    $data['bankCurrencyID'] = $gldata['bankCurrencyID'];
                    $data['bankCurrency'] = $gldata['bankCurrencyCode'];
                    $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                    $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data['bankCurrencyAmount'] = $data['debitAmount'] / $bank_currency['conversion'];
                }else{
                    $data['isBank'] = 0;
                    $data['bankCurrencyID'] = null;
                    $data['bankCurrency'] = null;
                    $data['bankCurrencyExchangeRate'] = null;
                    $data['bankCurrencyAmount'] = null;
                }
            }
            $data['description'] = $description;
            $data['type'] = 'GL';

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $this->db->insert('srp_erp_jvdetail', $data);

            }
    
        }

         /*$last_id = $this->db->insert_id();*/
         $this->db->trans_complete();
         if ($this->db->trans_status() === FALSE) {
             $this->db->trans_rollback();
             return array('e', 'GL Description : Saved Failed ');
         } else {
             $this->db->trans_commit();
             return array('s', 'GL Description :  Saved Successfully.');
         }
      

    }

    function journal_entry_confirmation($JVMasterAutoId,$client_sales_data){
       
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $this->db->select('documentID, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $this->db->from('srp_erp_jvmaster');
        $master_dt = $this->db->get()->row_array();

        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationemp = $this->common_data['emplanglocationid'];

        $this->db->select('*');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $detl = $this->db->get('srp_erp_jvdetail')->row_array();

        if(empty($detl)){
            $this->session->set_flashdata('w', 'JV Detail can not be empty');
            return false;
        }

        $this->load->library('sequence');
        if($master_dt['JVcode'] == "0"){
            if($locationwisecodegenerate == 1) {
                $this->db->select('locationID');
                $this->db->where('EIdNo', $currentuser);
                $this->db->where('Erp_companyID', $companyID);
                $this->db->from('srp_employeesdetails');
                $location = $this->db->get()->row_array();
                if ((empty($location)) || ($location =='')) {
                    $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                    return false;
                }else {
                    if($locationemp!='') {
                        $jvcd = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'],$locationemp,$master_dt['invYear'],$master_dt['invMonth']);
                    } else {
                        $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                        return false;
                    }
                }
            }else {
                $jvcd = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
            }
            $validate_code = validate_code_duplication($jvcd, 'JVcode', $JVMasterAutoId,'JVMasterAutoId', 'srp_erp_jvmaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }
            $jvcd = array(
                'JVcode' => $jvcd
            );
            $this->db->where('JVMasterAutoId', $JVMasterAutoId);
            $this->db->update('srp_erp_jvmaster', $jvcd);
        } else {
            $validate_code = validate_code_duplication($master_dt['JVcode'], 'JVcode', $JVMasterAutoId,'JVMasterAutoId', 'srp_erp_jvmaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }
        }

        $this->load->library('Approvals');
        $this->db->select('documentID,JVMasterAutoId, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID,JVdate');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $this->db->from('srp_erp_jvmaster');
        $app_data = $this->db->get()->row_array();

        $this->db->select_sum('debitAmount');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $amount = $this->db->get('srp_erp_jvdetail')->row_array();

        $autoApproval= 0; //get_document_auto_approval('JV');
        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($app_data['JVMasterAutoId'], 'srp_erp_jvmaster','JVMasterAutoId', 'JV',$app_data['JVcode'],$app_data['JVdate']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('JV', $app_data['JVMasterAutoId'], $app_data['JVcode'], 'Journal Entry', 'srp_erp_jvmaster', 'JVMasterAutoId',0,$app_data['JVdate']);
        }else{
            $this->session->set_flashdata('e', 'Approval levels are not set for this document');
            return false;
        }

        if ($approvals_status==1) {
            $autoApproval= 0;//get_document_auto_approval('JV');
            if($autoApproval==0) {
                $result = $this->save_jv_approval(0, $app_data['JVMasterAutoId'], 1, 'Auto Approved');
                if($result){
                    $this->session->set_flashdata('s', 'Journel Voucher Created Successfully.');
                    return true;
                }
            }else{
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'transactionAmount' => $amount['debitAmount']
                );

                $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
                $this->db->update('srp_erp_jvmaster', $data);
                $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                return true;
            }
        }else if($approvals_status==3){
            /*$this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return true;*/
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return false;
        }
    }


    function save_jv_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('JVMasterAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['JVMasterAutoId']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();

        $JVDetails = $this->db->query('SELECT
                srp_erp_jvdetail.*,srp_erp_chartofaccounts.bankCurrencyID,srp_erp_chartofaccounts.bankCurrencyCode,srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,srp_erp_chartofaccounts.isBank,srp_erp_chartofaccounts.bankName
            FROM
                srp_erp_jvdetail
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_jvdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
            WHERE
                JVMasterAutoId = '.$system_code.'
            AND srp_erp_jvdetail.companyID= '.$companyID.'  ')->result_array();
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'JV');
        }

        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_journal_entry_data($system_code, 'JV');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['JVMasterAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['JVcode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['JVdate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['JVType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['JVdate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['JVdate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['description'];
                $generalledger_arr[$i]['chequeNumber'] = null;
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                // $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                // $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                // $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['transactionAmount'] = round($amount,3);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1), 3);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1), 3);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1), 3);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = $double_entry['gl_detail'][$i]['projectID'];
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }


            foreach($JVDetails as $val){
                if($val['isBank']==1){
                    if($val['gl_type']=='Cr'){
                        $transactionType=2;
                        $transactionAmount=$val['creditAmount'];
                    }else{
                        $transactionType=1;
                        $transactionAmount=$val['debitAmount'];
                    }
                    $bankledger['documentDate']=$double_entry['master_data']['JVdate'];
                    $bankledger['transactionType']=$transactionType;
                    $bankledger['transactionCurrencyID']=$double_entry['master_data']['transactionCurrencyID'];
                    $bankledger['transactionCurrency']=$double_entry['master_data']['transactionCurrency'];
                    $bankledger['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                    $bankledger['transactionAmount']=$transactionAmount;
                    $bankledger['transactionCurrencyDecimalPlaces']=$double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $bankledger['bankCurrencyID']=$val['bankCurrencyID'];
                    $bankledger['bankCurrency']=$val['bankCurrencyCode'];
                    $bankledger['bankCurrencyExchangeRate']=$val['bankCurrencyExchangeRate'];
                    $bankledger['bankCurrencyAmount']=$val['bankCurrencyAmount'];
                    $bankledger['bankCurrencyDecimalPlaces']=$val['bankCurrencyDecimalPlaces'];
                    $bankledger['memo']=$val['description'];
                    $bankledger['bankName']=$val['bankName'];
                    $bankledger['bankGLAutoID']=$val['GLAutoID'];
                    $bankledger['bankSystemAccountCode']=$val['systemGLCode'];
                    $bankledger['bankGLSecondaryCode']=$val['GLCode'];
                    $bankledger['documentMasterAutoID']=$val['JVMasterAutoId'];
                    $bankledger['documentType']='JV';
                    $bankledger['documentSystemCode']=$double_entry['master_data']['JVcode'];
                    $bankledger['createdPCID']=$this->common_data['current_pc'];
                    $bankledger['companyID']=$val['companyID'];
                    $bankledger['companyCode']=$val['companyCode'];
                    $bankledger['segmentID']=$val['segmentID'];
                    $bankledger['segmentCode']=$val['segmentCode'];
                    $bankledger['createdUserID']=current_userID();
                    $bankledger['createdDateTime']=current_date();
                    $bankledger['createdUserName']=current_user();
                    $this->db->insert('srp_erp_bankledger', $bankledger);

                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Journal entry Approval Successfully.');
            return true;
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function set_debit_note_entry_header($sales_id){

        $this->db->trans_start();

        $client_sales_data = $this->get_sales_client_record($sales_id);
        $client_mapping = $this->get_sales_client_credit_debit($sales_id,1,null,9);
        $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,1,null,9);
        $ecommerce_settings = get_ecommerce_setting_by_company($this->common_data['company_data']['company_id']);
        $selected_gl_code = $ecommerce_settings['supplier_gl_code'];
        $selected_gl_record = [];
        $value_exts = 0;

        if(isset($client_mapping_summary['data'])){
            foreach($client_mapping_summary['data'] as $value){
                if($value['control_acc'] != 1){
                    if($value['final_value'] != 0){
                        $value_exts = 1;
                        $value['final_value'] = abs($value['final_value']);
                        $selected_gl_record[] = $value;
                    }
                }
            }
        }

      

       // send back nothing generate
        if($value_exts == 0){
           add_process_log_record(1,'DN',$sales_id,2,'No records to generate Debit Note',7);
           return array('status'=>'error', 'message' => 'No records to generate Debit Note');
        }

        try{

            $debit_note_response =  $this->save_debit_note_invoice_header($sales_id,$client_sales_data,$client_mapping);

            $response = json_decode($debit_note_response,true);
           
            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_debitNote_detail_GLCode_multiple($response['last_id'],$client_sales_data,$client_mapping,$selected_gl_record);
               
                $response_confirmation = $this->dn_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data($sales_id,$response['last_id'],'dn_auto_id');

                add_process_log_record($response['last_id'],'DN',$sales_id,1,'Debit Note Created',7);

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Debit Note created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function save_posting_date_for_execute(){

        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $comments = $this->input->post('comments');
        $doc_id = $this->input->post('doc_id');
        $date = $this->input->post('date');
        $posting_id = $this->input->post('posting_id');
        $service_type = $this->input->post('service_type');
        $mode_collection = $this->input->post('mode_collection');
        $data = array();

        //Check for active mapping records
        $ex_mappings = $this->check_for_active_mapping_records($service_type,$mode_collection);

        if(empty($ex_mappings)){
            return $this->session->set_flashdata('e', 'No active mapping found.');
        }

        //Check for orders to post
        $orders = $this->get_orders_for_range_service_type($dateFrom,$dateTo,$service_type,$mode_collection);


        if(empty($orders)){
            $this->session->set_flashdata('e', 'No active orders found.');
            return FALSE;
        }


        $this->db->trans_start();

        if($doc_id){

            $data['doc_id'] = $doc_id;
            $data['date_from'] = $dateFrom;
            $data['date_to'] = $dateTo.' 23:59:59';
            $data['description'] = $comments;
            $data['company_id'] = $this->common_data['company_data']['company_id'];
            $data['type'] = 1; //1- Manual
            $data['added_date'] = $date;
            $data['service_type'] = $service_type;
            $data['mode_collection'] = $mode_collection;

            if($posting_id){
                $this->db->where('id',$posting_id)->update('srp_erp_ecommerce_system_posting', $data);
            }else{
                $this->db->insert('srp_erp_ecommerce_system_posting', $data);
            }

        }

        /*$last_id = $this->db->insert_id();*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->session->set_flashdata('e', 'Posting data save Failed ');
        } else {
            $this->db->trans_commit();
            if($posting_id){
                return $this->session->set_flashdata('s', 'Posting data updated successfully.');
            }
            
            return $this->session->set_flashdata('s', 'Posting data save success.');
        }
       
    }

    function process_daily_posting_list(){
        
        $id = $this->input->post('id');
        $posting_data = $this->get_posting_data_from_posting($id);

        $dateFrom = $posting_data['date_from'];
        $dateTo = $posting_data['date_to'];
        $posting_id = $posting_data['doc_id'];

        /*
            check posting within active date range
        */

        $financial_arr = get_financial_period_date_wise($dateFrom);

        if(!isset($financial_arr['isActive']) || $financial_arr['isActive'] != 1){
            $this->session->set_flashdata('w', "Financial Period is not active for this posting.");
            return array('status' => false);
            exit;
        }
    
        $orders = $this->get_orders_for_range($dateFrom,$dateTo);
        $supplier_ids = $this->get_orders_distinct_suppliers($dateFrom,$dateTo);
        $supplier_ids_3pl = $this->get_orders_distinct_suppliers_3pl($dateFrom,$dateTo);
        $posting_active = $this->get_company_posting_active();
        $number_of_orders = count($orders);
        $already_posted_orders = $this->check_for_already_posted_orders($orders);


        if(!$posting_active){
            $this->session->set_flashdata('e', "Posting not been active.");
            return array();
        }

        if(empty($supplier_ids)){
            $this->session->set_flashdata('e', "Supplier ids not present.");
            return array('status' => false);
        }


        $posting_type = $posting_active['posting_method'];


        if($posting_data && $posting_type == 1) {
            
            //Check consists for already posted data
            if($already_posted_orders){
                $this->session->set_flashdata('w', "Already posted orders in range, can't continue");
                return array('status' => true);
            }
            
            $base_arr = array('store_id'=>'','message'=>'','type'=>'');
            $errors = array();

            //Group Posting
            foreach($supplier_ids as $supplier){

                if($supplier && isset($supplier['store_id'])){

                    $supplier_details = $this->get_supplier_details(trim($supplier['store_id'] ?? ''));

                    if(empty($supplier_details)){
                        $this->session->set_flashdata('e', 'Supplier '.$supplier['store_id'].' not been created.');
                        $errors[] = array('store_id'=>$supplier['store_id'] ,'message'=>$supplier['store_id']. '- Supplier not been created.','type'=>'error');
                        continue;
                    }
                   
                }

                //Supplier Invoice
                try{

                    $supplier_response = $this->generate_group_supplier_invoice($supplier['store_id'],$dateFrom,$dateTo,$posting_id);

                    $customer_response = $this->generate_group_customer_invoice($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
              
                    $supplier_direct_receipt = $this->generate_group_direct_voucher($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
                
                    $supplier_jv = $this->generate_group_journel_voucher($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
                        
                   
                }catch(\Exception $e){
                    $this->session->set_flashdata('w', "Something went wrong in the Supplier invoice generation section");
                    return array('status' => false);
                }
              
            }

            foreach($supplier_ids_3pl as $supplier_3pl){

                if($supplier_3pl && isset($supplier_3pl['3pl_company_id'])){

                    $supplier_details = $this->get_supplier_details(trim($supplier_3pl['3pl_company_id'] ?? ''));

                    if(empty($supplier_details)){
                        $this->session->set_flashdata('e', '3PL Supplier '.$supplier_3pl['3pl_company_id'].' not been created.');
                        $errors[] = array('store_id'=>$supplier['3pl_company_id'] ,'message'=>$supplier['3pl_company_id']. '- 3PL Supplier not been created.','type'=>'error');
                        continue;
                    }
                   
                }

               
                if($supplier_3pl['3pl_company_id']){

                  try{

                   $supplier_3PL = $this->generate_group_3PL_supplier_invoice($supplier_3pl['3pl_company_id'],$dateFrom,$dateTo,$posting_id);
                
                   $supplier_3PL_cus = $this->generate_group_3PL_customer_invoice($supplier_3pl['3pl_company_id'],$dateFrom,$dateTo,$posting_id);
                     

                  }catch(\Exception $e){
                        $this->session->set_flashdata('w', "Something went wrong in the 3PL company invoice generation section");
                        return array('status' => false);
                  }
                  
                }

            }


            $posting_response = $this->update_posting_complete($posting_id,1,$number_of_orders,0,$number_of_orders);

            $this->session->set_flashdata('s', "Posting completed with  ".count($number_of_orders)." - Successefully processed orders");
            return array('status' => true);


        }elseif($posting_data && $posting_type == 2){

            $success_arr = array();
            $err_arr = array();
            $number_of_orders = count($orders);

            foreach($orders as $order){

                $sales_id = $order['id'];
                $PL_company_id = $order['3pl_company_id'];
                $erp_record_status = $order['erp_record_status'];
              
                if(in_array($erp_record_status , array(1,2))){
                    //1,2
                    $err_arr[] = $sales_id;
                } else {

                try {

                    if($sales_id){

                        //Generate Supplier Record
                        $response = $this->set_general_ledger_records($sales_id);
    
                        //Generate customer invoice
                        $customer_response = $this->set_general_ledger_customer($sales_id);

                       
                        //Direct receipt
                        $direct_receipt = $this->set_receiptvoucher_header($sales_id);
    
                        //Journel Voucher
                        $journel_voucher = $this->set_journal_entry_header($sales_id);
                        
    
                        if($PL_company_id){
    
                            $PL_vendor_invoice = $this->set_general_ledger_records_3PL($sales_id);
    
                            $PL_customer_invoice = $this->set_general_ledger_customer_3PL($sales_id);
    
                        }
    

                        //update posting ids
                        $res = $this->update_client_data_individual($sales_id,$posting_id);
    
                    }

                    $success_arr[] = $sales_id;

                }catch(Exception $e){
                    $err_arr[] = $sales_id;
                }

                }
                
            }

            if(count($err_arr) > 0 && $number_of_orders > count($err_arr)){
                  //   update posting complete
                  //  1 -- Completed
                  //  2 -- partially completed
                  //  3 -- Failed
                $posting_response = $this->update_posting_complete($posting_id,2,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('w', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               


            }elseif( $number_of_orders == count($err_arr)){
                $posting_response = $this->update_posting_complete($posting_id,3,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('e', "Posting failed.");
                return array('status' => false);
               

            }else {

                $posting_response = $this->update_posting_complete($posting_id,1,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('s', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               

            }
            

        }

    }

    function process_daily_posting_list_automate(){

        $id = $this->input->post('id');
        $posting_data = $this->get_posting_data_from_posting($id);

        $dateFrom = $posting_data['date_from'];
        $dateTo = $posting_data['date_to'];
        $posting_id = $posting_data['doc_id'];

        /*
            check posting within active date range
        */

        $financial_arr = get_financial_period_date_wise($dateFrom);

        if(!isset($financial_arr['isActive']) || $financial_arr['isActive'] != 1){
            $this->session->set_flashdata('w', "Financial Period is not active for this posting.");
            return array('status' => false);
            exit;
        }

        try {

            $supplier_ids = $this->get_orders_distinct_suppliers($dateFrom,$dateTo,$posting_id);
            $supplier_ids_3pl = $this->get_orders_distinct_suppliers_3pl($dateFrom,$dateTo,$posting_id);
            
            //Add suppliers
            foreach($supplier_ids as $supplier){
                $store_id = $supplier['store_id'];
                
                $add_res = $this->add_supplier_automated_record($posting_id,$store_id,'',1);
            }
    
            // 3pl list
            foreach($supplier_ids_3pl as $supplier){
                $store_id = $supplier['3pl_company_id'];
                
                $add_res = $this->add_supplier_automated_record($posting_id,'',$store_id,2);
            }

            // add change status for progress
            $posting_res = $this->set_action_system_posting($posting_id,'status',3);
            $posting_res = $this->set_action_system_posting($posting_id,'running_status_vendor',1);
            $posting_res = $this->set_action_system_posting($posting_id,'running_status_3pl',0);

            // add log record process started
            add_process_log_record($posting_id,'','',1,"$posting_id - started process by ",2,$posting_id);
    
            $this->session->set_flashdata('s', "Posting started running now, this will take some time to complete.");
            return array('status' => true);

        } catch(Exception $e){
            $this->session->set_flashdata('e', "Something went wrong, contact the system admin for more details.");
            return array('status' => false);
        }
       

    }


    function add_supplier_automated_record($posting_id,$store_id,$pl_company_id,$type){

        $data = array();

        $data['store_id'] = $store_id;
        $data['3pl_company_id'] = $pl_company_id;
        $data['date'] = $this->common_data['current_date'];
        $data['status'] = 0;
        $data['added_by'] = $this->common_data['current_user'];
        $data['posting_id'] = $posting_id;
        $data['process_status'] = 0;
        $data['type'] = $type;

        $res = $this->db->insert('srp_erp_ecommerce_supplier_process',$data);

        return $res;

    }
   

    function check_for_active_mapping_records($service_type,$mode_collection){

        $response = $this->db->from('srp_erp_ecommerce_posting')
            ->where('service_type',$service_type)
            ->where('mode_collection',$mode_collection)
            ->where('status',1)
            ->get()
            ->row_array();

        return $response;

    }

    ////////////////////////////////Cron processing ////////////////////////////////////////////////////////////////

    function process_daily_posting_supplier_list($supplier_arr, $id){
        
        //$id = $this->input->post('id');
        $posting_data = $this->get_posting_data_from_posting($id);

        $dateFrom = $posting_data['date_from'];
        $dateTo = $posting_data['date_to'];
        $posting_id = $posting_data['doc_id'];
        $service_type = $posting_data['service_type'];
        $mode_collection = $posting_data['mode_collection'];

        /*
            check posting within active date range
        */
        $financial_arr = get_financial_period_date_wise($dateFrom);

        if(!isset($financial_arr['isActive']) || $financial_arr['isActive'] != 1){
            $this->session->set_flashdata('w', "Financial Period is not active for this posting.");
            return array('status' => false);
            exit;
        }

        $posting_active = $this->get_company_posting_active($service_type,$mode_collection);


        if(!$posting_active){
            $res = add_process_log_record('','','',1,'posting not been active',2,$posting_id);
            return array();
        }
    
        $posting_type = $posting_active['posting_method'];

        if($posting_data && $posting_type == 1) {
            
            $base_arr = array('store_id'=>'','message'=>'','type'=>'');
            $errors = array();

            //Group Posting
            foreach($supplier_arr as $key => $supplier) {

                $supplier_id = $supplier['store_id'];
                

                if($supplier && isset($supplier['store_id'])){

                    $supplier_details = $this->get_supplier_details(trim($supplier['store_id'] ?? ''));

                    //check customer details

                    if(empty($supplier_details)){
                        $res = add_process_log_record($supplier['store_id'],'','',1,"$supplier_id not been exists",2,$posting_id);
                        $errors[] = array('store_id'=>$supplier['store_id'] ,'message'=>$supplier['store_id']. '- Supplier not been created.','type'=>'error');
                        return $errors;
                    }
                   
                }

                $supplier_name = $supplier_details['supplierName'];


                //Supplier Invoice
                try{

                     $supplier_response = $this->generate_group_supplier_invoice($supplier['store_id'],$dateFrom,$dateTo,$posting_id);

                     $customer_response = $this->generate_group_customer_invoice($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
              
                     $supplier_direct_receipt = $this->generate_group_direct_voucher($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
                
                     $supplier_jv = $this->generate_group_journel_voucher($supplier['store_id'],$dateFrom,$dateTo,$posting_id);

                     $debit_note = $this->generate_debit_note($supplier['store_id'],$dateFrom,$dateTo,$posting_id);
                    
                     $res = add_process_log_record($supplier['store_id'],'','',1,"$supplier_name Created from $dateFrom to $dateTo",1,$posting_id);

                }catch(\Exception $e){

                    $res = add_process_log_record('','','',1,'$supplier_name failed from $dateFrom to $dateTo',2,$posting_id);
                    return array('status' => false);
                }
              
            }

            // $posting_response = $this->update_posting_complete($posting_id,1,$number_of_orders,0,$number_of_orders);
            $response_arr = array('store_id'=>$supplier_id,'message'=>$supplier_name.' - Supplier record created.','type'=>'success');
            
            return $response_arr;


        } elseif($posting_data && $posting_type == 2){

            $success_arr = array();
            $err_arr = array();
            $number_of_orders = 0;//count($orders);

            foreach($supplier_arr as $supplier){

                $supplier_id = $supplier['store_id'];

                if($supplier && isset($supplier['store_id'])){

                    $supplier_details = $this->get_supplier_details(trim($supplier['store_id'] ?? ''));

                    //check customer details

                    if(empty($supplier_details)){
                        $res = add_process_log_record($supplier['store_id'],'','',1,"$supplier_id not been exists",2,$posting_id);
                        $errors[] = array('store_id'=>$supplier['store_id'] ,'message'=>$supplier['store_id']. '- Supplier not been created.','type'=>'error');
                        return $errors;
                    }
                   
                }

                $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
               

                foreach($orders_list as $order){

                    $sales_id = $order['id'];
                    $PL_company_id = $order['3pl_company_id'];
                    $erp_record_status = $order['erp_record_status'];

                    if(in_array($erp_record_status , array(1,2))){
                        //1,2
                        $err_arr[] = $sales_id;
                    } else {
    
                        try {
        
                            if($sales_id){
        
                                //Generate Supplier Invoice
                                $response = $this->set_general_ledger_records($sales_id);

                                //Generate Customer Invoice
                                $customer_response = $this->set_general_ledger_customer($sales_id);
        
                                //Direct receipt
                                $direct_receipt = $this->set_receiptvoucher_header($sales_id);
            
                                //Journel Voucher
                                $journel_voucher = $this->set_journal_entry_header($sales_id);

                                //Add debit note
                                $debit_note = $this->set_debit_note_entry_header($sales_id);

            
                                if($PL_company_id){
            
                                    $PL_vendor_invoice = $this->set_general_ledger_records_3PL($sales_id);
            
                                    $PL_customer_invoice = $this->set_general_ledger_customer_3PL($sales_id);
            
                                }

                                //update posting ids
                                $res = $this->update_client_data_individual($sales_id,$posting_id);
            
                            }
        
                            $success_arr[] = $sales_id;
        
                        }catch(Exception $e){
                            $err_arr[] = $sales_id;
                        }
    
                    }

                    $number_of_orders++;
                    
                }


            }

            

            if(count($err_arr) > 0 && $number_of_orders > count($err_arr)){
                  //   update posting complete
                  //  1 -- Completed
                  //  2 -- partially completed
                  //  3 -- Failed
                $posting_response = $this->update_posting_complete($posting_id,2,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('w', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               


            }elseif( $number_of_orders == count($err_arr)){
                $posting_response = $this->update_posting_complete($posting_id,3,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('e', "Posting failed.");
                return array('status' => false);
               

            }else {

                $posting_response = $this->update_posting_complete($posting_id,1,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('s', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               

            }
            

        }

    }

    function process_daily_posting_supplier_3pl_list($supplier_3pl_arr,$id){
        
        $posting_data = $this->get_posting_data_from_posting($id);

        $dateFrom = $posting_data['date_from'];
        $dateTo = $posting_data['date_to'];
        $posting_id = $posting_data['doc_id'];

        /*
            check posting within active date range
        */

        $financial_arr = get_financial_period_date_wise($dateFrom);

        if(!isset($financial_arr['isActive']) || $financial_arr['isActive'] != 1){
            $this->session->set_flashdata('w', "Financial Period is not active for this posting.");
            return array('status' => false);
            exit;
        }

        $posting_active = $this->get_company_posting_active();

        if(!$posting_active){
            $this->session->set_flashdata('e', "Posting not been active.");
            return array();
        }

        $posting_type = $posting_active['posting_method'];


        if($posting_data && $posting_type == 1) {
            
            //Check consists for already posted data
            $base_arr = array('store_id'=>'','message'=>'','type'=>'');
            $errors = array();

            foreach($supplier_3pl_arr as $supplier_3pl){

                if($supplier_3pl && isset($supplier_3pl['3pl_company_id'])){

                    $supplier_3pl = $supplier_3pl['3pl_company_id'];

                    $supplier_details = $this->get_supplier_details(trim($supplier_3pl));

                    if(empty($supplier_details)){
                        $re = add_process_log_record($supplier_3pl,'','',1,"$supplier_3pl not been exists",2,$posting_id);
                        $errors[] = array('store_id'=>$supplier_3pl ,'message'=> $supplier_3pl. '- 3PL Supplier not been created.','type'=>'error');
                        return $errors;
                    }
                   
                }

                $supplier_name = $supplier_details['supplierName'];
               
                if($supplier_3pl){

                  try{

                        $supplier_3PL = $this->generate_group_3PL_supplier_invoice($supplier_3pl,$dateFrom,$dateTo,$posting_id);
                        
                        $supplier_3PL_cus = $this->generate_group_3PL_customer_invoice($supplier_3pl,$dateFrom,$dateTo,$posting_id);

                        $res = add_process_log_record($supplier_3pl,'','',1,"3PL Company $supplier_name invoice Created from $dateFrom to $dateTo",1,$posting_id);
                        
                        return array('status' => true);

                  }catch(\Exception $e){
                        $res = add_process_log_record($supplier_3pl,'','',1,"$supplier_3pl invoice generate failed",2,$posting_id);
                        return array('status' => false);
                  }
                  
                }

            }
            
            return array('status' => true);


        }elseif($posting_data && $posting_type == 2){

        
            $success_arr = array();
            $err_arr = array();
            $number_of_orders = count($orders);

            foreach($orders as $order){

                $sales_id = $order['id'];
                $PL_company_id = $order['3pl_company_id'];
                $erp_record_status = $order['erp_record_status'];
              
                if(in_array($erp_record_status , array(1,2))){
                    //1,2
                    $err_arr[] = $sales_id;
                } else {

                try {

                    if($sales_id){

                        //Generate Supplier Record
                       $response = $this->set_general_ledger_records($sales_id);
    
                        //Generate customer invoice
                       $customer_response = $this->set_general_ledger_customer($sales_id);

                       // exit;
    
                        if($PL_company_id){
    
                            $PL_vendor_invoice = $this->set_general_ledger_records_3PL($sales_id);
    
                            $PL_customer_invoice = $this->set_general_ledger_customer_3PL($sales_id);
    
                        }
    
                        //Direct receipt
                        $direct_receipt = $this->set_receiptvoucher_header($sales_id);
    
                        //Journel Voucher
                        $journel_voucher = $this->set_journal_entry_header($sales_id);

                        //update posting ids
                        $res = $this->update_client_data_individual($sales_id,$posting_id);
    
                    }

                    $success_arr[] = $sales_id;

                }catch(Exception $e){
                    $err_arr[] = $sales_id;
                }

                }
                
            }

            if(count($err_arr) > 0 && $number_of_orders > count($err_arr)){
                  //   update posting complete
                  //  1 -- Completed
                  //  2 -- partially completed
                  //  3 -- Failed
                $posting_response = $this->update_posting_complete($posting_id,2,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('w', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               


            }elseif( $number_of_orders == count($err_arr)){
                $posting_response = $this->update_posting_complete($posting_id,3,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('e', "Posting failed.");
                return array('status' => false);
               

            }else {

                $posting_response = $this->update_posting_complete($posting_id,1,count($success_arr),count($err_arr),$number_of_orders);

                $this->session->set_flashdata('s', "Posting completed with ".count($err_arr)." - Errors and ".count($success_arr)." - Successefully processed orders");
                return array('status' => false);
               

            }
            

        }

    }

    function check_process_running($type = 'vendor'){
        
        if($type == 'vendor'){
            $response = $this->db->from('srp_erp_ecommerce_system_posting')->where('running_status_vendor',1)->get()->row_array();
        }else{
            $response = $this->db->from('srp_erp_ecommerce_system_posting')->where('running_status_3pl',1)->get()->row_array();
        }
        
        return $response;

    }

    function get_cron_supplier_process_data($posting_id,$type = 1){

        $response = $this->db->from('srp_erp_ecommerce_supplier_process')
                        ->where('posting_id',$posting_id)
                        ->where('process_status',0)
                        ->where('type',$type)
                        ->limit(15)
                        ->get()
                        ->result_array();

        return $response;
    }

    function update_cron_supplier_process($id){

        $data = array();

        $data['process_status'] = 1;

        return $this->db->where('id',$id)->update('srp_erp_ecommerce_supplier_process',$data);

    }


    //////////////////////////////////// Cron processing end //////////////////////////////////////////////////////////


    function generate_group_supplier_invoice($supplier_id,$dateFrom,$dateTo,$posting_id = null){

        $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_arr = array();
        $sales_id = '';

        $num = 0;
       
        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            // $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,1,$posting_id,1);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,1,$posting_id,1);
          
            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;
            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }


            if($credit_value !=  $debit_value){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }

            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + abs($record_gl['final_value']));
    
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'added');

            }

        }

        if(empty($base_arr)){
            add_process_log_record(0,'SUP',$sales_id,2,"Supplier Invoice Create Failed for $supplier_id, No data to create",1);
            return array('status'=>'error', 'message' => 'No data to create Supplier Invoice.');
        }

        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;
        $client_sales_data['service_type'] = 'TMDONE';
        $client_sales_data['discount'] = '0';
       
        $supplier_header_response =  $this->save_supplier_invoice_header($order_auto_id,$client_sales_data,$client_mapping);

        $response = json_decode($supplier_header_response,true);

        if($response && $response['last_id']){
            
             $response_expense_record = $this->save_supplier_expense_records($response['last_id'],$client_sales_data,$client_mapping,$base_arr);

             $response_confirmation = $this->supplier_invoice_confirmation($response['last_id'],$client_sales_data);

             $up_response = $this->update_client_data_group($success_arr,$error_arr,1,$response['last_id'],$posting_id);

             return array('status'=>'success', 'message' => 'Supplier invoice successfully created.');

        }

        $error_count = count($error_arr);
        $success_count = count($success_arr);

        if($error_count == 0){
            $this->session->set_flashdata('s', "Supplier Invoice Confirmed Successfully with . $error_count - Errors and $success_count - Added");
        }else{
            $this->session->set_flashdata('e', "Supplier Invoice Confirmed Successfully with . $error_count - Errors and $success_count - Added");
        }

    }

    function generate_group_customer_invoice($supplier_id,$dateFrom,$dateTo,$posting_id = null){

        $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_err = array();
        $sales_id = '';

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,2,$posting_id);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,2,$posting_id);
          
            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;

            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }


            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + abs($record_gl['final_value']));
    
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'added');

            }

        }
        
        if(empty($base_arr)){
            add_process_log_record(0,'CUS',$sales_id,2,"Customer Invoice Create Failed for $supplier_id, No data to create",2);
            return array('status'=>'error', 'message' => 'No data to create Customer Invoice.');
        }


        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;

        try{

            $response_header = $this->save_customer_invoice_header($sales_id,$client_sales_data,$client_mapping);

           //$response = array('last_id'=>2435);
            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){
    
                $response_expense_record = $this->save_direct_invoice_detail($response['last_id'],$client_sales_data,$client_mapping,$base_arr);

                $response_confirmation = $this->invoice_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data_group($success_arr,$error_arr,2,$response['last_id'],$posting_id);

                return array('status'=>'success', 'message' => 'Customer invoice successfully created.');

            }else{
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Customer invoice successfully created.');

        } catch (Exception $e){
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }

    }

    function generate_group_3PL_supplier_invoice($supplier_id,$dateFrom,$dateTo,$posting_id){
       
        $orders_list = $this->get_orders_for_supplier_3pl($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_err = array();

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,3,$posting_id);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,3,$posting_id);

            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;

            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }


            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + abs($record_gl['final_value']));
                        
                       
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;

                       
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>3,'message'=>'added');

            }

        }

        if(empty($base_arr)){
            add_process_log_record(0,'SUP',$sales_id,2,"3PL Supplier Invoice Create Failed for $supplier_id, No data to create",6);
            return array('status'=>'error', 'message' => 'No data to create Supplier Invoice.');
        }


        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;
        $client_sales_data['service_type'] = 'TMDONE';
        $client_sales_data['discount'] = '0';

        try{

           $response_header = $this->save_supplier_invoice_header($sales_id,$client_sales_data,$client_mapping,'3pl');

           $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_supplier_expense_records($response['last_id'],$client_sales_data,$client_mapping,$base_arr);
            
                $response_confirmation = $this->supplier_invoice_confirmation($response['last_id'],$client_sales_data);

                $up_response = $this->update_client_data_group($success_arr,$error_arr,3,$response['last_id'],$posting_id);

              //  add_process_log_record($response['last_id'],'BSI',$sales_id,1,'3PL Vendor Invoice Created',3);
                return array('status'=>'success', 'message' => '3PL Supplier invoice successfully created.');

            }else{

                add_process_log_record(0,'BSI',$sales_id,2,'3PL Vendor Invoice Failed',3);
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            
            }
            return array('status'=>'success', 'message' => '3PL Supplier invoice successfully created.');

        } catch (Exception $e){
            add_process_log_record(0,'BSI',$sales_id,2,$e->getMessage(),3);
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }

    }

    function generate_group_3PL_customer_invoice($supplier_id,$dateFrom,$dateTo,$posting_id){

        $orders_list = $this->get_orders_for_supplier_3pl($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_err = array();

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,4,$posting_id);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,4,$posting_id);

            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;

            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }


            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + ($record_gl['final_value']));
                        
                       
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;

                       
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>4,'message'=>'added');

            }

        }

        if(empty($base_arr)){
            add_process_log_record(0,'CUS',$sales_id,2,"3PL Customer Invoice Create Failed for $supplier_id, No data to create",6);
            return array('status'=>'error', 'message' => 'No data to create Customer Invoice.');
        }
        

        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;

        try{

            $response_header = $this->save_customer_invoice_header($sales_id,$client_sales_data,$client_mapping,'3PL_customer');

            $response = json_decode($response_header,true);

            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_direct_invoice_detail($response['last_id'],$client_sales_data,$client_mapping,$base_arr);

                $response_confirmation = $this->invoice_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data_group($success_arr,$error_arr,4,$response['last_id'],$posting_id);

               // add_process_log_record($response['last_id'],'CINV',$sales_id,1,'3PL Customer Invoice Created',4);
               return array('status'=>'success', 'message' => 'Customer invoice successfully created.');

            }else{
                add_process_log_record(0,'CINV',$sales_id,1,'3PL Customer Invoice Failed',4);
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Customer invoice successfully created.');

        } catch (Exception $e){
            add_process_log_record(0,'CINV',$sales_id,1,$e->getMessage(),4);
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }
    }

    function generate_group_direct_voucher($supplier_id,$dateFrom,$dateTo,$posting_id){

        $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_arr = array();
        $client_mapping = array();

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,5,$posting_id);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,5,$posting_id);

            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;

            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }


            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + abs($record_gl['final_value']));
                        
                       
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;

                       
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>4,'message'=>'added');

            }

        }

        if(empty($base_arr)){
            add_process_log_record(0,'RV',$sales_id,2,"Direct Receipt Create Failed for $supplier_id, No data to create",6);
            return array('status'=>'error', 'message' => 'No data to create Direct Receipt.');
        }


        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;

        try{

            $response_header = $this->save_receiptvoucher_header($order_auto_id,$client_sales_data,$client_mapping);

            $response = $response_header;

            if($response && isset($response['last_id'])){

               $response_expense_record = $this->save_direct_rv_detail($response['last_id'],$client_sales_data,$client_mapping,$base_arr);

                $response_confirmation = $this->receipt_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data_group($success_arr,$error_arr,5,$response['last_id'],$posting_id);

             //   add_process_log_record($response['last_id'],'RV',$sales_id,1,'Direct Receipt Created',5);
                return array('status'=>'success', 'message' => 'Direct Income invoice Receipt created.');

            }else{
                add_process_log_record(0,'RV',$sales_id,2,'Direct Receipt Created',5);
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Direct Income invoice Receipt created.');

        } catch (Exception $e){
            add_process_log_record(0,'RV',$sales_id,2,$e->getMessage(),5);
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        }

    }

    function generate_group_journel_voucher($supplier_id,$dateFrom,$dateTo,$posting_id){

        $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_arr = array();
        $client_mapping = array();

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,6,$posting_id);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,6,$posting_id);

            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;

            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }

            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + $record_gl['final_value']);
                        
                    }else{

                        $base_arr[$record_gl['gl_account_code']] = $record_gl;
                       
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>4,'message'=>'added');

            }

        }

        if(empty($base_arr)){
            add_process_log_record(0,'JV',$sales_id,2,"Journel Voucher Create Failed for $supplier_id, No data to create",6);
            return array('status'=>'error', 'message' => 'No data to create Journel Voucher.');
        }

        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;

        try{

            $response_header = $this->save_journal_entry_header($sales_id,$client_sales_data,$client_mapping);

            $response = $response_header;
           
            if($response && isset($response['last_id'])){

                $response_expense_record = $this->save_gl_detail($response['last_id'],$client_sales_data,$client_mapping,$base_arr);
               
                $response_confirmation = $this->journal_entry_confirmation($response['last_id'],$client_sales_data); 

                $up_response = $this->update_client_data_group($success_arr,$error_arr,6,$response['last_id'],$posting_id);

                //add_process_log_record($response['last_id'],'JV',$sales_id,1,'Journel Voucher Created',6);
                return array('status'=>'success', 'message' => 'Journel Voucher Receipt created.');

            }else{
                add_process_log_record($response['last_id'],'JV',$sales_id,2,'Journel Voucher Create Failed',6);
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }
            return array('status'=>'success', 'message' => 'Journel Voucher Receipt created.');

        } catch (Exception $e){

            add_process_log_record($response['last_id'],'JV',$sales_id,2,$e->getMessage(),6);
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        
        }


    }

    function generate_debit_note($supplier_id,$dateFrom,$dateTo,$posting_id){

        $orders_list = $this->get_orders_for_supplier($supplier_id,$dateFrom,$dateTo,$posting_id);
        $total_orders = count($orders_list);
        $base_arr = array();
        $error_arr = array();
        $success_arr = array();

        $num = 0;

        foreach($orders_list as $order){
            
            $sales_id = $order['id'];

            // $client_sales_data = $this->get_sales_client_record($sales_id);
            $client_mapping = $this->get_sales_client_credit_debit($sales_id,1,$posting_id,9);
            $client_mapping_summary = $this->get_sales_client_credit_debit_summary($sales_id,1,$posting_id,9);

            
          
            $selected_gl_record = array();
            $final_gl_arr = array();
            $credit_value = 0;
            $debit_value = 0;
            $balanced = 1;
            
            if(isset($client_mapping_summary['data'])){
                foreach($client_mapping_summary['data'] as $value){
                    if($value['control_acc'] != 1){
                        if($value['final_value'] != 0){
                            $value['final_value'] = abs($value['final_value']);
                            $selected_gl_record[] = $value;
                        }
                    }

                    if($value['entry'] == 'cr'){
                        $credit_value = $credit_value + $value['final_value'];
                    }else{
                        $debit_value = $debit_value + $value['final_value'];
                    }
                }
            }

            if(abs($credit_value) !=  abs($debit_value)){
                $balanced = 0;
                $error_arr[] = array('order_id'=>$order['id'],'invoice_type'=>1,'message'=>'Credit and Debit not balanced properly.');
            }

            if($balanced == 1){

                foreach($selected_gl_record as $record_gl){

                    if(isset($base_arr[$record_gl['gl_account_code']])){
    
                        $base_values = $base_arr[$record_gl['gl_account_code']];
    
                        $base_arr[$record_gl['gl_account_code']]['debit'] = $base_values['debit'] + $record_gl['debit'];
                        $base_arr[$record_gl['gl_account_code']]['credit'] = $base_values['credit'] + $record_gl['credit'];
                        $base_arr[$record_gl['gl_account_code']]['final_value'] = ($base_arr[$record_gl['gl_account_code']]['final_value'] + abs($record_gl['final_value']));
    
                    }else{
    
                        $base_arr[$record_gl['gl_account_code']] = $record_gl;
                    }
    
                }

                $success_arr[] = array('order_id'=>$order['id'],'invoice_type'=>9,'message'=>'debit note added');

            }

        }

        

        $order_auto_id  = 9;
        $vendor_type = 'supplier';

        $client_sales_data['date_time'] = $dateFrom;
        $client_sales_data['completed_time'] = $dateTo;
        $client_sales_data['order'] = $posting_id;
        $client_sales_data['store_id'] = $supplier_id;
        $client_sales_data['service_type'] = 'TMDONE';
        $client_sales_data['discount'] = '0';

        if(empty($base_arr)){
            add_process_log_record(0,'DN',$order_auto_id,2,"Debit Note Create Failed for $supplier_id, No data to create",7);
            return array('status'=>'error', 'message' => 'No data to create Debit Note.');
        }

        try{

            $debit_note_response =  $this->save_debit_note_invoice_header($order_auto_id,$client_sales_data,$client_mapping);

            $response = json_decode($debit_note_response,true);
    
            if($response && $response['last_id']){
    
                $responseGL = $this->save_debitNote_detail_GLCode_multiple($response['last_id'],$client_sales_data,$client_mapping,$base_arr);
    
                $up_response = $this->dn_confirmation($response['last_id'],$client_sales_data);
    
                $up_response = $this->update_client_data_group($success_arr,$error_arr,7,$response['last_id'],$posting_id);
    
                return array('status'=>'success', 'message' => 'Debit note created.');

            }else{
                add_process_log_record($response['last_id'],'DN',$sales_id,2,'Debit Note Create Failed',7);
                return array('status'=>'error', 'message' => isset($response['message']) ? $response['message'] : 'Something went wrong');
            }

        } catch (Exception $e){

            add_process_log_record($response['last_id'],'DN',$sales_id,2,$e->getMessage(),7);
            return array('status'=>'error', 'message' => 'Something went wrong 2');
        
        }

    }

    /// Functions area ///

    function create_mapping_base_arr($mapping_columns){

        $base_record_arr = array('data'=>array(),'total_credit_value'=>'','total_debit_value'=>'');

        foreach($mapping_columns as $value){

            if(!isset($base_arr[$value['gl_account_code']])){
                $base_arr = $base_arr[$value['gl_account_code']] = array();
            }

            $base_arr = array('client_header'=>'','segement'=>'','gl_code'=>'','descripiton'=>'','credit'=>'','debit'=>'','entry'=>'','store_id'=>'','currency'=>'','control_acc'=>'');
            $client_sales_header_id = $value['client_sales_header_id'];
            $client_sales_header_name = $value['client_sales_header'];
            // $sales_value = $record[$client_sales_header_name];

            $base_arr['client_header'] = $client_sales_header_name;
            $segment_details = $this->get_segement_detials($value['erp_segment_id']);
            if($segment_details){
                $base_arr['segement'] = $segment_details['companyCode'].'|'.$segment_details['segmentCode']; //
            }
            $gl_details = $this->get_gl_code_details($value['erp_gl_code']);
            if($gl_details){
                $base_arr['gl_code'] = $gl_details['systemAccountCode'].'|'.$gl_details['GLDescription']; //
            }
            $base_arr['descripiton'] = $value['erp_description'];
            $base_arr['store_id'] = '';
            $base_arr['currency'] = '';
            $base_arr['segment_id'] = $value['erp_segment_id'];
            $base_arr['control_acc'] = $value['control_acc'];
            $base_arr['mapping_type'] = $value['mapping_type'];
            $base_arr['gl_account_code'] = $gl_details['systemAccountCode'];
            $base_arr['gl_account_description'] = $gl_details['GLDescription'];

            // if($value['erp_cr_dr'] == 'cr'){
            //     $base_arr['credit'] = $sales_value;
            //     $total_credit_value += $sales_value;
            // }else{
            //     $base_arr['debit'] = $sales_value;
            //     $total_debit_value += $sales_value;
            // }
            $base_arr['entry'] = $value['erp_cr_dr'];

            $base_record_arr['data'][] = $base_arr;

        }

        return $base_record_arr;

    }

    function get_posting_data_from_posting($id){
        
        $response = $this->db->from('srp_erp_ecommerce_system_posting')
            ->where('id',$id)
            ->get()
            ->row_array();

        return $response;

    }

    function get_posting_data_from_posting_from_doc_id($doc_id){
        
        $response = $this->db->from('srp_erp_ecommerce_system_posting')
            ->where('doc_id',$doc_id)
            ->get()
            ->row_array();

        return $response;

    }

    function set_action_system_posting($posting_id,$key,$value){

        $data = array();
        $data[$key] = $value;
        $this->db->where('doc_id',$posting_id)->update('srp_erp_ecommerce_system_posting',$data);

    }

    function get_order_process_action_log($posting_id){

        $company_id = $this->common_data['company_data']['company_id'];

        $response = $this->db->from('srp_erp_ecommerce_error_log');
            $this->db->where('posting_id',$posting_id);
            $this->db->where('company_id',$company_id);
            $this->db->order_by('id','desc');
            $query = $this->db->get();
        
        return $query->result_array();;
    }

    function get_orders_for_range($from,$to){

        $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')
            ->where('date_time >=', $from)
            ->where('date_time <=', $to)
            ->get()
            ->result_array();

        return $response;
    }

    function get_orders_for_range_service_type($from,$to,$service_type,$mode_collection){

        $service_type = srp_posting_service_type_get($service_type);
        $mode_collection = srp_posting_mode_collection_get($mode_collection);

        $this->db->from('srp_erp_ecommerce_sales_clientdata')
            ->where('date_time >=', $from)
            ->where('date_time <=', $to.' 23:59:59')
            ->where('erp_record_status',0)
            ->where('service_type',$service_type);

        if($mode_collection != 'ALL'){
            $this->db->where('payment',$mode_collection);
        }
        
        $response = $this->db->get()->result_array();

        return $response;
    }


    function get_orders_for_range_supplier($from,$to,$store_id,$type = 1){

        $response = $this->db->from('srp_erp_ecommerce_sales_clientdata');

            if($type == 1){
                $this->db->where('store_id',$store_id);
            }else{
                $this->db->where('3pl_company_id',$store_id);
            }
            
            $this->db->where('date_time >=', $from)
            ->where('date_time <=', $to)
            ->get()
            ->result_array();

        return $response;
    }

    function get_orders_distinct_suppliers($from,$to,$posting_id = null){

        if($posting_id){

            $posting_details = $this->get_posting_data_from_posting_from_doc_id($posting_id);

            $service_type = srp_posting_service_type_get($posting_details['service_type']);
            $mode_collection = srp_posting_mode_collection_get($posting_details['mode_collection']);

            $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->select('store_id')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('erp_record_status',0)
                ->group_by('store_id')
                ->where('service_type',$service_type);

            if($mode_collection != 'ALL'){
                $this->db->where('payment',$mode_collection);
            }
            
            $response = $this->db->get()->result_array();

        }else{

            $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->select('store_id')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('erp_record_status',0)
                ->group_by('store_id')
                ->get()
                ->result_array();

        }

       

        return $response;
    }

    function get_orders_distinct_suppliers_3pl($from,$to,$posting_id = null){
        if($posting_id){

            $posting_details = $this->get_posting_data_from_posting_from_doc_id($posting_id);

            $service_type = srp_posting_service_type_get($posting_details['service_type']);
            $mode_collection = srp_posting_mode_collection_get($posting_details['mode_collection']);

            $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->select('3pl_company_id')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('erp_record_status',0)
                ->where('3pl_company_id is NOT NULL',NULL,FALSE)
                ->group_by('3pl_company_id')
                ->where('service_type',$service_type);

            if($mode_collection != 'ALL'){
                $this->db->where('payment',$mode_collection);
            }
            
            $response = $this->db->get()->result_array();

        }else{

            $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->select('3pl_company_id')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('erp_record_status',0)
                ->where('3pl_company_id is NOT NULL',NULL,FALSE)
                ->group_by('3pl_company_id')
                ->get()
                ->result_array();

        }
        

        return $response;
    }

    function get_orders_for_supplier($store_id,$from,$to,$posting_id = null){

        if($posting_id){

            $posting_details = $this->get_posting_data_from_posting_from_doc_id($posting_id);

            $service_type = srp_posting_service_type_get($posting_details['service_type']);
            $mode_collection = srp_posting_mode_collection_get($posting_details['mode_collection']);

            $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('store_id',$store_id)
                ->where('service_type',$service_type);

            if($mode_collection != 'ALL'){
                $this->db->where('payment',$mode_collection);
            }
            
            $response = $this->db->get()->result_array();
        }else{

            $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')
                ->where('date_time >=', $from)
                ->where('date_time <=', $to)
                ->where('store_id',$store_id)
                ->get()
                ->result_array();

        }

       

        return $response;

    }

    function get_orders_for_supplier_3pl($company_id,$from,$to){

        $response = $this->db->from('srp_erp_ecommerce_sales_clientdata')
            ->where('date_time >=', $from)
            ->where('date_time <=', $to)
            ->where('3pl_company_id',$company_id)
            ->get()
            ->result_array();

        return $response;

    }

    function update_client_data_group($success_arr,$err_arr,$invoice_type,$auto_id,$doc_id){

        // update success data
        foreach($success_arr  as $success_val){

            $data = array();
            $inv_type = 'SUP';

            if($invoice_type == 1){
                $message = 'Supplier invoice created successfully';
                $data['invoice_auto_id'] = $auto_id;
            }elseif($invoice_type == 2){
                $inv_type = 'CINV';
                $message = 'Customer invoice created successfully';
                $data['customer_auto_id'] = $auto_id;
            }elseif($invoice_type == 3){
                $message = '3PL supplier invoice created successfully';
                $data['3pl_vendor_auto_id'] = $auto_id;
            }elseif($invoice_type == 4){
                $inv_type = 'CINV';
                $message = '3PL Customer invoice created successfully';
                $data['3pl_customer_auto_id'] = $auto_id;
            }elseif($invoice_type == 5){
                $inv_type = 'RV';
                $message = 'Direct receipt created successfully';
                $data['direct_receipt_auto_id'] = $auto_id;
            }elseif($invoice_type == 6){
                $inv_type = 'JV';
                $message = 'Journel Voucher created successfully';
                $data['jv_auto_id'] = $auto_id;
            }elseif($invoice_type == 7){
                $inv_type = 'DN';
                $message = 'Debit note created successfully';
                $data['dn_auto_id'] = $auto_id;
            }

            $data['posting_id'] = $doc_id;
            $data['erp_record_status'] = 1;

            $this->db->where('id',$success_val['order_id']);
            $this->db->update('srp_erp_ecommerce_sales_clientdata',$data);

            add_process_log_record($auto_id, $inv_type,$success_val['order_id'],1,$message,1);
            
        }

        foreach($err_arr as $err_val){
            if($invoice_type == 1){
                add_process_log_record($auto_id,'SUP',$err_val['order_id'],2,'Supplier invoice create Failed',1);
            }elseif($invoice_type == 2){
                add_process_log_record($auto_id,'CINV',$err_val['order_id'],2,'Customer invoice create Failed',2);
            }elseif($invoice_type == 3){
                add_process_log_record($auto_id,'SUP',$err_val['order_id'],2,'3PL vendor invoice create Failed',3);
            }elseif($invoice_type == 4){
                add_process_log_record($auto_id,'CINV',$err_val['order_id'],2,'3PL customer invoice create Failed',4);
            }elseif($invoice_type == 5){
                add_process_log_record($auto_id,'RV',$err_val['order_id'],2,'Direct receipt invoice create Failed',5);
            }elseif($invoice_type == 6){
                add_process_log_record($auto_id,'JV',$err_val['order_id'],2,'Journel Vouceher create Failed',2);
            }
           
        }
       
    }

    function update_client_data_individual($sales_id,$doc_id){

        $data = array();

        if($doc_id){

            $data['posting_id'] = $doc_id;

            $this->db->where('id',$sales_id);
            $this->db->update('srp_erp_ecommerce_sales_clientdata',$data);

        }

        return TRUE;
       
    }

    function update_posting_complete($posting_id,$status,$completed,$failed,$number_of_records){
        
        $data = array();

        if($posting_id){

            $data['triggered_by_name'] = 'Admin';
            $data['triggered_by_id'] = '1';
            $data['status'] = $status;
            $data['total_records'] = $number_of_records;
            $data['success'] = $completed;
            $data['failed'] = $failed;
            $data['end_time'] = date('Y-m-d H:i:s');

            $this->db->where('doc_id',$posting_id);
            $this->db->update('srp_erp_ecommerce_system_posting',$data);

        }

        return TRUE;
    }   

 
    function get_company_posting_active($service_type = null,$mode_collection = null){

        $company_id = $this->common_data['company_data']['company_id'];

        if($service_type){
            $response = $this->db->from('srp_erp_ecommerce_posting')
                ->where('company_id',$company_id)
                ->where('status',1)
                ->where('service_type',$service_type)
                ->where('mode_collection',$mode_collection)
                ->get()
                ->row_array();
        }else{
            $response = $this->db->from('srp_erp_ecommerce_posting')
                ->where('company_id',$company_id)
                ->where('status',1)
                ->get()
                ->row_array();
        }
       
        return $response;

    }

    function check_for_already_posted_orders($orders){
        $already_posted = 0;

        foreach($orders as $order){
            $already_posted =  (in_array($order['erp_record_status'] ,array(1,2))) ? 1 : 0;

            if($already_posted == 1){
                return $already_posted;
            }
        }
    }

    function check_for_not_exists_suppliers($suppliers,$type){
        $error[] = array();
        foreach($suppliers as $supplier){
           
            if($type == 'supplier'){
                $supplier_details = $this->get_supplier_details(trim($supplier['store_id'] ?? ''));
                if(empty($supplier_details)){
                    $error[] = $supplier['store_id'];
                }
            }elseif($type == '3PL_supplier'){
                
                $supplier_details = $this->get_supplier_details(trim($supplier['3pl_company_id'] ?? ''));
                if(empty($supplier_details)){
                    array_push($error,$supplier['3pl_company_id']);
                }
            }
            
        }

        return $error;

    }

    function edit_order_manage(){

        $order_id = $this->input->post('order_id');
        $fields = $this->input->post('fields');
        $edit_fields = $this->input->post('edit_fields');
        $apply_for_all = $this->input->post('apply_for_all');

        $order_details = $this->get_sales_client_record($order_id);
        $data = array();
        $arr_all_updates = array();

        $store_id = $order_details['store_id'];

        $this->db->trans_start();

        foreach($fields as $key => $value){

            $str_apply = $value.'_apply_for_all';
            $post_value = $this->input->post("$str_apply");
          

            if($post_value[0] == 2){
                $order_details[$value] = $edit_fields[$key];
            }else{
                $order_details[$value] = $edit_fields[$key];

                //update array for all store updates
                $arr_all_updates[$value] = $edit_fields[$key];
            }
            
        }

        //update the exsiting record
        //update value
        $this->db->where('id',$order_id);
        $this->db->update('srp_erp_ecommerce_sales_clientdata', $order_details);

        //Add a history
        $res = $this->order_update_history($order_details);

        //update all stores
        $res = $this->order_update_all($arr_all_updates,$store_id,$order_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Update failed');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Successfully updated');
            $this->db->trans_commit();
            return array('status' => true);
        }

    }

    function order_update_all($data,$store_id,$order_id){

        if(count($data) == 0){
            return true;
        }

        //get_sales_client_records_from_store_id
        $orders_from_store = $this->get_sales_client_records_from_store_id($store_id);

        foreach($orders_from_store as $key => $value){
            
            $id = $value['id'];

            if($order_id != $id){

                foreach($data as $all_key => $all_value){
                    $orders_from_store[$key][$all_key] = $all_value;
                }
    
                $updated_arr = $orders_from_store[$key];
    
                //update value
                $this->db->where('id',$id);
                $this->db->update('srp_erp_ecommerce_sales_clientdata', $updated_arr);
    
                //make a history of edit
                //Add a history
                $res = $this->order_update_history($updated_arr);

            }

        }

        return TRUE;

    }

    function order_update_history($data){

        $data['updated_by'] = current_employee();
        $data['updated_date'] = current_date();
        $data['ip'] = current_pc();
        $data['type'] = 'edit';

        $result = $this->db->insert('srp_erp_ecommerce_sales_clientdata_history',$data);

        return TRUE;
    }

    function edit_order_filtered_all(){

        $fields = $this->input->post('fields');
        $edit_fields = $this->input->post('edit_fields');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $service_type = $this->input->post('service_type');
        $storeType = $this->input->post('storeType');
        $apply_for_all = 0;
        $stores_id = array();

        if($storeType == 'all'){
            $apply_for_all = 1;
        }else{
            $stores_id = explode(',',$storeType);
        }

        //get orders according to the filter
        $orders = get_orders_according_to_filters($datefrom,$dateto,$service_type,$stores_id,$apply_for_all);

        if(count($orders) > 2000){
            $this->session->set_flashdata('e', 'Order count exceeds the update limit of 2000 records per time, contact Admin.');
            return false;
        }

        try{

            foreach($orders as $order){

                $id = $order['id'];
    
                foreach($fields as $key => $value){
                    $order[$value] = (isset($edit_fields[$key]) && $edit_fields[$key] != '') ? $edit_fields[$key] : $order[$value];
                }
    
                //update value
                $this->db->where('id',$id);
                $this->db->update('srp_erp_ecommerce_sales_clientdata', $order);
    
                //updated history
                $res = $this->order_update_history($order);
              
            }

            $this->session->set_flashdata('s', 'Order update is successfully completed.');
            return true;


        }catch(Exception $e){
            $this->session->set_flashdata('e', 'Order update failed to complete.');
            return false;
        }

    }

}