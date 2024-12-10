<?php

class Quotation_contract_model extends ERP_Model
{
    
    function save_quotation_contract_header()
    {

        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $cntrctDate = $this->input->post('contractDate');
        $contractDate = input_format_date($cntrctDate, $date_format_policy);
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $isbacktoback = getPolicyValues('BTB', 'All');
        $isdropshipping = getPolicyValues('DBTB', 'All');
        $cntrctEpDate = $this->input->post('contractExpDate');
        $contractExpDate = input_format_date($cntrctEpDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $subDocumentReference = $this->input->post('subDocumentReference');

        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $enable_backtoback = $this->input->post('enable_backtoback');
        $customerOrderID = $this->input->post('customerOrderID');
        $marginPercentage = $this->input->post('marginPercentage');
        $retentionPercentage = $this->input->post('retentionPercentage');
        
   
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        $data['contractType'] = trim($this->input->post('contractType') ?? '');
        $d_code = 'CNT';
        if ($data['contractType'] == 'Quotation') {
            $d_code = 'QUT';
        } elseif ($data['contractType'] == 'Sales Order') {
            $d_code = 'SO';
        }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $data['documentID'] = $d_code;
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');
        $data['contractDate'] = trim($contractDate);
        $data['contractExpDate'] = trim($contractExpDate);
        $contractNarration = ($this->input->post('contractNarration'));
        $data['contractNarration'] = str_replace('<br />', PHP_EOL, $contractNarration);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        //$data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        //$data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        // $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['paymentTerms'] = trim($this->input->post('paymentTerms') ?? '');

        $data['customerAddress'] = trim($this->input->post('addressBoxEditH') ?? '');
        $data['customerEmail'] = trim($this->input->post('emailBoxEditH') ?? '');
        $data['customerTelephone'] = trim($this->input->post('contactNumberBoxEditH') ?? '');
        $data['customerWebURL'] = trim($this->input->post('customerUrlBoxEditH') ?? '');

        $data['modeOfPayment'] = trim($this->input->post('modeOfPayment') ?? '');
        $data['principleRef'] = trim($this->input->post('principleRef') ?? '');
        $data['clientRef'] = trim($this->input->post('clientRef') ?? '');
        $data['deliveryWeek'] = trim($this->input->post('deleiverWeeks') ?? '');

        $data['purchaseOrderID'] = $purchaseOrderID;
        $data['customerOrderID'] = $customerOrderID;
        $data['isBackToBack'] = $enable_backtoback;
        $data['marginPercentage'] = $marginPercentage;
        $data['retentionPercentage'] = $retentionPercentage;

        $dataCMJSON = null;

        $crTypes = explode('<table', $this->input->post('Note') ?? '');
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['Note'] = trim($notes);
        
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
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
        $data['salesPersonID'] = $this->input->post('salesperson');
        $data['showImageYN'] = $this->input->post('showImageYN');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

      
        if (trim($this->input->post('contractAutoID') ?? '')) {
            $masterID = $this->input->post('contractAutoID');
            $taxAdded = $this->db->query("SELECT contractAutoID FROM srp_erp_contractdetails WHERE contractAutoID = $masterID
                                            UNION
                                        SELECT contractAutoID FROM srp_erp_contracttaxdetails WHERE contractAutoID = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }

            //checked approved document
            $contract_details_app = $this->db->query("SELECT approvedYN FROM srp_erp_contractmaster WHERE contractAutoID = '$masterID'")->row_array();

            if($contract_details_app && $contract_details_app['approvedYN'] == 1){
                $this->session->set_flashdata('e', 'Contract Update Failed, Already approved document');
                $this->db->trans_rollback();
                return array('status' => false);
            }


            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->update('srp_erp_contractmaster', $data);

            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('contractAutoID'));
            }
        } else {
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            $this->load->library('sequence');
            $company_id = current_companyID();
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $financeYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} 
                                                   AND '{$contractDate}' BETWEEN dateFrom AND dateTo")->row('companyFinanceYearID');

            $contr_year = date('Y', strtotime($contractDate));
            $contr_month = date('m', strtotime($contractDate));
            if($locationwisecodegenerate == 1){
                $contract_code = $this->sequence->sequence_generator_location($data['documentID'],$financeYearID,$this->common_data['emplanglocationid'],$contr_year,$contr_month);
            }else{
                $contract_code = $this->sequence->sequence_generator_fin($data['documentID'],$financeYearID,$contr_year,$contr_month);
            }

            $data['contractCode'] = $contract_code;

            $this->db->insert('srp_erp_contractmaster', $data);
            $last_id = $this->db->insert_id();

            //get purchase order items
            if($d_code == 'SO' && $isdropshipping == 1){
                $this->pull_items_from_purchaseOrder($purchaseOrderID,$last_id);
            }
            
            if($d_code == 'QUT'){
                $this->pull_items_from_customerorder($customerOrderID,$last_id);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }


