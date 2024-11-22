<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Procurement_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_purchase_order_header()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDate') ?? '');
        $POdate = trim($this->input->post('POdate') ?? '');
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($POdate, $date_format_policy);
        $rcmApplicable = trim($this->input->post('rcmApplicable') ?? '');
        $rcmYN = trim($this->input->post('rcmYN') ?? '');
        $assignCatPO = getPolicyValues('ECPO', 'All');
        $activeIncotermsPolicy = getPolicyValues('AITS', 'All');
        $activeRetensionPolicy = getPolicyValues('ACRT', 'All');
        $enableBlanketPoPolicy = getPolicyValues('BPOE', 'All');
        $warehouseSelectPO = getPolicyValues('WSPO', 'All');
        $addowner = getPolicyValues('POOW', 'All');

        if($assignCatPO==1){
            $itemCategoryID = trim($this->input->post('itemCatType') ?? '');
            $data['itemCategoryID'] = $itemCategoryID;
        }

        if($addowner==1){
            $owner = trim($this->input->post('owner') ?? '');
            $data['ownerID'] = $owner;
        }

        if($warehouseSelectPO==1){
            $location = trim($this->input->post('location') ?? '');
            $data['mfqWarehouseAutoID'] = $location;
        }

        if($activeIncotermsPolicy==1){
            $incoterms = trim($this->input->post('incoterms') ?? '');
            $data['incotermsID'] = $incoterms;
        }

        if($activeRetensionPolicy==1){ 
            $retension_date = trim($this->input->post('retension_date') ?? '');
            $retension_percentage = trim($this->input->post('retension_percentage') ?? '');
            $data['retensionDate'] = input_format_date($retension_date, $date_format_policy);
            $data['retensionPercentage'] = $retension_percentage;
        }

       // $payTerm = trim($this->input->post('payTerm') ?? '');
        $modeofship = trim($this->input->post('modeofship') ?? '');

       // $data['payTerm'] = $payTerm;
        $data['modeOfShipID'] = $modeofship;

        if($enableBlanketPoPolicy==1){
            $frequencyID = $this->input->post('frequencyID');
            $frequencyAmount = $this->input->post('frequencyAmount');
            $blanketpo_val = $this->input->post('blanketpo_val');

            if($blanketpo_val==1){
                $data['isBlanketPo'] = $blanketpo_val;
                $data['frequencyID'] = $frequencyID;
                $data['frequencyAmount'] = $frequencyAmount;
            }else{
                $data['isBlanketPo'] = 0;
                $data['frequencyID'] = null;
                $data['frequencyAmount'] = null;
            }
        }
        
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierPrimaryCode') ?? ''));
        $ship_data = fetch_address_po(trim($this->input->post('shippingAddressID') ?? ''));

        if(empty($ship_data))
        {
            $this->session->set_flashdata('e', 'Shipping address not found');
            return array('status' => false);
        }

        $sold_data = fetch_address_po(trim($this->input->post('soldToAddressID') ?? ''));
        $invoice_data = fetch_address_po(trim($this->input->post('invoiceToAddressID') ?? ''));

        $data['documentID'] = 'PO';
        $data['documentTaxType'] = $this->input->post('documentTaxType');

        $narration = ($this->input->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);

        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');
        $data['supplierPrimaryCode'] = trim($this->input->post('supplierPrimaryCode') ?? '');
        $data['purchaseOrderType'] = trim($this->input->post('purchaseOrderType') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->input->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['referenceNumber'] = trim($this->input->post('referenceNumber') ?? '');
        $data['creditPeriod'] = trim($this->input->post('creditPeriod') ?? '');
        $data['soldToAddressID'] = trim($this->input->post('soldToAddressID') ?? '');
        $data['shippingAddressID'] = trim($this->input->post('shippingAddressID') ?? '');
        $data['invoiceToAddressID'] = trim($this->input->post('invoiceToAddressID') ?? '');
        $data['supplierID'] = $supplier_arr['supplierAutoID'];
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;

        $paymentTerms = ($this->input->post('paymentTerms'));
        $data['paymentTerms'] = str_replace('<br />', PHP_EOL, $paymentTerms);
        $penaltyTerms = ($this->input->post('penaltyTerms'));
        $data['penaltyTerms'] = str_replace('<br />', PHP_EOL, $penaltyTerms);
        $deliveryTerms = ($this->input->post('deliveryTerms'));
        $data['deliveryTerms'] = str_replace('<br />', PHP_EOL, $deliveryTerms);

        $data['shippingAddressID'] = $ship_data['addressID'];
        $data['shippingAddressDescription'] = trim($this->input->post('shippingAddressDescription') ?? '');
        $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
        $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
        $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
        $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];

        if(!empty($invoice_data))
        {
            $data['invoiceToAddressID'] = $invoice_data['addressID'];
            $data['invoiceToAddressDescription'] = $invoice_data['addressDescription'];
            $data['invoiceTocontactPersonID'] = $invoice_data['contactPerson'];
            $data['invoiceTocontactPersonTelephone'] = $invoice_data['contactPersonTelephone'];
            $data['invoiceTocontactPersonFaxNo'] = $invoice_data['contactPersonFaxNo'];
            $data['invoiceTocontactPersonEmail'] = $invoice_data['contactPersonEmail'];
        }

        if(!empty($sold_data))
        {
            $data['soldToAddressID'] = $sold_data['addressID'];
            $data['soldToAddressDescription'] = $sold_data['addressDescription'];
            $data['soldTocontactPersonID'] = $sold_data['contactPerson'];
            $data['soldTocontactPersonTelephone'] = $sold_data['contactPersonTelephone'];
            $data['soldTocontactPersonFaxNo'] = $sold_data['contactPersonFaxNo'];
            $data['soldTocontactPersonEmail'] = $sold_data['contactPersonEmail'];
        }

        $data['customerOrderID'] = trim($this->input->post('customer_order_id') ?? '');
        $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['contactPersonName'] = trim($this->input->post('contactperson') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactnumber') ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

        $crTypes = explode('<table', $this->input->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['termsandconditions'] = $notes;
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
        
        $data['logisticYN'] = trim($this->input->post('logisticYN') ?? '');
        
        if (trim($this->input->post('purchaseOrderID') ?? '')) {
            $logisticPurchaseOrder = $this->getPOLogisticDetail(trim($this->input->post('purchaseOrderID') ?? ''));

            if(!empty($logisticPurchaseOrder) && 'LOG' !== $data['purchaseOrderType']){
                $this->session->set_flashdata('e', 'You cannot change the purchase order type from logistic because you have already pulled the logistic PO to the detail.');
                return array('status' => false);
            }

           $isGroupBasedTaxYn = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster', trim($this->input->post('purchaseOrderID') ?? ''), 'PO', 'purchaseOrderID');

           $data['rcmApplicableYN'] = (($isGroupBasedTaxYn==1)?$rcmYN:0); ;

            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('purchaseOrderID'), 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All')==1)?1:0);
            $data['rcmApplicableYN'] = ((getPolicyValues('GBT', 'All')==1)?$rcmYN:0); ;

            $this->db->insert('srp_erp_purchaseordermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                //  pull customer order details
                if($data['purchaseOrderType'] == 'BCO'){
                    $this->get_pull_customer_order_items($last_id);
                }

                if($data['purchaseOrderType'] == 'BQUT'){
                   $this->get_pull_quotation_order_items($last_id);
                }
                
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        }
    }


    function get_pull_customer_order_items($purchaseOrderID = null){

        $this->db->select('*');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $purchaseOrderDetails = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        
        if($purchaseOrderDetails){

            $customerOrderID = $purchaseOrderDetails['customerOrderID'];

            $this->db->select('*');
            $this->db->where('customerOrderID', $customerOrderID);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $customerOrderHeader = $this->db->get('srp_erp_srm_customerordermaster')->row_array();

            $this->db->select('*');
            $this->db->where('customerOrderID', $customerOrderID);
            $this->db->where('srp_erp_srm_customerorderdetails.companyID', $this->common_data['company_data']['company_id']);
            $this->db->join('srp_erp_itemmaster as item','srp_erp_srm_customerorderdetails.itemAutoID = item.itemAutoID','left');
            $CO_items = $this->db->get(' srp_erp_srm_customerorderdetails')->result_array();

            foreach($CO_items as $item){

                $data_arr = array();
                $data_arr['purchaseOrderID'] = $purchaseOrderID;
                $data_arr['itemAutoID'] = $item['itemAutoID'];
                $data_arr['itemType'] = $item['mainCategory'];
                $data_arr['itemSystemCode'] = $item['itemSystemCode'];
                $data_arr['itemDescription'] = $item['itemDescription'];
                $data_arr['defaultUOMID'] = $item['defaultUnitOfMeasureID'];
                $data_arr['defaultUOM'] = $item['defaultUnitOfMeasure'];
                $data_arr['unitOfMeasureID'] = $item['unitOfMeasureID'];
                $data_arr['unitOfMeasure'] = $item['defaultUnitOfMeasure'];
                $data_arr['conversionRateUOM'] = 1;
                $data_arr['requestedQty'] = $item['requestedQty'];
                $data_arr['unitAmount'] = $item['unitAmount'];
                $data_arr['totalAmount'] = $item['totalAmount'];
                $data_arr['discountAmount'] = $item['discountAmount'];
                $data_arr['comment'] = $item['comment'];
                $data_arr['companyID'] = $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

                $this->db->insert('srp_erp_purchaseorderdetails', $data_arr);

            }

            return True;

        }


    }

    function get_pull_quotation_order_items($purchaseOrderID = null){


        $this->db->select('*');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $purchaseOrderDetails = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        
        if($purchaseOrderDetails){

            $contractAutoID = $purchaseOrderDetails['contractAutoID'];

            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $contractDetails = $this->db->get('srp_erp_contractmaster')->row_array();

            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $this->db->where('srp_erp_contractdetails.companyID', $this->common_data['company_data']['company_id']);
            $this->db->join('srp_erp_itemmaster as item','srp_erp_contractdetails.itemAutoID = item.itemAutoID','left');
            $CO_items = $this->db->get('srp_erp_contractdetails')->result_array();

            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $this->db->where('extraCostID !=', '-1');
            $extraCharges = $this->db->get('srp_erp_contractextracharges')->result_array();

        
           
            foreach($CO_items as $item){

                $data_arr = array();
                $data_arr['purchaseOrderID'] = $purchaseOrderID;
                $data_arr['itemAutoID'] = $item['itemAutoID'];
                $data_arr['itemType'] = $item['mainCategory'];
                $data_arr['itemSystemCode'] = $item['itemSystemCode'];
                $data_arr['itemDescription'] = $item['itemDescription'];
                $data_arr['defaultUOMID'] = $item['defaultUnitOfMeasureID'];
                $data_arr['defaultUOM'] = $item['defaultUnitOfMeasure'];
                $data_arr['unitOfMeasureID'] = $item['unitOfMeasureID'];
                $data_arr['unitOfMeasure'] = $item['defaultUnitOfMeasure'];
                $data_arr['conversionRateUOM'] = 1;
                $data_arr['requestedQty'] = $item['requestedQty'];
                $data_arr['unitAmount'] = $item['unitAmount'];
                $data_arr['comission_percentage'] = $item['commissionPercentage'];
                $data_arr['commision_value'] = $item['commissionValue'];
                $data_arr['totalAmount'] = $item['requestedQty'] * $item['unitAmount'];//$item['transactionAmount'];
                $data_arr['discountAmount'] = 0;//$item['discountAmount'];
                $data_arr['comment'] = $item['comment'];
                $data_arr['companyID'] = $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

                $this->db->insert('srp_erp_purchaseorderdetails', $data_arr);

            }

            foreach($extraCharges as $charges){

                $data_arr = array();
                $data_arr['purchaseOrderID'] = $purchaseOrderID;
                $data_arr['extraCostName'] = $charges['extraCostName'];
                $data_arr['extraCostID'] = $charges['extraCostID'];
                $data_arr['extraCostValue'] = $charges['extraCostValue'];
                $data_arr['top_margin_value'] = $charges['top_margin_value'];
                $data_arr['markup_percentage'] = $charges['markup_percentage'];
                $data_arr['markup_value'] = $charges['markup_value'];
                $data_arr['commission_percentage'] = $charges['commission_percentage'];
                $data_arr['commission_value'] = $charges['commission_value'];
                $data_arr['companyID'] = $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

                $this->db->insert('srp_erp_purchaseorderextracharges', $data_arr);

            }

            return True;

        }


    }

    function save_uom()
    {
        $this->db->trans_start();
        $data['UnitShortCode'] = trim($this->input->post('UnitShortCode') ?? '');
        $data['UnitDes'] = trim($this->input->post('UnitDes') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('UnitID') ?? '')) {
            $this->db->where('UnitID', trim($this->input->post('UnitID') ?? ''));
            $this->db->update('srp_erp_unit_of_measure', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Update Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('UnitID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_unit_of_measure', $data);
            $last_id = $this->db->insert_id();
            $this->db->insert('srp_erp_unitsconversion', array('masterUnitID' => $last_id, 'subUnitID' => $last_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'companyID' => $this->common_data['company_data']['company_id']));

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Save Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_uom_conversion()
    {
        //$this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->insert('srp_erp_unitsconversion', $data);
        $last_id = $this->db->insert_id();

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $this->db->insert('srp_erp_unitsconversion', $data);
        //$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Save Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $data['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID') ?? '');
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

        $supplierCurrency = currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID') ?? '')) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID') ?? ''));
            $this->db->update('srp_erp_purchaseordertaxdetails', $data);
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
            $this->db->insert('srp_erp_purchaseordertaxdetails', $data);
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

    function change_conversion()
    {
        $this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Update Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_purchase_order_detail()
    {
        $purchaseOrderDetailsID = $this->input->post('purchaseOrderDetailsID');
        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $discount = $this->input->post('discount');
        $comment = $this->input->post('comment');
        $text_type = $this->input->post('text_type');
        $taxtotal = $this->input->post('taxtotal');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID');

        $this->db->select('*');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('confirmedYN', 2);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $dataMaster['valueChanged'] = 1;
            $this->db->where('purchaseOrderID',$purchaseOrderID);
            $this->db->update('srp_erp_purchaseordermaster', $dataMaster);
        }

        $this->db->select('documentTaxType,expectedDeliveryDate');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data($itemAutoID);

            $uomEx = explode('|', $uom[$key]);
            if (!$purchaseOrderDetailsID) {
                $this->db->select('purchaseOrderID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_purchaseorderdetails');
                $this->db->where('itemType', 'Inventory');
                $this->db->where('purchaseOrderID', $purchaseOrderID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                   // return array('w', 'Purchase Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $data['purchaseOrderID'] = $purchaseOrderID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data['itemType'] = $item_arr['mainCategory'];
            $data['detailExpectedDeliveryDate'] = $documentTaxType['expectedDeliveryDate'];
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = ($estimatedAmount[$key] / 100) * $discount[$key];
            $data['requestedQty'] = $quantityRequested[$key];
            $data['unitAmount'] = ($estimatedAmount[$key] - $data['discountAmount']);
            $data['totalAmount'] = ($data['unitAmount'] * $quantityRequested[$key]);
            $data['comment'] = $comment[$key];
            $data['remarks'] = '';

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['GRVSelectedYN'] = 0;
            $data['goodsRecievedYN'] = 0;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_purchaseorderdetails', $data);
            $last_id = $this->db->insert_id();
            
            if(!empty($text_type[$key])){
                
                if($group_based_tax == 1){ 
                    $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', $purchaseOrderID);

                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$text_type[$key]);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
                    $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
            
                    $dataTax['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID') ?? '');
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
                    $dataTax['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataTax['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataTax['createdUserGroup'] = $this->common_data['user_group'];
                    $dataTax['createdPCID'] = $this->common_data['current_pc'];
                    $dataTax['createdUserID'] = $this->common_data['current_userID'];
                    $dataTax['createdUserName'] = $this->common_data['current_user'];
                    $dataTax['createdDateTime'] = $this->common_data['current_date'];

                    tax_calculation_vat('srp_erp_purchaseordertaxdetails',$dataTax,$text_type[$key],'purchaseOrderAutoID',trim($this->input->post('purchaseOrderID') ?? ''),($estimatedAmount[$key]*$quantityRequested[$key]),'PO',$last_id,($data['discountAmount']*$data['requestedQty']),1, $isRcmDocument);
               
                }else {
                     // $this->line_by_tax_calculation($text_type[$key],$purchaseOrderID,$last_id,$data['totalAmount']);
                    $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$text_type[$key]}")->row_array();
                    if($taxCat['taxCategory'] == 2) {
                        $vatSubCat = $this->db->query("SELECT percentage 
                                                        FROM srp_erp_tax_vat_sub_categories
                                                            JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                        WHERE taxMasterAutoID = {$text_type[$key]}
                                                            AND itemAutoID = {$itemAutoID}")->row('percentage');
                        if($vatSubCat) {
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $vatSubCat;
                        } else {
                            $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                                FROM srp_erp_suppliermaster 
                                                JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                                WHERE purchaseOrderID = {$purchaseOrderID}")->row_array();
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $suppliertaxPercentage['vatPercentage'];
                        }     
                    } else {
                        $data['taxAmount'] = ($data['totalAmount'] / 100) * $taxCat['taxPercentage'];
                    }
                    $data['isVAT'] = 1;
                    $data['taxCalculationformulaID'] = $text_type[$key];
                    $this->db->where('purchaseOrderDetailsID', trim($last_id));
                    $this->db->update('srp_erp_purchaseorderdetails', $data);
                }

            }

            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage');
                $this->db->where('purchaseOrderID', $purchaseOrderID);
                $this->db->from('srp_erp_purchaseordermaster');
                $currency = $this->db->get()->row_array();


                $amount = $this->db->query("SELECT
                                                    SUM(totalAmount) as totalAmount
                                            FROM
                                                `srp_erp_purchaseorderdetails`
                                                JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
                                                LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_purchaseorderdetails`.`taxCalculationformulaID` 
                                            WHERE
                                                `purchaseOrderID` = '{$purchaseOrderID}'
                                                GROUP BY
                                                purchaseOrderID")->row_array();

                $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $amount['totalAmount']);
                /* print_r($disc_foottotal);
                 exit();*/
                /* $txtotal=$this->input->post('taxtotal');*/
                $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                $taxtotal=($taxtotal_amount);
                if($group_based_tax == 1){ 
                    tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$this->input->post('purchaseOrderID'),$taxtotal,$disc_foottotal,'PO');
                }else { 
                    $this->update_po_generaltax($purchaseOrderID,$taxtotal);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Purchase Order Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Purchase Order Details :  Saved Successfully.');
        }

    }

    function update_purchase_order_detail()
    {
        $text_type = $this->input->post('text_type');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');
        $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));

        if (!empty($this->input->post('purchaseOrderDetailsID'))) {
            $this->db->select('purchaseOrderID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('itemType', 'Inventory');
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('purchaseOrderDetailsID !=', trim($this->input->post('purchaseOrderDetailsID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                //return array('w', 'Purchase Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->select('documentTaxType,purchaseOrderType');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

        $this->db->select('totalAmount');
        $this->db->where('purchaseOrderDetailsID', $this->input->post('purchaseOrderDetailsID'));
        $po_detail = $this->db->get('srp_erp_purchaseorderdetails')->row_array();

        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $data['purchaseOrderID'] = trim($this->input->post('purchaseOrderID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['itemType'] = $item_arr['mainCategory'];
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($this->input->post('discount') ?? '');
        $data['discountAmount'] = (trim($this->input->post('estimatedAmount') ?? '') / 100) * trim($this->input->post('discount') ?? '');
        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unitAmount'] = (trim($this->input->post('estimatedAmount') ?? '') - $data['discountAmount']);
        $data['totalAmount'] = ($data['unitAmount'] * trim($this->input->post('quantityRequested') ?? ''));
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $purchaseOrderDetailID = $this->input->post('purchaseOrderDetailsID');
            $this->db->select('*');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('purchaseOrderDetailsID', $purchaseOrderDetailID);
            $query = $this->db->get();
            $poDetail = $query->row_array();

        if (trim($this->input->post('purchaseOrderDetailsID') ?? '')) {
            $this->db->where('purchaseOrderDetailsID', trim($this->input->post('purchaseOrderDetailsID') ?? ''));
            $this->db->update('srp_erp_purchaseorderdetails', $data);
            $this->db->trans_complete();

            if(!empty($text_type)){

                if($group_based_tax == 1){

                    if($documentTaxType['documentTaxType']==1 && !empty($text_type)){
                       // $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID');

                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$text_type);
                        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
                        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

                        $dataTax['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID') ?? '');
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

                        if($documentTaxType['purchaseOrderType']=='PR'){
                            //  fetchExistsDetailTBL('PO-PRQ',trim($this->input->post('purchaseOrderID') ?? ''),trim($this->input->post('purchaseOrderDetailsID') ?? ''),'srp_erp_purchaseordertaxdetails');
                            tax_calculation_vat(null,null,$text_type,'purchaseOrderAutoID',trim($this->input->post('purchaseOrderID') ?? ''),(trim($this->input->post('estimatedAmount') ?? '') * trim($this->input->post('quantityRequested') ?? '')),'PO-PRQ',trim($this->input->post('purchaseOrderDetailsID') ?? ''),($data['discountAmount']*$data['requestedQty']),1, $isRcmDocument);
                        } else {

                            //  fetchExistsDetailTBL('PO',trim($this->input->post('purchaseOrderID') ?? ''),trim($this->input->post('purchaseOrderDetailsID') ?? ''),'srp_erp_purchaseordertaxdetails');
                            tax_calculation_vat('srp_erp_purchaseordertaxdetails',$dataTax,$text_type,'purchaseOrderAutoID',trim($this->input->post('purchaseOrderID') ?? ''),(trim($this->input->post('estimatedAmount') ?? '') * trim($this->input->post('quantityRequested') ?? '')),'PO',trim($this->input->post('purchaseOrderDetailsID') ?? ''),($data['discountAmount']*$data['requestedQty']),1, $isRcmDocument);
                        }

                    } else {
                        fetchExistsDetailTBL('PO', trim($this->input->post('purchaseOrderID') ?? ''),trim($this->input->post('purchaseOrderDetailsID') ?? ''),'srp_erp_purchaseordertaxdetails',1,$data['totalAmount']);
                    }





                }else {

                    $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
                    $purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');
                    $data['taxAmount'] = 0;
                    $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$text_type}")->row_array();
                    if($taxCat['taxCategory'] == 2) {
                        $vatSubCat = $this->db->query("SELECT percentage 
                                                    FROM srp_erp_tax_vat_sub_categories
                                                        JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                    WHERE taxMasterAutoID = {$text_type}
                                                        AND itemAutoID = {$itemAutoID}")->row('percentage');
                        if($vatSubCat) {
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $vatSubCat;
                        } else {
                            $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                            FROM srp_erp_suppliermaster 
                                            JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                            WHERE purchaseOrderID = {$purchaseOrderID}")->row_array();
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $suppliertaxPercentage['vatPercentage'];
                        }
                    } else {
                        $data['taxAmount'] = ($data['totalAmount'] / 100) * $taxCat['taxPercentage'];
                    }
                    $data['isVAT'] = 1;
                    $data['taxCalculationformulaID'] = $text_type;
                    $this->db->where('purchaseOrderDetailsID', trim($this->input->post('purchaseOrderDetailsID') ?? ''));
                    $this->db->update('srp_erp_purchaseorderdetails', $data);



                    $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage');
                    $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $currency = $this->db->get()->row_array();
                    $purchaseOrderID =  trim($this->input->post('purchaseOrderID') ?? '');
                    $amount = $this->db->query("SELECT
                                            SUM(totalAmount) as totalAmount
                                        FROM `srp_erp_purchaseorderdetails`
                                        JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
                                        LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_purchaseorderdetails`.`taxCalculationformulaID` 
                                        WHERE
                                            `purchaseOrderID` = '{$purchaseOrderID}'
                                            GROUP BY
                                            purchaseOrderID")->row_array();


                    $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $amount['totalAmount']);
                    $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                    $taxtotal=($taxtotal_amount);




                    if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                        $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                        $taxtotal=($taxtotal_amount);
                        $this->update_po_generaltax($this->input->post('purchaseOrderID'),$taxtotal);
                    }
                }
            }

            
            
            $currentData = [
                'itemAutoID' => trim($this->input->post('itemAutoID') ?? ''),
                'unitOfMeasureID' => trim($this->input->post('UnitOfMeasureID') ?? ''),
                'requestedQty' => $this->input->post('quantityRequested'),
                // 'unitAmount' => (trim($this->input->post('estimatedAmount') ?? '') - trim($this->input->post('discount') ?? '')),
                'discountPercentage' => trim($this->input->post('discount') ?? ''),
                'discountAmount' => (trim($this->input->post('estimatedAmount') ?? '') / 100) * trim($this->input->post('discount') ?? ''),
                'taxCalculationformulaID' => $this->input->post('text_type'),
                'comment' => trim($this->input->post('comment') ?? '')
            ];
            var_dump( $currentData);
            var_dump( $poDetail);
            $isDifferent = false;
            foreach ($currentData as $key => $value) {
                if (isset($poDetail[$key]) && $poDetail[$key] != $value) {
                    $isDifferent = true;
                    break;
                }
            }
            
            if ($isDifferent) {
                
                $purchaseOrderID= $this->input->post('purchaseOrderID');
                $this->db->select('*'); 
                $this->db->from('srp_erp_purchaseordermaster');
                $this->db->where('purchaseOrderID', $purchaseOrderID);
                $this->db->where('confirmedYN', 2);
                $query = $this->db->get(); 
                   
                if ($query->num_rows() > 0) {
                    $dataMaster['valueChanged'] = 1; 
                    $this->db->where('purchaseOrderID',$purchaseOrderID); 
                    $this->db->update('srp_erp_purchaseordermaster', $dataMaster); 
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Purchase Order Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Purchase Order Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');

            }
        }
    }

    function conversionRateUOM($umo, $default_umo)
    {
        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $default_umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $masterUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $subUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('conversion');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->where('masterUnitID', $masterUnitID);
        $this->db->where('subUnitID', $subUnitID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row('conversion');
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function load_purchase_order_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordermaster');
        return $this->db->get()->row_array();
    }

    function fetch_itemrecode_po($categoryID = null)
    {      
        $category_filter = '';
        $default_item_filter = '';
        $isallow =$_GET['column'];
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";

        $category_policy = getPolicyValues('ECPO', 'All');
 
        if($category_policy==1){

            $masterID =$_GET['category'] ?? '';

            if($_GET['type']==1){ //PR
                $this->db->select('*');
                $this->db->where('purchaseRequestID', $masterID);
                $detail_master = $this->db->get('srp_erp_purchaserequestmaster')->row_array();
                $categoryID = $detail_master['itemCategoryID'] ?? 0;
            }

            if($_GET['type']==2){ //PO
                $this->db->select('*');
                $this->db->where('purchaseOrderID', $masterID);
                $detail_master_po = $this->db->get('srp_erp_purchaseordermaster')->row_array();
                $categoryID = $detail_master_po['itemCategoryID'];
            }
             
        } else {
             $categoryID = null;
        }

        if (true === isset($_GET['documentID']) && 'SRN' === $_GET['documentID'])
        {
            $categoryID = 2;
        }

         
        $default_item_filter = ' OR srp_erp_itemmaster.defaultYN = 1';
         
        if($categoryID  == 1){ 
            $category_filter = "AND mainCategory = 'Inventory'"; //Inventory
        } else if($categoryID  == 2){ 
            $category_filter = "AND mainCategory = 'Service'"; //Service
        }else  if($categoryID  == 3){ 
            $category_filter = "AND mainCategory = 'Fixed Assets'"; //Fixed Assets
        }else  if($categoryID  == 4){ 
            $category_filter = "AND mainCategory = 'Non Inventory'"; //Non Inventory
        } else{
            $category_filter;
        }

        $data = $this->db->query('SELECT
                                    srp_erp_itemmaster.mainCategory as mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.currentStock,companyLocalWacAmount,companyLocalSellingPrice,
                                    CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty"),"-", IFNULL(itemName,"empty")) AS "Match",
                                    isSubitemExist,
				    srp_erp_itemmaster.itemName,
                                    srp_erp_itemcategory.categoryTypeID,
                                    srp_erp_itemmaster.secondaryUOMID as secondaryUOMID,
                                    itemledgercurrent.currentstock as itemledgstock,
                                    companyLocalPurchasingPrice
                            FROM
                                srp_erp_itemmaster
                            LEFT JOIN srp_erp_itemcategory ON srp_erp_itemmaster.mainCategoryID = srp_erp_itemcategory.itemCategoryID
                            LEFT JOIN (SELECT
                                                IF (mainCategory = \'Inventory\', (TRIM(TRAILING "." FROM TRIM(TRAILING 0 FROM (ROUND(SUM(transactionQTY / convertionRate), 4))))), " ") AS currentstock, srp_erp_itemledger.itemAutoID
                                            FROM
                                                `srp_erp_itemledger`
                                            LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
                                            GROUP BY
                                                srp_erp_itemledger.itemAutoID
                                        )itemledgercurrent on itemledgercurrent.itemAutoID = srp_erp_itemmaster.itemAutoID
                            WHERE
                                ( itemSystemCode LIKE "' . $search_string . '" OR 
                                itemDescription LIKE "' . $search_string . '" OR 
				                itemName  LIKE "' . $search_string . '" OR 
                                seconeryItemCode LIKE "' . $search_string . '" OR 
                                barcode LIKE "' . $search_string . '" OR 
                                partNo LIKE "' . $search_string . '" OR 
                                itemName LIKE "' . $search_string . '" 
                                '.$default_item_filter.' ) 
                                AND srp_erp_itemmaster.companyCode = "' . $companyCode . '"
                                AND isActive = "1" AND '.$isallow.' = "1" 
                                AND masterApprovedYN = "1"
                                '.$category_filter.' ')->result_array();

         if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist'], 'revanueGLCode' => $val['revanueGLCode'], 'categoryTypeID' => $val['categoryTypeID'], 'mainCategory' => $val['mainCategory'], 'secondaryUOMID' => $val['secondaryUOMID'],'currentstockitemled' => $val['itemledgstock'],'companyLocalPurchasingPrice' => $val['companyLocalPurchasingPrice']);
         }

            }
         $dataArr2['suggestions'] = $dataArr;
         return $dataArr2;
    }

    function fetch_itemrecode()
    {
        $isallow =$_GET['column'];
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query('SELECT 
                                    mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,'.$item_code_alias.',costGLCode,assteGLCode,defaultUnitOfMeasure,
                                    defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,
                                    CONCAT(itemDescription, " - " ,srp_erp_itemmaster.itemSystemCode," - ",srp_erp_itemmaster.seconeryItemCode) AS "Match" 
                                FROM srp_erp_itemmaster 
                                WHERE 
                                    (itemSystemCode LIKE "' . $search_string . '" OR 
                                        itemDescription LIKE "' . $search_string . '" OR 
                                        seconeryItemCode LIKE "' . $search_string . '" OR 
                                        barcode LIKE "' . $search_string . '" OR 
                                        partNo LIKE "' . $search_string . '" OR 
                                        itemName LIKE "' . $search_string . '") 
                                    AND companyCode = "' . $companyCode . '"  
                                    AND isActive = "1" 
                                    AND '.$isallow.' = "1"  
                                    AND masterApprovedYN = "1" ')->result_array();
    }

    function fetch_po_detail_table()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 

        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage,purchaseOrderType,supplierID');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordermaster');
        $data['currency'] = $this->db->get()->row_array();
        $documentID = (($data['currency']['purchaseOrderType'] == 'PR') ? 'PO-PRQ':'PO');

        update_group_based_tax('srp_erp_purchaseordermaster','purchaseOrderAutoID',trim($this->input->post('purchaseOrderID') ?? ''),'srp_erp_purchaseordertaxdetails','purchaseOrderID',$documentID);

        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));

        $companyID = current_companyID();
        $purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');

        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $poAutoID = trim($this->input->post('purchaseOrderID') ?? '');

        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
     
        if($group_based_tax == 1){ 
            $this->db->select('srp_erp_purchaseorderdetails.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_purchaseorderdetails.`itemDescription` ), `srp_erp_purchaseorderdetails`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,IFNULL(srp_erp_taxcalculationformulamaster.Description ,\'-\') AS lineTaxDesc ,taxledger.taxDetailAutoID,srp_erp_purchaseordermaster.purchaseOrderType as purchaseRequestCode,'.$item_code.'');
            $this->db->where('srp_erp_purchaseorderdetails.purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID','left');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
            $this->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
            $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID', 'left');

            $this->db->join('(SELECT
                              documentDetailAutoID,
                              taxDetailAutoID
                              FROM
                              `srp_erp_taxledger`
                              where 
                              companyID = '.$companyID.' 
                              AND documentID = \'PO\'
                              AND documentMasterAutoID  = '.$purchaseOrderID.' 
                              GROUP BY
                              documentMasterAutoID,documentDetailAutoID)taxledger',' taxledger.documentDetailAutoID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID','left');
            
            $data['detail'] = $this->db->get()->result_array();
        }else {
            $this->db->query("UPDATE srp_erp_purchaseordermaster 
                                  SET rcmApplicableYN = 0
                                  WHERE
	                              purchaseOrderID ={$poAutoID} 
                                  AND companyID ={$companyID}");
            $data['isRcmDocument'] = 0;

            $this->db->select('srp_erp_purchaseorderdetails.*,	CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_purchaseorderdetails.`itemDescription` ), `srp_erp_purchaseorderdetails`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,IFNULL(IF(srp_erp_purchaseorderdetails.isVAT = 1, srp_erp_taxmaster.taxDescription,srp_erp_taxcalculationformulamaster.Description),\'-\') AS lineTaxDesc,srp_erp_purchaseordermaster.purchaseOrderType as purchaseRequestCode,'.$item_code.'');
            $this->db->where('srp_erp_purchaseorderdetails.purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID','left');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
            $this->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
            $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID', 'left');
            $data['detail'] = $this->db->get()->result_array();
        }

        $purchaseOrderID=$this->input->post('purchaseOrderID');
        $companyID=current_companyID();
        $data['tax_detail'] =$this->db->query("SELECT
                                    srp_erp_purchaseordertaxdetails.taxDescription,srp_erp_purchaseordertaxdetails.taxDetailAutoID,taxleg.amount,
                                    srp_erp_purchaseordertaxdetails.purchaseOrderAutoID
                                FROM
                                    srp_erp_purchaseordertaxdetails
                                INNER JOIN (
                                    SELECT
                                        SUM(amount) as amount,taxDetailAutoID
                                    FROM
                                        srp_erp_taxledger
                                    WHERE
                                        documentID = 'PO'
                                    AND documentMasterAutoID = $purchaseOrderID
                                GROUP BY documentMasterAutoID,taxDetailAutoID
                                ) taxleg ON srp_erp_purchaseordertaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                WHERE
                                    purchaseOrderAutoID = $purchaseOrderID
                                AND companyID = $companyID ")->result_array();

        $data['tax_detail_master'] = all_tax_formula_drop();

        $data['group_based_tax'] = $group_based_tax;

        if('LOG' === $data['currency']['purchaseOrderType']){
            $data['logistic_detail'] = $this->getPOLogisticDetail($poAutoID);
            $data['logistic_po'] = $this->getPOLogistic($data['currency']['supplierID']);
        }

        return $data;
    }

    function delete_purchase_order_detail()
    {
        $taxtotal = $this->input->post('taxtotal');
        $purchaseOrderID= $this->input->post('purchaseOrderID');
        $selected_amoutn =  $this->input->post('totalAmount');
        $group_based_tax =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');
        $companyID = current_companyID();
        $this->db->select('documentTaxType');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('totalAmount,generalDiscountAmount');
        $this->db->where('purchaseOrderDetailsID', $this->input->post('purchaseOrderDetailsID'));
        $detail = $this->db->get('srp_erp_purchaseorderdetails')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage,purchaseOrderType');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster');
        $currency = $this->db->get()->row_array();


        $amount = $this->db->query("SELECT
		SUM(totalAmount) as totalAmount
            FROM
                `srp_erp_purchaseorderdetails`
                JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
                LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_purchaseorderdetails`.`taxCalculationformulaID` 
            WHERE
                `purchaseOrderID` = '{$purchaseOrderID}'
                GROUP BY
                purchaseOrderID")->row_array();

        $totalamount_linewise = ($amount['totalAmount'] - $selected_amoutn);
       


        if($group_based_tax==1){ 
                if($documentTaxType['documentTaxType']==1){ 
                  
        
                        fetchExistsDetailTBL('PO',$this->input->post('purchaseOrderID'),$this->input->post('purchaseOrderDetailsID'),'srp_erp_purchaseordertaxdetails');
                        $this->db->delete('srp_erp_taxledger', array('documentDetailAutoID' => $this->input->post('purchaseOrderDetailsID'), 'documentMasterAutoID' => $this->input->post('purchaseOrderID'), 'documentID' => 'PO'));
                    
                  
                
              
                }else { 
              
                    $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $totalamount_linewise);
                    $taxtotal_amount = ($totalamount_linewise - $disc_foottotal);
                    $taxtotal=($taxtotal_amount);
                    tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$purchaseOrderID,$taxtotal,0,'PO');
                }
        } else {
            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $totalamount_linewise);
                $taxtotal_amount = ($totalamount_linewise - $disc_foottotal);
                $taxtotal=($taxtotal_amount);
               
                    $this->update_po_generaltax($purchaseOrderID,$taxtotal);
            }
            $this->db->delete('srp_erp_taxledger', array('documentDetailAutoID' => $this->input->post('purchaseOrderDetailsID'), 'documentMasterAutoID' => $this->input->post('purchaseOrderID'), 'documentID' => 'PO'));
            
        }
      
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderDetailsID' => trim($this->input->post('purchaseOrderDetailsID') ?? '')));

        $this->db->select('*'); 
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('confirmedYN', 2);
        $query = $this->db->get(); 
           
        if ($query->num_rows() > 0) {
            $dataMaster['valueChanged'] = 1; 
            $this->db->where('purchaseOrderID',$purchaseOrderID); 
            $this->db->update('srp_erp_purchaseordermaster', $dataMaster); 
        }
        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function delete_purchase_order()
    {
        $masterID = trim($this->input->post('purchaseOrderID') ?? '');

        $this->db->select('*');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', $masterID);
        $masterData = $this->db->get()->row_array();

        if ($masterData) {
            $this->session->set_flashdata('e', 'Purchase order not found');
            return true;
        }

        if('LOG' == $masterData['purchaseOrderType'])
        {
            $this->db->select('*');
            $this->db->from('srp_erp_purchase_order_logistic');
            $this->db->where('poMasterID', $masterID);
            $datas = $this->db->get()->row_array();
            if ($datas) {
                $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
                return true;
            }
        }else{
            $this->db->select('*');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $datas = $this->db->get()->row_array();
            if ($datas) {
                $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
                return true;
            }
        }


    /* Added */
        $documentCode = $this->db->get_where('srp_erp_purchaseordermaster', ['purchaseOrderID'=> $masterID])->row('purchaseOrderCode');
        $this->db->trans_start();

        $length = strlen($documentCode);
        if($length > 1){
          $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
          );
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->update('srp_erp_purchaseordermaster', $data);

        }
        else{
            $this->db->where('purchaseOrderAutoID', $masterID)->delete('srp_erp_purchaseordertaxdetails');
            $this->db->where('purchaseOrderID', $masterID)->delete('srp_erp_purchaseorderdetails');
            $this->db->where('purchaseOrderID', $masterID)->delete('srp_erp_purchaseordermaster');
            $this->db->where('poMasterID', $masterID)->delete('srp_erp_purchase_order_logistic');
        }
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }else{
            $this->session->set_flashdata('e', 'Error in delete process.');
           return false;
        }
        /* End */ 

    }

    function fetch_purchase_order_detail()
    {
        $this->db->select('srp_erp_purchaseorderdetails.*,srp_erp_itemmaster.seconeryItemCode,itemledgercurrent.currentstock AS itemledstock');
        $this->db->where('purchaseOrderDetailsID', trim($this->input->post('purchaseOrderDetailsID') ?? ''));
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID','left');
        $this->db->join("(SELECT IF (mainCategory = 'Inventory',  (SUM(transactionQTY/ convertionRate)),\" \") AS currentstock, srp_erp_itemledger.itemAutoID 
                            FROM `srp_erp_itemledger`
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
                            WHERE srp_erp_itemledger.itemAutoID is not null
                            GROUP BY srp_erp_itemledger.itemAutoID 
                          )itemledgercurrent","itemledgercurrent.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID ","left");
        
        return $this->db->get()->row_array();
    }

    function fetch_template_data($purchaseOrderID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $companyID=current_companyID();
        $this->db->select('createdUserID, confirmedByEmpID, confirmedbyName, purchaseOrderType,purchaseOrderID,versionNo,contactPersonName,contactPersonNumber,driverName,vehicleNo,creditPeriod,createdUserName,supplierID,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseOrderCode,shippingAddressDescription,
                    DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,referenceNumber,soldToAddressDescription,soldTocontactPersonID,soldTocontactPersonTelephone,supplierName,
                    supplierTelephone,supplierEmail,soldTocontactPersonEmail,supplierFax,soldTocontactPersonFaxNo,supplierAddress,invoiceToAddressDescription,shipTocontactPersonID,invoiceTocontactPersonID,
                    shipTocontactPersonTelephone,invoiceTocontactPersonTelephone,shipTocontactPersonFaxNo,invoiceTocontactPersonFaxNo,shipTocontactPersonEmail,invoiceTocontactPersonEmail,narration,
                    DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,paymentTerms,deliveryTerms,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,
                    approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,supplierCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode,penaltyTerms,generalDiscountAmount,generalDiscountPercentage,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,termsandconditions,documentTaxType,referenceNumber');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);


        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail, IFNULL(srp_erp_suppliermaster.vatidNo, " - ") as vatNumber, textIdentificationNo,srp_erp_company.companyVatNumber as companyvaNumber,srp_erp_suppliermaster.vatIdNo,textIdentificationNo');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_suppliermaster.companyID', 'Left');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        if('LOG' === $data['master']['purchaseOrderType']){
            $data['detail'] = $this->getPOLogisticDetail($purchaseOrderID);
        }
        else{
            $this->db->select('srp_erp_purchaseorderdetails.commision_value,taxledger.taxamountVat as taxamountVat,srp_erp_purchaseorderdetails.purchaseOrderDetailsID,srp_erp_purchaseorderdetails.purchaseOrderID,srp_erp_purchaseorderdetails.itemSystemCode,srp_erp_purchaseorderdetails.itemDescription,srp_erp_purchaseorderdetails.unitOfMeasure,srp_erp_purchaseorderdetails.requestedQty,srp_erp_purchaseorderdetails.unitAmount,srp_erp_purchaseorderdetails.discountAmount,srp_erp_purchaseorderdetails.comment,srp_erp_purchaseorderdetails.totalAmount,srp_erp_purchaseorderdetails.isClosedYN,srp_erp_purchaseorderdetails.discountPercentage,CONCAT_WS(\' - \',IF ( LENGTH( srp_erp_purchaseorderdetails.`comment` ), `srp_erp_purchaseorderdetails`.`comment`, NULL ),\' Part No : \',IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_purchaserequestmaster.purchaseRequestCode,srp_erp_taxcalculationformulamaster.Description as lineTaxDesc,taxAmount,srp_erp_purchaserequestmaster.purchaseRequestID,srp_erp_purchaserequestmaster.jobID,srp_erp_purchaserequestmaster.jobNumber,'.$item_code.'');
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID', 'left');
            $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
            $this->db->join("(SELECT
                SUM(
                IFNULL( amount, 0 )) AS taxamountVat,
                documentMasterAutoID,
                documentDetailAutoID 
            FROM
                srp_erp_taxledger
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
            WHERE
                documentID = 'PO' 
                AND taxCategory = 2 
            GROUP BY
                documentMasterAutoID,
                documentDetailAutoID)taxledger","taxledger.documentDetailAutoID =  srp_erp_purchaseorderdetails.purchaseOrderDetailsID","left");

            $data['detail'] = $this->db->get()->result_array();
        }


        $data['tax_detail'] =$this->db->query("SELECT
		 amount,taxDetailAutoID,srp_erp_taxmaster.taxDescription
            FROM
                srp_erp_taxledger
        LEFT JOIN srp_erp_taxmaster on srp_erp_taxledger.taxMasterID = srp_erp_taxmaster.taxMasterAutoID
            WHERE
                documentID = 'PO'
            AND documentMasterAutoID = $purchaseOrderID AND srp_erp_taxledger.companyID= $companyID ")->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseOrderID);
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();

        $this->db->where('purchaseOrderID',$purchaseOrderID);
        $this->db->from('srp_erp_purchaseorderextracharges');
        $data['extraCharges'] = $this->db->get()->result_array();

        $jobNumberStr = null;
        $total_estimate = 0;
        $estimate_arr = array();


        foreach($data['detail'] as $detail){
            $jobID = $detail['jobID'];

            if(!in_array($detail['jobNumber'],$estimate_arr)){
                $estimate_arr[] = $detail['jobNumber'];
            }

            $this->db->select('srp_erp_mfq_estimatemaster.*,srp_erp_mfq_job.bomMasterID');
            $this->db->where('workProcessID',$jobID);
            $this->db->join('srp_erp_mfq_estimatemaster','srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID','left');
            $estimated = $this->db->from('srp_erp_mfq_job')->get()->row_array();

            if($estimated){

                $this->load->model('MFQ_BillOfMaterial_model');

                $bomMasterID = $estimated['bomMasterID'];
                $_POST['bomMasterID'] = $bomMasterID;

                $results = $this->MFQ_BillOfMaterial_model->load_mfq_billOfMaterial_detail($bomMasterID);
                $grand_total = 0;

                foreach($results as $key => $section){

                    if($key == 'material'){
                        foreach($section as $material){
                            $grand_total += $material['materialCharge'];
                        }
                    }elseif($key == 'labour'){
                        foreach($section as $labour){
                            $grand_total += $labour['totalValue'];
                        }
                    }elseif($key == 'overhead'){
                        foreach($section as $overhead){
                            $grand_total += $overhead['totalValue'];
                        }
                    }elseif($key == 'third_party_service'){
                        foreach($section as $third_party_service){
                            $grand_total += $third_party_service['totalValue'];
                        }
                    }elseif($key == 'machine'){
                        foreach($section as $machine){
                            $grand_total += $machine['totalValue'];
                        }
                    }
                   
                }
    
               


              $total_estimate += $grand_total;
            }
           
        }

        $this->load->model('MFQ_Job_model');
        // $_POST['']

        $data['jobNumberStr'] = join(',',$estimate_arr);
        $data['estimate'] =  $total_estimate;

        return $data;
    }

    function fetch_template_data_version($purchaseOrderID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $companyID=current_companyID();
        $this->db->select('createdUserID, confirmedByEmpID, confirmedbyName, purchaseOrderType,purchaseOrderID,versionNo,contactPersonName,contactPersonNumber,driverName,vehicleNo,creditPeriod,createdUserName,supplierID,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseOrderCode,shippingAddressDescription,
                    DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,referenceNumber,soldToAddressDescription,soldTocontactPersonID,soldTocontactPersonTelephone,supplierName,
                    supplierTelephone,supplierEmail,soldTocontactPersonEmail,supplierFax,soldTocontactPersonFaxNo,supplierAddress,invoiceToAddressDescription,shipTocontactPersonID,invoiceTocontactPersonID,
                    shipTocontactPersonTelephone,invoiceTocontactPersonTelephone,shipTocontactPersonFaxNo,invoiceTocontactPersonFaxNo,shipTocontactPersonEmail,invoiceTocontactPersonEmail,narration,
                    DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,paymentTerms,deliveryTerms,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,
                    approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,supplierCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode,penaltyTerms,generalDiscountAmount,generalDiscountPercentage,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,termsandconditions,documentTaxType');
        $this->db->where('versionAutoID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster_version');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail, IFNULL(srp_erp_suppliermaster.vatidNo, " - ") as vatNumber, textIdentificationNo,srp_erp_company.companyVatNumber as companyvaNumber');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_suppliermaster.companyID', 'Left');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_purchaseorderdetails_version.commision_value,taxledger.taxamountVat as taxamountVat,srp_erp_purchaseorderdetails_version.purchaseOrderDetailsID,srp_erp_purchaseorderdetails_version.purchaseOrderID,srp_erp_purchaseorderdetails_version.itemSystemCode,srp_erp_purchaseorderdetails_version.itemDescription,srp_erp_purchaseorderdetails_version.unitOfMeasure,srp_erp_purchaseorderdetails_version.requestedQty,srp_erp_purchaseorderdetails_version.unitAmount,srp_erp_purchaseorderdetails_version.discountAmount,srp_erp_purchaseorderdetails_version.comment,srp_erp_purchaseorderdetails_version.totalAmount,srp_erp_purchaseorderdetails_version.isClosedYN,srp_erp_purchaseorderdetails_version.discountPercentage,CONCAT_WS(\' - \',IF ( LENGTH( srp_erp_purchaseorderdetails_version.`comment` ), `srp_erp_purchaseorderdetails_version`.`comment`, NULL ),\' Part No : \',IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_purchaserequestmaster.purchaseRequestCode,srp_erp_taxcalculationformulamaster.Description as lineTaxDesc,taxAmount,srp_erp_purchaserequestmaster.purchaseRequestID,'.$item_code.'');
        $this->db->where('versionMasterID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseorderdetails_version');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails_version.itemAutoID', 'left');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails_version.prMasterID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails_version.taxCalculationformulaID','left');
        $this->db->join("(SELECT
                SUM(
                IFNULL( amount, 0 )) AS taxamountVat,
                documentMasterAutoID,
                documentDetailAutoID 
            FROM
                srp_erp_taxledger
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
            WHERE
                documentID = 'PO' 
                AND taxCategory = 2 
            GROUP BY
                documentMasterAutoID,
                documentDetailAutoID)taxledger","taxledger.documentDetailAutoID =  srp_erp_purchaseorderdetails_version.purchaseOrderDetailsID","left");

        $data['detail'] = $this->db->get()->result_array();
      
        $data['tax_detail'] =$this->db->query("SELECT
		 amount,taxDetailAutoID,srp_erp_taxmaster.taxDescription
            FROM
                srp_erp_taxledger
        LEFT JOIN srp_erp_taxmaster on srp_erp_taxledger.taxMasterID = srp_erp_taxmaster.taxMasterAutoID
            WHERE
                documentID = 'PO'
            AND documentMasterAutoID = $purchaseOrderID AND srp_erp_taxledger.companyID= $companyID ")->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseOrderID);
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();

        $this->db->where('purchaseOrderID',$purchaseOrderID);
        $this->db->from('srp_erp_purchaseorderextracharges');
        $data['extraCharges'] = $this->db->get()->result_array();

        // print_r($data['extraCharges']); exit;

        return $data;
    }

    function fetch_po_template_data_for_supplier_portal($purchaseOrderID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $companyID=current_companyID();
        $this->db->select('createdUserID, confirmedByEmpID, confirmedbyName, purchaseOrderType,companyID,purchaseOrderID,driverName,vehicleNo,creditPeriod,createdUserName,supplierID,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseOrderCode,shippingAddressDescription,
                    DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,referenceNumber,soldToAddressDescription,soldTocontactPersonID,soldTocontactPersonTelephone,supplierName,
                    supplierTelephone,supplierEmail,soldTocontactPersonEmail,supplierFax,soldTocontactPersonFaxNo,supplierAddress,invoiceToAddressDescription,shipTocontactPersonID,invoiceTocontactPersonID,
                    shipTocontactPersonTelephone,invoiceTocontactPersonTelephone,shipTocontactPersonFaxNo,invoiceTocontactPersonFaxNo,shipTocontactPersonEmail,invoiceTocontactPersonEmail,narration,
                    DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,paymentTerms,deliveryTerms,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,
                    approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,supplierCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode,penaltyTerms,generalDiscountAmount,generalDiscountPercentage,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,termsandconditions,documentTaxType');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail, IFNULL(srp_erp_suppliermaster.vatidNo, " - ") as vatNumber, textIdentificationNo,srp_erp_company.companyVatNumber as companyvaNumber');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_suppliermaster.companyID', 'Left');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('taxledger.taxamountVat as taxamountVat,srp_erp_purchaseorderdetails.purchaseOrderDetailsID,srp_erp_purchaseorderdetails.purchaseOrderID,srp_erp_purchaseorderdetails.itemSystemCode,srp_erp_purchaseorderdetails.itemDescription,srp_erp_purchaseorderdetails.unitOfMeasure,srp_erp_purchaseorderdetails.requestedQty,srp_erp_purchaseorderdetails.unitAmount,srp_erp_purchaseorderdetails.discountAmount,srp_erp_purchaseorderdetails.comment,srp_erp_purchaseorderdetails.totalAmount,srp_erp_purchaseorderdetails.discountPercentage,CONCAT_WS(\' - \',IF ( LENGTH( srp_erp_purchaseorderdetails.`comment` ), `srp_erp_purchaseorderdetails`.`comment`, NULL ),\' Part No : \',IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_purchaserequestmaster.purchaseRequestCode,srp_erp_taxcalculationformulamaster.Description as lineTaxDesc,taxAmount,srp_erp_purchaserequestmaster.purchaseRequestID,'.$item_code.'');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID','left');
        $this->db->join("(SELECT
	SUM(
	IFNULL( amount, 0 )) AS taxamountVat,
	documentMasterAutoID,
	documentDetailAutoID 
FROM
	srp_erp_taxledger
	LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
WHERE
	documentID = 'PO' 
	AND taxCategory = 2 
GROUP BY
	documentMasterAutoID,
	documentDetailAutoID)taxledger","taxledger.documentDetailAutoID =  srp_erp_purchaseorderdetails.purchaseOrderDetailsID","left");

        $data['detail'] = $this->db->get()->result_array();
      
      $data['tax_detail'] =$this->db->query("SELECT
		 amount,taxDetailAutoID,srp_erp_taxmaster.taxDescription
	FROM
		srp_erp_taxledger
LEFT JOIN srp_erp_taxmaster on srp_erp_taxledger.taxMasterID = srp_erp_taxmaster.taxMasterAutoID
	WHERE
		documentID = 'PO'
	AND documentMasterAutoID = $purchaseOrderID AND srp_erp_taxledger.companyID= $companyID ")->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseOrderID);
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function purchase_order_confirmation()
    {
        $companyID = current_companyID();
        $documentID = "PO";
        $purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');
        $amountBasedApproval = getPolicyValues('ABA', 'All');
        $approvalType = getApprovalTypesONDocumentCode($documentID, $companyID);

        $poCheck=getPolicyValues('PAD', 'All');
        $enableCategoryPO = getPolicyValues('ECPO', 'All');
        if($poCheck==1){
            
            $this->db->select('*'); 
            $this->db->from('srp_erp_purchaseordermaster');
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $this->db->where('confirmedYN', 2);
            $this->db->where('valueChanged', 1);
            $query = $this->db->get(); 
            
            if ($query->num_rows() > 0) {
                
                $this->db->where('documentSystemCode', $purchaseOrderID);
                $this->db->delete('srp_erp_documentapproved');
    
                $masterConfirmedYN['currentLevelNo']=1;
                $this->db->where('purchaseOrderID', $purchaseOrderID);
                $this->db->update('srp_erp_purchaseordermaster', $masterConfirmedYN);
            }
        }

        $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                FROM srp_erp_purchaseordermaster
                    LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                    LEFT JOIN (
                            SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                            FROM srp_erp_taxledger 
                            WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                            GROUP BY documentMasterAutoID 
                    ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                WHERE
                    srp_erp_purchaseordermaster.purchaseOrderID = {$purchaseOrderID} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();

        $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];

        $approval_type_data = $this->db->query("SELECT segmentID,itemCategoryID FROM srp_erp_purchaseordermaster where purchaseOrderID = $purchaseOrderID AND companyID = {$companyID}")->row_array();

        if($approvalType['approvalType'] == 2) {
            $amountApprovable = amount_based_approval_setup($documentID, $poLocalAmount);

            if($amountApprovable['type'] == 'e') {
                $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO Value');
                return array(false, 'error');
            }
        }

        if($approvalType['approvalType'] == 3) {

           $segment_based_approval = segment_based_approval($documentID, $approval_type_data['segmentID']);

            if($segment_based_approval['type'] == 'e') {
                $this->session->set_flashdata('w', 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this PO Value');
                return array(false, 'error');
            }
        }

        if($approvalType['approvalType'] == 4) {
            $amount_base_segment_based_approval = amount_base_segment_based_approval($documentID, $poLocalAmount, $approval_type_data['segmentID']);

            if($amount_base_segment_based_approval['type'] == 'e') {
                $this->session->set_flashdata('w', 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this PO Value');
                return array(false, 'error');
            }
        }
        if($approvalType['approvalType'] == 5) {

            if($enableCategoryPO==1){
                $amountApprovable = category_based_approval_setup($documentID, $poLocalAmount,$approval_type_data['itemCategoryID']);

                if($amountApprovable['type'] == 'e') {
                    $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO category');
                    return array(false, 'error');
                }
            }else{
                $this->session->set_flashdata('w', 'Please enable Category Policy on PO');
                return array(false, 'error');
            }
        }

        $this->db->select('*,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordermaster');
        $masterRecord = $this->db->get()->row_array();

        if(!$masterRecord){
            $this->session->set_flashdata('w', 'Purchase order not found');
            return array(false, 'error');
        }

        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $currentuser = current_userID();

        if ('LOG' === $masterRecord['purchaseOrderType']) {
            $this->db->select('*');
            $this->db->where('poMasterID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->from('srp_erp_purchase_order_logistic');
            $detail = $this->db->get()->result_array();
            if (empty($detail)) {
                $this->session->set_flashdata('w', 'There are no records to confirm this document!');
                return array(false, 'error');
            }

            $approvals_status = $this->generateApproval(
                $masterRecord,
                $locationwisecodegenerate,
                $currentuser,
                $companyID,
                $locationemployee,
                $approval_type_data,
                $poLocalAmount
            );

            if ($approvals_status == 1) {
                return array(true, 'Success');
            } else {
                return array(false, 'error');
            }
        }

        $this->db->select('*');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseorderdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return array(false, 'error');
        } else {
            if (1 == $masterRecord['confirmedYN']) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return array(false, 'error');
            } else {

                $budegtControl = getPolicyValues('BDC', 'All');
                $bdcval = 0;
                $inventoryparr = array();
                $noninventoryparr = array();
                if ($budegtControl == 1) {
                    $this->db->select('documentDate,segmentID,companyReportingExchangeRate,companyReportingCurrencyDecimalPlaces');
                    $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $mastr = $this->db->get()->row_array();
                    foreach ($record as $rec) {
                        $item_arr = fetch_item_data($rec['itemAutoID']);
                        if ($item_arr['mainCategory'] == 'Inventory') {
                            $itemAutoID = $rec['itemAutoID'];

                            $itemlegqty = $this->db->query("SELECT IFNULL(SUM(transactionQTY/convertionRate),0) as qty FROM srp_erp_itemledger WHERE itemAutoID=$itemAutoID AND companyID=$companyID")->row_array();
                            $totqty = $itemlegqty['qty'] + $rec['requestedQty'];
                            if($item_arr['maximunQty']>0){
                                if ($totqty > $item_arr['maximunQty']) {
                                    array_push($noninventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "consumption" => $totqty, "budgetamount" => $item_arr['maximunQty']));
                                    $bdcval++;
                                }
                            }
                        }
                    }
                    $purchaseOrderID=$this->input->post('purchaseOrderID');
                    $records = $this->db->query("SELECT srp_erp_purchaseorderdetails.itemAutoID,SUM(totalAmount) AS totalAmount FROM srp_erp_purchaseorderdetails LEFT JOIN srp_erp_itemmaster ON srp_erp_purchaseorderdetails.itemAutoID = srp_erp_itemmaster.itemAutoID WHERE purchaseOrderID=$purchaseOrderID GROUP BY srp_erp_itemmaster.costGLAutoID")->result_array();
                    foreach ($records as $rec) {
                        $item_arr = fetch_item_data($rec['itemAutoID']);

                        if ($item_arr['mainCategory'] == 'Service' || $item_arr['mainCategory'] == 'Non Inventory') {
                            $costGLAutoID = $item_arr['costGLAutoID'];

                            $sgmnt = $mastr['segmentID'];
                            $docdt = $mastr['documentDate'];

                            //get finance year details using PO document Date
                            $financeyr = $this->db->query("SELECT companyFinanceYearID,beginingDate,endingDate FROM srp_erp_companyfinanceyear WHERE '$docdt' BETWEEN beginingDate and endingDate AND companyID=$companyID")->row_array();
                            $finYear = $financeyr['companyFinanceYearID'];
                            $beginingDate=$financeyr['beginingDate'];
                            $endingDate=$financeyr['endingDate'];

                            //get consumption amount
                            $cousumtnamnt = $this->db->query('SELECT SUM(companyReportingAmount) AS rptamnt FROM srp_erp_generalledger WHERE   companyID="' . $companyID . '" AND GLAutoID="' . $costGLAutoID . '" AND  segmentID="' . $sgmnt . '" AND documentDate BETWEEN "' . $beginingDate . '" AND "' . $endingDate . '" ')->row_array();
                            $consumtionAmount=($rec['totalAmount'] / $mastr['companyReportingExchangeRate']);
                            if(!empty($cousumtnamnt)){
                                $consumtionAmount=$cousumtnamnt['rptamnt']+ ($rec['totalAmount'] / $mastr['companyReportingExchangeRate']);
                            }

                            $bgtamnt = $this->db->query("SELECT SUM(IFNULL(srp_erp_budgetdetail.companyReportingAmount, 0)) AS amount FROM `srp_erp_budgetdetail` LEFT JOIN srp_erp_budgetmaster ON srp_erp_budgetmaster.budgetAutoID =  srp_erp_budgetdetail.budgetAutoID WHERE GLAutoID = $costGLAutoID AND srp_erp_budgetdetail.segmentID = $sgmnt AND companyFinanceYearID = $finYear AND approvedYN = 1 AND srp_erp_budgetdetail.companyID=$companyID")->row_array();

                            if (!empty($bgtamnt['amount'])) {
                                $budgetamount = $bgtamnt['amount']*-1;

                                if ($budgetamount == '') {
                                    $budgetamount = 0;
                                }
                                if ($consumtionAmount > $budgetamount) {
                                    $exceeded=$consumtionAmount-$budgetamount;
                                    $glcod=fetch_gl_account_desc($costGLAutoID);
                                    array_push($inventoryparr, array( "consumption" => round($consumtionAmount,$mastr['companyReportingCurrencyDecimalPlaces']), "glCode" => $glcod['GLSecondaryCode'].'-'.$glcod['GLDescription'], "budgetamount" => round($budgetamount,$mastr['companyReportingCurrencyDecimalPlaces']), "exceededamnt" => round($exceeded,$mastr['companyReportingCurrencyDecimalPlaces'])));
                                    $bdcval++;
                                }
                            }
                        }
                    }
                }
                if ($bdcval == 0) {

                    $approvals_status = $this->generateApproval(
                        $masterRecord,
                        $locationwisecodegenerate,
                        $currentuser,
                        $companyID,
                        $locationemployee,
                        $approval_type_data,
                        $poLocalAmount
                    );

                    if ($approvals_status == 1) {
                        return array(true, 'Success');
                    } else {
                        return array(false, 'error');
                    }
                } else {
                    return array("exceeded", 'error', $inventoryparr, $noninventoryparr);
                }
            }
        }
    }

    function save_purchase_order_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $companyID = current_companyID();
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('purchaseOrderID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('po_status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['purchaseOrderID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $amountBasedApproval = getPolicyValues('ABA', 'All');
        $approvalType = getApprovalTypesONDocumentCode('PO', $companyID);
        //if($amountBasedApproval == 1) {
            $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency, ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaseordermaster
                        LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                    WHERE
                        srp_erp_purchaseordermaster.purchaseOrderID = {$system_code} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();

//            $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
//            $conversion = currency_conversionID($documentTotal['transactionCurrencyID'], $defaultCurrencyID,  $documentTotal['total_value']);
            $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];
            // $amountApprovable = amount_based_approval('PO', $poLocalAmount, $level_id);
            // if($amountApprovable['type'] == 'e') {
            //     $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO Value');
            //     return false;
            // }

            $approval_type_data = $this->db->query("SELECT segmentID FROM srp_erp_purchaseordermaster where purchaseOrderID = $system_code AND companyID = {$companyID}")->row_array();

            if($approvalType['approvalType'] == 2) {
                $amountApprovable = amount_based_approval_setup('PO', $poLocalAmount);
                
                if($amountApprovable['type'] == 'e') {
                    $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO Value');
                    return array(false, 'error');exit;
                }
            }
            if($approvalType['approvalType'] == 3) {
              
               $segment_based_approval = segment_based_approval('PO', $approval_type_data['segmentID']);
              

                if($segment_based_approval['type'] == 'e') {
                    $this->session->set_flashdata('w', 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this PO Value');
                    return array(false, 'error');exit;
                }
            }
            if($approvalType['approvalType'] == 4) {
              
                $amount_base_segment_based_approval = amount_base_segment_based_approval('PO', $poLocalAmount, $approval_type_data['segmentID']);

                if($amount_base_segment_based_approval['type'] == 'e') {
                    $this->session->set_flashdata('w', 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this PO Value');
                    return array(false, 'error');exit;
                }
            }


       // }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PO');
        }
        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            //update last approval date as po date
            $updatelastapprovaldatePolicy = getPolicyValues('PADT', 'All');
            if($updatelastapprovaldatePolicy==1){
                $date_format_policy = date_format_policy();
                $current_date_new = date('Y-m-d');
                $format_last_approval_date = input_format_date($current_date_new, $date_format_policy);
                $data['documentDate'] = $format_last_approval_date;
            }

            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->update('srp_erp_purchaseordermaster', $data);

            $this->db->select('*');
            $this->db->from('srp_erp_purchaseordermaster');
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $po_all= $this->db->get()->row_array();

            if($po_all){
                if($po_all['isSrmCreated']==1){
                    // $p_auto =$this->input->post('purchaseOrderID');
                    // $res_po = send_approve_po_srm_portal($p_auto,1);  // 1-po ,2-grv
                }else{

                    $this->db->select('*');
                    $this->db->from('srp_erp_suppliermaster');
                    $this->db->where('supplierAutoID', $po_all['supplierID']);
                    $this->db->where('isSrmGenerated', 1);
                    $supplier_srm_created= $this->db->get()->row_array();

                    if($supplier_srm_created){

                        $this->db->select('*');
                        $this->db->from('srp_erp_srm_suppliermaster');
                        $this->db->where('erpSupplierAutoID', $po_all['supplierID']);
                        $supplier_srm_master= $this->db->get()->row_array();

                        $po_id =trim($this->input->post('purchaseOrderID') ?? '');
                        $data_po_master['master'] = $po_all;

                        $data_po_master['master']['srmSupplierID'] =  $supplier_srm_master['supplierAutoID'];
                        $data_po_master['master']['company_name'] = $this->common_data['company_data']['company_name'];
                        $data_po_master['master']['company_address1'] = $this->common_data['company_data']['company_address1'];
                        $data_po_master['master']['company_address2'] = $this->common_data['company_data']['company_address2'];
                        $data_po_master['master']['company_city'] = $this->common_data['company_data']['company_city'];
                        $data_po_master['master']['company_province'] = $this->common_data['company_data']['company_province'];
                        $data_po_master['master']['company_country'] = $this->common_data['company_data']['company_country'];
                        $data_po_master['master']['company_code'] = $this->common_data['company_data']['company_code']; 
                        $data_po_master['master']['inquiryID'] = 0;
                        $data_po_master['master']['purchaseOrderIDBackend'] = $po_id;

                        $master_n = [
                            "inquiryID"=> 0,
                            "supplierID"=>$supplier_srm_master['supplierAutoID'],
                            "companyID"=>$this->common_data['company_data']['company_id'],
                        ]; 

                        $data_po_master['rfq'] = $master_n;

                        $this->db->select('*');
                        $this->db->from('srp_erp_purchaseorderdetails');
                        $this->db->where('purchaseOrderID', $po_id);
                        $data_po_master['details'] = $this->db->get()->result_array();

                        foreach($data_po_master['details'] as $key1=>$val_data){
                            $data_po_master['details'][$key1]['erpBackendID'] = $val_data['purchaseOrderDetailsID'];
                            $data_po_master['details'][$key1]['erpBackendMasterID'] = $po_id;
                        }

                        $this->db->select('*');
                        $this->db->from('srp_erp_documentattachments');
                        $this->db->where('documentSystemCode', $po_id);
                        $data_po_master['document'] = $this->db->get()->row_array();

                        $token = getLoginToken();
                            
                        $token_array=json_decode($token);
        
                        if($token_array){
        
                            if($token_array->success==true){
                            
                                $res= srmCommonApiCall($data_po_master,null,$token_array->data->token,'/Api_ecommerce/save_supplier_po');
        
                                $res_array=json_decode($res);
        
                            }
                        }

                    }
                }
            }

            $this->session->set_flashdata('s', 'Document Approved Successfully.');
        } else {
            $this->session->set_flashdata('s', 'Approval Rejected Successfully.');
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

    function save_purchase_order_close()
    {
        $this->db->trans_start();
        $system_code = trim($this->input->post('purchaseOrderID') ?? '');
        $date_format_policy = date_format_policy();
        $docdate = $this->input->post('closedDate');
        $closeddate = input_format_date($docdate, $date_format_policy);

        $this->db->select('*');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $po_all= $this->db->get()->row_array();
        if($po_all){
            if($po_all['isCloseRequestYN']==1){
                $this->session->set_flashdata('w', 'You Have Pending Purchase Order Closed Request');
                return false;
                exit();
            }else{

                    $data['closedYN'] = 1;
                    $data['closedDate'] = $closeddate;
                    $data['closedBy'] = $this->common_data['current_user'];
                    $data['closedReason'] = trim($this->input->post('comments') ?? '');
                    $data['approvedYN'] = 5;
                    $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $data['approvedbyEmpName'] = $this->common_data['current_user'];
                    $data['approvedDate'] = $this->common_data['current_date'];
        
                    $this->db->select('*');
                    $this->db->from('srp_erp_suppliermaster');
                    $this->db->where('supplierAutoID', $po_all['supplierID']);
                    $this->db->where('isSrmGenerated', 1);
                    $supplier_srm_created= $this->db->get()->row_array();
        
                    if($supplier_srm_created){

                        if($po_all['isPortalPOSubmitted']==2){
                            $res_po = send_approve_po_srm_portal($system_code,4,$data);  // 1-po ,2-grv,4-po close
        
                            if($res_po==true){
                                $data_close_supplier['isCloseRequestYN'] =1;
                                $data_close_supplier['closedDate'] = $closeddate;
                                $data_close_supplier['closedBy'] = $this->common_data['current_user'];
                                $data_close_supplier['closedReason'] = trim($this->input->post('comments') ?? '');
            
                                $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                                $this->db->update('srp_erp_purchaseordermaster', $data_close_supplier);
                                
                                $this->session->set_flashdata('s', 'Close Request Send Successfully.');
                                //return array('s', 'Close Request Send Successfull');
                            }else{
                                $this->session->set_flashdata('w', 'Try again');
                                //return array('e', 'try again');
                            }
                        }else{
                            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                            $this->db->update('srp_erp_purchaseordermaster', $data);
                            $this->session->set_flashdata('s', 'Purchase Order Closed Successfully.');

                        }
        
                    }else{
        
                        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                        $this->db->update('srp_erp_purchaseordermaster', $data);
                        $this->session->set_flashdata('s', 'Purchase Order Closed Successfully.');
        
                    }
            }
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

    function fetch_convertion_detail_table()
    {
        $this->db->select('subUnitID,conversion,s.UnitShortCode as sub_code,s.UnitDes as sub_dese,m.UnitShortCode as m_code,m.UnitDes as m_dese');
        $this->db->where('masterUnitID', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_unitsconversion.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unitsconversion');
        $this->db->join('srp_erp_unit_of_measure s', 's.UnitID = srp_erp_unitsconversion.subUnitID');
        $this->db->join('srp_erp_unit_of_measure m', 'm.UnitID = srp_erp_unitsconversion.masterUnitID');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('UnitID,UnitShortCode,UnitDes');
        $this->db->where('UnitID !=', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_unit_of_measure.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unit_of_measure');
        //$this->db->join('srp_erp_unitsconversion', 'srp_erp_unitsconversion.subUnitID != srp_erp_unit_of_measure.UnitID','inner');
        $data['drop'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function fetch_supplier_currency()
    {
        $this->db->select('supplierCurrency');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_supplier_currency_by_id()
    {
        $this->db->select('supplierCurrencyID,supplierCreditPeriod,paymentTerms');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_customer_currency()
    {
        $this->db->select('customerCurrency');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        return $this->db->get()->row_array();
    }


    function delete_purchaseOrder_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function re_open_procurement()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->update('srp_erp_purchaseordermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_prq_code()
    {
        //transaction currency ID filter Removed (Task No SME-2443)
        //srp_erp_purchaserequestmaster.transactionCurrencyID = '$transactionCurrencyID'
        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $itemSearch = $this->input->post('itemSearch');
        $itemAutoID = $this->input->post('itemAutoID');

        $enableCatPo = getPolicyValues('ECPO', 'All'); 
        $this->db->select('documentDate,transactionCurrencyID,itemCategoryID');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', trim($purchaseOrderID));
        $result = $this->db->get()->row_array();

        $documentDate = $result['documentDate'];
        $transactionCurrencyID = $result['transactionCurrencyID'];
        $companyID = current_companyID();

        $itemCatID =null;
        $itemCat_filter ='';
        $item_filter ='';

        if( $enableCatPo==1){
            $itemCatID = $result['itemCategoryID'];

            $itemCat_filter ="AND srp_erp_purchaserequestmaster.itemCategoryID ='$itemCatID'";
        }

        if($itemSearch == 'true'){
            $item_Str = join(",",$itemAutoID);
            $item_filter = "AND prqd.itemAutoID IN ({$item_Str})";
        }

        $data = $this->db->query("SELECT
            srp_erp_purchaserequestmaster.purchaseRequestID,
            srp_erp_purchaserequestmaster.purchaseRequestCode,
            srp_erp_purchaserequestmaster.documentDate,
            srp_erp_purchaserequestmaster.requestedByName,	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( prqdetailpv.prQtypv ), 0 ) + IFNULL( SUM( prqdetailpo.prQtypo ), 0 )), 2 )))))) AS prQty,	
        /*IFNULL(SUM(prqdetailpv.prQtypv),0) + IFNULL(SUM(prqdetailpo.prQtypo),0) AS prQty,*/
        SUM(prqd.requestedQty) as requestedQty
        FROM
            srp_erp_purchaserequestdetails prqd
        LEFT JOIN srp_erp_purchaserequestmaster ON prqd.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID
        LEFT JOIN (
            SELECT
                prDetailID AS purchaseRequestID,
                IFNULL(
                    sum(
                        srp_erp_paymentvoucherdetail.requestedQty
                    ),
                    0
                ) AS prQtypv
            FROM
                srp_erp_paymentvoucherdetail
            GROUP BY
                prDetailID
        ) prqdetailpv ON prqdetailpv.purchaseRequestID = prqd.purchaseRequestDetailsID
        LEFT JOIN (
            SELECT
                prDetailID AS purchaseRequestID,
                IFNULL(
                    sum(
                        srp_erp_purchaseorderdetails.requestedQty
                    ),
                    0
                ) AS prQtypo
            FROM
                srp_erp_purchaseorderdetails
            GROUP BY
                prDetailID
        ) prqdetailpo ON prqdetailpo.purchaseRequestID = prqd.purchaseRequestDetailsID
        WHERE
        srp_erp_purchaserequestmaster.companyID = '$companyID'
        AND srp_erp_purchaserequestmaster.approvedYN = 1 AND closedYN != 1 {$itemCat_filter} {$item_filter}
        GROUP BY
            prqd.purchaseRequestID")->result_array();

        return $data;
        /*documentDate <= '$documentDate'
AND*/
        /*$this->db->select('purchaseRequestID,purchaseRequestCode,documentDate,requestedByName');
        $this->db->from('srp_erp_purchaserequestmaster');
        $this->db->where('documentDate <=', trim($result['documentDate'] ?? ''));
        $this->db->where('transactionCurrencyID', trim($result['transactionCurrencyID'] ?? ''));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->where('approvedYN', 1);
        return $this->db->get()->result_array();*/
    }

    function fetch_prq_detail_table()
    {
        /*$this->db->select('srp_erp_purchaserequestdetails.*,ifnull(sum(srp_erp_purchaseorderdetails.requestedQty),0)+ifnull(sum(srp_erp_paymentvoucherdetail.requestedQty),0) AS prQty');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->join('srp_erp_purchaseorderdetails', 'srp_erp_purchaseorderdetails.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->group_by("purchaseRequestDetailsID");
        $data['detail'] = $this->db->get()->result_array();*/

        $itemSearch = $this->input->post('itemSearch');
        $itemAutoID = $this->input->post('itemAutoID');

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        } 
        $companyID=current_companyID();
        $purchaseRequestID= $this->input->post('purchaseRequestID');
        $purchaseOrderID= $this->input->post('purchaseOrderID');
        $item_filter = '';
        if($itemSearch == 'true'){
            $item_str = join(",",$itemAutoID);
            $item_filter = "AND prqd.itemAutoID IN ({$item_str})";
        }

        $data['detail'] = $this->db->query("SELECT
                                prqd.*, /*IFNULL(prqdetailpv.prQtypv,0) + IFNULL(prqdetailpo.prQtypo,0) AS prQty*/
                                ((TRIM( ROUND( IFNULL( requestedQty , 0 ), 4 ) ) + 0 ) - ((TRIM( ROUND( IFNULL(  prqdetailpv.prQtypv , 0 ), 4 ) ) + 0 ) + (TRIM( ROUND( IFNULL(  prqdetailpo.prQtypo , 0 ), 4 ) ) + 0) ))  as balQty,srp_erp_purchaserequestmaster.purchaseRequestCode,
                                $item_code
                                    
                            FROM
                                srp_erp_purchaserequestdetails prqd
                            LEFT JOIN srp_erp_purchaserequestmaster ON prqd.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = prqd.itemAutoID
                            LEFT JOIN (
                                SELECT
                                    prDetailID AS purchaseRequestID,
                                    IFNULL(
                                        sum(
                                            srp_erp_paymentvoucherdetail.requestedQty
                                        ),
                                        0
                                    ) AS prQtypv
                                FROM
                                    srp_erp_paymentvoucherdetail
                                WHERE
                                    prMasterID = '$purchaseRequestID'
                                GROUP BY
                                    prDetailID
                            ) prqdetailpv ON prqdetailpv.purchaseRequestID = prqd.purchaseRequestDetailsID
                            LEFT JOIN (
                                SELECT
                                    prDetailID AS purchaseRequestID,
                                    IFNULL(
                                        sum(
                                            srp_erp_purchaseorderdetails.requestedQty
                                        ),
                                        0
                                    ) AS prQtypo
                                FROM
                                    srp_erp_purchaseorderdetails
                                WHERE
                                    prMasterID = '$purchaseRequestID'
                                GROUP BY
                                    prDetailID
                            ) prqdetailpo ON prqdetailpo.purchaseRequestID = prqd.purchaseRequestDetailsID
                            WHERE
                                prqd.purchaseRequestID = '$purchaseRequestID' AND prqd.isClosedYN = 0
                            AND srp_erp_purchaserequestmaster.approvedYN = 1 {$item_filter}
                            GROUP BY
                                prqd.purchaseRequestDetailsID")->result_array();

        // print_r($item_filter); exit;
        
        $vateligible = $this->db->query("SELECT IFNULL(vatEligible, 0) as vatEligible 
                                FROM srp_erp_purchaseordermaster 
                                JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID
                                WHERE purchaseOrderID = {$purchaseOrderID} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row('vatEligible');
        $str = '';
        if($vateligible == 2) {
            $str = "UNION ALL SELECT taxMasterAutoID, taxDescription FROM srp_erp_taxmaster WHERE taxCategory = 2 AND companyID = {$companyID}";
        }             
        $data['taxdrop'] = $this->db->query("SELECT taxMasterAutoID, taxDescription
                                    FROM srp_erp_taxmaster 
                                    WHERE taxCategory != 2 AND isClaimable = 0 AND taxType = 2 AND companyID = {$companyID}
                            {$str}")->result_array();

        // $data['taxdrop'] = $this->db->query("SELECT `taxCalculationformulaID`, `Description` FROM `srp_erp_taxcalculationformulamaster` WHERE `isClaimable` =0 AND `taxType` = 2 AND `companyID` = $companyID")->result_array();

        return $data;
    }


    function save_prq_base_items()
    {
        //$post = $this->input->post();

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaserequestdetails.*,sum(srp_erp_purchaseorderdetails.prQty) AS prQty,srp_erp_purchaserequestmaster.purchaseRequestCode');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where_in('srp_erp_purchaserequestdetails.purchaseRequestDetailsID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaserequestdetails.purchaseRequestID');
        $this->db->join('srp_erp_purchaseorderdetails', 'srp_erp_purchaseorderdetails.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->group_by("purchaseRequestDetailsID");
        $query = $this->db->get()->result_array();

        $this->db->select('expectedDeliveryDate');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $documentDeliveryDate = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discountPercentage = $this->input->post('discount');
        $discountAmount = $this->input->post('discountamt');
        $taxtype = $this->input->post('taxtype');
        $results=false;
        $purchaseOrderID =  trim($this->input->post('purchaseOrderID') ?? '');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID');
        for ($i = 0; $i < count($query); $i++) {
            $this->db->select('prMasterID');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('prMasterID', $query[$i]['purchaseRequestID']);
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();


  /*          if (!empty($order_detail)) {
                $this->session->set_flashdata('w', 'Purchase Request Details added already.');
            } else {*/

                $data[$i]['prMasterID'] = $query[$i]['purchaseRequestID'];
                // $data[$i]['purchaseRequestCode'] = $query[$i]['purchaseRequestCode'];
                $data[$i]['prDetailID'] = $query[$i]['purchaseRequestDetailsID'];
                $data[$i]['purchaseOrderID'] = trim($this->input->post('purchaseOrderID') ?? '');
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['prQty'] = $query[$i]['requestedQty'];
                $data[$i]['discountPercentage'] = $discountPercentage[$i];
                $data[$i]['discountAmount'] = $discountAmount[$i];
                $data[$i]['unitAmount'] = $amount[$i] - $discountAmount[$i];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['totalAmount'] = ($amount[$i] - $discountAmount[$i]) * $qty[$i];

                //add pr Group and replacement assets
                $data[$i]['groupAssetsID'] = $query[$i]['groupAssetsID'];
                $data[$i]['replacementAssetsID'] = $query[$i]['replacementAssetsID'];
                $data[$i]['detailExpectedDeliveryDate'] = $documentDeliveryDate['expectedDeliveryDate'];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $query[$i]['remarks'];
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

                $results=$this->db->insert('srp_erp_purchaseorderdetails', $data[$i]);
                $last_id = $this->db->insert_id();

                if(!empty($taxtype[$i])){

                    if($group_based_tax == 1){ 
                        tax_calculation_vat(null,null,$taxtype[$i],'purchaseOrderAutoID',trim($this->input->post('purchaseOrderID') ?? ''), ($amount[$i]) * $qty[$i],'PO-PRQ',$last_id, ($data[$i]['discountAmount']*$query[$i]['requestedQty']),1);
                    }else { 
                        // $this->line_by_tax_calculation($taxtype[$i],$this->input->post('purchaseOrderID'),$last_id,$data[$i]['totalAmount']);
                    $itemAutoID = trim($query[$i]['itemAutoID']);
                    $purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');
                    $taxData['taxAmount'] = 0;
                    $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$taxtype[$i]}")->row_array();
                    if($taxCat['taxCategory'] == 2) {
                        $vatSubCat = $this->db->query("SELECT percentage 
                                                        FROM srp_erp_tax_vat_sub_categories
                                                            JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                        WHERE taxMasterAutoID = {$taxtype[$i]}
                                                            AND itemAutoID = {$itemAutoID}")->row('percentage');
                        if($vatSubCat) {
                            $taxData['taxAmount'] = ($data[$i]['totalAmount'] / 100) * $vatSubCat;
                        } else {
                            $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                                FROM srp_erp_suppliermaster 
                                                JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                                WHERE purchaseOrderID = {$purchaseOrderID}")->row_array();
                            $taxData['taxAmount'] = ($data[$i]['totalAmount'] / 100) * $suppliertaxPercentage['vatPercentage'];
                        }     
                    } else {
                        $taxData['taxAmount'] = ($data[$i]['totalAmount'] / 100) * $taxCat['taxPercentage'];
                    }
                    $taxData['isVAT'] = 1;
                    $taxData['taxCalculationformulaID'] = $taxtype[$i];
                    $this->db->where('purchaseOrderDetailsID', $last_id);
                    $this->db->update('srp_erp_purchaseorderdetails', $taxData);
                    }
                }
                $this->db->select('documentTaxType');
                $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
                $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

                $this->db->select('taxMasterAutoID');
                $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
                $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

                if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                    $taxtotal = $this->input->post('taxtotal');
                    $this->update_po_generaltax($this->input->post('purchaseOrderID'),($taxtotal+$data[$i]['totalAmount']));
                }

        }

        if ($results) {
            //print_r($data);
            //$this->db->insert_batch('srp_erp_purchaseorderdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Request : Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false, 'data' => 'Purchase Request Details added already.');
        }
    }


    function fetch_last_grn_amount()
    {
        $itemAutoId = $this->input->post('itemAutoId');
        $currencyID = $this->input->post('currencyID');
        $supplierPrimaryCode = $this->input->post('supplierPrimaryCode');
        $purchaseOrderID = $this->input->post('purchaseOrderID');

        $catlogue = getPolicyValues('PMIC', 'All');

        if($catlogue == 1){

                $this->db->where('purchaseOrderID',$purchaseOrderID);
                $result = $this->db->from('srp_erp_purchaseordermaster')->get()->row_array();

                //pull items with catlogue values
                $this->db->where('itemAutoID',$itemAutoId);
                $this->db->where('approvedYN',1);
                $this->db->where('srp_erp_inventorycataloguemaster.supplierID',$supplierPrimaryCode);
                $this->db->join('srp_erp_inventorycataloguemaster','srp_erp_inventorycataloguemaster.mrAutoID = srp_erp_inventorycataloguedetails.mrAutoID');
                $catlogues = $this->db->from('srp_erp_inventorycataloguedetails')->get()->result_array();


                $price_arr = array();
                foreach($catlogues as $cat_value){
                
                    if(($cat_value['fromDate'] <= $result['documentDate']) && ($cat_value['toDate'] >= $result['documentDate'])){
                        $price_arr['status'] = true;
                        $price_arr['receivedAmount'] = $cat_value['transactionAmount'];
                     
                    }

                }

                return $price_arr;

        }else{

            $data = $this->db->query('SELECT
                    grvdetails.receivedAmount
                FROM
                    srp_erp_grvdetails grvdetails
                JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

                where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . '
                and grvmaster.supplierID=' . $supplierPrimaryCode . '
                and grvDate=(SELECT
                    max(grvmaster.grvDate) as maxdate
                FROM
                    srp_erp_grvdetails grvdetails
                JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

                where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . ' and grvmaster.supplierID=' . $supplierPrimaryCode . '
            )')->row_array();

            return $data;

        }

        
    
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }

    function loademail()
    {
        $poid = $this->input->post('purchaseOrderID');
        $this->db->select('srp_erp_purchaseordermaster.*,srp_erp_suppliermaster.supplierEmail as supplierEmail');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->db->from('srp_erp_purchaseordermaster ');
        return $this->db->get()->row_array();

    }

    function send_po_email()
    {
        $poid = trim($this->input->post('purchaseOrderID') ?? '');
        $supplierEmail = trim($this->input->post('email') ?? '');
        $this->db->select('srp_erp_purchaseordermaster.*,srp_erp_suppliermaster.supplierEmail as supplierEmail,srp_erp_suppliermaster.supplierName as supplierName');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->db->from('srp_erp_purchaseordermaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['supplierEmail'] == '') {
                $data_master['supplierEmail'] = $supplierEmail;
                $this->db->where('supplierAutoID', $results['supplierID']);
                $this->db->update('srp_erp_suppliermaster', $data_master);
            }
        }

        $data['approval'] = $this->input->post('approval');
        $data['extra'] = $this->Procurement_modal->fetch_template_data($poid);
        $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $data['printHeaderFooterYN'] = 1;
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH . base_url() . "/uploads/po/" . $poid . "-PO-" . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);

        $this->db->select('supplierEmail,supplierName');
        $this->db->where('supplierAutoID', $results['supplierID']);
        $this->db->from('srp_erp_suppliermaster ');
        $supplierMaster = $this->db->get()->row_array();

        if (!empty($supplierMaster)) {
            if ($supplierMaster['supplierEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our purchase order as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $supplierEmail,
                    'subject' => ' Purchase Order of ' . $supplierMaster['supplierName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);

                return array('s', 'Email Send Successfully.',$supplierEmail,$poid);
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }
    }

    function save_inv_disc_detail()
    {

        $this->db->select('documentTaxType,generalDiscountAmount');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
        
        $group_based_tax =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');

        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $disc_amount = $this->input->post('disc_amount');
        $companyID = current_companyID();
        if($group_based_tax == 1){
            $isRcmApplicable = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$this->input->post('purchaseOrderID'));
            if($documentTaxType['documentTaxType']==0 ){


            $taxType = $this->db->query("SELECT taxFormulaMasterID,taxDetailAutoID FROM `srp_erp_purchaseordertaxdetails` where companyID = $companyID AND purchaseOrderAutoID = $purchaseOrderID")->result_array();

              //  tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$this->input->post('purchaseOrderID'),($this->input->post('taxtotal')),0,'PO');
               if(!empty($taxType)){

                   foreach ($taxType as $val){

                       tax_calculation_vat('srp_erp_purchaseordertaxdetails',null,$val['taxFormulaMasterID'],'purchaseOrderAutoID',$this->input->post('purchaseOrderID'),($this->input->post('taxtotal')),'PO',null,$disc_amount,0,$isRcmApplicable,$val['taxDetailAutoID']);
                   }
               }
            }
        }else { 
            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                $this->update_po_generaltax($this->input->post('purchaseOrderID'),($this->input->post('taxtotal')-$this->input->post('disc_amount')));     
              }
        }


       
        $this->db->trans_start();
        $data['generalDiscountPercentage'] = $this->input->post('discpercentage');
        $data['generalDiscountAmount'] = $this->input->post('disc_amount');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $this->db->update('srp_erp_purchaseordermaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'data' => 'Discount Detail Save Failed ');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'data' => 'Discount Detail  Saved Successfully.');
        }
    }

    function delete_purchase_order_discount()
    {
        $this->db->select('documentTaxType,generalDiscountAmount');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
       
        $group_based_tax =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');

        if($group_based_tax == 1){ 
            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                    tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$this->input->post('purchaseOrderID'),($this->input->post('taxtotal')+$documentTaxType['generalDiscountAmount']),0,'PO');
            }
        }else {
            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                if($group_based_tax == 1){ 
                    tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$this->input->post('purchaseOrderID'),($this->input->post('taxtotal')+$documentTaxType['generalDiscountAmount']),0,'PO');
                }else { 
                    $this->update_po_generaltax($this->input->post('purchaseOrderID'),($this->input->post('taxtotal')+$documentTaxType['generalDiscountAmount']));
                }
            }
        }

     

        $data['generalDiscountPercentage'] = 0;
        $data['generalDiscountAmount'] = 0;
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $results = $this->db->update('srp_erp_purchaseordermaster', $data);
        if ($results) {
            return array('s', 'Record deleted successfully');
        } else {
            return array('e', 'Record deletion failed');
        }
    }

    function edit_discount()
    {
        $this->db->select('generalDiscountPercentage,generalDiscountAmount');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $this->db->from('srp_erp_purchaseordermaster');
        $result = $this->db->get()->row_array();
        return $result;
    }

    function trace_po_document()
    {
        $poid = trim($this->input->post('purchaseOrderID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
    }

    function get_inventory_qty_from_documents($itemAutoID, $tableName, $itemautoField, $qtyField, $companyIdfild, $mastertbl, $masterID, $detailID)
    {
        $companyID = current_companyID();
        $Qty = $this->db->query("SELECT
	IFNULL(SUM($tableName.$qtyField),0) as Qty
FROM
	$tableName
	LEFT JOIN $mastertbl ON $mastertbl.$masterID = $tableName.$detailID
WHERE
	$itemautoField = $itemAutoID AND $mastertbl.$companyIdfild=$companyID")->row_array();
        if (!empty($Qty)) {
            return $Qty['Qty'];
        } else {
            return 0;
        }
    }

    function save_purchase_order_header_buyback()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDate') ?? '');
        $POdate = trim($this->input->post('POdate') ?? '');
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($POdate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierPrimaryCode') ?? ''));
        $ship_data = fetch_address_po(trim($this->input->post('shippingAddressID') ?? ''));
        $sold_data = fetch_address_po(trim($this->input->post('soldToAddressID') ?? ''));
        $invoice_data = fetch_address_po(trim($this->input->post('invoiceToAddressID') ?? ''));
        $data['documentID'] = 'PO';
        $data['documentTaxType'] = $this->input->post('documentTaxType');
        $data['narration'] = trim_desc($this->input->post('narration'));
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');
        $data['supplierPrimaryCode'] = trim($this->input->post('supplierPrimaryCode') ?? '');
        $data['purchaseOrderType'] = trim($this->input->post('purchaseOrderType') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->input->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['referenceNumber'] = trim($this->input->post('referenceNumber') ?? '');
        $data['driverName'] = trim($this->input->post('drivername') ?? '');
        $data['vehicleNo'] = trim($this->input->post('vehicleNo') ?? '');
        $data['creditPeriod'] = trim($this->input->post('creditPeriod') ?? '');
        $data['soldToAddressID'] = trim($this->input->post('soldToAddressID') ?? '');
        $data['shippingAddressID'] = trim($this->input->post('shippingAddressID') ?? '');
        $data['invoiceToAddressID'] = trim($this->input->post('invoiceToAddressID') ?? '');
        $data['supplierID'] = $supplier_arr['supplierAutoID'];
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['paymentTerms'] = trim_desc($this->input->post('paymentTerms'));
        $data['penaltyTerms'] = trim_desc($this->input->post('penaltyTerms'));
        $data['deliveryTerms'] = trim_desc($this->input->post('deliveryTerms'));
        $data['shippingAddressID'] = $ship_data['addressID'];
        $data['shippingAddressDescription'] = trim($this->input->post('shippingAddressDescription') ?? '');
        $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
        $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
        $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
        $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];
        $data['invoiceToAddressID'] = $invoice_data['addressID'];
        $data['invoiceToAddressDescription'] = $invoice_data['addressDescription'];
        $data['invoiceTocontactPersonID'] = $invoice_data['contactPerson'];
        $data['invoiceTocontactPersonTelephone'] = $invoice_data['contactPersonTelephone'];
        $data['invoiceTocontactPersonFaxNo'] = $invoice_data['contactPersonFaxNo'];
        $data['invoiceTocontactPersonEmail'] = $invoice_data['contactPersonEmail'];
        $data['soldToAddressID'] = $sold_data['addressID'];
        $data['soldToAddressDescription'] = $sold_data['addressDescription'];
        $data['soldTocontactPersonID'] = $sold_data['contactPerson'];
        $data['soldTocontactPersonTelephone'] = $sold_data['contactPersonTelephone'];
        $data['soldTocontactPersonFaxNo'] = $sold_data['contactPersonFaxNo'];
        $data['soldTocontactPersonEmail'] = $sold_data['contactPersonEmail'];
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['contactPersonName'] = trim($this->input->post('contactperson') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactnumber') ?? '');
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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        if (trim($this->input->post('purchaseOrderID') ?? '')) {
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('purchaseOrderID'), 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_purchaseordermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        }
    }

    function confirmation_Inventory_check(){
        $budegtControl = getPolicyValues('BDC', 'All');
        $bdcval = 0;
        $inventoryparr = array();
        $companyID=current_companyID();
        $noninventoryparr = array();

        $this->db->select('documentDate,segmentID,companyReportingExchangeRate,purchaseOrderType');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordermaster');
        $mastr = $this->db->get()->row_array();

        if('LOG' === $mastr['purchaseOrderType']){
            return array("success", 'success', 0, 0);
        }

        $this->db->select('*');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseorderdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return array(false, 'error');
        } else {
            foreach ($record as $rec) {
                $item_arr = fetch_item_data($rec['itemAutoID']);
                if ($item_arr['mainCategory'] == 'Inventory') {
                    $itemAutoID = $rec['itemAutoID'];

                    if($item_arr['maximunQty']==0){
                        array_push($noninventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "Poqty" => $rec['requestedQty']));
                        $bdcval++;
                    }
                }else if ($item_arr['mainCategory'] == 'Service' || $item_arr['mainCategory'] == 'Non Inventory') {

                    $sgmnt = $mastr['segmentID'];
                    $costGLAutoID = $item_arr['costGLAutoID'];

                    $docdt = $mastr['documentDate'];

                    //get finance year details using PO document Date
                    $financeyr = $this->db->query("SELECT
companyFinanceYearID,beginingDate,endingDate
FROM
srp_erp_companyfinanceyear
WHERE
'$docdt' BETWEEN beginingDate and endingDate
AND companyID=$companyID")->row_array();
                    $finYear = $financeyr['companyFinanceYearID'];

                    $bgtamnt = $this->db->query("SELECT
SUM(IFNULL(srp_erp_budgetdetail.companyReportingAmount, 0)) AS amount
FROM
`srp_erp_budgetdetail`
LEFT JOIN srp_erp_budgetmaster ON srp_erp_budgetmaster.budgetAutoID =  srp_erp_budgetdetail.budgetAutoID
WHERE
    GLAutoID = $costGLAutoID
AND srp_erp_budgetdetail.segmentID = $sgmnt
AND companyFinanceYearID = $finYear
AND approvedYN = 1
AND srp_erp_budgetdetail.companyID=$companyID")->row_array();
                    //print_r($bgtamnt);exit;
                    if(empty($bgtamnt['amount'])){
                        $glcod=fetch_gl_account_desc($costGLAutoID);
                        array_push($inventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "Glcode" => $glcod['GLSecondaryCode'].'-'.$glcod['GLDescription'], "poamount" => $rec['totalAmount']));
                        $bdcval++;
                    }

                }
            }
            if ($bdcval > 0) {
                return array("exceeded", 'error', $inventoryparr, $noninventoryparr);
            }else{
                return array("success", 'success', 0, 0);
            }
        }
    }
    function open_all_notes(){
        $docid = trim($this->input->post('docid') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $this->db->select('autoID,description');
        $this->db->where('documentID', $docid);
        $this->db->where('typeID', $type);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->result_array();
        return $data;
    }
    function load_default_note(){
        $docid = trim($this->input->post('docid') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $this->db->select('description');
        $this->db->where('documentID', $docid);
        $this->db->where('typeID', $type);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('isDefault', 1);
        $data = $this->db->get('srp_erp_termsandconditions')->row_array();
        return $data;
    }
    function load_notes(){
        $autoID = trim($this->input->post('allnotedesc') ?? '');
        $check_type = trim($this->input->post('check_type') ?? '');
        $this->db->select('description,typeID');
        $this->db->where('autoID', $autoID);
        $this->db->where('typeID', $check_type);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->row_array();
        return $data;
    }

    function save_document_email_history(){
        $toemail = $this->input->post('toemail');
        $documentCode = $this->input->post('documentCode');
        $documentID = $this->input->post('documentID');

        $data['documentID'] = $documentCode;
        $data['documentAutoID'] = $documentID;
        $data['sentByEmpID'] = $this->common_data['current_userID'];
        $data['toEmailAddress'] = $toemail;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['sentDateTime'] = $this->common_data['current_date'];
        $data['timestamp'] = $this->common_data['current_date'];

        $result=$this->db->insert('srp_erp_documentemailhistory', $data);
        if($result){
            return array("s", 'Successfully Saved');
        }

    }

    function save_po_general_tax(){
        $tax_total=$this->input->post('tax_total');
        $companyID=current_companyID();
        $taxCalculationformulaID=$this->input->post('text_type');

        $group_based_tax =  getPolicyValues('GBT', 'All');

        $this->db->select('*');
        $this->db->where('taxCalculationformulaID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

        $this->db->select('supplierID, transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $data['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID') ?? '');
        $data['taxFormulaMasterID'] = trim($this->input->post('text_type') ?? '');
        $data['taxDescription'] = $master['Description'];
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


        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        
        if($group_based_tax == 1){ 
            return tax_calculation_vat('srp_erp_purchaseordertaxdetails',$data,trim($this->input->post('text_type') ?? ''),'purchaseOrderAutoID',$this->input->post('purchaseOrderID'),$tax_total,'PO');
       
        }else{ 
            $this->db->select('taxFormulaMasterID');
            $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
            $this->db->where('taxFormulaMasterID', $this->input->post('text_type'));
            $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
            if (!empty($tax_detail)) {
                return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
            }
            $result= $this->db->insert('srp_erp_purchaseordertaxdetails', $data);
            $last_id = $this->db->insert_id();

            if ($result) {
                $this->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories');
                $this->db->where('taxCalculationformulaID', $this->input->post('text_type'));
                $formulaDtl = $this->db->get('srp_erp_taxcalculationformuladetails')->result_array();
                if(!empty($formulaDtl)){
                    foreach($formulaDtl as $val){
                        $sortOrder=$val['sortOrder'];
                        $tax_categories = $this->db->query("SELECT
                                                                    srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
                                                                FROM
                                                                    srp_erp_taxcalculationformuladetails
                                                                LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                                WHERE
                                                                    taxCalculationformulaID = $taxCalculationformulaID
                                                                AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                        $this->db->select("supplierCountryID, supplierAutoID, vatEligible, supplierLocationID, srp_erp_location.locationType");
                        $this->db->join('srp_erp_location', 'srp_erp_location.locationID = srp_erp_suppliermaster.supplierLocationID', 'LEFT');
                        $this->db->where('supplierAutoID', $inv_master['supplierID']);
                        $vatDetails_sup = $this->db->get("srp_erp_suppliermaster")->row_array();

                        $formulaBuilder = tax_formulaBuilder_to_sql($val, $tax_categories,$tax_total);
                        $formulaDecodeval=$formulaBuilder['formulaDecode'];
                        $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();
    
                        $dataleg['documentID'] = 'PO';
                        $dataleg['documentMasterAutoID'] = $this->input->post('purchaseOrderID');
                        $dataleg['taxDetailAutoID'] = $last_id;
                        $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                        $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                        $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                        $dataleg['amount'] = $amounttx['amount'];
                        $dataleg['formula'] = $val['formula'];
                        $dataleg['countryID'] = $vatDetails_sup['supplierCountryID'];
                        $dataleg['partyVATEligibleYN'] = $vatDetails_sup['vatEligible'];
                        $dataleg['partyID'] = $vatDetails_sup['supplierAutoID'];
                        $dataleg['locationID'] = $vatDetails_sup['supplierLocationID'];
                        $dataleg['locationType'] = $vatDetails_sup['locationType'];
                        $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                        $dataleg['createdPCID'] = $this->common_data['current_pc'];
                        $dataleg['createdUserID'] = $this->common_data['current_userID'];
                        $dataleg['createdUserName'] = $this->common_data['current_user'];
                        $dataleg['createdDateTime'] = $this->common_data['current_date'];
                        $Dresult= $this->db->insert('srp_erp_taxledger', $dataleg);
                    }
                }else{
                    $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($last_id)));
                }
    
    
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail Saved Successfully.', 'last_id' => $last_id);
            } else {
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail Save Failed ');
            }
        }
    }


    function delete_tax_po()
    {
        $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        $this->db->delete('srp_erp_taxledger', array('taxDetailAutoID' => $this->input->post('taxDetailAutoID'), 'documentMasterAutoID' => $this->input->post('purchaseOrderID'), 'documentID' => 'PO'));
        return true;
    }

    function load_line_tax_amount(){
        $purchaseOrderID=$this->input->post('purchaseOrderID');
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $disount = trim($this->input->post('discount') ?? '');
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $purchaseRequestDetailsID = (($this->input->post('purchaseRequestDetailsID')!='')? $this->input->post('purchaseRequestDetailsID'):null);
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');
        if($isGroupByTax == 1){
            $amnt=0;
            $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$this->input->post('purchaseOrderID'));
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,($disount!=''?($disount*$quantityRequested):0),'PO',$purchaseOrderID,$this->input->post('purchaseOrderDetailsID'), $isRcmDocument);
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }else { 
            if(!$itemAutoID) {
                $purchaseRequestDetailsID = $this->input->post('purchaseRequestDetailsID');
                $itemAutoID = $this->db->query("SELECT itemAutoID FROM srp_erp_purchaserequestdetails WHERE purchaseRequestDetailsID = {$purchaseRequestDetailsID}")->row('itemAutoID');
            }
            $amnt=0;
            $companyID=current_companyID();
                $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$taxCalculationformulaID}")->row_array();
                if($taxCat['taxCategory'] == 2) {
                    $vatSubCat = $this->db->query("SELECT percentage 
                                                    FROM srp_erp_tax_vat_sub_categories
                                                        JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                    WHERE taxMasterAutoID = {$taxCalculationformulaID}
                                                        AND itemAutoID = {$itemAutoID}")->row('percentage');
                    if($vatSubCat) {
                        $amnt = ($applicableAmnt / 100) * $vatSubCat;
                    } else {
                        $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                            FROM srp_erp_suppliermaster 
                                            JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                            WHERE purchaseOrderID = {$purchaseOrderID}")->row_array();
                        $amnt = ($applicableAmnt / 100) * $suppliertaxPercentage['vatPercentage'];
                    }     
                } else {
                    $amnt = ($applicableAmnt / 100) * $taxCat['taxPercentage'];
                }
        }
      
      
            
        /** Formula based Tax Calculation muted */
        //     $this->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories');
        //     $this->db->where('taxCalculationformulaID', $this->input->post('taxtype'));
        //     $formulaDtl = $this->db->get('srp_erp_taxcalculationformuladetails')->result_array();
    
        //     if(!empty($formulaDtl)) {
        //         foreach ($formulaDtl as $val) {
        //             $sortOrder = $val['sortOrder'];
        //             $tax_categories = $this->db->query("SELECT
        //                         srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
        //                     FROM
        //                         srp_erp_taxcalculationformuladetails
        //                     LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
        //                     WHERE
        //                         taxCalculationformulaID = $taxCalculationformulaID
        //                     AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();
    
        //             $formulaBuilder = tax_formulaBuilder_to_sql($val, $tax_categories, $applicableAmnt);
        //             $formulaDecodeval = $formulaBuilder['formulaDecode'];
        //             $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();
        //             $amnt+=$amounttx['amount'];
        //         }
        //     }
        // }       

        return $amnt;
    }

    function line_by_tax_calculation($text_type,$purchaseOrderID,$last_id,$totalAmount,$insrt=0){
        $tax_total=$totalAmount;
        $companyID=current_companyID();
        $taxCalculationformulaID=$text_type;

        $this->db->select('*');
        $this->db->where('taxCalculationformulaID', $text_type);
        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

        $this->db->select('supplierID, transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories, inputVatGLAccountAutoID AS taxGLAutoID');
        $this->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID', 'LEFT');
        $this->db->where('taxCalculationformulaID', $text_type);
        $formulaDtl = $this->db->get('srp_erp_taxcalculationformuladetails')->result_array();
        if(!empty($formulaDtl)){
            $taxamnt=0;
            if($insrt==1){
                $this->db->delete('srp_erp_taxledger', array('documentDetailAutoID' => $last_id, 'documentMasterAutoID' => $purchaseOrderID, 'documentID' => 'PO'));
            }
            foreach($formulaDtl as $val){
                $sortOrder=$val['sortOrder'];
                $tax_categories = $this->db->query("SELECT
                                                            srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
                                                        FROM
                                                            srp_erp_taxcalculationformuladetails
                                                        LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                        WHERE
                                                            taxCalculationformulaID = $taxCalculationformulaID
                                                            AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                $this->db->select("supplierCountryID, supplierAutoID, vatEligible, supplierLocationID, srp_erp_location.locationType");
                $this->db->join('srp_erp_location', 'srp_erp_location.locationID = srp_erp_suppliermaster.supplierLocationID', 'LEFT');
                $this->db->where('supplierAutoID', $inv_master['supplierID']);
                $vatDetails_sup = $this->db->get("srp_erp_suppliermaster")->row_array();

                $formulaBuilder = tax_formulaBuilder_to_sql($val, $tax_categories,$tax_total);
                $formulaDecodeval=$formulaBuilder['formulaDecode'];
                $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();

                $dataleg['documentID'] = 'PO';
                $dataleg['documentMasterAutoID'] = $purchaseOrderID;
                $dataleg['documentDetailAutoID'] = $last_id;
                $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                $dataleg['amount'] = $amounttx['amount'];
                $dataleg['formula'] = $val['formula'];
                $dataleg['taxGlAutoID'] = $val['taxGLAutoID'];
                $dataleg['countryID'] = $vatDetails_sup['supplierCountryID'];
                $dataleg['partyVATEligibleYN'] = $vatDetails_sup['vatEligible'];
                $dataleg['partyID'] = $vatDetails_sup['supplierAutoID'];
                $dataleg['locationID'] = $vatDetails_sup['supplierLocationID'];
                $dataleg['locationType'] = $vatDetails_sup['locationType'];
                $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                $dataleg['createdPCID'] = $this->common_data['current_pc'];
                $dataleg['createdUserID'] = $this->common_data['current_userID'];
                $dataleg['createdUserName'] = $this->common_data['current_user'];
                $dataleg['createdDateTime'] = $this->common_data['current_date'];
                $Dresult= $this->db->insert('srp_erp_taxledger', $dataleg);
                $taxamnt+=$amounttx['amount'];
            }

            $data['taxAmount'] = $taxamnt;
            $data['taxCalculationformulaID'] = $text_type;
            $this->db->where('purchaseOrderDetailsID', trim($last_id));
            $this->db->update('srp_erp_purchaseorderdetails', $data);

        }
    }


    function update_po_generaltax($purchaseOrderID,$tax_total){
        $companyID=current_companyID();
        $this->db->select('*');
        $this->db->where('purchaseOrderAutoID', $purchaseOrderID);
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->result_array();

        foreach($tax_detail as $valu){
            $this->db->delete('srp_erp_taxledger', array('taxDetailAutoID' => $valu['taxDetailAutoID'], 'documentMasterAutoID' => $purchaseOrderID, 'documentID' => 'PO'));


        $this->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories, inputVatGLAccountAutoID AS taxGLAutoID');
        $this->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID', 'LEFT');
        $this->db->where('taxCalculationformulaID', $valu['taxFormulaMasterID']);
        $formulaDtl = $this->db->get('srp_erp_taxcalculationformuladetails')->result_array();
            $taxCalculationformulaID=$valu['taxFormulaMasterID'];
            if(!empty($formulaDtl)){
                foreach($formulaDtl as $val){
                    $sortOrder=$val['sortOrder'];
                    $tax_categories = $this->db->query("SELECT
    srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
    FROM
    srp_erp_taxcalculationformuladetails
    LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
    WHERE
    taxCalculationformulaID = $taxCalculationformulaID
    AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                    $formulaBuilder = tax_formulaBuilder_to_sql($val, $tax_categories,$tax_total);
                    $formulaDecodeval=$formulaBuilder['formulaDecode'];
                    $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();

                    $this->db->select("supplierCountryID, supplierAutoID, vatEligible, supplierLocationID, srp_erp_location.locationType");
                    $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'LEFT');
                    $this->db->join('srp_erp_location', 'srp_erp_location.locationID = srp_erp_suppliermaster.supplierLocationID', 'LEFT');
                    $this->db->where('purchaseOrderID', $purchaseOrderID);
                    $vatDetails_sup = $this->db->get("srp_erp_purchaseordermaster")->row_array();

                    $dataleg['documentID'] = 'PO';
                    $dataleg['documentMasterAutoID'] = $purchaseOrderID;
                    $dataleg['taxDetailAutoID'] = $valu['taxDetailAutoID'];
                    $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                    $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                    $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                    $dataleg['amount'] = $amounttx['amount'];
                    $dataleg['formula'] = $val['formula'];
                    $dataleg['taxGlAutoID'] = $val['taxGLAutoID'];
                    $dataleg['countryID'] = $vatDetails_sup['supplierCountryID'];
                    $dataleg['partyVATEligibleYN'] = $vatDetails_sup['vatEligible'];
                    $dataleg['partyID'] = $vatDetails_sup['supplierAutoID'];
                    $dataleg['locationID'] = $vatDetails_sup['supplierLocationID'];
                    $dataleg['locationType'] = $vatDetails_sup['locationType'];
                    $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                    $dataleg['createdPCID'] = $this->common_data['current_pc'];
                    $dataleg['createdUserID'] = $this->common_data['current_userID'];
                    $dataleg['createdUserName'] = $this->common_data['current_user'];
                    $dataleg['createdDateTime'] = $this->common_data['current_date'];
                    $Dresult= $this->db->insert('srp_erp_taxledger', $dataleg);
                }
            }
        }
    }

    function load_purchase_order_tracking()
    {
        $dateFrom=$this->input->post('dateFrom');
        $dateTo=$this->input->post('dateTo');
        $supplierAutoID=$this->input->post('supplierAutoID');
        $pocode =$this->input->post('poautoID');
        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($dateFrom, $date_format_policy);
        $datetoconvert = input_format_date($dateTo, $date_format_policy);
        $date = "";
        $segment = $this->input->post('SegmentAutoID');
        if (!empty($dateFrom) && !empty($dateTo)) {
            $date = " AND documentDate BETWEEN '$datefromconvert' AND '$datetoconvert'";
        }
        $supplier_filt = "";
        $pofilter = "";
        $segment_filter = "";
        if(!empty($supplierAutoID)){
            $supplierAutoIDs = join(',', $supplierAutoID);
            $supplier_filt = " AND supplierID IN ($supplierAutoIDs)";
        }
        if(!empty($pocode))
        {
            $pofilterAutoID = join(',', $pocode);
            $pofilter = " AND srp_erp_purchaseordermaster.purchaseOrderID IN ($pofilterAutoID)";
        }

        if(!empty($segment)){ 
            $segmentID = join(',', $segment);
            $segment_filter = " AND srp_erp_purchaseordermaster.segmentID IN ($segmentID)";
        }

        $companyID=current_companyID();
        $data['po_details'] = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID, expectedDeliveryDate,documentID,currencymaster.CurrencyCode, purchaseOrderCode, narration, supplierID, supplierCode, supplierName, DATE_FORMAT( approvedDate, '%d-%m-%Y' ) as approvedDate, narration, 	((SUM( srp_erp_purchaseorderdetails.totalAmount) ) + SUM(IFNULL(taxAmount,0) ) - srp_erp_purchaseordermaster.generalDiscountAmount ) AS Amount, transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces, documentDate,segmentCode 
                                FROM srp_erp_purchaseordermaster
	                            LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID 
	                            LEFT JOIN srp_erp_currencymaster currencymaster on currencymaster.currencyID = srp_erp_purchaseordermaster.transactionCurrencyID
	                            WHERE srp_erp_purchaseordermaster.companyID = {$companyID} AND srp_erp_purchaseorderdetails.companyID = {$companyID} 
	                            AND approvedYN = 1 {$date} {$supplier_filt} {$pofilter} {$segment_filter}
	                            GROUP BY srp_erp_purchaseordermaster.purchaseOrderID ORDER BY srp_erp_purchaseordermaster.purchaseOrderID DESC
	                            ")->result_array();

        $data['grv_details'] = $this->db->query("SELECT `srp_erp_grvmaster`.`grvAutoID` AS `grvAutoID`, grvDate, purchaseOrderMastertID,	`srp_erp_grvmaster`.`documentID` AS `documentID`, `grvPrimaryCode`, (SUM(receivedTotalAmount)) + SUM(IFNULL(taxAmount,0)) AS grvAmount, transactionCurrencyDecimalPlaces
                                                        FROM `srp_erp_grvmaster`
                                                        LEFT JOIN  srp_erp_grvdetails  ON  srp_erp_grvdetails.`grvAutoID` = srp_erp_grvmaster.grvAutoID
                                                        WHERE `srp_erp_grvmaster`.`companyID` = {$companyID} AND approvedYN = 1 AND purchaseOrderMastertID IS NOT NULL
                                                        GROUP BY srp_erp_grvdetails.purchaseOrderMastertID, srp_erp_grvmaster.grvAutoID")->result_array();

        $data['sup_po_details'] = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.documentID,`bookingInvCode`, srp_erp_paysupplierinvoicemaster.InvoiceAutoID, invoiceDate, SUM( srp_erp_paysupplierinvoicedetail.transactionAmount ) AS invoiceAmount, transactionCurrencyDecimalPlaces, purchaseOrderMastertID
FROM
	`srp_erp_paysupplierinvoicemaster`
	LEFT JOIN   srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
WHERE
	`srp_erp_paysupplierinvoicemaster`.`companyID` = {$companyID}
	AND approvedYN = 1
	AND purchaseOrderMastertID IS NOT NULL
	GROUP BY srp_erp_paysupplierinvoicemaster.InvoiceAutoID, srp_erp_paysupplierinvoicedetail.purchaseOrderMastertID 
")->result_array();

        $data['sup_grv_details'] = $this->db->query("SELECT `bookingInvCode`,srp_erp_paysupplierinvoicemaster.documentID, invoiceDate, SUM( srp_erp_paysupplierinvoicedetail.transactionAmount ) AS invoiceAmount, transactionCurrencyDecimalPlaces, grvAutoID, srp_erp_paysupplierinvoicemaster.InvoiceAutoID
FROM
	`srp_erp_paysupplierinvoicemaster`
	LEFT JOIN   srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
WHERE
	`srp_erp_paysupplierinvoicemaster`.`companyID` = {$companyID}
	AND approvedYN = 1
	AND grvAutoID IS NOT NULL
	GROUP BY srp_erp_paysupplierinvoicemaster.InvoiceAutoID, srp_erp_paysupplierinvoicedetail.grvAutoID ")->result_array();

        $data['payment_details'] = $this->db->query("SELECT srp_erp_paymentvouchermaster.documentID,InvoiceAutoID, srp_erp_paymentvouchermaster.payVoucherAutoId, PVcode, PVdate, SUM(srp_erp_paymentvoucherdetail.transactionAmount) as paymentGRV, srp_erp_paymentvoucherdetail.transactionCurrencyDecimalPlaces 
                            FROM srp_erp_paymentvouchermaster
	                        LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                            WHERE srp_erp_paymentvouchermaster.companyID = {$companyID} AND type = 'Invoice' AND approvedYN = 1 
                            GROUP BY InvoiceAutoID, srp_erp_paymentvouchermaster.payVoucherAutoId")->result_array();

        $data['po_payment_details'] = $this->db->query("SELECT srp_erp_paymentvouchermaster.documentID,srp_erp_paymentvouchermaster.payVoucherAutoId,purchaseOrderID, PVcode, PVdate, SUM(srp_erp_paymentvoucherdetail.transactionAmount) as paymentGRV, srp_erp_paymentvoucherdetail.transactionCurrencyDecimalPlaces	
                                        FROM srp_erp_paymentvouchermaster
                                        LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId 
                                        WHERE srp_erp_paymentvouchermaster.companyID = {$companyID} AND type = 'Advance' AND approvedYN = 1  AND  (purchaseOrderID IS NOT NULL OR purchaseOrderID <> 0) 
                                        GROUP BY purchaseOrderID, srp_erp_paymentvouchermaster.payVoucherAutoId")->result_array();

        return $data;
    }

    function fetch_purchase_order_tracking_excel()
    {
        $result = $this->load_purchase_order_tracking();
        $data_arr = array();
        $po_arr = array();
        if (!empty($result['po_details'])){
            $x = 1;
            foreach ($result['po_details'] as $res){
                if(!empty($result['grv_details'])) {
                    foreach ($result['grv_details'] as $grv) {
                        if($res['purchaseOrderID'] == $grv['purchaseOrderMastertID']) {
                            if(!empty($result['sup_grv_details'])) {
                                foreach ($result['sup_grv_details'] as $supInv) {
                                    if ($grv['grvAutoID'] == $supInv['grvAutoID']) {
                                        if (!empty($result['payment_details'])) {
                                            foreach ($result['payment_details'] as $inv_pv) {
                                                if($supInv['InvoiceAutoID'] == $inv_pv['InvoiceAutoID']) {
                                                    /*PO details*/
                                                    $data_arr['#'] = $x;
                                                    $data_arr['PONumber'] = $res['purchaseOrderCode'];
                                                    $data_arr['PODate'] = $res['documentDate'];
                                                    $data_arr['Narration'] = $res['narration'];
                                                    $data_arr['Segment'] = $res['segmentCode'];
                                                    $data_arr['SupplierCode'] = $res['supplierCode'];
                                                    $data_arr['SupplierName'] = $res['supplierName'];
                                                    $data_arr['Currency'] = $res['CurrencyCode'];
                                                    $data_arr['Amount'] = number_format($res['Amount'], $res['transactionCurrencyDecimalPlaces']);
                                                    /*GRV Details*/
                                                    $data_arr['GRVCode'] = $grv['grvPrimaryCode'];
                                                    $data_arr['GRVDate'] = $grv['grvDate'];
                                                    $data_arr['Amount_grv'] = number_format($grv['grvAmount'], $grv['transactionCurrencyDecimalPlaces']);
                                                    /*Supplier Invoice Details*/
                                                    $data_arr['InvoiceCode'] = $supInv['bookingInvCode'];
                                                    $data_arr['InvoiceDate'] = $supInv['invoiceDate'];
                                                    $data_arr['Amount_supin'] = number_format($supInv['invoiceAmount'], $supInv['transactionCurrencyDecimalPlaces']);
                                                    /*Payment Details*/
                                                    $data_arr['PaymentType'] = 'Invoice';
                                                    $data_arr['PaymentCode'] = $inv_pv['PVcode'];
                                                    $data_arr['PaymentDate'] = $inv_pv['PVdate'];
                                                    $data_arr['PaidAmount'] = number_format($inv_pv['paymentGRV'], $inv_pv['transactionCurrencyDecimalPlaces']);

                                                    array_push($po_arr, $data_arr);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if(!empty($result['sup_po_details'])) {
                    foreach ($result['sup_po_details'] as $PO_supInv) {
                        if($PO_supInv['purchaseOrderMastertID'] == $res['purchaseOrderID']){
                            if(!empty($result['payment_details'])){
                                foreach ($result['payment_details'] as $PO_PV){
                                    if($PO_PV['InvoiceAutoID'] == $PO_supInv['InvoiceAutoID']){
                                        /*PO details*/
                                        $data_arr['#'] = $x;
                                        $data_arr['PONumber'] = $res['purchaseOrderCode'];
                                        $data_arr['PODate'] = $res['documentDate'];
                                        $data_arr['Narration'] = $res['narration'];
                                        $data_arr['Segment'] = $res['segmentCode'];
                                        $data_arr['SupplierCode'] = $res['supplierCode'];
                                        $data_arr['SupplierName'] = $res['supplierName'];
                                        $data_arr['Currency'] = $res['CurrencyCode'];
                                        $data_arr['Amount'] = number_format($res['Amount'], $res['transactionCurrencyDecimalPlaces']);
                                        /*GRV Details*/
                                        $data_arr['GRVCode'] = '';
                                        $data_arr['GRVDate'] = '';
                                        $data_arr['Amount_grv'] = '';
                                        /*Supplier Invoice Details*/
                                        $data_arr['InvoiceCode'] = $supInv['bookingInvCode'];
                                        $data_arr['InvoiceDate'] = $supInv['invoiceDate'];
                                        $data_arr['Amount_supin'] = number_format($supInv['invoiceAmount'], $supInv['transactionCurrencyDecimalPlaces']);
                                        /*Payment Details*/
                                        $data_arr['PaymentType'] = 'Invoice';
                                        $data_arr['PaymentCode'] = $inv_pv['PVcode'];
                                        $data_arr['PaymentDate'] = $inv_pv['PVdate'];
                                        $data_arr['PaidAmount'] = number_format($inv_pv['paymentGRV'], $inv_pv['transactionCurrencyDecimalPlaces']);

                                        array_push($po_arr, $data_arr);
                                    }
                                }
                            }
                        }
                    }
                }

                if($result['po_payment_details']) {
                    foreach ($result['po_payment_details'] as $PO_PAY) {
                        if ($PO_PAY['purchaseOrderID'] == $res['purchaseOrderID']) {
                            /*PO details*/
                            $data_arr['#'] = $x;
                            $data_arr['PONumber'] = $res['purchaseOrderCode'];
                            $data_arr['PODate'] = $res['documentDate'];
                            $data_arr['Narration'] = $res['narration'];
                            $data_arr['Segment'] = $res['segmentCode'];
                            $data_arr['SupplierCode'] = $res['supplierCode'];
                            $data_arr['SupplierName'] = $res['supplierName'];
                            $data_arr['Currency'] = $res['CurrencyCode'];
                            $data_arr['Amount'] = number_format($res['Amount'], $res['transactionCurrencyDecimalPlaces']);
                            /*GRV Details*/
                            $data_arr['GRVCode'] = '';
                            $data_arr['GRVDate'] = '';
                            $data_arr['Amount_grv'] = '';
                            /*Supplier Invoice Details*/
                            $data_arr['InvoiceCode'] = '';
                            $data_arr['InvoiceDate'] = '';
                            $data_arr['Amount_supin'] = '';
                            /*Payment Details*/
                            $data_arr['PaymentType'] = 'Advance';
                            $data_arr['PaymentCode'] = $inv_pv['PVcode'];
                            $data_arr['PaymentDate'] = $inv_pv['PVdate'];
                            $data_arr['PaidAmount'] = number_format($inv_pv['paymentGRV'], $inv_pv['transactionCurrencyDecimalPlaces']);

                            array_push($po_arr, $data_arr);
                        }
                    }
                }
            }
        }
       
        return $po_arr;
    }

    function fetch_last_PO_price()
    {
        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $currency = $this->input->post('currency');
        $poDetails = "";
        if(!empty($itemAutoID)) {
            $poDetails = $this->db->query("SELECT
	podetails.unitAmount 
FROM
	srp_erp_purchaseorderdetails podetails
	JOIN srp_erp_purchaseordermaster pomaster ON podetails.purchaseOrderID = pomaster.purchaseOrderID 
WHERE
	pomaster.approvedYN = 1 
	AND pomaster.transactionCurrencyID = {$currency} 
	AND itemAutoID = {$itemAutoID} 
	AND podetails.companyID = {$companyID}
	AND documentDate =(
	SELECT
		max( pomaster.documentDate ) AS maxdate 
	FROM
		srp_erp_purchaseorderdetails podetails
		JOIN srp_erp_purchaseordermaster pomaster ON podetails.purchaseOrderID = pomaster.purchaseOrderID 
	WHERE
		pomaster.approvedYN = 1 
		AND pomaster.transactionCurrencyID = {$currency} 
	AND itemAutoID = {$itemAutoID} 
	AND podetails.companyID = {$companyID}
	)")->row_array();
        }
        //        echo '<pre>';print_r($poDetails);
        return $poDetails;
    }

    function fetch_details_item_status_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $postatus = $this->input->post('postatus');
        $items = $this->input->post('items');
        $poautoID = $this->input->post('poautoID');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $date = " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";
        $itemFilter = " AND srp_erp_purchaseorderdetails.itemAutoID IN (" . join(',', $items) . ")";
        $documentFilter = " AND srp_erp_purchaseorderdetails.purchaseOrderID IN (" . join(',', $poautoID) . ")";

        $where = array();
        $whereOr = '';
        if(!empty($postatus)) {
            foreach ($postatus AS $status) {
                if($status == 1) {
                    $where[] = 'prQty <= 0';
                }
                if($status == 2){
                    $where[] = '(prQty > 0 AND requestedQty > prQty)';
                }
                if($status == 3){
                    $where[] = 'balanceQty <= 0';
                }
            }
            if(!empty($where)) {
                $whereOr = " WHERE ( " . join(" OR ", $where) . " )";
            }
        }

        $sql = $this->db->query("SELECT * 
    FROM (
        SELECT
            srp_erp_purchaseorderdetails.purchaseOrderID,
            purchaseOrderCode,
            documentDate,
            srp_erp_purchaseorderdetails.itemAutoID,
            srp_erp_itemmaster.itemSystemCode,
            seconeryItemCode,
            srp_erp_itemmaster.itemDescription,
            unitOfMeasure,
            SUM(IFNULL(requestedQty, 0)) AS requestedQty,
            (SUM(IFNULL( grvQty, 0 )) + SUM(IFNULL( bsiQty, 0 ))) AS prQty,
            IFNULL( requestedQty, 0 ) - (SUM(IFNULL( grvQty, 0 )) + SUM(IFNULL( bsiQty, 0 ))) AS balanceQty,
            transactionCurrency,
            transactionCurrencyDecimalPlaces,
            SUM(totalAmount) AS totalAmount
        FROM
            srp_erp_purchaseorderdetails
            JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID
            LEFT JOIN ( SELECT SUM( receivedQty ) AS grvQty, purchaseOrderDetailsID FROM `srp_erp_grvdetails` GROUP BY purchaseOrderDetailsID ) grv ON grv.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID
            LEFT JOIN ( SELECT SUM( requestedQty ) AS bsiQty, purchaseOrderDetailsID FROM `srp_erp_paysupplierinvoicedetail` GROUP BY purchaseOrderDetailsID ) bsi ON bsi.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID
            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID 
        WHERE
            srp_erp_purchaseorderdetails.companyID = {$companyID} 
            AND approvedYN = 1 {$date} {$itemFilter} {$documentFilter}
        GROUP BY
            srp_erp_purchaseorderdetails.purchaseOrderID,
            srp_erp_purchaseorderdetails.itemAutoID
    )a {$whereOr}")->result_array();

        //        echo $this->db->last_query();
        //        exit();
        //        echo '<pre>'; print_r($sql);
        return $sql;
    }

    function fetch_pr_to_grv_details()
    {
        $companyID = current_companyID();
        $ItemAutoId = $this->input->post('items');
        $item_autoID_filter = '';
        $documentID = $this->input->post('documentID');
        $documentcode = $this->input->post('doccode');
        $date_format_policy = date_format_policy();
        $prDateFrom = $this->input->post('prDateFrom');
        $prDateFromconvert = input_format_date($prDateFrom, $date_format_policy);
        $prDateTo = $this->input->post('prDateTo');
        $prDateToconvert = input_format_date($prDateTo, $date_format_policy);
        $dateFilter = '';
        $documentcodefilter_pr = '';
        $documentcodefilter_po = '';
        $details = array();
        $posegment = $this->input->post('posegment');
        $filter_po_segment = '';
       
     
      
        if($ItemAutoId)
        {
            $item_autoID_filter .= "AND purchasereqdetail.itemAutoID IN ('".join(',', $ItemAutoId)."')";
        }
        if(!empty($prDateFrom) && !empty($prDateTo))
        {
            $dateFilter .= " AND purchasereqmaster.documentDate BETWEEN '" . $prDateFromconvert . "' AND '" . $prDateToconvert . "'";
        }
        if($documentcode)
        {
            if($documentID == 1)
            {
                $documentcodefilter_pr = " AND ((purchaseRequestCode Like '%" . $documentcode . "%')) ";
            }else {
                $documentcodefilter_po = " AND ((purchaseordermaster.purchaseOrderCode Like '%" . $documentcode . "%')) ";
            }
        }
        if($posegment){ 
            $filter_po_segment .= "AND purchaseordermaster.segmentID IN ('".join(',', $posegment)."')";
        }
        $purchaserequestmaster = $this->db->query("select 
                                         purchasereqmaster.purchaseRequestID as purchaseRequestID,
                                         purchasereqmaster.purchaseRequestCode,
                                         purchasereqmaster.documentDate,
                                         purchasereqmaster.narration,
                                         purchasereqmaster.approvedYN
                                         FROM
                                         srp_erp_purchaserequestmaster purchasereqmaster
                                         INNER JOIN (SELECT
                                                     purchasereqmaster.purchaseRequestID AS purchaseRequestID
                                                     FROM
                                                     srp_erp_purchaserequestdetails purchasereqdetail
                                                     LEFT JOIN srp_erp_purchaserequestmaster purchasereqmaster ON purchasereqmaster.purchaseRequestID = purchasereqdetail.purchaseRequestID
                                                     LEFT JOIN srp_erp_purchaseorderdetails purchaseorderdetial ON purchaseorderdetial.prDetailID = purchasereqdetail.purchaseRequestDetailsID
                                                     LEFT JOIN srp_erp_purchaseordermaster purchaseordermaster ON purchaseordermaster.purchaseOrderID = purchaseorderdetial.purchaseOrderID
                                                     LEFT JOIN srp_erp_suppliermaster suppmaster ON suppmaster.supplierAutoID = purchaseordermaster.supplierID
                                                     LEFT JOIN (SELECT
                                                                CONCAT( 'GRV', grvdetails.grvDetailsID ) AS masterIDcode,
                                                                grvmaster.grvPrimaryCode AS systemcode,
                                                                grvdetails.purchaseOrderDetailsID AS purchasedetId,
                                                                grvdetails.grvDetailsID AS masterDetailID,
                                                                grvdetails.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                                grvmaster.grvDate AS grvbsiDate,
                                                                grvdetails.requestedQty AS qty,
                                                                grvdetails.purchaseOrderDetailsID AS poorderID 
                                                                FROM
                                                                srp_erp_purchaseorderdetails purchaseorderdetial
                                                                LEFT JOIN srp_erp_grvdetails grvdetails ON grvdetails.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                                LEFT JOIN srp_erp_grvmaster grvmaster ON grvmaster.grvAutoID = grvdetails.grvAutoID 
                                                                GROUP BY
                                                                grvmaster.documentID,
                                                                grvmaster.grvAutoID UNION ALL
                                                                SELECT
                                                                CONCAT( 'BSI', supplierDetail.InvoiceDetailAutoID ) AS masterIDcode,
                                                                supplierinvmaster.bookingInvCode AS systemcode,
                                                                supplierDetail.purchaseOrderDetailsID AS purchasedetId,
                                                                supplierDetail.InvoiceDetailAutoID AS masterDetailID,
                                                                supplierDetail.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                                supplierinvmaster.bookingDate AS grvbsiDate,
                                                                supplierDetail.requestedQty AS qty,
                                                                supplierDetail.purchaseOrderDetailsID AS poorderID 
                                                                FROM
                                                                srp_erp_purchaseorderdetails purchaseorderdetial
                                                                LEFT JOIN srp_erp_paysupplierinvoicedetail supplierDetail ON supplierDetail.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                                LEFT JOIN srp_erp_paysupplierinvoicemaster supplierinvmaster ON supplierinvmaster.InvoiceAutoID = supplierDetail.InvoiceAutoID 
                                                                GROUP BY
                                                                supplierinvmaster.documentID,
                                                                supplierinvmaster.InvoiceAutoID 
                                                                ) grvdetail ON grvdetail.purchasedetId = purchaseorderdetial.purchaseOrderDetailsID 
                                                                WHERE
                                                                purchasereqmaster.companyID = $companyID 
                                                                AND purchasereqmaster.approvedYN = 1 
                                                                $item_autoID_filter
                                                                $documentcodefilter_pr
                                                                $documentcodefilter_po
                                                                $filter_po_segment
                                                                GROUP BY
                                                                purchasereqmaster.purchaseRequestID) prdetail ON prdetail.purchaseRequestID = purchasereqmaster.purchaseRequestID
                                                                WHERE
                                                                purchasereqmaster.companyID = $companyID 
                                                                $documentcodefilter_pr $dateFilter
                                                                 AND purchasereqmaster.approvedYN = 1")->result_array();

        $results = $this->db->query("SELECT
                                                purchasereqmaster.purchaseRequestID AS purchaseRequestID,
                                                purchasereqmaster.purchaseRequestCode,
                                                purchasereqmaster.documentDate,
                                                purchasereqdetail.itemAutoID,
                                                purchasereqdetail.itemSystemCode,
                                                purchasereqdetail.itemDescription,
                                                purchasereqdetail.defaultUOM,
                                                purchasereqdetail.requestedQty,
                                                purchasereqdetail.purchaseRequestDetailsID,
                                                purchasereqmaster.narration,
                                                purchasereqmaster.approvedYN,
                                                purchaseordermaster.purchaseOrderCode,
                                                purchaseorderdetial.prDetailID,
                                                purchaseorderdetial.purchaseOrderID,
                                                purchaseorderdetial.purchaseOrderDetailsID,
                                                purchaseordermaster.documentDate AS podate,
                                                suppmaster.supplierSystemCode,
                                                suppmaster.supplierName,
                                                purchaseorderdetial.requestedQty AS reqpoqty,
                                                purchaseordermaster.transactionCurrency AS pocurrency,
                                                purchaseorderdetial.totalAmount AS poamount,
                                                purchaseordermaster.transactionCurrencyDecimalPlaces AS podecimal,
                                                DATE_FORMAT( purchaseordermaster.ConfirmedDate, '%d/%m/%Y' ) AS poconfirmeddate,
                                                DATE_FORMAT( purchaseordermaster.approvedDate, '%d/%m/%Y' ) AS poapproveddate,
                                                purchaseordermaster.approvedYN AS poapprovedYN,
                                                grvdetail.systemcode,
                                                IFNULL(grvdetail.masterIDcode,'UN-1') as  masterIDcode,
                                                grvdetail.grvbsiDate,
                                                grvdetail.qty  as grvqty,
                                                IFNULL(grvdetail.poorderID,'N/A') as  poorderID,
                                                grvdetail.grvbsidoc,
                                                grvdetail.grvbsimasterID,
                                                grvdetail.grvbsiapprovedYN,
                                                srp_erp_segment.segmentCode
                                            FROM
                                                srp_erp_purchaserequestdetails purchasereqdetail
                                                LEFT JOIN srp_erp_purchaserequestmaster purchasereqmaster ON purchasereqmaster.purchaseRequestID = purchasereqdetail.purchaseRequestID
                                                LEFT JOIN srp_erp_purchaseorderdetails purchaseorderdetial ON purchaseorderdetial.prDetailID = purchasereqdetail.purchaseRequestDetailsID
                                                LEFT JOIN srp_erp_purchaseordermaster purchaseordermaster ON purchaseordermaster.purchaseOrderID = purchaseorderdetial.purchaseOrderID
                                                LEFT JOIN srp_erp_suppliermaster suppmaster ON suppmaster.supplierAutoID = purchaseordermaster.supplierID
                                                LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = purchaseordermaster.segmentID
                                                LEFT JOIN (
                                                SELECT
                                                    CONCAT( 'GRV', grvdetails.grvDetailsID ) AS masterIDcode,
                                                    grvmaster.grvPrimaryCode AS systemcode,
                                                    grvdetails.purchaseOrderDetailsID AS purchasedetId,
                                                    grvdetails.grvDetailsID AS masterDetailID,
                                                    grvdetails.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                    grvmaster.grvDate AS grvbsiDate,
                                                    grvdetails.receivedQty AS qty,
                                                    grvdetails.purchaseOrderDetailsID AS poorderID,
                                                    grvmaster.documentID  as grvbsidoc,
                                                    grvmaster.grvAutoID  as grvbsimasterID,
                                                    grvmaster.approvedYN AS grvbsiapprovedYN
                                                FROM
                                                    srp_erp_purchaseorderdetails purchaseorderdetial
                                                    LEFT JOIN srp_erp_grvdetails grvdetails ON grvdetails.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                    LEFT JOIN srp_erp_grvmaster grvmaster ON grvmaster.grvAutoID = grvdetails.grvAutoID 
                                                GROUP BY
                                                    grvmaster.documentID, grvmaster.grvAutoID, grvdetails.purchaseOrderDetailsID
                                            
                                                    UNION ALL
                                                SELECT
                                                    CONCAT( 'BSI', supplierDetail.InvoiceDetailAutoID ) AS masterIDcode,
                                                    supplierinvmaster.bookingInvCode AS systemcode,
                                                    supplierDetail.purchaseOrderDetailsID AS purchasedetId,
                                                    supplierDetail.InvoiceDetailAutoID AS masterDetailID,
                                                    supplierDetail.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                    supplierinvmaster.bookingDate AS grvbsiDate,
                                                    supplierDetail.requestedQty AS qty,
                                                    supplierDetail.purchaseOrderDetailsID AS poorderID,
                                                    supplierinvmaster.documentID as grvbsidoc,
                                                    supplierinvmaster.InvoiceAutoID  as grvbsimasterID,
                                                    supplierinvmaster.approvedYN AS grvbsiapprovedYN
                                                FROM
                                                    srp_erp_purchaseorderdetails purchaseorderdetial
                                                    LEFT JOIN srp_erp_paysupplierinvoicedetail supplierDetail ON supplierDetail.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                    LEFT JOIN srp_erp_paysupplierinvoicemaster supplierinvmaster ON supplierinvmaster.InvoiceAutoID = supplierDetail.InvoiceAutoID 
                                                GROUP BY
                                                    supplierinvmaster.documentID , supplierinvmaster.InvoiceAutoID, supplierDetail.purchaseOrderDetailsID
                                                ) grvdetail ON grvdetail.purchasedetId = purchaseorderdetial.purchaseOrderDetailsID 
                                            WHERE
                                                purchasereqmaster.companyID = $companyID 
                                                AND purchasereqmaster.approvedYN = 1 
                                                $item_autoID_filter $documentcodefilter_pr $documentcodefilter_po $filter_po_segment")->result_array();

        $pourchaseorder = array();
        $itemmaster = array();
        $grvbsi = array();
        foreach ($results as $key => $val) {
            $grvbsi[$val['masterIDcode']] = $val;
        }
        $grvdetail = $grvbsi;
        foreach ($results as $key => $val) {
            $pourchaseorder[$val['purchaseOrderDetailsID']] = $val;
        }
        foreach ($results as $key => $val) {
            $itemmaster[$val['purchaseRequestDetailsID']] = $val;
        }

        if(!empty($purchaserequestmaster))
        {
            $x = 1;
            foreach ($purchaserequestmaster as $val){
                $data['num'] = $x;
                $data['PRNumber'] = $val['purchaseRequestCode'];
                $data['PRDate'] = $val['documentDate'];
                $data['PRComment'] = $val['narration'];
                if($val['approvedYN']==1) {
                    $data['PRApproved'] = 'Approved';
                } else {
                    $data['PRApproved'] = 'Not Approved';
                }
                $item_index = 0;
                foreach ($itemmaster as $val1){
                    if($val1['purchaseRequestID'] == $val['purchaseRequestID']) {
                        if($item_index > 0) {
                            $data['num'] = '';
                            $data['PRNumber'] = '';
                            $data['PRDate'] = '';
                            $data['PRComment'] = '';
                            $data['PRApproved'] = '';
                        }
                        $data['itemSystemCode'] = $val1['itemSystemCode'];
                        $data['itemDescription'] = $val1['itemDescription'];
                        $data['defaultUOM'] = $val1['defaultUOM'];
                        $data['requestedQty'] = $val1['requestedQty'];

                        if($pourchaseorder){
                            $PO_Index = 0;
                            $data['purchaseOrderCode'] = '';
                            $data['podate'] = '';
                            $data['supplierSystemCode'] = '';
                            $data['supplierName'] = '';
                            $data['reqpoqty'] = '';
                            $data['pocurrency'] = '';
                            $data['segmentCode'] = '';
                            $data['poamount'] = '';
                            $data['poconfirmeddate'] = '';
                            $data['poapprovedYN'] = '';
                            $data['poapproveddate'] = '';
                            $data['systemcode'] = '';
                            $data['grvbsiDate'] = '';
                            $data['grvqty'] = '';
                            $data['grvbsiStatus'] = '';
                            foreach ($pourchaseorder as $podetail) {
                                if ($podetail['itemAutoID'] == $val1['itemAutoID'] && $podetail['purchaseRequestDetailsID'] == $val1['purchaseRequestDetailsID']
                                    && $podetail['poamount']>0 && ($podetail['poapprovedYN']!=null  || !empty( $podetail['poapprovedYN']))
                                    && ( $podetail['poorderID']!='N/A'))
                                {
                                    if($PO_Index > 0) {
                                        $data['num'] = '';
                                        $data['PRNumber'] = '';
                                        $data['PRDate'] = '';
                                        $data['PRComment'] = '';
                                        $data['PRApproved'] = '';
                                        $data['itemSystemCode'] = '';
                                        $data['itemDescription'] = '';
                                        $data['defaultUOM'] = '';
                                        $data['requestedQty'] = '';
                                    }
                                    $data['purchaseOrderCode'] = $podetail['purchaseOrderCode'];
                                    $data['podate'] = $podetail['podate'];
                                    $data['supplierSystemCode'] = $podetail['supplierSystemCode'];
                                    $data['supplierName'] = $podetail['supplierName'];
                                    $data['reqpoqty'] = $podetail['reqpoqty'];
                                    $data['pocurrency'] = $podetail['pocurrency'];
                                    $data['segmentCode'] = $podetail['segmentCode'];
                                    $data['poamount'] = number_format($podetail['poamount'],$podetail['podecimal']);
                                    $data['poconfirmeddate'] = $podetail['poconfirmeddate'];
                                    if($podetail['poapprovedYN']==1) {
                                        $data['poapprovedYN'] = 'Yes';
                                    }else if($podetail['poapprovedYN'] ==0 && ( $podetail['poapprovedYN']!=null  || !empty( $podetail['poapprovedYN']))){
                                        $data['poapprovedYN'] = 'No';
                                    }else {
                                        $data['poapprovedYN'] = '';
                                    }
                                    $data['poapproveddate'] = $podetail['poapproveddate'];

                                    if ($grvdetail) {
                                        $grv_bsi_index = 0;
                                        $data['systemcode'] = '';
                                        $data['grvbsiDate'] = '';
                                        $data['grvqty'] = '';
                                        $data['grvbsiStatus'] = '';
                                        foreach ($grvdetail as $grvbsi) {
                                            if ($grvbsi['itemAutoID'] == $podetail['itemAutoID'] && $grvbsi['poorderID'] == $podetail['purchaseOrderDetailsID'] && ($grvbsi['poorderID'] != 'N/A')) {
                                                if ($grv_bsi_index > 0) {
                                                    $data['num'] = '';
                                                    $data['PRNumber'] = '';
                                                    $data['PRDate'] = '';
                                                    $data['PRComment'] = '';
                                                    $data['PRApproved'] = '';
                                                    $data['itemSystemCode'] = '';
                                                    $data['itemDescription'] = '';
                                                    $data['defaultUOM'] = '';
                                                    $data['requestedQty'] = '';
                                                    $data['purchaseOrderCode'] = '';
                                                    $data['podate'] = '';
                                                    $data['supplierSystemCode'] = '';
                                                    $data['supplierName'] = '';
                                                    $data['reqpoqty'] = '';
                                                    $data['pocurrency'] = '';
                                                    $data['segmentCode'] = '';
                                                    $data['poamount'] = '';
                                                    $data['poconfirmeddate'] = '';
                                                    $data['poapprovedYN'] = '';
                                                    $data['poapproveddate'] = '';
                                                }

                                                $data['systemcode'] = $grvbsi['systemcode'];
                                                $data['grvbsiDate'] = $grvbsi['grvbsiDate'];
                                                $data['grvqty'] = $grvbsi['grvqty'];
                                                if($podetail['reqpoqty'] > $grvbsi['grvqty'] && $grvbsi['grvqty'] > 0) {
                                                    $data['grvbsiStatus'] = 'Partially Received';
                                                }else if($podetail['reqpoqty'] <= $grvbsi['grvqty']){
                                                    $data['grvbsiStatus'] = 'Fully Received';
                                                }else {
                                                    $data['grvbsiStatus'] = 'Not Received';
                                                }
        //                                                if($grvbsi['grvbsiapprovedYN']==1) {
        //                                                    $data['grvbsiapprovedYN'] = 'Yes';
        //                                                }else if($grvbsi['grvbsiapprovedYN']==0 && ( $grvbsi['grvbsiapprovedYN']!=null  || !empty( $grvbsi['grvbsiapprovedYN']))){
        //                                                    $data['grvbsiapprovedYN'] = 'No';
        //                                                }else {
        //                                                    $data['grvbsiapprovedYN'] = '';
        //                                                }

                                                $details[] = $data;
                                                $grv_bsi_index++;
                                            }
                                        }
                                    }
                                    if($grv_bsi_index == 0) {
                                        $details[] = $data;
                                    }
                                    $PO_Index++;
                                }
                            }
                        }
                        if($PO_Index == 0) {
                            $details[] = $data;
                        }
                        $item_index++;
                    }
                }
                $x++;
            }
        }

        return $details;
    }

    function fetch_line_tax_and_vat()
    {
        $companyID = current_companyID();
        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $itemAutoID = $this->input->post('itemAutoID');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->input->post('purchaseOrderID') ?? ''),'PO','purchaseOrderID');

        if($isGroupByTax == 1){
            $data['tax_drop'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 2);
            $selected_itemTax =   array_column($data['tax_drop'], 'assignedItemTaxFormula');
            $data['selected_itemTax'] =   $selected_itemTax[0];

        }else {
            $vateligible = $this->db->query("SELECT IFNULL(vatEligible, 0) as vatEligible 
            FROM srp_erp_purchaseordermaster 
            JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID
            WHERE purchaseOrderID = {$purchaseOrderID} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row('vatEligible');

            $str = '';
            if($vateligible == 2) {
            $str = "UNION ALL SELECT taxMasterAutoID, taxDescription FROM srp_erp_taxmaster WHERE taxCategory = 2 AND companyID = {$companyID}";
            }             
            $data = $this->db->query("SELECT taxMasterAutoID, taxDescription
                                      FROM srp_erp_taxmaster 
                                      WHERE taxCategory != 2 AND isClaimable = 0 AND taxType = 2 AND companyID = {$companyID}
                                      {$str}")->result_array();
        }
        return $data;
    }
  
   function update_commission_btb(){

        $percentage = $this->input->post('percentage');
        $detailAutoID = $this->input->post('detailAutoID');
        $companyID = current_companyID();

        //select record
        $this->db->where('purchaseOrderDetailsID',$detailAutoID);
        $this->db->where('companyID',$companyID);
        $detailRecord = $this->db->from('srp_erp_purchaseorderdetails')->get()->row_array();

        try {
            if($detailRecord){

                $totalAmount = $detailRecord['totalAmount'];
    
                $data = array();
    
                $data['comission_percentage'] = $percentage;
                $data['commision_value'] = round((($totalAmount * $percentage)/100),2);
    
                $this->db->where('purchaseOrderDetailsID',$detailAutoID)->where('companyID',$companyID)->update('srp_erp_purchaseorderdetails',$data);
    
            }

            $this->session->set_flashdata('s', 'Updated Successfully.');
            return true;
        } catch (\Throwable $th) {

            $this->session->set_flashdata('e', 'Something went wrong.');
            return false;
        }
   }


    function save_asset_detail_pr(){

        $this->db->trans_start();
        
        $det_id = trim($this->input->post('det_id') ?? '');
        $assets_replace = trim($this->input->post('assets_replace') ?? '');

        $assets_group = trim($this->input->post('assets_group') ?? '');

        $data['groupAssetsID']= isset($assets_group) ? $assets_group : null;
        $data['replacementAssetsID']= isset($assets_replace) ? $assets_replace : null;


        $this->db->where('purchaseRequestDetailsID', $det_id );
        $this->db->update('srp_erp_purchaserequestdetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e' ,'Try Again');
        } else {
            $this->db->trans_commit();
           
            return array("s", 'Successfully Saved');
        }

    }


    function save_po_item_delivery_date(){

        $this->db->trans_start();
        
        $masterID = trim($this->input->post('masterID') ?? '');
        $date = trim($this->input->post('date') ?? '');

        

        $date_format_policy = date_format_policy();
       
        $format_expectedDeliveryDate = input_format_date($date, $date_format_policy);
       
        $data['detailExpectedDeliveryDate']= $format_expectedDeliveryDate;


        $this->db->where('purchaseOrderDetailsID', $masterID );
        $this->db->update('srp_erp_purchaseorderdetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e' ,'Try Again');
        } else {
            $this->db->trans_commit();
           
            return array("s", 'Successfully Saved');
        }

    }

    function get_extra_charges_records($purchaseOrderID){

        $this->db->select('*');
        $this->db->from('srp_erp_purchaseorderextracharges');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        // $this->db->where('type', $type);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $extraChargeDetail = $this->db->get()->result_array();

        return $extraChargeDetail;

    }

    function set_contract_extra_charge(){

        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $extraChargeID = $this->input->post('extraChargeID');
        $extraChargeValue = $this->input->post('extraChargeValue');

        if($extraChargeID){


            $this->db->select('*');
            $this->db->from('srp_erp_discountextracharges');
            $this->db->where('discountExtraChargeID', $extraChargeID);
            $extraChargeDetail = $this->db->get()->row_array();
            
            if($extraChargeDetail){

                $data = array();
                
                $data['purchaseOrderID'] = $purchaseOrderID;
                $data['extraCostID'] = $extraChargeID;
                $data['extraCostName'] = $extraChargeDetail['Description'];
                $data['extraCostValue'] = $extraChargeValue;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $this->db->insert('srp_erp_purchaseorderextracharges', $data);
      
            }

            return array('s', 'Successfully added the Record');
            exit;

        }

    }

    function update_total_po_value($purchaseOrderID){

        $this->db->select('SUM(poUnitPrice) as amount');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', $contractAutoID);
        $contract_details = $this->db->get()->row_array();

        //check type 2 exists
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseorderextracharges');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->where('type', 2);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $po_cost_record = $this->db->get()->row_array();

        $data = array();

        if($po_cost_record){

            $record_id = $po_cost_record['id'];

            $data['extraCostValue'] = $contract_details['amount'];

            $this->db->where('id',$record_id)->update('srp_erp_purchaseorderextracharges',$data);

        }else{

            $data['type'] = 2;
            $data['purchaseOrderID'] = $purchaseOrderID;
            $data['extraCostID'] = -1;
            $data['extraCostName'] = 'Total PO Cost';
            $data['extraCostValue'] = $contract_details['amount'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_purchaseorderextracharges',$data);
        }
        
        return true;

    }


    function add_quotation_version_po()
    {
        $this->db->trans_start();
        $masterID = trim($this->input->post('masterID') ?? '');
        
        $this->db->select('purchaseOrderMastertID');
        $this->db->where('purchaseOrderMastertID', $masterID);
        $grv_data = $this->db->get('srp_erp_grvdetails')->result_array();

        $this->db->select('purchaseOrderMastertID');
        $this->db->where('purchaseOrderMastertID', $masterID);
        $invoice_data = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

        if (!empty($grv_data) || !empty($invoice_data)) {
            return array('status' => 0, 'type' => 'w', 'message' => 'You cannot create versions for this PO. GRV has been created already.');
        }

        $this->db->select('*');
        $this->db->where('purchaseOrderID', $masterID);
        $master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        
        $this->db->select('*');
        $this->db->where('purchaseOrderID', $masterID);
        $details = $this->db->get('srp_erp_purchaseorderdetails')->result_array();

        $this->db->insert('srp_erp_purchaseordermaster_version', $master);
        $last_id = $this->db->insert_id();

        

        if($last_id){
           
            foreach ($details as $key => $val) {
                $details[$key]['versionMasterID'] = $last_id;
    
                $this->db->insert('srp_erp_purchaseorderdetails_version', $details[$key]);
    
            }
          
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($masterID, 'PO');
            
            if ($status == 1) {
                $this->db->query("UPDATE srp_erp_purchaseordermaster SET versionNo = (versionNo +1) , confirmedYN=0 , approvedYN=0 WHERE purchaseOrderID='{$masterID}'");
               // $this->db->query("UPDATE srp_erp_purchaseordermaster_version SET versionNo = (versionNo +1) WHERE versionAutoID='{$last_id}'");
                $this->db->trans_complete();
                return array('status' => 1, 'type' => 's', 'message' => 'New Version of PO Created Successfully.');
            } else {

                $this->db->where('versionAutoID', $last_id)->delete('srp_erp_purchaseordermaster_version');
                $this->db->where('versionMasterID', $last_id)->delete('srp_erp_purchaseorderdetails_version');
                $this->db->trans_complete();
                return array('status' => 0, 'type' => 'w', 'message' => 'Version create fail !.');
            }
           
        }else{
            return array('status' => 0, 'type' => 'w', 'message' => 'Version create fail.');
        }

    }
  
  function fetch_commision_report_details()
    {
        $companyID = current_companyID();
        $details = array();
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();

        $from_date = trim($this->input->post('from_date') ?? '');
        $format_from_date = null;
        if (isset($from_date) && !empty($from_date)) {
            $format_from_date = input_format_date($from_date, $date_format_policy);
        }
        $to_date = trim($this->input->post('to_date') ?? '');
        $format_to_date = null;
        if (isset($to_date) && !empty($to_date)) {
            $format_to_date = input_format_date($to_date, $date_format_policy);
        }
        $date = "";
        if (!empty($from_date) && !empty($to_date)) {
            $date .= " AND ( po.documentDate >= '" . $format_from_date . " 00:00:00' AND documentDate <= '" . $format_to_date . " 23:59:00')";
        }

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $filter_supplierID = '';
        if (isset($supplierID) && !empty($supplierID)) {
            $filter_supplierID = " AND po.supplierID = {$supplierID}";
        }
    
        $where = "pod.companyID = " . $companyID . $date . $filter_supplierID;
    
        $results = $this->db->query("SELECT
                                            supplier.supplierAutoID,
                                            supplier.supplierSystemCode,
                                            supplier.supplierName AS supplierName,
                                            po.purchaseOrderID,
                                            po.purchaseOrderCode AS purchaseOrderCode,
                                            pod.purchaseOrderDetailsID,
                                            so.contractAutoID,
                                            so.contractCode AS contractCode,
                                            co.customerOrderID,
                                            co.customerOrderCode AS customerOrderCode,
                                            cus.customerName AS customerName,
                                            pod.commision_value AS commision_value,
                                            po.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                                            SUM( pod.commision_value ) AS commision_total 
                                        FROM
                                            srp_erp_purchaseorderdetails pod
                                            LEFT JOIN srp_erp_purchaseordermaster po ON po.purchaseorderID = pod.purchaseorderID
                                            LEFT JOIN srp_erp_contractmaster so ON so.purchaseorderID = po.purchaseorderID
                                            LEFT JOIN srp_erp_srm_customerordermaster co ON so.customerOrderID = co.customerOrderID
                                            LEFT JOIN srp_erp_customermaster cus ON co.customerID = cus.customerAutoID
                                            LEFT JOIN srp_erp_suppliermaster supplier ON po.supplierID = supplier.supplierAutoID 
                                        WHERE
                                            $where
                                           /* pod.companyID = 505 
                                            AND po.purchaseOrderType = 'BCO' 
                                            AND po.approvedYN = 1 
                                            AND co.isBackToBack = 1 
                                            AND so.approvedYN = 1 */
                                        GROUP BY
                                            pod.purchaseOrderID ")->result_array();

    $organized_records = [];
    foreach ($results as $record) {
        $supplierID = $record['supplierAutoID'];
        $organized_records[$supplierID][] = $record;
    }

    $data['organized_records'] = $organized_records;

    return $data;
    
    }
  
   function save_po_tax_detail(){

        $purchaseDetailID = $this->input->post('purchaseDetailID');
        $taxCalculationformulaID = $this->input->post('taxCalculationID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->where('purchaseOrderDetailsID', $purchaseDetailID );
        $purchaseOrderDetail = $this->db->from('srp_erp_purchaseorderdetails')->get()->row_array();
      

        if($purchaseOrderDetail){

            $tax_applicable_total = (($purchaseOrderDetail['unitAmount']*$purchaseOrderDetail['requestedQty']) - $purchaseOrderDetail['discountAmount']);
          
            $purchaseOrderID = $purchaseOrderDetail['purchaseOrderID'];
           
            $this->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories');
            $this->db->where('taxCalculationformulaID', $taxCalculationformulaID);
            $formulaDtl = $this->db->get('srp_erp_taxcalculationformuladetails')->result_array();

            $total_tax_amount = 0;

            if(!empty($formulaDtl)){
                    foreach($formulaDtl as $val){
                        $sortOrder=$val['sortOrder'];

                        $tax_amount = (($tax_applicable_total) * $val['taxPercentage']) / 100;
                        $total_tax_amount += $tax_amount;

                        
                        $dataleg['documentID'] = 'PO';
                        $dataleg['documentMasterAutoID'] = $purchaseOrderDetail['purchaseOrderID'];
                        $dataleg['taxDetailAutoID'] = $purchaseDetailID;
                        $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                        $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                        $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                        $dataleg['amount'] = $tax_amount;
                        $dataleg['formula'] = $val['formula'];
                      
                        $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                        $dataleg['createdPCID'] = $this->common_data['current_pc'];
                        $dataleg['createdUserID'] = $this->common_data['current_userID'];
                        $dataleg['createdUserName'] = $this->common_data['current_user'];
                        $dataleg['createdDateTime'] = $this->common_data['current_date'];
                        $Dresult= $this->db->insert('srp_erp_taxledger', $dataleg);
                    }
            }

            //update purchase order detail amount
            $data_details = array();

            $data_details['taxCalculationformulaID'] = $taxCalculationformulaID;
            $data_details['taxAmount'] =  $total_tax_amount;

            $this->db->where('purchaseOrderDetailsID',$purchaseDetailID);
            $this->db->update('srp_erp_purchaseorderdetails',$data_details);
    
        }
        $this->session->set_flashdata('s', 'Tax updated successfully');
        return array('status' => true);
    }

    function update_contract_extra_charge(){

        $contractAutoID = $this->input->post('contractAutoID');
        $extraChargeID = $this->input->post('extraChargeID');
        $column = $this->input->post('column');
        $changed_value = $this->input->post('changed_value');
        $id = $this->input->post('id');
        
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseorderextracharges');
        $this->db->where('id', $id);
        $ex_chargeID = $this->db->get()->row_array();

        if($ex_chargeID){

            $ex_chargeID[$column] = $changed_value;
            
            $data = array();

            $data[$column] = $changed_value;
            $data['markup_value'] = (($ex_chargeID['extraCostValue'] * $ex_chargeID['markup_percentage'])/100);
            $data['tax_value'] = (($ex_chargeID['extraCostValue'] * $ex_chargeID['tax_percentage'])/100);
            $data['commission_value'] = (($ex_chargeID['extraCostValue'] * $ex_chargeID['commission_percentage'])/100);
            $data['top_margin_value'] = $data['markup_value'] + $data['tax_value'];
           

            $this->db->where('id',$id)->update('srp_erp_purchaseorderextracharges',$data);

            return array('s', 'Update Successfull');
    
        }


    }

    /**
     * Get purchase order by Id
     *
     * @param integer $poId
     * @return array
     */
    public function getPurchaseOrderById($poId)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', $poId);
        $this->db->where('isDeleted', 0);

        return $this->db->get()->row_array();
    }

    /**
     * Get purchase order detail by Id
     *
     * @param integer $poId
     * @return array
     */
    public function getDetailsByPo($poId)
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if ($secondaryCode  == 1)
        { 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $this->db->select('srp_erp_purchaseorderdetails.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_purchaseorderdetails.`itemDescription` ), `srp_erp_purchaseorderdetails`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,'.$item_code.'');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID', 'left');
        $this->db->where('purchaseOrderID', $poId);
        return $this->db->get()->result_array();
    }

    /**
     * GetPurchaserOrderAddon
     *
     * @param integer $poId
     * @return array
     */
    public function getPurchaseOrderAddon($poId)
    {
        $this->db->select('*');
        $this->db->where('poAutoId', $poId);
        return $this->db->get('srp_erp_grv_addon')->result_array();
    }

    /**
     * Confirm addon
     *
     * @param integer $poId
     * @return array
     */
    public function purchaseOrderAddonConfirmation($poId)
    {
        $purchaseOrder = $this->getPurchaseOrderById($poId);

        if (true === empty($purchaseOrder))
        {
            $this->session->set_flashdata('w', 'Purchase order not found');
            return ['status' => false];
        }

        if (1 == $purchaseOrder['logisticConfirmedYN'])
        {
            $this->session->set_flashdata('w', 'This document is already confirmed');
            return ['status' => false];
        }

        $purchaseOrderAddon = $this->getPurchaseOrderAddon($poId);

        if (true === empty($purchaseOrderAddon))
        {
            $this->session->set_flashdata('w', 'There are no addon records to confirm this document');
            return ['status' => false];
        }

        $data = [
            'logisticConfirmedYN'      => 1,
            'logisticConfirmedDate'    => $this->common_data['current_date'],
            'logisticConfirmedByEmpID' => $this->common_data['current_userID'],
            'logisiticConfirmedByName' => $this->common_data['current_user'],
        ];

        $this->db->where('purchaseOrderID', $poId);
        $this->db->update('srp_erp_purchaseordermaster', $data);

        $this->session->set_flashdata('s', 'Successfully document confirmed');
        
        return ['status' => true];

    }
    
    function fetch_po_details_by_id(){

        $det_id = $this->input->post('det_id');
        
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where('purchaseOrderDetailsID', $det_id);
        return $this->db->get()->row_array();
     

    }

    function save_capitalize_detail_po(){

        $det_id = $this->input->post('det_id');
        $capitalizeDate = $this->input->post('capitalizeDate');
       
        $date_format_policy = date_format_policy();
        
        $format_capitalizeDate = input_format_date($capitalizeDate, $date_format_policy);

        $data['capitalizationDate'] = $format_capitalizeDate;

        $this->db->where('purchaseOrderDetailsID', $det_id);
        $this->db->update('srp_erp_purchaseorderdetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'try again');
        } else {
            $this->db->trans_commit();
            return array('s', 'Update Successfull');
        }


    }

    function open_po_sending_model_max_portal(){

        $poID = $this->input->post('poID');
        $statuspo = $this->input->post('statuspo');
        $res_po = send_approve_po_srm_portal($poID,1);  // 1-po ,2-grv

        if($res_po==true){
            return array('s', 'Update Successfull');
        }else{
            return array('e', 'try again');
        }

    }

    /**
     * Get logistic po addon
     *
     * @param integer $poId
     * @param integer $currentPoId
     * @return array
     */
    public function getLogisticPoAddons($poId, $currentPoId)
    {
        $this->db->select('*,logisticSub.totalMatchedAmount,(srp_erp_grv_addon.bookingCurrencyAmount - IFNULL(logisticSub.totalMatchedAmount, 0)) as logisticBalance');
        $this->db->from('srp_erp_grv_addon');
        $this->db->join('srp_erp_purchase_order_logistic', 'srp_erp_purchase_order_logistic.addonDetailID = srp_erp_grv_addon.id', 'left');
        $this->db->join('(SELECT SUM(matchedAmount) as totalMatchedAmount,addonDetailID FROM srp_erp_purchase_order_logistic GROUP BY addonDetailID) as logisticSub', 'logisticSub.addonDetailID = srp_erp_grv_addon.id', 'left');
        $this->db->where('poAutoID', $poId);
        $this->db->where('(srp_erp_purchase_order_logistic.poMasterID !=' . $currentPoId . ' OR `srp_erp_purchase_order_logistic`.`poMasterID` IS NULL)');
        $this->db->having('logisticBalance > 0');
        return $this->db->get()->result_array();
    }

    /**
     * Fetch po logistic detail
     *
     * @param integer $poAutoID
     * @return array
     */
    private function getPOLogisticDetail($poAutoID)
    {
        $this->db->select('srp_erp_purchase_order_logistic.*,srp_erp_purchaseordermaster.purchaseOrderCode');
        $this->db->where('poMasterID', $poAutoID);
        $this->db->join('srp_erp_grv_addon', 'srp_erp_grv_addon.id = srp_erp_purchase_order_logistic.addonDetailID', 'inner');
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_grv_addon.poAutoID', 'inner');
        return $this->db->get('srp_erp_purchase_order_logistic')->result_array();
    }

    /**
     * Fetch po for logistic
     *
     * @param integer $supplierId
     * @return array
     */
    private function getPOLogistic($supplierId)
    {
        $this->db->select('*');
        $this->db->where('supplierID', $supplierId);
        $this->db->where('logisticYN', 1);
        return $this->db->get('srp_erp_purchaseordermaster')->result_array();
    }

    /**
     * Save logistic po grv addons
     *
     * @param integer $poId
     * @param array $data
     * @return boolean
     */
    public function saveLogisticPoAddon($poId, $data)
    {
        $this->db->trans_start();
        foreach ($data['addonDetailID'] as $key => $val) {

            if ($data['addonBalance'][$key] < $data['matchedAmount'][$key]){
                $this->session->set_flashdata('e', 'Matching amount cannot be greater than balance');
                return false;
            }

            $insertData['poMasterID']           = $poId;
            $insertData['addonDetailID']        = $val;
            $insertData['addonAmount']          = $data['addonAmount'][$key];
            $insertData['addonBalance']         = $data['addonBalance'][$key];
            $insertData['matchedAmount']        = $data['matchedAmount'][$key];
            $insertData['actualLogisticAmount'] = $data['actualLogisticAmount'][$key];
            $insertData['companyID']            = $this->common_data['company_data']['company_id'];
            $insertData['companyCode']          = $this->common_data['company_data']['company_code'];
            $insertData['createdUserGroup']     = $this->common_data['user_group'];
            $insertData['createdPCID']          = $this->common_data['current_pc'];
            $insertData['createdUserID']        = $this->common_data['current_userID'];
            $insertData['createdUserName']      = $this->common_data['current_user'];
            $insertData['createdDateTime']      = $this->common_data['current_date'];

            $this->db->insert('srp_erp_purchase_order_logistic', $insertData);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Date added failed');
            return false;
        } else {
            $this->session->set_flashdata('s', 'Data added Successfully.');
            $this->db->trans_commit();
            return true;
        }

    }

    /**
     * Save logistic po grv addons
     *
     * @param integer $poLogisticID
     * @return array
     */
    public function getLogisticPoAddon($poLogisticID)
    {
        $this->db->select('*');
        $this->db->where('addonDetailID', $poLogisticID);
        return $this->db->get('srp_erp_purchase_order_logistic')->row_array();
    }

    /**
     * Get logistic po grv addons
     *
     * @param integer $logisticId
     * @return array
     */
    public function getLogisticPoAddonByID($logisticId)
    {
        $this->db->select('srp_erp_purchase_order_logistic.*,srp_erp_purchaseordermaster.purchaseOrderCode');
        $this->db->join('srp_erp_grv_addon', 'srp_erp_grv_addon.id = srp_erp_purchase_order_logistic.addonDetailID', 'inner');
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_grv_addon.poAutoID', 'inner');
        $this->db->where('poLogisticID', $logisticId);
        return $this->db->get('srp_erp_purchase_order_logistic')->row_array();
    }

    /**
     * Update logistic po grv addons
     *
     * @param array $data
     * @return boolean
     */
    public function updateLogisticPoAddon($data)
    {
        $this->db->trans_start();

        if ($data['addonBalance'] < $data['matchedAmount']){
            $this->session->set_flashdata('e', 'Matching amount cannot be greater than balance');
            return false;
        }

        $updateData['matchedAmount']        = $data['matchedAmount'];
        $updateData['actualLogisticAmount'] = $data['actualLogisticAmount'];
        $updateData['modifiedPCID']         = $this->common_data['current_pc'];
        $updateData['modifiedUserID']       = $this->common_data['current_userID'];
        $updateData['modifiedUserName']     = $this->common_data['current_user'];
        $updateData['modifiedDateTime']     = $this->common_data['current_date'];

        $this->db->where('poLogisticID', $data['poLogisticID']);
        $this->db->update('srp_erp_purchase_order_logistic', $updateData);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Date update failed');
            return false;
        } else {
            $this->session->set_flashdata('s', 'Data updated Successfully.');
            $this->db->trans_commit();
            return true;
        }

    }

    /**
     * Delete logistic po grv addons
     *
     * @param integer $logisticId
     * @return boolean
     */
    public function deleteLogisticPoAddon($logisticId)
    {
        $this->db->delete('srp_erp_purchase_order_logistic', ['poLogisticID' => $logisticId]);
        return true;
    }

    /**
     * Generate approval
     *
     * @param array $po_data
     * @param integer $locationwisecodegenerate
     * @param integer $currentuser
     * @param integer $companyID
     * @param integer $locationemployee
     * @param integer $approval_type_data
     * @param float $poLocalAmount
     * @return boolean
     */
    private function generateApproval(
        $po_data,
        $locationwisecodegenerate,
        $currentuser,
        $companyID,
        $locationemployee,
        $approval_type_data,
        $poLocalAmount
    )
    {
        $this->load->library('Approvals');

        $purchaseOrderID = $po_data['purchaseOrderID'];
        $docDate = $po_data['documentDate'];

        $Comp = current_companyID();

        $companyFinanceYearID = $this->db->query("SELECT
	                                                              period.companyFinanceYearID as companyFinanceYearID
                                                                  FROM
	                                                              srp_erp_companyfinanceperiod period
                                                                  WHERE
	                                                              period.companyID = $Comp
                                                                  AND '$docDate' BETWEEN period.dateFrom
                                                                  AND period.dateTo")->row_array();

        if (empty($companyFinanceYearID['companyFinanceYearID'])) {
            $companyFinanceYearID['companyFinanceYearID'] = NULL;
        }

        $this->load->library('sequence');
        if ($po_data['purchaseOrderCode'] == "0" || empty($po_data['purchaseOrderCode'])) {
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
                    if ($locationemployee != '') {
                        $codegeratorpo = $this->sequence->sequence_generator_location($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $locationemployee, $po_data['invYear'], $po_data['invMonth']);
                    } else {
                        $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                        return false;
                    }
                }
            } else {
                if($companyFinanceYearID['companyFinanceYearID'] == NULL) {
                    $this->session->set_flashdata('w', 'Financial Year Not generated For this Document Date!');
                    return false;
                } else if($po_data['invYear'] == null) {
                    $this->session->set_flashdata('w', 'Document Year Not Found For this Document!');
                    return false;
                } else if ($po_data['invMonth'] == null){
                    $this->session->set_flashdata('w', 'Document Month Not Found For this Document!');
                    return false;
                } else {
                    $purchaseOrderID = $po_data['purchaseOrderID'];
                    $codegeratorpo = $this->sequence->sequence_generator_fin($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $po_data['invYear'], $po_data['invMonth'], 0,$purchaseOrderID);
                  
                }
            }

            $validate_code = validate_code_duplication($codegeratorpo, 'purchaseOrderCode', $purchaseOrderID,'purchaseOrderID', 'srp_erp_purchaseordermaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }

            $pvCd = array(
                'purchaseOrderCode' => $codegeratorpo
            );
            
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $this->db->update('srp_erp_purchaseordermaster', $pvCd);
        } else {
            $validate_code = validate_code_duplication($po_data['purchaseOrderCode'], 'purchaseOrderCode', $purchaseOrderID,'purchaseOrderID', 'srp_erp_purchaseordermaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }
        }

        $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,generalDiscountPercentage');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster');
        $po_master = $this->db->get()->row_array();

        $autoApproval = get_document_auto_approval('PO');

        if ($autoApproval == 0) {
            $approvalStatus =  $this->approvals->auto_approve(
                $po_master['purchaseOrderID'],
                'srp_erp_purchaseordermaster',
                'purchaseOrderID',
                'PO',
                $po_master['purchaseOrderCode'],
                $po_master['documentDate']
            );
        } elseif ($autoApproval == 1) {
            $approvalStatus = $this->approvals->CreateApproval(
                'PO',
                $po_master['purchaseOrderID'],
                $po_master['purchaseOrderCode'],
                'Purchase Order',
                'srp_erp_purchaseordermaster',
                'purchaseOrderID',
                0,
                $po_master['documentDate'],
                $approval_type_data['segmentID'],
                $poLocalAmount,
                0,null,
                $approval_type_data['itemCategoryID']
            );
        } else {
            $this->session->set_flashdata('e', 'Approval levels are not set for this document');
            return false;
        }

        if ($approvalStatus == 0){
            return false;
        }

        $discountVal = 0;
        $po_total = 0;

        if ('LOG' == $po_data['purchaseOrderType']) {
            $this->db->select('SUM(matchedAmount) AS totalAmount');
            $this->db->where('poMasterID', $purchaseOrderID);
            $po_total = $this->db->get('srp_erp_purchase_order_logistic')->row('totalAmount');
        }

        if ('LOG' !== $po_data['purchaseOrderType']) {
            $this->db->select('(SUM(totalAmount) + IFNULL( SUM(taxAmount),0) ) AS totalAmount');
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $po_total = $this->db->get('srp_erp_purchaseorderdetails')->row('totalAmount');

            if ($po_data['generalDiscountPercentage'] > 0) {
                $discountVal = ($po_data['generalDiscountPercentage'] / 100) * $po_total;
            }

            $this->db->select('totalAmount,purchaseOrderDetailsID');
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $po_dtls = $this->db->get('srp_erp_purchaseorderdetails')->result_array();

            foreach ($po_dtls as $val) {
                $dataD = array(
                    'generalDiscountAmount' => ($val['totalAmount'] / $po_total) * $discountVal,
                );
                $this->db->where('purchaseOrderDetailsID', trim($val['purchaseOrderDetailsID'] ?? ''));
                $this->db->update('srp_erp_purchaseorderdetails', $dataD);
            }


            $this->db->select('totalAmount,purchaseOrderDetailsID');
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $po_dtls = $this->db->get('srp_erp_purchaseorderdetails')->result_array();

            $gentax = $this->db->query("SELECT ifnull(SUM(amount), 0) AS gentaxamount, documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = 'PO' AND documentDetailAutoID is null AND companyID = 13 AND documentMasterAutoID = $purchaseOrderID GROUP BY documentMasterAutoID")->row_array();
            $lineTax=0;
            if(!empty($gentax)){
                foreach ($po_dtls as $val) {
                    $lineTax=($val['totalAmount']/$po_total)*$gentax['gentaxamount'];

                    $dataD = array(
                        'generalTaxAmount' => $lineTax,
                    );
                    $this->db->where('purchaseOrderDetailsID', trim($val['purchaseOrderDetailsID'] ?? ''));
                    $this->db->update('srp_erp_purchaseorderdetails', $dataD);
                }
            }
        }

        $gentaxTot = $this->db->query("SELECT ifnull(SUM(amount), 0) AS gentaxamount, documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = 'PO' AND companyID = 13 AND documentMasterAutoID = $purchaseOrderID GROUP BY documentMasterAutoID")->row_array();
        $taxtotamnt= isset($gentaxTot['gentaxamount']) ? $gentaxTot['gentaxamount'] : 0;

        $autoApproval = get_document_auto_approval('PO');
        if ($autoApproval == 0) {
            $data = array(
                'generalDiscountAmount'  => $discountVal,
                'transactionAmount'      => round(($po_total - $discountVal)+$taxtotamnt, $po_data['transactionCurrencyDecimalPlaces']),
                'companyLocalAmount'     => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyLocalExchangeRate']), $po_data['companyLocalCurrencyDecimalPlaces']),
                'companyReportingAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyReportingExchangeRate']), $po_data['companyReportingCurrencyDecimalPlaces']),
                'supplierCurrencyAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['supplierCurrencyExchangeRate']), $po_data['supplierCurrencyDecimalPlaces']),
                'isReceived'             => 0,
            );
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $result = $this->save_purchase_order_approval(
                0,
                $po_data['purchaseOrderID'],
                1,
                'Auto Approved'
            );
            if ($result) {
                $this->session->set_flashdata('s', 'Approvals Created Successfully');
                return true;
            }
        } else {
            $data = array(
                'confirmedYN'            => 1,
                'approvedYN'             => 0,
                'confirmedDate'          => $this->common_data['current_date'],
                'confirmedByEmpID'       => $this->common_data['current_userID'],
                'confirmedByName'        => $this->common_data['current_user'],
                'generalDiscountAmount'  => $discountVal,
                'transactionAmount'      => round(($po_total - $discountVal)+$taxtotamnt, $po_data['transactionCurrencyDecimalPlaces']),
                'companyLocalAmount'     => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyLocalExchangeRate']), $po_data['companyLocalCurrencyDecimalPlaces']),
                'companyReportingAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyReportingExchangeRate']), $po_data['companyReportingCurrencyDecimalPlaces']),
                'supplierCurrencyAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['supplierCurrencyExchangeRate']), $po_data['supplierCurrencyDecimalPlaces']),
                'isReceived'             => 0,
            );
            $this->db->where('purchaseOrderID', $purchaseOrderID);
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $this->session->set_flashdata('s', 'Approvals Created Successfully');
            return true;
        }

        return false;
    }

}
