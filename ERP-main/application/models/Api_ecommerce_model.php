<?php

class Api_ecommerce_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('erp_data_sync');
    }

    function get_order_detail($order_id)
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_ecommerce_sales_clientdata');
        $CI->db->where('order', $order_id);
        return $CI->db->get()->row_array();
    }

    function get_order_detail_by_systemcode($system_id){

        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_ecommerce_sales_clientdata');
        $CI->db->where('system_id', $system_id);
        return $CI->db->get()->row_array();

    }   

    function update_order_detail($value){

        $CI =& get_instance();

        try {

            $system_id = $value['SYSTEM_ID'];
            $order_id = $value['ORDER'];
            $date =  $this->date_format($value['DATE_TIME']);
            $completed_date = $this->date_format($value['COMPLETED_TIME']);
            $int_data = array();
            $store_id = '62198ca28cbfe50c400d';

            $order_detail = $this->get_order_detail_by_systemcode($system_id);

            $check_processed = $this->check_order_already_processed($system_id);

            if(empty($order_detail)){
                throw new Exception("$system_id Id Invalid, No order present.");
            }
    
            $int_data = [
                'company_name' => isset($value['COMPANY_NAME']) ? $value['COMPANY_NAME'] : $order_detail['company_name'],
                'service_type' => isset($value['SERVICE_TYPE']) ? $value['SERVICE_TYPE'] : $order_detail['service_type'],
                'customer' => isset($value['CUSTOMER']) ? $value['CUSTOMER'] : $order_detail['customer'],
                'customer_tel' => isset($value['CUSTOMER_TEL']) ? $value['CUSTOMER_TEL'] : $order_detail['customer_tel'],
                'zone' => isset($value['ZONE']) ? $value['ZONE'] : $order_detail['zone'],
                'date_time' =>  isset($date) ? $date : $order_detail['date_time'],
                'completed_time' => isset($completed_date) ? $completed_date : $order_detail['completed_time'],
                'payment' => isset($value['PAYMENT']) ? $value['PAYMENT'] : $order_detail['payment'],
                'cr_dr' => isset($value['CR_DR']) ? $value['CR_DR'] : $order_detail['cr_dr'],
                'order_total' => isset($value['ORDER_TOTAL']) ? $value['ORDER_TOTAL'] : $order_detail['order_total'],
                'delivery_fee' => isset($value['DELIVERY_FEE']) ? $value['DELIVERY_FEE'] : $order_detail['delivery_fee'],
                'actual_delivery_fee' => isset($value['ACTUAL_DELIVERY_FEE']) ? $value['ACTUAL_DELIVERY_FEE'] : $order_detail['actual_delivery_fee'],
                'municipality_tax' => isset($value['MUNICIPALITY_TAX']) ? $value['MUNICIPALITY_TAX'] : $order_detail['municipality_tax'],
                'municipality_tax_vat' => isset($value['MUNICIPALITY_TAX_VAT']) ? $value['MUNICIPALITY_TAX_VAT'] : $order_detail['municipality_tax_vat'],
                'tourism_tax' => isset($value['TOURISM_TAX']) ? $value['TOURISM_TAX'] : $order_detail['tourism_tax'],
                'tourism_tax_vat' => isset($value['TOURISM_TAX_VAT']) ? $value['TOURISM_TAX_VAT'] : $order_detail['tourism_tax_vat'],
                'vat_on_order' => isset($value['VAT_ON_ORDER']) ? $value['VAT_ON_ORDER'] : $order_detail['vat_on_order'],
                'vat_delivery_fee' => isset($value['VAT_ON_DELIVERY_FEE']) ? $value['VAT_ON_DELIVERY_FEE'] : $order_detail['vat_delivery_fee'],
                'total_bill' => isset($value['TOTAL_BILL']) ? $value['TOTAL_BILL'] : $order_detail['total_bill'],
                'discount' => isset($value['DISCOUNTS']) ? $value['DISCOUNTS'] : $order_detail['discount'],
                'credit' => isset($value['CREDITS']) ? $value['CREDITS'] : $order_detail['credit'],
                'net_vendor_bill' => isset($value['NET_VENDOR_BILL']) ? $value['NET_VENDOR_BILL'] : $order_detail['net_vendor_bill'],
                'net_collection' => isset($value['NET_COLLECTION']) ? $value['NET_COLLECTION'] : $order_detail['net_collection'],
                'adjustment_type' => isset($value['ADJUSTMENT_TYPE']) ? $value['ADJUSTMENT_TYPE'] : $order_detail['adjustment_type'],
                'adjustment_reason' => isset($value['ADJUSTMENT_REASON']) ? $value['ADJUSTMENT_REASON'] : $order_detail['adjustment_reason'],
                'total_adjustment' => isset($value['TOTAL_ADJUSTMENT']) ? $value['TOTAL_ADJUSTMENT'] : $order_detail['total_adjustment'],
                'tmdone_adjustment' => isset($value['TMDONE_ADJUSTMENT']) ? $value['TMDONE_ADJUSTMENT'] : $order_detail['tmdone_adjustment'],
                'vendor_adjustment' => isset($value['VENDOR_ADJUSTMENT']) ? $value['VENDOR_ADJUSTMENT'] : $order_detail['vendor_adjustment'],
                'driver_adjustment' => isset($value['DRIVER_ADJUSTMENT']) ? $value['DRIVER_ADJUSTMENT'] : $order_detail['driver_adjustment'],
                'gross_payable' => isset($value['GROSS_PAYABLE']) ? $value['GROSS_PAYABLE'] : $order_detail['gross_payable'],
                'commission_percentage' => isset($value['COMMSSION']) ? $value['COMMSSION'] : $order_detail['commission_percentage'],
                'fixed_commission' => isset($value['FIXED_COMMSSION']) ? $value['FIXED_COMMSSION'] : $order_detail['fixed_commission'],
                'commissionable_income' => isset($value['COMMISSIONABLE_INCOME']) ? $value['COMMISSIONABLE_INCOME'] : $order_detail['commissionable_income'],
                'tmdone_commission' => isset($value['TMDONE_COMMSSION']) ? $value['TMDONE_COMMSSION'] : $order_detail['tmdone_commission'],
                'vat_tmdone_commission' => isset($value['VAT_ON_TMDONE_COMMSSION']) ? $value['VAT_ON_TMDONE_COMMSSION'] : $order_detail['vat_tmdone_commission'],
                'bank_charges' => isset($value['BANK_CHARGES']) ? $value['BANK_CHARGES'] : $order_detail['bank_charges'],
                'bank_charges_vat' => isset($value['BANK_CHARGE_VAT']) ? $value['BANK_CHARGE_VAT'] : $order_detail['bank_charges_vat'],
                'vendor_settlement' => isset($value['VENDOR_SETTLEMENT']) ? $value['VENDOR_SETTLEMENT'] : $order_detail['vendor_settlement'],
                'card_payment_reference' => isset($value['CARD_PAYMNET_REF']) ? $value['CARD_PAYMNET_REF'] : $order_detail['card_payment_reference'],
                'driver_name' => isset($value['DRIVER_NAME']) ? $value['DRIVER_NAME'] : $order_detail['driver_name'],
                'driver_id' => isset($value['DRIVER_ID']) ? $value['DRIVER_ID'] : $order_detail['driver_id'],
                'points_redeemed' => isset($value['POINTS_REDEEMED']) ? $value['POINTS_REDEEMED'] : $order_detail['points_redeemed'],
                'cash_collected' => isset($value['CASH_COLLECTED']) ? $value['CASH_COLLECTED'] : $order_detail['cash_collected'],
                'credit_card' => isset($value['CREDIT_CARD']) ? $value['CREDIT_CARD'] : $order_detail['credit_card'],
                'tm_credits' => isset($value['TM_CREDITS']) ? $value['TM_CREDITS'] : $order_detail['tm_credits'],
                '3pl_company_id' => $this->update_for_similar_params($value,$order_detail,'3PL_COMPANY_ID'),
                'tm_done_driver_id' => isset($value['TM_DRIVER_ID']) ? $value['TM_DRIVER_ID'] : $order_detail['tm_done_driver_id'],
                'delivery_cost' => isset($value['DELIVERY_COST']) ? $value['DELIVERY_COST'] : $order_detail['delivery_cost'],
                'drop_fee' => isset($value['DROP_FEE']) ? $value['DROP_FEE'] : $order_detail['drop_fee'],
                'receivable_balance' => isset($value['RECEIVABLE_BALANCE']) ? $value['RECEIVABLE_BALANCE'] : $order_detail['receivable_balance'],
                'item_code' => isset($value['ITEM_CODE']) ? $value['ITEM_CODE'] : null,
                'tablet_fee' => isset($value['TABLET_FEE']) ? $value['TABLET_FEE'] : null,
                'renewal_fee' => isset($value['RENEWAL_FEE']) ? $value['RENEWAL_FEE'] : null,
                'registration_fee' => isset($value['REGISTRATION_FEE']) ? $value['REGISTRATION_FEE'] : null,
                'grouping' => isset($value['GROUPING']) ? $value['GROUPING'] : null,
                'campaign_fee' => isset($value['CAMPAIGN_FEE']) ? $value['CAMPAIGN_FEE'] : null,
                'refunds' => isset($value['REFUNDS']) ? $value['REFUNDS'] : null,
                'other' => isset($value['OTHER']) ? $value['OTHER'] : null,
                'num_updated' => $order_detail['num_updated'] + 1,
                'last_updated' => date('Y-m-d H:i:s'),
                'erp_record_receive_date' => date('Y-m-d H:i:s'),
                'param'=>json_encode($value)
            ];

            $res = $CI->db->where('system_id',$system_id)->update('srp_erp_ecommerce_sales_clientdata',$int_data);

            return array('status'=>'success');

        } catch(Exception $e){
            return array('status'=>'error',"message" => $e->getMessage());
        }
      
    }

    function update_for_similar_params($value,$order_detail,$index){
        // isset($value['3PL_COMPANY_ID']) ? $value['3PL_COMPANY_ID'] : $order_detail['3pl_company_id'],

        if(isset($value[$index])){
            return $value[$index];
        }else{

            if($index == '3PL_COMPANY_ID'){
                if(isset($value['PL_COMPANY_ID'])){
                    return $value['PL_COMPANY_ID'];
                }else{
                    return $order_detail['3pl_company_id'];
                }
            }

        }

    }

    function update_order_settlement_detail($value){

        $CI =& get_instance();

        try {

            $system_id = $value['SYSTEM_ID'];
            $order_id = $value['ORDER'];
            $date =  $this->date_format($value['DATE_TIME']);
            $completed_date = $this->date_format($value['COMPLETED_TIME']);
            $int_data = array();

            $order_detail = $this->get_order_detail_by_systemcode($system_id);

            $check_processed = $this->check_order_already_processed($system_id);

            if(empty($order_detail)){
                throw new Exception("$system_id Id Invalid, No order present.");
            }
    
            $int_data = [
                'system_id' => $system_id,
                'company_name' => $this->check_for_exceptions($value,'COMPANY_NAME'),
                'service_type' => $this->check_for_exceptions($value,'SERVICE_TYPE'),
                'store_id' =>  $this->check_for_exceptions($value,'STORE_ID'),
                'store' => $this->check_for_exceptions($value,'STORE'),
                'order' => $this->check_for_exceptions($value,'ORDER'),
                'date_time' =>  $date,
                'completed_time' => $completed_date,
                'status' => $this->check_for_exceptions($value,'STATUS'),
                'currency' => $this->check_for_exceptions($value,'CURRENCY'),
                '3pl_company_id' => isset($value['3PL_COMPANY_ID']) ? $value['3PL_COMPANY_ID'] : null,
                'item_code' => isset($value['ITEM_CODE']) ? $value['ITEM_CODE'] : null,
                'tablet_fee' => (isset($value['TABLET_FEE']) && ($value['TABLET_FEE'] > 0)) ? $value['TABLET_FEE'] : null,
                'tablet_fee_settlement' => (isset($value['TABLET_FEE']) && ($value['TABLET_FEE'] < 0 )) ? abs($value['TABLET_FEE']) : null,
                'renewal_fee' => (isset($value['RENEWAL_FEE']) && ($value['RENEWAL_FEE'] > 0)) ? $value['RENEWAL_FEE'] : null,
                'renewal_fee_settlement' => (isset($value['RENEWAL_FEE']) && ($value['RENEWAL_FEE'] < 0 )) ? abs($value['RENEWAL_FEE']) : null,
                'registration_fee' => (isset($value['REGISTRATION_FEE']) && ($value['REGISTRATION_FEE'] > 0)) ? $value['REGISTRATION_FEE'] : null,
                'registration_fee_settlement' => (isset($value['REGISTRATION_FEE']) && ($value['REGISTRATION_FEE'] < 0 )) ? abs($value['REGISTRATION_FEE']) : null,
                'grouping' => isset($value['GROUPING']) ? $value['GROUPING'] : null,
                'campaign_fee' => (isset($value['CAMPAIGN_FEE']) && ($value['CAMPAIGN_FEE'] > 0)) ? $value['CAMPAIGN_FEE'] : null,
                'campaign_fee_settlement' => (isset($value['CAMPAIGN_FEE']) && ($value['CAMPAIGN_FEE'] < 0 )) ? abs($value['CAMPAIGN_FEE']) : null,
                'refunds' => isset($value['REFUNDS']) ? $value['REFUNDS'] : null,
                'other' => isset($value['OTHER']) ? $value['OTHER'] : null,
                'erp_record_receive_date' => date('Y-m-d H:i:s'),
                'param'=>json_encode($value)
            ];


            $res = $CI->db->where('system_id',$system_id)->update('srp_erp_ecommerce_sales_clientdata',$int_data);

            return array('status'=>'success');

        } catch(Exception $e){
            return array('status'=>'error',"message" => $e->getMessage());
        }
       

    }

    public function check_for_exceptions($arr , $index){

        if(isset($arr[$index])){
            return $arr[$index];
        }else{
            throw new Exception("Missing $index in values list");
        }

    }

    function date_format($date){

        $date_arr = explode(' ',$date);
        $date_time = $date_arr[1].' '.$date_arr[2];
        $time_in_24_hour_format  = date("H:i:s", strtotime("$date_time"));
        $final_date_time = date('Y-m-d' , strtotime($date_arr[0])).' '.$time_in_24_hour_format;

        return $final_date_time;

    }

    function add_supplier_detail($supplier_details,$company_info,$type){

        $this->load->library('sequence');

        $CI =& get_instance();

        $vendor_id = isset($supplier_details['Store_Id'])? $supplier_details['Store_Id'] : null;
        $pl_company_id = isset($supplier_details['3PL_Company_Id'])? $supplier_details['3PL_Company_Id'] : null;
        $supplier_type = isset($supplier_details['Supplier_Type'])? $supplier_details['Supplier_Type'] : null;
        $company_name = isset($supplier_details['Company_Name'])? $supplier_details['Company_Name'] : null;
        $name_on_cheque = (isset($supplier_details['Name_On_Cheque']) && $supplier_details['Name_On_Cheque'] != '')? $supplier_details['Name_On_Cheque'] : $supplier_details['Company_Name'];
        $country = isset($supplier_details['Country'])? $supplier_details['Country'] : null;
        $location = isset($supplier_details['Location'])? $supplier_details['Location'] : null;
        $tel = isset($supplier_details['Telephone'])? $supplier_details['Telephone'] : null;
        $email = isset($supplier_details['Email'])? $supplier_details['Email'] : null;
        $web_url = isset($supplier_details['Web_URL'])? $supplier_details['Web_URL'] : null;
        $fax = isset($supplier_details['Fax'])? $supplier_details['Fax'] : null;
        $vat_no = isset($supplier_details['Vat_No'])? $supplier_details['Vat_No'] : null;
        $address1 = isset($supplier_details['Address1']) ? $supplier_details['Address1'] : null;
        $address2 = isset($supplier_details['Address2']) ? $supplier_details['Address2'] : null;
        $currency = isset($supplier_details['Currency']) ? $supplier_details['Currency'] : 'OMR';
        $company_id = $company_info['company_id'];
        $company_code = $company_info['company_code'];
        $currentTime = $this->current_time();
        $generated_code = '';

        //Bank account details
        $bank_name = isset($supplier_details['Bank_Name']) ? $supplier_details['Bank_Name'] : '';
        $bank_currency = isset($supplier_details['Bank_Currency']) ? $supplier_details['Bank_Currency'] : '';
        $account_name = isset($supplier_details['Account_Name']) ? $supplier_details['Account_Name'] : '';
        $account_number = isset($supplier_details['Account_Number']) ? $supplier_details['Account_Number'] : '';
        $swift_code = isset($supplier_details['SWIFT_Code']) ? $supplier_details['SWIFT_Code'] : '';
        $iban_code = isset($supplier_details['IBAN_Code']) ? $supplier_details['IBAN_Code'] : '';
        $bank_address = isset($supplier_details['Bank_Address']) ? $supplier_details['Bank_Address'] : '';

        try{

            if($supplier_type == 'Vendor'){
                if(!$vendor_id){
                    throw new Exception("Store Id is not exists");
                }
                $store_id = $vendor_id;
            }elseif($supplier_type == '3PL'){
                if(!$pl_company_id){
                    throw new Exception("3PL comapny id is not exists");
                }
                $store_id = $pl_company_id;
            }else{
                throw new Exception("Suppler type is invalid");
            }

            // $company_id = $compan
            // $settings = get_clent_ecommerce_settings($company_id);

            // if(empty($settings)){
            //     throw new Exception('Client settings not been added to this company.');
            // }

            $gl_secondary_code = 'AP0001';
           // $liablity_gl_code = $settings['supplier_liability_gl_code'];

            $liability = get_chartofaccounts_by_secondarycode($gl_secondary_code,$company_id);

            if(!$liability){ throw new Exception("Liability chart of account not been set in the system."); }

            $ex_store_detail = get_supplier_master_by_secondary_code($store_id);
            $response = array();
            $data = array();

            if($ex_store_detail && $type == 'insert'){
                throw new Exception('Store is already been created.');
            }

            if($type == 'insert'){
                $data['secondaryCode'] = $store_id;
                $data['supplierSystemCode'] =  $generated_code = $this->sequence->sequence_generator_mobile('SUP', '', $company_id, $company_code, '0', '1693', 'Admin', 'Api', $currentTime);
            }
            
            $data['supplierTelephone'] = $tel;
            $data['supplierEmail'] = $email;
            $data['supplierName'] = $company_name;
            $data['supplierUrl'] = $web_url;
            $data['supplierFax'] = $fax;
            $data['vatNumber'] = $vat_no;
            $data['supplierAddress1'] = $address1;
            $data['supplierAddress2'] = $address2;
            $data['nameOnCheque'] = $name_on_cheque;
            $data['isActive'] = 1;

            $data['masterApprovedYN'] = 1;
            $data['companyCode'] = $company_info['company_code'];
            $data['companyID'] = $company_info['company_id'];

            $data['supplierCurrencyID'] = 1;
            $data['supplierCurrency'] = $currency;
            $data['suppliercountryID'] = 166;
            $data['supplierCountry'] = 'Oman';
            $data['partyCategoryID'] = '14';

            $data['liabilityAutoID'] = $liability['GLAutoID'];
            $data['liabilitySystemGLCode'] = $liability['systemAccountCode'];
            $data['liabilityGLAccount'] = $liability['GLSecondaryCode'];
            $data['liabilityDescription'] = $liability['GLDescription'];
            $data['liabilityType'] = $liability['subCategory'];

          
            
            $this->db->trans_start();
            if($type == 'insert'){
                $this->db->insert('srp_erp_suppliermaster', $data);

                // update bank details
                $data['bank_name'] = $bank_name;
                $data['bank_address'] = $bank_address;
                $data['bank_currency'] = $bank_currency;
                $data['account_name'] = $account_name;
                $data['account_number'] = $account_number;
                $data['swift_code'] = $swift_code;
                $data['iban_code'] = $iban_code;

                //create bank detail
                $supplierAutoID = $this->db->insert_id();

                $res = $this->add_bank_detail($supplierAutoID,$data,$company_info);


            }else{
                $this->db->where('secondaryCode',$store_id);
                $this->db->update('srp_erp_suppliermaster', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('status'=>'error','message'=>'Supplier : ' . $data['supplierName'] . ' Insert Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('status'=>'success','message'=>'Supplier : ' . $data['supplierName']. ' Added Successfully','system_id'=>$generated_code );
                $this->db->trans_commit();
            }

        } catch(\Exception $e){
            return array('status'=>'error','message'=>$e->getMessage());
        }

    }

    function add_customer_detail($supplier_details,$company_info,$type){
       
        $this->load->library('sequence');

        $CI =& get_instance();

        $vendor_id = isset($supplier_details['Store_Id'])? $supplier_details['Store_Id'] : null;
        $pl_company_id = isset($supplier_details['3PL_Company_Id'])? $supplier_details['3PL_Company_Id'] : null;
        $supplier_type = isset($supplier_details['Supplier_Type'])? $supplier_details['Supplier_Type'] : null;
       
        $company_name = isset($supplier_details['Company_Name'])? $supplier_details['Company_Name'] : null;
        $name_on_cheque = isset($supplier_details['Name_On_Cheque'])? $supplier_details['Name_On_Cheque'] : null;
        $country = isset($supplier_details['Country'])? $supplier_details['Country'] : null;
        $location = isset($supplier_details['Location'])? $supplier_details['Location'] : null;
        $tel = isset($supplier_details['Telephone'])? $supplier_details['Telephone'] : null;
        $email = isset($supplier_details['Email'])? $supplier_details['Email'] : null;
        $web_url = isset($supplier_details['Web_URL'])? $supplier_details['Web_URL'] : null;
        $fax = isset($supplier_details['Fax'])? $supplier_details['Fax'] : null;
        $vat_no = isset($supplier_details['Vat_No'])? $supplier_details['Vat_No'] : null;
        $address1 = isset($supplier_details['Address1'])? $supplier_details['Address1'] : null;
        $address2 = isset($supplier_details['Address2'])? $supplier_details['Address2'] : null;
        $currency = isset($supplier_details['Currency'])? $supplier_details['Currency'] : 'OMR';
        $company_id = $company_info['company_id'];
        $company_code = $company_info['company_code'];
        $currentTime = $this->current_time();


        try{

            if($supplier_type == 'Vendor'){
                $store_id = $vendor_id;
            }elseif($supplier_type == '3PL'){
                $store_id = $pl_company_id;
            }else{
                throw new Exception("Suppler type is invalid");
            }

            $liablity_gl_code = 'AR0001';//$settings['customer_receivable_gl_code'];

            $liability = get_chartofaccounts_by_secondarycode($liablity_gl_code,$company_id);
            
            if(empty($liability)){
                throw new Exception("Receivable chart of account not been set in the system.");
            }

            $ex_store_detail = get_customer_master_by_secondary_code($store_id);
            $response = array();
            $data = array();
            
            if($ex_store_detail && $type == 'insert'){
                throw new Exception('Store is already been created.');
            }


            if($type == 'insert'){
                $data['secondaryCode'] = $store_id;
                $data['customerSystemCode'] =  $this->sequence->sequence_generator_mobile('CUS', '', $company_id, $company_code, '0', '1693', 'Admin', 'Api', $currentTime);
            }
            
            $data['isActive'] = 1;
            $data['customerTelephone'] = $tel;
            $data['customerEmail'] = $email;
            $data['customerName'] = $company_name;
            $data['customerUrl'] = $web_url;
            $data['customerFax'] = $fax;
            $data['vatNumber'] = $vat_no;
            $data['customerAddress1'] = $address1;
            $data['customerAddress2'] = $address2;

            $data['companyCode'] = $company_info['company_code'];
            $data['companyID'] = $company_info['company_id'];

            $data['customerCurrencyID'] = 1;
            $data['customerCurrency'] = $currency;
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
            $data['customerCountryID'] = 166;
            $data['customerCountry'] = 'Oman';
            $data['partyCategoryID'] = '13';


            $data['receivableAutoID'] = $liability['GLAutoID'];
            $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
            $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
            $data['receivableDescription'] = $liability['GLDescription'];
            $data['receivableType'] = $liability['subCategory']; 

            $this->db->trans_start();

            if($type == 'insert'){
                $this->db->insert('srp_erp_customermaster', $data);
            }else{
                $this->db->where('secondaryCode',$store_id);
                $this->db->update('srp_erp_customermaster', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('status'=>'error','message'=>'Customer : ' . $data['customerName'] . ' Insert Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('status'=>'success','message'=>'Customer : ' . $data['customerName']. ' Added Successfully');
                $this->db->trans_commit();
            }

        } catch(\Exception $e){
            return array('status'=>'error','message'=>'Something went wrong.');
        }
        

    }

    function update_supplier_detail($supplier_details,$company_info){
        
        $this->load->library('sequence');

        $CI =& get_instance();

        $company_name = isset($supplier_details['Company_Name'])? $supplier_details['Company_Name'] : null;
        $name_on_cheque = isset($supplier_details['Name_On_Cheque'])? $supplier_details['Name_On_Cheque'] : null;
        $country = isset($supplier_details['Country'])? $supplier_details['Country'] : null;
        $location = isset($supplier_details['Location'])? $supplier_details['Location'] : null;
        $tel = isset($supplier_details['Telephone'])? $supplier_details['Telephone'] : null;
        $email = isset($supplier_details['Email'])? $supplier_details['Email'] : null;
        $web_url = isset($supplier_details['Web_URL'])? $supplier_details['Web_URL'] : null;
        $fax = isset($supplier_details['Fax'])? $supplier_details['Fax'] : null;
        $vat_no = isset($supplier_details['Vat_No'])? $supplier_details['Vat_No'] : null;
        $address1 = isset($supplier_details['Address1']) ? $supplier_details['Address1'] : null;
        $address2 = isset($supplier_details['Address2']) ? $supplier_details['Address2'] : null;
        $currency = isset($supplier_details['Currency']) ? $supplier_details['Currency'] : 'OMR';
        $company_id = $company_info['company_id'];
        $company_code = $company_info['company_code'];
        $currentTime = $this->current_time();
        $generated_code = '';

        // Bank details
        $bank_name = isset($supplier_details['Bank_Name']) ? $supplier_details['Bank_Name'] : '';
        $bank_currency = isset($supplier_details['Bank_Currency']) ? $supplier_details['Bank_Currency'] : '';
        $account_name = isset($supplier_details['Account_Name']) ? $supplier_details['Account_Name'] : '';
        $account_number = isset($supplier_details['Account_Number']) ? $supplier_details['Account_Number'] : '';
        $swift_code = isset($supplier_details['SWIFT_Code']) ? $supplier_details['SWIFT_Code'] : '';
        $iban_code = isset($supplier_details['IBAN_Code']) ? $supplier_details['IBAN_Code'] : '';
        $bank_address = isset($supplier_details['Bank_Address']) ? $supplier_details['Bank_Address'] : '';

        try{
            $system_id = isset($supplier_details['System_Id'])? $supplier_details['System_Id'] : null;

            if(!$system_id){
                throw new Exception("Store Id is not exists");
            }

            $supplier_record = get_supplier_master_by_system_code($system_id);

            if(!$supplier_record){
                throw new Exception("No Supplier exists for this Id : $system_id");
            }

            $secondaryCode = $supplier_record['secondaryCode'];
           
            $data['supplierTelephone'] = $tel;
            $data['supplierEmail'] = $email;
            $data['supplierName'] = $company_name;
            $data['supplierUrl'] = $web_url;
            $data['supplierFax'] = $fax;
            $data['vatNumber'] = $vat_no;
            $data['supplierAddress1'] = $address1;
            $data['supplierAddress2'] = $address2;
            $data['nameOnCheque'] = $name_on_cheque;
            $data['isActive'] = 1;

            // update bank details
            // $data['bank_name'] = $bank_name;
            // $data['bank_address'] = $bank_address;
            // $data['bank_currency'] = $bank_currency;
            // $data['account_name'] = $account_name;
            // $data['account_number'] = $account_number;
            // $data['swift_code'] = $swift_code;
            // $data['iban_code'] = $iban_code;

            $this->db->trans_start();

            $this->db->where('supplierSystemCode',$system_id);
            $this->db->update('srp_erp_suppliermaster', $data);


            //update customer details
            $res = $this->update_customer_detail($supplier_details,$company_info,$secondaryCode);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('status'=>'error','message'=>'Supplier : ' . $data['supplierName'] . ' Insert Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('status'=>'success','message'=>'Supplier : ' . $data['supplierName']. ' Updated Successfully','system_id'=>$system_id );
                $this->db->trans_commit();
            }

        } catch(\Exception $e){
            return array('status'=>'error','message'=>$e->getMessage());
        }
    }

    function update_customer_detail($supplier_details,$company_info,$secondaryCode){

        $this->load->library('sequence');

        $CI =& get_instance();

        $company_name = isset($supplier_details['Company_Name'])? $supplier_details['Company_Name'] : null;
        $name_on_cheque = isset($supplier_details['Name_On_Cheque'])? $supplier_details['Name_On_Cheque'] : null;
        $country = isset($supplier_details['Country'])? $supplier_details['Country'] : null;
        $location = isset($supplier_details['Location'])? $supplier_details['Location'] : null;
        $tel = isset($supplier_details['Telephone'])? $supplier_details['Telephone'] : null;
        $email = isset($supplier_details['Email'])? $supplier_details['Email'] : null;
        $web_url = isset($supplier_details['Web_URL'])? $supplier_details['Web_URL'] : null;
        $fax = isset($supplier_details['Fax'])? $supplier_details['Fax'] : null;
        $vat_no = isset($supplier_details['Vat_No'])? $supplier_details['Vat_No'] : null;
        $address1 = isset($supplier_details['Address1']) ? $supplier_details['Address1'] : null;
        $address2 = isset($supplier_details['Address2']) ? $supplier_details['Address2'] : null;
        $currency = isset($supplier_details['Currency']) ? $supplier_details['Currency'] : 'OMR';
        $company_id = $company_info['company_id'];
        $company_code = $company_info['company_code'];
        $currentTime = $this->current_time();
        $generated_code = '';

        try{

            $system_id = isset($supplier_details['System_Id'])? $supplier_details['System_Id'] : null;

            if(!$system_id){
                throw new Exception("Store Id is not exists");
            }
            
            $data['isActive'] = 1;
            $data['customerTelephone'] = $tel;
            $data['customerEmail'] = $email;
            $data['customerName'] = $company_name;
            $data['customerUrl'] = $web_url;
            $data['customerFax'] = $fax;
            $data['vatNumber'] = $vat_no;
            $data['customerAddress1'] = $address1;
            $data['customerAddress2'] = $address2;

            $this->db->trans_start();
            $this->db->where('secondaryCode',$secondaryCode);
            $this->db->update('srp_erp_customermaster', $data);
          
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return array('status'=>'error','message'=>'Customer : ' . $data['customerName'] . ' Insert Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('status'=>'success','message'=>'Customer : ' . $data['customerName']. ' Added Successfully');
                $this->db->trans_commit();
            }

        } catch(\Exception $e){
            return array('status'=>'error','message'=>$e->getMessage());
        }
        
    }

    function add_bank_detail($supplierAutoID,$data_ex,$company_info){

        $CI =& get_instance();

        $this->db->trans_start();
        $supplierBankMasterID= $this->input->post('supplierBankMasterID');
        $data['bankName'] = $data_ex['bank_name'];
        $data['currencyID'] = 1;
        $data['accountName'] = $data_ex['account_name'];
        $data['accountNumber'] = $data_ex['account_number'];
        $data['swiftCode'] = $data_ex['swift_code'];
        $data['ibanCode'] = $data_ex['iban_code'];
        $data['bankAddress'] = $data_ex['bank_address'];
        $data['supplierAutoID'] = $supplierAutoID;
        $data['companyID'] = $company_info['company_id'];
        $data['createdUserName'] = 'API';
        $data['createdDateTime'] = date('Y-m-d H:i:s');

        if(empty($supplierBankMasterID)) {
            $this->db->insert('srp_erp_supplierbankmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('status' => false);
            } else {
                return array('status' => true, 'last_id' => $last_id);
            }
        }else{
            $this->db->where('supplierBankMasterID', $supplierBankMasterID);
            $this->db->update('srp_erp_supplierbankmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $last_id = $this->db->insert_id();
                $this->session->set_flashdata('s', 'Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }




    }



    public function current_time($returnType = '')
    {
        switch ($returnType) {
            case 'd':
                return date('Y-m-d');
                break;
            case 't':
                return date('H:i:s');
                break;
            default :
                return
                    date('Y-m-d H:i:s');
        }
    }

    public function check_3rdparty_order_already_exists($order_id){

        $get_order = $this->get_order_detail($order_id);

        if($get_order){
            $system_id = isset($get_order['system_id']) ?  $get_order['system_id'] : null;
            throw new Exception("Order is already entered to the system $system_id");
        }

    }


    public function check_order_already_processed($system_id){

        $get_order = $this->get_order_detail_by_systemcode($system_id);

        if($get_order){
            $system_id = isset($get_order['system_id']) ?  $get_order['system_id'] : null;

            if($get_order && in_array($get_order['erp_record_status'],[1,2])){
                throw new Exception("Can't do adjusments since order $system_id , Already processed");
            }
           
        }

    }

}