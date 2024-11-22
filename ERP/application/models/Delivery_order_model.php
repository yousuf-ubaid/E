<?php

/**
 *
 * -- =============================================
 * -- File Name : Delivery_order.php
 * -- Project Name : SME
 * -- Module Name : Sales & Marketing
 * -- Create date : 06 February 2019
 * -- Description : Delivery order management.
 *
 * -- REVISION HISTORY
 *
 * -- =============================================
 **/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delivery_order_model extends ERP_Model
{

    function get_customer_data($customerID)
    {
        return $this->db->get_where('srp_erp_customermaster', ['customerAutoID' => $customerID])->row_array();
    }

    function save_delivery_order_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $invDate = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $finance_year_periodYN = getPolicyValues('FPC', 'All');
        $dateTime = current_date();

        if($finance_year_periodYN == 1) {
            $finance_yr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

            $FYBegin = input_format_date($finance_yr[0], $date_format_policy);
            $FYEnd = input_format_date($finance_yr[1], $date_format_policy);
        }
        else{
            $financeYearDetails=get_financial_year($invoiceDate);
            if(empty($financeYearDetails)){
                return ['e', 'Finance period not found for the selected document date'];

            }else{
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $_POST['finance_year'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($invoiceDate);

            if(empty($financePeriodDetails)){
                return ['e', 'Finance period not found for the selected document date'];

            }else{
                $_POST['finance_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $customerID = trim($this->input->post('customerID') ?? '');
        $customer_arr = $this->get_customer_data($customerID);

        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $finance_period = trim($this->input->post('finance_period') ?? '');
        $finance_period_data = $this->db->get_where('srp_erp_companyfinanceperiod', ['companyFinancePeriodID'=>$finance_period])->row_array();

        $data['documentID'] = 'DO';
        $data['companyFinanceYearID'] = trim($this->input->post('finance_year') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = $finance_period;
        $data['FYPeriodDateFrom'] = $finance_period_data['dateFrom'];
        $data['FYPeriodDateTo'] = $finance_period_data['dateTo'];

        $data['DODate'] = $invoiceDate;
        $data['customerInvoiceDate'] = $invoiceDate;
        $data['invoiceDueDate'] = $invoiceDate;
        $narration = ($this->input->post('invoiceNarration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);

        $crTypes = explode('<table', $this->input->post('invoiceNote'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['note'] = $notes;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }

        $data['DOType'] = trim($this->input->post('invoiceType') ?? '');
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
        $data['timestamp'] = $dateTime;

        $orderAutoID = trim($this->input->post('orderAutoID') ?? '');
        if (!empty($orderAutoID)) { // If update
            $masterID = $this->input->post('orderAutoID');
            $taxAdded = $this->db->query("SELECT DOAutoID FROM srp_erp_deliveryorderdetails WHERE DOAutoID = $masterID
                                            UNION
                                        SELECT DOAutoID FROM srp_erp_deliveryordertaxdetails WHERE DOAutoID = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $dateTime;

            $this->db->where('DOAutoID', $orderAutoID);
            $this->db->update('srp_erp_deliveryorder', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return ['e', 'Order update failed ' . $this->db->_error_message()];
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->db->trans_commit();

                return ['s', 'Order updated successfully.', 'last_id' => $orderAutoID];
            }
        }
        else {
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            //Initial save
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $dateTime;
            $data['DOCode'] = '0';


            $this->db->insert('srp_erp_deliveryorder', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Order saved failed ' . $this->db->_error_message()];
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->db->trans_commit();

                return ['s', 'Order saved successfully.', 'last_id' => $last_id];
            }
        }
    }

    function get_order_header_details($masterID)
    {
        update_group_based_tax('srp_erp_deliveryorder', 'DOAutoID', $masterID, null, null, 'DO');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(DODate,\'' . $convertFormat . '\') AS orderDate, DATE_FORMAT(customerInvoiceDate,\'' . $convertFormat . '\') AS cusInsDate
                           ,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invDueDate, DOType AS invoiceType');
        $this->db->where('DOAutoID', $masterID);
        return $this->db->get('srp_erp_deliveryorder')->row_array();
    }

    function add_direct_delivery_order_items()
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('invoiceAutoID') ?? ''),'DO','DOAutoID');
        $projectExist = project_is_exist();
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
        $promotionID = $this->input->post('promotionID');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $dateTime = current_date();
        $company_id = current_companyID();
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $parkQty = $this->input->post('parkQty');

        $this->db->trans_start();
        $this->db->select('companyReportingCurrency, companyReportingCurrencyID, companyLocalCurrency, companyLocalCurrencyID, transactionExchangeRate, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('DOAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_deliveryorder')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $service_itm= $this->db->get()->row_array();

            if (!trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                if($service_itm['mainCategory']=="Inventory") {
                    if (!empty($invoiceDetailsAutoID)) {
                        $this->db->select('DOAutoID,,itemDescription,itemSystemCode');
                        $this->db->from('srp_erp_deliveryorderdetails');
                        $this->db->where('DOAutoID', $invoiceAutoID);
                        $this->db->where('itemAutoID', $itemAutoID);
                        $this->db->where('DODetailsAutoID !=', $invoiceDetailsAutoID);
                        $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                        $order_detail = $this->db->get()->row_array();
                        if (!empty($order_detail)) {
                            return array('w', 'Delivery order detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                        }
                    }
                }
            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);

            $data['DOAutoID'] = $invoiceAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['deliveredQty'] = $quantityRequested[$key];
            $data['promotionID'] = $promotionID[$key];
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
            $data['deliveredTransactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
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
            if($service_itm['mainCategory']=="Service") {
                $data['wareHouseAutoID'] = 0;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }else{
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            }
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
            $data['grossQty'] = $grossQty[$key];
            $data['noOfUnits'] = $noOfUnits[$key];
            $data['deduction'] = $deduction[$key];
            $data['parkQty'] = $parkQty[$key];


            if (isset($item_text[$key])) {
                if($isGroupByTax == 1) {
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$item_text[$key]);
                    $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                    $dataTax['DOAutoID'] = trim($invoiceAutoID);
                    $dataTax['taxFormulaMasterID'] = $item_text[$key];
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

                    $tot_amount = $estimatedAmount[$key] * $quantityRequested[$key];                    
                } else {
                    $this->db->select('txtMas.*,chAcc.GLAutoID as liabilityAutoID,chAcc.systemAccountCode as liabilitySystemGLCode, chAcc.GLSecondaryCode as liabilityGLAccount,
                                        chAcc.GLDescription as liabilityDescription, chAcc.CategoryTypeDescription as liabilityType,curMas.CurrencyCode,curMas.DecimalPlaces');
                    $this->db->where('taxMasterAutoID', $item_text[$key]);
                    $this->db->from('srp_erp_taxmaster txtMas');
                    $this->db->join('srp_erp_chartofaccounts chAcc', 'chAcc.GLAutoID = txtMas.supplierGLAutoID');
                    $this->db->join('srp_erp_currencymaster curMas', 'curMas.currencyID = txtMas.supplierCurrencyID');
                    $tax_master = $this->db->get()->row_array();
             
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
                }
            }

            $data['companyID'] = $company_id;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $dateTime;
            $data['timestamp'] = $dateTime;
            $this->db->insert('srp_erp_deliveryorderdetails', $data);
            $last_id = $this->db->insert_id();

            if($isGroupByTax == 1) {
                $discountAmount = $discount_amount[$key] * $quantityRequested[$key];
                if(!empty($item_text[$key])){
                    tax_calculation_vat('srp_erp_deliveryordertaxdetails',$dataTax,$item_text[$key],'DOAutoID',trim($invoiceAutoID),$tot_amount,'DO',$last_id,$discountAmount,1);
                }

            }

            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $company_id);
                $ware_house_items = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($ware_house_items)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $data['wareHouseAutoID'],
                        'wareHouseLocation' => $data['wareHouseLocation'],
                        'wareHouseDescription' => $data['wareHouseDescription'],
                        'itemAutoID' => $data['itemAutoID'],
                        'itemSystemCode' => $data['itemSystemCode'],
                        'itemDescription' => $data['itemDescription'],
                        'unitOfMeasureID' => $data['defaultUOMID'],
                        'unitOfMeasure' => $data['defaultUOM'],
                        'currentStock' => 0,
                        'companyID' => $company_id,
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );
                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Order Detail : Saved Successfully.');
        }
    }

    function fetch_direct_delivery_order_details($masterID)
    {
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,DOType');
        $this->db->where('DOAutoID', $masterID);
        $data['currency'] = $this->db->get('srp_erp_deliveryorder')->row_array();

        $this->db->select("det.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.partNo, srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode, $item_code_alias");
        $this->db->from('srp_erp_deliveryorderdetails det');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = det.itemAutoID', 'left');
        $this->db->where('DOAutoID', $masterID);
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_deliveryordertaxdetails');
        $this->db->where('DOAutoID', $masterID);
        $data['tax_detail'] = $this->db->get()->result_array();

        return $data;
    }

    function fetch_delivery_order_detail($id)
    {
        $item_code_alias = "CONCAT(srp_erp_itemmaster.itemSystemCode, ' - ', srp_erp_itemmaster.seconeryItemCode) as itemSystemCode";
        $this->db->select("detTB.*,masTB.DOType,srp_erp_itemmaster.currentStock,srp_erp_itemmaster.mainCategory, (IFNULL(contractBalance.balance,0) + detTB.requestedQty) AS balanceQty, $item_code_alias");
        $this->db->where('DODetailsAutoID', $id);
        $this->db->join('srp_erp_deliveryorder masTB', 'detTB.DOAutoID = masTB.DOAutoID');
        $this->db->join('srp_erp_itemmaster', 'detTB.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->join('(SELECT srp_erp_contractdetails.contractDetailsAutoID, TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM(ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(TRAILING 0 FROM(ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
                                        FROM srp_erp_contractdetails
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID  GROUP BY contractDetailsAutoID) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                ) contractBalance', 'contractBalance.contractDetailsAutoID = detTB.contractDetailsAutoID','left');
        $this->db->from('srp_erp_deliveryorderdetails detTB');
        return $this->db->get()->row_array();
    }

    function update_delivery_order_item_detail($mainCategory)
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('invoiceAutoID') ?? ''),'DO','DOAutoID');
        $qty_validate = getPolicyValues('VSQ', 'All');
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
        $promotionID = $this->input->post('promotionID');
        $discount_amount = $this->input->post('discount_amount');
        $discount = $this->input->post('discount');
        $updateDeliveredQty = $this->input->post('updateDeliveredQty');
        $deliveredQty = $this->input->post('deliveredQty');
        $projectExist = project_is_exist();
        $dateTime = current_date();
        $company_id = current_companyID();
        $parkQty = $this->input->post('parkQty_edit');

        $msg = '';
        if($updateDeliveredQty == 0) {
            if($deliveredQty > $quantityRequested) {
                return array('e', 'The delivered Qty is greater the requested Qty!');
            }
        }


        $this->db->trans_start();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('DOAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_deliveryorder')->row_array();

        $contractID = $this->db->query("SELECT contractDetailsAutoID, deliveredQty FROM srp_erp_deliveryorderdetails WHERE DODetailsAutoID = {$invoiceDetailsAutoID}")->row_array();

        $tax_master = array();
        //    if($mainCategory == "Inventory") {
        //        if (!empty($invoiceDetailsAutoID)) {
        //            $this->db->select('DOAutoID,,itemDescription,itemSystemCode');
        //            $this->db->from('srp_erp_deliveryorderdetails');
        //            $this->db->where('DOAutoID', $invoiceAutoID);
        //            $this->db->where('itemAutoID', $itemAutoID);
        //            $this->db->where('DODetailsAutoID !=', $invoiceDetailsAutoID);
        //            $order_detail = $this->db->get()->row_array();
        //            if (!empty($order_detail)) {
        //                return array('w', 'Delivery order detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
        //            }
        //        }
        //    }

        $wareHouse_location = explode('|', $wareHouse);
        $item_arr = fetch_item_data($itemAutoID);
        $uomEx = explode('|', $uom);

        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['projectID'] = $projectID;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = $quantityRequested;
        $data['discountPercentage'] = $discount;
        $data['promotionID'] = $promotionID;
        $data['discountAmount'] = $discount_amount;
        $amountafterdiscount = $estimatedAmount - $discount_amount;
        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        $data['taxPercentage'] = $item_taxPercentage;
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
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $data['comment'] = $comment;
        $data['remarks'] = $remarks;
        $data['type'] = 'Item';
        $data['parkQty'] = $parkQty;

        if($updateDeliveredQty == 1) {
            if(isset($contractID['contractDetailsAutoID'])) {
                if($qty_validate == 1){
                    $balanceYN = $this->db->query("SELECT
                                                srp_erp_contractdetails.contractDetailsAutoID,
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
                                                GROUP BY contractDetailsAutoID
                                                    ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                                WHERE
                                                 srp_erp_contractdetails.contractDetailsAutoID = {$contractID['contractDetailsAutoID']}")->row_array();
                    $totalBalance = $balanceYN['balance'] + $contractID['deliveredQty'];

                    if($totalBalance < $quantityRequested) {
                        $msg = 'Delivered Qty is not updated as it exceeded the total contract total qty';
                    } else {
                        $data['deliveredQty'] = $quantityRequested;
                        $data['isDeliveredQtyUpdated'] = 0;
                        $data['deliveredTransactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                    }
                } else {
                    $data['deliveredQty'] = $quantityRequested;
                    $data['isDeliveredQtyUpdated'] = 0;
                    $data['deliveredTransactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                }
            } else {
                $data['deliveredQty'] = $quantityRequested;
                $data['isDeliveredQtyUpdated'] = 0;
                $data['deliveredTransactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            }
        }

        $item_data = fetch_item_data($data['itemAutoID']);
        if($mainCategory == "Service") {
            $data['wareHouseAutoID'] = 0;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }else{
            $data['wareHouseAutoID'] = $wareHouseAutoID;
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        }
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


        if (isset($item_text)) {
            if($isGroupByTax == 1) {
                $this->db->select('*');
                $this->db->where('taxCalculationformulaID',$item_text);
                $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                $dataTax['DOAutoID'] = trim($invoiceAutoID);
                $dataTax['taxFormulaMasterID'] = $item_text;
                $dataTax['taxDescription'] = $tax_master['Description'];

                $tot_amount = $amountafterdiscount * $quantityRequested;

            } else {
                $this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();
         
                if (!empty($tax_master)) {
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
                }
                else {
                    $data['taxSupplierCurrencyExchangeRate'] = 1;
                    $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                    $data['taxSupplierCurrencyAmount'] = 0;
                }
            }
        }

        $data['noOfItems'] = $this->input->post('noOfItems');
        $data['grossQty'] = $this->input->post('grossQty');
        $data['noOfUnits'] = $this->input->post('noOfUnits');
        $data['deduction'] = $this->input->post('deduction');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $dateTime;

        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $company_id);
            $warehouse_items = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouse_items)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $company_id,
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }

        if ($invoiceDetailsAutoID) {
            $compID = $this->common_data['company_data']['company_id'];
            if(isset($contractID['contractDetailsAutoID'])){
                $contractedTotal = $this->db->query("SELECT	(IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_contractdetails.requestedQty 
                    FROM srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS deliveredQty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails WHERE DODetailsAutoID != {$invoiceDetailsAutoID} GROUP BY contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, contractDetailsAutoID FROM srp_erp_customerinvoicedetails GROUP BY contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                    WHERE companyID = {$compID} AND srp_erp_contractdetails.contractDetailsAutoID = {$contractID['contractDetailsAutoID']}")->row_array();

                $deliveredTot = $contractedTotal['totalDeliveredQty'] + $quantityRequested;
                if ($deliveredTot >= $contractedTotal['requestedQty'])
                {
                    $cont_data['invoicedYN'] = 1;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                } else {
                    $cont_data['invoicedYN'] = 0;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                }
            }

            $this->db->where('DODetailsAutoID', $invoiceDetailsAutoID);
            $this->db->update('srp_erp_deliveryorderdetails', $data);

            if($isGroupByTax == 1 && isset($item_text)) {
                $discountAmount = $discount_amount * $quantityRequested;
                $taxApplicanleAmt =  $estimatedAmount * $quantityRequested;
                if(!empty($item_text)){
                    tax_calculation_vat('srp_erp_deliveryordertaxdetails',$dataTax,$item_text,'DOAutoID',trim($invoiceAutoID),$taxApplicanleAmt,'DO',$invoiceDetailsAutoID,$discountAmount,1);
                }


            } else if($isGroupByTax == 1 && !isset($item_text)){
                fetchExistsDetailTBL('DO', trim($invoiceAutoID),trim($invoiceDetailsAutoID),'srp_erp_deliveryordertaxdetails',1, $data['transactionAmount']);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message() , $msg);
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.', $msg);
            }
        }
    }

    function update_all_item_details()
    {
        $projectExist = project_is_exist();
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
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
        $dateTime = current_date();

        $this->db->trans_start();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,
            transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('DOAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_deliveryorder')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();

            $mainCategory = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID])->row('mainCategory');
//            if (!trim($invoiceDetailsAutoID[$key])) {
//                $this->db->select('DOAutoID,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_deliveryorderdetails');
//                $this->db->where('DOAutoID', $invoiceAutoID);
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
//                $order_detail = $this->db->get()->row_array();
//                if($mainCategory == "Inventory") {
//                    if (!empty($order_detail)) {
//                        return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                    }
//                }
//            }else{
//                $this->db->select('DOAutoID,,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_deliveryorderdetails');
//                $this->db->where('DOAutoID', $invoiceAutoID);
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
//                $this->db->where('DODetailsAutoID !=', $invoiceDetailsAutoID[$key]);
//                $order_detail = $this->db->get()->row_array();
//                if($mainCategory == "Inventory") {
//                    if (!empty($order_detail)) {
//                        return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                    }
//                }
//            }
            if (isset($item_text[$key])) {

                $this->db->select('srp_erp_taxmaster.*,chAcc.GLAutoID as liabilityAutoID,chAcc.systemAccountCode as liabilitySystemGLCode, 
                    chAcc.GLSecondaryCode as liabilityGLAccount, chAcc.GLDescription as liabilityDescription,chAcc.CategoryTypeDescription as liabilityType,
                    srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts chAcc', 'chAcc.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();
            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);


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
            $data['deliveredQty'] = $quantityRequested[$key];
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
            $data['deliveredTransactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
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
            if($mainCategory == "Service") {
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }else{
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            }
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


            if (trim($invoiceDetailsAutoID[$key])) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $dateTime;

                $this->db->where('DODetailsAutoID', trim($invoiceDetailsAutoID[$key]));
                $this->db->update('srp_erp_deliveryorderdetails', $data);

            } else {
                $data['DOAutoID'] = $invoiceAutoID;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $dateTime;
                $this->db->insert('srp_erp_deliveryorderdetails', $data);

                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouse_items = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouse_items)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
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
            return array('e', 'Order detail : save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Order detail : saved successfully.');
        }
    }

    function fetch_delivery_order_full_details($masterID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('masterTB.*,DATE_FORMAT(masterTB.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime ,DATE_FORMAT(masterTB.DODate,\'' . $convertFormat . '\') AS DODate , DATE_FORMAT(masterTB.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate, 
        DATE_FORMAT(masterTB.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate, DATE_FORMAT(masterTB.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,
        CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),
        IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END 
        confirmedYNn,srp_erp_salespersonmaster.SalesPersonName as SalesPersonName,srp_designation.DesDescription as DesDescription');
        $this->db->where('DOAutoID', $masterID);
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID = masterTB.salesPersonID','LEFT');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_salespersonmaster.EIdNo','LEFT');
        $this->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId','LEFT');
        $this->db->from('srp_erp_deliveryorder masterTB');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax,customerCountry');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('wareHouseLocation');
        $this->db->where('DOAutoID', $masterID);
        $this->db->where('wareHouseAutoID !=','');
        $this->db->from('srp_erp_deliveryorderdetails');
        $data['warehousearea'] = $this->db->get()->row_array();

        $this->db->select('det.itemAutoID, det.itemDescription, det.itemSystemCode, srp_erp_itemmaster.seconeryItemCode, det.deliveredQty, det.deliveredTransactionAmount');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = det.itemAutoID');
        $this->db->where('DOAutoID', $masterID);
        $this->db->from('srp_erp_deliveryorderdetails det');
        $data['total_value'] = $this->db->get()->result_array();

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $this->db->select("srp_erp_deliveryorderdetails.*,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,warehousemaster.wareHouseDescription warehouse,contractmaster.documentID,FORMAT(requestedQty,2) as requestedQtyformatted, $item_code_alias");
        $this->db->where('DOAutoID', $masterID);
        $this->db->where('type', 'Item');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_deliveryorderdetails.itemAutoID');
        $this->db->join('srp_erp_warehousemaster warehousemaster', 'warehousemaster.wareHouseAutoID =  srp_erp_deliveryorderdetails.wareHouseAutoID','left');
        $this->db->join('srp_erp_contractmaster contractmaster', 'contractmaster.contractAutoID  = srp_erp_deliveryorderdetails.contractAutoID','left');
        $this->db->from('srp_erp_deliveryorderdetails');
        $data['item_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('DOAutoID', $masterID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_deliveryorderdetails');
        $data['gl_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('DOAutoID', $masterID);
        $data['tax'] = $this->db->get('srp_erp_deliveryordertaxdetails')->result_array();

        $this->db->select('srp_erp_deliveryorderdetails.contractAutoID,
        srp_erp_contractmaster.referenceNo AS referenceNo,
        srp_erp_contractmaster.contactPersonName AS contactPersonName,
        srp_erp_contractmaster.contactPersonNumber AS contactPersonNumber,
        srp_erp_contractmaster.contractNarration AS narration,
        srp_erp_contractmaster.contractDate AS contractDate');
        $this->db->from('srp_erp_deliveryorderdetails');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_deliveryorderdetails.contractAutoID', 'LEFT');
        $this->db->where('DOAutoID', $masterID);
        $this->db->group_by("srp_erp_contractmaster.contractAutoID");
        $data['contactperson_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function on_delivery_order_confirmation($orderID){

        $validate = $this->validate_item_master_sub($orderID);

        if ($validate == false) {
            return ['e', 'Please complete your sub item configurations<br/>Add sub item/s before confirm this document.'];
        }

        $companyID = current_companyID();
        $t_arr = array();

        $this->db->select_sum('transactionAmount')->where('DOAutoID', $orderID);
        $transaction_total_amount = $this->db->get('srp_erp_deliveryorderdetails')->row('transactionAmount');

        $this->db->select_sum('totalAfterTax')->where('DOAutoID', $orderID);
        $item_tax = $this->db->get('srp_erp_deliveryorderdetails')->row('totalAfterTax');
        $total_amount = ($transaction_total_amount - $item_tax);

        $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate ,companyLocalExchangeRate ,taxPercentage');
        $tax_arr = $this->db->where('DOAutoID', $orderID)->get('srp_erp_deliveryordertaxdetails')->result_array();

        foreach($tax_arr as $x=>$row) {
            $tax_total_amount = (($row['taxPercentage'] / 100) * $total_amount);
            $t_arr[$x]['taxDetailAutoID'] = $row['taxDetailAutoID'];
            $t_arr[$x]['transactionAmount'] = $tax_total_amount;
            $t_arr[$x]['supplierCurrencyAmount'] = ($tax_total_amount / $row['supplierCurrencyExchangeRate']);
            $t_arr[$x]['companyLocalAmount'] = ($tax_total_amount / $row['companyLocalExchangeRate']);
            $t_arr[$x]['companyReportingAmount'] = ($tax_total_amount / $row['companyReportingExchangeRate']);
        }

        if (!empty($t_arr)) {
            $this->db->update_batch('srp_erp_deliveryordertaxdetails', $t_arr, 'taxDetailAutoID');
        }

        /*updating transaction amount using the query used in the master data table */

        $totalValue = $this->db->query("SELECT masTB.DOAutoID AS DOAutoID, masTB.companyLocalExchangeRate AS locER, masTB.companyLocalCurrencyDecimalPlaces AS loc_dPlace,
                                masTB.companyReportingExchangeRate AS rpt_ER, masTB.companyReportingCurrencyDecimalPlaces AS rpt_dPlace, masTB.customerCurrencyExchangeRate AS cus_cur_ER,
                                masTB.customerCurrencyDecimalPlaces AS cus_dPlace, masTB.transactionCurrencyDecimalPlaces AS trDplace,
                                ( ((IFNULL(addondet.taxPercentage,0) / 100) * (IFNULL(det.transactionAmount,0) - (IFNULL(det.detailtaxamount,0)))) + IFNULL(det.transactionAmount,0) ) AS total_value,
                                ( ((IFNULL(addondet.taxPercentage,0) / 100) * (IFNULL(det.deliveredTransactionAmount,0) - (IFNULL(det.detailtaxamount,0)))) + IFNULL(det.deliveredTransactionAmount,0) ) AS delivered_total_value
                                FROM srp_erp_deliveryorder masTB
                                LEFT JOIN (
                                    SELECT SUM(transactionAmount) AS transactionAmount, SUM( deliveredTransactionAmount ) AS deliveredTransactionAmount, SUM(totalafterTax) AS detailtaxamount, DOAutoID
                                    FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID
                                ) det ON det.DOAutoID = masTB.DOAutoID
                                LEFT JOIN ( 
                                    SELECT SUM(taxPercentage) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails GROUP BY DOAutoID
                                ) addondet ON  addondet.DOAutoID = masTB.DOAutoID 
                                WHERE companyID = {$companyID} AND masTB.DOAutoID = {$orderID}")->row_array();
        $data = array(
            'transactionAmount' => (round($totalValue['total_value'],$totalValue['trDplace'])),
            'deliveredTransactionAmount' => (round($totalValue['delivered_total_value'],$totalValue['trDplace'])),
            'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['locER'],$totalValue['loc_dPlace'])),
            'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['rpt_ER'],$totalValue['rpt_dPlace'])),
            'customerCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['cus_cur_ER'],$totalValue['cus_dPlace'])),
        );

        $this->db->where('DOAutoID', $orderID)->update('srp_erp_deliveryorder', $data);

        return ['s'];
    }

    function validate_item_master_sub($orderID)
    {
        /*Sub item configured count*/
        $sub_cong_count = $this->db->query("SELECT COUNT(ordDet.DODetailsAutoID) AS countAll 
                            FROM srp_erp_deliveryorder ordMas
                            JOIN srp_erp_deliveryorderdetails ordDet ON ordMas.DOAutoID = ordDet.DOAutoID
                            JOIN srp_erp_itemmaster_sub subMas ON subMas.soldDocumentDetailID = ordDet.DODetailsAutoID
                            AND soldDocumentID = 'DO'
                            JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = ordDet.itemAutoID
                            WHERE ordMas.DOAutoID = {$orderID} AND itemmaster.isSubitemExist = 1 ")->row('countAll');


        /*Total quantity on sub item exist items*/
        $total_qty = $this->db->query("SELECT IFNULL(SUM(ordDet.requestedQty), 0) AS totalQty 
                    FROM srp_erp_deliveryorder ordMas
                    JOIN srp_erp_deliveryorderdetails ordDet ON ordMas.DOAutoID = ordDet.DOAutoID
                    JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = ordDet.itemAutoID
                    WHERE ordMas.DOAutoID = {$orderID} AND itemmaster.isSubitemExist = 1")->row('totalQty');

        return ($sub_cong_count == $total_qty);
    }

    function approve_delivery_order(){

        $orderID = trim($this->input->post('orderAutoID') ?? '');
        $level_id = trim($this->input->post('level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $companyID= current_companyID();

        if($status == 1) { /*validate if approval */
            /* reupdate partk Qty */
            $deliveryOrderDetails = $this->db->query("SELECT DODetailsAutoID,itemAutoID  ,wareHouseAutoID FROM srp_erp_deliveryorderdetails 
                WHERE companyID = $companyID AND DOAutoID = $orderID GROUP BY srp_erp_deliveryorderdetails.itemAutoID 
                ")->result_array();
            foreach($deliveryOrderDetails as $detail){
                $this->load->model('Receipt_voucher_model');
                $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($detail['itemAutoID'],$detail['wareHouseAutoID'], 'DO',$orderID);
                $data['parkQty'] =  $pulled_stock['Unapproved_stock'];

                $this->db->where('DODetailsAutoID', $detail['DODetailsAutoID']);
                $this->db->update('srp_erp_deliveryorderdetails', $data);
            }
            /* end reupdate partk Qty */
            $company_id = current_companyID();
            /* $item_low_qty = $this->db->query("SELECT ware_house.itemAutoID,IFNULL( ware_house.currentStock,0) as currentStock, SUM( detTB.deliveredQty / detTB.conversionRateUOM ) AS qty,
                             round(( IFNULL(ware_house.currentStock,0)  - IFNULL(SUM( detTB.deliveredQty / detTB.conversionRateUOM ),0)),4) AS stock, detTB.wareHouseAutoID,
                            itm_mas.itemSystemCode, itm_mas.itemDescription,IFNULL( ware_house.currentStock,0) AS availableStock
                            FROM srp_erp_deliveryorderdetails AS detTB
                            LEFT JOIN (
                                SELECT SUM(transactionQTY/convertionRate) AS currentStock, wareHouseAutoID, itemAutoID
                                FROM srp_erp_itemledger WHERE companyID = {$company_id} GROUP BY wareHouseAutoID, itemAutoID
                            ) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID AND detTB.wareHouseAutoID = ware_house.wareHouseAutoID
                            JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID
                            WHERE DOAutoID = {$orderID} AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                            GROUP BY itemAutoID
                            HAVING stock < 0")->result_array(); */

            $item_low_qty = $this->db->query("SELECT ware_house.itemAutoID,IFNULL( ware_house.currentStock,0) as currentStock, SUM( detTB.deliveredQty / detTB.conversionRateUOM ) AS qty,
                               	round(( ( IFNULL( ware_house.currentStock, 0 ) - IFNULL( SUM( detTB.parkQty / detTB.conversionRateUOM ),0) )- IFNULL( SUM( detTB.deliveredQty / detTB.conversionRateUOM ), 0 )), 4 ) AS stock,
                                detTB.wareHouseAutoID, 
                              itm_mas.itemSystemCode, itm_mas.itemDescription,IFNULL( ware_house.currentStock,0) AS availableStock
                              FROM srp_erp_deliveryorderdetails AS detTB 
                              LEFT JOIN (
                                  SELECT SUM(transactionQTY/convertionRate) AS currentStock, wareHouseAutoID, itemAutoID 
                                  FROM srp_erp_itemledger WHERE companyID = {$company_id} GROUP BY wareHouseAutoID, itemAutoID
                              ) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID AND detTB.wareHouseAutoID = ware_house.wareHouseAutoID
                              JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID
                              WHERE DOAutoID = {$orderID} AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                              GROUP BY itemAutoID
                              HAVING stock < 0")->result_array();

            if (!empty($item_low_qty)) {
                die(json_encode(['e', 'Some Item quantities are not sufficient to approve this transaction.', 'in-suf-items' => $item_low_qty, 'in-suf-qty' => 'Y']));
            }
        }

        $this->load->library('approvals');



        $approvals_status = $this->approvals->approve_document($orderID, $level_id, $status, $comments, 'DO');
        reupdate_companylocalwac('srp_erp_deliveryorderdetails',$orderID,'DOAutoID','companyLocalWacAmount');
        if ($approvals_status == 1) {

           $do_details = $this->db->query("SELECT status FROM srp_erp_deliveryorder WHERE DOAutoID = {$orderID} ")->row('status');
           if( $do_details!= 2){
               $cont_data['status'] = 2;
               $this->db->where('DOAutoID', $orderID);
               $this->db->update('srp_erp_deliveryorder', $cont_data);
           }

            $master = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=> $orderID])->row_array();
            $this->update_item_ledger($orderID, $master);
            $this->double_entry_delivery_order($orderID);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Delivery order approval process.', 1);
        } else {
            $this->db->trans_commit();

            switch ($approvals_status){
                case 1: return ['s', 'Delivery order fully approved.']; break;
                case 2: return ['s', 'Delivery order level - '.$level_id.' successfully approved']; break;
                case 3: return ['s', 'Delivery order successfully rejected.']; break;
                case 5: return ['w', 'Previous Level Approval Not Finished']; break;
                default : return ['e', 'Error in Delivery order approvals process'];
            }
        }
    }

    function update_item_ledger($orderID, $master){
        $loc_ER = $master['companyLocalExchangeRate']; $rpt_ER = $master['companyReportingExchangeRate']; $cus_ER = $master['customerCurrencyExchangeRate'];
        $tr_dPlace = $master['transactionCurrencyDecimalPlaces']; $loc_dPlace = $master['companyLocalCurrencyDecimalPlaces'];
        $rpt_dPlace = $master['companyReportingCurrencyDecimalPlaces']; $cus_dPlace = $master['customerCurrencyDecimalPlaces'];

        $order_details = $this->db->get_where('srp_erp_deliveryorderdetails', ['DOAutoID'=> $orderID])->result_array();
        $company_id = $this->common_data['company_data']['company_id'];

        $this->db->select('srp_erp_deliveryorderdetails.contractAutoID,
        srp_erp_contractmaster.referenceNo AS referenceNo,
        srp_erp_contractmaster.contactPersonName AS contactPersonName,
        srp_erp_contractmaster.contactPersonNumber AS contactPersonNumber,
        srp_erp_contractmaster.contractNarration AS narration,
        srp_erp_contractmaster.contractDate AS contractDate');
        $this->db->from('srp_erp_deliveryorderdetails');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_deliveryorderdetails.contractAutoID', 'LEFT');
        $this->db->where('DOAutoID', $orderID);
        $this->db->group_by("srp_erp_contractmaster.contractAutoID");
        $contactperson_detail = $this->db->get()->result_array();
        $refnoarr=array();
        $view_ref = 0;
        if (!empty($contactperson_detail)){
            foreach($contactperson_detail as $val) {
                if(!empty($val['referenceNo'])) {
                    $view_ref = 1;
                    array_push($refnoarr,$val['referenceNo']);
                 }
            }
        }

        if($view_ref==1){
           $refno= join(",",$refnoarr);
        }else{
            $refno=$master['referenceNo'];
        }
        $postDate = $this->db->query("SELECT postDate FROM srp_erp_documentcodemaster 
            JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `srp_erp_documentcodemaster`.`documentID` 
            WHERE companyID='{$company_id}' AND `isApprovalDocument` = 1 AND srp_erp_documentcodemaster.documentID = 'DO'")->row('postDate');

        $n = 0;
        for ($a = 0; $a < count($order_details); $a++) {
            if ($order_details[$a]['type'] == 'Item') {
                $itemAutoID = $order_details[$a]['itemAutoID'];
                $item = fetch_item_data($itemAutoID);
                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {

                    $con_rate_UMO = $order_details[$a]['conversionRateUOM'];
                    $qty = $order_details[$a]['deliveredQty'] / $con_rate_UMO;
                    $wareHouseAutoID = $order_details[$a]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                    $item_arr[$a]['itemAutoID'] = $itemAutoID;
                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                    if (!empty($item_arr)) {
                        $this->db->where('itemAutoID', $itemAutoID);
                        $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                    }

                    $transactionQTY = ($order_details[$a]['deliveredQty'] * -1);

                    $item_ledger_arr[$n]['documentID'] = $master['documentID'];
                    $item_ledger_arr[$n]['documentCode'] = $master['documentID'];
                    $item_ledger_arr[$n]['documentAutoID'] = $master['DOAutoID'];
                    $item_ledger_arr[$n]['documentSystemCode'] = $master['DOCode'];
                    if($postDate == 1){
                        $item_ledger_arr[$n]['documentDate'] = $master['approvedDate'];
                    }else{
                        $item_ledger_arr[$n]['documentDate'] = $master['DODate'];
                    }
                    //$item_ledger_arr[$n]['documentDate'] = $master['DODate'];
                    $item_ledger_arr[$n]['referenceNumber'] = $refno;
                    $item_ledger_arr[$n]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                    $item_ledger_arr[$n]['companyFinanceYear'] = $master['companyFinanceYear'];
                    $item_ledger_arr[$n]['FYBegin'] = $master['FYBegin'];
                    $item_ledger_arr[$n]['FYEnd'] = $master['FYEnd'];
                    $item_ledger_arr[$n]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                    $item_ledger_arr[$n]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                    $item_ledger_arr[$n]['wareHouseAutoID'] = $order_details[$a]['wareHouseAutoID'];
                    $item_ledger_arr[$n]['wareHouseCode'] = $order_details[$a]['wareHouseCode'];
                    $item_ledger_arr[$n]['wareHouseLocation'] = $order_details[$a]['wareHouseLocation'];
                    $item_ledger_arr[$n]['wareHouseDescription'] = $order_details[$a]['wareHouseDescription'];
                    $item_ledger_arr[$n]['itemAutoID'] = $itemAutoID;
                    $item_ledger_arr[$n]['itemSystemCode'] = $order_details[$a]['itemSystemCode'];
                    $item_ledger_arr[$n]['itemDescription'] = $order_details[$a]['itemDescription'];
                    $item_ledger_arr[$n]['defaultUOMID'] = $order_details[$a]['defaultUOMID'];
                    $item_ledger_arr[$n]['defaultUOM'] = $order_details[$a]['defaultUOM'];
                    $item_ledger_arr[$n]['transactionUOMID'] = $order_details[$a]['unitOfMeasureID'];
                    $item_ledger_arr[$n]['transactionUOM'] = $order_details[$a]['unitOfMeasure'];
                    $item_ledger_arr[$n]['transactionQTY'] = $transactionQTY;
                    $item_ledger_arr[$n]['convertionRate'] = $con_rate_UMO;
                    $item_ledger_arr[$n]['currentStock'] = $item_arr[$a]['currentStock'];
                    $item_ledger_arr[$n]['PLGLAutoID'] = $item['costGLAutoID'];
                    $item_ledger_arr[$n]['PLSystemGLCode'] = $item['costSystemGLCode'];
                    $item_ledger_arr[$n]['PLGLCode'] = $item['costGLCode'];
                    $item_ledger_arr[$n]['PLDescription'] = $item['costDescription'];
                    $item_ledger_arr[$n]['PLType'] = $item['costType'];
                    $item_ledger_arr[$n]['BLGLAutoID'] = $item['assteGLAutoID'];
                    $item_ledger_arr[$n]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                    $item_ledger_arr[$n]['BLGLCode'] = $item['assteGLCode'];
                    $item_ledger_arr[$n]['BLDescription'] = $item['assteDescription'];
                    $item_ledger_arr[$n]['BLType'] = $item['assteType'];
                    $item_ledger_arr[$n]['transactionCurrencyDecimalPlaces'] = $tr_dPlace;

                    $ex_rate_wac = (1 / $loc_ER);
                    $tr_amount = round((($order_details[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($transactionQTY / $con_rate_UMO)), $tr_dPlace);
                    $item_ledger_arr[$n]['transactionAmount'] = $tr_amount;
                    $item_ledger_arr[$n]['salesPrice'] = (($order_details[$a]['deliveredTransactionAmount'] / ($transactionQTY / $con_rate_UMO)) * -1);
                    $item_ledger_arr[$n]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $item_ledger_arr[$n]['transactionCurrency'] = $master['transactionCurrency'];
                    $item_ledger_arr[$n]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                    $item_ledger_arr[$n]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $item_ledger_arr[$n]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $item_ledger_arr[$n]['companyLocalExchangeRate'] = $loc_ER;
                    $item_ledger_arr[$n]['companyLocalCurrencyDecimalPlaces'] = $loc_dPlace;
                    $item_ledger_arr[$n]['companyLocalAmount'] = round(($tr_amount / $loc_ER), $loc_dPlace);
                    $item_ledger_arr[$n]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_ledger_arr[$n]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $item_ledger_arr[$n]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $item_ledger_arr[$n]['companyReportingExchangeRate'] = $rpt_ER;
                    $item_ledger_arr[$n]['companyReportingCurrencyDecimalPlaces'] = $rpt_dPlace;
                    $item_ledger_arr[$n]['companyReportingAmount'] = round(($tr_amount / $rpt_ER), $rpt_dPlace);
                    $item_ledger_arr[$n]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $item_ledger_arr[$n]['partyCurrencyID'] = $master['customerCurrencyID'];
                    $item_ledger_arr[$n]['partyCurrency'] = $master['customerCurrency'];
                    $item_ledger_arr[$n]['partyCurrencyExchangeRate'] = $cus_ER;
                    $item_ledger_arr[$n]['partyCurrencyDecimalPlaces'] = $cus_dPlace;
                    $item_ledger_arr[$n]['partyCurrencyAmount'] = round(($tr_amount / $cus_ER), $cus_dPlace);
                    $item_ledger_arr[$n]['confirmedYN'] = $master['confirmedYN'];
                    $item_ledger_arr[$n]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                    $item_ledger_arr[$n]['confirmedByName'] = $master['confirmedByName'];
                    $item_ledger_arr[$n]['confirmedDate'] = $master['confirmedDate'];
                    $item_ledger_arr[$n]['approvedYN'] = $master['approvedYN'];
                    $item_ledger_arr[$n]['approvedDate'] = $master['approvedDate'];
                    $item_ledger_arr[$n]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                    $item_ledger_arr[$n]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                    $item_ledger_arr[$n]['segmentID'] = $master['segmentID'];
                    $item_ledger_arr[$n]['segmentCode'] = $master['segmentCode'];
                    $item_ledger_arr[$n]['companyID'] = $master['companyID'];
                    $item_ledger_arr[$n]['companyCode'] = $master['companyCode'];
                    $item_ledger_arr[$n]['createdUserGroup'] = $master['createdUserGroup'];
                    $item_ledger_arr[$n]['createdPCID'] = $master['createdPCID'];
                    $item_ledger_arr[$n]['createdUserID'] = $master['createdUserID'];
                    $item_ledger_arr[$n]['createdDateTime'] = $master['createdDateTime'];
                    $item_ledger_arr[$n]['createdUserName'] = $master['createdUserName'];
                    $item_ledger_arr[$n]['modifiedPCID'] = $master['modifiedPCID'];
                    $item_ledger_arr[$n]['modifiedUserID'] = $master['modifiedUserID'];
                    $item_ledger_arr[$n]['modifiedDateTime'] = $master['modifiedDateTime'];
                    $item_ledger_arr[$n]['modifiedUserName'] = $master['modifiedUserName'];
                    $item_ledger_arr[$n]['timestamp'] = current_date();
                    $n++;
                }
            }
        }
        if (!empty($item_ledger_arr)) {
            $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr);
        }

    }

    function double_entry_delivery_order($orderID){
        $this->load->model('Double_entry_model');
        $double_entry = $this->Double_entry_model->fetch_double_entry_delivery_order($orderID);
        $ledger_arr = [];
        $company_id = $this->common_data['company_data']['company_id'];
        $postDate = $this->db->query("SELECT postDate FROM srp_erp_documentcodemaster 
                JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `srp_erp_documentcodemaster`.`documentID` 
                WHERE companyID='{$company_id}' AND `isApprovalDocument` = 1 AND srp_erp_documentcodemaster.documentID = 'DO'")->row('postDate');

        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $ledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['DOAutoID'];
            $ledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
            $ledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['DOCode'];
            if($postDate == 1){
                $ledger_arr[$i]['documentDate'] = $double_entry['master_data']['approvedDate'];
            }else{
                $ledger_arr[$i]['documentDate'] = $double_entry['master_data']['DODate'];
            }
           // $ledger_arr[$i]['documentDate'] = $double_entry['master_data']['DODate'];
            $ledger_arr[$i]['documentType'] = '';
            $ledger_arr[$i]['documentYear'] = $double_entry['master_data']['DODate'];
            $ledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['DODate']));
            $ledger_arr[$i]['documentNarration'] = $double_entry['master_data']['narration'];
            $ledger_arr[$i]['chequeNumber'] = '';
            $ledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $ledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $ledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
            $ledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $ledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $ledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $ledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
            $ledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $ledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
            $ledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
            $ledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
            $ledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
            $ledger_arr[$i]['partyContractID'] = '';
            $ledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
            $ledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
            $ledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
            $ledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
            $ledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
            $ledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
            $ledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
            $ledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
            $ledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
            $ledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
            $ledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
            $ledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
            $ledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
            $ledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
            $ledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
            $ledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
            $ledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
            $ledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
            $amount = $double_entry['gl_detail'][$i]['gl_dr'];
            if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
            }
            $ledger_arr[$i]['transactionAmount'] = round($amount, $ledger_arr[$i]['transactionCurrencyDecimalPlaces']);
            $ledger_arr[$i]['companyLocalAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['companyLocalExchangeRate']), $ledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
            $ledger_arr[$i]['companyReportingAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['companyReportingExchangeRate']), $ledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
            $ledger_arr[$i]['partyCurrencyAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['partyExchangeRate']), $ledger_arr[$i]['partyCurrencyDecimalPlaces']);
            $ledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
            $ledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
            $ledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
            $ledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
            $ledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
            $ledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
            $ledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
            $ledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
            $ledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
            $ledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
            $ledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
            $ledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
            $ledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
            $ledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
            $ledger_arr[$i]['createdUserGroup'] = current_user_group();
            $ledger_arr[$i]['createdPCID'] = current_pc();
            $ledger_arr[$i]['createdUserID'] = current_userID();
            $ledger_arr[$i]['createdDateTime'] = current_date();
            $ledger_arr[$i]['createdUserName'] = current_user();
            $ledger_arr[$i]['modifiedPCID'] = current_pc();
            $ledger_arr[$i]['modifiedUserID'] = current_userID();
            $ledger_arr[$i]['modifiedDateTime'] = current_date();
            $ledger_arr[$i]['modifiedUserName'] = current_user();
        }

        if (!empty($ledger_arr)) {
            $this->db->insert_batch('srp_erp_generalledger', $ledger_arr);
        }
    }

    function save_con_base_items()
    {
        $orderAutoID = trim($this->input->post('orderAutoID') ?? '');
        $this->db->trans_start();

        $this->db->select('srp_erp_contractdetails.*,sum(srp_erp_customerinvoicedetails.requestedQty) AS receivedQty,srp_erp_contractmaster.contractCode');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where_in('srp_erp_contractdetails.contractDetailsAutoID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID');
        $this->db->join('srp_erp_customerinvoicedetails', 'srp_erp_customerinvoicedetails.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID', 'left');
        $this->db->group_by("contractDetailsAutoID");
        $query = $this->db->get()->result_array();


        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,
            transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('DOAutoID', $orderAutoID);
        $master = $this->db->get('srp_erp_deliveryorder')->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $whrehouse = $this->input->post('whrehouse');
        $tex_id = $this->input->post('tex_id');
        $tex_percntage = $this->input->post('tex_percntage');
        $remarks = $this->input->post('remarks');
        $taxCalculationFormulaID = $this->input->post('taxCalculationFormulaID');


        for ($i = 0; $i < count($query); $i++) {
            $itemAutoID = $query[$i]['itemAutoID'];
            $discount_percentage = ($discount[$i] / $amount[$i])*100;
            $this->db->select('contractAutoID');
            $this->db->from('srp_erp_deliveryorderdetails');
            $this->db->where('contractAutoID', $query[$i]['contractAutoID']);
            $this->db->where('DOAutoID', $orderAutoID);
            $this->db->where('itemAutoID', $itemAutoID);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($itemAutoID);
            $wareHouse_arr = explode('|', $whrehouse[$i]);

            $mainCategory = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID])->row('mainCategory');

            if (!empty($order_detail) && $mainCategory == "Inventory") {
                $this->db->trans_rollback();
                return ['w', 'Order detail : ' . trim($this->input->post('itemCode') ?? '') . ' already exists.'];
            }
            else {
                $data[$i]['type'] = 'Item';
                $data[$i]['contractAutoID'] = $query[$i]['contractAutoID'];
                $data[$i]['contractCode'] = $query[$i]['contractCode'];
                $data[$i]['contractDetailsAutoID'] = $query[$i]['contractDetailsAutoID'];
                $data[$i]['DOAutoID'] = $orderAutoID;
                $data[$i]['itemAutoID'] = $itemAutoID;
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['contractQty'] = $query[$i]['requestedQty'];
                $data[$i]['contractAmount'] = $query[$i]['unittransactionAmount'];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['deliveredQty'] = $qty[$i];
                $data[$i]['isDeliveredQtyUpdated'] = 0;
                $data[$i]['unittransactionAmount'] = $amount[$i];
                $data[$i]['discountAmount'] = $discount[$i];
                $data[$i]['discountPercentage'] = $discount_percentage;
                $data[$i]['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                $data[$i]['segmentID'] = $master['segmentID'];
                $data[$i]['segmentCode'] = $master['segmentCode'];
                $data[$i]['expenseGLAutoID'] = $item_data['costGLAutoID'];
                $data[$i]['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                $data[$i]['expenseGLCode'] = $item_data['costGLCode'];
                $data[$i]['expenseGLDescription'] = $item_data['costDescription'];
                $data[$i]['expenseGLType'] = $item_data['costType'];
                $data[$i]['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
                $data[$i]['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
                $data[$i]['revenueGLCode'] = $item_data['revanueGLCode'];
                $data[$i]['revenueGLDescription'] = $item_data['revanueDescription'];
                $data[$i]['revenueGLType'] = $item_data['revanueType'];
                $data[$i]['assetGLAutoID'] = $item_data['assteGLAutoID'];
                $data[$i]['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data[$i]['assetGLCode'] = $item_data['assteGLCode'];
                $data[$i]['assetGLDescription'] = $item_data['assteDescription'];
                $data[$i]['assetGLType'] = $item_data['assteType'];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $remarks[$i];
                $data[$i]['wareHouseAutoID'] = $wareHouseAutoID[$i];
                $data[$i]['wareHouseCode'] = $wareHouse_arr[0];
                $data[$i]['wareHouseLocation'] = $wareHouse_arr[1];

                $data[$i]['taxCalculationFormulaID'] = $taxCalculationFormulaID[$i];
                $data[$i]['taxPercentage'] = $tex_percntage[$i];
                $tax_amount = ($data[$i]['taxPercentage'] / 100) * ($data[$i]['unittransactionAmount'] - $data[$i]['discountAmount']);
                $data[$i]['taxAmount'] =round($tax_amount, $master['transactionCurrencyDecimalPlaces']);
                $totalAfterTax  = ($data[$i]['taxAmount'] * $data[$i]['requestedQty']);
                $data[$i]['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
                $transactionAmount = ($data[$i]['requestedQty'] * ($data[$i]['unittransactionAmount'] - $discount[$i] )) + $data[$i]['totalAfterTax'];
                $data[$i]['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);

                $totalAfterTax_delivered  = ($data[$i]['taxAmount'] * $data[$i]['deliveredQty']);
                $deliveredtransactionAmount = ($data[$i]['deliveredQty'] * ($data[$i]['unittransactionAmount'] - $discount[$i] )) + (round($totalAfterTax_delivered, $master['transactionCurrencyDecimalPlaces']));
                $data[$i]['deliveredTransactionAmount'] = round($deliveredtransactionAmount, $master['transactionCurrencyDecimalPlaces']);

                $companyLocalAmount = $data[$i]['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data[$i]['transactionAmount'] / $master['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $data[$i]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                if (isset($tex_id[$i])) {
                    $this->db->select('srp_erp_taxmaster.*,chAcc.GLAutoID as liabilityAutoID,chAcc.systemAccountCode as liabilitySystemGLCode, chAcc.GLSecondaryCode as liabilityGLAccount,
                    chAcc.GLDescription as liabilityDescription,chAcc.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                    $this->db->where('taxMasterAutoID', $tex_id[$i]);
                    $this->db->from('srp_erp_taxmaster');
                    $this->db->join('srp_erp_chartofaccounts chAcc', 'chAcc.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                    $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                    $tax_master = $this->db->get()->row_array();
                }

                if (!empty($tax_master)) {
                    $data[$i]['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                    $data[$i]['taxDescription'] = $tax_master['taxDescription'];
                    $data[$i]['taxShortCode'] = $tax_master['taxShortCode'];
                    $data[$i]['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                    $data[$i]['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                    $data[$i]['taxSupplierName'] = $tax_master['supplierName'];
                    $data[$i]['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                    $data[$i]['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                    $data[$i]['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                    $data[$i]['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                    $data[$i]['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                    $data[$i]['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                    $data[$i]['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                    $supplierCurrency = currency_conversion($master['transactionCurrency'], $data[$i]['taxSupplierCurrency']);
                    $data[$i]['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $data[$i]['taxSupplierCurrencyAmount'] = ($data[$i]['transactionAmount'] / $data[$i]['taxSupplierCurrencyExchangeRate']);
                }
                else {
                    $data[$i]['taxMasterAutoID'] = null;
                    $data[$i]['taxDescription'] = null;
                    $data[$i]['taxShortCode'] = null;
                    $data[$i]['taxSupplierAutoID'] = null;
                    $data[$i]['taxSupplierSystemCode'] = null;
                    $data[$i]['taxSupplierName'] = null;
                    $data[$i]['taxSupplierCurrencyID'] = null;
                    $data[$i]['taxSupplierCurrency'] = null;
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = null;
                    $data[$i]['taxSupplierliabilityAutoID'] = null;
                    $data[$i]['taxSupplierliabilitySystemGLCode'] = null;
                    $data[$i]['taxSupplierliabilityGLAccount'] = null;
                    $data[$i]['taxSupplierliabilityDescription'] = null;
                    $data[$i]['taxSupplierliabilityType'] = null;

                    $data[$i]['taxSupplierCurrencyExchangeRate'] = 1;
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = 2;
                    $data[$i]['taxSupplierCurrencyAmount'] = 0;
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

                if(!empty($query[$i]['contractDetailsAutoID']))
                {
                    $compID = $this->common_data['company_data']['company_id'];
                    $contractedTotal = $this->db->query("SELECT	(IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_contractdetails.requestedQty 
                    FROM srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS deliveredQty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, contractDetailsAutoID FROM srp_erp_customerinvoicedetails GROUP BY contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                    WHERE companyID = {$compID} AND srp_erp_contractdetails.contractDetailsAutoID = {$query[$i]['contractDetailsAutoID']}")->row_array();

                    $deliveredTot = $contractedTotal['totalDeliveredQty'] + $qty[$i];
                    if ($deliveredTot >= $contractedTotal['requestedQty'])
                    {
                        $cont_data['invoicedYN'] = 1;
                        $this->db->where('contractDetailsAutoID', $query[$i]['contractDetailsAutoID']);
                        $this->db->update('srp_erp_contractdetails', $cont_data);
                    }
                }
            }
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_deliveryorderdetails', $data);


            $CINVTax = $this->db->query("SELECT
                                                taxCalculationFormulaID,
                                                DOAutoID,
                                                DODetailsAutoID,
                                                (transactionAmount +IFNULL( (discountAmount * requestedQty), 0 )) AS transactionAmount,
                                                IFNULL( (discountAmount * requestedQty), 0 ) AS discountAmount
                                                from 
                                                srp_erp_deliveryorderdetails
                                                where 
                                                companyID = $compID 
                                                AND DOAutoID  = $orderAutoID")->result_array();


            if(existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($orderAutoID),'DO','DOAutoID')== 1){
                if(!empty($CINVTax)){ 
                    foreach($CINVTax as $val){
                        if($val['taxCalculationFormulaID']!=0){
                            tax_calculation_vat(null,null,$val['taxCalculationFormulaID'],'DOAutoID',trim($orderAutoID),$val['transactionAmount'],'DO',$val['DODetailsAutoID'],$val['discountAmount'],1);
                        }               
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Order details save failed ' . $this->db->_error_message()];
            } else {
                $this->db->trans_commit();
                return ['s', 'Order ' . count($query) . ' item details saved successfully.'];
            }
        }
        else {
            return ['e', 'No data to process'];
        }
    }
    function loademail()
    {
        $DoAutoId = $this->input->post('DOAutoID');
        $this->db->select('srp_erp_deliveryorder.*,`srp_erp_customermaster`.`customerEmail` AS `customerEmail` ');
        $this->db->where('DOAutoID', $DoAutoId);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_deliveryorder.customerID', 'left');
        $this->db->from('srp_erp_deliveryorder ');
        return $this->db->get()->row_array();
    }
    function send_do_email()
    {
        $doid = $this->input->post('invoiceid');
        $doemail = trim($this->input->post('email') ?? '');
        $this->db->select('srp_erp_deliveryorder.*,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerName as customerName');
        $this->db->where('DOAutoID', $doid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_deliveryorder.customerID', 'left');
        $this->db->from('srp_erp_deliveryorder ');
        $results = $this->db->get()->row_array();
        if (!empty($results)) {
            if ($results['customerEmail'] == '') {
                $data_master['customerEmail'] = $doemail;
                $this->db->where('customerAutoID', $results['customerID']);
                $this->db->update('srp_erp_customermaster', $data_master);
            }
        }

        $this->db->select('customerEmail,customerName');
        $this->db->where('customerAutoID', $results['customerID']);
        $this->db->from('srp_erp_customermaster ');
        $customerMaster = $this->db->get()->row_array();

        $data['approval'] = $this->input->post('approval');
        $data['extra'] = $this->Delivery_order_model->fetch_delivery_order_full_details($doid);
        $data['signature'] = fetch_signature_level('Do');
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $data['printHeaderFooterYN']=1;
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/delivery_order/delivery-order-print-view', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH.base_url().'/uploads/DO/'. $doid .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);


        if (!empty($customerMaster)) {
            if ($customerMaster['customerEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our Delivery Order as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $doemail,
                    'subject' => 'Delivery Order for ' .$customerMaster['customerName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);
                return array('s', 'Email Send Successfully.',$doemail,$doid,$results["documentID"]);
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }

    }

    function deliveryorder_collectionheader()
    {
        $DOAutoID = trim($this->input->post('autoID') ?? '');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("select *, DATE_FORMAT(deliveredDate,'{$convertFormat}') AS deliveredDate from srp_erp_deliveryorder where companyID = $companyid AND  DOAutoID = $DOAutoID")->row_array();
        return $data;
    }
    function update_deliveryorder_collectiondetails()
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('DOAutoIddo') ?? ''),'DO','DOAutoID');
        $this->db->trans_start();
        $companyID = current_companyID();
        $deliveryOrderid = trim($this->input->post('DOAutoIddo') ?? '');
        $status = trim($this->input->post('statusdo') ?? '');
        $date_format_policy = date_format_policy();
        $documentDate = $this->input->post('delivereddatedo');
        $formatted_documentDate = input_format_date($documentDate, $date_format_policy);
        $data['status'] = $status;
        if ($status == 1) {
            $data['driverName'] = $this->input->post('driver_name');
            $data['deliveredDate'] = $formatted_documentDate;
            $data['deliveryComment'] = $this->input->post('commentdo');
        } else if($status == 2){
            $data['driverName'] = $this->input->post('driver_name');
            $data['deliveredDate'] = $formatted_documentDate;
            $data['deliveryComment'] = $this->input->post('commentdo');

            $itemAutoID = $this->input->post('itemAutoID');
            $DODetailsAutoID = $this->input->post('DODetailsAutoID');
            foreach ($DODetailsAutoID as $item) {
                $DODetailsAutoID = $this->input->post('detailID_' . $item);
                $details['deliveredQty'] =  $this->input->post('delivered_' . $item);

                $det = $this->db->query("SELECT unittransactionAmount, requestedQty, deliveredQty,
                                                IFNULL(discountAmount , 0) AS discountAmount,
                                                IFNULL(taxAmount , 0) AS taxAmount,
                                                master.transactionCurrencyDecimalPlaces,
                                                IFNULL(balanceDet.balance, 0) AS balance,
                                                details.contractDetailsAutoID
                                             FROM srp_erp_deliveryorderdetails details
                                             LEFT JOIN srp_erp_deliveryorder master ON master.DOAutoID = details.DOAutoID
                                             LEFT JOIN (
                                                        SELECT
                                                            srp_erp_contractdetails.contractDetailsAutoID,
                                                            TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM(ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(TRAILING 0 FROM(ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
                                                        FROM
                                                            srp_erp_contractdetails
                                                            LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV FROM srp_erp_customerinvoicedetails GROUP BY contractDetailsAutoID ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                                            LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                                        ) balanceDet ON balanceDet.contractDetailsAutoID = details.contractDetailsAutoID 
                                             WHERE details.companyID = {$companyID} AND DODetailsAutoID = {$DODetailsAutoID}")->row_array();

                if($details['deliveredQty']  <=  $det['requestedQty']){
                    $details['isDeliveredQtyUpdated'] =  1;

                    $amountafterdiscount = $det['unittransactionAmount'] - $det['discountAmount'];
                    if($isGroupByTax == 1){
                        $transactionAmount = (($det['taxAmount']/$det['requestedQty']) + $amountafterdiscount) * $details['deliveredQty'];
                    } else {
                        $transactionAmount = ($det['taxAmount'] + $amountafterdiscount) * $details['deliveredQty'];
                    }
                    $details['deliveredTransactionAmount'] = round($transactionAmount, $det['transactionCurrencyDecimalPlaces']);

                    $this->db->where('DODetailsAutoID', $DODetailsAutoID);
                    $this->db->update('srp_erp_deliveryorderdetails', $details);

                    $balanceCNT = ($det['balance'] + $det['deliveredQty']) - $details['deliveredQty'];
                    if($balanceCNT <= 0) {
                        $cont_data['invoicedYN'] = 1;
                        $this->db->where('contractDetailsAutoID', $det['contractDetailsAutoID']);
                        $this->db->update('srp_erp_contractdetails', $cont_data);
                    } else {
                        $cont_data['invoicedYN'] = 0;
                        $this->db->where('contractDetailsAutoID', $det['contractDetailsAutoID']);
                        $this->db->update('srp_erp_contractdetails', $cont_data);
                    }
                }
            }
        } else{
            $data['driverName'] = null;
            $data['deliveredDate'] = null;
            $data['deliveryComment'] = null;
        }

        $totalValue = $this->db->query("SELECT masTB.DOAutoID AS DOAutoID, masTB.companyLocalExchangeRate AS locER, masTB.companyLocalCurrencyDecimalPlaces AS loc_dPlace,
                                masTB.companyReportingExchangeRate AS rpt_ER, masTB.companyReportingCurrencyDecimalPlaces AS rpt_dPlace, masTB.customerCurrencyExchangeRate AS cus_cur_ER,
                                masTB.customerCurrencyDecimalPlaces AS cus_dPlace, masTB.transactionCurrencyDecimalPlaces AS trDplace,
                                ( ((IFNULL(addondet.taxPercentage,0) / 100) * (IFNULL(det.transactionAmount,0) - (IFNULL(det.detailtaxamount,0)))) + IFNULL(det.transactionAmount,0) ) AS total_value,
                                ( ((IFNULL(addondet.taxPercentage,0) / 100) * (IFNULL(det.deliveredTransactionAmount,0) - (IFNULL(det.detailtaxamount,0)))) + IFNULL(det.deliveredTransactionAmount,0) ) AS delivered_total_value
                                FROM srp_erp_deliveryorder masTB
                                LEFT JOIN (
                                    SELECT SUM(transactionAmount) AS transactionAmount, SUM( deliveredTransactionAmount ) AS deliveredTransactionAmount, SUM(totalafterTax) AS detailtaxamount, DOAutoID
                                    FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID
                                ) det ON det.DOAutoID = masTB.DOAutoID
                                LEFT JOIN ( 
                                    SELECT SUM(taxPercentage) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails GROUP BY DOAutoID
                                ) addondet ON  addondet.DOAutoID = masTB.DOAutoID 
                                WHERE companyID = {$companyID} AND masTB.DOAutoID = {$deliveryOrderid}")->row_array();

        $data['deliveredTransactionAmount'] = round($totalValue['delivered_total_value'],$totalValue['trDplace']);
        $data['transactionAmount'] = round($totalValue['total_value'],$totalValue['trDplace']);

        $this->db->where('DOAutoID', $deliveryOrderid);
        $this->db->update('srp_erp_deliveryorder', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Delivery detail status updated ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Delivery detail status updated successfully.', $deliveryOrderid);
        }
    }

    function fetch_DO_delivered_item_details()
    {
        $companyID = current_companyID();
        $DOAutoID = trim($this->input->post('DOAutoID') ?? '');

        $data = $this->db->query("SELECT
                            DODetailsAutoID, itemAutoID, itemSystemCode, itemDescription, unitOfMeasure, requestedQty, IFNULL(deliveredQty, 0) AS deliveredQty, approvedYN, (IFNULL(contract.balance, 0) + IFNULL(deliveredQty, 0)) AS balance,wareHouseAutoID, srp_erp_deliveryorderdetails.DOAutoID
                            FROM
                                srp_erp_deliveryorderdetails 
                            LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
                            LEFT JOIN (
                                        SELECT
                                        srp_erp_contractdetails.contractDetailsAutoID,
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
                                        LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV FROM srp_erp_customerinvoicedetails GROUP BY contractDetailsAutoID ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                        LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                    GROUP BY
                                        srp_erp_contractdetails.contractDetailsAutoID
                            )contract ON contract.contractDetailsAutoID = srp_erp_deliveryorderdetails.contractDetailsAutoID                            
                            WHERE
                                srp_erp_deliveryorderdetails.DOAutoID = {$DOAutoID} AND srp_erp_deliveryorderdetails.companyID = {$companyID}")->result_array();

        $this->load->model('Receipt_voucher_model');
        foreach($data as $detail){
            $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($detail['itemAutoID'],$detail['wareHouseAutoID'], 'DO',$DOAutoID);
            $det['parkQty'] =  $pulled_stock['Unapproved_stock'];
            $this->db->where('DODetailsAutoID', $detail['DODetailsAutoID']);
            $this->db->update('srp_erp_deliveryorderdetails', $det);
        }
        return $data;
    }

    function check_DO_matched()
    {
        $DOAutoID = trim($this->input->post('DOAutoID') ?? '');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT ord.DOAutoID, ord.referenceNo, DOCode, DODate, DOType, segmentID, ord.customerID, ord.transactionCurrencyID, ord.transactionCurrency, deliveredTransactionAmount AS transactionAmount, transactionCurrencyDecimalPlaces,
                          (IFNULL(paid_amount,0) + IFNULL(return_amount,0)) AS invoiced_amount , ord.salesPersonID AS salesPersonID, ord.contactPersonName AS contactPersonName, ord.contactPersonNumber AS contactPersonNumber 
                          FROM srp_erp_deliveryorder ord
                          LEFT JOIN (
                              SELECT DOMasterID, SUM(det.transactionAmount) paid_amount FROM srp_erp_customerinvoicedetails det
                              JOIN srp_erp_customerinvoicemaster mas ON mas.invoiceAutoID = det.invoiceAutoID                              
                              WHERE mas.companyID = {$companyID} GROUP BY DOMasterID
                          ) paidDet ON paidDet.DOMasterID = ord.DOAutoID
                          LEFT JOIN(
                              SELECT returnDet.DOAutoID, SUM(returnDet.totalValue) return_amount
                              FROM srp_erp_salesreturnmaster AS returnMas
                              JOIN srp_erp_salesreturndetails AS returnDet ON returnMas.salesReturnAutoID = returnDet.salesReturnAutoID
                              WHERE returnMas.companyID = {$companyID}
                              AND returnDet.invoiceAutoID IS NULL GROUP BY returnDet.DOAutoID
                          ) AS return_tb ON return_tb.DOAutoID = ord.DOAutoID
                          WHERE companyID = {$companyID} AND approvedYN = 1 AND ord.DOAutoID = {$DOAutoID}")->row_array();

        if ($data['transactionAmount'] > $data['invoiced_amount']) {
            return array('s', $data);
        } else {
            return array('w', 'Delivery Order is Fully Invoiced!');
        }
    }

    function generate_invoice_from_DO_header()
    {
        $this->load->model('Invoice_model');
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $companyID = current_companyID();
        $DOAutoID = trim($this->input->post('DOAutoID') ?? '');
        $INV_Date = trim($this->input->post('INV_Date') ?? '');
        $INV_Due_Date = trim($this->input->post('INV_Due_Date') ?? '');
        $invoiceDueDate = input_format_date($INV_Due_Date, $date_format_policy);
        $invoiceDate = input_format_date($INV_Date, $date_format_policy);
        $financeyear = trim($this->input->post('financeyear') ?? '');
        $companyFinanceYear = trim($this->input->post('companyFinanceYear') ?? '');
        $financeyear_period = trim($this->input->post('financeyear_period') ?? '');
        $INV_narration = trim($this->input->post('INV_narration') ?? '');
        $INV_reference = trim($this->input->post('INV_reference') ?? '');
        $transactionAmount_delivered = trim($this->input->post('transactionAmount_delivered') ?? '');
        $invoiced_amount = trim($this->input->post('invoiced_amount') ?? '');
        $salesperson = trim($this->input->post('salesperson') ?? '');
        $contactPersonName = trim($this->input->post('contactPersonName') ?? '');
        $contactPersonNumber = trim($this->input->post('contactPersonNumber') ?? '');
        $isDOItemWisePolicy  = getPolicyValues('DOIW', 'All');

        $this->db->select('*');
        $this->db->from('srp_erp_deliveryorder');
        $this->db->where('DOAutoID', $DOAutoID);
        $master_data = $this->db->get()->row_array();

        if($invoiceDate >= $master_data['DODate']) {
            if ($financeyearperiodYN == 1) {
                $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                $FYBegin = input_format_date($financeyr[0], $date_format_policy);
                $FYEnd = input_format_date($financeyr[1], $date_format_policy);
            } else {
                $financeYearDetails = get_financial_year($invoiceDate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($invoiceDate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                }
            }

            $rebate = getPolicyValues('CRP', 'All');
            if($rebate == 1) {
                $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$master_data['customerID']}")->row_array();
                if(!empty($rebate)) {
                    $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                    $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
                }
            } else {
                $data['rebateGLAutoID'] = null;
                $data['rebatePercentage'] = null;
            }

            $data['documentID'] = 'CINV';
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['contactPersonName'] = $contactPersonName;
            $data['contactPersonNumber'] = $contactPersonNumber;
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
            $data['invoiceDate'] = $invoiceDate;
            $data['customerInvoiceDate'] = $invoiceDate;
            $data['invoiceDueDate'] = $invoiceDueDate;
            $data['invoiceNarration'] = trim_desc($INV_narration);
            $data['referenceNo'] = trim_desc($INV_reference);
            $data['invoiceNote'] = '';
            $data['segmentID'] = $master_data['segmentID'];
            $data['segmentCode'] = $master_data['segmentCode'];
            $data['salesPersonID'] = $salesperson;
            $data['invoiceType'] = 'DeliveryOrder';
            $data['referenceNo'] = $master_data['DOCode'];
            $data['isPrintDN'] = 0;
            $data['isGroupBasedTax'] = $master_data['isGroupBasedTax'];
            $data['customerID'] = $master_data['customerID'];
            $data['customerSystemCode'] = $master_data['customerSystemCode'];
            $data['customerName'] = $master_data['customerName'];
            $data['customerAddress'] = $master_data['customerAddress'];
            $data['customerTelephone'] = $master_data['customerTelephone'];
            $data['customerFax'] = $master_data['customerFax'];
            $data['customerEmail'] = $master_data['customerEmail'];
            $data['customerReceivableAutoID'] = $master_data['customerReceivableAutoID'];
            $data['customerReceivableSystemGLCode'] = $master_data['customerReceivableSystemGLCode'];
            $data['customerReceivableGLAccount'] = $master_data['customerReceivableGLAccount'];
            $data['customerReceivableDescription'] = $master_data['customerReceivableDescription'];
            $data['customerReceivableType'] = $master_data['customerReceivableType'];
            $data['customerCurrency'] = $master_data['customerCurrency'];
            $data['customerCurrencyID'] = $master_data['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $master_data['customerCurrencyDecimalPlaces'];
            $data['transactionCurrencyID'] = $master_data['transactionCurrencyID'];
            $data['transactionCurrency'] = $master_data['transactionCurrency'];
            $data['transactionExchangeRate'] = $master_data['transactionExchangeRate'];
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
            if($isDOItemWisePolicy == 1) {
                $data['isDOItemWisePolicy'] = 1;
            } else {
                $data['isDOItemWisePolicy'] = 0;
            }
            

            $this->db->trans_start();
            $this->db->insert('srp_erp_customerinvoicemaster', $data);
            $last_id = $this->db->insert_id();

            if($isDOItemWisePolicy == 1) {
                $this->db->select('*');
                $this->db->from('srp_erp_deliveryorderdetails');
                $this->db->where('DOAutoID', $DOAutoID);
                $do_det_data = $this->db->get()->result_array();
        
                if($last_id) {
                    $un_billed_gl = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                              WHERE GLAutoID = (
                                                  SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                              ) AND companyID={$companyID} ")->row_array();
                    foreach($do_det_data as $value){
                       
                            $detail['DOMasterID'] = $DOAutoID;
                            $detail['invoiceAutoID'] = $last_id;
                            $detail['DODetailsID'] = $value['DODetailsAutoID'];
                            $detail['itemSystemCode'] = $value['itemSystemCode'];
                            $detail['itemAutoID'] = $value['itemAutoID'];
                            $detail['itemDescription'] = $value['itemDescription'];
                            $detail['itemCategory'] = $value['itemCategory'];
                            $detail['taxPercentage'] = $value['taxPercentage'];
                            $detail['wareHouseAutoID'] = $value['wareHouseAutoID'];
                            $detail['defaultUOMID'] = $value['defaultUOMID'];
                            $detail['defaultUOM'] = $value['defaultUOM'];
                            $detail['unitOfMeasureID'] = $value['unitOfMeasureID'];
                            $detail['unitOfMeasure'] = $value['unitOfMeasure'];
                            $detail['conversionRateUOM'] = $value['conversionRateUOM'];
                            $detail['requestedQty'] = $value['requestedQty'];
                            $detail['companyLocalWacAmount'] = $value['companyLocalWacAmount'];
                            $detail['unittransactionAmount'] = $value['unittransactionAmount'];
                            $detail['transactionAmount'] = $value['transactionAmount'];
                            $detail['companyLocalAmount'] = $value['companyLocalAmount'];
                            $detail['companyReportingAmount'] = $value['companyReportingAmount'];
                            $detail['customerAmount'] = $value['customerAmount'];
                            $detail['customerAmount'] = $value['customerAmount'];
                            $detail['taxPercentage'] = $value['taxPercentage'];
                            $detail['segmentID'] = $value['segmentID'];
                            $detail['segmentCode'] = $value['segmentCode'];
                            $detail['discountPercentage'] = $value['discountPercentage'];
                            $detail['discountAmount'] = $value['discountAmount'];
                            $detail['taxDescription'] = $value['taxDescription'];
                            $detail['taxAmount'] = $value['taxAmount'];
                            $detail['totalAfterTax'] = $value['totalAfterTax'];
                            $detail['taxSupplierCurrencyDecimalPlaces'] = $value['taxSupplierCurrencyDecimalPlaces'];
                            $detail['taxSupplierCurrencyExchangeRate'] = $value['taxSupplierCurrencyExchangeRate'];
                            $detail['taxSupplierCurrencyAmount'] = $value['taxSupplierCurrencyAmount'];
                            $detail['taxSupplierCurrencyAmount'] = $value['taxSupplierCurrencyAmount'];
                            
                            $detail['revenueGLAutoID'] = $un_billed_gl['GLAutoID'];
                            $detail['revenueSystemGLCode'] = $un_billed_gl['systemAccountCode'];
                            $detail['revenueGLCode'] = $un_billed_gl['GLSecondaryCode'];
                            $detail['revenueGLDescription'] = $un_billed_gl['GLDescription'];
                            $detail['revenueGLType'] = $un_billed_gl['subCategory'];
            
                            $total_value = $transactionAmount_delivered - $invoiced_amount;
                            $detail['transactionAmount'] = $value['transactionAmount'];
                            $detail['due_amount'] = $value['transactionAmount'];
                            $detail['balance_amount'] = round((0), $data['customerCurrencyDecimalPlaces']);
                            $companyLocalAmount = $value['transactionAmount'] / $data['companyLocalExchangeRate'];
                            $detail['companyLocalAmount'] = round($companyLocalAmount, $data['companyLocalCurrencyDecimalPlaces']);
                            $companyReportingAmount = $value['transactionAmount'] / $data['companyReportingExchangeRate'];
                            $detail['companyReportingAmount'] = round($companyReportingAmount, $data['companyReportingCurrencyDecimalPlaces']);
                            $customerAmount = $value['transactionAmount'] / $data['customerCurrencyExchangeRate'];
                            $detail['customerAmount'] = $value['customerAmount'];
            
                            $detail['type'] = 'DO';
                            $detail['companyCode'] = $this->common_data['company_data']['company_code'];
                            $detail['companyID'] = $this->common_data['company_data']['company_id'];
                            $detail['createdUserGroup'] = $this->common_data['user_group'];
                            $detail['createdPCID'] = $this->common_data['current_pc'];
                            $detail['createdUserID'] = $this->common_data['current_userID'];
                            $detail['createdUserName'] = $this->common_data['current_user'];
                            $detail['createdDateTime'] = $this->common_data['current_date'];
            
                            $this->db->insert('srp_erp_customerinvoicedetails', $detail);
                            $last_detail_id = $this->db->insert_id();               
                    }
                    $ledgerDet = $this->db->query("SELECT
                            IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                            customerCountryID,
                            vatEligible,
                            customerID,
                            srp_erp_taxledger.*, outputVatGLAccountAutoID,
                            IF(taxCategory = 2 ,outputVatTransferGLAccountAutoID,taxGlAutoID) as outputVatTransferGLAccountAutoID, deliveredTransactionAmount,invoiceDetailsAutoID	 
                        FROM
                            srp_erp_taxledger
                            JOIN ( SELECT deliveredTransactionAmount, DOAutoID, customerID FROM srp_erp_deliveryorder ) mastertbl ON mastertbl.DOAutoID = srp_erp_taxledger.documentMasterAutoID 
                            AND srp_erp_taxledger.documentID = 'DO'
                            JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                            JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                            JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                            LEFT join srp_erp_customerinvoicedetails ON srp_erp_customerinvoicedetails.DODetailsID = srp_erp_taxledger.documentDetailAutoID
                            WHERE srp_erp_taxmaster.taxCategory = 2 AND DOAutoID = {$DOAutoID}")->result_array();

                    if(!empty($ledgerDet)) {
                        $taxAmount = 0;
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'CINV';
                            $dataleg['documentMasterAutoID'] = $last_id;
                            $dataleg['documentDetailAutoID'] = $val['invoiceDetailsAutoID'];
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = $val['taxPercentage'];
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['isClaimable'] = $val['isClaimable'];
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $dataleg['amount'] = ($val['amount'] / $val['deliveredTransactionAmount']) * $total_value;
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                            $dataleg['transferGLAutoID'] = $val['outputVatTransferGLAccountAutoID'];
                            $dataleg['countryID'] = $val['customerCountryID'];
                            $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                            $dataleg['partyID'] = $val['customerID'];
                            $dataleg['locationID'] = null;
                            $dataleg['locationType'] = null;
                            $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                            $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                            $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                            $dataleg['createdPCID'] = $this->common_data['current_pc'];
                            $dataleg['createdUserID'] = $this->common_data['current_userID'];
                            $dataleg['createdUserName'] = $this->common_data['current_user'];
                            $dataleg['createdDateTime'] = $this->common_data['current_date'];

                            $this->db->insert('srp_erp_taxledger', $dataleg);
                            $taxAmount += ($val['amount'] / $val['deliveredTransactionAmount']) * $total_value;
                        }
                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $taxAmount;
                        $this->db->where('invoiceDetailsAutoID', $val['invoiceDetailsAutoID']);
                        $this->db->update('srp_erp_customerinvoicedetails', $data_detailTBL);
                    }
                    
                }
            } else {
                if($last_id) {
                    $un_billed_gl = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                              WHERE GLAutoID = (
                                                  SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                              ) AND companyID={$companyID} ")->row_array();
    
                    $detail['DOMasterID'] = $DOAutoID;
                    $detail['invoiceAutoID'] = $last_id;
                    $detail['revenueGLAutoID'] = $un_billed_gl['GLAutoID'];
                    $detail['revenueSystemGLCode'] = $un_billed_gl['systemAccountCode'];
                    $detail['revenueGLCode'] = $un_billed_gl['GLSecondaryCode'];
                    $detail['revenueGLDescription'] = $un_billed_gl['GLDescription'];
                    $detail['revenueGLType'] = $un_billed_gl['subCategory'];
    
                    $total_value = $transactionAmount_delivered - $invoiced_amount;
                    $detail['transactionAmount'] = round($total_value, $data['customerCurrencyDecimalPlaces']);
                    $detail['due_amount'] = round($total_value, $data['customerCurrencyDecimalPlaces']);
                    $detail['balance_amount'] = round((0), $data['customerCurrencyDecimalPlaces']);
                    $companyLocalAmount = $total_value / $data['companyLocalExchangeRate'];
                    $detail['companyLocalAmount'] = round($companyLocalAmount, $data['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $total_value / $data['companyReportingExchangeRate'];
                    $detail['companyReportingAmount'] = round($companyReportingAmount, $data['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $total_value / $data['customerCurrencyExchangeRate'];
                    $detail['customerAmount'] = round($customerAmount, $data['customerCurrencyDecimalPlaces']);
    
                    $detail['type'] = 'DO';
                    $detail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $detail['companyID'] = $this->common_data['company_data']['company_id'];
                    $detail['createdUserGroup'] = $this->common_data['user_group'];
                    $detail['createdPCID'] = $this->common_data['current_pc'];
                    $detail['createdUserID'] = $this->common_data['current_userID'];
                    $detail['createdUserName'] = $this->common_data['current_user'];
                    $detail['createdDateTime'] = $this->common_data['current_date'];
    
                    $this->db->insert('srp_erp_customerinvoicedetails', $detail);
                    $last_detail_id = $this->db->insert_id();
    
                    $ledgerDet = $this->db->query("SELECT
                                        IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                        customerCountryID,
                                        vatEligible,
                                        customerID,
                                        srp_erp_taxledger.*, outputVatGLAccountAutoID,
                                        IF(taxCategory = 2 ,outputVatTransferGLAccountAutoID,taxGlAutoID) as outputVatTransferGLAccountAutoID, deliveredTransactionAmount 
                                    FROM
                                        srp_erp_taxledger
                                        JOIN ( SELECT deliveredTransactionAmount, DOAutoID, customerID FROM srp_erp_deliveryorder ) mastertbl ON mastertbl.DOAutoID = srp_erp_taxledger.documentMasterAutoID 
                                        AND srp_erp_taxledger.documentID = 'DO'
                                        JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                        JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                        JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                        WHERE srp_erp_taxmaster.taxCategory = 2 AND DOAutoID = {$DOAutoID}")->result_array();
    
                    if(!empty($ledgerDet)) {
                        $taxAmount = 0;
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'CINV';
                            $dataleg['documentMasterAutoID'] = $last_id;
                            $dataleg['documentDetailAutoID'] = $last_detail_id;
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = $val['taxPercentage'];
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['isClaimable'] = $val['isClaimable'];
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $dataleg['amount'] = ($val['amount'] / $val['deliveredTransactionAmount']) * $total_value;
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                            $dataleg['transferGLAutoID'] = $val['outputVatTransferGLAccountAutoID'];
                            $dataleg['countryID'] = $val['customerCountryID'];
                            $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                            $dataleg['partyID'] = $val['customerID'];
                            $dataleg['locationID'] = null;
                            $dataleg['locationType'] = null;
                            $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                            $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                            $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                            $dataleg['createdPCID'] = $this->common_data['current_pc'];
                            $dataleg['createdUserID'] = $this->common_data['current_userID'];
                            $dataleg['createdUserName'] = $this->common_data['current_user'];
                            $dataleg['createdDateTime'] = $this->common_data['current_date'];
    
                            $this->db->insert('srp_erp_taxledger', $dataleg);
                            $taxAmount += ($val['amount'] / $val['deliveredTransactionAmount']) * $total_value;
                        }
                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $taxAmount;
                        $this->db->where('invoiceDetailsAutoID', $last_detail_id);
                        $this->db->update('srp_erp_customerinvoicedetails', $data_detailTBL);
                    }
                }
    
            }

          
            /** Added (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$last_id}")->row_array();
            if(!empty($rebate)) {
                $this->Invoice_model->calculate_rebate_amount($last_id);
            }
            /** End (SME-2299)*/


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array(['e', 'Invoice generation failed ' . $this->db->_error_message()]);
            } else {
                $this->db->trans_commit();

                $confirmed = $this->Invoice_model->invoice_confirmation($last_id);
                if ($confirmed[0] == 's') {
                    return array('s', 'Invoice Generated & Confirmed Successfully!');
                } else {
                    return array('s', 'Invoice Generated Successfully!');
                }
            }
        } else {
            return array('e', 'Invoice date should be greater than or equal to Delivery Order Date');
        }
    }

    function fetch_line_tax_and_vat()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $data['isGroupByTax'] =  existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('DOAutoID') ?? ''),'DO','DOAutoID');
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
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('DOAutoID') ?? ''),'DO','DOAutoID');
        if($isGroupByTax == 1){
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'DO', trim($this->input->post('DOAutoID') ?? ''));
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        return $amnt;
    }


    function fetch_do_excel()
    {

        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $date_from = $this->input->post('date_from');
        $date_from_convert = input_format_date($date_from, $date_format_policy);
        $date_to = $this->input->post('date_to');
        $date_to_convert = input_format_date($date_to, $date_format_policy);

        $company_id = current_companyID();
        $customer = $this->input->post('customer_code');
        $customer_filter = (!empty($customer))? " AND customerID IN ({$customer})": '';
        $date = (!empty($date_from) && !empty($date_to))? " AND ( DATE(DODate) BETWEEN '{$date_from_convert}' AND '{$date_to_convert}')" : '';

        $status = $this->input->post('status');
        $status_filter = "";
        if ($status != 'all') {
            $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            switch ($status){
                case 1:  $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";  break;
                case 2:  $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";  break;
                case 4:  $status_filter = " AND ((confirmedYN = 3 AND approvedYN != 1) or (confirmedYN = 2 AND approvedYN != 1))";  break;
            }
        }

        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( DOCode Like '%$search%' ESCAPE '!') OR ( DOType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  
                          OR (narration Like '%$sSearch%') OR (cusMas.customerName Like '%$sSearch%') OR (DODate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') 
                          OR (referenceNo Like '%$sSearch%')) ";
        }
        $where = " ordMas.isDeleted != 1 AND ordMas.companyID = " . $company_id . $customer_filter . $date . $status_filter . $searches."";


        $this->datatables->select("ordMas.DOAutoID as DOAutoID,
            ordMas.confirmedByEmpID as confirmedByEmp,
            DOCode,
            narration as narration,
            cusMas.customerName as cus_name,
            transactionCurrency as transactionCurrency, 
            transactionCurrencyDecimalPlaces,
            IF(confirmedYN = 1, 'Confirmed', 'Not Confirmed') as confirmed,
            IF(approvedYN = 1, 'Approved', 'Not Approved') as approved,
            
            CASE
            WHEN status = '0' THEN  'Not Delivered' 
            WHEN status = '1' THEN  'sent to Delivery' 
            WHEN status = '2' THEN  'Delivered' 
            ELSE '-' 
            END AS status,

            ordMas.createdUserID as createdUser,
            DATE_FORMAT( DODate , '%d-%m-%Y' ) AS DODate,
            DATE_FORMAT(invoiceDueDate, '%d-%m-%Y') AS invoiceDueDate, 
            DOType,
            CASE
                WHEN DOType = 'Direct' THEN
                                        'Direct' 
                WHEN DOType = 'Quotation' THEN
                                        'Quotation Based' 
                WHEN DOType = 'Contract' THEN
                                        'Contract Based' 
                WHEN DOType = 'Sales Order' THEN
                                    'Sales Order' 
                ELSE DOType 
            END AS DOType,
            isDeleted, 
            referenceNo as referenceNo,
            (((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value");
        $this->datatables->from('srp_erp_deliveryorder AS ordMas');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,DOAutoID 
                                  FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID) det', '(det.DOAutoID = ordMas.DOAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,DOAutoID FROM srp_erp_deliveryordertaxdetails  
                                   GROUP BY DOAutoID) addondet', '(addondet.DOAutoID = ordMas.DOAutoID)', 'left');
        $this->datatables->join('srp_erp_customermaster AS cusMas', 'cusMas.customerAutoID = ordMas.customerID', 'left');
        $this->datatables->where($where);
        $result = $this->db->get()->result_array();
        

        $a = 1;
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'code' => $row['DOCode'],
                'documentDate' => $row['DODate'],
                'dueDate' => $row['invoiceDueDate'],
                'customerName' => $row['cus_name'],
                'type' => $row['DOType'],
                'referenceNumber' => $row['referenceNo'],
                'comment' => $row['narration'],
                'currency' => $row['transactionCurrency'],
                'amount' => $row['total_value'],
                'confirmed' => $row['confirmed'],
                'approved' => $row['approved'],
                'status' => $row['status'],
                'decimalPlace' => $row['transactionCurrencyDecimalPlaces']
            );
            $a++;
        }
        return $data;
    }
}
