<?php

class InvoicesPercentage_model extends ERP_Model
{

    function save_invoice_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $customerDate = $this->input->post('customerInvoiceDate');
        $customerInvoiceDate = input_format_date($customerDate, $date_format_policy);
        $driermasterID = $this->input->post('driermasterID');
        $vehiclemasterID = $this->input->post('vehiclemasterID');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        //$location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        if ($this->input->post('RVbankCode')) {
            $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
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
        $data['documentID'] = 'HCINV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['invoiceDate'] = trim($invoiceDate);
        $data['customerInvoiceDate'] = trim($customerInvoiceDate);
        $data['invoiceDueDate'] = trim($invoiceDueDate);
        $invoiceNarration = ($this->input->post('invoiceNarration'));
        $data['invoiceNarration'] = str_replace('<br />', PHP_EOL, $invoiceNarration);
        $data['invoiceNote'] = trim($this->input->post('invoiceNote') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['showTaxSummaryYN'] = trim($this->input->post('showTaxSummaryYN') ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        $data['warehouseAutoID'] = trim($this->input->post('warehouseAutoIDtemp') ?? '');
        $data['driverID'] = $driermasterID;
        $data['vehicleID'] = $vehiclemasterID;
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }
        // $data['wareHouseCode'] = trim($location[0] ?? '');
        // $data['wareHouseLocation'] = trim($location[1] ?? '');
        // $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['isPrintDN'] = trim($this->input->post('isPrintDN') ?? '');
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

        if (trim($this->input->post('invoiceAutoID') ?? '')) {
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Invoice Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('invoiceAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $policy = getPolicyValues('ATT', 'All');
            $locationwisecodegenerate = getPolicyValues('LDG', 'All');
            $data['taxType'] = $policy;

            $data['invoiceCode'] = 0;
            //if ($data['isPrintDN']==1) {
            $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');
            //}

            $this->db->insert('srp_erp_customerinvoicemaster_temp', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function delete_item_direct()
    {
        $id = $this->input->post('invoiceDetailsAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $this->db->where('invoiceDetailsAutoID', $id);
        $rTmp = $this->db->get()->row_array();


        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['invoiceAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['invoiceDetailsAutoID']);
        $this->db->where('soldDocumentID', 'CINV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $this->db->where('invoiceDetailsAutoID', $id);
        $results = $this->db->delete('srp_erp_customerinvoicedetails_temp');
        if ($results) {
            $this->db->where('documentDetailAutoID', $id);
            $this->db->delete('srp_erp_taxledger');

            $this->session->set_flashdata('s', 'Invoice Detail Deleted Successfully');
            return true;
        }
    }

    function save_invoice_item_detail_buyback()
    {
        $projectExist = project_is_exist();
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $projectID = $this->input->post('projectID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $deductionvalue = $this->input->post('deductionvalue');

        $this->db->trans_start();
        $this->db->select('customerID,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();
        $printID = array();

        $customerCreditLimit = CustomerCreditLimit($master['customerID']);
        $validateID = array();
        $validateAmount = 0;
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            if (!trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerinvoicedetails_temp');
                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
               /* if (!empty($order_detail)) {
                    return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }*/
            }

            if (isset($item_text[$key])) {
                /*$this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();*/

                /*$this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();*/

                /*$this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();*/

                $formulaDetail = $this->db->query(" SELECT formulaDetailID,formula AS formulaString,taxMasters AS  payGroupCategories,taxCalculationformulaID,sortOrder,taxMasterAutoID as taxMsterId FROM srp_erp_taxcalculationformuladetails WHERE taxCalculationformulaID = '" . $item_text[$key] . "' ORDER BY sortOrder ASC ")->result_array();

            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);

            $data['invoiceAutoID'] = trim($invoiceAutoID);
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['requestedDividedQty'] = $quantityRequested[$key];
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $amountafterdiscount = $estimatedAmount[$key] - $data['discountAmount'];
            $data['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            //$data['taxPercentage'] = $item_taxPercentage[$key];
            //$taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
            //$data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
            //$totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
            //$data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
            /*$transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);*/
            $data['comment'] = $comment[$key];
            $data['remarks'] = $remarks[$key];
            $data['type'] = 'Item';
            $item_data = fetch_item_data($data['itemAutoID']);
            $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];

            $data['noOfItems'] = $noOfItems[$key];
            $data['noOfDividedItems'] = $noOfItems[$key];
            $data['grossQty'] = $grossQty[$key];
            $data['grossDividedQty'] = $grossQty[$key];
            $data['noOfUnits'] = $noOfUnits[$key];
            $data['deduction'] = $deductionvalue[$key];
            $data['dividedDeduction'] = $deductionvalue[$key];
            $data['bucketWeightID'] = $deduction[$key];
            if($item_text>0){
                $data['taxMasterAutoID'] = $item_text[$key];
            }else{
                $data['taxMasterAutoID'] = null;
            }
            /*if (!empty($tax_master)) {
                $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                $data['taxDescription'] = $tax_master['taxDescription'];
                $data['taxShortCode'] = $tax_master['taxShortCode'];
                $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                $data['taxSupplierName'] = $tax_master['supplierName'];
                $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                $data['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                $data['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                $data['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                $data['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                $data['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                $data['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
            } else {
                $data['taxSupplierCurrencyExchangeRate'] = 1;
                $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                $data['taxSupplierCurrencyAmount'] = 0;
            }*/

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            if ($invoiceDetailsAutoID) {
                /*$this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                }*/
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails_temp', $data);
                $last_id = $this->db->insert_id();
                $printID[] = $last_id;

                if(!empty($formulaDetail) && $formulaDetail != null){
                    $tottax=0;
                    foreach($formulaDetail as $formul){
                        if(!empty($formul['formulaString'])){
                            $taxCalculationformulaID=$formul['taxCalculationformulaID'];
                            $companyID=current_companyID();
                            $sortOrder=$formul['sortOrder'];
                            //echo '<pre>';print_r($formul); echo '</pre>';
                            $tax_categories = $this->db->query("SELECT
	srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
FROM
	srp_erp_taxcalculationformuladetails
LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
WHERE
	taxCalculationformulaID = $taxCalculationformulaID
AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                            $this->db->select('supplierAutoID,supplierGLAutoID');
                            $this->db->where('taxMasterAutoID', $formul['taxMsterId']);
                            $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                            $formulaBuilder = tax_formulaBuilder_to_sql($formul, $tax_categories,$amountafterdiscount);
                            $formulaDecodeval=$formulaBuilder['formulaDecode'];
                            $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();
                            $datatxledger['documentID']='HCINV';
                            $datatxledger['documentMasterAutoID']=$invoiceAutoID;
                            $datatxledger['documentDetailAutoID']=$last_id;
                            $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                            $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                            $datatxledger['taxFormulaDetailID']=$formul['formulaDetailID'];
                            $datatxledger['taxMasterID']=$formul['taxMsterId'];
                            $datatxledger['amount']=$amounttx['amount']*$quantityRequested[$key];
                            $datatxledger['formula']=$formul['formulaString'];
                            $datatxledger['taxAuthorityAutoID']=$tax_master['supplierAutoID'];
                            $datatxledger['taxGlAutoID']=$tax_master['supplierGLAutoID'];
                            $datatxledger['companyID'] = $this->common_data['company_data']['company_id'];
                            $datatxledger['companyCode'] = $this->common_data['company_data']['company_code'];
                            $datatxledger['createdUserGroup'] = $this->common_data['user_group'];
                            $datatxledger['createdPCID'] = $this->common_data['current_pc'];
                            $datatxledger['createdUserID'] = $this->common_data['current_userID'];
                            $datatxledger['createdUserName'] = $this->common_data['current_user'];
                            $datatxledger['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_taxledger', $datatxledger);
                            $tottax+=$datatxledger['amount'];
                        }
                    }
                    if($tottax>0){
                        $unittax=$tottax/$quantityRequested[$key];
                        $transactionAmount = ($unittax + $amountafterdiscount) * $quantityRequested[$key];
                        $totalAfterTax = $unittax * $data['requestedQty'];
                        $transactionAmount = $transactionAmount - $totalAfterTax;
                        $unitAmount = $estimatedAmount[$key] - $unittax;
                        $datatx['unittransactionAmount'] = round($unitAmount, $master['transactionCurrencyDecimalPlaces']);
                        $datatx['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                   //     $datatx['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                        $datatx['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                        $companyLocalAmount = $datatx['transactionAmount'] / $master['companyLocalExchangeRate'];
                        $datatx['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                        $datatx['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                        $companyReportingAmount = $datatx['transactionAmount'] / $master['companyReportingExchangeRate'];
                        $datatx['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                        $datatx['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                        $customerAmount = $datatx['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                        $datatx['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                        $datatx['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                        $datatx['taxAmount'] = round($unittax, $master['transactionCurrencyDecimalPlaces']);

                        $datatx['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
                        $datatx['dividedTotalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);

                        $this->db->where('invoiceDetailsAutoID', trim($last_id));
                        $this->db->update('srp_erp_customerinvoicedetails_temp', $datatx);

                        $validateAmount += $datatx['companyLocalAmount'];
                    }else{
                       // $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
                        $transactionAmount = ($amountafterdiscount) * $quantityRequested[$key];
                        $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                        $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                        $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
                        $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                        $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                        $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
                        $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                        $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                        $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                        $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                        $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                        $this->db->where('invoiceDetailsAutoID', trim($last_id));
                        $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);

                        $validateAmount += $transdata['companyLocalAmount'];
                    }
                }else{
                    // $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
                    $transactionAmount = ($amountafterdiscount) * $quantityRequested[$key];
                    $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                    $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                    $this->db->where('invoiceDetailsAutoID', trim($last_id));
                    $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);
                    $validateAmount += $transdata['companyLocalAmount'];
                }

                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouseitems)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['defaultUOMID'],
                            'unitOfMeasure' => $data['defaultUOM'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }
        }
//var_dump($customerCreditLimit['amount']. '  ' . $validateAmount);
        if($customerCreditLimit['assigned'] == 1 && $customerCreditLimit['amount'] < $validateAmount){
            foreach ($printID as $key){
                $this->db->delete('srp_erp_customerinvoicedetails_temp', array('invoiceDetailsAutoID' => trim($key)));
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('e', 'Customer Credit Limit Exceeded.');
            }
        } else {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : Saved Successfully.', $printID);
            }
        }
    }

    function update_invoice_item_detail()
    {
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $projectID = $this->input->post('projectID');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount_amount = $this->input->post('discount_amount');
        $discount = $this->input->post('discount');
        $projectExist = project_is_exist();

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $tax_master = array();
        if (!empty($this->input->post('invoiceDetailsAutoID'))) {
            $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('invoiceDetailsAutoID !=', $invoiceDetailsAutoID);
            $order_detail = $this->db->get()->row_array();
            /*if (!empty($order_detail)) {
                return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }*/
        }

        if (isset($item_text)) {
            $this->db->select('*');
            $this->db->where('taxMasterAutoID', $item_text);
            $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

            $this->db->select('*');
            $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
            $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();

            $formulaDetail = $this->db->query(" SELECT formulaDetailID,formula AS formulaString,taxMasters AS  payGroupCategories,taxCalculationformulaID,sortOrder,taxMasterAutoID as taxMsterId FROM srp_erp_taxcalculationformuladetails WHERE taxCalculationformulaID = '" . $item_text . "' ORDER BY sortOrder ASC ")->result_array();
        }

        $wareHouse_location = explode('|', $wareHouse);
        $item_arr = fetch_item_data($itemAutoID);
        $uomEx = explode('|', $uom);

        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['projectID'] = $projectID;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = $quantityRequested;
        $data['requestedDividedQty'] = $quantityRequested;
        $data['discountPercentage'] = $discount;
        $data['discountAmount'] = $discount_amount;
        $amountafterdiscount = $estimatedAmount - $discount_amount;
        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        if($item_text>0){
            $data['taxMasterAutoID'] = $item_text;
        }else{
            $data['taxMasterAutoID'] = null;
        }
       /* $data['taxPercentage'] = $item_taxPercentage;
        $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
        $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
        $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
        $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
        $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested;
        $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);*/
        $data['comment'] = $comment;
        $data['remarks'] = $remarks;
        $data['type'] = 'Item';
        $item_data = fetch_item_data($data['itemAutoID']);
        $data['wareHouseAutoID'] = $wareHouseAutoID;
        $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
        $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
        $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['itemCategory'] = $item_data['mainCategory'];

        /*if (!empty($tax_master)) {
            $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
            $data['taxDescription'] = $tax_master['taxDescription'];
            $data['taxShortCode'] = $tax_master['taxShortCode'];
            $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
            $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
            $data['taxSupplierName'] = $tax_master['supplierName'];
            $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
            $data['taxSupplierCurrency'] = $tax_master['supplierCurrency'];
            $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['supplierCurrencyDecimalPlaces'];
            $data['taxSupplierliabilityAutoID'] = $tax_master['supplierGLAutoID'];
            $data['taxSupplierliabilitySystemGLCode'] = $tax_master['supplierGLSystemGLCode'];
            $data['taxSupplierliabilityGLAccount'] = $tax_master['supplierGLAccount'];
            $data['taxSupplierliabilityDescription'] = $tax_master['supplierGLDescription'];
            $data['taxSupplierliabilityType'] = $tax_master['supplierGLType'];
            $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
            $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
            $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
            $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
        } else {
            $data['taxSupplierCurrencyExchangeRate'] = 1;
            $data['taxSupplierCurrencyDecimalPlaces'] = 2;
            $data['taxSupplierCurrencyAmount'] = 0;
        }*/

        $data['noOfItems'] = $this->input->post('noOfItems');
        $data['noOfDividedItems'] = $this->input->post('noOfItems');
        $data['grossQty'] = $this->input->post('grossQty');
        $data['grossDividedQty'] = $this->input->post('grossQty');
        $data['noOfUnits'] = $this->input->post('noOfUnits');
        $data['deduction'] = $this->input->post('deductionvalue');
        $data['dividedDeduction'] = $this->input->post('deductionvalue');
        $data['bucketWeightID'] = $this->input->post('deduction');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }
        $this->db->where('documentDetailAutoID', $invoiceDetailsAutoID);
        $this->db->delete('srp_erp_taxledger');
        if(!empty($formulaDetail) && $formulaDetail != null){
            $tottax=0;
            foreach($formulaDetail as $formul){
                if(!empty($formul['formulaString'])){
                    $taxCalculationformulaID=$formul['taxCalculationformulaID'];
                    $companyID=current_companyID();
                    $sortOrder=$formul['sortOrder'];
                    //echo '<pre>';print_r($formul); echo '</pre>';
                    $tax_categories = $this->db->query("SELECT
	srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
FROM
	srp_erp_taxcalculationformuladetails
LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
WHERE
	taxCalculationformulaID = $taxCalculationformulaID
AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                    $this->db->select('supplierAutoID,supplierGLAutoID');
                    $this->db->where('taxMasterAutoID', $formul['taxMsterId']);
                    $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                    $formulaBuilder = tax_formulaBuilder_to_sql($formul, $tax_categories,$amountafterdiscount);
                    $formulaDecodeval=$formulaBuilder['formulaDecode'];
                    $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();
                    $datatxledger['documentID']='HCINV';
                    $datatxledger['documentMasterAutoID']=$invoiceAutoID;
                    $datatxledger['documentDetailAutoID']=$invoiceDetailsAutoID;
                    $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                    $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                    $datatxledger['taxFormulaDetailID']=$formul['formulaDetailID'];
                    $datatxledger['taxMasterID']=$formul['taxMsterId'];
                    $datatxledger['taxAuthorityAutoID']=$tax_master['supplierAutoID'];
                    $datatxledger['taxGlAutoID']=$tax_master['supplierGLAutoID'];
                    $datatxledger['amount']=$amounttx['amount']*$quantityRequested;
                    $datatxledger['formula']=$formul['formulaString'];
                    $datatxledger['companyID'] = $this->common_data['company_data']['company_id'];
                    $datatxledger['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datatxledger['createdUserGroup'] = $this->common_data['user_group'];
                    $datatxledger['createdPCID'] = $this->common_data['current_pc'];
                    $datatxledger['createdUserID'] = $this->common_data['current_userID'];
                    $datatxledger['createdUserName'] = $this->common_data['current_user'];
                    $datatxledger['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_taxledger', $datatxledger);
                    $tottax+=$datatxledger['amount'];
                }
            }
            if($tottax>0){
                $unittax=$tottax/$quantityRequested;
                $totalAfterTax = $unittax * $data['requestedQty'];
                $transactionAmount = ($unittax + $amountafterdiscount) * $quantityRequested;
                $transactionAmount = $transactionAmount - $totalAfterTax;
                $estiUnitAmount = $estimatedAmount - $unittax;
                $data['unittransactionAmount'] = round($estiUnitAmount, $master['transactionCurrencyDecimalPlaces']);
                $datatx['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $datatx['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $datatx['transactionAmount'] / $master['companyLocalExchangeRate'];
                $datatx['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $datatx['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $datatx['transactionAmount'] / $master['companyReportingExchangeRate'];
                $datatx['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $datatx['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $datatx['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $datatx['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                $datatx['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $datatx['taxAmount'] = round($unittax, $master['transactionCurrencyDecimalPlaces']);
                $datatx['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
                $datatx['dividedTotalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);

                $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $datatx);
            }else{
                // $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
                $transactionAmount = ($amountafterdiscount) * $quantityRequested;
                $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
                $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
                $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);
            }
        }else{
            // $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            $transactionAmount = ($amountafterdiscount) * $quantityRequested;
            $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
            $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
            $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $transdata['taxAmount'] = 0;
            $transdata['totalAfterTax'] =0;
            $transdata['dividedTotalAfterTax'] =0;
            $transdata['taxMasterAutoID'] = null;

            $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
            $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);
        }

        if ($invoiceDetailsAutoID) {
            $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
            $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_invoice_template_data($invoiceAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID=current_companyID();
        $currentdate = $this->common_data['current_date'];
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $datefromconvert = input_format_date($currentdate, $date_format_policy);

        $dayClosed = $this->db->query("SELECT isDayClosed FROM srp_erp_customerinvoicemaster_temp WHERE companyID = {$companyID} AND invoiceAutoID = $invoiceAutoID")->row_array();
        $data['isDayClosed'] = $dayClosed['isDayClosed'];
        if($dayClosed['isDayClosed'] == 0){
            $this->db->select('srp_erp_customerinvoicemaster_temp.*,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,IFNULL(confirmedByName,\'-\') as confirmedYNn, SalesPersonName');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $this->db->join('srp_erp_salespersonmaster','srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster_temp.salesPersonID', 'LEFT');
            $data['master'] = $this->db->get()->row_array();
            $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        } else {
            $this->db->select('*, isPrintDN as printTagYN, wareHouseAutoID as warehouseAutoID,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,IFNULL(confirmedByName,\'-\') as confirmedYNn ');
            $this->db->where('tempInvoiceID', $invoiceAutoID);
            $this->db->from('srp_erp_customerinvoicemaster');
            $data['master'] = $this->db->get()->row_array();
            $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        }

        $this->db->select('wareHouseCode,wareHouseDescription');
        $this->db->where('warehouseAutoID', $data['master']['warehouseAutoID']);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_warehousemaster');
        $data['warehouse'] = $this->db->get()->row_array();

        if(!empty($data['master']['driverID'])){
            $this->db->select('driverName');
            $this->db->where('driverMasID', $data['master']['driverID']);
            $this->db->where('companyID', $companyID);
            $this->db->from('fleet_drivermaster');
            $data['driver'] = $this->db->get()->row_array();
        }

        if(!empty($data['master']['vehicleID'])) {
            $this->db->select('VehicleNo');
            $this->db->where('vehicleMasterID', $data['master']['vehicleID']);
            $this->db->where('companyID', $companyID);
            $this->db->from('fleet_vehiclemaster');
            $data['vehicle'] = $this->db->get()->row_array();
        }

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        if($dayClosed['isDayClosed'] == 0){
            $this->db->select('srp_erp_customerinvoicedetails_temp.*, IFNULL(taxAmount,0) AS taxAmount, srp_erp_itemmaster.seconeryItemCode');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails_temp.itemAutoID', 'LEFT');
            $data['item_detail'] = $this->db->get()->result_array();

            $this->db->select('COUNT(srp_erp_customerinvoicedetails_temp.itemAutoID)');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails_temp.itemAutoID', 'LEFT');
            $this->db->group_by('srp_erp_customerinvoicedetails_temp.itemAutoID');
            $data['item_count'] = $this->db->get()->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $data['buyback_detail_delivery'] = $this->db->get()->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'GL');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $data['gl_detail'] = $this->db->get()->result_array();
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails_temp')->result_array();
        } else{
            $this->db->select('srp_erp_customerinvoicedetails.*, srp_erp_itemmaster.seconeryItemCode');
            $this->db->where('invoiceAutoID',  $data['master']['invoiceAutoID']);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID', 'LEFT');
            $data['item_detail'] = $this->db->get()->result_array();

            $this->db->select('COUNT(srp_erp_customerinvoicedetails.itemAutoID)');
            $this->db->where('invoiceAutoID',  $data['master']['invoiceAutoID']);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID', 'LEFT');
            $this->db->group_by('srp_erp_customerinvoicedetails.itemAutoID');
            $data['item_count'] = $this->db->get()->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $data['master']['invoiceAutoID']);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails');
            $data['buyback_detail_delivery'] = $this->db->get()->result_array();


            $this->db->select('*');
            $this->db->where('invoiceAutoID', $data['master']['invoiceAutoID']);
            $this->db->where('type', 'GL');
            $this->db->from('srp_erp_customerinvoicedetails');
            $data['gl_detail'] = $this->db->get()->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $data['master']['invoiceAutoID']);
            $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        }

        $data['taxledger'] = $this->db->query("SELECT
	tax.taxDescription,tax.taxShortCode,srp_erp_taxledger.taxMasterID,SUM(srp_erp_taxledger.amount)as amount
FROM
	`srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax on srp_erp_taxledger.taxMasterID=tax.taxMasterAutoID
WHERE
	documentMasterAutoID = $invoiceAutoID
AND	documentID = 'HCINV'
AND srp_erp_taxledger.companyID = $companyID

GROUP BY srp_erp_taxledger.taxMasterID ")->result_array();

        if($dayClosed['isDayClosed'] == 0){
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->result_array();
        } else{
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $data['master']['invoiceAutoID']);
            $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

            $this->db->select('*');
            $this->db->where('invoiceAutoID', $data['master']['invoiceAutoID']);
            $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();
        }


        /*Outstanding Amout For buyback invoice print*/
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
	AND srp_erp_customermaster.companyID = $companyID
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID
	AND srp_erp_chartofaccounts.companyID = $companyID
	LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode
	AND srp_erp_documentcodemaster.companyID = $companyID
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_generalledger.transactionCurrencyID )
WHERE
	( srp_erp_generalledger.partyAutoID = '{$data['master']['customerID']}')
	AND srp_erp_generalledger.documentDate <= '$datefromconvert'
	AND srp_erp_generalledger.companyID = $companyID
GROUP BY
	srp_erp_generalledger.partyAutoID
	) a")->row_array();

        $item_totSize = count($data['item_count']);
        $extracharge_totSize = count($data['extracharge']);
        if(!empty($extracharge_totSize)) {
            $item_totSize = $item_totSize + $extracharge_totSize + 2;
        }

        $gl_totSize = count($data['gl_detail']);
        if(!empty($gl_totSize)) {
            $item_totSize = $item_totSize + $gl_totSize + 2;
        }

        $discount_totSize = count($data['discount']);
        $taxLedger_totSize = count($data['taxledger']);
        if(!empty($discount_totSize) || !empty($taxLedger_totSize)) {
            if($discount_totSize > $taxLedger_totSize) {
                $item_totSize = $item_totSize + $discount_totSize + 2;
            } else {
                $item_totSize = $item_totSize + $taxLedger_totSize + 2;
            }
        }

        if ($item_totSize < 6) {
            $data['printSize'] = 'A5-L';
        } else {
            $data['printSize'] = 'A4';
        }
        $tax_totSize = count($data['tax']);

        return $data;
    }

    function fetch_dayClosed_invoice_data($invoiceAutoID)
    {
        $companyID=current_companyID();
        $currentdate = $this->common_data['current_date'];
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $datefromconvert = input_format_date($currentdate, $date_format_policy);

        $data['isDayClosed'] = 1;
        $this->db->select('*,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,IFNULL(confirmedByName,\'-\') as confirmedYNn ');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster_temp');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('wareHouseCode,wareHouseDescription');
        $this->db->where('warehouseAutoID', $data['master']['warehouseAutoID']);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_warehousemaster');
        $data['warehouse'] = $this->db->get()->row_array();

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $data['item_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $data['buyback_detail_delivery'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $data['gl_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails_temp')->result_array();

        $data['taxledger'] = $this->db->query("SELECT
	tax.taxDescription,tax.taxShortCode,srp_erp_taxledger.taxMasterID,SUM(srp_erp_taxledger.amount)as amount
FROM
	`srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax on srp_erp_taxledger.taxMasterID=tax.taxMasterAutoID
WHERE
	documentMasterAutoID = $invoiceAutoID
AND	documentID = 'HCINV'
AND srp_erp_taxledger.companyID = $companyID

GROUP BY srp_erp_taxledger.taxMasterID ")->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->result_array();

        /*Outstanding Amout For buyback invoice print*/
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
	AND srp_erp_customermaster.companyID = $companyID
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID
	AND srp_erp_chartofaccounts.companyID = $companyID
	LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode
	AND srp_erp_documentcodemaster.companyID = $companyID
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_generalledger.transactionCurrencyID )
WHERE
	( srp_erp_generalledger.partyAutoID = '{$data['master']['customerID']}')
	AND srp_erp_generalledger.documentDate <= '$datefromconvert'
	AND srp_erp_generalledger.companyID = $companyID
GROUP BY
	srp_erp_generalledger.partyAutoID
	) a")->row_array();

        return $data;
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

    function load_invoice_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        return $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();
    }

    function fetch_invoice_direct_details()
    {
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,invoiceType');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['currency'] = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();
        $this->db->select('srp_erp_customerinvoicedetails_temp.*, IFNULL(taxAmount,0) AS taxAmount, srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails_temp.itemAutoID', 'left');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicetaxdetails_temp');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['tax_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['discount_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['extraChargeDetail'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->result_array();

        return $data;
    }

    function fetch_detail()
    {
        $data = array();
        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails_temp')->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['extraCharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->result_array();
        return $data;
    }

    function save_direct_invoice_detail_buyback()
    {
        $this->db->trans_start();
        $this->db->select('customerID,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $projectExist = project_is_exist();
        $segment_gls = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $gl_code = $this->input->post('gl_code');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');

        $customerCreditLimit = CustomerCreditLimit($master['customerID']);
        $totalLocalAmtValidate = 0;
        foreach ($segment_gls as $key => $segment_gl) {
            $segment = explode('|', $segment_gl);
            $gl_code_de = explode(' | ', $gl_code_des[$key]);
            $data[$key]['invoiceAutoID'] = trim($invoiceAutoID);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['revenueGLAutoID'] = $gl_code[$key];
            $data[$key]['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
            $data[$key]['revenueGLCode'] = trim($gl_code_de[1] ?? '');
            $data[$key]['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
            $data[$key]['revenueGLType'] = trim($gl_code_de[3] ?? '');
            $data[$key]['segmentID'] = trim($segment[0] ?? '');
            $data[$key]['segmentCode'] = trim($segment[1] ?? '');
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['tranasactionDividedAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = (float)$data[$key]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['type'] = 'GL';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                /*$this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
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
                $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            }

            $totalLocalAmtValidate = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        }

        if($customerCreditLimit['assigned'] == 1 && $customerCreditLimit['amount'] < $totalLocalAmtValidate){
            $this->session->set_flashdata('e', 'Customer Credit Limit Exceeded');
            return array('status' => false);
        } else {
            $this->db->insert_batch('srp_erp_customerinvoicedetails_temp', $data);

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
    }

    function update_income_invoice_detail()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID = $this->input->post('projectID');
        $gl_code = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectExist = project_is_exist();

        $segment = explode('|', $segment_gl);
        $gl_code_de = explode(' | ', $gl_code_des);
        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['revenueGLAutoID'] = $gl_code;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
        $data['revenueGLCode'] = trim($gl_code_de[1] ?? '');
        $data['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
        $data['revenueGLType'] = trim($gl_code_de[3] ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['transactionAmount'] = round($amount, $master['transactionCurrencyDecimalPlaces']);
        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $data['description'] = trim($description);
        $data['type'] = 'GL';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_customer_invoice_detail()
    {
        $this->db->select('srp_erp_customerinvoicedetails_temp.*,srp_erp_customerinvoicemaster_temp.invoiceType,srp_erp_itemmaster.currentStock, SUM((unittransactionAmount) + IFNULL(taxAmount, 0)) as unittransactionAmountANDtax');
        $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
        $this->db->join('srp_erp_customerinvoicemaster_temp', 'srp_erp_customerinvoicedetails_temp.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_customerinvoicedetails_temp.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        return $this->db->get()->row_array();
    }

    function invoice_confirmation()
    {
        $this->db->trans_start();
        $total_amount = 0;
        $tax_total = 0;
        $t_arr = array();
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationemployee = $this->common_data['emplanglocationid'];
        $this->db->select('invoiceDetailsAutoID');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        }
        else
        {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $this->load->library('approvals');

                $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                $this->db->from('srp_erp_customerinvoicemaster_temp');
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
                        if ((empty($location)) || ($location =='')) {
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

                    $validate_code = validate_code_duplication($codegerator, 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster_temp');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $invcod = array(
                        'invoiceCode' => $codegerator,
                    );
                    $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                    $this->db->update('srp_erp_customerinvoicemaster_temp', $invcod);
                } else {
                    $validate_code = validate_code_duplication($master_dt['invoiceCode'], 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster_temp');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->db->select('invoiceAutoID,invoiceDate,invoiceCode, documentID,transactionCurrency, transactionExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,customerCurrencyExchangeRate');
                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                $this->db->from('srp_erp_customerinvoicemaster_temp');
                $master_data = $this->db->get()->row_array();

                $sql = "SELECT (srp_erp_customerinvoicedetails_temp.requestedQty / srp_erp_customerinvoicedetails_temp.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock-(srp_erp_customerinvoicedetails_temp.requestedQty / srp_erp_customerinvoicedetails_temp.conversionRateUOM)) as stock ,srp_erp_warehouseitems.itemAutoID,srp_erp_customerinvoicedetails_temp.wareHouseAutoID FROM srp_erp_customerinvoicedetails_temp INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerinvoicedetails_temp.itemAutoID AND srp_erp_customerinvoicedetails_temp.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where invoiceAutoID = '{$this->input->post('invoiceAutoID')}' AND itemCategory != 'Service'   Having stock < 0";
                $item_low_qty = $this->db->query($sql)->result_array();
               /* if (!empty($item_low_qty)) {
                    return array('e', 'Some Item quantities are not sufficient to confirm this transaction.',$item_low_qty);
                }*/

                $approvals_status = $this->approvals->CreateApproval($master_data['documentID'], $master_data['invoiceAutoID'], $master_data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster_temp', 'invoiceAutoID',0,$master_data['invoiceDate']);
                if ($approvals_status == 1) {

                    /** item Master Sub check */
                    $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
                    $validate = $this->validate_itemMasterSub($invoiceAutoID);

                    /** end of item master sub */
                    if ($validate) {
                        $this->db->select_sum('transactionAmount');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $transaction_total_amount = $this->db->get('srp_erp_customerinvoicedetails_temp')->row('transactionAmount');

                        $this->db->select_sum('totalAfterTax');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $item_tax = $this->db->get('srp_erp_customerinvoicedetails_temp')->row('totalAfterTax');
                        $total_amount = ($transaction_total_amount - $item_tax);
                        $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate ,companyLocalExchangeRate ,taxPercentage');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $tax_arr = $this->db->get('srp_erp_customerinvoicetaxdetails_temp')->result_array();
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
                        $invautoid=$this->input->post('invoiceAutoID');
                        $r1 = "SELECT
	`srp_erp_customerinvoicemaster_temp`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster_temp`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_customerinvoicemaster_temp`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster_temp`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_customerinvoicemaster_temp`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster_temp`.`customerCurrencyExchangeRate` AS `customerCurrencyExchangeRate`,
	`srp_erp_customerinvoicemaster_temp`.`customerCurrencyDecimalPlaces` AS `customerCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster_temp`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,

	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value

FROM
	`srp_erp_customerinvoicemaster_temp`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails_temp
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster_temp.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails_temp
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster_temp.InvoiceAutoID
)
WHERE
	`companyID` = $companyID
and srp_erp_customerinvoicemaster_temp.invoiceAutoID= $invautoid ";
                        $totalValue = $this->db->query($r1)->row_array();



                        $sumdetail = $this->db->query("SELECT
	SUM(transactionAmount) as tottransactionAmount,SUM(totalAfterTax) as totalAfterTax
FROM
	srp_erp_customerinvoicedetails_temp
WHERE
	invoiceAutoID = $invoiceAutoID")->row_array();

                        $this->db->select('*');
                        $this->db->where('invoiceAutoID', $invoiceAutoID);
                        $discountDetails = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->result_array();

                        if($discountDetails){
                            foreach($discountDetails as $val){

                                $tarnsdisc= ($sumdetail['tottransactionAmount']*$val['discountPercentage'])/100;
                                $custsdisc= (($sumdetail['tottransactionAmount']/$val['customerCurrencyExchangeRate'])*$val['discountPercentage'])/100;
                                $localsdisc= (($sumdetail['tottransactionAmount']/$val['companyLocalExchangeRate'])*$val['discountPercentage'])/100;
                                $reportsdisc= (($sumdetail['tottransactionAmount']/$val['companyReportingExchangeRate'])*$val['discountPercentage'])/100;
                                $disc['transactionAmount'] = $tarnsdisc;
                                $disc['transactionDividedAmount'] = $tarnsdisc;
                                $disc['customerCurrencyAmount'] = $custsdisc;
                                $disc['customerCurrencyDividedAmount'] = $custsdisc;
                                $disc['companyLocalAmount'] = $localsdisc;
                                $disc['companyLocalDividedAmount'] = $localsdisc;
                                $disc['companyReportingAmount']          =  $reportsdisc;
                                $disc['companyReportingDividedAmount']   =  $reportsdisc;

                                $this->db->where('discountDetailID', $val['discountDetailID']);
                                $this->db->update('srp_erp_customerinvoicediscountdetails_temp', $disc);
                            }
                        }

                        $discdetail = $this->db->query("SELECT
	SUM(transactionAmount) as tottransactionAmount,
	SUM(customerCurrencyAmount) as customerCurrencyAmount,
	SUM(companyLocalAmount) as companyLocalAmount,
	SUM(companyReportingAmount) as companyReportingAmount
FROM
	srp_erp_customerinvoicediscountdetails_temp
WHERE
	invoiceAutoID = $invoiceAutoID")->row_array();
                        if(!empty($discdetail['tottransactionAmount'])){
                            $discTransAmount=$discdetail['tottransactionAmount'];
                            $disccustomerAmount=$discdetail['customerCurrencyAmount'];
                            $dislocalAmount=$discdetail['companyLocalAmount'];
                            $disreportAmount=$discdetail['companyReportingAmount'];
                        }else{
                            $discTransAmount=0;
                            $disccustomerAmount=0;
                            $dislocalAmount=0;
                            $disreportAmount=0;
                        }

                        $extradetail = $this->db->query("SELECT
	SUM(transactionAmount) as tottransactionAmount,
	SUM(customerCurrencyAmount) as customerCurrencyAmount,
	SUM(companyLocalAmount) as companyLocalAmount,
	SUM(companyReportingAmount) as companyReportingAmount
FROM
	srp_erp_customerinvoiceextrachargedetails_temp
WHERE
	invoiceAutoID = $invoiceAutoID")->row_array();
                        if(!empty($extradetail['tottransactionAmount'])){
                            $extraTransAmount=$extradetail['tottransactionAmount'];
                            $extracustomerAmount=$extradetail['customerCurrencyAmount'];
                            $extralocalAmount=$extradetail['companyLocalAmount'];
                            $extrareportAmount=$extradetail['companyReportingAmount'];
                        }else{
                            $extraTransAmount=0;
                            $extracustomerAmount=0;
                            $extralocalAmount=0;
                            $extrareportAmount=0;
                        }


                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => (round(($totalValue['total_value']-$discTransAmount)+$extraTransAmount,$totalValue['transactionCurrencyDecimalPlaces'])),
                            'companyLocalAmount' => (round((($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'])-$dislocalAmount)+$extralocalAmount,$totalValue['companyLocalCurrencyDecimalPlaces'])),
                            'companyReportingAmount' => (round((($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'])-$disreportAmount)+$extrareportAmount,$totalValue['companyReportingCurrencyDecimalPlaces'])),
                            'customerCurrencyAmount' => (round((($totalValue['total_value'] / $totalValue['customerCurrencyExchangeRate'])-$disccustomerAmount)+$extracustomerAmount,$totalValue['customerCurrencyDecimalPlaces'])),
                            'transactionDividedAmount' => (round(($totalValue['total_value']-$discTransAmount)+$extraTransAmount,$totalValue['transactionCurrencyDecimalPlaces'])),
                            'companyLocalDividedAmount' => (round((($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'])-$dislocalAmount)+$extralocalAmount,$totalValue['companyLocalCurrencyDecimalPlaces'])),
                            'companyReportingDividedAmount' => (round((($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'])-$disreportAmount)+$extrareportAmount,$totalValue['companyReportingCurrencyDecimalPlaces'])),
                        );

                        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                        $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
                        if (!empty($t_arr)) {
                            $this->db->update_batch('srp_erp_customerinvoicetaxdetails_temp', $t_arr, 'taxDetailAutoID');
                        }
                    } else {
                        return array('e', 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                        /*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*//*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*/
                        exit;
                    }
                } else if ($approvals_status == 3) {
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
                //$this->session->set_flashdata('s', 'Supplier Invoice Detail : ' . $data['GLDescription']. ' Saved Successfully.');
                $this->db->trans_commit();
                return array('s', 'Document confirmed successfully');

                //return array('status' => true);
            }
        }
    }

    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_customerinvoicemaster_temp cinv
                    LEFT JOIN srp_erp_customerinvoicedetails_temp cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = cinvDetail.invoiceDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();

        $query2 = "SELECT
                        SUM(cinvDetail.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerinvoicemaster_temp cinv
                    LEFT JOIN srp_erp_customerinvoicedetails_temp cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
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

    function fetch_customer_con($master)
    {
        $customerID = $master['customerID'];
        $currencyID = $master['transactionCurrencyID'];
        $contractType = $master['invoiceType'];
        $invoiceAutoID = $master['invoiceAutoID'];
        //$invoiceDate    = format_date($master['invoiceDate']);
        //$contractExp    = $master['contractExpDate'];


        $data = $this->db->query("SELECT srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.contractCode, srp_erp_contractmaster.contractDate FROM srp_erp_contractdetails INNER JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customerinvoicedetails_temp ON srp_erp_customerinvoicedetails_temp.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID WHERE `customerID` = '{$customerID}' AND `contractType` = '{$contractType}' AND `transactionCurrencyID` = '{$currencyID}' AND `confirmedYN` = 1 AND `closedYN` = 0 AND srp_erp_contractdetails.invoicedYN = 0 AND  `approvedYN` = 1 AND srp_erp_contractmaster.contractAutoID NOT IN ( SELECT ifnull(contractAutoID,0) as contractAutoID FROM srp_erp_customerinvoicedetails_temp WHERE invoiceAutoID != $invoiceAutoID  GROUP BY contractAutoID )  GROUP BY srp_erp_contractmaster.contractCode")->result_array();
        //AND '{$invoiceDate}' BETWEEN contractDate AND contractExpDate
        return $data;
    }

    function fetch_con_detail_table()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('srp_erp_contractdetails.*,sum(srp_erp_customerinvoicedetails_temp.requestedQty) AS receivedQty,srp_erp_contractdetails.itemAutoID as itemAutoID');
        $this->db->where('srp_erp_contractdetails.contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $this->db->where('invoicedYN', 0);
        $this->db->from('srp_erp_contractdetails');
        $this->db->join('srp_erp_customerinvoicedetails_temp', 'srp_erp_customerinvoicedetails_temp.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID', 'left');
        $this->db->group_by("contractDetailsAutoID");
        $data['detail'] = $this->db->get()->result_array();

        $this->db->SELECT("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID,wareHouseLocation");
        $this->db->FROM('srp_erp_warehousemaster');
        $this->db->WHERE('companyID', $companyID);
        $data['ware_house'] = $this->db->get()->result_array();
        $data['tax_master'] = all_tax_drop(1, 0);

        $this->db->SELECT("weightAutoID,bucketWeight");
        $this->db->FROM('srp_erp_buyback_bucketweight');
        $this->db->WHERE('companyID', $companyID);
        $data['bucketweightdrop'] =  $this->db->get()->result_array();

        return $data;
    }

    function save_con_base_items()
    {
        $this->db->trans_start();
        $this->db->select('srp_erp_contractdetails.contractAutoID');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where_in('srp_erp_contractdetails.contractDetailsAutoID', $this->input->post('DetailsID'));
        $contractID = $this->db->get()->row_array();

        $items_arr = array();
        $this->db->select('srp_erp_contractdetails.*,sum(srp_erp_customerinvoicedetails_temp.requestedQty) AS receivedQty,srp_erp_contractmaster.contractCode');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where_in('srp_erp_contractdetails.contractDetailsAutoID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID');
        $this->db->join('srp_erp_customerinvoicedetails_temp', 'srp_erp_customerinvoicedetails_temp.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID', 'left');
        $this->db->group_by("contractDetailsAutoID");
        $query = $this->db->get()->result_array();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
        $this->db->from('srp_erp_customerinvoicemaster_temp');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();


        $this->db->select('srp_erp_warehousemaster.warehouseAutoID,wareHouseCode,wareHouseDescription,wareHouseLocation');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->join('srp_erp_warehousemaster','srp_erp_warehousemaster.wareHouseAutoID = srp_erp_customerinvoicemaster_temp.wareHouseAutoID');
        $wareHouseDetails = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
      //  $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $whrehouse = $this->input->post('whrehouse');
        $tex_id = $this->input->post('tex_id');
        $tex_percntage = $this->input->post('tex_percntage');
        $remarks = $this->input->post('remarks');

        $noofitems = $this->input->post('noofitems');
        $grossqty = $this->input->post('grossqty');
        $buckets = $this->input->post('buckets');
        $bucketweightID = $this->input->post('bucketweightID');
        $bucketweight = $this->input->post('bucketweight');
        $printID = array();

        foreach ($this->input->post('DetailsID') as $key => $DetailsID) {
            $discount_percentage = 0;
            if($amount[$key] > 0) {
                $discount_percentage = ($discount[$key] / $amount[$key])*100;
            }
            $this->db->select('contractAutoID');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $this->db->where('contractAutoID', $query[$key]['contractAutoID']);
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->where('itemAutoID', $query[$key]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($query[$key]['itemAutoID']);
            $wareHouse_arr = explode('|', $whrehouse[$key]);
            $invoiceAutoID=$this->input->post('invoiceAutoID');

            if (isset($tex_id[$key])) {
                /*$this->db->select('*');
                $this->db->where('taxMasterAutoID', $tex_id[$key]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();*/

                /*$this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();*/

                /*$this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $tex_id[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();*/

                $formulaDetail = $this->db->query(" SELECT formulaDetailID,formula AS formulaString,taxMasters AS  payGroupCategories,taxCalculationformulaID,sortOrder,taxMasterAutoID as taxMsterId FROM srp_erp_taxcalculationformuladetails WHERE taxCalculationformulaID = '" . $tex_id[$key] . "' ORDER BY sortOrder ASC ")->result_array();
            }

           /* if (!empty($order_detail)) {
                $this->session->set_flashdata('w', 'Invoice Detail : ' . trim($this->input->post('itemCode') ?? '') . '  already exists.');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {*/
                $data['type'] = 'Item';
                $data['contractAutoID'] = $query[$key]['contractAutoID'];




                $data['noOfItems'] = $noofitems[$key];
                $data['noOfDividedItems '] = $noofitems[$key];
                $data['grossQty'] = $grossqty[$key];
                $data['grossDividedQty'] = $grossqty[$key];
                $data['noOfUnits'] = $buckets[$key];
                $data['deduction'] = $bucketweight[$key];
                $data['dividedDeduction'] = $bucketweight[$key];
                $data['bucketWeightID'] = $bucketweightID[$key];



                $data['contractCode'] = $query[$key]['contractCode'];
                $data['contractDetailsAutoID'] = $query[$key]['contractDetailsAutoID'];
                $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                $data['itemAutoID'] = $query[$key]['itemAutoID'];
                $data['itemSystemCode'] = $query[$key]['itemSystemCode'];
                $data['itemDescription'] = $query[$key]['itemDescription'];
                $data['defaultUOM'] = $query[$key]['defaultUOM'];
                $data['defaultUOMID'] = $query[$key]['defaultUOMID'];
                $data['unitOfMeasure'] = $query[$key]['unitOfMeasure'];
                $data['unitOfMeasureID'] = $query[$key]['unitOfMeasureID'];
                $data['conversionRateUOM'] = $query[$key]['conversionRateUOM'];
                $data['contractQty'] = $query[$key]['requestedQty'];
                $data['contractAmount'] = $query[$key]['unittransactionAmount'];
                $data['comment'] = $query[$key]['comment'];
                $data['requestedQty'] = $qty[$key];
                $data['requestedDividedQty'] = $qty[$key];
                $data['unittransactionAmount'] = $amount[$key];
                $data['discountAmount'] = $discount[$key];
                $amountafterdiscount = $amount[$key] - $data['discountAmount'];
                $data['discountPercentage'] = $discount_percentage;
                $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                $data['segmentID'] = $master['segmentID'];
                $data['segmentCode'] = $master['segmentCode'];
                $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
                $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['expenseGLCode'] = $item_data['costGLCode'];
                $data['expenseGLDescription'] = $item_data['costDescription'];
                $data['expenseGLType'] = $item_data['costType'];
                $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
                $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
                $data['revenueGLCode'] = $item_data['revanueGLCode'];
                $data['revenueGLDescription'] = $item_data['revanueDescription'];
                $data['revenueGLType'] = $item_data['revanueType'];
                $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
                $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['assetGLCode'] = $item_data['assteGLCode'];
                $data['assetGLDescription'] = $item_data['assteDescription'];
                $data['assetGLType'] = $item_data['assteType'];
                $data['comment'] = $query[$key]['comment'];
                $data['remarks'] = $remarks[$key];
                $data['wareHouseAutoID'] = $wareHouseDetails['warehouseAutoID']; // $wareHouseAutoID[$key];
                $data['wareHouseCode'] = $wareHouseDetails['wareHouseCode']; // $wareHouse_arr[0];
                $data['wareHouseLocation'] = $wareHouseDetails['wareHouseLocation']; // $wareHouse_arr[1];
                $data['wareHouseDescription'] = $wareHouseDetails['wareHouseDescription']; // $wareHouse_arr[1];
                if($tex_id[$key]>0){
                    $data['taxMasterAutoID'] = $tex_id[$key];
                }else{
                    $data['taxMasterAutoID'] = null;
                }

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
          /* }*/

            $this->db->insert('srp_erp_customerinvoicedetails_temp', $data);
            $last_id = $this->db->insert_id();
            $printID[] = $last_id;

            if(!empty($formulaDetail) && $formulaDetail != null){
                $tottax=0;
                foreach($formulaDetail as $formul){
                    if(!empty($formul['formulaString'])){
                        $taxCalculationformulaID=$formul['taxCalculationformulaID'];
                        $companyID=current_companyID();
                        $sortOrder=$formul['sortOrder'];
                        //echo '<pre>';print_r($formul); echo '</pre>';
                        $tax_categories = $this->db->query("SELECT
	srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
FROM
	srp_erp_taxcalculationformuladetails
LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
WHERE
	taxCalculationformulaID = $taxCalculationformulaID
AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                        $this->db->select('supplierAutoID,supplierGLAutoID');
                        $this->db->where('taxMasterAutoID', $formul['taxMsterId']);
                        $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                        $formulaBuilder = tax_formulaBuilder_to_sql($formul, $tax_categories,$amountafterdiscount);
                        $formulaDecodeval=$formulaBuilder['formulaDecode'];
                        $amounttx = $this->db->query("SELECT $formulaDecodeval as amount ")->row_array();
                        $datatxledger['documentID']='HCINV';
                        $datatxledger['documentMasterAutoID']=$invoiceAutoID;
                        $datatxledger['documentDetailAutoID']=$last_id;
                        $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                        $datatxledger['taxFormulaMasterID']=$formul['taxCalculationformulaID'];
                        $datatxledger['taxFormulaDetailID']=$formul['formulaDetailID'];
                        $datatxledger['taxMasterID']=$formul['taxMsterId'];
                        $datatxledger['amount']=$amounttx['amount']*$qty[$key];
                        $datatxledger['formula']=$formul['formulaString'];
                        $datatxledger['taxAuthorityAutoID']=$tax_master['supplierAutoID'];
                        $datatxledger['taxGlAutoID']=$tax_master['supplierGLAutoID'];
                        $datatxledger['companyID'] = $this->common_data['company_data']['company_id'];
                        $datatxledger['companyCode'] = $this->common_data['company_data']['company_code'];
                        $datatxledger['createdUserGroup'] = $this->common_data['user_group'];
                        $datatxledger['createdPCID'] = $this->common_data['current_pc'];
                        $datatxledger['createdUserID'] = $this->common_data['current_userID'];
                        $datatxledger['createdUserName'] = $this->common_data['current_user'];
                        $datatxledger['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_taxledger', $datatxledger);
                        $tottax+=$datatxledger['amount'];
                    }
                }
                if($tottax>0){
                    $unittax=$tottax/$qty[$key];
                    $transactionAmount = ($unittax + $amountafterdiscount) * $qty[$key];
                    $totalAfterTax = $unittax * $data['requestedQty'];
                    $transactionAmount = $transactionAmount - $totalAfterTax;
                    $unitAmount = $amount[$key] - $unittax;
                    $datatx['unittransactionAmount'] = round($unitAmount, $master['transactionCurrencyDecimalPlaces']);
                    $datatx['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $datatx['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $datatx['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $datatx['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $datatx['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $datatx['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $datatx['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $datatx['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $datatx['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $datatx['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                    $datatx['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                    $datatx['taxAmount'] = round($unittax, $master['transactionCurrencyDecimalPlaces']);

                    $datatx['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
                    $datatx['dividedTotalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);

                    $this->db->where('invoiceDetailsAutoID', trim($last_id));
                    $this->db->update('srp_erp_customerinvoicedetails_temp', $datatx);
                }else{
                    // $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $qty[$key];
                    $transactionAmount = ($amountafterdiscount) * $qty[$key];
                    $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                    $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                    $this->db->where('invoiceDetailsAutoID', trim($last_id));
                    $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);
                }
            }else{
                $transactionAmount = ($amountafterdiscount) * $qty[$key];
                $transdata['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $transdata['tranasactionDividedAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $transdata['transactionAmount'] / $master['companyLocalExchangeRate'];
                $transdata['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $transdata['companyLocalDividedAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $transdata['transactionAmount'] / $master['companyReportingExchangeRate'];
                $transdata['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $transdata['companyReportingDividedAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $transdata['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $transdata['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                $transdata['customerDividedAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $this->db->where('invoiceDetailsAutoID', trim($last_id));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $transdata);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message(),$contractID['contractAutoID']);
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Detail : Saved Successfully.', $contractID['contractAutoID'],$printID);
        }
    }

    function save_invoice_approval()
    {
        $this->load->library('approvals');
        $system_id = trim($this->input->post('invoiceAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        $sql = "SELECT
	(
		srp_erp_warehouseitems.currentStock - srp_erp_customerinvoicedetails_temp.requestedQty
	) AS stockDiff,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_warehouseitems.currentStock as availableStock
FROM
	`srp_erp_customerinvoicedetails_temp`
JOIN `srp_erp_warehouseitems` ON `srp_erp_customerinvoicedetails_temp`.`itemAutoID` = `srp_erp_warehouseitems`.`itemAutoID`
AND `srp_erp_customerinvoicedetails_temp`.`wareHouseAutoID` = `srp_erp_warehouseitems`.`wareHouseAutoID`
JOIN `srp_erp_itemmaster` ON `srp_erp_customerinvoicedetails_temp`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`

WHERE
	`srp_erp_customerinvoicedetails_temp`.`invoiceAutoID` = '$system_id'
AND `srp_erp_warehouseitems`.`companyID` = " . current_companyID() . "
HAVING
	`stockDiff` < 0";
        $items_arr = $this->db->query($sql)->result_array();
        if($status!=1){
            $items_arr='';
        }

            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'HCINV');
            if ($approvals_status == 1) {
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicemaster_temp');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicedetails_temp');
                $invoice_detail = $this->db->get()->result_array();
                /*for ($a = 0; $a < count($invoice_detail); $a++) {
                    if ($invoice_detail[$a]['type'] == 'Item') {
                        $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                            $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                            $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                            $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                            $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                            $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
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
                }*/

                /*if (!empty($item_arr)) {
                    $item_arr = array_values($item_arr);
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }*/

/*                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }*/

                /*$this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
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
                }*/

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails_temp')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
                //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Approval Successful.', 1);
            }

    }

    function delete_customerInvoice_attachement()
    {
        $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($this->input->post('attachmentID') ?? '')));
        return true;
    }

    function delete_invoice_master()
    {
        /* $this->db->delete('srp_erp_customerinvoicemaster_temp', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));
         $this->db->delete('srp_erp_customerinvoicedetails_temp', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));
         $this->db->delete('srp_erp_customerinvoicetaxdetails_temp', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));*/
        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
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
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
            return true;
        }
    }

    function load_subItem_notSold_QueryGen($itemAutoID, $detailID, $warehouseID)
    {
        $query = "SELECT * FROM srp_erp_itemmaster_sub iSub
                                    WHERE iSub.itemAutoID = '" . $itemAutoID . "' AND  iSub.wareHouseAutoID ='" . $warehouseID . "'
                                    AND ( (( ISNULL(iSub.isSold) OR iSub.isSold = '' OR iSub.isSold = 0 ) ) OR (iSub.soldDocumentDetailID='" . $detailID . "' ) ) ;";

        return $query;
    }

    function load_subItem_notSold($detailID, $documentID, $warehouseID)
    {
        $subItemArray = array();

        switch ($documentID) {
            case "CINV":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_customerinvoicedetails_temp WHERE invoiceDetailsAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "RV":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_customerreceiptdetail WHERE receiptVoucherDetailAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "SR":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stockreturndetails WHERE stockReturnDetailsID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "MI":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_itemissuedetails WHERE itemIssueDetailID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }

                break;

            case "ST":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stocktransferdetails WHERE stockTransferDetailsID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();

                    $subItemArray = $result;
                }
                break;

            case "SA":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stockadjustmentdetails WHERE stockAdjustmentDetailsAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();

                    $subItemArray = $result;
                }
                break;


            default:
                echo $documentID . ' Error: Code not configured!<br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
        }

        return $subItemArray;
    }

    function get_invoiceDetail($id)
    {
        $this->db->select('srp_erp_customerinvoicedetails_temp.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails_temp.itemAutoID', 'left');
        $this->db->where('invoiceDetailsAutoID', $id);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function get_receiptVoucherDetail($id)
    {
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->where('srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID', $id);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function get_stockReturnDetail($id)
    {
        $this->db->select('srp_erp_stockreturndetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockreturndetails.itemAutoID', 'left');
        $this->db->where('srp_erp_stockreturndetails.stockReturnDetailsID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_materialIssueDetail($id)
    {
        $this->db->select('srp_erp_itemissuedetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_itemissuedetails.itemAutoID', 'left');
        $this->db->where('srp_erp_itemissuedetails.itemIssueDetailID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_stockTransferDetail($id)
    {
        $this->db->select('srp_erp_stocktransferdetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID', 'left');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferDetailsID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_stockAdjustmentDetail($id)
    {
        $this->db->select('srp_erp_stockadjustmentdetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_stockadjustmentdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockadjustmentdetails.itemAutoID', 'left');
        $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function load_invoice_header_id($id)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate');
        $this->db->where('invoiceAutoID', $id);
        return $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();
    }

    function save_subItemList()
    {
        $subItems = $this->input->post('subItemCode[]');
        $soldDocumentID = $this->input->post('soldDocumentID');
        $soldDocumentAutoID = $this->input->post('soldDocumentAutoID');
        $soldDocumentDetailID = $this->input->post('soldDocumentDetailID');

        $currentUser = current_pc();
        $modifiedUserID = current_userID();
        $modifiedDatetime = format_date_mysql_datetime();
        if (!empty($subItems)) {
            $i = 0;
            foreach ($subItems as $subItem) {
                $data[$i]['subItemAutoID'] = $subItem;
                $data[$i]['soldDocumentID'] = $soldDocumentID;
                $data[$i]['isSold'] = 1;
                $data[$i]['soldDocumentAutoID'] = $soldDocumentAutoID;
                $data[$i]['soldDocumentDetailID'] = $soldDocumentDetailID;
                $data[$i]['modifiedPCID'] = $currentUser;
                $data[$i]['modifiedUserID'] = $modifiedUserID;
                $data[$i]['modifiedDatetime'] = $modifiedDatetime;
                $i++;
            }


            if (!empty($data)) {

                $dataTmp['isSold'] = null;
                $dataTmp['soldDocumentAutoID'] = null;
                $dataTmp['soldDocumentDetailID'] = null;
                $dataTmp['soldDocumentID'] = null;
                $dataTmp['modifiedPCID'] = $currentUser;
                $dataTmp['modifiedUserID'] = $modifiedUserID;
                $dataTmp['modifiedDatetime'] = $modifiedDatetime;

                $this->db->where('soldDocumentAutoID', $soldDocumentAutoID);
                $this->db->where('soldDocumentDetailID', $soldDocumentDetailID);
                $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


                $this->db->update_batch('srp_erp_itemmaster_sub', $data, 'subItemAutoID');
            }
            return array('error' => 0, 'message' => 'Record/s updated successfully');

        } else {
            return array('error' => 1, 'message' => 'Please select sub items!');
        }

    }

    function re_open_invoice()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function customerinvoiceGLUpdate()
    {
        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'expenseGLAutoID' => $this->input->post('PLGLAutoID'),
            'expenseSystemGLCode' => $gl['systemAccountCode'],
            'expenseGLCode' => $gl['GLSecondaryCode'],
            'expenseGLDescription' => $gl['GLDescription'],
            'expenseGLType' => $gl['subCategory'],

        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array(
                'revenueGLAutoID' => $this->input->post('BLGLAutoID'),
                'revenueGLCode' => $bl['systemAccountCode'],
                'revenueSystemGLCode' => $bl['GLSecondaryCode'],
                'revenueGLDescription' => $bl['GLSecondaryCode']));
            /*'revenueGLType'=>'',*/


        }


        if ($this->input->post('applyAll') == 1) {
            $this->db->where('invoiceAutoID', trim($this->input->post('masterID') ?? ''));
        } else {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('detailID') ?? ''));
        }
        $this->db->update('srp_erp_customerinvoicedetails_temp ', $data);
        return array('s', 'GL Account Successfully Changed');
    }


    function fetch_customer_invoice_all_detail_edit()
    {
        $this->db->select('srp_erp_customerinvoicedetails_temp.*,srp_erp_customerinvoicemaster_temp.invoiceType,srp_erp_itemmaster.currentStock');
        $this->db->where('srp_erp_customerinvoicedetails_temp.invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->where('srp_erp_customerinvoicedetails_temp.type', 'Item');
        $this->db->join('srp_erp_customerinvoicemaster_temp', 'srp_erp_customerinvoicedetails_temp.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_customerinvoicedetails_temp.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->from('srp_erp_customerinvoicedetails_temp');
        return $this->db->get()->result_array();
    }

    function updateCustomerInvoice_edit_all_Item()
    {
        $projectExist = project_is_exist();
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
      //  $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $projectID = $this->input->post('projectID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            if (!trim($invoiceDetailsAutoID[$key])) {
                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerinvoicedetails_temp');
                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }else{
                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerinvoicedetails_temp');
                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('invoiceDetailsAutoID !=', $invoiceDetailsAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            if (isset($item_text[$key])) {
                $this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                $this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();

                $this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();
            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);

            $data['invoiceAutoID'] = trim($invoiceAutoID);
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $amountafterdiscount = $estimatedAmount[$key] - $data['discountAmount'];
            $data['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data['taxPercentage'] = $item_taxPercentage[$key];
            $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
            $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
            $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
            $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data['comment'] = $comment[$key];
            $data['remarks'] = $remarks[$key];
            $data['type'] = 'Item';
            $item_data = fetch_item_data($data['itemAutoID']);
            $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if (!empty($tax_master)) {
                $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                $data['taxDescription'] = $tax_master['taxDescription'];
                $data['taxShortCode'] = $tax_master['taxShortCode'];
                $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                $data['taxSupplierName'] = $tax_master['supplierName'];
                $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                $data['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                $data['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                $data['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                $data['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                $data['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                $data['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
            } else {
                $data['taxSupplierCurrencyExchangeRate'] = 1;
                $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                $data['taxSupplierCurrencyAmount'] = 0;
            }

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            if (trim($invoiceDetailsAutoID[$key])) {
                $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID[$key]));
                $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
                $this->db->trans_complete();
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails_temp', $data);

                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouseitems)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['defaultUOMID'],
                            'unitOfMeasure' => $data['defaultUOM'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Detail : Saved Successfully.');
        }
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'CINV');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }
    function invoiceloademail()
    {
        $invoiceautoid = $this->input->post('invoiceAutoID');
        $this->db->select('srp_erp_customerinvoicemaster_temp.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster_temp');
        return $this->db->get()->row_array();
    }
    function send_invoice_email()

    {
        $invoiceautoid = trim($this->input->post('invoiceid') ?? '');
        $invoiceemail = trim($this->input->post('email') ?? '');
        $this->db->select('srp_erp_customerinvoicemaster_temp.*,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerName as customerName');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster_temp ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['customerEmail'] == '') {
                $data_master['customerEmail'] = $invoiceemail;
                $this->db->where('customerAutoID', $results['customerID']);
                $this->db->update('srp_erp_customermaster', $data_master);
            }
        }
        $this->db->select('customerEmail,customerName');
        $this->db->where('customerAutoID', $results['customerID']);
        $this->db->from('srp_erp_customermaster ');
        $customerMaster = $this->db->get()->row_array();

        $this->load->library('NumberToWords');
        $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceautoid);
        $data['approval'] = $this->input->post('approval');
        $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
        $html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH.'/uploads/invoice/'. $invoiceautoid .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);


        if (!empty($customerMaster)) {
            if ($customerMaster['customerEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our invoice as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $invoiceemail ,
                    'subject' => ' Customer Invoice for '.$customerMaster['customerName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1,$path);
                return array('s', 'Email Send Successfully.');
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }
    }

    function day_close_invoice(){
        $invoiceAutoIDTemp=$this->input->post('selectedInvoices');
        $this->db->select('*');
        $this->db->where_in('invoiceAutoID', $invoiceAutoIDTemp);
        $this->db->from('srp_erp_customerinvoicemaster_temp');
        $invoice_temp_master = $this->db->get()->result_array();
        $invalidInvoicearr=array();
        $invaliddescarr=array();
        $invalidarr=array();
        $fc_master_last_id=0;
        $pvt_master_last_id=0;
        foreach($invoice_temp_master as  $tempmaster) {
            $fc_master_last_id='';
            $pvt_master_last_id='';
            $this->db->select('capAmount');
            $this->db->where('customerAutoID', $tempmaster['customerID']);
            $this->db->from('srp_erp_customermaster');
            $capAmount = $this->db->get()->row_array();

            $this->db->select('pvtCompanyID,company_code as pvtcompany_code');
            $this->db->where('company_id', $tempmaster['companyID']);
            $this->db->from('srp_erp_company');
            $company = $this->db->get()->row_array();

            $this->db->select('company_code as pvtcompany_code');
            $this->db->where('company_id', $company['pvtCompanyID']);
            $this->db->from('srp_erp_company');
            $pvtcompanycode = $this->db->get()->row_array();

            $this->db->select('finCompanyPercentage,pvtCompanyPercentage');
            $this->db->where('customerAutoID', $tempmaster['customerID']);
            $this->db->from('srp_erp_customermaster');
            $divedPercentage = $this->db->get()->row_array();

        if (($tempmaster['companyLocalAmount'] > $capAmount['capAmount']) && $tempmaster['eliminateYN']==0 && $divedPercentage['finCompanyPercentage']!=100) {
            $segmentarr = array();
            $GLAutoIDarr = array();
            $itemAutoIDarr = array();
            $wareHouseAutoIDarr = array();
            $discountDetailIDarr = array();
            $extraDetailIDarr = array();
            $taxMasterAutoIDarr = array();
            $taxMasterIDarr = array();
            $uomIDarr = array();
            $isDayClosed = array();


            $this->db->select('segmentID,customerID, isDayClosed');
            $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $invoice_temp_master_chk = $this->db->get()->row_array();
            if (!empty($invoice_temp_master_chk['segmentID'])) {
                array_push($segmentarr, $invoice_temp_master_chk['segmentID']);
            }

            if ($invoice_temp_master_chk['isDayClosed'] == 1) {
                array_push($isDayClosed, $invoice_temp_master_chk['isDayClosed']);
                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Day already closed"));

            }
            $customerIDchk = $invoice_temp_master_chk['customerID'];
            $this->db->select('segmentID,expenseGLAutoID,revenueGLAutoID,assetGLAutoID,itemAutoID,wareHouseAutoID,taxMasterAutoID,unitOfMeasureID,defaultUOMID');
            $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $invoice_temp_detail_chk = $this->db->get()->result_array();
            foreach ($invoice_temp_detail_chk as $temp_detail_chk) {
                if (!empty($temp_detail_chk['segmentID'])) {
                    if (!in_array($temp_detail_chk['segmentID'], $segmentarr)) {
                        array_push($segmentarr, $temp_detail_chk['segmentID']);
                    }
                }
                if (!empty($temp_detail_chk['expenseGLAutoID'])) {
                    if (!in_array($temp_detail_chk['expenseGLAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $temp_detail_chk['expenseGLAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['revenueGLAutoID'])) {
                    if (!in_array($temp_detail_chk['revenueGLAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $temp_detail_chk['revenueGLAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['assetGLAutoID'])) {
                    if (!in_array($temp_detail_chk['assetGLAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $temp_detail_chk['assetGLAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['itemAutoID'])) {
                    if (!in_array($temp_detail_chk['itemAutoID'], $itemAutoIDarr)) {
                        array_push($itemAutoIDarr, $temp_detail_chk['itemAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['wareHouseAutoID'])) {
                    if (!in_array($temp_detail_chk['wareHouseAutoID'], $wareHouseAutoIDarr)) {
                        array_push($wareHouseAutoIDarr, $temp_detail_chk['wareHouseAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['taxMasterAutoID'])) {
                    if (!in_array($temp_detail_chk['taxMasterAutoID'], $taxMasterAutoIDarr)) {
                        array_push($taxMasterAutoIDarr, $temp_detail_chk['taxMasterAutoID']);
                    }
                }
                if (!empty($temp_detail_chk['unitOfMeasureID'])) {
                    if (!in_array($temp_detail_chk['unitOfMeasureID'], $uomIDarr)) {
                        array_push($uomIDarr, $temp_detail_chk['unitOfMeasureID']);
                    }
                }
                if (!empty($temp_detail_chk['defaultUOMID'])) {
                    if (!in_array($temp_detail_chk['defaultUOMID'], $uomIDarr)) {
                        array_push($uomIDarr, $temp_detail_chk['defaultUOMID']);
                    }
                }
            }

            $this->db->select('discountMasterAutoID,GLAutoID');
            $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
            $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
            $invoice_temp_discount_chk = $this->db->get()->result_array();
            foreach ($invoice_temp_discount_chk as $temp_discount_chk) {
                if (!empty($temp_discount_chk['discountDetailID'])) {
                    if (!in_array($temp_discount_chk['discountMasterAutoID'], $discountDetailIDarr)) {
                        array_push($discountDetailIDarr, $temp_discount_chk['discountMasterAutoID']);
                    }
                }

                if (!empty($temp_discount_chk['GLAutoID'])) {
                    if (!in_array($temp_discount_chk['GLAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $temp_discount_chk['GLAutoID']);
                    }
                }

            }

            $this->db->select('extraChargeMasterAutoID,GLAutoID');
            $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
            $this->db->from('srp_erp_customerinvoiceextrachargedetails_temp');
            $invoice_temp_extra_chk = $this->db->get()->result_array();
            foreach ($invoice_temp_extra_chk as $temp_extra_chk) {
                if (!empty($temp_extra_chk['extraChargeDetailID'])) {
                    if (!in_array($temp_extra_chk['extraChargeMasterAutoID'], $extraDetailIDarr)) {
                        array_push($extraDetailIDarr, $temp_extra_chk['extraChargeMasterAutoID']);
                    }
                }

                if (!empty($temp_extra_chk['GLAutoID'])) {
                    if (!in_array($temp_extra_chk['GLAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $temp_extra_chk['GLAutoID']);
                    }
                }

            }

            $this->db->select('taxMasterID,taxGlAutoID');
            $this->db->where('documentMasterAutoID', $tempmaster['invoiceAutoID']);
            $this->db->where('documentID', 'HCINV');
            $this->db->from('srp_erp_taxledger');
            $taxMasterID_chk = $this->db->get()->result_array();
            foreach($taxMasterID_chk as $valu){
                if (!empty($valu['taxMasterID'])) {
                    if (!in_array($valu['taxMasterID'], $taxMasterIDarr)) {
                        array_push($taxMasterIDarr, $valu['taxMasterID']);
                    }
                }

                if (!empty($taxMasterID_chk['taxGlAutoID'])) {
                    if (!in_array($taxMasterID_chk['taxGlAutoID'], $GLAutoIDarr)) {
                        array_push($GLAutoIDarr, $taxMasterID_chk['taxGlAutoID']);
                    }
                }
            }


            foreach ($segmentarr as $segarr) {
                $this->db->select('groupSegmentID');
                $this->db->where('segmentID', $segarr);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_groupsegmentdetails');
                $groupsegment = $this->db->get()->row_array();
                $this->db->select('segmentCode');
                $this->db->where('segmentID', $segarr);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_segment');
                $segcode = $this->db->get()->row_array();
                if (!empty($groupsegment['groupSegmentID'])) {
                    $this->db->select('segmentID');
                    $this->db->where('groupSegmentID', $groupsegment['groupSegmentID']);
                    $this->db->where('companyID', $company['pvtCompanyID']);
                    $this->db->from('srp_erp_groupsegmentdetails');
                    $PVTsegment = $this->db->get()->row_array();
                    if (empty($PVTsegment['segmentID'])) {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Segment not linked" ." (".$segcode['segmentCode'].")" ));
                    }
                } else {
                        array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                        array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Segment not linked" ." (".$segcode['segmentCode'].")"));
                }
            }
            if (!empty($customerIDchk)) {
                $this->db->select('groupCustomerMasterID');
                $this->db->where('customerMasterID', $customerIDchk);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_groupcustomerdetails');
                $groupcustomer = $this->db->get()->row_array();

                $this->db->select('customerSystemCode');
                $this->db->where('customerAutoID', $customerIDchk);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_customermaster');
                $cuscode = $this->db->get()->row_array();
                if (!empty($groupcustomer['groupCustomerMasterID'])) {
                    $this->db->select('customerMasterID');
                    $this->db->where('groupCustomerMasterID', $groupcustomer['groupCustomerMasterID']);
                    $this->db->where('companyID', $company['pvtCompanyID']);
                    $this->db->from('srp_erp_groupcustomerdetails');
                    $PVTcustomer = $this->db->get()->row_array();
                    if (empty($PVTcustomer['customerMasterID'])) {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Customer not linked"." (".$cuscode['customerSystemCode'].")"));
                    }
                } else {
                        array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                        array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Customer not linked"." (".$cuscode['customerSystemCode'].")" ));
                }
            }
            if (!empty($GLAutoIDarr)) {
                foreach ($GLAutoIDarr as $glarr) {
                    $this->db->select('groupChartofAccountMasterID');
                    $this->db->where('chartofAccountID', $glarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupchartofaccountdetails');
                    $groupgl = $this->db->get()->row_array();

                    $this->db->select('systemAccountCode');
                    $this->db->where('GLAutoID', $glarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_chartofaccounts');
                    $GLcode = $this->db->get()->row_array();
                    if (!empty($groupgl['groupChartofAccountMasterID'])) {
                        $this->db->select('chartofAccountID');
                        $this->db->where('groupChartofAccountMasterID', $groupgl['groupChartofAccountMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupchartofaccountdetails');
                        $PVTsegment = $this->db->get()->row_array();
                        if (empty($PVTsegment['chartofAccountID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Gl Code not linked"." (".$GLcode['systemAccountCode'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Gl Code not linked"." (".$GLcode['systemAccountCode'].")"));
                    }
                }
            }
            if (!empty($itemAutoIDarr)) {
                foreach ($itemAutoIDarr as $itmarr) {
                    $this->db->select('groupItemMasterID');
                    $this->db->where('ItemAutoID', $itmarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupitemmasterdetails');
                    $groupitem = $this->db->get()->row_array();

                    $this->db->select('itemSystemCode');
                    $this->db->where('itemAutoID', $itmarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_itemmaster');
                    $itmcode = $this->db->get()->row_array();
                    if (!empty($groupitem['groupItemMasterID'])) {
                        $this->db->select('ItemAutoID');
                        $this->db->where('groupItemMasterID', $groupitem['groupItemMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupitemmasterdetails');
                        $PVTitem = $this->db->get()->row_array();
                        if (empty($PVTitem['ItemAutoID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Item not linked"." (".$itmcode['itemSystemCode'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Item not linked"." (".$itmcode['itemSystemCode'].")"));
                    }
                }
            }
            if (!empty($wareHouseAutoIDarr)) {
                foreach ($wareHouseAutoIDarr as $warearr) {
                    $this->db->select('groupWarehouseMasterID');
                    $this->db->where('warehosueMasterID', $warearr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupwarehousedetails');
                    $groupware = $this->db->get()->row_array();

                    $this->db->select('wareHouseCode');
                    $this->db->where('wareHouseAutoID', $warearr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_warehousemaster');
                    $warecode = $this->db->get()->row_array();
                    if (!empty($groupware['groupWarehouseMasterID'])) {
                        $this->db->select('warehosueMasterID');
                        $this->db->where('groupWarehouseMasterID', $groupware['groupWarehouseMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupwarehousedetails');
                        $PVTware = $this->db->get()->row_array();
                        if (empty($PVTware['warehosueMasterID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Warehouse not linked "." (".$warecode['wareHouseCode'].")" ));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Warehouse not linked "." (".$warecode['wareHouseCode'].")"));
                    }
                }
            }
            if (!empty($discountDetailIDarr)) {
                foreach ($discountDetailIDarr as $discarr) {
                    $this->db->select('groupDiscountExtraChargeID');
                    $this->db->where('discountExtraChargeID', $discarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                    $groupdisc = $this->db->get()->row_array();

                    $this->db->select('discountDescription');
                    $this->db->where('discountDetailID', $discarr);
                    $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
                    $discdesc = $this->db->get()->row_array();

                    if (!empty($groupdisc['groupDiscountExtraChargeID'])) {
                        $this->db->select('discountExtraChargeID');
                        $this->db->where('groupDiscountExtraChargeID', $groupdisc['groupDiscountExtraChargeID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                        $PVTdisc = $this->db->get()->row_array();
                        if (empty($PVTdisc['discountExtraChargeID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Discount not linked for "." (".$discdesc['discountDescription'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Discount not linked for "." (".$discarr.")"));
                    }
                }
            }

            if (!empty($extraDetailIDarr)) {
                foreach ($extraDetailIDarr as $extracarr) {
                    $this->db->select('groupDiscountExtraChargeID');
                    $this->db->where('discountExtraChargeID', $extracarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                    $groupextra = $this->db->get()->row_array();

                    $this->db->select('extraChargeDescription');
                    $this->db->where('extraChargeDetailID', $extracarr);
                    $this->db->from('srp_erp_customerinvoiceextrachargedetails_temp');
                    $extradesc = $this->db->get()->row_array();

                    if (!empty($groupextra['groupDiscountExtraChargeID'])) {
                        $this->db->select('discountExtraChargeID');
                        $this->db->where('groupDiscountExtraChargeID', $groupextra['groupDiscountExtraChargeID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                        $PVTextra = $this->db->get()->row_array();
                        if (empty($PVTextra['discountExtraChargeID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Extra Charges not linked for" ." (".$extradesc['extraChargeDescription'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Extra Charges not linked for" ." (".$extradesc['extraChargeDescription'].")"));
                    }
                }
            }

            if (!empty($taxMasterAutoIDarr)) {
                foreach ($taxMasterAutoIDarr as $taxarr) {
                    $this->db->select('groupTaxCalculationformulaID');
                    $this->db->where('taxCalculationFormulaID', $taxarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_grouptaxcalculationformuladetails');
                    $grouptax = $this->db->get()->row_array();

                    $this->db->select('Description');
                    $this->db->where('taxCalculationformulaID', $taxarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_taxcalculationformulamaster');
                    $taxdisc = $this->db->get()->row_array();
                    if (!empty($grouptax['groupTaxCalculationformulaID'])) {
                        $this->db->select('taxCalculationFormulaID');
                        $this->db->where('groupTaxCalculationformulaID', $grouptax['groupTaxCalculationformulaID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_grouptaxcalculationformuladetails');
                        $PVTtax = $this->db->get()->row_array();
                        if (empty($PVTtax['taxCalculationFormulaID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Tax Formula not linked"." (".$taxdisc['Description'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Tax Formula not linked"." (".$taxdisc['Description'].")"));
                    }
                }
            }
            if (!empty($taxMasterIDarr)) {
                foreach ($taxMasterIDarr as $taxmasterarr) {
                    $this->db->select('groupTaxMasterID');
                    $this->db->where('taxMasterID', $taxmasterarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_grouptaxdetail');
                    $grouptaxd = $this->db->get()->row_array();

                    $this->db->select('taxDescription');
                    $this->db->where('taxMasterAutoID', $taxmasterarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_taxmaster');
                    $taxdisc = $this->db->get()->row_array();

                    if (!empty($grouptaxd['groupTaxMasterID'])) {
                        $this->db->select('taxMasterID');
                        $this->db->where('groupTaxMasterID', $grouptaxd['groupTaxMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_grouptaxdetail');
                        $PVTtaxd = $this->db->get()->row_array();
                        if (empty($PVTtaxd['taxMasterID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Tax not linked"." (".$taxdisc['taxDescription'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Tax not linked"." (".$taxdisc['taxDescription'].")"));
                    }
                }
            }

            if (!empty($uomIDarr)) {
                foreach ($uomIDarr as $grpuomarr) {
                    $this->db->select('groupUOMMasterID');
                    $this->db->where('UOMMasterID', $grpuomarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupuomdetails');
                    $groupuom = $this->db->get()->row_array();

                    $this->db->select('UnitShortCode');
                    $this->db->where('UnitID', $grpuomarr);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_unit_of_measure');
                    $unitinval = $this->db->get()->row_array();

                    if (!empty($groupuom['groupUOMMasterID'])) {
                        $this->db->select('UOMMasterID');
                        $this->db->where('groupUOMMasterID', $groupuom['groupUOMMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupuomdetails');
                        $PVTgrpuom = $this->db->get()->row_array();
                        if (empty($PVTgrpuom['UOMMasterID'])) {
                                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "UOM not linked"." (".$unitinval['UnitShortCode'].")"));
                        }
                    } else {
                            array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                            array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "UOM not linked"." (".$unitinval['UnitShortCode'].")"));
                    }
                }
            }

            $tempinvID = $tempmaster['invoiceAutoID'];
            $tempDetailQty = $this->db->query("SELECT
	SUM(requestedQty/conversionRateUOM) as qty,itemAutoID,wareHouseAutoID,itemSystemCode
FROM
	srp_erp_customerinvoicedetails_temp
WHERE
	invoiceAutoID = $tempinvID
AND type='Item'
GROUP BY wareHouseAutoID,itemAutoID")->result_array();

            foreach ($tempDetailQty as $val) {
                $fcQty = ($val['qty'] / 100) * $divedPercentage['finCompanyPercentage'];
                $pvQty = $val['qty'] - $fcQty;
                $wareHouseAutoID = $val['wareHouseAutoID'];
                $itemAutoID = $val['itemAutoID'];

                $this->db->select('groupWarehouseMasterID');
                $this->db->where('warehosueMasterID', $wareHouseAutoID);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_groupwarehousedetails');
                $groupwareid = $this->db->get()->row_array();
                $this->db->select('warehosueMasterID');
                $this->db->where('groupWarehouseMasterID', $groupwareid['groupWarehouseMasterID']);
                $this->db->where('companyID', $company['pvtCompanyID']);
                $this->db->from('srp_erp_groupwarehousedetails');
                $PVTwareid = $this->db->get()->row_array();

                $this->db->select('groupItemMasterID');
                $this->db->where('ItemAutoID', $itemAutoID);
                $this->db->where('companyID', current_companyID());
                $this->db->from('srp_erp_groupitemmasterdetails');
                $groupitemid = $this->db->get()->row_array();
                $this->db->select('ItemAutoID');
                $this->db->where('groupItemMasterID', $groupitemid['groupItemMasterID']);
                $this->db->where('companyID', $company['pvtCompanyID']);
                $this->db->from('srp_erp_groupitemmasterdetails');
                $PVTitemid = $this->db->get()->row_array();


                $warehouseItemsfc = $this->db->query("SELECT
	currentStock
FROM
	srp_erp_warehouseitems
WHERE
	wareHouseAutoID = $wareHouseAutoID
AND itemAutoID=$itemAutoID")->row_array();
$pvtitemAutoID=$PVTitemid['ItemAutoID'];
$pvtwareHouseAutoID=$PVTwareid['warehosueMasterID'];
if(!empty($pvtwareHouseAutoID) && !empty($pvtitemAutoID)){
    $warehouseItemspvt = $this->db->query("SELECT
	currentStock
FROM
	srp_erp_warehouseitems
WHERE
	wareHouseAutoID = $pvtwareHouseAutoID
AND itemAutoID=$pvtitemAutoID")->row_array();
                }


                if ($warehouseItemsfc['currentStock'] < $fcQty) {
                        array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                        array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Stock not sufficient for ".$val['itemSystemCode']. " (".current_companyCode().")"));
                }
                if(!empty($pvtwareHouseAutoID) && !empty($pvtitemAutoID)) {
                    if ($warehouseItemspvt['currentStock'] < $pvQty) {
                        array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                        array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Stock not sufficient for " . $val['itemSystemCode'] . " (" . $pvtcompanycode['pvtcompany_code'] . ")"));
                    }
                }

            }

            $docDate= $tempmaster['invoiceDate'];
            $pvtComp= $company['pvtCompanyID'];
            $pvtFinanceperiod = $this->db->query("SELECT
	period.companyFinancePeriodID as companyFinancePeriodID,
	period.companyFinanceYearID as companyFinanceYearID,
	yer.beginingDate as beginingDate,
	yer.endingDate as endingDate
FROM
	srp_erp_companyfinanceperiod period
LEFT JOIN srp_erp_companyfinanceyear yer ON period.companyFinanceYearID = yer.companyFinanceYearID
WHERE
	period.companyID = $pvtComp
AND '$docDate' BETWEEN period.dateFrom
AND period.dateTo
AND period.isActive = 1")->row_array();

            if(empty($pvtFinanceperiod)){
                array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Document date not between financial periods ".$tempmaster['invoiceCode']. " (".$pvtcompanycode['pvtcompany_code'].")"));
            }

            if (in_array($tempmaster['invoiceCode'], $invalidInvoicearr)) {
                continue;
            } else {
                /*array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "success ".$tempmaster['invoiceCode'] ));
                continue;*/
                $this->db->select('finCompanyPercentage,pvtCompanyPercentage');
                $this->db->where('customerAutoID', $tempmaster['customerID']);
                $this->db->from('srp_erp_customermaster');
                $customer = $this->db->get()->row_array();

                $data_master_temp['transactionDividedAmount'] = ($tempmaster['transactionAmount'] * $customer['finCompanyPercentage']) / 100;
                $data_master_temp['companyLocalDividedAmount'] = ($tempmaster['companyLocalAmount'] * $customer['finCompanyPercentage']) / 100;
                $data_master_temp['companyReportingDividedAmount'] = ($tempmaster['companyReportingAmount'] * $customer['finCompanyPercentage']) / 100;
                $data_master_temp['fcPercentage'] = $customer['finCompanyPercentage'];
                $data_master_temp['pvtPercentage'] = $customer['pvtCompanyPercentage'];
                $data_master_temp['isDayClosed'] = 1;

                $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                $result_master_temp = $this->db->update('srp_erp_customerinvoicemaster_temp', $data_master_temp);
                if ($result_master_temp) {
                    if ($divedPercentage ['pvtCompanyPercentage'] != 100) {
                        $data_fc_master['documentID'] = 'CINV';
                        $data_fc_master['tempInvoiceID'] = trim($tempmaster['invoiceAutoID'] ?? '');
                        $data_fc_master['companyFinanceYearID'] = trim($tempmaster['companyFinanceYearID'] ?? '');
                        $data_fc_master['companyFinanceYear'] = trim($tempmaster['companyFinanceYear'] ?? '');
                        $data_fc_master['contactPersonName'] = trim($tempmaster['contactPersonName'] ?? '');
                        $data_fc_master['contactPersonNumber'] = trim($tempmaster['contactPersonNumber'] ?? '');
                        $data_fc_master['FYBegin'] = trim($tempmaster['FYBegin'] ?? '');
                        $data_fc_master['FYEnd'] = trim($tempmaster['FYEnd'] ?? '');
                        $data_fc_master['companyFinancePeriodID'] = trim($tempmaster['companyFinancePeriodID'] ?? '');
                        $data_fc_master['invoiceDate'] = trim($tempmaster['invoiceDate'] ?? '');
                        $data_fc_master['customerInvoiceDate'] = trim($tempmaster['customerInvoiceDate'] ?? '');
                        $data_fc_master['invoiceDueDate'] = trim($tempmaster['invoiceDueDate'] ?? '');
                        $data_fc_master['invoiceNarration'] = trim_desc($tempmaster['invoiceNarration']);
                        $data_fc_master['invoiceNote'] = trim($tempmaster['invoiceNote'] ?? '');
                        $data_fc_master['segmentID'] = trim($tempmaster['segmentID'] ?? '');
                        $data_fc_master['segmentCode'] = trim($tempmaster['segmentCode'] ?? '');
                        $data_fc_master['salesPersonID'] = trim($tempmaster['salesPersonID'] ?? '');
                        if ($data_fc_master['salesPersonID']) {
                            $data_fc_master['SalesPersonCode'] = trim($tempmaster['SalesPersonCode'] ?? '');
                        }
                        $data_fc_master['invoiceType'] = trim($tempmaster['invoiceType'] ?? '');
                        $data_fc_master['referenceNo'] = trim($tempmaster['referenceNo'] ?? '');
                        $data_fc_master['showTaxSummaryYN'] = trim($tempmaster['showTaxSummaryYN'] ?? '');
                        $data_fc_master['isPrintDN'] = trim($tempmaster['isPrintDN'] ?? '');
                        $data_fc_master['customerID'] = $tempmaster['customerID'];
                        $data_fc_master['customerSystemCode'] = $tempmaster['customerSystemCode'];
                        $data_fc_master['customerName'] = $tempmaster['customerName'];
                        $data_fc_master['customerAddress'] = $tempmaster['customerAddress'];
                        $data_fc_master['customerTelephone'] = $tempmaster['customerTelephone'];
                        $data_fc_master['customerFax'] = $tempmaster['customerFax'];
                        $data_fc_master['customerEmail'] = $tempmaster['customerEmail'];
                        $data_fc_master['customerReceivableAutoID'] = $tempmaster['customerReceivableAutoID'];
                        $data_fc_master['customerReceivableSystemGLCode'] = $tempmaster['customerReceivableSystemGLCode'];
                        $data_fc_master['customerReceivableGLAccount'] = $tempmaster['customerReceivableGLAccount'];
                        $data_fc_master['customerReceivableDescription'] = $tempmaster['customerReceivableDescription'];
                        $data_fc_master['customerReceivableType'] = $tempmaster['customerReceivableType'];
                        $data_fc_master['customerCurrency'] = $tempmaster['customerCurrency'];
                        $data_fc_master['customerCurrencyID'] = $tempmaster['customerCurrencyID'];
                        $data_fc_master['customerCurrencyDecimalPlaces'] = $tempmaster['customerCurrencyDecimalPlaces'];
                        $data_fc_master['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_fc_master['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_fc_master['modifiedUserName'] = $this->common_data['current_user'];
                        $data_fc_master['modifiedDateTime'] = $this->common_data['current_date'];
                        $data_fc_master['transactionCurrencyID'] = trim($tempmaster['transactionCurrencyID'] ?? '');
                        $data_fc_master['transactionCurrency'] = trim($tempmaster['transactionCurrency'] ?? '');
                        $data_fc_master['transactionExchangeRate'] = $tempmaster['transactionExchangeRate'];
                        $data_fc_master['transactionCurrencyDecimalPlaces'] = $tempmaster['transactionCurrencyDecimalPlaces'];
                        $data_fc_master['companyLocalCurrencyID'] = $tempmaster['companyLocalCurrencyID'];
                        $data_fc_master['companyLocalCurrency'] = $tempmaster['companyLocalCurrency'];
                        $default_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['companyLocalCurrencyID']);
                        $data_fc_master['companyLocalExchangeRate'] = $tempmaster['companyLocalExchangeRate'];
                        $data_fc_master['companyLocalCurrencyDecimalPlaces'] = $tempmaster['companyLocalCurrencyDecimalPlaces'];
                        $data_fc_master['companyReportingCurrency'] = $tempmaster['companyReportingCurrency'];
                        $data_fc_master['companyReportingCurrencyID'] = $tempmaster['companyReportingCurrencyID'];
                        $reporting_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['companyReportingCurrencyID']);
                        $data_fc_master['companyReportingExchangeRate'] = $tempmaster['companyReportingExchangeRate'];
                        $data_fc_master['companyReportingCurrencyDecimalPlaces'] = $tempmaster['companyReportingCurrencyDecimalPlaces'];
                        $customer_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['customerCurrencyID']);
                        $data_fc_master['customerCurrencyExchangeRate'] = $tempmaster['customerCurrencyExchangeRate'];
                        $data_fc_master['customerCurrencyDecimalPlaces'] = $tempmaster['customerCurrencyDecimalPlaces'];
                        $data_fc_master['transactionAmount'] = ($tempmaster['transactionAmount']* $customer['finCompanyPercentage']) / 100;
                        $data_fc_master['companyLocalAmount'] = ($tempmaster['companyLocalAmount']* $customer['finCompanyPercentage']) / 100;
                        $data_fc_master['companyReportingAmount'] = ($tempmaster['companyReportingAmount']* $customer['finCompanyPercentage']) / 100;
                        $data_fc_master['customerCurrencyAmount'] = (($tempmaster['transactionAmount']/$tempmaster['customerCurrencyExchangeRate'])* $customer['finCompanyPercentage']) / 100;
                        $data_fc_master['confirmedYN'] = 1;
                        $data_fc_master['confirmedByEmpID'] = current_userID();
                        $data_fc_master['confirmedByName'] = current_user();
                        $data_fc_master['confirmedDate'] = $this->common_data['current_date'];
                        $data_fc_master['approvedYN'] = 1;
                        $data_fc_master['currentLevelNo'] = 1;
                        $data_fc_master['approvedbyEmpID'] = current_userID();
                        $data_fc_master['approvedbyEmpName'] = current_user();
                        $data_fc_master['approvedDate'] = $this->common_data['current_date'];
                        $this->load->library('sequence');
                        $data_fc_master['companyCode'] = $tempmaster['companyCode'];
                        $data_fc_master['companyID'] = $tempmaster['companyID'];
                        $data_fc_master['createdUserGroup'] = $this->common_data['user_group'];
                        $data_fc_master['createdPCID'] = $this->common_data['current_pc'];
                        $data_fc_master['createdUserID'] = $this->common_data['current_userID'];
                        $data_fc_master['createdUserName'] = $this->common_data['current_user'];
                        $data_fc_master['createdDateTime'] = $this->common_data['current_date'];
                        /* $data_fc_master['invoiceCode'] = $this->sequence->sequence_generator($data_fc_master['documentID']);
                         $data_fc_master['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');*/
                        $data_fc_master['invoiceCode'] = $tempmaster['invoiceCode'];
                        $data_fc_master['deliveryNoteSystemCode'] = $tempmaster['deliveryNoteSystemCode'];

                        $this->db->insert('srp_erp_customerinvoicemaster', $data_fc_master);
                        $fc_master_last_id = $this->db->insert_id();

                        $data_app_fc['companyID'] = $tempmaster['companyID'];
                        $data_app_fc['companyCode'] = $tempmaster['companyCode'];
                        $data_app_fc['departmentID'] = 'CINV';
                        $data_app_fc['documentID'] = 'CINV';
                        $data_app_fc['documentSystemCode'] = $fc_master_last_id;
                        $data_app_fc['documentCode'] = $data_fc_master['invoiceCode'];
                        $data_app_fc['table_name'] = 'srp_erp_customerinvoicemaster';
                        $data_app_fc['table_unique_field_name'] = 'invoiceAutoID';
                        $data_app_fc['documentDate'] = $tempmaster['invoiceDate'];
                        $data_app_fc['approvalLevelID'] = 1;
                        $data_app_fc['roleID'] = null;
                        $data_app_fc['approvalGroupID'] = $this->common_data['user_group'];
                        $data_app_fc['roleLevelOrder'] = null;
                        $data_app_fc['docConfirmedDate'] = $this->common_data['current_date'];
                        $data_app_fc['docConfirmedByEmpID'] = $this->common_data['current_userID'];
                        $data_app_fc['approvedEmpID'] = $this->common_data['current_userID'];;
                        $data_app_fc['isReverseApplicableYN'] = 0;
                        $data_app_fc['approvedYN'] = 1;
                        $data_app_fc['approvedDate'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_documentapproved', $data_app_fc);


                        $this->db->select('*');
                        $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->from('srp_erp_customerinvoicedetails_temp');
                        $temp_detail = $this->db->get()->result_array();
                        foreach ($temp_detail as $Dtemp) {
                            $data_detail_temp['requestedDividedQty'] = ($Dtemp['requestedQty'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['noOfDividedItems'] = ($Dtemp['noOfItems'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['grossDividedQty'] = ($Dtemp['grossQty'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['dividedDeduction'] = ($Dtemp['deduction'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['tranasactionDividedAmount'] = ($Dtemp['transactionAmount'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['dividedTotalAfterTax'] = ($Dtemp['totalAfterTax'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['companyLocalDividedAmount'] = ($Dtemp['companyLocalAmount'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['companyReportingDividedAmount'] = ($Dtemp['companyReportingAmount'] * $customer['finCompanyPercentage']) / 100;
                            $data_detail_temp['customerDividedAmount'] = ($Dtemp['customerAmount'] * $customer['finCompanyPercentage']) / 100;

                            $this->db->where('invoiceDetailsAutoID', $Dtemp['invoiceDetailsAutoID']);
                            $result_detail_temp = $this->db->update('srp_erp_customerinvoicedetails_temp', $data_detail_temp);
                            if ($result_detail_temp) {
                                $data_fc_detail['invoiceAutoID'] = $fc_master_last_id;
                                $data_fc_detail['tempinvoiceDetailID'] = $Dtemp['invoiceDetailsAutoID'];
                                $data_fc_detail['type'] = $Dtemp['type'];
                                $data_fc_detail['contractAutoID'] = $Dtemp['contractAutoID'];
                                $data_fc_detail['contractDetailsAutoID'] = $Dtemp['contractDetailsAutoID'];
                                $data_fc_detail['contractCode'] = $Dtemp['contractCode'];
                                $data_fc_detail['projectID'] = $Dtemp['projectID'];
                                $data_fc_detail['projectExchangeRate'] = $Dtemp['projectExchangeRate'];
                                $data_fc_detail['itemAutoID'] = $Dtemp['itemAutoID'];
                                $data_fc_detail['itemSystemCode'] = $Dtemp['itemSystemCode'];
                                $data_fc_detail['itemDescription'] = $Dtemp['itemDescription'];
                                $data_fc_detail['itemCategory'] = $Dtemp['itemCategory'];
                                $data_fc_detail['expenseGLAutoID'] = $Dtemp['expenseGLAutoID'];
                                $data_fc_detail['expenseSystemGLCode'] = $Dtemp['expenseSystemGLCode'];
                                $data_fc_detail['expenseGLCode'] = $Dtemp['expenseGLCode'];
                                $data_fc_detail['expenseGLDescription'] = $Dtemp['expenseGLDescription'];
                                $data_fc_detail['expenseGLType'] = $Dtemp['expenseGLType'];
                                $data_fc_detail['revenueGLAutoID'] = $Dtemp['revenueGLAutoID'];
                                $data_fc_detail['revenueGLCode'] = $Dtemp['revenueGLCode'];
                                $data_fc_detail['revenueSystemGLCode'] = $Dtemp['revenueSystemGLCode'];
                                $data_fc_detail['revenueGLDescription'] = $Dtemp['revenueGLDescription'];
                                $data_fc_detail['revenueGLType'] = $Dtemp['revenueGLType'];
                                $data_fc_detail['assetGLAutoID'] = $Dtemp['assetGLAutoID'];
                                $data_fc_detail['assetGLCode'] = $Dtemp['assetGLCode'];
                                $data_fc_detail['assetSystemGLCode'] = $Dtemp['assetSystemGLCode'];
                                $data_fc_detail['assetGLDescription'] = $Dtemp['assetGLDescription'];
                                $data_fc_detail['taxMasterAutoID'] = $Dtemp['taxMasterAutoID'];
                                $data_fc_detail['taxPercentage'] = $Dtemp['taxPercentage'];
                                $data_fc_detail['assetGLType'] = $Dtemp['assetGLType'];
                                $data_fc_detail['wareHouseAutoID'] = $Dtemp['wareHouseAutoID'];
                                $data_fc_detail['wareHouseCode'] = $Dtemp['wareHouseCode'];
                                $data_fc_detail['wareHouseLocation'] = $Dtemp['wareHouseLocation'];
                                $data_fc_detail['wareHouseDescription'] = $Dtemp['wareHouseDescription'];
                                $data_fc_detail['defaultUOMID'] = $Dtemp['defaultUOMID'];
                                $data_fc_detail['defaultUOM'] = $Dtemp['defaultUOM'];
                                $data_fc_detail['unitOfMeasureID'] = $Dtemp['unitOfMeasureID'];
                                $data_fc_detail['unitOfMeasure'] = $Dtemp['unitOfMeasure'];
                                $data_fc_detail['conversionRateUOM'] = $Dtemp['conversionRateUOM'];
                                $data_fc_detail['contractQty'] = $Dtemp['contractQty'];
                                $data_fc_detail['contractAmount'] = $Dtemp['contractAmount'];

                                $data_fc_detail['requestedQty'] = ($Dtemp['requestedQty'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['noOfItems'] = ($Dtemp['noOfItems'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['grossQty'] = ($Dtemp['grossQty'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['noOfUnits'] = $Dtemp['noOfUnits'];
                                $data_fc_detail['deduction'] = ($Dtemp['deduction'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['comment'] = $Dtemp['comment'];
                                $data_fc_detail['remarks'] = $Dtemp['remarks'];
                                $data_fc_detail['description'] = $Dtemp['description'];
                                $data_fc_detail['companyLocalWacAmount'] = $Dtemp['companyLocalWacAmount'];
                                $data_fc_detail['unittransactionAmount'] = $Dtemp['unittransactionAmount'];
                                $data_fc_detail['transactionAmount'] = ($Dtemp['transactionAmount'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['companyLocalAmount'] = ($Dtemp['companyLocalAmount'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['companyReportingAmount'] = ($Dtemp['companyReportingAmount'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['customerAmount'] = ($Dtemp['customerAmount'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['segmentID'] = $Dtemp['segmentID'];
                                $data_fc_detail['segmentCode'] = $Dtemp['segmentCode'];
                                $data_fc_detail['companyID'] = $Dtemp['companyID'];
                                $data_fc_detail['companyCode'] = $Dtemp['companyCode'];
                                $data_fc_detail['discountPercentage'] = $Dtemp['discountPercentage'];
                                $data_fc_detail['discountAmount'] = $Dtemp['discountAmount'];
                                $data_fc_detail['taxDescription'] = $Dtemp['taxDescription'];
                                $data_fc_detail['taxAmount'] = $Dtemp['taxAmount'];
                                $data_fc_detail['totalAfterTax'] = ($Dtemp['totalAfterTax'] * $customer['finCompanyPercentage']) / 100;
                                $data_fc_detail['taxShortCode'] = $Dtemp['taxShortCode'];
                                $data_fc_detail['taxSupplierAutoID'] = $Dtemp['taxSupplierAutoID'];
                                $data_fc_detail['taxSupplierSystemCode'] = $Dtemp['taxSupplierSystemCode'];
                                $data_fc_detail['taxSupplierName'] = $Dtemp['taxSupplierName'];
                                $data_fc_detail['taxSupplierliabilityAutoID'] = $Dtemp['taxSupplierliabilityAutoID'];
                                $data_fc_detail['taxSupplierliabilitySystemGLCode'] = $Dtemp['taxSupplierliabilitySystemGLCode'];
                                $data_fc_detail['taxSupplierliabilityGLAccount'] = $Dtemp['taxSupplierliabilityGLAccount'];
                                $data_fc_detail['taxSupplierliabilityDescription'] = $Dtemp['taxSupplierliabilityDescription'];
                                $data_fc_detail['taxSupplierliabilityType'] = $Dtemp['taxSupplierliabilityType'];
                                $data_fc_detail['taxSupplierCurrencyID'] = $Dtemp['taxSupplierCurrencyID'];
                                $data_fc_detail['taxSupplierCurrency'] = $Dtemp['taxSupplierCurrency'];
                                $data_fc_detail['taxSupplierCurrencyExchangeRate'] = $Dtemp['taxSupplierCurrencyExchangeRate'];
                                $data_fc_detail['taxSupplierCurrencyAmount'] = $Dtemp['taxSupplierCurrencyAmount'];
                                $data_fc_detail['taxSupplierCurrencyDecimalPlaces'] = $Dtemp['taxSupplierCurrencyDecimalPlaces'];
                                $data_fc_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $data_fc_detail['createdPCID'] = $this->common_data['current_pc'];
                                $data_fc_detail['createdUserID'] = $this->common_data['current_userID'];
                                $data_fc_detail['createdUserName'] = $this->common_data['current_user'];
                                $data_fc_detail['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_customerinvoicedetails', $data_fc_detail);
                                $fc_detail_last_id= $this->db->insert_id();

                                $this->db->select('*');
                                $this->db->where('documentMasterAutoID', $tempmaster['invoiceAutoID']);
                                $this->db->where('documentDetailAutoID', $Dtemp['invoiceDetailsAutoID']);
                                $this->db->where('documentID', 'HCINV');
                                $this->db->from('srp_erp_taxledger');
                                $tax_ledger = $this->db->get()->result_array();
                                if(!empty($tax_ledger)){
                                    foreach($tax_ledger as $txled){
                                        $data_fc_tax_ledgr['documentID'] = 'CINV';
                                        $data_fc_tax_ledgr['documentMasterAutoID'] = $fc_master_last_id;
                                        $data_fc_tax_ledgr['documentDetailAutoID'] = $fc_detail_last_id;
                                        $data_fc_tax_ledgr['taxDetailAutoID'] = $txled['taxDetailAutoID'];
                                        $data_fc_tax_ledgr['taxFormulaMasterID'] = $txled['taxFormulaMasterID'];
                                        $data_fc_tax_ledgr['taxFormulaDetailID'] = $txled['taxFormulaDetailID'];
                                        $data_fc_tax_ledgr['taxMasterID'] = $txled['taxMasterID'];
                                        $data_fc_tax_ledgr['amount'] = ($txled['amount']* $customer['finCompanyPercentage']) / 100;
                                        $data_fc_tax_ledgr['formula'] = $txled['formula'];
                                        $data_fc_tax_ledgr['taxAuthorityAutoID'] = $txled['taxAuthorityAutoID'];
                                        $data_fc_tax_ledgr['taxGlAutoID'] = $txled['taxGlAutoID'];
                                        $data_fc_tax_ledgr['companyID'] = current_companyID();
                                        $data_fc_tax_ledgr['companyCode'] = $this->common_data['company_data']['company_code'];
                                        $data_fc_tax_ledgr['createdUserGroup'] = $this->common_data['user_group'];
                                        $data_fc_tax_ledgr['createdPCID'] = $this->common_data['current_pc'];
                                        $data_fc_tax_ledgr['createdUserID'] = $this->common_data['current_userID'];
                                        $data_fc_tax_ledgr['createdUserName'] = $this->common_data['current_user'];
                                        $data_fc_tax_ledgr['createdDateTime'] = $this->common_data['current_date'];

                                        $this->db->insert('srp_erp_taxledger', $data_fc_tax_ledgr);
                                    }
                                }
                            }
                        }

                        $this->db->select('*');
                        $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
                        $temp_discount = $this->db->get()->result_array();
                        if(!empty($temp_discount)){
                            foreach($temp_discount as $disc){
                                $data_fc_discount['invoiceAutoID'] = $fc_master_last_id;
                                $data_fc_discount['referenceNo'] = $disc['referenceNo'];
                                $data_fc_discount['discountMasterAutoID'] = $disc['discountMasterAutoID'];
                                $data_fc_discount['isChargeToExpense'] = $disc['isChargeToExpense'];
                                $data_fc_discount['discountDescription'] = $disc['discountDescription'];
                                $data_fc_discount['discountPercentage'] = $disc['discountPercentage'];
                                $data_fc_discount['transactionCurrencyID'] = $disc['transactionCurrencyID'];
                                $data_fc_discount['transactionCurrency'] = $disc['transactionCurrency'];
                                $data_fc_discount['transactionExchangeRate'] = $disc['transactionExchangeRate'];
                                $data_fc_discount['transactionCurrencyDecimalPlaces'] = $disc['transactionCurrencyDecimalPlaces'];
                                $data_fc_discount['transactionAmount'] = ($disc['transactionAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_discount['customerCurrencyID'] = $disc['customerCurrencyID'];
                                $data_fc_discount['customerCurrency'] = $disc['customerCurrency'];
                                $data_fc_discount['customerCurrencyExchangeRate'] = $disc['customerCurrencyExchangeRate'];
                                $data_fc_discount['customerCurrencyAmount'] = ($disc['customerCurrencyAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_discount['customerCurrencyDecimalPlaces'] = $disc['customerCurrencyDecimalPlaces'];
                                $data_fc_discount['companyLocalCurrencyID'] = $disc['companyLocalCurrencyID'];
                                $data_fc_discount['companyLocalCurrency'] = $disc['companyLocalCurrency'];
                                $data_fc_discount['companyLocalExchangeRate'] = $disc['companyLocalExchangeRate'];
                                $data_fc_discount['companyLocalAmount'] = ($disc['companyLocalAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_discount['companyReportingCurrencyID'] = $disc['companyReportingCurrencyID'];
                                $data_fc_discount['companyReportingCurrency'] = $disc['companyReportingCurrency'];
                                $data_fc_discount['companyReportingExchangeRate'] = $disc['companyReportingExchangeRate'];
                                $data_fc_discount['companyReportingAmount'] = ($disc['companyReportingAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_discount['GLAutoID'] = $disc['GLAutoID'];
                                $data_fc_discount['systemGLCode'] = $disc['systemGLCode'];
                                $data_fc_discount['GLCode'] = $disc['GLCode'];
                                $data_fc_discount['GLDescription'] = $disc['GLDescription'];
                                $data_fc_discount['GLType'] = $disc['GLType'];
                                $data_fc_discount['segmentID'] = $disc['segmentID'];
                                $data_fc_discount['segmentCode'] = $disc['segmentCode'];
                                $data_fc_discount['companyID'] = current_companyID();
                                $data_fc_discount['companyCode'] = $this->common_data['company_data']['company_code'];
                                $data_fc_discount['createdUserGroup'] = $this->common_data['user_group'];
                                $data_fc_discount['createdPCID'] = $this->common_data['current_pc'];
                                $data_fc_discount['createdUserID'] = $this->common_data['current_userID'];
                                $data_fc_discount['createdUserName'] = $this->common_data['current_user'];
                                $data_fc_discount['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_customerinvoicediscountdetails', $data_fc_discount);
                            }
                        }

                        $this->db->select('*');
                        $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->from('srp_erp_customerinvoiceextrachargedetails_temp');
                        $temp_extra = $this->db->get()->result_array();
                        if(!empty($temp_extra)){
                            foreach($temp_extra as $extra){
                                $data_fc_extra['invoiceAutoID'] = $fc_master_last_id;
                                $data_fc_extra['referenceNo'] = $extra['referenceNo'];
                                $data_fc_extra['extraChargeMasterAutoID'] = $extra['extraChargeMasterAutoID'];
                                $data_fc_extra['isTaxApplicable'] = $extra['isTaxApplicable'];
                                $data_fc_extra['extraChargeDescription'] = $extra['extraChargeDescription'];
                                $data_fc_extra['transactionCurrencyID'] = $extra['transactionCurrencyID'];
                                $data_fc_extra['transactionCurrency'] = $extra['transactionCurrency'];
                                $data_fc_extra['transactionExchangeRate'] = $extra['transactionExchangeRate'];
                                $data_fc_extra['transactionCurrencyDecimalPlaces'] = $extra['transactionCurrencyDecimalPlaces'];
                                $data_fc_extra['transactionAmount'] = ($extra['transactionAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_extra['customerCurrencyID'] = $extra['customerCurrencyID'];
                                $data_fc_extra['customerCurrency'] = $extra['customerCurrency'];
                                $data_fc_extra['customerCurrencyExchangeRate'] = $extra['customerCurrencyExchangeRate'];
                                $data_fc_extra['customerCurrencyAmount'] = ($extra['customerCurrencyAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_extra['customerCurrencyDecimalPlaces'] = $extra['customerCurrencyDecimalPlaces'];
                                $data_fc_extra['companyLocalCurrencyID'] = $extra['companyLocalCurrencyID'];
                                $data_fc_extra['companyLocalCurrency'] = $extra['companyLocalCurrency'];
                                $data_fc_extra['companyLocalExchangeRate'] = $extra['companyLocalExchangeRate'];
                                $data_fc_extra['companyLocalAmount'] = ($extra['companyLocalAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_extra['companyReportingCurrencyID'] = $extra['companyReportingCurrencyID'];
                                $data_fc_extra['companyReportingCurrency'] = $extra['companyReportingCurrency'];
                                $data_fc_extra['companyReportingExchangeRate'] = $extra['companyReportingExchangeRate'];
                                $data_fc_extra['companyReportingAmount'] = ($extra['companyReportingAmount']* $customer['finCompanyPercentage']) / 100;
                                $data_fc_extra['GLAutoID'] = $extra['GLAutoID'];
                                $data_fc_extra['systemGLCode'] = $extra['systemGLCode'];
                                $data_fc_extra['GLCode'] = $extra['GLCode'];
                                $data_fc_extra['GLDescription'] = $extra['GLDescription'];
                                $data_fc_extra['GLType'] = $extra['GLType'];
                                $data_fc_extra['segmentID'] = $extra['segmentID'];
                                $data_fc_extra['segmentCode'] = $extra['segmentCode'];
                                $data_fc_extra['companyID'] = current_companyID();
                                $data_fc_extra['companyCode'] = $this->common_data['company_data']['company_code'];
                                $data_fc_extra['createdUserGroup'] = $this->common_data['user_group'];
                                $data_fc_extra['createdPCID'] = $this->common_data['current_pc'];
                                $data_fc_extra['createdUserID'] = $this->common_data['current_userID'];
                                $data_fc_extra['createdUserName'] = $this->common_data['current_user'];
                                $data_fc_extra['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_customerinvoiceextrachargedetails', $data_fc_extra);
                            }
                        }

                        /*$this->db->select('*');
                        $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->from('srp_erp_customerinvoicetaxdetails_temp');
                        $temp_tax = $this->db->get()->result_array();

                        if (!empty($temp_tax)) {
                            $data_fc_tax['invoiceAutoID'] = $fc_master_last_id;
                            $data_fc_tax['referenceNo'] = $temp_tax['referenceNo'];
                            $data_fc_tax['taxMasterAutoID'] = $temp_tax['taxMasterAutoID'];
                            $data_fc_tax['tempInvoiceTaxDetailID'] = $temp_tax['taxDetailAutoID'];
                            $data_fc_tax['taxDescription'] = $temp_tax['taxDescription'];
                            $data_fc_tax['taxShortCode'] = $temp_tax['taxShortCode'];
                            $data_fc_tax['taxPercentage'] = $temp_tax['taxPercentage'];
                            $data_fc_tax['supplierAutoID'] = $temp_tax['supplierAutoID'];
                            $data_fc_tax['supplierSystemCode'] = $temp_tax['supplierSystemCode'];
                            $data_fc_tax['supplierName'] = $temp_tax['supplierName'];
                            $data_fc_tax['transactionCurrencyID'] = $temp_tax['transactionCurrencyID'];
                            $data_fc_tax['transactionCurrency'] = $temp_tax['transactionCurrency'];
                            $data_fc_tax['transactionExchangeRate'] = $temp_tax['transactionExchangeRate'];
                            $data_fc_tax['transactionCurrencyDecimalPlaces'] = $temp_tax['transactionCurrencyDecimalPlaces'];
                            $data_fc_tax['transactionAmount'] = $temp_tax['transactionAmount'];
                            $data_fc_tax['supplierCurrencyID'] = $temp_tax['supplierCurrencyID'];
                            $data_fc_tax['supplierCurrency'] = $temp_tax['supplierCurrency'];
                            $data_fc_tax['supplierCurrencyExchangeRate'] = $temp_tax['supplierCurrencyExchangeRate'];
                            $data_fc_tax['supplierCurrencyAmount'] = $temp_tax['supplierCurrencyAmount'];
                            $data_fc_tax['supplierCurrencyDecimalPlaces'] = $temp_tax['supplierCurrencyDecimalPlaces'];
                            $data_fc_tax['companyLocalCurrencyID'] = $temp_tax['companyLocalCurrencyID'];
                            $data_fc_tax['companyLocalCurrency'] = $temp_tax['companyLocalCurrency'];
                            $data_fc_tax['companyLocalExchangeRate'] = $temp_tax['companyLocalExchangeRate'];
                            $data_fc_tax['companyLocalAmount'] = $temp_tax['companyLocalAmount'];
                            $data_fc_tax['companyReportingCurrencyID'] = $temp_tax['companyReportingCurrencyID'];
                            $data_fc_tax['companyReportingCurrency'] = $temp_tax['companyReportingCurrency'];
                            $data_fc_tax['companyReportingExchangeRate'] = $temp_tax['companyReportingExchangeRate'];
                            $data_fc_tax['companyReportingAmount'] = $temp_tax['companyReportingAmount'];
                            $data_fc_tax['GLAutoID'] = $temp_tax['GLAutoID'];
                            $data_fc_tax['systemGLCode'] = $temp_tax['systemGLCode'];
                            $data_fc_tax['GLCode'] = $temp_tax['GLCode'];
                            $data_fc_tax['GLDescription'] = $temp_tax['GLDescription'];
                            $data_fc_tax['GLType'] = $temp_tax['GLType'];
                            $data_fc_tax['segmentID'] = $temp_tax['segmentID'];
                            $data_fc_tax['segmentCode'] = $temp_tax['segmentCode'];
                            $data_fc_tax['companyID'] = $temp_tax['companyID'];
                            $data_fc_tax['companyCode'] = $temp_tax['companyCode'];
                            $data_fc_tax['createdUserGroup'] = $this->common_data['user_group'];
                            $data_fc_tax['createdPCID'] = $this->common_data['current_pc'];
                            $data_fc_tax['createdUserID'] = $this->common_data['current_userID'];
                            $data_fc_tax['createdUserName'] = $this->common_data['current_user'];
                            $data_fc_tax['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoicedetails', $data_fc_tax);
                        }*/
                    }

                    /*private Company*/

                    $this->db->select('groupSegmentID');
                    $this->db->where('segmentID', $tempmaster['segmentID']);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupsegmentdetails');
                    $pvtgroupsegment = $this->db->get()->row_array();

                    $this->db->select('segmentID');
                    $this->db->where('groupSegmentID', $pvtgroupsegment['groupSegmentID']);
                    $this->db->where('companyID', $company['pvtCompanyID']);
                    $this->db->from('srp_erp_groupsegmentdetails');
                    $pvtsegment = $this->db->get()->row_array();
                    $pvtsegmentcode= get_segment_code($pvtsegment['segmentID']);

                    $this->db->select('groupCustomerMasterID');
                    $this->db->where('customerMasterID', $tempmaster['customerID']);
                    $this->db->where('companyID', current_companyID());
                    $this->db->from('srp_erp_groupcustomerdetails');
                    $pvtgroupcustomer = $this->db->get()->row_array();

                    $this->db->select('customerMasterID');
                    $this->db->where('groupCustomerMasterID', $pvtgroupcustomer['groupCustomerMasterID']);
                    $this->db->where('companyID', $company['pvtCompanyID']);
                    $this->db->from('srp_erp_groupcustomerdetails');
                    $pvtcustomer = $this->db->get()->row_array();

                    $customerDe=get_customer_details($pvtcustomer['customerMasterID'],$company['pvtCompanyID']);
                    $companyDe=get_company_details($company['pvtCompanyID']);


                    $data_pvt_master['documentID'] = 'CINV';
                    $data_pvt_master['tempInvoiceID'] = trim($tempmaster['invoiceAutoID'] ?? '');
                    $data_pvt_master['companyFinanceYearID'] = trim($pvtFinanceperiod['companyFinanceYearID'] ?? '');
                    $data_pvt_master['companyFinanceYear'] = trim($pvtFinanceperiod['beginingDate'].'-'.$pvtFinanceperiod['endingDate']);
                    $data_pvt_master['contactPersonName'] = trim($tempmaster['contactPersonName'] ?? '');
                    $data_pvt_master['contactPersonNumber'] = trim($tempmaster['contactPersonNumber'] ?? '');
                    $data_pvt_master['FYBegin'] = trim($pvtFinanceperiod['beginingDate'] ?? '');
                    $data_pvt_master['FYEnd'] = trim($pvtFinanceperiod['endingDate'] ?? '');
                    $data_pvt_master['companyFinancePeriodID'] = trim($pvtFinanceperiod['companyFinancePeriodID'] ?? '');
                    $data_pvt_master['invoiceDate'] = trim($tempmaster['invoiceDate'] ?? '');
                    $data_pvt_master['customerInvoiceDate'] = trim($tempmaster['customerInvoiceDate'] ?? '');
                    $data_pvt_master['invoiceDueDate'] = trim($tempmaster['invoiceDueDate'] ?? '');
                    $data_pvt_master['invoiceNarration'] = trim_desc($tempmaster['invoiceNarration']);
                    $data_pvt_master['invoiceNote'] = trim($tempmaster['invoiceNote'] ?? '');
                    $data_pvt_master['segmentID'] = trim($pvtsegment['segmentID'] ?? '');
                    $data_pvt_master['segmentCode'] = trim($pvtsegmentcode);
                    $data_pvt_master['salesPersonID'] = trim($tempmaster['salesPersonID'] ?? '');
                    if ($data_pvt_master['salesPersonID']) {
                        $data_pvt_master['SalesPersonCode'] = trim($tempmaster['SalesPersonCode'] ?? '');
                    }
                    $data_pvt_master['invoiceType'] = trim($tempmaster['invoiceType'] ?? '');
                    $data_pvt_master['showTaxSummaryYN'] = trim($tempmaster['showTaxSummaryYN'] ?? '');
                    $data_pvt_master['referenceNo'] = trim($tempmaster['referenceNo'] ?? '');
                    $data_pvt_master['isPrintDN'] = trim($tempmaster['isPrintDN'] ?? '');
                    $data_pvt_master['customerID'] = $pvtcustomer['customerMasterID'];
                    $data_pvt_master['customerSystemCode'] = $customerDe['customerSystemCode'];
                    $data_pvt_master['customerName'] = $customerDe['customerName'];
                    $data_pvt_master['customerAddress'] = $customerDe['customerAddress1'];
                    $data_pvt_master['customerTelephone'] = $customerDe['customerTelephone'];
                    $data_pvt_master['customerFax'] = $customerDe['customerFax'];
                    $data_pvt_master['customerEmail'] = $customerDe['customerEmail'];
                    $data_pvt_master['customerReceivableAutoID'] = $customerDe['receivableAutoID'];
                    $data_pvt_master['customerReceivableSystemGLCode'] = $customerDe['receivableSystemGLCode'];
                    $data_pvt_master['customerReceivableGLAccount'] = $customerDe['receivableGLAccount'];
                    $data_pvt_master['customerReceivableDescription'] = $customerDe['receivableDescription'];
                    $data_pvt_master['customerReceivableType'] = $customerDe['receivableType'];
                    $data_pvt_master['customerCurrency'] = $customerDe['customerCurrency'];
                    $data_pvt_master['customerCurrencyID'] = $customerDe['customerCurrencyID'];
                    $data_pvt_master['customerCurrencyDecimalPlaces'] = $customerDe['customerCurrencyDecimalPlaces'];
                    $data_pvt_master['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_pvt_master['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_pvt_master['modifiedUserName'] = $this->common_data['current_user'];
                    $data_pvt_master['modifiedDateTime'] = $this->common_data['current_date'];
                    $data_pvt_master['transactionCurrencyID'] = trim($tempmaster['transactionCurrencyID'] ?? '');
                    $data_pvt_master['transactionCurrency'] = trim($tempmaster['transactionCurrency'] ?? '');
                    $data_pvt_master['transactionExchangeRate'] = $tempmaster['transactionExchangeRate'];
                    $data_pvt_master['transactionCurrencyDecimalPlaces'] = $tempmaster['transactionCurrencyDecimalPlaces'];
                    $data_pvt_master['companyLocalCurrencyID'] = $companyDe['company_default_currencyID'];
                    $data_pvt_master['companyLocalCurrency'] = $companyDe['company_default_currency'];
                    $default_currency = currency_conversionID($data_pvt_master['transactionCurrencyID'], $data_pvt_master['companyLocalCurrencyID']);
                    $data_pvt_master['companyLocalExchangeRate'] = $tempmaster['companyLocalExchangeRate'];
                    $data_pvt_master['companyLocalCurrencyDecimalPlaces'] = $companyDe['company_default_decimal'];
                    $data_pvt_master['companyReportingCurrency'] = $companyDe['company_reporting_currency'];
                    $data_pvt_master['companyReportingCurrencyID'] = $companyDe['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($data_pvt_master['transactionCurrencyID'], $data_pvt_master['companyReportingCurrencyID']);
                    $data_pvt_master['companyReportingExchangeRate'] = $tempmaster['companyReportingExchangeRate'];
                    $data_pvt_master['companyReportingCurrencyDecimalPlaces'] = $companyDe['company_reporting_decimal'];
                    $customer_currency = currency_conversionID($data_pvt_master['transactionCurrencyID'], $data_pvt_master['customerCurrencyID']);
                    $data_pvt_master['customerCurrencyExchangeRate'] = $tempmaster['customerCurrencyExchangeRate'];
                    $data_pvt_master['customerCurrencyDecimalPlaces'] = $customerDe['customerCurrencyDecimalPlaces'];
                    $data_pvt_master['transactionAmount'] = ($tempmaster['transactionAmount']* $customer['pvtCompanyPercentage']) / 100;
                    $data_pvt_master['companyLocalAmount'] = ($tempmaster['companyLocalAmount']* $customer['pvtCompanyPercentage']) / 100;
                    $data_pvt_master['companyReportingAmount'] = ($tempmaster['companyReportingAmount']* $customer['pvtCompanyPercentage']) / 100;
                    $data_pvt_master['customerCurrencyAmount'] = (($tempmaster['customerCurrencyAmount']/$tempmaster['customerCurrencyExchangeRate'])* $customer['pvtCompanyPercentage']) / 100;
                    $data_pvt_master['confirmedYN'] = 1;
                    $data_pvt_master['confirmedByEmpID'] = current_userID();
                    $data_pvt_master['confirmedByName'] = current_user();
                    $data_pvt_master['confirmedDate'] = $this->common_data['current_date'];
                    $data_pvt_master['approvedYN'] = 1;
                    $data_pvt_master['currentLevelNo'] = 1;
                    $data_pvt_master['approvedbyEmpID'] = current_userID();
                    $data_pvt_master['approvedbyEmpName'] = current_user();
                    $data_pvt_master['approvedDate'] = $this->common_data['current_date'];
                    $this->load->library('sequence');
                    $data_pvt_master['companyCode'] = $companyDe['company_code'];
                    $data_pvt_master['companyID'] = $company['pvtCompanyID'];
                    $data_pvt_master['createdUserGroup'] = $this->common_data['user_group'];
                    $data_pvt_master['createdPCID'] = $this->common_data['current_pc'];
                    $data_pvt_master['createdUserID'] = $this->common_data['current_userID'];
                    $data_pvt_master['createdUserName'] = $this->common_data['current_user'];
                    $data_pvt_master['createdDateTime'] = $this->common_data['current_date'];
                   /* $data_pvt_master['invoiceCode'] = $this->sequence->sequence_generator_byback($data_pvt_master['documentID'],0,$company['pvtCompanyID'],$companyDe['company_code']);
                    $data_pvt_master['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');*/

                   /* to generate private company document code */
                    $pvtCodeCreate = explode('/', $tempmaster['invoiceCode']);
                    $a = 1;
                    $sysCode = '';
                    foreach ($pvtCodeCreate as $var){
                        if($a == 1){
                            $sysCode = $companyDe['company_code'];
                        } else {
                            $sysCode = $sysCode . '/' . $var;
                        }
                        $a++;
                    }

                    $data_pvt_master['invoiceCode'] = $sysCode;
                    $data_fc_master['deliveryNoteSystemCode'] = $tempmaster['deliveryNoteSystemCode'];

                    $this->db->insert('srp_erp_customerinvoicemaster', $data_pvt_master);
                    $pvt_master_last_id = $this->db->insert_id();

                    $data_app_pvt['companyID'] = $company['pvtCompanyID'];
                    $data_app_pvt['companyCode'] = $companyDe['company_code'];
                    $data_app_pvt['departmentID'] = 'CINV';
                    $data_app_pvt['documentID'] = 'CINV';
                    $data_app_pvt['documentSystemCode'] = $pvt_master_last_id;
                    $data_app_pvt['documentCode'] = $data_pvt_master['invoiceCode'];
                    $data_app_pvt['table_name'] = 'srp_erp_customerinvoicemaster';
                    $data_app_pvt['table_unique_field_name'] = 'invoiceAutoID';
                    $data_app_pvt['documentDate'] = $tempmaster['invoiceDate'];
                    $data_app_pvt['approvalLevelID'] = 1;
                    $data_app_pvt['roleID'] = null;
                    $data_app_pvt['approvalGroupID'] = $this->common_data['user_group'];
                    $data_app_pvt['roleLevelOrder'] = null;
                    $data_app_pvt['docConfirmedDate'] = $this->common_data['current_date'];
                    $data_app_pvt['docConfirmedByEmpID'] = $this->common_data['current_userID'];
                    $data_app_pvt['approvedEmpID'] = $this->common_data['current_userID'];
                    $data_app_pvt['isReverseApplicableYN'] = 0;
                    $data_app_pvt['approvedYN'] = 1;
                    $data_app_pvt['approvedDate'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_documentapproved', $data_app_pvt);


                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicedetails_temp');
                    $pvttemp_detail = $this->db->get()->result_array();
                    foreach ($pvttemp_detail as $Dtemp) {
                        $this->db->select('groupSegmentID');
                        $this->db->where('segmentID', $Dtemp['segmentID']);
                        $this->db->where('companyID', current_companyID());
                        $this->db->from('srp_erp_groupsegmentdetails');
                        $pvtsegDet = $this->db->get()->row_array();

                        $this->db->select('segmentID');
                        $this->db->where('groupSegmentID', $pvtsegDet['groupSegmentID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupsegmentdetails');
                        $pvtsegmentDet = $this->db->get()->row_array();
                        $pvtsegmentDecode= get_segment_code($pvtsegmentDet['segmentID']);

                        $this->db->select('groupWarehouseMasterID');
                        $this->db->where('warehosueMasterID', $Dtemp['wareHouseAutoID']);
                        $this->db->where('companyID', current_companyID());
                        $this->db->from('srp_erp_groupwarehousedetails');
                        $groupware = $this->db->get()->row_array();

                        $this->db->select('warehosueMasterID');
                        $this->db->where('groupWarehouseMasterID', $groupware['groupWarehouseMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupwarehousedetails');
                        $pvtware = $this->db->get()->row_array();
                        $warehouseDet=get_warehouse_details($pvtware['warehosueMasterID'],$company['pvtCompanyID']);

                        $this->db->select('groupItemMasterID');
                        $this->db->where('ItemAutoID', $Dtemp['itemAutoID']);
                        $this->db->where('companyID', current_companyID());
                        $this->db->from('srp_erp_groupitemmasterdetails');
                        $groupitemDet = $this->db->get()->row_array();

                        $this->db->select('ItemAutoID');
                        $this->db->where('groupItemMasterID', $groupitemDet['groupItemMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupitemmasterdetails');
                        $pvtitem = $this->db->get()->row_array();
                        $itmDetls=get_item_details($pvtitem['ItemAutoID'],$company['pvtCompanyID']);

                        $this->db->select('groupUOMMasterID');
                        $this->db->where('UOMMasterID', $Dtemp['unitOfMeasureID']);
                        $this->db->where('companyID', current_companyID());
                        $this->db->from('srp_erp_groupuomdetails');
                        $groupuomD = $this->db->get()->row_array();

                        $this->db->select('UOMMasterID');
                        $this->db->where('groupUOMMasterID', $groupuomD['groupUOMMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupuomdetails');
                        $PVTgrpuomD = $this->db->get()->row_array();
                        $this->db->select('UnitShortCode');
                        $this->db->where('UnitID', $PVTgrpuomD['UOMMasterID']);
                        $this->db->from('srp_erp_unit_of_measure');
                        $UnitShortCode = $this->db->get()->row_array();

                        $this->db->select('groupUOMMasterID');
                        $this->db->where('UOMMasterID', $Dtemp['defaultUOMID']);
                        $this->db->where('companyID', current_companyID());
                        $this->db->from('srp_erp_groupuomdetails');
                        $groupDefuomD = $this->db->get()->row_array();

                        $this->db->select('UOMMasterID');
                        $this->db->where('groupUOMMasterID', $groupDefuomD['groupUOMMasterID']);
                        $this->db->where('companyID', $company['pvtCompanyID']);
                        $this->db->from('srp_erp_groupuomdetails');
                        $PVTgrpDefuomD = $this->db->get()->row_array();
                        $this->db->select('UnitShortCode');
                        $this->db->where('UnitID', $PVTgrpDefuomD['UOMMasterID']);
                        $this->db->from('srp_erp_unit_of_measure');
                        $UnitShortCodeDef = $this->db->get()->row_array();

                        if($Dtemp['type']=='GL'){
                            $this->db->select('groupChartofAccountMasterID');
                            $this->db->where('chartofAccountID', $Dtemp['revenueGLAutoID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $groupgldet = $this->db->get()->row_array();

                            $this->db->select('chartofAccountID');
                            $this->db->where('groupChartofAccountMasterID', $groupgldet['groupChartofAccountMasterID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $PVTrevgl = $this->db->get()->row_array();
                            $GLdetailrev=get_coa_details($PVTrevgl['chartofAccountID'],$company['pvtCompanyID']);
                        }

                        $data_pvt_detail['invoiceAutoID'] = $pvt_master_last_id;
                        $data_pvt_detail['tempinvoiceDetailID'] = $Dtemp['invoiceDetailsAutoID'];
                        $data_pvt_detail['type'] = $Dtemp['type'];
                        $data_pvt_detail['contractAutoID'] = $Dtemp['contractAutoID'];
                        $data_pvt_detail['contractDetailsAutoID'] = $Dtemp['contractDetailsAutoID'];
                        $data_pvt_detail['contractCode'] = $Dtemp['contractCode'];
                        $data_pvt_detail['projectID'] = $Dtemp['projectID'];
                        $data_pvt_detail['projectExchangeRate'] = $Dtemp['projectExchangeRate'];
                        if($Dtemp['type']=='Item'){
                            $data_pvt_detail['itemAutoID'] = $pvtitem['ItemAutoID'];
                            $data_pvt_detail['itemSystemCode'] = $itmDetls['itemSystemCode'];
                            $data_pvt_detail['itemDescription'] = $itmDetls['itemDescription'];
                            $data_pvt_detail['itemCategory'] = $itmDetls['mainCategory'];
                            $data_pvt_detail['expenseGLAutoID'] = $itmDetls['costGLAutoID'];
                            $data_pvt_detail['expenseSystemGLCode'] = $itmDetls['costSystemGLCode'];
                            $data_pvt_detail['expenseGLCode'] = $itmDetls['costGLCode'];
                            $data_pvt_detail['expenseGLDescription'] = $itmDetls['costDescription'];
                            $data_pvt_detail['expenseGLType'] = $itmDetls['costType'];
                            $data_pvt_detail['revenueGLAutoID'] = $itmDetls['revanueGLAutoID'];
                            $data_pvt_detail['revenueGLCode'] = $itmDetls['revanueGLCode'];
                            $data_pvt_detail['revenueSystemGLCode'] = $itmDetls['revanueSystemGLCode'];
                            $data_pvt_detail['revenueGLDescription'] = $itmDetls['revanueDescription'];
                            $data_pvt_detail['revenueGLType'] = $itmDetls['revanueType'];
                            $data_pvt_detail['assetGLAutoID'] = $itmDetls['assteGLAutoID'];
                            $data_pvt_detail['assetGLCode'] = $itmDetls['assteGLCode'];
                            $data_pvt_detail['assetSystemGLCode'] = $itmDetls['assteSystemGLCode'];
                            $data_pvt_detail['assetGLDescription'] = $itmDetls['assteDescription'];
                            $data_pvt_detail['assetGLType'] = $itmDetls['assteType'];
                        }else{
                            $data_pvt_detail['itemAutoID'] = null;
                            $data_pvt_detail['itemSystemCode'] = null;
                            $data_pvt_detail['itemDescription'] = null;
                            $data_pvt_detail['itemCategory'] = null;
                            $data_pvt_detail['expenseGLAutoID'] = null;
                            $data_pvt_detail['expenseSystemGLCode'] = null;
                            $data_pvt_detail['expenseGLCode'] = null;
                            $data_pvt_detail['expenseGLDescription'] = null;
                            $data_pvt_detail['expenseGLType'] = null;
                            $data_pvt_detail['revenueGLAutoID'] = $GLdetailrev['GLAutoID'];
                            $data_pvt_detail['revenueGLCode'] = $GLdetailrev['GLSecondaryCode'];
                            $data_pvt_detail['revenueSystemGLCode'] = $GLdetailrev['systemAccountCode'];
                            $data_pvt_detail['revenueGLDescription'] = $GLdetailrev['GLDescription'];
                            $data_pvt_detail['revenueGLType'] = $GLdetailrev['masterCategory'];
                            $data_pvt_detail['assetGLAutoID'] = null;
                            $data_pvt_detail['assetGLCode'] = null;
                            $data_pvt_detail['assetSystemGLCode'] = null;
                            $data_pvt_detail['assetGLDescription'] = null;
                            $data_pvt_detail['assetGLType'] = null;
                        }
                        $data_pvt_detail['taxMasterAutoID'] = $Dtemp['taxMasterAutoID'];
                        $data_pvt_detail['taxPercentage'] = $Dtemp['taxPercentage'];
                        $data_pvt_detail['wareHouseAutoID'] = $pvtware['warehosueMasterID'];
                        $data_pvt_detail['wareHouseCode'] = $warehouseDet['wareHouseCode'];
                        $data_pvt_detail['wareHouseLocation'] = $warehouseDet['wareHouseLocation'];
                        $data_pvt_detail['wareHouseDescription'] = $warehouseDet['wareHouseDescription'];
                        $data_pvt_detail['defaultUOMID'] = $PVTgrpDefuomD['UOMMasterID'];
                        $data_pvt_detail['defaultUOM'] = $UnitShortCodeDef['UnitShortCode'];
                        $data_pvt_detail['unitOfMeasureID'] = $PVTgrpuomD['UOMMasterID'];
                        $data_pvt_detail['unitOfMeasure'] = $UnitShortCode['UnitShortCode'];
                        $data_pvt_detail['conversionRateUOM'] = $Dtemp['conversionRateUOM'];
                        $data_pvt_detail['contractQty'] = $Dtemp['contractQty'];
                        $data_pvt_detail['contractAmount'] = $Dtemp['contractAmount'];

                        $data_pvt_detail['requestedQty'] = ($Dtemp['requestedQty'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['noOfItems'] = ($Dtemp['noOfItems'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['grossQty'] = ($Dtemp['grossQty'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['noOfUnits'] = $Dtemp['noOfUnits'];
                        $data_pvt_detail['deduction'] = ($Dtemp['deduction'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['comment'] = $Dtemp['comment'];
                        $data_pvt_detail['remarks'] = $Dtemp['remarks'];
                        $data_pvt_detail['description'] = $Dtemp['description'];
                        $data_pvt_detail['companyLocalWacAmount'] = $Dtemp['companyLocalWacAmount'];
                        $data_pvt_detail['unittransactionAmount'] = $Dtemp['unittransactionAmount'];
                        $data_pvt_detail['transactionAmount'] = ($Dtemp['transactionAmount'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['companyLocalAmount'] = ($Dtemp['companyLocalAmount'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['companyReportingAmount'] = ($Dtemp['companyReportingAmount'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['customerAmount'] = ($Dtemp['customerAmount'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['segmentID'] = $pvtsegmentDet['segmentID'];
                        $data_pvt_detail['segmentCode'] = $pvtsegmentDecode;
                        $data_pvt_detail['companyID'] = $company['pvtCompanyID'];
                        $data_pvt_detail['companyCode'] = $companyDe['company_code'];
                        $data_pvt_detail['discountPercentage'] = $Dtemp['discountPercentage'];
                        $data_pvt_detail['discountAmount'] = $Dtemp['discountAmount'];
                        $data_pvt_detail['taxDescription'] = $Dtemp['taxDescription'];
                        $data_pvt_detail['taxAmount'] = $Dtemp['taxAmount'];
                        $data_pvt_detail['totalAfterTax'] = ($Dtemp['totalAfterTax'] * $customer['pvtCompanyPercentage']) / 100;
                        $data_pvt_detail['taxShortCode'] = $Dtemp['taxShortCode'];
                        $data_pvt_detail['taxSupplierAutoID'] = $Dtemp['taxSupplierAutoID'];
                        $data_pvt_detail['taxSupplierSystemCode'] = $Dtemp['taxSupplierSystemCode'];
                        $data_pvt_detail['taxSupplierName'] = $Dtemp['taxSupplierName'];
                        $data_pvt_detail['taxSupplierliabilityAutoID'] = $Dtemp['taxSupplierliabilityAutoID'];
                        $data_pvt_detail['taxSupplierliabilitySystemGLCode'] = $Dtemp['taxSupplierliabilitySystemGLCode'];
                        $data_pvt_detail['taxSupplierliabilityGLAccount'] = $Dtemp['taxSupplierliabilityGLAccount'];
                        $data_pvt_detail['taxSupplierliabilityDescription'] = $Dtemp['taxSupplierliabilityDescription'];
                        $data_pvt_detail['taxSupplierliabilityType'] = $Dtemp['taxSupplierliabilityType'];
                        $data_pvt_detail['taxSupplierCurrencyID'] = $Dtemp['taxSupplierCurrencyID'];
                        $data_pvt_detail['taxSupplierCurrency'] = $Dtemp['taxSupplierCurrency'];
                        $data_pvt_detail['taxSupplierCurrencyExchangeRate'] = $Dtemp['taxSupplierCurrencyExchangeRate'];
                        $data_pvt_detail['taxSupplierCurrencyAmount'] = $Dtemp['taxSupplierCurrencyAmount'];
                        $data_pvt_detail['taxSupplierCurrencyDecimalPlaces'] = $Dtemp['taxSupplierCurrencyDecimalPlaces'];
                        $data_pvt_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_pvt_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_pvt_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_pvt_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_pvt_detail['createdDateTime'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_customerinvoicedetails', $data_pvt_detail);
                        $pvt_detail_last_id= $this->db->insert_id();

                        $this->db->select('*');
                        $this->db->where('documentMasterAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->where('documentDetailAutoID', $Dtemp['invoiceDetailsAutoID']);
                        $this->db->where('documentID', 'HCINV');
                        $this->db->from('srp_erp_taxledger');
                        $pvt_tax_ledger = $this->db->get()->result_array();
                        if(!empty($pvt_tax_ledger)){
                            foreach($pvt_tax_ledger as $txled){
                                $this->db->select('groupChartofAccountMasterID');
                                $this->db->where('chartofAccountID', $txled['taxGlAutoID']);
                                $this->db->where('companyID', current_companyID());
                                $this->db->from('srp_erp_groupchartofaccountdetails');
                                $pvttxgl = $this->db->get()->row_array();

                                $this->db->select('chartofAccountID');
                                $this->db->where('groupChartofAccountMasterID', $pvttxgl['groupChartofAccountMasterID']);
                                $this->db->where('companyID', $company['pvtCompanyID']);
                                $this->db->from('srp_erp_groupchartofaccountdetails');
                                $PVTtxglcode = $this->db->get()->row_array();

                                $this->db->select('groupTaxMasterID');
                                $this->db->where('taxMasterID', $txled['taxMasterID']);
                                $this->db->where('companyID', current_companyID());
                                $this->db->from('srp_erp_grouptaxdetail');
                                $grouptaxleg = $this->db->get()->row_array();
                                $this->db->select('taxMasterID');
                                $this->db->where('groupTaxMasterID', $grouptaxleg['groupTaxMasterID']);
                                $this->db->where('companyID', $company['pvtCompanyID']);
                                $this->db->from('srp_erp_grouptaxdetail');
                                $PVTtaxdleg = $this->db->get()->row_array();

                                $this->db->select('groupTaxCalculationformulaID');
                                $this->db->where('taxCalculationFormulaID', $txled['taxFormulaMasterID']);
                                $this->db->where('companyID', current_companyID());
                                $this->db->from('srp_erp_grouptaxcalculationformuladetails');
                                $grouptaxformu = $this->db->get()->row_array();
                                $this->db->select('taxCalculationFormulaID');
                                $this->db->where('groupTaxCalculationformulaID', $grouptaxformu['groupTaxCalculationformulaID']);
                                $this->db->where('companyID', $company['pvtCompanyID']);
                                $this->db->from('srp_erp_grouptaxcalculationformuladetails');
                                $PVTtafoumux = $this->db->get()->row_array();

                                $this->db->select('supplierAutoID');
                                $this->db->where('taxMasterAutoID', $PVTtaxdleg['taxMasterID']);
                                $this->db->from('srp_erp_taxmaster');
                                $PVTauthid = $this->db->get()->row_array();

                                $data_pvt_tax_ledgr['documentID'] = 'CINV';
                                $data_pvt_tax_ledgr['documentMasterAutoID'] = $pvt_master_last_id;
                                $data_pvt_tax_ledgr['documentDetailAutoID'] = $pvt_detail_last_id;
                                $data_pvt_tax_ledgr['taxDetailAutoID'] = $txled['taxDetailAutoID'];
                                $data_pvt_tax_ledgr['taxFormulaMasterID'] = $PVTtafoumux['taxCalculationFormulaID'];
                                $data_pvt_tax_ledgr['taxFormulaDetailID'] = null;
                                $data_pvt_tax_ledgr['taxMasterID'] = $PVTtaxdleg['taxMasterID'];
                                $data_pvt_tax_ledgr['amount'] = ($txled['amount']* $customer['pvtCompanyPercentage']) / 100;
                                $data_pvt_tax_ledgr['formula'] = $txled['formula'];
                                $data_pvt_tax_ledgr['taxAuthorityAutoID'] = $PVTauthid['supplierAutoID'];
                                $data_pvt_tax_ledgr['taxGlAutoID'] = $PVTtxglcode['chartofAccountID'];
                                $data_pvt_tax_ledgr['companyID'] = $company['pvtCompanyID'];
                                $data_pvt_tax_ledgr['companyCode'] = $companyDe['company_code'];
                                $data_pvt_tax_ledgr['createdUserGroup'] = $this->common_data['user_group'];
                                $data_pvt_tax_ledgr['createdPCID'] = $this->common_data['current_pc'];
                                $data_pvt_tax_ledgr['createdUserID'] = $this->common_data['current_userID'];
                                $data_pvt_tax_ledgr['createdUserName'] = $this->common_data['current_user'];
                                $data_pvt_tax_ledgr['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_taxledger', $data_pvt_tax_ledgr);
                            }
                        }
                    }

                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
                    $pvt_temp_discount = $this->db->get()->result_array();
                    if(!empty($pvt_temp_discount)){
                        foreach($pvt_temp_discount as $disc){
                            $this->db->select('groupChartofAccountMasterID');
                            $this->db->where('chartofAccountID', $disc['GLAutoID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $pvtdisgl = $this->db->get()->row_array();

                            $this->db->select('chartofAccountID');
                            $this->db->where('groupChartofAccountMasterID', $pvtdisgl['groupChartofAccountMasterID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $PVTdisglcode = $this->db->get()->row_array();

                            $GLdetail=get_coa_details($PVTdisglcode['chartofAccountID'],$company['pvtCompanyID']);

                            $this->db->select('groupDiscountExtraChargeID');
                            $this->db->where('discountExtraChargeID', $disc['discountMasterAutoID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                            $groupdiscpvt = $this->db->get()->row_array();

                            $this->db->select('discountExtraChargeID');
                            $this->db->where('groupDiscountExtraChargeID', $groupdiscpvt['groupDiscountExtraChargeID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                            $PVTdiscD = $this->db->get()->row_array();

                            $this->db->select('groupSegmentID');
                            $this->db->where('segmentID', $disc['segmentID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupsegmentdetails');
                            $pvtsegDetD = $this->db->get()->row_array();

                            $this->db->select('segmentID');
                            $this->db->where('groupSegmentID', $pvtsegDetD['groupSegmentID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupsegmentdetails');
                            $pvtsegmentDets = $this->db->get()->row_array();
                            $pvtsegmentDecodeD= get_segment_code($pvtsegmentDets['segmentID']);


                            $data_pvt_discount['invoiceAutoID'] = $pvt_master_last_id;
                            $data_pvt_discount['referenceNo'] = $disc['referenceNo'];
                            $data_pvt_discount['discountMasterAutoID'] = $PVTdiscD['discountExtraChargeID'];
                            $data_pvt_discount['isChargeToExpense'] = $disc['isChargeToExpense'];
                            $data_pvt_discount['discountDescription'] = $disc['discountDescription'];
                            $data_pvt_discount['discountPercentage'] = $disc['discountPercentage'];
                            $data_pvt_discount['transactionCurrencyID'] = $disc['transactionCurrencyID'];
                            $data_pvt_discount['transactionCurrency'] = $disc['transactionCurrency'];
                            $data_pvt_discount['transactionExchangeRate'] = $disc['transactionExchangeRate'];
                            $data_pvt_discount['transactionCurrencyDecimalPlaces'] = $disc['transactionCurrencyDecimalPlaces'];
                            $data_pvt_discount['transactionAmount'] = ($disc['transactionAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_discount['customerCurrencyID'] = $disc['customerCurrencyID'];
                            $data_pvt_discount['customerCurrency'] = $disc['customerCurrency'];
                            $data_pvt_discount['customerCurrencyExchangeRate'] = $disc['customerCurrencyExchangeRate'];
                            $data_pvt_discount['customerCurrencyAmount'] = ($disc['customerCurrencyAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_discount['customerCurrencyDecimalPlaces'] = $disc['customerCurrencyDecimalPlaces'];
                            $data_pvt_discount['companyLocalCurrencyID'] = $disc['companyLocalCurrencyID'];
                            $data_pvt_discount['companyLocalCurrency'] = $disc['companyLocalCurrency'];
                            $data_pvt_discount['companyLocalExchangeRate'] = $disc['companyLocalExchangeRate'];
                            $data_pvt_discount['companyLocalAmount'] = ($disc['companyLocalAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_discount['companyReportingCurrencyID'] = $disc['companyReportingCurrencyID'];
                            $data_pvt_discount['companyReportingCurrency'] = $disc['companyReportingCurrency'];
                            $data_pvt_discount['companyReportingExchangeRate'] = $disc['companyReportingExchangeRate'];
                            $data_pvt_discount['companyReportingAmount'] = ($disc['companyReportingAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_discount['GLAutoID'] = $PVTdisglcode['chartofAccountID'];
                            $data_pvt_discount['systemGLCode'] = $GLdetail['systemAccountCode'];
                            $data_pvt_discount['GLCode'] = $GLdetail['GLSecondaryCode'];
                            $data_pvt_discount['GLDescription'] = $GLdetail['GLDescription'];
                            $data_pvt_discount['GLType'] = $GLdetail['masterCategory'];
                            $data_pvt_discount['segmentID'] = $pvtsegmentDets['segmentID'];
                            $data_pvt_discount['segmentCode'] = $pvtsegmentDecodeD;
                            $data_pvt_discount['companyID'] = $company['pvtCompanyID'];
                            $data_pvt_discount['companyCode'] = $companyDe['company_code'];
                            $data_pvt_discount['createdUserGroup'] = $this->common_data['user_group'];
                            $data_pvt_discount['createdPCID'] = $this->common_data['current_pc'];
                            $data_pvt_discount['createdUserID'] = $this->common_data['current_userID'];
                            $data_pvt_discount['createdUserName'] = $this->common_data['current_user'];
                            $data_pvt_discount['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoicediscountdetails', $data_pvt_discount);
                        }
                    }

                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoiceextrachargedetails_temp');
                    $pvt_temp_extra = $this->db->get()->result_array();
                    if(!empty($pvt_temp_extra)){
                        foreach($pvt_temp_extra as $extra){
                            $this->db->select('groupChartofAccountMasterID');
                            $this->db->where('chartofAccountID', $extra['GLAutoID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $pvtextgl = $this->db->get()->row_array();

                            $this->db->select('chartofAccountID');
                            $this->db->where('groupChartofAccountMasterID', $pvtextgl['groupChartofAccountMasterID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupchartofaccountdetails');
                            $PVTextglcode = $this->db->get()->row_array();
                            $GLdetail=get_coa_details($PVTextglcode['chartofAccountID'],$company['pvtCompanyID']);

                            $this->db->select('groupDiscountExtraChargeID');
                            $this->db->where('discountExtraChargeID', $extra['extraChargeMasterAutoID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                            $groupextrapvt = $this->db->get()->row_array();

                            $this->db->select('discountExtraChargeID');
                            $this->db->where('groupDiscountExtraChargeID', $groupextrapvt['groupDiscountExtraChargeID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupdiscountextrachargesdetails');
                            $PVTextraD = $this->db->get()->row_array();

                            $this->db->select('groupSegmentID');
                            $this->db->where('segmentID', $extra['segmentID']);
                            $this->db->where('companyID', current_companyID());
                            $this->db->from('srp_erp_groupsegmentdetails');
                            $pvtsegextra = $this->db->get()->row_array();

                            $this->db->select('segmentID');
                            $this->db->where('groupSegmentID', $pvtsegextra['groupSegmentID']);
                            $this->db->where('companyID', $company['pvtCompanyID']);
                            $this->db->from('srp_erp_groupsegmentdetails');
                            $pvtsegmentextra = $this->db->get()->row_array();
                            $pvtsegmentDecodeExtra= get_segment_code($pvtsegmentextra['segmentID']);

                            $data_pvt_extra['invoiceAutoID'] = $pvt_master_last_id;
                            $data_pvt_extra['referenceNo'] = $extra['referenceNo'];
                            $data_pvt_extra['extraChargeMasterAutoID'] = $PVTextraD['discountExtraChargeID'];
                            $data_pvt_extra['isTaxApplicable'] = $extra['isTaxApplicable'];
                            $data_pvt_extra['extraChargeDescription'] = $extra['extraChargeDescription'];
                            $data_pvt_extra['transactionCurrencyID'] = $extra['transactionCurrencyID'];
                            $data_pvt_extra['transactionCurrency'] = $extra['transactionCurrency'];
                            $data_pvt_extra['transactionExchangeRate'] = $extra['transactionExchangeRate'];
                            $data_pvt_extra['transactionCurrencyDecimalPlaces'] = $extra['transactionCurrencyDecimalPlaces'];
                            $data_pvt_extra['transactionAmount'] = ($extra['transactionAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_extra['customerCurrencyID'] = $extra['customerCurrencyID'];
                            $data_pvt_extra['customerCurrency'] = $extra['customerCurrency'];
                            $data_pvt_extra['customerCurrencyExchangeRate'] = $extra['customerCurrencyExchangeRate'];
                            $data_pvt_extra['customerCurrencyAmount'] = ($extra['customerCurrencyAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_extra['customerCurrencyDecimalPlaces'] = $extra['customerCurrencyDecimalPlaces'];
                            $data_pvt_extra['companyLocalCurrencyID'] = $extra['companyLocalCurrencyID'];
                            $data_pvt_extra['companyLocalCurrency'] = $extra['companyLocalCurrency'];
                            $data_pvt_extra['companyLocalExchangeRate'] = $extra['companyLocalExchangeRate'];
                            $data_pvt_extra['companyLocalAmount'] = ($extra['companyLocalAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_extra['companyReportingCurrencyID'] = $extra['companyReportingCurrencyID'];
                            $data_pvt_extra['companyReportingCurrency'] = $extra['companyReportingCurrency'];
                            $data_pvt_extra['companyReportingExchangeRate'] = $extra['companyReportingExchangeRate'];
                            $data_pvt_extra['companyReportingAmount'] = ($extra['companyReportingAmount']* $customer['pvtCompanyPercentage']) / 100;
                            $data_pvt_extra['GLAutoID'] = $PVTextglcode['chartofAccountID'];
                            $data_pvt_extra['systemGLCode'] = $GLdetail['systemAccountCode'];
                            $data_pvt_extra['GLCode'] = $GLdetail['GLSecondaryCode'];
                            $data_pvt_extra['GLDescription'] = $GLdetail['GLDescription'];
                            $data_pvt_extra['GLType'] = $GLdetail['masterCategory'];
                            $data_pvt_extra['segmentID'] = $pvtsegmentextra['segmentID'];
                            $data_pvt_extra['segmentCode'] = $pvtsegmentDecodeExtra;
                            $data_pvt_extra['companyID'] = $company['pvtCompanyID'];
                            $data_pvt_extra['companyCode'] = $companyDe['company_code'];
                            $data_pvt_extra['createdUserGroup'] = $this->common_data['user_group'];
                            $data_pvt_extra['createdPCID'] = $this->common_data['current_pc'];
                            $data_pvt_extra['createdUserID'] = $this->common_data['current_userID'];
                            $data_pvt_extra['createdUserName'] = $this->common_data['current_user'];
                            $data_pvt_extra['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoiceextrachargedetails', $data_pvt_extra);
                        }
                    }

                    /*$this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicetaxdetails_temp');
                    $temp_tax = $this->db->get()->result_array();

                    if (!empty($temp_tax)) {
                        $data_fc_tax['invoiceAutoID'] = $fc_master_last_id;
                        $data_fc_tax['referenceNo'] = $temp_tax['referenceNo'];
                        $data_fc_tax['taxMasterAutoID'] = $temp_tax['taxMasterAutoID'];
                        $data_fc_tax['tempInvoiceTaxDetailID'] = $temp_tax['taxDetailAutoID'];
                        $data_fc_tax['taxDescription'] = $temp_tax['taxDescription'];
                        $data_fc_tax['taxShortCode'] = $temp_tax['taxShortCode'];
                        $data_fc_tax['taxPercentage'] = $temp_tax['taxPercentage'];
                        $data_fc_tax['supplierAutoID'] = $temp_tax['supplierAutoID'];
                        $data_fc_tax['supplierSystemCode'] = $temp_tax['supplierSystemCode'];
                        $data_fc_tax['supplierName'] = $temp_tax['supplierName'];
                        $data_fc_tax['transactionCurrencyID'] = $temp_tax['transactionCurrencyID'];
                        $data_fc_tax['transactionCurrency'] = $temp_tax['transactionCurrency'];
                        $data_fc_tax['transactionExchangeRate'] = $temp_tax['transactionExchangeRate'];
                        $data_fc_tax['transactionCurrencyDecimalPlaces'] = $temp_tax['transactionCurrencyDecimalPlaces'];
                        $data_fc_tax['transactionAmount'] = $temp_tax['transactionAmount'];
                        $data_fc_tax['supplierCurrencyID'] = $temp_tax['supplierCurrencyID'];
                        $data_fc_tax['supplierCurrency'] = $temp_tax['supplierCurrency'];
                        $data_fc_tax['supplierCurrencyExchangeRate'] = $temp_tax['supplierCurrencyExchangeRate'];
                        $data_fc_tax['supplierCurrencyAmount'] = $temp_tax['supplierCurrencyAmount'];
                        $data_fc_tax['supplierCurrencyDecimalPlaces'] = $temp_tax['supplierCurrencyDecimalPlaces'];
                        $data_fc_tax['companyLocalCurrencyID'] = $temp_tax['companyLocalCurrencyID'];
                        $data_fc_tax['companyLocalCurrency'] = $temp_tax['companyLocalCurrency'];
                        $data_fc_tax['companyLocalExchangeRate'] = $temp_tax['companyLocalExchangeRate'];
                        $data_fc_tax['companyLocalAmount'] = $temp_tax['companyLocalAmount'];
                        $data_fc_tax['companyReportingCurrencyID'] = $temp_tax['companyReportingCurrencyID'];
                        $data_fc_tax['companyReportingCurrency'] = $temp_tax['companyReportingCurrency'];
                        $data_fc_tax['companyReportingExchangeRate'] = $temp_tax['companyReportingExchangeRate'];
                        $data_fc_tax['companyReportingAmount'] = $temp_tax['companyReportingAmount'];
                        $data_fc_tax['GLAutoID'] = $temp_tax['GLAutoID'];
                        $data_fc_tax['systemGLCode'] = $temp_tax['systemGLCode'];
                        $data_fc_tax['GLCode'] = $temp_tax['GLCode'];
                        $data_fc_tax['GLDescription'] = $temp_tax['GLDescription'];
                        $data_fc_tax['GLType'] = $temp_tax['GLType'];
                        $data_fc_tax['segmentID'] = $temp_tax['segmentID'];
                        $data_fc_tax['segmentCode'] = $temp_tax['segmentCode'];
                        $data_fc_tax['companyID'] = $temp_tax['companyID'];
                        $data_fc_tax['companyCode'] = $temp_tax['companyCode'];
                        $data_fc_tax['createdUserGroup'] = $this->common_data['user_group'];
                        $data_fc_tax['createdPCID'] = $this->common_data['current_pc'];
                        $data_fc_tax['createdUserID'] = $this->common_data['current_userID'];
                        $data_fc_tax['createdUserName'] = $this->common_data['current_user'];
                        $data_fc_tax['createdDateTime'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_customerinvoicedetails', $data_fc_tax);
                    }*/
                    /*pvt company end*/

                    //$this->fc_double_entry($fc_master_last_id);
                }
            }

        }else{
            //if cap amount < company local amount
            $tempinvID = $tempmaster['invoiceAutoID'];

            $this->db->select('isDayClosed');
            $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $invoice_temp_master_chk = $this->db->get()->row_array();

            if ($invoice_temp_master_chk['isDayClosed'] == 1) {
                if (!in_array($tempmaster['invoiceCode'], $invalidInvoicearr)) {
                    array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                    array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Day already closed" ));
                }
            }

            $tempDetailQty = $this->db->query("SELECT
	SUM(requestedQty/conversionRateUOM) as qty,itemAutoID,wareHouseAutoID,itemSystemCode
FROM
	srp_erp_customerinvoicedetails_temp
WHERE
	invoiceAutoID = $tempinvID
AND type='Item'
GROUP BY wareHouseAutoID,itemAutoID")->result_array();

            foreach ($tempDetailQty as $val) {
                $fcQty = $val['qty'];

                $wareHouseAutoID = $val['wareHouseAutoID'];
                $itemAutoID = $val['itemAutoID'];
                $warehouseItems = $this->db->query("SELECT
	currentStock
FROM
	srp_erp_warehouseitems
WHERE
	wareHouseAutoID = $wareHouseAutoID
AND itemAutoID=$itemAutoID")->row_array();
                if ($warehouseItems['currentStock'] < $fcQty) {
                    if (!in_array($tempmaster['invoiceCode'], $invalidInvoicearr)) {
                        array_push($invalidInvoicearr, $tempmaster['invoiceCode']);
                        array_push($invalidarr, array("itemcode" => $tempmaster['invoiceCode'], "itemDescription" => "Stock not sufficient for " .$val['itemSystemCode']. " (".current_companyCode().")" ));
                    }
                }

            }

            if (in_array($tempmaster['invoiceCode'], $invalidInvoicearr)) {
                continue;
            } else {
               /* $this->db->select('finCompanyPercentage,pvtCompanyPercentage');
                $this->db->where('customerAutoID', $tempmaster['customerID']);
                $this->db->from('srp_erp_customermaster');
                $customer = $this->db->get()->row_array();*/

                /*$data_master_temp['transactionDividedAmount'] = $tempmaster['transactionAmount'] * $customer['finCompanyPercentage'] / 100;
                $data_master_temp['companyLocalDividedAmount'] = $tempmaster['companyLocalAmount'] * $customer['finCompanyPercentage'] / 100;
                $data_master_temp['companyReportingDividedAmount'] = $tempmaster['companyReportingAmount'] * $customer['finCompanyPercentage'] / 100;*/
                $data_master_temp['isDayClosed'] = 1;

                $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                $result_master_temp = $this->db->update('srp_erp_customerinvoicemaster_temp', $data_master_temp);
                if ($result_master_temp) {

                    $data_fc_master['documentID'] = 'CINV';
                    $data_fc_master['tempInvoiceID'] = trim($tempmaster['invoiceAutoID'] ?? '');
                    $data_fc_master['companyFinanceYearID'] = trim($tempmaster['companyFinanceYearID'] ?? '');
                    $data_fc_master['companyFinanceYear'] = trim($tempmaster['companyFinanceYear'] ?? '');
                    $data_fc_master['contactPersonName'] = trim($tempmaster['contactPersonName'] ?? '');
                    $data_fc_master['contactPersonNumber'] = trim($tempmaster['contactPersonNumber'] ?? '');
                    $data_fc_master['FYBegin'] = trim($tempmaster['FYBegin'] ?? '');
                    $data_fc_master['FYEnd'] = trim($tempmaster['FYEnd'] ?? '');
                    $data_fc_master['companyFinancePeriodID'] = trim($tempmaster['companyFinancePeriodID'] ?? '');
                    $data_fc_master['invoiceDate'] = trim($tempmaster['invoiceDate'] ?? '');
                    $data_fc_master['customerInvoiceDate'] = trim($tempmaster['customerInvoiceDate'] ?? '');
                    $data_fc_master['invoiceDueDate'] = trim($tempmaster['invoiceDueDate'] ?? '');
                    $data_fc_master['invoiceNarration'] = trim_desc($tempmaster['invoiceNarration']);
                    $data_fc_master['invoiceNote'] = trim($tempmaster['invoiceNote'] ?? '');
                    $data_fc_master['segmentID'] = trim($tempmaster['segmentID'] ?? '');
                    $data_fc_master['segmentCode'] = trim($tempmaster['segmentCode'] ?? '');
                    $data_fc_master['salesPersonID'] = trim($tempmaster['salesPersonID'] ?? '');
                    if ($data_fc_master['salesPersonID']) {
                        $data_fc_master['SalesPersonCode'] = trim($tempmaster['SalesPersonCode'] ?? '');
                    }
                    $data_fc_master['invoiceType'] = trim($tempmaster['invoiceType'] ?? '');
                    $data_fc_master['showTaxSummaryYN'] = trim($tempmaster['showTaxSummaryYN'] ?? '');
                    $data_fc_master['referenceNo'] = trim($tempmaster['referenceNo'] ?? '');
                    $data_fc_master['isPrintDN'] = trim($tempmaster['isPrintDN'] ?? '');
                    $data_fc_master['customerID'] = $tempmaster['customerID'];
                    $data_fc_master['customerSystemCode'] = $tempmaster['customerSystemCode'];
                    $data_fc_master['customerName'] = $tempmaster['customerName'];
                    $data_fc_master['customerAddress'] = $tempmaster['customerAddress'];
                    $data_fc_master['customerTelephone'] = $tempmaster['customerTelephone'];
                    $data_fc_master['customerFax'] = $tempmaster['customerFax'];
                    $data_fc_master['customerEmail'] = $tempmaster['customerEmail'];
                    $data_fc_master['customerReceivableAutoID'] = $tempmaster['customerReceivableAutoID'];
                    $data_fc_master['customerReceivableSystemGLCode'] = $tempmaster['customerReceivableSystemGLCode'];
                    $data_fc_master['customerReceivableGLAccount'] = $tempmaster['customerReceivableGLAccount'];
                    $data_fc_master['customerReceivableDescription'] = $tempmaster['customerReceivableDescription'];
                    $data_fc_master['customerReceivableType'] = $tempmaster['customerReceivableType'];
                    $data_fc_master['customerCurrency'] = $tempmaster['customerCurrency'];
                    $data_fc_master['customerCurrencyID'] = $tempmaster['customerCurrencyID'];
                    $data_fc_master['customerCurrencyDecimalPlaces'] = $tempmaster['customerCurrencyDecimalPlaces'];
                    $data_fc_master['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_fc_master['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_fc_master['modifiedUserName'] = $this->common_data['current_user'];
                    $data_fc_master['modifiedDateTime'] = $this->common_data['current_date'];
                    $data_fc_master['transactionCurrencyID'] = trim($tempmaster['transactionCurrencyID'] ?? '');
                    $data_fc_master['transactionCurrency'] = trim($tempmaster['transactionCurrency'] ?? '');
                    $data_fc_master['transactionExchangeRate'] = $tempmaster['transactionExchangeRate'];
                    $data_fc_master['transactionCurrencyDecimalPlaces'] = $tempmaster['transactionCurrencyDecimalPlaces'];
                    $data_fc_master['companyLocalCurrencyID'] = $tempmaster['companyLocalCurrencyID'];
                    $data_fc_master['companyLocalCurrency'] = $tempmaster['companyLocalCurrency'];
                    $default_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['companyLocalCurrencyID']);
                    $data_fc_master['companyLocalExchangeRate'] = $tempmaster['companyLocalExchangeRate'];
                    $data_fc_master['companyLocalCurrencyDecimalPlaces'] = $tempmaster['companyLocalCurrencyDecimalPlaces'];
                    $data_fc_master['companyReportingCurrency'] = $tempmaster['companyReportingCurrency'];
                    $data_fc_master['companyReportingCurrencyID'] = $tempmaster['companyReportingCurrencyID'];
                    $reporting_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['companyReportingCurrencyID']);
                    $data_fc_master['companyReportingExchangeRate'] = $tempmaster['companyReportingExchangeRate'];
                    $data_fc_master['companyReportingCurrencyDecimalPlaces'] = $tempmaster['companyReportingCurrencyDecimalPlaces'];
                    $customer_currency = currency_conversionID($data_fc_master['transactionCurrencyID'], $data_fc_master['customerCurrencyID']);
                    $data_fc_master['customerCurrencyExchangeRate'] = $tempmaster['customerCurrencyExchangeRate'];
                    $data_fc_master['customerCurrencyDecimalPlaces'] = $tempmaster['customerCurrencyDecimalPlaces'];
                    $data_fc_master['transactionAmount'] = $tempmaster['transactionAmount'];
                    $data_fc_master['companyLocalAmount'] = $tempmaster['companyLocalAmount'];
                    $data_fc_master['companyReportingAmount'] = $tempmaster['companyReportingAmount'];
                    $data_fc_master['customerCurrencyAmount'] = ($tempmaster['transactionAmount']/$tempmaster['customerCurrencyExchangeRate']);
                    $data_fc_master['confirmedYN'] = 1;
                    $data_fc_master['confirmedByEmpID'] = current_userID();
                    $data_fc_master['confirmedByName'] = current_user();
                    $data_fc_master['confirmedDate'] = $this->common_data['current_date'];
                    $data_fc_master['approvedYN'] = 1;
                    $data_fc_master['currentLevelNo'] = 1;
                    $data_fc_master['approvedbyEmpID'] = current_userID();
                    $data_fc_master['approvedbyEmpName'] = current_user();
                    $data_fc_master['approvedDate'] = $this->common_data['current_date'];
                    $this->load->library('sequence');
                    $data_fc_master['companyCode'] = $tempmaster['companyCode'];
                    $data_fc_master['companyID'] = $tempmaster['companyID'];
                    $data_fc_master['createdUserGroup'] = $this->common_data['user_group'];
                    $data_fc_master['createdPCID'] = $this->common_data['current_pc'];
                    $data_fc_master['createdUserID'] = $this->common_data['current_userID'];
                    $data_fc_master['createdUserName'] = $this->common_data['current_user'];
                    $data_fc_master['createdDateTime'] = $this->common_data['current_date'];
                   /* $data_fc_master['invoiceCode'] = $this->sequence->sequence_generator($data_fc_master['documentID']);
                    $data_fc_master['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');*/
                    $data_fc_master['invoiceCode'] = $tempmaster['invoiceCode'];
                    $data_fc_master['deliveryNoteSystemCode'] = $tempmaster['deliveryNoteSystemCode'];

                    $this->db->insert('srp_erp_customerinvoicemaster', $data_fc_master);
                    $fc_master_last_id = $this->db->insert_id();

                    $data_app_fc['companyID'] = $tempmaster['companyID'];
                    $data_app_fc['companyCode'] = $tempmaster['companyCode'];
                    $data_app_fc['departmentID'] = 'CINV';
                    $data_app_fc['documentID'] = 'CINV';
                    $data_app_fc['documentSystemCode'] = $fc_master_last_id;
                    $data_app_fc['documentCode'] = $data_fc_master['invoiceCode'];
                    $data_app_fc['table_name'] = 'srp_erp_customerinvoicemaster';
                    $data_app_fc['table_unique_field_name'] = 'invoiceAutoID';
                    $data_app_fc['documentDate'] = $tempmaster['invoiceDate'];
                    $data_app_fc['approvalLevelID'] = 1;
                    $data_app_fc['roleID'] = null;
                    $data_app_fc['approvalGroupID'] = $this->common_data['user_group'];
                    $data_app_fc['roleLevelOrder'] = null;
                    $data_app_fc['docConfirmedDate'] = $this->common_data['current_date'];
                    $data_app_fc['docConfirmedByEmpID'] = $this->common_data['current_userID'];
                    $data_app_fc['approvedEmpID'] = $this->common_data['current_userID'];
                    $data_app_fc['isReverseApplicableYN'] = 0;
                    $data_app_fc['approvedYN'] = 1;
                    $data_app_fc['approvedDate'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_documentapproved', $data_app_fc);


                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicedetails_temp');
                    $temp_detail = $this->db->get()->result_array();
                    foreach ($temp_detail as $Dtemp) {
                        /*$data_detail_temp['requestedDividedQty'] = $Dtemp['requestedQty'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['noOfDividedItems'] = $Dtemp['noOfItems'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['grossDividedQty'] = $Dtemp['grossQty'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['dividedDeduction'] = $Dtemp['deduction'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['tranasactionDividedAmount'] = $Dtemp['transactionAmount'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['dividedTotalAfterTax'] = $Dtemp['totalAfterTax'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['companyLocalDividedAmount'] = $Dtemp['companyLocalAmount'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['companyReportingDividedAmount'] = $Dtemp['companyReportingAmount'] * $customer['finCompanyPercentage'] / 100;
                        $data_detail_temp['customerDividedAmount'] = $Dtemp['customerAmount'] * $customer['finCompanyPercentage'] / 100;

                        $this->db->where('invoiceDetailsAutoID', $Dtemp['invoiceDetailsAutoID']);
                        $result_detail_temp = $this->db->update('srp_erp_customerinvoicedetails_temp', $data_detail_temp);
                        if ($result_detail_temp) {*/
                            $data_fc_detail['invoiceAutoID'] = $fc_master_last_id;
                            $data_fc_detail['tempinvoiceDetailID'] = $Dtemp['invoiceDetailsAutoID'];
                            $data_fc_detail['type'] = $Dtemp['type'];
                            $data_fc_detail['contractAutoID'] = $Dtemp['contractAutoID'];
                            $data_fc_detail['contractDetailsAutoID'] = $Dtemp['contractDetailsAutoID'];
                            $data_fc_detail['contractCode'] = $Dtemp['contractCode'];
                            $data_fc_detail['projectID'] = $Dtemp['projectID'];
                            $data_fc_detail['projectExchangeRate'] = $Dtemp['projectExchangeRate'];
                            $data_fc_detail['itemAutoID'] = $Dtemp['itemAutoID'];
                            $data_fc_detail['itemSystemCode'] = $Dtemp['itemSystemCode'];
                            $data_fc_detail['itemDescription'] = $Dtemp['itemDescription'];
                            $data_fc_detail['itemCategory'] = $Dtemp['itemCategory'];
                            $data_fc_detail['expenseGLAutoID'] = $Dtemp['expenseGLAutoID'];
                            $data_fc_detail['expenseSystemGLCode'] = $Dtemp['expenseSystemGLCode'];
                            $data_fc_detail['expenseGLCode'] = $Dtemp['expenseGLCode'];
                            $data_fc_detail['expenseGLDescription'] = $Dtemp['expenseGLDescription'];
                            $data_fc_detail['expenseGLType'] = $Dtemp['expenseGLType'];
                            $data_fc_detail['revenueGLAutoID'] = $Dtemp['revenueGLAutoID'];
                            $data_fc_detail['revenueGLCode'] = $Dtemp['revenueGLCode'];
                            $data_fc_detail['revenueSystemGLCode'] = $Dtemp['revenueSystemGLCode'];
                            $data_fc_detail['revenueGLDescription'] = $Dtemp['revenueGLDescription'];
                            $data_fc_detail['revenueGLType'] = $Dtemp['revenueGLType'];
                            $data_fc_detail['assetGLAutoID'] = $Dtemp['assetGLAutoID'];
                            $data_fc_detail['assetGLCode'] = $Dtemp['assetGLCode'];
                            $data_fc_detail['assetSystemGLCode'] = $Dtemp['assetSystemGLCode'];
                            $data_fc_detail['assetGLDescription'] = $Dtemp['assetGLDescription'];
                            $data_fc_detail['taxMasterAutoID'] = $Dtemp['taxMasterAutoID'];
                            $data_fc_detail['taxPercentage'] = $Dtemp['taxPercentage'];
                            $data_fc_detail['assetGLType'] = $Dtemp['assetGLType'];
                            $data_fc_detail['wareHouseAutoID'] = $Dtemp['wareHouseAutoID'];
                            $data_fc_detail['wareHouseCode'] = $Dtemp['wareHouseCode'];
                            $data_fc_detail['wareHouseLocation'] = $Dtemp['wareHouseLocation'];
                            $data_fc_detail['wareHouseDescription'] = $Dtemp['wareHouseDescription'];
                            $data_fc_detail['defaultUOMID'] = $Dtemp['defaultUOMID'];
                            $data_fc_detail['defaultUOM'] = $Dtemp['defaultUOM'];
                            $data_fc_detail['unitOfMeasureID'] = $Dtemp['unitOfMeasureID'];
                            $data_fc_detail['unitOfMeasure'] = $Dtemp['unitOfMeasure'];
                            $data_fc_detail['conversionRateUOM'] = $Dtemp['conversionRateUOM'];
                            $data_fc_detail['contractQty'] = $Dtemp['contractQty'];
                            $data_fc_detail['contractAmount'] = $Dtemp['contractAmount'];

                            $data_fc_detail['requestedQty'] = $Dtemp['requestedQty'];
                            $data_fc_detail['noOfItems'] = $Dtemp['noOfItems'];
                            $data_fc_detail['grossQty'] = $Dtemp['grossQty'];
                            $data_fc_detail['noOfUnits'] = $Dtemp['noOfUnits'];
                            $data_fc_detail['deduction'] = $Dtemp['deduction'];
                            $data_fc_detail['comment'] = $Dtemp['comment'];
                            $data_fc_detail['remarks'] = $Dtemp['remarks'];
                            $data_fc_detail['description'] = $Dtemp['description'];
                            $data_fc_detail['companyLocalWacAmount'] = $Dtemp['companyLocalWacAmount'];
                            $data_fc_detail['unittransactionAmount'] = $Dtemp['unittransactionAmount'];
                            $data_fc_detail['transactionAmount'] = $Dtemp['transactionAmount'];
                            $data_fc_detail['companyLocalAmount'] = $Dtemp['companyLocalAmount'];
                            $data_fc_detail['companyReportingAmount'] = $Dtemp['companyReportingAmount'];
                            $data_fc_detail['customerAmount'] = $Dtemp['customerAmount'];
                            $data_fc_detail['segmentID'] = $Dtemp['segmentID'];
                            $data_fc_detail['segmentCode'] = $Dtemp['segmentCode'];
                            $data_fc_detail['companyID'] = $Dtemp['companyID'];
                            $data_fc_detail['companyCode'] = $Dtemp['companyCode'];
                            $data_fc_detail['discountPercentage'] = $Dtemp['discountPercentage'];
                            $data_fc_detail['discountAmount'] = $Dtemp['discountAmount'];
                            $data_fc_detail['taxDescription'] = $Dtemp['taxDescription'];
                            $data_fc_detail['taxAmount'] = $Dtemp['taxAmount'];
                            $data_fc_detail['totalAfterTax'] = $Dtemp['totalAfterTax'];
                            $data_fc_detail['taxShortCode'] = $Dtemp['taxShortCode'];
                            $data_fc_detail['taxSupplierAutoID'] = $Dtemp['taxSupplierAutoID'];
                            $data_fc_detail['taxSupplierSystemCode'] = $Dtemp['taxSupplierSystemCode'];
                            $data_fc_detail['taxSupplierName'] = $Dtemp['taxSupplierName'];
                            $data_fc_detail['taxSupplierliabilityAutoID'] = $Dtemp['taxSupplierliabilityAutoID'];
                            $data_fc_detail['taxSupplierliabilitySystemGLCode'] = $Dtemp['taxSupplierliabilitySystemGLCode'];
                            $data_fc_detail['taxSupplierliabilityGLAccount'] = $Dtemp['taxSupplierliabilityGLAccount'];
                            $data_fc_detail['taxSupplierliabilityDescription'] = $Dtemp['taxSupplierliabilityDescription'];
                            $data_fc_detail['taxSupplierliabilityType'] = $Dtemp['taxSupplierliabilityType'];
                            $data_fc_detail['taxSupplierCurrencyID'] = $Dtemp['taxSupplierCurrencyID'];
                            $data_fc_detail['taxSupplierCurrency'] = $Dtemp['taxSupplierCurrency'];
                            $data_fc_detail['taxSupplierCurrencyExchangeRate'] = $Dtemp['taxSupplierCurrencyExchangeRate'];
                            $data_fc_detail['taxSupplierCurrencyAmount'] = $Dtemp['taxSupplierCurrencyAmount'];
                            $data_fc_detail['taxSupplierCurrencyDecimalPlaces'] = $Dtemp['taxSupplierCurrencyDecimalPlaces'];
                            $data_fc_detail['createdUserGroup'] = $this->common_data['user_group'];
                            $data_fc_detail['createdPCID'] = $this->common_data['current_pc'];
                            $data_fc_detail['createdUserID'] = $this->common_data['current_userID'];
                            $data_fc_detail['createdUserName'] = $this->common_data['current_user'];
                            $data_fc_detail['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoicedetails', $data_fc_detail);
                            $fc_detail_last_id= $this->db->insert_id();

                        $this->db->select('*');
                        $this->db->where('documentMasterAutoID', $tempmaster['invoiceAutoID']);
                        $this->db->where('documentDetailAutoID', $Dtemp['invoiceDetailsAutoID']);
                        $this->db->where('documentID', 'HCINV');
                        $this->db->from('srp_erp_taxledger');
                        $tax_ledger = $this->db->get()->result_array();
                        if(!empty($tax_ledger)){
                            foreach($tax_ledger as $txled){
                                $data_fc_tax_ledgr['documentID'] = 'CINV';
                                $data_fc_tax_ledgr['documentMasterAutoID'] = $fc_master_last_id;
                                $data_fc_tax_ledgr['documentDetailAutoID'] = $fc_detail_last_id;
                                $data_fc_tax_ledgr['taxDetailAutoID'] = $txled['taxDetailAutoID'];
                                $data_fc_tax_ledgr['taxFormulaMasterID'] = $txled['taxFormulaMasterID'];
                                $data_fc_tax_ledgr['taxFormulaDetailID'] = $txled['taxFormulaDetailID'];
                                $data_fc_tax_ledgr['taxMasterID'] = $txled['taxMasterID'];
                                $data_fc_tax_ledgr['amount'] = $txled['amount'];
                                $data_fc_tax_ledgr['formula'] = $txled['formula'];
                                $data_fc_tax_ledgr['taxAuthorityAutoID'] = $txled['taxAuthorityAutoID'];
                                $data_fc_tax_ledgr['taxGlAutoID'] = $txled['taxGlAutoID'];
                                $data_fc_tax_ledgr['companyID'] = current_companyID();
                                $data_fc_tax_ledgr['companyCode'] = $this->common_data['company_data']['company_code'];
                                $data_fc_tax_ledgr['createdUserGroup'] = $this->common_data['user_group'];
                                $data_fc_tax_ledgr['createdPCID'] = $this->common_data['current_pc'];
                                $data_fc_tax_ledgr['createdUserID'] = $this->common_data['current_userID'];
                                $data_fc_tax_ledgr['createdUserName'] = $this->common_data['current_user'];
                                $data_fc_tax_ledgr['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_taxledger', $data_fc_tax_ledgr);
                            }
                        }
                       /* }*/
                    }

                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicediscountdetails_temp');
                    $temp_discount = $this->db->get()->result_array();
                    if(!empty($temp_discount)){
                        foreach($temp_discount as $disc){
                            $data_fc_discount['invoiceAutoID'] = $fc_master_last_id;
                            $data_fc_discount['referenceNo'] = $disc['referenceNo'];
                            $data_fc_discount['discountMasterAutoID'] = $disc['discountMasterAutoID'];
                            $data_fc_discount['isChargeToExpense'] = $disc['isChargeToExpense'];
                            $data_fc_discount['discountDescription'] = $disc['discountDescription'];
                            $data_fc_discount['discountPercentage'] = $disc['discountPercentage'];
                            $data_fc_discount['transactionCurrencyID'] = $disc['transactionCurrencyID'];
                            $data_fc_discount['transactionCurrency'] = $disc['transactionCurrency'];
                            $data_fc_discount['transactionExchangeRate'] = $disc['transactionExchangeRate'];
                            $data_fc_discount['transactionCurrencyDecimalPlaces'] = $disc['transactionCurrencyDecimalPlaces'];
                            $data_fc_discount['transactionAmount'] = $disc['transactionAmount'];
                            $data_fc_discount['customerCurrencyID'] = $disc['customerCurrencyID'];
                            $data_fc_discount['customerCurrency'] = $disc['customerCurrency'];
                            $data_fc_discount['customerCurrencyExchangeRate'] = $disc['customerCurrencyExchangeRate'];
                            $data_fc_discount['customerCurrencyAmount'] = $disc['customerCurrencyAmount'];
                            $data_fc_discount['customerCurrencyDecimalPlaces'] = $disc['customerCurrencyDecimalPlaces'];
                            $data_fc_discount['companyLocalCurrencyID'] = $disc['companyLocalCurrencyID'];
                            $data_fc_discount['companyLocalCurrency'] = $disc['companyLocalCurrency'];
                            $data_fc_discount['companyLocalExchangeRate'] = $disc['companyLocalExchangeRate'];
                            $data_fc_discount['companyLocalAmount'] = $disc['companyLocalAmount'];
                            $data_fc_discount['companyReportingCurrencyID'] = $disc['companyReportingCurrencyID'];
                            $data_fc_discount['companyReportingCurrency'] = $disc['companyReportingCurrency'];
                            $data_fc_discount['companyReportingExchangeRate'] = $disc['companyReportingExchangeRate'];
                            $data_fc_discount['companyReportingAmount'] = $disc['companyReportingAmount'];
                            $data_fc_discount['GLAutoID'] = $disc['GLAutoID'];
                            $data_fc_discount['systemGLCode'] = $disc['systemGLCode'];
                            $data_fc_discount['GLCode'] = $disc['GLCode'];
                            $data_fc_discount['GLDescription'] = $disc['GLDescription'];
                            $data_fc_discount['GLType'] = $disc['GLType'];
                            $data_fc_discount['segmentID'] = $disc['segmentID'];
                            $data_fc_discount['segmentCode'] = $disc['segmentCode'];
                            $data_fc_discount['companyID'] = current_companyID();
                            $data_fc_discount['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data_fc_discount['createdUserGroup'] = $this->common_data['user_group'];
                            $data_fc_discount['createdPCID'] = $this->common_data['current_pc'];
                            $data_fc_discount['createdUserID'] = $this->common_data['current_userID'];
                            $data_fc_discount['createdUserName'] = $this->common_data['current_user'];
                            $data_fc_discount['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoicediscountdetails', $data_fc_discount);
                        }
                    }


                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoiceextrachargedetails_temp');
                    $temp_extra = $this->db->get()->result_array();
                    if(!empty($temp_extra)){
                        foreach($temp_extra as $extra){
                            $data_fc_extra['invoiceAutoID'] = $fc_master_last_id;
                            $data_fc_extra['referenceNo'] = $extra['referenceNo'];
                            $data_fc_extra['extraChargeMasterAutoID'] = $extra['extraChargeMasterAutoID'];
                            $data_fc_extra['isTaxApplicable'] = $extra['isTaxApplicable'];
                            $data_fc_extra['extraChargeDescription'] = $extra['extraChargeDescription'];
                            $data_fc_extra['transactionCurrencyID'] = $extra['transactionCurrencyID'];
                            $data_fc_extra['transactionCurrency'] = $extra['transactionCurrency'];
                            $data_fc_extra['transactionExchangeRate'] = $extra['transactionExchangeRate'];
                            $data_fc_extra['transactionCurrencyDecimalPlaces'] = $extra['transactionCurrencyDecimalPlaces'];
                            $data_fc_extra['transactionAmount'] = $extra['transactionAmount'];
                            $data_fc_extra['customerCurrencyID'] = $extra['customerCurrencyID'];
                            $data_fc_extra['customerCurrency'] = $extra['customerCurrency'];
                            $data_fc_extra['customerCurrencyExchangeRate'] = $extra['customerCurrencyExchangeRate'];
                            $data_fc_extra['customerCurrencyAmount'] = $extra['customerCurrencyAmount'];
                            $data_fc_extra['customerCurrencyDecimalPlaces'] = $extra['customerCurrencyDecimalPlaces'];
                            $data_fc_extra['companyLocalCurrencyID'] = $extra['companyLocalCurrencyID'];
                            $data_fc_extra['companyLocalCurrency'] = $extra['companyLocalCurrency'];
                            $data_fc_extra['companyLocalExchangeRate'] = $extra['companyLocalExchangeRate'];
                            $data_fc_extra['companyLocalAmount'] = $extra['companyLocalAmount'];
                            $data_fc_extra['companyReportingCurrencyID'] = $extra['companyReportingCurrencyID'];
                            $data_fc_extra['companyReportingCurrency'] = $extra['companyReportingCurrency'];
                            $data_fc_extra['companyReportingExchangeRate'] = $extra['companyReportingExchangeRate'];
                            $data_fc_extra['companyReportingAmount'] = $extra['companyReportingAmount'];
                            $data_fc_extra['GLAutoID'] = $extra['GLAutoID'];
                            $data_fc_extra['systemGLCode'] = $extra['systemGLCode'];
                            $data_fc_extra['GLCode'] = $extra['GLCode'];
                            $data_fc_extra['GLDescription'] = $extra['GLDescription'];
                            $data_fc_extra['GLType'] = $extra['GLType'];
                            $data_fc_extra['segmentID'] = $extra['segmentID'];
                            $data_fc_extra['segmentCode'] = $extra['segmentCode'];
                            $data_fc_extra['companyID'] = current_companyID();
                            $data_fc_extra['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data_fc_extra['createdUserGroup'] = $this->common_data['user_group'];
                            $data_fc_extra['createdPCID'] = $this->common_data['current_pc'];
                            $data_fc_extra['createdUserID'] = $this->common_data['current_userID'];
                            $data_fc_extra['createdUserName'] = $this->common_data['current_user'];
                            $data_fc_extra['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_customerinvoiceextrachargedetails', $data_fc_extra);
                        }
                    }



                    /*$this->db->select('*');
                    $this->db->where('invoiceAutoID', $tempmaster['invoiceAutoID']);
                    $this->db->from('srp_erp_customerinvoicetaxdetails_temp');
                    $temp_tax = $this->db->get()->result_array();


                    if (!empty($temp_tax)) {
                        $data_fc_tax['invoiceAutoID'] = $fc_master_last_id;
                        $data_fc_tax['referenceNo'] = $temp_tax['referenceNo'];
                        $data_fc_tax['taxMasterAutoID'] = $temp_tax['taxMasterAutoID'];
                        $data_fc_tax['tempInvoiceTaxDetailID'] = $temp_tax['taxDetailAutoID'];
                        $data_fc_tax['taxDescription'] = $temp_tax['taxDescription'];
                        $data_fc_tax['taxShortCode'] = $temp_tax['taxShortCode'];
                        $data_fc_tax['taxPercentage'] = $temp_tax['taxPercentage'];
                        $data_fc_tax['supplierAutoID'] = $temp_tax['supplierAutoID'];
                        $data_fc_tax['supplierSystemCode'] = $temp_tax['supplierSystemCode'];
                        $data_fc_tax['supplierName'] = $temp_tax['supplierName'];
                        $data_fc_tax['transactionCurrencyID'] = $temp_tax['transactionCurrencyID'];
                        $data_fc_tax['transactionCurrency'] = $temp_tax['transactionCurrency'];
                        $data_fc_tax['transactionExchangeRate'] = $temp_tax['transactionExchangeRate'];
                        $data_fc_tax['transactionCurrencyDecimalPlaces'] = $temp_tax['transactionCurrencyDecimalPlaces'];
                        $data_fc_tax['transactionAmount'] = $temp_tax['transactionAmount'];
                        $data_fc_tax['supplierCurrencyID'] = $temp_tax['supplierCurrencyID'];
                        $data_fc_tax['supplierCurrency'] = $temp_tax['supplierCurrency'];
                        $data_fc_tax['supplierCurrencyExchangeRate'] = $temp_tax['supplierCurrencyExchangeRate'];
                        $data_fc_tax['supplierCurrencyAmount'] = $temp_tax['supplierCurrencyAmount'];
                        $data_fc_tax['supplierCurrencyDecimalPlaces'] = $temp_tax['supplierCurrencyDecimalPlaces'];
                        $data_fc_tax['companyLocalCurrencyID'] = $temp_tax['companyLocalCurrencyID'];
                        $data_fc_tax['companyLocalCurrency'] = $temp_tax['companyLocalCurrency'];
                        $data_fc_tax['companyLocalExchangeRate'] = $temp_tax['companyLocalExchangeRate'];
                        $data_fc_tax['companyLocalAmount'] = $temp_tax['companyLocalAmount'];
                        $data_fc_tax['companyReportingCurrencyID'] = $temp_tax['companyReportingCurrencyID'];
                        $data_fc_tax['companyReportingCurrency'] = $temp_tax['companyReportingCurrency'];
                        $data_fc_tax['companyReportingExchangeRate'] = $temp_tax['companyReportingExchangeRate'];
                        $data_fc_tax['companyReportingAmount'] = $temp_tax['companyReportingAmount'];
                        $data_fc_tax['GLAutoID'] = $temp_tax['GLAutoID'];
                        $data_fc_tax['systemGLCode'] = $temp_tax['systemGLCode'];
                        $data_fc_tax['GLCode'] = $temp_tax['GLCode'];
                        $data_fc_tax['GLDescription'] = $temp_tax['GLDescription'];
                        $data_fc_tax['GLType'] = $temp_tax['GLType'];
                        $data_fc_tax['segmentID'] = $temp_tax['segmentID'];
                        $data_fc_tax['segmentCode'] = $temp_tax['segmentCode'];
                        $data_fc_tax['companyID'] = $temp_tax['companyID'];
                        $data_fc_tax['companyCode'] = $temp_tax['companyCode'];
                        $data_fc_tax['createdUserGroup'] = $this->common_data['user_group'];
                        $data_fc_tax['createdPCID'] = $this->common_data['current_pc'];
                        $data_fc_tax['createdUserID'] = $this->common_data['current_userID'];
                        $data_fc_tax['createdUserName'] = $this->common_data['current_user'];
                        $data_fc_tax['createdDateTime'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_customerinvoicedetails', $data_fc_tax);

                    }*/

                }
            }
            }
            if (!in_array($tempmaster['invoiceCode'], $invalidInvoicearr)) {
                if ($divedPercentage ['pvtCompanyPercentage'] != 100) {
                    $this->fc_double_entry($fc_master_last_id);
                }
                if(!empty($pvt_master_last_id) || $pvt_master_last_id!=0){
                    $this->pvt_double_entry($pvt_master_last_id);
                }
            }

        }
        return array('s','successfully Updated',$invalidarr);

    }

    function fc_double_entry($master_last_id){
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $master_last_id);
            $this->db->from('srp_erp_customerinvoicemaster');
            $master = $this->db->get()->row_array();
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $master_last_id);
            $this->db->from('srp_erp_customerinvoicedetails');
            $invoice_detail = $this->db->get()->result_array();
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
                        $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                        $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
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

            /*if (!empty($item_arr)) {
                $item_arr = array_values($item_arr);
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }*/

            if (!empty($itemledger_arr)) {
                //echo '<pre>';print_r($itemledger_arr); echo '</pre>';
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            //$double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($master_last_id, 'CINV');
            $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_temp_data($master_last_id, 'CINV');
            //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
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
            }

            $this->db->select_sum('transactionAmount');
            $this->db->where('invoiceAutoID', $master_last_id);
            $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

            $data['approvedYN'] = 1;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->where('invoiceAutoID', $master_last_id);
            $this->db->update('srp_erp_customerinvoicemaster', $data);
            //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');
    }


    function pvt_double_entry($master_last_id){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $master_last_id);
        $this->db->from('srp_erp_customerinvoicemaster');
        $master = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $master_last_id);
        $this->db->from('srp_erp_customerinvoicedetails');
        $invoice_detail = $this->db->get()->result_array();
        for ($a = 0; $a < count($invoice_detail); $a++) {
            if ($invoice_detail[$a]['type'] == 'Item') {
                $item = fetch_item_data_pvt($invoice_detail[$a]['itemAutoID'],$master['companyID']);
                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                    $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                    $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                    $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                    $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
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

        /*if (!empty($item_arr)) {
            $item_arr = array_values($item_arr);
            $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
        }*/

        if (!empty($itemledger_arr)) {
            //echo '<pre>';print_r($itemledger_arr); echo '</pre>';
            $itemledger_arr = array_values($itemledger_arr);
            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
        }

        $this->load->model('Double_entry_model');
        //$double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($master_last_id, 'CINV');
        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_temp_data($master_last_id, 'CINV');
        //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
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
        }

        $this->db->select_sum('transactionAmount');
        $this->db->where('invoiceAutoID', $master_last_id);
        $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('invoiceAutoID', $master_last_id);
        $this->db->update('srp_erp_customerinvoicemaster', $data);
        //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');
    }

    function save_inv_tax_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails_temp')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Tax Detail added already ! ');
            return array('status' => true);
        }
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['taxMasterAutoID']                 = $master['taxMasterAutoID'];
        $data['taxDescription']                  = $master['taxDescription'];
        $data['taxShortCode']                    = $master['taxShortCode'];
        $data['supplierAutoID']                  = $master['supplierAutoID'];
        $data['supplierSystemCode']              = $master['supplierSystemCode'];
        $data['supplierName']                    = $master['supplierName'];
        $data['supplierCurrencyID']              = $master['supplierCurrencyID'];
        $data['supplierCurrency']                = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces']   = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID']                        = $master['supplierGLAutoID'];
        $data['systemGLCode']                    = $master['supplierGLSystemGLCode'];
        $data['GLCode']                          = $master['supplierGLAccount'];
        $data['GLDescription']                   = $master['supplierGLDescription'];
        $data['GLType']                          = $master['supplierGLType'];
        $data['taxPercentage']                   = trim($this->input->post('percentage') ?? '');
        $data['transactionAmount']               = trim($this->input->post('amount') ?? '');
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency      = currency_conversion($data['transactionCurrency'],$data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate']    = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces']   = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID') ?? '')) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicetaxdetails_temp', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_customerinvoicetaxdetails_temp', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription']. ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_inv_discount_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('discountMasterAutoID', $this->input->post('discountExtraChargeID'));
        $tax_detail = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Discount Detail added already ! ');
            return array('status' => true);
        }
        $this->db->select('*');
        $this->db->where('discountExtraChargeID', $this->input->post('discountExtraChargeID'));
        $master = $this->db->get('srp_erp_discountextracharges')->row_array();

        $this->db->select('segmentCode,segmentID,customerCurrencyDecimalPlaces,customerCurrencyExchangeRate,customerCurrencyID,customerCurrency,transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['discountMasterAutoID']            = $master['discountExtraChargeID'];
        $data['discountDescription']             = $master['Description'];
        $data['isChargeToExpense']               = $master['isChargeToExpense'];
        $data['discountPercentage']              = trim($this->input->post('discountPercentage') ?? '');
        $data['transactionAmount']               = trim($this->input->post('discount_amount') ?? '');
        $data['transactionDividedAmount']        = trim($this->input->post('discount_amount') ?? '');
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];
        $data['customerCurrencyID']              = $inv_master['customerCurrencyID'];
        $data['customerCurrency']                = $inv_master['customerCurrency'];
        $data['customerCurrencyExchangeRate']    = $inv_master['customerCurrencyExchangeRate'];
        $data['customerCurrencyDecimalPlaces']   = $inv_master['customerCurrencyDecimalPlaces'];
        $data['segmentID']                       = $inv_master['segmentID'];
        $data['segmentCode']                     = $inv_master['segmentCode'];
        $data['customerCurrencyAmount']          =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['customerCurrencyDividedAmount']   =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['companyLocalAmount']              =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyLocalDividedAmount']       =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyReportingAmount']          =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        $data['companyReportingDividedAmount']   =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        if(!empty($master['glCode'])){
            $data['GLAutoID']                        = $master['glCode'];
            $gl = fetch_gl_account_desc($master['glCode']);
            $data['systemGLCode']                    = $gl['systemAccountCode'];
            $data['GLCode']                          = $gl['GLSecondaryCode'];
            $data['GLDescription']                   = $gl['GLDescription'];
            $data['GLType']                          = $gl['subCategory'];
        }
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];
        $data['companyCode']        = $this->common_data['company_data']['company_code'];
        $data['companyID']          = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup']   = $this->common_data['user_group'];
        $data['createdPCID']        = $this->common_data['current_pc'];
        $data['createdUserID']      = $this->common_data['current_userID'];
        $data['createdUserName']    = $this->common_data['current_user'];
        $data['createdDateTime']    = $this->common_data['current_date'];
        $this->db->insert('srp_erp_customerinvoicediscountdetails_temp', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Discount Detail Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Discount Detail Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function delete_tax_detail(){
        $this->db->delete('srp_erp_customerinvoicetaxdetails_temp',array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function delete_discount_gen(){
        $this->db->delete('srp_erp_customerinvoicediscountdetails_temp',array('discountDetailID' => trim($this->input->post('discountDetailID') ?? '')));
        return true;
    }

    function save_inv_extra_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('extraChargeMasterAutoID', $this->input->post('discountExtraChargeIDExtra'));
        $tax_detail = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Extra Charges added already ! ');
            return array('status' => true);
        }
        $this->db->select('*');
        $this->db->where('discountExtraChargeID', $this->input->post('discountExtraChargeIDExtra'));
        $master = $this->db->get('srp_erp_discountextracharges')->row_array();

        $this->db->select('segmentCode,segmentID,customerCurrencyDecimalPlaces,customerCurrencyExchangeRate,customerCurrencyID,customerCurrency,transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['extraChargeMasterAutoID']         = $master['discountExtraChargeID'];
        $data['extraChargeDescription']          = $master['Description'];
        $data['isTaxApplicable']                 = $master['isTaxApplicable'];
        $data['transactionAmount']               = trim($this->input->post('extra_amount') ?? '');
        $data['transactionDividedAmount']        = trim($this->input->post('extra_amount') ?? '');
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];
        $data['customerCurrencyID']              = $inv_master['customerCurrencyID'];
        $data['customerCurrency']                = $inv_master['customerCurrency'];
        $data['customerCurrencyExchangeRate']    = $inv_master['customerCurrencyExchangeRate'];
        $data['customerCurrencyDecimalPlaces']   = $inv_master['customerCurrencyDecimalPlaces'];
        $data['segmentID']                       = $inv_master['segmentID'];
        $data['segmentCode']                     = $inv_master['segmentCode'];
        $data['customerCurrencyAmount']          =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['customerCurrencyDividedAmount']   =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['companyLocalAmount']              =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyLocalDividedAmount']       =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyReportingAmount']          =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        $data['companyReportingDividedAmount']   =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        $data['GLAutoID']                        = $master['glCode'];
        $gl = fetch_gl_account_desc($master['glCode']);
        $data['systemGLCode']                    = $gl['systemAccountCode'];
        $data['GLCode']                          = $gl['GLSecondaryCode'];
        $data['GLDescription']                   = $gl['GLDescription'];
        $data['GLType']                          = $gl['subCategory'];
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];
        $data['companyCode']        = $this->common_data['company_data']['company_code'];
        $data['companyID']          = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup']   = $this->common_data['user_group'];
        $data['createdPCID']        = $this->common_data['current_pc'];
        $data['createdUserID']      = $this->common_data['current_userID'];
        $data['createdUserName']    = $this->common_data['current_user'];
        $data['createdDateTime']    = $this->common_data['current_date'];
        $this->db->insert('srp_erp_customerinvoiceextrachargedetails_temp', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Extra Charge Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Extra Charge Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function delete_extra_gen(){
        $this->db->delete('srp_erp_customerinvoiceextrachargedetails_temp',array('extraChargeDetailID' => trim($this->input->post('extraChargeDetailID') ?? '')));
        return true;
    }

    function get_tax_drop(){
        $this->db->select('salesTaxFormulaID');
        $this->db->where('itemAutoID', $this->input->post('itemAutoId'));
        $salesTaxFormulaID = $this->db->get('srp_erp_itemmaster')->row_array();

        $this->db->select('taxCalculationformulaID,Description');
        $this->db->where('taxCalculationformulaID', $salesTaxFormulaID['salesTaxFormulaID']);
        $salesTaxFormulaID = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
        return $salesTaxFormulaID;
    }

    function update_isEliminated(){
        $checkedval=$this->input->post('checkedval');
        $invoiceId=$this->input->post('invoiceId');

        $data['eliminateYN'] = $checkedval;
        $this->db->where('invoiceAutoID', $invoiceId);
        $result= $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
        if($result){
            return array('s','Successfully updated');
        }
    }
    function add_print_tagline()
    {
        $invoicedetailid = trim($this->input->post('invoiceDetailsAutoID') ?? '');
        $companyid = current_companyID();
        $type = trim($this->input->post('type') ?? '');

        if($type == 1)
        {
            $data['printTagYN'] = 0;
        }else
        {
            $data['printTagYN'] = 1;
        }
        $this->db->where('companyID', $companyid);
        $this->db->where('invoiceDetailsAutoID', $invoicedetailid);
        $result= $this->db->update('srp_erp_customerinvoicedetails_temp', $data);
        if($result){
            return array('s','Successfully print tag updated');
        }
    }
    function fetch_invoice_template_data_print_tagline($invoiceAutoID)
    {
        $companyid = current_companyID();
      $data['detail'] = $this->db->query("SELECT
	detail.itemDescription,
	detail.noOfUnits,
	detail.grossQty,
	detail.deduction,
	detail.requestedQty,
	mastertbl.customerName,
	mastertbl.invoiceDate
FROM
	srp_erp_customerinvoicedetails_temp detail
	LEFT JOIN srp_erp_customerinvoicemaster mastertbl on detail.invoiceAutoID = mastertbl.invoiceAutoID
	where
	detail.companyID = $companyid
		AND printTagYN = 1
    AND detail.invoiceDetailsAutoID = $invoiceAutoID")->result_array();

        return $data;
    }

    function fetch_invoice_template_data_print_tagline_ONSAVE($invoiceAutoID)
    {
        $data = '';
        if(!empty($invoiceAutoID)) {
            $companyid = current_companyID();
            $data['detail'] = $this->db->query('SELECT
	detail.itemDescription,
	detail.noOfUnits,
	detail.grossQty,
	detail.deduction,
	detail.requestedQty,
	mastertbl.customerName,
	mastertbl.invoiceDate
FROM
	srp_erp_customerinvoicedetails_temp detail
	LEFT JOIN srp_erp_customerinvoicemaster mastertbl on detail.invoiceAutoID = mastertbl.invoiceAutoID
	where
	detail.companyID = ' . $companyid . ' AND detail.invoiceDetailsAutoID IN (' . $invoiceAutoID . ')')->result_array();

        }
        return $data;
    }

    function fetch_sales_price()
    {
        $this->db->select('customerID,invoiceDate,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
        $this->db->where('invoiceAutoID', $this->input->post('id'));
        $invmaster = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();
        $customerID=$invmaster['customerID'];
        $invoiceDate=$invmaster['invoiceDate'];
        $itemAutoID=$this->input->post('itemAutoID');
        $policy = getPolicyValues('CPS', 'All');

        if($policy == 1 && !empty($customerID)) {
            $fromcusitemprc= $this->db->query("SELECT salesPrice,isModificationAllowed FROM srp_erp_customeritemprices Where customerAutoID=$customerID AND itemAutoID=$itemAutoID AND '$invoiceDate' between IFNULL(applicableDateFrom,'1990-01-01') AND IFNULL(applicableDateTo,'2070-01-01') AND isActive=1")->row_array();
            if(!empty($fromcusitemprc['salesPrice'])){
                $slsprc=$fromcusitemprc['salesPrice'];
                $isModificationAllowed=$fromcusitemprc['isModificationAllowed'];
                $localCurrencyER = 1 / $invmaster['companyLocalExchangeRate'];
                $unitprice = round(($slsprc / $localCurrencyER), $invmaster['transactionCurrencyDecimalPlaces']);

                return array('status' => true, 'amount' => $unitprice, 'isModificationAllowed' => $isModificationAllowed);
            }else{
                $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
                $this->db->where($this->input->post('primaryKey'), $this->input->post('id'));
                $result = $this->db->get($this->input->post('tableName'))->row_array();

                $localCurrencyER = 1 / $result['companyLocalExchangeRate'];
                $salesprice = trim($this->input->post('salesprice') ?? '');
                $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);

                return array('status' => true, 'amount' => $unitprice, 'isModificationAllowed' => 1);
            }
        } else{
            $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
            $this->db->where($this->input->post('primaryKey'), $this->input->post('id'));
            $result = $this->db->get($this->input->post('tableName'))->row_array();

            $localCurrencyER = 1 / $result['companyLocalExchangeRate'];
            $salesprice = trim($this->input->post('salesprice') ?? '');
            $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);

            return array('status' => true, 'amount' => $unitprice, 'isModificationAllowed' => 1);
        }
    }

    function get_tax_drop_buyback(){
        $comapnyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoId');
        $salesTaxFormulaID = $this->db->query("SELECT taxFormulaID,Description 
                                                    FROM srp_erp_itemtaxformula
                                                    LEFT JOIN srp_erp_taxcalculationformulamaster on srp_erp_itemtaxformula.taxFormulaID = srp_erp_taxcalculationformulamaster.taxCalculationformulaID
                                                    where 
                                                    itemAutoID = $itemAutoID
                                                   And srp_erp_itemtaxformula.taxType = 1 ")->result_array();
        return $salesTaxFormulaID;
    }


    function update_deliveryorder_status(){

    }

    function buyback_deliveryorder_collectionheader(){
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("select *, DATE_FORMAT(deliveredDate,'{$convertFormat}') AS deliveredDate from srp_erp_customerinvoicemaster_temp where companyID = $companyid AND  invoiceAutoID = $invoiceAutoID")->row_array();
        return $data;
    }
    function update_deliveryorder_collectiondetails()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $invoiceAutoID = $this->input->post('invoiceautoid');
        $status = $this->input->post('statuschq');
        $deliverydate = $this->input->post('delivereddatebb');
        $comment = $this->input->post('comment');
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $delivereddateconverted = input_format_date($deliverydate, $date_format_policy);
        $data['deliveryStatus'] = $status;
        if ($status == 1){
            $data['deliveredDate'] = $delivereddateconverted;
            $data['DeliveryComment'] = $comment;
        } else if ($status == 2){
            $data['deliveredDate'] = $delivereddateconverted;
            $data['DeliveryComment'] = $comment;
        } else {
            $data['deliveredDate'] = null;
            $data['DeliveryComment'] = null;
        }


        $this->db->where('invoiceAutoID',$invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster_temp', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Delivery Order Status Changed Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Delivery Order Status Changed Successfully');

        }
    }

    function fetch_dn_details(){
        $companyID=current_companyID();
        $convertFormat = convert_date_format_sql();
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $dayClosed = $this->db->query("SELECT isDayClosed FROM srp_erp_customerinvoicemaster_temp WHERE companyID = {$companyID} AND invoiceAutoID = $invoiceAutoID")->row_array();
        $isDayClosed = $dayClosed['isDayClosed'];

        if( $isDayClosed == 0){
            $this->db->select('srp_erp_customerinvoicemaster_temp.*,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster_temp.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,IFNULL(confirmedByName,\'-\') as confirmedYNn, SalesPersonName');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $this->db->join('srp_erp_salespersonmaster','srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster_temp.salesPersonID', 'LEFT');
            $master = $this->db->get()->row_array();
            //$data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        } else {
            $this->db->select('*, isPrintDN as printTagYN, wareHouseAutoID as warehouseAutoID,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,IFNULL(confirmedByName,\'-\') as confirmedYNn ');
            $this->db->where('tempInvoiceID', $invoiceAutoID);
            $this->db->from('srp_erp_customerinvoicemaster');
            $master = $this->db->get()->row_array();
            //$data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        }

        if($isDayClosed == 0){
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails_temp');
            $data = $this->db->get()->result_array();
        }else{
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $master['invoiceAutoID']);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_customerinvoicedetails');
            $data = $this->db->get()->result_array();
        }
        if (!$data){
            return 0;
        }else{
            return 1;
        }

    }
}