    function pull_items_from_purchaseOrder($purchaseOrderID = null,$contractAutoID = null){

        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $contract_master = $this->db->get('srp_erp_contractmaster')->row_array();

        $this->db->select('*');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $purchaseOrderDetails = $this->db->get('srp_erp_purchaseorderdetails')->result_array();

        foreach($purchaseOrderDetails as $purchaseItem){

            $data_arr = array();

            $data_arr['contractAutoID'] = $contractAutoID;
            $data_arr['itemAutoID'] = $purchaseItem['itemAutoID'];
            $data_arr['itemSystemCode'] = $purchaseItem['itemSystemCode'];
            $data_arr['itemDescription'] = $purchaseItem['itemDescription'];
            $data_arr['itemCategory'] = $purchaseItem['itemType'];
            $data_arr['defaultUOMID'] = $purchaseItem['defaultUOMID'];
            $data_arr['defaultUOM'] = $purchaseItem['defaultUOM'];
            $data_arr['unitOfMeasureID'] = $purchaseItem['unitOfMeasureID'];
            $data_arr['unitOfMeasure'] = $purchaseItem['unitOfMeasure'];
            $data_arr['conversionRateUOM'] = 1;
            $data_arr['requestedQty'] = $purchaseItem['requestedQty'];
            $data_arr['unittransactionAmount'] = $purchaseItem['unitAmount'];
            $data_arr['unitAmount'] = $purchaseItem['unitAmount'];
            
            $data_arr['discountAmount'] = $purchaseItem['discountAmount'];
            $data_arr['taxCalculationformulaID'] = $purchaseItem['taxCalculationformulaID'];
            $data_arr['taxAmount'] = $purchaseItem['taxAmount'];

            $data_arr['poUnitPrice'] = $purchaseItem['totalAmount'];
            $data_arr['transactionAmount'] = $purchaseItem['totalAmount'];
            $data_arr['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data_arr['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data_arr['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data_arr['comment'] = $purchaseItem['comment'];
            $data_arr['companyID'] = $this->common_data['company_data']['company_id'];
            $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_contractdetails', $data_arr);

        }

        return true;

    }

    function pull_items_from_customerorder($customerOrderID = null,$contractAutoID = null){

        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $contract_master = $this->db->get('srp_erp_contractmaster')->row_array();

        $this->db->select('*');
        $this->db->where('customerOrderID', $customerOrderID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $customerOrderDetails = $this->db->get('srp_erp_srm_customerorderdetails')->result_array();

        $ap_amount = 0;

        if($contract_master && $contract_master['isBackToBack'] == 1){

            $this->db->select('ap_amount');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', $contractAutoID);
            $contract_details = $this->db->get()->row_array();

            if($contract_details){
                $ap_amount = $contract_details['ap_amount'];
            }

        }

        if($ap_amount <= 0){
            $ap_amount = 1;
        }


        foreach($customerOrderDetails as $purchaseItem){

            $data_arr = array();

            $this->db->select('UnitID,UnitShortCode,UnitDes');
            $this->db->from('srp_erp_unit_of_measure');
            $this->db->where('UnitID', $purchaseItem['unitOfMeasureID']);
            $uom_select = $this->db->get()->row_array();

            $data_arr['contractAutoID'] = $contractAutoID;

            $data_item_details = fetch_item_data($purchaseItem['itemAutoID']);
            
            $data_arr['itemAutoID'] = $data_item_details['itemAutoID'];
            $data_arr['itemSystemCode'] = $data_item_details['itemSystemCode'];
            $data_arr['itemDescription'] = $data_item_details['itemDescription'];
            $data_arr['itemCategory'] = $data_item_details['mainCategory'];
            $data_arr['defaultUOMID'] = $data_item_details['defaultUnitOfMeasureID'];
            $data_arr['defaultUOM'] = $data_item_details['defaultUnitOfMeasure'];
            $data_arr['unitOfMeasureID'] = $purchaseItem['unitOfMeasureID'];
            $data_arr['unitOfMeasure'] = $uom_select['UnitDes'];
            $data_arr['conversionRateUOM'] = conversionRateUOM_id($data_arr['unitOfMeasureID'], $data_arr['defaultUOMID']);;
            $data_arr['requestedQty'] = $purchaseItem['requestedQty'];
            $data_arr['unittransactionAmount'] = $purchaseItem['unitAmount'];
            $data_arr['unitAmount'] = $purchaseItem['unitAmount'];
            
            $data_arr['discountAmount'] = $purchaseItem['discountAmount'];
            // $data_arr['taxCalculationformulaID'] = $purchaseItem['taxCalculationformulaID'];
            // $data_arr['taxAmount'] = $purchaseItem['taxAmount'];

            $data_arr['poUnitPrice'] = $purchaseItem['totalAmount'];
            $data_arr['retensionValue'] = ((($data_arr['poUnitPrice'] * $ap_amount) * $contract_master['retentionPercentage']) / 100 );
            $data_arr['transactionAmount'] = $purchaseItem['totalAmount'];
            $data_arr['companyLocalAmount'] = ($data_arr['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data_arr['companyReportingAmount'] = ($data_arr['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data_arr['customerAmount'] = ($data_arr['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data_arr['comment'] = $purchaseItem['comment'];
            $data_arr['companyID'] = $this->common_data['company_data']['company_id'];
            $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_contractdetails', $data_arr);

        }

        // update po extra charge
        $this->update_total_po_value($contractAutoID);

        return true;
    }

    function save_quotation_contract_header_job()
    {
        $date_format_policy = date_format_policy();
        $cntrctDate = $this->input->post('contractDate');
        $contractDate = input_format_date($cntrctDate, $date_format_policy);
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $cntrctEpDate = $this->input->post('contractExpDate');
        $contractExpDate = input_format_date($cntrctEpDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $this->db->trans_start();
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        $data['contractType'] = trim($this->input->post('contractType') ?? '');
        $d_code = 'CNT';
        // if ($data['contractType'] == 'Quotation') {
        //     $d_code = 'QUT';
        // } elseif ($data['contractType'] == 'Sales Order') {
        //     $d_code = 'SO';
        // }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $data['documentID'] = $d_code;
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');
        $data['contractDate'] = trim($contractDate);
        $data['contractExpDate'] = trim($contractExpDate);
        $contractNarration = ($this->input->post('contractNarration'));
        $data['contractNarration'] = str_replace('<br />', PHP_EOL, $contractNarration);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        //$data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        //$data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['isAdvance'] = 1;
        $data['contactValue']=trim($this->input->post('contactValue') ?? '');
        // $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['paymentTerms'] = trim($this->input->post('paymentTerms') ?? '');

        $data['customerAddress'] = trim($this->input->post('addressBoxEditH') ?? '');
        $data['customerEmail'] = trim($this->input->post('emailBoxEditH') ?? '');
        $data['customerTelephone'] = trim($this->input->post('contactNumberBoxEditH') ?? '');
        $data['customerWebURL'] = trim($this->input->post('customerUrlBoxEditH') ?? '');

        $data['docTypeID'] = trim($this->input->post('docType') ?? '');

        $data['email'] = trim($this->input->post('email') ?? '');
        $data['ticketTemplate'] = trim($this->input->post('ticket') ?? '');
        $data['dalilTemplateyID'] = trim($this->input->post('dalilTemplatey') ?? '');
        //$data['conType'] = trim($this->input->post('contract_type') ?? '');
        $data['LinkActivityYN'] = trim($this->input->post('LinkActivityYN') ?? '');
        $data['activityID'] = trim($this->input->post('activityCode') ?? '');

        if($data['ticketTemplate']=='job'){
            $data['editJobBillingYN'] = trim($this->input->post('editJobBillingYN') ?? '');
            
        }

        $dataCMJSON = null;

        $crTypes = explode('<table', $this->input->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['Note'] = trim($notes);
        
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
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
        $data['salesPersonID'] = $this->input->post('salesperson');
        $data['showImageYN'] = $this->input->post('showImageYN');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('contractAutoID') ?? '')) {
            $masterID = $this->input->post('contractAutoID');
            $taxAdded = $this->db->query("SELECT contractAutoID FROM srp_erp_contractdetails WHERE contractAutoID = $masterID
                                            UNION
                                        SELECT contractAutoID FROM srp_erp_contracttaxdetails WHERE contractAutoID = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }

            //checked approved document
            $contract_details_app = $this->db->query("SELECT approvedYN FROM srp_erp_contractmaster WHERE contractAutoID = '$masterID'")->row_array();

            if($contract_details_app && $contract_details_app['approvedYN'] == 1){
                $this->session->set_flashdata('e', 'Contract Update Failed, Already approved document');
                $this->db->trans_rollback();
                return array('status' => false);
            }


            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->update('srp_erp_contractmaster', $data);

            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('contractAutoID'));
            }
        } else {
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            $this->load->library('sequence');
            $company_id = current_companyID();
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $financeYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} 
                                                   AND '{$contractDate}' BETWEEN dateFrom AND dateTo")->row('companyFinanceYearID');

            $contr_year = date('Y', strtotime($contractDate));
            $contr_month = date('m', strtotime($contractDate));
            if($locationwisecodegenerate == 1){
                $contract_code = $this->sequence->sequence_generator_location($data['documentID'],$financeYearID,$this->common_data['emplanglocationid'],$contr_year,$contr_month);
            }else{
                $contract_code = $this->sequence->sequence_generator_fin($data['documentID'],$financeYearID,$contr_year,$contr_month);
            }

            $data['contractCode'] = $contract_code;

            $this->db->insert('srp_erp_contractmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_quotation_contract_header_job_amendment(){

        //amendmentID
        $amendmentID = $this->input->post('amendmentID');
        $contractAutoID = $this->input->post('contractAutoID');
        $referenceNo = $this->input->post('referenceNo');
        $contractAmdExpDate = $this->input->post('contractAmdExpDate');
     
        $param_arr = array();
        $param_arr[] = 'referenceNo';
        $param_arr[] = 'Note';
        $param_arr[] = 'contractAmdExpDate';
        
        $data = array();
        $data_updated = array();

        //checked approved document
        $contract_details_app = $this->db->query("SELECT * FROM srp_erp_contractmaster WHERE contractAutoID = '$contractAutoID'")->row_array();

        //get amendment data
        $amendment_details =  $this->db->query("SELECT * FROM srp_erp_document_amendments WHERE id = '$amendmentID'")->row_array();

        if($amendment_details){
            $amendmentType_arr = explode(',',$amendment_details['amendmentType']);

            if($amendment_details['status'] == 0 && !in_array(2,$amendmentType_arr)){
                $this->session->set_flashdata('e', 'Price change not been allowed in this Amendment.');
                return array('e', 'Price change not been allowed in this Amendment.');
            }
        }

        $crTypes = explode('<table', $this->input->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $Note = trim($notes);
        $data['Note'] = $Note;
        $data['contractAmdExpDate'] = $contractAmdExpDate;
        $data['referenceNo'] = $referenceNo;
        $data['contractCode'] = $amendment_details['amendmentCode'];

        $res = $this->db->where('contractAutoID',$contractAutoID)->update('srp_erp_contractmaster',$data);

        foreach($param_arr as $val){
            $amd_arr = array();
            $ex_record = $this->db->query("SELECT * FROM srp_erp_document_amendments_values WHERE amendmentID = '$amendmentID' and parameter = '$val'")->row_array();
            
            $amd_arr['amendmentID'] = $amendmentID;
            $amd_arr['docCode'] = $amendment_details['docCode'];
            $amd_arr['docID'] = $contractAutoID;
            $amd_arr['amendmentValueType'] = 2;
            $amd_arr['parameter'] = $val;
            $amd_arr['currentValue'] = $contract_details_app[$val];
            $amd_arr['changedValue'] = $$val;
            $amd_arr['createdDate'] = $this->common_data['current_date'];
            $amd_arr['createdUser'] = $this->common_data['current_user'];
            $amd_arr['createdUserID'] = $this->common_data['current_userID'];

            if($ex_record){
                $ex_rec_id = $ex_record['id'];
                $res = $this->db->where('id',$ex_rec_id)->update('srp_erp_document_amendments_values',$amd_arr);
            } else {
                $res = $this->db->insert('srp_erp_document_amendments_values',$amd_arr);
            }
        
        }

        $this->session->set_flashdata('s', 'Contract Successfully Amended.');
        return true;

    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function fetch_payment_applications_headers()
    {
        $contractAutoID = $this->input->post('contractAutoID');
        $companyid = current_companyID();
        $this->db->select('`srp_erp_payment_application`.*, IFNULL(srp_erp_payment_application.confirmedYN, 0 ) AS confirmedPA,IFNULL(srp_erp_payment_application.confirmedBy, "-" ) AS confirmedBy');
        $this->db->from('srp_erp_payment_application');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->where('companyID', $companyid);
        $this->db->order_by('autoID');
        return $this->db->get()->result_array();
    }

    function fetch_payment_application_details($autoID){
        $companyid = current_companyID();
        $data['detail'] = $this->db->query("SELECT
        srp_erp_payment_application_details.itemSystemCode,
        srp_erp_payment_application_details.itemDescription,
        IFNULL(srp_erp_payment_application_details.PAcuQty,0) AS PAcuQty,
        srp_erp_payment_application_details.cuQty,
        srp_erp_payment_application_details.prevQty,
        srp_erp_payment_application_details.currentQty
        FROM
        srp_erp_payment_application_details
        INNER JOIN srp_erp_payment_application ON srp_erp_payment_application.autoID = srp_erp_payment_application_details.PAAutoID
        WHERE
        srp_erp_payment_application.autoID = $autoID")->result_array();
        
        return $data;
    }

    function edit_payment_application_details($autoID){
        $companyid = current_companyID();
        $data['detail'] = $this->db->query("SELECT
        srp_erp_payment_application_details.PADetailsAutoID,
        srp_erp_payment_application_details.PAAutoID,
        IFNULL(srp_erp_payment_application_details.PAcuQty,0) AS PAcuQty,
        srp_erp_payment_application_details.unittransactionAmount,
        srp_erp_payment_application_details.itemSystemCode,
        srp_erp_payment_application_details.itemDescription,
        srp_erp_payment_application_details.cuQty,
        srp_erp_payment_application_details.prevQty,
        srp_erp_payment_application_details.currentQty
        FROM
        srp_erp_payment_application_details
        INNER JOIN srp_erp_payment_application ON srp_erp_payment_application.autoID = srp_erp_payment_application_details.PAAutoID
        WHERE
        srp_erp_payment_application.autoID = $autoID")->result_array();
        
        return $data;
    }

    function fetch_contract_template_header($contractAutoID){

        $this->db->select('srp_erp_contractmaster.contractCode,srp_erp_contractmaster.transactionCurrency,srp_erp_contractmaster.transactionCurrencyID,srp_erp_contractmaster.contactPersonName as principle,order.contactPersonName,order.customerReferenceNumber,srp_erp_contractmaster.referenceNo,customerName,marginPercentage');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $this->db->join('srp_erp_srm_customerordermaster order','order.customerOrderID = srp_erp_contractmaster.customerOrderID ','left');
        return $this->db->get()->row_array();
    }

    function fetch_contract_template_data($contractAutoID)
    {
        $projectExist = project_is_exist();
        $convertFormat = convert_date_format_sql();
        $currentdate = $this->common_data['current_date'];
        $companyid = current_companyID();
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $datefromconvert = input_format_date($currentdate, $date_format_policy);

        $this->db->select('srp_erp_contractmaster.createdUserName,srp_erp_contractmaster.customerWebURL,srp_erp_contractmaster.paymentTerms, DATE_FORMAT(srp_erp_contractmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime, warehousemaster.wareHouseDescription,srp_erp_contractmaster.*,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate ,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
        CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,showImageYN,segment.segmentCode as segmentcodemaster,segment.description as segDescription,isBackToBack,retentionPercentage');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $this->db->join('srp_erp_segment segment','segment.segmentID = srp_erp_contractmaster.segmentID ','left');
        $this->db->join('srp_erp_warehousemaster warehousemaster','warehousemaster.wareHouseAutoID = srp_erp_contractmaster.warehouseAutoID ','left');
        $data['master'] = $this->db->get()->row_array();

        
        $this->db->select('srp_erp_chartofaccounts.bankName, srp_erp_chartofaccounts.bankBranch, srp_erp_chartofaccounts.bankShortCode, srp_erp_chartofaccounts.bankSwiftCode,  srp_erp_chartofaccounts.bankAccountNumber,  srp_erp_chartofaccounts.bankCurrencyCode, srp_erp_chartofaccounts.GLDescription');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');       
        $this->db->join('srp_erp_chartofaccounts srp_erp_chartofaccounts','srp_erp_chartofaccounts.GLAutoID = srp_erp_contractmaster.RVbankCode ','inner');
        $data['bankDetails'] = $this->db->get()->row_array();


        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax,customerAutoID');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        /*$this->db->select("srp_erp_contractdetails.*,srp_erp_taxcalculationformulamaster.Description as taxDescription, srp_erp_itemmaster.itemImage as itemImage,FORMAT(requestedQty,0) as requestedQtyformated,requestedQty AS requestedQtyNotFormated, $item_code_alias
        /*$this->db->select("srp_erp_contractdetails.*,srp_erp_taxcalculationformulamaster.Description as taxDescription, srp_erp_itemmaster.itemImage as itemImage,FORMAT(requestedQty,0) as requestedQtyformated,requestedQty AS requestedQtyNotFormated, $item_code_alias
        ,IFNULL(`srp_erp_boq_header`.`projectDescription`,' - ' ) AS `projectName`, IFNULL(`project`.`description` ,' - '),IFNULL(srp_erp_contractdetails.projectID,0) as projectIDNew,");
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
         $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
        $this->db->from('srp_erp_contractdetails');
        $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_contractdetails.projectID  ','left');
        $this->db->join('srp_erp_projects project', 'project.projectID = srp_erp_contractdetails.projectID  ','left');
        $data['detail'] = $this->db->get()->result_array();*/

       
        $cusDetails = $this->db->query("SELECT customerID FROM srp_erp_contractmaster WHERE contractAutoID= $contractAutoID")->row();

        $data['detail'] = $this->db->query("SELECT
            `srp_erp_contractdetails`.*, `srp_erp_itemmaster`.`partNo`,
            `srp_erp_itemcategory`.`description` AS `mainCategory`,`sub`.`description` AS `subCategory`,`rev`.`systemAccountCode` AS `revanuedes`,`cost`.`systemAccountCode` AS `costdes`,
            `srp_erp_taxcalculationformulamaster`.`Description` AS `taxDescription`,
            `srp_erp_itemmaster`.`itemImage` AS `itemImage`,
            FORMAT( requestedQty, 0 ) AS requestedQtyformated,
            `requestedQty` AS `requestedQtyNotFormated`,
            `srp_erp_contractdetails`.`itemSystemCode` AS `itemSystemCode`,
            IFNULL( `srp_erp_boq_header`.`projectDescription`, ' - ' ) AS `projectName`,
            IFNULL( `project`.`description`, ' - ' ),
            IFNULL( srp_erp_contractdetails.projectID, 0 ) AS projectIDNew,
            qty_detail.prevqty,
            IFNULL((srp_erp_contractdetails.requestedQty + qty_detail.prevqty),0) AS cumilativeQty,
            IFNULL((srp_erp_contractdetails.unittransactionAmount * srp_erp_contractdetails.requestedQty),0) AS currentAmount,
            IFNULL(((qty_detail.prevAmount)+(srp_erp_contractdetails.unittransactionAmount * srp_erp_contractdetails.requestedQty)),0) AS cumilativeAmount,
            qty_detail.prevAmount
        FROM
            `srp_erp_contractdetails`
            LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_contractdetails`.`itemAutoID`

            LEFT JOIN `srp_erp_itemcategory` ON `srp_erp_itemcategory`.`itemCategoryID` = `srp_erp_contractdetails`.`mainCategoryID`
            LEFT JOIN `srp_erp_itemcategory`  `sub` ON `sub`.`itemCategoryID` = `srp_erp_contractdetails`.`subcategoryID`
            LEFT JOIN `srp_erp_chartofaccounts` `rev` ON `rev`.`GLAutoID` = `srp_erp_contractdetails`.`revanueGLAutoID`
            LEFT JOIN `srp_erp_chartofaccounts`  `cost` ON `cost`.`GLAutoID` = `srp_erp_contractdetails`.`costGLAutoID`

            LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_contractdetails`.`taxCalculationformulaID`
            LEFT JOIN `srp_erp_boq_header` ON `srp_erp_boq_header`.`headerID` = `srp_erp_contractdetails`.`projectID`
            LEFT JOIN `srp_erp_projects` `project` ON `project`.`projectID` = `srp_erp_contractdetails`.`projectID`
            LEFT JOIN (
        SELECT
            w.itemAutoID,
            SUM( w.requestedQty ) AS prevqty,
        SUM( w.transactionAmount ) AS prevAmount 	
        FROM
            srp_erp_contractmaster a
            LEFT OUTER JOIN srp_erp_contractdetails w ON a.contractAutoID = w.contractAutoID 
        WHERE
            a.customerID = $cusDetails->customerID
            AND a.contractType = 'Quotation' 
            AND w.contractAutoID <> $contractAutoID 
        GROUP BY
            w.itemAutoID 
            ) qty_detail ON srp_erp_contractdetails.itemAutoID = qty_detail.itemAutoID 
        WHERE
            `contractAutoID` = $contractAutoID")->result_array();

        // echo '<pre>';
        // print_r($data['detail']); exit;

        $data['outstandingamt'] = $this->db->query("SELECT
                    a.companyLocalAmount AS companyLocalAmount,
                    a.companyLocalAmountDecimalPlaces,
                    a.companyReportingAmount,
                    a.companyReportingCurrencyDecimalPlaces,
                    a.document,
                    a.documentMasterAutoID,
                    DATE_FORMAT( a.documentDate, '%d-%m-%Y' ) AS documentDate,
                    a.documentCode,
                    a.documentSystemCode,
                    a.documentNarration,
                    a.customerName,
                    a.customerSystemCode,
                    a.GLSecondaryCode,
                    a.GLDescription
                FROM
                    (
                SELECT
                    CL.DecimalPlaces AS companyLocalAmountDecimalPlaces,
                    SUM( srp_erp_generalledger.companyLocalAmount ) AS companyLocalAmount,
                    CR.DecimalPlaces AS companyReportingCurrencyDecimalPlaces,
                    SUM( srp_erp_generalledger.companyReportingAmount ) AS companyReportingAmount,
                    srp_erp_documentcodemaster.document,
                    srp_erp_generalledger.documentMasterAutoID,
                    srp_erp_generalledger.documentDate,
                    srp_erp_generalledger.documentCode,
                    srp_erp_generalledger.documentSystemCode,
                    srp_erp_generalledger.documentNarration,
                    srp_erp_customermaster.customerName,
                    srp_erp_customermaster.customerSystemCode,
                    srp_erp_chartofaccounts.GLSecondaryCode,
                    srp_erp_chartofaccounts.GLDescription
                FROM
                    srp_erp_generalledger
                    INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID
                    AND srp_erp_generalledger.subLedgerType = 3
                    AND srp_erp_customermaster.companyID = $companyid
                    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID
                    AND srp_erp_chartofaccounts.companyID = $companyid
                    LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode
                    AND srp_erp_documentcodemaster.companyID = $companyid
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID )
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_generalledger.transactionCurrencyID )
                WHERE
                    ( srp_erp_generalledger.partyAutoID = '{$data['customer']['customerAutoID']}')
                    AND srp_erp_generalledger.documentDate <= '$datefromconvert'
                    AND srp_erp_generalledger.companyID = $companyid
                GROUP BY
                    srp_erp_generalledger.partyAutoID
                    ) a")->row_array();

        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($contractAutoID),'CNT', 'contractAutoID');
        if($isGroupByTax == 1) {
            $companyID = current_companyID();
            $data['tax'] =$this->db->query("SELECT
                                        srp_erp_contracttaxdetails.taxDescription,srp_erp_contracttaxdetails.taxDetailAutoID,taxleg.amount as amount
                                    FROM
                                    srp_erp_contracttaxdetails
                                    INNER JOIN (
                                        SELECT
                                            SUM(amount) as amount,taxDetailAutoID
                                        FROM
                                            srp_erp_taxledger
                                        WHERE
                                            documentID = 'CNT'
                                            AND documentMasterAutoID = $contractAutoID
                                        GROUP BY documentMasterAutoID,taxDetailAutoID
                                    ) taxleg ON srp_erp_contracttaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                    WHERE
                                        contractAutoID = $contractAutoID AND companyID = $companyID")->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['tax'] = $this->db->get('srp_erp_contracttaxdetails')->result_array();
        }
        return $data;
    }


    function fetch_contract_template_data_new($contractAutoID, $paymentAutoID=NULL)
    {
        $projectExist = project_is_exist();
        $convertFormat = convert_date_format_sql();
        $currentdate = $this->common_data['current_date'];
        $companyid = current_companyID();
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $datefromconvert = input_format_date($currentdate, $date_format_policy);

        $this->db->select('srp_erp_contractmaster.createdUserName,srp_erp_contractmaster.customerWebURL,srp_erp_contractmaster.paymentTerms, DATE_FORMAT(srp_erp_contractmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime, warehousemaster.wareHouseDescription,srp_erp_contractmaster.*,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate ,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
        CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,showImageYN,segment.segmentCode as segmentcodemaster,segment.description as segDescription');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $this->db->join('srp_erp_segment segment','segment.segmentID = srp_erp_contractmaster.segmentID ','left');
        $this->db->join('srp_erp_warehousemaster warehousemaster','warehousemaster.wareHouseAutoID = srp_erp_contractmaster.warehouseAutoID ','left');
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_chartofaccounts.bankName, srp_erp_chartofaccounts.bankBranch, srp_erp_chartofaccounts.bankShortCode, srp_erp_chartofaccounts.bankSwiftCode,  srp_erp_chartofaccounts.bankAccountNumber,  srp_erp_chartofaccounts.bankCurrencyCode');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');       
        $this->db->join('srp_erp_chartofaccounts srp_erp_chartofaccounts','srp_erp_chartofaccounts.GLAutoID = srp_erp_contractmaster.RVbankCode ','inner');
        $data['bankDetails'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_payment_application.documentID');
        $this->db->where('srp_erp_payment_application.autoID', $paymentAutoID);
        $this->db->from('srp_erp_payment_application');
        $this->db->join('srp_erp_payment_application_details srp_erp_payment_application_details','srp_erp_payment_application_details.PAAutoID = srp_erp_payment_application.autoID ','inner');
        $data['PADocumentID'] = $this->db->get()->row_array();

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax,customerAutoID');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        /*$this->db->select("srp_erp_contractdetails.*,srp_erp_taxcalculationformulamaster.Description as taxDescription, srp_erp_itemmaster.itemImage as itemImage,FORMAT(requestedQty,0) as requestedQtyformated,requestedQty AS requestedQtyNotFormated, $item_code_alias
        ,IFNULL(`srp_erp_boq_header`.`projectDescription`,' - ' ) AS `projectName`, IFNULL(`project`.`description` ,' - '),IFNULL(srp_erp_contractdetails.projectID,0) as projectIDNew,");
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
         $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
        $this->db->from('srp_erp_contractdetails');
        $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_contractdetails.projectID  ','left');
        $this->db->join('srp_erp_projects project', 'project.projectID = srp_erp_contractdetails.projectID  ','left');
        $data['detail'] = $this->db->get()->result_array();*/

       
        $cusDetails = $this->db->query("SELECT customerID FROM srp_erp_contractmaster WHERE contractAutoID= $contractAutoID")->row();

        
            $data['detailsNew'] = $this->db->query("SELECT
            srp_erp_contractdetails.*,
            srp_erp_contractdetails.requestedQty AS cumilativeQty,
            IFNULL( table1.PAcuQty, 0 ) AS PAcuQty,
            IFNULL( table1.totalPreviousQty, 0 ) AS totalPreviousQty,
            IFNULL( ( srp_erp_contractdetails.unittransactionAmount * srp_erp_contractdetails.requestedQty ), 0 ) AS currentAmount  
            FROM
            srp_erp_contractdetails
            LEFT JOIN srp_erp_payment_application ON srp_erp_contractdetails.contractAutoID = srp_erp_payment_application.contractAutoID
            LEFT JOIN ( SELECT contractDetailsAutoID,PAcuQty, IFNULL( SUM( currentQty ), 0 ) AS totalPreviousQty FROM srp_erp_payment_application_details GROUP BY contractDetailsAutoID )
            table1 ON table1.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID  AND srp_erp_payment_application.contractAutoID
            WHERE
            srp_erp_payment_application.autoID = $paymentAutoID")->result_array();


            $data['detailsReport'] = $this->db->query("SELECT
            * 
            FROM
            srp_erp_payment_application_details
            LEFT JOIN srp_erp_payment_application ON srp_erp_payment_application_details.PADetailsAutoID = srp_erp_payment_application.autoID 
            WHERE
            srp_erp_payment_application_details.PAAutoID = $paymentAutoID")->result_array();
        

        $data['detail'] = $this->db->query("SELECT
            `srp_erp_contractdetails`.*,
            `srp_erp_taxcalculationformulamaster`.`Description` AS `taxDescription`,
            `srp_erp_itemmaster`.`itemImage` AS `itemImage`,
            FORMAT( requestedQty, 0 ) AS requestedQtyformated,
            `requestedQty` AS `requestedQtyNotFormated`,
            `srp_erp_itemmaster`.`itemSystemCode` AS `itemSystemCode`,
            IFNULL( `srp_erp_boq_header`.`projectDescription`, ' - ' ) AS `projectName`,
            IFNULL( `project`.`description`, ' - ' ),
            IFNULL( srp_erp_contractdetails.projectID, 0 ) AS projectIDNew,
            IFNULL(qty_detail.prevqty,0) AS prevqty,
            IFNULL((srp_erp_contractdetails.requestedQty + qty_detail.prevqty),0) AS cumilativeQty,
            IFNULL((srp_erp_contractdetails.unittransactionAmount * srp_erp_contractdetails.requestedQty),0) AS currentAmount,
            IFNULL(((qty_detail.prevAmount)+(srp_erp_contractdetails.unittransactionAmount * srp_erp_contractdetails.requestedQty)),0) AS cumilativeAmount,
            qty_detail.prevAmount
        FROM
            `srp_erp_contractdetails`
            LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_contractdetails`.`itemAutoID`
            LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_contractdetails`.`taxCalculationformulaID`
            LEFT JOIN `srp_erp_boq_header` ON `srp_erp_boq_header`.`headerID` = `srp_erp_contractdetails`.`projectID`
            LEFT JOIN `srp_erp_projects` `project` ON `project`.`projectID` = `srp_erp_contractdetails`.`projectID`
            LEFT JOIN (
        SELECT
            w.itemAutoID,
            SUM( w.requestedQty ) AS prevqty,
        SUM( w.transactionAmount ) AS prevAmount 	
        FROM
            srp_erp_contractmaster a
            LEFT OUTER JOIN srp_erp_contractdetails w ON a.contractAutoID = w.contractAutoID 
        WHERE
            a.customerID = $cusDetails->customerID
            AND a.contractType = 'Quotation' 
            AND w.contractAutoID <> $contractAutoID 
        GROUP BY
            w.itemAutoID 
            ) qty_detail ON srp_erp_contractdetails.itemAutoID = qty_detail.itemAutoID 
        WHERE
            `contractAutoID` = $contractAutoID")->result_array();

            

        $data['outstandingamt'] = $this->db->query("SELECT
                    a.companyLocalAmount AS companyLocalAmount,
                    a.companyLocalAmountDecimalPlaces,
                    a.companyReportingAmount,
                    a.companyReportingCurrencyDecimalPlaces,
                    a.document,
                    a.documentMasterAutoID,
                    DATE_FORMAT( a.documentDate, '%d-%m-%Y' ) AS documentDate,
                    a.documentCode,
                    a.documentSystemCode,
                    a.documentNarration,
                    a.customerName,
                    a.customerSystemCode,
                    a.GLSecondaryCode,
                    a.GLDescription
                FROM
                    (
                SELECT
                    CL.DecimalPlaces AS companyLocalAmountDecimalPlaces,
                    SUM( srp_erp_generalledger.companyLocalAmount ) AS companyLocalAmount,
                    CR.DecimalPlaces AS companyReportingCurrencyDecimalPlaces,
                    SUM( srp_erp_generalledger.companyReportingAmount ) AS companyReportingAmount,
                    srp_erp_documentcodemaster.document,
                    srp_erp_generalledger.documentMasterAutoID,
                    srp_erp_generalledger.documentDate,
                    srp_erp_generalledger.documentCode,
                    srp_erp_generalledger.documentSystemCode,
                    srp_erp_generalledger.documentNarration,
                    srp_erp_customermaster.customerName,
                    srp_erp_customermaster.customerSystemCode,
                    srp_erp_chartofaccounts.GLSecondaryCode,
                    srp_erp_chartofaccounts.GLDescription
                FROM
                    srp_erp_generalledger
                    INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID
                    AND srp_erp_generalledger.subLedgerType = 3
                    AND srp_erp_customermaster.companyID = $companyid
                    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID
                    AND srp_erp_chartofaccounts.companyID = $companyid
                    LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode
                    AND srp_erp_documentcodemaster.companyID = $companyid
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID )
                    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_generalledger.transactionCurrencyID )
                WHERE
                    ( srp_erp_generalledger.partyAutoID = '{$data['customer']['customerAutoID']}')
                    AND srp_erp_generalledger.documentDate <= '$datefromconvert'
                    AND srp_erp_generalledger.companyID = $companyid
                GROUP BY
                    srp_erp_generalledger.partyAutoID
                    ) a")->row_array();

        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($contractAutoID),'CNT', 'contractAutoID');
        if($isGroupByTax == 1) {
            $companyID = current_companyID();
            $data['tax'] =$this->db->query("SELECT
                                        srp_erp_contracttaxdetails.taxDescription,srp_erp_contracttaxdetails.taxDetailAutoID,taxleg.amount as amount
                                    FROM
                                    srp_erp_contracttaxdetails
                                    INNER JOIN (
                                        SELECT
                                            SUM(amount) as amount,taxDetailAutoID
                                        FROM
                                            srp_erp_taxledger
                                        WHERE
                                            documentID = 'CNT'
                                            AND documentMasterAutoID = $contractAutoID
                                        GROUP BY documentMasterAutoID,taxDetailAutoID
                                    ) taxleg ON srp_erp_contracttaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                    WHERE
                                        contractAutoID = $contractAutoID AND companyID = $companyID")->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['tax'] = $this->db->get('srp_erp_contracttaxdetails')->result_array();
        }
        return $data;
    }

    function fetch_item_detail_table()
    {
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($contractAutoID),'CNT', 'contractAutoID');
        $data = array();
        $secondaryCode = getPolicyValues('SSC', 'All');
        $AdvanceCostCapture = getPolicyValues('ACC','All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $query1_activity_code ='';
        if($AdvanceCostCapture==1){
            $query1_activity_code = ',srp_erp_activity_code_main.activity_code as activity_code';
        }

        if($isGroupByTax == 1) {
            // $this->db->select("*,srp_erp_itemmaster.itemImage as itemImage, IFNULL(taxAmount, 0) as taxAmount, IFNULL(srp_erp_taxcalculationformulamaster.Description, '') as taxDescription, $item_code_alias");
            // $this->db->from('srp_erp_contractdetails');
            // $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
            // $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            // $this->db->join('(SELECT
            //         documentDetailAutoID,
            //         taxDetailAutoID
            //         FROM
            //         `srp_erp_taxledger`
            //         where 
            //         companyID = '. current_companyID() .' 
            //         AND documentID = \'CNT\'
            //         AND documentMasterAutoID  = '.$contractAutoID.' 
            //         GROUP BY
            //         documentMasterAutoID)taxledger',' taxledger.documentDetailAutoID = srp_erp_contractdetails.contractDetailsAutoID','left');
            // $this->db->where('contractAutoID', $contractAutoID);
            // $data['detail'] = $this->db->get()->result_array();

            $this->db->select("srp_erp_contractdetails.*,srp_erp_itemmaster.itemName as itemname,cat_group.categoryName as categoryGroupName,srp_erp_itemcategory.description as mainCategory,sub.description as subCategory,subsub.description as subsubCategory,rev.systemAccountCode as revanuedes,cost.systemAccountCode as costdes, IFNULL(taxAmount, 0) as taxAmount, srp_erp_taxcalculationformulamaster.Description as taxDescription $query1_activity_code");
            $this->db->from('srp_erp_contractdetails');
            $this->db->join('srp_erp_itemcategory', 'srp_erp_itemcategory.itemCategoryID = srp_erp_contractdetails.mainCategoryID','left');
            $this->db->join('srp_erp_itemcategory as sub', 'sub.itemCategoryID = srp_erp_contractdetails.subcategoryID','left');
            $this->db->join('srp_erp_itemcategory as subsub', 'subsub.itemCategoryID = srp_erp_contractdetails.subsubcategoryID','left');
            $this->db->join('srp_erp_chartofaccounts as rev', 'rev.GLAutoID = srp_erp_contractdetails.revanueGLAutoID','left');
            $this->db->join('srp_erp_chartofaccounts as cost', 'cost.GLAutoID = srp_erp_contractdetails.costGLAutoID','left');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
            $this->db->join('srp_erp_op_contract_details_category_list cat_group', 'cat_group.autoID = srp_erp_contractdetails.categoryGroupID','left');
            if($AdvanceCostCapture==1){
                $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_contractdetails.activityCodeID','left');
            }
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            $this->db->join('(SELECT
                    documentDetailAutoID,
                    taxDetailAutoID
                    FROM
                    `srp_erp_taxledger`
                    where 
                    companyID = '. current_companyID() .' 
                    AND documentID = \'CNT\'
                    AND documentMasterAutoID  = '.$contractAutoID.' 
                    GROUP BY
                    documentMasterAutoID)taxledger',' taxledger.documentDetailAutoID = srp_erp_contractdetails.contractDetailsAutoID','left');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['detail'] = $this->db->get()->result_array();
        } else {
            // $this->db->select("*,srp_erp_itemmaster.itemImage as itemImage, IFNULL(taxAmount, 0) as taxAmount, srp_erp_taxcalculationformulamaster.Description as taxDescription, $item_code_alias");
            // $this->db->from('srp_erp_contractdetails');
            // $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
            // $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            // $this->db->where('contractAutoID', $contractAutoID);
            // $data['detail'] = $this->db->get()->result_array();
            
            $this->db->select("srp_erp_contractdetails.*,srp_erp_itemmaster.itemName as itemname,subsub.description as subsubCategory,srp_erp_itemcategory.description as mainCategory,sub.description as subCategory,rev.systemAccountCode as revanuedes,cost.systemAccountCode as costdes, IFNULL(taxAmount, 0) as taxAmount, srp_erp_taxcalculationformulamaster.Description as taxDescription $query1_activity_code");
            $this->db->from('srp_erp_contractdetails');
            $this->db->join('srp_erp_itemcategory', 'srp_erp_itemcategory.itemCategoryID = srp_erp_contractdetails.mainCategoryID','left');
            $this->db->join('srp_erp_itemcategory as sub', 'sub.itemCategoryID = srp_erp_contractdetails.subcategoryID','left');
            $this->db->join('srp_erp_itemcategory as subsub', 'subsub.itemCategoryID = srp_erp_contractdetails.subsubcategoryID','left');
            $this->db->join('srp_erp_chartofaccounts as rev', 'rev.GLAutoID = srp_erp_contractdetails.revanueGLAutoID','left');
            $this->db->join('srp_erp_chartofaccounts as cost', 'cost.GLAutoID = srp_erp_contractdetails.costGLAutoID','left');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_contractdetails.itemAutoID','left');
            if($AdvanceCostCapture==1){
                $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_contractdetails.activityCodeID','left');
            }
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['detail'] = $this->db->get()->result_array();
        }

        // foreach($data['detail'] as $key => $val){
        //     $data['detail'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['detail'][$key]['itemImage'], '1 hour');
        // }

        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,transactionCurrencyID,showImageYN');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['currency'] = $this->db->get('srp_erp_contractmaster')->row_array();

        $this->db->select("*");
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $data['master'] = $this->db->get()->row_array();
        
       
        if($isGroupByTax == 1) {
            $companyID = current_companyID();
            $data['tax_detail'] =$this->db->query("SELECT
                                        srp_erp_contracttaxdetails.taxDescription,srp_erp_contracttaxdetails.taxDetailAutoID,taxleg.amount as amount
                                    FROM
                                    srp_erp_contracttaxdetails
                                    INNER JOIN (
                                        SELECT
                                            SUM(amount) as amount,taxDetailAutoID
                                        FROM
                                            srp_erp_taxledger
                                        WHERE
                                            documentID = 'CNT'
                                            AND documentMasterAutoID = $contractAutoID
                                        GROUP BY documentMasterAutoID,taxDetailAutoID
                                    ) taxleg ON srp_erp_contracttaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                    WHERE
                                        contractAutoID = $contractAutoID AND companyID = $companyID")->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $this->db->from('srp_erp_contracttaxdetails');
            $data['tax_detail'] = $this->db->get()->result_array();
        }

        return $data;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_taxledger', array('taxDetailAutoID' => $this->input->post('taxDetailAutoID'), 'documentMasterAutoID' => $this->input->post('contractAutoID'), 'documentDetailAutoID' => NULL, 'documentID' => 'CNT'));
        $this->db->delete('srp_erp_contracttaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function load_contract_header()
    {
        update_group_based_tax('srp_erp_contractmaster', 'contractAutoID', $this->input->post('contractAutoID'), 'srp_erp_contracttaxdetails', null, 'CNT');

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        return $this->db->get('srp_erp_contractmaster')->row_array();
    }

    function save_item_order_detail()
    {
        $projectExist = project_is_exist();
        $itemAutoIDs = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $itemAutoIDJoin = join(',', $itemAutoIDs);
        $text_type = $this->input->post('text_type');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');
        $ap_amount = 0;

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID,isBackToBack,retentionPercentage');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();

        //        if (!trim($this->input->post('contractDetailsAutoID') ?? '')) {
        //            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
        //            $this->db->from('srp_erp_contractdetails');
        //            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        //            $this->db->where('itemAutoID IN (' . $itemAutoIDJoin . ')');
        //            $order_detail = $this->db->get()->row_array();
        //            if (!empty($order_detail)) {
        //                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
        //            }
        //        }

        if($contract_master && $contract_master['isBackToBack'] == 1){

            $this->db->select('ap_amount');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $contract_details = $this->db->get()->row_array();

            if($contract_details){
                $ap_amount = $contract_details['ap_amount'];
            }

        }

        if($ap_amount <= 0){
            $ap_amount = 0;
        }
    
        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data(trim($itemAutoID));
            $projectID = $this->input->post('projectID');
            $uom = explode('|', $uoms[$key]);
            $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['itemCategory'] = $item_arr['mainCategory'];
            $data['unitOfMeasure'] = trim($uom[0] ?? '');
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['itemReferenceNo'] = trim($itemReferenceNo[$key]);
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $data['noOfItems'] = trim($noOfItems[$key] ?? '');
            $data['requestedQty'] = trim($quantityRequested[$key] ?? '');
            $data['unittransactionAmount'] = (trim($estimatedAmount[$key]) - $data['discountAmount']);



            $data['unitAmount'] = (trim($estimatedAmount[$key]) - $data['discountAmount']);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
            $data['poUnitPrice'] = ($data['unittransactionAmount'] * $data['requestedQty']);

            if($contract_master && $contract_master['isBackToBack'] == 1){
                $data['transactionAmount'] = ($data['poUnitPrice'] * $ap_amount);
                $data['salesPriceTotal'] = ($data['poUnitPrice'] * $ap_amount);
                $data['retensionValue'] = ((($data['poUnitPrice'] * $ap_amount) * $contract_master['retentionPercentage']) / 100 );
            }

            $data['ap_amount'] = $ap_amount;
            $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);

            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];


            $this->db->insert('srp_erp_contractdetails', $data);
            $last_id = $this->db->insert_id();
                
            if(!empty($text_type[$key])){
                if($isGroupByTax == 1){ 

                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$text_type[$key]);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
                    $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();
            
                    $dataTax['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
                    $dataTax['taxFormulaMasterID'] = $text_type[$key];
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

                    $total_doc_amount = trim($estimatedAmount[$key]) * $data['requestedQty'];
                    $discountAmount = $discount_amount[$key] * $quantityRequested[$key];
                    tax_calculation_vat('srp_erp_contracttaxdetails',$dataTax,$text_type[$key],'contractAutoID',trim($this->input->post('contractAutoID') ?? ''),$total_doc_amount,'CNT',$last_id,$discountAmount,1);
                }             
            }
        }

        // if (!empty($this->input->post('contractDetailsAutoID'))) {
        //     $data[$key]['contractDetailsAutoID'] = trim($this->input->post('contractDetailsAutoID') ?? '');
        //     $this->db->update_batch('srp_erp_contractdetails', $data, 'contractDetailsAutoID');

        
        //     if($isGroupByTax == 1) {
        //         $this->db->select('SUM(transactionAmount) as amount');
        //         $this->db->from('srp_erp_contractdetails');
        //         $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        //         $total_doc_amount = $this->db->get()->row('amount');
        //         tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($this->input->post('contractAutoID') ?? ''), $total_doc_amount, 0, 'CNT');
        //     }

        //     $this->db->trans_complete();
        //     if ($this->db->trans_status() === FALSE) {
        //         $this->db->trans_rollback();
        //         return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
        //     } else {
        //         $this->db->trans_commit();
        //         return array('s', 'Order Detail :  Updated Successfully.');
        //     }
        // } else {
            // $this->db->insert_batch('srp_erp_contractdetails', $data);
            
         
            if($isGroupByTax == 1) {
                $this->db->select('SUM(transactionAmount) as amount');
                $this->db->from('srp_erp_contractdetails');
                $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                $total_doc_amount = $this->db->get()->row('amount');
                tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($this->input->post('contractAutoID') ?? ''), $total_doc_amount, 0, 'CNT');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();

                if($contract_master && $contract_master['isBackToBack'] == 1){
                    $_POST['ap_unit'] = $ap_amount;
                    $_POST['contractAutoID'] = $this->input->post('contractAutoID');
                    $this->update_ap_value();
                }
                
                return array('s', 'Order Detail : Saved Successfully.');
            }
        // }
    }

    function save_item_order_detail_job()
    {
        $projectExist = project_is_exist();
        $itemAutoIDs = $this->input->post('itemAutoID');
        $AdvanceCostCapture = getPolicyValues('ACC','All');
        $itemname = $this->input->post('search');
        $pOrService = $this->input->post('pOrService');
        $mainCategoryID = $this->input->post('mainCategoryID');
        $subcategoryID = $this->input->post('subcategoryID');
        $subsubcategoryID = $this->input->post('subsubcategoryID');
        $revanueGLAutoID = $this->input->post('revanueGLAutoID');
        $costGLAutoID = $this->input->post('costGLAutoID');
        $item = $this->input->post('itemID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $groupToCategory = $this->input->post('groupToCategory');
        
        if($AdvanceCostCapture==1){
            $DetailctivityCode = $this->input->post('DetailctivityCode');
        }
       // $itemAutoIDJoin = join(',', $itemAutoIDs);
        $text_type = $this->input->post('text_type');
        $isAmendment = $this->input->post('isAmendment');
        $itemarticleNo = $this->input->post('itemarticleNo');
        $itemrefernumber= $this->input->post('itemrefeNo');
     


        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID,currentAmedmentID,activityID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $amendmentID = $contract_master['currentAmedmentID'];

        //get amendment details
        $this->db->select('*');
        $this->db->where('id', $amendmentID);
        $amendment_details = $this->db->get('srp_erp_document_amendments')->row_array();

        if($amendment_details){
            $amendment_status = $amendment_details['status'];
            $amendmentType_arr = explode(',',$amendment_details['amendmentType']);

            if($amendment_status == 0 && !in_array(1,$amendmentType_arr)){
                return array('e', 'Price change not been allowed in this Amendment.');
            }
        }

        if($isAmendment && $amendment_status == 1){
            return array('e', 'Open a Amendment for Add changes');
        }


        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();

        //        if (!trim($this->input->post('contractDetailsAutoID') ?? '')) {
        //            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
        //            $this->db->from('srp_erp_contractdetails');
        //            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        //            $this->db->where('itemAutoID IN (' . $itemAutoIDJoin . ')');
        //            $order_detail = $this->db->get()->row_array();
        //            if (!empty($order_detail)) {
        //                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
        //            }
        //        }
        $this->db->trans_start();
        foreach ($itemname as $key => $itemAutoID) {
           // $item_arr = fetch_item_data(trim($itemAutoID));
            $projectID = $this->input->post('projectID');
          //  $uom = explode('|', $uoms[$key]);
            $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

           /// $data['itemAutoID'] = trim($itemAutoID);

          //  $data['itemSystemCode'] = $item_arr['itemSystemCode'];
           // $data['itemDescription'] = $item_arr['itemDescription'];
           // $data['itemCategory'] = $item_arr['mainCategory'];

           $data['itemAutoID'] = $item[$key];
           $data['typeItemName'] = $itemname[$key];
           $data['pOrService'] = $pOrService[$key];
           $data['mainCategoryID'] = $mainCategoryID[$key];
           $data['subcategoryID'] = $subcategoryID[$key];
           $data['subsubcategoryID'] = $subsubcategoryID[$key];
           $data['revanueGLAutoID'] = $revanueGLAutoID[$key];
           $data['costGLAutoID'] = $costGLAutoID[$key];
           $data['categoryGroupID'] = $groupToCategory[$key];
           if($AdvanceCostCapture==1){
                if($DetailctivityCode[$key]){
                    $data['activityCodeID'] = $DetailctivityCode[$key];
                }else{
                    $data['activityCodeID'] = $contract_master['activityID'];
                }
            
           }
           $this->db->select('UnitID,UnitShortCode,UnitDes');
           $this->db->from('srp_erp_unit_of_measure');
           $this->db->where('UnitID', $UnitOfMeasureID[$key]);
           $uom_select = $this->db->get()->row_array();

            $data['unitOfMeasure'] = $uom_select['UnitShortCode'];
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['itemReferenceNo'] = trim($itemReferenceNo[$key]);
            $data['itemarticleNo'] = trim($itemarticleNo[$key]);
            $data['itemReferNo'] = trim($itemrefernumber[$key]);
           // $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
           // $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
           // $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $data['noOfItems'] = trim($noOfItems[$key]);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = (trim($estimatedAmount[$key]) - $data['discountAmount']);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
            $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
           

            $this->db->insert('srp_erp_contractdetails', $data);
            $last_id = $this->db->insert_id();
                
            if(!empty($text_type[$key])){
                if($isGroupByTax == 1){ 

                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$text_type[$key]);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
                    $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();
            
                    $dataTax['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
                    $dataTax['taxFormulaMasterID'] = $text_type[$key];
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

                    $total_doc_amount = trim($estimatedAmount[$key]) * $data['requestedQty'];
                    $discountAmount = $discount_amount[$key] * $quantityRequested[$key];
                    tax_calculation_vat('srp_erp_contracttaxdetails',$dataTax,$text_type[$key],'contractAutoID',trim($this->input->post('contractAutoID') ?? ''),$total_doc_amount,'CNT',$last_id,$discountAmount,1);
                }             
            }
        }

        // if (!empty($this->input->post('contractDetailsAutoID'))) {
        //     $data[$key]['contractDetailsAutoID'] = trim($this->input->post('contractDetailsAutoID') ?? '');
        //     $this->db->update_batch('srp_erp_contractdetails', $data, 'contractDetailsAutoID');

        
        //     if($isGroupByTax == 1) {
        //         $this->db->select('SUM(transactionAmount) as amount');
        //         $this->db->from('srp_erp_contractdetails');
        //         $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        //         $total_doc_amount = $this->db->get()->row('amount');
        //         tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($this->input->post('contractAutoID') ?? ''), $total_doc_amount, 0, 'CNT');
        //     }

        //     $this->db->trans_complete();
        //     if ($this->db->trans_status() === FALSE) {
        //         $this->db->trans_rollback();
        //         return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
        //     } else {
        //         $this->db->trans_commit();
        //         return array('s', 'Order Detail :  Updated Successfully.');
        //     }
        // } else {
            // $this->db->insert_batch('srp_erp_contractdetails', $data);
            
         
            if($isGroupByTax == 1) {
                $this->db->select('SUM(transactionAmount) as amount');
                $this->db->from('srp_erp_contractdetails');
                $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                $total_doc_amount = $this->db->get()->row('amount');
                tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($this->input->post('contractAutoID') ?? ''), $total_doc_amount, 0, 'CNT');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail : Saved Successfully.');
            }
        // }
    }


    function confirm_payment_application($autoID){
        $data['confirmedYN']= 1;
        $this->db->where('autoID', $autoID);
        $this->db->update('srp_erp_payment_application', $data);
        $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Confirmation failed' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Confirmation Success');
                }
    }

    function update_payment_application_item_details($PADetailsAutoID,$currentQty,$netUnitPrice,$prevQty,$PAcuQty){
        $data['currentQty']= $currentQty;
        $data['PAcuQty']= ($currentQty + $prevQty);
        $data['currentAmount']= ($currentQty * $netUnitPrice);
        $dataCum['cumilativeAmount']= ($PAcuQty * $netUnitPrice);

        $this->db->where('PADetailsAutoID', $PADetailsAutoID);
        $this->db->update('srp_erp_payment_application_details', $data);
        $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Failed' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    $result = $this->db->query("SELECT PAcuQty FROM srp_erp_payment_application_details WHERE PADetailsAutoID= $PADetailsAutoID")->row_array();
                    $PAcuQtyUpdatedValue =$result['PAcuQty'];
                    $dataCum['cumilativeAmount']= ($PAcuQtyUpdatedValue * $netUnitPrice);
                    $this->db->where('PADetailsAutoID', $PADetailsAutoID);
                    $this->db->update('srp_erp_payment_application_details', $dataCum);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        return array('e', 'Failed' . $this->db->_error_message());
                    } else {
                        $this->db->trans_commit();
                        return array('s', 'Success');
                    }                    
                }
    }

    function update_item_order_detail()
    {
        $text_type = $this->input->post('text_type');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');
        $AdvanceCostCapture = getPolicyValues('ACC','All');

        if($this->input->post('itemID')){
            $itemAutoID = $this->input->post('itemID');
        }else{
            $itemAutoID = $this->input->post('itemAutoID');
        }
      
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
       
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');
        $projectExist = project_is_exist();
        $projectID = $this->input->post('projectID');
        $isAmendment = $this->input->post('isAmendment');
        $itemarticleNo = $this->input->post('itemarticleNo');
        $itemrefernumber= $this->input->post('itemrefeNo');
        $DetailctivityCode_edit= $this->input->post('DetailctivityCode_edit');

        $amendment_status = 0;
        //        if (!empty($this->input->post('contractDetailsAutoID'))) {
        //            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
        //            $this->db->from('srp_erp_contractdetails');
        //            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        //            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
        //            $this->db->where('contractDetailsAutoID !=', trim($this->input->post('contractDetailsAutoID') ?? ''));
        //            $order_detail = $this->db->get()->row_array();
        //            if (!empty($order_detail)) {
        //                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
        //            }
        //        }
        $this->db->select('companyLocalExchangeRate,approvedYN,companyReportingExchangeRate,customerCurrencyExchangeRate, customerID,transactionCurrencyID,currentAmedmentID,isBackToBack,retentionPercentage');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();
       
        $amendmentID = $contract_master['currentAmedmentID'];

        //get amendment details
        $this->db->select('*');
        $this->db->where('id', $amendmentID);
        $amendment_details = $this->db->get('srp_erp_document_amendments')->row_array();

        if($amendment_details){
            $amendment_status = $amendment_details['status'];
            $amendmentType_arr = explode(',',$amendment_details['amendmentType']);

            if($amendment_status == 0 && !in_array(1,$amendmentType_arr)){
                return array('e', 'Price change not been allowed in this Amendment.');
            }

        }

        if($isAmendment && $amendment_status == 1){
            return array('e', 'Please open an Amedment to Do the changes.');
        }

        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();

        $companyid = $this->common_data['company_data']['company_id'];
        $contractDetailsAutoID = $this->input->post('contractDetailsAutoID');

        $savedAmount = $this->db->query("SELECT (srp_erp_contractdetails.transactionAmount / companyLocalExchangeRate) AS Amount 
                                    FROM srp_erp_contractdetails
                                    LEFT JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID
                                    WHERE srp_erp_contractdetails.companyID = {$companyid} AND contractDetailsAutoID = {$contractDetailsAutoID}")->row_array();
       

        $customerCreditLimit = CustomerCreditLimit($contract_master['customerID']);

        $this->db->trans_start();

        $item_arr = fetch_item_data(trim($itemAutoID));
        // $uom = explode('|', $uoms);
        $data['categoryGroupID'] = trim($this->input->post('groupToCategory_edit') ?? '');
        $data['typeItemName'] = trim($this->input->post('search') ?? '');
        $data['pOrService'] = trim($this->input->post('pOrService') ?? '');
        $data['mainCategoryID'] = trim($this->input->post('mainCategoryID') ?? '');
        $data['subcategoryID'] =trim($this->input->post('subcategoryID') ?? '');
        $data['subsubcategoryID'] =trim($this->input->post('subsubcategoryID') ?? '');
        $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
        $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemID') ?? '');
        $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['itemCategory'] = $item_arr['mainCategory'];
        // $data['unitOfMeasure'] = $this->input->post('UnitOfMeasureID');
        $this->db->select('UnitID,UnitShortCode,UnitDes');
        $this->db->from('srp_erp_unit_of_measure');
        $this->db->where('UnitID', $this->input->post('UnitOfMeasureID'));
        $uom_select = $this->db->get()->row_array();

        $data['unitOfMeasure'] = $uom_select['UnitShortCode'];
        $data['unitOfMeasureID'] = $this->input->post('UnitOfMeasureID');
        $data['itemReferenceNo'] = trim($itemReferenceNo);
        $data['itemarticleNo'] = trim($itemarticleNo);
        $data['itemReferNo'] = trim($itemrefernumber);
        // $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        // $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
       /// $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($discount);
        $data['discountAmount'] = $discount_amount;
        $data['noOfItems'] = trim($noOfItems);
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = (trim($estimatedAmount) - $data['discountAmount']);
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);

        if($contract_master && $contract_master['isBackToBack'] == 1) {

            $data['retensionValue'] =  ($data['transactionAmount'] * $contract_master['retentionPercentage']) / 100;

        }

        $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
        $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
        $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if($AdvanceCostCapture==1){
            $data['activityCodeID'] = $DetailctivityCode_edit;
        }
        
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        /*        $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];*/

        if($customerCreditLimit['assigned'] == 1) {
            $customerCreditLimit['amount'] = $customerCreditLimit['amount'] + $savedAmount['Amount'];
        }
        //        var_dump($customerCreditLimit['amount'] .'---'.$data['companyLocalAmount']);


       /* if ($customerCreditLimit['assigned'] == 1 && $customerCreditLimit['amount'] < $data['companyLocalAmount']){
            return array('e', 'Customer Credit Limit Exceeded. (' . $this->common_data['company_data']['company_default_currency'] . ': ' . number_format($customerCreditLimit['amount'],$this->common_data['company_data']['company_default_decimal']) . ')');
        }*/ /*else {*/
            if (!empty($this->input->post('contractDetailsAutoID'))) {

                if($amendment_status == 0 && $isAmendment){

                    if ($contract_master['approvedYN'] == 1) {
                        $data_update = array();
                        $data_update['status'] = 0;
                        $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID') ?? ''));
                        $this->db->update('srp_erp_contractdetails', $data_update);
                
                       
                        $data['amendmentID'] = $amendmentID;
                        $this->db->insert('srp_erp_contractdetails', $data);
                    } else {
                        
                        $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID') ?? ''));
                        $this->db->update('srp_erp_contractdetails', $data);
                    }

                }else{
                    $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID') ?? ''));
                    $this->db->update('srp_erp_contractdetails', $data);
                }
               

                if(empty($text_type)) 
                {
                    fetchExistsDetailTBL('CNT', trim($this->input->post('contractAutoID') ?? ''),trim($this->input->post('contractDetailsAutoID') ?? ''),'srp_erp_contracttaxdetails',1,$data['transactionAmount']);
                } else if($isGroupByTax == 1 && !empty($text_type)){ 
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$text_type);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
                            
                   
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
                    $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();
            
                    $dataTax['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
                    $dataTax['taxFormulaMasterID'] = $text_type;
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
                    $dataTax['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataTax['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataTax['createdUserGroup'] = $this->common_data['user_group'];
                    $dataTax['createdPCID'] = $this->common_data['current_pc'];
                    $dataTax['createdUserID'] = $this->common_data['current_userID'];
                    $dataTax['createdUserName'] = $this->common_data['current_user'];
                    $dataTax['createdDateTime'] = $this->common_data['current_date'];
                    
                    $total_doc_amount = trim($estimatedAmount) * $data['requestedQty'];
                    $discountAmont = $discount_amount * $quantityRequested;
                    tax_calculation_vat('srp_erp_contracttaxdetails',$dataTax,$text_type,'contractAutoID',trim($this->input->post('contractAutoID') ?? ''),$total_doc_amount,'CNT',$this->input->post('contractDetailsAutoID'),$discountAmont,1);

                    tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($this->input->post('contractAutoID') ?? ''), $total_doc_amount, 0, 'CNT');
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Order Detail :  Updated Successfully.');
                }
            }
      /*  }*/
    }

    function update_item_order_detail_buyback()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');

     /*   if (!empty($this->input->post('contractDetailsAutoID'))) {
            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
            $this->db->where('contractDetailsAutoID !=', trim($this->input->post('contractDetailsAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }*/
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate, customerID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $companyid = $this->common_data['company_data']['company_id'];
        $contractDetailsAutoID = $this->input->post('contractDetailsAutoID');
        $savedAmount = $this->db->query("SELECT (srp_erp_contractdetails.transactionAmount / companyLocalExchangeRate) AS Amount 
        FROM
        srp_erp_contractdetails
        LEFT JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID
	 WHERE srp_erp_contractdetails.companyID = {$companyid} AND contractDetailsAutoID = {$contractDetailsAutoID}")->row_array();

        $customerCreditLimit = CustomerCreditLimit($contract_master['customerID']);

        $this->db->trans_start();

        $item_arr = fetch_item_data(trim($itemAutoID));
        $uom = explode('|', $uoms);
        $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['itemCategory'] = $item_arr['mainCategory'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($UnitOfMeasureID);
        $data['itemReferenceNo'] = trim($itemReferenceNo);
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($discount);
        $data['discountAmount'] = $discount_amount;
        $data['noOfItems'] = trim($noOfItems);
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = (trim($estimatedAmount) - $data['discountAmount']);
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
        $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
        $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
        $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        /*        $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];*/

        if($customerCreditLimit['assigned'] == 1) {
            $customerCreditLimit['amount'] = $customerCreditLimit['amount'] + $savedAmount['Amount'];
        }
        //        var_dump($customerCreditLimit['amount'] .'---'.$data['companyLocalAmount']);


        if ($customerCreditLimit['assigned'] == 1 && $customerCreditLimit['amount'] < $data['companyLocalAmount']){
            return array('e', 'Customer Credit Limit Exceeded. (' . $this->common_data['company_data']['company_default_currency'] . ': ' . number_format($customerCreditLimit['amount'],$this->common_data['company_data']['company_default_decimal']) . ')');
        } else {
            if (!empty($this->input->post('contractDetailsAutoID'))) {
                $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID') ?? ''));
                $this->db->update('srp_erp_contractdetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Order Detail :  Updated Successfully.');
                }
            }
        }
    }


    function set_contract_extra_charge(){

        $contractAutoID = $this->input->post('contractAutoID');
        $extraChargeID = $this->input->post('extraChargeID');
        $extraChargeValue = $this->input->post('extraChargeValue');

        if($extraChargeID){

            $this->db->select('SUM(transactionAmount) as amount');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', $contractAutoID);
            $contract_details = $this->db->get()->row_array();

            $this->db->select('*');
            $this->db->from('srp_erp_contractmaster');
            $this->db->where('contractAutoID', $contractAutoID);
            $contract_master = $this->db->get()->row_array();

            if($contract_details['amount'] == 0){

                return array('e', 'Please add the details records first.');
                exit;

            }else{
                $this->update_total_po_value($contractAutoID);
            }


            $this->db->select('*');
            $this->db->from('srp_erp_discountextracharges');
            $this->db->where('discountExtraChargeID', $extraChargeID);
            $extraChargeDetail = $this->db->get()->row_array();
            
            if($extraChargeDetail){

                $data = array();
                
                $data['contractAutoID'] = $contractAutoID;
                $data['extraCostID'] = $extraChargeID;
                $data['extraCostName'] = $extraChargeDetail['Description'];
                $data['markup_percentage'] = $contract_master['marginPercentage'];
                $data['markup_value'] =  ($extraChargeValue * $contract_master['marginPercentage']) / 100;
                $data['top_margin_value'] = $data['markup_value'];
                $data['extraCostValue'] = $extraChargeValue;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $this->db->insert('srp_erp_contractextracharges', $data);
      
            }

            return array('s', 'Successfully added the Record');
            exit;

        }

    }

    function update_total_po_value($contractAutoID){

        $this->db->select('SUM(poUnitPrice) as amount');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', $contractAutoID);
        $contract_details = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $contractAutoID);
        $contract_master = $this->db->get()->row_array();

        //check type 2 exists
        $this->db->select('*');
        $this->db->from('srp_erp_contractextracharges');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->where('type', 2);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $po_cost_record = $this->db->get()->row_array();

        $data = array();

        if($po_cost_record){

            $record_id = $po_cost_record['id'];

            $data['extraCostValue'] = $contract_details['amount'];

            $this->db->where('id',$record_id)->update('srp_erp_contractextracharges',$data);

        }else{

            $data['type'] = 2;
            $data['contractAutoID'] = $contractAutoID;
            $data['extraCostID'] = -1;
            $data['extraCostName'] = 'Total PO Cost';
            $data['markup_percentage'] = $contract_master['marginPercentage'];
            $data['markup_value'] =  ($contract_details['amount'] * $contract_master['marginPercentage']) / 100;
            $data['top_margin_value'] = $data['markup_value'];
            $data['extraCostValue'] = $contract_details['amount'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_contractextracharges',$data);
        }
        
        return true;

    }

    function get_extra_charges_records($contractAutoID,$type = 1){

        $compayLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $compayLocalCurrency = $this->common_data['company_data']['company_default_currency'];

        // LEFT JOIN srp_erp_contractmaster as master ON charge.contractAutoID = master.contractAutoID
        $this->db->select('srp_erp_contractextracharges.*,srp_erp_contractmaster.transactionCurrencyID,srp_erp_contractmaster.transactionCurrency');
        $this->db->from('srp_erp_contractextracharges');
        $this->db->join('srp_erp_contractmaster','srp_erp_contractextracharges.contractAutoID = srp_erp_contractmaster.contractAutoID');
        $this->db->where('srp_erp_contractextracharges.contractAutoID', $contractAutoID);
        $this->db->where('srp_erp_contractextracharges.type', $type);
        $this->db->where('srp_erp_contractextracharges.companyID', $this->common_data['company_data']['company_id']);
        $extraChargeDetail = $this->db->get()->result_array();

        foreach($extraChargeDetail as $key => $value){
            $conversion = currency_conversionID($compayLocalCurrencyID,$value['transactionCurrencyID'],$value['extraCostValue']);
            $extraChargeDetail[$key]['extraCostValueAED'] = $conversion['convertedAmount'];
        }

        return $extraChargeDetail;

    }

    function get_contract_details($contractAutoID){

        $this->db->select('detail.*');
        $this->db->from('srp_erp_contractdetails as detail');
        $this->db->where('detail.contractAutoID', $contractAutoID);
        $this->db->where('detail.companyID', $this->common_data['company_data']['company_id']);
        $contract_details = $this->db->get()->result_array();

        return $contract_details;
        
    }

    function get_customer_order_details(){
        
        $customerOrderID = $this->input->post('customerOrderID');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, DATE_FORMAT(bidStartDate,\'' . $convertFormat . '\') AS bidStartDate, DATE_FORMAT(bidEndDate,\'' . $convertFormat . '\') AS bidEndDate,DATE_FORMAT(expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('customerOrderID', $this->input->post('customerOrderID'));
        $data = $this->db->get('srp_erp_srm_customerordermaster')->row_array();
        return $data;

    }

    function get_purchase_order_details(){
        
        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $data = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        return $data;

    }

    function fetch_item_detail()
    {
        $wareHouseAutoID = $this->input->post('warehouseAutoid');
        $where_filter = '';
        if($wareHouseAutoID) {
            $where_filter = 'Where wareHouseAutoID = '.$wareHouseAutoID.'';
        }
        $item_code_alias = "CONCAT(srp_erp_itemmaster.itemSystemCode, ' - ', srp_erp_itemmaster.seconeryItemCode) as itemSystemCode";

        $this->db->select("srp_erp_contractdetails.*,srp_erp_itemmaster.itemName as itemname,itemledgercurrent.currentstock AS itemledstock, $item_code_alias");
        $this->db->from('srp_erp_contractdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_contractdetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'LEFT');
        $this->db->join("(SELECT IF (mainCategory = 'Inventory',  (SUM(transactionQTY/ convertionRate)),\" \") AS currentstock, srp_erp_itemledger.itemAutoID 
                            FROM `srp_erp_itemledger`
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
                            $where_filter
                            GROUP BY srp_erp_itemledger.itemAutoID 
                          )itemledgercurrent","itemledgercurrent.itemAutoID = srp_erp_contractdetails.itemAutoID ","left");
        $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_item_detail()
    {
       $contractDetailsAutoID = $this->input->post('contractDetailsAutoID');
       $contractID = $this->db->query("SELECT contractAutoID FROM srp_erp_contractdetails WHERE contractDetailsAutoID = $contractDetailsAutoID")->row('contractAutoID');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($contractID),'CNT', 'contractAutoID');
        if($isGroupByTax == 1) {
            fetchExistsDetailTBL('CNT',$contractID,$this->input->post('contractDetailsAutoID'),'srp_erp_contracttaxdetails');
            $this->db->delete('srp_erp_taxledger', array('documentDetailAutoID' => $this->input->post('contractDetailsAutoID'), 'documentMasterAutoID' => $contractID, 'documentID' => 'CNT'));
            
            $this->db->select('SUM(transactionAmount) as amount');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($contractID));
            $this->db->where('contractDetailsAutoID !=', trim($contractDetailsAutoID));
            $total_doc_amount = $this->db->get()->row('amount');
            if(empty($total_doc_amount)) {
                $total_doc_amount = 0;
            }
            tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($contractID), $total_doc_amount, 0, 'CNT');
        }
        $this->db->delete('srp_erp_contractdetails', array('contractDetailsAutoID' => trim($this->input->post('contractDetailsAutoID') ?? '')));
        return true;
    }

    function delete_extra_charge_entry(){
       try {
            $id = $this->input->post('id');
            $type = $this->input->post('type');

            if($type == 'PO'){
                $this->db->delete('srp_erp_purchaseorderextracharges', array('id' => $id));
            }else{
                $this->db->delete('srp_erp_contractextracharges', array('id' => $id));
            }
            return array('s', 'Deleted Successfully.');
       
        } catch (\Throwable $th) {
            return array('e', 'Something went wrong.');
       }
       
    }

    function contract_confirmation()
    {
        $this->db->select('contractDetailsAutoID');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $this->db->from('srp_erp_contractdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_contractmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $contractAutoID = trim($this->input->post('contractAutoID') ?? '');

                $this->load->library('Approvals');
                $this->db->select('documentID,contractType,contractCode,customerCurrencyExchangeRate,companyReportingExchangeRate, companyLocalExchangeRate ,contractAutoID,transactionCurrencyDecimalPlaces,contractDate');
                $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                $this->db->from('srp_erp_contractmaster');
                $c_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($c_data['contractCode'], 'contractCode', $contractAutoID,'contractAutoID', 'srp_erp_contractmaster');
                if(!empty($validate_code)) {
                    return array(['e', 'The document Code Already Exist.(' . $validate_code . ')']);
                }

                $autoApproval= get_document_auto_approval($c_data['documentID']);
                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($c_data['contractAutoID'], 'srp_erp_contractmaster','contractAutoID', $c_data['documentID'],$c_data['contractCode'], $c_data['contractDate']);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['contractAutoID'], $c_data['contractCode'], $c_data['contractType'], 'srp_erp_contractmaster', 'contractAutoID', 1, $c_data['contractDate']);
                }else{
                    return array('e', 'Approval levels are not set for this document');
                    exit;
                }

                if ($approvals_status) {
                    $this->db->select_sum('transactionAmount');
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                    $total = $this->db->get('srp_erp_contractdetails')->row('transactionAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($total, $c_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($total / $c_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($total / $c_data['companyReportingExchangeRate']),
                        'customerCurrencyAmount' => ($total / $c_data['customerCurrencyExchangeRate']),
                    );
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                    $this->db->update('srp_erp_contractmaster', $data);
                    /*$this->session->set_flashdata('s', 'Create Approval : ' . $c_data['contractCode'] . ' Approvals Created Successfully ');
                    return true;*/
                    if($autoApproval==0) {
                        $result = $this->save_quotation_contract_approval(0, $c_data['contractAutoID'], 1, 'Auto Approved');
                        if($result){
                            return array('s', 'Approvals Created Successfully.');
                        }
                    }else{
                        return array('s', 'Approvals Created Successfully.');
                    }
                } else {
                    /*return false;*/
                    return array('e', 'oops, something went wrong!.');
                }
            }
        }
    }

    function save_quotation_contract_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('contractAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
            $code = trim($this->input->post('code') ?? '');
        }else{



            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        $this->db->select('documentID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $system_code);
        $code = $this->db->get()->row('documentID');

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $code);
        }

        if ($approvals_status == 1) {
            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            // $this->db->update('srp_erp_creditnotemaster', $data);
            $this->session->set_flashdata('s', 'Approval Successfully.');
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

    function delete_con_master()
    {
        //$this->db->delete('srp_erp_contractmaster', array('isDeleted' => 1,'deletedEmpID' => current_userID(),'deletedDate' => current_date()));
        //$this->db->delete('srp_erp_contractdetails', array('contractAutoID' => trim($this->input->post('contractAutoID') ?? '')));
        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->update('srp_erp_contractmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function quotation_version()
    {
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $this->db->select('invoiceAutoID');
        $this->db->where('contractAutoID', $contractAutoID);
        $inv_data = $this->db->get('srp_erp_customerinvoicedetails')->row_array();
        if (!empty($inv_data)) {
            return array('status' => 0, 'type' => 'w', 'message' => 'You cannot create versions for this quotation. Invoice has been created already.');
        }
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $srp_erp_contractmaster = $this->db->get('srp_erp_contractmaster')->row_array();
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $srp_erp_contractdetails = $this->db->get('srp_erp_contractdetails')->result_array();

        $this->db->insert('srp_erp_contractversion', $srp_erp_contractmaster);
        $last_id = $this->db->insert_id();

        $version_detail = array();
        foreach ($srp_erp_contractdetails as $key => $val) {
            $version_detail[$key]['versionAutoID'] = $last_id;
            $version_detail[$key]['contractDetailsAutoID'] = $val['contractDetailsAutoID'];
            $version_detail[$key]['contractAutoID'] = $val['contractAutoID'];
            $version_detail[$key]['itemAutoID'] = $val['itemAutoID'];
            $version_detail[$key]['itemSystemCode'] = $val['itemSystemCode'];
            $version_detail[$key]['itemDescription'] = $val['itemDescription'];
            $version_detail[$key]['itemReferenceNo'] = $val['itemReferenceNo'];
            $version_detail[$key]['itemCategory'] = $val['itemCategory'];
            $version_detail[$key]['defaultUOMID'] = $val['defaultUOMID'];
            $version_detail[$key]['defaultUOM'] = $val['defaultUOM'];
            $version_detail[$key]['unitOfMeasureID'] = $val['unitOfMeasureID'];
            $version_detail[$key]['unitOfMeasure'] = $val['unitOfMeasure'];
            $version_detail[$key]['conversionRateUOM'] = $val['conversionRateUOM'];
            $version_detail[$key]['requestedQty'] = $val['requestedQty'];
            $version_detail[$key]['invoicedYN'] = $val['invoicedYN'];
            $version_detail[$key]['comment'] = $val['comment'];
            $version_detail[$key]['remarks'] = $val['remarks'];
            $version_detail[$key]['unittransactionAmount'] = $val['unittransactionAmount'];
            $version_detail[$key]['discountPercentage'] = $val['discountPercentage'];
            $version_detail[$key]['discountTotal'] = $val['discountTotal'];
            $version_detail[$key]['transactionAmount'] = $val['transactionAmount'];
            $version_detail[$key]['companyLocalAmount'] = $val['companyLocalAmount'];
            $version_detail[$key]['companyReportingAmount'] = $val['companyReportingAmount'];
            $version_detail[$key]['customerAmount'] = $val['customerAmount'];
            $version_detail[$key]['companyID'] = current_companyID();
            $version_detail[$key]['companyCode'] = $val['companyCode'];
            $version_detail[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $version_detail[$key]['createdPCID'] = $this->common_data['current_pc'];
            $version_detail[$key]['createdUserID'] = $this->common_data['current_userID'];
            $version_detail[$key]['createdUserName'] = $this->common_data['current_user'];
            $version_detail[$key]['createdDateTime'] = $this->common_data['current_date'];

        }
        if(!empty($version_detail)){
            $this->db->insert_batch('srp_erp_contractversiondetails', $version_detail);
        }


        $this->db->query("UPDATE srp_erp_contractmaster SET versionNo = (versionNo +1) , confirmedYN=0 , approvedYN=0 WHERE contractAutoID='{$contractAutoID}'");
        $this->db->where('documentID', 'QUT');
        $this->db->where('documentSystemCode', $contractAutoID);
        $this->db->delete('srp_erp_documentapproved');
        return array('status' => 1, 'type' => 's', 'message' => 'New Version of Quotation Created Successfully.');
    }

    function document_drill_down_View_modal()
    {
        $this->db->select('srp_erp_customerinvoicedetails.invoiceAutoID,invoiceType,invoiceDate,invoiceCode,invoiceDueDate,customerName ,(contractAmount*(requestedQty/conversionRateUOM)) as contractAmount,transactionCurrencyDecimalPlaces');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $this->db->where('srp_erp_customerinvoicedetails.companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->result_array();
    }

    function save_inv_tax_detail()
    {
        $this->db->trans_start();
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');
        $tax_total=$this->input->post('tax_total');

        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        if($isGroupByTax == 1) {
            $this->db->select('*');
            $this->db->where('taxCalculationformulaID', $this->input->post('text_type'));
            $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
        } else {
            $this->db->select('*');
            $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
            $master = $this->db->get('srp_erp_taxmaster')->row_array();
        }
        
        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();        

        $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
        $data['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency'] = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if($isGroupByTax == 1){
            $data['taxFormulaMasterID'] = trim($this->input->post('text_type') ?? '');
            $data['taxDescription'] = $master['Description'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            return tax_calculation_vat('srp_erp_contracttaxdetails',$data,trim($this->input->post('text_type') ?? ''),'contractAutoID',$this->input->post('contractAutoID'),$tax_total,'CNT', null, 0, 0, 0);
        }else{
            $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
            $data['taxDescription'] = $master['taxDescription'];
            $data['taxShortCode'] = $master['taxShortCode'];
            $data['supplierAutoID'] = $master['supplierAutoID'];
            $data['supplierSystemCode'] = $master['supplierSystemCode'];
            $data['supplierName'] = $master['supplierName'];
            $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
            $data['supplierCurrency'] = $master['supplierCurrency'];
            $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $data['GLAutoID'] = $master['supplierGLAutoID'];
            $data['systemGLCode'] = $master['supplierGLSystemGLCode'];
            $data['GLCode'] = $master['supplierGLAccount'];
            $data['GLDescription'] = $master['supplierGLDescription'];
            $data['GLType'] = $master['supplierGLType'];
            $data['taxPercentage'] = trim($this->input->post('percentage') ?? '');
            $data['transactionAmount'] = trim($this->input->post('amount') ?? '');
    
            $supplierCurrency = currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
            $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
            $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

            if (trim($this->input->post('taxDetailAutoID') ?? '')) {
                $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID') ?? ''));
                $this->db->update('srp_erp_contracttaxdetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === 0) {
                    $this->db->trans_rollback();
                    return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ');
                } else {
                    $this->db->trans_commit();
                    return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.', 'last_id' => $this->input->post('taxDetailAutoID'));
                }
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_contracttaxdetails', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Save Failed ');
                } else {
                    $this->db->trans_commit();
                    return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.', 'last_id' => $last_id);
                }
            }
        }
    }

    function load_unitprice_exchangerate()
    { //get localwac amount into exchange rate

        $localwacAmount = trim($this->input->post('LocalWacAmount') ?? '');
        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round(($localwacAmount / $localCurrency['conversion']), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function delete_quotationContract_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";

        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function re_open_contract()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $this->db->update('srp_erp_contractmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'Qut');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function loademail()
    {
        $contractautoid = $this->input->post('contractAutoID');
        $this->db->select('srp_erp_contractmaster.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('contractAutoID', $contractautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->db->from('srp_erp_contractmaster ');
        return $this->db->get()->row_array();
    }

    function send_quatation_email()
    {
        $contractid = trim($this->input->post('contractid') ?? '');
        $contractemail = trim($this->input->post('email') ?? '');
        $this->db->select('srp_erp_contractmaster.*,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerName as customerName');
        $this->db->where('contractAutoID', $contractid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->db->from('srp_erp_contractmaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['customerEmail'] == '') {
                $data_master['customerEmail'] = $contractemail;
                $this->db->where('customerAutoID', $results['customerID']);
                $this->db->update('srp_erp_customermaster', $data_master);
            }
        }
        $this->db->select('customerEmail,customerName');
        $this->db->where('customerAutoID', $results['customerID']);
        $this->db->from('srp_erp_customermaster ');
        $customerMaster = $this->db->get()->row_array();

        $data['approval'] = $this->input->post('approval');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractid);
        $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $data['printHeaderFooterYN']=1;
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH.base_url().'/uploads/qu/'. $contractid .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);


        if (!empty($customerMaster)) {
            if ($customerMaster['customerEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our '.$results["contractType"].' as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $contractemail,
                    'subject' => $results["contractType"]. ' for ' .$customerMaster['customerName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);
                return array('s', 'Email Send Successfully.',$contractemail,$contractid,$results["documentID"]);
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }
    }

    function save_item_order_detail_buyback()
    {
        $projectExist = project_is_exist();
        $itemAutoIDs = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');

        $itemAutoIDJoin = join(',', $itemAutoIDs);

        $this->db->select('customerID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $customerCreditLimit = CustomerCreditLimit($contract_master['customerID']);
       /* if (!trim($this->input->post('contractDetailsAutoID') ?? '')) {
            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->where('itemAutoID IN (' . $itemAutoIDJoin . ')');
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }*/
        $this->db->trans_start();
        $quatationTotal = 0;
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data(trim($itemAutoID));
            $projectID = $this->input->post('projectID');
            $uom = explode('|', $uoms[$key]);
            $data[$key]['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }

            $data[$key]['itemAutoID'] = trim($itemAutoID);
            $data[$key]['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data[$key]['itemDescription'] = $item_arr['itemDescription'];
            $data[$key]['itemCategory'] = $item_arr['mainCategory'];
            $data[$key]['unitOfMeasure'] = trim($uom[0] ?? '');
            $data[$key]['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data[$key]['itemReferenceNo'] = trim($itemReferenceNo[$key]);
            $data[$key]['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data[$key]['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data[$key]['conversionRateUOM'] = conversionRateUOM_id($data[$key]['unitOfMeasureID'], $data[$key]['defaultUOMID']);
            $data[$key]['discountPercentage'] = $discount[$key];
            $data[$key]['discountAmount'] = $discount_amount[$key];
            $data[$key]['noOfItems'] = trim($noOfItems[$key]);
            $data[$key]['requestedQty'] = trim($quantityRequested[$key]);
            $data[$key]['unittransactionAmount'] = (trim($estimatedAmount[$key]) - $data[$key]['discountAmount']);
            $data[$key]['transactionAmount'] = ($data[$key]['unittransactionAmount'] * $data[$key]['requestedQty']);
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data[$key]['customerAmount'] = ($data[$key]['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data[$key]['discountTotal'] = ($data[$key]['discountAmount'] * $data[$key]['requestedQty']);
            $data[$key]['comment'] = trim($comment[$key]);
            $data[$key]['remarks'] = trim($remarks[$key]);
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            $quatationTotal += ($data[$key]['transactionAmount']/$contract_master['companyLocalExchangeRate']);
        }

//        var_dump($customerCreditLimit['assigned'] . '==' . $customerCreditLimit['amount']);
        if($customerCreditLimit['assigned'] == 1 && $customerCreditLimit['amount'] < $quatationTotal){
            return array('e', 'Customer Credit Limit Exceeded. (' . $this->common_data['company_data']['company_default_currency'] . ': ' . number_format($customerCreditLimit['amount'],$this->common_data['company_data']['company_default_decimal']) . ')');
        } else {
            if (!empty($this->input->post('contractDetailsAutoID'))) {
                $data[$key]['contractDetailsAutoID'] = trim($this->input->post('contractDetailsAutoID') ?? '');
                $this->db->update_batch('srp_erp_contractdetails', $data, 'contractDetailsAutoID');
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Order Detail :  Updated Successfully.');
                }
            } else {

                $this->db->insert_batch('srp_erp_contractdetails', $data);
                $last_id = 0;
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Order Detail : Saved Successfully.');
                }
            }
        }
    }

    function open_delivery_order_modal(){
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['master']=$this->db->get()->row_array();

        $this->db->select("GLAutoID");
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('isDefaultlBank', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['GL']=$this->db->get()->row_array();

        //$customerID = $data['master']['customerID'];
        //$DOdate = current_date();
        //$currencyID = $data['master']['transactionCurrencyID'];

       // $dataw = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, (( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrencyID` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' AND srp_erp_customerinvoicemaster.invoiceAutoID = $invoiceAutoID ")->row_array();


        return $data;
    }
    function save_deliveryorder_from_quotation_contract_header()
    {
   
        $this->load->model('Receipt_voucher_model');

        $company_id = current_companyID();
        $contractAutoID=$this->input->post('contractAutoID');
        $warehouseAutoIDtemp = $this->input->post('warehouseAutoIDtemp');
        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $details = $this->db->get()->result_array();
        $ids = array();
        foreach ($details as $key2=>$det) {
            $balance = $this->db->query("SELECT
                                                srp_erp_contractdetails.contractAutoID,
                                                srp_erp_contractdetails.contractDetailsAutoID,
                                                srp_erp_contractdetails.itemAutoID,
                                                srp_erp_contractdetails.requestedQty AS requestedQtyTot,
                                                cinv.requestedQtyINV,
                                                ifnull( deliveryorder.requestedQtyDO, 0 ) AS requestedQtyDO,
                                                TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM (ROUND(
                                                    ifnull( srp_erp_contractdetails.requestedQty, 0 )/ifnull(srp_erp_contractdetails.conversionRateUOM, 1)
                                                    , 2 )))
                                                     - 
                                                    TRIM(TRAILING 0 FROM (ROUND(
                                                      ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ) 
                                                    , 2 )))) AS balance 
                                            FROM
                                                srp_erp_contractdetails
                                                LEFT JOIN (
                                                SELECT
                                                    contractAutoID,
                                                    contractDetailsAutoID,
                                                    itemAutoID,
                                                    IFNULL( SUM( requestedQty/conversionRateUOM ), 0 ) AS requestedQtyINV
                                                FROM
                                                    srp_erp_customerinvoicedetails 
                                                WHERE
                                                    contractAutoID IS NOT NULL 
                                                    AND contractAutoID = $contractAutoID 
                                                GROUP BY
                                                    contractDetailsAutoID 
                                                ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                                LEFT JOIN (
                                                SELECT
                                                    contractAutoID,
                                                    contractDetailsAutoID,
                                                    itemAutoID,
                                                    IFNULL( SUM( deliveredQty/conversionRateUOM), 0 ) AS requestedQtyDO
                                                FROM
                                                    srp_erp_deliveryorderdetails 
                                                WHERE
                                                    contractAutoID IS NOT NULL 
                                                    AND contractAutoID = $contractAutoID 
                                                GROUP BY
                                                    contractDetailsAutoID 
                                                ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                            WHERE
                                                srp_erp_contractdetails.contractAutoID IS NOT NULL 
                                                AND srp_erp_contractdetails.contractAutoID = $contractAutoID 
                                                AND srp_erp_contractdetails.itemAutoID = {$det['itemAutoID']}
                                            GROUP BY
                                                srp_erp_contractdetails.contractDetailsAutoID 
                                            HAVING
                                                balance >0 ")->row_array();
            $details[$key2]['balanceQtyVal'] = $balance['balance'] ?? 0;
            $warehouseQty = $this->db->query("  SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID 
                                     FROM srp_erp_itemledger 
                                     WHERE companyID = {$company_id} AND wareHouseAutoID = {$warehouseAutoIDtemp} AND itemAutoID = {$det['itemAutoID']}
                                     GROUP BY wareHouseAutoID, itemAutoID ")->row('currentStock');
            
            //$this->load->model('Receipt_voucher_model');
            $parkQty = 0;
            if($det['itemCategory'] == 'Inventory') {
                $Unapproved_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($det['itemAutoID'], $warehouseAutoIDtemp);
                $parkQty = $Unapproved_stock['Unapproved_stock'] ;
            }
            
            if(($warehouseQty - $parkQty) < $balance['balance'] ?? 0 ){
                array_push($ids, $det['itemAutoID']);
            }
        }
        if($this->input->post('confirm') == 1 && !empty($ids)) {
            
            $item_low_qty = $this->db->query("SELECT itemAutoID, itemSystemCode, itemDescription, IFNULL(ware.currentStock, 0) as currentStock
                                     FROM srp_erp_itemmaster 
                                     LEFT JOIN (
                                     	SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID as itemID
                                     FROM srp_erp_itemledger 
                                     WHERE companyID = {$company_id} AND wareHouseAutoID = {$warehouseAutoIDtemp}
                                     GROUP BY wareHouseAutoID, itemAutoID
                                     )ware ON ware.itemID = srp_erp_itemmaster.itemAutoID
                                     WHERE companyID = {$company_id} 
                                     AND (mainCategory != 'Service' AND mainCategory != 'Non Inventory') 
                                     AND itemAutoID IN ( " . join(',', $ids) . " )
                                     ")->result_array();
            if(!empty($item_low_qty)) {
                return array('w', 'Some Item quantities are not sufficient to confirm this transaction.', 'itemInsufficient' => $item_low_qty, 'in-suf-qty' => 'Y');
            }
        }

        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $DOdates = $this->input->post('DOdate');
        $DOdate = input_format_date($DOdates, $date_format_policy);
        $companyID = current_companyID();
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $this->db->select('contractDate,contractCode,segmentID,segmentCode,warehouseAutoID, isGroupBasedTax');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $contractAutoID);
        $delOrderdate=$this->db->get()->row_array();
        $warehouse  = $this->db->query("SELECT wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID = $warehouseAutoIDtemp")->row_array();

        if($DOdate>=$delOrderdate['contractDate']){
            if ($financeyearperiodYN == 1) {
                $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                $FYBegin = input_format_date($financeyr[0], $date_format_policy);
                $FYEnd = input_format_date($financeyr[1], $date_format_policy);
            } else {
                $financeYearDetails = get_financial_year($DOdate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($DOdate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                }
            }
            $this->db->select("segmentCode");
            $this->db->from('srp_erp_segment');
            $this->db->where('segmentID', $this->input->post('segment'));
            $segment = $this->db->get()->row_array();

            $currency_code = fetch_currency_code($this->input->post('transactionCurrencyID'));

            $data['documentID'] = 'DO';
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');

            $data['DOdate'] = trim($DOdate);
            $data['isGroupBasedTax'] = $delOrderdate['isGroupBasedTax'];
            $data['narration'] = trim_desc($this->input->post('Narration'));
            $data['segmentID'] = trim($this->input->post('segment') ?? '');
            $data['segmentCode'] = trim($segment['segmentCode'] ?? '');

            $data['DOType'] = trim($this->input->post('dotype') ?? '');
            $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
            $data['salesPersonID'] = $this->input->post('salesperson');
            $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
            $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');

            $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
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
            $data['customerCurrency'] = $customer_arr['customerCurrency'];
            $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];

            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['transactionCurrency'] = trim($currency_code);
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

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['DOcode'] = 0;

            $this->db->trans_start();
            $result=$this->db->insert('srp_erp_deliveryorder', $data);
            $last_id = $this->db->insert_id();
            if($last_id) {
                foreach ($details as $det) {
                    $itemmaster = $this->db->query("SELECT
	                                                    costGLAutoID AS expenseGLAutoID,
	                                                    costSystemGLCode AS expenseSystemGLCode,
	                                                    costGLCode AS expenseGLCode,
	                                                    costDescription AS expenseGLDescription,
	                                                    costType AS expenseGLType,
	                                                    revanueGLAutoID AS revenueGLAutoID,
	                                                    revanueGLCode AS revenueGLCode,
	                                                    revanueSystemGLCode AS revenueSystemGLCode,
	                                                    revanueDescription AS revenueGLDescription,
	                                                    revanueType AS revenueGLType,
	                                                    assteGLAutoID,
	                                                    assteGLCode,
	                                                    assteSystemGLCode,
	                                                    assteDescription,
	                                                    companyLocalWacAmount
                                                        FROM
	                                                    `srp_erp_itemmaster` 
                                                        WHERE
                                                        companyID = $companyID
                                                        AND	itemAutoID = '{$det['itemAutoID']}'")->row_array();


                    $itemAutoID = $det['itemAutoID'];
                    $detail['DOAutoID'] = $last_id;
                    $detail['type'] = 'Item';
                    $detail['contractcode'] = $delOrderdate['contractCode'];
                    $detail['projectExchangeRate'] = '1';
                    $detail['contractDetailsAutoID'] = $det['contractDetailsAutoID'];
                    $detail['contractAutoID'] = $det['contractAutoID'];
                    $detail['itemAutoID'] = $det['itemAutoID'];
                    $detail['itemSystemCode'] = $det['itemSystemCode'];
                    $detail['itemDescription'] = $det['itemDescription'];
                    $detail['itemCategory'] = $det['itemCategory'];
                    $detail['defaultUOMID'] = $det['defaultUOMID'];
                    $detail['projectID'] = $det['projectID'];
                    $detail['projectExchangeRate'] = $det['projectExchangeRate'];
                    $detail['defaultUOM'] = $det['defaultUOM'];
                    $detail['unitOfMeasureID'] = $det['unitOfMeasureID'];
                    $detail['unitOfMeasure'] = $det['unitOfMeasure'];
                    $detail['conversionRateUOM'] = 1;
                    $detail['wareHouseAutoID'] = $warehouse['wareHouseAutoID'];
                    $detail['wareHouseCode'] =  $warehouse['wareHouseCode'];
                    $detail['wareHouseLocation'] = $warehouse['wareHouseLocation'];
                    $detail['wareHouseDescription'] = $warehouse['wareHouseDescription'];
                    $detail['expenseGLAutoID'] = $itemmaster['expenseGLAutoID'];
                    $detail['expenseSystemGLCode'] = $itemmaster['expenseSystemGLCode'];
                    $detail['expenseGLCode'] = $itemmaster['expenseGLCode'];
                    $detail['expenseGLDescription'] = $itemmaster['expenseGLDescription'];
                    $detail['expenseGLType'] = $itemmaster['expenseGLType'];
                    $detail['revenueGLAutoID'] = $itemmaster['revenueGLAutoID'];
                    $detail['revenueGLCode'] = $itemmaster['revenueGLCode'];
                    $detail['revenueSystemGLCode'] = $itemmaster['revenueSystemGLCode'];
                    $detail['revenueGLDescription'] = $itemmaster['revenueGLDescription'];
                    $detail['revenueGLType'] = $itemmaster['revenueGLType'];
                    $detail['assetGLAutoID'] = $itemmaster['assteGLAutoID'];
                    $detail['assetGLCode'] = $itemmaster['assteGLCode'];
                    $detail['assetSystemGLCode'] = $itemmaster['assteSystemGLCode'];
                    $detail['assetGLDescription'] = $itemmaster['assteDescription'];
                    $detail['contractQty'] = $det['requestedQty'];
                    $detail['contractAmount'] = $det['transactionAmount'];
                    $detail['companyLocalWacAmount'] = $itemmaster['companyLocalWacAmount'];

                    $balance = $this->db->query("SELECT
                                                        srp_erp_contractdetails.contractAutoID,
                                                        srp_erp_contractdetails.contractDetailsAutoID,
                                                        srp_erp_contractdetails.itemAutoID,
                                                        srp_erp_contractdetails.requestedQty AS requestedQtyTot,
                                                        cinv.requestedQtyINV,
                                                        ifnull( deliveryorder.requestedQtyDO, 0 ) AS requestedQtyDO,
                                                        TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM (ROUND(
                                                            ifnull( srp_erp_contractdetails.requestedQty, 0 )
                                                            , 2 )))
                                                             - 
                                                            TRIM(TRAILING 0 FROM (ROUND(
                                                              ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ) 
                                                            , 2 )))) AS balance 
                                                    FROM
                                                        srp_erp_contractdetails
                                                        LEFT JOIN (
                                                        SELECT
                                                            contractAutoID,
                                                            contractDetailsAutoID,
                                                            itemAutoID,
                                                            IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV 
                                                        FROM
                                                            srp_erp_customerinvoicedetails 
                                                        WHERE
                                                            contractAutoID IS NOT NULL 
                                                            AND contractAutoID = $contractAutoID 
                                                        GROUP BY
                                                            contractDetailsAutoID 
                                                        ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                                        LEFT JOIN (
                                                        SELECT
                                                            contractAutoID,
                                                            contractDetailsAutoID,
                                                            itemAutoID,
                                                            IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO 
                                                        FROM
                                                            srp_erp_deliveryorderdetails 
                                                        WHERE
                                                            contractAutoID IS NOT NULL 
                                                            AND contractAutoID = $contractAutoID 
                                                        GROUP BY
                                                            contractDetailsAutoID 
                                                        ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                                    WHERE
                                                        srp_erp_contractdetails.contractAutoID IS NOT NULL 
                                                        AND srp_erp_contractdetails.contractAutoID = $contractAutoID 
                                                        AND srp_erp_contractdetails.itemAutoID = {$det['itemAutoID']}
                                                    GROUP BY
                                                        srp_erp_contractdetails.contractDetailsAutoID 
                                                    HAVING
                                                        balance >0 ")->row_array();

                    $detail['requestedQty'] = $balance['balance'];
                    $detail['deliveredQty'] = $balance['balance'];
                    $detail['isDeliveredQtyUpdated'] = 0;
                    $detail['deliveredTransactionAmount'] =  ($det['unittransactionAmount']*$balance['balance']);
                    $detail['noOfItems'] = $det['noOfItems'];
                    $detail['invoicedYN'] = $det['invoicedYN'];
                    $detail['taxCalculationformulaID'] = $det['taxCalculationformulaID'];
                    $detail['comment'] = $det['comment'];
                    $detail['remarks'] = $det['remarks'];
                    $detail['unittransactionAmount'] = ($det['unittransactionAmount']+$det['discountAmount']);
                    $detail['discountPercentage'] = $det['discountPercentage'];
                    $detail['discountAmount'] = $det['discountAmount'];
                    $detail['transactionAmount'] = ($det['unittransactionAmount']*$balance['balance']);
                    $detail['companyLocalAmount'] = $det['companyLocalAmount'];
                    $detail['companyReportingAmount'] = $det['companyReportingAmount'];
                    $detail['customerAmount'] = $det['customerAmount'];
                    $detail['companyID'] = $det['companyID'];
                    $detail['companyCode'] = $det['companyCode'];
                    $detail['createdUserGroup'] = $det['createdUserGroup'];
                    $detail['createdPCID'] = $det['createdPCID'];
                    $detail['createdUserID'] = $det['createdUserID'];
                    $detail['createdDateTime'] = $det['createdDateTime'];
                    $detail['createdUserName'] = $det['createdUserName'];
                    $detail['modifiedPCID'] = $det['modifiedPCID'];
                    $detail['modifiedUserID'] = $det['modifiedUserID'];
                    $detail['modifiedDateTime'] = $det['modifiedDateTime'];
                    $detail['modifiedUserName'] = $det['modifiedUserName'];
                    $detail['timestamp'] = $det['timestamp'];
                    $detail['segmentID'] = $delOrderdate['segmentID'];
                    $detail['segmentCode'] = $delOrderdate['segmentCode'];
                    $detail['taxamount'] = 0;

                    $vat_calculate_amount = $detail['unittransactionAmount'] * $balance['balance'];
                    $vat_calculate_discount = $detail['discountAmount'] * $balance['balance'];
                    //$this->load->model('Receipt_voucher_model');
                    $Unapproved_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($detail['itemAutoID'], $detail['wareHouseAutoID'],'DO',$detail['DOAutoID']);
                    $detail['parkQty'] = $Unapproved_stock['Unapproved_stock'];

                    if($balance) {
                        $this->db->insert('srp_erp_deliveryorderdetails', $detail);
                        $last_de_detailID = $this->db->insert_id();

                        if($delOrderdate['isGroupBasedTax'] == 1 && !empty($det['taxCalculationformulaID'])) {
                            tax_calculation_vat(null,null,$det['taxCalculationformulaID'],'DOAutoID',trim($last_id), $vat_calculate_amount,'DO', $last_de_detailID, $vat_calculate_discount,1);
                        }
                    }
                }

                $invoicedYN['invoicedYN'] = 1;
                $this->db->where('contractAutoID', $contractAutoID);
                $this->db->update('srp_erp_contractdetails', $invoicedYN);
            }
            if($delOrderdate['warehouseAutoID'] == '' || $delOrderdate['warehouseAutoID']==0)
            {
                $data_mastre['warehouseAutoID'] = $warehouse['wareHouseAutoID'];
                $this->db->where('contractAutoID', $contractAutoID);
                $this->db->update('srp_erp_contractmaster', $data_mastre);
            }

            if($this->input->post('confirm') == 1) {

                $this->load->library('approvals');

                $this->db->select('documentID,DOCode,DATE_FORMAT(DODate, "%Y") as invYear,DATE_FORMAT(DODate, "%m") as invMonth,companyFinanceYearID, DODate ');
                $this->db->where('DOAutoID', $last_id);
                $this->db->from('srp_erp_deliveryorder');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');

                $order_code = $master_dt['DOCode'];

                $this->db->trans_start();

                if(strlen($order_code) == 1){ /*Document code generation*/
                    $location_wise_code_generation = getPolicyValues('LDG', 'All');

                    if($location_wise_code_generation == 1) {
                        $location_emp = trim($this->common_data['emplanglocationid']);
                        if($location_emp != '') {
                            $order_code = $this->sequence->sequence_generator_location('DO', $master_dt['companyFinanceYearID'], $location_emp, $master_dt['invYear'], $master_dt['invMonth']);
                        }
                        else {
                            die( json_encode(['w', 'Location is not assigned for current employee']));
                        }
                    }
                    else {
                        $order_code = $this->sequence->sequence_generator_fin('DO', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $this->db->where('DOAutoID', $last_id)->update('srp_erp_deliveryorder', ['DOCode' => $order_code]);
                }

                $this->load->model('Delivery_order_model');
                $validation = $this->Delivery_order_model->on_delivery_order_confirmation($last_id);
                if($validation[0] == 'e'){
                    die( json_encode($validation));
                }

                $autoApproval = get_document_auto_approval('DO');
                $order_date = $master_dt['DODate'];

                $approvals_status = null;
                if($autoApproval == 0){
                    $auto_approve_status = $this->approvals->auto_approve($last_id, 'srp_erp_deliveryorder','DOAutoID', 'DO', $order_code, $order_date);
                    /*if ($auto_approve_status == 1) {*/
                        /*If delivery order auto approval successfully approved*/
                     /*   return $this->Delivery_order_model->on_delivery_order_approval($last_id);
                    }
                    else{
                        die( json_encode(['e', 'Error in auto approval process.']));
                    }*/
                }elseif($autoApproval == 1){
                    $approvals_status = $this->approvals->CreateApproval('DO', $last_id, $order_code, 'Invoice', 'srp_erp_deliveryorder', 'DOAutoID',0, $order_date);
                }else{
                    die( json_encode(['e', 'Approval levels are not set for this document']));
                }

                if($approvals_status == 3){
                    die( json_encode(['w', 'There are no users exist to perform approval for this document.']) );
                }

            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice generation failed ' . $this->db->_error_message());
                update_warehouse_items();
                update_item_master();
            } else {
                $this->db->trans_commit();
                return array('s', 'Delivery Order saved successfully');
                $this->load->model('_model');
                $confirmed = $this->Invoice_model->invoice_confirmation($last_id);
                if ($confirmed[0] == 's') {
                    return array('s', 'Invoice Generated & Confirmed Successfully!');
                } else {
                    return array('s', 'Invoice Generated Successfully!');
                }
            }
        }else{
            return array('e', 'Delivery Order date should be greater than or equal to delivery order date');
        }
    }

    function check_item_balance_from_quotation_contract(){
        $contractAutoID = trim($this->input->post('contractAutoId') ?? '');
        $balanceYN= $this->db->query("SELECT
    srp_erp_contractdetails.contractAutoID,
srp_erp_contractdetails.contractDetailsAutoID,
    srp_erp_contractdetails.itemAutoID,
    srp_erp_contractdetails.requestedQty AS requestedQtyTot ,
        cinv.requestedQtyINV,
        ifnull(deliveryorder.requestedQtyDO,0) as requestedQtyDO ,
  TRIM(
		TRAILING '.' 
	FROM
		TRIM(
			TRAILING 0 
		FROM
			(
			ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(
			TRAILING 0 
		FROM
			(
			ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
FROM
    srp_erp_contractdetails
LEFT JOIN(  
    SELECT
    contractAutoID,
  contractDetailsAutoID,
    itemAutoID,
    IFNULL(SUM(requestedQty),0) AS requestedQtyINV
FROM
    srp_erp_customerinvoicedetails 
WHERE
    contractAutoID IS NOT NULL 
    AND contractAutoID = {$contractAutoID}
    GROUP BY contractDetailsAutoID
    ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID` 
    LEFT JOIN(
    SELECT
    contractAutoID,
contractDetailsAutoID,
    itemAutoID,
    IFNULL(SUM(deliveredQty),0) AS requestedQtyDO
FROM
    srp_erp_deliveryorderdetails 
WHERE
    contractAutoID IS NOT NULL 
    AND contractAutoID = {$contractAutoID}
    GROUP BY contractDetailsAutoID
        ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
    WHERE
    srp_erp_contractdetails.contractAutoID IS NOT NULL
    AND srp_erp_contractdetails.contractAutoID = {$contractAutoID}
    GROUP BY srp_erp_contractdetails.contractDetailsAutoID
having balance>0")->row_array();
        if ($balanceYN) {
            return array('error' => 1, 'message' => 'Create delivery order', 'contractAutoID' => $contractAutoID);
        }else{
            return array('error' => 0, 'message' => 'All the items in this document has been pulled for invoice / delivery order already!', 'contractAutoID' => $contractAutoID);
        }
    }
    function save_quotation_contract_header_nh()
    {
        $date_format_policy = date_format_policy();
        $cntrctDate = $this->input->post('contractDate');
        $contractDate = input_format_date($cntrctDate, $date_format_policy);
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $cntrctEpDate = $this->input->post('contractExpDate');
        $contractExpDate = input_format_date($cntrctEpDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $this->db->trans_start();
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $warehouseAutoID = $this->input->post('location');

        $data['contractType'] = trim($this->input->post('contractType') ?? '');
        $d_code = 'CNT';
        if ($data['contractType'] == 'Quotation') {
            $d_code = 'QUT';
        } elseif ($data['contractType'] == 'Sales Order') {
            $d_code = 'SO';
        }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $data['documentID'] = $d_code;
        $data['warehouseAutoID'] = $warehouseAutoID;
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['contractDate'] = trim($contractDate);
        $data['contractExpDate'] = trim($contractExpDate);
        $contractNarration = ($this->input->post('contractNarration'));
        $data['contractNarration'] = str_replace('<br />', PHP_EOL, $contractNarration);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
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
        $isGroupBasedTax = getPolicyValues('GBT', 'All');
        if($isGroupBasedTax && $isGroupBasedTax == 1) {
            $data['isGroupBasedTax'] = 1;
        }

        $crTypes = explode('<table', $this->input->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['Note'] = trim($notes);
        // $data['Note'] = trim($this->input->post('Note') ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
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
        $data['salesPersonID'] = $this->input->post('salesperson');
        $data['showImageYN'] = $this->input->post('showImageYN');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('contractAutoID') ?? '')) {
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->update('srp_erp_contractmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('contractAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $company_id = current_companyID();
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $financeYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} 
                                                   AND '{$contractDate}' BETWEEN dateFrom AND dateTo")->row('companyFinanceYearID');

            $contr_year = date('Y', strtotime($contractDate));
            $contr_month = date('m', strtotime($contractDate));
            if($locationwisecodegenerate == 1){
                $contract_code = $this->sequence->sequence_generator_location($data['documentID'],$financeYearID,$this->common_data['emplanglocationid'],$contr_year,$contr_month);
            }else{
                $contract_code = $this->sequence->sequence_generator_fin($data['documentID'],$financeYearID,$contr_year,$contr_month);
            }

            $data['contractCode'] = $contract_code;

            $this->db->insert('srp_erp_contractmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }
    function contract_confirmation_nh()
    {
        $this->db->select('contractDetailsAutoID');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $this->db->from('srp_erp_contractdetails');
        $companyid = current_companyID();
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('w', 'There are no records to confirm this document!');
        } else {


            $this->db->select('contractAutoID,warehouseAutoID');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_contractmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $contractAutoID = trim($this->input->post('contractAutoID') ?? '');

                $this->load->library('Approvals');
                $this->db->select('documentID,contractType,contractCode,customerCurrencyExchangeRate,companyReportingExchangeRate, companyLocalExchangeRate ,contractAutoID,transactionCurrencyDecimalPlaces,contractDate');
                $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                $this->db->from('srp_erp_contractmaster');
                $c_data = $this->db->get()->row_array();

                $item_low_qty = $this->db->query("SELECT
	ware_house.itemAutoID,
	ware_house.currentStock,
	( detTB.requestedQty / detTB.conversionRateUOM ) AS qty,
	round(( ware_house.currentStock - ( detTB.requestedQty / detTB.conversionRateUOM ) ), 4 ) AS stock,
	detmaster.wareHouseAutoID,
	itm_mas.itemSystemCode,
	itm_mas.itemDescription,
	ware_house.currentStock AS availableStock 
FROM
	srp_erp_contractdetails AS detTB
	LEFT JOIN srp_erp_contractmaster detmaster on detmaster.contractAutoID = detTB.contractAutoID
	JOIN (
	SELECT
		SUM( transactionQTY / convertionRate ) AS currentStock,
		wareHouseAutoID,
		itemAutoID 
	FROM
		srp_erp_itemledger 
	WHERE
		companyID = $companyid
	GROUP BY
		wareHouseAutoID,
		itemAutoID 
	) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID
	JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID 
	AND detmaster.wareHouseAutoID = ware_house.wareHouseAutoID 
WHERE
	detTB.contractAutoID =  $contractAutoID
	AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' ) 
HAVING
	stock < 0")->result_array();



                if (!empty($item_low_qty)) {
                    die(json_encode(['e', 'Some Item quantities are not sufficient to confirm this transaction.', 'in-suf-items' => $item_low_qty, 'in-suf-qty' => 'Y']));
                }

                $autoApproval= get_document_auto_approval($c_data['documentID']);



                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($c_data['contractAutoID'], 'srp_erp_contractmaster','contractAutoID', $c_data['documentID'],$c_data['contractCode'], $c_data['contractDate']);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['contractAutoID'], $c_data['contractCode'], $c_data['contractType'], 'srp_erp_contractmaster', 'contractAutoID', 1, $c_data['contractDate']);
                }else{
                    return array('e', 'Approval levels are not set for this document');
                    exit;
                }


                if ($approvals_status) {
                    $this->db->select_sum('transactionAmount');
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                    $total = $this->db->get('srp_erp_contractdetails')->row('transactionAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($total, $c_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($total / $c_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($total / $c_data['companyReportingExchangeRate']),
                        'customerCurrencyAmount' => ($total / $c_data['customerCurrencyExchangeRate']),
                    );
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
                    $this->db->update('srp_erp_contractmaster', $data);
                    /*$this->session->set_flashdata('s', 'Create Approval : ' . $c_data['contractCode'] . ' Approvals Created Successfully ');
                    return true;*/
                    if($autoApproval==0) {
                        $result = $this->save_quotation_contract_approval(0, $c_data['contractAutoID'], 1, 'Auto Approved');
                        if($result){
                            return array('s', 'Approvals Created Successfully.');
                        }
                    }else{
                        return array('s', 'Approvals Created Successfully.');
                    }
                } else {
                    /*return false;*/
                    return array('e', 'oops, something went wrong!.');
                }

            }


        }
    }
    function fetch_quotation_contract_detail()
    {
        $contractAutoID = $this->input->post('contractAutoID');
        $warehouseid = $this->input->post('warehouseAutoid');
        $companyid = current_companyID();
        $warehousefilter = '';
        if($warehouseid)
        {
            $warehousefilter = ' AND wareHouseAutoID = '.$warehouseid.'';
        }
        $query = $this->db->query("SELECT `srp_erp_contractdetails`.*, `srp_erp_itemmaster`.`currentStock`,`srp_erp_itemmaster`.`mainCategory`,	(discountAmount + unittransactionAmount) as unit,discountPercentage as discountPercentagecnt,unittransactionAmount as unicnt,
                                        discountAmount as discountAmountcnt,srp_erp_contractdetails.transactionAmount as transactionAmountcnt,	itemstock.currentStock as currentStockitemledge,companyLocalWacAmount as companyLocalWacAmountcnt,
                                        (companyLocalWacAmount / (1/srp_erp_contractmaster.companyLocalExchangeRate)) as comany_localwac,contractDetailsAutoID as contractDetailsAutoIDcnt
                                        
                                        FROM `srp_erp_contractdetails` LEFT JOIN `srp_erp_contractmaster` ON `srp_erp_contractdetails`.`contractAutoID` = `srp_erp_contractmaster`.`contractAutoID`
	                                    LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_contractdetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID` 
	                                    LEFT JOIN (SELECT SUM( transactionQTY / convertionRate ) AS currentStock,wareHouseAutoID,itemAutoID FROM
		                                srp_erp_itemledger WHERE companyID = $companyid $warehousefilter GROUP BY
		                                itemAutoID ) itemstock on itemstock.ItemAutoID = srp_erp_contractdetails.itemAutoID
                                        WHERE `srp_erp_contractdetails`.`contractAutoID` = '{$contractAutoID}' ")->result_array();
        return $query;
    }
    function update_qut_items()
    {
        $projectExist = project_is_exist();
        $itemAutoIDs = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');
        $noOfItems = $this->input->post('noOfItems');
        $contractDetailsAutoID = $this->input->post('contractDetailsAutoID');
        $itemAutoIDJoin = join(',', $itemAutoIDs);

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data(trim($itemAutoID));
            $projectID = $this->input->post('projectID');
            $uom = explode('|', $uoms[$key]);
            $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }

            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['itemCategory'] = $item_arr['mainCategory'];
            $data['unitOfMeasure'] = trim($uom[0] ?? '');
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['itemReferenceNo'] = trim($itemReferenceNo[$key]);
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $data['noOfItems'] = trim($noOfItems[$key]);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = (trim($estimatedAmount[$key]) - $data['discountAmount']);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
            $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->where('contractDetailsAutoID', ($contractDetailsAutoID[$key]));
            $this->db->update('srp_erp_contractdetails', $data);
        }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail :  Updated Successfully.');
            }

    }

    function save_quotation_contract_approval_nh($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $companyid = current_companyID();
        $system_code = trim($this->input->post('contractAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $code = trim($this->input->post('code') ?? '');
        if($autoappLevel==1) {
            $item_low_qty = $this->db->query("SELECT
	ware_house.itemAutoID,
	ware_house.currentStock,
	( detTB.requestedQty / detTB.conversionRateUOM ) AS qty,
	round(( ware_house.currentStock - ( detTB.requestedQty / detTB.conversionRateUOM ) ), 4 ) AS stock,
	detmaster.wareHouseAutoID,
	itm_mas.itemSystemCode,
	itm_mas.itemDescription,
	ware_house.currentStock AS availableStock 
FROM
	srp_erp_contractdetails AS detTB
	LEFT JOIN srp_erp_contractmaster detmaster on detmaster.contractAutoID = detTB.contractAutoID
	JOIN (
	SELECT
		SUM( transactionQTY / convertionRate ) AS currentStock,
		wareHouseAutoID,
		itemAutoID 
	FROM
		srp_erp_itemledger 
	WHERE
		companyID = $companyid
	GROUP BY
		wareHouseAutoID,
		itemAutoID 
	) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID
	JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID 
	AND detmaster.wareHouseAutoID = ware_house.wareHouseAutoID 
WHERE
	detTB.contractAutoID =  $system_code
	AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' ) 
HAVING
	stock < 0")->result_array();
            if (!empty($item_low_qty)) {
                die(json_encode(['e', 'Some Item quantities are not sufficient to confirm this transaction.', 'in-suf-items' => $item_low_qty, 'in-suf-qty' => 'Y']));
            }

        }else{



            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        $this->db->select('documentID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $system_code);
        $code = $this->db->get()->row('documentID');

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $code);
        }

        if ($approvals_status == 1) {
            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            // $this->db->update('srp_erp_creditnotemaster', $data);
            $this->session->set_flashdata('s', 'Approval Successfully.');
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

    function close_contract()
    {
        $this->db->trans_start();
        $system_code = trim($this->input->post('contractAutoID') ?? '');
        $date_format_policy = date_format_policy();
        $docdate = $this->input->post('closedDate');
        $closeddate = input_format_date($docdate, $date_format_policy);

        $data['closedYN'] = 1;
        $data['closedDate'] = $closeddate;
        $data['closedBy'] = $this->common_data['current_user'];
        $data['closedReason'] = trim($this->input->post('comments') ?? '');
        $data['approvedYN'] = 5;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('contractAutoID', trim($system_code));
        $this->db->update('srp_erp_contractmaster', $data);
        $this->session->set_flashdata('s', 'Document Closed Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /* Function added */
    function  fetch_referenceNo(){

        $companyid = $this->common_data['company_data']['company_id'];
        $referenceNo = $this->input->post('referenceNo');
        $contractAutoID = $this->input->post('contractAutoID');
        
        if(!empty($referenceNo) ){

           // if (trim($this->input->post('contractAutoID') ?? '')) {    
            if (isset($contractAutoID) && !empty($contractAutoID)){
               // var_dump($contractAutoID);
                $this->db->select('count(referenceNo) AS referenceNo ');
                $this->db->where('companyID', $companyid);
                $this->db->where('referenceNo', $referenceNo);
                $this->db->where('contractAutoID !=', $contractAutoID);
                $result = $this->db->get('srp_erp_contractmaster')->row_array();
            }else{
                $this->db->select('count(referenceNo) AS referenceNo ');
                $this->db->where('companyID', $companyid);
                $this->db->where('referenceNo', $referenceNo);
                $result = $this->db->get('srp_erp_contractmaster')->row_array();
            }
            
            if( $result['referenceNo'] > 0 ){
                return $result;
            }else{
                return false;
            }

        }else{
             return false;
        }
        
    }

    function save_invoice_from_quotation_contract_header(){
         
        $this->load->model('Invoice_model');
        $company_id = current_companyID();
        $contractAutoID=$this->input->post('contractAutoID');
        $warehouseAutoIDtemp = $this->input->post('warehouseAutoIDtemp');

        $this->db->select('COUNT(receiptVoucherDetailAutoID) as advanceCount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $this->db->where('type', 'Advance');
        $this->db->where('companyID', $company_id);
        $advance_pulled = $this->db->get()->row('advanceCount');

        if($advance_pulled > 0) {
            return array('e', 'Advance already generated for this Document!');
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
            $details = $this->db->get()->result_array();
            $ids = array();
            foreach ($details as $key2=>$det) {
                $balance = $this->db->query("SELECT
                        srp_erp_contractdetails.contractAutoID,
                        srp_erp_contractdetails.contractDetailsAutoID,
                        srp_erp_contractdetails.itemAutoID,
                        srp_erp_contractdetails.requestedQty AS requestedQtyTot,
                        cinv.requestedQtyINV,
                        ifnull( deliveryorder.requestedQtyDO, 0 ) AS requestedQtyDO,
                        TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM (ROUND(
                            ifnull( srp_erp_contractdetails.requestedQty, 0 )
                            , 2 )))
                             - 
                            TRIM(TRAILING 0 FROM (ROUND(
                              ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ) 
                            , 2 )))) AS balance 
                    FROM
                        srp_erp_contractdetails
                        LEFT JOIN (
                        SELECT
                            contractAutoID,
                            contractDetailsAutoID,
                            itemAutoID,
                            IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV 
                        FROM
                            srp_erp_customerinvoicedetails 
                        WHERE
                            contractAutoID IS NOT NULL 
                            AND contractAutoID = $contractAutoID 
                        GROUP BY
                            contractDetailsAutoID 
                        ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                        LEFT JOIN (
                        SELECT
                            contractAutoID,
                            contractDetailsAutoID,
                            itemAutoID,
                            IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO 
                        FROM
                            srp_erp_deliveryorderdetails 
                        WHERE
                            contractAutoID IS NOT NULL 
                            AND contractAutoID = $contractAutoID 
                        GROUP BY
                            contractDetailsAutoID 
                        ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                    WHERE
                        srp_erp_contractdetails.contractAutoID IS NOT NULL 
                        AND srp_erp_contractdetails.contractAutoID = $contractAutoID 
                        AND srp_erp_contractdetails.itemAutoID = {$det['itemAutoID']}
                    GROUP BY
                        srp_erp_contractdetails.contractDetailsAutoID 
                    HAVING
                        balance >0 ")->row_array();
                $details[$key2]['balanceQtyVal'] = $balance['balance'];
                $warehouseQty = $this->db->query("SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID 
                                     FROM srp_erp_itemledger 
                                     WHERE companyID = {$company_id} AND wareHouseAutoID = {$warehouseAutoIDtemp} AND itemAutoID = {$det['itemAutoID']}
                                     GROUP BY wareHouseAutoID, itemAutoID ")->row('currentStock');
                //check for available stock
                $this->load->model('Receipt_voucher_model');
                $parkQty = 0;
                if($det['itemCategory'] == 'Inventory') {
                    $Unapproved_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($det['itemAutoID'], $warehouseAutoIDtemp);
                    $parkQty = $Unapproved_stock['Unapproved_stock'] ;
                }
                //if($warehouseQty < $balance['balance']) {
                if(($warehouseQty - $parkQty) < $balance['balance']) {
                    array_push($ids, $det['itemAutoID']);
                }
            }
            if($this->input->post('confirm') == 1 && (!empty($ids))) {
                $item_low_qty = $this->db->query("  SELECT itemAutoID, itemSystemCode, itemDescription, IFNULL(ware.currentStock, 0) as currentStock
                                     FROM srp_erp_itemmaster 
                                     LEFT JOIN (
                                     	SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID as itemID
                                     FROM srp_erp_itemledger 
                                     WHERE companyID = {$company_id} AND wareHouseAutoID = {$warehouseAutoIDtemp}
                                     GROUP BY wareHouseAutoID, itemAutoID
                                     )ware ON ware.itemID = srp_erp_itemmaster.itemAutoID
                                     WHERE companyID = {$company_id} 
                                     AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' ) 
                                     AND itemAutoID IN ( " . join(',', $ids) . " )
                                     ")->result_array();
                if (!empty($item_low_qty)) {
                    return array('w', 'Some Item quantities are not sufficient to confirm this transaction.', 'itemInsufficient' => $item_low_qty, 'in-suf-qty' => 'Y');
                }
            }
            $date_format_policy = date_format_policy();
            $financeyearperiodYN = getPolicyValues('FPC', 'All');
            $DOdates = $this->input->post('DOdate');
            $DOdate = input_format_date($DOdates, $date_format_policy);
            $companyID = current_companyID();
            //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
            $this->db->select('*');
            $this->db->from('srp_erp_contractmaster');
            $this->db->where('contractAutoID', $contractAutoID);
            $delOrderdate=$this->db->get()->row_array();

            $warehouse  = $this->db->query("SELECT wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID = $warehouseAutoIDtemp")->row_array();

            if($DOdate>=$delOrderdate['contractDate']){
                if ($financeyearperiodYN == 1) {
                    $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                    $FYBegin = input_format_date($financeyr[0], $date_format_policy);
                    $FYEnd = input_format_date($financeyr[1], $date_format_policy);
                } else {
                    $financeYearDetails = get_financial_year($DOdate);
                    if (empty($financeYearDetails)) {
                        return array('e', 'Finance period not found for the selected document date');
                        exit;
                    } else {
                        $FYBegin = $financeYearDetails['beginingDate'];
                        $FYEnd = $financeYearDetails['endingDate'];
                        $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                        $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                    }
                    $financePeriodDetails = get_financial_period_date_wise($DOdate);

                    if (empty($financePeriodDetails)) {
                        return array('e', 'Finance period not found for the selected document date');
                        exit;
                    } else {
                        $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                    }
                }

                $this->db->select("segmentCode");
                $this->db->from('srp_erp_segment');
                $this->db->where('segmentID', $this->input->post('segment'));
                $segment = $this->db->get()->row_array();

                $data['documentID'] = 'CINV';
                $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
                $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
                $data['FYBegin'] = trim($FYBegin);
                $data['FYEnd'] = trim($FYEnd);
                $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
                $data['invoiceDate'] = trim($DOdate);
                $data['customerInvoiceDate'] = trim($DOdate);
                $data['invoiceDueDate'] = trim($DOdate);
                //$data['invoiceNarration'] = trim_desc($INV_narration);
                $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
                $data['invoiceNote'] = '';
                $data['segmentID'] = trim($this->input->post('segment') ?? '');
                $data['segmentCode'] = trim($segment['segmentCode'] ?? '');
                $data['salesPersonID'] = $this->input->post('salesperson');
                $data['invoiceType'] = trim($this->input->post('dotype') ?? '');
                $data['isPrintDN'] = 0;
                $data['isGroupBasedTax'] = $delOrderdate['isGroupBasedTax'];
                $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
                $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
                $data['invoiceNarration'] = trim_desc($this->input->post('narration'));
                //$data['narration'] = trim_desc($this->input->post('Narration'));
                $data['customerID'] = $delOrderdate['customerID'];
                $data['customerSystemCode'] = $delOrderdate['customerSystemCode'];
                $data['customerName'] = $delOrderdate['customerName'];
                $data['customerAddress'] = $delOrderdate['customerAddress'];
                $data['customerTelephone'] = $delOrderdate['customerTelephone'];
                $data['customerFax'] = $delOrderdate['customerFax'];
                $data['customerEmail'] = $delOrderdate['customerEmail'];
                $data['customerReceivableAutoID'] = $delOrderdate['customerReceivableAutoID'];
                $data['customerReceivableSystemGLCode'] = $delOrderdate['customerReceivableSystemGLCode'];
                $data['customerReceivableGLAccount'] = $delOrderdate['customerReceivableGLAccount'];
                $data['customerReceivableDescription'] = $delOrderdate['customerReceivableDescription'];
                $data['customerReceivableType'] = $delOrderdate['customerReceivableType'];
                $data['customerCurrency'] = $delOrderdate['customerCurrency'];
                $data['customerCurrencyID'] = $delOrderdate['customerCurrencyID'];
                $data['customerCurrencyDecimalPlaces'] = $delOrderdate['customerCurrencyDecimalPlaces'];
                $data['transactionCurrencyID'] = $delOrderdate['transactionCurrencyID'];
                $data['transactionCurrency'] = $delOrderdate['transactionCurrency'];
                $data['transactionExchangeRate'] = $delOrderdate['transactionExchangeRate'];
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

                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['invoiceCode'] = 0;
//            $data['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $data['isSytemGenerated'] = 0;

                $rebate = getPolicyValues('CRP', 'All');
                if($rebate == 1) {
                    $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$data['customerID']}")->row_array();
                    if(!empty($rebate)) {
                        $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                        $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
                    }
                } else {
                    $data['rebateGLAutoID'] = null;
                    $data['rebatePercentage'] = null;
                }

                $this->db->trans_start();
                $this->db->insert('srp_erp_customerinvoicemaster', $data);
                $last_id = $this->db->insert_id();


                if($last_id) {
                    foreach ($details as $det) {
                        $itemmaster = $this->db->query("SELECT
                                                        costGLAutoID AS expenseGLAutoID,
                                                        costSystemGLCode AS expenseSystemGLCode,
                                                        costGLCode AS expenseGLCode,
                                                        costDescription AS expenseGLDescription,
                                                        costType AS expenseGLType,
                                                        revanueGLAutoID AS revenueGLAutoID,
                                                        revanueGLCode AS revenueGLCode,
                                                        revanueSystemGLCode AS revenueSystemGLCode,
                                                        revanueDescription AS revenueGLDescription,
                                                        revanueType AS revenueGLType,
                                                        assteGLAutoID,
                                                        assteGLCode,
                                                        assteSystemGLCode,
                                                        assteDescription,
                                                        companyLocalWacAmount,
                                                        assteType
                                                        FROM
                                                        `srp_erp_itemmaster` 
                                                        WHERE
                                                        companyID = $companyID
                                                        AND itemAutoID = '{$det['itemAutoID']}'")->row_array();

                        $itemAutoID = $det['itemAutoID'];
                        $detail['invoiceAutoID'] = $last_id;
                        $detail['type'] = 'Item';
                        $detail['contractcode'] = $delOrderdate['contractCode'];
                        $detail['projectExchangeRate'] = '1';
                        $detail['contractDetailsAutoID'] = $det['contractDetailsAutoID'];
                        $detail['contractAutoID'] = $det['contractAutoID'];
                        $detail['itemAutoID'] = $det['itemAutoID'];
                        $detail['itemSystemCode'] = $det['itemSystemCode'];
                        $detail['itemDescription'] = $det['itemDescription'];
                        $detail['itemCategory'] = $det['itemCategory'];
                        $detail['defaultUOMID'] = $det['defaultUOMID'];
                        $detail['projectID'] = $det['projectID'];
                        //$detail['projectExchangeRate'] = $det['projectExchangeRate'];
                        $detail['defaultUOM'] = $det['defaultUOM'];
                        $detail['unitOfMeasureID'] = $det['unitOfMeasureID'];
                        $detail['unitOfMeasure'] = $det['unitOfMeasure'];
                        $detail['conversionRateUOM'] = 1;

                        $detail['wareHouseAutoID'] = $warehouse['wareHouseAutoID'];
                        $detail['wareHouseCode'] =  $warehouse['wareHouseCode'];
                        $detail['wareHouseLocation'] = $warehouse['wareHouseLocation'];
                        $detail['wareHouseDescription'] = $warehouse['wareHouseDescription'];
                        $detail['expenseGLAutoID'] = $itemmaster['expenseGLAutoID'];
                        $detail['expenseSystemGLCode'] = $itemmaster['expenseSystemGLCode'];
                        $detail['expenseGLCode'] = $itemmaster['expenseGLCode'];
                        $detail['expenseGLDescription'] = $itemmaster['expenseGLDescription'];
                        $detail['expenseGLType'] = $itemmaster['expenseGLType'];

                        $detail['revenueGLAutoID'] = $itemmaster['revenueGLAutoID'];
                        $detail['revenueGLCode'] = $itemmaster['revenueGLCode'];
                        $detail['revenueSystemGLCode'] = $itemmaster['revenueSystemGLCode'];
                        $detail['revenueGLDescription'] = $itemmaster['revenueGLDescription'];
                        $detail['revenueGLType'] = $itemmaster['revenueGLType'];

                        $detail['assetGLAutoID'] = $itemmaster['assteGLAutoID'];
                        $detail['assetGLCode'] = $itemmaster['assteGLCode'];
                        $detail['assetSystemGLCode'] = $itemmaster['assteSystemGLCode'];
                        $detail['assetGLDescription'] = $itemmaster['assteDescription'];
                        $detail['assetGLType'] = $itemmaster['assteType'];

                        $detail['contractQty'] = $det['requestedQty'];
                        $detail['contractAmount'] = $det['transactionAmount'];
                        $detail['companyLocalWacAmount'] = $itemmaster['companyLocalWacAmount'];

                        $balance = $this->db->query("SELECT
                                                            srp_erp_contractdetails.contractAutoID,
                                                            srp_erp_contractdetails.contractDetailsAutoID,
                                                            srp_erp_contractdetails.itemAutoID,
                                                            srp_erp_contractdetails.requestedQty AS requestedQtyTot,
                                                            cinv.requestedQtyINV,
                                                            ifnull( deliveryorder.requestedQtyDO, 0 ) AS requestedQtyDO,
                                                            TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM (ROUND(
                                                                ifnull( srp_erp_contractdetails.requestedQty, 0 )
                                                                , 2 )))
                                                                 - 
                                                                TRIM(TRAILING 0 FROM (ROUND(
                                                                  ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ) 
                                                                , 2 )))) AS balance 
                                                        FROM
                                                            srp_erp_contractdetails
                                                            LEFT JOIN (
                                                            SELECT
                                                                contractAutoID,
                                                                contractDetailsAutoID,
                                                                itemAutoID,
                                                                IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV 
                                                            FROM
                                                                srp_erp_customerinvoicedetails 
                                                            WHERE
                                                                contractAutoID IS NOT NULL 
                                                                AND contractAutoID = $contractAutoID 
                                                            GROUP BY
                                                                contractDetailsAutoID 
                                                            ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                                            LEFT JOIN (
                                                            SELECT
                                                                contractAutoID,
                                                                contractDetailsAutoID,
                                                                itemAutoID,
                                                                IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO 
                                                            FROM
                                                                srp_erp_deliveryorderdetails 
                                                            WHERE
                                                                contractAutoID IS NOT NULL 
                                                                AND contractAutoID = $contractAutoID 
                                                            GROUP BY
                                                                contractDetailsAutoID 
                                                            ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                                        WHERE
                                                            srp_erp_contractdetails.contractAutoID IS NOT NULL 
                                                            AND srp_erp_contractdetails.contractAutoID = $contractAutoID 
                                                            AND srp_erp_contractdetails.itemAutoID = {$det['itemAutoID']}
                                                        GROUP BY
                                                            srp_erp_contractdetails.contractDetailsAutoID 
                                                        HAVING
                                                            balance >0 ")->row_array();
                        $detail['requestedQty'] = $balance['balance'];
                        $detail['noOfItems'] = $det['noOfItems'];
                        $detail['comment'] = $det['comment'];
                        $detail['remarks'] = $det['remarks'];
                        $detail['unittransactionAmount'] = ($det['unittransactionAmount']+$det['discountAmount']);
                        $detail['discountPercentage'] = $det['discountPercentage'];
                        $detail['discountAmount'] = $det['discountAmount'];
                        $detail['transactionAmount'] = ($det['unittransactionAmount']*$balance['balance']);
                        $detail['companyLocalAmount'] = $det['companyLocalAmount'];
                        $detail['companyReportingAmount'] = $det['companyReportingAmount'];
                        $detail['customerAmount'] = $det['customerAmount'];
                        $detail['taxCalculationformulaID'] = $det['taxCalculationformulaID'];
                        $detail['companyID'] = $det['companyID'];
                        $detail['companyCode'] = $det['companyCode'];
                        $detail['createdUserGroup'] = $det['createdUserGroup'];
                        $detail['createdPCID'] = $det['createdPCID'];
                        $detail['createdUserID'] = $det['createdUserID'];
                        $detail['createdDateTime'] = $det['createdDateTime'];
                        $detail['createdUserName'] = $det['createdUserName'];
                        $detail['modifiedPCID'] = $det['modifiedPCID'];
                        $detail['modifiedUserID'] = $det['modifiedUserID'];
                        $detail['modifiedDateTime'] = $det['modifiedDateTime'];
                        $detail['modifiedUserName'] = $det['modifiedUserName'];
                        $detail['timestamp'] = $det['timestamp'];
                        $detail['segmentID'] = $delOrderdate['segmentID'];
                        $detail['segmentCode'] = $delOrderdate['segmentCode'];
                        $detail['taxamount'] = 0;

                        $vat_calculate_amount = $detail['unittransactionAmount'] * $balance['balance'];
                        $vat_calculate_discount = $detail['discountAmount'] * $balance['balance'];
                        if($balance) {
                            $this->db->insert('srp_erp_customerinvoicedetails', $detail);
                            $last_detail_id = $this->db->insert_id();

                            if($delOrderdate['isGroupBasedTax'] == 1) {
                                if($det['taxCalculationformulaID'] != 0) {
                                    tax_calculation_vat(null,null,$det['taxCalculationformulaID'],'invoiceAutoID',trim($last_id), $vat_calculate_amount,'CINV', $last_detail_id, $vat_calculate_discount,1);
                                }
                            }
                        }
                    }
                    $invoicedYN['invoicedYN'] = 1;
                    $this->db->where('contractAutoID', $contractAutoID);
                    $this->db->update('srp_erp_contractdetails', $invoicedYN);
                    $this->db->trans_complete();
                }

                if($delOrderdate['warehouseAutoID'] == '' || $delOrderdate['warehouseAutoID']==0)
                {
                    $data_mastre['warehouseAutoID'] = $warehouse['wareHouseAutoID'];
                    $this->db->where('contractAutoID', $contractAutoID);
                    $this->db->update('srp_erp_contractmaster', $data_mastre);
                }

                 /** Added (SME-2299)*/
                $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$last_id}")->row_array();
                if(!empty($rebate)) {
                    $this->Invoice_model->calculate_rebate_amount($last_id);
                }
                /** End (SME-2299)*/
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array(['e', 'Invoice generation failed ' . $this->db->_error_message()]);
                    update_warehouse_items();
                    update_item_master();
                } else {
                    $this->db->trans_commit();
                    if($this->input->post('confirm') == 1) {

                        $confirmed = $this->Invoice_model->invoice_confirmation($last_id);
                        if ($confirmed[0] == 's') {
                            return array('s', 'Invoice Generated & Confirmed Successfully!');
                        }else{
                            die( json_encode($confirmed));
                        }
                    }else {
                        return array('s', 'Invoice Generated Successfully!',$last_id );
                        //return array('s', $last_id);

                    }
                }
            }else{
                return array('e', 'Invoice date should be greater than or equal to Contract date');
            }
        }
    }
    /* End  Function */

    function fetch_line_tax_and_vat()
    {
        $data = array();
        $itemAutoID = $this->input->post('itemAutoID');

        $data['isGroupByTax'] =  existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');
        if($data['isGroupByTax'] == 1){
            $data['dropdown'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 1);
            $selected_itemTax =   array_column($data['dropdown'], 'assignedItemTaxFormula');
            $data['selected_itemTax'] =   $selected_itemTax[0];
        }
        return $data;
    }

    function load_line_tax_amount()
    {
        $amnt=0;
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($this->input->post('contractAutoID') ?? ''),'CNT', 'contractAutoID');
        if($isGroupByTax == 1){
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'CNT', trim($this->input->post('contractAutoID') ?? ''));
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        return $amnt;
    }

    function save_contract_job_crew(){

        $employee = $this->input->post('employee');
        $crew_designation = $this->input->post('crew_designation');
        $crew_note = $this->input->post('crew_note');
        $contractAutoID = $this->input->post('contractAutoID');
        $is_primary = $this->input->post('is_primary');
        $groupToCrew = $this->input->post('groupToCrew');

        $data_arr = array();

        try {
            //code...
            foreach($employee as $key => $emp_val){
                $empID = $emp_val;
                $is_primary_selected = $is_primary[$key];
    
                if($empID){
                    $employee_details = get_contract_employee_detail($empID);
                    $ex_employee = get_contract_crew_detial($empID,$contractAutoID);

                    if($is_primary_selected == 1){
                        $ex_is_primary = get_contract_crew_detial($empID,null,1);

                        if($ex_is_primary){
                            $this->session->set_flashdata('e', $employee_details['ECode'].' Already marked differnt project as Primary.');
                            continue;
                        }

                    }
                   

                    if($ex_employee){
                        $this->session->set_flashdata('e', $employee_details['ECode'].' Already added to this contract.');
                    }else{
                        $data_arr['contractAutoID'] = $contractAutoID;
                        $data_arr['empID'] = $emp_val;
                        $data_arr['isPrimary'] = $is_primary_selected;
                        $data_arr['empDesignation'] = isset($crew_designation[$key]) ? $crew_designation[$key] : '';
                        $data_arr['comment'] =  isset($crew_note[$key]) ? $crew_note[$key] : '';
                        $data_arr['empName'] =  isset($employee_details['Ename1']) ? $employee_details['Ename1'] : '';
                        $data_arr['empCode'] =  isset($employee_details['ECode']) ? $employee_details['ECode'] : '';
                        $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
                        $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
        
                        $data_arr['date_added'] =  $this->common_data['current_date'];
                        $data_arr['added_by'] =  $this->common_data['current_user'];
                        $data_arr['groupToID'] = isset($groupToCrew[$key]) ? $groupToCrew[$key] : '';

                        if(count($groupToCrew)>0){
                            $this->db->select('*');
                            $this->db->from('srp_erp_op_module_group_to');
                            $this->db->where('groupAutoID', $groupToCrew[$key]);
                            $group = $this->db->get()->row_array();
                            $data_arr['groupToName'] = $group['groupName'];
                        }
        
                        // Save the data
                        $this->db->insert('srp_erp_contractcrew', $data_arr);
                        $last_detail_id = $this->db->insert_id();

                        $this->session->set_flashdata('s',  $employee_details['ECode'].' Successfully added the records');
                       
                    }
    
                   
    
                }
               
            }

            return true;
          

        } catch (\Exception $e) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            return false;
        }
        


    }

    function get_emp_details(){
       $empID = $this->input->post('empID');
       return get_contract_employee_detail($empID,'DesDescription');
    }

    function edit_contract_job_crew(){

        $employee = $this->input->post('employee');
        $crew_designation = $this->input->post('crew_designation');
        $crew_note = $this->input->post('crew_note');
        $contractAutoID = $this->input->post('contractAutoID');
        $crew_id = $this->input->post('crew_id');
        $is_priamry_edit = $this->input->post('is_priamry_edit');
        $groupToCrew = $this->input->post('groupToCrew');
        $data_arr = array();

        if($employee){

            $employee_details = get_contract_employee_detail($employee);
            $ex_employee = get_contract_crew_detial($employee,$contractAutoID);
            $is_primary_selected = $is_priamry_edit;

            if($is_primary_selected == 1){
                $ex_is_primary = get_contract_crew_detial($employee,null,1);

                if($ex_is_primary && $ex_is_primary['contractAutoID'] != $contractAutoID){
                    $this->session->set_flashdata('e', $employee_details['ECode'].' Already marked differnt project as Primary.');
                    return false;
                }

            }

            if($ex_employee && ($ex_employee['id'] != $crew_id)){
                $this->session->set_flashdata('e', $employee_details['ECode'].' Already added to this contract.');
            }else{
                $data_arr['empID'] = $employee;
                $data_arr['isPrimary'] = $is_primary_selected;
                $data_arr['empDesignation'] = $crew_designation;
                $data_arr['comment'] =  $crew_note;
                $data_arr['empName'] =  isset($employee_details['Ename1']) ? $employee_details['Ename1'] : '';
                $data_arr['empCode'] =  isset($employee_details['ECode']) ? $employee_details['ECode'] : '';
                $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];

                $data_arr['date_added'] =  $this->common_data['current_date'];
                $data_arr['added_by'] =  $this->common_data['current_user'];

                $data_arr['groupToID'] = isset($groupToCrew) ? $groupToCrew : '';

                if($groupToCrew){
                    $this->db->select('*');
                    $this->db->from('srp_erp_op_module_group_to');
                    $this->db->where('groupAutoID', $groupToCrew);
                    $group = $this->db->get()->row_array();
                    $data_arr['groupToName'] = $group['groupName'];
                }

                // Save the data
                $this->db->where('id',$crew_id);
                $this->db->update('srp_erp_contractcrew', $data_arr);
                $last_detail_id = $this->db->insert_id();

                $this->session->set_flashdata('s',  $employee_details['ECode'].' Successfully updated the records');
               
            }

            return true;
        }
    }

    function delete_crew_detail(){

        $crew_id = $this->input->post('crew_id');
        $contractAutoID = $this->input->post('contractAutoID');

        try {
            $this->db->where('id',$crew_id)->where('contractAutoID',$contractAutoID)->delete('srp_erp_contractcrew');
            $this->session->set_flashdata('s',  'Successfully deleted the records');
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e',  'Something went wrong');
            return false;
        }
    }

    function delete_checklist_detail(){

        $id = $this->input->post('id');

        try {
            $this->db->where('contractChecklistAutoID',$id)->delete('srp_erp_op_module_contractchecklist');
            $this->session->set_flashdata('s',  'Successfully deleted the records');
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e',  'Something went wrong');
            return false;
        }
    }

    function delete_asset_detail(){

        $id = $this->input->post('id');

        try {
            $this->db->where('id',$id)->delete('srp_erp_contractassets');
            $this->session->set_flashdata('s',  'Successfully deleted the records');
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e',  'Something went wrong');
            return false;
        }
    }

    
    function delete_contract_visibility(){

        $id = $this->input->post('id');

        try {
            $this->db->where('visibilityAutoID',$id)->delete('srp_erp_op_module_contractvisibility');
            $this->session->set_flashdata('s',  'Successfully deleted the records');
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e',  'Something went wrong');
            return false;
        }
    }

    function save_contract_assets(){

        $assets = $this->input->post('assets');
        $assets_name = $this->input->post('assets_name');
        $assets_reference = $this->input->post('assets_reference');
        $contractAutoID = $this->input->post('contractAutoID');
        $groupToAsset = $this->input->post('groupToAsset');
        $data_arr = array();

        try {

            foreach($assets as $key => $asset){
                $asset = $asset;
    
                if($asset){
                    $asset_details = get_contract_assets(null,$asset);
                    $ex_asset = get_contract_asset_detail($asset,$contractAutoID);

                    if($ex_asset){
                        $this->session->set_flashdata('e', $asset_details['assetDescription'].' Already added to this contract.');
                    }else{
                        $data_arr['contractAutoID'] = $contractAutoID;
                        $data_arr['faID'] = $asset;
                        $data_arr['assetName'] = isset($assets_name[$key]) ? $assets_name[$key] : '';
                        $data_arr['assetRef'] =  isset($assets_reference[$key]) ? $assets_reference[$key] : '';
                        $data_arr['assetDescription'] =  isset($asset_details['assetDescription']) ? $asset_details['assetDescription'] : '';
                        $data_arr['faCode'] =  isset($asset_details['faCode']) ? $asset_details['faCode'] : '';
                        $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
                        $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
                        $data_arr['date_added'] =  $this->common_data['current_date'];
                        $data_arr['added_by'] =  $this->common_data['current_user'];
                        $data_arr['groupToID'] = isset($groupToAsset[$key]) ? $groupToAsset[$key] : '';
                        if(count($groupToAsset)>0){
                            $this->db->select('*');
                            $this->db->from('srp_erp_op_module_group_to');
                            $this->db->where('groupAutoID', $groupToAsset[$key]);
                            $group = $this->db->get()->row_array();
                            $data_arr['groupToName'] = $group['groupName'];
                        }
        
                        // Save the data
                        $this->db->insert('srp_erp_contractassets', $data_arr);
                        $last_detail_id = $this->db->insert_id();

                        $this->session->set_flashdata('s',  $asset_details['assetDescription'].' Successfully added the records');
                       
                    }
    
                   
    
                }
               
            }

            return true;
            
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            return false;
        }

    }

    function edit_contract_asset_crew(){

        $asset = $this->input->post('assets');
        $assets_name = $this->input->post('assets_name');
        $assets_reference = $this->input->post('assets_reference');
        $contractAutoID = $this->input->post('contractAutoID');
        $asset_id = $this->input->post('asset_id');
         $groupToAsset = $this->input->post('groupToAsset');
        $data_arr = array();

        try {

            $asset_details = get_contract_assets(null,$asset);
            $ex_asset = get_contract_asset_detail($asset,$contractAutoID);

            if($ex_asset && ($ex_asset['id'] != $asset_id)){
                $this->session->set_flashdata('e', $asset_details['assetDescription'].' Already added to this contract.');
            }else{
                $data_arr['contractAutoID'] = $contractAutoID;
                $data_arr['faID'] = $asset;
                $data_arr['assetName'] = $assets_name;
                $data_arr['assetRef'] =  $assets_reference;
                $data_arr['assetDescription'] =  isset($asset_details['assetDescription']) ? $asset_details['assetDescription'] : '';
                $data_arr['faCode'] =  isset($asset_details['faCode']) ? $asset_details['faCode'] : '';
                $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
                $data_arr['added_by'] =  $this->common_data['current_user'];

                $data_arr['groupToID'] = isset($groupToAsset) ? $groupToAsset : '';

                if($groupToAsset){
                    $this->db->select('*');
                    $this->db->from('srp_erp_op_module_group_to');
                    $this->db->where('groupAutoID', $groupToAsset);
                    $group = $this->db->get()->row_array();
                    $data_arr['groupToName'] = $group['groupName'];
                }


                // Save the data
                $this->db->where('id',$asset_id);
                $this->db->update('srp_erp_contractassets', $data_arr);
                $last_detail_id = $this->db->insert_id();

                $this->session->set_flashdata('s',  $asset_details['assetDescription'].' Successfully updated the records');
               
            }

            return true;
            
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    function save_contract_group_to()
    {
        $groupName = trim($this->input->post('groupName') ?? '');
        $groupType = trim($this->input->post('groupType') ?? '');
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $job_id = trim($this->input->post('job_id') ?? '');

        $this->db->trans_start();

        $this->db->select('*');
        $this->db->from('srp_erp_op_module_group_to');
        $this->db->where('groupName', $groupName);
        $this->db->where('groupType', $groupType);
        if($groupType == 3){
            $this->db->where('job_id', $job_id);
        }else{
            $this->db->where('contractAutoID', $contractAutoID);
        }
       
        $group = $this->db->get()->row_array();

        if($group){
            $this->session->set_flashdata('e', 'Record already exists');
            return array('status'=>false, 'id' => '','name'=>'');
        }else{
            $data['job_id'] = $job_id;
            $data['groupName'] = $groupName;
            $data['groupType'] = $groupType;
            $data['contractAutoID'] = $contractAutoID;
            $data['companyID'] = current_companyID();
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['status'] = 1;
            $this->db->insert('srp_erp_op_module_group_to', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->_error_message();
                $this->session->set_flashdata('e', ' Error');
                return array('status' => false, 'message' => 'Error: ' . $error);

            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', ' Record Added Successfully');
                return array('status'=>true, 'id' => $last_id,'name'=>$groupName);
            }

        }

    }

    function save_contract_group_to_category()
    {
        $groupName = trim($this->input->post('groupName') ?? '');
        //$groupType = trim($this->input->post('groupType') ?? '');
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        //$job_id = trim($this->input->post('job_id') ?? '');

        $this->db->trans_start();

        $this->db->select('*');
        $this->db->from('srp_erp_op_contract_details_category_list');
        $this->db->where('categoryName', $groupName);
        $this->db->where('contractID', $contractAutoID);
        $this->db->where('companyID', current_companyID());
        $group = $this->db->get()->row_array();

        if($group){
            $this->session->set_flashdata('e', 'Record already exists');
            return array('status'=>false, 'id' => '','name'=>'');
        }else{

            $data['categoryName'] = $groupName;
            $data['contractID'] = $contractAutoID;
            $data['companyID'] = current_companyID();
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['status'] = 1;
            $this->db->insert('srp_erp_op_contract_details_category_list', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->_error_message();
                $this->session->set_flashdata('e', ' Error');
                return array('status' => false, 'message' => 'Error: ' . $error);

            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', ' Record Added Successfully');
                return array('status'=>true, 'id' => $last_id,'name'=>$groupName);
            }

        }

    }

    
    function assignCheckListForContract()
    {
        $assignCheckListSync = $this->input->post('assignCheckListSync');
        $contractAutoID=$this->input->post('contractAutoID');

        if (!empty($assignCheckListSync)) {
            foreach ($assignCheckListSync as $key => $assignCheckList) {

                $data['checklistID'] = $assignCheckList;
                $data['status'] = 1;
                $data['companyID'] = current_companyID();
                $data['contractAutoID'] = $contractAutoID;
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_op_module_contractchecklist', $data);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'CheckList Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'CheckList Assigned Successfully');
        }
    }

    function selectCallingUpdate()
    {

        $callID = trim($this->input->post('callID') ?? '');
        $masterID = trim($this->input->post('masterID') ?? '');

        $upData = array(
            'callingCode' => $callID,
        );

        $this->db->where('contractChecklistAutoID', $masterID)->update('srp_erp_op_module_contractchecklist', $upData);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Calling Details Updated Successfully.');
        }
    }

    function selectChecklistUserUpdate()
    {

        $users = $this->input->post('users');
        $masterID = trim($this->input->post('masterID') ?? '');

        if($users){
            $arraydata1 = implode(",", $users);
            $upData = array(
                'checklistAccessUser' => $arraydata1,
            );

            $this->db->where('contractChecklistAutoID', $masterID)->update('srp_erp_op_module_contractchecklist', $upData);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Checklist Updated Successfully.');
        }
    }


    function save_visibility()
    {
        $section = $this->input->post('section');
        $customerCode = $this->input->post('customerCode');
        $actionAr = $this->input->post('actionAr');
        $contractAutoID=$this->input->post('contractAutoID');

        if ($contractAutoID) {
            
            $arraydata1 = implode(",", $customerCode);
            $arraydata2 = implode(",",$actionAr);

            $data['visibilityuserIDs'] =  $arraydata1;
            $data['status'] = 1;
            $data['companyID'] = current_companyID();
            $data['contractAutoID'] = $contractAutoID;
            $data['actionCodes'] = $arraydata2;
            
            $data['sectionCode'] =$section ;

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_op_module_contractvisibility', $data);
            

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', ' Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', ' Saved Successfully.');
            $this->db->trans_commit();
    
            return array('status' => true, 'last_id' => "");
        }
    
    }

    function save_visibility_edit()
    {
        $section = $this->input->post('section_edit');
        $customerCode = $this->input->post('customerCode_edit');
        $actionAr = $this->input->post('actionAr_edit');
       // $contractAutoID=$this->input->post('contractAutoID');
        $visibilityAutoID=$this->input->post('visibilityAutoID');

        if ($visibilityAutoID) {
            
            $arraydata1 = implode(",", $customerCode);
            $arraydata2 = implode(",",$actionAr);

            $data['visibilityuserIDs'] =  $arraydata1;
            $data['actionCodes'] = $arraydata2;
            
            $data['sectionCode'] =$section ;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('visibilityAutoID', $visibilityAutoID);
            $this->db->update('srp_erp_op_module_contractvisibility', $data);

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', ' update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', ' update Successfully.');
            $this->db->trans_commit();
    
            return array('status' => true, 'last_id' => "");
        }
    
    }

    function fetch_line_visibility_edit()
    {
        $masterID = $this->input->post('masterID');
        $companyid = current_companyID();

        $this->db->select('*');
        $this->db->from('srp_erp_op_module_contractvisibility');
        $this->db->where('visibilityAutoID', $masterID);
        $this->db->where('companyID', $companyid);
        return $this->db->get()->row_array();
    }

    function create_amendment_for_document(){

      
        $ammendmentType = $this->input->post('ammendmentType');
        $contractAutoID = $this->input->post('contractAutoID');
        $currentAmendmentID = $this->input->post('currentAmendmentID');

        $contract_details_app = $this->db->query("SELECT * FROM srp_erp_contractmaster WHERE contractAutoID = '$contractAutoID'")->row_array();

        $num_amendment = $this->db->query("SELECT * FROM srp_erp_document_amendments WHERE docID = '$contractAutoID' and docType = 'CNT'")->result_array();
        //check approved

        if($contract_details_app){

            if(empty($currentAmendmentID)){

                $current_amendment_num = count($num_amendment) + 1;
                $current_contract_code = explode('/A/',$contract_details_app['contractCode']);

                $data = array();
                $data['docType'] = $contract_details_app['documentID'];
                $data['docID'] = $contractAutoID;
                $data['docCode'] = $contract_details_app['contractCode'];
                $data['amendmentType'] = implode(',',$ammendmentType);
                $data['amendmentCode'] = $current_contract_code[0]."/A/".$current_amendment_num;
                $data['createdDate'] = $this->common_data['current_date'];
                $data['createdUser'] = $this->common_data['current_user'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['companyID'] = current_companyID();
                $data['companyCode'] =  $this->common_data['company_data']['company_code'];

                $res = $this->db->insert('srp_erp_document_amendments',$data);

                $currentAmendmentID = $this->db->insert_id();

                //update current amendment id
                $data_contract = array();
                $data_contract['currentAmedmentID'] = $currentAmendmentID;
                $res = $this->db->where('contractAutoID',$contractAutoID)->update('srp_erp_contractmaster',$data_contract);
                

            }else{

            }

            $this->session->set_flashdata('s', ' Amendment Created Successfully.');
            return True;
        }


    }

    function close_amendment_for_document(){

        $contractAutoID = $this->input->post('contractAutoID');
        $currentAmendmentID = $this->input->post('currentAmendmentID');

        $contract_details_app = $this->db->query("SELECT * FROM srp_erp_contractmaster WHERE contractAutoID = '$contractAutoID'")->row_array();

        if($contract_details_app){
            $amendmentID = $contract_details_app['currentAmedmentID'];

            $data = array();
            $data['status'] = 1;

            $res = $this->db->where('id',$amendmentID)->update('srp_erp_document_amendments',$data);
        }

        $this->session->set_flashdata('s', ' Amendment Closed Successfully.');
        return True;

    }

    function fetch_amendment_details(){

        $amendmentID = $this->input->post('amendmentID');

        $this->db->select('*');
        $this->db->from('srp_erp_document_amendments');
        $this->db->where('id', $amendmentID);
        return $this->db->get()->row_array();

    }

    function update_contract_extra_charge(){

        $contractAutoID = $this->input->post('contractAutoID');
        $extraChargeID = $this->input->post('extraChargeID');
        $column = $this->input->post('column');
        $changed_value = $this->input->post('changed_value');
        $id = $this->input->post('id');
        
        $this->db->select('*');
        $this->db->from('srp_erp_contractextracharges');
        $this->db->where('id', $id);
        $ex_chargeID = $this->db->get()->row_array();

        if($ex_chargeID){

            $ex_chargeID[$column] = $changed_value;
            
            $data = array();

            $data[$column] = $changed_value;
            $data['markup_value'] = (($ex_chargeID['extraCostValue'] * $ex_chargeID['markup_percentage'])/100);
            $data['commission_value'] = (($ex_chargeID['extraCostValue'] * $ex_chargeID['commission_percentage'])/100);
            $data['top_margin_value'] = $data['markup_value'];
           

            $this->db->where('id',$id)->update('srp_erp_contractextracharges',$data);

            return array('s', 'Update Successfull');
    
        }


    }

    function update_contract_detail_value(){

        $contractAutoID = $this->input->post('contractAutoID');
        $column = $this->input->post('column');
        $changed_value = $this->input->post('changed_value');
        $id = $this->input->post('id');

        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractDetailsAutoID', $id);
        $details = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('contractAutoID', $details['contractAutoID']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $contract_master = $this->db->get('srp_erp_contractmaster')->row_array();

        if($details){

            $data = array();

            $data['taxAmount'] = $details['taxAmount'];
            $data['discountAmount'] = $details['discountAmount'];
            $data['commissionValue'] = $details['commissionValue'];

            if($contract_master['rounding'] == 1){
                $details['unittransactionAmount'] = ceil($details['unittransactionAmount']);
                $sales_price = ceil($details['unittransactionAmount'] * $details['requestedQty']);
            }else{
                $details['unittransactionAmount'] = floor($details['unittransactionAmount']);
                $sales_price = floor($details['unittransactionAmount'] * $details['requestedQty']);
            }
            

            if($column == 'discountPercentage'){
                $data['discountPercentage'] = $changed_value;
               // $item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']
                //($details['poUnitPrice']*$details['ap_amount']) + $details['poUnitPrice'];
                $discountAmount = ($sales_price * $changed_value) / 100;
                $data['discountAmount'] = $discountAmount;
            }

            if($column == 'taxPercentage'){
                $data['taxPercentage'] = $changed_value;
               // $item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']
                //$sales_price = $details['unittransactionAmount'] * $details['requestedQty'];//($details['poUnitPrice']*$details['ap_amount']) + $details['poUnitPrice'];
                $taxAmount = (($sales_price - $details['discountAmount']) * $changed_value) / 100;
                $data['taxAmount'] = $taxAmount;

            }

            if($column == 'commissionPercentage'){
                $data['commissionPercentage'] = $changed_value;
               // $item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']
                $sales_price_com = $details['poUnitPrice'];//($details['poUnitPrice']*$details['ap_amount']) + $details['poUnitPrice'];
                $commissionValue = (($sales_price_com) * $changed_value) / 100;
                $data['commissionValue'] = $commissionValue;

            }

            $data['unittransactionAmount'] = $details['unittransactionAmount'];
            $data['transactionAmount'] = ($sales_price) - $data['discountAmount'];
            $data['salesPriceTotal'] = $data['transactionAmount'];
            $data['companyLocalAmount'] = ($details['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($details['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($details['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);


            $this->db->where('contractDetailsAutoID',$id)->update('srp_erp_contractdetails',$data);

            return array('s', 'Update Successfullly');

        }


    }

    function update_ap_value(){

        $contractAutoID = $this->input->post('contractAutoID');
        $ap_unit = $this->input->post('ap_unit');

        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $contract_master = $this->db->get('srp_erp_contractmaster')->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', $contractAutoID);
        $itemdetails = $this->db->get()->result_array();

        foreach($itemdetails as $item){

            $data = array();
            $contractDetailsAutoID = $item['contractDetailsAutoID'];

            $item['ap_amount'] = $ap_unit;

            $data['ap_amount'] = $ap_unit;

          
            //$sales_price = ($item['poUnitPrice']*$item['ap_amount']) + $item['poUnitPrice'];
        
            if($contract_master['rounding'] == 1){
                $sales_price = (ceil(($item['unitAmount']*$item['ap_amount']) + $item['unitAmount']) * $item['requestedQty']);
                $data['salesPriceTotal'] = ceil($sales_price);
                $sales_price = ceil($sales_price);

                $discount_amount = ($sales_price * $item['discountPercentage']) / 100;
                $tax_amount = (($sales_price-$discount_amount) * $item['taxPercentage']) / 100;
                $unittransaction =   ceil($sales_price / $item['requestedQty']);

            }else{
                $sales_price = (floor(($item['unitAmount']*$item['ap_amount']) + $item['unitAmount']) * $item['requestedQty']);
                $data['salesPriceTotal'] = floor($sales_price);
                $sales_price = floor($sales_price);

                $discount_amount = ($sales_price * $item['discountPercentage']) / 100;
                $tax_amount = (($sales_price-$discount_amount) * $item['taxPercentage']) / 100;
                $unittransaction =  floor($sales_price / $item['requestedQty']);
            }
           
            
           
            $data['unittransactionAmount'] = $unittransaction;
            $data['discountAmount'] = $discount_amount;
            // $data['taxAmount'] = $tax_amount;
            $data['transactionAmount'] = $sales_price - $discount_amount;
            $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);

            $this->db->where('contractDetailsAutoID',$contractDetailsAutoID)->update('srp_erp_contractdetails',$data);

        }

        return array('s', 'Update Successfullly');

    }

    function update_approved_status(){

        $contractAutoID = $this->input->post('contractAutoID');
        $statuspv = $this->input->post('statuspv');
        $commentpv = $this->input->post('commentpv');
        $colectedbyemp = $this->input->post('colectedbyemp');
        $collectiondatepv = $this->input->post('collectiondatepv');
        $commentpv = $this->input->post('commentpv');

        $data = array();

        $data['documentStatus'] = $statuspv;
        if($commentpv){
            $data['approved_comment'] = $commentpv;
        }

        if($colectedbyemp){
            $data['approved_by'] = $colectedbyemp;
        }

        if($collectiondatepv){
            $data['approved_date'] = date('Y-m-d',strtotime($collectiondatepv));
        }
      

        $this->db->where('contractAutoID',$contractAutoID)->update('srp_erp_contractmaster',$data);

        return array('s', 'Update Successfullly');

    }

    function update_rounding_value(){
        $contractAutoID = $this->input->post('contractAutoID');
        $rounding = $this->input->post('rounding');

        $data = array();

        $data['rounding'] = $rounding;

        $this->db->where('contractAutoID',$contractAutoID)->update('srp_erp_contractmaster',$data);

        return array('s', 'Update Successfullly');

    }

}
